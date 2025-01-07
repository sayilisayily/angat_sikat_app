<?php
include 'connection.php';

$errors = [];
$data = [];

// Validate required fields
if (empty($_POST['summary_item_id'])) {
    $errors['summary_item_id'] = 'Summary Item ID is required.';
} else {
    $summary_item_id = intval($_POST['summary_item_id']);
}

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

$profit = 0; // Initialize profit for all event types
if (isset($_POST['profit']) && floatval($_POST['profit']) > 0) {
    $profit = floatval($_POST['profit']);
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
    // No file uploaded, retain the existing reference
    $existing_reference_query = "SELECT reference FROM event_summary_items WHERE summary_item_id = ?";
    $stmt = $conn->prepare($existing_reference_query);
    $stmt->bind_param("i", $summary_item_id);

    if ($stmt->execute()) {
        $stmt->bind_result($existing_reference);
        $stmt->fetch();
        $reference = $existing_reference; // Retain the existing reference
    }
    $stmt->close();
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

// Update summary item and event totals if no errors
if (empty($errors)) {
    $conn->begin_transaction(); // Start transaction

    try {
        // Calculate totals based on event type
        if ($event_type === 'Income') {
            $total_profit = $quantity * $profit;
            $total_amount = $quantity * ($amount + $profit);

            // Update event_summary_items table
            $query = "UPDATE event_summary_items 
                      SET description = ?, quantity = ?, unit = ?, amount = ?, profit = ?, total_profit = ?, total_amount = ?, reference = ? 
                      WHERE summary_item_id = ?";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param("siddddssi", $description, $quantity, $unit, $amount, $profit, $total_profit, $total_amount, $reference, $summary_item_id);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update income summary item.');
                }
                $stmt->close();
            }

        } else if ($event_type === 'Expense') {
            $total_amount = $quantity * $amount;

            // Update event_summary_items table
            $query = "UPDATE event_summary_items 
                      SET description = ?, quantity = ?, unit = ?, amount = ?, total_amount = ?, reference = ? 
                      WHERE summary_item_id = ?";
            $stmt = $conn->prepare($query);

            if ($stmt) {
                $stmt->bind_param("sidddsi", $description, $quantity, $unit, $amount, $total_amount, $reference, $summary_item_id);
                if (!$stmt->execute()) {
                    throw new Exception('Failed to update expense summary item.');
                }
                $stmt->close();
            }
        }

        // Update the event totals (total_amount and total_profit) in the events table
        $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0), COALESCE(SUM(total_profit), 0) FROM event_summary_items WHERE event_id = ?");
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $stmt->bind_result($new_total_amount, $new_total_profit);
        $stmt->fetch();
        $stmt->close();

        // Update the event totals in the events table
        $stmt = $conn->prepare("UPDATE events_summary SET total_amount = ?, total_profit = ? WHERE event_id = ?");
        $stmt->bind_param("ddi", $new_total_amount, $new_total_profit, $event_id);
        if (!$stmt->execute()) {
            throw new Exception('Failed to update event totals.');
        }
        $stmt->close();

        
            // Check for budget overrun and create notification
            $stmt = $conn->prepare("SELECT COALESCE(amount, 0) FROM financial_plan WHERE plan_id = ?");
            $stmt->bind_param("i", $plan_id);
            $stmt->execute();
            $stmt->bind_result($allocated_budget);
            $stmt->fetch();
            $stmt->close();
            
            if ($new_total_amount > $allocated_budget) {
                // Retrieve the event title
                $stmt = $conn->prepare("SELECT title FROM events WHERE event_id = ?");
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                $stmt->bind_result($event_title);
                $stmt->fetch();
                $stmt->close();

                if ($event_title) {
                    // Create notification for users in the organization
                    $notification_message = "The total amount for the event '$event_title' has exceeded the allocated budget.";
                    $stmt = $conn->prepare("
                        INSERT INTO notifications (recipient_id, organization_id, message, is_read, created_at)
                        SELECT user_id, organization_id, ?, 0, NOW()
                        FROM users
                        WHERE organization_id = (SELECT organization_id FROM events WHERE event_id = ?)
                    ");
                    $stmt->bind_param("si", $notification_message, $event_id);

                    if (!$stmt->execute()) {
                        throw new Exception('Failed to create notification.');
                    }
                    $stmt->close();
                }
            }


        $conn->commit(); // Commit transaction
        $data['success'] = true;
        $data['message'] = 'Summary item updated successfully!';

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
