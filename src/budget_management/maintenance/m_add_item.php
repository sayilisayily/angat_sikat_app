<?php
require 'connection.php';

// Initialize an empty response array
$response = array('success' => false, 'errors' => array());

// Check if the required fields are set
if (isset($_POST['maintenance_id'], $_POST['description'], $_POST['quantity'], $_POST['unit'], $_POST['amount'])) {
    $maintenance_id = intval($_POST['maintenance_id']);
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
        // Check for duplicate descriptions
        $stmt = $conn->prepare("SELECT COUNT(*) FROM maintenance_items WHERE maintenance_id = ? AND description = ?");
        $stmt->bind_param("is", $maintenance_id, $description);
        if (!$stmt->execute()) {
            $response['errors']['database'] = 'Failed to check for duplicate descriptions: ' . $stmt->error;
        } else {
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count > 0) {
                $response['errors']['description'] = 'An item with the same description already exists for this maintenance';
            }
        }

        if (empty($response['errors'])) {
            // Start a transaction to ensure atomicity
            $conn->begin_transaction();
            
            try {
                // Retrieve the maintenance title based on the maintenance_id to get the corresponding title in the financial plan
                $stmt = $conn->prepare("SELECT title FROM maintenance WHERE maintenance_id = ?");
                $stmt->bind_param("i", $maintenance_id);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to fetch maintenance title: ' . $stmt->error);
                }
                $stmt->bind_result($purchase_title);
                $stmt->fetch();
                $stmt->close();

                // If no Maintenance title is found, return an error
                if (empty($purchase_title)) {
                    throw new Exception('Maintenance title not found for this maintenance_id');
                }

                // Retrieve the allocated budget for the Maintenance based on its title in the financial plan table
                $stmt = $conn->prepare("SELECT amount FROM financial_plan WHERE title = ?");
                $stmt->bind_param("s", $purchase_title);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to fetch allocated budget: ' . $stmt->error);
                }
                $stmt->bind_result($allocated_budget);
                $stmt->fetch();
                $stmt->close();

                // If no allocated budget is found, return an error
                if ($allocated_budget === null) {
                    throw new Exception('No allocated budget found for this maintenance');
                }

                // Now, retrieve the current total amount of maintenance items
                $stmt = $conn->prepare("SELECT SUM(amount * quantity) AS total_items_amount FROM maintenance_items WHERE maintenance_id = ?");
                $stmt->bind_param("i", $maintenance_id);
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

                // Calculate the new total amount after adding this item
                $new_total_amount = $current_total_amount + ($quantity * $amount);

                // Check if the new total amount exceeds the allocated budget
                if ($new_total_amount > $allocated_budget) {
                    $response['errors']['budget'] = 'The total amount of items exceeds the allocated budget for this maintenance';
                } else {
                    // Prepare the SQL statement to insert the maintenance item
                    $stmt = $conn->prepare("INSERT INTO maintenance_items (maintenance_id, description, quantity, unit, amount) VALUES (?, ?, ?, ?, ?)");
                    if ($stmt === false) {
                        throw new Exception('Prepare failed: ' . $conn->error);
                    }

                    // Bind the parameters
                    $stmt->bind_param("isisi", $maintenance_id, $description, $quantity, $unit, $amount);

                    // Execute the query to insert the new item
                    if (!$stmt->execute()) {
                        throw new Exception('Failed to add item: ' . $stmt->error);
                    }

                    // Close the statement after insertion
                    $stmt->close();

                    // Update the maintenance's total amount
                    $stmt = $conn->prepare("UPDATE maintenance SET total_amount = ? WHERE maintenance_id = ?");
                    $stmt->bind_param("di", $new_total_amount, $maintenance_id);
                    if (!$stmt->execute()) {
                        throw new Exception('Failed to update maintenance total amount: ' . $stmt->error);
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
    }
} else {
    $response['errors']['form'] = 'Required fields are missing';
}

// Return the response in JSON format
echo json_encode($response);
?>
