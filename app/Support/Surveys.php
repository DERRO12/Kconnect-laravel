<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Satisfaction surveys: surveys and their questions are set in K-Connect admin
 * (admin/surveys) and answered on the public form (dashboard.kibokogroup.com/satisfaction.php?s=...).
 *
 * Answers are anonymous: a response row keeps only its survey and the day it was sent.
 * No name, email, IP address, device or cookie is stored.
 */
class Surveys
{
    public const TYPES = [
        'likert' => 'Agreement scale (1 to 5)',
        'single' => 'One choice (radio buttons)',
        'multiple' => 'Several choices (tick boxes)',
        'dropdown' => 'Drop-down list',
        'text' => 'Short answer',
        'textarea' => 'Long answer',
        'number' => 'Number',
    ];

    /** Types that need a list of options (one per line). */
    public const CHOICE_TYPES = ['single', 'multiple', 'dropdown'];

    public const LIKERT_LABELS = ['Strongly Disagree', 'Disagree', 'Neutral', 'Agree', 'Strongly Agree'];

    public const STATUSES = ['draft' => 'Draft (not open yet)', 'open' => 'Open (accepting answers)', 'closed' => 'Closed'];

    /** The four tables, created only when missing (nothing existing is changed). Same SQL: database/sql/satisfaction_surveys.sql */
    public static function tableSql(): array
    {
        return [
            'surveys' => "CREATE TABLE `surveys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(40) NOT NULL,
  `intro` text DEFAULT NULL,
  `thank_you` text DEFAULT NULL,
  `status` enum('draft','open','closed') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_survey_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            'survey_questions' => "CREATE TABLE `survey_questions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `section` varchar(150) DEFAULT NULL,
  `question` text NOT NULL,
  `help_text` varchar(255) DEFAULT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'likert',
  `options` text DEFAULT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_sq_survey` (`survey_id`, `is_active`, `sort_order`),
  CONSTRAINT `survey_questions_survey_fk` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            'survey_responses' => "CREATE TABLE `survey_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) NOT NULL,
  `submitted_on` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sr_survey` (`survey_id`, `submitted_on`),
  CONSTRAINT `survey_responses_survey_fk` FOREIGN KEY (`survey_id`) REFERENCES `surveys` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
            'survey_answers' => "CREATE TABLE `survey_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `section` varchar(150) DEFAULT NULL,
  `question_text` text NOT NULL,
  `answer` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sa_response` (`response_id`),
  KEY `idx_sa_question` (`question_id`),
  CONSTRAINT `survey_answers_response_fk` FOREIGN KEY (`response_id`) REFERENCES `survey_responses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci",
        ];
    }

    /** K-Connect fetches rows as arrays (AppServiceProvider); survey code works with objects. */
    public static function rows(string $sql, array $bindings = []): array
    {
        return array_map(fn ($r) => (object) $r, DB::select($sql, $bindings));
    }

    public static function one(string $sql, array $bindings = []): ?object
    {
        $row = DB::selectOne($sql, $bindings);

        return $row === null ? null : (object) $row;
    }

    public static function tablesExist(): bool
    {
        foreach (array_keys(self::tableSql()) as $table) {
            if (! Schema::hasTable($table)) {
                return false;
            }
        }

        return true;
    }

    /** Create any missing survey table. Returns an error message, or null when all tables exist. */
    public static function ensureTables(): ?string
    {
        try {
            foreach (self::tableSql() as $table => $sql) {
                if (! Schema::hasTable($table)) {
                    DB::statement($sql);
                }
            }

            return null;
        } catch (\Throwable $e) {
            report($e);

            return $e->getMessage();
        }
    }

    public static function newSlug(): string
    {
        do {
            $slug = strtolower(substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(12))), 0, 10));
        } while (self::one('SELECT id FROM surveys WHERE slug = ?', [$slug]));

        return $slug;
    }

    /** Where employees open a survey (the form on the dashboard subdomain). */
    public static function publicLink(string $slug): string
    {
        $base = (string) env('SURVEY_FORM_URL', 'https://dashboard.kibokogroup.com/satisfaction.php');

        return $base.(str_contains($base, '?') ? '&' : '?').'s='.rawurlencode($slug);
    }

    /** Options of a choice question, one per line. */
    public static function options(?string $raw): array
    {
        $out = [];
        foreach (preg_split('/\r\n|\r|\n/', (string) $raw) as $line) {
            $line = trim($line);
            if ($line !== '' && ! in_array($line, $out, true)) {
                $out[] = $line;
            }
        }

        return $out;
    }

    public static function questions(int $surveyId, bool $activeOnly = true): array
    {
        return self::rows('SELECT * FROM survey_questions WHERE survey_id = ?'.($activeOnly ? ' AND is_active = 1' : '').' ORDER BY sort_order, id', [$surveyId]);
    }

    /** What the public form needs to draw a survey. */
    public static function publicPayload(object $survey): array
    {
        $sections = [];
        $index = [];
        foreach (self::questions((int) $survey->id) as $q) {
            $name = trim((string) $q->section);
            if (! isset($index[$name])) {
                $index[$name] = count($sections);
                $sections[] = ['title' => $name, 'questions' => []];
            }
            $sections[$index[$name]]['questions'][] = [
                'id' => (int) $q->id,
                'type' => $q->type,
                'text' => $q->question,
                'help' => (string) $q->help_text,
                'required' => (bool) $q->required,
                'options' => in_array($q->type, self::CHOICE_TYPES, true) ? self::options($q->options) : [],
            ];
        }

        return [
            'title' => $survey->title,
            'intro' => (string) $survey->intro,
            'thank_you' => (string) ($survey->thank_you ?: 'Thank you! Your answers have been received anonymously.'),
            'likert_labels' => self::LIKERT_LABELS,
            'sections' => $sections,
        ];
    }

    /**
     * Check and save one set of answers. $answers maps question id => string or list of strings.
     * Returns [true, null] or [false, ['question id' => 'message', ...]].
     */
    public static function saveResponse(object $survey, array $answers): array
    {
        $questions = self::questions((int) $survey->id);
        $clean = [];
        $errors = [];
        foreach ($questions as $q) {
            $raw = $answers[$q->id] ?? $answers[(string) $q->id] ?? null;
            $value = self::cleanAnswer($q, $raw);
            if ($value === false) {
                $errors[$q->id] = 'Please choose one of the listed answers.';
                continue;
            }
            if ($value === '' && $q->required) {
                $errors[$q->id] = 'This question needs an answer.';
                continue;
            }
            if ($value !== '') {
                $clean[] = [$q, $value];
            }
        }
        if ($errors) {
            return [false, $errors];
        }
        if (! $clean) {
            return [false, ['_' => 'Please answer at least one question.']];
        }

        DB::transaction(function () use ($survey, $clean) {
            // Only the day is kept: no time, name, email, IP or device.
            DB::insert('INSERT INTO survey_responses (survey_id, submitted_on) VALUES (?, CURDATE())', [$survey->id]);
            $responseId = (int) DB::getPdo()->lastInsertId();
            foreach ($clean as [$q, $value]) {
                DB::insert('INSERT INTO survey_answers (response_id, question_id, section, question_text, answer) VALUES (?, ?, ?, ?, ?)',
                    [$responseId, $q->id, $q->section, $q->question, $value]);
            }
        });

        return [true, null];
    }

    /** One answer as stored text: '' when empty, false when it is not one of the allowed options. */
    private static function cleanAnswer(object $q, mixed $raw): string|false
    {
        $list = is_array($raw) ? $raw : [$raw];
        $list = array_values(array_filter(array_map(fn ($v) => is_scalar($v) ? trim((string) $v) : '', $list), fn ($v) => $v !== ''));
        if (! $list) {
            return '';
        }
        switch ($q->type) {
            case 'likert':
                $n = (int) $list[0];

                return ($n >= 1 && $n <= 5 && (string) $n === $list[0]) ? (string) $n : false;
            case 'single':
            case 'dropdown':
                return in_array($list[0], self::options($q->options), true) ? $list[0] : false;
            case 'multiple':
                $opts = self::options($q->options);
                foreach ($list as $v) {
                    if (! in_array($v, $opts, true)) {
                        return false;
                    }
                }

                return implode('; ', array_unique($list));
            case 'number':
                return is_numeric($list[0]) ? mb_substr($list[0], 0, 30) : false;
            default:
                return mb_substr($list[0], 0, $q->type === 'text' ? 500 : 5000);
        }
    }

    /** Copy a survey and its questions into a new draft. Returns the new id. */
    public static function duplicate(int $id, ?string $title = null): int
    {
        $s = self::one('SELECT * FROM surveys WHERE id = ?', [$id]);
        DB::insert("INSERT INTO surveys (title, slug, intro, thank_you, status, updated_at) VALUES (?, ?, ?, ?, 'draft', NOW())",
            [$title ?? ($s->title.' (copy)'), self::newSlug(), $s->intro, $s->thank_you]);
        $newId = (int) DB::getPdo()->lastInsertId();
        DB::insert('INSERT INTO survey_questions (survey_id, section, question, help_text, type, options, required, sort_order, is_active)
            SELECT ?, section, question, help_text, type, options, required, sort_order, 1 FROM survey_questions WHERE survey_id = ? AND is_active = 1', [$newId, $id]);

        return $newId;
    }

    /** Create the Kiboko Employee Satisfaction survey (from the HR form) as a new draft. Returns its id. */
    public static function createFromTemplate(): int
    {
        $t = self::employeeSatisfactionTemplate();
        DB::insert("INSERT INTO surveys (title, slug, intro, thank_you, status, updated_at) VALUES (?, ?, ?, ?, 'draft', NOW())",
            [$t['title'], self::newSlug(), $t['intro'], $t['thank_you']]);
        $id = (int) DB::getPdo()->lastInsertId();
        $order = 0;
        foreach ($t['sections'] as $section => $questions) {
            foreach ($questions as $q) {
                [$type, $text, $options, $required] = $q + [2 => '', 3 => false];
                $order += 10;
                DB::insert('INSERT INTO survey_questions (survey_id, section, question, type, options, required, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)',
                    [$id, $section, $text, $type, $options, $required ? 1 : 0, $order]);
            }
        }

        return $id;
    }

    /** Departments for the Bio Data drop-down: K-Connect's departments, or a short default list. */
    private static function departmentOptions(): string
    {
        try {
            $names = array_column(self::rows("SELECT DISTINCT name FROM departments WHERE name <> '' ORDER BY name"), 'name');
        } catch (\Throwable $e) {
            $names = [];
        }
        $names = $names ?: ['Finance', 'Human Resources', 'IT', 'Logistics', 'Procurement', 'Sales', 'Warehouse'];
        $names[] = 'Other';

        return implode("\n", array_unique($names));
    }

    public static function employeeSatisfactionTemplate(): array
    {
        $L = fn (string $text) => ['likert', $text, '', true];

        return [
            'title' => 'Kiboko Group Employee Satisfaction Survey',
            'intro' => "Purpose: to understand employee engagement and satisfaction, identify areas for improvement, and strengthen our workplace culture at Kiboko Group. Your responses are anonymous and confidential.\n\n"
                ."For each statement, choose how much you agree: 1 = Strongly Disagree, 2 = Disagree, 3 = Neutral, 4 = Agree, 5 = Strongly Agree. For the open questions, please give your honest feedback.",
            'thank_you' => 'Thank you for your honest feedback. Your answers were received anonymously and will help make Kiboko a better place to work.',
            'sections' => [
                'Bio Data' => [
                    ['single', 'Gender', "Male\nFemale"],
                    ['dropdown', 'Age range', "18 - 25\n26 - 35\n36 - 45\n46 - 55\n56 and above"],
                    ['dropdown', 'Department', self::departmentOptions()],
                    ['text', 'Job title / role'],
                    ['single', 'Length of service', "Less than 1 year\n1 - 5 years\n5 - 10 years\nMore than 10 years"],
                ],
                'Section A: Company Image, Mission & Values' => [
                    $L("I understand and support the company's mission and values."),
                    $L('The company has a positive reputation in the industry.'),
                    $L("The company's vision and goals are clear to me."),
                ],
                'Section B: Leadership' => [
                    $L('Leaders demonstrate integrity and ethical behaviour.'),
                    $L('Leaders provide clear guidance and direction.'),
                    $L('Leaders are approachable and open to employee feedback.'),
                ],
                'Section C: Supervision' => [
                    $L('My supervisor provides clear instructions and guidance.'),
                    $L('My supervisor supports me in solving work-related challenges.'),
                    $L('My supervisor treats me fairly and respectfully.'),
                    $L('My supervisor encourages my professional growth and development.'),
                ],
                'Section D: Internal Processes' => [
                    $L('I am satisfied with the way decisions are made within the company.'),
                    $L('Communication and coordination between departments is effective.'),
                ],
                'Section E: Work Environment' => [
                    $L('I feel safe and comfortable in my work environment.'),
                    $L('I have the tools and resources I need to perform my job effectively.'),
                    $L('My workplace encourages teamwork and collaboration.'),
                ],
                'Section F: Communication' => [
                    $L('My manager communicates clearly about goals and expectations.'),
                    $L('I receive timely and constructive feedback on my work.'),
                    $L('Important information is shared transparently within the organization.'),
                ],
                'Section G: Career Development' => [
                    $L('I see opportunities for professional growth within the company.'),
                    $L('Training and development programs help me improve my skills.'),
                    $L('My career goals are supported by my manager and the company.'),
                ],
                'Section H: Recognition & Appreciation' => [
                    $L('My work is recognized and appreciated.'),
                    $L('I feel motivated by the benefits and rewards provided by the company.'),
                ],
                'Section I: Engagement & Satisfaction' => [
                    $L('I am proud to work for the company.'),
                    $L('I would recommend the company as a good place to work.'),
                    $L("I feel motivated to contribute to the company's success."),
                ],
                'Section J: Performance Management' => [
                    $L('My performance goals are clearly defined and achievable.'),
                    $L('I receive regular feedback on my performance.'),
                    $L('Performance evaluations are fair and transparent.'),
                ],
                'Section K: Health and Safety' => [
                    $L('I am aware of the health and safety procedures and protocols at my workplace.'),
                    $L('Health and safety equipment and protective measures are provided and used effectively.'),
                    $L('I feel safe performing my daily tasks.'),
                    $L('Management actively promotes a culture of health and safety.'),
                ],
                'Section L: Customer Experience' => [
                    $L('I understand how my role impacts the customer experience.'),
                    $L('I have the necessary tools and training to provide excellent service to customers.'),
                    $L('Kiboko Group consistently meets customer expectations.'),
                    $L('I feel empowered to address customer issues and feedback effectively.'),
                ],
                'Section M: Open-Ended Questions' => [
                    ['textarea', 'What do you like most about working in this company?'],
                    ['textarea', 'What should be improved to make the company a better workplace?'],
                    ['textarea', 'Do you have any other comments or feedback?'],
                ],
            ],
        ];
    }
}
