    <?php // Run ONCE in the browser to create the default admin, then delete this file.
    require 'includes/init.php';
    $exists = $pdo->query("SELECT COUNT(*) FROM users WHERE role='admin'")->fetchColumn();
    if ($exists) { die('Admin already exists. Delete setup.php.'); }
    $st = $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?, 'admin')");
    $st->execute(['Administrator', 'admin@vote.com', password_hash('Admin@123', PASSWORD_DEFAULT)]);
    echo 'Admin created.<br>Email: admin@vote.com<br>Password: Admin@123<br><b>Delete setup.php now and change the password.</b>';
