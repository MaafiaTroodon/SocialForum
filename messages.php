<?php
session_start();
require_once 'includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: includes/login.php");
    exit();
}

header('Content-Type: application/json');

try {
    // Fetch messages for the logged-in user
    $stmt = $mysqli->prepare("
        SELECT 
            messages.sender_id, 
            messages.receiver_id, 
            messages.content, 
            messages.timestamp,
            sender.username AS sender_name,
            receiver.username AS receiver_name
        FROM messages
        JOIN users AS sender ON messages.sender_id = sender.id
        JOIN users AS receiver ON messages.receiver_id = receiver.id
        WHERE messages.sender_id = ? OR messages.receiver_id = ?
        ORDER BY messages.timestamp DESC
    ");
    
    $user_id = $_SESSION['user_id'];
    $stmt->bind_param("ii", $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }

    echo json_encode($messages);
} catch (Exception $e) {
    // Return a JSON error message
    echo json_encode(['error' => $e->getMessage()]);
}

$mysqli->close();
?>