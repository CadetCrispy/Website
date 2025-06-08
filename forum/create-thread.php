<?php
// [2025-06-08 01:15 AM] Forum create thread
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
ensureLoggedIn();
$user = currentUser();

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Invalid CSRF token.';
    } else {
        $title = trim($_POST['title']);
        $content = trim($_POST['content']);
        if ($title === '' || $content === '') {
            $message = 'Please fill in all fields.';
        } else {
            $stmt = $pdo->prepare('INSERT INTO threads (user_id, title, created_at) VALUES (?, ?, NOW())');
            $stmt->execute([$user['id'], $title]);
            $threadId = $pdo->lastInsertId();
            $stmt2 = $pdo->prepare('INSERT INTO posts (thread_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())');
            $stmt2->execute([$threadId, $user['id'], $content]);
            header("Location: thread.php?thread_id=$threadId");
            exit;
        }
    }
}
?>
<main>
    <h1>Create Thread</h1>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" action="create-thread.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required>

        <label for="content">Post:</label>
        <textarea id="content" name="content" required></textarea>

        <button type="submit" class="btn--primary">Post Thread</button>
    </form>
</main>
<?php include __DIR__ . '/../includes/footer.php';
