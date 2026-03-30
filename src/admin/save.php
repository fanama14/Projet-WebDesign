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

function build_thumb_path($imagePath)
{
    $dotPos = strrpos($imagePath, '.');
    if ($dotPos === false) {
        return $imagePath . '-thumb';
    }

    return substr($imagePath, 0, $dotPos) . '-thumb' . substr($imagePath, $dotPos);
}

function save_article_images(PDO $pdo, $articleId, $mainImagePath, $mainImageAlt)
{
    if ($mainImagePath === '') {
        $deleteStmt = $pdo->prepare('DELETE FROM article_images WHERE article_id = :article_id AND image_kind IN (\'main\', \'thumb\')');
        $deleteStmt->execute(array('article_id' => (int)$articleId));
        return;
    }

    $thumbImagePath = build_thumb_path($mainImagePath);

    $mainStmt = $pdo->prepare(
        "INSERT INTO article_images (article_id, image_kind, image_path, image_alt, sort_order)
         VALUES (:article_id, 'main', :image_path, :image_alt, 1)
         ON DUPLICATE KEY UPDATE
            image_path = VALUES(image_path),
            image_alt = VALUES(image_alt),
            sort_order = VALUES(sort_order)"
    );
    $mainStmt->execute(array(
        'article_id' => (int)$articleId,
        'image_path' => $mainImagePath,
        'image_alt' => $mainImageAlt !== '' ? $mainImageAlt : null,
    ));

    $thumbStmt = $pdo->prepare(
        "INSERT INTO article_images (article_id, image_kind, image_path, image_alt, sort_order)
         VALUES (:article_id, 'thumb', :image_path, :image_alt, 1)
         ON DUPLICATE KEY UPDATE
            image_path = VALUES(image_path),
            image_alt = VALUES(image_alt),
            sort_order = VALUES(sort_order)"
    );
    $thumbStmt->execute(array(
        'article_id' => (int)$articleId,
        'image_path' => $thumbImagePath,
        'image_alt' => $mainImageAlt !== '' ? $mainImageAlt : null,
    ));
}

if ($title === '' || $slug === '' || $content === '') {
    header('Location: /admin');
    exit;
}

if ($id > 0) {
    $stmt = $pdo->prepare(
        'UPDATE articles
         SET title = :title, slug = :slug, content = :content,
             is_published = :is_published
         WHERE id = :id'
    );
    $stmt->execute(array(
        'id' => $id,
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'is_published' => $isPublished,
    ));

    save_article_images($pdo, $id, $imageUrl, $imageAlt);

    header('Location: /admin?ok=updated');
    exit;
}

$stmt = $pdo->prepare(
    'INSERT INTO articles (title, slug, content, is_published)
     VALUES (:title, :slug, :content, :is_published)'
);
$stmt->execute(array(
    'title' => $title,
    'slug' => $slug,
    'content' => $content,
    'is_published' => $isPublished,
));

$newArticleId = (int)$pdo->lastInsertId();
save_article_images($pdo, $newArticleId, $imageUrl, $imageAlt);

header('Location: /admin?ok=created');
exit;
