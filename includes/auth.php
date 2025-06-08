<?php
// [2025-06-08 01:10 AM] Added authentication functions
require_once __DIR__ . '/db.php';

/**
 * Register a new user if email not taken. Returns user ID or false.
 */
function registerUser($username, $email, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return false;
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role, join_date) VALUES (?, ?, ?, ?, NOW())');
    $stmt->execute([$username, $email, $hash, 'regular']);
    return $pdo->lastInsertId();
}

/**
 * Authenticate user credentials. Returns user ID or false.
 */
function authenticate($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT id, password FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        return $user['id'];
    }
    return false;
}

/**
 * Log in the user by setting session variable.
 */
function loginUser($userId) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $userId;
}

/**
 * Redirect to login if not logged in.
 */
function ensureLoggedIn() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Ensure the current user is an admin.
 */
function ensureAdmin() {
    ensureLoggedIn();
    global $pdo;
    $stmt = $pdo->prepare('SELECT role FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $role = $stmt->fetchColumn();
    if ($role !== 'admin') {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

/**
 * Get current user data.
 */
function currentUser() {
    global $pdo;
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    $stmt = $pdo->prepare('SELECT id, username, email, role, join_date FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}
