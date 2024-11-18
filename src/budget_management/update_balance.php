<?php
// Include the database connection file
include 'connection.php';

// Handle modal submissions for updating values
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = [
        'success' => false,
        'message' => ""
    ];

    // Set the organization ID dynamically based on user session or login
    $org_id = 1; 

    // Fetch current beginning_balance
    $query = "SELECT beginning_balance FROM organizations WHERE organization_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $org_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $beginning_balance = $row['beginning_balance'];

    // Calculate total expenses
    $expenses_query = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE organization_id = ?";
    $expenses_stmt = $conn->prepare($expenses_query);
    $expenses_stmt->bind_param('i', $org_id);
    $expenses_stmt->execute();
    $expenses_result = $expenses_stmt->get_result();
    $expenses_row = $expenses_result->fetch_assoc();
    $total_expenses = $expenses_row['total_expenses'] ?? 0; // Default to 0 if no expenses

    // Calculate current balance
    $current_balance = $beginning_balance - $total_expenses;

    // Check for beginning balance submission
    if (isset($_POST['beginning_balance'])) {
        $beginning_balance = $_POST['beginning_balance'];
        
        // Update the Beginning Balance
        $update_query = "UPDATE organizations SET beginning_balance = ? WHERE organization_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param('di', $beginning_balance, $org_id);
        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] .= "Beginning Balance updated successfully! ";
        } else {
            $response['message'] .= "Error updating Beginning Balance: " . mysqli_error($conn);
        }
    }

    // Check for cash on bank submission
    if (isset($_POST['cash_on_bank'])) {
        $cash_on_bank = $_POST['cash_on_bank'];
        
        // Validate cash_on_bank
        if ($cash_on_bank > $current_balance) {
            $response['message'] .= "Cash on Bank cannot exceed the balance! ";
        } else {
            // Update the Cash on Bank
            $update_query = "UPDATE organizations SET cash_on_bank = ? WHERE organization_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('di', $cash_on_bank, $org_id);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] .= "Cash on Bank updated successfully! ";
            } else {
                $response['message'] .= "Error updating Cash on Bank: " . mysqli_error($conn);
            }
        }
    }

    // Check for cash on hand submission
    if (isset($_POST['cash_on_hand'])) {
        $cash_on_hand = $_POST['cash_on_hand'];
        
        // Validate cash_on_hand
        if ($cash_on_hand > $current_balance) {
            $response['message'] .= "Cash on Hand cannot exceed the balance! ";
        } else {
            // Update the Cash on Hand
            $update_query = "UPDATE organizations SET cash_on_hand = ? WHERE organization_id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param('di', $cash_on_hand, $org_id);
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] .= "Cash on Hand updated successfully! ";
            } else {
                $response['message'] .= "Error updating Cash on Hand: " . mysqli_error($conn);
            }
        }
    }

    // Calculate new balance
    $new_balance = $beginning_balance - $total_expenses;

    // Update the balance in the database
    $update_balance_query = "UPDATE organizations SET balance = ? WHERE organization_id = ?";
    $stmt = $conn->prepare($update_balance_query);
    $stmt->bind_param('di', $new_balance, $org_id);
    if ($stmt->execute()) {
        $response['success'] = true; // Update success
        $response['message'] .= "Balance updated successfully!";
    } else {
        $response['message'] .= "Error updating Balance: " . mysqli_error($conn);
    }

    // Send the response as JSON
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
