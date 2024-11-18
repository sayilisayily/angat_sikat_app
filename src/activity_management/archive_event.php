<?php
include 'connection.php';

$data = [];

if (isset($_POST['event_id'])) {
    $event_id = intval($_POST['event_id']);
    
    // Update the archived status of the event
    $query = "UPDATE events SET archived = 1 WHERE event_id = $event_id";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Event archived successfully!';
    } else {
        $data['success'] = false;
        $data['message'] = 'Failed to archive event.';
    }
} else {
    $data['success'] = false;
    $data['message'] = 'Invalid event ID.';
}

echo json_encode($data);
?>
