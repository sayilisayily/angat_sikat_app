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
    $profit = isset($_POST['profit']) ? floatval($_POST['profit']) : 0;

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

            // Fetch the current item details and associated event
            $stmt = $conn->prepare("SELECT event_id, quantity, amount, profit FROM event_items WHERE item_id = ?");
            $stmt->bind_param("i", $item_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to fetch item details: ' . $stmt->error);
            }
            $stmt->bind_result($event_id, $current_quantity, $current_amount, $current_profit);
            $stmt->fetch();
            $stmt->close();

            // Retrieve event details to determine its type
            $stmt = $conn->prepare("SELECT event_type, plan_id FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to fetch event details: ' . $stmt->error);
            }
            $stmt->bind_result($event_type, $plan_id);
            $stmt->fetch();
            $stmt->close();

            // If no event details are found, return an error
            if (empty($event_type)) {
                throw new Exception('Event not found for this item');
            }

            // Fetch the allocated budget from the financial plan table using plan_id
            $stmt = $conn->prepare("SELECT amount FROM financial_plan WHERE plan_id = ?");
            $stmt->bind_param("i", $plan_id);
            $stmt->execute();
            $stmt->bind_result($allocated_budget);
            $stmt->fetch();
            $stmt->close();

            if (empty($allocated_budget)) {
                throw new Exception('Allocated budget not found for the given plan_id.');
            }

            // Calculate current item totals
            $current_item_total_amount = $event_type === 'Income' 
                ? $current_quantity * ($current_amount + $current_profit)
                : $current_quantity * $current_amount;

            $current_item_total_profit = $event_type === 'Income' 
                ? $current_quantity * $current_profit
                : 0;

            // Calculate new item totals
            $new_item_total_amount = $event_type === 'Income' 
                ? $quantity * ($amount + $profit)
                : $quantity * $amount;

            $new_item_total_profit = $event_type === 'Income' 
                ? $quantity * $profit
                : 0;

            // Calculate the new total amount for the event
            $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) AS current_total FROM event_items WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to calculate event total: ' . $stmt->error);
            }
            $stmt->bind_result($current_event_total);
            $stmt->fetch();
            $stmt->close();

            if ($event_type === 'Expense') {
                // Calculate the new total event amount after the update
                $new_event_total = $current_event_total - $current_item_total_amount + $new_item_total_amount;

            } else if ($event_type === 'Income') {
                // Calculate the new total event amount after the update
                $new_event_total = $current_event_total - $current_item_total_amount + $new_item_total_amount;
            }
            
            // Check if the new event total exceeds the allocated budget
            if ($event_type === 'Expense' && $new_event_total > $allocated_budget) {
                throw new Exception('The new total for this event exceeds the allocated budget.');
            }

            // Update the event item
            $stmt = $conn->prepare("UPDATE event_items SET description = ?, quantity = ?, unit = ?, amount = ?, profit = ?, total_amount = ?, total_profit = ? WHERE item_id = ?");
            $stmt->bind_param("sisiddii", $description, $quantity, $unit, $amount, $profit, $new_item_total_amount, $new_item_total_profit, $item_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update item: ' . $stmt->error);
            }
            $stmt->close();

            // Update the event's total amount for expenses
            $stmt = $conn->prepare("UPDATE events SET total_amount = ? WHERE event_id = ?");
            $stmt->bind_param("di", $new_event_total, $event_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update event total: ' . $stmt->error);
            }
            $stmt->close();

            // If it's an income event, update the total profit for the event
            if ($event_type === 'Income') {
                // Calculate the new total profit for all income items
                $stmt = $conn->prepare("SELECT COALESCE(SUM(total_profit), 0) AS current_total_profit FROM event_items WHERE event_id = ?");
                $stmt->bind_param("i", $event_id);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to calculate total profit: ' . $stmt->error);
                }
                $stmt->bind_result($current_total_profit);
                $stmt->fetch();
                $stmt->close();

                // Update the event's total profit
                $stmt = $conn->prepare("UPDATE events SET total_profit = ? WHERE event_id = ?");
                $stmt->bind_param("di", $current_total_profit, $event_id);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update event profit: ' . $stmt->error);
                }
                $stmt->close();
            }

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
