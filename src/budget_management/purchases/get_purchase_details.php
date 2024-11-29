<?php
include '../connection.php';

if (isset($_POST['purchase_id'])) {
    $purchase_id = $_POST['purchase_id']; // Correct variable name

    $sql = "SELECT * FROM purchases WHERE purchase_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $purchase_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $purchase = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $purchase]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Purchase not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No Purchase ID provided']);
}


?>
