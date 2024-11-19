<?php
// Include database connection
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $organization_id = $_POST['organization_id'];
    $current_beginning_balance = $_POST['current_beginning_balance'] ?? 0; // Readonly value
    $add_amount = $_POST['add_amount'] ?? 0;
    $subtract_amount = $_POST['subtract_amount'] ?? 0;

    // Validation
    if ($add_amount < 0) {
        $errors['add_amount'] = 'Add amount cannot be negative.';
    }

    if ($subtract_amount < 0) {
        $errors['subtract_amount'] = 'Subtract amount cannot be negative.';
    }

    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Calculate new beginning balance
        $new_beginning_balance = $current_beginning_balance + $add_amount - $subtract_amount;

        // Fetch current expenses for the organization
        $fetch_query = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE organization_id = ?";
        $fetch_stmt = $conn->prepare($fetch_query);

        if ($fetch_stmt) {
            $fetch_stmt->bind_param('i', $organization_id);
            $fetch_stmt->execute();
            $fetch_stmt->bind_result($total_expenses);
            $fetch_stmt->fetch();
            $fetch_stmt->close();

            // Set $total_expenses to 0 if no expenses are found
            $total_expenses = $total_expenses ?? 0;

            // Calculate the new balance (new beginning balance - total expenses)
            $balance = $new_beginning_balance - $total_expenses;

            // Prepare and execute the update query
            $update_query = "UPDATE organizations SET beginning_balance = ?, balance = ? WHERE organization_id = ?";
            $update_stmt = $conn->prepare($update_query);

            if ($update_stmt) {
                $update_stmt->bind_param('ddi', $new_beginning_balance, $balance, $organization_id);

                if ($update_stmt->execute()) {
                    $data['success'] = true;
                    $data['message'] = 'Beginning balance and balance updated successfully!';
                } else {
                    $data['success'] = false;
                    $data['errors'] = ['database' => 'Failed to update the database.'];
                }

                $update_stmt->close();
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to prepare the update statement.'];
            }
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to prepare the fetch statement.'];
        }
    }
}

echo json_encode($data);
?>
