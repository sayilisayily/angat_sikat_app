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
                      <a class="sidebar-link" href="../dashboard/admin_dashboard.php" aria-expanded="false"
                        data-tooltip="Dashboard">
                        <i class="bx bxs-dashboard"></i>
                        <span class="hide-menu">Dashboard</span>
                      </a>
                    </li>
                    <li class="sidebar-item">
                      <a class="sidebar-link" href="../user_management/users.php" aria-expanded="false"
                        data-tooltip="Users">
                        <i class="bx bx-user"></i>
                        <span class="hide-menu">Users</span>
                      </a>
                    </li>
                    <li class="sidebar-item">
                      <a class="sidebar-link" href="../organization_management/organizations.php" aria-expanded="false"
                        data-tooltip="Organizations">
                        <i class="bx bx-group"></i>
                        <span class="hide-menu">Organizations</span>
                      </a>
                    </li>
                    <li class="sidebar-item">
                      <a class="sidebar-link" href="../activity_management/admin_calendar.php" aria-expanded="false"
                        data-tooltip="Manage Events">
                        <i class="bx bx-calendar"></i>
                        <span class="hide-menu">Events</span>
                      </a>
                    </li>
                    <div class="submenu">                
                        <a href="../activity_management/calendar.php">› Calendar</a>
                      </div>
                    <li class="sidebar-item">
                      <a class="sidebar-link" aria-expanded="false" data-tooltip="Budget">
                        <i class="bx bx-wallet"></i>
                        <span class="hide-menu">Budget</span>
                      </a>
                      <div class="submenu">                
                        <a href="../budget_management/admin_budget_approval_table.php">› Approval</a>
                      </div>
                    </li>
                    
                    <li class="sidebar-item profile-container">
                      <a class="sidebar-link" href="../user/profile.html" aria-expanded="false" data-tooltip="Profile">
                        <div class="profile-pic-border">
                          <img src="<?php echo !empty($profile_picture) ? '../user/uploads/' . htmlspecialchars($profile_picture) : '../user/uploads/default.png'; ?>" alt="Profile Picture" class="profile-pic" />
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
                                        <span style="font-weight: bold; color: #004024; text-decoration: none;"><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></span>
                                        <span style="font-size: 0.85em; color: #6c757d; text-decoration: none;"><?php echo htmlspecialchars($user['email']); ?></span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="../user/profile.html"><i class="bx bx-user"></i>
                                            My
                                            Profile</a></li>
                                    <li><a class="dropdown-item" href="../user/logout.php"><i class="bx bx-log-out"></i>
                                            Logout</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <!-- Header End -->

        </div>
        <!-- End of 2nd Body Wrapper -->
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