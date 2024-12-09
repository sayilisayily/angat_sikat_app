<?php

include 'connection.php';
include '../session_check.php';

$errors = [];
$data = [];

// Validate title input
if (empty($_POST['title'])) {
    $errors['title'] = 'Event title is required.';
} else {
    $title = mysqli_real_escape_string($conn, $_POST['title']);

    // Check if a request with the same title already exists
    $query = "SELECT * FROM budget_approvals WHERE title = '$title' AND archived = 0 AND organization_id = $organization_id";
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
        $status = 'Pending'; // Default status
        $category = '';
        $attachment = '';

        // File upload handling
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
            $file_tmp = $_FILES['attachment']['tmp_name'];
            $file_name = $_FILES['attachment']['name'];

            if (!empty($file_name)) {
                $upload_dir = 'uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $file_path = $upload_dir . basename($file_name);
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $attachment = $file_name;
                } else {
                    $errors['attachment'] = 'Error moving the uploaded file.';
                }
            } else {
                $errors['attachment'] = 'Uploaded file name is empty.';
            }
        }

        // Determine category based on the title
        $tables = ['events' => 'Activities', 'purchases' => 'Purchases', 'maintenance' => 'Maintenance'];
        foreach ($tables as $table => $cat) {
            $query = "SELECT title FROM $table WHERE title = '$title'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                $category = $cat;
                break;
            }
        }

        if (empty($category)) {
            $errors['category'] = 'Event title not found in any category.';
        } else {
            // Insert into budget_approvals table
            $insert_query = "INSERT INTO budget_approvals (title, category, attachment, status, organization_id) 
                             VALUES ('$title', '$category', '$attachment', '$status', $organization_id)";

            if (mysqli_query($conn, $insert_query)) {
                $admin_query = "SELECT user_id FROM users WHERE role = 'admin'";
                $admin_result = mysqli_query($conn, $admin_query);

                if ($admin_result && mysqli_num_rows($admin_result) > 0) {
                    $notification_message = mysqli_real_escape_string($conn, "A new budget approval request for '$title' has been submitted.");
                    
                    while ($row = mysqli_fetch_assoc($admin_result)) {
                        $admin_id = $row['user_id'];
                        
                        $insert_notification_query = "INSERT INTO notifications (recipient_id, message, is_read, created_at) 
                                                    VALUES ($admin_id, '$notification_message', 0, NOW())";
                        
                        if (!mysqli_query($conn, $insert_notification_query)) {
                            error_log("Notification Error: " . mysqli_error($conn)); // Log MySQL errors
                            error_log("Query: " . $insert_notification_query); // Log the exact query
                        } else {
                            $data['success'] = true;
                            $data['message'] = 'Request and notifications added successfully!';
                        }
                    }
                } else {
                    error_log("Admin query failed or returned no results: " . mysqli_error($conn));
                }

                
            } else {
                $data['success'] = false;
                $data['errors'] = ['database' => 'Failed to add request to the database.'];
            }
        }
    }
}

echo json_encode($data);

?>
