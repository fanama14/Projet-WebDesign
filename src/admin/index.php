<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

$ok = isset($_SESSION['flash_ok']) ? $_SESSION['flash_ok'] : (isset($_GET['ok']) ? $_GET['ok'] : '');
$err = isset($_SESSION['flash_err']) ? $_SESSION['flash_err'] : (isset($_GET['err']) ? $_GET['err'] : '');
$note = isset($_SESSION['flash_note']) ? $_SESSION['flash_note'] : (isset($_GET['note']) ? $_GET['note'] : '');
$editArticle = null;

unset($_SESSION['flash_ok'], $_SESSION['flash_err'], $_SESSION['flash_note']);

$editorContent = '';

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare(
        "SELECT
            a.*,
            COALESCE(ai_main.image_path, '') AS image_url,
                COALESCE(ai_main.image_alt, '') AS image_alt,
                COALESCE(ai_thumb.image_path, '') AS image_thumb_url
         FROM articles a
         LEFT JOIN article_images ai_main
            ON ai_main.article_id = a.id AND ai_main.image_kind = 'main'
            LEFT JOIN article_images ai_thumb
                ON ai_thumb.article_id = a.id AND ai_thumb.image_kind = 'thumb'
         WHERE a.id = :id
         LIMIT 1"
    );
    $stmt->execute(array('id' => (int)$_GET['edit']));
    $editArticle = $stmt->fetch();

    if ($editArticle) {
        $editorContent = html_entity_decode((string)$editArticle['content'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}

$articles = $pdo->query(
    "SELECT
          a.*, 
          COALESCE(ai_main.image_path, '') AS image_main_url,
          COALESCE(ai_thumb.image_path, ai_main.image_path, '') AS image_thumb_url
      FROM articles a
      LEFT JOIN article_images ai_thumb
          ON ai_thumb.article_id = a.id AND ai_thumb.image_kind = 'thumb'
      LEFT JOIN article_images ai_main
          ON ai_main.article_id = a.id AND ai_main.image_kind = 'main'
      ORDER BY a.id ASC"
)->fetchAll();

render_head('Backoffice Articles - Iran News', 'Gestion des articles: ajout, modification et suppression.', true);
render_nav();
?>

<h1>Backoffice Articles</h1>

<?php if ($ok === 'created' || $ok === 'updated'): ?>
    <p class="alert alert-success">L'article sur la guerre en Iran a ete publie avec succes !</p>
<?php elseif ($ok === 'deleted'): ?>
    <p class="alert alert-success">Article supprime avec succes.</p>
<?php endif; ?>

<?php if ($note === 'slug_changed'): ?>
    <p class="alert alert-success">Le slug existait deja. Un slug unique a ete genere automatiquement.</p>
<?php endif; ?>

<?php if ($err === 'missing'): ?>
    <p class="alert alert-error">Le titre, le slug et le contenu sont obligatoires.</p>
<?php elseif ($err === 'slug_exists'): ?>
    <p class="alert alert-error">Ce slug existe deja. Utilisez un slug unique.</p>
<?php elseif ($err === 'db'): ?>
    <p class="alert alert-error">Erreur base de donnees pendant l'enregistrement.</p>
<?php elseif ($err === 'upload'): ?>
    <p class="alert alert-error">Erreur upload image: format non supporte ou fichier invalide.</p>
<?php endif; ?>

<div id="articles-list" class="box">
    <h2>Liste des articles</h2>
    <div class="table-wrap">
        <table>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Slug</th>
                <th>Publie</th>
                <th>Actions</th>
                <th>Image</th>
            </tr>
            <?php foreach ($articles as $article): ?>
                <tr>
                    <td><?php echo (int)$article['id']; ?></td>
                    <td><?php echo h($article['title']); ?></td>
                    <td>
                        <a href="/articles/guerre-iran-article-<?php echo (int)$article['id']; ?>-1-5.html">
                            <?php echo h($article['slug']); ?>
                        </a>
                    </td>
                    <td><?php echo (int)$article['is_published'] === 1 ? 'Oui' : 'Non'; ?></td>
                    <td class="actions-col">
                        <a class="btn btn-secondary" href="/admin?edit=<?php echo (int)$article['id']; ?>#create-form"><i class="fa-solid fa-pen"></i>Modifier</a>
                        <a class="btn" href="/admin/delete.php?id=<?php echo (int)$article['id']; ?>" onclick="return confirm('Supprimer cet article ?');"><i class="fa-solid fa-trash"></i>Supprimer</a>
                    </td>
                    <td>
                        <?php if (!empty($article['image_thumb_url'])): ?>
                            <a href="<?php echo h(!empty($article['image_main_url']) ? $article['image_main_url'] : $article['image_thumb_url']); ?>" target="_blank" rel="noopener" title="Voir l'image en grand">
                                <img class="table-thumb" src="<?php echo h($article['image_thumb_url']); ?>" alt="image article <?php echo (int)$article['id']; ?>" loading="lazy" decoding="async">
                            </a>
                        <?php else: ?>
                            <span class="link-muted">Aucune</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div id="create-form" class="box">
    <h2><?php echo $editArticle ? 'Modifier un article' : 'Ajouter un article'; ?></h2>
    <form method="post" action="/admin/save.php" enctype="multipart/form-data" onsubmit="if (window.tinymce) { tinymce.triggerSave(); }">
        <?php if ($editArticle): ?>
            <input type="hidden" name="id" value="<?php echo (int)$editArticle['id']; ?>">
        <?php endif; ?>

        <label>Titre</label>
        <input name="title" required value="<?php echo h(isset($editArticle['title']) ? $editArticle['title'] : ''); ?>">

        <label>Slug (URL)</label>
        <input name="slug" required value="<?php echo h(isset($editArticle['slug']) ? $editArticle['slug'] : ''); ?>">

        <label>Contenu</label>
        <textarea id="contenu" name="content" rows="6"><?php echo h($editorContent); ?></textarea>

        <label>Image (jpg, jpeg, png, webp, gif)</label>
        <input type="file" name="image_file" accept="image/*">

        <?php if (!empty($editArticle['image_url'])): ?>
            <p>Image principale actuelle: <a href="<?php echo h($editArticle['image_url']); ?>" target="_blank" rel="noopener"><?php echo h($editArticle['image_url']); ?></a></p>
        <?php endif; ?>
        <?php if (!empty($editArticle['image_thumb_url'])): ?>
            <p>Image miniature actuelle: <a href="<?php echo h($editArticle['image_thumb_url']); ?>" target="_blank" rel="noopener"><?php echo h($editArticle['image_thumb_url']); ?></a></p>
        <?php endif; ?>

        <label>Image ALT</label>
        <input name="image_alt" value="<?php echo h(isset($editArticle['image_alt']) ? $editArticle['image_alt'] : ''); ?>">

        <label>
            <input type="checkbox" name="is_published" <?php echo !isset($editArticle['is_published']) || (int)$editArticle['is_published'] === 1 ? 'checked' : ''; ?>>
            Publie
        </label>

        <div class="form-actions">
            <button type="submit"><i class="fa-solid fa-floppy-disk"></i><?php echo $editArticle ? 'Mettre a jour' : 'Ajouter'; ?></button>
            <?php if ($editArticle): ?><a class="btn btn-secondary" href="/admin">Annuler</a><?php endif; ?>
        </div>
    </form>
</div>

<?php render_foot(); ?>