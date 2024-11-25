<?php

include 'connection.php';
include '../session_check.php'; // Assuming session_check.php sets the organization_id in the session

$errors = [];
$data = [];

// Validate the plan title
if (empty($_POST['title'])) {
    $errors['title'] = 'Plan title is required.';
} else {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    
    // Check if a plan with the same title already exists for the organization
    $query = "SELECT * FROM financial_plan WHERE title = '$title' AND organization_id = {$_SESSION['organization_id']}";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $errors['title'] = 'A plan with this title already exists.';
    }
}

// Validate type (Income or Expense)
if (empty($_POST['type']) || !in_array($_POST['type'], ['Income', 'Expense'])) {
    $errors['type'] = 'Valid plan type (Income or Expense) is required.';
}

// Validate category for expense plans
if ($_POST['type'] === 'Expense') {
    if (empty($_POST['category'])) {
        $errors['category'] = 'Category is required for expense plans.';
    } else {
        $category = mysqli_real_escape_string($conn, $_POST['category']);
    }
} else {
    $category = ''; // Default empty for non-expense plans
}

// Validate date (required for Activities or Income plans)
if (isset($_POST['date']) && ($_POST['type'] === 'Income' || ($_POST['type'] === 'Expense' && $category === 'Activities'))) {
    if (empty($_POST['date'])) {
        $errors['date'] = 'Plan date is required for Income plans or Activities category.';
    } else {
        $date = mysqli_real_escape_string($conn, $_POST['date']);
    }
} else {
    $date = ''; // Default to empty if not applicable
}

// Validate amount
if (empty($_POST['amount']) || !is_numeric($_POST['amount']) || $_POST['amount'] <= 0) {
    $errors['amount'] = 'Valid plan amount is required.';
} else {
    $amount = mysqli_real_escape_string($conn, $_POST['amount']);
}

// Validate if total plans in the category exceed allocated budget
if ($_POST['type'] === 'Expense' && !empty($category)) {
    // Fetch the total amount of plans in the same category
    $sum_query = "SELECT SUM(amount) as total_plans_in_category 
                  FROM financial_plan 
                  WHERE category = '$category' AND organization_id = $organization_id";
    $sum_result = mysqli_query($conn, $sum_query);
    $sum_row = mysqli_fetch_assoc($sum_result);
    $total_plans_in_category = $sum_row['total_plans_in_category'] ?? 0;

    // Fetch the allocated budget for the category
    $allocation_query = "SELECT allocated_budget 
                         FROM budget_allocation 
                         WHERE category = '$category' AND organization_id = $organization_id";
    $allocation_result = mysqli_query($conn, $allocation_query);
    $allocation_row = mysqli_fetch_assoc($allocation_result);
    $allocated_budget = $allocation_row['allocated_budget'] ?? 0;

    // Debugging logs
    error_log("Total plans in category: $total_plans_in_category");
    error_log("Allocated budget: $allocated_budget");
    error_log("Proposed plan amount: $amount");

    // Check if the total plans plus the new plan amount exceeds the allocated budget
    if (($total_plans_in_category + $amount) > $allocated_budget) {
        $errors['budget'] = 'Total amount for plans in this category exceeds the allocated budget.';
    }
}

// If there are no errors, proceed to insert the financial plan
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $organization_id = $_SESSION['organization_id'];

    // Insert into financial_plan table
    $query = "INSERT INTO financial_plan (title, type, category, date, amount, organization_id) 
              VALUES ('$title', '$type', '$category', '$date', $amount, $organization_id)";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Plan added successfully!';
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to add plan to the database.'];
    }
}

echo json_encode($data);

?>
