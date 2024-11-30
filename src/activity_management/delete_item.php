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
        // Prepare the SQL query to get the item details (total_amount, event_id, event_type)
        $query = "SELECT event_id, amount, profit, total_amount FROM event_items WHERE item_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind parameters and execute the query
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $stmt->bind_result($event_id, $amount, $profit, $item_total);
            $stmt->fetch();
            $stmt->close();

            // If item is found
            if ($event_id) {
                // Retrieve event type from the events table
                $stmt = $conn->prepare("SELECT event_type FROM events WHERE event_id = ?");
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $stmt->bind_result($event_type);
                $stmt->fetch();
                $stmt->close();

                // Adjust total_amount or total_profit based on event type
                if ($event_type === 'Expense') {
                    // For expense type, subtract the item's total amount
                    $new_total_amount = -$item_total; // Subtract item total from event total
                } else if ($event_type === 'Income') {
                    // For income type, subtract the item's total amount (quantity * amount + profit)
                    $new_total_amount = -$item_total; // Subtract the item total amount (amount + profit)
                }

                // Delete the item from the event_items table
                $query = "DELETE FROM event_items WHERE item_id = ?";
                $stmt = $conn->prepare($query);

                if ($stmt) {
                    $stmt->bind_param('i', $item_id);

                    if ($stmt->execute()) {
                        // After item deletion, update the total_amount in the events table
                        $stmt = $conn->prepare("UPDATE events SET total_amount = total_amount + ? WHERE event_id = ?");
                        $stmt->bind_param("di", $new_total_amount, $event_id);

                        if ($stmt->execute()) {
                            // Check if total profit needs to be adjusted (only for income events)
                            if ($event_type === 'Income') {
                                $new_total_profit = -($profit); // Subtract the total profit of the item
                                $stmt = $conn->prepare("UPDATE events SET total_profit = total_profit + ? WHERE event_id = ?");
                                $stmt->bind_param("di", $new_total_profit, $event_id);
                                $stmt->execute();
                            }

                            $data['success'] = true;
                            $data['message'] = 'Item deleted successfully.';
                        } else {
                            $data['success'] = false;
                            $data['errors'] = ['database' => 'Failed to update event total after deletion.'];
                        }
                        $stmt->close();
                    } else {
                        $data['success'] = false;
                        $data['errors'] = ['database' => 'Failed to delete item from the database.'];
                    }
                } else {
                    $data['success'] = false;
                    $data['errors'] = ['database' => 'Failed to prepare the delete statement.'];
                }
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Item not found.'];
            }
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to prepare the query to retrieve item details.'];
        }
    }
}

// Output the JSON response
echo json_encode($data);
?>
