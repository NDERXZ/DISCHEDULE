<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\EmailService;
use Core\Controller;
use Core\Session;

class AuthController extends Controller
{
    private EmailService $emailService;

    public function __construct($request, $response)
    {
        parent::__construct($request, $response);
        $this->emailService = new EmailService();
    }

    public function showLogin(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/calendar');
            return;
        }

        $this->render('auth.login', ['title' => 'Sign In - Dischedule'], 'auth');
    }

    public function login(): void
    {
        $input = $this->request->all();

        $this->validate($input, [
            'login'    => 'required',
            'password' => 'required'
        ]);

        $login = trim((string)$input['login']);
        $password = (string)$input['password'];
        $remember = !empty($input['remember']);

        $user = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? User::findByEmail($login)
            : User::findByUsername($login);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->flash('error', 'Invalid email/username or password.');
            Session::set('_old_input', ['login' => $login]);
            $this->redirect('/login');
            return;
        }

        if ($user['status'] === 'banned') {
            $this->flash('error', 'Your account has been suspended. Please contact support.');
            $this->redirect('/login');
            return;
        }

        // Authentication Success
        Session::setUser(User::sanitize($user));

        // Handle Remember Me
        if ($remember) {
            $rawRememberToken = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $rawRememberToken);
            User::update($user['id'], ['remember_token' => $tokenHash]);

            $this->response->setCookie(
                'remember_token',
                $rawRememberToken,
                time() + (86400 * 30),
                '/',
                '',
                filter_var($_ENV['SESSION_SECURE'] ?? false, FILTER_VALIDATE_BOOLEAN),
                true,
                'Lax'
            );
        }

        ActivityLog::log((int)$user['id'], 'login', 'User logged in successfully.');

        $this->flash('success', "Welcome back, {$user['username']}!");
        $redirectUrl = Session::get('_redirect_after_login', '/calendar');
        Session::remove('_redirect_after_login');

        $this->redirect($redirectUrl);
    }

    public function showRegister(): void
    {
        if ($this->isLoggedIn()) {
            $this->redirect('/calendar');
            return;
        }

        $this->render('auth.register', ['title' => 'Create Account - Dischedule'], 'auth');
    }

    public function register(): void
    {
        $input = $this->request->all();

        $this->validate($input, [
            'username'              => 'required|min:3|max:30|unique:users,username',
            'email'                 => 'required|email|max:191|unique:users,email',
            'password'              => 'required|password_strength',
            'password_confirmation' => 'required|matches:password'
        ]);

        $verificationToken = bin2hex(random_bytes(32));
        $passwordHash = password_hash((string)$input['password'], PASSWORD_BCRYPT);

        $userId = User::create([
            'username'           => trim((string)$input['username']),
            'email'              => strtolower(trim((string)$input['email'])),
            'password_hash'      => $passwordHash,
            'timezone'           => 'UTC',
            'language'           => 'en',
            'theme'              => 'system',
            'role'               => 'user',
            'status'             => 'active',
            'verification_token' => $verificationToken,
            'email_verified_at'  => null,
            'created_at'         => date('Y-m-d H:i:s')
        ]);

        $user = User::find($userId);

        // Send verification email
        $this->emailService->sendVerificationEmail($user, $verificationToken);

        ActivityLog::log($userId, 'register', 'New user account created.');

        // Auto login user
        Session::setUser(User::sanitize($user));
        $this->flash('success', 'Account created successfully! We sent a verification email to activate all features.');
        $this->redirect('/calendar');
    }

    public function verifyEmail(): void
    {
        $token = (string)$this->request->query('token', '');
        if (empty($token)) {
            $this->flash('error', 'Invalid verification link.');
            $this->redirect('/login');
            return;
        }

        $user = User::findByVerificationToken($token);
        if (!$user) {
            $this->flash('error', 'Verification token is invalid or has already been used.');
            $this->redirect('/login');
            return;
        }

        User::update($user['id'], [
            'email_verified_at'  => date('Y-m-d H:i:s'),
            'verification_token' => null
        ]);

        if (Session::isLoggedIn() && Session::getUserId() === (int)$user['id']) {
            $updated = User::find($user['id']);
            Session::setUser(User::sanitize($updated));
        }

        $this->flash('success', 'Your email address has been verified successfully!');
        $this->redirect('/calendar');
    }

    public function showForgot(): void
    {
        $this->render('auth.forgot', ['title' => 'Forgot Password - Dischedule'], 'auth');
    }

    public function forgot(): void
    {
        $input = $this->request->all();
        $this->validate($input, ['email' => 'required|email']);

        $email = strtolower(trim((string)$input['email']));
        $user = User::findByEmail($email);

        if ($user) {
            $resetToken = bin2hex(random_bytes(32));
            $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hour

            User::update($user['id'], [
                'reset_token'      => $resetToken,
                'reset_expires_at' => $expiresAt
            ]);

            $this->emailService->sendPasswordResetEmail($user, $resetToken);
        }

        // Always show same message for privacy
        $this->flash('success', 'If an account exists for that email, we have dispatched a password reset link.');
        $this->redirect('/login');
    }

    public function showReset(): void
    {
        $token = (string)$this->request->query('token', '');
        $user = !empty($token) ? User::findByResetToken($token) : null;

        if (!$user) {
            $this->flash('error', 'The password reset token is invalid or has expired.');
            $this->redirect('/forgot-password');
            return;
        }

        $this->render('auth.reset', [
            'title' => 'Reset Password - Dischedule',
            'token' => $token
        ], 'auth');
    }

    public function reset(): void
    {
        $input = $this->request->all();

        $this->validate($input, [
            'token'                 => 'required',
            'password'              => 'required|password_strength',
            'password_confirmation' => 'required|matches:password'
        ]);

        $token = (string)$input['token'];
        $user = User::findByResetToken($token);

        if (!$user) {
            $this->flash('error', 'This password reset link is invalid or expired.');
            $this->redirect('/forgot-password');
            return;
        }

        $newHash = password_hash((string)$input['password'], PASSWORD_BCRYPT);
        User::update($user['id'], [
            'password_hash'    => $newHash,
            'reset_token'      => null,
            'reset_expires_at' => null
        ]);

        ActivityLog::log((int)$user['id'], 'password_reset', 'Password was reset via email token.');

        $this->flash('success', 'Password reset successfully! Please sign in with your new password.');
        $this->redirect('/login');
    }

    public function logout(): void
    {
        $userId = $this->userId();
        if ($userId) {
            User::update($userId, ['remember_token' => null]);
            ActivityLog::log($userId, 'logout', 'User signed out.');
        }

        $this->response->deleteCookie('remember_token');
        Session::logout();

        $this->flash('info', 'You have been signed out.');
        $this->redirect('/login');
    }
}
