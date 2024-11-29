<?php
include '../connection.php';

$response = array(); // Initialize response array

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get input data
    $maintenance_id = $_POST['maintenance_id'];
    $completion_status = $_POST['completion_status'];

    // Prepare SQL statement
    $stmt = $conn->prepare("UPDATE maintenance SET completion_status = ? WHERE maintenance_id = ?");
    $stmt->bind_param('ii', $completion_status, $maintenance_id);

    // Execute the statement and check for success
    if ($stmt->execute()) {
        // Success response
        $response['success'] = true;
        $response['message'] = "Completion status updated successfully!";
    } else {
        // Failure response
        $response['success'] = false;
        $response['message'] = "Error updating completion status.";
        $response['errors'] = array('Database error: ' . $stmt->error);
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();

    // Return the response as JSON
    echo json_encode($response);
}
?>
