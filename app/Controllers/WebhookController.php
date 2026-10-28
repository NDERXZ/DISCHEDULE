<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ActivityLog;
use App\Models\Webhook;
use App\Services\DiscordService;
use Core\Controller;

class WebhookController extends Controller
{
    private DiscordService $discordService;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->discordService = new DiscordService();
    }

    public function index(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $webhooks = Webhook::getForUser($userId);

        $this->render('webhooks.index', [
            'title'    => 'Discord Webhooks - Dischedule',
            'webhooks' => $webhooks
        ], 'main');
    }

    public function store(): void
    {
        $this->requireAuth();
        $userId = $this->userId();
        $input = $this->request->all();

        $this->validate($input, [
            'name' => 'required|max:100',
            'url'  => 'required|url'
        ]);

        $url = trim((string)$input['url']);
        if (!str_contains($url, 'discord.com/api/webhooks/')) {
            $this->flash('error', 'Invalid Discord webhook URL format. Must start with https://discord.com/api/webhooks/...');
            $this->back();
            return;
        }

        $isDefault = !empty($input['is_default']) ? 1 : 0;
        if ($isDefault) {
            Webhook::setDefault(0, $userId); // Reset previous default
        }

        $webhookId = Webhook::create([
            'user_id'      => $userId,
            'name'         => trim((string)$input['name']),
            'url'          => $url,
            'mention_role' => !empty($input['mention_role']) ? trim((string)$input['mention_role']) : null,
            'is_default'   => $isDefault,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        ActivityLog::log($userId, 'webhook_created', "Added Discord webhook '{$input['name']}'.");

        $this->flash('success', 'Discord webhook added successfully! Click "Test Webhook" to verify delivery.');
        $this->redirect('/webhooks');
    }

    public function update(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $webhook = Webhook::find($id);

        if (!$webhook || ((int)$webhook['user_id'] !== $userId && !$this->isAdmin())) {
            $this->flash('error', 'Unauthorized or webhook not found.');
            $this->redirect('/webhooks');
            return;
        }

        $input = $this->request->all();
        $this->validate($input, [
            'name' => 'required|max:100',
            'url'  => 'required|url'
        ]);

        $isDefault = !empty($input['is_default']) ? 1 : 0;
        if ($isDefault) {
            Webhook::setDefault($id, $userId);
        }

        Webhook::update($id, [
            'name'         => trim((string)$input['name']),
            'url'          => trim((string)$input['url']),
            'mention_role' => !empty($input['mention_role']) ? trim((string)$input['mention_role']) : null,
            'is_default'   => $isDefault
        ]);

        ActivityLog::log($userId, 'webhook_updated', "Updated webhook '{$input['name']}'.");

        $this->flash('success', 'Webhook settings updated.');
        $this->redirect('/webhooks');
    }

    public function destroy(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $webhook = Webhook::find($id);

        if (!$webhook || ((int)$webhook['user_id'] !== $userId && !$this->isAdmin())) {
            $this->flash('error', 'Unauthorized or webhook not found.');
            $this->redirect('/webhooks');
            return;
        }

        Webhook::delete($id);
        ActivityLog::log($userId, 'webhook_deleted', "Deleted webhook #{$id}.");

        $this->flash('success', 'Discord webhook removed.');
        $this->redirect('/webhooks');
    }

    public function test(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();
        $webhook = Webhook::find($id);

        if (!$webhook || ((int)$webhook['user_id'] !== $userId && !$this->isAdmin())) {
            $this->json(['success' => false, 'message' => 'Webhook not found.'], 404);
            return;
        }

        $result = $this->discordService->testWebhook($webhook['url'], (string)($webhook['mention_role'] ?? ''));

        if ($result['success']) {
            $this->json([
                'success' => true,
                'message' => 'Discord test message delivered successfully! Check your Discord channel.'
            ]);
        } else {
            $this->json([
                'success'   => false,
                'message'   => "Failed to deliver webhook. HTTP Status: {$result['http_code']}. " . ($result['error'] ?: $result['response_body']),
                'http_code' => $result['http_code']
            ], 400);
        }
    }

    public function setDefault(): void
    {
        $this->requireAuth();
        $id = (int)$this->request->getRouteParam('id');
        $userId = $this->userId();

        Webhook::setDefault($id, $userId);
        $this->flash('success', 'Default webhook updated.');
        $this->redirect('/webhooks');
    }
}
