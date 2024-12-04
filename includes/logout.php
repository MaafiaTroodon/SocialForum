<?php
// Code re-used from Assignment 3 (CSCI 2170) for implementing the user logout functionality.
// The original code was modified to manage session destruction and redirection for Assignment 4.
session_start(); // Start the session
error_log("Logout script initiated.");

// Destroy all session data
session_unset();
session_destroy();
error_log("Session destroyed.");

// Redirect to the login page
header("Location: ../includes/login.php");
exit();
?>