<?php
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST variables
    $item_id = $_POST['item_id'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $amount = $_POST['amount'];

    // Validate input
    if (empty($description) || empty($quantity) || empty($unit) || empty($amount)) {
        $errors[] = 'All fields are required.';
    }

    // Check for validation errors before proceeding
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    } else {
        // Fetch the current amount and event_id of the item being updated
        $stmt = $conn->prepare("SELECT amount, quantity, event_id FROM event_items WHERE item_id = ?");
        $stmt->bind_param("i", $item_id);
        $stmt->execute();
        $stmt->bind_result($current_amount, $quantity, $event_id);
        $stmt->fetch();
        $stmt->close();

        // Calculate the difference between the new amount and the old amount
        $amount_difference = $amount - $current_amount;

        // Update the item in the event_items table
        $query = "UPDATE event_items SET description = ?, quantity = ?, unit = ?, amount = ? WHERE item_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind parameters and execute the query to update the item
            $stmt->bind_param('sidsi', $description, $quantity, $unit, $amount, $item_id);

            if ($stmt->execute()) {
                // Update the total_amount of the associated event
                $stmt->close(); // Close the item update statement

                // Get the current total_amount of the event
                $stmt = $conn->prepare("SELECT total_amount FROM events WHERE event_id = ?");
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $stmt->bind_result($current_total_amount);
                $stmt->fetch();
                $stmt->close();

                // If the event total_amount doesn't exist yet, initialize it to 0
                if ($current_total_amount === null) {
                    $current_total_amount = 0;
                }

                // Calculate the new total amount
                $new_total_amount = $current_total_amount + $amount_difference;

                // Update the total_amount in the events table
                $stmt = $conn->prepare("UPDATE events SET total_amount = ? WHERE event_id = ?");
                $stmt->bind_param("di", $new_total_amount, $event_id);
                $stmt->execute();
                $stmt->close();

                $data['success'] = true;
                $data['message'] = 'Item updated and total amount updated successfully!';
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to update item in the database.'];
            }
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to prepare the update statement.'];
        }
    }
}

// Output the JSON response
echo json_encode($data);
?>
