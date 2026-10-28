<?php

declare(strict_types=1);

return [
    'name'        => $_ENV['APP_NAME'] ?? 'Dischedule',
    'env'         => $_ENV['APP_ENV'] ?? 'development',
    'debug'       => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
    'url'         => rtrim($_ENV['APP_URL'] ?? 'http://localhost:8000', '/'),
    'timezone'    => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    'locale'      => $_ENV['APP_LOCALE'] ?? 'en',
    'key'         => $_ENV['APP_KEY'] ?? 'base64:defaultsecretkeyfordischedule2026',
    'maintenance' => filter_var($_ENV['MAINTENANCE_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN),
    'max_upload'  => (int)($_ENV['MAX_FILE_SIZE_MB'] ?? 10),
    'allowed_ext' => explode(',', $_ENV['ALLOWED_FILE_TYPES'] ?? 'jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,ics,csv')
];
