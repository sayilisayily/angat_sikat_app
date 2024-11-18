<?php
include 'connection.php';

if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $event_name = $_POST['title'];
    $event_venue = $_POST['event_venue'];
    $event_start_date = $_POST['event_start_date'];
    $event_end_date = $_POST['event_end_date'];
    $event_type = $_POST['event_type'];
    $accomplishment_status = isset($_POST['accomplishment_status']) ? 1 : 0;

    $sql = "UPDATE events SET 
            title = '$event_name', 
            event_venue = '$event_venue', 
            event_start_date = '$event_start_date', 
            event_end_date = '$event_end_date', 
            event_type = '$event_type',
            accomplishment_status = $accomplishment_status
            WHERE event_id = $event_id";

    if ($conn->query($sql) === TRUE) {
        echo "Event updated successfully";
    } else {
        echo "Error updating event: " . $conn->error;
    }

    $conn->close();
}
?>
