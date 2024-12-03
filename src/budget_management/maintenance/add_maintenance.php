<?php

include '../connection.php';
include '../../session_check.php';  // Ensure the organization ID is in the session

$errors = [];
$data = [];

// Validate the maintenance and other expenses title (make sure it's not empty or a duplicate)
if (empty($_POST['title'])) {
    $errors['title'] = 'Maintenance and other expenses title is required.';
} else {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    
    // Check if a maintenance and other expenses with the same title already exists for this organization
    $query = "SELECT * FROM maintenance WHERE title = '$title' AND organization_id = {$_SESSION['organization_id']}";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        $errors['database_check'] = 'Error checking for duplicate title: ' . mysqli_error($conn);
    } elseif (mysqli_num_rows($result) > 0) {
        $errors['title'] = 'A maintenance and other expenses with this title already exists.';
    }
}

// Validate that plan_id is provided and not empty
if (empty($_POST['plan_id'])) {
    $errors['plan_id'] = 'Plan ID is required.';
} else {
    $plan_id = mysqli_real_escape_string($conn, $_POST['plan_id']);
}

// If there are errors, return them in the response
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Prepare variables for insertion
    $organization_id = $_SESSION['organization_id'];
    
    // Insert the maintenance and other expenses if no validation errors
    $query = "INSERT INTO maintenance (title, maintenance_status, completion_status, organization_id, plan_id) 
              VALUES ('$title', 'Pending', 0, '$organization_id', '$plan_id')";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Maintenance and other expenses added successfully!';
    } else {
        $data['success'] = false;
        $data['errors'] = [
            'database' => 'Failed to add maintenance and other expenses to the database: ' . mysqli_error($conn)
        ];
    }
}

// Output the JSON-encoded response
echo json_encode($data);

?>
