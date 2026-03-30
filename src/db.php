<?php

function db()
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('DB_HOST') ? getenv('DB_HOST') : 'db';
    $port = getenv('DB_PORT') ? getenv('DB_PORT') : '3306';
    $name = getenv('DB_NAME') ? getenv('DB_NAME') : 'iran_news';
    $user = getenv('DB_USER') ? getenv('DB_USER') : 'user_projet';
    $pass = getenv('DB_PASSWORD') ? getenv('DB_PASSWORD') : 'password_projet';

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
        $options[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci';
    }

    $pdo = new PDO($dsn, $user, $pass, $options);

    return $pdo;
}
