<?php

declare(strict_types=1);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function auth_user(): ?array
{
    if (!isset($_SESSION)) {
        session_start();
    }

    return $_SESSION['user'] ?? null;
}

function require_auth(): array
{
    $user = auth_user();
    if ($user === null) {
        flash('error', 'Please login to continue.');
        redirect('login.php');
    }

    return $user;
}

function login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT user_id, username, password_hash, full_name, role, department, is_active FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if (!$user || (int) $user['is_active'] !== 1) {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    if (!isset($_SESSION)) {
        session_start();
    }

    unset($user['password_hash']);
    $_SESSION['user'] = $user;

    $update = db()->prepare('UPDATE users SET last_login = NOW() WHERE user_id = :id');
    $update->execute(['id' => $user['user_id']]);

    return true;
}

function logout(): void
{
    if (!isset($_SESSION)) {
        session_start();
    }

    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    session_destroy();
}
