<?php
include('connection.php');

// Initialize response data
$response = ['success' => false, 'errors' => [], 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $accomplishment_status = $_POST['accomplishment_status'];

    // Validate inputs
    if (empty($event_id)) {
        $response['errors']['event_id'] = 'Event ID is required.';
    }

    // If no errors, proceed
    if (empty($response['errors'])) {
        // Start a transaction
        $conn->begin_transaction();

        try {
            // Update accomplishment status
            $update_query = "UPDATE events SET accomplishment_status = ? WHERE event_id = ?";
            $stmt_update = $conn->prepare($update_query);
            $stmt_update->bind_param('ii', $accomplishment_status, $event_id);

            if (!$stmt_update->execute()) {
                throw new Exception('Failed to update accomplishment status.');
            }

            // If accomplished, insert event details into the summary table
            if ($accomplishment_status == 1) {
                $select_query = "SELECT title, event_venue, event_start_date, event_end_date, event_type, event_status, organization_id FROM events WHERE event_id = ?";
                $stmt_select = $conn->prepare($select_query);
                $stmt_select->bind_param('i', $event_id);
                $stmt_select->execute();
                $event_details = $stmt_select->get_result()->fetch_assoc();

                // Ensure all keys exist before binding parameters to avoid errors
                if ($event_details) {
                    $insert_query = "INSERT INTO events_summary (event_id, title, venue, start_date, end_date, type, status, organization_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt_insert = $conn->prepare($insert_query);
                    $stmt_insert->bind_param(
                        'issssssi', 
                        $event_id,
                        $event_details['title'],
                        $event_details['event_venue'],
                        $event_details['event_start_date'],
                        $event_details['event_end_date'],
                        $event_details['event_type'],
                        $event_details['event_status'],
                        $event_details['organization_id']
                    );

                    if (!$stmt_insert->execute()) {
                        throw new Exception('Failed to insert event into summary table.');
                    }
                } else {
                    throw new Exception('Event details not found for event_id: ' . $event_id);
                }
            }

            // Commit the transaction
            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Accomplishment status updated successfully.';
        } catch (Exception $e) {
            $conn->rollback();
            $response['errors']['database'] = $e->getMessage();
        }
    }
}

// Return response as JSON
echo json_encode($response);
?>
