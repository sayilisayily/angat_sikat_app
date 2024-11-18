<?php
// Include database connection
include('../connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate fields
    if (empty($_POST['username'])) {
        $errors[] = 'Username is required.';
    } else {
        $username = mysqli_real_escape_string($conn, $_POST['username']);
        $user_id = mysqli_real_escape_string($conn, $_POST['user_id']); // Set $user_id for use
    }

    if (empty($_POST['first_name'])) {
        $errors[] = 'First name is required.';
    } else {
        $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    }

    if (empty($_POST['last_name'])) {
        $errors[] = 'Last name is required.';
    } else {
        $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    }

    if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email is required.';
    } else {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
    }

    if (empty($_POST['role'])) {
        $errors[] = 'Role is required.';
    } else {
        $role = mysqli_real_escape_string($conn, $_POST['role']);
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Prepare and execute the update query
        $query = "UPDATE users SET 
                    username = '$username', 
                    first_name = '$first_name', 
                    last_name = '$last_name', 
                    email = '$email', 
                    role = '$role' 
                  WHERE user_id = '$user_id'";

        if (mysqli_query($conn, $query)) {
            $data['success'] = true;
            $data['message'] = 'User updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update user in the database.'];
        }

        // If profile picture is uploaded, handle the update
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
            $file = $_FILES['profile_picture'];
            $uploadDirectory = 'uploads/'; // Adjust path as needed
            $uploadFile = $uploadDirectory . basename($file['name']);

            // Validate file upload
            $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png'];

            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                    // Update the profile picture in the database
                    $query = "UPDATE users SET profile_picture = '$uploadFile' WHERE user_id = '$user_id'";
                    if (mysqli_query($conn, $query)) {
                        $data['success'] = true;
                        $data['message'] = 'User updated successfully, with new profile picture!';
                    } else {
                        $data['success'] = false;
                        $data['errors'] = ['database' => 'Failed to update profile picture in the database.'];
                    }
                } else {
                    $data['success'] = false;
                    $data['errors'] = ['file_upload' => 'Failed to upload profile picture.'];
                }
            } else {
                $data['success'] = false;
                $data['errors'] = ['file_upload' => 'Invalid file type for profile picture.'];
            }
        }
    }
}

echo json_encode($data);
?>
