<?php

require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/upload.php';

require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/');
    exit;
}

$id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
$title = trim(isset($_POST['title']) ? $_POST['title'] : '');
$slug = slugify(isset($_POST['slug']) ? $_POST['slug'] : '');
$content = trim(isset($_POST['content']) ? $_POST['content'] : '');
$imageAlt = trim(isset($_POST['image_alt']) ? $_POST['image_alt'] : '');
$isPublished = isset($_POST['is_published']) ? 1 : 0;
$slugChanged = false;

function make_unique_slug($pdo, $baseSlug, $currentId)
{
    $candidate = $baseSlug;
    $suffix = 2;

    while (true) {
        if ($currentId > 0) {
            $stmt = $pdo->prepare('SELECT id FROM articles WHERE slug = :slug AND id <> :id LIMIT 1');
            $stmt->execute(array('slug' => $candidate, 'id' => $currentId));
        } else {
            $stmt = $pdo->prepare('SELECT id FROM articles WHERE slug = :slug LIMIT 1');
            $stmt->execute(array('slug' => $candidate));
        }

        $exists = $stmt->fetch();
        if (!$exists) {
            return $candidate;
        }

        $candidate = $baseSlug . '-' . $suffix;
        $suffix++;
    }
}

if ($title === '' || $slug === '' || $content === '') {
    $_SESSION['flash_err'] = 'missing';
    if ($id > 0) {
        header('Location: /admin/?edit=' . $id);
        exit;
    }
    header('Location: /admin/');
    exit;
}

$uniqueSlug = make_unique_slug($pdo, $slug, $id);
if ($uniqueSlug !== $slug) {
    $slug = $uniqueSlug;
    $slugChanged = true;
}

try {
    $pdo->beginTransaction();

    if ($id > 0) {
        $stmt = $pdo->prepare(
            'UPDATE articles
             SET title = :title, slug = :slug, content = :content, is_published = :is_published
             WHERE id = :id'
        );
        $stmt->execute(array(
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'is_published' => $isPublished,
        ));

        upload_process_article_image(
            $pdo,
            $id,
            isset($_FILES['image_file']) ? $_FILES['image_file'] : array(),
            $imageAlt
        );

        $pdo->commit();

        $_SESSION['flash_ok'] = 'updated';
        if ($slugChanged) {
            $_SESSION['flash_note'] = 'slug_changed';
        }
        header('Location: /admin/');
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

    $newId = (int)$pdo->lastInsertId();

    upload_process_article_image(
        $pdo,
        $newId,
        isset($_FILES['image_file']) ? $_FILES['image_file'] : array(),
        $imageAlt
    );

    $pdo->commit();

    $_SESSION['flash_ok'] = 'created';
    if ($slugChanged) {
        $_SESSION['flash_note'] = 'slug_changed';
    }
    header('Location: /admin/');
    exit;
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    if ($e->getCode() === '23000') {
        $_SESSION['flash_err'] = 'slug_exists';
        if ($id > 0) {
            header('Location: /admin/?edit=' . $id);
            exit;
        }
        header('Location: /admin/');
        exit;
    }

    $_SESSION['flash_err'] = 'db';
    if ($id > 0) {
        header('Location: /admin/?edit=' . $id);
        exit;
    }
    header('Location: /admin/');
    exit;
} catch (RuntimeException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['flash_err'] = 'upload';
    if ($id > 0) {
        header('Location: /admin/?edit=' . $id);
        exit;
    }
    header('Location: /admin/');
    exit;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $_SESSION['flash_err'] = 'db';
    if ($id > 0) {
        header('Location: /admin/?edit=' . $id);
        exit;
    }
    header('Location: /admin/');
    exit;
}
