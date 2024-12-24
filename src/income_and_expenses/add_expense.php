<?php 
include('../connection.php');
include '../session_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Initialize response data
    $data = [];
    $errors = [];

    // Retrieve data from POST
    $title = trim($_POST['title']);
    $total_amount = floatval($_POST['total_amount']);
    $category = trim($_POST['category']);
    $summary_id = intval($_POST['summary_id']);

    // Input Validation
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }

    if (empty($total_amount) || $total_amount <= 0) {
        $errors[] = 'Valid amount is required.';
    }
    
    if (empty($category)) {
        $errors[] = 'Category is required.';
    }

    // Check for duplicate expense
    $duplicate_check_query = "SELECT expense_id FROM expenses WHERE summary_id = ? AND organization_id = ?";
    $duplicate_stmt = $conn->prepare($duplicate_check_query);
    $duplicate_stmt->bind_param('ii', $summary_id, $organization_id);

    if ($duplicate_stmt->execute()) {
        $duplicate_result = $duplicate_stmt->get_result();
        if ($duplicate_result->num_rows > 0) {
            $errors[] = 'An expense with the same title already exists.';
        }
    } else {
        $errors[] = 'Error checking for duplicate expenses: ' . $duplicate_stmt->error;
    }

    $duplicate_stmt->close();

    // Handle file upload
    $reference = null;
    if (isset($_FILES['reference']) && $_FILES['reference']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['reference']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['reference']['tmp_name'];
            $file_name = $_FILES['reference']['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['doc', 'docx', 'xls', 'xlsx', 'pdf'];

            // Validate file extension
            if (!in_array($file_extension, $allowed_extensions)) {
                $errors[] = 'Invalid file type.';
            }

            // Define upload directory and handle the file upload
            $upload_dir = 'uploads/references/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_path = $upload_dir . basename($file_name);

            if (!move_uploaded_file($file_tmp, $file_path)) {
                $errors[] = 'Error moving the uploaded file.';
            } else {
                $reference = $file_name; // Save the file name to be inserted into the database
            }
        } else {
            $errors[] = 'File upload error.';
        }
    }

    // Return validation errors if any
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    }

    // Start a transaction to ensure consistency
    $conn->begin_transaction();

    try {
        // Insert the expense details into the expenses table
        $query = "INSERT INTO expenses (title, amount, category, reference, organization_id, summary_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Prepare error: " . $conn->error);
        }

        $stmt->bind_param('sdssii', $title, $total_amount, $category, $reference, $organization_id, $summary_id);

        if (!$stmt->execute()) {
            throw new Exception("Execution error: " . $stmt->error);
        }

        $stmt->close();

        // Update the budget_allocation table's total_spent field
        $update_query = "
            UPDATE budget_allocation 
            SET total_spent = total_spent + ? 
            WHERE category = ? AND organization_id = ?
        ";
        $update_stmt = $conn->prepare($update_query);

        if (!$update_stmt) {
            throw new Exception("Prepare error for update: " . $conn->error);
        }

        $update_stmt->bind_param('dsi', $total_amount, $category, $organization_id);

        if (!$update_stmt->execute()) {
            throw new Exception("Execution error for update: " . $update_stmt->error);
        }

        $update_stmt->close();

        // Update the organizations table's expense and balance fields
        $org_update_query = "
            UPDATE organizations 
            SET expense = expense + ?, 
                balance = balance - ? 
            WHERE organization_id = ?
        ";
        $org_update_stmt = $conn->prepare($org_update_query);

        if (!$org_update_stmt) {
            throw new Exception("Prepare error for organization update: " . $conn->error);
        }

        $org_update_stmt->bind_param('ddi', $total_amount, $total_amount, $organization_id);

        if (!$org_update_stmt->execute()) {
            throw new Exception("Execution error for organization update: " . $org_update_stmt->error);
        }

        $org_update_stmt->close();

        // Fetch the updated balance from the organizations table
        $balance_query = "SELECT balance FROM organizations WHERE organization_id = ?";
        $balance_stmt = $conn->prepare($balance_query);

        if (!$balance_stmt) {
            throw new Exception("Prepare error for balance fetch: " . $conn->error);
        }

        $balance_stmt->bind_param('i', $organization_id);

        if (!$balance_stmt->execute()) {
            throw new Exception("Execution error for balance fetch: " . $balance_stmt->error);
        }

        $balance_result = $balance_stmt->get_result();
        $balance_row = $balance_result->fetch_assoc();
        $updated_balance = $balance_row['balance'];

        $balance_stmt->close();

        // Insert a record into the balance_history table
        $history_query = "INSERT INTO balance_history (organization_id, balance, updated_at) VALUES (?, ?, NOW())";
        $history_stmt = $conn->prepare($history_query);

        if (!$history_stmt) {
            throw new Exception("Prepare error for balance history: " . $conn->error);
        }

        $history_stmt->bind_param('id', $organization_id, $updated_balance);

        if (!$history_stmt->execute()) {
            throw new Exception("Execution error for balance history: " . $history_stmt->error);
        }

        $history_stmt->close();

        // Commit the transaction
        $conn->commit();

        // Set success response
        $data['success'] = true;
        $data['message'] = 'Expense added, organization balance updated, and balance history recorded successfully!';
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();

        // Set error response
        $data['success'] = false;
        $data['errors'] = ['error' => $e->getMessage()];
    }

    echo json_encode($data);
}
?>
