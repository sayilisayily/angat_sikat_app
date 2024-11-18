<?php
// Include database connection
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $organization_id = $_POST['organization_id'];

    // Fetch cash on hand for the organization
    $query = "SELECT cash_on_hand FROM organizations WHERE organization_id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param('i', $organization_id);
        $stmt->execute();
        $stmt->bind_result($cash_on_hand);
        $stmt->fetch();
        
        // Return the cash on hand value in JSON format
        echo json_encode(['success' => true, 'cash_on_hand' => $cash_on_hand]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error fetching Cash on Hand.']);
    }

    $stmt->close();
    $conn->close();
}
?>
