<?php
// Include database connection
include('connection.php');
include '../session_check.php';

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize fields
    if (empty($_POST['edit_title'])) {
        $errors[] = 'Plan title is required.';
    } else {
        $title = mysqli_real_escape_string($conn, $_POST['edit_title']);
        $plan_id = mysqli_real_escape_string($conn, $_POST['edit_plan_id']); // Plan ID for update

        // Check for duplicate plan title (excluding the current record)
        $query = "SELECT * FROM financial_plan WHERE title = '$title' AND plan_id != '$plan_id'";
        $result = mysqli_query($conn, $query);

        // Check if query was successful
        if (!$result) {
            $errors[] = 'Database error: ' . mysqli_error($conn); // Show MySQL error
        } else {
            if (mysqli_num_rows($result) > 0) {
                $errors[] = 'A plan with this title already exists.';
            }
        }
    }

    if (empty($_POST['edit_amount'])) {
        $errors[] = 'Amount is required.';
    } else if (!is_numeric($_POST['edit_amount']) || $_POST['edit_amount'] < 0) {
        $errors[] = 'Amount must be a valid positive number.';
    } else {
        $amount = mysqli_real_escape_string($conn, $_POST['edit_amount']);
    }

    if (empty($_POST['edit_type'])) {
        $errors[] = 'Plan type is required.';
    } else {
        $type = mysqli_real_escape_string($conn, $_POST['edit_type']);
    }

    // Validate category for expense plans
    if ($_POST['edit_type'] === 'Expense') {
        if (empty($_POST['edit_category'])) {
            $errors['category'] = 'Category is required for expense plans.';
        } else {
            $category = mysqli_real_escape_string($conn, $_POST['edit_category']);
        }
    } else {
        $category = ''; // Default empty for non-expense plans
    }

    // Validate date (required for Activities or Income plans)
    if (isset($_POST['edit_date']) && ($_POST['edit_type'] === 'Income' || ($_POST['type'] === 'Expense' && $category === 'Activities'))) {
        if (empty($_POST['date'])) {
            $errors['date'] = 'Plan date is required for Income plans or Activities category.';
        } else {
            $date = mysqli_real_escape_string($conn, $_POST['edit_date']);
        }
    } else {
        $date = ''; // Default to empty if not applicable
    }

    // Validate if total plans in the category exceed allocated budget
    if ($_POST['edit_type'] === 'Expense' && !empty($category)) {
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

    // If there are validation errors, return them
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Prepare the update query
        $query = "UPDATE financial_plan SET 
                    title = '$title', 
                    date = '$date', 
                    amount = '$amount', 
                    type = '$type', 
                    category =  '$category'
                  WHERE plan_id = '$plan_id'";

        // Execute the update query
        if (mysqli_query($conn, $query)) {
            $data['success'] = true;
            $data['message'] = 'Plan updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update plan in the database. ' . mysqli_error($conn)];
        }
    }
}

// Return the response as JSON
echo json_encode($data);
?>
