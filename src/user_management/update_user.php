<?php
include '../connection.php';

// Initialize an array to hold any errors
$errors = [];
$data = [];

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and sanitize inputs
    $user_id = intval($_POST['user_id']);
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $first_name = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $last_name = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $organization = intval($_POST['organization']);
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];

    // Validation
    if (empty($username) || empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        $errors[] = "All fields are required.";
    }

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if username or email already exists (excluding the current user)
    $query = "SELECT * FROM users WHERE (username = ? OR email = ?) AND user_id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $username, $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $errors[] = "Username or email is already taken.";
    }

    // Verify the provided password matches the user's current password
    $password_query = "SELECT password FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($password_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $errors[] = "User not found.";
    } else {
        $user = $result->fetch_assoc();
        if (!password_verify($password, $user['password'])) {
            $errors[] = "Incorrect password.";
        }
    }

    // If no errors, proceed with update
    if (empty($errors)) {
        // Update the user in the database (password remains unchanged)
        $update_query = "UPDATE users 
                         SET username = ?, first_name = ?, last_name = ?, organization_id = ?, email = ? 
                         WHERE user_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("sssisi", $username, $first_name, $last_name, $organization, $email, $user_id);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'User updated successfully!';
        } else {
            $errors[] = "Failed to update the user. Please try again.";
        }
    }

    // Handle errors
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    }
}

echo json_encode($data);
?>
