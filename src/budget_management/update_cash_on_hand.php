<?php
// Include database connection
include('connection.php');

$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate Cash on Hand input
    if (!isset($_POST['cash_on_hand']) || $_POST['cash_on_hand'] === '') {
        $errors['cash_on_hand'] = 'Cash on Hand is required.';
    }

    // If there are validation errors, return early
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    }

    // Retrieve values from POST request
    $organization_id = (int)$_POST['organization_id'];
    $cash_on_hand = (float)$_POST['cash_on_hand'];

    // Fetch current cash_on_bank and balance from the database
    $fetch_query = "SELECT cash_on_bank, balance FROM organizations WHERE organization_id = ?";
    $fetch_stmt = $conn->prepare($fetch_query);
    if ($fetch_stmt) {
        $fetch_stmt->bind_param('i', $organization_id);
        $fetch_stmt->execute();
        $fetch_stmt->bind_result($cash_on_bank, $balance);
        $fetch_stmt->fetch();
        $fetch_stmt->close();

        // Check if the new cash_on_bank plus cash_on_hand exceeds the balance
        if (($cash_on_bank + $cash_on_hand) > $balance) {
            $errors['cash_on_hand'] = 'Cash on Bank plus Cash on Hand cannot exceed the Balance.';
            $data['success'] = false;
            $data['errors'] = $errors;
        } else {
            // Proceed with the update if validation passes
            $update_query = "UPDATE organizations SET cash_on_hand = ? WHERE organization_id = ?";
            $update_stmt = $conn->prepare($update_query);
            if ($update_stmt) {
                $update_stmt->bind_param('di', $cash_on_hand, $organization_id);

                if ($update_stmt->execute()) {
                    $data['success'] = true;
                    $data['message'] = 'Cash on Hand updated successfully!';
                } else {
                    $data['success'] = false;
                    $data['errors'] = ['database' => 'Failed to update Cash on Hand in the database.'];
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
