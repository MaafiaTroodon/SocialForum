<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: includes/login.php");
    exit();
}
require_once 'templates/header.php';
require_once 'includes/db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Dalhousie Forum</title>
    <link rel="stylesheet" href="assets/styles.css">
    <script src="assets/scripts.js" type="module" defer></script>
</head>
<body>
    <!-- Three.js Background -->
    <div class="relative w-screen h-screen">
        <div class="grid-icosahedron w-full h-full bg-black overflow-hidden"></div>
    </div>

    <!-- Forum Content -->
    <div class="forum-wrapper">
        <?php include 'includes/header.php'; ?>

        <div class="main-container">
            <h1>Welcome to Dalhousie Forum</h1>
            <?php if (isset($_SESSION['username'])): ?>
                <p>Hello and welcome, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>!</p>
            <?php endif; ?>
            <p>Below is the latest feed from the forum:</p>
            <div id="post-feed"></div>
        </div>
    </div>

    <div id="editModal" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Post</h2>
        <form id="editPostForm">
            <input type="hidden" id="editPostId">
            <label for="editTitle">Title:</label>
            <input type="text" id="editTitle" required>
            <label for="editContent">Content:</label>
            <textarea id="editContent" required></textarea>
            <button type="submit">Update</button>
        </form>
    </div>
</div>

    <?php include 'includes/footer.php'; ?>
</body>
</html>