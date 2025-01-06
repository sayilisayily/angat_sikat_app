<?php
include '../connection.php';

if (isset($_POST['semester_id'])) {
    $semester_id = $_POST['semester_id'];

    $query = "SELECT * FROM semesters WHERE semester_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $semester_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $semester = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $semester]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Semester not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No semester ID provided.']);
}
?>