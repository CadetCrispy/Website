<?php
// [2025-06-08 01:12 AM] Added login form and processing
include 'includes/header.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Invalid CSRF token.';
    } else {
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $userId = authenticate($email, $password);
        if ($userId) {
            loginUser($userId);
            header('Location: dashboard.php');
            exit;
        } else {
            $message = 'Invalid email or password.';
        }
    }
}
?>
<main>
    <h1>Login</h1>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" action="login.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn--primary">Login</button>
    </form>
</main>
<?php include 'includes/footer.php';
