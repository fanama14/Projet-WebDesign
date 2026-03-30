<?php

function ensure_default_backoffice_user($pdo)
{
    try {
        $tableCheck = $pdo->query("SHOW TABLES LIKE 'users'")->fetchColumn();
        if ($tableCheck === false) {
            return;
        }

        $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(array('username' => 'admin'));
        $exists = $stmt->fetchColumn();

        if ($exists !== false) {
            return;
        }

        $insert = $pdo->prepare('INSERT INTO users (username, password_hash) VALUES (:username, :password_hash)');
        $insert->execute(array(
            'username' => 'admin',
            // Default password: admin123
            'password_hash' => '$2y$10$2BlhLlBnrCkmLkutol2MS.PRbGqXuQKFZ6uR5npMxlE7CJMSnVIJa',
        ));
    } catch (Throwable $e) {
        // Keep front and backoffice available even if user bootstrap fails.
    }
}

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
    ensure_default_backoffice_user($pdo);

    return $pdo;
}
