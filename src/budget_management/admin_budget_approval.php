<?php
// Include the database connection file
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $action = $_POST['action'];

    // Validate input
    if ($action == 'approve') {
        $status = 'Approved';
    } elseif ($action == 'disapprove') {
        $status = 'Disapproved';
    } else {
        die("Invalid action.");
    }

    // Update the status in the budget approvals table
    $update_query = "UPDATE budget_approvals SET status = '$status' WHERE approval_id = $id";

    if (mysqli_query($conn, $update_query)) {
        // Fetch the title and category from the budget approval record
        $query = "SELECT title, category FROM budget_approvals WHERE approval_id = $id";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            $title = $row['title'];
            $category = $row['category'];

            // Determine which table to update based on the category
            if ($category === 'Events') {
                $update_event_query = "UPDATE events SET event_status = '$status' WHERE title = '$title'";
                mysqli_query($conn, $update_event_query);
            } elseif ($category === 'Purchases') {
                $update_purchase_query = "UPDATE purchases SET purchase_status = '$status' WHERE title = '$title'";
                mysqli_query($conn, $update_purchase_query);
            } elseif ($category === 'Maintenance') {
                $update_maintenance_query = "UPDATE maintenance SET status = '$status' WHERE title = '$title'";
                mysqli_query($conn, $update_maintenance_query);
            }
        }

        // Success alert
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var alertBox = document.getElementById('alertBox');
                    var alertMessage = document.getElementById('alertMessage');
                    if (alertBox && alertMessage) {
                        alertMessage.innerText = 'Status updated successfully!';
                        alertBox.classList.add('alert-success');
                        alertBox.classList.remove('d-none');
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                });
              </script>";
    } else {
        // Error alert
        $error_message = "Error updating status: " . mysqli_error($conn);
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    var alertBox = document.getElementById('alertBox');
                    var alertMessage = document.getElementById('alertMessage');
                    if (alertBox && alertMessage) {
                        alertMessage.innerText = '$error_message';
                        alertBox.classList.add('alert-danger');
                        alertBox.classList.remove('d-none');
                    }
                });
              </script>";
    }
}
?>
