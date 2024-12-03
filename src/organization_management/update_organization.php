<?php
include '../connection.php';
include '../session_check.php';

$errors = [];
$data = [];

// Validate organization ID (ensure it exists)
if (empty($_POST['organization_id'])) {
    $errors['organization_id'] = 'Organization ID is required.';
} else {
    $organization_id = intval($_POST['organization_id']);
}

// Validate organization name
if (empty($_POST['organization_name'])) {
    $errors['organization_name'] = 'Organization name is required.';
} else {
    $organization_name = mysqli_real_escape_string($conn, $_POST['organization_name']);

    // Check if an organization with the same name already exists (other than the current one)
    $query = "SELECT * FROM organizations WHERE organization_name = '$organization_name' AND organization_id != $organization_id";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $errors['organization_name'] = 'An organization with this name already exists.';
    }
}

// Validate and upload organization logo (if uploaded)
if (isset($_FILES['organization_logo']) && $_FILES['organization_logo']['error'] == 0) {
    $file_tmp = $_FILES['organization_logo']['tmp_name'];
    $file_name = $_FILES['organization_logo']['name'];
    
    if (!empty($file_name)) {
        $upload_dir = 'uploads/';
        
        // Ensure uploads directory exists
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        // Check the file type (e.g., JPEG, PNG, GIF)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($file_tmp);

        if (!in_array($file_type, $allowed_types)) {
            $errors['organization_logo'] = 'Invalid file type.';
        } else {
            // Move uploaded file to the directory
            $file_path = $upload_dir . basename($file_name);
            if (move_uploaded_file($file_tmp, $file_path)) {
                $organization_logo = $file_name; // File uploaded successfully
            } else {
                $errors['organization_logo'] = 'Error moving the uploaded file.';
            }
        }
    } else {
        $organization_logo = $_POST['existing_logo']; // Use existing logo if no new file is uploaded
    }
} else {
    $organization_logo = $_POST['existing_logo']; // Use existing logo if no new file is uploaded
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

// Validate organization color (if provided)
$organization_color = isset($_POST['organization_color']) ? $_POST['organization_color'] : null;
if (!empty($organization_color) && !preg_match('/^#[a-fA-F0-9]{6}$/', $organization_color)) {
    $errors['organization_color'] = 'Invalid color format. Please use a valid hex color code.';
}

// If there are errors, return them
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Use prepared statement to update the organization in the database
    $query = "UPDATE organizations SET 
              organization_name = ?, 
              organization_logo = ?, 
              organization_members = ?, 
              organization_status = ?, 
              organization_color = ? 
              WHERE organization_id = ?";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssisss', $organization_name, $organization_logo, $organization_members, $organization_status, $organization_color, $organization_id);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'Organization updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update organization in the database.'];
        }

        $stmt->close();
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to prepare update statement.'];
    }
}

// Output response as JSON
echo json_encode($data);
?>
