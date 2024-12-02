<?php
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the approval_id from POST
    $approval_id = $_POST['approval_id'];

    // Validate input
    if (empty($approval_id)) {
        $errors[] = 'Approval ID is required.';
    }

    // Check for validation errors before proceeding
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    } else {
        // Prepare the SQL query using a prepared statement
        $query = "UPDATE budget_approvals SET archived = 0 WHERE approval_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind parameters and execute the query
            $stmt->bind_param('i', $approval_id);

            if ($stmt->execute()) {
                $data['success'] = true;
                $data['message'] = 'Request recovered successfully!';
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to recover request to the database.'];
            }

            $stmt->close();
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to prepare the recover statement.'];
        }
    }
}

// Output the JSON response
echo json_encode($data);
?>
