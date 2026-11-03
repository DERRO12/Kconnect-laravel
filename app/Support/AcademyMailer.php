<?php

namespace App\Support;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * K-Academy e-mails, sent through K-Connect's own mailer (the MAIL_* settings in .env,
 * the same Gmail account as the rest of K-Connect).
 *
 * Every e-mail is sent after the page has gone to the browser (app()->terminating), so a
 * slow or failing SMTP server never slows a page down, and a failure is only logged.
 * Nothing here writes to the database: in-app notifications stay where they were.
 */
class AcademyMailer
{
    /** First K-Academy visit of an employee: their K-Academy account was just created. */
    public static function welcome(array $user): void
    {
        $body = '<p>Welcome to <strong>K-Academy</strong>, the Kiboko Group learning centre inside K-Connect. '
            .'Your learning account is ready and uses your normal K-Connect login, so there is no new password to remember.</p>'
            .self::steps([
                ['Browse courses', 'Pick from the compliance, foundation, mastery and resource pathways.'],
                ['Enrol and learn', 'Watch the lessons at your own pace. Your progress is saved as you go.'],
                ['Pass the quiz', 'Finish every lesson and pass the course quiz.'],
                ['Get certified', 'HR approves your certificate and you download it from My Certificates.'],
            ]);

        self::queue($user['email'] ?? '', $user['full_name'] ?? '', 'Welcome to K-Academy', 'Welcome to K-Academy', $body,
            'Start learning', self::link('academy/browse-courses'));
    }

    /** The learner enrolled in a course. */
    public static function enrolled(int $userId, int $courseId): void
    {
        self::defer(function () use ($userId, $courseId) {
            $ka = Academy::db();
            $user = $ka->selectOne('SELECT full_name, email FROM users WHERE id = ?', [$userId]);
            $course = $ka->selectOne('SELECT c.title, c.level, c.duration, (SELECT COUNT(*) FROM lessons l WHERE l.course_id = c.id) AS lessons
                FROM courses c WHERE c.id = ?', [$courseId]);
            if (! $user || ! $course) {
                return;
            }
            $body = '<p>You are now enrolled in <strong>'.self::e($course['title']).'</strong>.</p>'
                .self::facts([
                    'Lessons' => (int) $course['lessons'],
                    'Level' => ucfirst((string) ($course['level'] ?? '')),
                    'Duration' => $course['duration'] ?? '',
                ])
                .'<p>Complete every lesson and pass the quiz to earn your certificate.</p>';
            self::send($user['email'], $user['full_name'], 'Enrolled: '.$course['title'], 'You are enrolled', $body,
                'Open the course', self::link('academy/course-overview?id='.$courseId));
        });
    }

    /** A pending certificate was just recorded: tell the learner, and the K-Academy admins. */
    public static function certificateRequested(int $certificateId): void
    {
        self::defer(function () use ($certificateId) {
            $cert = self::certificate($certificateId);
            if (! $cert) {
                return;
            }
            $body = '<p>Congratulations! You have completed <strong>'.self::e($cert['course_title']).'</strong>'
                .($cert['score'] !== null ? ' with a quiz score of <strong>'.round((float) $cert['score']).'%</strong>' : '').'.</p>'
                .'<p>Your certificate <strong>'.self::e($cert['certificate_number']).'</strong> has been sent to HR for approval. '
                .'We will e-mail you as soon as it is ready to download.</p>';
            self::send($cert['email'], $cert['full_name'], 'Course completed: '.$cert['course_title'], 'Well done!', $body,
                'View my certificates', self::link('academy/certificates'));

            $adminBody = '<p><strong>'.self::e($cert['full_name']).'</strong>'
                .($cert['employee_code'] ? ' ('.self::e($cert['employee_code']).')' : '')
                .' has completed <strong>'.self::e($cert['course_title']).'</strong> and is waiting for a certificate.</p>'
                .self::facts([
                    'Certificate' => $cert['certificate_number'],
                    'Quiz score' => $cert['score'] !== null ? round((float) $cert['score']).'%' : '',
                ]);
            foreach (Academy::db()->select("SELECT full_name, email FROM users WHERE role = 'admin' AND status = 'active'") as $admin) {
                self::send($admin['email'], $admin['full_name'], 'Certificate approval needed: '.$cert['full_name'], 'Certificate approval needed',
                    $adminBody, 'Review the certificate', self::link('academy/admin/approve-certificate?id='.$certificateId));
            }
        });
    }

    /** An admin approved the certificate. */
    public static function certificateApproved(int $certificateId): void
    {
        self::defer(function () use ($certificateId) {
            $cert = self::certificate($certificateId);
            if (! $cert) {
                return;
            }
            $body = '<p>Great news! Your certificate for <strong>'.self::e($cert['course_title']).'</strong> has been approved.</p>'
                .self::facts(['Certificate' => $cert['certificate_number']])
                .($cert['admin_notes'] ? '<p><em>'.nl2br(self::e($cert['admin_notes'])).'</em></p>' : '')
                .'<p>You can download it now and share it.</p>';
            self::send($cert['email'], $cert['full_name'], 'Your certificate is ready: '.$cert['course_title'], 'Your certificate is ready', $body,
                'Download certificate', self::link('academy/download-certificate?id='.$certificateId));
        });
    }

    /** An admin rejected the certificate. */
    public static function certificateRejected(int $certificateId): void
    {
        self::defer(function () use ($certificateId) {
            $cert = self::certificate($certificateId);
            if (! $cert) {
                return;
            }
            $body = '<p>Your certificate request for <strong>'.self::e($cert['course_title']).'</strong> was not approved this time.</p>'
                .($cert['rejection_reason'] ? '<p style="background:#fef2f2;border-left:4px solid #ef4444;padding:12px 16px;border-radius:10px;color:#7f1d1d">'
                    .nl2br(self::e($cert['rejection_reason'])).'</p>' : '')
                .'<p>Please contact HR or the K-Academy administrator if you have questions.</p>';
            self::send($cert['email'], $cert['full_name'], 'Certificate update: '.$cert['course_title'], 'Certificate update', $body,
                'Go to the course', self::link('academy/course-overview?id='.$cert['course_id']));
        });
    }

    /** A course was published: tell every active learner (BCC, in batches). */
    public static function coursePublished(int $courseId): void
    {
        self::defer(function () use ($courseId) {
            $ka = Academy::db();
            $course = $ka->selectOne("SELECT id, title, description, level, duration FROM courses WHERE id = ? AND status = 'published'", [$courseId]);
            // Once per course, and only when it has something to learn (the old site had no announcement at all).
            if (! $course || ! $ka->selectOne('SELECT id FROM lessons WHERE course_id = ? LIMIT 1', [$courseId])) {
                return;
            }
            $marker = json_encode(['course_id' => $courseId]);
            if ($ka->selectOne("SELECT id FROM activity_logs WHERE action = 'course_announced' AND details = ? LIMIT 1", [$marker])) {
                return;
            }
            Academy::log(null, 'course_announced', ['course_id' => $courseId]);
            $emails = array_values(array_filter(array_map(fn ($r) => $r['email'],
                $ka->select("SELECT email FROM users WHERE role = 'user' AND status = 'active'")), [self::class, 'deliverable']));
            if (! $emails) {
                return;
            }
            $desc = trim(strip_tags(html_entity_decode((string) $course['description'])));
            $body = '<p>A new course is available on K-Academy:</p>'
                .'<h3 style="margin:0 0 8px;color:#0f172a;font-size:20px">'.self::e($course['title']).'</h3>'
                .($desc !== '' ? '<p>'.self::e(mb_strimwidth($desc, 0, 300, '...')).'</p>' : '')
                .self::facts(['Level' => ucfirst((string) ($course['level'] ?? '')), 'Duration' => $course['duration'] ?? '']);
            $html = self::layout('New course on K-Academy', 'Hello,', $body, 'View the course', self::link('academy/course-overview?id='.$courseId));
            foreach (array_chunk($emails, 50) as $batch) {
                self::deliver(config('mail.from.address'), 'K-Academy learners', 'New course: '.$course['title'], $html, $batch);
            }
        });
    }

    // ---------------------------------------------------------------- internals

    private static function certificate(int $id): ?array
    {
        return Academy::db()->selectOne('SELECT c.id, c.certificate_number, c.course_id, c.admin_notes, c.rejection_reason,
                u.full_name, u.email, u.employee_code, co.title AS course_title, qr.score
            FROM certificates c JOIN users u ON u.id = c.user_id JOIN courses co ON co.id = c.course_id
            LEFT JOIN quiz_results qr ON qr.id = c.quiz_result_id WHERE c.id = ?', [$id]);
    }

    private static function queue(string $to, string $name, string $subject, string $heading, string $body, ?string $cta, ?string $url): void
    {
        self::defer(fn () => self::send($to, $name, $subject, $heading, $body, $cta, $url));
    }

    /** Runs after the response has been sent (straight away on the command line). */
    private static function defer(callable $job): void
    {
        $run = function () use ($job) {
            try {
                $job();
            } catch (\Throwable $e) {
                Log::warning('[K-Academy mail] '.$e->getMessage());
            }
        };
        if (app()->runningInConsole()) {
            $run();
        } else {
            app()->terminating($run);
        }
    }

    private static function send(string $to, string $name, string $subject, string $heading, string $body, ?string $cta, ?string $url): void
    {
        if (! self::deliverable($to)) {
            return;
        }
        $first = trim(explode(' ', trim($name))[0] ?? '');
        $html = self::layout($heading, 'Hello '.self::e($first !== '' ? $first : 'there').',', $body, $cta, $url);
        self::deliver($to, $name, $subject, $html);
    }

    private static function deliver(string $to, string $name, string $subject, string $html, array $bcc = []): void
    {
        $from = config('mail.from.address');
        $fromName = 'K-Academy | '.(config('mail.from.name') ?: 'K-Connect');
        $text = trim(html_entity_decode(strip_tags(preg_replace(['/<br\s*\/?>/i', '/<\/(p|h\d|tr|li)>/i', '/<a [^>]*href="([^"]+)"[^>]*>(.*?)<\/a>/is'],
            ["\n", "\n\n", '$2 ($1)'], preg_replace('/<style.*?<\/style>/is', '', $html)))));
        $text = preg_replace("/\n\s*\n+/", "\n\n", $text);
        try {
            Mail::html($html, function ($m) use ($to, $name, $subject, $from, $fromName, $text, $bcc) {
                $m->from($from, $fromName)->to($to, $name)->subject($subject);
                if ($reply = config('mail.reply_to.address')) {
                    $m->replyTo($reply);
                }
                if ($bcc) {
                    $m->bcc($bcc);
                }
                $m->getSymfonyMessage()->text($text, 'utf-8');
            });
            Log::info('[K-Academy mail] "'.$subject.'" sent to '.($bcc ? count($bcc).' learners' : $to));
        } catch (\Throwable $e) {
            Log::warning('[K-Academy mail] "'.$subject.'" to '.($bcc ? count($bcc).' learners' : $to).' failed: '.$e->getMessage());
        }
    }

    /** Real addresses only: the placeholder ...@kconnect.local accounts have no mailbox. */
    public static function deliverable(?string $email): bool
    {
        return $email !== null && filter_var($email, FILTER_VALIDATE_EMAIL) && ! str_ends_with(strtolower($email), '.local');
    }

    private static function link(string $path): string
    {
        return kcSiteUrl($path);
    }

    private static function e($v): string
    {
        return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
    }

    private static function facts(array $rows): string
    {
        $rows = array_filter($rows, fn ($v) => $v !== '' && $v !== null);
        if (! $rows) {
            return '';
        }
        $html = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;border-radius:12px;margin:18px 0">';
        foreach ($rows as $k => $v) {
            $html .= '<tr><td style="padding:10px 16px;color:#64748b;font-size:14px;border-bottom:1px solid #e2e8f0">'.self::e($k).'</td>'
                .'<td align="right" style="padding:10px 16px;color:#0f172a;font-weight:bold;font-size:14px;border-bottom:1px solid #e2e8f0">'.self::e($v).'</td></tr>';
        }

        return $html.'</table>';
    }

    private static function steps(array $steps): string
    {
        $html = '<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0">';
        foreach ($steps as $i => [$title, $text]) {
            $html .= '<tr><td width="44" valign="top" style="padding:8px 0"><div style="width:32px;height:32px;line-height:32px;border-radius:50%;'
                .'background:#009ada;background:linear-gradient(135deg,#009ada,#76bc21);color:#fff;text-align:center;font-weight:bold;font-size:14px">'.($i + 1).'</div></td>'
                .'<td valign="top" style="padding:8px 0"><strong style="color:#0f172a">'.self::e($title).'</strong><br>'
                .'<span style="color:#64748b;font-size:14px">'.self::e($text).'</span></td></tr>';
        }

        return $html.'</table>';
    }

    /** The branded e-mail: K-Connect blue to green, works in Gmail and Outlook, readable on phones. */
    private static function layout(string $heading, string $greeting, string $body, ?string $cta, ?string $url): string
    {
        $button = $cta && $url ? '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:26px auto 6px"><tr><td align="center" bgcolor="#009ada" '
            .'style="border-radius:12px;background:#009ada;background:linear-gradient(135deg,#009ada,#76bc21)">'
            .'<a href="'.self::e($url).'" style="display:inline-block;padding:14px 34px;color:#ffffff;font-weight:bold;font-size:15px;text-decoration:none;border-radius:12px">'
            .self::e($cta).'</a></td></tr></table>'
            .'<p style="text-align:center;font-size:12px;color:#94a3b8;margin:8px 0 0">Or open: <a href="'.self::e($url).'" style="color:#009ada;word-break:break-all">'.self::e($url).'</a></p>' : '';

        return '<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.self::e($heading).'</title>'
            .'<style>@media (max-width:600px){.ka-c{padding:24px 18px!important}.ka-h{padding:26px 18px!important}}</style></head>'
            .'<body style="margin:0;padding:0;background:#f4f7fb;font-family:Arial,Helvetica,sans-serif">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb;padding:24px 12px"><tr><td align="center">'
            .'<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:580px;background:#ffffff;border-radius:22px;overflow:hidden;box-shadow:0 12px 32px rgba(15,23,42,0.08)">'
            .'<tr><td class="ka-h" align="center" bgcolor="#009ada" style="padding:32px 30px;background:#009ada;background:linear-gradient(135deg,#009ada,#76bc21)">'
            .'<div style="font-size:28px;line-height:1">&#127891;</div>'
            .'<div style="color:#ffffff;font-size:24px;font-weight:bold;margin-top:8px;letter-spacing:0.3px">K-Academy</div>'
            .'<div style="color:#e0f7ff;font-size:13px;margin-top:4px">Kiboko Group learning on K-Connect</div>'
            .'</td></tr>'
            .'<tr><td class="ka-c" style="padding:34px 32px;color:#475569;font-size:15px;line-height:1.6">'
            .'<h2 style="margin:0 0 14px;color:#0f172a;font-size:22px">'.self::e($heading).'</h2>'
            .'<p style="margin:0 0 14px">'.$greeting.'</p>'.$body.$button
            .'</td></tr>'
            .'<tr><td align="center" style="background:#f8fafc;padding:18px 24px;border-top:1px solid #e2e8f0;color:#94a3b8;font-size:12px;line-height:1.5">'
            .'&copy; '.date('Y').' Kiboko Group &middot; K-Academy on K-Connect<br>This is an automated message. Sign in with your K-Connect account.'
            .'</td></tr></table></td></tr></table></body></html>';
    }
}
