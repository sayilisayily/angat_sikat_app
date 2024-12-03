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
    body {
      margin: 0;
      height: 100vh; /* Full viewport height */
      position: relative;
      overflow: hidden;
    }

    body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: url('../user/CCAT-Campus-Scaled.jpg'); /* Replace with your image path */
      background-size: cover; /* Cover the entire area */
      background-position: center; /* Center the image */
      filter: blur(8px); /* Blur the background */
      z-index: 1; /* Behind the content */
    }

    .page-wrapper {
      position: relative;
      z-index: 2; /* On top of the background */
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .form-container {
      display: flex;
      align-items: stretch; /* Align items to stretch */
      max-width: 950px; /* Set max width */
      width: 100%; /* Ensure full width */
    }

    .image-container {
      width: calc(50% + 5px); /* Increase width by 5px */
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden; /* Prevent overflow */
    }

    .image-container img {
      width: 100%;
      height: 100%; /* Ensure full height */
      object-fit: cover; /* Cover the area */
      object-position: center; /* Center the image */
    }

    .form-box {
      background-color: white; /* White background for the form */
      padding: 2rem; /* Padding around the form */
      display: flex;
      flex-direction: column;
      justify-content: center;
      height: 100%; /* Match height of the image */
      width: 50%; /* Set width for the form box */
    }

    .row .col-md-6 {
      padding: 0 8px; /* Minimal spacing between form fields */
    }

    .form-label {
      font-weight: 500;
      font-size: 0.9rem;
    }

    .input-group,
    .form-select {
      width: 100%;
    }

    .form-select,
    .form-control {
      height: calc(2.5rem + 2px); /* Adjust height for consistency */
      max-width: 100%; /* Ensures uniform width */
    }
  </style>
</head>

<body>
  <?php
  include('connection.php'); // Ensure the database connection file is included
  ?>
  
  <!-- Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <div class="position-relative min-vh-100 d-flex align-items-center justify-content-center">
      <div class="container d-flex align-items-center justify-content-center" style="max-width: 1000px;">

        <!-- Combined Container for Form and Image -->
        <div class="form-container">

          <!-- Image Section -->
          <div class="image-container">
            <img src="register.jpg" alt="Registration Illustration" class="img-fluid">
          </div>

          <!-- Sign Up Form Section -->
          <div class="form-box"> <!-- Container for the form -->
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
                <div class="col-md-6">
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
                <div class="col-md-6">
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
                    <input type="password" class="form-control" id="confirmpassword" name="confirm_password" placeholder="Re-type Password" required>
                  </div>
                </div>
              </div>

              <!-- Submit Button -->
              <button type="submit" class="btn w-100 mb-4" style="background-color: #3E7044; color: white; padding: 10px; border-radius: 0;">Sign Up</button>

              <!-- Adjusted Account Creation Link -->
              <div class="text-center d-flex justify-content-center align-items-center">
                <p class="mb-0 fw-normal text-muted" style="font-size: 0.8rem; margin-right: 5px;">Have an Account?</p>
                <a class="fw-bold" href="../user/login.html" style="color: #00542F;">Sign In</a>
              </div>
            </form>
          </div> <!-- End of form-box -->
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