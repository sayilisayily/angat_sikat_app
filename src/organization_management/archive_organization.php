<?php
include 'connection.php';

$data = [];

if (isset($_POST['organization_id'])) {
    $organization_id = intval($_POST['organization_id']);
    
    // Update the archived status of the organization
    $query = "UPDATE organizations SET organization_status = 'Archived' WHERE organization_id = $organization_id";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Organization archived successfully!';
    } else {
        $data['success'] = false;
        $data['message'] = 'Failed to archive organization.';
    }
} else {
    $data['success'] = false;
    $data['message'] = 'Invalid organization ID.';
}

echo json_encode($data);
?>
