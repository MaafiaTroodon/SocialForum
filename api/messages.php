<?php
session_start();
require_once '../includes/db_connect.php'; // Adjust the path as needed

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle sending a new message
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['recipient']) && isset($data['message'])) {
        $recipient = trim($data['recipient']);
        $message = trim($data['message']);
        $sender_id = $_SESSION['user_id'];

        // Validate input
        if (empty($recipient) || empty($message)) {
            echo json_encode(['error' => 'Recipient and message are required.']);
            exit();
        }

        try {
            // Find the recipient's ID
            $stmt = $mysqli->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->bind_param("s", $recipient);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 0) {
                echo json_encode(['error' => 'Recipient does not exist.']);
                exit();
            }

            $recipient_id = $result->fetch_assoc()['id'];

            // Insert the new message into the database
            $stmt = $mysqli->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $sender_id, $recipient_id, $message);
            $stmt->execute();

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Invalid request.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Handle fetching messages
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
        echo json_encode(['error' => $e->getMessage()]);
    }
}

$mysqli->close();
?>