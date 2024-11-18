<?php
include 'connection.php';

$sql = "SELECT event_id, title, event_start_date, event_end_date FROM events WHERE accomplishment_status = 1";
$result = $conn->query($sql);

$events = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $events[] = array(
            'id' => $row['event_id'],
            'title' => $row['title'],
            'start' => $row['event_start_date'],
            'end' => $row['event_end_date']
        );
    }
}

echo json_encode($events);

$conn->close();
?>
