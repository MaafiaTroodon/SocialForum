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
</body>
</html>