<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (isset($data['postId'], $data['title'], $data['content'])) {
            $postId = intval($data['postId']);
            $title = htmlspecialchars(trim($data['title']));
            $content = htmlspecialchars(trim($data['content']));
            $userId = $_SESSION['user_id'];

            // Debugging: Log received data
            error_log("Post ID: $postId, Title: $title, Content: $content, User ID: $userId");

            // Check if the post belongs to the logged-in user
            $stmt = $mysqli->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            $result = $stmt->get_result();
            $post = $result->fetch_assoc();

            if ($post && $post['user_id'] == $userId) {
                // Update the post
                $stmt = $mysqli->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
                $stmt->bind_param("ssi", $title, $content, $postId);
                if ($stmt->execute()) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['error' => 'Failed to update post in the database.']);
                }
            } else {
                echo json_encode(['error' => 'Unauthorized action.']);
            }
        } else {
            echo json_encode(['error' => 'Invalid input.']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$mysqli->close();
?>