<?php
// Include necessary files
include('connection.php');
include('../session_check.php'); 

$errors = [];
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form inputs
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $maintenance_id = mysqli_real_escape_string($conn, $_POST['edit_maintenance_id']);
    
    if (empty($title)) {
        $errors['title'] = 'Maintenance title is required.';
    } else {
        // Check for duplicate titles
        $check_query = "SELECT * FROM maintenance WHERE title = '$title' AND maintenance_id != '$maintenance_id'";
        $result = mysqli_query($conn, $check_query);
        if (mysqli_num_rows($result) > 0) {
            $errors['title'] = 'A maintenance with this title already exists.';
        }
    }

    // Proceed if no errors
    if (empty($errors)) {
        $update_query = "UPDATE maintenance SET 
                            title = '$title'
                         WHERE maintenance_id = '$maintenance_id'";
        if (mysqli_query($conn, $update_query)) {
            $response['success'] = true;
            $response['message'] = 'Maintenance updated successfully!';
        } else {
            $response['success'] = false;
            $errors['database'] = 'Failed to update the maintenance in the database.';
        }
    } else {
        $response['errors'] = $errors;
    }
} else {
    $response['errors'] = ['general' => 'Invalid request method.'];
}

$response['errors'] = $errors ?? [];
echo json_encode($response);

?>
