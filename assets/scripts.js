import * as THREE from "https://cdn.skypack.dev/three@0.124.0";
import { OrbitControls } from "https://cdn.skypack.dev/three@0.124.0/examples/jsm/controls/OrbitControls";

// Three.js Implementation
class GridIcosahedron {
    constructor(container) {
        this.container = document.querySelector(container);
        this.init();
    }

    init() {
        this.scene = new THREE.Scene();
        this.camera = new THREE.PerspectiveCamera(75, window.innerWidth / window.innerHeight, 0.1, 1000);
        this.renderer = new THREE.WebGLRenderer({ alpha: true, antialias: true });
        this.renderer.setSize(window.innerWidth, window.innerHeight);
        this.container.appendChild(this.renderer.domElement);
        this.camera.position.z = 5;

        this.createIcosahedron();
        this.addLight();
        this.createControls();
        this.scrollEffect();
        this.animate();

        window.addEventListener("resize", this.onResize.bind(this));
    }

    createIcosahedron() {
        const geometry = new THREE.IcosahedronGeometry(1, 1);
        const material = new THREE.MeshBasicMaterial({ color: 0xffffff, wireframe: true });
        this.icosahedron = new THREE.Mesh(geometry, material);
        this.scene.add(this.icosahedron);
    }

    addLight() {
        const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
        this.scene.add(ambientLight);
    }

    createControls() {
        this.controls = new OrbitControls(this.camera, this.renderer.domElement);
        this.controls.enableDamping = true;
    }

    scrollEffect() {
        window.addEventListener("scroll", () => {
            const scrollY = window.scrollY || document.documentElement.scrollTop;
            const scale = 1 + scrollY / 1000;
            this.icosahedron.scale.set(scale, scale, scale);
        });
    }

    animate() {
        requestAnimationFrame(() => this.animate());
        this.icosahedron.rotation.x += 0.005;
        this.icosahedron.rotation.y += 0.005;
        this.controls.update();
        this.renderer.render(this.scene, this.camera);
    }

    onResize() {
        this.camera.aspect = window.innerWidth / window.innerHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize(window.innerWidth, window.innerHeight);
    }
}

async function fetchPosts() {
    try {
        const response = await fetch("api/posts.php");
        const posts = await response.json();
        const postFeed = document.getElementById("post-feed");

        postFeed.innerHTML = "";
        if (!posts.length) {
            postFeed.innerHTML = "<p>No posts available.</p>";
            return;
        }

        posts.forEach((post) => {
            // Skip posts without valid data
            if (!post.id || !post.title || !post.content) {
                console.warn("Skipping invalid post:", post);
                return;
            }

            const postDiv = document.createElement("div");
            postDiv.className = "post";
            postDiv.dataset.id = post.id;

            postDiv.innerHTML = `
                <div class="post-content">
                    <h3>${post.title}</h3>
                    <p>${post.content}</p>
                    <p><strong>Author:</strong> ${post.username}</p>
                    <p><em>Posted on:</em> ${new Date(post.created_at).toLocaleString()}</p>
                </div>
                <div class="vote-container">
                    <button class="upvote-button" onclick="toggleVote(${post.id}, 'upvote')">▲</button>
                    <div class="vote-count">${post.vote_count}</div>
                    <button class="downvote-button" onclick="toggleVote(${post.id}, 'downvote')">▼</button>
                </div>
                <div class="post-actions">
                    <a class="btn" onclick="editPost(${post.id}, '${post.title}', '${post.content}')"><span>Edit</span><em></em></a>
                    <a class="btn" onclick="deletePost(${post.id})"><span>Delete</span><em></em></a>
                    <a class="btn" onclick="toggleCommentSection(${post.id})"><span>Comments</span><em></em></a>
                </div>
                <div id="comment-section-${post.id}" class="comment-section" style="display: none;">
                    <textarea id="new-comment-${post.id}" placeholder="Add a comment"></textarea>
                    <a class="btn add-comment-button" onclick="addComment(${post.id})"><span>Post</span><em></em></a>
                    <div class="comments"></div>
                </div>
            `;
            postFeed.appendChild(postDiv);
        });
    } catch (error) {
        console.error("Error fetching posts:", error);
    }
}

// Attach the function to the global window object
window.toggleVote = async function(postId, action) {
    try {
        const response = await fetch("api/upvotes.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ postId, action }),
        });
        const result = await response.json();
        if (result.success) {
            // Update the vote count for the specific post
            const postDiv = document.querySelector(`.post[data-id="${postId}"]`);
            const voteCountDiv = postDiv.querySelector(".vote-count");
            voteCountDiv.textContent = result.newVoteCount;
        } else {
            console.error(result.error);
        }
    } catch (error) {
        console.error("Error toggling vote:", error);
    }
};

document.getElementById("editPostForm").addEventListener("submit", async function (event) {
    event.preventDefault();

    const postId = document.getElementById("editPostId").value;
    const title = document.getElementById("editTitle").value;
    const content = document.getElementById("editContent").value;

    console.log("Submitting edit:", { postId, title, content });

    try {
        const response = await fetch("api/edit_post.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ postId, title, content }),
        });

        const result = await response.json();
        console.log("Edit Response:", result);

        if (result.success) {
            alert("Post updated successfully!");
            closeModal();
            fetchPosts(); // Refresh the posts
        } else {
            alert(result.error || "Failed to update post.");
        }
    } catch (error) {
        console.error("Error editing post:", error);
    }
});

// Toggle comment section visibility
window.toggleCommentSection = async function (postId) {
    const commentSection = document.getElementById(`comment-section-${postId}`);
    const commentsContainer = commentSection.querySelector(".comments");

    if (commentSection.style.display === "none" || commentSection.style.display === "") {
        // Fetch and display comments
        try {
            const response = await fetch(`api/comment_section.php?post_id=${postId}`);
            const result = await response.json();

            if (result.success) {
                commentsContainer.innerHTML = ""; // Clear previous comments
                result.comments.forEach(comment => {
                    const commentDiv = document.createElement("div");
                    commentDiv.className = "comment";
                    commentDiv.innerHTML = `
                        <p><strong>${comment.username}:</strong> ${comment.comment}</p>
                        <p><small>${new Date(comment.created_at).toLocaleString()}</small></p>
                    `;
                    commentsContainer.appendChild(commentDiv);
                });
            } else {
                commentsContainer.innerHTML = "<p>No comments yet.</p>";
            }
        } catch (error) {
            console.error("Error fetching comments:", error);
        }

        commentSection.style.display = "block";
    } else {
        commentSection.style.display = "none";
    }
};

window.editPost = function (postId, currentTitle, currentContent) {
    console.log("Opening edit modal for Post ID:", postId);

    document.getElementById("editPostId").value = postId;
    document.getElementById("editTitle").value = currentTitle;
    document.getElementById("editContent").value = currentContent;

    document.getElementById("editModal").style.display = "block";
};

window.closeModal = function () {
    document.getElementById("editModal").style.display = "none";
};

document.getElementById("editPostForm").addEventListener("submit", async function (event) {
    event.preventDefault();

    const postId = document.getElementById("editPostId").value;
    const title = document.getElementById("editTitle").value;
    const content = document.getElementById("editContent").value;

    try {
        const response = await fetch("api/edit_post.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ postId, title, content }),
        });

        const result = await response.json();

        if (result.success) {
            addNotification('success', 'Post edited successfully!');
            closeModal();
            fetchPosts(); // Refresh the posts
        } else {
            addNotification('danger', result.error || 'Failed to update the post.');
        }
    } catch (error) {
        addNotification('danger', 'An error occurred while editing the post.');
        console.error("Error editing post:", error);
    }
});
window.deletePost = async function (postId) {
    if (confirm("Are you sure you want to delete this post?")) {
        try {
            const response = await fetch("api/delete_post.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ postId }),
            });

            const result = await response.json();

            if (result.success) {
                addNotification("success", "Post deleted successfully!");
                fetchPosts(); // Refresh posts
            } else if (result.error === "Unauthorized action. This post does not belong to you.") {
                addNotification("danger", "Unauthorized action. This post does not belong to you.");
            } else {
                addNotification("danger", result.error || "Failed to delete post.");
            }
        } catch (error) {
            addNotification("danger", "An error occurred while deleting the post.");
            console.error("Error deleting post:", error);
        }
    }
};

// Add a new comment
window.addComment = async function (postId) {
    const commentInput = document.getElementById(`new-comment-${postId}`);
    const comment = commentInput.value.trim();

    if (!comment) return;

    try {
        const response = await fetch("api/comment_section.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ post_id: postId, comment }),
        });

        const result = await response.json();

        if (result.success) {
            alert("Comment added!");
            commentInput.value = ""; // Clear input
            toggleCommentSection(postId); // Reload comments
        } else {
            alert(result.error || "Failed to add comment.");
        }
    } catch (error) {
        console.error("Error adding comment:", error);
    }
};

document.addEventListener("DOMContentLoaded", () => {
    new GridIcosahedron(".grid-icosahedron");
    fetchPosts();
});

async function fetchMessages() {
    try {
        const response = await fetch("api/messages.php"); // Ensure the API path is correct
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const messages = await response.json();

        if (messages.error) {
            addNotification("danger", messages.error);
            console.error(messages.error);
            return;
        }

        const receivedMessagesTable = document.getElementById("received-messages");
        const sentMessagesTable = document.getElementById("sent-messages");

        // Clear previous data
        receivedMessagesTable.innerHTML = "";
        sentMessagesTable.innerHTML = "";

        messages.forEach((message) => {
            const row = document.createElement("tr");

            if (message.receiver_id === parseInt(user_id)) {
                // Message received by the logged-in user
                row.innerHTML = `
                    <td>${message.sender_name}</td>
                    <td>${message.content}</td>
                    <td>${new Date(message.timestamp).toLocaleString()}</td>
                `;
                receivedMessagesTable.appendChild(row);
            } else if (message.sender_id === parseInt(user_id)) {
                // Message sent by the logged-in user
                row.innerHTML = `
                    <td>${message.receiver_name}</td>
                    <td>${message.content}</td>
                    <td>${new Date(message.timestamp).toLocaleString()}</td>
                `;
                sentMessagesTable.appendChild(row);
            }
        });

        addNotification("success", "Messages fetched successfully!");
    } catch (error) {
        addNotification("danger", "Failed to fetch messages. Please try again.");
        console.error("Error fetching messages:", error);
    }
}

newMessageForm.addEventListener('submit', async (e) => {
    e.preventDefault();

    const recipient = document.getElementById('recipient').value.trim();
    const message = document.getElementById('message').value.trim();

    if (!recipient || !message) {
        addNotification('danger', 'Please fill in both fields.');
        return;
    }

    try {
        const response = await fetch('api/messages.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ recipient, message }),
        });

        const result = await response.json();

        if (result.success) {
            addNotification('success', 'Message sent successfully!');
            toggleMessageForm(); // Close the form
            fetchMessages(); // Refresh messages
        } else {
            addNotification('danger', result.error || 'Failed to send the message.');
        }
    } catch (error) {
        addNotification('danger', 'An error occurred. Please try again.');
        console.error('Error sending message:', error);
    }
});

function addNotification(type, message) {
    const notificationContainer = document.getElementById('notifications');
    if (!notificationContainer) return;

    const typeClasses = {
        success: 'alert-success',
        info: 'alert-info',
        warning: 'alert-warning',
        danger: 'alert-danger'
    };

    const notification = document.createElement('div');
    notification.className = `alert ${typeClasses[type]} alert-dismissible fade show`;
    notification.style.marginBottom = '10px';
    notification.innerHTML = `
        <strong>${capitalize(type)}!</strong> ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;

    notificationContainer.appendChild(notification);

    // Automatically remove notification after 5 seconds
    setTimeout(() => {
        if (notificationContainer.contains(notification)) {
            notificationContainer.removeChild(notification);
        }
    }, 5000);
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}


