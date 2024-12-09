<?php
$notifications_query = "SELECT * FROM notifications WHERE recipient_id = $admin_id AND is_read = 0 ORDER BY created_at DESC";
$notifications_result = mysqli_query($conn, $notifications_query);

// Display the notifications
while ($notification = mysqli_fetch_assoc($notifications_result)) {
    echo "<li>" . htmlspecialchars($notification['message']) . "</li>";
}

?>