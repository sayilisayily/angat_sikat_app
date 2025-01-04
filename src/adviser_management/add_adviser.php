<?php
include '../connection.php';

// Initialize an array to hold any errors
$errors = [];
$data = [];

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data and sanitize inputs
    $first_name = trim(mysqli_real_escape_string($conn, $_POST['first_name']));
    $last_name = trim(mysqli_real_escape_string($conn, $_POST['last_name']));
    $organization_id = mysqli_real_escape_string($conn, $_POST['organization_id']);
    $position = trim(mysqli_real_escape_string($conn, $_POST['position']));

    // Validation
    if (empty($first_name) || empty($last_name) || empty($organization_id) || empty($position)) {
        $errors[] = "All fields are required.";
    }

    // Check if organization exists
    $organization_check_query = "SELECT * FROM organizations WHERE organization_id = '$organization_id'";
    $organization_check_result = mysqli_query($conn, $organization_check_query);
    if (mysqli_num_rows($organization_check_result) == 0) {
        $errors[] = "Selected organization does not exist.";
    }

    // Handle the picture upload
    if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_info = pathinfo($_FILES['picture']['name']);
        $file_extension = strtolower($file_info['extension']);
        $picture_name = basename($_FILES['picture']['name']);
        $target_dir = "uploads/";

        // Validate the file extension
        if (!in_array($file_extension, $allowed_extensions)) {
            $errors[] = "Only image files (JPG, JPEG, PNG, GIF) are allowed.";
        }

        // Set the target file path
        $target_file = $target_dir . $picture_name;

        // Create the target directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Move the file to the uploads directory
        if (empty($errors) && !move_uploaded_file($_FILES['picture']['tmp_name'], $target_file)) {
            $errors[] = "Failed to upload the picture.";
        }
    } else {
        $picture_name = null;  // If no picture is uploaded, set it to null
    }

    // If no errors, proceed with insertion
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Insert the adviser into the database
        $sql = "INSERT INTO advisers (first_name, last_name, organization_id, position, picture) 
                VALUES ('$first_name', '$last_name', '$organization_id', '$position', '$picture_name')";

        if (mysqli_query($conn, $sql)) {
            // Adviser added successfully
            $data['success'] = true;
            $data['message'] = 'Adviser added successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to add adviser to the database.'];
        }
    }
}

echo json_encode($data);
?>
