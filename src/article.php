<?php

require_once __DIR__ . '/includes/bootstrap.php';

$slug = isset($_GET['slug']) ? $_GET['slug'] : '';
$stmt = $pdo->prepare('SELECT title, slug, content, image_url, image_alt, updated_at FROM articles WHERE slug = :slug AND is_published = 1 LIMIT 1');
$stmt->execute(array('slug' => $slug));
$article = $stmt->fetch();

if (!$article) {
    http_response_code(404);
    render_head('Article introuvable - Iran News', 'La page demandee est introuvable.', false);
    render_nav();
    echo '<h1>Article introuvable</h1>';
    echo '<p>Le contenu demande n existe pas.</p>';
    render_foot();
    exit;
}

$metaDescription = substr(trim($article['content']), 0, 155);
render_head($article['title'] . ' - Iran News', $metaDescription, false);
render_nav();
?>

<h1><?php echo h($article['title']); ?></h1>
<p><em>Maj: <?php echo h($article['updated_at']); ?></em></p>

<?php if (!empty($article['image_url'])): ?>
    <img src="<?php echo h($article['image_url']); ?>" alt="<?php echo h(!empty($article['image_alt']) ? $article['image_alt'] : $article['title']); ?>" loading="lazy">
<?php endif; ?>

<div class="box"><?php echo render_article_html($article['content']); ?></div>

<?php render_foot(); ?>