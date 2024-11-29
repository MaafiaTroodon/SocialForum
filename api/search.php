<?php
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

// Validate input query
if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode(['success' => false, 'data' => [], 'error' => 'Invalid or empty search query.']);
    exit();
}

$query = htmlspecialchars(trim($_GET['q'])); // Sanitize input

try {
    $stmt = $mysqli->prepare("
        SELECT 
            posts.id AS post_id, 
            posts.title, 
            posts.content, 
            users.username, 
            posts.created_at 
        FROM posts
        JOIN users ON posts.user_id = users.id
        WHERE posts.title LIKE ? OR posts.content LIKE ?
        LIMIT 20
    ");

    $likeQuery = "%$query%";
    $stmt->bind_param("ss", $likeQuery, $likeQuery);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = [
            'post_id' => $row['post_id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'username' => $row['username'],
            'created_at' => $row['created_at']
        ];
    }

    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$mysqli->close();