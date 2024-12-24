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

    // Insert the expense details into the database
    $query = "INSERT INTO expenses (title, amount, category, reference, organization_id) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        $data['success'] = false;
        $data['errors'] = ['prepare_error' => $conn->error];
        echo json_encode($data);
        exit;
    }

    $stmt->bind_param('sdssi', $title, $total_amount, $category, $reference, $organization_id);

    if ($stmt->execute()) {
        // Update the budget_allocation table's total_spent field
        $update_query = "
            UPDATE budget_allocation 
            SET total_spent = total_spent + ?
            WHERE category = ? AND organization_id = ?
        ";
        $update_stmt = $conn->prepare($update_query);

        if ($update_stmt) {
            $update_stmt->bind_param('dsi', $total_amount, $category, $organization_id);

            if ($update_stmt->execute()) {
                $data['success'] = true;
                $data['message'] = 'Expense added and budget allocation updated successfully!';
            } else {
                $data['success'] = false;
                $data['errors'] = ['update_error' => $update_stmt->error];
            }

            $update_stmt->close();
        } else {
            $data['success'] = false;
            $data['errors'] = ['update_prepare_error' => $conn->error];
        }
    } else {
        $data['success'] = false;
        $data['errors'] = ['execution_error' => $stmt->error];
    }

    $stmt->close();
    echo json_encode($data);

}
?>
