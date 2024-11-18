<?php
include 'connection.php'; // Include database connection

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['allocation_id'])) {
    $allocation_id = $_POST['allocation_id'];

    // Query to fetch the budget allocation details
    $query = "SELECT * FROM budget_allocation WHERE allocation_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $allocation_id);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            $response['success'] = true;
            $response['allocated_budget'] = $row['allocated_budget'];
        } else {
            $response['success'] = false;
            $response['message'] = 'No data found';
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Query failed';
    }

    $stmt->close();
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request';
}

echo json_encode($response); // Return response in JSON format
?>
