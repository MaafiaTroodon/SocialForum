<?php
session_start();
require_once '../includes/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$postId = intval($data['postId']);
$title = trim($data['title']);
$content = trim($data['content']);
$userId = $_SESSION['user_id'];

$stmt = $mysqli->prepare("SELECT user_id FROM posts WHERE id = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0 && $result->fetch_assoc()['user_id'] == $userId) {
    $stmt = $mysqli->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $content, $postId);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Database update failed.']);
    }
} else {
    echo json_encode(['error' => 'Unauthorized']);
}

$mysqli->close();