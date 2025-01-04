<?php
include '../connection.php';
include '../session_check.php';
include '../user_query.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/login.html");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Advisers Management</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/angat sikat.png" />
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
                      <a class="sidebar-link" href="../activity_management/calendar.php" aria-expanded="false"
                        data-tooltip="Manage Events">
                        <i class="bx bx-calendar"></i>
                        <span class="hide-menu">Events</span>
                      </a>
                    </li>
                    <div class="submenu">                
                        <a href="../activity_management/admin_calendar.php">› Calendar</a>
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
         <div class="container mt-5 p-5">
            <h2 class="mb-4">Advisers
                <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addAdviserModal"
                    style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                    <i class="fa-solid fa-plus"></i> Add Adviser
                </button>
            </h2>
            <table id="advisersTable" class="table">
                <thead>
                    <tr>
                        <th>Picture</th> <!-- New Column for Picture -->
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Position</th>
                        <th>Organization</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch advisers and join with organizations table to get organization name
                    $adviserQuery = "SELECT advisers.*, organizations.organization_name 
                                    FROM advisers 
                                    JOIN organizations ON advisers.organization_id = organizations.organization_id";
                    $adviserResult = $conn->query($adviserQuery);

                    if ($adviserResult->num_rows > 0) {
                        while ($adviserRow = $adviserResult->fetch_assoc()) {
                            // Check if there's a picture and display it, or use a placeholder image
                            $picture = !empty($adviserRow['picture']) ? "uploads/" . $adviserRow['picture'] : "path/to/default-image.jpg";
                            echo "<tr>
                                    <td>
                                        <img src='$picture' alt='Adviser Picture' class='img-fluid' style='width: 50px; height: 50px; object-fit: cover; border-radius: 50%;'>
                                    </td>
                                    <td>{$adviserRow['first_name']}</td>
                                    <td>{$adviserRow['last_name']}</td>
                                    <td>{$adviserRow['position']}</td>
                                    <td>{$adviserRow['organization_name']}</td>
                                    <td>
                                        <button class='btn btn-primary btn-sm edit-btn mb-3' 
                                                data-bs-toggle='modal' 
                                                data-bs-target='#editAdviserModal' 
                                                data-id='{$adviserRow['adviser_id']}'>
                                            <i class='fa-solid fa-pen'></i> Edit
                                        </button>
                                        <button class='btn btn-danger btn-sm archive-btn mb-3' 
                                                data-id='{$adviserRow['adviser_id']}'>
                                            <i class='fa-solid fa-box-archive'></i> Archive
                                        </button>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center'>No advisers found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


    <!-- End of Overall Body Wrapper -->
    <!-- Add Adviser Modal -->
    <div class="modal fade" id="addAdviserModal" tabindex="-1" role="dialog" aria-labelledby="addAdviserLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="addAdviserForm" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAdviserLabel">Add New Adviser</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- First Name and Last Name Row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-user'></i></span>
                                    <input type="text" class="form-control" name="first_name" id="first_name" placeholder="Enter First Name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class='bx bx-user'></i></span>
                                    <input type="text" class="form-control" name="last_name" id="last_name" placeholder="Enter Last Name" required>
                                </div>
                            </div>
                        </div>

                        <!-- Organization -->
                        <div class="row mb-3">
                            <div class="col">
                                <label for="organization" class="form-label">Organization</label>
                                <select class="form-select" name="organization_id" id="organization_id" required>
                                    <option value="">Select Organization</option>
                                    <?php
                                    $query = "SELECT organization_id, organization_name FROM organizations";
                                    $result = mysqli_query($conn, $query);
                                    if ($result) {
                                        while ($org = mysqli_fetch_assoc($result)) {
                                            echo "<option value='{$org['organization_id']}'>{$org['organization_name']}</option>";
                                        }
                                    } else {
                                        echo "<option value=''>No Organizations Available</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Position -->
                        <div class="row mb-3">
                            <div class="col">
                                <label for="position" class="form-label">Position</label>
                                <input type="text" class="form-control" name="position" id="position" placeholder="Enter Position" required>
                            </div>
                        </div>

                        <!-- Picture -->
                        <div class="row mb-3">
                            <div class="col">
                                <label for="picture" class="form-label">Picture</label>
                                <input type="file" class="form-control" name="picture" id="picture" accept="image/*">
                            </div>
                        </div>

                        <!-- Success Message Alert -->
                        <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                            Adviser added successfully!
                        </div>

                        <!-- Error Message Alert -->
                        <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList"></ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Adviser</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="editUserForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserLabel">Edit User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden field for user ID -->
                        <input type="hidden" id="editUserId" name="user_id">
                        <!-- Form fields -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class='bx bx-user'></i></span>
                                <input type="text" class="form-control" name="username" id="edit_username" placeholder="Enter Username" required>
                            </div>
                            </div>
                            <div class="col-md-6">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                                <input type="email" class="form-control" name="email" id="edit_email" placeholder="Enter Email" required>
                            </div>
                            </div>
                        </div>

                        <!-- First Name and Last Name Row -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                            <label for="first_name" class="form-label">First Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class='bx bx-user'></i></span>
                                <input type="text" class="form-control" name="first_name" id="edit_firstname" placeholder="Enter First Name" required>
                            </div>
                            </div>
                            <div class="col-md-6">
                            <label for="last_name" class="form-label">Last Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class='bx bx-user'></i></span>
                                <input type="text" class="form-control" name="last_name" id="edit_lastname" placeholder="Enter Last Name" required>
                            </div>
                            </div>
                        </div>

                        <!-- Organization and Role Row -->
                        <div class="row mb-3">
                            <div class="col">
                                <label for="organization" class="form-label">Organization</label>
                                <select class="form-select" name="organization" id="edit_organization" required>
                                    <option value="">Select Organization</option>
                                    <?php
                                    $query = "SELECT organization_id, organization_name FROM organizations";
                                    $result = mysqli_query($conn, $query);
                                    if ($result) {
                                    while ($org = mysqli_fetch_assoc($result)) {
                                        echo "<option value='{$org['organization_id']}'>{$org['organization_name']}</option>";
                                    }
                                    } else {
                                    echo "<option value=''>No Organizations Available</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Password and Confirm Password Row -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class='bx bx-lock'></i></span>
                                <input type="password" class="form-control" id="edit_password" name="password" placeholder="Password" required>
                            </div>
                            </div>
                        </div>
                        
                        <!-- Success Message Alert -->
                        <div id="editSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                            User updated successfully!
                        </div>

                        <!-- Error Message Alert -->
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
                    <h5 class="modal-title" id="archiveModalLabel">Archive User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to archive this user?
                    <input type="hidden" id="archiveId">
                    <!-- Success Message Alert -->
                    <div id="archiveSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                        User archived successfully!
                    </div>
                    <!-- Error Message Alert -->
                    <div id="archiveErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                        <ul id="archiveErrorList"></ul> <!-- List for showing validation errors -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmArchiveBtn" class="btn btn-danger">Archive</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Backend Scripts -->
    <script src="js/advisers.js"></script>

</body>

</html>

<?php
$conn->close();
?>