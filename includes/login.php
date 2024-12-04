<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php'; // Includes the MySQLi connection

// Redirect to index.php if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Initialize error message
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    try {
        // Check if the user exists in the database
        $stmt = $mysqli->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $password === $user['password']) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['notification'] = ['type' => 'success', 'message' => 'Welcome back, ' . htmlspecialchars($username) . '!'];
            header("Location: ../index.php");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
        $stmt->close();
    } catch (Exception $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dalhousie Forum</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/styles.css"> <!-- Custom CSS -->
</head>
<body>
    <!-- Three.js Background -->
    <div class="relative">
        <div class="grid-icosahedron"></div>
    </div>

    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="../index.php">
                    <img src="../img/logo-img.jpeg" alt="Dalhousie Forum Logo" class="d-inline-block align-text-top" style="height: 50px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item"><a class="nav-link" href="../includes/login.php">Login</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Notifications -->
    <section id="notifications" style="position: fixed; top: 10px; right: 10px; z-index: 1000; width: 300px;">
        <!-- Dynamic Notifications will be added here -->
    </section>

    <div class="relative">
    <div class="grid-icosahedron"></div>
</div>

<div class="container">
    <div class="login-container">
        <h1>Login to Dalhousie Forum</h1>
        <form action="login.php" method="POST">
            <input type="text" id="username" name="username" placeholder="Username" required>
            <input type="password" id="password" name="password" placeholder="Password" required>
            
            <button type="submit" class="btn btn-success"><span>Login</span><em></em></button>
        </form>
    </div>

    <div class="guest-container">
        <a href="../index.php">Continue as Guest</a>
    </div>
</div>

    <?php include '../templates/footer.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/scripts.js" type="module"></script> <!-- Three.js Script -->
</body>
</html>