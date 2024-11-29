<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $summary_item_id = $_POST['summary_item_id'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $amount = $_POST['amount'];
    $reference = isset($_FILES['reference']['name']) ? $_FILES['reference']['name'] : '';

    // Check if a new file is uploaded
    if (!empty($reference)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($reference);

        if (move_uploaded_file($_FILES['reference']['tmp_name'], $target_file)) {
            // Update query with the new reference file
            $query = "UPDATE maintenance_summary_items SET description = ?, quantity = ?, unit = ?, amount = ?, reference = ? WHERE summary_item_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sisdsi", $description, $quantity, $unit, $amount, $reference, $summary_item_id);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
            exit;
        }
    } else {
        // Update query without the reference file
        $query = "UPDATE maintenance_summary_items SET description = ?, quantity = ?, unit = ?, amount = ? WHERE summary_item_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sisdi", $description, $quantity, $unit, $amount, $summary_item_id);
    }

    // Execute the query
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Summary item updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update summary item']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
