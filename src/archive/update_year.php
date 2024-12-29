<?php

include '../connection.php';

$errors = [];
$data = [];

// Validate the year ID
if (empty($_POST['year_id'])) {
    $errors['year_id'] = 'Year ID is required.';
} else {
    $year_id = mysqli_real_escape_string($conn, $_POST['year_id']);
}

// Validate the start date
if (empty($_POST['year_start_date'])) {
    $errors['year_start_date'] = 'Start date is required.';
} else {
    $year_start_date = mysqli_real_escape_string($conn, $_POST['year_start_date']);
}

// Validate the end date
if (empty($_POST['year_end_date'])) {
    $errors['year_end_date'] = 'End date is required.';
} else {
    $year_end_date = mysqli_real_escape_string($conn, $_POST['year_end_date']);
}

// Validate date logic
if (!empty($year_start_date) && !empty($year_end_date)) {
    $start_timestamp = strtotime($year_start_date);
    $end_timestamp = strtotime($year_end_date);

    if ($start_timestamp > $end_timestamp) {
        $errors['date'] = 'The start date cannot be later than the end date.';
    }

    $start_year = (int)date('Y', $start_timestamp);
    $end_year = (int)date('Y', $end_timestamp);

    // Ensure the duration fits a typical school year (e.g., one year span)
    if ($end_year - $start_year != 1) {
        $errors['date'] = 'The duration must cover a complete academic year (e.g., AY 2024-2025).';
    }
}

if (!isset($_POST['year_status']) || !in_array($_POST['year_status'], ['Active', 'Inactive'])) {
        $errors['year_status'] = 'Year status is required.';
} else {
        $year_status = mysqli_real_escape_string($conn, $_POST['year_status']);
}

// Generate the year name
if (empty($errors)) {
    $start_year = (int)date('Y', strtotime($year_start_date));
    $end_year = (int)date('Y', strtotime($year_end_date));
    $name = "AY $start_year-$end_year";

    // Check if the year name already exists in the database (excluding the current year ID)
    $query = "SELECT * FROM years WHERE name = '$name' AND year_id != '$year_id'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $errors['name'] = 'A year with this name already exists.';
    }
}

// If there are errors, return them
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Update the year in the database
    $query = "UPDATE years SET name = '$name', start_date = '$year_start_date', end_date = '$year_end_date', status = '$year_status' WHERE year_id = '$year_id'";

    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Year updated successfully!';
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to update the year in the database.'];
    }
}

// Return the response as JSON
echo json_encode($data);

?>
