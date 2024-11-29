<?php
include 'connection.php';

if (isset($_POST['maintenance_id'])) {
    $maintenance_id = $_POST['maintenance_id']; // Correct variable name

    $sql = "SELECT * FROM maintenance WHERE maintenance_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $maintenance_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $maintenance = $result->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $maintenance]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Maintenance not found']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Query preparation failed: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No Maintenance ID provided']);
}


?>
