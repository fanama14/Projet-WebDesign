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
?>
    <!doctype html>
    <html lang="fr">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo h($title); ?></title>
        <meta name="description" content="<?php echo h($description); ?>">
        <meta name="robots" content="index,follow">
        <?php if ($includeEditor): ?>
            <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
            <script>
                tinymce.init({
                    selector: '#contenu',
                    menubar: 'file edit view insert format tools table help',
                    plugins: 'lists link image table code autoresize',
                    toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | alignleft aligncenter alignright | link image table | code',
                    image_caption: true,
                    branding: false,
                    height: 420
                });
            </script>
        <?php endif; ?>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 24px;
                line-height: 1.45;
            }

            .top a {
                margin-right: 12px;
            }

            .box {
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 14px;
                margin: 14px 0;
            }

            .box img {
                max-width: 100%;
                height: auto;
                display: block;
                margin: 8px 0;
                border-radius: 6px;
            }

            input,
            textarea {
                width: 100%;
                padding: 8px;
                margin-top: 4px;
                margin-bottom: 10px;
            }

            button {
                padding: 8px 12px;
                cursor: pointer;
            }

            .error {
                color: #9a1a1a;
            }

            .ok {
                color: #0b6b2c;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            th,
            td {
                border-bottom: 1px solid #e7e7e7;
                text-align: left;
                padding: 8px;
            }
        </style>
    </head>

    <body>
    <?php
}

function render_nav()
{
    ?>
        <div class="top">
            <a href="/guerre-iran-accueil.html">Accueil</a>
            <a href="/articles/guerre-iran-article-1-1-5.html">Exemple article</a>
            <?php if (is_logged_in()): ?>
                <a href="/admin">Backoffice</a>
                <a href="/logout">Deconnexion</a>
            <?php else: ?>
                <a href="/login">Connexion BO</a>
            <?php endif; ?>
        </div>
    <?php
}

function render_foot()
{
    echo "</body></html>";
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
