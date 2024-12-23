<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $approval_id = $_POST['approval_id'];
    $title = $_POST['title'];
    $attachment = $_FILES['attachment'];

    // File upload handling
    $uploaded_file = '';
    if (isset($attachment) && $attachment['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($attachment['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $attachment['tmp_name'];
            $file_name = $attachment['name'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed_extensions = ['doc', 'docx', 'xls', 'xlsx', 'pdf'];

            if (!in_array($file_extension, $allowed_extensions)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type. Only DOC, DOCX, XLS, XLSX, and PDF are allowed.']);
                exit;
            }

            $upload_dir = 'uploads/';
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
        $query = "UPDATE budget_approvals SET title = ?, attachment = ? WHERE approval_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $title, $uploaded_file, $approval_id);
    } else {
        // If no new file is uploaded, update only the title
        $query = "UPDATE budget_approvals SET title = ? WHERE approval_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $title, $approval_id);
    }

    if ($stmt->execute()) {
        // Send notification to the admin about the update
        $notification_message = "Budget approval request for '$title' has been updated.";

        // Query to get all admin users
        $admin_query = "SELECT user_id FROM users WHERE role = 'admin'";
        $admin_result = mysqli_query($conn, $admin_query);

        if ($admin_result && mysqli_num_rows($admin_result) > 0) {
            while ($row = mysqli_fetch_assoc($admin_result)) {
                $admin_id = $row['user_id'];

                // Insert notification for the admin
                $insert_notification_query = "INSERT INTO notifications (recipient_id, message, is_read, created_at) 
                                              VALUES ($admin_id, '$notification_message', 0, NOW())";

                if (!mysqli_query($conn, $insert_notification_query)) {
                    error_log("Admin Notification Error: " . mysqli_error($conn));
                    error_log("Query: " . $insert_notification_query);
                }
            }
        } else {
            error_log("Admin query failed or returned no results: " . mysqli_error($conn));
        }

        echo json_encode(['success' => true, 'message' => 'Budget approval updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update budget approval.']);
    }
}
?>
