<?php
include '../connection.php';

$data = [];

if (isset($_POST['purchase_id'])) {
    $purchase_id = intval($_POST['purchase_id']);
    
    // Update the archived status of the purchase
    $query = "UPDATE purchases SET archived = 1 WHERE purchase_id = $purchase_id";
    
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
