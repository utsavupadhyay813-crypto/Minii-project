<?php $pageTitle = 'Election Results'; require '../includes/header.php'; require_role('admin');
$elections = $pdo->query('SELECT * FROM elections ORDER BY start_date DESC')->fetchAll();
$eid = (int)($_GET['election'] ?? ($elections[0]['election_id'] ?? 0));
$cur = null; foreach ($elections as $x) if ($x['election_id'] == $eid) $cur = $x;
?>
<h1>Results &amp; Analytics</h1>
<?php if (!$cur): ?><div class="card">No elections yet.</div><?php else:
[$cands, $total, $elig, $turn, $voted] = tally($pdo, $eid); $max = $cands ? $cands[0]['votes'] : 0; ?>
<form method="get" class="card noprint"><label>Election</label><select name="election" onchange="this.form.submit()">
<?php foreach ($elections as $x): ?><option value="<?= $x['election_id'] ?>" <?= $x['election_id'] == $eid ? 'selected' : '' ?>><?= e($x['title']) ?></option><?php endforeach; ?></select></form>
<div class="card"><h2><?= e($cur['title']) ?> <?= badge(election_state($cur)) ?></h2>
<p><?= e($cur['start_date']) ?> &rarr; <?= e($cur['end_date']) ?></p>
<div class="grid"><div class="stat"><b><?= $total ?></b><span>Votes cast</span></div><div class="stat"><b><?= $elig ?></b><span>Eligible voters</span></div><div class="stat"><b><?= $turn ?>%</b><span>Turnout</span></div></div><br>
<?php foreach ($cands as $c): $pct = $total ? round($c['votes'] * 100 / $total, 1) : 0; ?>
<div class="<?= ($max > 0 && $c['votes'] == $max && election_state($cur) !== 'live') ? 'winner' : '' ?>" style="padding-left:8px">
<b><?= e($c['symbol']) ?> <?= e($c['name']) ?></b> <small>(<?= e($c['party']) ?>)</small> &mdash; <?= $c['votes'] ?> votes (<?= $pct ?>%)
<div class="bar"><i style="width:<?= $pct ?>%"></i></div></div>
<?php endforeach; ?>
<p class="noprint"><button class="btn" onclick="window.print()">🖨️ Print / Save as PDF</button></p>
<p><small>Generated on <?= date('d M Y, h:i A') ?></small></p></div>
<?php endif; require '../includes/footer.php'; ?>
