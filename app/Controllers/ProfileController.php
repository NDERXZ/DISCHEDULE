<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\DateHelper;
use App\Models\ActivityLog;
use App\Models\ApiToken;
use App\Models\User;
use Core\Controller;
use Core\I18n;
use Core\Session;

class ProfileController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $user = User::find($this->userId());

        $this->render('profile.index', [
            'title'     => 'Account Settings - Dischedule',
            'user'      => $user,
            'timezones' => DateHelper::getTimezonesList()
        ], 'main');
    }

    public function updateProfile(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $input = $this->request->all();

        $this->validate($input, [
            'email'    => "required|email|unique:users,email,{$userId}",
            'timezone' => 'required',
            'language' => 'required|in:en,id,es',
            'theme'    => 'required|in:light,dark,system'
        ]);

        $updateData = [
            'email'        => strtolower(trim((string)$input['email'])),
            'bio'          => !empty($input['bio']) ? trim((string)$input['bio']) : null,
            'timezone'     => trim((string)$input['timezone']),
            'language'     => trim((string)$input['language']),
            'theme'        => trim((string)$input['theme']),
            'accent_color' => !empty($input['accent_color']) ? trim((string)$input['accent_color']) : '#4F46E5'
        ];

        // Handle Avatar Upload
        $avatarFile = $this->request->file('avatar');
        if ($avatarFile && ($avatarFile['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($avatarFile['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                $uploadDir = dirname(__DIR__, 2) . '/storage/uploads';
                if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);

                $avatarFilename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
                if (move_uploaded_file($avatarFile['tmp_name'], $uploadDir . '/' . $avatarFilename)) {
                    $updateData['avatar'] = '/storage/uploads/' . $avatarFilename;
                }
            }
        }

        User::update($userId, $updateData);

        // Update Session user
        $updatedUser = User::find($userId);
        Session::setUser(User::sanitize($updatedUser));
        I18n::setLocale($updatedUser['language']);

        ActivityLog::log($userId, 'profile_updated', 'Updated profile information.');

        $this->flash('success', 'Profile settings updated successfully.');
        $this->redirect('/profile');
    }

    public function security(): void
    {
        $this->requireAuth();
        $this->render('profile.security', ['title' => 'Security & Password - Dischedule'], 'main');
    }

    public function updatePassword(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $user = User::find($userId);
        $input = $this->request->all();

        $this->validate($input, [
            'current_password'      => 'required',
            'new_password'          => 'required|password_strength',
            'password_confirmation' => 'required|matches:new_password'
        ]);

        if (!password_verify((string)$input['current_password'], $user['password_hash'])) {
            $this->flash('error', 'Current password does not match our records.');
            $this->redirect('/profile/security');
            return;
        }

        $newHash = password_hash((string)$input['new_password'], PASSWORD_BCRYPT);
        User::update($userId, ['password_hash' => $newHash]);

        ActivityLog::log($userId, 'password_changed', 'User changed account password.');

        $this->flash('success', 'Password updated successfully!');
        $this->redirect('/profile/security');
    }

    public function discord(): void
    {
        $this->requireAuth();
        $user = User::find($this->userId());
        $discordService = new \App\Services\DiscordService();

        $this->render('profile.discord', [
            'title'             => 'Discord Integration - Dischedule',
            'user'              => $user,
            'isOAuthConfigured' => $discordService->isOAuthConfigured()
        ], 'main');
    }

    public function apiTokens(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $tokens = ApiToken::getForUser($userId);
        $newPlainToken = Session::get('new_plain_token');
        Session::remove('new_plain_token');

        $this->render('profile.api_tokens', [
            'title'         => 'API Tokens - Dischedule',
            'tokens'        => $tokens,
            'newPlainToken' => $newPlainToken
        ], 'main');
    }

    public function createApiToken(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $input = $this->request->all();

        $this->validate($input, [
            'name' => 'required|max:100'
        ]);

        $name = trim((string)$input['name']);
        $perms = !empty($input['permissions']) ? (is_array($input['permissions']) ? implode(',', $input['permissions']) : (string)$input['permissions']) : 'read,write';
        $expires = !empty($input['expires_days']) ? (int)$input['expires_days'] : null;

        $tokenResult = ApiToken::createToken($userId, $name, $perms, $expires);
        Session::set('new_plain_token', $tokenResult['plain_token']);

        ActivityLog::log($userId, 'api_token_created', "Created API token '{$name}'.");

        $this->flash('success', 'API token created! Please copy it now as it will not be displayed again.');
        $this->redirect('/profile/api-tokens');
    }

    public function revokeApiToken(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();

        $token = ApiToken::find($id);
        if ($token && (int)$token['user_id'] === $userId) {
            ApiToken::delete($id);
            ActivityLog::log($userId, 'api_token_revoked', "Revoked API token #{$id}.");
            $this->flash('info', 'API token revoked.');
        }

        $this->redirect('/profile/api-tokens');
    }

    public function deleteAccount(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $user = User::find($userId);
        $password = (string)$this->request->input('confirm_password', '');

        if (!password_verify($password, $user['password_hash'])) {
            $this->flash('error', 'Incorrect password. Account was not deleted.');
            $this->redirect('/profile');
            return;
        }

        // Anonymize and soft-delete user
        User::update($userId, [
            'username'          => 'deleted_user_' . $userId,
            'email'             => 'deleted_' . $userId . '@dischedule.local',
            'discord_id'        => null,
            'discord_username'  => null,
            'status'            => 'inactive',
            'deleted_at'        => date('Y-m-d H:i:s')
        ]);

        Session::logout();
        $this->flash('info', 'Your Dischedule account has been deactivated.');
        $this->redirect('/');
    }
}
