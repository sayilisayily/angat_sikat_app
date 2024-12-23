<?php
include '../connection.php';
include '../session_check.php';

$errors = [];

// Validate organization ID
if (empty($_POST['organization_id'])) {
    $errors['organization_id'] = 'Organization ID is required.';
} else {
    $organization_id = intval($_POST['organization_id']);
}

// Validate organization name
if (empty($_POST['organization_name'])) {
    $errors['organization_name'] = 'Organization name is required.';
} else {
    $organization_name = trim($_POST['organization_name']);
}

// Check for duplicate organization name
$query = "SELECT * FROM organizations WHERE organization_name = ? AND organization_id != ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('si', $organization_name, $organization_id);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $errors['organization_name'] = 'An organization with this name already exists.';
}
$stmt->close();

// Validate other fields
$organization_members = isset($_POST['organization_members']) ? intval($_POST['organization_members']) : 0;
$organization_status = isset($_POST['organization_status']) ? trim($_POST['organization_status']) : '';
$organization_color = isset($_POST['organization_color']) ? trim($_POST['organization_color']) : '#000000';

// File upload logic
$organization_logo = $_POST['existing_logo'];
if (isset($_FILES['organization_logo']) && $_FILES['organization_logo']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $file_tmp = $_FILES['organization_logo']['tmp_name'];
    $file_name = basename($_FILES['organization_logo']['name']);
    $file_type = mime_content_type($file_tmp);

    if (in_array($file_type, $allowed_types)) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($file_tmp, $file_path)) {
            $organization_logo = $file_name;
        } else {
            $errors['organization_logo'] = 'Error uploading the file.';
        }
    } else {
        $errors['organization_logo'] = 'Invalid file type.';
    }
}

// If no errors, update the database
if (empty($errors)) {
    $query = "UPDATE organizations SET 
              organization_name = ?, 
              organization_logo = ?, 
              organization_members = ?, 
              organization_status = ?, 
              organization_color = ? 
              WHERE organization_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        'ssissi',
        $organization_name,
        $organization_logo,
        $organization_members,
        $organization_status,
        $organization_color,
        $organization_id
    );

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Organization updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed.']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'errors' => $errors]);
}
?>
