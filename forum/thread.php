<?php
// [2025-06-08 01:15 AM] Forum single thread view and posts
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
$user = currentUser();

if (!isset($_GET['thread_id'])) {
    header('Location: index.php');
    exit;
}
$threadId = intval($_GET['thread_id']);

// Fetch thread
$stmt = $pdo->prepare('SELECT t.*, u.username FROM threads t JOIN users u ON u.id = t.user_id WHERE t.id = ?');
$stmt->execute([$threadId]);
$thread = $stmt->fetch();
if (!$thread) {
    echo '<p>Thread not found.</p>';
    include __DIR__ . '/../includes/footer.php';
    exit;
}

// Handle new post/comment
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'comment') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token.';
    } elseif (!$user) {
        $error = 'You must be logged in to comment.';
    } else {
        $content = trim($_POST['content']);
        if ($content !== '') {
            $stmt2 = $pdo->prepare('INSERT INTO posts (thread_id, user_id, content, created_at) VALUES (?, ?, ?, NOW())');
            $stmt2->execute([$threadId, $user['id'], $content]);
            header("Location: thread.php?thread_id=$threadId");
            exit;
        }
    }
}

?>
<main>
    <h1><?= htmlspecialchars($thread['title']) ?></h1>
    <p>by <?= htmlspecialchars($thread['username']) ?> at <?= $thread['created_at'] ?></p>
    <?php if ($error): ?>
        <p><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <section class="posts">
        <?php
        // Fetch posts
        $stmt3 = $pdo->prepare('SELECT p.*, u.username FROM posts p JOIN users u ON u.id = p.user_id WHERE p.thread_id = ? ORDER BY p.created_at ASC');
        $stmt3->execute([$threadId]);
        $posts = $stmt3->fetchAll();
        foreach ($posts as $post):
            // Fetch vote count
            $stmtV = $pdo->prepare('SELECT SUM(vote) AS total FROM post_votes WHERE post_id = ?');
            $stmtV->execute([$post['id']]);
            $totalVotes = $stmtV->fetchColumn() ?? 0;
        ?>
            <div class="post">
                <p><strong><?= htmlspecialchars($post['username']) ?></strong> at <?= $post['created_at'] ?></p>
                <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                <p>Score: <?= $totalVotes ?></p>
                <?php if ($user): ?>
                    <form method="post" action="vote.php" style="display:inline">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <button type="submit" name="vote" value="1" class="btn--primary">Upvote</button>
                        <button type="submit" name="vote" value="-1" class="btn--primary">Downvote</button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </section>
    <?php if ($user): ?>
        <form method="post" action="thread.php?thread_id=<?= $threadId ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="comment">
            <label for="content">Add a comment:</label>
            <textarea id="content" name="content" required></textarea>
            <button type="submit" class="btn--primary">Post Comment</button>
        </form>
    <?php else: ?>
        <p>Please <a href="../login.php">login</a> to comment.</p>
    <?php endif; ?>
</main>
<?php include __DIR__ . '/../includes/footer.php';
