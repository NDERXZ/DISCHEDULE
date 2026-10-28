<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\DateHelper;
use App\Helpers\StringHelper;
use Core\Database;
use Core\Logger;

/**
 * Dischedule Discord Integration Service
 * Handles Webhook Embeds, Mentions, Rate Limits, and OAuth2 flow via cURL.
 */
class DiscordService
{
    private array $config;

    public function __construct()
    {
        $this->config = require dirname(__DIR__) . '/Config/discord.php';

        try {
            $dbClientId = \App\Models\Setting::get('discord_client_id');
            $dbClientSecret = \App\Models\Setting::get('discord_client_secret');
            $dbRedirectUri = \App\Models\Setting::get('discord_redirect_uri');
            $dbBotToken = \App\Models\Setting::get('discord_bot_token');
            $dbGlobalWebhook = \App\Models\Setting::get('discord_global_webhook_url');
            $dbBotName = \App\Models\Setting::get('discord_bot_name');
            $dbAvatarUrl = \App\Models\Setting::get('discord_avatar_url');

            if (!empty($dbClientId)) $this->config['client_id'] = $dbClientId;
            if (!empty($dbClientSecret)) $this->config['client_secret'] = $dbClientSecret;
            if (!empty($dbRedirectUri)) $this->config['redirect_uri'] = $dbRedirectUri;
            if (!empty($dbBotToken)) $this->config['bot_token'] = $dbBotToken;
            if (!empty($dbGlobalWebhook)) $this->config['global_webhook_url'] = $dbGlobalWebhook;
            if (!empty($dbBotName)) $this->config['bot_name'] = $dbBotName;
            if (!empty($dbAvatarUrl)) $this->config['avatar_url'] = $dbAvatarUrl;
        } catch (\Throwable) {
            // DB not ready or running outside HTTP lifecycle
        }
    }

    public function getClientId(): string
    {
        return (string)($this->config['client_id'] ?? '');
    }

    public function getClientSecret(): string
    {
        return (string)($this->config['client_secret'] ?? '');
    }

    public function getRedirectUri(): string
    {
        return (string)($this->config['redirect_uri'] ?? '');
    }

    public function isOAuthConfigured(): bool
    {
        return !empty(trim($this->getClientId())) && !empty(trim($this->getClientSecret()));
    }


    /**
     * Build and send a rich Discord embed to one or multiple webhooks
     *
     * @param string|array $webhookUrls Single URL or array of webhook URLs
     * @param string $title
     * @param string $description
     * @param array $options [color, fields, url, mention, event_id, user_id, trigger_type]
     * @return array<string, bool> Results per webhook URL
     */
    public function sendWebhook(string|array $webhookUrls, string $title, string $description, array $options = []): array
    {
        $urls = is_array($webhookUrls) ? $webhookUrls : [$webhookUrls];
        $results = [];

        $colorHex = $options['color'] ?? '#4F46E5';
        $colorInt = StringHelper::hexToDiscordInt($colorHex);

        $embed = [
            'title'       => substr($title, 0, 256),
            'description' => substr($description, 0, 2048),
            'color'       => $colorInt,
            'timestamp'   => date('c'),
            'footer'      => [
                'text'     => 'Dischedule • Event Management',
                'icon_url' => $this->config['avatar_url']
            ]
        ];

        if (!empty($options['url']) && filter_var($options['url'], FILTER_VALIDATE_URL)) {
            $embed['url'] = $options['url'];
        }

        if (!empty($options['fields']) && is_array($options['fields'])) {
            $embed['fields'] = $options['fields'];
        }

        $payload = [
            'username'   => $this->config['bot_name'] ?? 'Dischedule Bot',
            'avatar_url' => $this->config['avatar_url'] ?? 'https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png',
            'embeds'     => [$embed]
        ];

        if (!empty($options['mention'])) {
            $payload['content'] = $options['mention'];
        }

        foreach ($urls as $url) {
            $url = trim((string)$url);
            if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
                continue;
            }

            $res = $this->executeCurlPost($url, $payload, [
                'webhook_id'   => $options['webhook_id'] ?? null,
                'user_id'      => $options['user_id'] ?? null,
                'event_id'     => $options['event_id'] ?? null,
                'trigger_type' => $options['trigger_type'] ?? 'custom'
            ]);

            $results[$url] = $res['success'];
        }

        // Also check if admin global webhook is enabled and should be notified
        if (!empty($this->config['global_webhook_url']) && !in_array($this->config['global_webhook_url'], $urls, true)) {
            $this->executeCurlPost($this->config['global_webhook_url'], $payload, [
                'webhook_id'   => null,
                'user_id'      => $options['user_id'] ?? null,
                'event_id'     => $options['event_id'] ?? null,
                'trigger_type' => ($options['trigger_type'] ?? 'custom') . '_global'
            ]);
        }

        return $results;
    }

    /**
     * Dispatch event notification to Discord
     */
    public function sendEventNotification(array $event, array $webhookUrls, string $triggerType = 'created'): array
    {
        $triggerLabels = [
            'created'  => '🎉 New Event Created',
            'updated'  => '✏️ Event Updated',
            'deleted'  => '🗑️ Event Cancelled/Deleted',
            'reminder' => '⏰ Event Reminder',
            'started'  => '🚀 Event Started Now',
            'ended'    => '🏁 Event Concluded'
        ];

        $label = $triggerLabels[$triggerType] ?? 'Event Notification';
        if ($triggerType === 'reminder') {
            $mins = isset($event['minutes_before']) ? (int)$event['minutes_before'] : 0;
            if ($mins === 0) {
                $label = '⏰ Event Starting Now';
            } else {
                $label = "⏰ Event Reminder ({$mins}m before)";
            }
        }

        $title = $label . ': ' . $event['title'];
        $description = !empty($event['description']) ? StringHelper::truncate($event['description'], 300) : 'No description provided.';
        $eventUrl = !empty($event['id']) ? absolute_url('events/' . $event['id']) : absolute_url('calendar');

        $userTimezone = $event['timezone'] ?? 'UTC';
        $timeStr = DateHelper::humanReadable($event['start_datetime'], $userTimezone) . ' - ' . DateHelper::humanReadable($event['end_datetime'], $userTimezone) . ' (' . $userTimezone . ')';

        $fields = [
            [
                'name'   => '📅 Schedule Time',
                'value'  => $timeStr,
                'inline' => false
            ]
        ];

        if (!empty($event['location'])) {
            $fields[] = [
                'name'   => '📍 Location',
                'value'  => !empty($event['location_url']) ? '[' . $event['location'] . '](' . $event['location_url'] . ')' : $event['location'],
                'inline' => true
            ];
        }

        if (!empty($event['category_name'])) {
            $fields[] = [
                'name'   => '🏷️ Category',
                'value'  => $event['category_name'],
                'inline' => true
            ];
        }

        if (!empty($event['priority'])) {
            $fields[] = [
                'name'   => '⚡ Priority',
                'value'  => ucfirst($event['priority']),
                'inline' => true
            ];
        }

        if (!empty($event['recurrence_rule'])) {
            $fields[] = [
                'name'   => '🔄 Recurrence',
                'value'  => $event['recurrence_rule'],
                'inline' => true
            ];
        }

        $mention = null;
        if (!empty($event['mention_role'])) {
            $mention = $event['mention_role'];
        }

        return $this->sendWebhook($webhookUrls, $title, $description, [
            'color'        => $event['color'] ?? '#4F46E5',
            'fields'       => $fields,
            'url'          => $eventUrl,
            'mention'      => $mention,
            'event_id'     => (int)($event['id'] ?? 0),
            'user_id'      => (int)($event['user_id'] ?? 0),
            'trigger_type' => $triggerType
        ]);
    }

    /**
     * Test a specific webhook URL and return detailed status and response
     */
    public function testWebhook(string $url, string $mentionRole = ''): array
    {
        $payload = [
            'username'   => $this->config['bot_name'],
            'avatar_url' => $this->config['avatar_url'],
            'content'    => !empty($mentionRole) ? "Test mention: {$mentionRole}" : null,
            'embeds'     => [
                [
                    'title'       => '✅ Dischedule Webhook Connection Test',
                    'description' => 'Your Discord webhook has been successfully linked to **Dischedule**! You will receive live event reminders and schedule updates in this channel.',
                    'color'       => 5793266, // Discord Blurple
                    'fields'      => [
                        ['name' => 'System Status', 'value' => '🟢 Operational', 'inline' => true],
                        ['name' => 'Server Time', 'value' => date('Y-m-d H:i:s T'), 'inline' => true]
                    ],
                    'footer'      => [
                        'text'     => 'Dischedule Discord Integration',
                        'icon_url' => $this->config['avatar_url']
                    ],
                    'timestamp'   => date('c')
                ]
            ]
        ];

        return $this->executeCurlPost($url, $payload, [
            'trigger_type' => 'webhook_test'
        ]);
    }

    /**
     * Execute POST request to Discord Webhook via cURL with error logging
     */
    private function executeCurlPost(string $url, array $payload, array $logMeta = []): array
    {
        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonPayload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json; charset=utf-8',
            'User-Agent: DischeduleBot (https://dischedule.local, 1.0.0)'
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $responseBody = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        $success = ($httpCode >= 200 && $httpCode < 300);

        if (!$success) {
            Logger::error("Discord Webhook Error [HTTP {$httpCode}]: {$responseBody} | Curl: {$curlError} | URL: {$url}");
        } else {
            Logger::info("Discord Webhook Sent Successfully [HTTP {$httpCode}] to {$url}");
        }

        // Save log to database
        try {
            Database::getInstance()->insert('webhook_logs', [
                'webhook_id'    => $logMeta['webhook_id'] ?? null,
                'user_id'       => $logMeta['user_id'] ?? null,
                'event_id'      => $logMeta['event_id'] ?? null,
                'trigger_type'  => $logMeta['trigger_type'] ?? 'notification',
                'payload'       => $jsonPayload,
                'response_code' => $httpCode > 0 ? $httpCode : null,
                'response_body' => $responseBody ?: $curlError,
                'status'        => $success ? 'success' : 'failed',
                'created_at'    => date('Y-m-d H:i:s')
            ]);
        } catch (\Throwable $e) {
            // Ignore DB log error to not fail main flow
        }

        return [
            'success'       => $success,
            'http_code'     => $httpCode,
            'response_body' => $responseBody,
            'error'         => $curlError
        ];
    }

    // --- Discord OAuth2 Implementation ---

    public function getOAuthAuthorizationUrl(string $state): string
    {
        $params = [
            'client_id'     => $this->config['client_id'],
            'redirect_uri'  => $this->config['redirect_uri'],
            'response_type' => 'code',
            'scope'         => $this->config['scopes'],
            'state'         => $state,
            'prompt'        => 'consent'
        ];

        return $this->config['auth_url'] . '?' . http_build_query($params);
    }

    public function exchangeOAuthCode(string $code): ?array
    {
        $data = [
            'client_id'     => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $this->config['redirect_uri']
        ];

        $ch = curl_init($this->config['token_url']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode((string)$response, true);
        }

        Logger::error("Discord OAuth Token Exchange Failed [HTTP {$httpCode}]: {$response}");
        return null;
    }

    public function getDiscordUserProfile(string $accessToken): ?array
    {
        $ch = curl_init($this->config['api_base'] . '/users/@me');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$accessToken}",
            "User-Agent: DischeduleBot (https://dischedule.local, 1.0.0)"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode((string)$response, true);
        }

        Logger::error("Discord OAuth Fetch User Failed [HTTP {$httpCode}]: {$response}");
        return null;
    }
}
