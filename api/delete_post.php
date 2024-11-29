<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (isset($data['postId'])) {
            $postId = intval($data['postId']);
            $userId = $_SESSION['user_id'];

            // Check if the post belongs to the logged-in user
            $stmt = $mysqli->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();
            $result = $stmt->get_result();
            $post = $result->fetch_assoc();

            if ($post && $post['user_id'] == $userId) {
                // Delete the post
                $stmt = $mysqli->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->bind_param("i", $postId);
                $stmt->execute();

                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['error' => 'Unauthorized action.']);
            }
        } else {
            echo json_encode(['error' => 'Invalid input.']);
        }
    } else {
        echo json_encode(['error' => 'Invalid request method']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$mysqli->close();