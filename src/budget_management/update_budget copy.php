<?php
include 'connection.php';

$response = ['success' => false];

// Validate and get values from POST request
if (isset($_POST['allocation_id']) && is_numeric($_POST['allocation_id']) &&
    isset($_POST['add_budget']) && is_numeric($_POST['add_budget']) &&
    isset($_POST['subtract_budget']) && is_numeric($_POST['subtract_budget']) &&
    isset($_POST['organization_id']) && is_numeric($_POST['organization_id'])) {

    $allocation_id = (int) $_POST['allocation_id'];
    $add_budget = (float) $_POST['add_budget'];
    $subtract_budget = (float) $_POST['subtract_budget'];
    $organization_id = (int) $_POST['organization_id'];

    // Step 1: Fetch current allocated budget
    $query = "SELECT allocated_budget FROM budget_allocation WHERE allocation_id = ? AND organization_id = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        $response['message'] = 'SQL Error: Failed to prepare statement for fetching allocated budget.';
        $response['error'] = $conn->error;
        echo json_encode($response);
        exit;
    }

    $stmt->bind_param('ii', $allocation_id, $organization_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        $response['message'] = 'SQL Error: Failed to execute the query for fetching allocated budget.';
        $response['error'] = $stmt->error;
        echo json_encode($response);
        exit;
    }

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $current_allocated_budget = $row['allocated_budget'];

        // Step 2: Fetch beginning balance from the organizations table
        $org_query = "SELECT beginning_balance FROM organizations WHERE organization_id = ?";
        $org_stmt = $conn->prepare($org_query);
        if (!$org_stmt) {
            $response['message'] = 'SQL Error: Failed to prepare statement for fetching beginning balance.';
            $response['error'] = $conn->error;
            echo json_encode($response);
            exit;
        }

        $org_stmt->bind_param('i', $organization_id);
        $org_stmt->execute();
        $org_result = $org_stmt->get_result();

        if (!$org_result) {
            $response['message'] = 'SQL Error: Failed to execute the query for fetching beginning balance.';
            $response['error'] = $org_stmt->error;
            echo json_encode($response);
            exit;
        }

        if ($org_result && $org_result->num_rows > 0) {
            $org_row = $org_result->fetch_assoc();
            $beginning_balance = $org_row['beginning_balance'];

            // Step 3: Validate if the new allocated budget exceeds the beginning balance
            $new_budget = $current_allocated_budget + $add_budget - $subtract_budget;

            if ($new_budget > $beginning_balance) {
                $response['message'] = 'Total allocated budget cannot exceed the beginning balance.';
                $response['new_budget'] = $new_budget;
                $response['beginning_balance'] = $beginning_balance;
            } else {
                // Step 4: Update the allocated budget in the database
                $update_query = "UPDATE budget_allocation SET allocated_budget = ? WHERE allocation_id = ? AND organization_id = ?";
                $update_stmt = $conn->prepare($update_query);
                if (!$update_stmt) {
                    $response['message'] = 'SQL Error: Failed to prepare statement for updating allocated budget.';
                    $response['error'] = $conn->error;
                    echo json_encode($response);
                    exit;
                }

                $update_stmt->bind_param('dii', $new_budget, $allocation_id, $organization_id);
                if ($update_stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Budget updated successfully.';
                    $response['new_budget'] = $new_budget;
                } else {
                    $response['message'] = 'Failed to update the budget in the database.';
                    $response['error'] = $update_stmt->error;
                }

                $update_stmt->close();
            }
        } else {
            $response['message'] = 'No organization found for the provided ID.';
            $response['organization_id'] = $organization_id;
        }

        $org_stmt->close();
    } else {
        $response['message'] = 'No budget allocation found for the provided allocation ID and organization.';
        $response['allocation_id'] = $allocation_id;
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid input data. Please check the input values.';
    $response['input_data'] = $_POST;
}

// Output the response
echo json_encode($response);
?>
