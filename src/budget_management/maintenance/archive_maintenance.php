<?php
include 'connection.php';

$data = [];

if (isset($_POST['maintenance_id'])) {
    $maintenance_id = intval($_POST['maintenance_id']);
    
    // Update the archived status of the purchase
    $query = "UPDATE maintenance SET archived = 1 WHERE maintenance_id = $maintenance_id";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Purchase archived successfully!';
    } else {
        $data['success'] = false;
        $data['message'] = 'Failed to archive purchase.';
    }
} else {
    $data['success'] = false;
    $data['message'] = 'Invalid purchase ID.';
}

echo json_encode($data);
?>
