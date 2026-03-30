<?php

require_once __DIR__ . '/includes/bootstrap.php';

if (is_logged_in()) {
    header('Location: /admin');
    exit;
}

$error = null;
$usernameDefault = 'admin';
$passwordDefault = 'admin123';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(isset($_POST['username']) ? $_POST['username'] : '');
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $usernameDefault = $username;
    $passwordDefault = $password;

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM users WHERE username = :username LIMIT 1');
    $stmt->execute(array('username' => $username));
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user'] = array(
            'id' => (int)$user['id'],
            'username' => $user['username'],
        );
        header('Location: /admin');
        exit;
    }

    $error = 'Identifiants invalides.';
}

render_head('Connexion Backoffice - Iran News', 'Connexion administrateur pour gerer les contenus du site.');
render_nav();
?>

<h1>Connexion Backoffice</h1>

<?php if ($error): ?>
    <p class="error"><?php echo h($error); ?></p>
<?php endif; ?>

<div class="box">
    <form method="post" action="/login">
        <label>Username</label>
        <input name="username" required value="<?php echo h($usernameDefault); ?>">

        <label>Password</label>
        <input type="password" name="password" required value="<?php echo h($passwordDefault); ?>">

        <button type="submit">Se connecter</button>
    </form>
    <p>Identifiants par defaut: admin / admin123</p>
</div>

<?php render_foot(); ?>