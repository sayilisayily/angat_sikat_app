<?php
require 'connection.php';

// Initialize an empty response array
$response = array('success' => false, 'errors' => array());

// Check if the required fields are set
if (isset($_POST['purchase_id'], $_POST['description'], $_POST['quantity'], $_POST['unit'], $_POST['amount']) && isset($_FILES['reference'])) {
    // Retrieve and sanitize input data
    $purchase_id = intval($_POST['purchase_id']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $unit = trim($_POST['unit']);
    $amount = floatval($_POST['amount']);

    // Validation errors
    if (empty($description)) {
        $response['errors']['description'] = 'Description is required';
    }
    if ($quantity <= 0) {
        $response['errors']['quantity'] = 'Quantity must be greater than zero';
    }
    if (empty($unit)) {
        $response['errors']['unit'] = 'Unit is required';
    }
    if ($amount <= 0) {
        $response['errors']['amount'] = 'Amount must be greater than zero';
    }

    // Handle file upload
    $reference_path = null;
    if ($_FILES['reference']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/references/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // Create the directory if it doesn't exist
        }

        $file_name = basename($_FILES['reference']['name']);
        $file_tmp = $_FILES['reference']['tmp_name'];
        $reference_path = $upload_dir . uniqid() . '_' . $file_name;

        if (!move_uploaded_file($file_tmp, $reference_path)) {
            $response['errors']['reference'] = 'Failed to upload reference file.';
        }
    } else {
        $response['errors']['reference'] = 'Reference file is required.';
    }

    // Check for duplicate descriptions
    if (empty($response['errors'])) {
        $checkStmt = $conn->prepare("SELECT COUNT(*) FROM purchase_summary_items WHERE purchase_id = ? AND description = ?");
        if ($checkStmt === false) {
            $response['errors']['database'] = 'Failed to prepare duplicate check statement: ' . $conn->error;
        } else {
            $checkStmt->bind_param("is", $purchase_id, $description);
            $checkStmt->execute();
            $checkStmt->bind_result($count);
            $checkStmt->fetch();
            $checkStmt->close();

            if ($count > 0) {
                $response['errors']['duplicate'] = 'Description already exists for this purchase.';
            }
        }
    }

    // Proceed only if no errors
    if (empty($response['errors'])) {
        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO purchase_summary_items (purchase_id, description, quantity, unit, amount, reference) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Check if preparation was successful
        if ($stmt === false) {
            $response['errors']['prepare'] = 'Prepare failed: ' . $conn->error;
        } else {
            // Bind the parameters
            if (!$stmt->bind_param("isids", $purchase_id, $description, $quantity, $unit, $amount, $reference_path)) {
                $response['errors']['bind'] = 'Bind failed: ' . $stmt->error;
            } else {
                // Execute the query
                if ($stmt->execute()) {
                    $response['success'] = true;
                } else {
                    $response['errors']['execute'] = 'Failed to add summary item: ' . $stmt->error;
                }
            }
            // Close the statement
            $stmt->close();
        }
    }
} else {
    $response['errors']['form'] = 'Required fields are missing';
}

// Return the response in JSON format
echo json_encode($response);
?>
