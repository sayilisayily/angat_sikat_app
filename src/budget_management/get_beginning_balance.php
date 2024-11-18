<?php
// Include database connection
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $organization_id = $_POST['organization_id'];

    // Fetch beginning balance for the organization
    $query = "SELECT beginning_balance FROM organizations WHERE organization_id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param('i', $organization_id);
        $stmt->execute();
        $stmt->bind_result($beginning_balance);
        $stmt->fetch();
        
        // Return the balance in JSON format
        echo json_encode(['success' => true, 'beginning_balance' => $beginning_balance]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error fetching balance.']);
    }

    $stmt->close();
    $conn->close();
}
?>
