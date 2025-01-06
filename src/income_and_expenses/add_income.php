<?php 
include('../connection.php');
include '../session_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Initialize response data
    $data = [];
    $errors = [];

    // Retrieve data from POST
    $title = trim($_POST['title']);
    $revenue = floatval($_POST['revenue']);
    $summary_id = intval($_POST['summary_id']); // Make sure summary_id is passed from the form

    // Input Validation
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }

    if (empty($revenue) || $revenue <= 0) {
        $errors[] = 'Valid revenue amount is required.';
    }

    if (empty($summary_id)) {
        $errors[] = 'Summary ID is required.';
    }

    // Check for duplicate summary_id
    $duplicateCheckQuery = "SELECT COUNT(*) AS count FROM income WHERE summary_id = ?";
    $duplicateStmt = $conn->prepare($duplicateCheckQuery);

    if (!$duplicateStmt) {
        $errors[] = "Prepare error for duplicate check: " . $conn->error;
    } else {
        $duplicateStmt->bind_param('i', $summary_id);
        $duplicateStmt->execute();
        $duplicateResult = $duplicateStmt->get_result();
        $duplicateRow = $duplicateResult->fetch_assoc();

        if ($duplicateRow['count'] > 0) {
            $errors[] = 'This summary ID already exists in the income records.';
        }

        $duplicateStmt->close();
    }

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

    // Start a transaction to ensure data consistency
    $conn->begin_transaction();

    try {
        // Insert the income details into the income table
        $query = "INSERT INTO income (title, amount, reference, organization_id, summary_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            throw new Exception("Prepare error: " . $conn->error);
        }

        $stmt->bind_param('sdsii', $title, $revenue, $reference, $organization_id, $summary_id);

        if (!$stmt->execute()) {
            throw new Exception("Execution error: " . $stmt->error);
        }

        $stmt->close();

        // Update the income and balance in the organizations table
        $updateQuery = "UPDATE organizations 
                        SET income = income + ?, 
                            balance = balance + ? 
                        WHERE organization_id = ?";
        $updateStmt = $conn->prepare($updateQuery);

        if (!$updateStmt) {
            throw new Exception("Prepare error for update: " . $conn->error);
        }

        $updateStmt->bind_param('ddi', $revenue, $revenue, $organization_id);

        if (!$updateStmt->execute()) {
            throw new Exception("Execution error for update: " . $updateStmt->error);
        }

        // Insert a record into the income_history table
        $incomeHistoryQuery = "INSERT INTO income_history (organization_id, income, updated_at) 
                               VALUES (?, ?, NOW())";
        $incomeHistoryStmt = $conn->prepare($incomeHistoryQuery);

        if (!$incomeHistoryStmt) {
            throw new Exception("Prepare error for income history: " . $conn->error);
        }

        $incomeHistoryStmt->bind_param('id', $organization_id, $revenue);

        if (!$incomeHistoryStmt->execute()) {
            throw new Exception("Execution error for income history: " . $incomeHistoryStmt->error);
        }

        $incomeHistoryStmt->close();

        // Get the updated balance for the balance_history table
        $updatedBalanceQuery = "SELECT balance FROM organizations WHERE organization_id = ?";
        $balanceStmt = $conn->prepare($updatedBalanceQuery);

        if (!$balanceStmt) {
            throw new Exception("Prepare error for fetching balance: " . $conn->error);
        }

        $balanceStmt->bind_param('i', $organization_id);

        if (!$balanceStmt->execute()) {
            throw new Exception("Execution error for fetching balance: " . $balanceStmt->error);
        }

        $balanceResult = $balanceStmt->get_result();
        $balanceRow = $balanceResult->fetch_assoc();
        $updatedBalance = $balanceRow['balance'];

        $balanceStmt->close();

        // Insert a record into the balance_history table
        $historyQuery = "INSERT INTO balance_history (organization_id, balance, updated_at) 
                         VALUES (?, ?, NOW())";
        $historyStmt = $conn->prepare($historyQuery);

        if (!$historyStmt) {
            throw new Exception("Prepare error for balance history: " . $conn->error);
        }

        $historyStmt->bind_param('id', $organization_id, $updatedBalance);

        if (!$historyStmt->execute()) {
            throw new Exception("Execution error for balance history: " . $historyStmt->error);
        }

        $historyStmt->close();

        // Commit the transaction
        $conn->commit();

        // Set success response
        $data['success'] = true;
        $data['message'] = 'Income added and organization balance updated successfully!';
    } catch (Exception $e) {
        // Rollback transaction in case of an error
        $conn->rollback();

        // Set error response
        $data['success'] = false;
        $data['errors'] = ['error' => $e->getMessage()];
    }

    echo json_encode($data);
}
?>