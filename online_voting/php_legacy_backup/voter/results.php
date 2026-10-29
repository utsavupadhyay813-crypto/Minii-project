<?php $pageTitle = 'Results'; require '../includes/header.php'; require_role('voter');
$st = $pdo->prepare('SELECT * FROM elections WHERE election_id=?'); $st->execute([(int)($_GET['id'] ?? 0)]); $el = $st->fetch();
if (!$el || !results_visible($el)) { flash('Results are not available yet.', 'error'); redirect('voter/dashboard.php'); }
[$cands, $total, , $turn] = tally($pdo, $el['election_id']); $max = $cands ? $cands[0]['votes'] : 0;
?>
<h1><?= e($el['title']) ?> &mdash; Results</h1>
<div class="card"><p>Total votes: <b><?= $total ?></b> &middot; Turnout: <b><?= $turn ?>%</b></p>
<?php foreach ($cands as $c): $pct = $total ? round($c['votes'] * 100 / $total, 1) : 0; ?>
<div class="<?= ($max > 0 && $c['votes'] == $max) ? 'winner' : '' ?>" style="padding-left:8px"><b><?= e($c['symbol']) ?> <?= e($c['name']) ?></b> <small>(<?= e($c['party']) ?>)</small> &mdash; <?= $c['votes'] ?> votes (<?= $pct ?>%)
<div class="bar"><i style="width:<?= $pct ?>%"></i></div></div>
<?php endforeach; ?></div>
<a class="btn ghost" href="dashboard.php">Back</a>
<?php require '../includes/footer.php'; ?>
