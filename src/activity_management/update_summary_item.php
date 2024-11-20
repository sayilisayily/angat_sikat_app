<?php
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve POST variables
    $item_id = $_POST['summary_item_id'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $amount = $_POST['amount'];

    // Calculate total amount
    $total_amount = $quantity * $amount;

    // Validate input
    if (empty($description) || empty($quantity) || empty($unit) || empty($amount)) {
        $errors[] = 'All fields are required.';
    }

    // Check for validation errors before proceeding
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    } else {
        // Prepare the SQL query using a prepared statement
        $query = "UPDATE event_summary_items 
                  SET description = ?, quantity = ?, unit = ?, amount = ?, total_amount = ? 
                  WHERE summary_item_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            // Bind parameters and execute the query
            $stmt->bind_param('sidsii', $description, $quantity, $unit, $amount, $total_amount, $item_id);

            if ($stmt->execute()) {
                $data['success'] = true;
                $data['message'] = 'Item updated successfully!';
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to update item in the database.'];
            }

            $stmt->close();
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to prepare the update statement.'];
        }
    }
}

// Output the JSON response
echo json_encode($data);
?>
