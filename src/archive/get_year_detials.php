<?php
include '../connection.php';

if (isset($_POST['year_id'])) {
    $year_id = $_POST['year_id'];

    $query = "SELECT * FROM years WHERE year_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $year_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $year = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $year]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Year not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No year ID provided.']);
}
?>