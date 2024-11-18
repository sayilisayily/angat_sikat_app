

<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    //echo '1';
    $purchase_id = $_POST['purchase_id'];
    //echo '1';
    $completion_status = $_POST['completion_status'];

    $stmt = $conn->prepare("UPDATE purchases SET completion_status = ? WHERE purchase_id = ?");
    $stmt->bind_param('ii', $completion_status, $purchase_id);

    if ($stmt->execute()) {
        echo "Success";
    } else {
        echo "Error";
    }

    $stmt->close();
    $conn->close();
}
?>

