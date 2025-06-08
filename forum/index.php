<?php
// [2025-06-08 01:15 AM] Forum thread list
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
$user = currentUser();

// Fetch threads with post counts and author
$stmt = $pdo->query(
    'SELECT t.id, t.title, t.user_id, t.created_at, u.username, COUNT(p.id) AS post_count
     FROM threads t
     LEFT JOIN posts p ON p.thread_id = t.id
     JOIN users u ON u.id = t.user_id
     GROUP BY t.id
     ORDER BY t.created_at DESC'
);
$threads = $stmt->fetchAll();
?>
<main>
    <h1>Forum</h1>
    <?php if ($user): ?>
        <a class="btn--primary" href="create-thread.php">Create Thread</a>
    <?php else: ?>
        <p>Please <a href="../login.php">login</a> to create threads.</p>
    <?php endif; ?>
    <ul class="thread-list">
        <?php foreach ($threads as $thread): ?>
            <li>
                <a href="thread.php?thread_id=<?= $thread['id'] ?>"><?= htmlspecialchars($thread['title']) ?></a>
                by <?= htmlspecialchars($thread['username']) ?> (<?= $thread['post_count'] ?> posts)
            </li>
        <?php endforeach; ?>
    </ul>
</main>
<?php include __DIR__ . '/../includes/footer.php';
