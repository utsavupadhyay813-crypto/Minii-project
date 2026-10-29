<?php $pageTitle = 'Manage Candidates'; require '../includes/header.php'; require_role('admin');
$elections = $pdo->query('SELECT * FROM elections ORDER BY start_date DESC')->fetchAll();
$eid = (int)($_GET['election'] ?? ($elections[0]['election_id'] ?? 0));
$cur = null; foreach ($elections as $x) if ($x['election_id'] == $eid) $cur = $x;
$editable = $cur && $cur['status'] === 'draft';   // candidates are locked once voting is opened

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $cur) {
    csrf_check();
    if (!$editable) flash('Candidates can only be changed while the election is in draft.', 'error');
    else {
        $act = $_POST['action'] ?? '';
        $name = trim($_POST['name'] ?? ''); $party = trim($_POST['party'] ?? '');
        $sym = trim($_POST['symbol'] ?? '🗳️') ?: '🗳️'; $man = trim($_POST['manifesto'] ?? '');
        if ($act === 'add' && $name && $party) {
            $pdo->prepare('INSERT INTO candidates (election_id,name,party,symbol,manifesto) VALUES (?,?,?,?,?)')->execute([$eid,$name,$party,$sym,$man]);
            flash('Candidate added.');
        } elseif ($act === 'update' && $name && $party) {
            $pdo->prepare('UPDATE candidates SET name=?,party=?,symbol=?,manifesto=? WHERE candidate_id=? AND election_id=?')
                ->execute([$name,$party,$sym,$man,(int)$_POST['id'],$eid]);
            flash('Candidate updated.');
        } elseif ($act === 'delete') {
            $pdo->prepare('DELETE FROM candidates WHERE candidate_id=? AND election_id=?')->execute([(int)$_POST['id'],$eid]);
            flash('Candidate deleted.');
        }
    }
    header("Location: candidates.php?election=$eid"); exit;
}
$cands = []; if ($cur) { $s = $pdo->prepare('SELECT * FROM candidates WHERE election_id=? ORDER BY name'); $s->execute([$eid]); $cands = $s->fetchAll(); }
$edit = null; if (isset($_GET['edit'])) foreach ($cands as $c) if ($c['candidate_id'] == (int)$_GET['edit']) $edit = $c;
?>
<h1>Candidates</h1>
<?php if (!$elections): ?><div class="card">Create an election first.</div><?php else: ?>
<form method="get" class="card"><label>Election</label>
<select name="election" onchange="this.form.submit()"><?php foreach ($elections as $x): ?>
<option value="<?= $x['election_id'] ?>" <?= $x['election_id'] == $eid ? 'selected' : '' ?>><?= e($x['title']) ?> (<?= e(election_state($x)) ?>)</option><?php endforeach; ?></select></form>
<?php if ($editable): ?>
<div class="card"><h3><?= $edit ? 'Edit candidate' : 'Add candidate' ?></h3>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
<?php if ($edit): ?><input type="hidden" name="id" value="<?= $edit['candidate_id'] ?>"><?php endif; ?>
<div class="grid"><div><label>Name</label><input name="name" required value="<?= e($edit['name'] ?? '') ?>"></div>
<div><label>Party / group</label><input name="party" required value="<?= e($edit['party'] ?? '') ?>"></div>
<div><label>Symbol (emoji)</label><input name="symbol" maxlength="4" value="<?= e($edit['symbol'] ?? '') ?>"></div></div>
<label>Manifesto</label><textarea name="manifesto" rows="3"><?= e($edit['manifesto'] ?? '') ?></textarea>
<p><button class="btn"><?= $edit ? 'Update' : 'Add' ?></button> <?php if ($edit) echo '<a class="btn ghost" href="candidates.php?election='.$eid.'">Cancel</a>'; ?></p></form></div>
<?php else: ?><div class="alert a-error">This election is no longer a draft, so candidates are locked.</div><?php endif; ?>
<div class="card"><h3>Candidate list</h3><div class="table-wrap"><table><tr><th></th><th>Name</th><th>Party</th><th>Manifesto</th><th></th></tr>
<?php foreach ($cands as $c): ?><tr><td style="font-size:1.6rem"><?= e($c['symbol']) ?></td><td><?= e($c['name']) ?></td><td><?= e($c['party']) ?></td><td><?= e($c['manifesto']) ?></td>
<td><?php if ($editable): ?><a class="btn sm ghost" href="?election=<?= $eid ?>&edit=<?= $c['candidate_id'] ?>">Edit</a>
<form class="inline" method="post" onsubmit="return confirm('Delete candidate?')"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $c['candidate_id'] ?>"><button class="btn sm bad">Delete</button></form><?php endif; ?></td></tr>
<?php endforeach; if (!$cands) echo '<tr><td colspan="5">No candidates yet.</td></tr>'; ?></table></div></div>
<?php endif; require '../includes/footer.php'; ?>
