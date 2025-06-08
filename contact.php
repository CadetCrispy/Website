<?php
// [2025-06-08 01:09 AM] Added contact form with CSRF protection
include 'includes/header.php';

$message = '';
if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Invalid CSRF token.';
    } else {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $msg = trim($_POST['message']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Invalid email address.';
        } else {
            // Placeholder for email sending logic
            $message = 'Thank you for your message!';
            // Regenerate CSRF token to prevent replay
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
?>
<main>
    <h1>Contact Us</h1>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" action="contact.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="message">Message:</label>
        <textarea id="message" name="message" required></textarea>

        <button type="submit" class="btn--primary">Send</button>
    </form>
</main>
<?php include 'includes/footer.php';
