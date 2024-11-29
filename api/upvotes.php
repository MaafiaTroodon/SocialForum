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
        $postId = $data['postId'];
        $action = $data['action']; // 'upvote' or 'downvote'
        $userId = $_SESSION['user_id'];

        if (!in_array($action, ['upvote', 'downvote'])) {
            echo json_encode(['error' => 'Invalid action']);
            exit();
        }

        // Check if the user already upvoted/downvoted
        $stmt = $mysqli->prepare("SELECT vote_action FROM votes WHERE user_id = ? AND post_id = ?");
        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $existingVote = $result->fetch_assoc();

        if ($existingVote) {
            // If the same action is clicked again, remove the vote
            if ($existingVote['vote_action'] === $action) {
                $stmt = $mysqli->prepare("DELETE FROM votes WHERE user_id = ? AND post_id = ?");
                $stmt->bind_param("ii", $userId, $postId);
                $stmt->execute();

                // Adjust the vote count
                $voteChange = ($action === 'upvote') ? -1 : 1;
                $stmt = $mysqli->prepare("UPDATE posts SET vote_count = vote_count + ? WHERE id = ?");
                $stmt->bind_param("ii", $voteChange, $postId);
                $stmt->execute();
            } else {
                // If switching between upvote and downvote
                $stmt = $mysqli->prepare("UPDATE votes SET vote_action = ? WHERE user_id = ? AND post_id = ?");
                $stmt->bind_param("sii", $action, $userId, $postId);
                $stmt->execute();

                $voteChange = ($action === 'upvote') ? 2 : -2; // Switching impacts by 2
                $stmt = $mysqli->prepare("UPDATE posts SET vote_count = vote_count + ? WHERE id = ?");
                $stmt->bind_param("ii", $voteChange, $postId);
                $stmt->execute();
            }
        } else {
            // Add a new vote
            $stmt = $mysqli->prepare("INSERT INTO votes (user_id, post_id, vote_action) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $userId, $postId, $action);
            $stmt->execute();

            $voteChange = ($action === 'upvote') ? 1 : -1;
            $stmt = $mysqli->prepare("UPDATE posts SET vote_count = vote_count + ? WHERE id = ?");
            $stmt->bind_param("ii", $voteChange, $postId);
            $stmt->execute();
        }

        // Fetch the updated vote count
        $stmt = $mysqli->prepare("SELECT vote_count FROM posts WHERE id = ?");
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $voteCount = $result->fetch_assoc()['vote_count'];

        echo json_encode(['success' => true, 'newVoteCount' => $voteCount]);
    } else {
        echo json_encode(['error' => 'Invalid request method']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

$mysqli->close();
?>