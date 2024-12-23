<?php
include '../connection.php';

// Initialize an array to hold any errors
$errors = [];
$data = [];

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and sanitize inputs
    $username = trim(mysqli_real_escape_string($conn, $_POST['username']));
    $fname = trim(mysqli_real_escape_string($conn, $_POST['fname']));
    $lname = trim(mysqli_real_escape_string($conn, $_POST['lname']));
    $organization = mysqli_real_escape_string($conn, $_POST['organization']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $email = trim(mysqli_real_escape_string($conn, $_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($fname) || empty($lname) || empty($email) || empty($password) || empty($confirm_password)) {
        $errors[] = "All fields are required.";
    }

    // Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Check if password and confirm password match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check if username or email already exists
    $query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $result = mysqli_query($conn, $query);
    if (mysqli_num_rows($result) > 0) {
        $errors[] = "Username or email is already taken.";
    }

    // If no errors, proceed with insertion
    if (!empty($errors)) {
       $data['success'] = false;
        $data['errors'] = $errors; 
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Insert the user into the database
        $sql = "INSERT INTO users (username, first_name, last_name, organization_id, role, email, password) 
                VALUES ('$username', '$fname', '$lname', '$organization', '$role', '$email', '$hashed_password')";

        if (mysqli_query($conn, $sql)) {
            // Registration successful
            $data['success'] = true;
            $data['message'] = 'User added successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to add event to the database.'];
        }
    }
}

echo json_encode($data);
?>
