<?php
include 'connection.php';

$errors = [];
$data = [];

if (empty($_POST['allocated_budget'])) {
    $errors['allocated_budget'] = 'Allocated budget is required.';
}

if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    $allocated_budget = (float)$_POST['allocated_budget'];
    $allocation_id = (int)$_POST['allocation_id'];
    $organization_id = (int)$_POST['organization_id']; // Assuming this is passed in

    // Fetch the beginning balance for the organization
    $balance_query = "SELECT beginning_balance FROM organizations WHERE organization_id = ?";
    $balance_stmt = $conn->prepare($balance_query);
    $balance_stmt->bind_param('i', $organization_id);
    $balance_stmt->execute();
    $balance_stmt->bind_result($beginning_balance);
    $balance_stmt->fetch();
    $balance_stmt->close();

    // Calculate the total allocated budget for the organization, excluding the current allocation (if updating)
    $sum_query = "SELECT SUM(allocated_budget) as total_allocated FROM budget_allocation WHERE organization_id = ? AND allocation_id != ?";
    $sum_stmt = $conn->prepare($sum_query);
    $sum_stmt->bind_param('ii', $organization_id, $allocation_id);
    $sum_stmt->execute();
    $sum_stmt->bind_result($total_allocated);
    $sum_stmt->fetch();
    $sum_stmt->close();

    // Add the new or updated allocation to the total
    $total_allocated += $allocated_budget;

    // Validate if the total allocated budget exceeds the beginning balance
    if ($total_allocated > $beginning_balance) {
        $errors['allocated_budget'] = 'Total allocated budget exceeds the beginning balance.';
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Proceed with the update if validation is successful
        $query = "UPDATE budget_allocation SET allocated_budget = ? WHERE allocation_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('di', $allocated_budget, $allocation_id);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'Budget updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update budget.'];
        }

        $stmt->close();
    }
}

echo json_encode($data);
?>
