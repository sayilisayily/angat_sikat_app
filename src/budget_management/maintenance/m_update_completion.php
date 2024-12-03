<?php
include '../connection.php';

$response = ['success' => false, 'errors' => [], 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $maintenance_id = $_POST['maintenance_id'];
    $completion_status = $_POST['completion_status'];

    if (empty($maintenance_id)) {
        $response['errors']['maintenance_id'] = 'Maintenance ID is required.';
    }

    if (empty($response['errors'])) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Update completion status
            $update_query = "UPDATE maintenance SET completion_status = ? WHERE maintenance_id = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param('ii', $completion_status, $maintenance_id);

            if (!$stmt_update->execute()) {
                throw new Exception('Failed to update completion status: ' . $stmt_update->error);
            }

            // If completed, insert maintenance details into the summary table
            if ($completion_status == 1) {
                $select_query = "SELECT title, maintenance_status, completion_status, organization_id 
                                 FROM maintenance WHERE maintenance_id = ?";
                $stmt_select = $conn->prepare($select_query);
                $stmt_select->bind_param('i', $maintenance_id);
                $stmt_select->execute();
                $maintenance_details = $stmt_select->get_result()->fetch_assoc();

                if ($maintenance_details) {
                    $insert_query = "INSERT INTO maintenance_summary 
                                    (maintenance_id, title, maintenance_status, completion_status, organization_id) 
                                    VALUES (?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($insert_query);
                    $stmt_insert->bind_param(
                        'issii',
                        $maintenance_id,
                        $maintenance_details['title'],
                        $maintenance_details['maintenance_status'],
                        $maintenance_details['completion_status'],
                        $maintenance_details['organization_id']
                    );

                    if (!$stmt_insert->execute()) {
                        throw new Exception('Failed to insert into maintenance_summary: ' . $stmt_insert->error);
                    }
                } else {
                    throw new Exception('Maintenance details not found for maintenance_id: ' . $maintenance_id);
                }
            }

            // Commit the transaction
            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Completion status updated and summary recorded successfully.';
        } catch (Exception $e) {
            $conn->rollback();
            $response['errors']['database'] = $e->getMessage();
        }
    }
}

// Return response as JSON
echo json_encode($response);
?>
