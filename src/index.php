<?php

require_once __DIR__ . '/includes/front_helpers.php';

$viewRaw = isset($_GET['page']) ? (string)$_GET['page'] : 'accueil';
$view = in_array($viewRaw, array('accueil', 'actualites', 'contact'), true) ? $viewRaw : 'accueil';

$articles = front_fetch_articles();
$featuredArticle = count($articles) > 0 ? $articles[0] : null;
$gridArticles = count($articles) > 1 ? array_slice($articles, 1) : array();
$recentArticles = front_recent_articles(5);

$activeMenu = $view === 'accueil' ? 'accueil' : $view;
$pageTitle = 'Actualites guerre Iran - FrontOffice journal moderne';
$pageDescription = 'Suivez les actualites sur la guerre en Iran: analyses, points de situation et suivi geopolitique.';
$canonicalPath = '/guerre-iran-accueil.html';

if ($view === 'actualites') {
    $pageTitle = 'Actualites conflit Iran - Dossier complet';
    $canonicalPath = '/guerre-iran-actualites.html';
}

if ($view === 'contact') {
    $pageTitle = 'Contact redaction - Actualites guerre Iran';
    $canonicalPath = '/guerre-iran-contact.html';
}

$canonicalUrl = front_canonical_url($canonicalPath);

include __DIR__ . '/includes/header.php';
?>

<main class="layout-main">
    <section class="hero-news" aria-labelledby="hero-title">
        <?php if ($featuredArticle): ?>
            <div class="hero-content">
                <p class="kicker">Article principal</p>
                <h1 id="hero-title"><?php echo h($featuredArticle['title']); ?></h1>
                <p class="hero-summary"><?php echo h(front_excerpt($featuredArticle['content'], 230)); ?></p>
                <a class="link-read" href="<?php echo h(front_article_url($featuredArticle)); ?>">Lire l'article principal</a>
            </div>
            <div class="hero-image-wrap">
                <img
                    src="<?php echo h(front_article_image_src($featuredArticle, 1200, 760, 'conflit en iran article principal')); ?>"
                    alt="<?php echo h(front_article_alt($featuredArticle, 'conflit en iran article principal')); ?>"
                    loading="eager">
            </div>
        <?php else: ?>
            <div class="hero-content">
                <p class="kicker">FrontOffice</p>
                <h1 id="hero-title">Actualites sur la guerre en Iran</h1>
                <p class="hero-summary">Les articles seront affiches ici des qu'un contenu sera publie depuis le backoffice.</p>
            </div>
        <?php endif; ?>
    </section>

    <div class="content-grid">
        <section id="actualites" class="articles-section" aria-labelledby="articles-title">
            <h2 id="articles-title">Autres articles</h2>

            <?php if (count($gridArticles) === 0): ?>
                <p class="empty-state">Aucun autre article disponible pour le moment.</p>
            <?php else: ?>
                <div class="cards-grid">
                    <?php foreach ($gridArticles as $article): ?>
                        <article class="news-card">
                            <a class="card-image-link" href="<?php echo h(front_article_url($article)); ?>">
                                <img
                                    src="<?php echo h(front_article_image_src($article, 860, 520, 'actualite guerre iran')); ?>"
                                    alt="<?php echo h(front_article_alt($article, 'actualite guerre iran')); ?>"
                                    loading="lazy">
                            </a>
                            <div class="card-body">
                                <h3><a href="<?php echo h(front_article_url($article)); ?>"><?php echo h($article['title']); ?></a></h3>
                                <p><?php echo h(front_excerpt($article['content'], 140)); ?></p>
                                <a class="link-read" href="<?php echo h(front_article_url($article)); ?>">Lire plus</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <aside class="sidebar" aria-labelledby="sidebar-title">
            <h2 id="sidebar-title">Articles recents</h2>
            <ul>
                <?php foreach ($recentArticles as $recent): ?>
                    <li>
                        <a href="<?php echo h(front_article_url($recent)); ?>"><?php echo h($recent['title']); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>