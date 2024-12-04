<?php
session_start(); // Start the session
error_log("Logout script initiated.");

// Destroy all session data
session_unset();
session_destroy();
error_log("Session destroyed.");

// Redirect to the login page
header("Location: ../includes/login.php"); // Ensure correct path
exit();
?>