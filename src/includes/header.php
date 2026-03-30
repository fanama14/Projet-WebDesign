<?php

if (!isset($pageTitle) || trim((string)$pageTitle) === '') {
    $pageTitle = 'Actualites guerre Iran | FrontOffice';
}

if (!isset($pageDescription) || trim((string)$pageDescription) === '') {
    $pageDescription = 'FrontOffice moderne sur les actualites de la guerre en Iran.';
}

if (!isset($canonicalUrl) || trim((string)$canonicalUrl) === '') {
    $canonicalUrl = front_canonical_url('/guerre-iran-accueil.html');
}

if (!isset($activeMenu)) {
    $activeMenu = 'accueil';
}
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

    <link rel="stylesheet" href="/css/style.css">
</head>

<body>
    <header class="site-header">
        <div class="header-topline">
            <p>Edition numerique - Dossier special conflit en Iran</p>
        </div>
        <div class="header-main">
            <a class="brand" href="/guerre-iran-accueil.html" aria-label="Retour a l'accueil">Iran Chronicle</a>
            <nav class="main-nav" aria-label="Navigation principale">
                <a class="<?php echo $activeMenu === 'accueil' ? 'is-active' : ''; ?>" href="/guerre-iran-accueil.html">Accueil</a>
                <a class="<?php echo $activeMenu === 'actualites' ? 'is-active' : ''; ?>" href="/guerre-iran-actualites.html">Actualites</a>
                <a class="<?php echo $activeMenu === 'contact' ? 'is-active' : ''; ?>" href="/guerre-iran-contact.html">Contact</a>
            </nav>
        </div>
    </header>
