<?php
include 'connection.php'; // Database connection

if (isset($_POST['purchase_id'])) {
    $item_id = $_POST['purchase_id'];

    // Prepare the SQL query
    $sql = "SELECT * FROM purchases WHERE purchase_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $purchase_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the item exists
    if ($result->num_rows > 0) {
        $purchase = $result->fetch_assoc();

        // Return the item details in a JSON response
        echo json_encode([
            'success' => true,
            'data' => $purchase
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Purchase not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No Purchase ID provided'
    ]);
}

?>
