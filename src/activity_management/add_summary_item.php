<?php
require 'connection.php';

$response = ['success' => false, 'errors' => []];

if (isset($_POST['event_id'], $_POST['description'], $_POST['quantity'], $_POST['unit'], $_POST['amount'])) {
    $event_id = intval($_POST['event_id']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $unit = trim($_POST['unit']);
    $amount = floatval($_POST['amount']);
    $profit = isset($_POST['profit']) ? floatval($_POST['profit']) : 0;

    // Validation checks
    if (empty($description)) {
        $response['errors']['description'] = 'Description is required.';
    }
    if ($quantity <= 0) {
        $response['errors']['quantity'] = 'Quantity must be greater than zero.';
    }
    if (empty($unit)) {
        $response['errors']['unit'] = 'Unit is required.';
    }
    if ($amount <= 0) {
        $response['errors']['amount'] = 'Amount must be greater than zero.';
    }

    if (empty($response['errors'])) {
        $conn->begin_transaction();

        try {
            // Retrieve event type
            $stmt = $conn->prepare("SELECT event_type FROM events WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $stmt->bind_result($event_type);
            $stmt->fetch();
            $stmt->close();

            if (!$event_type) {
                throw new Exception('Invalid event ID.');
            }

            // Calculate totals
            $total_profit = ($event_type === 'Income') ? $quantity * $profit : 0;
            $total_amount = ($event_type === 'Income') 
                ? $quantity * ($amount + $profit) 
                : $quantity * $amount;

            // Handle file upload if provided
            $reference = null;
            if (isset($_FILES['reference']) && $_FILES['reference']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['reference']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['reference']['tmp_name'];
                    $file_name = $_FILES['reference']['name'];
                    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    $allowed_extensions = ['doc', 'docx', 'xls', 'xlsx', 'pdf'];

                    if (!in_array($file_extension, $allowed_extensions)) {
                        throw new Exception('Invalid file type.');
                    }

                    $upload_dir = 'uploads/references/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $file_path = $upload_dir . basename($file_name);

                    if (!move_uploaded_file($file_tmp, $file_path)) {
                        throw new Exception('Error moving the uploaded file.');
                    }

                    $reference = $file_name;
                } else {
                    throw new Exception('File upload error.');
                }
            }

            // Insert new summary item
            $stmt = $conn->prepare("INSERT INTO event_summary_items 
                (event_id, description, quantity, unit, amount, profit, total_profit, total_amount, reference) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isiddddss", $event_id, $description, $quantity, $unit, $amount, $profit, $total_profit, $total_amount, $reference);

            if (!$stmt->execute()) {
                throw new Exception('Failed to insert the summary item.');
            }
            $stmt->close();

            // Update total amounts and profits in events_summary
            $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0), COALESCE(SUM(total_profit), 0) FROM event_summary_items WHERE event_id = ?");
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $stmt->bind_result($new_total_amount, $new_total_profit);
            $stmt->fetch();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE events_summary SET total_amount = ?, total_profit = ? WHERE event_id = ?");
            $stmt->bind_param("ddi", $new_total_amount, $new_total_profit, $event_id);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update event totals.');
            }
            $stmt->close();

            $conn->commit();
            $response['success'] = true;

        } catch (Exception $e) {
            $conn->rollback();
            $response['errors']['database'] = $e->getMessage();
        }
    }
} else {
    $response['errors']['form'] = 'Required fields are missing.';
}

echo json_encode($response);
?>
