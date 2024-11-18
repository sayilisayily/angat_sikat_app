

<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_id = $_POST['event_id'];
    $accomplishment_status = $_POST['accomplishment_status'];

    $stmt = $conn->prepare("UPDATE events SET accomplishment_status = ? WHERE event_id = ?");
    $stmt->bind_param('ii', $accomplishment_status, $event_id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error";
    }

    $stmt->close();
    $conn->close();
}
?>

