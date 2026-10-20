<?php

/*
|--------------------------------------------------------------------------
| IT / rewards / birthday e-mails (ported from it-mailer.php)
|--------------------------------------------------------------------------
| Same templates and functions as before. Sending now goes through Laravel's mailer
| (MAIL_* settings in .env: Gmail SMTP, STARTTLS on 587) instead of PHPMailer.
| Functions that took the mysqli $conn still accept it, but ignore it.
*/

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;

defined('MAIL_FROM') || define('MAIL_FROM', env('MAIL_FROM_ADDRESS', 'kibokohr@gmail.com'));
defined('MAIL_FROM_NAME') || define('MAIL_FROM_NAME', env('MAIL_FROM_NAME', 'K-Connect'));
defined('MAIL_REPLY_TO') || define('MAIL_REPLY_TO', env('MAIL_REPLY_TO', 'it-support@kibokogroup.com'));
defined('COMPANY_URL') || define('COMPANY_URL', 'https://www.kconnect.kibokogroup.com');
defined('COMPANY_NAME') || define('COMPANY_NAME', 'Kiboko Group');
defined('COMPANY_DOMAIN') || define('COMPANY_DOMAIN', 'kibokogroup.com');

/**
 * Absolute URL of a K-Connect page for e-mail links: the current site (url()) during a request,
 * config('app.url') from the console; COMPANY_URL only if neither is available.
 */
if (! function_exists('kcSiteUrl')) {
    function kcSiteUrl(string $path = ''): string {
        try {
            $base = app()->runningInConsole() ? (string) config('app.url') : url('/');
        } catch (\Throwable $e) {
            $base = '';
        }
        $base = rtrim($base !== '' ? $base : COMPANY_URL, '/');
        return $path === '' ? $base : $base . '/' . ltrim($path, '/');
    }
}

/**
 * <li> items for the active nomination categories (nomination_categories), for the reminder e-mails.
 * Falls back to $fallback (the old fixed list) when the table is empty or cannot be read.
 */
if (! function_exists('nominationCategoryListItems')) {
    function nominationCategoryListItems(string $fallback): string {
        try {
            $rows = DB::select('SELECT name, description FROM nomination_categories WHERE status = 1 ORDER BY sort_order, name');
        } catch (\Throwable $e) {
            return $fallback;
        }
        if (empty($rows)) {
            return $fallback;
        }
        $html = '';
        foreach ($rows as $row) {
            $desc = trim(preg_replace('/\s+/', ' ', strip_tags(html_entity_decode((string) $row['description'], ENT_QUOTES, 'UTF-8'))));
            if (mb_strlen($desc) > 140) {
                $desc = rtrim(mb_substr($desc, 0, 137)) . '...';
            }
            $html .= '<li><strong>' . htmlspecialchars($row['name']) . '</strong>' . ($desc !== '' ? ' – ' . htmlspecialchars($desc) : '') . '</li>';
        }
        return $html;
    }
}

function getLogoBase64() {
    $logoPath = public_path('K-CONNECT.png');
    if (!file_exists($logoPath)) {
        foreach ([public_path('K-CONNECT.png'), public_path('kconnect.png'), public_path('logo.png'), public_path('images/logo.png')] as $alt) {
            if (file_exists($alt)) {
                $logoPath = $alt;
                break;
            }
        }
    }
    if (file_exists($logoPath)) {
        return 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
    return '';
}

/**
 * Get employee photo as base64
 */
function getEmployeePhotoBase64($photoPath) {
    if (empty($photoPath)) return '';
    $path = public_path('uploads/employees/' . $photoPath);
    if (file_exists($path)) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $path);
        finfo_close($finfo);
        return 'data:' . $mimeType . ';base64,' . base64_encode(file_get_contents($path));
    }
    return '';
}

/**
 * Core mail sender: returns true when the message was handed to the SMTP server.
 */
function sendITMail($to, $toName, $subject, $htmlBody) {
    if (empty($to)) {
        error_log("[IT Mailer] Empty email address");
        return false;
    }
    try {
        Mail::html($htmlBody, function ($message) use ($to, $toName, $subject, $htmlBody) {
            $message->from(MAIL_FROM, MAIL_FROM_NAME)
                ->replyTo(MAIL_REPLY_TO, MAIL_FROM_NAME)
                ->to($to, $toName)
                ->subject($subject);
            $message->getSymfonyMessage()->text(strip_tags(str_replace(['<br>', '<br/>', '</p>'], "\n", $htmlBody)), 'utf-8');
            $headers = $message->getSymfonyMessage()->getHeaders();
            $headers->addTextHeader('X-Mailer', 'Kiboko-IT-System');
            $headers->addTextHeader('X-Priority', '3');
        });
        error_log("[IT Mailer] ✓ Email sent to: $to");
        return true;
    } catch (\Throwable $e) {
        error_log("[IT Mailer] ✗ Failed to $to: " . $e->getMessage());
        return false;
    }
}

/**
 * Premium Email Template Wrapper with Dark Design
 */
function itMailWrapper($title, $content, $isAdmin = false) {
    $logoBase64 = getLogoBase64();
    
    // Logo HTML - with fallback
    $logoHtml = '';
    if ($logoBase64) {
        $logoHtml = '<img src="' . $logoBase64 . '" alt="' . COMPANY_NAME . '" style="height:55px; width:auto; max-width:200px; display:block; margin:0 auto;">';
    } else {
        $logoHtml = '<div style="font-size:28px; font-weight:800; color:#ffffff; letter-spacing:-1px; text-shadow:0 2px 10px rgba(0,0,0,0.15);">' . COMPANY_NAME . '</div>';
    }
    
    // Dark color palette
    $kibokoBlue = '#0082b3';
    $kibokoGreen = '#6aa61d';
    $kibokoDark = '#0f172a';
    $kibokoGray = '#475569';
    $kibokoGrayLight = '#94a3b8';
    $kibokoBorder = '#e2e8f0';
    $kibokoLight = '#f8fafc';
    
    $badge = $isAdmin ? '<span style="display:inline-block; background:' . $kibokoGreen . '; color:#fff; font-size:10px; padding:4px 16px; border-radius:20px; font-weight:700; margin-left:10px; letter-spacing:0.5px; text-transform:uppercase; vertical-align:middle;">🔒 Admin</span>' : '';
    
    return '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . '</title>
    <style>
        /* ── RESET ── */
        body, table, td, p, a, div, span {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            margin: 0;
            padding: 0;
            border: 0;
            font-size: 100%;
            vertical-align: baseline;
        }
        body {
            background: #0f172a;
            padding: 40px 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        table {
            border-collapse: collapse;
            width: 100%;
        }
        
        /* ── CONTAINER ── */
        .email-container {
            max-width: 620px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        /* ── HEADER ── */
        .email-header {
            background: linear-gradient(135deg, #0a1a2e 0%, #1a3a4e 50%, #0f2a3a 100%);
            padding: 35px 30px 28px;
            text-align: center;
            position: relative;
            border-bottom: 4px solid #6aa61d;
        }
        .email-header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0082b3, #6aa61d, #0082b3);
            background-size: 200% 100%;
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%, 100% { background-position: -200% 0; }
            50% { background-position: 200% 0; }
        }
        .email-header .logo {
            display: block;
            margin: 0 auto 12px;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin: 6px 0 4px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .email-header .subtitle {
            color: rgba(255,255,255,0.7);
            font-size: 13px;
            font-weight: 400;
            margin-top: 4px;
            letter-spacing: 0.8px;
        }
        .email-header .badge-wrap {
            margin-top: 10px;
        }
        .email-header .badge-wrap span {
            display: inline-block;
            background: rgba(255,255,255,0.1);
            color: rgba(255,255,255,0.8);
            font-size: 10px;
            font-weight: 600;
            padding: 4px 16px;
            border-radius: 20px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: 1px solid rgba(255,255,255,0.05);
        }
        
        /* ── BODY ── */
        .email-body {
            padding: 35px 35px 25px;
            background: #ffffff;
        }
        .email-body .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #0f172a;
            margin-bottom: 14px;
        }
        .email-body .greeting strong {
            color: #0082b3;
        }
        .email-body p {
            font-size: 15px;
            line-height: 1.8;
            color: #475569;
            margin-bottom: 16px;
        }
        .email-body p strong {
            color: #0f172a;
        }
        
        /* ── DIVIDER ── */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #0082b3, #6aa61d, transparent);
            margin: 28px 0;
            opacity: 0.25;
        }
        .divider-light {
            height: 1px;
            background: #e2e8f0;
            margin: 20px 0;
        }
        
        /* ── INFO CARDS ── */
        .info-card {
            background: #f8fafc;
            border-radius: 16px;
            padding: 20px 24px;
            margin: 20px 0;
            border: 1px solid #eef2f6;
        }
        .info-card.blue {
            border-left: 4px solid #0082b3;
        }
        .info-card.green {
            border-left: 4px solid #6aa61d;
        }
        .info-card.gold {
            border-left: 4px solid #f59e0b;
        }
        .info-card.purple {
            border-left: 4px solid #8b5cf6;
        }
        .info-card h4 {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .info-card p {
            font-size: 14px;
            line-height: 1.6;
            color: #475569;
            margin-bottom: 4px;
        }
        .info-card ul {
            margin: 8px 0 0;
            padding-left: 0;
            list-style: none;
        }
        .info-card ul li {
            padding: 5px 0 5px 28px;
            font-size: 14px;
            color: #475569;
            position: relative;
            border-bottom: 1px solid #f1f5f9;
        }
        .info-card ul li:last-child {
            border-bottom: none;
        }
        .info-card ul li::before {
            content: "✦";
            color: #0082b3;
            font-weight: 700;
            position: absolute;
            left: 0;
            font-size: 14px;
        }
        .info-card ul li .icon {
            position: absolute;
            left: 0;
            font-size: 16px;
        }
        
        /* ── BIRTHDAY CARDS ── */
        .birthday-grid {
            margin: 15px 0 5px;
        }
        .birthday-card {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 18px;
            background: #ffffff;
            border-radius: 14px;
            margin-bottom: 10px;
            border: 1px solid #eef2f6;
            transition: all 0.2s;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }
        .birthday-card .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0082b3, #6aa61d);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
            flex-shrink: 0;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,130,179,0.2);
        }
        .birthday-card .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .birthday-card .info {
            flex: 1;
        }
        .birthday-card .info strong {
            display: block;
            font-size: 15px;
            color: #0f172a;
            font-weight: 600;
        }
        .birthday-card .info span {
            font-size: 13px;
            color: #94a3b8;
        }
        .birthday-card .date {
            font-size: 13px;
            color: #94a3b8;
            font-weight: 500;
            flex-shrink: 0;
            background: #f8fafc;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .birthday-card .badge-birthday {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: #fff;
            font-size: 10px;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* ── BUTTONS ── */
        .btn-primary {
            display: inline-block;
            background: linear-gradient(135deg, #0082b3, #6aa61d);
            color: #ffffff !important;
            font-weight: 600;
            font-size: 15px;
            padding: 14px 36px;
            border-radius: 50px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(0,130,179,0.35);
            border: none;
            margin: 8px 0;
            letter-spacing: 0.3px;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,130,179,0.45);
        }
        .btn-secondary {
            display: inline-block;
            background: #f8fafc;
            color: #0f172a !important;
            font-weight: 600;
            font-size: 14px;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
            margin: 8px 0;
        }
        .btn-secondary:hover {
            background: #f1f5f9;
            border-color: #0082b3;
        }
        .text-center {
            text-align: center;
        }
        
        /* ── STATUS BADGES ── */
        .status-badge {
            display: inline-block;
            padding: 4px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.3px;
        }
        .status-badge.success { background: #dcfce7; color: #166534; }
        .status-badge.warning { background: #fef3c7; color: #92400e; }
        .status-badge.info { background: #dbeafe; color: #1e40af; }
        .status-badge.danger { background: #fee2e2; color: #991b1b; }
        .status-badge.primary { background: #dbeafe; color: #1e40af; }
        
        /* ── FOOTER ── */
        .email-footer {
            background: #f8fafc;
            padding: 25px 35px;
            text-align: center;
            border-top: 1px solid #eef2f6;
        }
        .email-footer p {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.6;
            margin: 0;
        }
        .email-footer .footer-divider {
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, #0082b3, #6aa61d);
            margin: 14px auto;
            border-radius: 2px;
        }
        .email-footer .footer-links {
            margin-top: 10px;
        }
        .email-footer .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 12px;
            margin: 0 10px;
            transition: color 0.2s;
        }
        .email-footer .footer-links a:hover {
            color: #0082b3;
        }
        
        /* ── TICKET DETAILS ── */
        .ticket-detail {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f1f5f9;
        }
        .ticket-detail:last-child {
            border-bottom: none;
        }
        .ticket-detail .label {
            font-weight: 600;
            color: #0f172a;
            font-size: 14px;
        }
        .ticket-detail .value {
            color: #475569;
            font-size: 14px;
        }
        
        /* ── RESPONSIVE ── */
        @media only screen and (max-width: 600px) {
            body { padding: 15px 10px; }
            .email-body { padding: 25px 18px; }
            .email-header { padding: 25px 18px 22px; }
            .email-footer { padding: 20px; }
            .btn-primary, .btn-secondary { display: block; width: 100%; text-align: center; padding: 14px 20px; }
            .birthday-card { flex-wrap: wrap; gap: 10px; padding: 12px 14px; }
            .birthday-card .date { margin-left: 0; }
            .info-card { padding: 16px 18px; }
            .ticket-detail { flex-direction: column; gap: 2px; }
            .email-header .logo img { max-width: 140px; }
        }
    </style>
</head>
<body>
    <table cellpadding="0" cellspacing="0" align="center">
        <tr>
            <td align="center">
                <div class="email-container">
                    <!-- HEADER -->
                    <div class="email-header">
                        <div class="logo">
                            ' . $logoHtml . '
                        </div>
                        <h1>' . COMPANY_NAME . '</h1>
                        <div class="subtitle">Connecting People · Empowering Growth</div>
                        <div class="badge-wrap">
                            <span>' . ($isAdmin ? '🔒 Admin Notification' : '📬 Official Communication') . '</span>
                            ' . $badge . '
                        </div>
                    </div>
                    
                    <!-- BODY -->
                    <div class="email-body">
                        ' . $content . '
                    </div>
                    
                    <!-- FOOTER -->
                    <div class="email-footer">
                        <div class="footer-divider"></div>
                        <p>
                            <strong style="color:#0f172a;">' . COMPANY_NAME . '</strong><br>
                            This is an automated message from our internal communication system.
                        </p>
                        <div class="footer-links">
                            <a href="' . kcSiteUrl() . '">Visit K-Connect</a>
                        </div>
                        <p style="font-size:11px; color:#cbd5e1; margin-top:10px;">
                            © ' . date('Y') . ' ' . COMPANY_NAME . '. All rights reserved.
                        </p>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</body>
</html>';
}

// ============================================================
// QUARTERLY NOMINATION REMINDER EMAILS
// ============================================================
function sendQuarterlyNominationReminder($employeeEmail, $employeeName, $quarterNum, $year) {
    if (empty($employeeEmail)) return false;
    
    $quarterNames = [
        1 => 'Q1 (January - March)',
        2 => 'Q2 (April - June)',
        3 => 'Q3 (July - September)',
        4 => 'Q4 (October - December)'
    ];
    
    $quarterName = $quarterNames[$quarterNum] ?? "Q{$quarterNum}";
    $nominationUrl = kcSiteUrl('rewards/nomination');
    
    $content = '
        <div class="greeting">Dear <strong>' . htmlspecialchars($employeeName) . '</strong>,</div>
        
        <p>We noticed you haven\'t submitted a nomination for the <strong>' . $quarterName . ' ' . $year . '</strong> Rewards & Recognition program.</p>
        
        <div class="info-card blue">
            <h4>📢 Nominations are still open!</h4>
            <p>Recognize your colleagues who demonstrate excellence, teamwork, and dedication.</p>
            <p style="margin-top:8px;"><strong>Available Categories:</strong></p>
            <ul>
                ' . nominationCategoryListItems('<li><strong>🏆 Sanyu Spotlight</strong> – Celebrate a colleague who brings positivity and team spirit</li>
                <li><strong>📈 Business Catalyst</strong> – For driving business growth and innovation</li>
                <li><strong>⭐ Priority Performer</strong> – For measurable contributions to Growth, Cost Savings, or Cash Generation</li>') . '
            </ul>
        </div>
        
        <div class="divider"></div>
        
        <div class="text-center">
            <a href="' . $nominationUrl . '" class="btn-primary">🏆 Submit Your Nomination Now</a>
        </div>
        
        <p style="font-size:14px; color:#94a3b8; text-align:center; margin-top:18px;">
            💡 Don\'t miss this opportunity to recognize your deserving colleagues!
        </p>
    ';
    
    return sendITMail(
        $employeeEmail,
        $employeeName,
        "🏆 Nomination Reminder: {$quarterName} {$year}",
        itMailWrapper("Rewards & Recognition - Nomination Reminder", $content)
    );
}

function sendBulkQuarterlyReminders($conn, $quarterNum, $year, $adminEmail) {
    $sent = 0;
    $failed = 0;
    $errors = [];
    $sentList = [];
    
    $query = "SELECT id, employee_code, full_name, email 
              FROM employees 
              WHERE status = 1 
              AND email IS NOT NULL 
              AND email != ''";
    try {
        $result = DB::select($query);
    } catch (\Throwable $e) {
        return ['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()];
    }
    
    $submittedQuery = "SELECT DISTINCT nominator_id 
                       FROM nominations 
                       WHERE quarter = ? 
                       AND year = ? 
                       AND status IN ('pending', 'reviewed', 'approved', 'rejected')";
    $submittedIds = [];
    foreach (DB::select($submittedQuery, [$quarterNum, $year]) as $row) {
        $submittedIds[] = $row['nominator_id'];
    }
    
    foreach ($result as $employee) {
        if (in_array($employee['id'], $submittedIds)) {
            continue;
        }
        
        $emailSent = sendQuarterlyNominationReminder(
            $employee['email'],
            $employee['full_name'],
            $quarterNum,
            $year
        );
        
        if ($emailSent) {
            $sent++;
            $sentList[] = $employee['full_name'] . ' (' . $employee['email'] . ')';
        } else {
            $failed++;
            $errors[] = $employee['full_name'] . ' (' . $employee['email'] . ')';
        }
    }
    
    if ($adminEmail) {
        sendQuarterlyReminderReport($adminEmail, $sent, $failed, $sentList, $errors, $quarterNum, $year);
    }
    
    return [
        'success' => true,
        'sent' => $sent,
        'failed' => $failed,
        'sent_list' => $sentList,
        'errors' => $errors
    ];
}

function sendQuarterlyReminderReport($adminEmail, $sent, $failed, $sentList, $errors, $quarterNum, $year) {
    $quarterNames = [
        1 => 'Q1 (January - March)',
        2 => 'Q2 (April - June)',
        3 => 'Q3 (July - September)',
        4 => 'Q4 (October - December)'
    ];
    $quarterName = $quarterNames[$quarterNum] ?? "Q{$quarterNum}";
    
    $sentHtml = !empty($sentList) ? '<ul style="list-style:none; padding:0; margin:0;">' . implode('', array_map(function($name) {
        return '<li style="padding:6px 0; border-bottom:1px solid #eef2f6;">✅ ' . htmlspecialchars($name) . '</li>';
    }, $sentList)) . '</ul>' : '<p style="color:#94a3b8;">No emails sent.</p>';
    
    $errorsHtml = !empty($errors) ? '<ul style="list-style:none; padding:0; margin:0; color:#dc3545;">' . implode('', array_map(function($name) {
        return '<li style="padding:6px 0; border-bottom:1px solid #fee2e2;">❌ ' . htmlspecialchars($name) . '</li>';
    }, $errors)) . '</ul>' : '<p style="color:#6aa61d;">✅ None.</p>';
    
    $content = '
        <div class="greeting">📊 <strong>Quarterly Reminder Report</strong></div>
        
        <div class="info-card blue">
            <h4>📋 Summary</h4>
            <p><strong>Period:</strong> ' . $quarterName . ' ' . $year . '</p>
            <p><strong>✅ Successfully Sent:</strong> <span style="color:#6aa61d; font-weight:700;">' . $sent . '</span></p>
            <p><strong>❌ Failed:</strong> <span style="color:#dc3545; font-weight:700;">' . $failed . '</span></p>
        </div>
        
        <div class="info-card green">
            <h4>✅ Sent to:</h4>
            ' . $sentHtml . '
        </div>
        ' . ($failed > 0 ? '<div class="info-card gold"><h4>❌ Failed:</h4>' . $errorsHtml . '</div>' : '') . '
    ';
    
    sendITMail(
        $adminEmail,
        'Admin',
        "📊 Reminder Report: {$quarterName} {$year}",
        itMailWrapper("Quarterly Reminder Report", $content, true)
    );
}

// ============================================================
// BIRTHDAY NOTIFICATIONS WITH PHOTOS
// ============================================================
function sendBirthdayNotification($employeeEmail, $employeeName, $birthdayPeople, $monthName) {
    if (empty($employeeEmail)) return false;
    
    $birthdayHtml = '';
    foreach ($birthdayPeople as $person) {
        // Try to get employee photo
        $photoHtml = '';
        if (!empty($person['photo'])) {
            $photoBase64 = getEmployeePhotoBase64($person['photo']);
            if ($photoBase64) {
                $photoHtml = '<img src="' . $photoBase64 . '" alt="' . htmlspecialchars($person['full_name']) . '" style="width:48px; height:48px; border-radius:50%; object-fit:cover;">';
            }
        }
        
        // If no photo, use emoji fallback
        if (empty($photoHtml)) {
            $photoHtml = '🎂';
        }
        
        $birthdayHtml .= '
            <div class="birthday-card">
                <div class="avatar">' . $photoHtml . '</div>
                <div class="info">
                    <strong>' . htmlspecialchars($person['full_name']) . '</strong>
                    <span>' . htmlspecialchars($person['department'] ?? '') . '</span>
                </div>
                <div class="date">' . date('M d', strtotime($person['birth_date'])) . '</div>
            </div>
        ';
    }
    
    $content = '
        <div class="greeting">Dear <strong>' . htmlspecialchars($employeeName) . '</strong>,</div>
        
        <p>🎉 Here are our colleagues celebrating their birthdays in <strong>' . $monthName . '</strong>!</p>
        
        <div class="info-card purple">
            <h4>🎂 Birthday Celebrants</h4>
            <div class="birthday-grid">
                ' . ($birthdayHtml ?: '<p style="color:#94a3b8; text-align:center; padding:20px 0;">No birthdays found for this month.</p>') . '
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="text-center">
            <a href="' . kcSiteUrl('') . '" class="btn-primary">🎊 View All Birthdays</a>
        </div>
        
        <p style="font-size:14px; color:#94a3b8; text-align:center; margin-top:16px;">
            💝 Don\'t forget to wish them a happy birthday!
        </p>
    ';
    
    return sendITMail(
        $employeeEmail,
        $employeeName,
        "🎂 Happy Birthday to Our Team - {$monthName}!",
        itMailWrapper("Birthday Celebrations - {$monthName}", $content)
    );
}

function sendBulkBirthdayNotifications($conn, $month = null, $adminEmail = null) {
    if ($month === null) {
        $month = date('m');
    }
    
    $monthName = date('F', mktime(0, 0, 0, $month, 1));
    $sent = 0;
    $failed = 0;
    $birthdayPeople = [];
    $errors = [];
    $sentList = [];
    
    // Get birthdays with employee photos
    $query = "SELECT b.full_name, b.department, b.birth_date, e.photo
              FROM birthdays b
              LEFT JOIN employees e ON b.full_name = e.full_name
              WHERE MONTH(b.birth_date) = ? 
              AND b.status = 1
              ORDER BY DAY(b.birth_date) ASC";
    try {
        $result = DB::select($query, [$month]);
    } catch (\Throwable $e) {
        return ['success' => false, 'error' => 'Database query failed: ' . $e->getMessage()];
    }
    
    foreach ($result as $row) {
        $birthdayPeople[] = $row;
    }
    
    if (empty($birthdayPeople)) {
        return ['success' => true, 'sent' => 0, 'failed' => 0, 'message' => "No birthdays found for {$monthName}"];
    }
    
    $employeesQuery = "SELECT id, employee_code, full_name, email 
                       FROM employees 
                       WHERE status = 1 
                       AND email IS NOT NULL 
                       AND email != ''";
    foreach (DB::select($employeesQuery) as $employee) {
        $emailSent = sendBirthdayNotification(
            $employee['email'],
            $employee['full_name'],
            $birthdayPeople,
            $monthName
        );
        
        if ($emailSent) {
            $sent++;
            $sentList[] = $employee['full_name'] . ' (' . $employee['email'] . ')';
        } else {
            $failed++;
            $errors[] = $employee['full_name'] . ' (' . $employee['email'] . ')';
        }
    }
    
    if ($adminEmail) {
        sendBirthdayReport($adminEmail, $sent, $failed, $sentList, $errors, $monthName, $birthdayPeople);
    }
    
    return [
        'success' => true,
        'sent' => $sent,
        'failed' => $failed,
        'birthday_people' => $birthdayPeople,
        'sent_list' => $sentList,
        'errors' => $errors
    ];
}

function sendBirthdayReport($adminEmail, $sent, $failed, $sentList, $errors, $monthName, $birthdayPeople) {
    $birthdayHtml = '';
    foreach ($birthdayPeople as $person) {
        $birthdayHtml .= '<li style="padding:6px 0; border-bottom:1px solid #eef2f6;">🎂 ' . htmlspecialchars($person['full_name']) . ' (' . date('M d', strtotime($person['birth_date'])) . ')</li>';
    }
    
    $content = '
        <div class="greeting">📊 <strong>Birthday Notification Report - ' . $monthName . '</strong></div>
        
        <div class="info-card blue">
            <h4>📋 Summary</h4>
            <p><strong>🎂 Total Birthdays:</strong> <span style="color:#f59e0b; font-weight:700;">' . count($birthdayPeople) . '</span></p>
            <p><strong>✅ Successfully Sent:</strong> <span style="color:#6aa61d; font-weight:700;">' . $sent . '</span></p>
            <p><strong>❌ Failed:</strong> <span style="color:#dc3545; font-weight:700;">' . $failed . '</span></p>
        </div>
        
        <div class="info-card green">
            <h4>🎂 Birthday Celebrants:</h4>
            <ul style="list-style:none; padding:0; margin:0;">' . $birthdayHtml . '</ul>
        </div>
        ' . ($failed > 0 ? '<div class="info-card gold"><h4>❌ Failed deliveries:</h4><ul style="list-style:none; padding:0; margin:0; color:#dc3545;">' . implode('', array_map(function($name) {
            return '<li style="padding:6px 0; border-bottom:1px solid #fee2e2;">❌ ' . htmlspecialchars($name) . '</li>';
        }, $errors)) . '</ul></div>' : '') . '
    ';
    
    sendITMail(
        $adminEmail,
        'Admin',
        "📊 Birthday Report - {$monthName}",
        itMailWrapper("Birthday Notification Report", $content, true)
    );
}

// ============================================================
// IT SUPPORT NOTIFICATIONS (Premium Design)
// ============================================================
function notifyNewTicket($ticket, $employeeEmail, $employeeName, $adminEmails) {
    $tn = $ticket['ticket_number'];
    $sub = $ticket['subject'];
    $priority = $ticket['priority'];
    $priorityColors = ['Low'=>'#6c757d', 'Medium'=>'#0082b3', 'High'=>'#f59e0b', 'Urgent'=>'#dc3545'];
    $priorityColor = $priorityColors[$priority] ?? '#6c757d';
    $priorityBadge = ['Low'=>'info', 'Medium'=>'primary', 'High'=>'warning', 'Urgent'=>'danger'];
    $badgeClass = $priorityBadge[$priority] ?? 'info';
    
    // To employee (always sent)
    if (!empty($employeeEmail)) {
        $content = '
            <div class="greeting">Dear <strong>' . htmlspecialchars($employeeName) . '</strong>,</div>
            
            <p>Your IT support ticket has been submitted successfully.</p>
            
            <div class="info-card blue">
                <h4>🎫 Ticket Details</h4>
                <div class="ticket-detail">
                    <span class="label">Ticket #</span>
                    <span class="value"><strong>' . $tn . '</strong></span>
                </div>
                <div class="ticket-detail">
                    <span class="label">Subject</span>
                    <span class="value">' . htmlspecialchars($sub) . '</span>
                </div>
                <div class="ticket-detail">
                    <span class="label">Priority</span>
                    <span class="value"><span class="status-badge ' . $badgeClass . '" style="background:' . $priorityColor . '20; color:' . $priorityColor . ';">' . $priority . '</span></span>
                </div>
                <div class="ticket-detail">
                    <span class="label">Assigned To</span>
                    <span class="value"><strong>' . htmlspecialchars($ticket['assigned_to_name'] ?? 'Not Assigned') . '</strong></span>
                </div>
            </div>
            
            <div class="text-center">
                <a href="' . kcSiteUrl('it-support') . '" class="btn-primary">🔍 View Your Tickets</a>
            </div>
        ';
        sendITMail($employeeEmail, $employeeName, "✅ Ticket #{$tn} Received", itMailWrapper("Ticket Confirmation", $content));
    }
    
    // To assigned admin ONLY (not all admins)
    foreach ((array)$adminEmails as $adminEmail) {
        if (!empty($adminEmail)) {
            $content = '
                <div class="greeting">🆕 <strong>New IT Support Ticket Assigned to You</strong></div>
                
                <div class="info-card gold">
                    <h4>🚨 Action Required</h4>
                    <div class="ticket-detail">
                        <span class="label">Ticket #</span>
                        <span class="value"><strong>' . $tn . '</strong></span>
                    </div>
                    <div class="ticket-detail">
                        <span class="label">From</span>
                        <span class="value">' . htmlspecialchars($employeeName) . '</span>
                    </div>
                    <div class="ticket-detail">
                        <span class="label">Subject</span>
                        <span class="value">' . htmlspecialchars($sub) . '</span>
                    </div>
                    <div class="ticket-detail">
                        <span class="label">Priority</span>
                        <span class="value"><span class="status-badge ' . $badgeClass . '" style="background:' . $priorityColor . '20; color:' . $priorityColor . ';">' . $priority . '</span></span>
                    </div>
                </div>
                
                <div class="divider"></div>
                
                <p style="font-size:14px; color:#94a3b8; text-align:center;">
                    ⏰ Please respond to this ticket as soon as possible.
                </p>
                
                <div class="text-center">
                    <a href="' . kcSiteUrl('admin/it-admin') . '" class="btn-primary">🔧 Manage Ticket</a>
                </div>
            ';
            sendITMail($adminEmail, "IT Admin", "🆕 NEW Ticket #{$tn} Assigned to You", itMailWrapper("New Ticket Alert", $content, true));
        }
    }
}

function notifyStatusUpdate($ticket, $employeeEmail, $employeeName, $adminNotes = '') {
    if (empty($employeeEmail)) return;
    
    $tn = $ticket['ticket_number'];
    $status = $ticket['status'];
    $statusColors = ['New'=>'#0082b3', 'In Progress'=>'#f59e0b', 'Resolved'=>'#6aa61d', 'Closed'=>'#6c757d', 'Rejected'=>'#dc3545'];
    $statusColor = $statusColors[$status] ?? '#6c757d';
    $statusBadge = ['New'=>'info', 'In Progress'=>'warning', 'Resolved'=>'success', 'Closed'=>'secondary', 'Rejected'=>'danger'];
    $badgeClass = $statusBadge[$status] ?? 'info';
    
    $content = '
        <div class="greeting">Dear <strong>' . htmlspecialchars($employeeName) . '</strong>,</div>
        
        <p>Your ticket status has been updated to:</p>
        
        <div style="text-align:center; padding:15px 0;">
            <span class="status-badge ' . $badgeClass . '" style="background:' . $statusColor . '20; color:' . $statusColor . '; font-size:16px; padding:8px 24px;">' . $status . '</span>
        </div>
        
        ' . ($adminNotes ? '<div class="info-card blue"><h4>📝 Admin Note:</h4><p>' . nl2br(htmlspecialchars($adminNotes)) . '</p></div>' : '') . '
        
        <div class="divider"></div>
        
        <div class="text-center">
            <a href="' . kcSiteUrl('it-support') . '" class="btn-primary">🔍 View Ticket</a>
        </div>
    ';
    sendITMail($employeeEmail, $employeeName, "📊 Ticket #{$tn} Status: {$status}", itMailWrapper("Ticket Status Update", $content));
}

function notifyNewComment($ticket, $commentText, $commenterName, $isAdminComment, $employeeEmail, $employeeName, $conn = null) {
    $tn = $ticket['ticket_number'];
    
    if ($isAdminComment && !empty($employeeEmail)) {
        // Admin replied - send to employee only
        $content = '
            <div class="greeting">Dear <strong>' . htmlspecialchars($employeeName) . '</strong>,</div>
            
            <p>💬 IT Support has replied to your ticket.</p>
            
            <div class="info-card blue">
                <h4>💬 Reply from IT Support</h4>
                <p style="font-size:15px; background:#f8fafc; padding:16px; border-radius:12px; margin:8px 0 0;">' . nl2br(htmlspecialchars($commentText)) . '</p>
                <p style="font-size:13px; color:#94a3b8; margin-top:8px;">— <strong>IT Support Team</strong></p>
            </div>
            
            <div class="divider"></div>
            
            <div class="text-center">
                <a href="' . kcSiteUrl('it-support') . '" class="btn-primary">💬 View & Reply</a>
            </div>
        ';
        sendITMail($employeeEmail, $employeeName, "💬 Reply on Ticket #{$tn}", itMailWrapper("New Reply", $content));
        
    } elseif (!$isAdminComment) {
        // Employee replied - send ONLY to the assigned admin
        $assignedAdminEmail = '';
        if (!empty($ticket['assigned_to'])) {
            if ($adminRow = DB::selectOne("SELECT email FROM employees WHERE id = " . (int) $ticket['assigned_to'])) {
                $assignedAdminEmail = $adminRow['email'];
            }
        }
        
        // Only send to the assigned admin
        if (!empty($assignedAdminEmail)) {
            $content = '
                <div class="greeting">💬 <strong>New Reply on Ticket #' . $tn . '</strong></div>
                
                <p><strong>' . htmlspecialchars($employeeName) . '</strong> has replied to the ticket.</p>
                
                <div class="info-card gold">
                    <h4>💬 Employee Reply</h4>
                    <p style="font-size:15px; background:#f8fafc; padding:16px; border-radius:12px; margin:8px 0 0;">' . nl2br(htmlspecialchars($commentText)) . '</p>
                    <p style="font-size:13px; color:#94a3b8; margin-top:8px;">— <strong>' . htmlspecialchars($employeeName) . '</strong></p>
                </div>
                
                <div class="divider"></div>
                
                <div class="text-center">
                    <a href="' . kcSiteUrl('admin/it-admin') . '" class="btn-primary">💬 View & Respond</a>
                </div>
            ';
            sendITMail($assignedAdminEmail, "IT Admin", "💬 Employee Reply #{$tn}", itMailWrapper("New Reply", $content, true));
        }
    }
}

function priorityColor($priority) {
    $map = ['Low'=>'#6c757d', 'Medium'=>'#0082b3', 'High'=>'#f59e0b', 'Urgent'=>'#dc3545'];
    return $map[$priority] ?? '#6c757d';
}
// ============================================================
// NOMINATION THANK YOU EMAIL
// ============================================================
function sendNominationThankYouEmail($nominatorEmail, $nominatorName, $categoryName, $nomineeName, $quarter, $year, $nominationId) {
    if (empty($nominatorEmail)) return false;
    
    $quarterNames = [
        1 => 'Q1 (January - March)',
        2 => 'Q2 (April - June)',
        3 => 'Q3 (July - September)',
        4 => 'Q4 (October - December)'
    ];
    $quarterName = $quarterNames[$quarter] ?? "Q{$quarter}";
    
    $trackingUrl = kcSiteUrl('rewards/nomination');
    
    $content = '
        <div class="greeting">Dear <strong>' . htmlspecialchars($nominatorName) . '</strong>,</div>
        
        <p>Thank you for submitting a nomination for the <strong>Rewards & Recognition</strong> program.</p>
        
        <div class="info-card green">
            <h4>✅ Nomination Received</h4>
            <div class="ticket-detail">
                <span class="label">Nominee</span>
                <span class="value"><strong>' . htmlspecialchars($nomineeName) . '</strong></span>
            </div>
            <div class="ticket-detail">
                <span class="label">Category</span>
                <span class="value"><strong>' . htmlspecialchars($categoryName) . '</strong></span>
            </div>
            <div class="ticket-detail">
                <span class="label">Period</span>
                <span class="value"><strong>' . $quarterName . ' ' . $year . '</strong></span>
            </div>
            <div class="ticket-detail">
                <span class="label">Reference #</span>
                <span class="value"><strong>NOM-' . str_pad($nominationId, 6, '0', STR_PAD_LEFT) . '</strong></span>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <p style="font-size:14px; color:#475569;">
            Your nomination is now under review by the committee. You will be notified once the review process is complete.
        </p>
        
        <div class="info-card blue" style="background:#f0f9ff; border-left-color:#0082b3;">
            <h4>💡 What Happens Next?</h4>
            <ul>
                <li>Your nomination will be reviewed by the committee</li>
                <li>You may be contacted for additional information if needed</li>
                <li>Winners will be announced at the end of the quarter</li>
            </ul>
        </div>
        
        <div class="divider"></div>
        
        <div class="text-center">
            <a href="' . $trackingUrl . '" class="btn-primary">🏆 View All Categories</a>
        </div>
        
        <p style="font-size:14px; color:#94a3b8; text-align:center; margin-top:18px;">
            🌟 Your recognition matters. Thank you for being part of our culture of appreciation!
        </p>
    ';
    
    return sendITMail(
        $nominatorEmail,
        $nominatorName,
        "✅ Nomination Received: {$categoryName} - {$quarterName} {$year}",
        itMailWrapper("Nomination Confirmation", $content)
    );
}

// ============================================================
// NOMINATION NOTIFICATION TO COMMITTEE
// ============================================================
function sendNominationNotificationToCommittee($committeeEmail, $committeeName, $nominatorName, $nomineeName, $categoryName, $quarter, $year, $nominationId) {
    if (empty($committeeEmail)) return false;
    
    $quarterNames = [
        1 => 'Q1 (January - March)',
        2 => 'Q2 (April - June)',
        3 => 'Q3 (July - September)',
        4 => 'Q4 (October - December)'
    ];
    $quarterName = $quarterNames[$quarter] ?? "Q{$quarter}";
    
    $adminUrl = kcSiteUrl('rewards/committee-review');
    
    $content = '
        <div class="greeting">Dear <strong>' . htmlspecialchars($committeeName) . '</strong>,</div>
        
        <p>A new nomination has been submitted for the <strong>Rewards & Recognition</strong> program.</p>
        
        <div class="info-card gold">
            <h4>📋 New Nomination Requires Review</h4>
            <div class="ticket-detail">
                <span class="label">Reference #</span>
                <span class="value"><strong>NOM-' . str_pad($nominationId, 6, '0', STR_PAD_LEFT) . '</strong></span>
            </div>
            <div class="ticket-detail">
                <span class="label">Nominator</span>
                <span class="value">' . htmlspecialchars($nominatorName) . '</span>
            </div>
            <div class="ticket-detail">
                <span class="label">Nominee</span>
                <span class="value"><strong>' . htmlspecialchars($nomineeName) . '</strong></span>
            </div>
            <div class="ticket-detail">
                <span class="label">Category</span>
                <span class="value"><strong>' . htmlspecialchars($categoryName) . '</strong></span>
            </div>
            <div class="ticket-detail">
                <span class="label">Period</span>
                <span class="value">' . $quarterName . ' ' . $year . '</span>
            </div>
            <div class="ticket-detail">
                <span class="label">Status</span>
                <span class="value"><span class="status-badge warning">Pending Review</span></span>
            </div>
        </div>
        
        <div class="divider"></div>
        
        <div class="text-center">
            <a href="' . $adminUrl . '" class="btn-primary">🔍 Review Nomination</a>
        </div>
        
        <p style="font-size:14px; color:#94a3b8; text-align:center; margin-top:16px;">
            ⏰ Please review this nomination at your earliest convenience.
        </p>
    ';
    
    return sendITMail(
        $committeeEmail,
        $committeeName,
        "🆕 New Nomination: {$categoryName} - {$quarterName} {$year}",
        itMailWrapper("New Nomination - Committee Alert", $content, true)
    );
}

// ============================================================
// NOMINATION STATUS UPDATE EMAIL
// ============================================================
function sendNominationStatusUpdate($nominatorEmail, $nominatorName, $nomineeName, $categoryName, $status, $quarter, $year, $notes = '') {
    if (empty($nominatorEmail)) return false;
    
    $quarterNames = [
        1 => 'Q1 (January - March)',
        2 => 'Q2 (April - June)',
        3 => 'Q3 (July - September)',
        4 => 'Q4 (October - December)'
    ];
    $quarterName = $quarterNames[$quarter] ?? "Q{$quarter}";
    
    $statusColors = [
        'pending' => '#f59e0b',
        'reviewed' => '#0082b3',
        'approved' => '#6aa61d',
        'rejected' => '#dc3545'
    ];
    
    $statusBadges = [
        'pending' => 'warning',
        'reviewed' => 'info',
        'approved' => 'success',
        'rejected' => 'danger'
    ];
    
    $statusColor = $statusColors[$status] ?? '#6c757d';
    $badgeClass = $statusBadges[$status] ?? 'info';
    
    $statusMessages = [
        'pending' => 'Your nomination is pending review by the committee.',
        'reviewed' => 'Your nomination has been reviewed and is being considered.',
        'approved' => '🎉 Congratulations! Your nomination has been approved!',
        'rejected' => 'Your nomination was not selected this time. We encourage you to nominate again in the future.'
    ];
    
    $statusMessage = $statusMessages[$status] ?? 'Your nomination status has been updated.';
    
    $content = '
        <div class="greeting">Dear <strong>' . htmlspecialchars($nominatorName) . '</strong>,</div>
        
        <p>There has been an update to your nomination for the <strong>Rewards & Recognition</strong> program.</p>
        
        <div class="info-card blue">
            <h4>📊 Nomination Status Update</h4>
            <div class="ticket-detail">
                <span class="label">Nominee</span>
                <span class="value"><strong>' . htmlspecialchars($nomineeName) . '</strong></span>
            </div>
            <div class="ticket-detail">
                <span class="label">Category</span>
                <span class="value"><strong>' . htmlspecialchars($categoryName) . '</strong></span>
            </div>
            <div class="ticket-detail">
                <span class="label">Period</span>
                <span class="value">' . $quarterName . ' ' . $year . '</span>
            </div>
            <div class="ticket-detail">
                <span class="label">Status</span>
                <span class="value"><span class="status-badge ' . $badgeClass . '" style="background:' . $statusColor . '20; color:' . $statusColor . ';">' . ucfirst($status) . '</span></span>
            </div>
        </div>
        
        <p style="font-size:15px; padding:16px; background:#f8fafc; border-radius:12px; margin:16px 0;">
            ' . $statusMessage . '
        </p>
        
        ' . (!empty($notes) ? '<div class="info-card" style="border-left-color:#0082b3;"><h4>📝 Committee Notes:</h4><p>' . nl2br(htmlspecialchars($notes)) . '</p></div>' : '') . '
        
        <div class="divider"></div>
        
        <div class="text-center">
            <a href="' . kcSiteUrl('rewards/nomination') . '" class="btn-primary">📋 View My Nominations</a>
        </div>
    ';
    
    return sendITMail(
        $nominatorEmail,
        $nominatorName,
        "📊 Nomination Status Update: {$categoryName} - {$status}",
        itMailWrapper("Nomination Status Update", $content)
    );
}

// ============================================================
// BULK NOMINATION REMINDER EMAIL
// ============================================================
function sendNominationReminder($employeeEmail, $employeeName, $quarter, $year) {
    if (empty($employeeEmail)) return false;
    
    $quarterNames = [
        1 => 'Q1 (January - March)',
        2 => 'Q2 (April - June)',
        3 => 'Q3 (July - September)',
        4 => 'Q4 (October - December)'
    ];
    $quarterName = $quarterNames[$quarter] ?? "Q{$quarter}";
    
    $nominationUrl = kcSiteUrl('rewards/nomination');
    
    $content = '
        <div class="greeting">Dear <strong>' . htmlspecialchars($employeeName) . '</strong>,</div>
        
        <p>Nominations are currently open for the <strong>Rewards & Recognition</strong> program.</p>
        
        <div class="info-card blue">
            <h4>🏆 Nominate a Colleague Today!</h4>
            <p>Have you noticed a colleague who goes above and beyond? Recognize their efforts by nominating them for one of our award categories:</p>
            <ul>
                ' . nominationCategoryListItems('<li><strong>⭐ Sanyu Spotlight</strong> – For team spirit and positivity</li>
                <li><strong>📈 Business Catalyst</strong> – For driving business growth</li>
                <li><strong>🏅 Priority Performer</strong> – For measurable contributions to Growth, Cost Savings, or Cash Generation</li>') . '
            </ul>
        </div>
        
        <div class="divider"></div>
        
        <div class="text-center">
            <a href="' . $nominationUrl . '" class="btn-primary">🏆 Submit a Nomination</a>
        </div>
        
        <p style="font-size:14px; color:#94a3b8; text-align:center; margin-top:16px;">
            🗓️ Nominations close at the end of ' . $quarterName . ' ' . $year . '. Don\'t miss this opportunity to recognize your deserving colleagues!
        </p>
    ';
    
    return sendITMail(
        $employeeEmail,
        $employeeName,
        "🏆 Nominate a Colleague - {$quarterName} {$year}",
        itMailWrapper("Nomination Reminder", $content)
    );
}
