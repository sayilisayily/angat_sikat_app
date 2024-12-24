<?php
include '../connection.php';

$errors = [];
$data = [];

if (isset($_POST['income_id'])) {
    $income_id = intval($_POST['income_id']);
    
    // Update the archived status of the event
    $query = "UPDATE income SET archived = 1 WHERE income_id = $income_id";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Income archived successfully!';
    } else {
        $data['success'] = false;
        $data['message'] = 'Failed to archive income.';
    }
} else {
    $data['success'] = false;
    $data['message'] = 'Invalid income ID.';
}

echo json_encode($data);
?>
