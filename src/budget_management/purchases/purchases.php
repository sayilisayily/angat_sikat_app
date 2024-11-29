<?php
include '../../connection.php';
include '../../session_check.php'; 
include '../../user_query.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
    header("Location: ../../user/login.html");
    exit();
}

$sql = "SELECT * FROM purchases WHERE archived = 0 AND organization_id = $organization_id";
$result = $conn->query($sql);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Purchases Table</title>
    <link rel="shortcut icon" type="image/png" href="../../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../../assets/css/styles.min.css" />
    <!--Custom CSS for Sidebar-->
    <link rel="stylesheet" href="../../html/sidebar.css" />
    <!--Custom CSS for Activities-->
    <link rel="stylesheet" href="../../activity_management/css/activities.css" />
    <!--Boxicon-->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <!--Font Awesome-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Lordicon (for animated icons) -->
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <!--Bootstrap Script-->
    <script src="../../assets/libs/jquery/dist/jquery.min.js"></script>
    <script src="../../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/sidebarmenu.js"></script>
    <script src="../../assets/js/app.min.js"></script>
    <script src="../../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
    <script src="../../assets/libs/simplebar/dist/simplebar.js"></script>
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
        <?php include '../../sidebar.php'; ?>

        <?php include '../../navbar.php';?>
    </div>
    <!-- End of Overall Body Wrapper -->

    
    <div class="container mt-5 p-5">
        <h2 class="mb-4 d-flex align-items-center justify-content-between">
            <div>
                <span class="text-warning fw-bold me-2">|</span> Purchases
                <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addPurchaseModal"
                style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                    <i class="fa-solid fa-plus"></i> Add Purchase
                </button>
            </div>
            <a href="purchases_archive.php" class="text-gray text-decoration-none fw-bold" 
            style="font-size: 14px;">
                View Archive
            </a>
        </h2>
            <table id="purchasesTable" class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Completed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $checked = $row['completion_status'] ? 'checked' : '';
                        $disabled = ($row['purchase_status'] !== 'Approved') ? 'disabled' : '';
                        echo "<tr>
                                <td><a class='link-offset-2 link-underline link-underline-opacity-0' href='purchase_details.php?purchase_id={$row['purchase_id']}'>{$row['title']}</a></td>
                                <td>{$row['total_amount']}</td>
                                <td>";
                        
                        // Display purchase status with appropriate badge
                        if ($row['purchase_status'] == 'Pending') {
                            echo "<span class='badge rounded-pill pending'>Pending</span>";
                        } elseif ($row['purchase_status'] == 'Approved') {
                            echo "<span class='badge rounded-pill approved'>Approved</span>";
                        } elseif ($row['purchase_status'] == 'Disapproved') {
                            echo "<span class='badge rounded-pill disapproved'>Disapproved</span>";
                        }

                        echo "</td>
                            <td>
                                <input type='checkbox' class='form-check-input' onclick='showConfirmationModal({$row['purchase_id']}, this.checked)' $checked $disabled>
                            </td>
                            <td>
                                <button class='btn btn-primary btn-sm edit-btn mb-3' data-bs-toggle='modal' data-bs-target='#editPurchaseModal' data-id='{$row['purchase_id']}'>
                                    <i class='fa-solid fa-pen'></i> Edit
                                </button>
                                <button class='btn btn-danger btn-sm archive-btn mb-3' data-id='{$row['purchase_id']}'><i class='fa-solid fa-box-archive'></i> Archive</button>
                            </td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No purchases found</td></tr>";
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
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Completion Status Change</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to change the completion status of this purchase?
                    <!-- Success Message Alert -->
                    <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                        Status updated successfully!
                    </div>
                    <!-- Error Message Alert -->
                    <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                        <ul id="errorList"></ul> <!-- List for showing validation errors -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmUpdateBtn">Confirm</button>
                </div>
                
            </div>
        </div>
    </div>

    <!-- Add Purchase Modal -->
    <div class="modal fade" id="addPurchaseModal" tabindex="-1" role="dialog" aria-labelledby="addPurchaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form id="addPurchaseForm">
            <div class="modal-header">
            <h5 class="modal-title" id="addPurchaseLabel">Add New Purchase</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <div class="form-group row mb-2">
                <div class="col">
                    <label for="title">Purchase Title</label>
                    <!-- Purchase title dropdown -->
                    <select class="form-control" id="title" name="title">
                        <option value="">Select Purchase Title</option>
                        <?php
                        // Query to fetch titles with category 'Purchases'
                        $title_query = "SELECT title, amount FROM financial_plan WHERE category = 'Purchases' AND organization_id = $organization_id";
                        $title_result = mysqli_query($conn, $title_query);
                        if ($title_result && mysqli_num_rows($title_result) > 0) {
                            while ($row = mysqli_fetch_assoc($title_result)) {
                                echo '<option value="' . htmlspecialchars($row['title']) . '" data-amount="' . htmlspecialchars($row['amount']) . '">' . htmlspecialchars($row['title']) . '</option>';
                            }
                        }
                        
                        ?>
                    </select>
                </div>
                <!-- Success Message Alert -->
                <div id="addSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                        Purchase added successfully!
                </div>  
                <!-- Error Message Alert -->
                <div id="addErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                    <ul id="addErrorList"></ul>
                </div>
            </div>

            
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Add Purchase</button>
            </div>
        </form>
        </div>
    </div>
    </div>

    <!-- Edit Purchase Modal -->
    <div class="modal fade" id="editPurchaseModal" tabindex="-1" role="dialog" aria-labelledby="editPurchaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
        <form id="editPurchaseForm">
            <div class="modal-header">
            <h5 class="modal-title" id="editPurchaseModalLabel">Edit Purchase</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <input type="hidden" id="editPurchaseId" name="edit_purchase_id">
            
            <div class="form-group">
                <label for="editPurchaseTitle">Purchase Title</label>
                <input type="text" class="form-control" id="editPurchaseTitle" name="title" required>
            </div>
            <!-- Success Message Alert -->
            <div id="successMessage2" class="alert alert-success d-none mt-3" role="alert">
                    Purchase added successfully!
            </div>  
            <!-- Error Message Alert -->
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

    <!-- Archive Confirmation Modal -->
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="archiveModalLabel">Archive Purchase</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to archive this Purchase?
                    <input type="hidden" id="archivePurchaseId">
                    <!-- Success Message Alert -->
                    <div id="archiveSuccessMessage" class="alert alert-success d-none mt-3" role="alert">
                            Purchase archived successfully!
                    </div>  
                    <!-- Error Message Alert -->
                    <div id="archiveErrorMessage" class="alert alert-danger d-none mt-3" role="alert">
                        <ul id="archiveErrorList"></ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirmArchiveBtn" class="btn btn-danger">Archive</button>
                </div>
                
            </div>
        </div>
    </div>

    <!-- BackEnd -->
    <script src="../js/purchases.js"></script>
</body>

</html>

<?php
$conn->close();
?>