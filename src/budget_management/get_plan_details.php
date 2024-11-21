<?php
include 'connection.php';

if (isset($_POST['plan_id'])) {
    $plan_id = $_POST['plan_id'];

    $query = "SELECT * FROM financial_plan WHERE plan_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $plan_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $plan = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $plan]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Plan not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No plan ID provided.']);
}
?>
