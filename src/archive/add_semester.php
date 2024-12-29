<?php
include '../connection.php';

$errors = [];
$data = [];

// Validate the start date
if (empty($_POST['semester_start_date'])) {
    $errors['semester_start_date'] = 'Start date is required.';
} else {
    $semester_start_date = mysqli_real_escape_string($conn, $_POST['semester_start_date']);
}

// Validate the end date
if (empty($_POST['semester_end_date'])) {
    $errors['semester_end_date'] = 'End date is required.';
} else {
    $semester_end_date = mysqli_real_escape_string($conn, $_POST['semester_end_date']);
}

// Validate date logic
if (!empty($semester_start_date) && !empty($semester_end_date)) {
    $start_timestamp = strtotime($semester_start_date);
    $end_timestamp = strtotime($semester_end_date);

    if ($start_timestamp > $end_timestamp) {
        $errors['date'] = 'The start date cannot be later than the end date.';
    }
}

if (!isset($_POST['semester_status']) || !in_array($_POST['semester_status'], ['Active', 'Inactive'])) {
    $errors['semester_status'] = 'Semester status is required.';
} else {
    $semester_status = mysqli_real_escape_string($conn, $_POST['semester_status']);
}

if (!isset($_POST['semester_year'])) {
    $errors['semester_year'] = 'Year is required.';
} else {
    $year_id = mysqli_real_escape_string($conn, $_POST['semester_year']);
    
    // Fetch the year name and start date from the database
    $year_query = "SELECT name, start_date, end_date FROM years WHERE year_id = '$year_id'";
    $year_result = mysqli_query($conn, $year_query);
    
    if ($year_result && mysqli_num_rows($year_result) > 0) {
        $year_row = mysqli_fetch_assoc($year_result);
        $year_name = $year_row['name'];  // Assuming format like '2024-2025'
        $year_start_date = $year_row['start_date'];  // Assuming the year table has a 'start_date' field
        $year_end_date = $year_row['end_date'];  // Assuming the year table has an 'end_date' field
    } else {
        $errors['semester_year'] = 'Invalid year selected.';
    }
}

if (!isset($_POST['type']) || !in_array($_POST['type'], ['First', 'Second'])) {
    $errors['type'] = 'Type is required.';
} else {
    $type = mysqli_real_escape_string($conn, $_POST['type']);
}

// Validate that the start date matches the selected year's start date
if (empty($errors)) {
    $start_timestamp = strtotime($semester_start_date);
    $year_start_timestamp = strtotime($year_start_date);
    
    if ($start_timestamp != $year_start_timestamp && $type == 'First') {
        $errors['semester_start_date'] = "Start date must match start date of the selected year.";
    }
}

// Validation for Second Semester
if (empty($errors) && $type == 'Second') {
    // Check if a First Semester exists for the selected year
    $first_semester_query = "SELECT * FROM semesters WHERE type = 'First' AND year_id = '$year_id'";
    $first_semester_result = mysqli_query($conn, $first_semester_query);
    
    if (mysqli_num_rows($first_semester_result) == 0) {
        $errors['first_semester'] = 'Please provide a First Semester for the selected year first.';
    } else {
        // Fetch the end date of the First Semester
        $first_semester_row = mysqli_fetch_assoc($first_semester_result);
        $first_semester_end_date = $first_semester_row['end_date'];

        // Check if the start date of the Second Semester is later than the end date of the First Semester
        if (strtotime($semester_start_date) <= strtotime($first_semester_end_date)) {
            $errors['semester_start_date'] = 'The start date of the Second Semester must be later than the end date of the First Semester.';
        }
    }
    
    // Additional validation to check if the end date of the Second Semester matches the year's end date
    $semester_end_timestamp = strtotime($semester_end_date);
    $year_end_timestamp = strtotime($year_end_date);
    
    if ($semester_end_timestamp != $year_end_timestamp) {
        $errors['semester_end_date'] = 'The end date of the Second Semester must match the end date of the selected year.';
    }
}

// Generate the semester name
if (empty($errors)) {
    $name = "$type Semester $year_name";

    // Check if a semester with the same name already exists
    $query = "SELECT * FROM semesters WHERE name = '$name'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $errors['name'] = 'This semester already exists.';
    }
}

// If there are errors, return them
if (!empty($errors)) {
    $data['success'] = false;
    $data['errors'] = $errors;
} else {
    // Insert the semester into the database
    $query = "INSERT INTO semesters (name, type, year_id, start_date, end_date, status) 
              VALUES ('$name', '$type', $year_id, '$semester_start_date', '$semester_end_date', '$semester_status')";

    if (mysqli_query($conn, $query)) {
        $data['success'] = true;
        $data['message'] = 'Semester added successfully!';
    } else {
        $data['success'] = false;
        $data['errors'] = ['database' => 'Failed to add the semester to the database.'];
    }
}

// Return the response as JSON
echo json_encode($data);

?>
