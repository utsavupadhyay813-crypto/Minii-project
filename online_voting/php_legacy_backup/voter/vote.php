<?php $pageTitle = 'Cast Your Vote'; require '../includes/header.php'; require_role('voter');
$st = $pdo->prepare('SELECT * FROM voters WHERE user_id=?'); $st->execute([$_SESSION['uid']]); $me = $st->fetch();
$eid = (int)($_GET['id'] ?? $_POST['election_id'] ?? 0);
$st = $pdo->prepare('SELECT * FROM elections WHERE election_id=?'); $st->execute([$eid]); $el = $st->fetch();
if (!$el || election_state($el) !== 'live' || $me['status'] !== 'verified') { flash('Voting is not available for this election.', 'error'); redirect('voter/dashboard.php'); }

$chk = $pdo->prepare('SELECT 1 FROM voter_participation WHERE election_id=? AND voter_id=?'); $chk->execute([$eid, $me['voter_id']]);
if ($chk->fetch()) { flash('You have already voted in this election.', 'error'); redirect('voter/dashboard.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check(); $cid = (int)($_POST['candidate_id'] ?? 0);
    $v = $pdo->prepare('SELECT 1 FROM candidates WHERE candidate_id=? AND election_id=?'); $v->execute([$cid, $eid]);
    if (!$v->fetch()) { flash('Please select a valid candidate.', 'error'); redirect("voter/vote.php?id=$eid"); }
    try {
        $pdo->beginTransaction();
        // 1) mark participation (PRIMARY KEY blocks a second vote even in a race condition)
        $pdo->prepare('INSERT INTO voter_participation (election_id, voter_id) VALUES (?,?)')->execute([$eid, $me['voter_id']]);
        // 2) store the anonymous ballot (no voter id)
        $pdo->prepare('INSERT INTO votes (election_id, candidate_id) VALUES (?,?)')->execute([$eid, $cid]);
        $pdo->commit();
    } catch (PDOException $ex) {
        $pdo->rollBack(); flash('Your vote could not be recorded (possible duplicate).', 'error'); redirect('voter/dashboard.php');
    }
    $done = true;
}
$cs = $pdo->prepare('SELECT * FROM candidates WHERE election_id=? ORDER BY name'); $cs->execute([$eid]); $cands = $cs->fetchAll();
?>
<?php if (!empty($done)): ?>
<div class="card hero"><h1>✅ Vote recorded</h1><p>Thank you for voting in <b><?= e($el['title']) ?></b>. Your ballot is anonymous.</p>
<a class="btn" href="dashboard.php">Back to elections</a></div>
<?php else: ?>
<h1><?= e($el['title']) ?></h1>
<input id="search" placeholder="🔎 Search candidate or party" oninput="filter(this.value)" style="margin-bottom:14px">
<form method="post" onsubmit="return confirm('Submit your vote? This cannot be changed.')"><?= csrf_field() ?>
<input type="hidden" name="election_id" value="<?= $eid ?>">
<?php foreach ($cands as $c): ?>
<label class="cand" data-text="<?= e(strtolower($c['name'] . ' ' . $c['party'])) ?>">
  <input type="radio" name="candidate_id" value="<?= $c['candidate_id'] ?>" required>
  <span class="sym"><?= e($c['symbol']) ?></span>
  <span><b><?= e($c['name']) ?></b> <small>&middot; <?= e($c['party']) ?></small><br><small><?= e($c['manifesto']) ?></small></span>
</label>
<?php endforeach; ?>
<button class="btn ok">Submit Vote</button> <a class="btn ghost" href="dashboard.php">Cancel</a>
</form>
<script>function filter(t){t=t.toLowerCase();document.querySelectorAll('.cand').forEach(c=>c.style.display=c.dataset.text.includes(t)?'flex':'none')}</script>
<?php endif; require '../includes/footer.php'; ?>
