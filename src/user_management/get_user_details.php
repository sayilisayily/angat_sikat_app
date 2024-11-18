<?php
include '../connection.php';

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];

    // SQL query to fetch user details and associated organization_id
    $query = "SELECT u.*, o.organization_id 
              FROM users u
              LEFT JOIN organizations o ON u.organization_id = o.organization_id
              WHERE u.user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user was found
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $user]);
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No user ID provided.']);
}
?>
