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

        if ($user) {
            // Log fetched user details for debugging
            error_log("User fetched: " . print_r($user, true));
            error_log("Password entered: $password");
            error_log("Password from DB: " . $user['password']);
            
            // Direct password comparison (no hashing)
            if ($password === $user['password']) {
                error_log("Password matched for user: $username");
                
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: ../index.php");
                exit();
            } else {
                error_log("Password mismatch for user: $username");
                $error = "Invalid username or password.";
            }
        } else {
            error_log("User not found: $username");
            $error = "Invalid username or password.";
        }
        // Close the statement
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
    <link rel="stylesheet" href="../assets/styles.css"> <!-- Adjusted path to styles.css -->
</head>
<body>
    <div class="login-container">
        <h1>Login to Dalhousie Forum</h1>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>
            <br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="../register.php">Register here</a>.</p> <!-- Adjusted path to register.php -->
    </div>
</body>
</html>