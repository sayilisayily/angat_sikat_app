<?php
// Include database connection
include('connection.php');

// Initialize an array for validation errors and response data
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input fields
    $plan_id = isset($_POST['edit_plan_id']) ? mysqli_real_escape_string($conn, $_POST['edit_plan_id']) : '';
    $title = isset($_POST['edit_title']) ? mysqli_real_escape_string($conn, $_POST['edit_title']) : '';
    $amount = isset($_POST['edit_amount']) ? mysqli_real_escape_string($conn, $_POST['edit_amount']) : '';
    $type = isset($_POST['edit_type']) ? mysqli_real_escape_string($conn, $_POST['edit_type']) : '';
    $category = isset($_POST['edit_category']) ? mysqli_real_escape_string($conn, $_POST['edit_category']) : '';
    $date = isset($_POST['edit_date']) ? mysqli_real_escape_string($conn, $_POST['edit_date']) : '';

    // Title validation
    if (empty($title)) {
        $errors[] = 'Plan title is required.';
    } else {
        // Check for duplicate titles (excluding current plan)
        $query = "SELECT * FROM financial_plan WHERE title = '$title' AND plan_id != '$plan_id'";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            $errors[] = 'Database error: ' . mysqli_error($conn);
        } elseif (mysqli_num_rows($result) > 0) {
            $errors[] = 'A plan with this title already exists.';
        }
    }

    // Amount validation
    if (empty($amount)) {
        $errors[] = 'Amount is required.';
    } elseif (!is_numeric($amount) || $amount < 0) {
        $errors[] = 'Amount must be a positive number.';
    }

    // Type validation
    if (empty($type)) {
        $errors[] = 'Plan type is required.';
    }

    // Category validation for Expense type
    if ($type === 'Expense' && empty($category)) {
        $errors[] = 'Category is required for expense plans.';
    }

    // Date validation for specific types or categories
    if (($type === 'Income' || ($type === 'Expense' && $category === 'Activities')) && empty($date)) {
        $errors[] = 'Plan date is required for Income plans or Activities category.';
    }

    // Return errors if validation fails
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Update query
        $query = "UPDATE financial_plan SET 
                    title = '$title',
                    date = '$date',
                    amount = '$amount',
                    type = '$type',
                    category = '$category'
                  WHERE plan_id = '$plan_id'";

        if (mysqli_query($conn, $query)) {
            $data['success'] = true;
            $data['message'] = 'Plan updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update the plan. ' . mysqli_error($conn)];
        }
    }
}

// Return response as JSON
echo json_encode($data);
?>
