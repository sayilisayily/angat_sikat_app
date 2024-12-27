<?php
    // Include the database connection file
    include '../connection.php';
    include '../session_check.php';
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reports</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/angat sikat.png" />
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
    <!-- Google Charts -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <!--Bootstrap JS-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <style>
.left-sidebar {
    width: 250px; /* Full width of sidebar */
    border-radius: 0; /* Remove border radius */
    height: 100vh; /* Full height of viewport */
    position: fixed; /* Keep sidebar fixed */
    top: 0; /* Stick to top */
    bottom: 0; /* Stick to bottom */
    overflow-y: auto; /* Allow scrolling if content overflows */
}

.left-sidebar.collapsed {
    width: 80px; /* Adjust this for collapsed sidebar */
    height: 100vh; /* Maintain full height when collapsed */
}
</style>

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
                                    <li><a class="dropdown-item" href="../user/profile.html"><i class="bx bx-user"></i> My
                                            Profile</a></li>
                                    <li><a class="dropdown-item" href="../user/logout.php"><i
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

            <div class="container mt-5">
            <h2 class="mb-3"><span class="text-warning fw-bold me-2">|</span> Reports Management</h2>

            <!-- Report Type Cards -->
            <div class="row mb-4">
                <!-- Budget Request Card -->
                <div class="col-md">
                    <div 
                        class="card text-white gradient-card-1 mb-3 py-4" 
                        data-bs-toggle="modal" 
                        data-bs-target="#budgetRequestModal"
                        style="cursor: pointer;">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-file-invoice fa-2x mb-2"></i>
                            <h5 class="card-title">Budget Request</h5>
                        </div>
                    </div>
                </div>

                <!-- Project Proposal Card -->
                <div class="col-md">
                    <div class="card text-white gradient-card-2 mb-3 py-4"
                        data-bs-toggle="modal" 
                        data-bs-target="#projectProposalModal">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-lightbulb fa-2x mb-2"></i>
                            <h5 class="card-title">Project Proposal</h5>
                        </div>
                    </div>
                </div>

                <!-- Permit to Withdraw Card -->
                <div class="col-md">
                    <div class="card text-white gradient-card-1 mb-3 py-4"
                        data-bs-toggle="modal" 
                        data-bs-target="#permitModal">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-coins fa-2x mb-2"></i>
                            <h5 class="card-title">Permit to Withdraw</h5>
                        </div>
                    </div>
                </div>

                <!-- Collection Report Card -->
                <div class="col-md">
                    <div class="card text-white gradient-card-2 mb-3 py-4"
                        data-bs-toggle="modal" 
                        data-bs-target="#collectionModal">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-coins fa-2x mb-2"></i>
                            <h5 class="card-title">Collection Report</h5>
                        </div>
                    </div>
                </div>

                <!-- Permit to Withdraw Card -->
                <div class="col-md">
                    <div class="card text-white gradient-card-1 mb-3 py-4"
                        data-bs-toggle="modal" 
                        data-bs-target="#salesModal">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-coins fa-2x mb-2"></i>
                            <h5 class="card-title">Sales Report</h5>
                        </div>
                    </div>
                </div>

                <!-- Liquidation Card -->
                <div class="col-md">
                    <div class="card text-white gradient-card-2 mb-3 py-4"
                        data-bs-toggle="modal" 
                        data-bs-target="#liquidationModal">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-file-circle-check fa-2x mb-2"></i>
                            <h5 class="card-title">Liquidation Report</h5>
                        </div>
                    </div>
                </div>

                <!-- Accomplishment Card -->
                <div class="col-md">
                    <div class="card text-white gradient-card-1 mb-3 py-4">
                        <div class="card-body text-center">
                            <i class="fa-solid fa-trophy fa-2x mb-2"></i>
                            <h5 class="card-title">Accomplishment Report</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Table -->
            <div class="tablecontainer mt-3 p-4">
                <h4 class="mb-4">Reports
                    <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addReportModal"
                        style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                        <i class="fa-solid fa-plus"></i> Add Report
                    </button>
                </h4>
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>Report Name</th>
                            <th>Report Type</th>
                            <th>Uploaded On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT * FROM reports WHERE organization_id=$organization_id";
                        $result = mysqli_query($conn, $query);

                        if (!$result) {
                            die("Query failed: " . mysqli_error($conn));
                        }

                        while ($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['file_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['report_type']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <button class="btn btn-secondary"><i class="fa-solid fa-file-export"></i> Export</button>
                                <button class="btn btn-primary"><i class="fa-solid fa-print"></i> Print</button>
                                <button class="btn btn-danger"><i class="fa-solid fa-archive"></i> Archive</button>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

            <!-- Modals -->
            <!-- Budget Request Modal -->
            <div class="modal fade" id="budgetRequestModal" tabindex="-1" role="dialog" aria-labelledby="budgetRequestLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="budgetRequestForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="budgetRequestLabel">Generate Budget Request Report</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form fields -->
                                    <!-- Organization Name -->
                                    <?php
                                    // Fetch organization name associated with the current user's organization_id
                                    $org_query = "SELECT organization_name FROM organizations WHERE organization_id = $organization_id";
                                    $org_result = mysqli_query($conn, $org_query);

                                    if ($org_result && mysqli_num_rows($org_result) > 0) {
                                        $org_row = mysqli_fetch_assoc($org_result);
                                        $organization_name = $org_row['organization_name'];
                                    } else {
                                        $organization_name = "Unknown Organization"; // Fallback if no name is found
                                    }
                                    ?>

                                    
                                    
                                <div class="form-group row mb-2">
                                    <!-- Event Title -->
                                    <div class="col-12">
                                        <label for="event_title">Event Title</label>
                                        <select class="form-control" id="event_title" name="event_title" required>
                                            <option value="">Select Event Title</option>
                                            <?php
                                            // Fetch event titles with event_type 'expense' and accomplishment_status = 0
                                            $event_query = "SELECT title, event_id, event_start_date FROM events 
                                                            WHERE event_type = 'expense' 
                                                            AND accomplishment_status = 0 AND event_status != 'Approved'
                                                            AND organization_id = $organization_id";
                                            $result = mysqli_query($conn, $event_query);

                                            if ($result && mysqli_num_rows($result) > 0) {
                                                while ($row = mysqli_fetch_assoc($result)) {
                                                    echo '<option value="' . htmlspecialchars($row['title']) . '" 
                                                        data-event-id="' . htmlspecialchars($row['event_id']) . '" 
                                                        data-start-date="' . htmlspecialchars($row['event_start_date']) . '">' 
                                                        . htmlspecialchars($row['title']) . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">No events available</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <!-- Event Start Date -->
                                    <div class="col-12">
                                        <label for="event_start_date">Event Start Date</label>
                                        <input type="text" class="form-control" id="event_start_date" name="event_start_date" readonly>
                                    </div>
                                </div>
                                <!-- Hidden input fields-->
                                <input type="text" class="form-control" id="organization_name" name="organization_name" 
                                    value="<?php echo htmlspecialchars($organization_name); ?>" readonly>
                                <input type="text" class="form-control" id="event_id" name="event_id">

                                <!-- Success Message Alert -->
                                <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                    Budget request report generated successfully!
                                </div>
                                <!-- Error Message Alert -->
                                <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="errorList"></ul> <!-- List for showing validation errors -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Project Proposal Modal -->
            <div class="modal fade" id="projectProposalModal" tabindex="-1" role="dialog" aria-labelledby="projectProposalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="projectProposalForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="projectProposalLabel">Generate Project Proposal Report</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form fields -->

                                    
                                    
                                <div class="form-group row mb-2">
                                    <!-- Event Title -->
                                    <div class="col-12">
                                        <label for="proposal_title">Event Title</label>
                                        <select class="form-control" id="proposal_title" name="event_title" required>
                                            <option value="">Select Event Title</option>
                                            <?php
                                            // Fetch event titles with event_type 'expense' and accomplishment_status = 0
                                            $proposal_query = "SELECT title, event_id, event_start_date, event_venue FROM events 
                                                            WHERE accomplishment_status = 0 AND event_status != 'Approved'
                                                            AND organization_id = $organization_id";
                                            $proposal_result = mysqli_query($conn, $proposal_query);

                                            if ($proposal_result && mysqli_num_rows($proposal_result) > 0) {
                                                while ($proposal = mysqli_fetch_assoc($proposal_result)) {
                                                    echo '<option value="' . htmlspecialchars($proposal['title']) . '" 
                                                        data-event-id="' . htmlspecialchars($proposal['event_id']) . '" 
                                                        data-venue="' . htmlspecialchars($proposal['event_venue']) . '" 
                                                        data-start-date="' . htmlspecialchars($proposal['event_start_date']) . '">' 
                                                        . htmlspecialchars($proposal['title']) . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">No events available</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <!-- Event Start Date -->
                                    <div class="col-12">
                                        <label for="event_start_date">Event Start Date</label>
                                        <input type="text" class="form-control" id="proposal_start_date" name="event_start_date" readonly>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <!-- Event Start Date -->
                                    <div class="col-12">
                                        <label for="proposal_venue">Event Venue</label>
                                        <input type="text" class="form-control" id="proposal_venue" name="event_venue" readonly>
                                        </div>
                                </div>
                                <!-- Hidden input fields-->
                                <input type="text" class="form-control" id="proposal_name" name="organization_name" 
                                    value="<?php echo htmlspecialchars($organization_name); ?>" readonly>
                                <input type="text" class="form-control" id="proposal_id" name="event_id">
                                

                                <!-- Success Message Alert -->
                                <div id="successMessage2" class="alert alert-success d-none mt-3" role="alert">
                                    Project proposal report generated successfully!
                                </div>
                                <!-- Error Message Alert -->
                                <div id="errorMessage2" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="errorList2"></ul> <!-- List for showing validation errors -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Permit to Withdraw Modal -->
            <div class="modal fade" id="permitModal" tabindex="-1" role="dialog" aria-labelledby="permitLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="permitForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="permitLabel">Generate Project Proposal Report</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form fields -->
                                    
                                <div class="form-group row mb-2">
                                    <!-- Event Title -->
                                    <div class="col-12">
                                        <label for="permit_title">Event Title</label>
                                        <select class="form-control" id="permit_title" name="event_title" required>
                                            <option value="">Select Event Title</option>
                                            <?php
                                            // Fetch event titles with event_type 'expense' and accomplishment_status = 0
                                            $permit_query = "SELECT title, event_id, total_amount FROM events 
                                                            WHERE accomplishment_status = 0 AND event_status = 'Approved'
                                                            AND organization_id = $organization_id";
                                            $permit_result = mysqli_query($conn, $permit_query);

                                            if ($permit_result && mysqli_num_rows($permit_result) > 0) {
                                                while ($permit = mysqli_fetch_assoc($permit_result)) {
                                                    echo '<option value="' . htmlspecialchars($permit['title']) . '" 
                                                        data-event-id="' . htmlspecialchars($permit['event_id']) . '" 
                                                        data-amount="' . htmlspecialchars($permit['total_amount']) . '">' 
                                                        . htmlspecialchars($permit['title']) . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">No events available</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <!-- Event Start Date -->
                                    <div class="col-12">
                                        <label for="total_amount">Amount</label>
                                        <input type="text" class="form-control" id="total_amount" name="total_amount" readonly>
                                        </div>
                                </div>
                                <!-- Hidden input fields-->
                                <input type="text" class="form-control" id="permit_name" name="organization_name" 
                                    value="<?php echo htmlspecialchars($organization_name); ?>" readonly>
                                <input type="text" class="form-control" id="permit_id" name="event_id">
                                

                                <!-- Success Message Alert -->
                                <div id="successMessage3" class="alert alert-success d-none mt-3" role="alert">
                                    Permit to Withdraw generated successfully!
                                </div>
                                <!-- Error Message Alert -->
                                <div id="errorMessage3" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="errorList3"></ul> <!-- List for showing validation errors -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Permit to Withdraw Modal -->
            <div class="modal fade" id="liquidationModal" tabindex="-1" role="dialog" aria-labelledby="liquidationLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="liquidationForm">
                            <div class="modal-header">
                                <h5 class="modal-title" id="liquidationLabel">Generate Liquidation Report</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Form fields -->
                                    
                                <div class="form-group row mb-2">
                                    <!-- Event Title -->
                                    <div class="col-12">
                                        <label for="liquidation_title">Event Title</label>
                                        <select class="form-control" id="liquidation_title" name="event_title" required>
                                            <option value="">Select Event Title</option>
                                            <?php
                                            // Fetch event titles with event_type 'expense' and accomplishment_status = 0
                                            $liquidation_query = "SELECT title, event_id, total_amount FROM events_summary
                                                            AND organization_id = $organization_id";
                                            $liquidation_result = mysqli_query($conn, $liquidation_query);

                                            if ($liquidation_result && mysqli_num_rows($liquidation_result) > 0) {
                                                while ($liquidation = mysqli_fetch_assoc($liquidation_result)) {
                                                    echo '<option value="' . htmlspecialchars($liquidation['title']) . '" 
                                                        data-event-id="' . htmlspecialchars($liquidation['event_id']) . '" 
                                                        data-amount="' . htmlspecialchars($liquidation['total_amount']) . '">' 
                                                        . htmlspecialchars($liquidation['title']) . '</option>';
                                                }
                                            } else {
                                                echo '<option value="">No events available</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group row mb-2">
                                    <!-- Event Start Date -->
                                    <div class="col-12">
                                        <label for="liquidation_amount">Amount</label>
                                        <input type="text" class="form-control" id="liquidation_amount" name="total_amount" readonly>
                                        </div>
                                </div>
                                <!-- Hidden input fields-->
                                <input type="text" class="form-control" id="liquidation_name" name="organization_name" 
                                    value="<?php echo htmlspecialchars($organization_name); ?>" readonly>
                                <input type="text" class="form-control" id="liquidation_id" name="event_id">
                                

                                <!-- Success Message Alert -->
                                <div id="successMessage4" class="alert alert-success d-none mt-3" role="alert">
                                    Liquidation Report generated successfully!
                                </div>
                                <!-- Error Message Alert -->
                                <div id="errorMessage4" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="errorList4"></ul> <!-- List for showing validation errors -->
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Generate Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <script src="js/reports.js">

            </script>

        </div>
        <!-- End of 2nd Body Wrapper -->
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
