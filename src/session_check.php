<?php
// Start the session
session_start();

// Check if the user is logged in by verifying session variables
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if user is not logged in
    header("Location: user/login.html");
    exit();
}

// Retrieve user details from session variables
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role']; // For roles like 'admin', 'officer', etc.
$organization_id = $_SESSION['organization_id']; // The organization the user belongs to
?>