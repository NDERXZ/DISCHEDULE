<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\DateHelper;
use Core\Mailer;

/**
 * Dischedule Transactional Email Templates & Delivery Service
 */
class EmailService
{
    public function sendVerificationEmail(array $user, string $token): bool
    {
        $verifyUrl = absolute_url('verify-email?token=' . $token);
        $appName = $_ENV['APP_NAME'] ?? 'Dischedule';

        $subject = "Verify your email address - {$appName}";
        $html = $this->wrapTemplate("
            <h2 style='color:#1E293B;margin-top:0;'>Welcome to {$appName}, " . htmlspecialchars($user['username']) . "!</h2>
            <p style='color:#475569;font-size:15px;line-height:1.6;'>
                Thank you for creating an account on {$appName}. To activate all Discord integrations, scheduled webhooks, and team calendars, please verify your email address.
            </p>
            <div style='text-align:center;margin:30px 0;'>
                <a href='{$verifyUrl}' style='display:inline-block;padding:12px 28px;background-color:#4F46E5;color:#FFFFFF;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;'>
                    Verify Email Address
                </a>
            </div>
            <p style='color:#64748B;font-size:13px;line-height:1.5;'>
                This verification link will expire in 24 hours. If you did not sign up for {$appName}, please disregard this email.
            </p>
        ");

        return Mailer::send($user['email'], $subject, $html);
    }

    public function sendPasswordResetEmail(array $user, string $token): bool
    {
        $resetUrl = absolute_url('reset-password?token=' . $token);
        $appName = $_ENV['APP_NAME'] ?? 'Dischedule';

        $subject = "Reset Your Password - {$appName}";
        $html = $this->wrapTemplate("
            <h2 style='color:#1E293B;margin-top:0;'>Password Reset Request</h2>
            <p style='color:#475569;font-size:15px;line-height:1.6;'>
                Hello " . htmlspecialchars($user['username']) . ", we received a request to reset your password for your {$appName} account.
            </p>
            <div style='text-align:center;margin:30px 0;'>
                <a href='{$resetUrl}' style='display:inline-block;padding:12px 28px;background-color:#4F46E5;color:#FFFFFF;text-decoration:none;border-radius:6px;font-weight:600;font-size:15px;'>
                    Reset Password
                </a>
            </div>
            <p style='color:#64748B;font-size:13px;line-height:1.5;'>
                This link will expire in 1 hour. If you did not request a password reset, no further action is required and your account remains safe.
            </p>
        ");

        return Mailer::send($user['email'], $subject, $html);
    }

    public function sendEventReminderEmail(array $user, array $reminder): bool
    {
        $eventUrl = url('events/' . $reminder['event_id']);
        $appName = $_ENV['APP_NAME'] ?? 'Dischedule';
        $userTz = $user['timezone'] ?? 'UTC';

        $timeStr = DateHelper::humanReadable($reminder['start_datetime'], $userTz);

        $subject = "⏰ Reminder: {$reminder['title']} (in {$reminder['minutes_before']} minutes)";
        $html = $this->wrapTemplate("
            <h2 style='color:#1E293B;margin-top:0;'>Upcoming Event Reminder</h2>
            <div style='background-color:#F8FAFC;border-left:4px solid #4F46E5;padding:16px;margin:20px 0;border-radius:4px;'>
                <h3 style='margin:0 0 8px 0;color:#1E293B;'>" . htmlspecialchars($reminder['title']) . "</h3>
                <p style='margin:0 0 4px 0;color:#475569;'><strong>Time:</strong> {$timeStr} ({$userTz})</p>
                " . (!empty($reminder['location']) ? "<p style='margin:0;color:#475569;'><strong>Location:</strong> " . htmlspecialchars($reminder['location']) . "</p>" : "") . "
            </div>
            <p style='color:#475569;font-size:14px;'>" . nl2br(htmlspecialchars($reminder['description'] ?? '')) . "</p>
            <div style='text-align:center;margin:30px 0;'>
                <a href='{$eventUrl}' style='display:inline-block;padding:10px 24px;background-color:#4F46E5;color:#FFFFFF;text-decoration:none;border-radius:6px;font-weight:600;font-size:14px;'>
                    View Event Details
                </a>
            </div>
        ");

        return Mailer::send($user['email'], $subject, $html);
    }

    private function wrapTemplate(string $bodyHtml): string
    {
        $appName = $_ENV['APP_NAME'] ?? 'Dischedule';
        $appUrl = url();

        return "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>{$appName}</title>
        </head>
        <body style='margin:0;padding:0;background-color:#F1F5F9;font-family:-apple-system,BlinkMacSystemFont,\"Segoe UI\",Roboto,Helvetica,Arial,sans-serif;'>
            <table border='0' cellpadding='0' cellspacing='0' width='100%' style='table-layout:fixed;'>
                <tr>
                    <td align='center' style='padding:40px 10px;'>
                        <table border='0' cellpadding='0' cellspacing='0' width='100%' style='max-width:580px;background-color:#FFFFFF;border-radius:8px;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);overflow:hidden;'>
                            <tr>
                                <td style='background-color:#4F46E5;padding:24px 32px;text-align:center;'>
                                    <h1 style='color:#FFFFFF;margin:0;font-size:22px;font-weight:700;letter-spacing:-0.5px;'>
                                        📅 {$appName}
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding:32px;'>
                                    {$bodyHtml}
                                </td>
                            </tr>
                            <tr>
                                <td style='background-color:#F8FAFC;padding:20px 32px;text-align:center;border-top:1px solid #E2E8F0;'>
                                    <p style='margin:0;color:#94A3B8;font-size:12px;'>
                                        &copy; " . date('Y') . " <a href='{$appUrl}' style='color:#6366F1;text-decoration:none;'>{$appName}</a>. All rights reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>";
    }
}
