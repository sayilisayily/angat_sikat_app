<?php
// Include database connection
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate organization ID
    if (!isset($_POST['organization_id']) || !is_numeric($_POST['organization_id'])) {
        $errors['organization_id'] = 'Invalid organization ID.';
    }

    // Validate Add and Subtract inputs
    $add_cash_on_bank = isset($_POST['add_cash_on_bank']) ? (float)$_POST['add_cash_on_bank'] : 0;
    $subtract_cash_on_bank = isset($_POST['subtract_cash_on_bank']) ? (float)$_POST['subtract_cash_on_bank'] : 0;

    // Ensure at least one field (add or subtract) is provided
    if ($add_cash_on_bank === 0 && $subtract_cash_on_bank === 0) {
        $errors['cash_on_bank'] = 'Please enter an amount to add or subtract.';
    }

    // If there are errors, return early
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    }

    // Sanitize and assign variables
    $organization_id = (int)$_POST['organization_id'];

    // Fetch current cash_on_bank, cash_on_hand, and balance from the database
    $fetch_query = "SELECT cash_on_bank, cash_on_hand, balance FROM organizations WHERE organization_id = ?";
    $fetch_stmt = $conn->prepare($fetch_query);

    if ($fetch_stmt) {
        $fetch_stmt->bind_param('i', $organization_id);
        $fetch_stmt->execute();
        $fetch_stmt->bind_result($current_cash_on_bank, $cash_on_hand, $balance);
        $fetch_stmt->fetch();
        $fetch_stmt->close();

        // Calculate the new cash_on_bank value
        $new_cash_on_bank = $current_cash_on_bank + $add_cash_on_bank - $subtract_cash_on_bank;

        // Check if the new cash_on_bank plus cash_on_hand exceeds the balance
        if (($new_cash_on_bank + $cash_on_hand) > $balance) {
            $errors['cash_on_bank'] = 'The total of Cash on Bank and Cash on Hand cannot exceed the Balance.';
            $data['success'] = false;
            $data['errors'] = $errors;
        } elseif ($new_cash_on_bank < 0) {
            $errors['cash_on_bank'] = 'Cash on Bank cannot be negative.';
            $data['success'] = false;
            $data['errors'] = $errors;
        } else {
            // Proceed with the update if validations pass
            $update_query = "UPDATE organizations SET cash_on_bank = ? WHERE organization_id = ?";
            $update_stmt = $conn->prepare($update_query);

            if ($update_stmt) {
                $update_stmt->bind_param('di', $new_cash_on_bank, $organization_id);

                if ($update_stmt->execute()) {
                    $data['success'] = true;
                    $data['message'] = 'Cash on Bank updated successfully!';
                } else {
                    $data['success'] = false;
                    $data['errors'] = ['database' => 'Failed to update Cash on Bank in the database.'];
                }
                $update_stmt->close();
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to prepare the update statement.'];
            }
        }
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to prepare the fetch statement.'];
    }
}

// Output the result in JSON format
echo json_encode($data);
?>
