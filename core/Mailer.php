<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Native Mailer Engine
 * Supports PHP mail() and pure socket-based SMTP without any third-party dependencies.
 */
class Mailer
{
    private string $driver;
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $encryption;
    private string $fromAddress;
    private string $fromName;

    public function __construct()
    {
        $config = require dirname(__DIR__) . '/app/Config/mail.php';
        $this->driver = $config['driver'] ?? 'mail';
        $this->host = $config['host'] ?? '127.0.0.1';
        $this->port = (int)($config['port'] ?? 25);
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->encryption = strtolower($config['encryption'] ?? '');
        $this->fromAddress = $config['from']['address'] ?? 'notifications@dischedule.local';
        $this->fromName = $config['from']['name'] ?? 'Dischedule Notifications';
    }

    public static function send(string $to, string $subject, string $htmlBody, ?string $altBody = null): bool
    {
        $mailer = new self();
        return $mailer->dispatch($to, $subject, $htmlBody, $altBody);
    }

    public function dispatch(string $to, string $subject, string $htmlBody, ?string $altBody = null): bool
    {
        try {
            if ($this->driver === 'smtp' && !empty($this->host)) {
                return $this->sendViaSmtp($to, $subject, $htmlBody, $altBody);
            }
            return $this->sendViaPhpMail($to, $subject, $htmlBody, $altBody);
        } catch (\Throwable $e) {
            Logger::error("Failed to send email to {$to}: " . $e->getMessage());
            return false;
        }
    }

    private function sendViaPhpMail(string $to, string $subject, string $htmlBody, ?string $altBody = null): bool
    {
        $boundary = "==Multipart_Boundary_x" . md5((string)time()) . "x";

        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "From: =?UTF-8?B?" . base64_encode($this->fromName) . "?= <{$this->fromAddress}>";
        $headers[] = "Reply-To: {$this->fromAddress}";
        $headers[] = "X-Mailer: Dischedule-Native-Mailer/1.0";
        $headers[] = "Content-Type: multipart/alternative; boundary=\"{$boundary}\"";

        $plain = $altBody ?? strip_tags($htmlBody);

        $body = "--{$boundary}\r\n";
        $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($plain)) . "\r\n";

        $body .= "--{$boundary}\r\n";
        $body .= "Content-Type: text/html; charset=UTF-8\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($htmlBody)) . "\r\n";
        $body .= "--{$boundary}--";

        $encodedSubject = "=?UTF-8?B?" . base64_encode($subject) . "?=";

        $result = @mail($to, $encodedSubject, $body, implode("\r\n", $headers));
        if ($result) {
            Logger::info("Email sent to {$to} via PHP mail()");
        }
        return $result;
    }

    private function sendViaSmtp(string $to, string $subject, string $htmlBody, ?string $altBody = null): bool
    {
        $timeout = 15;
        $host = $this->host;
        if ($this->encryption === 'ssl') {
            $host = 'ssl://' . $host;
        }

        $socket = @fsockopen($host, $this->port, $errno, $errstr, $timeout);
        if (!$socket) {
            throw new \RuntimeException("SMTP Connection failed to {$this->host}:{$this->port} - {$errstr} ({$errno})");
        }

        stream_set_timeout($socket, $timeout);
        $this->getSmtpResponse($socket, [220]);

        $this->sendSmtpCommand($socket, "EHLO " . gethostname(), [250]);

        if ($this->encryption === 'tls') {
            $this->sendSmtpCommand($socket, "STARTTLS", [220]);
            stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT);
            $this->sendSmtpCommand($socket, "EHLO " . gethostname(), [250]);
        }

        if (!empty($this->username) && !empty($this->password)) {
            $this->sendSmtpCommand($socket, "AUTH LOGIN", [334]);
            $this->sendSmtpCommand($socket, base64_encode($this->username), [334]);
            $this->sendSmtpCommand($socket, base64_encode($this->password), [235]);
        }

        $this->sendSmtpCommand($socket, "MAIL FROM: <{$this->fromAddress}>", [250]);
        $this->sendSmtpCommand($socket, "RCPT TO: <{$to}>", [250, 251]);
        $this->sendSmtpCommand($socket, "DATA", [354]);

        $boundary = "==Multipart_Boundary_x" . md5((string)time()) . "x";
        $plain = $altBody ?? strip_tags($htmlBody);

        $headers = "From: =?UTF-8?B?" . base64_encode($this->fromName) . "?= <{$this->fromAddress}>\r\n";
        $headers .= "To: <{$to}>\r\n";
        $headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
        $headers .= "Date: " . date('r') . "\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n\r\n";

        $message = $headers;
        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($plain)) . "\r\n";

        $message .= "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($htmlBody)) . "\r\n";
        $message .= "--{$boundary}--\r\n.\r\n";

        fwrite($socket, $message);
        $this->getSmtpResponse($socket, [250]);

        $this->sendSmtpCommand($socket, "QUIT", [221]);
        fclose($socket);

        Logger::info("Email sent to {$to} via Socket SMTP");
        return true;
    }

    private function sendSmtpCommand($socket, string $command, array $expectedCodes): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->getSmtpResponse($socket, $expectedCodes);
    }

    private function getSmtpResponse($socket, array $expectedCodes): string
    {
        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (substr($line, 3, 1) === ' ') {
                break;
            }
        }

        $code = (int)substr($response, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            throw new \RuntimeException("SMTP Unexpected response: {$response}");
        }

        return $response;
    }
}
