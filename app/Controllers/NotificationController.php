<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Notification;
use App\Models\User;
use Core\Controller;
use Core\Session;

class NotificationController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $notifications = Notification::getForUser($userId, 50);

        $this->render('notifications.index', [
            'title'         => 'Notification Center - Dischedule',
            'notifications' => $notifications
        ], 'main');
    }

    public function unreadList(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        $notifications = Notification::getForUser($userId, 10);
        $unreadCount = Notification::unreadCount($userId);

        $this->json([
            'unread_count'  => $unreadCount,
            'notifications' => $notifications
        ]);
    }

    public function markRead(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();

        Notification::markAsRead($id, $userId);

        if ($this->request->isAjax()) {
            $this->json(['success' => true]);
            return;
        }

        $this->back();
    }

    public function markAllRead(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        Notification::markAllAsRead($userId);

        if ($this->request->isAjax()) {
            $this->json(['success' => true, 'message' => 'All notifications marked as read.']);
            return;
        }

        $this->flash('success', 'All notifications marked as read.');
        $this->redirect('/notifications');
    }

    public function clearAll(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        Notification::clearAll($userId);
        $this->flash('info', 'All notifications cleared.');
        $this->redirect('/notifications');
    }

    public function updatePreferences(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $input = $this->request->all();

        User::update($userId, [
            'notify_email'   => !empty($input['notify_email']) ? 1 : 0,
            'notify_discord' => !empty($input['notify_discord']) ? 1 : 0,
            'notify_in_app'  => !empty($input['notify_in_app']) ? 1 : 0
        ]);

        $updated = User::find($userId);
        Session::setUser(User::sanitize($updated));

        $this->flash('success', 'Notification preferences saved.');
        $this->redirect('/profile#notifications');
    }
}
