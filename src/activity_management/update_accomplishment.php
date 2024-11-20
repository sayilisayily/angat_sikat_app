<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $accomplishment_status = $_POST['accomplishment_status'];

    // Prepare SQL statement
    $stmt = $conn->prepare("UPDATE events SET accomplishment_status = ? WHERE event_id = ?");
    $stmt->bind_param('ii', $accomplishment_status, $event_id);

    // Initialize response array
    $response = array();

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Success response
        $response['success'] = true;
        $response['message'] = "Status updated successfully!";
    } else {
        // Failure response
        $response['success'] = false;
        $response['message'] = "Error updating status.";
        $response['errors'] = array('Database error: ' . $stmt->error);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Return the response as JSON
    echo json_encode($response);
}
?>
