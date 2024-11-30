<?php
session_start();
require_once '../includes/db_connect.php'; // Ensure the database connection file is included

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized. Please log in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch all messages related to the logged-in user
    try {
        $stmt = $mysqli->prepare("
            SELECT 
                m.id, m.content, m.timestamp, 
                s.username AS sender_name, 
                r.username AS receiver_name, 
                m.sender_id, m.receiver_id
            FROM messages m
            JOIN users s ON m.sender_id = s.id
            JOIN users r ON m.receiver_id = r.id
            WHERE m.sender_id = ? OR m.receiver_id = ?
            ORDER BY m.timestamp DESC
        ");
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }

        echo json_encode($messages);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error fetching messages: ' . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle sending a new message
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['recipient'], $data['message'])) {
        $recipient = trim($data['recipient']);
        $message = trim($data['message']);

        if (empty($recipient) || empty($message)) {
            echo json_encode(['error' => 'Recipient and message fields are required.']);
            exit();
        }

        try {
            // Find the recipient's user ID
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
            $stmt->bind_param("iis", $user_id, $recipient_id, $message);
            $stmt->execute();

            echo json_encode(['success' => true, 'message' => 'Message sent successfully.']);
        } catch (Exception $e) {
            echo json_encode(['error' => 'Error sending message: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'Invalid request.']);
    }
} else {
    echo json_encode(['error' => 'Invalid request method.']);
}

$mysqli->close();