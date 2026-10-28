<?php

declare(strict_types=1);

namespace App\Middleware;

use Core\Request;
use Core\Response;
use Core\Session;
use Core\View;

class AdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response): void
    {
        // First ensure user is logged in
        $authMiddleware = new AuthMiddleware();
        $authMiddleware->handle($request, $response);

        $user = Session::getUser();
        if (!$user || ($user['role'] ?? '') !== 'admin') {
            if ($request->isAjax() || str_starts_with($request->getPath(), '/api/')) {
                $response->json([
                    'success' => false,
                    'message' => 'Forbidden. Administrator permissions required.'
                ], 403);
                exit;
            }

            $response->setStatusCode(403);
            $html = View::render('errors.403', ['title' => 'Access Denied'], 'main');
            $response->html($html);
            exit;
        }
    }
}
