<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\DiscordService;
use Core\Controller;
use Core\Session;

class DiscordAuthController extends Controller
{
    private DiscordService $discordService;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->discordService = new DiscordService();
    }

    public function redirectToDiscord(): void
    {
        if (!$this->discordService->isOAuthConfigured()) {
            $this->flash('warning', 'Discord OAuth2 is not configured yet. Please configure Discord Client ID and Secret in Platform Settings or .env.');
            $this->redirect($this->isLoggedIn() ? '/profile/discord' : '/login');
            return;
        }

        $state = bin2hex(random_bytes(16));
        Session::set('discord_oauth_state', $state);

        $authUrl = $this->discordService->getOAuthAuthorizationUrl($state);
        $this->response->redirect($authUrl);
    }

    public function callback(): void
    {
        $code = (string)$this->request->query('code', '');
        $state = (string)$this->request->query('state', '');
        $savedState = Session::get('discord_oauth_state');
        Session::remove('discord_oauth_state');

        if (empty($code) || empty($state) || $state !== $savedState) {
            $this->flash('error', 'Discord OAuth authorization failed or expired.');
            $this->redirect($this->isLoggedIn() ? '/profile/discord' : '/login');
            return;
        }

        $tokenData = $this->discordService->exchangeOAuthCode($code);
        if (!$tokenData || empty($tokenData['access_token'])) {
            $this->flash('error', 'Could not exchange Discord OAuth code. Please verify Client ID and Secret.');
            $this->redirect($this->isLoggedIn() ? '/profile/discord' : '/login');
            return;
        }

        $discordUser = $this->discordService->getDiscordUserProfile($tokenData['access_token']);
        if (!$discordUser || empty($discordUser['id'])) {
            $this->flash('error', 'Failed to retrieve Discord profile information.');
            $this->redirect($this->isLoggedIn() ? '/profile/discord' : '/login');
            return;
        }

        $discordId = (string)$discordUser['id'];
        $discordUsername = $discordUser['username'] . (!empty($discordUser['discriminator']) && $discordUser['discriminator'] !== '0' ? '#' . $discordUser['discriminator'] : '');
        $discordAvatar = !empty($discordUser['avatar'])
            ? "https://cdn.discordapp.com/avatars/{$discordId}/{$discordUser['avatar']}.png"
            : null;

        if ($this->isLoggedIn()) {
            // Link Discord account to logged in user
            $currentUserId = $this->userId();
            
            // Check if discord_id is already linked to another account
            $existing = User::findByDiscordId($discordId);
            if ($existing && (int)$existing['id'] !== $currentUserId) {
                $this->flash('error', 'This Discord account is already connected to another Dischedule user.');
                $this->redirect('/profile/discord');
                return;
            }

            User::update($currentUserId, [
                'discord_id'       => $discordId,
                'discord_username' => $discordUsername,
                'discord_avatar'   => $discordAvatar
            ]);

            Session::setUser(User::sanitize(User::find($currentUserId)));
            ActivityLog::log($currentUserId, 'discord_linked', "Linked Discord account {$discordUsername}.");

            $this->flash('success', "Discord account ({$discordUsername}) connected successfully!");
            $this->redirect('/profile/discord');
            return;
        }

        // User is not logged in: Log in or Register via Discord
        $user = User::findByDiscordId($discordId);

        if (!$user && !empty($discordUser['email'])) {
            $user = User::findByEmail(strtolower($discordUser['email']));
            if ($user) {
                // Link Discord to existing email account
                User::update($user['id'], [
                    'discord_id'       => $discordId,
                    'discord_username' => $discordUsername,
                    'discord_avatar'   => $discordAvatar,
                    'email_verified_at'=> $user['email_verified_at'] ?? date('Y-m-d H:i:s')
                ]);
            }
        }

        if (!$user) {
            // Auto-register new user from Discord
            $baseUsername = preg_replace('/[^a-zA-Z0-9_]/', '', $discordUser['username']) ?: 'user';
            $uniqueUsername = $baseUsername;
            $counter = 1;
            while (User::findByUsername($uniqueUsername)) {
                $uniqueUsername = $baseUsername . $counter++;
            }

            $email = !empty($discordUser['email']) ? strtolower($discordUser['email']) : "{$discordId}@discord.dischedule.local";
            $randomPassword = bin2hex(random_bytes(16));

            $newUserId = User::create([
                'username'          => $uniqueUsername,
                'email'             => $email,
                'password_hash'     => password_hash($randomPassword, PASSWORD_BCRYPT),
                'discord_id'        => $discordId,
                'discord_username'  => $discordUsername,
                'discord_avatar'    => $discordAvatar,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'role'              => 'user',
                'status'            => 'active',
                'created_at'        => date('Y-m-d H:i:s')
            ]);

            $user = User::find($newUserId);
            ActivityLog::log($newUserId, 'register_discord', 'Registered via Discord OAuth2.');
        }

        if ($user['status'] === 'banned') {
            $this->flash('error', 'Your account has been suspended.');
            $this->redirect('/login');
            return;
        }

        Session::setUser(User::sanitize($user));
        ActivityLog::log((int)$user['id'], 'login_discord', 'Logged in via Discord OAuth2.');

        $this->flash('success', "Welcome back, {$user['username']}! Signed in via Discord.");
        $this->redirect('/calendar');
    }

    public function unlink(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        User::update($userId, [
            'discord_id'       => null,
            'discord_username' => null,
            'discord_avatar'   => null
        ]);

        Session::setUser(User::sanitize(User::find($userId)));
        ActivityLog::log($userId, 'discord_unlinked', 'Unlinked Discord account.');

        $this->flash('info', 'Discord account disconnected.');
        $this->redirect('/profile/discord');
    }
}
