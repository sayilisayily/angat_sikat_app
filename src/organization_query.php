<?php

$organization_id =  $_SESSION['organization_id'];
// Fetch organization details
$org_query = "SELECT * FROM organizations WHERE organization_id = ?";
$stmt = $conn->prepare($org_query);
$stmt->bind_param("i", $organization_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $organization = $result->fetch_assoc();
    // Store organization data in variables
    $org_name = $organization['organization_name'];
    $org_logo = $organization['organization_logo'];
    $org_members = $organization['organization_members'];
    $org_status = $organization['organization_status'];
    $beginning_balance = $organization['beginning_balance'];
    $cash_on_bank = $organization['cash_on_bank'];
    $cash_on_hand = $organization['cash_on_hand'];
    $balance = $organization['balance']; 
    $income = $organization['income']; 
    $expense = $organization['expense']; 
} else {
    // Default values if organization is not found
    $org_name = "Organization not found";
    $org_logo = "default_logo.png"; // Default logo if none is found
    $org_members = "0";
    $org_status = "Unknown";
}

$stmt->close();
?>