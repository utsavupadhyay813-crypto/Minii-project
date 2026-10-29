<?php $pageTitle = 'My Elections'; require '../includes/header.php'; require_role('voter');
$st = $pdo->prepare('SELECT * FROM voters WHERE user_id=?'); $st->execute([$_SESSION['uid']]); $me = $st->fetch();
$elections = $pdo->query("SELECT * FROM elections WHERE status<>'draft' ORDER BY start_date DESC")->fetchAll();
$done = $pdo->prepare('SELECT election_id FROM voter_participation WHERE voter_id=?'); $done->execute([$me['voter_id']]);
$voted = array_column($done->fetchAll(), 'election_id');
?>
<h1>Welcome, <?= e($_SESSION['name']) ?></h1>
<div class="card">Voter No: <b><?= e($me['voter_number']) ?></b> &nbsp; Status: <?= badge($me['status']) ?>
<?php if ($me['status'] !== 'verified') echo '<p>Your account must be verified by the admin before you can vote.</p>'; ?></div>
<div class="card"><h3>Elections</h3><div class="table-wrap"><table><tr><th>Title</th><th>Schedule</th><th>State</th><th></th></tr>
<?php foreach ($elections as $el): $state = election_state($el); ?>
<tr><td><b><?= e($el['title']) ?></b><br><small><?= e($el['description']) ?></small></td>
<td><?= e($el['start_date']) ?><br><small>to <?= e($el['end_date']) ?></small></td><td><?= badge($state) ?></td><td>
<?php if (in_array($el['election_id'], $voted)) echo '✅ Voted ';
 if ($state === 'live' && $me['status'] === 'verified' && !in_array($el['election_id'], $voted)) echo '<a class="btn sm ok" href="vote.php?id='.$el['election_id'].'">Vote now</a>';
 if (results_visible($el)) echo ' <a class="btn sm ghost" href="results.php?id='.$el['election_id'].'">Results</a>'; ?></td></tr>
<?php endforeach; if (!$elections) echo '<tr><td colspan="4">No elections available.</td></tr>'; ?></table></div></div>
<?php require '../includes/footer.php'; ?>
