<?php
include '../connection.php';

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
        // Retrieve the item total_amount and purchase_id for adjustment
        $query = "SELECT total_amount, purchase_id FROM purchase_items WHERE item_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param('i', $item_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $item = $result->fetch_assoc();
            $stmt->close();

            if ($item) {
                $total_amount = $item['total_amount'];
                $purchase_id = $item['purchase_id'];

                // Update the total_amount in the purchases table
                $update_query = "UPDATE purchases SET total_amount = total_amount - ? WHERE purchase_id = ?";
                $update_stmt = $conn->prepare($update_query);

                if ($update_stmt) {
                    $update_stmt->bind_param('di', $total_amount, $purchase_id);

                    if ($update_stmt->execute()) {
                        // Proceed to delete the item
                        $delete_query = "DELETE FROM purchase_items WHERE item_id = ?";
                        $delete_stmt = $conn->prepare($delete_query);

                        if ($delete_stmt) {
                            $delete_stmt->bind_param('i', $item_id);

                            if ($delete_stmt->execute()) {
                                $data['success'] = true;
                                $data['message'] = 'Item deleted successfully!';
                            } else {
                                $data['success'] = false;
                                $data['errors'] = ['database' => 'Failed to delete item.'];
                            }

                            $delete_stmt->close();
                        }
                    } else {
                        $data['success'] = false;
                        $data['errors'] = ['database' => 'Failed to update total amount.'];
                    }

                    $update_stmt->close();
                }
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Item not found.'];
            }
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to retrieve item details.'];
        }
    }
}

// Output the JSON response
echo json_encode($data);
?>
