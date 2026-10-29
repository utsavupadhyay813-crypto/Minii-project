<?php $pageTitle = 'Online Voting System'; require 'includes/header.php';
if (!empty($_SESSION['uid'])) redirect($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'voter/dashboard.php'); ?>
<div class="card hero">
  <h1>🗳️ Online Voting System</h1>
  <p>Secure, transparent and anonymous digital elections.</p>
  <p><a class="btn" href="login.php">Login</a> <a class="btn ghost" href="register.php">Register as Voter</a></p>
</div>
<div class="grid">
  <div class="card"><h3>🔐 Secure</h3>Hashed passwords, CSRF protection, prepared statements.</div>
  <div class="card"><h3>🕵️ Anonymous</h3>Ballots are stored separately from voter identity.</div>
  <div class="card"><h3>☝️ One Person, One Vote</h3>Enforced by a database-level constraint.</div>
</div>
<?php require 'includes/footer.php'; ?>
