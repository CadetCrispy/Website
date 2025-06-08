<?php
// [2025-06-08 01:06 AM] Set up PDO database connection
require_once __DIR__ . '/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // [2025-06-08 01:06 AM] Handle connection error
    die("Database connection failed: " . $e->getMessage());
}

// [2025-06-08 01:14 AM] Ensure lessons and user_lessons tables exist
$pdo->exec("CREATE TABLE IF NOT EXISTS lessons (id INT AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, description TEXT, content TEXT, is_premium BOOLEAN DEFAULT FALSE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
$pdo->exec("CREATE TABLE IF NOT EXISTS user_lessons (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, lesson_id INT NOT NULL, progress INT DEFAULT 0, last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY unique_user_lesson (user_id, lesson_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
// [2025-06-08 01:15 AM] Ensure forum tables exist
$pdo->exec("CREATE TABLE IF NOT EXISTS threads (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
$pdo->exec("CREATE TABLE IF NOT EXISTS posts (id INT AUTO_INCREMENT PRIMARY KEY, thread_id INT NOT NULL, user_id INT NOT NULL, content TEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
$pdo->exec("CREATE TABLE IF NOT EXISTS post_votes (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, post_id INT NOT NULL, vote TINYINT NOT NULL, UNIQUE KEY unique_vote (user_id, post_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
