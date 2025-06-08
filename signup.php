<?php
// [2025-06-08 01:11 AM] Added signup form and processing
include 'includes/header.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Invalid CSRF token.';
    } else {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        if ($password !== $confirm) {
            $message = 'Passwords do not match.';
        } else {
            $userId = registerUser($username, $email, $password);
            if ($userId) {
                loginUser($userId);
                header('Location: dashboard.php');
                exit;
            } else {
                $message = 'User with that email already exists.';
            }
        }
    }
}
?>
<main>
    <h1>Sign Up</h1>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" action="signup.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" class="btn--primary">Sign Up</button>
    </form>
</main>
<?php include 'includes/footer.php';
