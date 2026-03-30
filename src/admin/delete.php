<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $imgStmt = $pdo->prepare('SELECT image_path FROM article_images WHERE article_id = :id');
    $imgStmt->execute(array('id' => $id));
    $images = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($images as $img) {
        $path = isset($img['image_path']) ? (string)$img['image_path'] : '';
        if ($path === '') {
            continue;
        }

        $fullPath = __DIR__ . '/../' . ltrim($path, '/');
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->execute(array('id' => $id));
    $_SESSION['flash_ok'] = 'deleted';
}

header('Location: /admin/');
exit;
