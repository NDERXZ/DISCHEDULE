<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule HTTP Response Handler
 */
class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $content = '';

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httpOnly = true,
        string $sameSite = 'Lax'
    ): self {
        setcookie($name, $value, [
            'expires'  => $expires,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite
        ]);
        return $this;
    }

    public function deleteCookie(string $name, string $path = '/'): self
    {
        setcookie($name, '', [
            'expires'  => time() - 3600,
            'path'     => $path,
            'domain'   => '',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        return $this;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function json(mixed $data, int $status = 200): void
    {
        $this->setStatusCode($status);
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->setContent(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        $this->send();
        exit;
    }

    public function html(string $html, int $status = 200): void
    {
        $this->setStatusCode($status);
        $this->setHeader('Content-Type', 'text/html; charset=utf-8');
        $this->setContent($html);
    }

    public function redirect(string $url, int $status = 302): void
    {
        $this->setStatusCode($status);
        $this->setHeader('Location', $url);
        $this->send();
        exit;
    }

    public function download(string $filePath, ?string $downloadName = null): void
    {
        if (!file_exists($filePath)) {
            $this->setStatusCode(404);
            $this->setContent("File not found.");
            $this->send();
            exit;
        }

        $downloadName = $downloadName ?? basename($filePath);
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filePath) ?: 'application/octet-stream';
        finfo_close($finfo);

        $this->setStatusCode(200);
        $this->setHeader('Content-Type', $mime);
        $this->setHeader('Content-Disposition', 'attachment; filename="' . addslashes($downloadName) . '"');
        $this->setHeader('Content-Length', (string)filesize($filePath));
        $this->setHeader('Pragma', 'public');
        $this->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');

        $this->sendHeaders();
        readfile($filePath);
        exit;
    }

    public function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
    }

    public function send(): void
    {
        $this->sendHeaders();
        echo $this->content;
    }
}
