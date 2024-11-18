<?php

include '../connection.php';
include '../session_check.php';

$errors = [];
$data = [];
print_r($_FILES);

// Validate organization name
if (empty($_POST['organization_name'])) {
    $errors['organization_name'] = 'Organization name is required.';
} else {
    $organization_name = mysqli_real_escape_string($conn, $_POST['organization_name']);

    // Check if an organization with the same name already exists
    $query = "SELECT * FROM organizations WHERE organization_name = '$organization_name'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $errors['organization_name'] = 'An organization with this name already exists.';
    }
}

// Validate and upload organization logo
if (isset($_FILES['organization_logo']) && $_FILES['organization_logo']['error'] == 0) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 2 * 1024 * 1024; // 2 MB

    $file_type = $_FILES['organization_logo']['type'];
    $file_size = $_FILES['organization_logo']['size'];
    $file_tmp = $_FILES['organization_logo']['tmp_name'];
    $file_name = basename($_FILES['organization_logo']['name']);
    $upload_dir = 'uploads/';
    $upload_file = $upload_dir . uniqid() . '_' . $file_name;

    // Check file type
    if (!in_array($file_type, $allowed_types)) {
        $errors['organization_logo'] = 'Only JPEG, PNG, and GIF files are allowed.';
    }

    // Check file size
    if ($file_size > $max_size) {
        $errors['organization_logo'] = 'Logo file size must be under 2MB.';
    }

    // Move file to uploads directory if no errors
    if (empty($errors['organization_logo']) && move_uploaded_file($file_tmp, $upload_file)) {
        $organization_logo = mysqli_real_escape_string($conn, $upload_file);
    } else {
        $errors['organization_logo'] = 'Failed to upload logo.';
    }
} else {
    $errors['organization_logo'] = 'Organization logo is required.';
}

// Validate organization members
if (empty($_POST['organization_members']) || !is_numeric($_POST['organization_members'])) {
    $errors['organization_members'] = 'Valid number of members is required.';
} else {
    $organization_members = intval($_POST['organization_members']);
}

// Validate organization status
if (empty($_POST['organization_status'])) {
    $errors['organization_status'] = 'Organization status is required.';
} else {
    $organization_status = mysqli_real_escape_string($conn, $_POST['organization_status']);
}

// If there are errors, return them
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Insert organization into the database
    $query = "INSERT INTO organizations (organization_name, organization_logo, organization_members, organization_status) 
              VALUES ('$organization_name', '$organization_logo', $organization_members, '$organization_status')";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Organization added successfully!';
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to add organization to the database.'];
    }
}

// Output response as JSON
echo json_encode($data);

?>
