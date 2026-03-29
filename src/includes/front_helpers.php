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

function front_article_image_src($article, $width, $height, $fallbackAlt = 'actualite guerre iran')
{
    $local = front_local_image_url(isset($article['image_url']) ? $article['image_url'] : '');
    if ($local !== null) {
        return $local;
    }

    return front_svg_placeholder(front_article_alt($article, $fallbackAlt), $width, $height);
}

function front_theme_articles()
{
    return array(
        array(
            'id' => 1001,
            'title' => 'Iran: evolution du conflit et tensions regionales',
            'slug' => 'iran-evolution-conflit-regional',
            'content' => 'Les tensions autour du conflit en Iran se poursuivent avec des repercussions regionales sur la securite, les flux commerciaux et la diplomatie. Cette analyse revient sur les faits marquants de la semaine et les scenarios possibles.',
            'image_url' => '/assets/images/article1.jpg',
            'image_alt' => 'conflit en iran, panorama geopolitique',
            'created_at' => '2026-03-25 09:30:00',
            'updated_at' => '2026-03-28 18:10:00',
        ),
        array(
            'id' => 1002,
            'title' => 'Civils et infrastructures: les enjeux humanitaires en Iran',
            'slug' => 'enjeux-humanitaires-iran',
            'content' => 'Les consequences humanitaires restent au centre des preoccupations avec une pression croissante sur les services de sante et les infrastructures critiques. Le suivi des besoins locaux devient une priorite des organisations internationales.',
            'image_url' => '/assets/images/article2.jpg',
            'image_alt' => 'actualite guerre iran et impact humanitaire',
            'created_at' => '2026-03-23 10:00:00',
            'updated_at' => '2026-03-27 11:20:00',
        ),
        array(
            'id' => 1003,
            'title' => 'Iran: diplomatie, cessez-le-feu et rapports de force',
            'slug' => 'iran-diplomatie-cessez-le-feu',
            'content' => 'Les initiatives diplomatiques se multiplient autour de propositions de desescalade. Les acteurs regionaux et internationaux cherchent un compromis durable, alors que les rapports de force evoluent rapidement sur le terrain.',
            'image_url' => '/assets/images/article3.jpg',
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
        $sql = 'SELECT id, title, slug, content, image_url, image_alt, created_at, updated_at FROM articles WHERE is_published = 1 ORDER BY updated_at DESC';
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            return $rows;
        }
    } catch (PDOException $exception) {
        // Fallback to a schema without is_published.
    }

    try {
        $sql = 'SELECT id, title, slug, content, image_url, image_alt, created_at, updated_at FROM articles ORDER BY updated_at DESC';
        $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            return $rows;
        }
    } catch (PDOException $exception) {
        // Fallback to thematic static data.
    }

    return front_theme_articles();
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
    $articles = front_fetch_articles();
    $result = array();

    foreach ($articles as $article) {
        if ($excludeId !== null && (int)$article['id'] === (int)$excludeId) {
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
