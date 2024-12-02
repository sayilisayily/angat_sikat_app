<?php
include '../connection.php';
include '../session_check.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.html");
    exit();
}

include '../user_query.php';
$sql = "SELECT * FROM organizations";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Organizations Management</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <!--Custom CSS for Sidebar-->
    <link rel="stylesheet" href="../html/sidebar.css" />
    <!--Custom CSS for Activities-->
    <link rel="stylesheet" href="../activity_management/css/activities.css" />
    <!--Boxicon-->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Lordicon (for animated icons) -->
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <!--Bootstrap Script-->
    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
    <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="../assets/js/dashboard.js"></script>
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
                            <!-- Search Bar -->
                            <div class="d-none d-sm-flex position-relative" style=" width: 250px; margin-right: 10px;">
                                <input class="form-control py-1 ps-4 pe-3 border border-dark rounded-pill" type="search"
                                    placeholder="Search" id="searchInput" style="width: 100%; padding: 0.25rem 1rem;">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="position-absolute top-50 start-0 translate-middle-y ms-2 text-secondary"
                                    width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    id="searchIcon" style="margin-left: 8px;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 19l-2-2m0 0a7 7 0 1110 0l-2 2m-2-2a7 7 0 110-14 7 7 0 010 14z" />
                                </svg>
                            </div>

                            <!-- Notification Icon -->
                            <button id="notificationBtn"
                                style="background-color: transparent; border: none; padding: 0;">
                                <lord-icon src="https://cdn.lordicon.com/lznlxwtc.json" trigger="hover"
                                    colors="primary:#004024" style="width:30px; height:30px;">
                                </lord-icon>
                            </button>

                            <!-- Profile Dropdown -->
                            <div class="dropdown">
                                <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown"
                                    aria-expanded="false" style="text-decoration: none;">
                                    <img class="border border-dark rounded-circle" src="<?php echo !empty($profile_picture) ? '../user/uploads/' . htmlspecialchars($profile_picture) : '../user/uploads/default.png'; ?>" alt="Profile"
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
                                    <li><a class="dropdown-item" href="../user/login.html"><i class="bx bx-log-out"></i>
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

    <div class="container mt-5 p-5">
        <h2 class="mb-4">Organizations
            <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addOrganizationModal"
                style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                <i class="fa-solid fa-plus"></i> Add Organization
            </button>
        </h2>
        <table id="organizationsTable" class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Logo</th>
                <th>Members</th>
                <th>Status</th>
                <th>Color</th> <!-- Added column for organization color -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $organization_logo = $row['organization_logo']; // Assuming the logo is stored as the file name
                // Check if logo exists and construct the path
                $logo_path = !empty($organization_logo) && file_exists('uploads/' . $organization_logo) 
                            ? 'uploads/' . $organization_logo 
                            : 'uploads/default_logo.png';

                // Get the organization color
                $organization_color = !empty($row['organization_color']) ? $row['organization_color'] : '#FFFFFF'; // Default to white if no color is set
                
                // Display the organization data in the table
                echo "<tr>
                        <td>{$row['organization_name']}</td>
                        <td><img src='$logo_path' alt='Logo' style='width: 50px; height: 50px; object-fit: cover;'></td>
                        <td>{$row['organization_members']}</td>
                        <td>{$row['organization_status']}</td>
                        <td style='background-color: {$organization_color}; color: white; text-align: center;'> <!-- Display color -->
                            {$organization_color}
                        </td>
                        <td>
                            <button class='btn btn-primary btn-sm edit-btn mb-3' 
                                    data-bs-toggle='modal' 
                                    data-bs-target='#editOrganizationModal' 
                                    data-id='{$row['organization_id']}'>
                                <i class='fa-solid fa-pen'></i> Edit
                            </button>
                            <button class='btn btn-danger btn-sm archive-btn mb-3' 
                                    data-id='{$row['organization_id']}'>
                                <i class='fa-solid fa-box-archive'></i> Archive
                            </button>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='6' class='text-center'>No organizations found</td></tr>"; // Updated colspan to 6
        }
        ?>
        </tbody>
    </table>

    </div>

    <!-- Add Organization Modal -->
    <div class="modal fade" id="addOrganizationModal" tabindex="-1" role="dialog" aria-labelledby="addOrganizationLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="addOrganizationForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addOrganizationLabel">Add New Organization</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form fields -->
                        <div class="form-group mb-3">
                            <label for="organization_name">Organization Name</label>
                            <input type="text" class="form-control" id="organization_name" name="organization_name"
                                required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="organization_logo">Logo</label>
                            <input type="file" class="form-control" id="organization_logo" name="organization_logo"
                                required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="organization_members">Members</label>
                            <input type="number" class="form-control" id="organization_members"
                                name="organization_members" min="1" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="organization_status">Status</label>
                            <select class="form-control" id="organization_status" name="organization_status">
                                <option value="Active">Probationary</option>
                                <option value="Active">Level I</option>
                                <option value="Inactive">Level II</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <div class="col-sm-3">
                                <label for="organization_color">Color</label>
                                <input type="color" class="form-control" id="organization_color"
                                    name="organization_color" required>
                            </div>
                            <div class="col-sm-3">
                            </div>
                        </div>

                        <!-- Success Message Alert -->
                        <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                            Organization added successfully!
                        </div>
                        <!-- Error Message Alert -->
                        <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList"></ul> <!-- List for showing validation errors -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Organization</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Organization Modal -->
    <div class="modal fade" id="editOrganizationModal" tabindex="-1" role="dialog"
        aria-labelledby="editOrganizationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="editOrganizationForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editOrganizationLabel">Edit Organization</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden field for organization ID -->
                        <input type="hidden" id="editOrganizationId" name="organization_id">

                        <!-- Other form fields -->
                        <div class="form-group mb-3">
                            <label for="editOrganizationName">Organization Name</label>
                            <input type="text" class="form-control" id="editOrganizationName" name="organization_name"
                                required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="editOrganizationLogo">Logo</label>
                            <input type="file" class="form-control" id="editOrganizationLogo" name="organization_logo"
                                accept="image/*">
                        </div>
                        <div class="form-group mb-3">
                            <label for="editOrganizationMembers">Members</label>
                            <input type="number" class="form-control" id="editOrganizationMembers"
                                name="organization_members" min="1" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="editOrganizationStatus">Status</label>
                            <select class="form-control" id="editOrganizationStatus" name="organization_status">
                                <option value="Active">Probationary</option>
                                <option value="Active">Level I</option>
                                <option value="Inactive">Level II</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <div class="col-sm-3">
                                <label for="organization_color">Color</label>
                                <input type="color" class="form-control" id="editOrganizationColor"
                                    name="organization_color" required>
                            </div>
                            <div class="col-sm-3">
                            </div>
                        </div>
                        <!-- Success Message Alert -->
                        <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                            Organization updated successfully!
                        </div>
                        <!-- Error Message Alert -->
                        <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList"></ul> <!-- List for showing validation errors -->
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

    <!-- Backend Scripts -->
    <script src="js/organizations.js"></script>
</body>

</html>

<?php
$conn->close();
?>