<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
</head>
<body>
    <?php include 'templates/header.php'; ?>
    <div class="relative">
        <div class="grid-icosahedron"></div>
    </div>
<div class="ex-container">
    <div class="new-post-container">
        <h1>Create a New Post</h1>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form class="new-post-form" method="POST" action="new-post.php">
            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" placeholder="Enter your post title..." required>
            </div>

            <div class="form-group">
                <label for="content">Content:</label>
                <textarea id="content" name="content" placeholder="Write your post content here..." required></textarea>
            </div>

            <button class="submit-btn" type="submit">
                <span>Post</span>
                <i class="fas fa-paper-plane"></i>
                
            </button>
        </form>
        <button class="btn" type="submit">
        <a href="index.php">
                <span>Go Back to Forum Page</span>
                <i class="fas fa-arrow-right"></i>
                </a>
            </button>
            
    </div>
    </div>
    <?php include 'templates/footer.php'; ?>
    <script>
    document.querySelector('.new-post-form').addEventListener('submit', async (event) => {
        event.preventDefault(); // Prevent the default form submission
        
        const form = event.target;
        const formData = new FormData(form);

        try {
            const response = await fetch('api/posts.php', {
                method: 'POST',
                body: JSON.stringify({
                    title: formData.get('title'),
                    content: formData.get('content'),
                }),
                headers: {
                    'Content-Type': 'application/json',
                },
            });

            const result = await response.json();

            if (result.success) {
                // Redirect back to index.php with a success message
                window.location.href = 'index.php?message=Post added successfully!';
            } else {
                alert(result.error || 'Failed to add post. Please try again.');
            }
        } catch (error) {
            console.error('Error adding post:', error);
            alert('An error occurred. Please try again.');
        }
    });
</script>
</body>
</html>