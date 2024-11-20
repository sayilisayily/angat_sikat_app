<?php
include('connection.php');

$response = ['success' => false, 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    if (empty($id) || empty($action)) {
        $response['errors'][] = 'Invalid request data.';
    } else {
        // Determine the SQL query based on the action
        if ($action === 'approve') {
            $query = "UPDATE budget_approvals SET status = 'approved' WHERE approval_id = ?";
        } elseif ($action === 'disapprove') {
            $query = "UPDATE budget_approvals SET status = 'disapproved' WHERE approval_id = ?";
        } else {
            $response['errors'][] = 'Invalid action.';
        }

        // Execute the query if valid
        if (empty($response['errors'])) {
            $stmt = $conn->prepare($query);
            if ($stmt) {
                $stmt->bind_param('i', $id);
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = $action === 'approve'
                        ? 'Budget request approved successfully!'
                        : 'Budget request disapproved successfully!';
                } else {
                    $response['errors'][] = 'Database update failed.';
                }
                $stmt->close();
            } else {
                $response['errors'][] = 'Failed to prepare the statement.';
            }
        }
    }
}

echo json_encode($response);
