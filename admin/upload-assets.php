<?php
// [2025-06-08 01:16 AM] Admin upload for lesson assets
include __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
ensureAdmin();
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $message = 'Invalid CSRF token.';
    } elseif (!isset($_FILES['asset']) || $_FILES['asset']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Error uploading file.';
    } else {
        $file = $_FILES['asset'];
        $allowedExt = ['pdf','mp4','png','jpg','jpeg','gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            $message = 'Invalid file type.';
        } elseif ($file['size'] > 50 * 1024 * 1024) {
            $message = 'File exceeds 50MB.';
        } else {
            $newName = bin2hex(random_bytes(16)) . '.' . $ext;
            $dest = __DIR__ . '/../uploads/lesson-assets/' . $newName;
            if (move_uploaded_file($file['tmp_name'], $dest)) {
                $lessonId = !empty($_POST['lesson_id']) ? intval($_POST['lesson_id']) : null;
                $stmt = $pdo->prepare('INSERT INTO lesson_assets (lesson_id, filename, original_name, mime_type) VALUES (?, ?, ?, ?)');
                $stmt->execute([$lessonId, $newName, $file['name'], $file['type']]);
                $message = 'File uploaded successfully.';
            } else {
                $message = 'Failed to save file.';
            }
        }
    }
}
$lessons = $pdo->query('SELECT id, title FROM lessons')->fetchAll();
?>
<main>
    <h1>Upload Lesson Asset</h1>
    <?php if ($message): ?>
        <p><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data" action="upload-assets.php">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
        <label for="lesson_id">Lesson (optional):</label>
        <select name="lesson_id" id="lesson_id">
            <option value="">-- None --</option>
            <?php foreach ($lessons as $lesson): ?>
                <option value="<?= $lesson['id'] ?>"><?= htmlspecialchars($lesson['title']) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="asset">File:</label>
        <input type="file" name="asset" id="asset" required>
        <button type="submit" class="btn--primary">Upload</button>
    </form>
</main>
<?php include __DIR__ . '/../includes/footer.php'; 