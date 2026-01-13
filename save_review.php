<?php
// save_review.php

// Use absolute path so both scripts write to SAME db
$dbPath = __DIR__ . '/reviews.db';

// Open or create database
$db = new SQLite3($dbPath, SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE);

// Create table if not exists
$db->exec("
CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    rating INTEGER NOT NULL CHECK(rating BETWEEN 1 AND 5),
    message TEXT NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP
);
");

// ---- INPUT VALIDATION ----

$name = trim($_POST['name'] ?? '');
$rating = (int)($_POST['rating'] ?? 0);
$message = trim($_POST['message'] ?? '');

// Basic validation
if ($name === '' || $message === '' || $rating < 1 || $rating > 5) {
    die('Invalid input');
}

// Length limits to prevent abuse
if (mb_strlen($name) > 60 || mb_strlen($message) > 2000) {
    die('Input too long');
}

// ---- INSERT SAFELY USING PREPARED STATEMENT ----

$stmt = $db->prepare('INSERT INTO reviews (name, rating, message) VALUES (:name, :rating, :message)');
$stmt->bindValue(':name', $name, SQLITE3_TEXT);
$stmt->bindValue(':rating', $rating, SQLITE3_INTEGER);
$stmt->bindValue(':message', $message, SQLITE3_TEXT);

$result = $stmt->execute();

if (!$result) {
    die('Database insert failed');
}

$db->close();

// Redirect back to review page
header('Location: reviews.php');
exit;
?>
