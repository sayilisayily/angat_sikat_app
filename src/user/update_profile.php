<?php
include 'connection.php';

session_start();
$user_id = $_SESSION['user_id']; // Assuming user ID is stored in session after login

$errors = [];
$data = [];

// Check if form data is set and validate inputs
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $profile_picture = '';

    // Validate first name
    if (empty($first_name)) {
        $errors['first_name'] = "First name is required.";
    }

    // Validate last name
    if (empty($last_name)) {
        $errors['last_name'] = "Last name is required.";
    }

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Valid email is required.";
    }

    // Validate password and confirm password
    if ($password !== $confirm_password) {
        $errors['password'] = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hash the password
    }

    // Handle profile picture upload if a file was uploaded
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_tmp = $_FILES['profile_picture']['tmp_name'];
        $file_name = basename($_FILES['profile_picture']['name']);
        $file_path = $upload_dir . $user_id . "_" . $file_name;

        if (move_uploaded_file($file_tmp, $file_path)) {
            $profile_picture = $file_path;
        } else {
            $errors['profile_picture'] = "Failed to upload profile picture.";
        }
    }

    // Update profile if no errors
    if (empty($errors)) {
        $query = "UPDATE users SET first_name = ?, last_name = ?, email = ?, password = ?";
        $params = [$first_name, $last_name, $email, $hashed_password];

        if (!empty($profile_picture)) {
            $query .= ", profile_picture = ?";
            $params[] = $profile_picture;
        }

        $query .= " WHERE user_id = ?";
        $params[] = $user_id;

        $stmt = $conn->prepare($query);
        $stmt->bind_param(str_repeat("s", count($params) - 1) . "i", ...$params);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = "Profile updated successfully.";
        } else {
            $data['success'] = false;
            $data['errors'] = ["database" => "Failed to update profile."];
        }

        $stmt->close();
    } else {
        $data['success'] = false;
        $data['errors'] = $errors;
    }
}

echo json_encode($data);
?>
