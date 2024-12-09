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
    <!-- 2nd Body wrapper -->
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

                <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                    <div class="container-fluid d-flex justify-content-end align-items-center" style="padding: 0 1rem;">
                        <!-- Notification Icon -->
                        <button id="notificationBtn" style="background-color: transparent; border: none; padding: 0;">
                            <lord-icon src="https://cdn.lordicon.com/lznlxwtc.json" trigger="hover"
                                colors="primary:#004024" style="width:30px; height:30px;">
                            </lord-icon>
                        </button>

                        <!-- Profile Dropdown -->
                        <div class="dropdown">
                            <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown"
                                aria-expanded="false" style="text-decoration: none;">
                                <img class="border border-dark rounded-circle" src="<?php echo !empty($profile_picture) ? 'default.png' . htmlspecialchars($profile_picture) : 'default.png'; ?>" alt="Profile"
                                    style="width: 40px; height: 40px; margin-left: 10px;">
                                <span class="visually-hidden"><?php echo htmlspecialchars($user['username']); ?></span>
                                <div class="d-flex flex-column align-items-start ms-2">
                                    <span style="font-weight: bold; color: #004024;"><?php echo htmlspecialchars($fullname); ?></span>
                                    <span style="font-size: 0.85em; color: #6c757d;"><?php echo htmlspecialchars($email); ?></span>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="../user/profile.html"><i class="bx bx-user"></i> My Profile</a></li>
                                <li><a class="dropdown-item" href="../user/logout.php"><i class="bx bx-log-out"></i> Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </header>
        <!-- Header End -->
    </div>
    <!-- End of 2nd Body Wrapper -->

    <style>
        /* Navbar styles */
        .app-header {
            position: fixed; /* Fix the position of the navbar */
            width: calc(100% - 250px); /* Adjust width based on sidebar */
            top: 0; /* Align to the top */
            left: 250px; /* Align next to the sidebar */
            transition: width 0.3s ease-in-out, left 0.3s ease-in-out; /* Smooth transition */
            z-index: 1000; /* Keep navbar above other content */
        }

        /* Adjust navbar width when sidebar is collapsed */
        .left-sidebar.collapsed + .app-header {
            width: calc(100% - 70px);
            left: 70px;
        }
    </style>
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
                <nav class="sidebar-nav mx-4">
                    <ul id="sidebarnav">
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="../../dashboard/officer_dashboard.php" aria-expanded="false" data-tooltip="Dashboard">
                                <i class="bx bxs-dashboard"></i>
                                <span class="hide-menu">Dashboard</span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="../../activity_management/activities.php" aria-expanded="false" data-tooltip="Manage Events">
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
                                <a href="../budget_overview.php"> Overview </a>
                                <a href="../financial_plan.php"> Plan </a>
                                <a href="../purchases/purchases.php"> Purchases</a>
                                <a href="../maintenance/maintenance.php"> MOE</a>
                                <a href="../budget_approval_table.php"> Approval</a>
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
                                <a href="#income"> Income</a>
                                <a href="../../income_and_expenses/expenses.php"> Expenses</a>
                            </div>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="#reports.php" aria-expanded="false" data-tooltip="Reports">
                                <i class="bx bx-file"></i>
                                <span class="hide-menu">Reports</span>
                            </a>
                        </li>
                        <li class="sidebar-item profile-container">
                            <a class="sidebar-link" href="../user/profile.html" aria-expanded="false" data-tooltip="Profile">
                                <div class="profile-pic-border">
                                    <img src="<?php echo !empty($profile_picture) ? 'default.png' . htmlspecialchars($profile_picture) : 'default.png'; ?>" alt="Profile Picture" class="profile-pic" />
                                </div>
                                <span class="profile-name"><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- Sidebar End -->

        <!-- JavaScript to toggle submenu visibility -->
        <script>
            document.querySelectorAll('.sidebar-item > .sidebar-link').forEach(item => {
                item.addEventListener('click', function (e) {
                    const submenu = this.nextElementSibling; // Get the submenu if it exists
                    const sidebar = document.getElementById('sidebar'); // Reference to sidebar

                    // Check if the clicked link is "Budget"
                    if (this.textContent.includes('Budget') && sidebar.classList.contains('collapsed')) {
                        // Expand the sidebar if it is collapsed
                        sidebar.classList.remove('collapsed');
                        const mainWrapper = document.getElementById('main-wrapper');
                        const appHeader = document.querySelector('.app-header');

                        // Adjust navbar width based on sidebar state
                        appHeader.style.width = 'calc(100% - 250px)';
                        appHeader.style.left = '250px';
                        mainWrapper.classList.remove('expanded'); // Adjust main wrapper margin
                    }

                    // Toggle submenu visibility only if there is a submenu
                    if (submenu) {
                        e.preventDefault(); // Prevent default only if there's a submenu
                        this.parentElement.classList.toggle('show-submenu'); // Toggle submenu visibility
                    }
                });
            });

            // Sidebar toggle functionality
            document.getElementById('toggleSidebar').addEventListener('click', function () {
                const sidebar = document.getElementById('sidebar');
                const mainWrapper = document.getElementById('main-wrapper');
                const appHeader = document.querySelector('.app-header');

                sidebar.classList.toggle('collapsed');
                mainWrapper.classList.toggle('expanded');

                // Adjust navbar width based on sidebar state
                if (sidebar.classList.contains('collapsed')) {
                    appHeader.style.width = 'calc(100% - 70px)';
                    appHeader.style.left = '70px';
                } else {
                    appHeader.style.width = 'calc(100% - 250px)';
                    appHeader.style.left = '250px';
                }
            });
        </script>

        <style>
            /* Sidebar initial styles */
            .left-sidebar {
                width: 250px;
                position: fixed;
                top: 0;
                left: 10px;
                height: 100%;
                background-color: #00542F;
                overflow: hidden;
                transition: width 0.3s ease-in-out;
            }

            .left-sidebar.collapsed {
                width: 70px;
            }

            #main-wrapper {
                margin-left: 250px;
                transition: margin-left 0.3s ease-in-out;
            }

            #main-wrapper.expanded {
                margin-left: 70px;
            }

            .left-sidebar.collapsed .hide-menu {
                display: none;
            }

            .left-sidebar.collapsed i {
                text-align: center;
                width: 100%;
            }

            .submenu {
                display: none;
                padding-left: 20px;
                background-color: #006f4e;
            }

            .sidebar-item.show-submenu .submenu {
                display: block;
            }
        </style>

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
        </div>
    </div>
    <!-- End of Overall Body Wrapper -->

    
    

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