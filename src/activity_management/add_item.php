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
    $profit = isset($_POST['profit']) ? floatval($_POST['profit']) : 0;

    // Validation checks
    if (empty($description)) {
        $response['errors']['description'] = 'Description is required.';
    }
    if ($quantity <= 0) {
        $response['errors']['quantity'] = 'Quantity must be greater than zero.';
    }
    if (empty($unit)) {
        $response['errors']['unit'] = 'Unit is required.';
    }
    if ($amount <= 0) {
        $response['errors']['amount'] = 'Amount must be greater than zero.';
    }

    // If there are no validation errors
    if (empty($response['errors'])) {
        // Start a transaction to ensure atomicity
        $conn->begin_transaction();

        try {
            // Check for duplicate description
            $stmt = $conn->prepare("SELECT 1 FROM event_items WHERE event_id = ? AND description = ?");
            $stmt->bind_param("is", $event_id, $description);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                throw new Exception('An item with this description already exists for this event.');
            }
            $stmt->close();

            // Retrieve event type and plan_id from the events table
            $stmt = $conn->prepare("SELECT event_type, plan_id FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $stmt->bind_result($event_type, $plan_id);
            $stmt->fetch();
            $stmt->close();

            if (empty($event_type) || empty($plan_id)) {
                throw new Exception('Event not found or plan_id is missing.');
            }

            // Fetch allocated budget from the financial plan table using plan_id
            $stmt = $conn->prepare("SELECT amount FROM financial_plan WHERE plan_id = ?");
            $stmt->bind_param("i", $plan_id);
            $stmt->execute();
            $stmt->bind_result($allocated_budget);
            $stmt->fetch();
            $stmt->close();

            if (empty($allocated_budget)) {
                throw new Exception('Allocated budget not found for the given plan_id.');
            }

            // Calculate total amount for the new item
            $item_total = ($event_type === 'Income') 
                ? $quantity * ($amount + $profit) // Total amount = quantity * (amount + profit)
                : $quantity * $amount; // For expense events, total amount = quantity * amount

            // Calculate total profit for income events (quantity * profit)
            $total_profit = ($event_type === 'Income') ? $quantity * $profit : 0;

            // Calculate current total expense or income
            $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) AS current_total, COALESCE(SUM(total_profit), 0) AS current_profit FROM event_items WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $stmt->bind_result($current_total, $current_profit);
            $stmt->fetch();
            $stmt->close();

            // Check for budget overruns for expense events
            if ($event_type === 'Expense' && ($current_total + $item_total) > $allocated_budget) {
                throw new Exception('Adding this item exceeds the allocated budget for the event.');
            }

            // Insert the new item
            $stmt = $conn->prepare("INSERT INTO event_items (event_id, description, quantity, unit, amount, profit, total_amount, total_profit) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isisiddd", $event_id, $description, $quantity, $unit, $amount, $profit, $item_total, $total_profit);
            if (!$stmt->execute()) {
                throw new Exception('Failed to add item: ' . $stmt->error);
            }
            $stmt->close();

            // Update the total amount in the events table
            if ($event_type === 'Expense') {
                $new_total_amount = $current_total + $item_total;
                $new_total_profit = 0;
            } else if ($event_type === 'Income') {
                $new_total_amount = $current_total + $item_total;
                $new_total_profit = $current_profit + $total_profit;
            }

            // Update total_amount and total_profit in the events table
            $stmt = $conn->prepare("UPDATE events SET total_amount = ?, total_profit = ? WHERE event_id = ?");
            $stmt->bind_param("ddi", $new_total_amount, $new_total_profit, $event_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update event totals: ' . $stmt->error);
            }
            $stmt->close();

            // Commit the transaction
            $conn->commit();
            $response['success'] = true;

        } catch (Exception $e) {
            // Rollback transaction if any error occurs
            $conn->rollback();
            $response['errors']['database'] = $e->getMessage();
        }
    }
} else {
    $response['errors']['form'] = 'Required fields are missing.';
}

// Return the response in JSON format
echo json_encode($response);
?>
