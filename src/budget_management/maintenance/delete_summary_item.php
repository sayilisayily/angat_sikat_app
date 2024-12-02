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
            // Get the maintenance_id associated with the summary_item_id
            $maintenance_id_query = "SELECT maintenance_id FROM maintenance_summary_items WHERE summary_item_id = ?";
            $stmt = $conn->prepare($maintenance_id_query);
            if (!$stmt) {
                throw new Exception('Failed to prepare maintenance_id query.');
            }
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $stmt->bind_result($maintenance_id);
            $stmt->fetch();
            $stmt->close();

            if (empty($maintenance_id)) {
                throw new Exception('Invalid item ID or no associated maintenance record found.');
            }

            // Delete the summary item
            $delete_query = "DELETE FROM maintenance_summary_items WHERE summary_item_id = ?";
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
            $recalculate_query = "SELECT COALESCE(SUM(total_amount), 0) AS new_total FROM maintenance_summary_items WHERE maintenance_id = ?";
            $stmt = $conn->prepare($recalculate_query);
            if (!$stmt) {
                throw new Exception('Failed to prepare the recalculate query.');
            }
            $stmt->bind_param('i', $maintenance_id);
            $stmt->execute();
            $stmt->bind_result($new_total);
            $stmt->fetch();
            $stmt->close();

            // Update the total in the maintenance table
            $update_total_query = "UPDATE maintenance_summary SET total_amount = ? WHERE maintenance_id = ?";
            $stmt = $conn->prepare($update_total_query);
            if (!$stmt) {
                throw new Exception('Failed to prepare the update total query.');
            }
            $stmt->bind_param('di', $new_total, $maintenance_id);

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
