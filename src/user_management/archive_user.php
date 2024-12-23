<?php
include '../connection.php';

$data = [];

// Check if user_id is set
if (isset($_POST['user_id'])) {
    // Sanitize the input and cast to an integer
    $user_id = intval($_POST['user_id']);
    
    // Check if user_id is a valid integer greater than 0
    if ($user_id > 0) {
        // Use a prepared statement to prevent SQL injection
        $query = "UPDATE users SET archived = 1 WHERE user_id = ?"; // Assuming 'id' is the correct column
        if ($stmt = mysqli_prepare($conn, $query)) {
            // Bind the parameter to the prepared statement
            mysqli_stmt_bind_param($stmt, 'i', $user_id);
            
            // Execute the statement
            if (mysqli_stmt_execute($stmt)) {
                $data['success'] = true;
                $data['message'] = 'User archived successfully!';
            } else {
                $data['success'] = false;
                $data['message'] = 'Failed to archive user: ' . mysqli_error($conn);
            }
            mysqli_stmt_close($stmt); // Close the prepared statement
        } else {
            $data['success'] = false;
            $data['message'] = 'Error preparing the query: ' . mysqli_error($conn);
        }
    } else {
        $data['success'] = false;
        $data['message'] = 'Invalid user ID.';
    }
} else {
    $data['success'] = false;
    $data['message'] = 'User ID not provided.';
}

// Return the response as JSON
echo json_encode($data);
?>
