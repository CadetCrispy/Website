<?php
// [2025-06-08 01:17 AM] Serve lesson assets securely
include 'includes/header.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
ensureLoggedIn();
$user = currentUser();
if (!isset($_GET['id'])) {
    http_response_code(400);
    echo 'Missing file ID';
    exit;
}
$assetId = intval($_GET['id']);
$stmt = $pdo->prepare('SELECT * FROM lesson_assets WHERE id = ?');
$stmt->execute([$assetId]);
$asset = $stmt->fetch();
if (!$asset) {
    http_response_code(404);
    echo 'File not found';
    exit;
}
if ($asset['lesson_id']) {
    $stmt2 = $pdo->prepare('SELECT is_premium FROM lessons WHERE id = ?');
    $stmt2->execute([$asset['lesson_id']]);
    $lesson = $stmt2->fetch();
    if ($lesson && $lesson['is_premium'] && $user['role'] !== 'premium' && $user['role'] !== 'admin') {
        http_response_code(403);
        echo 'Premium asset. Upgrade to access.';
        exit;
    }
}
$filePath = __DIR__ . '/uploads/lesson-assets/' . $asset['filename'];
if (!file_exists($filePath)) {
    http_response_code(404);
    echo 'File missing';
    exit;
}
header('Content-Description: File Transfer');
header('Content-Type: ' . $asset['mime_type']);
header('Content-Disposition: attachment; filename="'.basename($asset['original_name']).'"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit; 