<?php

function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function slugify($value)
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/', '-', $value);
    if ($value === null) {
        $value = '';
    }
    return trim($value, '-');
}

function render_head($title, $description, $includeEditor = false)
{
    $boCssFile = __DIR__ . '/../assets/css/backoffice.css';
    $boCssVersion = is_file($boCssFile) ? (string)filemtime($boCssFile) : '1';
?>
    <!doctype html>
    <html lang="fr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo h($title); ?></title>
        <meta name="description" content="<?php echo h($description); ?>">
        <meta name="robots" content="index,follow">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8RkGH1Z0W3xVxA7l+Yh6fP0A6p3R5tw==" crossorigin="anonymous" referrerpolicy="no-referrer">
        <link rel="stylesheet" href="/assets/css/backoffice.css?v=<?php echo h($boCssVersion); ?>">
        <?php if ($includeEditor): ?>
            <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
            <script>
                tinymce.init({
                    selector: '#contenu',
                    menubar: 'file edit view insert format tools table help',
                    plugins: 'lists link image table code autoresize',
                    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | alignleft aligncenter alignright | link image table | code',
                    entity_encoding: 'raw',
                    image_caption: true,
                    branding: false,
                    height: 420
                });
            </script>
        <?php endif; ?>
    </head>

    <body>
        <div class="bo-layout">
        <?php
    }

    function render_nav()
    {
        ?>
            <aside class="bo-sidebar">
                <a class="bo-brand" href="/admin">
                    Iran Admin
                    <small>BackOffice editorial</small>
                </a>
                <nav class="bo-nav" aria-label="Menu administration">
                    <?php if (is_logged_in()): ?>
                        <a href="/admin#articles-list"><i class="fa-solid fa-list"></i>Voir les articles</a>
                        <a href="/admin#create-form"><i class="fa-solid fa-plus"></i>Ajouter</a>
                        <a class="bo-muted-link" href="/guerre-iran-accueil.html"><i class="fa-solid fa-house"></i>Retour site</a>
                        <a class="bo-muted-link" href="/logout"><i class="fa-solid fa-right-from-bracket"></i>Deconnexion</a>
                    <?php else: ?>
                        <a class="bo-muted-link" href="/login"><i class="fa-solid fa-user-lock"></i>Connexion BO</a>
                    <?php endif; ?>
                </nav>
            </aside>
            <main class="bo-main">
            <?php
        }

        function render_foot()
        {
            echo "</main></div></body></html>";
        }

        function render_article_html($html)
        {
            $clean = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
            if ($clean === null) {
                $clean = '';
            }

            return strip_tags(
                $clean,
                '<p><br><strong><b><em><i><u><ul><ol><li><a><h1><h2><h3><h4><h5><h6><blockquote><img><figure><figcaption>'
            );
        }
