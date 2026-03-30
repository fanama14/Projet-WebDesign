<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

$ok = isset($_SESSION['flash_ok']) ? $_SESSION['flash_ok'] : (isset($_GET['ok']) ? $_GET['ok'] : '');
$err = isset($_SESSION['flash_err']) ? $_SESSION['flash_err'] : (isset($_GET['err']) ? $_GET['err'] : '');
$note = isset($_SESSION['flash_note']) ? $_SESSION['flash_note'] : (isset($_GET['note']) ? $_GET['note'] : '');
$editArticle = null;

unset($_SESSION['flash_ok'], $_SESSION['flash_err'], $_SESSION['flash_note']);

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare(
        "SELECT
            a.*,
            COALESCE(ai_main.image_path, '') AS image_url,
            COALESCE(ai_main.image_alt, '') AS image_alt
         FROM articles a
         LEFT JOIN article_images ai_main
            ON ai_main.article_id = a.id AND ai_main.image_kind = 'main'
         WHERE a.id = :id
         LIMIT 1"
    );
    $stmt->execute(array('id' => (int)$_GET['edit']));
    $editArticle = $stmt->fetch();
}

$articles = $pdo->query('SELECT * FROM articles ORDER BY updated_at DESC')->fetchAll();

render_head('Backoffice Articles - Iran News', 'Gestion des articles: ajout, modification et suppression.', true);
render_nav();
?>

<h1>Backoffice Articles</h1>

<?php if ($ok): ?>
    <p class="ok">Operation reussie.</p>
<?php endif; ?>

<?php if ($note === 'slug_changed'): ?>
    <p class="ok">Le slug existait deja. Un slug unique a ete genere automatiquement.</p>
<?php endif; ?>

<?php if ($err === 'missing'): ?>
    <p class="error">Le titre, le slug et le contenu sont obligatoires.</p>
<?php elseif ($err === 'slug_exists'): ?>
    <p class="error">Ce slug existe deja. Utilisez un slug unique.</p>
<?php elseif ($err === 'db'): ?>
    <p class="error">Erreur base de donnees pendant l'enregistrement.</p>
<?php endif; ?>

<div class="box">
    <h2><?php echo $editArticle ? 'Modifier un article' : 'Ajouter un article'; ?></h2>
    <form method="post" action="/admin/save.php" onsubmit="if (window.tinymce) { tinymce.triggerSave(); }">
        <?php if ($editArticle): ?>
            <input type="hidden" name="id" value="<?php echo (int)$editArticle['id']; ?>">
        <?php endif; ?>

        <label>Titre</label>
        <input name="title" required value="<?php echo h(isset($editArticle['title']) ? $editArticle['title'] : ''); ?>">

        <label>Slug (URL)</label>
        <input name="slug" required value="<?php echo h(isset($editArticle['slug']) ? $editArticle['slug'] : ''); ?>">

        <label>Contenu</label>
        <textarea id="contenu" name="content" rows="6"><?php echo h(isset($editArticle['content']) ? $editArticle['content'] : ''); ?></textarea>

        <label>Image URL</label>
        <input name="image_url" value="<?php echo h(isset($editArticle['image_url']) ? $editArticle['image_url'] : ''); ?>">

        <label>Image ALT</label>
        <input name="image_alt" value="<?php echo h(isset($editArticle['image_alt']) ? $editArticle['image_alt'] : ''); ?>">

        <label>
            <input type="checkbox" name="is_published" <?php echo !isset($editArticle['is_published']) || (int)$editArticle['is_published'] === 1 ? 'checked' : ''; ?>>
            Publie
        </label>

        <p>
            <button type="submit"><?php echo $editArticle ? 'Mettre a jour' : 'Ajouter'; ?></button>
            <?php if ($editArticle): ?><a href="/admin">Annuler</a><?php endif; ?>
        </p>
    </form>
</div>

<div class="box">
    <h2>Liste des articles</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Titre</th>
            <th>Slug</th>
            <th>Publie</th>
            <th>Actions</th>
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
                <td>
                    <a href="/admin?edit=<?php echo (int)$article['id']; ?>">Modifier</a>
                    <a href="/admin/delete.php?id=<?php echo (int)$article['id']; ?>" onclick="return confirm('Supprimer cet article ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php render_foot(); ?>