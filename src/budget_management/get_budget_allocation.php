<?php
include 'connection.php';

$response = ['success' => false];

// Validate allocation_id
if (isset($_POST['allocation_id']) && is_numeric($_POST['allocation_id'])) {
    $allocation_id = (int) $_POST['allocation_id'];

    // Query to fetch allocation data
    $query = "SELECT allocation_id, allocated_budget, organization_id, category FROM budget_allocation WHERE allocation_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $allocation_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $response['success'] = true;
        $response['allocation_id'] = $row['allocation_id'];
        $response['organization_id'] = $row['organization_id'];
        $response['allocated_budget'] = $row['allocated_budget'];
        $response['category'] = $row['category'];
    } else {
        $response['message'] = 'No budget allocation found for the provided ID.';
    }

    $stmt->close();
} else {
    $response['message'] = 'Invalid allocation ID.';
}

echo json_encode($response);
?>
