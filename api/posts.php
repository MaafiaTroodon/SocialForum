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
            // Fetch all posts with vote counts
            $stmt = $mysqli->prepare("
    SELECT 
        posts.id, 
        posts.title, 
        posts.content, 
        users.username, 
        posts.created_at, 
        posts.vote_count
    FROM posts 
    JOIN users ON posts.user_id = users.id 
    ORDER BY posts.created_at DESC
");
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row; // Includes vote_count
}
echo json_encode($posts); // Check that vote_count is present in the JSON response
            break;

            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if (isset($data['title'], $data['content'])) {
                    $title = htmlspecialchars(trim($data['title']));
                    $content = htmlspecialchars(trim($data['content']));
                    $user_id = $_SESSION['user_id'];
                    $randomVotes = rand(0, 999);
            
                    try {
                        $stmt = $mysqli->prepare("INSERT INTO posts (user_id, title, content, vote_count) VALUES (?, ?, ?, ?)
                                                  ON DUPLICATE KEY UPDATE vote_count = vote_count");
                        $stmt->bind_param("issi", $user_id, $title, $content, $randomVotes);
                        $stmt->execute();
                        if ($stmt->affected_rows > 0) {
                            echo json_encode(['success' => true, 'post_id' => $mysqli->insert_id]);
                        } else {
                            echo json_encode(['error' => 'Duplicate post detected.']);
                        }
                    } catch (Exception $e) {
                        echo json_encode(['error' => $e->getMessage()]);
                    }
                } else {
                    echo json_encode(['error' => 'Invalid input.']);
                }
                break;
                case 'DELETE':
    // Decode the JSON input
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
    break;

        default:
            echo json_encode(['error' => 'Invalid request method.']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$mysqli->close();
?>