<?php
// Database connection
require 'connection.php';
include '../session_check.php';
include '../user_query.php';

// Check if 'event_id' is passed in the URL
if (isset($_GET['event_id']) && !empty($_GET['event_id'])) {
    $event_id = intval($_GET['event_id']); // Get and sanitize the event_id from the URL

    // Fetch event details
    $stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if event exists
    if ($result->num_rows > 0) {
        $event = $result->fetch_assoc();
    } else {
        echo "No event found.";
        exit;
    }

    // Fetch items for Financial Plan
    $itemStmt = $conn->prepare("SELECT * FROM event_items WHERE event_id = ?");
    if ($itemStmt === false) {
        die('Prepare for items failed: ' . $conn->error);
    }
    $itemStmt->bind_param("i", $event_id);
    $itemStmt->execute();
    $itemsResult = $itemStmt->get_result();
    $items = [];
    if ($itemsResult->num_rows > 0) {
        while ($row = $itemsResult->fetch_assoc()) {
            $items[] = $row;
        }
    }
    $itemStmt->close();

    // Fetch items for Financial Summary if the event is accomplished
    $summaryItems = [];
    if ($event['accomplishment_status'] == 1) { // Check accomplishment status
        $summaryStmt = $conn->prepare("SELECT * FROM event_summary_items WHERE event_id = ?");
        if ($summaryStmt === false) {
            die('Prepare for summary items failed: ' . $conn->error);
        }
        $summaryStmt->bind_param("i", $event_id);
        $summaryStmt->execute();
        $summaryResult = $summaryStmt->get_result();
        if ($summaryResult->num_rows > 0) {
            while ($row = $summaryResult->fetch_assoc()) {
                $summaryItems[] = $row;
            }
        }
        $summaryStmt->close();
    }
} else {
    echo "No event ID provided.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Event Financial Details</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
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
        <?php include '../sidebar.php'; include '../navbar.php';?>

            <div class="container mt-5 p-4">
                <h2><span class="text-warning fw-bold me-2">|</span> Event Details</h2>
                <!-- Tabs for Financial Plan and Financial Summary -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="financial-plan-tab" data-bs-toggle="tab" href="#financial-plan"
                            role="tab" aria-controls="financial-plan" aria-selected="true">Financial Plan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="financial-summary-tab" data-bs-toggle="tab" href="#financial-summary"
                            role="tab" aria-controls="financial-summary" aria-selected="false">Financial Summary</a>
                    </li>
                </ul>


                <div class="tab-content mt-4 mx-3">
                    <!-- Financial Plan Tab -->
                    <div class="tab-pane fade show active" id="financial-plan" role="tabpanel" aria-labelledby="financial-plan-tab">

                        <!-- Event Information -->
                        <h4>Title:
                            <?php echo $event['title']; ?>
                        </h4>
                        <p>Venue:
                            <?php echo $event['event_venue']; ?>
                        </p>
                        <p>Start Date:
                            <?php echo $event['event_start_date']; ?>
                        </p>
                        <p>End Date:
                            <?php echo $event['event_end_date']; ?>
                        </p>

                        <h4>Items<?php if ($event['accomplishment_status'] === 0): ?>
                        <button class="btn btn-sm btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addItemModal"><i class="fa-solid fa-plus"></i> Add Item</button>
                        <?php endif; ?></h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Amount</th>
                                    <?php
                                    if ($event['event_type'] === 'Income') {
                                        echo "<th> Markup </th>";
                                    }
                                    ?>
                                    <th>Total Amount</th>
                                    <?php
                                    if ($event['accomplishment_status'] === 0) {
                                        echo "<th>Actions</th>";
                                    }
                                    ?>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($items)) {
                                    foreach ($items as $item) {
                                        $total_amount = $item['quantity'] * $item['amount'];
                                        echo "<tr>
                                                <td>{$item['description']}</td>
                                                <td>{$item['quantity']}</td>
                                                <td>{$item['unit']}</td>
                                                <td>{$item['amount']}</td>
                                                <td>{$total_amount}</td>
                                                <td>";
                                        if ($event['accomplishment_status'] === 0) {
                                            echo "<button class='btn edit-btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#editItemModal' data-id='{$item['item_id']}' data-description='{$item['description']}' data-quantity='{$item['quantity']}' data-unit='{$item['unit']}' data-amount='{$item['amount']}'><i class='fa-solid fa-pen'></i> Edit</button>
                                                <button class='btn delete-btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#deleteItemModal' data-id='{$item['item_id']}'><i class='fa-solid fa-trash'></i> Delete</button>";
                                        }
                                        echo "</td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>No items found</td></tr>";
                                }
                                ?>
                            </tbody>
                            
                        </table>
                        <?php
                                // Query to calculate the total amount for the event
                                //$total_query = "SELECT SUM(total_amount) AS total FROM event_items WHERE event_id = ?";
                                //$stmt = $conn->prepare($total_query);
//
                                //if ($stmt) {
                                //    $stmt->bind_param("i", $event_id);
                                //    $stmt->execute();
                                //    $stmt->bind_result($event_total);
                                //    $stmt->fetch();
                                //    $stmt->close();
                                //} else {
                                //    $event_total = 0; // Default value in case of a query failure
                                //}
                            ?>
                                <h6 class="flex-end">Total Amount: <?= number_format($event_total, 2); ?></h6>    
                    </div>

                    <!-- Financial Summary Tab -->
                    <div class="tab-pane fade" id="financial-summary" role="tabpanel" aria-labelledby="financial-summary-tab">
                        <?php if ($event['accomplishment_status'] === 1): ?>
                        <!-- Event Information -->
                        <h4>Title:
                            <?php echo $event['title']; ?>
                        </h4>
                        <p>Venue:
                            <?php echo $event['event_venue']; ?>
                        </p>
                        <p>Start Date:
                            <?php echo $event['event_start_date']; ?>
                        </p>
                        <p>End Date:
                            <?php echo $event['event_end_date']; ?>
                        </p>

                        <h4>Items<button class="btn btn-sm btn-primary ms-3" data-bs-toggle="modal"
                        data-bs-target="#summaryAddItemModal"><i class="fa-solid fa-plus"></i> Add Item</button></h4>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Unit</th>
                                    <th>Amount</th>
                                    <?php
                                    if ($event['event_type'] === 'Income') {
                                        echo "<th> Markup </th>";
                                    }
                                    ?>
                                    <th>Total Amount</th>
                                    <th>References</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($summaryItems)) {
                                    foreach ($summaryItems as $item) {
                                        $total_amount = $item['quantity'] * $item['amount'];
                                        echo "<tr>
                                                <td>{$item['description']}</td>
                                                <td>{$item['quantity']}</td>
                                                <td>{$item['unit']}</td>
                                                <td>{$item['amount']}</td>
                                                <td>{$total_amount}</td>
                                                <td>{$item['reference']}</td>
                                                <td>
                                                    <button class='btn summary-edit-btn btn-primary btn-sm' data-bs-toggle='modal' data-bs-target='#summaryEditItemModal' data-id='{$item['summary_item_id']}' data-description='{$item['description']}' data-quantity='{$item['quantity']}' data-unit='{$item['unit']}' data-amount='{$item['amount']}'><i class='fa-solid fa-pen'></i> Edit</button>
                                                    <button class='btn summary-delete-btn btn-danger btn-sm' data-bs-toggle='modal' data-bs-target='#summaryDeleteItemModal' data-id='{$item['summary_item_id']}'><i class='fa-solid fa-trash'></i> Delete</button>
                                                </td>
                                            </tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center'>No summary items found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                        <?php else: ?>
                        <p>This event has not been accomplished yet. The financial summary will be available once the event is marked as accomplished.</p>
                        <?php endif; ?>
                    </div>
                </div>
                


                <!-- Plan Add Item Modal -->
                <div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form id="addItemForm">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addItemModalLabel">Add Item</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">

                                    <!-- Modal content for adding item -->
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                    <!-- Event ID -->
                                    <div class="form-group row mb-2">
                                        <div class="col">
                                            <label for="description">Description</label>
                                            <input type="text" class="form-control" id="description" name="description"
                                                required>
                                        </div>
                                        <div class="col">
                                            <label for="quantity">Quantity</label>
                                            <input type="number" class="form-control" id="quantity" name="quantity"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <div class="col">
                                            <label for="unit">Unit</label>
                                            <input type="text" class="form-control" id="unit" name="unit" required>
                                        </div>
                                        <div class="col">
                                            <label for="amount">Amount</label>
                                            <input type="number" step="0.01" class="form-control" id="amount"
                                                name="amount" required>
                                        </div>
                                    </div>

                                    <!-- Success Message Alert -->
                                    <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                                        Item added successfully!
                                    </div>
                                    <!-- Error Message Alert -->
                                    <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
                                        <ul id="errorList"></ul> <!-- List for showing validation errors -->
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Add Item</button>
                                    </div>

                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Item Modal -->
                <div class="modal fade" id="editItemModal" tabindex="-1" role="dialog"
                    aria-labelledby="editItemModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editItemModalLabel">Edit Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form id="editItemForm">
                                <div class="modal-body">
                                    <input type="hidden" id="edit_item_id" name="item_id">
                                    <input type="hidden" id="edit_event_id" name="event_id"
                                        value="<?php echo $event_id; ?>"> <!-- Add event_id -->
                                    <div class="form-group row mb-2">
                                        <div class="col">
                                            <label for="edit_description" class="form-label">Description</label>
                                            <input type="text" class="form-control" id="edit_description"
                                                name="description" required>
                                        </div>
                                        <div class="col">
                                            <label for="edit_quantity" class="form-label">Quantity</label>
                                            <input type="number" class="form-control" id="edit_quantity" name="quantity"
                                                required>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <div class="col">
                                            <label for="edit_unit" class="form-label">Unit</label>
                                            <input type="text" class="form-control" id="edit_unit" name="unit" required>
                                        </div>
                                        <div class="col">
                                            <label for="edit_amount" class="form-label">Amount</label>
                                            <input type="number" step="0.01" class="form-control" id="edit_amount"
                                                name="amount" required>
                                        </div>
                                    </div>

                                    <!-- Success Message Alert -->
                                    <div id="successMessage2" class="alert alert-success d-none mt-3" role="alert">
                                        Item updated successfully!
                                    </div>
                                    <!-- Error Message Alert -->
                                    <div id="errorMessage2" class="alert alert-danger d-none mt-3" role="alert">
                                        <ul id="errorList2"></ul> <!-- List for showing validation errors -->
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- Delete Item Modal -->
                <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this item?</p>

                                <!-- Hidden form for item and event IDs -->
                                <form id="deleteItemForm">
                                    <input type="hidden" name="item_id" id="delete_item_id"> <!-- Item ID -->
                                    <input type="hidden" name="event_id" id="delete_event_id" value="<?php echo $event_id; ?>"> <!-- Event ID -->
                                </form>

                                <!-- Success message -->
                                <div id="successMessage3" class="alert alert-success d-none"></div>
                                
                                <!-- Error message -->
                                <div id="errorMessage3" class="alert alert-danger d-none">
                                    <ul id="errorList3"></ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Item Modal for Summary Tab -->
                <div class="modal fade" id="summaryAddItemModal" tabindex="-1"
                    aria-labelledby="summaryAddItemModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form id="summaryAddItemForm">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="summaryAddItemModalLabel">Add Item</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                                    <!-- Dropdown to select an event -->
                                    <div class="form-group mb-2">
                                        <label for="item_selector">Select Item</label>
                                        <select class="form-control" id="item_selector" name="selected_item">
                                            <option value="">Select Item</option>
                                            <?php
                                            // Fetch events from the database

                                            foreach ($items as $item) {
                                                echo '<option value="' . htmlspecialchars(json_encode($item), ENT_QUOTES, 'UTF-8') . '">'
                                                    . htmlspecialchars($item['description']) . '</option>';
                                            }

                                            $stmt->close();
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <div class="col">
                                            <label for="summary_description">Description</label>
                                            <input type="text" class="form-control" id="summary_description"
                                                name="description" required>
                                        </div>
                                        <div class="col">
                                            <label for="summary_quantity">Quantity</label>
                                            <input type="number" class="form-control" id="summary_quantity"
                                                name="quantity" required>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <div class="col">
                                            <label for="summary_unit">Unit</label>
                                            <input type="text" class="form-control" id="summary_unit" name="unit"
                                                required>
                                        </div>
                                        <div class="col">
                                            <label for="summary_amount">Amount</label>
                                            <input type="number" step="0.01" class="form-control" id="summary_amount"
                                                name="amount" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                            <label for="reference">Reference</label>
                                            <input type="file" class="form-control" id="reference" name="reference" required>
                                    </div>
                                    <div id="successMessage4" class="alert alert-success d-none mt-3"
                                        role="alert">Item added successfully!</div>
                                    <div id="errorMessage4" class="alert alert-danger d-none mt-3" role="alert">
                                        <ul id="errorList4"></ul>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Add Item</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Edit Item Modal for Summary Tab -->
                <div class="modal fade" id="summaryEditItemModal" tabindex="-1"
                    aria-labelledby="summaryEditItemModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form id="summaryEditItemForm">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="summaryEditItemModalLabel">Edit Item</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" id="summary_edit_item_id" name="summary_item_id">
                                    <input type="hidden" id="summary_edit_event_id" name="event_id"
                                        value="<?php echo $event_id; ?>">
                                    <div class="form-group row mb-2">
                                        <div class="col">
                                            <label for="summary_edit_description">Description</label>
                                            <input type="text" class="form-control" id="summary_edit_description"
                                                name="description" required>
                                        </div>
                                        <div class="col">
                                            <label for="summary_edit_quantity">Quantity</label>
                                            <input type="number" class="form-control" id="summary_edit_quantity"
                                                name="quantity" required>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-2">
                                        <div class="col">
                                            <label for="summary_edit_unit">Unit</label>
                                            <input type="text" class="form-control" id="summary_edit_unit" name="unit"
                                                required>
                                        </div>
                                        <div class="col">
                                            <label for="summary_edit_amount">Amount</label>
                                            <input type="number" step="0.01" class="form-control"
                                                id="summary_edit_amount" name="amount" required>
                                        </div>
                                    </div>
                                    <div id="successMessage5" class="alert alert-success d-none mt-3"
                                        role="alert">Item updated successfully!</div>
                                    <div id="errorMessage5" class="alert alert-danger d-none mt-3"
                                        role="alert">
                                        <ul id="editErrorList5"></ul>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Delete Item Modal for Summary -->
                <div class="modal fade" id="summaryDeleteItemModal" tabindex="-1" aria-labelledby="summaryDeleteItemModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Delete Item</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Are you sure you want to delete this item?</p>

                                <!-- Hidden form for item and event IDs -->
                                <form id="summaryDeleteItemForm">
                                    <input type="hidden" name="item_id" id="summary_delete_item_id"> <!-- Item ID -->
                                    <input type="hidden" name="event_id" id="summary_delete_event_id" value="<?php echo $event_id; ?>"> <!-- Event ID -->
                                </form>

                                <!-- Success message -->
                                <div id="successMessage6" class="alert alert-success d-none"></div>
                                
                                <!-- Error message -->
                                <div id="errorMessage6" class="alert alert-danger d-none">
                                    <ul id="errorList6"></ul>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger" id="summaryConfirmDeleteBtn">Delete</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
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

    <script src="js/event_details.js"></script>
</body>

</html>