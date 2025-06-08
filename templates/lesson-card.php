<?php
// [2025-06-08 01:14 AM] Added lesson card template
/**
 * $lesson: ['id', 'title', 'description', 'content', 'is_premium']
 * $progress: int
 * $user: current user array or null
 */
?>
<div class="lesson-card">
    <h3 class="lesson-card__title"><?= htmlspecialchars(
        $lesson['title']
    ) ?></h3>
    <p class="lesson-card__description"><?= htmlspecialchars(
        $lesson['description']
    ) ?></p>
    <?php if ($lesson['is_premium'] && (!$user || ($user['role'] !== 'premium' && $user['role'] !== 'admin'))): ?>
        <button class="btn--primary" disabled>Locked</button>
    <?php else: ?>
        <a class="btn--primary" href="course.php?lesson_id=<?= $lesson['id'] ?>">View Lesson</a>
    <?php endif; ?>
    <div class="lesson-card__progress-bar">
        <div class="lesson-card__progress" style="width: <?= $progress ?>%;"></div>
    </div>
    <span class="lesson-card__progress-text"><?= $progress ?>% complete</span>
</div>
