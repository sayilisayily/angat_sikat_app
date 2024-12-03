<?php
// Start session
session_start();

// Include database connection
include 'connection.php';

// Initialize variables for error handling
$errors = [];

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize form inputs
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];

    // Check if email and password are provided
    if (empty($email) || empty($password)) {
        $errors[] = "Please enter both email and password.";
    } else {
        // Prepare query to check if the user exists
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($conn, $query);

        // Check if the user exists
        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {
                // Set session variables based on user data
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['organization_id'] = $user['organization_id'];

                // Redirect based on role
                if ($user['role'] === 'member') {
                    header("Location: dashboard/member_dashboard.php");
                } elseif ($user['role'] === 'officer') {
                    header("Location: ../dashboard/officer_dashboard.php");
                } elseif ($user['role'] === 'admin') {
                    header("Location: ../dashboard/admin_dashboard.php");
                } else {
                    $errors[] = "Unknown user role.";
                }
                exit();
            } else {
                $errors[] = "Incorrect password.";
            }
        } else {
            $errors[] = "User not found.";
        }
    }
}

// Display errors if any
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "<p style='color: red;'>$error</p>";
    }
}

// Close database connection
mysqli_close($conn);
?>