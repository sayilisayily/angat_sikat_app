<?php
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the plan_id from POST
    $plan_id = $_POST['plan_id'];

    // Validate input
    if (empty($plan_id)) {
        $errors[] = 'Plan ID is required.';
    }

    // Check for validation errors before proceeding
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    } else {
        // Prepare the SQL query using a prepared statement
        $query = "DELETE FROM financial_plan WHERE plan_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind parameters and execute the query
            $stmt->bind_param('i', $plan_id);

            if ($stmt->execute()) {
                $data['success'] = true;
                $data['message'] = 'Plan deleted successfully!';
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to delete plan from the database.'];
            }

            $stmt->close();
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to prepare the delete statement.'];
        }
    }
}

// Output the JSON response
echo json_encode($data);
?>
