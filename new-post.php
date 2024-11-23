<?php
session_start();
require_once 'includes/db_connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: includes/login.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];

    if (!empty($title) && !empty($content)) {
        try {
            $stmt = $mysqli->prepare("INSERT INTO posts (user_id, title, content) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $title, $content);
            $stmt->execute();

            // Redirect to index.php
            header("Location: index.php");
            exit();
        } catch (Exception $e) {
            $error = "Error saving the post: " . $e->getMessage();
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
    <?php include 'templates/header.php'; ?> <!-- Include header -->

    <div class="main-container">
        <h1>Create a New Post</h1>

        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form method="POST" action="new-post.php">
            <label for="title">Title:</label>
            <input type="text" id="title" name="title" required>
            <br>
            <label for="content">Content:</label>
            <textarea id="content" name="content" required></textarea>
            <br>
            <button type="submit">Post</button>
        </form>
    </div>

    <?php include 'templates/footer.php'; ?> <!-- Include footer -->
</body>
</html>