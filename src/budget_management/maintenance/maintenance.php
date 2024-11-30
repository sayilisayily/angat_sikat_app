<?php
include '../../connection.php';
include '../../session_check.php'; 
include '../../user_query.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
    header("Location: ../../user/login.html");
    exit();
}

// Fetch non-archived maintenance entries
$sql = "SELECT * FROM maintenance WHERE archived = 0 AND organization_id=$organization_id";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>MOE Table</title>
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
        <?php include '../../sidebar.php'; ?>

        <?php include '../../navbar.php';?>
    </div>
    <!-- End of Overall Body Wrapper -->

    
    <div class="container mt-5 p-5">
        <h2 class="mb-4 d-flex align-items-center justify-content-between">
            <div>    
                <span class="text-warning fw-bold me-2">|</span> Maintenance and Other Expenses
                <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addModal"
                style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                    <i class="fa-solid fa-plus"></i> Add MOE
                </button>
            </div>
            <a href="maintenance_archive.php" class="text-gray text-decoration-none fw-bold" 
            style="font-size: 14px;">
                View Archive
            </a>
        </h2>
            <table id="maintenanceTable" class="table">
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
                        $disabled = ($row['maintenance_status'] !== 'Approved') ? 'disabled' : '';
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
                            <td><input type='checkbox' class='form-check-input' onclick='showConfirmationModal({$row['maintenance_id']}, this.checked)' $checked $disabled></td>
                            <td>
                                <button class='btn btn-primary btn-sm edit-btn mb-3' data-bs-toggle='modal' data-bs-target='#editMaintenanceModal' data-id='{$row['maintenance_id']}'><i class='fa-solid fa-pen'></i> Edit</button>
                                <button class='btn btn-danger btn-sm archive-btn mb-3' data-id='{$row['maintenance_id']}'><i class='fa-solid fa-box-archive'></i> Archive</button>
                            </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No maintenance or other expenses found</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Completion Status Change</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to change the completion status of this purchase?
                    <!-- Success Message Alert -->
                    <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                        Status updated successfully!
                    </div>
                    <!-- Error Message Alert -->
                    <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                        <ul id="errorList"></ul> <!-- List for showing validation errors -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmUpdateBtn">Confirm</button>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Add Maintenance Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form id="addForm">
            <div class="modal-header">
            <h5 class="modal-title" id="addLabel">Add New Maintenance or Other Expense</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <!-- Form fields -->
            <div class="form-group row mb-2">
            <!-- Plan ID -->
            <input type="hidden" id="plan_id" name="plan_id">
            <div class="col">
                    <label for="title">Maintenance or Other Expense Title</label>
                    <!-- Purchase title dropdown -->
                    <select class="form-control" id="title" name="title">
                        <option value="">Select MOE Title</option>
                        <?php
                        // Query to fetch titles with category 'Purchases'
                        $title_query = "SELECT title, amount, plan_id FROM financial_plan WHERE category = 'Maintenance and Other Expenses' AND organization_id = $organization_id";
                        $title_result = mysqli_query($conn, $title_query);
                        if ($title_result && mysqli_num_rows($title_result) > 0) {
                            while ($row = mysqli_fetch_assoc($title_result)) {
                                echo '<option value="' . htmlspecialchars($row['title']) . '" 
                                      data-plan-id="' . htmlspecialchars($row['plan_id']) . '" 
                                      data-amount="' . htmlspecialchars($row['amount']) . '">' . 
                                      htmlspecialchars($row['title']) . '</option>';
                            }
                        }
                        
                        ?>
                    </select>
                </div>
            </div>

            <!-- Success and Error Message Alert -->
            <div id="addSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                    Maintenance added successfully!
            </div>  
            <div id="addErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                <ul id="addErrorList"></ul>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Add Maintenance</button>
            </div>
        </form>
        </div>
    </div>
    </div>


    <!-- Edit Maintenance Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form id="editForm">
            <div class="modal-header">
            <h5 class="modal-title" id="editLabel">Edit Maintenance</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <!-- Hidden field for maintenance ID -->
            <input type="hidden" id="editMaintenanceId" name="edit_maintenance_id">

            <!-- Other form fields -->
            <div class="form-group">
                <label for="editMaintenanceTitle">Maintenance or Other Expense Title</label>
                <input type="text" class="form-control" id="editMaintenanceTitle" name="title" required>
            </div>

            <!-- Success and Error Message Alert -->
            <div id="editSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                    Maintenance or Other Expense added successfully!
            </div>  
            <div id="editErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                <ul id="editErrorList"></ul>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
        </div>
    </div>
    </div>


    <!-- Archive Confirmation Modal -->
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveModalLabel">Archive Maintenance or Other Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to archive this Maintence or Other Expense?
                    <input type="hidden" id="archiveMaintenanceId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmArchiveBtn" class="btn btn-danger">Archive</button>
                </div>
                <!-- Success Message Alert -->
                <div id="archiveSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                        Maintenance or Other Expense archived successfully!
                </div>  
                <!-- Error Message Alert -->
                <div id="archiveErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                    <ul id="archiveErrorList"></ul>
                </div>
            </div>
        </div>
    </div>

    <!-- BackEnd -->
    <script src="../js/maintenance.js"></script>
</body>

</html>

<?php
$conn->close();
?>