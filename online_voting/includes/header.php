<?php require_once __DIR__ . '/init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'Online Voting System') ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<nav class="nav">
  <a class="brand" href="<?= BASE_URL ?>/">🗳️ Online Voting</a>
  <div class="links">
  <?php if (!empty($_SESSION['uid']) && $_SESSION['role'] === 'admin'): ?>
    <a href="<?= BASE_URL ?>/admin/dashboard.php">Dashboard</a>
    <a href="<?= BASE_URL ?>/admin/elections.php">Elections</a>
    <a href="<?= BASE_URL ?>/admin/candidates.php">Candidates</a>
    <a href="<?= BASE_URL ?>/admin/voters.php">Voters</a>
    <a href="<?= BASE_URL ?>/admin/results.php">Results</a>
  <?php elseif (!empty($_SESSION['uid'])): ?>
    <a href="<?= BASE_URL ?>/voter/dashboard.php">My Elections</a>
  <?php else: ?>
    <a href="<?= BASE_URL ?>/login.php">Login</a>
    <a href="<?= BASE_URL ?>/register.php">Register</a>
  <?php endif; ?>
  <?php if (!empty($_SESSION['uid'])): ?>
    <span class="who"><?= e($_SESSION['name']) ?></span>
    <a href="<?= BASE_URL ?>/logout.php">Logout</a>
  <?php endif; ?>
  </div>
</nav>
<main class="container">
<?php if (!empty($_SESSION['flash'])): [$fm, $ft] = $_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert a-<?= e($ft) ?>"><?= e($fm) ?></div>
<?php endif; ?>
