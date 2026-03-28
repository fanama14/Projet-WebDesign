<?php
$page = $_GET['page'] ?? 'accueil';
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Projet Web Design</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 2rem;
            line-height: 1.5;
        }

        code {
            background: #f3f3f3;
            padding: 0.15rem 0.35rem;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <h1>Bonjour</h1>
    <p>Chef <strong><?php echo htmlspecialchars($page, ENT_QUOTES, 'UTF-8'); ?></strong></p>
    <p>Test rewrite: ouvrez <code>/guerre</code> dans l'URL.</p>
</body>

</html>