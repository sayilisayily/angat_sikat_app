<?php
include '../connection.php';
include '../session_check.php'; 

$sql = "SELECT * FROM expenses WHERE organization_id = $organization_id"; // Adjust the organization_id as needed
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Expenses Table</title>
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
                <h2 class="mb-4"><span class="text-warning fw-bold me-2">|</span> Expenses
                    <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addExpenseModal"
                        style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                        <i class="fa-solid fa-plus"></i> Add Expense
                    </button>
                </h2>
                <table id="expensesTable" class="table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Title</th>
                            <th>Amount</th>
                            <th>Reference</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    
                    echo "<tr>
                            <td>{$row['category']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['amount']}</td>
                            <td>{$row['reference']}</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No expenses found</td></tr>";
            }
            ?>
                    </tbody>
                </table>
            </div>

            <!-- Add Expense Modal -->
            <div class="modal fade" id="addExpenseModal" tabindex="-1" role="dialog" aria-labelledby="addExpenseLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form id="addExpenseForm" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addExpenseLabel">Add New Expense</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                
                                <input type="hidden" id="id" name="id">

                                <!-- Title Field -->
                                <div class="form-group">
                                    <label for="title">Title</label>
                                    <select name="title" id="title" class="form-control" required>
                                        <option value="">Select Title</option>
                                        <!-- Fetch titles from events, purchases, and maintenance -->
                                        <?php
                                        // Fetch events
                                        $event_query = "SELECT event_id, title, total_amount FROM events_summary WHERE type = 'Expense' AND archived = 0 AND organization_id = $organization_id";
                                        $event_result = mysqli_query($conn, $event_query);
                                        echo "<optgroup label='Events'>";
                                        while ($row = mysqli_fetch_assoc($event_result)) {
                                            echo '<option value="' . htmlspecialchars($row['title']) . '" 
                                                    data-id="' . htmlspecialchars($row['event_id']) . '"
                                                    data-amount="' . htmlspecialchars($row['total_amount']) . '">' 
                                                    . htmlspecialchars($row['title']) . '</option>';
                                        }
                                        echo "</optgroup>";
        
                                        // Fetch purchases
                                        $purchase_query = "SELECT purchase_id, title, total_amount FROM purchases_summary WHERE archived = 0 AND organization_id = $organization_id";
                                        $purchase_result = mysqli_query($conn, $purchase_query);
                                        echo "<optgroup label='Purchases'>";
                                        while ($row = mysqli_fetch_assoc($purchase_result)) {
                                            echo '<option value="' . htmlspecialchars($row['title']) . '" 
                                                    data-id="' . htmlspecialchars($row['purchase_id']) . '"
                                                    data-amount="' . htmlspecialchars($row['total_amount']) . '">' 
                                                    . htmlspecialchars($row['title']) . '</option>';
                                        }
                                        echo "</optgroup>";
        
                                        // Fetch maintenance
                                        $maintenance_query = "SELECT maintenance_id, title, total_amount FROM maintenance_summary WHERE archived = 0 AND organization_id = $organization_id";
                                        $maintenance_result = mysqli_query($conn, $maintenance_query);
                                        echo "<optgroup label='Mainteance and Other Expenses'>";
                                        while ($row = mysqli_fetch_assoc($maintenance_result)) {
                                            echo '<option value="' . htmlspecialchars($row['title']) . '" 
                                                    data-id="' . htmlspecialchars($row['maintenance_id']) . '"
                                                    data-amount="' . htmlspecialchars($row['total_amount']) . '">' 
                                                    . htmlspecialchars($row['title']) . '</option>';
                                        }
                                        echo "</optgroup>";
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group row mb-2">
                                    <div class="col">
                                        <label for="amount">Amount</label>
                                        <input type="number" class="form-control" id="amount" name="amount" readonly>
                                    </div>
                                </div>

                                <!-- Success Message Alert -->
                                <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                    Expense added successfully!
                                </div>

                                <!-- Error Message Alert -->
                                <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="errorList"></ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Add Expense</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


            <!-- Edit Expense Modal -->
            <div class="modal fade" id="editExpenseModal" tabindex="-1" role="dialog"
                aria-labelledby="editExpenseModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="editExpenseForm" action="update_expense.php" method="POST"
                            enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editExpenseModalLabel">Edit Expense</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" id="editItemId" name="item_id">

                                <div class="form-group">
                                    <label for="editCategory">Category</label>
                                    <select class="form-control" id="editCategory" name="category" required>
                                        <option value="" disabled selected>Select a category</option>
                                        <option value="activities">Activities</option>
                                        <option value="purchases">Purchases</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="editTitle">Title</label>
                                    <input type="text" class="form-control" id="editTitle" name="title" required>
                                </div>
                                <div class="form-group">
                                    <label for="editAmount">Amount</label>
                                    <input type="number" class="form-control" id="editAmount" name="amount" step="0.01"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="editReference">Reference (Upload File)</label>
                                    <input type="file" class="form-control" id="editReference" name="reference">
                                </div>

                                <!-- Success Message Alert -->
                                <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                    Expense updated successfully!
                                </div>
                                <!-- Error Message Alert -->
                                <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                    <ul id="errorList"></ul>
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
            <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="archiveModalLabel">Archive Expense</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to archive this expense?
                            <input type="hidden" id="archiveItemId">
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
    <script src="js/expenses.js"></script>
</body>

</html>

<?php
$conn->close();
?>