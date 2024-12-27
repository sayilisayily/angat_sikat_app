<?php
// Fetch officer data if needed
$user_id = $_SESSION['user_id'];
$user_query = "SELECT * FROM users WHERE user_id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);


// Check if the query was successful
if ($user_result && mysqli_num_rows($user_result) > 0) {
    $user = mysqli_fetch_assoc($user_result);
    $username = $user['username'];
    $firstname = $user['first_name'];
    $lastname = $user['last_name'];
    $fullname = $firstname . ' ' . $lastname;
    $email = $user['email'];
    $role = $user['role'];
    $profile_picture = $user['profile_picture'];

} else {
    echo "Error fetching user data: " . mysqli_error($conn);
    exit(); // Stop further execution if there is an error
}
?>