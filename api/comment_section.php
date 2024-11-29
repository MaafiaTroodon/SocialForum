<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

try {
    $method = $_SERVER['REQUEST_METHOD'];

    switch ($method) {
        case 'GET':
            if (isset($_GET['post_id'])) {
                $postId = intval($_GET['post_id']);
                $stmt = $mysqli->prepare("
                    SELECT 
                        comments.id, 
                        comments.comment, 
                        comments.created_at, 
                        users.username 
                    FROM comments 
                    JOIN users ON comments.user_id = users.id 
                    WHERE comments.post_id = ? 
                    ORDER BY comments.created_at ASC
                ");
                $stmt->bind_param("i", $postId);
                $stmt->execute();
                $result = $stmt->get_result();
        
                $comments = [];
                while ($row = $result->fetch_assoc()) {
                    $comments[] = $row;
                }
                echo json_encode(['success' => true, 'comments' => $comments]);
            } else {
                echo json_encode(['error' => 'Invalid post ID.']);
            }
            break;

            case 'POST':
                // Add a new comment
                $data = json_decode(file_get_contents('php://input'), true);
                if (isset($data['post_id'], $data['comment'])) {
                    $postId = intval($data['post_id']);
                    $comment = htmlspecialchars(trim($data['comment']));
                    $userId = $_SESSION['user_id']; // Ensure user ID is from session
            
                    $stmt = $mysqli->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->bind_param("iis", $postId, $userId, $comment);
                    $stmt->execute();
            
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true]);
                    } else {
                        echo json_encode(['error' => 'Failed to save comment.']);
                    }
                } else {
                    echo json_encode(['error' => 'Invalid input.']);
                }
                break;

        default:
            echo json_encode(['error' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$mysqli->close();
?>