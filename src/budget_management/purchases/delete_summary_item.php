<?php
include('../connection.php');

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
        // Start transaction
        $conn->begin_transaction();

        try {
            // Get the purchase_id associated with the summary_item_id
            $purchase_id_query = "SELECT purchase_id FROM purchase_summary_items WHERE summary_item_id = ?";
            $stmt = $conn->prepare($purchase_id_query);
            if (!$stmt) {
                throw new Exception('Failed to prepare purchase_id query.');
            }
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $stmt->bind_result($purchase_id);
            $stmt->fetch();
            $stmt->close();

            if (empty($purchase_id)) {
                throw new Exception('Invalid item ID or no associated maintenance record found.');
            }

            // Delete the summary item
            $delete_query = "DELETE FROM purchase_summary_items WHERE summary_item_id = ?";
            $stmt = $conn->prepare($delete_query);
            if (!$stmt) {
                throw new Exception('Failed to prepare the delete statement.');
            }
            $stmt->bind_param('i', $item_id);

            if (!$stmt->execute()) {
                throw new Exception('Failed to delete item from the database.');
            }
            $stmt->close();

            // Recalculate the total for the maintenance record
            $recalculate_query = "SELECT COALESCE(SUM(total_amount), 0) AS new_total FROM purchase_summary_items WHERE purchase_id = ?";
            $stmt = $conn->prepare($recalculate_query);
            if (!$stmt) {
                throw new Exception('Failed to prepare the recalculate query.');
            }
            $stmt->bind_param('i', $purchase_id);
            $stmt->execute();
            $stmt->bind_result($new_total);
            $stmt->fetch();
            $stmt->close();

            // Update the total in the maintenance table
            $update_total_query = "UPDATE purchases_summary SET total_amount = ? WHERE purchase_id = ?";
            $stmt = $conn->prepare($update_total_query);
            if (!$stmt) {
                throw new Exception('Failed to prepare the update total query.');
            }
            $stmt->bind_param('di', $new_total, $purchase_id);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update maintenance total.');
            }
            $stmt->close();

            // Commit the transaction
            $conn->commit();

            // Return success response
            $data['success'] = true;
            $data['message'] = 'Item deleted successfully!';
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            $data['success'] = false;
            $data['errors'] = ['database' => $e->getMessage()];
        }
    }
}

// Output the JSON response
echo json_encode($data);
?>
