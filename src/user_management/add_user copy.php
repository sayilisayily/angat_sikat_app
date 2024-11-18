<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get inputs from the form
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $organization_id = $_POST['organization_id']; // This should be passed from the form or fetched from the session
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Handle file upload for the profile picture (optional)
    $profile_picture = null; // Default value for profile picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $file = $_FILES['profile_picture'];
        $uploadDirectory = 'uploads/'; // Adjust path as needed
        $uploadFile = $uploadDirectory . basename($file['name']);
        
        // Validate file upload
        $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                $profile_picture = $uploadFile; // Store file path for the database
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload profile picture.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type for profile picture.']);
            exit;
        }
    }

    // Validate inputs
    $errors = [];
    if (empty($username)) {
        $errors[] = "Username is required.";
    }
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    }
    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (empty($role)) {
        $errors[] = "Role is required.";
    }
    if (empty($password)) {
        $errors[] = "Password is required.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Password and Confirm Password do not match.";
    }

    // If no errors, hash the password and insert the user entry
    if (empty($errors)) {
        // Hash the password before storing
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert the new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, first_name, last_name, email, role, password, profile_picture, organization_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssi", $username, $first_name, $last_name, $email, $role, $hashedPassword, $profile_picture, $organization_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add user.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
}

$conn->close();
?>
