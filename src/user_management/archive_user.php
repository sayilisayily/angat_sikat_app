<?php
include '../connection.php';

$data = [];

if (isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);
    
    // Update the archived status of the user
    $query = "UPDATE users SET archived = 1 WHERE user_id = $user_id";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'User archived successfully!';
    } else {
        $data['success'] = false;
        $data['message'] = 'Failed to archive user.';
    }
} else {
    $data['success'] = false;
    $data['message'] = 'Invalid user ID.';
}

echo json_encode($data);
?>
