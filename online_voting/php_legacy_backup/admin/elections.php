<?php $pageTitle = 'Manage Elections'; require '../includes/header.php'; require_role('admin');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $act = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    if ($act === 'create') {
        $t = trim($_POST['title'] ?? ''); $s = $_POST['start_date'] ?? ''; $en = $_POST['end_date'] ?? '';
        if ($t === '' || !$s || !$en || strtotime($en) <= strtotime($s)) flash('Enter a title and a valid start/end time.', 'error');
        else {
            $pdo->prepare('INSERT INTO elections (title,description,start_date,end_date) VALUES (?,?,?,?)')
                ->execute([$t, trim($_POST['description'] ?? ''), date('Y-m-d H:i:s', strtotime($s)), date('Y-m-d H:i:s', strtotime($en))]);
            flash('Election created as draft.');
        }
    } elseif (in_array($act, ['open', 'closed', 'draft'], true)) {
        if ($act === 'open') {
            $n = $pdo->prepare('SELECT COUNT(*) FROM candidates WHERE election_id=?'); $n->execute([$id]);
            if ($n->fetchColumn() < 2) { flash('Add at least 2 candidates before opening.', 'error'); header('Location: elections.php'); exit; }
        }
        $pdo->prepare('UPDATE elections SET status=? WHERE election_id=?')->execute([$act, $id]);
        flash('Election status updated.');
    } elseif ($act === 'delete') {
        $pdo->prepare("DELETE FROM elections WHERE election_id=? AND status='draft'")->execute([$id]);
        flash('Draft election deleted.');
    }
    header('Location: elections.php'); exit;
}
$list = $pdo->query('SELECT * FROM elections ORDER BY start_date DESC')->fetchAll();
?>
<h1>Elections</h1>
<div class="card"><h3>Create / schedule election</h3>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="create">
  <label>Title</label><input name="title" required>
  <label>Description</label><textarea name="description" rows="2"></textarea>
  <div class="grid"><div><label>Start</label><input type="datetime-local" name="start_date" required></div>
  <div><label>End</label><input type="datetime-local" name="end_date" required></div></div>
  <p><button class="btn">Create</button></p>
</form></div>
<div class="card"><h3>All elections</h3><div class="table-wrap"><table>
<tr><th>Title</th><th>Schedule</th><th>State</th><th>Actions</th></tr>
<?php foreach ($list as $el): ?>
<tr><td><?= e($el['title']) ?></td><td><?= e($el['start_date']) ?><br><small>to <?= e($el['end_date']) ?></small></td>
<td><?= badge(election_state($el)) ?></td><td>
<?php foreach ([['open','Start','ok'],['closed','Stop','bad'],['draft','Draft','ghost']] as [$a,$l,$cl]):
  if ($el['status'] === $a) continue; if ($a === 'draft' && $el['status'] !== 'open') continue; ?>
  <form class="inline" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="<?= $a ?>"><input type="hidden" name="id" value="<?= $el['election_id'] ?>"><button class="btn sm <?= $cl ?>"><?= $l ?></button></form>
<?php endforeach; if ($el['status'] === 'draft'): ?>
  <form class="inline" method="post" onsubmit="return confirm('Delete this election?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $el['election_id'] ?>"><button class="btn sm bad">Delete</button></form>
<?php endif; ?></td></tr>
<?php endforeach; if (!$list) echo '<tr><td colspan="4">No elections yet.</td></tr>'; ?>
</table></div>
<p><small>An election accepts votes only when status is <b>open</b> AND the current time is between start and end.</small></p></div>
<?php require '../includes/footer.php'; ?>
