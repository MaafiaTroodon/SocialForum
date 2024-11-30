<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized. Please log in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['postId'])) {
        echo json_encode(['error' => 'Post ID is required.']);
        exit();
    }

    $postId = intval($data['postId']);

    try {
        // Check if the post belongs to the logged-in user
        $stmt = $mysqli->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            echo json_encode(['error' => 'Post not found.']);
            exit();
        }

        $post = $result->fetch_assoc();

        if ($post['user_id'] !== $user_id) {
            echo json_encode(['error' => 'Unauthorized action. This post does not belong to you.']);
            exit();
        }

        // Delete the post
        $stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();

        echo json_encode(['success' => true, 'message' => 'Post deleted successfully.']);
    } catch (Exception $e) {
        echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

$mysqli->close();
?>