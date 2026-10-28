<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Webhook;

/**
 * Dischedule Multi-Channel Notification Dispatcher
 */
class NotificationService
{
    private DiscordService $discord;
    private EmailService $email;

    public function __construct()
    {
        $this->discord = new DiscordService();
        $this->email = new EmailService();
    }

    public function onEventCreated(array $event, int $userId): void
    {
        $user = User::find($userId);
        if (!$user) return;

        // 1. In-App
        if (!empty($user['notify_in_app'])) {
            Notification::notify(
                $userId,
                "Event Scheduled: " . $event['title'],
                "Your event '{$event['title']}' was successfully created.",
                "/events/" . $event['id'],
                'success'
            );
        }

        // 2. Discord Webhooks
        if (!empty($user['notify_discord'])) {
            $webhooks = Webhook::getForEvent((int)$event['id']);
            if (empty($webhooks)) {
                $default = Webhook::getDefault($userId);
                if ($default) $webhooks = [$default];
            }

            $urls = array_column($webhooks, 'url');
            if (!empty($urls)) {
                $this->discord->sendEventNotification($event, $urls, 'created');
            }
        }
    }

    public function onEventUpdated(array $event, int $userId): void
    {
        $user = User::find($userId);
        if (!$user) return;

        if (!empty($user['notify_in_app'])) {
            Notification::notify(
                $userId,
                "Event Updated: " . $event['title'],
                "Event details for '{$event['title']}' have been modified.",
                "/events/" . $event['id'],
                'info'
            );
        }

        if (!empty($user['notify_discord'])) {
            $webhooks = Webhook::getForEvent((int)$event['id']);
            if (empty($webhooks)) {
                $default = Webhook::getDefault($userId);
                if ($default) $webhooks = [$default];
            }

            $urls = array_column($webhooks, 'url');
            if (!empty($urls)) {
                $this->discord->sendEventNotification($event, $urls, 'updated');
            }
        }
    }

    public function onEventDeleted(array $event, int $userId): void
    {
        $user = User::find($userId);
        if (!$user) return;

        if (!empty($user['notify_in_app'])) {
            Notification::notify(
                $userId,
                "Event Cancelled: " . $event['title'],
                "Event '{$event['title']}' has been removed from your calendar.",
                "/calendar",
                'warning'
            );
        }

        if (!empty($user['notify_discord'])) {
            $webhooks = Webhook::getForEvent((int)$event['id']);
            if (empty($webhooks)) {
                $default = Webhook::getDefault($userId);
                if ($default) $webhooks = [$default];
            }

            $urls = array_column($webhooks, 'url');
            if (!empty($urls)) {
                $this->discord->sendEventNotification($event, $urls, 'deleted');
            }
        }
    }

    public function onReminderFired(array $reminder): void
    {
        $userId = (int)$reminder['user_id'];
        $user = User::find($userId);
        if (!$user) return;

        $type = $reminder['type']; // discord, email, in_app

        if ($type === 'in_app' || $type === 'all') {
            Notification::notify(
                $userId,
                "⏰ Reminder: " . $reminder['title'],
                "Event starting in {$reminder['minutes_before']} minutes at {$reminder['start_datetime']} ({$user['timezone']})",
                "/events/" . $reminder['event_id'],
                'warning'
            );
        }

        if ($type === 'email' || $type === 'all') {
            $this->email->sendEventReminderEmail($user, $reminder);
        }

        if ($type === 'discord' || $type === 'all') {
            $webhooks = Webhook::getForEvent((int)$reminder['event_id']);
            if (empty($webhooks)) {
                $default = Webhook::getDefault($userId);
                if ($default) $webhooks = [$default];
            }

            $urls = array_column($webhooks, 'url');
            if (!empty($urls)) {
                $this->discord->sendEventNotification($reminder, $urls, 'reminder');
            }
        }
    }
}
