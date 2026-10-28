<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\User;
use Core\Request;
use Core\Response;
use Core\Session;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response): void
    {
        // Try auto-login via remember_me cookie if not in session
        if (!Session::isLoggedIn()) {
            $rememberCookie = $request->cookie('remember_token');
            if (!empty($rememberCookie)) {
                $user = User::findByRememberToken($rememberCookie);
                if ($user && $user['status'] === 'active') {
                    Session::setUser($user);
                }
            }
        }

        if (!Session::isLoggedIn()) {
            if ($request->isAjax() || str_starts_with($request->getPath(), '/api/')) {
                $response->json([
                    'success' => false,
                    'message' => 'Unauthorized. Please log in.'
                ], 401);
                exit;
            }

            Session::set('_redirect_after_login', $request->getUri());
            Session::setFlash('warning', 'Please sign in to access this page.');
            $response->redirect('/login');
            exit;
        }

        // Check if user is banned
        $currentUser = Session::getUser();
        if ($currentUser && ($currentUser['status'] ?? '') === 'banned') {
            Session::logout();
            Session::setFlash('error', 'Your account has been suspended. Please contact support.');
            $response->redirect('/login');
            exit;
        }
    }
}
