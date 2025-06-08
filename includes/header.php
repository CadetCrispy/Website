<?php
// [2025-06-08 01:07 AM] Global header: session start, CSRF token, HTML head, and navigation
// [2025-06-08 01:19 AM] Security hardening: enforce HTTPS, secure cookies, and security headers
if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] === 'off') {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('Location: ' . $redirect);
    exit;
}
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => $_SERVER['HTTP_HOST'],
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer-when-downgrade');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self';");
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gro Bros</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <button class="hamburger" id="hamburger">&#9776;</button>
    <nav id="nav-menu">
        <a href="index.php">Home</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="signup.php">Sign Up</a>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>


