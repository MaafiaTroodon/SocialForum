<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET' && isset($_GET['post_id'])) {
    $postId = intval($_GET['post_id']);
    $stmt = $mysqli->prepare("SELECT comments.comment, comments.created_at, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = ? ORDER BY comments.created_at DESC");
    $stmt->bind_param("i", $postId);
    $stmt->execute();
    $result = $stmt->get_result();
    $comments = [];
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }
    echo json_encode(['success' => true, 'comments' => $comments]);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $postId = intval($data['post_id']);
    $comment = htmlspecialchars(trim($data['comment']));
    $userId = $_SESSION['user_id'];
    $stmt = $mysqli->prepare("INSERT INTO comments (post_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("iis", $postId, $userId, $comment);
    $stmt->execute();
    echo json_encode(['success' => $stmt->affected_rows > 0]);
}
$mysqli->close();
?>