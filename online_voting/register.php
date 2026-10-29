<?php $pageTitle = 'Voter Registration'; require 'includes/header.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $name = trim($_POST['name'] ?? ''); $email = strtolower(trim($_POST['email'] ?? ''));
    $pw = $_POST['password'] ?? ''; $pw2 = $_POST['confirm'] ?? '';
    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) flash('Enter a valid name and email.', 'error');
    elseif (strlen($pw) < 8) flash('Password must be at least 8 characters.', 'error');
    elseif ($pw !== $pw2) flash('Passwords do not match.', 'error');
    else {
        $chk = $pdo->prepare('SELECT 1 FROM users WHERE email = ?'); $chk->execute([$email]);
        if ($chk->fetch()) flash('Email already registered.', 'error');
        else {
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?, 'voter')")
                ->execute([$name, $email, password_hash($pw, PASSWORD_DEFAULT)]);
            $uid = $pdo->lastInsertId();
            $vn = 'VTR' . date('y') . str_pad((string)$uid, 5, '0', STR_PAD_LEFT);
            $pdo->prepare('INSERT INTO voters (user_id, voter_number) VALUES (?,?)')->execute([$uid, $vn]);
            $pdo->commit();
            flash("Registered! Your voter number is $vn. Wait for admin verification, then log in.");
            redirect('login.php');
        }
    }
    header('Location: register.php'); exit;
}
?>
<div class="card form-narrow">
  <h2>Voter Registration</h2>
  <form method="post"><?= csrf_field() ?>
    <label>Full name</label><input name="name" required>
    <label>Email</label><input type="email" name="email" required>
    <label>Password (min 8 chars)</label><input type="password" name="password" required minlength="8">
    <label>Confirm password</label><input type="password" name="confirm" required>
    <p><button class="btn">Register</button></p>
  </form>
</div>
<?php require 'includes/footer.php'; ?>
