<?php

include 'connection.php';
include '../session_check.php';  // Assuming session_check.php sets the organization_id in the session

$errors = [];
$data = [];

// Validate the organization ID (using session)
if (!isset($_SESSION['organization_id']) || !is_numeric($_SESSION['organization_id'])) {
    $errors['organization'] = 'Invalid organization ID.';
} else {
    $organization_id = $_SESSION['organization_id'];
}

// Validate the category
if (empty($_POST['category'])) {
    $errors['category'] = 'Budget category is required.';
} else {
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // Check for duplicate category in the budget allocation table
    $duplicate_check_query = "SELECT COUNT(*) as count FROM budget_allocation 
                              WHERE organization_id = ? AND category = ?";
    $stmt = $conn->prepare($duplicate_check_query);
    $stmt->bind_param('is', $organization_id, $category);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        $errors['category'] = 'This category already exists for the selected organization.';
    }
}

// Validate the allocated budget
if (empty($_POST['allocated_budget']) || !is_numeric($_POST['allocated_budget']) || $_POST['allocated_budget'] <= 0) {
    $errors['allocated_budget'] = 'Allocated budget must be a positive number.';
} else {
    $allocated_budget = (float)$_POST['allocated_budget'];
}

// Check for errors
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Fetch current total allocated budget for the organization
    $query = "SELECT SUM(allocated_budget) as total_allocated, beginning_balance 
              FROM organizations 
              LEFT JOIN budget_allocation ON organizations.organization_id = budget_allocation.organization_id
              WHERE organizations.organization_id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $organization_id);
    $stmt->execute();
    $stmt->bind_result($current_total_allocated, $beginning_balance);
    $stmt->fetch();
    $stmt->close();

    $current_total_allocated = $current_total_allocated ?: 0;  // Default to 0 if no budgets exist

    // Ensure the new budget won't exceed the beginning balance
    if (($current_total_allocated + $allocated_budget) > $beginning_balance) {
        $errors['allocated_budget'] = 'Total allocated budget exceeds the beginning balance.';
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Calculate total spent for the category from the expenses table
        $query = "SELECT SUM(amount) as total_spent FROM expenses 
                  WHERE organization_id = ? AND category = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('is', $organization_id, $category);
        $stmt->execute();
        $stmt->bind_result($total_spent);
        $stmt->fetch();
        $stmt->close();

        $total_spent = $total_spent ?: 0;  // Default to 0 if no expenses exist for the category

        // Insert new budget record
        $query = "INSERT INTO budget_allocation (organization_id, category, allocated_budget, total_spent) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('isdd', $organization_id, $category, $allocated_budget, $total_spent);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'Budget added successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to add budget to the database.'];
        }

        $stmt->close();
    }
}

echo json_encode($data);

?>
