<?php
require_once '../includes/db_connect.php'; // Include database connection

header('Content-Type: application/json'); // Ensure JSON output format

try {
    $stmt = $mysqli->prepare("
        SELECT 
            posts.id, 
            posts.title, 
            posts.content, 
            users.username, 
            posts.created_at 
        FROM posts 
        JOIN users ON posts.user_id = users.id 
        ORDER BY posts.created_at DESC
    ");
    $stmt->execute();
    $result = $stmt->get_result();

    $posts = [];
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }

    echo json_encode($posts); // Properly encode posts as JSON
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$mysqli->close(); // Close database connection
?>