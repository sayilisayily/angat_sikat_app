<?php
include 'connection.php';

if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];

    $query = "SELECT * FROM events WHERE event_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $event]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Event not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No event ID provided.']);
}
?>
