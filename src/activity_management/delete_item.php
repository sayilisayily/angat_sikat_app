<?php
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the item_id from POST
    $item_id = $_POST['item_id'];

    // Validate input
    if (empty($item_id)) {
        $errors[] = 'Item ID is required.';
    }

    // Check for validation errors before proceeding
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    } else {
        try {
            // Start a transaction
            $conn->begin_transaction();

            // Prepare the SQL query to get the item details
            $query = "SELECT event_id, quantity, amount, profit, total_amount FROM event_items WHERE item_id = ?";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                throw new Exception('Failed to prepare query to retrieve item details.');
            }

            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $stmt->bind_result($event_id, $quantity, $amount, $profit, $item_total);
            $stmt->fetch();
            $stmt->close();

            // Retrieve event type
            $stmt = $conn->prepare("SELECT event_type FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $stmt->bind_result($event_type);
            $stmt->fetch();
            $stmt->close();

            if (empty($event_type)) {
                throw new Exception('Event not found for this item.');
            }

            // Delete the item from the event_items table
            $stmt = $conn->prepare("DELETE FROM event_items WHERE item_id = ?");
            $stmt->bind_param('i', $item_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to delete item from the database.');
            }
            $stmt->close();

            // Adjust total amounts
            $new_total_amount = -$item_total; // Subtract item total from event total

            // Update total_amount in the events table
            $stmt = $conn->prepare("UPDATE events SET total_amount = total_amount + ? WHERE event_id = ?");
            $stmt->bind_param("di", $new_total_amount, $event_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update event total after deletion.');
            }
            $stmt->close();

            // Adjust total profit only for income events
            if ($event_type === 'Income') {
                $new_total_profit = -($quantity * $profit); // Subtract the total profit of the item
                $stmt = $conn->prepare("UPDATE events SET total_profit = total_profit + ? WHERE event_id = ?");
                $stmt->bind_param("di", $new_total_profit, $event_id);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update event profit after deletion.');
                }
                $stmt->close();
            }

            // Commit transaction
            $conn->commit();

            $data['success'] = true;
            $data['message'] = 'Item deleted successfully.';
        } catch (Exception $e) {
            // Rollback transaction if any error occurs
            $conn->rollback();
            $data['success'] = false;
            $data['errors'] = ['database' => $e->getMessage()];
        }
    }
}

// Output the JSON response
echo json_encode($data);
?>
