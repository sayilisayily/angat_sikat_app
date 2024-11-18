<?php
// Include database connection
include('connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate organization name
    if (empty($_POST['organization_name'])) {
        $errors[] = 'Organization name is required.';
    } else {
        $organization_name = mysqli_real_escape_string($conn, $_POST['organization_name']);
        $organization_id = mysqli_real_escape_string($conn, $_POST['organization_id']); // Get the organization ID
    }

    // Validate organization members (optional, can be left empty)
    if (empty($_POST['organization_members'])) {
        $organization_members = null; // Optional field, can be empty
    } else {
        $organization_members = mysqli_real_escape_string($conn, $_POST['organization_members']);
    }

    // Validate organization status (can be active or inactive)
    if (empty($_POST['organization_status'])) {
        $errors[] = 'Organization status is required.';
    } else {
        $organization_status = mysqli_real_escape_string($conn, $_POST['organization_status']);
    }

    // Handle organization logo (if a file is uploaded)
    if ($_FILES['organization_logo']['error'] === 0) {
        $logo_name = $_FILES['organization_logo']['name'];
        $logo_tmp_name = $_FILES['organization_logo']['tmp_name'];
        $logo_size = $_FILES['organization_logo']['size'];
        $logo_error = $_FILES['organization_logo']['error'];

        // Validate file size and type
        if ($logo_error === 0 && $logo_size <= 5000000 && in_array(pathinfo($logo_name, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif'])) {
            $logo_new_name = uniqid('', true) . "." . pathinfo($logo_name, PATHINFO_EXTENSION);
            $logo_upload_path = "uploads/" . $logo_new_name;
            if (!move_uploaded_file($logo_tmp_name, $logo_upload_path)) {
                $errors[] = 'Failed to upload logo image.';
            } else {
                $organization_logo = $logo_new_name; // Set the new logo name
            }
        } else {
            $errors[] = 'Invalid logo file type or size.';
        }
    } else {
        $organization_logo = null; // No new logo uploaded, retain old logo
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Prepare and execute the update query
        $query = "UPDATE organizations SET 
                    organization_name = '$organization_name', 
                    organization_logo = '$organization_logo', 
                    organization_members = '$organization_members', 
                    organization_status = '$organization_status' 
                  WHERE organization_id = '$organization_id'";

        if (mysqli_query($conn, $query)) {
            $data['success'] = true;
            $data['message'] = 'Organization updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update organization in the database.'];
        }
    }
}

echo json_encode($data);
?>
