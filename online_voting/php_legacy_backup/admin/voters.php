<?php $pageTitle = 'Manage Voters'; require '../includes/header.php'; require_role('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $act = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if (in_array($act, ['verified', 'rejected'], true)) {
        $pdo->prepare('UPDATE voters SET status=? WHERE voter_id=?')->execute([$act, $id]); flash('Voter ' . $act . '.');
    } elseif ($act === 'delete') {
        $pdo->prepare('DELETE u FROM users u JOIN voters v ON v.user_id=u.user_id WHERE v.voter_id=?')->execute([$id]); flash('Voter deleted.');
    } elseif ($act === 'add') {
        $name = trim($_POST['name'] ?? ''); $email = strtolower(trim($_POST['email'] ?? '')); $pw = $_POST['password'] ?? '';
        $chk = $pdo->prepare('SELECT 1 FROM users WHERE email=?'); $chk->execute([$email]);
        if (!$name || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pw) < 8) flash('Valid name, email and 8+ char password required.', 'error');
        elseif ($chk->fetch()) flash('Email already exists.', 'error');
        else {
            $pdo->beginTransaction();
            $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?, 'voter')")->execute([$name,$email,password_hash($pw, PASSWORD_DEFAULT)]);
            $uid = $pdo->lastInsertId();
            $pdo->prepare("INSERT INTO voters (user_id,voter_number,status) VALUES (?,?, 'verified')")->execute([$uid, 'VTR' . date('y') . str_pad((string)$uid, 5, '0', STR_PAD_LEFT)]);
            $pdo->commit(); flash('Voter added and verified.');
        }
    }
    header('Location: voters.php'); exit;
}
$q = trim($_GET['q'] ?? '');
$s = $pdo->prepare('SELECT v.*, u.name, u.email FROM voters v JOIN users u ON u.user_id=v.user_id WHERE u.name LIKE ? OR u.email LIKE ? OR v.voter_number LIKE ? ORDER BY v.status, u.name');
$s->execute(["%$q%", "%$q%", "%$q%"]); $voters = $s->fetchAll();
?>
<h1>Voters</h1>
<div class="card"><h3>Add voter</h3><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="add">
<div class="grid"><div><label>Name</label><input name="name" required></div><div><label>Email</label><input type="email" name="email" required></div>
<div><label>Password</label><input type="password" name="password" minlength="8" required></div></div><p><button class="btn">Add &amp; verify</button></p></form></div>
<div class="card"><form method="get" class="noprint"><input name="q" placeholder="🔎 Search name, email or voter number" value="<?= e($q) ?>"></form><br>
<div class="table-wrap"><table><tr><th>Voter No.</th><th>Name</th><th>Email</th><th>Status</th><th>Actions</th></tr>
<?php foreach ($voters as $v): ?><tr><td><?= e($v['voter_number']) ?></td><td><?= e($v['name']) ?></td><td><?= e($v['email']) ?></td><td><?= badge($v['status']) ?></td><td>
<?php foreach ([['verified','Verify','ok'],['rejected','Reject','warn']] as [$a,$l,$cl]): if ($v['status'] === $a) continue; ?>
<form class="inline" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="<?= $a ?>"><input type="hidden" name="id" value="<?= $v['voter_id'] ?>"><button class="btn sm <?= $cl ?>"><?= $l ?></button></form>
<?php endforeach; ?>
<form class="inline" method="post" onsubmit="return confirm('Delete voter?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $v['voter_id'] ?>"><button class="btn sm bad">Delete</button></form></td></tr>
<?php endforeach; if (!$voters) echo '<tr><td colspan="5">No voters found.</td></tr>'; ?></table></div></div>
<?php require '../includes/footer.php'; ?>
