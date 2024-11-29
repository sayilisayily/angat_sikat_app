<?php
include('../connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the maintenance_id from POST
    $maintenance_id = $_POST['maintenance_id'];

    // Validate input
    if (empty($maintenance_id)) {
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
        $query = "DELETE FROM maintenance WHERE maintenance_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind parameters and execute the query
            $stmt->bind_param('i', $maintenance_id);

            if ($stmt->execute()) {
                $data['success'] = true;
                $data['message'] = 'Maintenance or other expense deleted successfully!';
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to delete maintenance or other expense from the database.'];
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
