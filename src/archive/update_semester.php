<?php
// Include database connection
include('../connection.php');

// Initialize an array to hold validation errors
$errors = [];
$data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the semester ID (ensure it exists)
    if (empty($_POST['semester_id'])) {
        $errors['semester_id'] = 'Semester ID is required.';
    } else {
        $semester_id = mysqli_real_escape_string($conn, $_POST['semester_id']);
        
        // Check if the semester exists
        $query = "SELECT * FROM semesters WHERE semester_id = '$semester_id'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 0) {
            $errors['semester_id'] = 'Semester not found.';
        }
    }

    // Validate the period (type)
    if (!isset($_POST['type']) || !in_array($_POST['type'], ['First', 'Second'])) {
        $errors['type'] = 'Type is required.';
    } else {
        $type = mysqli_real_escape_string($conn, $_POST['type']);
    }

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

    // Validate the status field
    if (!isset($_POST['semester_status']) || !in_array($_POST['semester_status'], ['Active', 'Inactive'])) {
        $errors['semester_status'] = 'Semester status is required.';
    } else {
        $semester_status = mysqli_real_escape_string($conn, $_POST['semester_status']);
    }

    // Validate the selected year
    if (!isset($_POST['semester_year'])) {
        $errors['semester_year'] = 'Year is required.';
    } else {
        $year_id = mysqli_real_escape_string($conn, $_POST['semester_year']);
        
        // Fetch the year name and start date from the database
        $year_query = "SELECT name, start_date, end_date FROM years WHERE year_id = '$year_id'";
        $year_result = mysqli_query($conn, $year_query);
        
        if ($year_result && mysqli_num_rows($year_result) > 0) {
            $year_row = mysqli_fetch_assoc($year_result);
            $year_name = $year_row['name'];  // Format like '2024-2025'
            $year_start_date = $year_row['start_date'];  // The year start date
            $year_end_date = $year_row['end_date'];  // The year end date
        } else {
            $errors['semester_year'] = 'Invalid year selected.';
        }
    }

    // Validate that the start date matches the selected year's start date
    if (empty($errors)) {
        $start_timestamp = strtotime($semester_start_date);
        $year_start_timestamp = strtotime($year_start_date);
        
        if ($start_timestamp != $year_start_timestamp && $type == 'First') {
            $errors['semester_start_date'] = "Start date must match the start date of the selected year.";
        }
    }

    // New validation for Second Semester
    if (empty($errors) && $type == 'Second') {
        // Check if a first semester exists for the selected year
        $first_semester_query = "SELECT * FROM semesters WHERE type = 'First' AND year_id = '$year_id' AND status = 'Active'";
        $first_semester_result = mysqli_query($conn, $first_semester_query);

        if (mysqli_num_rows($first_semester_result) == 0) {
            $errors['semester_type'] = 'Please provide a first semester before adding the second semester.';
        } else {
            // Get the end date of the first semester
            $first_semester_row = mysqli_fetch_assoc($first_semester_result);
            $first_semester_end_date = $first_semester_row['end_date'];

            // Ensure the second semester start date is later than the first semester's end date
            $first_semester_end_timestamp = strtotime($first_semester_end_date);
            $second_semester_start_timestamp = strtotime($semester_start_date);

            if ($second_semester_start_timestamp <= $first_semester_end_timestamp) {
                $errors['semester_start_date'] = 'The start date for the second semester must be later than the end date of the first semester.';
            }
        }

        // Ensure that the second semester end date matches the selected year's end date
        if (strtotime($semester_end_date) != strtotime($year_end_date)) {
            $errors['semester_end_date'] = 'The end date for the second semester must match the end date of the selected year.';
        }
    }

    // If there are validation errors, return them
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors'] = $errors;
    } else {
        // Prepare and execute the update query
        $query = "UPDATE semesters SET 
                    name = '$type Semester $year_name', 
                    type = '$type',
                    year_id = '$year_id', 
                    start_date = '$semester_start_date', 
                    end_date = '$semester_end_date', 
                    status = '$semester_status' 
                  WHERE semester_id = '$semester_id'";

        if (mysqli_query($conn, $query)) {
            $data['success'] = true;
            $data['message'] = 'Semester updated successfully!';
        } else {
            $data['success'] = false;
            $data['errors'] = ['database' => 'Failed to update semester in the database.'];
        }
    }
}

echo json_encode($data);
?>