<?php

declare(strict_types=1);

return [
    'client_id'          => $_ENV['DISCORD_CLIENT_ID'] ?? '',
    'client_secret'      => $_ENV['DISCORD_CLIENT_SECRET'] ?? '',
    'redirect_uri'       => $_ENV['DISCORD_REDIRECT_URI'] ?? 'http://localhost:8000/auth/discord/callback',
    'bot_token'          => $_ENV['DISCORD_BOT_TOKEN'] ?? '',
    'global_webhook_url' => $_ENV['DISCORD_GLOBAL_WEBHOOK_URL'] ?? '',
    'bot_name'           => $_ENV['DISCORD_BOT_NAME'] ?? 'Dischedule Bot',
    'avatar_url'         => $_ENV['DISCORD_AVATAR_URL'] ?? 'https://raw.githubusercontent.com/discord/discord-api-docs/master/assets/discord-logo-color.png',
    'api_base'           => 'https://discord.com/api/v10',
    'auth_url'           => 'https://discord.com/api/oauth2/authorize',
    'token_url'          => 'https://discord.com/api/oauth2/token',
    'scopes'             => 'identify email'
];
