<?php
include 'connection.php';
include '../session_check.php'; 
include '../user_query.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
    header("Location: ../user/login.html");
    exit();
}

$sql = "SELECT * FROM financial_plan WHERE organization_id = $organization_id";
$result = $conn->query($sql);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Plan of Activities</title>
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
        <?php include '../sidebar.php'; ?>

        <?php include '../navbar.php';?>
            <div class="container mt-5 p-5">
                <h2 class="mb-4"><span class="text-warning fw-bold me-2">|</span> Financial Plan
                    <button class="btn add-btn btn-primary ms-3" id="add-btn" data-bs-toggle="modal" data-bs-target="#addPlanModal"
                    style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                        <i class="fa-solid fa-plus"></i> Add Plan
                    </button>
                </h2>
                <table id="financialPlanTable" class="table">
                    <thead>
                        <tr>
                            <th>Projected Income</th>
                            <th style="text-align: center;">Proposed Date</th>
                            <th style="text-align: center;">Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $has_income = false;
                        $has_expenses = false;

                        if ($result->num_rows > 0) {
                            // Loop through income records
                            while ($row = $result->fetch_assoc()) {
                                if ($row['type'] === 'Income') {
                                    $has_income = true;
                                    echo "<tr>
                                            <td>{$row['title']}</td>
                                            <td style='text-align: center;'>" . date('F j, Y', strtotime($row['date'])) . "</td>
                                            <td style='text-align: center;'>{$row['amount']}</td>
                                            <td>
                                                <button class='btn btn-primary btn-sm edit-btn mb-3' 
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#editPlanModal' 
                                                        data-id='{$row['plan_id']}'>
                                                    <i class='fa-solid fa-pen'></i> Edit
                                                </button>
                                                <button class='btn btn-danger btn-sm delete-btn mb-3' 
                                                        data-id='{$row['plan_id']}'>
                                                    <i class='fa-solid fa-trash'></i> Delete
                                                </button>
                                            </td>
                                        </tr>";
                                }
                            }

                            if (!$has_income) {
                                echo "<tr><td colspan='4' class='text-center'>No projected income found</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center'>No records found</td></tr>";
                        }
                        ?>
                    </tbody>

                    <thead>
                        <tr>
                            <th colspan="4">Projected Expenses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Reset result pointer for expense segregation
                        $result->data_seek(0); // Move back to the beginning of the result set

                        $categories = ['Activities', 'Purchases', 'Maintenance and Other Expenses'];
                        foreach ($categories as $category) {
                            echo "<tr><th colspan='4'>$category</th></tr>";

                            // Loop through expense records by category
                            $has_expenses_for_category = false;
                            while ($row = $result->fetch_assoc()) {
                                if ($row['type'] === 'Expense' && $row['category'] === $category) {
                                    $has_expenses = true;
                                    $has_expenses_for_category = true;
                                    echo "<tr>
                                            <td>{$row['title']}</td>";
                                
                                    if ($category === 'Activities') {
                                        echo "<td style='text-align: center;'>" . date('F j, Y', strtotime($row['date'])) . "</td>";
                                    } else {
                                        echo "<td></td>";
                                    }
                                            
                                            echo "
                                            <td style='text-align: center;'>{$row['amount']}</td>
                                            <td>
                                                <button class='btn btn-primary btn-sm edit-btn mb-3' 
                                                        data-bs-toggle='modal' 
                                                        data-bs-target='#editPlanModal' 
                                                        data-id='{$row['plan_id']}'>
                                                    <i class='fa-solid fa-pen'></i> Edit
                                                </button>
                                                <button class='btn btn-danger btn-sm delete-btn mb-3' 
                                                        data-id='{$row['plan_id']}'>
                                                    <i class='fa-solid fa-trash'></i> Delete
                                                </button>
                                            </td>
                                        </tr>";
                                }
                            }

                            if (!$has_expenses_for_category) {
                                echo "<tr><td colspan='4' class='text-center'>No expenses for $category</td></tr>";
                            }

                            // Reset pointer for the next category
                            $result->data_seek(0);
                        }

                        if (!$has_expenses) {
                            echo "<tr><td colspan='4' class='text-center'>No projected expenses found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <!-- End of Overall Body Wrapper -->

    <!-- Add Plan Modal -->
    <div class="modal fade" id="addPlanModal" tabindex="-1" role="dialog" aria-labelledby="addPlanLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="addPlanForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addPlanLabel">Add New Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form fields -->
                        <div class="form-group mb-2">
                            <div class="col">
                                <label for="title">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="type">Type</label>
                                <select class="form-control" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="Income">Income</option>
                                    <option value="Expense">Expense</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    // Fetch categories
                                    $category_query = "SELECT category FROM categories"; // Only fetch the category column
                                    $category_result = mysqli_query($conn, $category_query);

                                    if (!$category_result) {
                                        // Query error
                                        echo '<option value="">Error loading categories</option>';
                                    } else {
                                        if (mysqli_num_rows($category_result) > 0) {
                                            while ($category_row = mysqli_fetch_assoc($category_result)) {
                                                // Use htmlspecialchars to prevent XSS
                                                echo '<option value="' . htmlspecialchars($category_row['category']) . '">' . htmlspecialchars($category_row['category']) . '</option>';
                                            }
                                        } else {
                                            // No categories available
                                            echo '<option value="">No categories available</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="date">Proposed Date</label>
                                <input type="date" class="form-control" id="date" name="date" required>
                            </div>
                            <div class="col">
                                <label for="amount">Amount</label>
                                <input type="number" class="form-control" id="amount" name="amount" min="0" step="0.01" required>
                            </div>
                        </div>

                        <!-- Success Message Alert -->
                        <div id="successMessage1" class="alert alert-success d-none mt-3" role="alert">
                            Plan added successfully!
                        </div>
                        <!-- Error Message Alert -->
                        <div id="errorMessage1" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList1"></ul> <!-- List for showing validation errors -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">Add Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Edit Plan Modal -->
    <div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog" aria-labelledby="editPlanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="editPlanForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPlanModalLabel">Edit Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden field for plan ID -->
                        <input type="hidden" id="editPlanId" name="edit_plan_id">

                        <!-- Title -->
                        <div class="form-group mb-2">
                            <label for="editTitle">Title</label>
                            <input type="text" class="form-control" id="editTitle" name="edit_title" required>
                        </div>

                        <!-- Type and Category -->
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="editType">Type</label>
                                <select class="form-control" id="editType" name="edit_type" required>
                                    <option value="">Select Type</option>
                                    <option value="Income">Income</option>
                                    <option value="Expense">Expense</option>
                                </select>
                            </div>
                            <div class="col">
                                <label for="editCategory">Category</label>
                                <select class="form-control" id="editCategory" name="edit_category" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    // Fetch categories
                                    $category_query = "SELECT category FROM categories"; // Only fetch the category column
                                    $category_result = mysqli_query($conn, $category_query);

                                    if (!$category_result) {
                                        // Query error
                                        echo '<option value="">Error loading categories</option>';
                                    } else {
                                        if (mysqli_num_rows($category_result) > 0) {
                                            while ($category_row = mysqli_fetch_assoc($category_result)) {
                                                // Use htmlspecialchars to prevent XSS
                                                echo '<option value="' . htmlspecialchars($category_row['category']) . '">' . htmlspecialchars($category_row['category']) . '</option>';
                                            }
                                        } else {
                                            // No categories available
                                            echo '<option value="">No categories available</option>';
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Date and Amount -->
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="editDate">Proposed Date</label>
                                <input type="date" class="form-control" id="editDate" name="edit_date">
                            </div>
                            <div class="col">
                                <label for="editAmount">Amount</label>
                                <input type="number" class="form-control" id="editAmount" name="edit_amount" min="0" step="0.01" required>
                            </div>
                        </div>

                        <!-- Alerts -->
                        <div id="successMessage2" class="alert alert-success d-none mt-3" role="alert">
                            Plan updated successfully!
                        </div>
                        <div id="errorMessage2" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList2"></ul>
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



    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Archive Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this plan?
                    <input type="hidden" id="deletePlanId">
                    <!-- Success Message Alert -->
                    <div id="successMessage3" class="alert alert-success d-none mt-3" role="alert">
                            Plan deleted successfully!
                        </div>
                        <!-- Error Message Alert -->
                        <div id="errorMessage3" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList3"></ul> <!-- List for showing validation errors -->
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- BackEnd -->
    <script src="js/financial_plan.js"></script>
</body>

</html>

<?php
$conn->close();
?>