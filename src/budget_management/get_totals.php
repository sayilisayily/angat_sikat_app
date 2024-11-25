<?php

include 'connection.php';
include '../session_check.php'; // Assuming session_check.php sets the organization_id in the session

header('Content-Type: application/json');

$response = [];

// Check if organization_id exists in the session
if (!isset($_SESSION['organization_id']) || !is_numeric($_SESSION['organization_id'])) {
    echo json_encode(['error' => 'Invalid organization ID.']);
    exit;
}

$organization_id = (int) $_SESSION['organization_id']; // Cast to integer to prevent SQL injection

// Query to fetch the total planned amount for each category
$query = "
    SELECT category, SUM(amount) AS total_amount 
    FROM financial_plan 
    WHERE organization_id = ? AND type = 'Expense' 
    GROUP BY category
";

// Prepare the statement
$stmt = $conn->prepare($query);
if ($stmt) {
    $stmt->bind_param('i', $organization_id); // Bind the organization_id parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $response[] = [
                'category' => $row['category'],
                'total_amount' => (float) $row['total_amount'],
            ];
        }
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'Database query failed.']);
    exit;
}

// Send the JSON response
echo json_encode($response);

?>
