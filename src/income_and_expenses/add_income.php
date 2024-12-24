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

    // Input Validation
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }

    if (empty($revenue) || $revenue <= 0) {
        $errors[] = 'Valid revenue amount is required.';
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

    // Insert the income details into the database
    $query = "INSERT INTO income (title, amount, reference, organization_id) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        $data['success'] = false;
        $data['errors'] = ['prepare_error' => $conn->error];
        echo json_encode($data);
        exit;
    }

    $stmt->bind_param('sdsi', $title, $revenue, $reference, $organization_id);

    if ($stmt->execute()) {
        $data['success'] = true;
        $data['message'] = 'Income added successfully!';
    } else {
        $data['success'] = false;
        $data['errors'] = ['execution_error' => $stmt->error];
    }

    $stmt->close();
    echo json_encode($data);
}
?>
