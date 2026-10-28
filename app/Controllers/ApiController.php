<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\DateHelper;
use App\Helpers\StringHelper;
use App\Models\ApiToken;
use App\Models\Category;
use App\Models\Event;
use App\Models\Notification;
use App\Models\Tag;
use App\Models\User;
use App\Models\Webhook;
use App\Services\DiscordService;
use App\Services\NotificationService;
use Core\Controller;
use Core\Validator;

class ApiController extends Controller
{
    private DiscordService $discordService;
    private NotificationService $notifService;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->discordService = new DiscordService();
        $this->notifService = new NotificationService();
    }

    public function login(): void
    {
        $input = $this->request->json();
        $validator = Validator::make($input, [
            'login'    => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
            return;
        }

        $login = trim((string)$input['login']);
        $password = (string)$input['password'];

        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::findByEmail($login)
            : User::findByUsername($login);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->json(['success' => false, 'message' => 'Invalid credentials.'], 401);
            return;
        }

        $tokenResult = ApiToken::createToken((int)$user['id'], 'API Login Token', 'read,write', 30);

        $this->json([
            'success' => true,
            'token'   => $tokenResult['plain_token'],
            'expires' => $tokenResult['expires_at'],
            'user'    => User::sanitize($user)
        ]);
    }

    public function listEvents(): void
    {
        $userId = $this->userId();
        $page = (int)$this->request->query('page', 1);
        $search = (string)$this->request->query('search', '');
        $categoryId = $this->request->query('category_id');

        $where = "(`user_id` = :uid OR `visibility` = 'public') AND `deleted_at` IS NULL";
        $params = ['uid' => $userId];

        if ($search !== '') {
            $where .= " AND (`title` LIKE :s OR `description` LIKE :s)";
            $params['s'] = "%{$search}%";
        }
        if ($categoryId) {
            $where .= " AND `category_id` = :c";
            $params['c'] = (int)$categoryId;
        }

        $paginator = Event::paginate($page, 20, $where, $params, 'start_datetime ASC');
        $this->json(['success' => true, 'data' => $paginator]);
    }

    public function createEvent(): void
    {
        $userId = $this->userId();
        $input = $this->request->json();

        $validator = Validator::make($input, [
            'title'          => 'required|max:255',
            'start_datetime' => 'required|datetime',
            'end_datetime'   => 'required|datetime'
        ]);

        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
            return;
        }

        $user = $this->user();
        $userTz = $user['timezone'] ?? 'UTC';

        $startUtc = DateHelper::convertUserToUtc((string)$input['start_datetime'], $userTz);
        $endUtc = DateHelper::convertUserToUtc((string)$input['end_datetime'], $userTz);
        $title = trim((string)$input['title']);
        $slug = StringHelper::slug($title) . '-' . substr(bin2hex(random_bytes(4)), 0, 6);

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
            'priority'         => $input['priority'] ?? 'medium',
            'status'           => $input['status'] ?? 'published',
            'visibility'       => $input['visibility'] ?? 'private',
            'recurrence_rule'  => $input['recurrence_rule'] ?? null
        ]);

        if (!empty($input['tags'])) {
            Tag::syncEventTags($eventId, $input['tags']);
        }

        $created = Event::findWithDetails($eventId);
        $this->notifService->onEventCreated($created, $userId);

        $this->json(['success' => true, 'data' => $created], 201);
    }

    public function getEvent(): void
    {
        $id = (int)$this->request->getRouteParam('id');
        $event = Event::findWithDetails($id, $this->userId());

        if (!$event) {
            $this->json(['success' => false, 'message' => 'Event not found.'], 404);
            return;
        }

        $this->json(['success' => true, 'data' => $event]);
    }

    public function updateEvent(): void
    {
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $event = Event::find($id);

        if (!$event || ((int)$event['user_id'] !== $userId && !$this->isAdmin())) {
            $this->json(['success' => false, 'message' => 'Unauthorized or event not found.'], 403);
            return;
        }

        $input = $this->request->json();
        $validator = Validator::make($input, [
            'title'          => 'required|max:255',
            'start_datetime' => 'required|datetime',
            'end_datetime'   => 'required|datetime'
        ]);

        if ($validator->fails()) {
            $this->json(['success' => false, 'errors' => $validator->errors()], 422);
            return;
        }

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
            'priority'         => $input['priority'] ?? 'medium',
            'status'           => $input['status'] ?? 'published',
            'visibility'       => $input['visibility'] ?? 'private',
            'recurrence_rule'  => $input['recurrence_rule'] ?? null
        ]);

        $updated = Event::findWithDetails($id);
        $this->notifService->onEventUpdated($updated, $userId);

        $this->json(['success' => true, 'data' => $updated]);
    }

    public function deleteEvent(): void
    {
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $event = Event::find($id);

        if (!$event || ((int)$event['user_id'] !== $userId && !$this->isAdmin())) {
            $this->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            return;
        }

        Event::softDelete($id);
        $this->notifService->onEventDeleted($event, $userId);

        $this->json(['success' => true, 'message' => 'Event deleted.']);
    }

    public function listCategories(): void
    {
        $userId = $this->userId();
        $categories = Category::getForUser($userId);
        $this->json(['success' => true, 'data' => $categories]);
    }

    public function listNotifications(): void
    {
        $userId = $this->userId();
        $notifs = Notification::getForUser($userId, 20);
        $this->json(['success' => true, 'data' => $notifs]);
    }

    public function testWebhook(): void
    {
        $input = $this->request->json();
        $url = (string)($input['url'] ?? '');
        $mention = (string)($input['mention_role'] ?? '');

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            $this->json(['success' => false, 'message' => 'Valid URL required.'], 422);
            return;
        }

        $res = $this->discordService->testWebhook($url, $mention);
        $this->json($res, $res['success'] ? 200 : 400);
    }
}
