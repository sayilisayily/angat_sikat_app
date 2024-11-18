<?php
// Include database connection
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['beginning_balance'])) {
        $errors['beginning_balance'] = 'Beginning Balance is required.';
    }

    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        $organization_id = $_POST['organization_id'];
        $beginning_balance = $_POST['beginning_balance'];

        // Fetch current expenses for the organization
        $fetch_query = "SELECT SUM(amount) as total_expenses FROM expenses WHERE organization_id = ?";
        $fetch_stmt = $conn->prepare($fetch_query);

        if ($fetch_stmt) {
            $fetch_stmt->bind_param('i', $organization_id);
            $fetch_stmt->execute();
            $fetch_stmt->bind_result($total_expenses);
            $fetch_stmt->fetch();
            $fetch_stmt->close();

            // Set $total_expenses to 0 if no expenses are found
            $total_expenses = $total_expenses ?? 0;

            // Calculate new balance (beginning balance - total expenses)
            $balance = $beginning_balance - $total_expenses;

            // Prepare and execute the update query to update beginning balance and balance
            $update_query = "UPDATE organizations SET beginning_balance = ?, balance = ? WHERE organization_id = ?";
            $update_stmt = $conn->prepare($update_query);

            if ($update_stmt) {
                $update_stmt->bind_param('ddi', $beginning_balance, $balance, $organization_id);

                if ($update_stmt->execute()) {
                    $data['success'] = true;
                    $data['message'] = 'Beginning balance and balance updated successfully!';
                } else {
                    $data['success'] = false;
                    $data['errors'] = ['database' => 'Failed to update balance in the database.'];
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
