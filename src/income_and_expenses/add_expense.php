<?php
include '../connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get inputs from the form
    $category = $_POST['category'];
    $title = $_POST['title'];
    $amount = $_POST['amount'];
    $organization_id = 1; // Change as needed or fetch from the session

    // Handle file upload for the reference
    $reference = null; // Default value for reference
    if (isset($_FILES['reference']) && $_FILES['reference']['error'] == 0) {
        $file = $_FILES['reference'];
        $uploadDirectory = 'uploads/'; // Adjust path as needed
        $uploadFile = $uploadDirectory . basename($file['name']);
        
        // Validate file upload
        $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
        $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'txt'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                $reference = $uploadFile; // Store file path for the database
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload file.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid file type.']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload error.']);
        exit;
    }

    // Validate inputs
    $errors = [];
    if (empty($category)) {
        $errors[] = "Category is required.";
    }
    if (empty($title)) {
        $errors[] = "Title is required.";
    }
    if (empty($amount) || !is_numeric($amount)) {
        $errors[] = "Valid amount is required.";
    }

    // If no errors, insert the expense entry
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO expenses (category, title, amount, reference, organization_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $category, $title, $amount, $reference, $organization_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Expense added successfully!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to add expense.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'errors' => $errors]);
    }
}

$conn->close();
?>
