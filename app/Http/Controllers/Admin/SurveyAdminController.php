<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Surveys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Satisfaction surveys admin:
 *   admin/surveys          list, create (blank or from the HR template), duplicate, open/close, delete
 *   admin/survey-edit      survey settings and its questions (add, edit, reorder, remove)
 *   admin/survey-results   answers summary and Excel (CSV) export
 *   admin/survey-form-file download the form file for dashboard.kibokogroup.com
 */
class SurveyAdminController extends Controller
{
    private function guard(): void
    {
        if (! session()->has('admin_id') && ! session()->has('admin_username')) {
            kc_redirect('login');
        }
    }

    private function back(string $to, string $message, string $type = 'success')
    {
        return redirect(url($to))->with('survey_flash', [$type, $message]);
    }

    /** The tables are added the first time the page is opened (additive only). */
    private function setupError(): ?string
    {
        return Surveys::tablesExist() ? null : Surveys::ensureTables();
    }

    public function index(Request $request)
    {
        $this->guard();
        if ($setup_error = $this->setupError()) {
            return view('admin.surveys', ['setup_error' => $setup_error, 'surveys' => []]);
        }

        if ($request->isMethod('post')) {
            $action = (string) $request->post('action');
            $id = (int) $request->post('id');
            switch ($action) {
                case 'create':
                    $title = trim((string) $request->post('title'));
                    if ($title === '') {
                        return $this->back('admin/surveys', 'Give the survey a title.', 'danger');
                    }
                    DB::insert("INSERT INTO surveys (title, slug, intro, status, updated_at) VALUES (?, ?, ?, 'draft', NOW())",
                        [mb_substr($title, 0, 200), Surveys::newSlug(), (string) $request->post('intro')]);

                    return $this->back('admin/survey-edit?id='.DB::getPdo()->lastInsertId(), 'Survey created. Now add its questions.');
                case 'template':
                    $newId = Surveys::createFromTemplate();

                    return $this->back('admin/survey-edit?id='.$newId, 'Employee Satisfaction survey created from the HR form. Check the questions, then set it to Open.');
                case 'duplicate':
                    if (! Surveys::one('SELECT id FROM surveys WHERE id = ?', [$id])) {
                        break;
                    }
                    $newId = Surveys::duplicate($id);

                    return $this->back('admin/survey-edit?id='.$newId, 'Copy created as a draft with its own new link. Answers are not copied.');
                case 'status':
                    $status = (string) $request->post('status');
                    if (isset(Surveys::STATUSES[$status])) {
                        DB::update('UPDATE surveys SET status = ?, updated_at = NOW() WHERE id = ?', [$status, $id]);

                        return $this->back('admin/surveys', 'Survey is now '.strtolower(explode(' ', Surveys::STATUSES[$status])[0]).'.');
                    }
                    break;
                case 'delete':
                    $s = Surveys::one('SELECT title FROM surveys WHERE id = ?', [$id]);
                    if ($s && trim((string) $request->post('confirm_title')) === $s->title) {
                        DB::delete('DELETE FROM surveys WHERE id = ?', [$id]);   // questions, responses and answers go with it

                        return $this->back('admin/surveys', 'Survey and all its answers deleted.');
                    }

                    return $this->back('admin/surveys', 'Not deleted: type the survey title exactly to confirm.', 'danger');
            }

            return $this->back('admin/surveys', 'Nothing was changed.', 'warning');
        }

        $surveys = Surveys::rows('SELECT s.*,
                (SELECT COUNT(*) FROM survey_questions q WHERE q.survey_id = s.id AND q.is_active = 1) AS question_count,
                (SELECT COUNT(*) FROM survey_responses r WHERE r.survey_id = s.id) AS response_count,
                (SELECT MAX(r.submitted_on) FROM survey_responses r WHERE r.survey_id = s.id) AS last_response
            FROM surveys s ORDER BY s.created_at DESC, s.id DESC');
        foreach ($surveys as $s) {
            $s->link = Surveys::publicLink($s->slug);
        }

        return view('admin.surveys', ['setup_error' => null, 'surveys' => $surveys]);
    }

    public function edit(Request $request)
    {
        $this->guard();
        if ($err = $this->setupError()) {
            return $this->back('admin/surveys', $err, 'danger');
        }
        $id = (int) $request->query('id');
        $survey = Surveys::one('SELECT * FROM surveys WHERE id = ?', [$id]);
        if (! $survey) {
            return $this->back('admin/surveys', 'Survey not found.', 'danger');
        }
        $here = 'admin/survey-edit?id='.$id;

        if ($request->isMethod('post')) {
            $action = (string) $request->post('action');
            if ($action === 'reorder') {   // AJAX from the drag handles
                $order = (array) $request->input('order', []);
                foreach (array_values($order) as $i => $qid) {
                    DB::update('UPDATE survey_questions SET sort_order = ? WHERE id = ? AND survey_id = ?', [($i + 1) * 10, (int) $qid, $id]);
                }

                return response()->json(['ok' => true]);
            }
            if ($action === 'settings') {
                $status = (string) $request->post('status');
                DB::update('UPDATE surveys SET title = ?, intro = ?, thank_you = ?, status = ?, updated_at = NOW() WHERE id = ?', [
                    mb_substr(trim((string) $request->post('title')) ?: $survey->title, 0, 200),
                    (string) $request->post('intro'), (string) $request->post('thank_you'),
                    isset(Surveys::STATUSES[$status]) ? $status : $survey->status, $id,
                ]);

                return $this->back($here, 'Survey settings saved.');
            }
            if ($action === 'save_question') {
                $qid = (int) $request->post('question_id');
                $type = (string) $request->post('type');
                $text = trim((string) $request->post('question'));
                $options = implode("\n", Surveys::options((string) $request->post('options')));
                if ($text === '' || ! isset(Surveys::TYPES[$type])) {
                    return $this->back($here, 'Write the question and choose its answer type.', 'danger');
                }
                if (in_array($type, Surveys::CHOICE_TYPES, true) && count(Surveys::options($options)) < 2) {
                    return $this->back($here, 'A choice question needs at least two options (one per line).', 'danger');
                }
                $section = mb_substr(trim((string) $request->post('section')), 0, 150);
                $help = mb_substr(trim((string) $request->post('help_text')), 0, 255);
                $required = $request->post('required') ? 1 : 0;
                if ($qid) {
                    DB::update('UPDATE survey_questions SET section = ?, question = ?, help_text = ?, type = ?, options = ?, required = ? WHERE id = ? AND survey_id = ?',
                        [$section, $text, $help, $type, $options, $required, $qid, $id]);

                    return $this->back($here.'#q'.$qid, 'Question updated.');
                }
                // New questions go at the end of their section (or of the survey)
                $after = Surveys::one('SELECT MAX(sort_order) AS m FROM survey_questions WHERE survey_id = ? AND is_active = 1 AND section = ?', [$id, $section])->m
                    ?? Surveys::one('SELECT MAX(sort_order) AS m FROM survey_questions WHERE survey_id = ?', [$id])->m ?? 0;
                DB::update('UPDATE survey_questions SET sort_order = sort_order + 10 WHERE survey_id = ? AND sort_order > ?', [$id, $after]);
                DB::insert('INSERT INTO survey_questions (survey_id, section, question, help_text, type, options, required, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [$id, $section, $text, $help, $type, $options, $required, $after + 10]);

                return $this->back($here.'#q'.DB::getPdo()->lastInsertId(), 'Question added.');
            }
            if ($action === 'remove_question') {
                $qid = (int) $request->post('question_id');
                $used = Surveys::one('SELECT 1 FROM survey_answers WHERE question_id = ? LIMIT 1', [$qid]);
                if ($used) {
                    // Keep it for the answers already given; it just stops showing on the form.
                    DB::update('UPDATE survey_questions SET is_active = 0 WHERE id = ? AND survey_id = ?', [$qid, $id]);

                    return $this->back($here, 'Question removed from the form. Its earlier answers stay in the results and export.');
                }
                DB::delete('DELETE FROM survey_questions WHERE id = ? AND survey_id = ?', [$qid, $id]);

                return $this->back($here, 'Question deleted.');
            }
            if ($action === 'restore_question') {
                DB::update('UPDATE survey_questions SET is_active = 1 WHERE id = ? AND survey_id = ?', [(int) $request->post('question_id'), $id]);

                return $this->back($here, 'Question is back on the form.');
            }
            if ($action === 'rename_section') {
                $from = (string) $request->post('from');
                $to = mb_substr(trim((string) $request->post('to')), 0, 150);
                DB::update('UPDATE survey_questions SET section = ? WHERE survey_id = ? AND section = ?', [$to, $id, $from]);

                return $this->back($here, 'Section renamed.');
            }

            return $this->back($here, 'Nothing was changed.', 'warning');
        }

        $questions = Surveys::questions($id, false);
        $sections = array_values(array_unique(array_map(fn ($q) => (string) $q->section, array_filter($questions, fn ($q) => $q->is_active))));
        $answer_counts = [];
        foreach (Surveys::rows('SELECT a.question_id, COUNT(*) AS n FROM survey_answers a JOIN survey_responses r ON r.id = a.response_id WHERE r.survey_id = ? GROUP BY a.question_id', [$id]) as $row) {
            $answer_counts[(int) $row->question_id] = (int) $row->n;
        }
        $response_count = (int) Surveys::one('SELECT COUNT(*) AS n FROM survey_responses WHERE survey_id = ?', [$id])->n;

        return view('admin.survey-edit', [
            'survey' => $survey,
            'link' => Surveys::publicLink($survey->slug),
            'preview_link' => url('satisfaction').'?s='.rawurlencode($survey->slug),
            'questions' => $questions,
            'sections' => $sections,
            'answer_counts' => $answer_counts,
            'response_count' => $response_count,
            'types' => Surveys::TYPES,
            'statuses' => Surveys::STATUSES,
        ]);
    }

    /** Date filter shared by the results page and the export. */
    private function range(Request $request): array
    {
        $from = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query('from')) ? (string) $request->query('from') : '';
        $to = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $request->query('to')) ? (string) $request->query('to') : '';
        $sql = ($from ? ' AND r.submitted_on >= '.DB::getPdo()->quote($from) : '').($to ? ' AND r.submitted_on <= '.DB::getPdo()->quote($to) : '');

        return [$from, $to, $sql];
    }

    /** Questions to report: those on the form now, then removed ones that still have answers. */
    private function reportQuestions(int $id, string $rangeSql): array
    {
        $answered = [];
        foreach (Surveys::rows("SELECT a.question_id, MAX(a.question_text) AS question_text, MAX(a.section) AS section
            FROM survey_answers a JOIN survey_responses r ON r.id = a.response_id
            WHERE r.survey_id = ? $rangeSql GROUP BY a.question_id", [$id]) as $row) {
            $answered[(int) $row->question_id] = $row;
        }
        $list = [];
        foreach (Surveys::questions($id, false) as $q) {
            if ($q->is_active || isset($answered[(int) $q->id])) {
                $list[] = $q;
                unset($answered[(int) $q->id]);
            }
        }
        foreach ($answered as $qid => $row) {   // question deleted entirely: use the saved text
            $list[] = (object) ['id' => $qid, 'section' => $row->section, 'question' => $row->question_text, 'type' => 'text', 'options' => '', 'is_active' => 0];
        }

        return $list;
    }

    public function results(Request $request)
    {
        $this->guard();
        if ($err = $this->setupError()) {
            return $this->back('admin/surveys', $err, 'danger');
        }
        $id = (int) $request->query('id');
        $survey = Surveys::one('SELECT * FROM surveys WHERE id = ?', [$id]);
        if (! $survey) {
            return $this->back('admin/surveys', 'Survey not found.', 'danger');
        }
        [$from, $to, $rangeSql] = $this->range($request);
        $questions = $this->reportQuestions($id, $rangeSql);

        if ($request->query('export') === 'csv') {
            return $this->export($survey, $questions, $rangeSql);
        }

        $total = (int) Surveys::one("SELECT COUNT(*) AS n FROM survey_responses r WHERE r.survey_id = ? $rangeSql", [$id])->n;
        $rows = Surveys::rows("SELECT a.question_id, a.answer FROM survey_answers a JOIN survey_responses r ON r.id = a.response_id WHERE r.survey_id = ? $rangeSql ORDER BY r.id DESC", [$id]);
        $by = [];
        foreach ($rows as $row) {
            $by[(int) $row->question_id][] = (string) $row->answer;
        }

        $summary = [];
        $sectionScores = [];
        foreach ($questions as $q) {
            $answers = $by[(int) $q->id] ?? [];
            $item = ['q' => $q, 'count' => count($answers)];
            if ($q->type === 'likert') {
                $dist = array_fill(1, 5, 0);
                foreach ($answers as $a) {
                    $dist[(int) $a] = ($dist[(int) $a] ?? 0) + 1;
                }
                $item['dist'] = $dist;
                $item['avg'] = $answers ? round(array_sum(array_map('intval', $answers)) / count($answers), 2) : null;
                $item['agree'] = $answers ? round(($dist[4] + $dist[5]) * 100 / count($answers)) : null;
                if ($item['avg'] !== null) {
                    $sectionScores[(string) $q->section][] = $item['avg'];
                }
            } elseif (in_array($q->type, Surveys::CHOICE_TYPES, true)) {
                $counts = array_fill_keys(Surveys::options($q->options), 0);
                foreach ($answers as $a) {
                    foreach ($q->type === 'multiple' ? explode('; ', $a) : [$a] as $v) {
                        $counts[$v] = ($counts[$v] ?? 0) + 1;
                    }
                }
                $item['counts'] = $counts;
            } else {
                $item['texts'] = array_slice($answers, 0, 200);
            }
            $summary[] = $item;
        }
        $sections = [];
        foreach ($sectionScores as $name => $avgs) {
            $sections[$name] = round(array_sum($avgs) / count($avgs), 2);
        }

        return view('admin.survey-results', [
            'survey' => $survey, 'summary' => $summary, 'total' => $total, 'sections' => $sections,
            'from' => $from, 'to' => $to, 'likert' => Surveys::LIKERT_LABELS,
        ]);
    }

    /** One row per response, one column per question; opens in Excel (UTF-8 CSV). */
    private function export(object $survey, array $questions, string $rangeSql)
    {
        $responses = Surveys::rows("SELECT r.id, r.submitted_on FROM survey_responses r WHERE r.survey_id = ? $rangeSql ORDER BY r.id", [$survey->id]);
        $answers = [];
        foreach (Surveys::rows("SELECT a.response_id, a.question_id, a.answer FROM survey_answers a JOIN survey_responses r ON r.id = a.response_id WHERE r.survey_id = ? $rangeSql", [$survey->id]) as $a) {
            $answers[(int) $a->response_id][(int) $a->question_id] = $a->answer;
        }
        $likert = Surveys::LIKERT_LABELS;
        $fh = fopen('php://temp', 'w+');
        fwrite($fh, "\xEF\xBB\xBF");   // so Excel reads the accents and dashes correctly
        $head = ['Response #', 'Date'];
        foreach ($questions as $q) {
            $head[] = trim(($q->section ? $q->section.' - ' : '').$q->question).($q->is_active ? '' : ' (removed)');
        }
        fputcsv($fh, $head);
        $n = 0;
        foreach ($responses as $r) {
            $line = [++$n, $r->submitted_on];
            foreach ($questions as $q) {
                $v = $answers[(int) $r->id][(int) $q->id] ?? '';
                if ($q->type === 'likert' && $v !== '') {
                    $v = $v.' - '.($likert[(int) $v - 1] ?? '');
                }
                // stop Excel treating an answer such as "=..." as a formula
                $line[] = preg_match('/^[=+\-@]/', (string) $v) && ! is_numeric($v) ? "'".$v : $v;
            }
            fputcsv($fh, $line);
        }
        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);
        $name = preg_replace('/[^A-Za-z0-9]+/', '_', $survey->title);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.trim($name, '_').'_'.date('Y-m-d').'.csv"',
        ]);
    }

    /** The form file to put on dashboard.kibokogroup.com */
    public function formFile()
    {
        $this->guard();

        return response()->download(resource_path('survey-form/satisfaction.php'), 'satisfaction.php', ['Content-Type' => 'application/octet-stream']);
    }
}
