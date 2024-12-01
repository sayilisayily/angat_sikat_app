<?php
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the item_id from POST
    $item_id = $_POST['item_id'];
    $event_id = $_POST['event_id']; // Assuming the event_id is also passed in POST

    // Validate input
    if (empty($item_id)) {
        $errors[] = 'Item ID is required.';
    }

    if (empty($event_id)) {
        $errors[] = 'Event ID is required.';
    }

    // Check for validation errors before proceeding
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    }

    // Check if the item exists and fetch its details
    $reference = null;
    $description = null;
    $quantity = 0;
    $amount = 0;
    $profit = 0;
    $total_profit = 0;
    $total_amount = 0;

    $check_query = "SELECT reference, description, quantity, amount, profit, total_profit, total_amount 
                    FROM event_summary_items 
                    WHERE summary_item_id = ?";
    $stmt_check = $conn->prepare($check_query);

    if ($stmt_check) {
        $stmt_check->bind_param('i', $item_id);
        $stmt_check->execute();
        $stmt_check->bind_result($reference, $description, $quantity, $amount, $profit, $total_profit, $total_amount);
        $stmt_check->fetch();
        $stmt_check->close();
    }

    // If no record is found, return an error
    if (!$reference) {
        $errors[] = 'Summary item not found.';
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    }

    // If a reference exists, delete the file from the server
    if ($reference) {
        $file_path = "uploads/references/" . $reference;

        // Check if the file exists before attempting to delete
        if (file_exists($file_path)) {
            if (!unlink($file_path)) {
                $data['success'] = false;
                $data['errors'] = ['file' => 'Failed to delete associated reference file.'];
                echo json_encode($data);
                exit;
            }
        }
    }

    // Proceed to delete the database record
    $query = "DELETE FROM event_summary_items WHERE summary_item_id = ?";
    $stmt_delete = $conn->prepare($query);

    if ($stmt_delete) {
        $stmt_delete->bind_param('i', $item_id);
        
        if ($stmt_delete->execute()) {
            // After deletion, we need to update the event's total_amount and total_profit
            // Recalculate the totals for the event
            if ($event_id) {
                // Calculate new total amount and profit for the event
                $update_event_query = "SELECT SUM(total_amount), SUM(total_profit) FROM event_summary_items WHERE event_id = ?";
                $stmt_update_event = $conn->prepare($update_event_query);
                $stmt_update_event->bind_param('i', $event_id);
                $stmt_update_event->execute();
                $stmt_update_event->bind_result($new_total_amount, $new_total_profit);
                $stmt_update_event->fetch();
                $stmt_update_event->close();

                // Update the event with the new totals
                $update_event_query = "UPDATE events_summary 
                                       SET total_amount = ?, total_profit = ? 
                                       WHERE event_id = ?";
                $stmt_update_event = $conn->prepare($update_event_query);
                $stmt_update_event->bind_param('ddi', $new_total_amount, $new_total_profit, $event_id);

                if ($stmt_update_event->execute()) {
                    $data['success'] = true;
                    $data['message'] = 'Item deleted and event totals updated successfully!';
                } else {
                    $data['success'] = false;
                    $data['errors'] = ['database' => 'Failed to update event totals.'];
                }
                $stmt_update_event->close();
            }
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to delete item from the database.'];
        }

        $stmt_delete->close();
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to prepare the delete statement.'];
    }
}

// Output the JSON response
echo json_encode($data);
?>
