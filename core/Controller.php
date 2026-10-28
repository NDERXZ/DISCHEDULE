<?php

declare(strict_types=1);

namespace Core;

/**
 * Dischedule Base Controller
 */
abstract class Controller
{
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function render(string $template, array $data = [], ?string $layout = 'main'): void
    {
        $html = View::render($template, $data, $layout);
        $this->response->html($html);
    }

    protected function json(mixed $data, int $status = 200): void
    {
        $this->response->json($data, $status);
    }

    protected function redirect(string $url, int $status = 302): void
    {
        $this->response->redirect($url, $status);
    }

    protected function back(string $fallback = '/'): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? $fallback;
        $this->redirect($referer);
    }

    protected function flash(string $type, string $message): void
    {
        Session::setFlash($type, $message);
    }

    protected function validate(array $data, array $rules): array
    {
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            if ($this->request->isAjax()) {
                $this->json([
                    'success' => false,
                    'message' => $validator->firstError(),
                    'errors'  => $validator->errors()
                ], 422);
            }

            Session::set('_old_input', $data);
            Session::set('_validation_errors', $validator->errors());
            $this->flash('error', $validator->firstError() ?? 'Please correct the form errors.');
            $this->back();
            exit;
        }

        return $data;
    }

    protected function user(): ?array
    {
        return Session::getUser();
    }

    protected function userId(): ?int
    {
        return Session::getUserId();
    }

    protected function isLoggedIn(): bool
    {
        return Session::isLoggedIn();
    }

    protected function isAdmin(): bool
    {
        $user = $this->user();
        return $user !== null && ($user['role'] ?? '') === 'admin';
    }

    protected function isModerator(): bool
    {
        $user = $this->user();
        return $user !== null && in_array($user['role'] ?? '', ['admin', 'moderator'], true);
    }

    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            if ($this->request->isAjax()) {
                $this->json(['success' => false, 'message' => 'Unauthorized. Please log in.'], 401);
            }
            $this->flash('warning', 'Please sign in to access this page.');
            $this->redirect('/login');
        }
    }

    protected function requireAdmin(): void
    {
        $this->requireAuth();
        if (!$this->isAdmin()) {
            if ($this->request->isAjax()) {
                $this->json(['success' => false, 'message' => 'Forbidden. Administrator privileges required.'], 403);
            }
            $this->response->setStatusCode(403);
            $this->render('errors.403', ['title' => 'Access Denied'], 'main');
            exit;
        }
    }
}
