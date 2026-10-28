<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\ApiToken;
use App\Models\User;
use Core\Request;
use Core\Response;
use Core\Session;

class ApiAuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response): void
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            $response->json([
                'success' => false,
                'message' => 'Unauthenticated. Missing Bearer API token.'
            ], 401);
            exit;
        }

        $tokenHash = hash('sha256', $token);
        $apiToken = ApiToken::findByTokenHash($tokenHash);

        if (!$apiToken) {
            $response->json([
                'success' => false,
                'message' => 'Invalid or expired API token.'
            ], 401);
            exit;
        }

        // Check if token has expired
        if (!empty($apiToken['expires_at']) && strtotime($apiToken['expires_at']) < time()) {
            $response->json([
                'success' => false,
                'message' => 'API token has expired.'
            ], 401);
            exit;
        }

        $user = User::find($apiToken['user_id']);
        if (!$user || $user['status'] !== 'active') {
            $response->json([
                'success' => false,
                'message' => 'User account is inactive or suspended.'
            ], 403);
            exit;
        }

        // Update token last used at
        ApiToken::update($apiToken['id'], [
            'last_used_at' => date('Y-m-d H:i:s')
        ]);

        // Inject into Session scope for controller access
        Session::setUser($user);
        Session::set('_api_token', $apiToken);
    }
}
