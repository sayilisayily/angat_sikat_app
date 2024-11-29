<?php

include '../connection.php';

$errors = [];
$data = [];

// Validate required fields
if (empty($_POST['purchase_id'])) {
    $errors['purchase_id'] = 'Maintenance ID is required.';
} else {
    $purchase_id = intval($_POST['purchase_id']);
}

if (empty($_POST['description'])) {
    $errors['description'] = 'Description is required.';
} else {
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
}

if (empty($_POST['quantity']) || intval($_POST['quantity']) <= 0) {
    $errors['quantity'] = 'Quantity must be greater than zero.';
} else {
    $quantity = intval($_POST['quantity']);
}

if (empty($_POST['unit'])) {
    $errors['unit'] = 'Unit is required.';
} else {
    $unit = mysqli_real_escape_string($conn, trim($_POST['unit']));
}

if (empty($_POST['amount']) || floatval($_POST['amount']) <= 0) {
    $errors['amount'] = 'Amount must be greater than zero.';
} else {
    $amount = floatval($_POST['amount']);
}

// Debugging: Log the $_FILES array
file_put_contents('upload_debug.log', print_r($_FILES, true), FILE_APPEND);

// Handle file upload if no errors yet
if (empty($errors)) {
    if (isset($_FILES['reference']) && $_FILES['reference']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Debugging: Check the error code
        if ($_FILES['reference']['error'] !== UPLOAD_ERR_OK) {
            $errors['reference'] = 'File upload error code: ' . $_FILES['reference']['error'];
        } else {
            $file_tmp = $_FILES['reference']['tmp_name'];
            $file_name = $_FILES['reference']['name'];

            if (!empty($file_name)) {
                $upload_dir = 'uploads/references/';

                // Ensure uploads directory exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Move uploaded file to the directory
                $file_path = $upload_dir . basename($file_name);
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $reference = $file_name; // File uploaded successfully
                } else {
                    $errors['reference'] = 'Error moving the uploaded file.';
                }
            } else {
                $errors['reference'] = 'Uploaded file name is empty.';
            }
        }
    } else {
        $errors['reference'] = 'Reference file is required.';
    }
}

// Check for duplicates in the database
if (empty($errors)) {
    $check_query = "SELECT COUNT(*) FROM purchase_summary_items WHERE purchase_id = ? AND description = ?";
    $stmt = $conn->prepare($check_query);

    if ($stmt) {
        $stmt->bind_param("is", $purchase_id, $description);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $errors['description'] = 'A summary item with this description already exists for the maintenance or other expense.';
        }
    } else {
        $errors['database'] = 'Failed to prepare duplicate check query: ' . $conn->error;
    }
}

// Insert into the database if no errors
if (empty($errors)) {
    $insert_query = "INSERT INTO purchase_summary_items (purchase_id, description, quantity, unit, amount, reference) 
                     VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_query);

    if ($stmt) {
        $stmt->bind_param("isidss", $purchase_id, $description, $quantity, $unit, $amount, $reference);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'Summary item added successfully!';
        } else {
            $errors['database'] = 'Failed to insert summary item: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        $errors['database'] = 'Failed to prepare insert query: ' . $conn->error;
    }
}

// Return errors or success response
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
}

echo json_encode($data);

?>

