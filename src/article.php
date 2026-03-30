<?php

require_once __DIR__ . '/includes/front_helpers.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
$pageNum = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
$rubrique = filter_input(INPUT_GET, 'rubrique', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));

$safeId = ($id !== false && $id !== null) ? (int)$id : null;
$safePageNum = ($pageNum !== false && $pageNum !== null) ? (int)$pageNum : 1;
$safeRubrique = ($rubrique !== false && $rubrique !== null) ? (int)$rubrique : 5;

$article = front_find_article(null, $safeId);

if ($article === null) {
    http_response_code(404);
    $pageTitle = 'Article introuvable | Orient Vif';
    $pageDescription = 'Cet article n est pas disponible. Consultez les autres actualites sur la guerre en Iran.';
    $canonicalUrl = front_canonical_url('/guerre-iran-actualites.html');
    $activeMenu = 'actualites';

    include __DIR__ . '/includes/header.php';
    ?>
    <main class="layout-main">
        <section class="not-found">
            <h1>Article introuvable</h1>
            <h2>Le contenu demande n est pas accessible</h2>
            <p>Retournez aux dernieres actualites pour poursuivre la lecture.</p>
            <a class="link-read" href="/guerre-iran-actualites.html">Voir les actualites</a>
        </section>
    </main>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

$articlePath = '/articles/guerre-iran-article-' . (int)$article['id'] . '-' . $safePageNum . '-' . $safeRubrique . '.html';
$pageTitle = (string)$article['title'] . ' | Orient Vif';
$pageDescription = front_excerpt($article['content'], 155);
$canonicalUrl = front_canonical_url($articlePath);
$activeMenu = 'actualites';
$recentArticles = front_recent_articles(5, (int)$article['id']);
$detailMainSrc = front_article_image_src($article, 1280, 720, 'actualite guerre iran');
$detailThumbSrc = front_article_thumb_src($article, 860, 520, 'actualite guerre iran');

include __DIR__ . '/includes/header.php';
?>

<main class="layout-main article-layout">
    <article class="article-detail" itemscope itemtype="https://schema.org/NewsArticle">
        <header class="article-header">
            <h1 itemprop="headline"><?php echo h($article['title']); ?></h1>
            <p class="article-meta">
                Date publication:
                <time itemprop="datePublished" datetime="<?php echo h(isset($article['created_at']) ? $article['created_at'] : $article['updated_at']); ?>">
                    <?php echo h(front_format_date(isset($article['created_at']) ? $article['created_at'] : $article['updated_at'])); ?>
                </time>
            </p>
        </header>

        <figure class="article-figure">
            <picture>
                <source media="(max-width: 800px)" srcset="<?php echo h($detailThumbSrc); ?>">
                <img
                    src="<?php echo h($detailMainSrc); ?>"
                    alt="<?php echo h(front_article_alt($article, 'actualite guerre iran')); ?>"
                    width="1280"
                    height="720"
                    itemprop="image"
                    loading="eager"
                    fetchpriority="high"
                    decoding="async">
            </picture>
        </figure>

        <section class="article-content" aria-labelledby="content-title" itemprop="articleBody">
            <h2 id="content-title" class="sr-only">Corps de l'article</h2>
            <p><?php echo nl2br(h($article['content'])); ?></p>
        </section>
    </article>

    <aside class="sidebar article-sidebar" aria-labelledby="recent-title">
        <h2 id="recent-title">Autres articles</h2>
        <ul>
            <?php foreach ($recentArticles as $recent): ?>
                <li><a href="<?php echo h(front_article_url($recent)); ?>"><?php echo h($recent['title']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </aside>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>