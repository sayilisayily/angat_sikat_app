<?php
// Include the database connection file
include 'connection.php';

$data = [];

if (isset($_POST['approval_id'])) {
    $approval_id = intval($_POST['approval_id']);
    
    // Update the status to 'archived' in the database
    $query = "UPDATE budget_approvals SET archived = 1 WHERE approval_id = $approval_id";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = "Budget approval archived successfully!";
    } else {
        $data['success'] = false;
        $data['message'] = "Error archiving budget approval: " . mysqli_error($conn);
    }
} else {
    $data['success'] = false;
    $data['message'] = "Invalid ID.";
}

// Return the response as JSON

echo json_encode($data);
?>
