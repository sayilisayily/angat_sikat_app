<?php 
// Include database connection
include('connection.php');
include '../session_check.php';

// Initialize response data
$errors = [];
$data = [];

// Validate and update logic
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch organization_id from session
    $organization_id = $_SESSION['organization_id']; 

    // Validate and sanitize inputs
    $plan_id = mysqli_real_escape_string($conn, $_POST['edit_plan_id'] ?? '');
    $title = mysqli_real_escape_string($conn, $_POST['edit_title'] ?? '');
    $type = mysqli_real_escape_string($conn, $_POST['edit_type'] ?? '');
    $category = mysqli_real_escape_string($conn, $_POST['edit_category'] ?? '');
    $date = mysqli_real_escape_string($conn, $_POST['edit_date'] ?? '');
    $amount = mysqli_real_escape_string($conn, $_POST['edit_amount'] ?? '');

    // Basic validations
    if (empty($title)) $errors[] = 'Plan title is required.';
    if (empty($amount) || !is_numeric($amount) || $amount < 0) $errors[] = 'Amount must be a valid positive number.';
    if (empty($type)) $errors[] = 'Plan type is required.';
    if ($type === 'Expense' && empty($category)) $errors[] = 'Category is required for expense plans.';
    if (($type === 'Income' || ($type === 'Expense' && $category === 'Activities')) && empty($date)) {
        $errors[] = 'Plan date is required for Income or Activities category.';
    }

    // Check for duplicate titles (excluding current plan)
    $duplicate_query = "SELECT * FROM financial_plan WHERE title = '$title' AND plan_id != '$plan_id'";
    $duplicate_result = mysqli_query($conn, $duplicate_query);
    if ($duplicate_result && mysqli_num_rows($duplicate_result) > 0) {
        $errors[] = 'A plan with this title already exists.';
    }

    // Check budget allocation (for Expense)
    if ($type === 'Expense' && !empty($category)) {
        $sum_query = "SELECT SUM(amount) as total_plans FROM financial_plan WHERE category = '$category' AND organization_id = $organization_id";
        $allocation_query = "SELECT allocated_budget FROM budget_allocation WHERE category = '$category' AND organization_id = $organization_id";

        $sum_result = mysqli_query($conn, $sum_query);
        $allocation_result = mysqli_query($conn, $allocation_query);

        $total_plans = mysqli_fetch_assoc($sum_result)['total_plans'] ?? 0;
        $allocated_budget = mysqli_fetch_assoc($allocation_result)['allocated_budget'] ?? 0;

        if (($total_plans + $amount) > $allocated_budget) {
            $errors[] = 'Total plans exceed the allocated budget for this category.';
        }
    }

    // If no errors, update the plan
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE financial_plan SET title = ?, date = ?, amount = ?, type = ?, category = ? WHERE plan_id = ?");
        $stmt->bind_param('ssdssi', $title, $date, $amount, $type, $category, $plan_id);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'Plan updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update plan in the database. ' . $stmt->error];
        }
    } else {
        $data['success'] = false;
        $data['errors'] = $errors;
    }
}

// Return JSON response
echo json_encode($data);
?>
