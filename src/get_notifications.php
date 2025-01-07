<?php
include 'connection.php'; // Ensure your DB connection file is included
include 'session_check.php';
// Fetch notifications from the database
$sql = "SELECT id, message, created_at, is_read FROM notifications WHERE is_read=0 AND recipient_id=$user_id ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);

$notifications = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
}

// Return notifications as JSON
header('Content-Type: application/json');
echo json_encode($notifications);
?>
