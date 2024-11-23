<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: includes/login.php");
    exit();
}
require_once 'templates/header.php';
require_once 'includes/db_connect.php'; // Database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Dalhousie Forum</title>
    <link rel="stylesheet" href="assets/styles.css"> <!-- Adjust path as needed -->
</head>
<body>
    <?php include 'includes/header.php'; ?> <!-- Include header -->

    <div class="main-container">
        <h1>Welcome to Dalhousie Forum</h1>
        <?php if (isset($_SESSION['username'])): ?>
            <p>Hello and welcome, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>!</p>
        <?php endif; ?>
        <p>Below is the latest feed from the forum:</p>
        <div id="post-feed">
            <p>Loading posts...</p>
        </div>
    </div>

    <script>
    // Fetch posts dynamically using JavaScript
    async function fetchPosts() {
        try {
            const response = await fetch('api/posts.php');
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const posts = await response.json();
            console.log(posts); // Log the response for debugging

            const postFeed = document.getElementById('post-feed');
            postFeed.innerHTML = ''; // Clear the loading text

            // Check if posts array is empty
            if (posts.length === 0) {
                postFeed.innerHTML = '<p>No posts available.</p>';
                return;
            }

            // Populate posts
            posts.forEach(post => {
                const postDiv = document.createElement('div');
                postDiv.className = 'post';
                postDiv.innerHTML = `
                    <h3>${post.title}</h3>
                    <p>${post.content}</p>
                    <p><strong>Author:</strong> ${post.username}</p>
                    <p><em>Posted on:</em> ${new Date(post.created_at).toLocaleString()}</p>
                `;
                postFeed.appendChild(postDiv);
            });
        } catch (error) {
            console.error("Error fetching posts:", error);
            document.getElementById('post-feed').innerHTML = `<p>Error loading posts. Please try again later.</p>`;
        }
    }

    // Call the fetchPosts function on page load
    fetchPosts();
    </script>

    <?php include 'includes/footer.php'; ?> <!-- Include footer -->
</body>
</html>