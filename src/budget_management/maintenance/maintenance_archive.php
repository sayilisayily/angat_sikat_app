<?php
include '../connection.php';
include '../../session_check.php'; 
include '../../user_query.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
    header("Location: ../user/login.html");
    exit();
}

$sql = "SELECT * FROM maintenance WHERE archived = 1 AND organization_id = $organization_id";
$result = $conn->query($sql);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>MOE Archive</title>
    <link rel="shortcut icon" type="image/png" href="../../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../../assets/css/styles.min.css" />
    <!--Custom CSS for Sidebar-->
    <link rel="stylesheet" href="../../html/sidebar.css" />
    <!--Custom CSS for Activities-->
    <link rel="stylesheet" href="../../activity_management/css/activities.css" />
    <!--Boxicon-->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Lordicon (for animated icons) -->
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <!--Bootstrap Script-->
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/sidebarmenu.js"></script>
    <script src="../../assets/js/app.min.js"></script>
    <script src="../../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="../../assets/libs/simplebar/dist/simplebar.js"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <!--Bootstrap JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JavaScript for responsive components and modals -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JavaScript for table interactions and pagination -->
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap.min.css" />
</head>

<body>
    <!-- Overall Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../..//sidebar.php'; ?>

        <?php include '../../navbar.php';?>
    </div>
    <!-- End of Overall Body Wrapper -->

    
    <div class="container mt-5 p-5">
        <h2 class="mb-4"><span class="text-warning fw-bold me-2">|</span> Maintenance and Other Expenses Archive
        </h2>
        <table id="archiveMaintenanceTable" class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Total Budget</th>
                    <th>Status</th>
                    <th>Completed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $checked = $row['completion_status'] ? 'checked' : '';
                        $disabled = ($row['archived'] !== '0') ? 'disabled' : '';
                        echo "<tr>
                                <td><a class='link-offset-2 link-underline link-underline-opacity-0' href='maintenance_details.php?maintenance_id={$row['maintenance_id']}'>{$row['title']}</a></td>
                                <td>{$row['total_amount']}</td>
                                <td>";
                        
                                if ($row['maintenance_status'] == 'Pending') {
                                    echo "<span class='badge rounded-pill pending'> ";
                                } else if ($row['maintenance_status'] == 'Approved') {
                                    echo "<span class='badge rounded-pill approved'> ";
                                } else if ($row['maintenance_status'] == 'Disapproved') {
                                    echo "<span class='badge rounded-pill disapproved'> ";
                                }

                                echo "{$row['maintenance_status']}</span></td>
                                        
                                <td>
                                    <input type='checkbox' 
                                    class='form-check-input' 
                                    $checked 
                                    $disabled>

                                </td>
                                <td>
                                    <button class='btn btn-primary btn-sm recover-btn mb-3' 
                                            data-bs-toggle='modal' 
                                            data-bs-target='#recoverModal' 
                                            data-id='{$row['maintenance_id']}'>
                                        <i class='fa-solid fa-hammer'></i> Recover
                                    </button>
                                    <button class='btn btn-danger btn-sm delete-btn mb-3' 
                                            data-bs-toggle='modal' 
                                            data-bs-target='#deleteModal'
                                            data-id='{$row['maintenance_id']}'>
                                        <i class='fa-solid fa-trash'></i> Delete
                                    </button>
                                </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9' class='text-center'>No maintenance records found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- Recover Event Modal -->
                <div class="modal fade" id="recoverModal" tabindex="-1" aria-labelledby="recoverLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Recover Maintenance or Other Expense</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to recover this maintenance or other expense?</p>

                                <!-- Hidden form for item and event IDs -->
                                <form id="recoverForm">
                                    <input type="hidden" name="maintenance_id" id="recover_maintenance_id"> <!-- Event ID -->
                                </form>

                                <!-- Success message -->
                                <div id="recoverSuccessMessage" class="alert alert-success d-none"></div>
                                
                                <!-- Error message -->
                                <div id="recoverErrorMessage" class="alert alert-danger d-none">
                                    <ul id="recoverErrorList"></ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger" id="confirmRecoverBtn">Recover</button>
                            </div>
                        </div>
                    </div>
                </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Maintenance or Other Expense</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this maintenance or other expense?</p>

                                <!-- Hidden form for item and event IDs -->
                                <form id="deleteForm">
                                    <input type="hidden" name="maintenance_id" id="delete_maintenance_id"> <!-- Event ID -->
                                </form>

                                <!-- Success message -->
                                <div id="deleteSuccessMessage" class="alert alert-success d-none"></div>
                                
                                <!-- Error message -->
                                <div id="deleteErrorMessage" class="alert alert-danger d-none">
                                    <ul id="deleteErrorList"></ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

    <!-- BackEnd -->
    <script src="../js/maintenance_archive.js"></script>
</body>

</html>

<?php
$conn->close();
?>