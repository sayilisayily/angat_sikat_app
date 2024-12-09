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
    <!--Custom CSS for Budget Overview-->
    <link rel="stylesheet" href="../budget_management/budget.css" />
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
                            <th>Actions</th>
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
                            
                            <td>
                                <button class='btn btn-primary btn-sm edit-btn' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#editExpenseModal' 
                                        data-id='{$row['expense_id']}'>
                                    <i class='fa-solid fa-pen'></i> Edit
                                </button>
                                <button class='btn btn-danger btn-sm archive-btn' 
                                        data-id='{$row['expense_id']}'>
                                    <i class='fa-solid fa-box-archive'></i> Archive
                                </button>
                            </td>
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
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form id="addExpenseForm" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addExpenseLabel">Add New Expense</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Category Field -->
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="activities">Activities</option>
                                        <option value="purchases">Purchases</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                </div>

                                <!-- Title Field -->
                                <div class="form-group mt-3">
                                    <label for="title">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>

                                <!-- Amount Field -->
                                <div class="form-group mt-3">
                                    <label for="amount">Amount</label>
                                    <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                                        required>
                                </div>

                                <!-- Reference (File Upload) Field -->
                                <div class="form-group mt-3">
                                    <label for="reference">Reference (File Upload)</label>
                                    <input type="file" class="form-control" id="reference" name="reference"
                                        accept=".pdf,.jpg,.png,.doc,.docx" required>
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
    <script src="js/expenses.js">
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