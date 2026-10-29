<?php $pageTitle = 'Login'; require 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $_SESSION['tries'] = $_SESSION['tries'] ?? 0;
    if ($_SESSION['tries'] >= 5 && time() - ($_SESSION['last_try'] ?? 0) < 300) {
        flash('Too many attempts. Try again in 5 minutes.', 'error');
    } else {
        $st = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $st->execute([strtolower(trim($_POST['email'] ?? ''))]);
        $u = $st->fetch();
        if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
            session_regenerate_id(true);
            $_SESSION = ['uid' => $u['user_id'], 'name' => $u['name'], 'role' => $u['role']];
            redirect($u['role'] === 'admin' ? 'admin/dashboard.php' : 'voter/dashboard.php');
        }
        $_SESSION['tries']++; $_SESSION['last_try'] = time();
        flash('Invalid email or password.', 'error');
    }
    header('Location: login.php'); exit;
}
?>
<div class="card form-narrow">
  <h2>Login</h2>
  <form method="post"><?= csrf_field() ?>
    <label>Email</label><input type="email" name="email" required>
    <label>Password</label><input type="password" name="password" required>
    <p><button class="btn">Login</button> <a href="register.php">New voter? Register</a></p>
  </form>
</div>
<?php require 'includes/footer.php'; ?>
