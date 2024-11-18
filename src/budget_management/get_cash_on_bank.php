<?php
// Include database connection
include('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $organization_id = $_POST['organization_id'];

    // Fetch cash on bank for the organization
    $query = "SELECT cash_on_bank FROM organizations WHERE organization_id = ?";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param('i', $organization_id);
        $stmt->execute();
        $stmt->bind_result($cash_on_bank);
        $stmt->fetch();
        
        // Return the cash on bank value in JSON format
        echo json_encode(['success' => true, 'cash_on_bank' => $cash_on_bank]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error fetching Cash on Bank.']);
    }

    $stmt->close();
    $conn->close();
}
?>
