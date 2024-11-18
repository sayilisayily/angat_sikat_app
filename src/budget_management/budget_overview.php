<?php
    // Include the database connection file
    include 'connection.php';
    include '../session_check.php';

    // Check if user is logged in and has officer role
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
        header("Location: ../user/login.html");
        exit();
    }
    // Fetch organization data
    
    $query = "SELECT 
                beginning_balance,
                cash_on_bank,
                cash_on_hand,
                balance
            FROM 
                organizations 
            WHERE 
                organization_id = $organization_id";
    $result = mysqli_query($conn, $query);

    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    $row = mysqli_fetch_assoc($result);

    $beginning_balance = $row['beginning_balance'];
    $cash_on_bank = $row['cash_on_bank'];
    $cash_on_hand = $row['cash_on_hand'];
    $balance = $row['balance']; 
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Budget Management</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <!--Custom CSS for Sidebar-->
    <link rel="stylesheet" href="../html/sidebar.css" />
    <!--Custom CSS for Budget Overview-->
    <link rel="stylesheet" href="../budget_management/budget.css" />
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

                <h2 class="mb-3"> Budget Management </h2>
                <!-- Balance Card -->
                <div class="row">
                    <div class="col-md">
                        <div class="card text-white gradient-card mb-3 py-4">
                            <div class="card-header">Balance</div>
                            <div class="card-body">
                                <h3 class="card-title">₱
                                    <?php echo number_format($balance, 2); ?>
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <!-- Beginning Balance Card -->
                    <div class="col-md-4">
                        <div class="card gradient-card-2 text-white mb-3 py-4">
                            <div class="card-body">
                                <h5 class="card-title">₱
                                    <?php echo number_format($beginning_balance, 2); ?>
                                </h5>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <span>Beginning Balance</span>
                                <button class="btn btn-light edit-balance-btn" data-bs-toggle="modal"
                                    data-bs-target="#editBeginningBalanceModal"
                                    data-id="<?php echo $organization_id; ?>"><i class="fa-solid fa-pen"></i>
                                    Edit</button>
                            </div>
                        </div>
                    </div>

                    <!-- Cash on Bank Card -->
                    <div class="col-md-4">
                        <div class="card text-white gradient-card-3 mb-3 py-4">
                            <div class="card-body">
                                <h5 class="card-title">₱
                                    <?php echo number_format($cash_on_bank, 2); ?>
                                </h5>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <span>Cash on Bank</span>
                                <button class="btn btn-light edit-balance-btn" data-bs-toggle="modal"
                                    data-bs-target="#editCashOnBankModal" data-id="<?php echo $organization_id; ?>"><i
                                        class="fa-solid fa-pen"></i> Edit</button>
                            </div>
                        </div>
                    </div>


                    <!-- Cash on Hand Card -->
                    <div class="col-md-4">
                        <div class="card text-white gradient-card-1 mb-3 py-4">
                            <div class="card-body">
                                <h5 class="card-title">₱
                                    <?php echo number_format($cash_on_hand, 2); ?>
                                </h5>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <span>Cash on Hand</span>
                                <button class="btn btn-light edit-balance-btn" data-bs-toggle="modal"
                                    data-bs-target="#editCashOnHandModal" data-id="<?php echo $organization_id; ?>"><i
                                        class="fa-solid fa-pen"></i> Edit</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <h4 class="col">
                        <div class="vr"></div> Budget Allocation
                    </h4>
                    <h4 class="col"> Budget Status </h4>
                </div>
                <div class="row">

                    <div id="budgetStructure" class="col" style="width: 500px; height: 350px;"></div>
                    <div id="budgetStatus" class="col" style="width: 500px; height: 350px;"></div>
                </div>

                <div class="tablecontainer mt-3 p-4">
                    <h4 class="mb-4"> Budget Allocation </h4>
                    <table class="table">
                        <thead class="thead-light">
                            <tr>
                                <th>Category</th>
                                <th>Allocated Budget</th>
                                <th>Total Spent</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $query = "SELECT * FROM budget_allocation";
                                $result = mysqli_query($conn, $query);
                
                                if (!$result) {
                                    die("Query failed: " . mysqli_error($conn));
                                }
                
                                while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td>
                                        <?php echo htmlspecialchars($row['category']); ?>
                                    </td>
                                    <td>₱
                                        <?php echo number_format($row['allocated_budget'], 2); ?>
                                    </td>
                                    <td>₱
                                        <?php echo number_format($row['total_spent'], 2); ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary edit-btn"
                                            data-id="<?php echo $row['allocation_id']; ?>" data-bs-toggle="modal"
                                            data-bs-target="#editBudgetModal"><i class="fa-solid fa-pen"></i> Edit</button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Modals -->
            <!-- Edit Beginning Balance Modal -->
            <div class="modal fade" id="editBeginningBalanceModal" tabindex="-1"
                aria-labelledby="editBeginningBalanceLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editBeginningBalanceLabel">Edit Beginning Balance</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editBeginningBalanceForm">
                                <div class="mb-3">
                                    <label for="beginningBalance" class="form-label">Beginning Balance</label>
                                    <input type="number" step="0.01" class="form-control" id="beginningBalance"
                                        name="beginning_balance" required>
                                </div>
                                <input type="hidden" name="organization_id" value="<?php echo $organization_id; ?>">
                            </form>
                            <!-- Success Message Alert -->
                            <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                Beginning Balance updated successfully!
                            </div>
                            <!-- Error Message Alert -->
                            <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                <ul id="errorList"></ul> <!-- List for showing validation errors -->
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="editBeginningBalanceForm" class="btn btn-primary">Save
                                changes</button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Edit Cash on Bank Modal -->
            <div class="modal fade" id="editCashOnBankModal" tabindex="-1" aria-labelledby="editCashOnBankLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCashOnBankLabel">Edit Cash on Bank</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editCashOnBankForm">
                                <div class="mb-3">
                                    <label for="cashOnBank" class="form-label">Cash on Bank</label>
                                    <input type="number" step="0.01" class="form-control" id="cashOnBank"
                                        name="cash_on_bank" required>
                                </div>
                                <input type="hidden" name="organization_id" value="<?php echo $organization_id; ?>">
                            </form>
                            <!-- Success Message Alert -->
                            <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                Cash on Bank updated successfully!
                            </div>
                            <!-- Error Message Alert -->
                            <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                <ul id="errorList"></ul> <!-- List for showing validation errors -->
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="editCashOnBankForm" class="btn btn-primary">Save
                                changes</button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Edit Cash on Hand Modal -->
            <div class="modal fade" id="editCashOnHandModal" tabindex="-1" aria-labelledby="editCashOnHandLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editCashOnHandLabel">Edit Cash on Hand</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editCashOnHandForm">
                                <div class="mb-3">
                                    <label for="cashOnHand" class="form-label">Cash on Hand</label>
                                    <input type="number" step="0.01" class="form-control" id="cashOnHand"
                                        name="cash_on_hand" required>
                                </div>
                                <input type="hidden" name="organization_id" value="<?php echo $organization_id; ?>">
                            </form>

                            <!-- Success Message Alert -->
                            <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                Cash on Hand updated successfully!
                            </div>
                            <!-- Error Message Alert -->
                            <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                <ul id="errorList"></ul> <!-- List for showing validation errors -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="editCashOnHandForm" class="btn btn-primary">Save
                                changes</button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Edit Budget Modal -->
            <div class="modal fade" id="editBudgetModal" tabindex="-1" aria-labelledby="editBudgetModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editBudgetModalLabel">Edit Budget</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editBudgetForm">
                                <input type="hidden" id="allocationId" name="allocation_id">
                                <!-- Hidden input for allocation ID -->
                                <div class="mb-3">
                                    <label for="allocated_budget" class="form-label">Allocated Budget</label>
                                    <input type="number" class="form-control" id="allocated_budget"
                                        name="allocated_budget" required>
                                </div>
                                <input type="hidden" name="organization_id" value="<?php echo $organization_id; ?>">
                            </form>
                            <!-- Success Message Alert -->
                            <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                Budget updated successfully!
                            </div>
                            <!-- Error Message Alert -->
                            <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                <ul id="errorList"></ul> <!-- List for showing validation errors -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" form="editBudgetForm" class="btn btn-primary">Save changes</button>
                        </div>

                    </div>
                </div>
            </div>

            <script src="js/budget_overview.js">

            </script>

            <script type="text/javascript">
                google.charts.load("current", { packages: ["corechart"] });
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    // Prepare the data directly from the PHP code
                    var data = google.visualization.arrayToDataTable([
                        ['Category', 'Amount'],
                        <?php

                            // Fetch budget allocation data from the database
                            $query = "SELECT category, allocated_budget FROM budget_allocation";
                            $result = mysqli_query($conn, $query);

                            // Loop through the results and output them as JavaScript array elements
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "['".$row['category']. "', ". (float)$row['allocated_budget']. "],";
                            }
                        ?>
                    ]);

                    var options = {
                        pieHole: 0.6,
                        colors: ['#FFDB29', '#5BD2DA', '#595FD7']
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('budgetStructure'));
                    chart.draw(data, options);
                }
            </script>

            <script type="text/javascript">
                google.charts.load("current", { packages: ["corechart"] });
                google.charts.setOnLoadCallback(drawChart);

                function drawChart() {
                    // Prepare the data directly from the PHP code
                    var data = google.visualization.arrayToDataTable([
                        ['Category', 'Amount'],
                        <?php
                            // Fetch balance and total expense data from the database
                            $query = "SELECT balance FROM organizations WHERE organization_id = $organization_id"; // Fetch only balance
                            $result = mysqli_query($conn, $query);

                            // Fetch the balance
                            if ($row = mysqli_fetch_assoc($result)) {
                                echo "['Balance', ". (float)$row['balance']. "],";
                            }

                            // Now fetch total expenses from the expenses table
                            $expenses_query = "SELECT SUM(amount) AS total_expenses FROM expenses WHERE organization_id = $organization_id";
                            $expenses_result = mysqli_query($conn, $expenses_query);

                            // Fetch the total expenses
                            if ($expenses_row = $expenses_result -> fetch_assoc()) {
                                echo "['Expense', ". (float)$expenses_row['total_expenses']. "],";
                            }
                        ?>
                    ]);


                    var options = {
                        pieHole: 0.6,
                        colors: ['#E6E6E6', '#FF7575'],
                    };

                    var chart = new google.visualization.PieChart(document.getElementById('budgetStatus'));
                    chart.draw(data, options);
                }
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
