<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $expense_id = $_POST['expense_id'] ?? null;

    if (!$expense_id) {
        echo json_encode(['success' => false, 'message' => 'No expense_id received.']);
        exit;
    }

    // Debug log
    error_log("Received expense_id: " . $expense_id);

    $query = "SELECT * FROM expenses WHERE expense_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $expense_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No expense found.']);
    }
}
?>
