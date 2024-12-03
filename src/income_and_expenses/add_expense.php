<?php 
include('../connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = [];
    $data = [];

    // Retrieve data from POST
    $title = isset($_POST['title']) ? trim($_POST['title']) : null;
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : null;

    // Input Validation
    if (empty($title)) {
        $errors[] = 'Title is required.';
    }

    if (empty($id)) {
        $errors[] = 'Record ID is required.';
    }

    if (empty($amount) || $amount <= 0) {
        $errors[] = 'Valid amount is required.';
    }

    // Return validation errors
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
        echo json_encode($data);
        exit;
    }

    // Determine the category based on the ID
    $category = null;

    // Check in events_summary table
    $event_query = "SELECT title FROM events_summary WHERE event_id = ?";
    $stmt = $conn->prepare($event_query);
    if (!$stmt) {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to prepare statement for events_summary.'];
        echo json_encode($data);
        exit;
    }
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $category = 'Activities';
    }
    $stmt->close();

    // Check in purchases_summary table if not found
    if (!$category) {
        $purchase_query = "SELECT title FROM purchases_summary WHERE purchase_id = ?";
        $stmt = $conn->prepare($purchase_query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $category = 'Purchases';
        }
        $stmt->close();
    }

    // Check in maintenance_summary table if not found
    if (!$category) {
        $maintenance_query = "SELECT title FROM maintenance_summary WHERE maintenance_id = ?";
        $stmt = $conn->prepare($maintenance_query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $category = 'Maintenance';
        }
        $stmt->close();
    }

    // Handle the case where no category is found
    if (empty($category)) {
        $errors['category'] = 'Event title not found in any category.';
    } else {
        // Insert the new expense record
        $query = "INSERT INTO expenses (category, title, amount) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to prepare insert statement.'];
            echo json_encode($data);
            exit;
        }
        $stmt->bind_param('ssd', $category, $title, $amount);

        if ($stmt->execute()) {
            $data['success'] = true;
            $data['message'] = 'Expense added successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to add the expense to the database.'];
        }
        $stmt->close();
    }

    // Return the response
    echo json_encode($data);
}
?>
