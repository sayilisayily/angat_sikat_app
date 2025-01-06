<?php

include '../connection.php';

$errors = [];
$data = [];

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
        $errors['year_status'] = 'year_status is required.';
    } else {
        $year_status = mysqli_real_escape_string($conn, $_POST['year_status']);
    }

// Generate the year name
if (empty($errors)) {
    $name = "AY $start_year-$end_year";

    // Check if a year with the same name already exists
    $query = "SELECT * FROM years WHERE name = '$name'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $errors['name'] = 'This academic year already exists.';
    }
}

// If there are errors, return them
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Insert the year into the database
    $query = "INSERT INTO years (name, start_date, end_date, status) 
              VALUES ('$name', '$year_start_date', '$year_end_date', '$year_status')";

    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Year added successfully!';
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to add the year to the database.'];
    }
}

// Return the response as JSON
echo json_encode($data);

?>