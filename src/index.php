<?php

require_once __DIR__ . '/includes/front_helpers.php';

$viewRaw = isset($_GET['page']) ? (string)$_GET['page'] : 'accueil';
$view = in_array($viewRaw, array('accueil', 'actualites', 'contact'), true) ? $viewRaw : 'accueil';

$featuredList = front_fetch_articles_by_ids(array(1));
$otherArticles = front_fetch_articles_by_ids(array(2, 3, 4));
$latestArticles = front_fetch_articles_by_ids(array(5, 6));

$featuredArticle = count($featuredList) > 0 ? $featuredList[0] : null;
$spotlightArticles = $otherArticles;
$sidebarLatestArticles = $latestArticles;

if ($featuredArticle === null || count($spotlightArticles) === 0 || count($sidebarLatestArticles) === 0) {
    $fallback = front_fetch_articles();
    $excludedIds = array();

    if ($featuredArticle === null && count($fallback) > 0) {
        $featuredArticle = $fallback[0];
    }
    if ($featuredArticle !== null && isset($featuredArticle['id'])) {
        $excludedIds[] = (int)$featuredArticle['id'];
    }

    if (count($spotlightArticles) === 0) {
        $spotlightArticles = front_recent_articles_excluding(3, $excludedIds);
    }

    foreach ($spotlightArticles as $spotlightArticle) {
        if (isset($spotlightArticle['id'])) {
            $excludedIds[] = (int)$spotlightArticle['id'];
        }
    }

    if (count($sidebarLatestArticles) === 0) {
        $sidebarLatestArticles = front_recent_articles_excluding(2, $excludedIds);
    }
}

$activeMenu = $view === 'accueil' ? 'accueil' : $view;
$pageTitle = 'Iran News - Actualites guerre Iran';
$pageDescription = 'Suivez les actualites sur la guerre en Iran: analyses, points de situation et suivi geopolitique.';
$canonicalPath = '/guerre-iran-accueil.html';

if ($view === 'actualites') {
    $pageTitle = 'Actualites conflit Iran - Iran News';
    $canonicalPath = '/guerre-iran-actualites.html';
}

if ($view === 'contact') {
    $pageTitle = 'Contact redaction - Iran News';
    $canonicalPath = '/guerre-iran-contact.html';
}

$canonicalUrl = front_canonical_url($canonicalPath);
$heroMainSrc = null;
$heroThumbSrc = null;

if ($featuredArticle !== null) {
    $heroMainSrc = front_article_image_src($featuredArticle, 1200, 760, 'conflit en iran article principal');
    $heroThumbSrc = front_article_thumb_src($featuredArticle, 860, 520, 'conflit en iran article principal');

    $preloadImageHref = $heroMainSrc;
    $preloadImageSrcset = $heroThumbSrc . ' 860w, ' . $heroMainSrc . ' 1200w';
    $preloadImageSizes = '(max-width: 800px) 100vw, 980px';
}

include __DIR__ . '/includes/header.php';
?>

<main class="layout-main">
    <section class="hero-news" aria-labelledby="hero-title">
        <div class="hero-main">
            <?php if ($featuredArticle): ?>
                <div class="hero-content">
                    <p class="kicker">A la une</p>
                    <h1 id="hero-title"><?php echo h($featuredArticle['title']); ?></h1>
                    <p class="article-time"><?php echo h(front_format_datetime(front_article_datetime_value($featuredArticle))); ?></p>
                    <p class="hero-summary"><?php echo h(front_excerpt($featuredArticle['content'], 230)); ?></p>
                    <a class="link-read" href="<?php echo h(front_article_url($featuredArticle)); ?>">Lire l'article</a>
                </div>
                <div class="hero-image-wrap">
                    <picture>
                        <source media="(max-width: 800px)" srcset="<?php echo h($heroThumbSrc); ?>">
                        <img
                            src="<?php echo h($heroMainSrc); ?>"
                            alt="<?php echo h(front_article_alt($featuredArticle, 'conflit en iran article principal')); ?>"
                            width="1200"
                            height="760"
                            loading="eager"
                            fetchpriority="high"
                            decoding="async">
                    </picture>
                </div>
            <?php else: ?>
                <div class="hero-content">
                    <p class="kicker">Iran News</p>
                    <h1 id="hero-title">Actualites sur la guerre en Iran</h1>
                    <p class="hero-summary">Les articles seront affiches ici des qu'un contenu sera publie.</p>
                </div>
            <?php endif; ?>
        </div>

        <aside class="sidebar hero-sidebar" aria-labelledby="sidebar-title">
            <h2 id="sidebar-title">Articles recents</h2>
            <ul>
                <?php foreach ($sidebarLatestArticles as $recent): ?>
                    <li>
                        <a class="recent-link" href="<?php echo h(front_article_url($recent)); ?>">
                            <img
                                src="<?php echo h(front_article_thumb_src($recent, 240, 140, 'actualite recente guerre iran')); ?>"
                                alt="<?php echo h(front_article_thumb_alt($recent, 'actualite recente guerre iran')); ?>"
                                width="240"
                                height="140"
                                loading="lazy"
                                fetchpriority="low"
                                decoding="async">
                            <span class="recent-text">
                                <span class="recent-title"><?php echo h($recent['title']); ?></span>
                                <small class="recent-time"><?php echo h(front_format_datetime(front_article_datetime_value($recent))); ?></small>
                            </span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>
    </section>

    <section id="actualites" class="articles-section" aria-labelledby="articles-title">
            <h2 id="articles-title" class="section-kicker">A ne pas manquer</h2>

        <?php if (count($spotlightArticles) === 0): ?>
            <p class="empty-state">Aucun autre article disponible pour le moment.</p>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($spotlightArticles as $article): ?>
                    <article class="news-card">
                        <a class="card-image-link" href="<?php echo h(front_article_url($article)); ?>">
                            <img
                                src="<?php echo h(front_article_thumb_src($article, 860, 520, 'actualite guerre iran')); ?>"
                                alt="<?php echo h(front_article_thumb_alt($article, 'actualite guerre iran')); ?>"
                                width="860"
                                height="520"
                                loading="lazy"
                                fetchpriority="low"
                                decoding="async">
                        </a>
                        <div class="card-body">
                            <h3><a href="<?php echo h(front_article_url($article)); ?>"><?php echo h($article['title']); ?></a></h3>
                            <p class="article-time"><?php echo h(front_format_datetime(front_article_datetime_value($article))); ?></p>
                            <p><?php echo h(front_excerpt($article['content'], 140)); ?></p>
                            <a class="link-read" href="<?php echo h(front_article_url($article)); ?>">Lire plus</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="key-figures" aria-labelledby="figures-title">
        <h2 id="figures-title" class="section-kicker">Le conflit en chiffres</h2>
        <div class="figures-grid">
            <div class="figure-item">
                <span class="figure-number">31</span>
                <span class="figure-label">Jours de conflit</span>
            </div>
            <div class="figure-item">
                <span class="figure-number">12</span>
                <span class="figure-label">Pays impliqués</span>
            </div>
            <div class="figure-item">
                <span class="figure-number">4 800+</span>
                <span class="figure-label">Victimes civiles recensées</span>
            </div>
            <div class="figure-item">
                <span class="figure-number">3</span>
                <span class="figure-label">Résolutions ONU adoptées</span>
            </div>
        </div>
    </section>

    <section class="conflict-timeline" aria-labelledby="timeline-title">
        <h2 id="timeline-title" class="section-kicker">Chronologie du conflit</h2>
        <div class="timeline">
    
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-date">28 Fév. 2026</div>
                <div class="timeline-content">
                    <strong>Début du conflit</strong>
                    <p>Les États-Unis et Israël lancent les premières frappes aériennes ciblées sur des installations nucléaires et militaires en Iran.</p>
                </div>
            </div>
    
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-date">5 Mars 2026</div>
                <div class="timeline-content">
                    <strong>Première riposte iranienne</strong>
                    <p>L'Iran riposte avec des salves de missiles balistiques visant des bases américaines au Moyen-Orient et des villes israéliennes.</p>
                </div>
            </div>
    
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-date">12 Mars 2026</div>
                <div class="timeline-content">
                    <strong>Résolution ONU bloquée</strong>
                    <p>Le Conseil de sécurité tente un cessez-le-feu. La résolution est bloquée par le veto américain et russe.</p>
                </div>
            </div>
    
            <div class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-date">20 Mars 2026</div>
                <div class="timeline-content">
                    <strong>Entrée en guerre des Houthis</strong>
                    <p>Les rebelles Houthis du Yémen annoncent des tirs de missiles vers Israël en soutien à l'Iran, élargissant le conflit.</p>
                </div>
            </div>
    
            <div class="timeline-item timeline-item--active">
                <div class="timeline-dot"></div>
                <div class="timeline-date">31 Mars 2026</div>
                <div class="timeline-content">
                    <strong>Situation actuelle</strong>
                    <p>Les combats se poursuivent. Des négociations diplomatiques sont en cours à Genève sous l'égide des Nations Unies.</p>
                </div>
            </div>
    
        </div>
    </section>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>