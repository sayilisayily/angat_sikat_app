<?php
// Include the database connection file
include 'connection.php';

// Initialize response array
$response = ['success' => false, 'message' => 'An error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['item_id'])) {
        $item_id = $_POST['item_id'];
    
        // Prepare and execute the delete query
        $query = "DELETE FROM event_items WHERE item_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $item_id);
                
        if ($stmt = $conn->prepare($query)) {
            // Bind parameters and execute the statement
            $stmt->bind_param("i", $item_id);
            
            if ($stmt->execute()) {
                // Check if the item was successfully deleted
                if ($stmt->affected_rows > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Item deleted successfully.';
                } else {
                    $response['message'] = 'Item not found or already deleted.';
                }
            } else {
                $response['message'] = 'Failed to execute the query.';
            }
            // Close the statement
            $stmt->close();
        } else {
            $response['message'] = 'Failed to prepare the SQL statement.';
        }
    } else {
        $response['message'] = 'Invalid item or event ID.';
    }
} else {
    $response['message'] = 'Invalid request method.';
}

// Return the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
