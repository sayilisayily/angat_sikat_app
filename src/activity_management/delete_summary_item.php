<?php
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the item_id from POST
    $item_id = $_POST['item_id'];

    // Validate input
    if (empty($item_id)) {
        $errors[] = 'Item ID is required.';
    }

    // Check for validation errors before proceeding
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    }

    // Check if the item exists and fetch its reference
    $reference = null;
    $check_query = "SELECT reference FROM event_summary_items WHERE summary_item_id = ?";
    $stmt_check = $conn->prepare($check_query);

    if ($stmt_check) {
        $stmt_check->bind_param('i', $item_id);
        $stmt_check->execute();
        $stmt_check->bind_result($reference);
        $stmt_check->fetch();
        $stmt_check->close();
    }

    // If a reference exists, delete the file from the server
    if ($reference) {
        $file_path = "uploads/references/" . $reference;

        // Check if the file exists before attempting to delete
        if (file_exists($file_path)) {
            if (!unlink($file_path)) {
                $data['success'] = false;
                $data['errors'] = ['file' => 'Failed to delete associated reference file.'];
                echo json_encode($data);
                exit;
            }
        }
    }

    // Proceed to delete the database record
    $query = "DELETE FROM event_summary_items WHERE summary_item_id = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param('i', $item_id);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'Item deleted successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to delete item from the database.'];
        }

        $stmt->close();
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to prepare the delete statement.'];
    }
}

// Output the JSON response
echo json_encode($data);
?>
