<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $income_id = $_POST['income_id'];
    $reference = $_FILES['reference'];

    // File upload handling
    $uploaded_file = '';
    if (isset($reference) && $reference['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($reference['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $reference['tmp_name'];
            $file_name = $reference['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['doc', 'docx', 'xls', 'xlsx', 'pdf'];

            if (!in_array($file_extension, $allowed_extensions)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only DOC, DOCX, XLS, XLSX, and PDF are allowed.']);
                exit;
            }

            $upload_dir = 'uploads/references/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_path = $upload_dir . basename($file_name);
            if (move_uploaded_file($file_tmp, $file_path)) {
                $uploaded_file = $file_name;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move the uploaded file.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'File upload error.']);
            exit;
        }
    }

    // Update query
    if (!empty($uploaded_file)) {
        $query = "UPDATE income SET reference = ? WHERE income_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $uploaded_file, $income_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Income reference updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update income reference.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded.']);
    }
}
?>
