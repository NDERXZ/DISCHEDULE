<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Request;
use Core\Response;
use Core\Session;

class CsrfMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response): void
    {
        // Skip for safe read methods
        if (in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'], true)) {
            return;
        }

        // Skip for API routes which use API tokens
        if (str_starts_with($request->getPath(), '/api/')) {
            return;
        }

        // Skip for Discord webhook / bot callbacks
        if (str_starts_with($request->getPath(), '/webhooks/incoming')) {
            return;
        }

        $token = $request->input('_csrf_token') ?? 
                 $request->header('x-csrf-token') ?? 
                 $request->header('x-xsrf-token');

        if (!Session::validateCsrfToken($token)) {
            if ($request->isAjax()) {
                $response->json([
                    'success' => false,
                    'message' => 'CSRF token mismatch. Please refresh and try again.'
                ], 419);
                exit;
            }

            Session::setFlash('error', 'Your session expired or the form security token was invalid. Please try again.');
            $referer = $_SERVER['HTTP_REFERER'] ?? '/';
            $response->redirect($referer);
            exit;
        }
    }
}
