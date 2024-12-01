<?php

include 'connection.php';

$errors = [];
$data = [];

// Validate required fields
if (empty($_POST['event_id'])) {
    $errors['event_id'] = 'Event ID is required.';
} else {
    $event_id = intval($_POST['event_id']);
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

if (empty($_POST['profit']) || floatval($_POST['profit']) <= 0) {
    $errors['profit'] = 'Profit must be greater than zero.';
} else {
    $profit = floatval($_POST['profit']);
}

// Handle file upload if no errors yet
if (empty($errors)) {
    if (isset($_FILES['reference']) && $_FILES['reference']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['reference']['error'] !== UPLOAD_ERR_OK) {
            $errors['reference'] = 'File upload error code: ' . $_FILES['reference']['error'];
        } else {
            $file_tmp = $_FILES['reference']['tmp_name'];
            $file_name = $_FILES['reference']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION)); // Get file extension
            $allowed_extensions = ['doc', 'docx', 'xls', 'xlsx', 'pdf']; // Allowed file extensions
            $allowed_mime_types = [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/pdf',
            ];

            if (!empty($file_name)) {
                // Validate file extension
                if (!in_array($file_extension, $allowed_extensions)) {
                    $errors['reference'] = 'Invalid file type.';
                } else {
                    // Validate MIME type
                    $file_mime = mime_content_type($file_tmp);
                    if (!in_array($file_mime, $allowed_mime_types)) {
                        $errors['reference'] = 'Invalid file type based on MIME type.';
                    } else {
                        $upload_dir = 'uploads/references/';

                        // Ensure uploads directory exists
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0777, true);
                        }

                        $file_path = $upload_dir . basename($file_name);
                        if (move_uploaded_file($file_tmp, $file_path)) {
                            $reference = $file_name;
                        } else {
                            $errors['reference'] = 'Error moving the uploaded file.';
                        }
                    }
                }
            } else {
                $errors['reference'] = 'Uploaded file name is empty.';
            }
        }
    } else {
        $errors['reference'] = 'Reference file is required.';
    }
}

// Determine event type
if (empty($errors)) {
    $event_type_query = "SELECT event_type FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($event_type_query);

    if ($stmt) {
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->bind_result($event_type);
        $stmt->fetch();
        $stmt->close();

        if (!$event_type) {
            $errors['event_id'] = 'Event type not found for the specified event.';
        }
    } else {
        $errors['database'] = 'Failed to retrieve event type: ' . $conn->error;
    }
}

// Check for duplicates
if (empty($errors)) {
    $check_query = "SELECT COUNT(*) FROM event_summary_items WHERE event_id = ? AND description = ?";
    $stmt = $conn->prepare($check_query);

    if ($stmt) {
        $stmt->bind_param("is", $event_id, $description);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $errors['description'] = 'A summary item with this description already exists for the event.';
        }
    } else {
        $errors['database'] = 'Failed to prepare duplicate check query: ' . $conn->error;
    }
}

// Insert into the database if no errors
if (empty($errors)) {
    if ($event_type === 'Income') {
        $total_profit = $quantity * $profit;
        $total_amount = $quantity * ($amount + $profit);
        $insert_query = "INSERT INTO event_summary_items (event_id, description, quantity, unit, amount, profit, total_profit, total_amount, reference) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
    
        if ($stmt) {
            $stmt->bind_param("isiddddds", $event_id, $description, $quantity, $unit, $amount, $profit, $total_profit, $total_amount, $reference);
            if ($stmt->execute()) {
                $data['success'] = true;
                $data['message'] = 'Income summary item added successfully!';
            } else {
                $errors['database'] = 'Failed to insert income summary item: ' . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors['database'] = 'Failed to prepare insert query: ' . $conn->error;
        }
    } else if ($event_type === 'Expense'){
        // For expense events
        $total_amount = $quantity * $amount;
        $insert_query = "INSERT INTO event_summary_items (event_id, description, quantity, unit, amount, total_amount, reference) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);

        if ($stmt) {
            $stmt->bind_param("isiddds", $event_id, $description, $quantity, $unit, $amount, $total_amount, $reference);

            if ($stmt->execute()) {
                $data['success'] = true;
                $data['message'] = 'Expense summary item added successfully!';
            } else {
                $errors['database'] = 'Failed to insert expense summary item: ' . $stmt->error;
            }

            $stmt->close();
        } else {
            $errors['database'] = 'Failed to prepare insert query: ' . $conn->error;
        }
    }
}

// Return errors or success response
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
}

echo json_encode($data);

?>
