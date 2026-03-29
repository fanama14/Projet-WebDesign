<?php

require_once __DIR__ . '/../includes/bootstrap.php';

require_login();

$ok = isset($_GET['ok']) ? $_GET['ok'] : '';
$editArticle = null;

if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM articles WHERE id = :id LIMIT 1');
    $stmt->execute(array('id' => (int)$_GET['edit']));
    $editArticle = $stmt->fetch();
}

$articles = $pdo->query('SELECT * FROM articles ORDER BY updated_at DESC')->fetchAll();

render_head('Backoffice Articles - Iran News', 'Gestion des articles: ajout, modification et suppression.');
render_nav();
?>

<h1>Backoffice Articles</h1>

<?php if ($ok): ?>
    <p class="ok">Operation reussie.</p>
<?php endif; ?>

<div class="box">
    <h2><?php echo $editArticle ? 'Modifier un article' : 'Ajouter un article'; ?></h2>
    <form method="post" action="/admin/save.php">
        <?php if ($editArticle): ?>
            <input type="hidden" name="id" value="<?php echo (int)$editArticle['id']; ?>">
        <?php endif; ?>

        <label>Titre</label>
        <input name="title" required value="<?php echo h(isset($editArticle['title']) ? $editArticle['title'] : ''); ?>">

        <label>Slug (URL)</label>
        <input name="slug" required value="<?php echo h(isset($editArticle['slug']) ? $editArticle['slug'] : ''); ?>">

        <label>Contenu</label>
        <textarea name="content" rows="6" required><?php echo h(isset($editArticle['content']) ? $editArticle['content'] : ''); ?></textarea>

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
                <td><a href="/<?php echo h($article['slug']); ?>"><?php echo h($article['slug']); ?></a></td>
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