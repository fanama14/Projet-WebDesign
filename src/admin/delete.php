<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $stmt = $pdo->prepare('DELETE FROM articles WHERE id = :id');
    $stmt->execute(array('id' => $id));
}

header('Location: /admin?ok=deleted');
exit;
