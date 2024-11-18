<?php
require 'connection.php';

// Initialize an empty response array
$response = array('success' => false, 'errors' => array());

// Check if the required fields are set
if (isset($_POST['event_id'], $_POST['description'], $_POST['quantity'], $_POST['unit'], $_POST['amount'])) {
    $event_id = intval($_POST['event_id']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $unit = trim($_POST['unit']);
    $amount = floatval($_POST['amount']);
    
    // Check for validation errors
    if (empty($description)) {
        $response['errors']['description'] = 'Description is required';
    }
    if ($quantity <= 0) {
        $response['errors']['quantity'] = 'Quantity must be greater than zero';
    }
    if (empty($unit)) {
        $response['errors']['unit'] = 'Unit is required';
    }
    if ($amount <= 0) {
        $response['errors']['amount'] = 'Amount must be greater than zero';
    }

    // If there are no validation errors
    if (empty($response['errors'])) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO event_items (event_id, description, quantity, unit, amount) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt === false) {
            die('Prepare failed: ' . $conn->error);
        }

        // Bind the parameters
        $stmt->bind_param("isisi", $event_id, $description, $quantity, $unit, $amount);

        // Execute the query
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['errors']['database'] = 'Failed to add item: ' . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
} else {
    $response['errors']['form'] = 'Required fields are missing';
}

// Return the response in JSON format
echo json_encode($response);
?>
