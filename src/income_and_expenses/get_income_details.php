<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $income_id = $_POST['income_id'];

    // Debugging to ensure data is received
    error_log("Received income_id: " . $income_id);

    $query = "SELECT * FROM income WHERE income_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $income_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $data]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Income not found.']);
    }
}
?>
