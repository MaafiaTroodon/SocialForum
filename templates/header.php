<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Ensure session is started
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dalhousie Forum</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/styles.css"> <!-- Custom CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Akaya+Kanadaka&family=Nunito:ital,wght@0,700;1,700&family=Outfit:wght@100..900&family=Parkinsans:wght@300..800&family=Roboto:ital,wght@0,700;0,900;1,700;1,900&display=swap" rel="stylesheet">
<script src="../assets/scripts.js" type="module" defer></script>
    
</head>
<!-- Notifications -->
<section id="notifications" style="position: fixed; top: 10px; right: 10px; z-index: 1000; width: 300px;">
    <!-- Dynamic Notifications will be added here -->
</section>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="../index.php">
                    <img src="img/logo-img.jpeg" alt="Dalhousie Forum Logo" class="d-inline-block align-text-top" style="height: 50px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                        <div class="search-container">
    <input 
        type="text" 
        id="searchInput" 
        placeholder="Search posts or users..." 
        onkeyup="debouncedSearch()" 
    />
</div>
                        </li>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li class="nav-item"><a class="nav-link" href="new-post.php">New Post</a></li>
                            <li class="nav-item"><a class="nav-link" href="messages.php">Messages</a></li>
                            <li class="nav-item"><a class="nav-link" href="includes/logout.php">Logout</a></li>
                        <?php else: ?>
                            <li class="nav-item"><a class="nav-link" href="new-post.php">New Post</a></li>
                            <li class="nav-item"><a class="nav-link" href="messages.php">Messages</a></li>
                            <li class="nav-item"><a class="nav-link" href="includes/login.php">Login</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Bootstrap JS (for navbar toggle) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let debounceTimeout;

    function debouncedSearch() {
        clearTimeout(debounceTimeout);
        debounceTimeout = setTimeout(() => {
            performSearch();
        }, 300); // 300ms delay
    }

    async function performSearch() {
        const query = document.getElementById('searchInput').value.trim();
        if (query === '') {
            // Reset posts if the query is empty
            fetchPosts();
            return;
        }

        try {
            const response = await fetch('api/search.php?q=' + encodeURIComponent(query));
            const results = await response.json();

            if (results.success) {
                filterAndHighlightPosts(results.data, query);
            } else {
                console.error('No results found:', results.error);
            }
        } catch (error) {
            console.error('Error fetching search results:', error);
        }
    }

    function filterAndHighlightPosts(data, query) {
        const postFeed = document.getElementById('post-feed');
        postFeed.innerHTML = ''; // Clear the existing posts

        data.forEach(post => {
            // Highlight the query in the title and content
            const highlightedTitle = highlightText(post.title, query);
            const highlightedContent = highlightText(post.content, query);

            const postDiv = document.createElement('div');
            postDiv.className = 'post';
            postDiv.dataset.id = post.post_id;

            postDiv.innerHTML = `
                <div class="post-content">
                    <h3>${highlightedTitle}</h3>
                    <p>${highlightedContent}</p>
                    <p><strong>Author:</strong> ${post.username}</p>
                    <p><em>Posted on:</em> ${new Date(post.created_at).toLocaleString()}</p>
                </div>
                <div class="action-container">
                    <button class="edit-button" onclick="editPost(${post.post_id}, '${post.title}', '${post.content}')">✏️ Edit</button>
                    <button class="delete-button" onclick="deletePost(${post.post_id})">🗑️ Delete</button>
                </div>
            `;

            postFeed.appendChild(postDiv);
        });
    }

    function highlightText(text, query) {
        const regex = new RegExp(`(${query})`, 'gi'); // Create a regex to match the query case-insensitively
        return text.replace(regex, '<span class="highlight">$1</span>'); // Wrap matches in a span
    }
</script>

<style>
    .highlight {
        background-color: yellow; /* Highlight matching text with yellow */
        font-weight: bold;
    }
</style>
</body>
</html>