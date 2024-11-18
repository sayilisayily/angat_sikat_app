<?php

include 'connection.php';
include '../session_check.php'; 


$errors = [];
$data = [];

if (empty($_POST['title'])) {
    $errors['title'] = 'Event title is required.';
} else {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    
    // Check if the event with the same title already exists
    $query = "SELECT * FROM budget_approvals WHERE title = '$title'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $errors['title'] = 'A request with this title already exists.';
    }
}

if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors; 
} else {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Fetch the selected title and attachment from the form submission
        $title = $_POST['title'];
        $status = 'Pending'; // Default status
        
        // File upload handling
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
            $file_tmp = $_FILES['attachment']['tmp_name'];
            $file_name = $_FILES['attachment']['name'];
            
            if (!empty($file_name)) {
                $upload_dir = 'uploads/';
                
                // Ensure uploads directory exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                // Move uploaded file to the directory
                $file_path = $upload_dir . basename($file_name);
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $attachment = $file_name; // File uploaded successfully
                } else {
                    $errors['attachment'] = 'Error moving the uploaded file.';
                }
            } else {
                $errors['attachment'] = 'Uploaded file name is empty.';
            }
        } else {
            $attachment = ''; // No file uploaded or error in file upload
        }        
        
        // Check in events table
        $event_query = "SELECT title FROM events WHERE title = '$title'";
        $event_result = mysqli_query($conn, $event_query);
        
        if (mysqli_num_rows($event_result) > 0) {
            $category = 'Events';
        }
    
        // Check in purchases table
        $purchase_query = "SELECT title FROM purchases WHERE title = '$title'";
        $purchase_result = mysqli_query($conn, $purchase_query);
        
        if (mysqli_num_rows($purchase_result) > 0) {
            $category = 'Purchases';
        }
    
        // Check in maintenance table
        $maintenance_query = "SELECT title FROM maintenance WHERE title = '$title'";
        $maintenance_result = mysqli_query($conn, $maintenance_query);
        
        if (mysqli_num_rows($maintenance_result) > 0) {
            $category = 'Maintenance';
        }
    
        // Handle the case where the title doesn't exist in any table
        if (empty($category)) {
            $errors['category'] = 'Event title not found in category';
        } else {
            // Hardcoded organization_id and created_by
            $organization_id = 2;
            $created_by = 1;
            
            // Insert into the budget_approvals table
            $insert_query = "INSERT INTO budget_approvals (title, category, attachment, status, organization_id, created_by) 
                             VALUES ('$title', '$category', '$attachment', '$status', $organization_id, $created_by)";
    
            if (mysqli_query($conn, $insert_query)) {
                $data['success'] = true;
                $data['message'] = 'Request added successfully!';
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to add request to the database.'];
            }
        }
    }
}

echo json_encode($data);
?>