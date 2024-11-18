<?php
include 'connection.php';

// Fetch non-archived maintenance entries
$sql = "SELECT * FROM maintenance WHERE archived = 0";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include 'head.php'; ?>
        <title>Maintenance Table</title>
    </head>
<body>

<?php //include 'sidebar.php'; ?>

<div class="container mt-5 p-4">
    <h2 class="mb-4">Maintenance <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal"><i class="fa-solid fa-plus"></i> Add Maintenance</button></h2>
    <table id="maintenanceTable" class="table">
        <thead>
            <tr> 
                <th>Title</th>
                <th>Total Cost</th>
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
                    $disabled = ($row['maintenance_status'] !== 'Approved') ? 'disabled' : '';
                    echo "<tr>
                            <td><a class='link-offset-2 link-underline link-underline-opacity-0' href='maintenance_details.php?maintenance_id={$row['maintenance_id']}'>{$row['title']}</a></td>
                            <td>{$row['total_cost']}</td>
                            <td>";
                    
                    if ($row['maintenance_status'] == 'Pending') {
                        echo "<span class='badge rounded-pill pending'> ";
                    } else if ($row['maintenance_status'] == 'Approved') {
                        echo "<span class='badge rounded-pill approved'> ";
                    } else if ($row['maintenance_status'] == 'Disapproved') {
                        echo "<span class='badge rounded-pill disapproved'> ";
                    }

                    echo "{$row['maintenance_status']}</span></td>
                          <td><input type='checkbox' class='form-check-input' onclick='toggleCompletion({$row['maintenance_id']}, this.checked)' $checked $disabled></td>
                          <td>
                              <button class='btn btn-primary btn-sm edit-btn mb-3' data-bs-toggle='modal' data-bs-target='#editMaintenanceModal' data-id='{$row['maintenance_id']}'><i class='fa-solid fa-pen'></i> Edit</button>
                              <button class='btn btn-danger btn-sm archive-btn mb-3' data-id='{$row['maintenance_id']}'><i class='fa-solid fa-box-archive'></i> Archive</button>
                          </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center'>No maintenance records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>


<!-- Add Maintenance Modal -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1" role="dialog" aria-labelledby="addMaintenanceLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="addMaintenanceForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addMaintenanceLabel">Add New Maintenance</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Form fields -->
          <div class="form-group row mb-2">
            <div class="col">
              <label for="title">Maintenance Title</label>
              <input type="text" class="form-control" id="title" name="title" required>
            </div>
            <div class="col">
              <label for="total_cost">Total Cost</label>
              <input type="number" step="0.01" class="form-control" id="total_cost" name="total_cost" required>
            </div>
          </div>

          <!-- Success and Error Message Alert -->
          <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
                Maintenance added successfully!
          </div>  
          <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
              <ul id="errorList"></ul>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Add Maintenance</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit Maintenance Modal -->
<div class="modal fade" id="editMaintenanceModal" tabindex="-1" role="dialog" aria-labelledby="editMaintenanceLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <form id="editMaintenanceForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editMaintenanceLabel">Edit Maintenance</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Hidden field for maintenance ID -->
          <input type="hidden" id="editMaintenanceId" name="maintenance_id">

          <!-- Other form fields -->
          <div class="form-group">
            <label for="editMaintenanceTitle">Maintenance Title</label>
            <input type="text" class="form-control" id="editMaintenanceTitle" name="title" required>
          </div>
          <div class="form-group">
            <label for="editTotalCost">Total Cost</label>
            <input type="number" step="0.01" class="form-control" id="editTotalCost" name="total_cost" required>
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
<div class="modal fade" id="archiveMaintenanceModal" tabindex="-1" aria-labelledby="archiveMaintenanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="archiveMaintenanceModalLabel">Archive Maintenance</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to archive this maintenance record?
        <input type="hidden" id="archiveMaintenanceId">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" id="confirmArchiveMaintenanceBtn" class="btn btn-danger">Archive</button>
      </div>
    </div>
  </div>
</div>

<script src="js/maintenance.js"></script>
</body>
</html>

<?php
$conn->close();
?>
