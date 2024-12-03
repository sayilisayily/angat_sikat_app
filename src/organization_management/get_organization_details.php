<?php
include 'connection.php';

if (isset($_POST['organization_id'])) {
    $organization_id = $_POST['organization_id'];

    $query = "SELECT * FROM organizations WHERE organization_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $organization_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $organization = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $organization]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Organization not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No organization ID provided.']);
}
?>
