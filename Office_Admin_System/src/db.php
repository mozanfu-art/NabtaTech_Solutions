<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/env.php';

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = env('DB_HOST', env('MYSQLHOST', '127.0.0.1'));
    $port = env('DB_PORT', env('MYSQLPORT', '3306'));
    $name = env('DB_NAME', env('MYSQLDATABASE', 'nabtatech_office'));
    $user = env('DB_USER', env('MYSQLUSER', 'root'));
    $pass = env('DB_PASS', env('MYSQLPASSWORD', ''));

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);

    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}
