<?php
// Include the database connection file
include '../connection.php';
include '../session_check.php'; 
include '../user_query.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
    header("Location: ../user/login.html");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Budget Approvals</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/angat sikat.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <!--Custom CSS for Budget Overview-->
    <link rel="stylesheet" href="../budget_management/css/budget.css" />
    <!--Boxicon-->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Lordicon (for animated icons) -->
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <!--Bootstrap Script-->
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
    <!-- DataTables JavaScript for table interactions and pagination -->
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.1.7/js/dataTables.bootstrap5.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.7/css/dataTables.bootstrap.min.css" />
</head>

<body>
    <?php include '../navbar.php';?>
    <div class="page-wrapper d-flex flex-column h-100" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>

            <div class="container mt-4 p-5">
            <div class="container mt-4 p-5">
                    <h2 class="mb-4 d-flex flex-column flex-sm-row align-items-center justify-content-between">
                        <div class="d-flex align-items-center mb-2 mb-sm-0">    
                            <span class="text-warning fw-bold me-2">|</span> Budget Approvals 
                        </div>
                        <a href="budget_approvals_archive.php" class="text-gray text-decoration-none fw-bold" 
                        style="font-size: 14px;">
                            View Archive
                        </a>
                    </h2>

                    <div class="d-flex flex-column flex-sm-row justify-content-between mb-3">
                        <button type="button" class="btn btn-primary ms-0 ms-sm-3" data-bs-toggle="modal"
                            data-bs-target="#budgetApprovalModal"
                            style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                            <i class="fa-solid fa-paper-plane"></i> Request Budget Approval
                        </button>
                    </div>

                    <!-- Approval Table -->
                    <div class="table-responsive">
                        <table id="budgetApprovalsTable" class="table mt-4">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Attachment</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Fetch data from budget_approvals table for non-admin users
                                $approvalsQuery = "SELECT * FROM budget_approvals WHERE organization_id = $organization_id AND archived = 0";
                                $approvalsResult = mysqli_query($conn, $approvalsQuery);
                                while ($row = mysqli_fetch_assoc($approvalsResult)) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row['title']; ?></td>
                                        <td><?php echo ucfirst($row['category']); ?></td>
                                        <td>
                                            <a href="uploads/<?php echo $row['attachment']; ?>"
                                            class='link-offset-2 link-underline link-underline-opacity-0' target="_blank">
                                            <?php echo $row['attachment']; ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php 
                                            // Display status
                                            $statusClass = '';
                                            if ($row['status'] == 'Pending') {
                                                $statusClass = 'pending';
                                            } else if ($row['status'] == 'Approved') {
                                                $statusClass = 'approved';
                                            } else if ($row['status'] == 'Disapproved') {
                                                $statusClass = 'disapproved';
                                            }
                                            ?>
                                            <span class='badge rounded-pill <?php echo $statusClass; ?>'>
                                                <?php echo ucfirst($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class='btn btn-primary btn-sm edit-btn mb-3'
                                                    data-bs-toggle='modal'
                                                    data-bs-target='#editBudgetApprovalModal'
                                                    data-id="<?php echo $row['approval_id']; ?>">
                                                <i class='fa-solid fa-pen'></i> Edit
                                            </button>
                                            <button class='btn btn-danger btn-sm archive-btn mb-3'
                                                    data-id="<?php echo $row['approval_id']; ?>">
                                                <i class='fa-solid fa-box-archive'></i> Archive
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                        
                    </div>
                </div>

                <style>
                @media (max-width: 576px) {
                    .table-responsive {
                        overflow-x: auto;
                    }

                    .btn {
                        width: 100%; /* Make buttons full width on small screens */
                        margin-bottom: 10px; /* Space between buttons */
                    }
                }
                </style>


            <!-- Add Budget Approval Modal -->
            <div class="modal fade" id="budgetApprovalModal" tabindex="-1" aria-labelledby="budgetApprovalModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="budgetApprovalModalLabel">Budget Approval Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">

                            <form id="addBudgetApprovalForm" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="hidden" name="id" id="id">
                                    <label for="title">Title</label>
                                    <select name="title" id="title" class="form-control" required>
                                        <option value="">Select Title</option>
                                        <!-- Fetch titles from events, purchases, and maintenance -->
                                        <?php
                                        // Fetch events
                                        $event_query = "SELECT title, event_id FROM events where archived = 0 and organization_id = $organization_id";
                                        $event_result = mysqli_query($conn, $event_query);
                                        echo "<optgroup label='Events'>";
                                        while ($row = mysqli_fetch_assoc($event_result)) {
                                            echo '<option value="' . htmlspecialchars($row['title']) . '" 
                                                    data-id="' . htmlspecialchars($row['event_id']) .'">' 
                                                    . htmlspecialchars($row['title']) . '</option>';
                                        }
                                        echo "</optgroup>";
        
                                        // Fetch purchases
                                        $purchase_query = "SELECT title, purchase_id FROM purchases where archived = 0 and organization_id = $organization_id";
                                        $purchase_result = mysqli_query($conn, $purchase_query);
                                        echo "<optgroup label='Purchases'>";
                                        while ($row = mysqli_fetch_assoc($purchase_result)) {
                                            echo '<option value="' . htmlspecialchars($row['title']) . '" 
                                                    data-id="' . htmlspecialchars($row['purchase_id']) .'">' 
                                                    . htmlspecialchars($row['title']) . '</option>';
                                        }
                                        echo "</optgroup>";
        
                                        // Fetch maintenance
                                        $maintenance_query = "SELECT title, maintenance_id FROM maintenance where archived = 0 and organization_id = $organization_id";
                                        $maintenance_result = mysqli_query($conn, $maintenance_query);
                                        echo "<optgroup label='Mainteance and Other Expenses'>";
                                        while ($row = mysqli_fetch_assoc($maintenance_result)) {
                                            echo '<option value="' . htmlspecialchars($row['title']) . '" 
                                                    data-id="' . htmlspecialchars($row['maintenance_id']) .'">' 
                                                    . htmlspecialchars($row['title']) . '</option>';
                                        }
                                        echo "</optgroup>";
                                        ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="attachment" class="form-label">Attachment:</label>
                                    <input type="file" name="attachment" id="attachment" class="form-control" required>
                                </div>
                                <!-- Success Message Alert -->
                                <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                    Request added successfully!
                                </div>
                                <!-- Error Message Alert -->
                                <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="errorList"></ul> <!-- List for showing validation errors -->
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- Edit Approval Modal -->
            <div class="modal fade" id="editBudgetApprovalModal" tabindex="-1"
                aria-labelledby="editBudgetApprovalModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editBudgetApprovalModalLabel">Edit Budget Approval</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">


                            <form id="editBudgetApprovalForm" enctype="multipart/form-data">
                                <input type="hidden" name="approval_id" id="editApprovalId">
                                
                                <div class="form-group">
                                    <label for="editTitle">Title</label>
                                    <input name="title" class="form-control" id="editTitle" readonly>
                                        
                                </div>
                                <div class="mb-3">
                                    <label for="editAttachment" class="form-label">Attachment:</label>
                                    <input type="file" name="attachment" id="editAttachment" class="form-control">
                                    <div id="currentAttachment" class="mt-2"></div> <!-- Display current file -->
                                </div>
                                <div id="editMessage" class="alert d-none"></div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>                            
                        </div>
                    </div>
                </div>
            </div>

            <!-- Archive Confirmation Modal -->
            <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="archiveModalLabel">Archive Request</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to archive this budget approval?
                            <input type="hidden" id="archiveBudgetApprovalId" value="">
                            <!-- Hidden input to store the ID -->
                             <!-- Success Message Alert -->
                            <div id="successMessage3" class="alert alert-success d-none mt-3" role="alert">
                                Request archived successfully!
                            </div>
                            <!-- Error Message Alert -->
                            <div id="errorMessage3" class="alert alert-danger d-none mt-3" role="alert">
                                <ul id="errorList3"></ul> <!-- List for showing validation errors -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-danger" id="confirmArchiveBtn">Archive</button>
                        </div>
                    </div>
                </div>
            </div>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>                                
            <script src="js/budget_approvals.js"></script>
            <script>
                $(document).ready(function () {
                    // Toggle the sidebar using the bars icon
                    $('#sidebarToggle').on('click', function () {
                        $('#sidebar').toggleClass('active');
                        $('#content').toggleClass('active');
                        $(this).toggleClass('active');
                    });
                });
            </script>

        </div>
        <!-- End of 2nd Body Wrapper -->
    </div>
    <!-- End of Overall Body Wrapper -->
</body>

</html>