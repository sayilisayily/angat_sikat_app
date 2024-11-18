<?php
include 'connection.php';

if (isset($_POST['expense_id'])) {
    $expense_id = $_POST['expense_id'];

    $query = "SELECT * FROM expenses WHERE expense_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $expense_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $expense = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $expense]);
    } else {
        echo json_encode(['success' => false, 'message' => 'expense not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No expense ID provided.']);
}
?>
