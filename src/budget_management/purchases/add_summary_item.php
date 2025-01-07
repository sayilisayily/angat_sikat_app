<?php
include '../connection.php';

$response = ['success' => false, 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['purchase_id'], $_POST['description'], $_POST['quantity'], $_POST['unit'], $_POST['amount'])) {
    $purchase_id = intval($_POST['purchase_id']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);
    $unit = trim($_POST['unit']);
    $amount = floatval($_POST['amount']);

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
            // Calculate the total amount
            $total_amount = $quantity * $amount;

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

            // Insert new purchase summary item
            $stmt = $conn->prepare("INSERT INTO purchase_summary_items 
                (purchase_id, description, quantity, unit, amount, total_amount, reference) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isiddds", $purchase_id, $description, $quantity, $unit, $amount, $total_amount, $reference);

            if (!$stmt->execute()) {
                throw new Exception('Failed to insert the summary item.');
            }
            $stmt->close();

            // Update total amount in purchases_summary
            $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM purchase_summary_items WHERE purchase_id = ?");
            $stmt->bind_param("i", $purchase_id);
            $stmt->execute();
            $stmt->bind_result($new_total_amount);
            $stmt->fetch();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE purchases_summary SET total_amount = ? WHERE purchase_id = ?");
            $stmt->bind_param("di", $new_total_amount, $purchase_id);

            if (!$stmt->execute()) {
                throw new Exception('Failed to update purchases totals.');
            }
            $stmt->close();

            // Recalculate the total for the purchase summary table
        $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM purchase_summary_items WHERE purchase_id = ?");
        $stmt->bind_param("i", $purchase_id);
        $stmt->execute();
        $stmt->bind_result($new_total_amount);
        $stmt->fetch();
        $stmt->close();

        // Update the total amount in the purchase table
        $stmt = $conn->prepare("UPDATE purchases_summary SET total_amount = ? WHERE purchase_id = ?");
        if ($stmt) {
            $stmt->bind_param("di", $new_total_amount, $purchase_id);
            if (!$stmt->execute()) {
                throw new Exception('Failed to update purchase summary totals.');
            }
            $stmt->close();
        }

        
            // Check for budget overrun and create notification
            $stmt = $conn->prepare("SELECT COALESCE(amount, 0) FROM financial_plan WHERE plan_id = ?");
            $stmt->bind_param("i", $plan_id);
            $stmt->execute();
            $stmt->bind_result($allocated_budget);
            $stmt->fetch();
            $stmt->close();
            
            if ($new_total_amount > $allocated_budget) {
                // Retrieve the event title
                $stmt = $conn->prepare("SELECT title FROM purchases WHERE purchase_id = ?");
                $stmt->bind_param("i", $purchase_id);
                $stmt->execute();
                $stmt->bind_result($purchase_title);
                $stmt->fetch();
                $stmt->close();

                if ($purchase_title) {
                    // Create notification for users in the organization
                    $notification_message = "The total amount for the event '$purchase_title' has exceeded the allocated budget.";
                    $stmt = $conn->prepare("
                        INSERT INTO notifications (recipient_id, organization_id, message, is_read, created_at)
                        SELECT user_id, organization_id, ?, 0, NOW()
                        FROM users
                        WHERE organization_id = (SELECT organization_id FROM purchases WHERE purchase_id = ?)
                    ");
                    $stmt->bind_param("si", $notification_message, $purchase_id);

                    if (!$stmt->execute()) {
                        throw new Exception('Failed to create notification.');
                    }
                    $stmt->close();
                }
            }

            $conn->commit();
            $response['success'] = true;
            $response['message'] = 'Item added successfully!';
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
