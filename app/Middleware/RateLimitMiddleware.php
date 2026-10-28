<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Database;
use Core\Request;
use Core\Response;
use Core\Session;

class RateLimitMiddleware implements MiddlewareInterface
{
    private int $maxAttempts;
    private int $decaySeconds;

    public function __construct(int $maxAttempts = 5, int $decaySeconds = 900)
    {
        $this->maxAttempts = $maxAttempts;
        $this->decaySeconds = $decaySeconds;
    }

    public function handle(Request $request, Response $response): void
    {
        $ip = $request->ip();
        $path = $request->getPath();
        $key = 'rate_limit:' . md5($ip . '|' . $path);
        $keyHash = md5($key);
        $now = time();

        try {
            $db = Database::getInstance();
            
            // Clean up old rate limit records occasionally
            if (random_int(1, 100) === 1) {
                $db->query("DELETE FROM `rate_limits` WHERE `reset_at` < :now", ['now' => $now]);
            }

            $record = $db->fetch("SELECT * FROM `rate_limits` WHERE `key_hash` = :hash", ['hash' => $keyHash]);

            if ($record) {
                if ($record['reset_at'] > $now) {
                    if ((int)$record['hits'] >= $this->maxAttempts) {
                        $retryAfter = $record['reset_at'] - $now;
                        $response->setHeader('Retry-After', (string)$retryAfter);

                        if ($request->isAjax() || str_starts_with($path, '/api/')) {
                            $response->json([
                                'success'     => false,
                                'message'     => 'Too many requests. Please try again later.',
                                'retry_after' => $retryAfter
                            ], 429);
                            exit;
                        }

                        Session::setFlash('error', "Too many requests. Please wait {$retryAfter} seconds before trying again.");
                        $response->redirect($_SERVER['HTTP_REFERER'] ?? '/');
                        exit;
                    }

                    // Increment hits
                    $db->update('rate_limits', ['hits' => $record['hits'] + 1], "`key_hash` = :h", ['h' => $keyHash]);
                } else {
                    // Reset window
                    $db->update('rate_limits', ['hits' => 1, 'reset_at' => $now + $this->decaySeconds], "`key_hash` = :h", ['h' => $keyHash]);
                }
            } else {
                $db->insert('rate_limits', [
                    'key_hash' => $keyHash,
                    'hits'     => 1,
                    'reset_at' => $now + $this->decaySeconds
                ]);
            }
        } catch (\Throwable) {
            // If DB unavailable for rate limiting, fail gracefully and allow request
        }
    }
}
