<?php

if (!isset($pageTitle) || trim((string)$pageTitle) === '') {
    $pageTitle = 'Actualites guerre Iran | Orient Vif';
}

if (!isset($pageDescription) || trim((string)$pageDescription) === '') {
    $pageDescription = 'Suivi editorial independant des crises au Proche-Orient.';
}

if (!isset($canonicalUrl) || trim((string)$canonicalUrl) === '') {
    $canonicalUrl = front_canonical_url('/guerre-iran-accueil.html');
}

if (!isset($activeMenu)) {
    $activeMenu = 'accueil';
}

$cssFilePath = __DIR__ . '/../css/style.min.css';
$cssVersion = is_file($cssFilePath) ? (string)filemtime($cssFilePath) : '1';
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo h($pageTitle); ?></title>
    <meta name="description" content="<?php echo h($pageDescription); ?>">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="<?php echo h($canonicalUrl); ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo h($pageTitle); ?>">
    <meta property="og:description" content="<?php echo h($pageDescription); ?>">
    <meta property="og:url" content="<?php echo h($canonicalUrl); ?>">

    <link rel="stylesheet" href="/css/style.min.css?v=<?php echo h($cssVersion); ?>">
</head>

<body>
    <div class="breaking-bar">
        <span class="breaking-bar__label">En direct</span>
        Suivi du conflit &mdash; Mises a jour editoriales quotidiennes
    </div>
    <header class="site-header">
        <div class="header-topline">
            <p>Edition numerique &mdash; Dossier geopolitique Proche-Orient &middot; <?php echo date('d/m/Y'); ?></p>
        </div>
        <div class="header-main">
            <nav class="main-nav main-nav--left" aria-label="Rubriques">
                <a href="/guerre-iran-actualites.html">International</a>
                <a href="#">Politique</a>
                <a href="#">Culture</a>
            </nav>
            <a class="brand" href="/guerre-iran-accueil.html" aria-label="Retour a l'accueil">Orient Vif</a>
            <nav class="main-nav" aria-label="Navigation principale">
                <a class="<?php echo $activeMenu === 'accueil' ? 'is-active' : ''; ?>" href="/guerre-iran-accueil.html">Accueil</a>
                <a class="<?php echo $activeMenu === 'actualites' ? 'is-active' : ''; ?>" href="/guerre-iran-actualites.html">Actualites</a>
                <a href="#">Science</a>
                <a href="#">Sport</a>
            </nav>
        </div>
        <nav class="subnav" aria-label="Sous-rubriques">
            <a href="#">Iran</a>
            <a href="#">Israel</a>
            <a href="#">Etats-Unis</a>
            <a href="#">Russie</a>
            <a href="#">ONU</a>
            <a href="#">Humanitaire</a>
            <a href="#">Economie de guerre</a>
            <a href="#">Chronologie</a>
        </nav>
    </header>