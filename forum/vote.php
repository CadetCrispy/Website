<?php
// [2025-06-08 01:16 AM] Forum vote handler
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
ensureLoggedIn();
$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Invalid CSRF token.');
    }
    $postId = intval($_POST['post_id']);
    $vote = intval($_POST['vote']) === 1 ? 1 : -1;
    // Insert or update vote
    $stmt = $pdo->prepare('INSERT INTO post_votes (user_id, post_id, vote) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE vote = ?');
    $stmt->execute([$user['id'], $postId, $vote, $vote]);
}
// Redirect back
$ref = $_SERVER['HTTP_REFERER'] ?? '../forum/index.php';
header("Location: $ref");
exit; 