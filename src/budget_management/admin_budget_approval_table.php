<?php
// Include the database connection file
include 'connection.php';
include '../session_check.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.html");
    exit();
}

include '../user_query.php';
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
    <!-- Overall Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../../navbar.php';?>
    </div>
    <!-- End of Overall Body Wrapper -->

    <!-- Alert Box -->
    <div id="alertBox" class="alert alert-success alert-dismissible fade show d-none" role="alert">
        <span id="alertMessage"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <div class="container p-4" style="margin-top: 50px;">
        <h2><span class="text-warning fw-bold me-2">|</span> Budget Approvals</h2>

        <!-- Approval Table -->
        <table class="table mt-4" id="approvalsTable">
            <thead>
                <tr>
                    <th>Organization</th> <!-- New column for organization -->
                    <th>Title</th>
                    <th>Category</th>
                    <th>Attachment</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
            // Fetch budget approvals with organization names from the database
            $query = "
                SELECT b.*, o.organization_name 
                FROM budget_approvals b 
                JOIN organizations o ON b.organization_id = o.organization_id";
            $result = mysqli_query($conn, $query);

            while ($row = mysqli_fetch_assoc($result)) {
                $organization = $row['organization_name'];  // Organization name
                $title = $row['title'];
                $category = $row['category'];
                $attachment = $row['attachment'];
                $status = $row['status'];
                $id = $row['approval_id']; // Assuming there's an ID field in your budget_approvals table
                ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($organization); ?>
                    </td> <!-- Organization name -->
                    <td>
                        <?php echo htmlspecialchars($title); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($category); ?>
                    </td>
                    <td>
                        <a href="uploads/<?php echo htmlspecialchars($attachment); ?>"
                            class='link-offset-2 link-underline link-underline-opacity-0' target="_blank">
                            <?php echo htmlspecialchars($attachment); ?>
                        </a>
                    </td>
                    <td>
                        <?php 
                        if ($row['status'] == 'Pending') {
                            echo " <span class='badge rounded-pill pending'> ";
                        } else if ($row['status'] == 'Approved') {
                            echo " <span class='badge rounded-pill approved'> ";
                        } else if ($row['status'] == 'Disapproved') {
                            echo " <span class='badge rounded-pill disapproved'> ";
                        }
                        echo ucfirst($row['status']); 
                        ?>
                        </span>
                    </td>
                    <td>
                    <button type="button" class="btn btn-sm btn-success mb-3" 
                            data-bs-toggle="modal" 
                            data-bs-target="#confirmationModal" 
                            data-action="approve" 
                            data-id="<?php echo $id; ?>">
                        <i class="fa-solid fa-check"></i> Approve
                    </button>

                    <!-- Disapprove Button -->
                    <button type="button" class="btn btn-sm btn-danger mb-3"
                            data-bs-toggle="modal" 
                            data-bs-target="#confirmationModal" 
                            data-action="disapprove" 
                            data-id="<?php echo $id; ?>">
                        <i class="fa-solid fa-xmark"></i> Disapprove
                    </button>
                    </td>
                </tr>
                <?php
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
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to <span id="actionText"></span> this budget request?
                    <!-- Success Message Alert -->
                    <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                            Event added successfully!
                        </div>
                        <!-- Error Message Alert -->
                        <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList"></ul> <!-- List for showing validation errors -->
                        </div>
                </div>
                <div class="modal-footer">
                    <form id="confirmationForm" action="admin_budget_approval.php" method="POST">
                        <input type="hidden" name="id" id="confirmId">
                        <input type="hidden" name="action" id="confirmAction">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin_budget_approvals.js"></script>
</body>

</html>