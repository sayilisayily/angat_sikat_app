<?php
include '../connection.php';

$errors = [];
$data = [];

if (isset($_POST['expense_id'])) {
    $expense_id = intval($_POST['expense_id']);
    
    // Update the archived status of the event
    $query = "UPDATE expenses SET archived = 1 WHERE expense_id = $expense_id";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Expense archived successfully!';
    } else {
        $data['success'] = false;
        $data['message'] = 'Failed to archive expense.';
    }
} else {
    $data['success'] = false;
    $data['message'] = 'Invalid expense ID.';
}

echo json_encode($data);
?>
