<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule HTTP Request Handler
 */
class Request
{
    private string $method;
    private string $uri;
    private string $path;
    private array $queryParams;
    private array $bodyParams;
    private ?array $jsonBody = null;
    private array $files;
    private array $headers;
    private array $cookies;
    private ?array $routeParams = [];

    public function __construct()
    {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        
        // Support method override via _method input or X-HTTP-Method-Override
        if ($this->method === 'POST') {
            $override = $_POST['_method'] ?? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ?? null;
            if ($override && in_array(strtoupper($override), ['PUT', 'PATCH', 'DELETE'], true)) {
                $this->method = strtoupper($override);
            }
        }

        $this->uri = $_SERVER['REQUEST_URI'] ?? '/';
        $this->path = $this->parsePath($this->uri);
        $this->queryParams = $_GET ?? [];
        $this->bodyParams = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->cookies = $_COOKIE ?? [];
        $this->headers = $this->extractHeaders();

        // Parse JSON body if Content-Type is application/json
        $contentType = $this->header('Content-Type') ?? '';
        if (str_contains($contentType, 'application/json')) {
            $rawInput = file_get_contents('php://input');
            if (!empty($rawInput)) {
                $decoded = json_decode($rawInput, true);
                if (is_array($decoded)) {
                    $this->jsonBody = $decoded;
                }
            }
        }
    }

    private function parsePath(string $uri): string
    {
        $path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $scriptName = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '/' && $scriptName !== '\\' && str_starts_with($path, $scriptName)) {
            $path = substr($path, strlen($scriptName));
        }
        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    private function extractHeaders(): array
    {
        $headers = [];
        if (function_exists('getallheaders')) {
            $all = getallheaders();
            if ($all !== false) {
                foreach ($all as $key => $value) {
                    $headers[strtolower($key)] = $value;
                }
                return $headers;
            }
        }

        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerName = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$headerName] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'], true)) {
                $headerName = strtolower(str_replace('_', '-', $key));
                $headers[$headerName] = $value;
            }
        }

        return $headers;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function isGet(): bool
    {
        return $this->method === 'GET';
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    public function isPut(): bool
    {
        return $this->method === 'PUT';
    }

    public function isDelete(): bool
    {
        return $this->method === 'DELETE';
    }

    public function isAjax(): bool
    {
        return strtolower($this->header('x-requested-with') ?? '') === 'xmlhttprequest' ||
               str_contains($this->header('accept') ?? '', 'application/json');
    }

    public function input(string $key, mixed $default = null): mixed
    {
        if ($this->jsonBody !== null && array_key_exists($key, $this->jsonBody)) {
            return $this->jsonBody[$key];
        }
        if (array_key_exists($key, $this->bodyParams)) {
            return $this->bodyParams[$key];
        }
        if (array_key_exists($key, $this->queryParams)) {
            return $this->queryParams[$key];
        }
        if ($this->routeParams !== null && array_key_exists($key, $this->routeParams)) {
            return $this->routeParams[$key];
        }
        return $default;
    }

    public function all(): array
    {
        $all = $this->queryParams;
        if ($this->jsonBody !== null) {
            $all = array_merge($all, $this->jsonBody);
        } else {
            $all = array_merge($all, $this->bodyParams);
        }
        if (!empty($this->routeParams)) {
            $all = array_merge($all, $this->routeParams);
        }
        return $all;
    }

    public function json(): array
    {
        return $this->jsonBody ?? [];
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        if (isset($this->files[$key]) && is_array($this->files[$key]) && !empty($this->files[$key]['name'])) {
            return $this->files[$key];
        }
        return null;
    }

    public function hasFile(string $key): bool
    {
        return $this->file($key) !== null && ($this->files[$key]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK;
    }

    public function header(string $name, ?string $default = null): ?string
    {
        $lower = strtolower($name);
        return $this->headers[$lower] ?? $default;
    }

    public function bearerToken(): ?string
    {
        $header = $this->header('authorization');
        if (!empty($header) && preg_match('/Bearer\s+(\S+)/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }

    public function ip(): string
    {
        if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($list[0]);
        }
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    public function userAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    }

    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    public function getRouteParam(string $key, mixed $default = null): mixed
    {
        return $this->routeParams[$key] ?? $default;
    }
}
