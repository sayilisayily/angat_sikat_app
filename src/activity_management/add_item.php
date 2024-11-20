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
        // Start a transaction to ensure atomicity
        $conn->begin_transaction();
        
        try {
            // Prepare the SQL statement to insert the event item
            $stmt = $conn->prepare("INSERT INTO event_items (event_id, description, quantity, unit, amount) VALUES (?, ?, ?, ?, ?)");
            if ($stmt === false) {
                throw new Exception('Prepare failed: ' . $conn->error);
            }

            // Bind the parameters
            $stmt->bind_param("isisi", $event_id, $description, $quantity, $unit, $amount);

            // Execute the query to insert the new item
            if (!$stmt->execute()) {
                throw new Exception('Failed to add item: ' . $stmt->error);
            }
            // Close the statement after insertion
            $stmt->close();

            // Now, retrieve the current total_amount of the event
            $stmt = $conn->prepare("SELECT total_amount FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to fetch event total amount: ' . $stmt->error);
            }
            $stmt->bind_result($current_total_amount);
            $stmt->fetch();
            $stmt->close();

            // If there is no total_amount (in case of new event), initialize it to 0
            if ($current_total_amount === null) {
                $current_total_amount = 0;
            }

            // Update the event's total_amount
            $new_total_amount = $current_total_amount + ($quantity * $amount);

            // Prepare the update statement
            $stmt = $conn->prepare("UPDATE events SET total_amount = ? WHERE event_id = ?");
            $stmt->bind_param("di", $new_total_amount, $event_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update event total amount: ' . $stmt->error);
            }

            // Close the update statement
            $stmt->close();

            // Commit the transaction
            $conn->commit();

            // Return success response
            $response['success'] = true;
        } catch (Exception $e) {
            // Rollback transaction if any error occurs
            $conn->rollback();

            // Return error response
            $response['errors']['database'] = $e->getMessage();
        }
    }
} else {
    $response['errors']['form'] = 'Required fields are missing';
}

// Return the response in JSON format
echo json_encode($response);
?>
