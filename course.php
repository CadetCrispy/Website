<?php
// [2025-06-08 01:14 AM] Added lesson listing and detail view
include 'includes/header.php';
require_once 'includes/db.php';
require_once 'includes/auth.php';
$user = currentUser();

if (isset($_GET['lesson_id'])) {
    // Handle marking complete
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $error = 'Invalid CSRF token.';
        } else {
            $lessonId = intval($_GET['lesson_id']);
            // Ensure lesson exists
            $stmt = $pdo->prepare('SELECT * FROM lessons WHERE id = ?');
            $stmt->execute([$lessonId]);
            if ($stmt->fetch()) {
                // Insert or update progress to 100%
                $stmtProg = $pdo->prepare('SELECT id FROM user_lessons WHERE user_id = ? AND lesson_id = ?');
                $stmtProg->execute([$user['id'], $lessonId]);
                if ($stmtProg->fetch()) {
                    $update = $pdo->prepare('UPDATE user_lessons SET progress = 100 WHERE user_id = ? AND lesson_id = ?');
                    $update->execute([$user['id'], $lessonId]);
                } else {
                    $insert = $pdo->prepare('INSERT INTO user_lessons (user_id, lesson_id, progress) VALUES (?, ?, 100)');
                    $insert->execute([$user['id'], $lessonId]);
                }
            }
            header("Location: course.php?lesson_id=$lessonId");
            exit;
        }
    }

    // Display single lesson
    $lessonId = intval($_GET['lesson_id']);
    $stmt = $pdo->prepare('SELECT * FROM lessons WHERE id = ?');
    $stmt->execute([$lessonId]);
    $lesson = $stmt->fetch();
    ?>
    <main>
        <?php if (!$lesson): ?>
            <p>Lesson not found.</p>
        <?php else: ?>
            <h1><?= htmlspecialchars($lesson['title']) ?></h1>
            <?php if ($lesson['is_premium'] && (!$user || ($user['role'] !== 'premium' && $user['role'] !== 'admin'))): ?>
                <p>This is a premium lesson. <a href="#">Upgrade to premium</a></p>
            <?php else: ?>
                <div class="lesson-content">
                    <?= nl2br(htmlspecialchars($lesson['content'])) ?>
                </div>
                <?php
                $stmtProg = $pdo->prepare('SELECT progress FROM user_lessons WHERE user_id = ? AND lesson_id = ?');
                $stmtProg->execute([$user['id'], $lessonId]);
                $row = $stmtProg->fetch();
                $progress = $row ? $row['progress'] : 0;
                ?>
                <p>Progress: <?= $progress ?>%</p>
                <form method="post" action="course.php?lesson_id=<?= $lessonId ?>">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <button type="submit" class="btn--primary">Mark Complete</button>
                </form>
            <?php endif; ?>
            <p><a href="course.php">Back to lessons</a></p>
        <?php endif; ?>
    </main>
    <?php
} else {
    // Listing view
    $stmt = $pdo->query('SELECT * FROM lessons');
    $lessons = $stmt->fetchAll();
    ?>
    <main>
        <h1>Lessons</h1>
        <div class="lessons-grid">
            <?php foreach ($lessons as $lesson):
                if ($user) {
                    $stmtProg = $pdo->prepare('SELECT progress FROM user_lessons WHERE user_id = ? AND lesson_id = ?');
                    $stmtProg->execute([$user['id'], $lesson['id']]);
                    $row = $stmtProg->fetch();
                    $progress = $row ? $row['progress'] : 0;
                } else {
                    $progress = 0;
                }
                include 'templates/lesson-card.php';
            endforeach; ?>
        </div>
    </main>
    <?php
}

include 'includes/footer.php';
