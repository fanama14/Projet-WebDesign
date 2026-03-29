<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin');
    exit;
}

$id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
$title = trim(isset($_POST['title']) ? $_POST['title'] : '');
$slug = slugify(isset($_POST['slug']) ? $_POST['slug'] : '');
$content = trim(isset($_POST['content']) ? $_POST['content'] : '');
$imageUrl = trim(isset($_POST['image_url']) ? $_POST['image_url'] : '');
$imageAlt = trim(isset($_POST['image_alt']) ? $_POST['image_alt'] : '');
$isPublished = isset($_POST['is_published']) ? 1 : 0;

if ($title === '' || $slug === '' || $content === '') {
    header('Location: /admin');
    exit;
}

if ($id > 0) {
    $stmt = $pdo->prepare(
        'UPDATE articles
         SET title = :title, slug = :slug, content = :content, image_url = :image_url,
             image_alt = :image_alt, is_published = :is_published
         WHERE id = :id'
    );
    $stmt->execute(array(
        'id' => $id,
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'image_url' => $imageUrl !== '' ? $imageUrl : null,
        'image_alt' => $imageAlt !== '' ? $imageAlt : null,
        'is_published' => $isPublished,
    ));

    header('Location: /admin?ok=updated');
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO articles (title, slug, content, image_url, image_alt, is_published)
     VALUES (:title, :slug, :content, :image_url, :image_alt, :is_published)'
);
$stmt->execute(array(
    'title' => $title,
    'slug' => $slug,
    'content' => $content,
    'image_url' => $imageUrl !== '' ? $imageUrl : null,
    'image_alt' => $imageAlt !== '' ? $imageAlt : null,
    'is_published' => $isPublished,
));

header('Location: /admin?ok=created');
exit;
