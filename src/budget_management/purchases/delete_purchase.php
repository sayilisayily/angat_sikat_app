<?php
include('../connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the purchase_id from POST
    $purchase_id = $_POST['purchase_id'];

    // Validate input
    if (empty($purchase_id)) {
        $errors[] = 'Event ID is required.';
    }

    // Check for validation errors before proceeding
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    } else {
        // Prepare the SQL query using a prepared statement
        $query = "DELETE FROM purchases WHERE purchase_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind parameters and execute the query
            $stmt->bind_param('i', $purchase_id);

            if ($stmt->execute()) {
                $data['success'] = true;
                $data['message'] = 'Purchase deleted successfully!';
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to delete purchase from the database.'];
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
