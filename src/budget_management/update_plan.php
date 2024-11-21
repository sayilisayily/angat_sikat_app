<?php
// Include database connection
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize fields
    if (empty($_POST['title'])) {
        $errors[] = 'Plan title is required.';
    } else {
        $title = mysqli_real_escape_string($conn, $_POST['title']);
        $plan_id = mysqli_real_escape_string($conn, $_POST['plan_id']); // Plan ID for update

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

    if (empty($_POST['amount'])) {
        $errors[] = 'Amount is required.';
    } else if (!is_numeric($_POST['amount']) || $_POST['amount'] < 0) {
        $errors[] = 'Amount must be a valid positive number.';
    } else {
        $amount = mysqli_real_escape_string($conn, $_POST['amount']);
    }

    if (empty($_POST['type'])) {
        $errors[] = 'Plan type is required.';
    } else {
        $type = mysqli_real_escape_string($conn, $_POST['type']);
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
