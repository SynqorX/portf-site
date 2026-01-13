<?php
header('Content-Type: application/json');

// Open database
$db = new SQLite3('reviews.db');

// Ensure table exists
$db->exec('CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    rating INTEGER NOT NULL,
    message TEXT NOT NULL,
    date DATETIME DEFAULT CURRENT_TIMESTAMP
)');

// Fetch all reviews newest first
$result = $db->query('SELECT * FROM reviews ORDER BY date DESC');

$reviews = [];
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $reviews[] = [
        'name' => htmlspecialchars($row['name']),
        'rating' => intval($row['rating']),
        'message' => htmlspecialchars($row['message']),
        'date' => $row['date']
    ];
}

echo json_encode($reviews);

$db->close();
