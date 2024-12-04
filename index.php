<?php
session_start();
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
    <script>
        window.isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
    </script>
</head>
<body>
    <!-- Include Header -->
    <?php include 'templates/header.php'; ?>

    <div class="relative w-screen h-screen">
    <div class="grid-icosahedron w-full h-full bg-black overflow-hidden"></div>
</div>
    <!-- Conditionally include edit modal -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php include 'edit_form.php'; ?>
    <?php endif; ?>

    <!-- Forum Content -->
    <div class="forum-wrapper">
        <div class="main-container">
            <h1>Welcome to Dalhousie Forum</h1>
            <?php if (isset($_SESSION['username'])): ?>
                <p>Hello and welcome, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>!</p>
            <?php else: ?>
                <p>Welcome! You are currently viewing the forum as a guest. Please <a href="includes/login.php">log in</a> to participate fully.</p>
            <?php endif; ?>

            <p>Below is the latest feed from the forum:</p>
            <div id="post-feed">
                <?php
                $query = "SELECT * FROM posts ORDER BY created_at DESC";
                $result = $mysqli->query($query);

                if ($result && $result->num_rows > 0):
                    while ($post = $result->fetch_assoc()):
                ?>
                        <div class="post">
                            <h3><?= htmlspecialchars($post['title']); ?></h3>
                            <p><?= htmlspecialchars($post['content']); ?></p>
                            <p><em>Posted on: <?= $post['created_at']; ?></em></p>

                            <!-- Only show edit/delete options for logged-in users -->
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button onclick="editPost(<?= $post['id']; ?>)">Edit</button>
                                <button onclick="deletePost(<?= $post['id']; ?>)">Delete</button>
                                <button onclick="commentOnPost(<?= $post['id']; ?>)">Comment</button>
                            <?php else: ?>
                                <p><strong>Login to edit, delete, or comment on this post.</strong></p>
                            <?php endif; ?>
                        </div>
                <?php
                    endwhile;
                else:
                ?>
                    <p>No posts available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'templates/footer.php'; ?>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const notification = document.getElementById("notification");
        if (notification) {
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 1000); // Remove element after fade-out
            }, 5000); // Display for 5 seconds
        }
    });
</script>
</body>
</html>