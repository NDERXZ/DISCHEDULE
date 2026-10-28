<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\DateHelper;
use App\Helpers\StringHelper;
use App\Models\ActivityLog;
use App\Models\Attachment;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Event;
use App\Models\Reminder;
use App\Models\Tag;
use App\Models\Webhook;
use App\Services\CSVService;
use App\Services\ICSExportService;
use App\Services\ICSImportService;
use App\Services\NotificationService;
use Core\Controller;
use Core\Session;

class EventController extends Controller
{
    private NotificationService $notifService;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->notifService = new NotificationService();
    }

    public function index(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        $page = (int)$this->request->query('page', 1);
        $search = trim((string)$this->request->query('search', ''));
        $categoryId = $this->request->query('category_id');
        $priority = $this->request->query('priority');
        $status = $this->request->query('status');

        $where = "(`user_id` = :uid OR `visibility` = 'public') AND `deleted_at` IS NULL";
        $params = ['uid' => $userId];

        if ($search !== '') {
            $where .= " AND (`title` LIKE :search OR `description` LIKE :search OR `location` LIKE :search)";
            $params['search'] = "%{$search}%";
        }
        if (!empty($categoryId)) {
            $where .= " AND `category_id` = :cat";
            $params['cat'] = (int)$categoryId;
        }
        if (!empty($priority)) {
            $where .= " AND `priority` = :prio";
            $params['prio'] = $priority;
        }
        if (!empty($status)) {
            $where .= " AND `status` = :stat";
            $params['stat'] = $status;
        }

        $paginator = Event::paginate($page, 15, $where, $params, 'start_datetime DESC');
        $categories = Category::getForUser($userId);

        $this->render('events.index', [
            'title'      => 'All Events - Dischedule',
            'paginator'  => $paginator,
            'categories' => $categories,
            'filters'    => [
                'search'      => $search,
                'category_id' => $categoryId,
                'priority'    => $priority,
                'status'      => $status
            ]
        ], 'main');
    }

    public function create(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        $categories = Category::getForUser($userId);
        $webhooks = Webhook::getForUser($userId);
        $initialDate = $this->request->query('date', date('Y-m-d'));
        $initialStart = $this->request->query('start', $initialDate . 'T09:00');
        $initialEnd = $this->request->query('end', $initialDate . 'T10:00');

        $this->render('events.create', [
            'title'        => 'Create Event - Dischedule',
            'categories'   => $categories,
            'webhooks'     => $webhooks,
            'initialDate'  => $initialDate,
            'initialStart' => $initialStart,
            'initialEnd'   => $initialEnd
        ], 'main');
    }

    public function store(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $input = $this->request->all();

        $this->validate($input, [
            'title'          => 'required|max:255',
            'start_datetime' => 'required|datetime',
            'end_datetime'   => 'required|datetime'
        ]);

        $user = $this->user();
        $userTz = $user['timezone'] ?? 'UTC';

        $title = trim((string)$input['title']);
        $slug = StringHelper::slug($title) . '-' . substr(bin2hex(random_bytes(4)), 0, 6);

        // Convert user local datetime to UTC for database storage
        $startUtc = DateHelper::convertUserToUtc((string)$input['start_datetime'], $userTz);
        $endUtc = DateHelper::convertUserToUtc((string)$input['end_datetime'], $userTz);

        $eventId = Event::create([
            'user_id'          => $userId,
            'category_id'      => !empty($input['category_id']) ? (int)$input['category_id'] : null,
            'title'            => $title,
            'slug'             => $slug,
            'description'      => $input['description'] ?? null,
            'start_datetime'   => $startUtc,
            'end_datetime'     => $endUtc,
            'timezone'         => $userTz,
            'location'         => $input['location'] ?? null,
            'location_url'     => $input['location_url'] ?? null,
            'color'            => $input['color'] ?? null,
            'priority'         => $input['priority'] ?? 'medium',
            'status'           => $input['status'] ?? 'published',
            'visibility'       => $input['visibility'] ?? 'private',
            'recurrence_rule'  => !empty($input['recurrence_rule']) ? $input['recurrence_rule'] : null,
            'recurrence_end'   => !empty($input['recurrence_end']) ? DateHelper::convertUserToUtc((string)$input['recurrence_end'], $userTz) : null,
            'recurrence_count' => !empty($input['recurrence_count']) ? (int)$input['recurrence_count'] : null,
            'created_at'       => date('Y-m-d H:i:s')
        ]);

        // Tags
        if (!empty($input['tags'])) {
            Tag::syncEventTags($eventId, (string)$input['tags']);
        }

        // Webhooks
        if (isset($input['webhooks']) && is_array($input['webhooks'])) {
            Webhook::syncEventWebhooks($eventId, $input['webhooks']);
        }

        // Reminders
        if (isset($input['reminders']) && is_array($input['reminders'])) {
            Reminder::syncEventReminders($eventId, $input['reminders'], $startUtc);
        }

        // Handle file attachment if uploaded
        $this->handleUploadedAttachment($eventId, $userId);

        $createdEvent = Event::findWithDetails($eventId);
        $this->notifService->onEventCreated($createdEvent, $userId);
        ActivityLog::log($userId, 'event_created', "Created event '{$title}'.", $eventId);

        if ($this->request->isAjax()) {
            $this->json([
                'success'  => true,
                'message'  => 'Event created successfully!',
                'event_id' => $eventId,
                'redirect' => url('events/' . $eventId)
            ]);
            return;
        }

        $this->flash('success', 'Event scheduled successfully!');
        $this->redirect('/events/' . $eventId);
    }

    public function show(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $event = Event::findWithDetails($id, $this->userId());

        if (!$event) {
            $this->response->setStatusCode(404);
            $this->render('errors.404', ['title' => 'Event Not Found'], 'main');
            return;
        }

        $this->render('events.show', [
            'title' => $event['title'] . ' - Dischedule',
            'event' => $event
        ], 'main');
    }

    public function edit(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $event = Event::findWithDetails($id, $userId);

        if (!$event || ((int)$event['user_id'] !== $userId && !$this->isAdmin())) {
            $this->flash('error', 'Event not found or unauthorized to edit.');
            $this->redirect('/calendar');
            return;
        }

        $categories = Category::getForUser($userId);
        $webhooks = Webhook::getForUser($userId);

        $this->render('events.edit', [
            'title'      => 'Edit: ' . $event['title'] . ' - Dischedule',
            'event'      => $event,
            'categories' => $categories,
            'webhooks'   => $webhooks
        ], 'main');
    }

    public function update(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $event = Event::find($id);

        if (!$event || ((int)$event['user_id'] !== $userId && !$this->isAdmin())) {
            $this->flash('error', 'Unauthorized.');
            $this->redirect('/calendar');
            return;
        }

        $input = $this->request->all();
        $this->validate($input, [
            'title'          => 'required|max:255',
            'start_datetime' => 'required|datetime',
            'end_datetime'   => 'required|datetime'
        ]);

        $user = $this->user();
        $userTz = $user['timezone'] ?? 'UTC';

        $startUtc = DateHelper::convertUserToUtc((string)$input['start_datetime'], $userTz);
        $endUtc = DateHelper::convertUserToUtc((string)$input['end_datetime'], $userTz);

        Event::update($id, [
            'category_id'      => !empty($input['category_id']) ? (int)$input['category_id'] : null,
            'title'            => trim((string)$input['title']),
            'description'      => $input['description'] ?? null,
            'start_datetime'   => $startUtc,
            'end_datetime'     => $endUtc,
            'location'         => $input['location'] ?? null,
            'location_url'     => $input['location_url'] ?? null,
            'color'            => $input['color'] ?? null,
            'priority'         => $input['priority'] ?? 'medium',
            'status'           => $input['status'] ?? 'published',
            'visibility'       => $input['visibility'] ?? 'private',
            'recurrence_rule'  => !empty($input['recurrence_rule']) ? $input['recurrence_rule'] : null,
            'recurrence_end'   => !empty($input['recurrence_end']) ? DateHelper::convertUserToUtc((string)$input['recurrence_end'], $userTz) : null,
            'recurrence_count' => !empty($input['recurrence_count']) ? (int)$input['recurrence_count'] : null
        ]);

        // Tags
        if (isset($input['tags'])) {
            Tag::syncEventTags($id, (string)$input['tags']);
        }

        // Webhooks
        if (isset($input['webhooks']) && is_array($input['webhooks'])) {
            Webhook::syncEventWebhooks($id, $input['webhooks']);
        }

        // Reminders
        if (isset($input['reminders']) && is_array($input['reminders'])) {
            Reminder::syncEventReminders($id, $input['reminders'], $startUtc);
        }

        // Upload attachment
        $this->handleUploadedAttachment($id, $userId);

        $updatedEvent = Event::findWithDetails($id);
        $this->notifService->onEventUpdated($updatedEvent, $userId);
        ActivityLog::log($userId, 'event_updated', "Updated event '{$input['title']}'.", $id);

        if ($this->request->isAjax()) {
            $this->json([
                'success' => true,
                'message' => 'Event updated successfully!',
                'event'   => $updatedEvent
            ]);
            return;
        }

        $this->flash('success', 'Event updated successfully!');
        $this->redirect('/events/' . $id);
    }

    public function destroy(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $event = Event::find($id);

        if (!$event || ((int)$event['user_id'] !== $userId && !$this->isAdmin())) {
            $this->flash('error', 'Unauthorized or event not found.');
            $this->redirect('/calendar');
            return;
        }

        Event::softDelete($id);
        $this->notifService->onEventDeleted($event, $userId);
        ActivityLog::log($userId, 'event_deleted', "Deleted event '{$event['title']}'.", $id);

        if ($this->request->isAjax()) {
            $this->json(['success' => true, 'message' => 'Event deleted.']);
            return;
        }

        $this->flash('success', 'Event has been deleted.');
        $this->redirect('/calendar');
    }

    public function duplicate(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();

        try {
            $newId = Event::duplicate($id, $userId);
            ActivityLog::log($userId, 'event_duplicated', "Cloned event #{$id} into #{$newId}.", $newId);
            $this->flash('success', 'Event duplicated successfully! You can now adjust its details.');
            $this->redirect('/events/' . $newId . '/edit');
        } catch (\Throwable $e) {
            $this->flash('error', 'Failed to duplicate event: ' . $e->getMessage());
            $this->redirect('/events/' . $id);
        }
    }

    public function bulkAction(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $isAdmin = $this->isAdmin();

        $action = (string)$this->request->input('action');
        $ids = (array)$this->request->input('event_ids', []);

        if (empty($ids)) {
            $this->flash('warning', 'No events selected.');
            $this->back();
            return;
        }

        switch ($action) {
            case 'delete':
                $count = Event::bulkDelete($ids, $userId, $isAdmin);
                $this->flash('success', "{$count} event(s) deleted.");
                break;
            case 'archive':
                $count = Event::bulkArchive($ids, $userId, $isAdmin);
                $this->flash('success', "{$count} event(s) archived.");
                break;
            case 'change_category':
                $catId = (int)$this->request->input('target_category_id');
                $count = Event::bulkChangeCategory($ids, $catId, $userId, $isAdmin);
                $this->flash('success', "{$count} event(s) updated.");
                break;
            default:
                $this->flash('error', 'Invalid bulk action requested.');
                break;
        }

        $this->back();
    }

    public function addComment(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();

        $content = trim((string)$this->request->input('content', ''));
        if (empty($content)) {
            $this->flash('error', 'Comment cannot be empty.');
            $this->redirect('/events/' . $id);
            return;
        }

        Comment::create([
            'event_id' => $id,
            'user_id'  => $userId,
            'content'  => $content
        ]);

        ActivityLog::log($userId, 'comment_added', 'Added comment to event.', $id);
        $this->flash('success', 'Comment posted.');
        $this->redirect('/events/' . $id);
    }

    public function generateShareLink(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $event = Event::find($id);

        if (!$event || ((int)$event['user_id'] !== $userId && !$this->isAdmin())) {
            $this->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            return;
        }

        $shareToken = bin2hex(random_bytes(20));
        $password = $this->request->input('share_password');
        $passwordHash = !empty($password) ? password_hash((string)$password, PASSWORD_BCRYPT) : null;
        $expiresAt = $this->request->input('share_expires_at');

        Event::update($id, [
            'visibility'          => 'public',
            'share_token'         => $shareToken,
            'share_password_hash' => $passwordHash,
            'share_expires_at'    => !empty($expiresAt) ? date('Y-m-d H:i:s', strtotime((string)$expiresAt)) : null
        ]);

        $shareUrl = url('share/' . $shareToken);

        $this->json([
            'success'   => true,
            'message'   => 'Public share link generated!',
            'share_url' => $shareUrl
        ]);
    }

    public function exportICS(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $eventId = (int)$this->request->query('event_id', 0);

        if ($eventId > 0) {
            $event = Event::findWithDetails($eventId, $userId);
            $events = $event ? [$event] : [];
            $filename = 'event-' . $eventId . '.ics';
        } else {
            $events = Event::getEventsForRange($userId, date('Y-01-01 00:00:00'), date('Y-12-31 23:59:59'));
            $filename = 'dischedule-calendar-' . date('Y-m-d') . '.ics';
        }

        $icsData = ICSExportService::export($events, 'Dischedule Calendar');

        $this->response->setHeader('Content-Type', 'text/calendar; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $this->response->setContent($icsData);
        $this->response->send();
        exit;
    }

    public function exportCSV(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        $events = Event::getEventsForRange($userId, date('Y-01-01 00:00:00'), date('Y-12-31 23:59:59'));
        $csv = CSVService::export($events);

        $this->response->setHeader('Content-Type', 'text/csv; charset=utf-8');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="dischedule-events-' . date('Y-m-d') . '.csv"');
        $this->response->setContent($csv);
        $this->response->send();
        exit;
    }

    public function showImportExport(): void
    {
        $this->requireAuth();
        $this->render('events.import_export', ['title' => 'Import & Export Events - Dischedule'], 'main');
    }

    public function processImport(): void
    {
        $this->requireAuth();
        $userId = $this->userId();

        $file = $this->request->file('import_file');
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Please upload a valid .ics or .csv file.');
            $this->back();
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $events = [];

        if ($ext === 'ics') {
            $content = file_get_contents($file['tmp_name']);
            $events = ICSImportService::parse((string)$content);
        } elseif ($ext === 'csv') {
            $events = CSVService::import($file['tmp_name']);
        } else {
            $this->flash('error', 'Only .ics and .csv calendar files are supported.');
            $this->back();
            return;
        }

        $importedCount = 0;
        foreach ($events as $e) {
            $title = $e['title'] ?? 'Imported Event';
            $slug = StringHelper::slug($title) . '-' . substr(bin2hex(random_bytes(4)), 0, 6);

            Event::create([
                'user_id'         => $userId,
                'title'           => $title,
                'slug'            => $slug,
                'description'     => $e['description'] ?? null,
                'start_datetime'  => $e['start_datetime'] ?? date('Y-m-d H:i:s'),
                'end_datetime'    => $e['end_datetime'] ?? date('Y-m-d H:i:s', strtotime('+1 hour')),
                'location'        => $e['location'] ?? null,
                'status'          => 'published',
                'visibility'      => 'private',
                'recurrence_rule' => $e['recurrence_rule'] ?? null
            ]);
            $importedCount++;
        }

        $this->flash('success', "Successfully imported {$importedCount} event(s) into your calendar!");
        $this->redirect('/calendar');
    }

    private function handleUploadedAttachment(int $eventId, int $userId): void
    {
        $file = $this->request->file('attachment');
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return;
        }

        $maxMb = (int)($_ENV['MAX_FILE_SIZE_MB'] ?? 10);
        if (($file['size'] / 1048576) > $maxMb) {
            return;
        }

        $uploadDir = dirname(__DIR__, 2) . '/storage/uploads';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $originalName = basename($file['name']);
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $hashedName = bin2hex(random_bytes(16)) . '.' . $ext;
        $targetPath = $uploadDir . '/' . $hashedName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $targetPath) ?: 'application/octet-stream';
            finfo_close($finfo);

            Attachment::create([
                'event_id'      => $eventId,
                'user_id'       => $userId,
                'filename'      => $hashedName,
                'original_name' => $originalName,
                'mime_type'     => $mimeType,
                'size'          => (int)$file['size'],
                'path'          => $targetPath,
                'created_at'    => date('Y-m-d H:i:s')
            ]);
        }
    }
}
