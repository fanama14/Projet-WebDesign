<?php

require_once __DIR__ . '/../db.php';

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function front_excerpt($text, $maxLength = 180)
{
    $plain = trim((string)preg_replace('/\s+/', ' ', strip_tags((string)$text)));
    if ($plain === '') {
        return '';
    }

    if (strlen($plain) <= $maxLength) {
        return $plain;
    }

    return rtrim(substr($plain, 0, $maxLength - 1)) . '...';
}

function front_canonical_url($path)
{
    $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $isHttps ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? (string)$_SERVER['HTTP_HOST'] : 'localhost:8080';

    return $scheme . '://' . $host . $path;
}

function front_article_url($article)
{
    $id = isset($article['id']) ? (int)$article['id'] : 0;
    if ($id < 1) {
        return '/guerre-iran-actualites.html';
    }

    $page = 1;
    $rubrique = isset($article['rubrique']) ? (int)$article['rubrique'] : 5;
    if ($rubrique < 1) {
        $rubrique = 5;
    }

    return '/articles/guerre-iran-article-' . $id . '-' . $page . '-' . $rubrique . '.html';
}

function front_article_alt($article, $fallback = 'actualite guerre iran')
{
    if (!empty($article['image_alt'])) {
        return (string)$article['image_alt'];
    }

    if (!empty($article['title'])) {
        return 'illustration: ' . (string)$article['title'];
    }

    return $fallback;
}

function front_article_thumb_alt($article, $fallback = 'actualite guerre iran')
{
    if (!empty($article['image_thumb_alt'])) {
        return (string)$article['image_thumb_alt'];
    }

    return front_article_alt($article, $fallback);
}

function front_svg_placeholder($label, $width, $height)
{
    $safeLabel = htmlspecialchars((string)$label, ENT_QUOTES, 'UTF-8');
    $safeWidth = (int)$width;
    $safeHeight = (int)$height;

    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $safeWidth . '" height="' . $safeHeight . '" viewBox="0 0 ' . $safeWidth . ' ' . $safeHeight . '">'
        . '<rect width="100%" height="100%" fill="#f3f3f3"/>'
        . '<rect x="16" y="16" width="' . ($safeWidth - 32) . '" height="' . ($safeHeight - 32) . '" fill="#ffffff" stroke="#dcdcdc"/>'
        . '<text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#1f1f1f" font-family="Georgia, serif" font-size="24">'
        . $safeLabel
        . '</text>'
        . '</svg>';

    return 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($svg);
}

function front_local_image_url($rawPath)
{
    $path = trim((string)$rawPath);
    if ($path === '') {
        return null;
    }

    if (preg_match('/^https?:\/\//i', $path)) {
        return null;
    }

    $normalized = '/' . ltrim($path, '/');
    $fullPath = dirname(__DIR__) . str_replace('/', DIRECTORY_SEPARATOR, $normalized);

    if (is_file($fullPath)) {
        return $normalized;
    }

    return null;
}

function front_build_thumb_path($rawPath)
{
    $path = trim((string)$rawPath);
    if ($path === '' || preg_match('/^https?:\/\//i', $path)) {
        return null;
    }

    $dotPos = strrpos($path, '.');
    if ($dotPos === false) {
        return null;
    }

    $prefix = substr($path, 0, $dotPos);
    $suffix = substr($path, $dotPos);

    if (substr($prefix, -6) === '-thumb') {
        return $path;
    }

    return $prefix . '-thumb' . $suffix;
}

function front_article_image_src($article, $width, $height, $fallbackAlt = 'actualite guerre iran')
{
    $local = front_local_image_url(isset($article['image_url']) ? $article['image_url'] : '');
    if ($local !== null) {
        return $local;
    }

    return front_svg_placeholder(front_article_alt($article, $fallbackAlt), $width, $height);
}

function front_article_thumb_src($article, $width, $height, $fallbackAlt = 'actualite guerre iran')
{
    $thumbLocalFromDb = front_local_image_url(isset($article['image_thumb_url']) ? $article['image_thumb_url'] : '');
    if ($thumbLocalFromDb !== null) {
        return $thumbLocalFromDb;
    }

    $thumbPath = front_build_thumb_path(isset($article['image_url']) ? $article['image_url'] : '');
    $thumbLocal = front_local_image_url($thumbPath);
    if ($thumbLocal !== null) {
        return $thumbLocal;
    }

    return front_article_image_src($article, $width, $height, $fallbackAlt);
}

function front_render_article_html($html)
{
    $decoded = html_entity_decode((string)$html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $decoded);

    if ($clean === null) {
        $clean = '';
    }

    return strip_tags(
        $clean,
        '<p><br><strong><b><em><i><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><img><figure><figcaption>'
    );
}

function front_theme_articles()
{
    return array(
        array(
            'id' => 1001,
            'title' => 'Iran: evolution du conflit et tensions regionales',
            'slug' => 'iran-evolution-conflit-regional',
            'content' => 'Les tensions autour du conflit en Iran se poursuivent avec des repercussions regionales sur la securite, les flux commerciaux et la diplomatie. Cette analyse revient sur les faits marquants de la semaine et les scenarios possibles.',
            'image_url' => '/assets/images/article-1.webp',
            'image_alt' => 'conflit en iran, panorama geopolitique',
            'created_at' => '2026-03-25 09:30:00',
            'updated_at' => '2026-03-28 18:10:00',
        ),
        array(
            'id' => 1002,
            'title' => 'Civils et infrastructures: les enjeux humanitaires en Iran',
            'slug' => 'enjeux-humanitaires-iran',
            'content' => 'Les consequences humanitaires restent au centre des preoccupations avec une pression croissante sur les services de sante et les infrastructures critiques. Le suivi des besoins locaux devient une priorite des organisations internationales.',
            'image_url' => '/assets/images/article-2.webp',
            'image_alt' => 'actualite guerre iran et impact humanitaire',
            'created_at' => '2026-03-23 10:00:00',
            'updated_at' => '2026-03-27 11:20:00',
        ),
        array(
            'id' => 1003,
            'title' => 'Iran: diplomatie, cessez-le-feu et rapports de force',
            'slug' => 'iran-diplomatie-cessez-le-feu',
            'content' => 'Les initiatives diplomatiques se multiplient autour de propositions de desescalade. Les acteurs regionaux et internationaux cherchent un compromis durable, alors que les rapports de force evoluent rapidement sur le terrain.',
            'image_url' => '/assets/images/article-3.webp',
            'image_alt' => 'analyse diplomatique du conflit en iran',
            'created_at' => '2026-03-21 07:40:00',
            'updated_at' => '2026-03-26 09:05:00',
        ),
    );
}

function front_fetch_articles()
{
    $pdo = db();

    try {
        $sql = "SELECT
                    a.id,
                    a.title,
                    a.slug,
                    a.content,
                    ai_main.image_path AS image_url,
                    COALESCE(ai_thumb.image_path, '') AS image_thumb_url,
                    COALESCE(NULLIF(ai_main.image_alt, ''), a.title) AS image_alt,
                    COALESCE(NULLIF(ai_thumb.image_alt, ''), NULLIF(ai_main.image_alt, ''), a.title) AS image_thumb_alt,
                    a.created_at,
                    a.updated_at
                FROM articles a
                LEFT JOIN article_images ai_main
                    ON ai_main.article_id = a.id AND ai_main.image_kind = 'main'
                LEFT JOIN article_images ai_thumb
                    ON ai_thumb.article_id = a.id AND ai_thumb.image_kind = 'thumb'
                WHERE a.is_published = 1
                ORDER BY a.updated_at DESC";
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            return $rows;
        }
    } catch (PDOException $exception) {
        // Fallbacks for older schemas.
    }

    try {
        $sql = "SELECT
                    a.id,
                    a.title,
                    a.slug,
                    a.content,
                    ai_main.image_path AS image_url,
                    COALESCE(ai_thumb.image_path, '') AS image_thumb_url,
                    COALESCE(NULLIF(ai_main.image_alt, ''), a.title) AS image_alt,
                    COALESCE(NULLIF(ai_thumb.image_alt, ''), NULLIF(ai_main.image_alt, ''), a.title) AS image_thumb_alt,
                    a.created_at,
                    a.updated_at
                FROM articles a
                LEFT JOIN article_images ai_main
                    ON ai_main.article_id = a.id AND ai_main.image_kind = 'main'
                LEFT JOIN article_images ai_thumb
                    ON ai_thumb.article_id = a.id AND ai_thumb.image_kind = 'thumb'
                ORDER BY a.updated_at DESC";
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            return $rows;
        }
    } catch (PDOException $exception) {
        // Fallbacks for schemas without article_images.
    }

    try {
        $sql = 'SELECT id, title, slug, content, NULL AS image_url, NULL AS image_thumb_url, title AS image_alt, title AS image_thumb_alt, created_at, updated_at FROM articles WHERE is_published = 1 ORDER BY updated_at DESC';
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            return $rows;
        }
    } catch (PDOException $exception) {
        // Fallback for schema without article_images and without image columns.
    }

    try {
        $sql = 'SELECT id, title, slug, content, NULL AS image_url, NULL AS image_thumb_url, title AS image_alt, title AS image_thumb_alt, created_at, updated_at FROM articles ORDER BY updated_at DESC';
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            return $rows;
        }
    } catch (PDOException $exception) {
        // Fallback to thematic static data.
    }

    return front_theme_articles();
}

function front_fetch_articles_by_ids($ids)
{
    $cleanIds = array();
    foreach ($ids as $id) {
        $num = (int)$id;
        if ($num > 0) {
            $cleanIds[] = $num;
        }
    }

    if (count($cleanIds) === 0) {
        return array();
    }

    $pdo = db();
    $placeholders = implode(',', array_fill(0, count($cleanIds), '?'));
    $rows = array();

    try {
        $sql = "SELECT
                    a.id,
                    a.title,
                    a.slug,
                    a.content,
                    ai_main.image_path AS image_url,
                    COALESCE(ai_thumb.image_path, '') AS image_thumb_url,
                    COALESCE(NULLIF(ai_main.image_alt, ''), a.title) AS image_alt,
                    COALESCE(NULLIF(ai_thumb.image_alt, ''), NULLIF(ai_main.image_alt, ''), a.title) AS image_thumb_alt,
                    a.created_at,
                    a.updated_at
                FROM articles a
                LEFT JOIN article_images ai_main
                    ON ai_main.article_id = a.id AND ai_main.image_kind = 'main'
                LEFT JOIN article_images ai_thumb
                    ON ai_thumb.article_id = a.id AND ai_thumb.image_kind = 'thumb'
                WHERE a.is_published = 1 AND a.id IN (" . $placeholders . ")";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($cleanIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $exception) {
        try {
            $sql = "SELECT
                        a.id,
                        a.title,
                        a.slug,
                        a.content,
                        ai_main.image_path AS image_url,
                        COALESCE(ai_thumb.image_path, '') AS image_thumb_url,
                        COALESCE(NULLIF(ai_main.image_alt, ''), a.title) AS image_alt,
                        COALESCE(NULLIF(ai_thumb.image_alt, ''), NULLIF(ai_main.image_alt, ''), a.title) AS image_thumb_alt,
                        a.created_at,
                        a.updated_at
                    FROM articles a
                    LEFT JOIN article_images ai_main
                        ON ai_main.article_id = a.id AND ai_main.image_kind = 'main'
                    LEFT JOIN article_images ai_thumb
                        ON ai_thumb.article_id = a.id AND ai_thumb.image_kind = 'thumb'
                    WHERE a.id IN (" . $placeholders . ")";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($cleanIds);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $sql = 'SELECT id, title, slug, content, NULL AS image_url, NULL AS image_thumb_url, title AS image_alt, title AS image_thumb_alt, created_at, updated_at FROM articles WHERE is_published = 1 AND id IN (' . $placeholders . ')';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($cleanIds);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    if (empty($rows)) {
        try {
            $sql = 'SELECT id, title, slug, content, NULL AS image_url, NULL AS image_thumb_url, title AS image_alt, title AS image_thumb_alt, created_at, updated_at FROM articles WHERE id IN (' . $placeholders . ')';
            $stmt = $pdo->prepare($sql);
            $stmt->execute($cleanIds);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            $rows = array();
        }
    }

    if (empty($rows)) {
        $fallback = front_theme_articles();
        $fallbackById = array();
        foreach ($fallback as $item) {
            $fallbackById[(int)$item['id']] = $item;
        }

        $rows = array();
        foreach ($cleanIds as $id) {
            $fallbackId = 1000 + $id;
            if (isset($fallbackById[$fallbackId])) {
                $rows[] = $fallbackById[$fallbackId];
            }
        }
    }

    $indexed = array();
    foreach ($rows as $row) {
        $indexed[(int)$row['id']] = $row;
    }

    $ordered = array();
    foreach ($cleanIds as $id) {
        if (isset($indexed[$id])) {
            $ordered[] = $indexed[$id];
        }
    }

    return $ordered;
}

function front_find_article($slug, $id)
{
    $articles = front_fetch_articles();

    if ($id !== null) {
        foreach ($articles as $article) {
            if ((int)$article['id'] === (int)$id) {
                return $article;
            }
        }
    }

    if ($slug !== null && $slug !== '') {
        foreach ($articles as $article) {
            if (isset($article['slug']) && (string)$article['slug'] === (string)$slug) {
                return $article;
            }
        }
    }

    return null;
}

function front_recent_articles($limit = 5, $excludeId = null)
{
    $excludeIds = array();
    if ($excludeId !== null) {
        $excludeIds[] = (int)$excludeId;
    }

    return front_recent_articles_excluding($limit, $excludeIds);
}

function front_recent_articles_excluding($limit = 5, $excludeIds = array())
{
    $articles = front_fetch_articles();
    $result = array();
    $excludeMap = array();

    foreach ($excludeIds as $excludeId) {
        $id = (int)$excludeId;
        if ($id > 0) {
            $excludeMap[$id] = true;
        }
    }

    foreach ($articles as $article) {
        $articleId = isset($article['id']) ? (int)$article['id'] : 0;
        if ($articleId > 0 && isset($excludeMap[$articleId])) {
            continue;
        }

        $result[] = $article;
        if (count($result) >= $limit) {
            break;
        }
    }

    return $result;
}

function front_format_date($value)
{
    try {
        $date = new DateTime((string)$value);
    } catch (Exception $exception) {
        return (string)$value;
    }

    $months = array(
        1 => 'janvier',
        2 => 'fevrier',
        3 => 'mars',
        4 => 'avril',
        5 => 'mai',
        6 => 'juin',
        7 => 'juillet',
        8 => 'aout',
        9 => 'septembre',
        10 => 'octobre',
        11 => 'novembre',
        12 => 'decembre',
    );

    $month = (int)$date->format('n');
    return $date->format('d') . ' ' . $months[$month] . ' ' . $date->format('Y');
}

function front_format_datetime($value)
{
    try {
        $date = new DateTime((string)$value);
    } catch (Exception $exception) {
        return (string)$value;
    }

    $months = array(
        1 => 'janvier',
        2 => 'fevrier',
        3 => 'mars',
        4 => 'avril',
        5 => 'mai',
        6 => 'juin',
        7 => 'juillet',
        8 => 'aout',
        9 => 'septembre',
        10 => 'octobre',
        11 => 'novembre',
        12 => 'decembre',
    );

    $month = (int)$date->format('n');

    return $date->format('d') . ' ' . $months[$month] . ' ' . $date->format('Y') . ' a ' . $date->format('H:i');
}

function front_article_datetime_value($article)
{
    if (isset($article['updated_at']) && trim((string)$article['updated_at']) !== '') {
        return (string)$article['updated_at'];
    }

    if (isset($article['created_at']) && trim((string)$article['created_at']) !== '') {
        return (string)$article['created_at'];
    }

    return '';
}
