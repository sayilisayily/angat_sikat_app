<?php
include 'connection.php';
include '../session_check.php';

$organization_id = $_SESSION['organization_id'];  // Fetch organization_id from session

$sql = "SELECT * FROM purchases WHERE archived = 0 AND organization_id = $organization_id";
$result = $conn->query($sql);

// Check for SQL errors
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.php'; ?>
        <title>Purchases Table</title>
    </head>
<body>

<?php // include 'sidebar.php'; ?>

<div class="container mt-5 p-4">
    <h2 class="mb-4">Purchases
        <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addPurchaseModal">
            <i class="fa-solid fa-plus"></i> Add Purchase
        </button>
    </h2>
    <table id="purchasesTable" class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Total Budget</th>
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
                            <td>{$row['total_budget']}</td>
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
                              <input type='checkbox' class='form-check-input' onclick='toggleCompletion({$row['purchase_id']}, this.checked)' $checked $disabled>
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
              <input type="text" class="form-control" id="title" name="title">
            </div>
          </div>

          <!-- Success Message Alert -->
          <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                Purchase added successfully!
          </div>  
          <!-- Error Message Alert -->
          <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
              <ul id="errorList"></ul>
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
          <input type="text" id="editPurchaseId" name="purchase_id">
          <div class="form-group">
            <label for="editPurchaseTitle">Purchase Title</label>
            <input type="text" class="form-control" id="editPurchaseTitle" name="title" required>
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
                <input text="hidden" id="archivePurchaseId">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmArchiveBtn" class="btn btn-danger">Archive</button>
            </div>
        </div>
    </div>
</div>

<script src="js/purchases.js"></script>
</body>
</html>

<?php
$conn->close();
?>
