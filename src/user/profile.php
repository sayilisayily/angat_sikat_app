<?php
include 'connection.php';
include '../session_check.php'; 
include '../user_query.php';
include '../organization_query.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Profile</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <!--Custom CSS for Sidebar-->
    <link rel="stylesheet" href="../html/sidebar.css" />
    <!--Custom CSS for Profile-->
    <link rel="stylesheet" href="profile.css" />
    <!--Boxicon-->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Lordicon (for animated icons) -->
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <!--Calendar JS-->
    <script src="path/to/calendar.js"></script>


    <script>
        function toggleEditMode() {
            // Toggle between read-only and edit mode
            document.getElementById('profile-info').classList.toggle('hidden');
            document.getElementById('edit-profile-form').classList.toggle('hidden');
        }
    </script>
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
                    <a href="./index.html" class="logo-img">
                        <img src="angat sikat.png" alt="Angat Sikat Logo" class="sidebar-logo">
                    </a>
                    <span class="logo-text">ANGATSIKAT</span>
                </div>
                <!-- Sidebar navigation -->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="../dashboard/officer_dashboard.php" aria-expanded="false"
                                data-tooltip="Dashboard">
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
                            <a class="sidebar-link" href="#transactions" aria-expanded="false"
                                data-tooltip="Transactions">
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
                            <a class="sidebar-link" href="../user/profile.php" aria-expanded="false"
                                data-tooltip="Profile">
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

        <!-- Main Content area -->
        <div class="content" style="background-color: transparent;">
            <!-- Top Navbar -->
            <nav class="navbar navbar-expand-lg w-100 px-0" style="background-color: transparent;">
                <div class="container-fluid d-flex justify-content-end align-items-center"
                    style="max-width: 100%; padding: 0 1rem; background-color: transparent;">

                    <!-- Notification Icon -->
                    <script src="https://cdn.lordicon.com/lordicon.js"></script>
                    <button id="notificationBtn" style="background-color: transparent; border: none; padding: 0;">
                        <lord-icon src="https://cdn.lordicon.com/lznlxwtc.json" trigger="hover" colors="primary:#004024"
                            style="width:30px; height:30px;">
                        </lord-icon>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown"
                            aria-expanded="false" style="text-decoration: none;">
                            <img class="border border-dark rounded-circle" src="<?php echo !empty($profile_picture) ? '../user/uploads/' . htmlspecialchars($profile_picture) : '../user/uploads/default.png'; ?>" alt="Profile"
                                        style="width: 40px; height: 40px; margin-left: 10px;">
                            <div class="d-flex flex-column align-items-start ms-2">
                                <span style="font-weight: bold; color: #004024; text-decoration: none;"><?php echo htmlspecialchars($fullname); ?></span>
                                <span
                                    style="font-size: 0.85em; color: #6c757d; text-decoration: none;"><?php echo htmlspecialchars($email); ?></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../user/profile.html"><i class="fas fa-user-edit"></i>
                                    Edit
                                    Profile</a></li>
                            <li><a class="dropdown-item" href="../user/logout.php"><i
                                        class="fas fa-sign-out-alt"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- End of Top Navbar -->


            <!-- Profile Content -->
            <div class="profile-content">
                <div class="edit-section">
                    <h2>Profile Information</h2>
                    
                    <!-- Display Profile Information -->
                    <div id="profile-info">
                        <div style="text-align: center;">
                        <img src="<?php echo !empty($profile_picture) ? 'uploads/' . htmlspecialchars($profile_picture) : 'uploads/default.png'; ?>" 
                            alt="Profile Image" 
                             
                            class="profile-img">
                        </div>
                        <div class="info-text">
                            <div class="info-label">Full Name</div>
                            <div><?php echo htmlspecialchars($fullname); ?></div>
                        </div>
                        <div class="info-text">
                            <div class="info-label">Email</div>
                            <div><?php echo htmlspecialchars($email); ?></div>
                        </div>
                        <div class="info-text">
                            <div class="info-label">Organization</div>
                            <div>20241111</div>
                        </div>
                        <div class="info-text">
                            <div class="info-label">Role</div>
                            <div><?php echo htmlspecialchars($role); ?></div>
                        </div>
                        
                        
                        <div class="edit-btn">
                            <button onclick="toggleEditMode()">Edit Profile</button>
                        </div>
                    </div>

                    <!-- Edit Profile Form -->
                    <form id="edit-profile-form" class="hidden" action="update_profile.php" method="POST"
                        enctype="multipart/form-data">
                        <div class="profile-upload">
                        <img src="<?php echo !empty($profile_picture) ? 'uploads/' . htmlspecialchars($profile_picture) : 'uploads/default.png'; ?>" 
                            alt="Profile Image" 
                            id="profilePreview" 
                            class="profile-img">

                            <label for="profile_picture" class="upload-icon">+</label>
                            <input type="file" id="profile_picture" name="profile_picture" class="hidden"
                                onchange="document.getElementById('profilePreview').src = window.URL.createObjectURL(this.files[0])">
                        </div>
                        <div class="info-text">
                            <div class="info-label">First Name</div>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($firstname); ?>">
                        </div>
                        <div class="info-text">
                            <div class="info-label">Last Name</div>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($lastname); ?>">
                        </div>
                        <div class="info-text">
                            <div class="info-label">Email</div>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                        <div class="info-text">
                            <div class="info-label">Password</div>
                            <input type="password" name="password" required>

                        </div>
                        <div class="info-text">
                            <div class="info-label">Confirm Password</div>
                            <input type="password" name="confirm_password" required>

                        </div>
                        
                        <div class="save-delete">
                            <div class="delete-btn">
                                <button type="button" onclick="confirmDelete()">Delete Account</button>
                            </div>
                            <div class="save-btn">
                                <button type="submit">Save Changes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function confirmDelete() {
                const confirmation = confirm("Are you sure you want to delete your account? This action cannot be undone.");
                if (confirmation) {
                    // Proceed with deletion, redirect to delete action
                    window.location.href = 'delete_account.php'; // replace with your delete action URL
                }
            }
        </script>

    </div>
    <!-- End of Overall Body Wrapper -->

    <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/sidebarmenu.js"></script>
    <script src="../assets/js/app.min.js"></script>
    <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="../assets/libs/simplebar/dist/simplebar.js"></script>
    <script src="../assets/js/dashboard.js"></script>
    <!-- solar icons -->
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>