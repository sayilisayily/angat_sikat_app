<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign Up</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

  <style>
    .form-label {
      font-weight: 500;
      font-size: 0.9rem;
    }
    .input-group, .form-select {
      width: 100%;
    }
    .form-select, .form-control {
      height: calc(2.5rem + 2px); /* Adjust height for consistency */
      max-width: 100%; /* Ensures uniform width */
    }
    .row .col-md-6 {
      padding-right: 8px;
      padding-left: 8px;
    }
  </style>
</head>

<body>
  <?php
    include('connection.php'); // Make sure the database connection file is included
  ?>
  
  <!-- Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
      data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative overflow-hidden text-bg-light min-vh-100 d-flex align-items-center justify-content-center">
      <div class="container d-flex align-items-center justify-content-center" style="max-width: 1000px;">

        <!-- Combined Container for Form and Image with White Background -->
        <div class="row w-100 shadow-lg p-4 rounded d-flex align-items-stretch"
          style="background-color: white; max-width: 950px;">

          <!-- Image Section -->
          <div class="col-md-6 d-none d-md-block p-4">
            <div class="image-container" style="height: 100%; display: flex; align-items: center; justify-content: center;">
              <img src="register.jpg" alt="Login Illustration" class="img-fluid rounded"
                style="width: 100%; height: 100%; object-fit: contain; object-position: center;">
            </div>
          </div>

          <!-- Sign Up Form Section -->
          <div class="col-md-6 d-flex flex-column justify-content-center p-4">
            
            <!-- Form with ID, action, and method -->
            <form id="registrationForm" action="registration.php" method="POST">
              <h1 class="welcome-message h5 fw-bold mb-4">
                <span class="text-warning fw-bold me-2">|</span>Registration
              </h1>
              
              <!-- Username and Email Row -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="username" class="form-label">Username</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class='bx bx-user'></i></span>
                    <input type="text" class="form-control" name="username" id="username" placeholder="Enter Username" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">Email</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class='bx bx-envelope'></i></span>
                    <input type="email" class="form-control" name="email" id="email" placeholder="Enter Email" required>
                  </div>
                </div>
              </div>

              <!-- First Name and Last Name Row -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="first_name" class="form-label">First Name</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class='bx bx-user'></i></span>
                    <input type="text" class="form-control" name="fname" id="firstname" placeholder="Enter First Name" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="last_name" class="form-label">Last Name</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class='bx bx-user'></i></span>
                    <input type="text" class="form-control" name="lname" id="lastname" placeholder="Enter Last Name" required>
                  </div>
                </div>
              </div>

              <!-- Organization and Role Row -->
              <div class="row mb-3">
                <div class="col-md-6" style="padding-right: 8px;">
                  <label for="organization" class="form-label">Organization</label>
                  <select class="form-select" name="organization" id="organization" required>
                    <option value="">Select Organization</option>
                    <?php
                      $query = "SELECT organization_id, organization_name FROM organizations";
                      $result = mysqli_query($conn, $query);
                      if ($result) {
                        while ($org = mysqli_fetch_assoc($result)) {
                          echo "<option value='{$org['organization_id']}'>{$org['organization_name']}</option>";
                        }
                      } else {
                        echo "<option value=''>No Organizations Available</option>";
                      }
                    ?>
                  </select>
                </div>
                <div class="col-md-6" style="padding-left: 8px;">
                  <label for="role" class="form-label">Role</label>
                  <select class="form-select" name="role" id="role" required>
                    <option value="officer">Officer</option>
                    <option value="member">Member</option>
                  </select>
                </div>
              </div>

              <!-- Password and Confirm Password Row -->
              <div class="row mb-4">
                <div class="col-md-6">
                  <label for="password" class="form-label">Password</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class='bx bx-lock'></i></span>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <label for="confirm_password" class="form-label">Confirm Password</label>
                  <div class="input-group">
                    <span class="input-group-text"><i class='bx bx-lock'></i></span>
                    <input type="password" class="form-control" id="confirmpassword" name="confirm_password"
                      placeholder="Re-type Password" required>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <button type="submit" class="btn w-100 mb-4" style="background-color: #3E7044; color: white; padding: 10px; border-radius: 8px;">Sign Up</button>

              <!-- Adjusted Account Creation Link -->
              <div class="text-center d-flex justify-content-center align-items-center">
                <p class="mb-0 fw-normal text-muted" style="font-size: 0.8rem; margin-right: 5px;">Have an Account?</p>
                <a class="fw-bold" href="../user/login.html" style="color: #00542F;">Sign In</a>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>



  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <!-- solar icons -->
  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
</body>

</html>