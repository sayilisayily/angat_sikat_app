<?php
include '../connection.php';
include '../session_check.php';

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$sql = "SELECT * FROM organizations";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include '../head.php'; ?>
    <title>Users Management</title>
</head>
<body>

<div class="container mt-5 p-5">
    <h2 class="mb-4">Users
        <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="fa-solid fa-plus"></i> Add User
        </button>
    </h2>
    <table id="usersTable" class="table">
        <thead>
            <tr>
                <th>Username</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Organization</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch users and join with organizations table to get organization name
            $userQuery = "SELECT users.*, organizations.organization_name FROM users 
                          JOIN organizations ON users.organization_id = organizations.organization_id";
            $userResult = $conn->query($userQuery);

            if ($userResult->num_rows > 0) {
                while ($userRow = $userResult->fetch_assoc()) {
                    echo "<tr>
                            <td>{$userRow['username']}</td>
                            <td>{$userRow['first_name']}</td>
                            <td>{$userRow['last_name']}</td>
                            <td>{$userRow['email']}</td>
                            <td>{$userRow['role']}</td>
                            <td>{$userRow['organization_name']}</td>
                            <td>
                                <button class='btn btn-primary btn-sm edit-btn mb-3' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#editUserModal' 
                                        data-id='{$userRow['user_id']}'>
                                    <i class='fa-solid fa-pen'></i> Edit
                                </button>
                                <button class='btn btn-danger btn-sm delete-btn mb-3' 
                                        data-id='{$userRow['user_id']}'>
                                    <i class='fa-solid fa-trash'></i> Delete
                                </button>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="addUserForm">
        <div class="modal-header">
          <h5 class="modal-title" id="addUserLabel">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Form fields -->
          <div class="form-group row mb-2">
            <div class="col">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="col">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          </div>
          
          <div class="form-group row mb-2">
            <div class="col">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="col">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
          </div>
          <div class="form-group row mb-2">
            <div class="col">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role">
                <option value="admin">Admin</option>
                <option value="officer">Officer</option>
                <option value="member">Member</option>
                </select>
            </div>
            <div class="col">
                <label for="organization">Organization</label>
                <select class="form-control" id="organization" name="organization_id">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='{$row['organization_id']}'>{$row['organization_name']}</option>";
                    }
                }
                ?>
                </select>
            </div>
          </div>
          <div class="form-group row mb-2">
            <div class="col">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="col">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
          </div>
          <!-- Success Message Alert -->
          <div id="successMessage" class="alert alert-success d-none mt-3" role="alert">
            User added successfully!
          </div>  
          <!-- Error Message Alert -->
          <div id="errorMessage" class="alert alert-danger d-none mt-3" role="alert">
            <ul id="errorList"></ul> <!-- List for showing validation errors -->
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <form id="editUserForm">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Hidden field for user ID -->
          <input type="hidden" id="editUserId" name="user_id">
          <!-- Form fields -->
          <div class="form-group mb-3">
            <label for="editUsername">Username</label>
            <input type="text" class="form-control" id="editUsername" name="username" required>
          </div>
          <div class="form-group mb-3">
            <label for="editFirstName">First Name</label>
            <input type="text" class="form-control" id="editFirstName" name="first_name" required>
          </div>
          <div class="form-group mb-3">
            <label for="editLastName">Last Name</label>
            <input type="text" class="form-control" id="editLastName" name="last_name" required>
          </div>
          <div class="form-group mb-3">
            <label for="editEmail">Email</label>
            <input type="email" class="form-control" id="editEmail" name="email" required>
          </div>
          <div class="form-group mb-3">
            <label for="editRole">Role</label>
            <select class="form-control" id="editRole" name="role">
              <option value="admin">Admin</option>
              <option value="officer">Officer</option>
              <option value="member">Member</option>
            </select>
          </div>
          <div class="form-group mb-3">
            <label for="editOrganization">Organization</label>
            <select class="form-control" id="editOrganization" name="organization_id">
              <?php
              // Repopulate organizations in the edit modal
              if ($result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      echo "<option value='{$row['organization_id']}'>{$row['organization_name']}</option>";
                  }
              }
              ?>
            </select>
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

<!-- Backend Scripts -->
<script src="js/users.js"></script>

</body>
</html>

<?php
$conn->close();
?>
