<?php
require 'connection.php';

// Initialize an empty response array
$response = array('success' => false, 'errors' => array());

// Check if the required fields are set
if (isset($_POST['item_id'], $_POST['description'], $_POST['quantity'], $_POST['unit'], $_POST['amount'])) {
    $item_id = intval($_POST['item_id']);
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
        try {
            // Start a transaction
            $conn->begin_transaction();

            // Fetch the current item details
            $stmt = $conn->prepare("SELECT event_id, quantity, amount FROM event_items WHERE item_id = ?");
            $stmt->bind_param("i", $item_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to fetch item details: ' . $stmt->error);
            }
            $stmt->bind_result($event_id, $current_quantity, $current_amount);
            $stmt->fetch();
            $stmt->close();

            // Retrieve the event title based on the event_id
            $stmt = $conn->prepare("SELECT title FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to fetch event title: ' . $stmt->error);
            }
            $stmt->bind_result($event_title);
            $stmt->fetch();
            $stmt->close();

            // If no event title is found, return an error
            if (empty($event_title)) {
                throw new Exception('Event title not found for this event_id');
            }

            // Retrieve the allocated budget for the event based on its title
            $stmt = $conn->prepare("SELECT amount FROM financial_plan WHERE title = ?");
            $stmt->bind_param("s", $event_title);
            if (!$stmt->execute()) {
                throw new Exception('Failed to fetch allocated budget: ' . $stmt->error);
            }
            $stmt->bind_result($allocated_budget);
            $stmt->fetch();
            $stmt->close();

            // If no allocated budget is found, return an error
            if ($allocated_budget === null) {
                throw new Exception('No allocated budget found for this event');
            }

            // Retrieve the current total amount of event items
            $stmt = $conn->prepare("SELECT SUM(amount * quantity) AS total_items_amount FROM event_items WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to fetch current total amount: ' . $stmt->error);
            }
            $stmt->bind_result($current_total_amount);
            $stmt->fetch();
            $stmt->close();

            // If there is no total amount (in case of no items), initialize it to 0
            if ($current_total_amount === null) {
                $current_total_amount = 0;
            }

            // Calculate the difference from the previous total
            $current_item_total = $current_quantity * $current_amount;
            $new_item_total = $quantity * $amount;
            $new_total_amount = $current_total_amount - $current_item_total + $new_item_total;

            // Check if the new total amount exceeds the allocated budget
            if ($new_total_amount > $allocated_budget) {
                $response['errors']['budget'] = 'The updated total amount of items exceeds the allocated budget for this event';
            } else {
                // Prepare the SQL statement to update the event item
                $stmt = $conn->prepare("UPDATE event_items SET description = ?, quantity = ?, unit = ?, amount = ? WHERE item_id = ?");
                if ($stmt === false) {
                    throw new Exception('Prepare failed: ' . $conn->error);
                }

                // Bind the parameters
                $stmt->bind_param("sisdi", $description, $quantity, $unit, $amount, $item_id);

                // Execute the query to update the item
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update item: ' . $stmt->error);
                }

                // Close the statement after the update
                $stmt->close();

                // Update the event's total amount
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
            }
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
