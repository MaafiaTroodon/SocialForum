<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized. Please log in.']);
    exit();
}

try {
    // Retrieve and decode the JSON input
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['postId'], $data['title'], $data['content'])) {
        echo json_encode(['error' => 'Invalid input. Missing required fields.']);
        exit();
    }

    $postId = intval($data['postId']);
    $title = htmlspecialchars(trim($data['title']));
    $content = htmlspecialchars(trim($data['content']));
    $userId = $_SESSION['user_id'];

    // Debugging
    error_log("Edit Post: User ID: $userId, Post ID: $postId, Title: $title");

    // Check if the post exists
    $stmt = $mysqli->prepare("SELECT user_id FROM posts WHERE id = ?");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['error' => 'Post not found. Please refresh the page.']);
        exit();
    }

    $post = $result->fetch_assoc();

    if ($post['user_id'] != $userId) {
        echo json_encode(['error' => 'Unauthorized access to this post.']);
        exit();
    }

    // Update the post in the database
    $stmt = $mysqli->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $postId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Post updated successfully.']);
    } else {
        echo json_encode(['error' => 'Database update failed.']);
    }

} catch (Exception $e) {
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
} finally {
    $mysqli->close();
}

