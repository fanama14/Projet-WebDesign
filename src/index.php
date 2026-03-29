<?php

require_once __DIR__ . '/includes/bootstrap.php';

$articles = $pdo->query('SELECT title, slug, content, image_url, image_alt, updated_at FROM articles WHERE is_published = 1 ORDER BY updated_at DESC')->fetchAll();

render_head('Iran News - Guerre en Iran', 'Site d informations sur la guerre en Iran avec front office et backoffice.', false);
render_nav();
?>

<h1>Actualites - Guerre en Iran</h1>

<?php foreach ($articles as $item): ?>
    <div class="box">
        <h2><a href="/<?php echo h($item['slug']); ?>"><?php echo h($item['title']); ?></a></h2>
        <?php if (!empty($item['image_url'])): ?>
            <img src="<?php echo h($item['image_url']); ?>" alt="<?php echo h(!empty($item['image_alt']) ? $item['image_alt'] : $item['title']); ?>" loading="lazy">
        <?php endif; ?>
        <p><?php echo h(substr(trim(strip_tags($item['content'])), 0, 180)); ?>...</p>
    </div>
<?php endforeach; ?>

<?php render_foot(); ?>