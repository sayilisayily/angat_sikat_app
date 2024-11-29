<?php
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the event_id from POST
    $event_id = $_POST['event_id'];

    // Validate input
    if (empty($event_id)) {
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
        $query = "DELETE FROM events WHERE event_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind parameters and execute the query
            $stmt->bind_param('i', $event_id);

            if ($stmt->execute()) {
                $data['success'] = true;
                $data['message'] = 'Event deleted successfully!';
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to delete event from the database.'];
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
