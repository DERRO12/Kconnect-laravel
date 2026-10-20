<?php

namespace App\Support;

use Illuminate\Support\Facades\Mail;

/**
 * E-mails of the "people" pages: register.php sendWelcomeEmail() (was PHPMailer, now Laravel's
 * mailer with the same SMTP account) and forgot-password.php sendPasswordResetEmail()
 * (via the it-mailer functions, as before).
 */
class PeopleMailer
{
    /** register.php sendWelcomeEmail() */
    public static function sendWelcomeEmail($toEmail, $toName, $employeeCode, $password)
    {
        $loginUrl = SITE_URL.'/login.php';
        $changePasswordUrl = SITE_URL.'/change-password.php';

        $subject = '🎉 Welcome to '.SITE_NAME.' - Your Account Credentials';

        // Get logo
        $logoPath = public_path('K CONNECT PNG.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $imageData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,'.base64_encode($imageData);
        }

        $htmlBody = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Welcome to '.SITE_NAME.'</title>
    <style>
        body{font-family:Arial,sans-serif;background:#f4f7fb;margin:0;padding:20px}
        .container{max-width:560px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 20px 40px rgba(0,0,0,0.08)}
        .header{background:linear-gradient(135deg,#009ada,#76bc21);padding:30px;text-align:center}
        .header img{max-height:50px;margin-bottom:15px}
        .header h2{color:#fff;margin:0;font-size:20px}
        .content{padding:35px 30px}
        .content h3{color:#1e293b;margin-top:0;font-size:22px}
        .content p{color:#475569;line-height:1.6;margin-bottom:16px}
        .login-button{display:inline-block;background:linear-gradient(135deg,#009ada,#76bc21);color:#fff;text-decoration:none;padding:14px 40px;border-radius:12px;font-weight:600;margin:20px 0}
        .info-box{background:#f8fafc;border-left:4px solid #009ada;padding:15px 20px;border-radius:12px;margin:20px 0;font-size:14px}
        .credentials{background:#f1f5f9;padding:15px 20px;border-radius:12px;margin:15px 0}
        .credentials div{padding:6px 0;display:flex;justify-content:space-between;border-bottom:1px solid #e2e8f0}
        .credentials div:last-child{border-bottom:none}
        .credentials .label{color:#64748b;font-weight:600}
        .credentials .value{color:#1e293b;font-family:monospace;font-weight:bold;font-size:16px}
        .credentials .value.password{color:#009ada;}
        .footer{background:#f8fafc;padding:20px 30px;text-align:center;font-size:12px;color:#94a3b8;border-top:1px solid #e2e8f0}
        .warning{color:#dc2626;font-size:13px;margin-top:15px;text-align:center}
        @media (max-width:600px){.content{padding:25px 20px}.login-button{display:block;text-align:center}}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            '.($logoBase64 ? '<img src="'.$logoBase64.'" alt="'.SITE_NAME.'">' : '<h2>'.SITE_NAME.'</h2>').'
            <h2>Welcome Aboard!</h2>
        </div>
        <div class="content">
            <h3>Hello '.htmlspecialchars($toName).'!</h3>
            <p>Your account has been successfully created on <strong>'.SITE_NAME.'</strong>. Below are your login credentials.</p>

            <div class="credentials">
                <div>
                    <span class="label">Employee Code:</span>
                    <span class="value">'.htmlspecialchars($employeeCode).'</span>
                </div>
                <div>
                    <span class="label">Password:</span>
                    <span class="value password">'.htmlspecialchars($password).'</span>
                </div>
            </div>

            <div class="info-box" style="border-left-color:#f59e0b;">
                <strong>🔐 Important Security Notes:</strong>
                <ul style="margin:10px 0 0 20px;color:#475569;">
                    <li>Keep your password secure and never share it with anyone</li>
                    <li><strong>For security, please change your password after first login</strong></li>
                    <li>If you didn\'t register, please contact IT support immediately</li>
                </ul>
            </div>

            <div style="text-align:center">
                <a href="'.$loginUrl.'" class="login-button">🔑 Login Now</a>
            </div>

            <p style="text-align:center;font-size:14px;color:#64748b;margin-top:10px;">
                After logging in, go to <a href="'.$changePasswordUrl.'" style="color:#009ada;">Change Password</a> to set your own password.
            </p>

            <div class="warning">
                <small>⚠️ This email contains sensitive information. Please keep it confidential.</small>
            </div>
        </div>
        <div class="footer">
            <p>&copy; '.date('Y').' '.SITE_NAME.'. All rights reserved.</p>
            <p>This is an automated message from our employee portal.</p>
        </div>
    </div>
</body>
</html>';

        $altBody = 'Welcome to '.SITE_NAME."!\n\nYour Employee Code: ".$employeeCode."\nYour Password: ".$password."\n\nLogin at: ".$loginUrl."\n\nPlease change your password after first login.\n\n- ".SITE_NAME.' Support';

        $from = config('mail.from.address');
        $fromName = config('mail.from.name');

        try {
            Mail::html($htmlBody, function ($message) use ($toEmail, $toName, $subject, $altBody, $from, $fromName) {
                $message->from($from, $fromName)
                    ->to($toEmail, $toName)
                    ->replyTo($from, $fromName)
                    ->subject($subject);
                $message->getSymfonyMessage()->text($altBody, 'utf-8');
            });
            error_log("[Registration] Welcome email sent to: $toEmail");

            return true;
        } catch (\Throwable $e) {
            error_log("[Registration] Welcome email failed to $toEmail: ".$e->getMessage());

            return false;
        }
    }

    /** forgot-password.php sendPasswordResetEmail() */
    public static function sendPasswordResetEmail($employee, $new_password)
    {
        $to = $employee['email'];
        $toName = $employee['full_name'];
        $employee_code = $employee['employee_code'];

        $subject = "K-Connect Password Reset - $employee_code";

        $content = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <h2 style="color: #009ada;">K-Connect Password Reset</h2>
        <p>Dear <strong>'.htmlspecialchars($toName).'</strong>,</p>
        <p>A password reset was requested for your account.</p>

        <div style="background: #f0f9ff; padding: 20px; margin: 20px 0; border-left: 4px solid #009ada;">
            <p style="margin: 0 0 5px 0; font-size: 12px;">Your New Password:</p>
            <p style="margin: 0; font-size: 24px; font-weight: bold; font-family: monospace; color: #009ada;">'.$new_password.'</p>
        </div>

        <p><strong>Important:</strong></p>
        <ul>
            <li>Use this password to login at: <a href="'.COMPANY_URL.'/login.php">'.COMPANY_URL.'/login.php</a></li>
            <li>After login, go to "My Profile" → "Change Password" to set your own password</li>
            <li>If you did not request this reset, please contact IT support immediately</li>
        </ul>

        <hr style="margin: 20px 0;">
        <p style="font-size: 12px; color: #666;">© '.date('Y').' Kiboko Group</p>
    </div>';

        $htmlBody = itMailWrapper('Password Reset', $content);

        return sendITMail($to, $toName, $subject, $htmlBody);
    }
}
