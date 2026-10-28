<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Event;
use App\Models\Setting;
use App\Models\User;
use App\Models\Webhook;
use Core\Controller;
use Core\Database;

class AdminController extends Controller
{
    public function dashboard(): void
    {
        $this->requireAdmin();

        $db = Database::getInstance();

        $stats = [
            'total_users'     => (int)$db->fetchColumn("SELECT COUNT(*) FROM `users` WHERE `deleted_at` IS NULL"),
            'total_events'    => (int)$db->fetchColumn("SELECT COUNT(*) FROM `events` WHERE `deleted_at` IS NULL"),
            'total_webhooks'  => (int)$db->fetchColumn("SELECT COUNT(*) FROM `webhooks`"),
            'upcoming_events' => (int)$db->fetchColumn("SELECT COUNT(*) FROM `events` WHERE `start_datetime` >= NOW() AND `deleted_at` IS NULL"),
            'total_logs'      => (int)$db->fetchColumn("SELECT COUNT(*) FROM `webhook_logs`")
        ];

        $recentActivity = ActivityLog::getRecent(10);
        $recentUsers = User::where("`deleted_at` IS NULL", [], "id DESC", 5);

        $systemInfo = [
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'PHP CLI / Built-in',
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'db_version' => $db->fetchColumn("SELECT VERSION()"),
            'os' => PHP_OS
        ];

        $this->render('admin.dashboard', [
            'title'          => 'Admin Dashboard - Dischedule',
            'stats'          => $stats,
            'recentActivity' => $recentActivity,
            'recentUsers'    => $recentUsers,
            'systemInfo'     => $systemInfo
        ], 'admin');
    }

    public function users(): void
    {
        $this->requireAdmin();

        $page = (int)$this->request->query('page', 1);
        $search = trim((string)$this->request->query('search', ''));
        $role = $this->request->query('role');
        $status = $this->request->query('status');

        $where = "`deleted_at` IS NULL";
        $params = [];

        if ($search !== '') {
            $where .= " AND (`username` LIKE :search OR `email` LIKE :search)";
            $params['search'] = "%{$search}%";
        }
        if (!empty($role)) {
            $where .= " AND `role` = :role";
            $params['role'] = $role;
        }
        if (!empty($status)) {
            $where .= " AND `status` = :status";
            $params['status'] = $status;
        }

        $paginator = User::paginate($page, 20, $where, $params, 'id DESC');

        $this->render('admin.users', [
            'title'     => 'User Management - Dischedule Admin',
            'paginator' => $paginator,
            'search'    => $search,
            'role'      => $role,
            'status'    => $status
        ], 'admin');
    }

    public function updateUserRole(): void
    {
        $this->requireAdmin();
        $id = (int)$this->request->getRouteParam('id');
        $newRole = (string)$this->request->input('role');

        if (in_array($newRole, ['admin', 'moderator', 'user'], true)) {
            User::update($id, ['role' => $newRole]);
            ActivityLog::log($this->userId(), 'admin_user_role_changed', "Changed role of user #{$id} to {$newRole}.");
            $this->flash('success', 'User role updated.');
        }

        $this->back();
    }

    public function toggleUserBan(): void
    {
        $this->requireAdmin();
        $id = (int)$this->request->getRouteParam('id');
        $user = User::find($id);

        if ($user) {
            $newStatus = ($user['status'] === 'banned') ? 'active' : 'banned';
            User::update($id, ['status' => $newStatus]);
            ActivityLog::log($this->userId(), 'admin_user_ban_toggle', "Set user #{$id} status to {$newStatus}.");
            $this->flash('info', "User account status changed to {$newStatus}.");
        }

        $this->back();
    }

    public function deleteUser(): void
    {
        $this->requireAdmin();
        $id = (int)$this->request->getRouteParam('id');
        
        if ($id === $this->userId()) {
            $this->flash('error', 'You cannot delete your own admin account.');
            $this->back();
            return;
        }

        User::softDelete($id);
        ActivityLog::log($this->userId(), 'admin_user_deleted', "Deleted user #{$id}.");
        $this->flash('success', 'User deleted.');
        $this->back();
    }

    public function events(): void
    {
        $this->requireAdmin();

        $page = (int)$this->request->query('page', 1);
        $search = trim((string)$this->request->query('search', ''));

        $where = "`deleted_at` IS NULL";
        $params = [];

        if ($search !== '') {
            $where .= " AND (`title` LIKE :search OR `description` LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $paginator = Event::paginate($page, 20, $where, $params, 'id DESC');

        $this->render('admin.events', [
            'title'     => 'All Events Management - Dischedule Admin',
            'paginator' => $paginator,
            'search'    => $search
        ], 'admin');
    }

    public function webhooks(): void
    {
        $this->requireAdmin();

        $db = Database::getInstance();
        $logs = $db->fetchAll(
            "SELECT wl.*, w.name AS webhook_name, u.username 
             FROM `webhook_logs` wl
             LEFT JOIN `webhooks` w ON w.id = wl.webhook_id
             LEFT JOIN `users` u ON u.id = wl.user_id
             ORDER BY wl.id DESC LIMIT 50"
        );

        $this->render('admin.webhooks', [
            'title' => 'Webhook Delivery Logs - Dischedule Admin',
            'logs'  => $logs
        ], 'admin');
    }

    public function logs(): void
    {
        $this->requireAdmin();

        $logFile = dirname(__DIR__, 2) . '/storage/logs/app-' . date('Y-m-d') . '.log';
        $logContent = file_exists($logFile) ? (string)file_get_contents($logFile) : 'No logs recorded today.';

        $this->render('admin.logs', [
            'title'      => 'System Logs - Dischedule Admin',
            'logContent' => $logContent
        ], 'admin');
    }

    public function clearLogs(): void
    {
        $this->requireAdmin();
        $logFile = dirname(__DIR__, 2) . '/storage/logs/app-' . date('Y-m-d') . '.log';
        if (file_exists($logFile)) {
            @unlink($logFile);
        }
        $this->flash('success', 'Today log file cleared.');
        $this->redirect('/admin/logs');
    }

    public function settings(): void
    {
        $this->requireAdmin();
        $settings = Setting::allSettings();

        $this->render('admin.settings', [
            'title'    => 'Platform Settings - Dischedule Admin',
            'settings' => $settings
        ], 'admin');
    }

    public function updateSettings(): void
    {
        $this->requireAdmin();
        $input = $this->request->all();

        foreach ($input as $key => $val) {
            if ($key === '_csrf_token') continue;
            Setting::set($key, $val);
        }

        ActivityLog::log($this->userId(), 'admin_settings_updated', 'Updated system settings.');
        $this->flash('success', 'System settings saved.');
        $this->redirect('/admin/settings');
    }
}
