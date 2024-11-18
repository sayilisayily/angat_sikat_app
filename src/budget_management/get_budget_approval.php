<?php
include 'connection.php';

if (isset($_POST['approval_id'])) {
    $approval_id = $_POST['approval_id'];

    // Fetch the specific budget approval details based on the approval_id
    $query = "SELECT title, category, attachment FROM budget_approvals WHERE approval_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $approval_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Send back the response in JSON format for the modal to populate the form
        echo json_encode([
            'success' => true,
            'title' => $row['title'],
            'category' => $row['category'],
            'attachment' => $row['attachment']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No record found']);
    }
}
?>
