<?php

declare(strict_types=1);

require_once __DIR__ . '/src/db.php';

$pwd = password_hash('admin123', PASSWORD_DEFAULT);
$pdo = db();

$users = ['admin', 'reception', 'support1'];
$update = $pdo->prepare('UPDATE users SET password_hash = :hash WHERE username = :username');

foreach ($users as $username) {
    $update->execute(['hash' => $pwd, 'username' => $username]);
}

echo "Passwords reset to admin123 for: " . implode(', ', $users) . PHP_EOL;
