<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use App\Services\NotificationService;
use Core\Controller;

class CalendarController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        $categories = Category::getForUser($userId);
        $tags = Tag::popular(15);
        $viewType = $this->request->query('view', 'month');

        $this->render('calendar.index', [
            'title'       => 'Calendar - Dischedule',
            'categories'  => $categories,
            'tags'        => $tags,
            'currentView' => $viewType
        ], 'main');
    }

    public function eventsFeed(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        $start = $this->request->query('start', date('Y-m-01 00:00:00'));
        $end = $this->request->query('end', date('Y-m-t 23:59:59'));

        $filters = [
            'category_id' => $this->request->query('category_id'),
            'priority'    => $this->request->query('priority'),
            'status'      => $this->request->query('status'),
            'tag_id'      => $this->request->query('tag_id'),
            'search'      => $this->request->query('search')
        ];

        $events = Event::getEventsForRange($userId, $start, $end, $filters);
        $this->json($events);
    }

    public function quickMove(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        $eventId = (int)$this->request->input('event_id');
        $newStart = (string)$this->request->input('start');
        $newEnd = (string)$this->request->input('end');

        $event = Event::find($eventId);
        if (!$event || ((int)$event['user_id'] !== $userId && !$this->isAdmin())) {
            $this->json(['success' => false, 'message' => 'Event not found or access denied.'], 403);
            return;
        }

        Event::update($eventId, [
            'start_datetime' => $newStart,
            'end_datetime'   => $newEnd
        ]);

        $updatedEvent = Event::findWithDetails($eventId);
        $notifService = new NotificationService();
        $notifService->onEventUpdated($updatedEvent, $userId);

        $this->json([
            'success' => true,
            'message' => 'Event schedule moved successfully.'
        ]);
    }

    public function quickResize(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        $eventId = (int)$this->request->input('event_id');
        $newEnd = (string)$this->request->input('end');

        $event = Event::find($eventId);
        if (!$event || ((int)$event['user_id'] !== $userId && !$this->isAdmin())) {
            $this->json(['success' => false, 'message' => 'Event not found or access denied.'], 403);
            return;
        }

        Event::update($eventId, [
            'end_datetime' => $newEnd
        ]);

        $updatedEvent = Event::findWithDetails($eventId);
        $notifService = new NotificationService();
        $notifService->onEventUpdated($updatedEvent, $userId);

        $this->json([
            'success' => true,
            'message' => 'Event duration updated.'
        ]);
    }
}
