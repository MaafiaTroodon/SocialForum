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
    <script defer>
    const userId = <?= json_encode($_SESSION['user_id']); ?>;

    async function fetchPosts() {
    try {
        const response = await fetch('api/posts.php');
        const posts = await response.json();

        const postFeed = document.getElementById('post-feed');
        postFeed.innerHTML = ''; // Clear existing posts to avoid duplicates

        if (!posts.length) {
            postFeed.innerHTML = '<p>No posts available.</p>';
            return;
        }

        // Render unique posts
        posts.forEach(post => {
            const postDiv = document.createElement('div');
            postDiv.className = 'post';
            postDiv.dataset.id = post.id;

            postDiv.innerHTML = `
                <div class="post-content">
                    <h3>${post.title}</h3>
                    <p>${post.content}</p>
                    <p><strong>Author:</strong> ${post.username}</p>
                    <p><em>Posted on:</em> ${new Date(post.created_at).toLocaleString()}</p>
                </div>
                <div class="vote-container">
                    <button class="upvote-button" onclick="toggleUpvote(${post.id}, 'upvote')">▲</button>
                    <div class="vote-count">${post.vote_count}</div>
                    <button class="downvote-button" onclick="toggleUpvote(${post.id}, 'downvote')">▼</button>
                </div>
                <div class="action-container">
                    <button class="edit-button" onclick="editPost(${post.id}, '${post.title}', '${post.content}')">✏️ Edit</button>
                    <button class="delete-button" onclick="deletePost(${post.id})">🗑️ Delete</button>
                </div>
            `;
            postFeed.appendChild(postDiv);
        });
    } catch (error) {
        console.error("Error fetching posts:", error);
    }
}

    async function toggleUpvote(postId, action) {
        try {
            const response = await fetch('api/upvotes.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ postId, action }),
            });

            const result = await response.json();
            if (result.success) {
                const postDiv = document.querySelector(`.post[data-id="${postId}"]`);
                const voteCountDiv = postDiv.querySelector('.vote-count');
                voteCountDiv.textContent = result.newVoteCount;
            } else {
                console.error('Error updating vote count:', result.message);
            }
        } catch (error) {
            console.error("Error toggling upvote:", error);
        }
    }

    document.addEventListener('DOMContentLoaded', fetchPosts);

    async function deletePost(postId) {
    if (confirm('Do you really want to delete this post?')) {
        try {
            const response = await fetch('api/delete_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ postId }),
            });

            const result = await response.json();
            if (result.success) {
                // Remove the post from the DOM
                const postDiv = document.querySelector(`.post[data-id="${postId}"]`);
                postDiv.remove();
            } else {
                console.error('Error deleting post:', result.message);
                alert('Failed to delete post.');
            }
        } catch (error) {
            console.error("Error deleting post:", error);
        }
    }
}
async function fetchPosts() {
    try {
        const response = await fetch('api/posts.php');
        const posts = await response.json();

        const postFeed = document.getElementById('post-feed');
        postFeed.innerHTML = ''; // Clear existing posts

        if (!posts.length) {
            postFeed.innerHTML = '<p>No posts available.</p>';
            return;
        }

        posts.forEach(post => {
            const postDiv = document.createElement('div');
            postDiv.className = 'post';
            postDiv.dataset.id = post.id;

            postDiv.innerHTML = `
    <div class="post-content">
        <h3>${post.title}</h3>
        <p>${post.content}</p>
        <p><strong>Author:</strong> ${post.username}</p>
        <p><em>Posted on:</em> ${new Date(post.created_at).toLocaleString()}</p>
    </div>
    <div class="vote-container">
        <button class="upvote-button" onclick="toggleUpvote(${post.id}, 'upvote')">▲</button>
        <div class="vote-count">${post.vote_count}</div>
        <button class="downvote-button" onclick="toggleUpvote(${post.id}, 'downvote')">▼</button>
    </div>
    <div class="action-container">
        <button class="edit-button" onclick="editPost(${post.id}, '${post.title}', '${post.content}')">✏️ Edit</button>
        <button class="delete-button" onclick="deletePost(${post.id})">🗑️ Delete</button>
    </div>
`;
postDiv.innerHTML = `
    <div class="post-content">
        <h3>${post.title}</h3>
        <p>${post.content}</p>
        <p><strong>Author:</strong> ${post.username}</p>
        <p><em>Posted on:</em> ${new Date(post.created_at).toLocaleString()}</p>
    </div>
    <div class="vote-container">
        <button class="upvote-button" onclick="toggleUpvote(${post.id}, 'upvote')">▲</button>
        <div class="vote-count">${post.vote_count}</div>
        <button class="downvote-button" onclick="toggleUpvote(${post.id}, 'downvote')">▼</button>
    </div>
    <div class="action-container">
        <button class="edit-button" onclick="editPost(${post.id}, '${post.title}', '${post.content}')">✏️ Edit</button>
        <button class="delete-button" onclick="deletePost(${post.id})">🗑️ Delete</button>
        <button class="comment-button" onclick="toggleCommentSection(${post.id})">💬 Comments</button>
    </div>
    <div class="comment-section" id="comment-section-${post.id}" style="display: none;">
        <div class="comments"></div>
        <textarea id="new-comment-${post.id}" placeholder="Add a comment..."></textarea>
        <button onclick="addComment(${post.id})">Post</button>
    </div>
`;
            postFeed.appendChild(postDiv);
        });
    } catch (error) {
        console.error("Error fetching posts:", error);
    }
}
async function editPost(postId, currentTitle, currentContent) {
    const newTitle = prompt("Edit title:", currentTitle);
    const newContent = prompt("Edit content:", currentContent);

    if (newTitle && newContent) {
        try {
            const response = await fetch('api/edit_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ postId, title: newTitle, content: newContent }),
            });

            const result = await response.json();
            if (result.success) {
                alert('Post updated successfully!');
                fetchPosts(); // Refresh posts
            } else {
                alert(result.error || 'Failed to update post.');
            }
        } catch (error) {
            console.error("Error editing post:", error);
        }
    }
}
function editPost(postId, currentTitle, currentContent) {
    // Open the modal and prefill data
    document.getElementById('editModal').style.display = 'block';
    document.getElementById('editPostId').value = postId;
    document.getElementById('editTitle').value = currentTitle;
    document.getElementById('editContent').value = currentContent;
}

function closeModal() {
    // Close the modal
    document.getElementById('editModal').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    // Add form submission logic
    document.getElementById('editPostForm').addEventListener('submit', async function (e) {
        e.preventDefault();

        const postId = document.getElementById('editPostId').value;
        const title = document.getElementById('editTitle').value;
        const content = document.getElementById('editContent').value;

        try {
            const response = await fetch('api/edit_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ postId, title, content }),
            });

            const result = await response.json();
            if (result.success) {
                alert('Post updated successfully!');
                closeModal();
                fetchPosts(); // Refresh the posts
            } else {
                alert(result.error || 'Failed to update post.');
            }
        } catch (error) {
            console.error("Error editing post:", error);
        }
    });
});
function toggleCommentSection(postId) {
    const section = document.getElementById(`comment-section-${postId}`);
    if (section.style.display === 'none') {
        section.style.display = 'block';
        loadComments(postId);
    } else {
        section.style.display = 'none';
    }
}

async function addComment(postId) {
    const newComment = document.getElementById(`new-comment-${postId}`).value.trim();
    if (newComment) {
        try {
            const response = await fetch('api/comment_section.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ post_id: postId, comment: newComment }),
            });

            const result = await response.json();

            if (result.success) {
                document.getElementById(`new-comment-${postId}`).value = ''; // Clear the input field
                loadComments(postId); // Reload comments
            } else {
                console.error('Error:', result.error);
                alert('Failed to add comment.');
            }
        } catch (error) {
            console.error('Fetch error:', error);
        }
    } else {
        alert('Comment cannot be empty.');
    }
}

async function loadComments(postId) {
    try {
        const response = await fetch('api/comment_section.php?post_id=' + postId);
        const result = await response.json();

        const commentContainer = document.querySelector(`#comment-section-${postId} .comments`);
        commentContainer.innerHTML = ''; // Clear existing comments

        if (result.success && result.comments.length > 0) {
            result.comments.forEach(comment => {
                const commentDiv = document.createElement('div');
                commentDiv.className = 'comment';
                commentDiv.innerHTML = `
                    <p><strong>${comment.username}:</strong> ${comment.comment}</p>
                    <p><em>${new Date(comment.created_at).toLocaleString()}</em></p>
                `;
                commentContainer.appendChild(commentDiv);
            });
        } else {
            commentContainer.innerHTML = '<p>No comments yet.</p>';
        }
    } catch (error) {
        console.error('Fetch error:', error);
    }
}
</script>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="main-container">
        <h1>Welcome to Dalhousie Forum</h1>
        <?php if (isset($_SESSION['username'])): ?>
            <p>Hello and welcome, <strong><?= htmlspecialchars($_SESSION['username']); ?></strong>!</p>
        <?php endif; ?>
        <p>Below is the latest feed from the forum:</p>
        <div id="post-feed"></div>
    </div>

    <div id="editModal" class="modal">
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