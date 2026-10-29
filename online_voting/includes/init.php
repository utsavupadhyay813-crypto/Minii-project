<?php
// Common bootstrap: session, DB connection, helpers
ob_start();   // buffer output so redirects work after header.php prints HTML
date_default_timezone_set('Asia/Kolkata');
session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
session_start();

define('BASE_URL', '/online_voting');          // folder name inside htdocs
$DB_HOST = 'localhost'; $DB_NAME = 'online_voting'; $DB_USER = 'root'; $DB_PASS = '';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,   // real prepared statements
    ]);
} catch (PDOException $e) {
    die('Database connection failed. Import sql/database.sql and check includes/init.php');
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function redirect($path) { header('Location: ' . BASE_URL . '/' . $path); exit; }
function flash($msg, $type = 'success') { $_SESSION['flash'] = [$msg, $type]; }

function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_check() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(419); die('Invalid request (CSRF check failed).');
    }
}
function require_role($role) {
    if (empty($_SESSION['uid']) || $_SESSION['role'] !== $role) redirect('login.php');
}

// draft | scheduled | live | ended | closed
function election_state($el) {
    if ($el['status'] === 'draft')  return 'draft';
    if ($el['status'] === 'closed') return 'closed';
    $now = time();
    if ($now < strtotime($el['start_date'])) return 'scheduled';
    if ($now > strtotime($el['end_date']))   return 'ended';
    return 'live';
}
function badge($state) {
    return '<span class="badge b-' . e($state) . '">' . strtoupper(e($state)) . '</span>';
}
function results_visible($el) { return in_array(election_state($el), ['ended', 'closed'], true); }

// Returns [candidates with vote counts, total votes, eligible voters, turnout %, voted count]
function tally(PDO $pdo, $eid) {
    $s = $pdo->prepare('SELECT c.*, COUNT(v.vote_id) AS votes FROM candidates c
        LEFT JOIN votes v ON v.candidate_id = c.candidate_id
        WHERE c.election_id = ? GROUP BY c.candidate_id ORDER BY votes DESC, c.name');
    $s->execute([$eid]); $cands = $s->fetchAll();
    $total = array_sum(array_column($cands, 'votes'));
    $elig = (int)$pdo->query("SELECT COUNT(*) FROM voters WHERE status='verified'")->fetchColumn();
    $s = $pdo->prepare('SELECT COUNT(*) FROM voter_participation WHERE election_id = ?');
    $s->execute([$eid]); $voted = (int)$s->fetchColumn();
    return [$cands, $total, $elig, $elig ? round($voted * 100 / $elig, 1) : 0, $voted];
}
