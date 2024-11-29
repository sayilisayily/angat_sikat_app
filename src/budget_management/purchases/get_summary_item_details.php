<?php
include '../connection.php'; // Database connection

if (isset($_POST['item_id'])) {
    $item_id = $_POST['item_id'];

    // Prepare the SQL query
    $query = "SELECT * FROM purchase_summary_items WHERE summary_item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the item exists
    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();

        // Return the item details in a JSON response
        echo json_encode([
            'success' => true,
            'data' => $item
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Item not found'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'No item ID provided'
    ]);
}

?>
