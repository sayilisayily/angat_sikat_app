<?php
// Include database connection
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate Cash on Bank input
    if (!isset($_POST['cash_on_bank']) || $_POST['cash_on_bank'] === '') {
        $errors['cash_on_bank'] = 'Cash on Bank is required.';
    }

    // If there are errors, return early to prevent further processing
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    }

    // Sanitize and assign variables
    $organization_id = (int)$_POST['organization_id'];
    $cash_on_bank = (float)$_POST['cash_on_bank'];

    // Fetch current cash_on_hand and balance from the database
    $fetch_query = "SELECT cash_on_hand, balance FROM organizations WHERE organization_id = ?";
    $fetch_stmt = $conn->prepare($fetch_query);
    
    if ($fetch_stmt) {
        $fetch_stmt->bind_param('i', $organization_id);
        $fetch_stmt->execute();
        $fetch_stmt->bind_result($cash_on_hand, $balance);
        $fetch_stmt->fetch();
        $fetch_stmt->close();

        // Check if the new cash_on_bank plus cash_on_hand is greater than the balance
        if (($cash_on_bank + $cash_on_hand) > $balance) {
            // If the condition is not met, return an error message
            $errors['cash_on_bank'] = 'Cash on Bank plus Cash on Hand cannot exceed the Balance.';
            $data['success'] = false;
            $data['errors'] = $errors;
        } else {
            // Proceed with the update if the condition is met
            $update_query = "UPDATE organizations SET cash_on_bank = ? WHERE organization_id = ?";
            $update_stmt = $conn->prepare($update_query);

            if ($update_stmt) {
                $update_stmt->bind_param('di', $cash_on_bank, $organization_id);

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
