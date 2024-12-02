<?php
include '../connection.php';
include '../session_check.php';

$errors = [];
$data = [];

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
        $errors['organization_logo'] = 'Uploaded file name is empty.';
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
    // Use prepared statement to insert organization into the database
    $query = "INSERT INTO organizations (organization_name, organization_logo, organization_members, organization_status, organization_color) 
              VALUES (?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param('ssiss', $organization_name, $organization_logo, $organization_members, $organization_status, $organization_color);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'Organization added successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to add organization to the database.'];
        }

        $stmt->close();
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to prepare insert statement.'];
    }
}

// Output response as JSON
echo json_encode($data);
?>
