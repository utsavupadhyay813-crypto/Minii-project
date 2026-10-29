<?php $pageTitle = 'Admin Dashboard'; require '../includes/header.php'; require_role('admin');
$c = fn($sql) => (int)$pdo->query($sql)->fetchColumn();
$elections = $pdo->query('SELECT * FROM elections ORDER BY start_date DESC LIMIT 5')->fetchAll();
?>
<h1>Admin Dashboard</h1>
<div class="grid">
  <div class="card stat"><b><?= $c("SELECT COUNT(*) FROM voters WHERE status='verified'") ?></b><span>Verified voters</span></div>
  <div class="card stat"><b><?= $c("SELECT COUNT(*) FROM voters WHERE status='pending'") ?></b><span>Pending verification</span></div>
  <div class="card stat"><b><?= $c('SELECT COUNT(*) FROM elections') ?></b><span>Elections</span></div>
  <div class="card stat"><b><?= $c('SELECT COUNT(*) FROM votes') ?></b><span>Total votes cast</span></div>
</div>
<div class="card"><h3>Recent elections</h3>
<div class="table-wrap"><table><tr><th>Title</th><th>Start</th><th>End</th><th>Status</th><th>Turnout</th></tr>
<?php foreach ($elections as $el): [, , , $turn, $voted] = tally($pdo, $el['election_id']); ?>
<tr><td><?= e($el['title']) ?></td><td><?= e($el['start_date']) ?></td><td><?= e($el['end_date']) ?></td>
<td><?= badge(election_state($el)) ?></td><td><?= $voted ?> voted (<?= $turn ?>%)</td></tr>
<?php endforeach; if (!$elections) echo '<tr><td colspan="5">No elections yet.</td></tr>'; ?>
</table></div></div>
<?php require '../includes/footer.php'; ?>
