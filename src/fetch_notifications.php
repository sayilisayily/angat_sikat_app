<?php
include 'connection.php';
include 'session_check.php'; // Ensure the user is authenticated

// Get the logged-in user's ID
$admin_id = $_SESSION['user_id']; // Assuming `user_id` is stored in the session

// Query to fetch unread notifications for the admin
$query = "SELECT id, message, is_read, created_at 
          FROM notifications 
          WHERE recipient_id = $admin_id 
          ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);

$notifications = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notifications[] = $row;
    }
}

echo json_encode($notifications);
?>
