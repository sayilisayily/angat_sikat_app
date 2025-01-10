<?php
include '../connection.php';
include '../session_check.php'; 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Archive Legend Table</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/angat sikat.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <!--Custom CSS for Sidebar-->
    <link rel="stylesheet" href="../html/sidebar.css" />
    <!--Custom CSS for Budget Overview-->
    <link rel="stylesheet" href="../activity_management/css/activities.css" />
    <!--Boxicon-->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Lordicon (for animated icons) -->
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <!-- Selectize -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css"
        integrity="sha256-ze/OEYGcFbPRmvCnrSeKbRTtjG4vGLHXgOqsyLFTRjg=" crossorigin="anonymous" />
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.1/css/dataTables.bootstrap5.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap5.min.js"></script>
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
                <nav class="sidebar-nav mx-4">
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
                            <a href="../budget_management/budget_overview.php"> Overview </a>
                            <a href="../budget_management/financial_plan.php"> Plan </a>
                            <a href="../budget_management/purchases/purchases.php"> Purchases</a>
                            <a href="../budget_management/maintenance/maintenance.php"> MOE</a>
                            <a href="../budget_management/budget_approval_table.php"> Approval</a>
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
                                <a href="#income"> Income</a>
                                <a href="../income_and_expenses/expenses.php"> Expenses</a>
                            </div>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link" href="#reports.php" aria-expanded="false" data-tooltip="Reports">
                                <i class="bx bx-file"></i>
                                <span class="hide-menu">Reports</span>
                            </a>
                        </li>
                        <li class="sidebar-item profile-container">
                            <a class="sidebar-link" href="../user/profile.html" aria-expanded="false"
                                data-tooltip="Profile">
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

            <div class="container mt-5 p-5">
                <h2 class="mb-4"><span class="text-warning fw-bold me-2">|</span> Archive Legend</h2>

                <!-- Years Table -->
                <div class="mb-5">
                    <h3>Years
                        <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addYearModal"
                            style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                            <i class="fa-solid fa-plus"></i> Add Year
                        </button>
                    </h3>
                    <table id="yearsTable" class="table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $years_query = "SELECT * FROM years";
                            $years_result = $conn->query($years_query);

                            if ($years_result->num_rows > 0) {
                                while ($year = $years_result->fetch_assoc()) {
                                    $status_class = $year['status'] === 'Active' ? 'approved' : 'disapproved';

                                    echo "<tr>
                                        <td>{$year['name']}</td>
                                        <td>" . date('F d, Y', strtotime($year['start_date'])) . "</td>
                                        <td>" . date('F d, Y', strtotime($year['end_date'])) . "</td>
                                        <td><span class='badge rounded-pill {$status_class}'>{$year['status']}</span></td>
                                        <td>
                                            <button class='btn btn-primary btn-sm edit-year-btn mb-3' 
                                                    data-bs-toggle='modal' 
                                                    data-bs-target='#editYearModal' 
                                                    data-id='{$year['year_id']}'>
                                                <i class='fa-solid fa-pen'></i> Edit
                                            </button>
                                            <button class='btn btn-danger btn-sm delete-year-btn mb-3' 
                                                    data-id='{$year['year_id']}'>
                                                <i class='fa-solid fa-trash'></i> Delete
                                            </button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center'>No years found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- Semesters Table -->
                <div>
                    <h3>Semesters
                        <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addSemesterModal"
                            style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                            <i class="fa-solid fa-plus"></i> Add Semester
                        </button>
                    </h3>
                    <table id="semestersTable" class="table">
                        <thead>
                            <tr>
                                <th>Period</th>
                                <th>Year</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $semesters_query = "SELECT s.*, y.name AS year_name FROM semesters s JOIN years y ON s.year_id = y.year_id";
                            $semesters_result = $conn->query($semesters_query);

                            if ($semesters_result->num_rows > 0) {
                                while ($semester = $semesters_result->fetch_assoc()) {
                                    $status_class = $semester['status'] === 'Active' ? 'approved' : 'disapproved';

                                    echo "<tr>
                                        <td>{$semester['name']}</td>
                                        <td>{$semester['year_name']}</td>
                                        <td>" . date('F d, Y', strtotime($semester['start_date'])) . "</td>
                                        <td>" . date('F d, Y', strtotime($semester['end_date'])) . "</td>
                                        <td><span class='badge rounded-pill {$status_class}'>{$semester['status']}</span></td>
                                        <td>
                                            <button class='btn btn-primary btn-sm edit-semester-btn mb-3' 
                                                    data-bs-toggle='modal' 
                                                    data-bs-target='#editSemesterModal' 
                                                    data-id='{$semester['semester_id']}'>
                                                <i class='fa-solid fa-pen'></i> Edit
                                            </button>
                                            <button class='btn btn-danger btn-sm delete-semester-btn mb-3' 
                                                    data-id='{$semester['semester_id']}'>
                                                <i class='fa-solid fa-trash'></i> Delete
                                            </button>
                                        </td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='6' class='text-center'>No semesters found</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>



            <!-- Add Year Modal -->
            <div class="modal fade" id="addYearModal" tabindex="-1" role="dialog" aria-labelledby="addYearLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="addYearForm" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addYearLabel">Add New Year</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <!-- Start Date Field -->
                                <div class="form-group mt-3">
                                    <label for="year_start_date">Start Date</label>
                                    <input type="date" class="form-control" id="year_start_date" name="year_start_date" required>
                                </div>

                                <!-- End Date Field -->
                                <div class="form-group mt-3">
                                    <label for="year_end_date">End Date</label>
                                    <input type="date" class="form-control" id="year_end_date" name="year_end_date" required>
                                </div>

                                <!-- Status Field -->
                                <div class="form-group mt-3">
                                    <label for="year_status">Status</label>
                                    <select name="year_status" id="year_status" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <!-- Success Message Alert -->
                                <div id="yearSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                                    Year added successfully!
                                </div>

                                <!-- Error Message Alert -->
                                <div id="yearErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="yearErrorList"></ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Year</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Add Semester Modal -->
            <div class="modal fade" id="addSemesterModal" tabindex="-1" role="dialog" aria-labelledby="addSemesterLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="addSemesterForm" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addSemesterLabel">Add New Semester</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <!-- Year Dropdown Field -->
                                <div class="form-group mt-3">
                                    <label for="semester_year">Year</label>
                                    <select name="semester_year" id="semester_year" class="form-control" required>
                                        <option value="">Select Year</option>
                                        <?php
                                        $years_query = "SELECT year_id, name FROM years";
                                        $years_result = $conn->query($years_query);
                                        if ($years_result->num_rows > 0) {
                                            while ($year = $years_result->fetch_assoc()) {
                                                echo "<option value='{$year['year_id']}'>{$year['name']}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Type Field -->
                                <div class="form-group mt-3">
                                    <label for="type">Type</label>
                                    <select name="type" id="type" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="First">First</option>
                                        <option value="Second">Second</option>
                                    </select>
                                </div>

                                <!-- Start Date Field -->
                                <div class="form-group mt-3">
                                    <label for="semester_start_date">Start Date</label>
                                    <input type="date" class="form-control" id="semester_start_date" name="semester_start_date" required>
                                </div>

                                <!-- End Date Field -->
                                <div class="form-group mt-3">
                                    <label for="semester_end_date">End Date</label>
                                    <input type="date" class="form-control" id="semester_end_date" name="semester_end_date" required>
                                </div>

                                <!-- Status Field -->
                                <div class="form-group mt-3">
                                    <label for="semester_status">Status</label>
                                    <select name="semester_status" id="semester_status" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <!-- Success Message Alert -->
                                <div id="semesterSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                                    Semester added successfully!
                                </div>

                                <!-- Error Message Alert -->
                                <div id="semesterErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="semesterErrorList"></ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Semester</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



            <!-- Edit Semester Modal -->
            <div class="modal fade" id="editSemesterModal" tabindex="-1" role="dialog" aria-labelledby="editSemesterLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="editSemesterForm" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editSemesterLabel">Edit Semester</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <!-- Hidden input to store the semester ID -->
                                <input type="hidden" id="editSemesterId" name="semester_id">

                                <!-- Year Dropdown Field -->
                                <div class="form-group mt-3">
                                    <label for="editSemesterYear">Year</label>
                                    <select name="semester_year" id="editSemesterYear" class="form-control" required>
                                        <option value="">Select Year</option>
                                        <?php
                                        $years_query = "SELECT year_id, name FROM years";
                                        $years_result = $conn->query($years_query);
                                        if ($years_result->num_rows > 0) {
                                            while ($year = $years_result->fetch_assoc()) {
                                                echo "<option value='{$year['year_id']}'>{$year['name']}</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Type Field -->
                                <div class="form-group mt-3">
                                    <label for="editType">Type</label>
                                    <select name="type" id="editType" class="form-control" required>
                                        <option value="">Select Type</option>
                                        <option value="First">First</option>
                                        <option value="Second">Second</option>
                                    </select>
                                </div>

                                <!-- Start Date Field -->
                                <div class="form-group mt-3">
                                    <label for="editStartDate">Start Date</label>
                                    <input type="date" class="form-control" id="editStartDate" name="semester_start_date" required>
                                </div>

                                <!-- End Date Field -->
                                <div class="form-group mt-3">
                                    <label for="editEndDate">End Date</label>
                                    <input type="date" class="form-control" id="editEndDate" name="semester_end_date" required>
                                </div>

                                <!-- Status Field -->
                                <div class="form-group mt-3">
                                    <label for="editStatus">Status</label>
                                    <select name="semester_status" id="editStatus" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <!-- Success Message Alert -->
                                <div id="semesterEditSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                                    Semester updated successfully!
                                </div>

                                <!-- Error Message Alert -->
                                <div id="semesterEditErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="semesterEditErrorList"></ul>
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


            <!-- Edit Year Modal -->
            <div class="modal fade" id="editYearModal" tabindex="-1" role="dialog" aria-labelledby="editYearLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="editYearForm" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editYearLabel">Edit Year</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">

                                <!-- Hidden input to store the year ID -->
                                <input type="hidden" id="editYearId" name="year_id">

                                <!-- Start Date Field -->
                                <div class="form-group mt-3">
                                    <label for="editYearStartDate">Start Date</label>
                                    <input type="date" class="form-control" id="editYearStartDate" name="year_start_date" required>
                                </div>

                                <!-- End Date Field -->
                                <div class="form-group mt-3">
                                    <label for="editYearEndDate">End Date</label>
                                    <input type="date" class="form-control" id="editYearEndDate" name="year_end_date" required>
                                </div>

                                <!-- Status Field -->
                                <div class="form-group mt-3">
                                    <label for="editYearStatus">Status</label>
                                    <select name="year_status" id="editYearStatus" class="form-control" required>
                                        <option value="">Select Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>

                                <!-- Success Message Alert -->
                                <div id="yearEditSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                                    Year updated successfully!
                                </div>

                                <!-- Error Message Alert -->
                                <div id="yearEditErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="yearEditErrorList"></ul>
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
                            <h5 class="modal-title" id="archiveModalLabel">Archive Income</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to archive this expense?
                            <input type="hidden" id="archiveId">
                            <!-- Success Message Alert -->
                            <div id="archiveSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                                Expense archived successfully!
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

        </div>
        <!-- End of 2nd Body Wrapper -->
    </div>
    <!-- End of Overall Body Wrapper -->

    <!-- BackEnd -->
    <script src="js/archive_legend.js">
    </script>
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

<?php
$conn->close();
?>