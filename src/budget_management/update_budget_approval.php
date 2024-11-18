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
        echo json_encode(['success' => true, 'message' => 'Budget approval updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update budget approval']);
    }
}
?>
