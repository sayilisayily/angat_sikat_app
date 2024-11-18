<?php
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = $_POST['event_id'];
    $accomplishment_status = $_POST['accomplishment_status'];

    // Update the accomplishment status in the database
    $query = "UPDATE events SET accomplishment_status = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $accomplishment_status, $event_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}
?>
