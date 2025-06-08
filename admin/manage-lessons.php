<?php
// [2025-06-08 01:17 AM] Admin dashboard: lesson CRUD and analytics
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
ensureAdmin();
$error = '';

// Handle create and update actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token.';
    } else {
        $action = $_POST['action'] ?? '';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $is_premium = isset($_POST['is_premium']) ? 1 : 0;
        if ($title === '' || $description === '' || $content === '') {
            $error = 'All fields are required.';
        } else {
            if ($action === 'create') {
                $stmt = $pdo->prepare('INSERT INTO lessons (title, description, content, is_premium) VALUES (?, ?, ?, ?)');
                $stmt->execute([$title, $description, $content, $is_premium]);
            } elseif ($action === 'update') {
                $id = intval($_POST['lesson_id']);
                $stmt = $pdo->prepare('UPDATE lessons SET title = ?, description = ?, content = ?, is_premium = ? WHERE id = ?');
                $stmt->execute([$title, $description, $content, $is_premium, $id]);
            }
            header('Location: manage-lessons.php');
            exit;
        }
    }
}

// Handle delete action
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $pdo->prepare('DELETE FROM lessons WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: manage-lessons.php');
    exit;
}

// Fetch lessons
$stmt = $pdo->query('SELECT * FROM lessons');
$lessons = $stmt->fetchAll();

// Analytics: total users and completions per lesson
$usersCount = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$completions = $pdo->query(
    'SELECT l.title, COUNT(ul.id) AS completions
     FROM lessons l
     LEFT JOIN user_lessons ul ON l.id = ul.lesson_id AND ul.progress = 100
     GROUP BY l.id'
)->fetchAll();
?>
<main>
    <h1>Admin Dashboard: Manage Lessons</h1>
    <?php if ($error): ?>
        <p style="color:red"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <section>
        <h2>Create New Lesson</h2>
        <form method="post" action="manage-lessons.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="create">
            <label>Title:<br><input type="text" name="title" required></label><br>
            <label>Description:<br><textarea name="description" required></textarea></label><br>
            <label>Content:<br><textarea name="content" required></textarea></label><br>
            <label><input type="checkbox" name="is_premium"> Premium</label><br>
            <button type="submit" class="btn--primary">Add Lesson</button>
        </form>
    </section>
    <section>
        <h2>Existing Lessons</h2>
        <table>
            <thead><tr><th>ID</th><th>Title</th><th>Premium</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($lessons as $l): ?>
                <tr>
                    <td><?= $l['id'] ?></td>
                    <td><?= htmlspecialchars($l['title']) ?></td>
                    <td><?= $l['is_premium'] ? 'Yes' : 'No' ?></td>
                    <td>
                        <a href="manage-lessons.php?edit_id=<?= $l['id'] ?>">Edit</a> |
                        <a href="manage-lessons.php?delete_id=<?= $l['id'] ?>" onclick="return confirm('Delete this lesson?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
    <?php if (isset($_GET['edit_id'])):
        $editId = intval($_GET['edit_id']);
        $stmt2 = $pdo->prepare('SELECT * FROM lessons WHERE id = ?');
        $stmt2->execute([$editId]);
        $lesson = $stmt2->fetch();
        if ($lesson):
    ?>
    <section>
        <h2>Edit Lesson #<?= $lesson['id'] ?></h2>
        <form method="post" action="manage-lessons.php?edit_id=<?= $lesson['id'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="lesson_id" value="<?= $lesson['id'] ?>">
            <label>Title:<br><input type="text" name="title" value="<?= htmlspecialchars($lesson['title']) ?>" required></label><br>
            <label>Description:<br><textarea name="description" required><?= htmlspecialchars($lesson['description']) ?></textarea></label><br>
            <label>Content:<br><textarea name="content" required><?= htmlspecialchars($lesson['content']) ?></textarea></label><br>
            <label><input type="checkbox" name="is_premium" <?= $lesson['is_premium'] ? 'checked' : '' ?>> Premium</label><br>
            <button type="submit" class="btn--primary">Update Lesson</button>
        </form>
    </section>
    <?php endif; endif; ?>
    <section>
        <h2>Analytics</h2>
        <p>Total Users: <?= $usersCount ?></p>
        <table>
            <thead><tr><th>Lesson</th><th>Completions</th></tr></thead>
            <tbody>
            <?php foreach ($completions as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['title']) ?></td>
                    <td><?= $c['completions'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>
<?php include __DIR__ . '/../includes/footer.php'; ?>
