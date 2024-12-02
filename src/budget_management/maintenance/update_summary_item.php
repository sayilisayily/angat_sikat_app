<?php
include '../connection.php';

$errors = [];
$data = [];

// Validate required fields
if (empty($_POST['summary_item_id'])) {
    $errors['summary_item_id'] = 'Summary Item ID is required.';
} else {
    $summary_item_id = intval($_POST['summary_item_id']);
}

if (empty($_POST['maintenance_id'])) {
    $errors['maintenance_id'] = 'Maintenance ID is required.';
} else {
    $maintenance_id = intval($_POST['maintenance_id']);
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

// Handle file upload
if (isset($_FILES['reference']) && $_FILES['reference']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['reference']['error'] !== UPLOAD_ERR_OK) {
        $errors['reference'] = 'File upload error code: ' . $_FILES['reference']['error'];
    } else {
        $file_tmp = $_FILES['reference']['tmp_name'];
        $file_name = $_FILES['reference']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['doc', 'docx', 'xls', 'xlsx', 'pdf'];

        if (!in_array($file_extension, $allowed_extensions)) {
            $errors['reference'] = 'Invalid file type.';
        } else {
            $upload_dir = 'uploads/references/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_path = $upload_dir . basename($file_name);
            if (!move_uploaded_file($file_tmp, $file_path)) {
                $errors['reference'] = 'Error moving the uploaded file.';
            } else {
                $reference = $file_name;
            }
        }
    }
} else {
    // Retain the existing reference
    $existing_reference_query = "SELECT reference FROM maintenance_summary_items WHERE summary_item_id = ?";
    $stmt = $conn->prepare($existing_reference_query);
    $stmt->bind_param("i", $summary_item_id);

    if ($stmt->execute()) {
        $stmt->bind_result($existing_reference);
        $stmt->fetch();
        $reference = $existing_reference; // Retain the existing reference
    }
    $stmt->close();
}

// Update summary item and totals if no errors
if (empty($errors)) {
    $conn->begin_transaction(); // Start transaction

    try {
        $total_amount = $quantity * $amount;

        // Update maintenance_summary_items table
        $query = "UPDATE maintenance_summary_items 
                  SET description = ?, quantity = ?, unit = ?, amount = ?, total_amount = ?, reference = ? 
                  WHERE summary_item_id = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("sidddsi", $description, $quantity, $unit, $amount, $total_amount, $reference, $summary_item_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update maintenance summary item.');
            }
            $stmt->close();
        }

        // Recalculate the total for the maintenance summary table
        $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM maintenance_summary_items WHERE maintenance_id = ?");
        $stmt->bind_param("i", $maintenance_id);
        $stmt->execute();
        $stmt->bind_result($new_total_amount);
        $stmt->fetch();
        $stmt->close();

        // Update the total amount in the maintenance table
        $stmt = $conn->prepare("UPDATE maintenance_summary SET total_amount = ? WHERE maintenance_id = ?");
        if ($stmt) {
            $stmt->bind_param("di", $new_total_amount, $maintenance_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update maintenance summary totals.');
            }
            $stmt->close();
        }

        $conn->commit(); // Commit transaction
        $data['success'] = true;
        $data['message'] = 'Summary item and totals updated successfully!';
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction on failure
        $data['success'] = false;
        $data['errors'] = ['database' => $e->getMessage()];
    }
}

// Return errors or success response
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
}

echo json_encode($data);
?>
