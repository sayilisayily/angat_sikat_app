<?php
include('connection.php');

$response = ['success' => false, 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if (empty($id) || empty($action)) {
        $response['errors'][] = 'Invalid request data.';
    } else {
        // Determine the new status based on the action
        $new_status = ($action === 'approve') ? 'approved' : 'disapproved';

        // Fetch the title and category associated with this approval
        $title_query = "SELECT title, category FROM budget_approvals WHERE approval_id = ?";
        $stmt = $conn->prepare($title_query);

        if ($stmt) {
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            
            if ($row) {
                $title = $row['title'];
                $category = $row['category'];
                $stmt->close();

                // Update the status in the relevant table based on the category
                if ($category === 'Events') {
                    $update_event_query = "UPDATE events SET event_status = ? WHERE title = ?";
                    $event_stmt = $conn->prepare($update_event_query);
                    $event_stmt->bind_param('ss', $new_status, $title);
                    $event_stmt->execute();
                    $event_stmt->close();
                } elseif ($category === 'Purchases') {
                    $update_purchase_query = "UPDATE purchases SET purchase_status = ? WHERE title = ?";
                    $purchase_stmt = $conn->prepare($update_purchase_query);
                    $purchase_stmt->bind_param('ss', $new_status, $title);
                    $purchase_stmt->execute();
                    $purchase_stmt->close();
                } elseif ($category === 'Maintenance') {
                    $update_maintenance_query = "UPDATE maintenance SET maintenance_status = ? WHERE title = ?";
                    $maintenance_stmt = $conn->prepare($update_maintenance_query);
                    $maintenance_stmt->bind_param('ss', $new_status, $title);
                    $maintenance_stmt->execute();
                    $maintenance_stmt->close();
                }

                // Update the budget approval status
                $update_approval_query = "UPDATE budget_approvals SET status = ? WHERE approval_id = ?";
                $approval_stmt = $conn->prepare($update_approval_query);
                $approval_stmt->bind_param('si', $new_status, $id);
                if ($approval_stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = $action === 'approve'
                        ? 'Budget request approved successfully!'
                        : 'Budget request disapproved successfully!';
                } else {
                    $response['errors'][] = 'Database update failed for budget approval.';
                }
                $approval_stmt->close();
            } else {
                $response['errors'][] = 'Approval record not found.';
            }
        } else {
            $response['errors'][] = 'Failed to prepare statement for fetching title and category.';
        }
    }
}

echo json_encode($response);
?>
