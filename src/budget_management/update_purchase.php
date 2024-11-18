<?php
// Include database connection
include('connection.php');
include '../session_check.php'; 

// Set content type to JSON
header('Content-Type: application/json');

// Initialize an array to hold validation errors and response data
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate title
    if (empty($_POST['title'])) {
        $errors['title'] = 'Purchase title is required.';
    } else {
        $title = mysqli_real_escape_string($conn, $_POST['title']); // Correctly set $title here
        $purchase_id = mysqli_real_escape_string($conn, $_POST['purchase_id']); // Set $purchase_id for use

        // Check for duplicate purchase title
        $query = "SELECT * FROM purchases WHERE title = '$title' AND purchase_id != '$purchase_id'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            $errors['title'] = 'A purchase with this title already exists.';
        }
    }

    // Validate completion_status if necessary (assuming it’s required)
    if (!isset($_POST['completion_status'])) {
        $errors['completion_status'] = 'Completion status is required.';
    } else {
        $completion_status = (int)$_POST['completion_status'];
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Prepare organization_id from session
        $organization_id = $_SESSION['organization_id'];

        // Prepare and execute the update query
        $query = "UPDATE purchases SET 
                    title = '$title',
                    completion_status = '$completion_status'
                  WHERE purchase_id = '$purchase_id'";

        if (mysqli_query($conn, $query)) {
            $data['success'] = true;
            $data['message'] = 'Purchase updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update purchase in the database.'];
        }
    }
}

// Return JSON response
echo json_encode($data);
?>
