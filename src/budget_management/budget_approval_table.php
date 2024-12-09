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
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <!--Custom CSS for Sidebar-->
    <link rel="stylesheet" href="../html/sidebar.css" />
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
        <!-- Sidebar Start -->
        <aside class="left-sidebar" id="sidebar">
            <div class="top-bar">
                <div id="toggleSidebar" class="burger-icon">
                <i class="bx bx-menu"></i>
                </div>
            </div>
            <!-- Sidebar scroll -->
            <div>
                <!-- Brand Logo -->
                <div class="brand-logo logo-container">
                <a href="../html/officer_dashboard.html" class="logo-img">
                    <img src="angat sikat.png" alt="Angat Sikat Logo" class="sidebar-logo">
                </a>
                <span class="logo-text">ANGATSIKAT</span>
                </div>
                <!-- Sidebar navigation -->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="../dashboard/officer_dashboard.php" aria-expanded="false" data-tooltip="Dashboard">
                            <i class="bx bxs-dashboard"></i>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="../activity_management/activities.php" aria-expanded="false"
                            data-tooltip="Manage Events">
                            <i class="bx bx-calendar"></i>
                            <span class="hide-menu">Manage Events</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" aria-expanded="false" data-tooltip="Budget">
                            <i class="bx bx-wallet"></i>
                            <span class="hide-menu">Budget</span>
                        </a>
                        <div class="submenu">
                            <a href="../budget_management/budget_overview.php">› Overall</a>
                            <a href="#purchases">› Purchases</a>
                            <a href="#moe">› MOE</a>
                            <a href="../budget_management/budget_approval_table.php">› Approval</a>
                        </div>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="#transactions" aria-expanded="false" data-tooltip="Transactions">
                            <i class="bx bx-dollar-circle"></i>
                            <span class="hide-menu">Transactions</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" aria-expanded="false" data-tooltip="Income & Expenses">
                            <i class="bx bx-chart"></i>
                            <span class="hide-menu">Income & Expenses</span>
                        </a>
                        <div class="submenu">
                            <a href="#income">› Income</a>
                            <a href="../income_and_expenses/expenses.php">› Expenses</a>
                        </div>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="reports.php" aria-expanded="false" data-tooltip="Reports">
                            <i class="bx bx-file"></i>
                            <span class="hide-menu">Reports</span>
                        </a>
                        </li>
                        <li class="sidebar-item profile-container">
                        <a class="sidebar-link" href="../user/profile.html" aria-expanded="false" data-tooltip="Profile">
                            <div class="profile-pic-border">
                            <img src="byte.png" alt="Profile Picture" class="profile-pic" />
                            </div>
                            <span class="profile-name">BYTE</span>
                        </a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!-- Sidebar End -->

        <!-- JavaScript to toggle submenu visibility -->
        <script>
            document.querySelectorAll('.sidebar-item').forEach(item => {
                item.addEventListener('click', function () {
                    // Toggle the submenu for the clicked item
                    this.classList.toggle('show-submenu');

                    // Close other submenus
                    document.querySelectorAll('.sidebar-item').forEach(otherItem => {
                        if (otherItem !== this) {
                        otherItem.classList.remove('show-submenu');
                        }
                    });
                });
            });
        </script>


        <!-- JavaScript to toggle the sidebar -->
        <script>
            document.getElementById('toggleSidebar').addEventListener('click', function () { document.getElementById('sidebar').classList.toggle('collapsed'); });
        </script>

        <!--  2nd Body wrapper -->
        <div class="body-wrapper">
            <!-- Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler" id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                    </ul>

                    <!-- Custom Search and Profile Section -->
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <div class="container-fluid d-flex justify-content-end align-items-center"
                            style="padding: 0 1rem; background-color: transparent;">
                            

                            <!-- Notification Icon -->
                            <div style="position: relative; display: inline-block;">
                            <button id="notificationBtn" style="background-color: transparent; border: none; padding: 0; position: relative;">
                                <lord-icon
                                    src="https://cdn.lordicon.com/lznlxwtc.json"
                                    trigger="hover"
                                    colors="primary:#004024"
                                    style="width:30px; height:30px;">
                                </lord-icon>
                                <!-- Notification Count Badge -->
                                <span id="notificationCount" style="
                                    position: absolute;
                                    top: -5px;
                                    right: -5px;
                                    background-color: red;
                                    color: white;
                                    font-size: 12px;
                                    padding: 2px 6px;
                                    border-radius: 50%;
                                    display: none;">0</span>
                            </button>


                                <!-- Notification Dropdown -->
                                <div id="notificationDropdown" class="dropdown-menu p-2 shadow" 
                                    style="display: none; position: absolute; right: 0; top: 35px; width: 300px; max-height: 400px; 
                                    overflow-y: auto; background-color: white; border-radius: 5px; z-index: 1000;">
                                    <p style="margin: 0; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 5px;">
                                        Notifications
                                    </p>
                                    <div id="notificationList">
                                        <!-- Notifications will be dynamically loaded here -->
                                    </div>
                                    <p id="noNotifications" style="text-align: center; margin-top: 10px; color: gray; display: none;">
                                        No new notifications
                                    </p>
                                </div>
                            </div>

                            <!-- Profile Dropdown -->
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown"
                                    aria-expanded="false" style="text-decoration: none;">
                                    <img class="border border-dark rounded-circle" src="byte.png" alt="Profile"
                                        style="width: 40px; height: 40px; margin-left: 10px;">
                                    <div class="d-flex flex-column align-items-start ms-2">
                                        <span style="font-weight: bold; color: #004024; text-decoration: none;">BYTE
                                            ORG</span>
                                        <span
                                            style="font-size: 0.85em; color: #6c757d; text-decoration: none;">byte.org@gmail.com</span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="../user/profile.html"><i class="bx bx-user"></i> My
                                            Profile</a></li>
                                    <li><a class="dropdown-item" href="../user/login.html"><i
                                                class="bx bx-log-out"></i>
                                            Logout</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <!-- Header End -->

            <div class="container mt-4 p-5">
                <h2 class="mb-4 d-flex align-items-center justify-content-between">
                    <div>    
                        <span class="text-warning fw-bold me-2">|</span> Budget Approvals 
                        <button type="button" class="btn btn-primary ms-3" data-bs-toggle="modal"
                            data-bs-target="#budgetApprovalModal"
                            style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                            <i class="fa-solid fa-paper-plane"></i> Request Budget Approval
                        </button>
                    </div>
                    <a href="budget_approvals_archive.php" class="text-gray text-decoration-none fw-bold" 
                    style="font-size: 14px;">
                        View Archive
                    </a>

                </h2>

                <!-- Approval Table -->
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
                    $approvalsQuery = "SELECT * FROM budget_approvals WHERE organization_id = $organization_id AND archived = 0"; // Hardcoded for testing
                    $approvalsResult = mysqli_query($conn, $approvalsQuery);
                    while ($row = mysqli_fetch_assoc($approvalsResult)) {
                        ?>
                        <tr>
                            <td>
                                <?php echo $row['title']; ?>
                            </td>
                            <td>
                                <?php echo ucfirst($row['category']); ?>
                            </td>
                            <td><a href="uploads/<?php echo $row['attachment']; ?>"
                                    class='link-offset-2 link-underline link-underline-opacity-0' target="_blank">
                                    <?php echo $row['attachment']; ?>
                                </a></td>
                            <td>
                                <?php 
                                // Display status but don't allow editing
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
                                <!-- Non-admin users can edit other fields except status -->
                                <button class='btn btn-primary btn-sm edit-btn mb-3' data-bs-toggle='modal'
                                    data-bs-target='#editBudgetApprovalModal'
                                    data-id="<?php echo $row['approval_id']; ?>"><i class='fa-solid fa-pen'></i> Edit
                                </button>
                                <button class='btn btn-danger btn-sm archive-btn mb-3'
                                    data-id="<?php echo $row['approval_id']; ?>"><i class='fa-solid fa-box-archive'></i>
                                    Archive</button>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>



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
                                <!-- Success Message Alert -->
                                <div id="editSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                                    Request updated successfully!
                                </div>
                                <!-- Error Message Alert -->
                                <div id="editErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="editErrorList"></ul> <!-- List for showing validation errors -->
                                </div>
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