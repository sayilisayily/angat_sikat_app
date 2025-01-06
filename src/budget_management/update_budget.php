<?php
include 'connection.php';

$errors = [];
$data = [];

// Validate organization ID
if (!isset($_POST['edit_organization_id']) || !is_numeric($_POST['edit_organization_id'])) {
    $errors['organization_id'] = 'Invalid organization ID.';
}

// Validate allocation ID
if (!isset($_POST['edit_allocation_id']) || !is_numeric($_POST['edit_allocation_id'])) {
    $errors['allocation_id'] = 'Invalid allocation ID.';
}

// Validate Add and Subtract inputs
$add_budget = isset($_POST['add_budget']) ? (float)$_POST['add_budget'] : 0;
$subtract_budget = isset($_POST['subtract_budget']) ? (float)$_POST['subtract_budget'] : 0;

// Ensure at least one field (add or subtract) is provided
if ($add_budget === 0 && $subtract_budget === 0) {
    $errors['budget'] = 'Please enter an amount to add or subtract.';
}

// If there are errors, return early
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
    echo json_encode($data);
    exit;
}

// Sanitize and assign variables
$organization_id = (int)$_POST['edit_organization_id'];
$allocation_id = (int)$_POST['edit_allocation_id']; // Assign allocation_id properly

// Fetch the balance for the organization
$balance_query = "SELECT balance FROM organizations WHERE organization_id = ?";
$balance_stmt = $conn->prepare($balance_query);

if ($balance_stmt) {   
    $balance_stmt->bind_param('i', $organization_id);
    if ($balance_stmt->execute()) {
        $balance_stmt->bind_result($balance);
        $balance_stmt->fetch();
        $balance_stmt->close();

        if (!$balance) {
            $errors['balance'] = 'Organization balance not found.';
        }
    } else {
        $errors['sql'] = 'Error fetching balance: ' . $balance_stmt->error;
    }
} else {
    $errors['sql'] = 'Error preparing balance query: ' . $conn->error;
}

// If there were errors, return early
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
    echo json_encode($data);
    exit;
}

// Calculate the total allocated budget for the organization, excluding the current allocation (if updating)
$sum_query = "SELECT SUM(allocated_budget) as total_allocated FROM budget_allocation WHERE organization_id = ? AND allocation_id != ?";
$sum_stmt = $conn->prepare($sum_query);
$sum_stmt->bind_param('ii', $organization_id, $allocation_id);
$sum_stmt->execute();
$sum_stmt->bind_result($total_allocated);
$sum_stmt->fetch();
$sum_stmt->close();

// Calculate the current allocated budget for the organization
$budget_query = "SELECT allocated_budget FROM budget_allocation WHERE organization_id = ? AND allocation_id = ?";
$budget_stmt = $conn->prepare($budget_query);
$budget_stmt->bind_param('ii', $organization_id, $allocation_id);
$budget_stmt->execute();
$budget_stmt->bind_result($current_allocated_budget);
$budget_stmt->fetch();
$budget_stmt->close();

// Calculate the new budget value
$new_budget = $current_allocated_budget + $add_budget - $subtract_budget;

// Add the new or updated allocation to the total
$total_allocated += $new_budget;

// Validate if the total allocated budget exceeds the balance
if ($total_allocated > $balance) {
    $errors['allocated_budget'] = 'Total allocated budget exceeds the balance.';
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Proceed with the update if validation is successful
    $update_query = "UPDATE budget_allocation SET allocated_budget = ? WHERE allocation_id = ?";
    $update_stmt = $conn->prepare($update_query);
    if ($update_stmt) {
        $update_stmt->bind_param('di', $new_budget, $allocation_id);

        if ($update_stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'Budget updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update budget: ' . $update_stmt->error];
        }

        $update_stmt->close();
    } else {
        $data['success'] = false;
        $data['errors'] = ['sql' => 'Error preparing update query: ' . $conn->error];
    }
}

echo json_encode($data);
?>