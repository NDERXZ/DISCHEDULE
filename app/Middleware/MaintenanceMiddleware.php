<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Models\Setting;
use Core\Request;
use Core\Response;
use Core\Session;
use Core\View;

class MaintenanceMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, Response $response): void
    {
        $inMaintenance = filter_var($_ENV['MAINTENANCE_MODE'] ?? false, FILTER_VALIDATE_BOOLEAN);
        
        try {
            $setting = Setting::get('maintenance_mode');
            if ($setting !== null) {
                $inMaintenance = filter_var($setting, FILTER_VALIDATE_BOOLEAN);
            }
        } catch (\Throwable) {}

        if ($inMaintenance) {
            $user = Session::getUser();
            if ($user && ($user['role'] ?? '') === 'admin') {
                return; // Allow admins to browse during maintenance
            }

            if ($request->isAjax() || str_starts_with($request->getPath(), '/api/')) {
                $response->json([
                    'success' => false,
                    'message' => 'Dischedule is currently undergoing scheduled maintenance. Please check back soon.'
                ], 503);
                exit;
            }

            $response->setStatusCode(503);
            $html = View::render('errors.maintenance', ['title' => 'Service Maintenance'], null);
            $response->html($html);
            exit;
        }
    }
}
