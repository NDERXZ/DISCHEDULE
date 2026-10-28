<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\Event;
use Core\Controller;
use Core\Session;

class ShareController extends Controller
{
    public function show(): void
    {
        $token = (string)$this->request->getRouteParam('token');
        $event = Event::findByShareToken($token);

        if (!$event) {
            $this->response->setStatusCode(404);
            $this->render('errors.404', [
                'title'   => 'Shared Event Not Found',
                'message' => 'This shared link is invalid, expired, or has been revoked by the author.'
            ], 'main');
            return;
        }

        // Check if password protected
        $needsPassword = !empty($event['share_password_hash']);
        $isUnlocked = Session::get('unlocked_event_' . $event['id']) === true;

        if ($needsPassword && !$isUnlocked) {
            if ($this->request->isPost()) {
                $password = (string)$this->request->input('password', '');
                if (password_verify($password, $event['share_password_hash'])) {
                    Session::set('unlocked_event_' . $event['id'], true);
                    $this->redirect('/share/' . $token);
                    return;
                } else {
                    $this->flash('error', 'Incorrect password for this shared event.');
                }
            }

            $this->render('events.share_password', [
                'title' => 'Password Protected Event - ' . $event['title'],
                'token' => $token,
                'event' => $event
            ], 'auth');
            return;
        }

        $this->render('events.share', [
            'title' => $event['title'] . ' - Public Event',
            'event' => $event
        ], 'main');
    }
}
