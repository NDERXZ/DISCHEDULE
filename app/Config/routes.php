<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\ApiController;
use App\Controllers\AuthController;
use App\Controllers\CalendarController;
use App\Controllers\CategoryController;
use App\Controllers\DiscordAuthController;
use App\Controllers\EventController;
use App\Controllers\HomeController;
use App\Controllers\NotificationController;
use App\Controllers\ProfileController;
use App\Controllers\ShareController;
use App\Controllers\TeamController;
use App\Controllers\WebhookController;
use App\Middleware\AdminMiddleware;
use App\Middleware\ApiAuthMiddleware;
use App\Middleware\AuthMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\MaintenanceMiddleware;
use App\Middleware\RateLimitMiddleware;
use Core\Router;

/** @var Router $router */

// Global maintenance & CSRF middlewares apply across standard web routes
$webMiddlewares = [MaintenanceMiddleware::class, CsrfMiddleware::class];

// Public Home & Landing
$router->get('/', [HomeController::class, 'index'], [MaintenanceMiddleware::class]);

// Guest Auth Routes
$router->get('/login', [AuthController::class, 'showLogin'], [MaintenanceMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [MaintenanceMiddleware::class, CsrfMiddleware::class, RateLimitMiddleware::class]);
$router->get('/register', [AuthController::class, 'showRegister'], [MaintenanceMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [MaintenanceMiddleware::class, CsrfMiddleware::class, RateLimitMiddleware::class]);
$router->get('/verify-email', [AuthController::class, 'verifyEmail'], [MaintenanceMiddleware::class]);
$router->get('/forgot-password', [AuthController::class, 'showForgot'], [MaintenanceMiddleware::class]);
$router->post('/forgot-password', [AuthController::class, 'forgot'], [MaintenanceMiddleware::class, CsrfMiddleware::class, RateLimitMiddleware::class]);
$router->get('/reset-password', [AuthController::class, 'showReset'], [MaintenanceMiddleware::class]);
$router->post('/reset-password', [AuthController::class, 'reset'], [MaintenanceMiddleware::class, CsrfMiddleware::class]);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout'], [CsrfMiddleware::class]);

// Discord OAuth2 Flow
$router->get('/auth/discord', [DiscordAuthController::class, 'redirectToDiscord']);
$router->get('/auth/discord/callback', [DiscordAuthController::class, 'callback']);
$router->post('/auth/discord/unlink', [DiscordAuthController::class, 'unlink'], [AuthMiddleware::class, CsrfMiddleware::class]);

// Public Event Sharing
$router->get('/share/{token}', [ShareController::class, 'show'], [MaintenanceMiddleware::class]);
$router->post('/share/{token}', [ShareController::class, 'show'], [MaintenanceMiddleware::class, CsrfMiddleware::class]);

// Authenticated Application Routes
$router->group(['middleware' => [MaintenanceMiddleware::class, AuthMiddleware::class, CsrfMiddleware::class]], function (Router $r) {
    // Calendar
    $r->get('/calendar', [CalendarController::class, 'index']);
    $r->get('/calendar/feed', [CalendarController::class, 'eventsFeed']);
    $r->post('/calendar/quick-move', [CalendarController::class, 'quickMove']);
    $r->post('/calendar/quick-resize', [CalendarController::class, 'quickResize']);

    // Events
    $r->get('/events', [EventController::class, 'index']);
    $r->get('/events/create', [EventController::class, 'create']);
    $r->post('/events', [EventController::class, 'store']);
    $r->get('/events/import-export', [EventController::class, 'showImportExport']);
    $r->post('/events/import', [EventController::class, 'processImport']);
    $r->get('/events/export/ics', [EventController::class, 'exportICS']);
    $r->get('/events/export/csv', [EventController::class, 'exportCSV']);
    $r->post('/events/bulk', [EventController::class, 'bulkAction']);
    $r->get('/events/{id}', [EventController::class, 'show']);
    $r->get('/events/{id}/edit', [EventController::class, 'edit']);
    $r->post('/events/{id}', [EventController::class, 'update']);
    $r->put('/events/{id}', [EventController::class, 'update']);
    $r->delete('/events/{id}', [EventController::class, 'destroy']);
    $r->post('/events/{id}/delete', [EventController::class, 'destroy']);
    $r->post('/events/{id}/duplicate', [EventController::class, 'duplicate']);
    $r->post('/events/{id}/comments', [EventController::class, 'addComment']);
    $r->post('/events/{id}/share-link', [EventController::class, 'generateShareLink']);

    // Categories
    $r->get('/categories', [CategoryController::class, 'index']);
    $r->post('/categories', [CategoryController::class, 'store']);
    $r->post('/categories/{id}', [CategoryController::class, 'update']);
    $r->put('/categories/{id}', [CategoryController::class, 'update']);
    $r->delete('/categories/{id}', [CategoryController::class, 'destroy']);
    $r->post('/categories/{id}/delete', [CategoryController::class, 'destroy']);

    // Webhooks
    $r->get('/webhooks', [WebhookController::class, 'index']);
    $r->post('/webhooks', [WebhookController::class, 'store']);
    $r->post('/webhooks/{id}', [WebhookController::class, 'update']);
    $r->put('/webhooks/{id}', [WebhookController::class, 'update']);
    $r->delete('/webhooks/{id}', [WebhookController::class, 'destroy']);
    $r->post('/webhooks/{id}/delete', [WebhookController::class, 'destroy']);
    $r->post('/webhooks/{id}/test', [WebhookController::class, 'test']);
    $r->post('/webhooks/{id}/set-default', [WebhookController::class, 'setDefault']);

    // Notifications
    $r->get('/notifications', [NotificationController::class, 'index']);
    $r->get('/notifications/unread-list', [NotificationController::class, 'unreadList']);
    $r->post('/notifications/{id}/read', [NotificationController::class, 'markRead']);
    $r->post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    $r->post('/notifications/clear-all', [NotificationController::class, 'clearAll']);
    $r->post('/notifications/preferences', [NotificationController::class, 'updatePreferences']);

    // Teams
    $r->get('/teams', [TeamController::class, 'index']);
    $r->post('/teams', [TeamController::class, 'store']);
    $r->get('/teams/{id}', [TeamController::class, 'show']);
    $r->post('/teams/{id}/invite', [TeamController::class, 'inviteMember']);
    $r->post('/teams/{id}/remove-member', [TeamController::class, 'removeMember']);
    $r->post('/teams/{id}/delete', [TeamController::class, 'destroy']);

    // Profile & Account Settings
    $r->get('/profile', [ProfileController::class, 'index']);
    $r->post('/profile', [ProfileController::class, 'updateProfile']);
    $r->get('/profile/security', [ProfileController::class, 'security']);
    $r->post('/profile/security', [ProfileController::class, 'updatePassword']);
    $r->get('/profile/discord', [ProfileController::class, 'discord']);
    $r->get('/profile/api-tokens', [ProfileController::class, 'apiTokens']);
    $r->post('/profile/api-tokens', [ProfileController::class, 'createApiToken']);
    $r->post('/profile/api-tokens/{id}/delete', [ProfileController::class, 'revokeApiToken']);
    $r->post('/profile/delete-account', [ProfileController::class, 'deleteAccount']);
});

// Admin Panel Routes
$router->group(['prefix' => 'admin', 'middleware' => [MaintenanceMiddleware::class, AdminMiddleware::class, CsrfMiddleware::class]], function (Router $r) {
    $r->get('', [AdminController::class, 'dashboard']);
    $r->get('/dashboard', [AdminController::class, 'dashboard']);
    $r->get('/users', [AdminController::class, 'users']);
    $r->post('/users/{id}/role', [AdminController::class, 'updateUserRole']);
    $r->post('/users/{id}/toggle-ban', [AdminController::class, 'toggleUserBan']);
    $r->post('/users/{id}/delete', [AdminController::class, 'deleteUser']);
    $r->get('/events', [AdminController::class, 'events']);
    $r->get('/webhooks', [AdminController::class, 'webhooks']);
    $r->get('/logs', [AdminController::class, 'logs']);
    $r->post('/logs/clear', [AdminController::class, 'clearLogs']);
    $r->get('/settings', [AdminController::class, 'settings']);
    $r->post('/settings', [AdminController::class, 'updateSettings']);
});

// RESTful JSON API (v1)
$router->group(['prefix' => 'api/v1'], function (Router $r) {
    $r->post('/auth/login', [ApiController::class, 'login']);

    // Protected API Endpoints (Bearer Token)
    $r->group(['middleware' => [ApiAuthMiddleware::class]], function (Router $api) {
        $api->get('/events', [ApiController::class, 'listEvents']);
        $api->post('/events', [ApiController::class, 'createEvent']);
        $api->get('/events/{id}', [ApiController::class, 'getEvent']);
        $api->put('/events/{id}', [ApiController::class, 'updateEvent']);
        $api->delete('/events/{id}', [ApiController::class, 'deleteEvent']);
        $api->get('/categories', [ApiController::class, 'listCategories']);
        $api->get('/notifications', [ApiController::class, 'listNotifications']);
        $api->post('/webhooks/test', [ApiController::class, 'testWebhook']);
    });
});
