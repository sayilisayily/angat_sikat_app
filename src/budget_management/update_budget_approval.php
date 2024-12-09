<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $approval_id = $_POST['approval_id'];
    $title = $_POST['title'];
    $attachment = $_FILES['attachment']['name'];

    // Check if a new file is uploaded
    if (!empty($attachment)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($attachment);
        
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            // Update query with the new attachment
            $query = "UPDATE budget_approvals SET title = ?, attachment = ? WHERE approval_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $title, $attachment, $approval_id);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
            exit;
        }
    } else {
        // Update only the title if no new attachment is provided
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
                $insert_notification_query_admin = "INSERT INTO notifications (recipient_id, message, is_read, created_at) 
                                                     VALUES ($admin_id, '$notification_message', 0, NOW())";

                if (!mysqli_query($conn, $insert_notification_query_admin)) {
                    error_log("Admin Notification Error: " . mysqli_error($conn));
                    error_log("Query: " . $insert_notification_query_admin);
                }
            }
        } else {
            error_log("Admin query failed or returned no results: " . mysqli_error($conn));
        }

        echo json_encode(['success' => true, 'message' => 'Budget approval updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update budget approval']);
    }
}
?>
