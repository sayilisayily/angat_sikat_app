<?php

include 'connection.php';
include '../session_check.php';  // Assuming session_check.php sets the organization_id in the session

$errors = [];
$data = [];

// Validate the event title 
if (empty($_POST['title'])) {
    $errors['title'] = 'Event title is required.';
} else {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    
    // Check if the event with the same title already exists for the organization
    $query = "SELECT * FROM events WHERE title = '$title' AND organization_id = {$_SESSION['organization_id']}";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $errors['title'] = 'An event with this title already exists.';
    }
}

// Validate the plan_id
if (empty($_POST['plan_id'])) {
    $errors['plan_id'] = 'Event plan_id is required.';
} else {
    $plan_id = mysqli_real_escape_string($conn, $_POST['plan_id']);
    
    // Check if the event with the same plan_id already exists for the organization
    $query = "SELECT * FROM events WHERE plan_id = '$plan_id' AND organization_id = {$_SESSION['organization_id']}";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $errors['plan_id'] = 'Event already exists.';
    }
}

// Validate other fields
if (empty($_POST['venue'])) {
    $errors['venue'] = 'Event venue is required.';
}

if (empty($_POST['start_date'])) {
    $errors['start_date'] = 'Event start date is required.';
}

if (empty($_POST['end_date'])) {
    $errors['end_date'] = 'Event end date is required.';
}

if ((strtotime($_POST['start_date'])) > (strtotime($_POST['end_date']))) {
    $errors['date'] = 'Invalid event start and end date.';
}

// If there are no errors, proceed to insert the event
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Insert event into database
    $venue = mysqli_real_escape_string($conn, $_POST['venue']);
    $start_date = mysqli_real_escape_string($conn, $_POST['start_date']);
    $end_date = mysqli_real_escape_string($conn, $_POST['end_date']);
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $organization_id = $_SESSION['organization_id'];

    $query = "INSERT INTO events (title, plan_id, event_venue, event_start_date, event_end_date, event_type, event_status, accomplishment_status, organization_id) 
              VALUES ('$title', $plan_id, '$venue', '$start_date', '$end_date', '$type', 'Pending', 0, $organization_id)";
    
    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Event added successfully!';
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to add event to the database.'];
    }
}

echo json_encode($data);

?>
