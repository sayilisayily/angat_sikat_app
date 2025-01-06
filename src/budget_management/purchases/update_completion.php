<?php
include '../../connection.php';

$response = ['success' => false, 'errors' => [], 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $purchase_id = $_POST['purchase_id'];
    $completion_status = $_POST['completion_status'];

    if (empty($purchase_id)) {
        $response['errors']['purchase_id'] = 'Purchase ID is required.';
    }

    if (empty($response['errors'])) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Update completion status
            $update_query = "UPDATE purchases SET completion_status = ? WHERE purchase_id = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param('ii', $completion_status, $purchase_id);

            if (!$stmt_update->execute()) {
                throw new Exception('Failed to update completion status: ' . $stmt_update->error);
            }

            // If completed, insert purchases details into the summary table
            if ($completion_status == 1) {
                $select_query = "SELECT title, purchase_status, completion_status, organization_id 
                                 FROM purchases WHERE purchase_id = ?";
                $stmt_select = $conn->prepare($select_query);
                $stmt_select->bind_param('i', $purchase_id);
                $stmt_select->execute();
                $maintenance_details = $stmt_select->get_result()->fetch_assoc();

                if ($maintenance_details) {
                    $insert_query = "INSERT INTO purchases_summary 
                                    (purchase_id, title, purchase_status, completion_status, organization_id) 
                                    VALUES (?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($insert_query);
                    $stmt_insert->bind_param(
                        'issii',
                        $purchase_id,
                        $maintenance_details['title'],
                        $maintenance_details['purchase_status'],
                        $maintenance_details['completion_status'],
                        $maintenance_details['organization_id']
                    );

                    if (!$stmt_insert->execute()) {
                        throw new Exception('Failed to insert into purchases_summary: ' . $stmt_insert->error);
                    }
                } else {
                    throw new Exception('Purchase details not found for purchase_id: ' . $purchase_id);
                }
            }

            // Commit the transaction
            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Completion status updated successfully.';
        } catch (Exception $e) {
            $conn->rollback();
            $response['errors']['database'] = $e->getMessage();
        }
    }
}

// Return response as JSON
echo json_encode($response);
?>