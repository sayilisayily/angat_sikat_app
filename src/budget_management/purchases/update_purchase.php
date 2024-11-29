<?php
// Include necessary files
include('../connection.php');
include('../../session_check.php'); 

$errors = [];
$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form inputs
    $title = mysqli_real_escape_string($conn, trim($_POST['title']));
    $purchase_id = mysqli_real_escape_string($conn, $_POST['edit_purchase_id']);
    
    if (empty($title)) {
        $errors['title'] = 'Purchase title is required.';
    } else {
        // Check for duplicate titles
        $check_query = "SELECT * FROM purchases WHERE title = '$title' AND purchase_id != '$purchase_id'";
        $result = mysqli_query($conn, $check_query);
        if (mysqli_num_rows($result) > 0) {
            $errors['title'] = 'A purchase with this title already exists.';
        }
    }

    // Proceed if no errors
    if (empty($errors)) {
        $update_query = "UPDATE purchases SET 
                            title = '$title'
                         WHERE purchase_id = '$purchase_id'";
        if (mysqli_query($conn, $update_query)) {
            $response['success'] = true;
            $response['message'] = 'Purchase updated successfully!';
        } else {
            $response['success'] = false;
            $errors['database'] = 'Failed to update the purchase in the database.';
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
