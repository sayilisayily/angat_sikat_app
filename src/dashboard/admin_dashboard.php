<?php
include("../connection.php");
include '../session_check.php';
include '../user_query.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: ../user/login.html");
  exit();
}


?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/angat sikat.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <!--Custom CSS for Sidebar-->
  <link rel="stylesheet" href="../html/sidebar.css" />
  <!--Boxicon-->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <!--Font Awesome-->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <!-- Lordicon (for animated icons) -->
  <script src="https://cdn.lordicon.com/lordicon.js"></script>
  <!--Calendar JS-->
  <script src="path/to/calendar.js"></script>
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
              <a class="sidebar-link" href="../dashboard/admin_dashboard.php" aria-expanded="false"
                data-tooltip="Dashboard">
                <i class="bx bxs-dashboard"></i>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../user_management/users.php" aria-expanded="false"
                data-tooltip="Users">
                <i class="bx bx-user"></i>
                <span class="hide-menu">Users</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../organization_management/organizations.php" aria-expanded="false"
                data-tooltip="Organizations">
                <i class="bx bx-group"></i>
                <span class="hide-menu">Organizations</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="../activity_management/admin_calendar.php" aria-expanded="false"
                data-tooltip="Manage Events">
                <i class="bx bx-calendar"></i>
                <span class="hide-menu">Events</span>
              </a>
            </li>
            <div class="submenu">                
                <a href="../activity_management/admin_calendar.php">› Calendar</a>
              </div>
            <li class="sidebar-item">
              <a class="sidebar-link" aria-expanded="false" data-tooltip="Budget">
                <i class="bx bx-wallet"></i>
                <span class="hide-menu">Budget</span>
              </a>
              <div class="submenu">                
                <a href="../budget_management/admin_budget_approval_table.php">› Approval</a>
              </div>
            </li>
            
            <li class="sidebar-item profile-container">
              <a class="sidebar-link" href="../user/profile.html" aria-expanded="false" data-tooltip="Profile">
                <div class="profile-pic-border">
                  <img src="<?php echo !empty($profile_picture) ? '../user/uploads/' . htmlspecialchars($profile_picture) : '../user/uploads/default.png'; ?>" alt="Profile Picture" class="profile-pic" />
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
                  class="position-absolute top-50 start-0 translate-middle-y ms-2 text-secondary" width="16" height="16"
                  fill="none" viewBox="0 0 24 24" stroke="currentColor" id="searchIcon" style="margin-left: 8px;">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-2-2m0 0a7 7 0 1110 0l-2 2m-2-2a7 7 0 110-14 7 7 0 010 14z" />
                </svg>
              </div>

              <!-- Notification Icon -->
              <button id="notificationBtn" style="background-color: transparent; border: none; padding: 0;">
                <lord-icon src="https://cdn.lordicon.com/lznlxwtc.json" trigger="hover" colors="primary:#004024"
                  style="width:30px; height:30px;">
                </lord-icon>
              </button>

              <!-- Profile Dropdown -->
              <div class="dropdown">
                <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown"
                  aria-expanded="false" style="text-decoration: none;">
                  <img class="border border-dark rounded-circle" src="<?php echo !empty($profile_picture) ? '../user/uploads/' . htmlspecialchars($profile_picture) : '../user/uploads/default.png'; ?>" alt="Profile"
                                        style="width: 40px; height: 40px; margin-left: 10px;">
                    <span class="visually-hidden"><?php echo htmlspecialchars($user['username']); ?></span> <!-- Visually hide username -->
                  <div class="d-flex flex-column align-items-start ms-2">
                    <span style="font-weight: bold; color: #004024; text-decoration: none;"><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></span>
                    <span style="font-size: 0.85em; color: #6c757d; text-decoration: none;"><?php echo htmlspecialchars($user['email']); ?></span>
                  </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                  <li><a class="dropdown-item" href="../user/profile.html"><i class="bx bx-user"></i> My Profile</a>
                  </li>
                  <li><a class="dropdown-item" href="../user/logout.php"><i class="bx bx-log-out"></i>
                      Logout</a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </nav>
      </header>
      <!-- Header End -->

      <!-- First Main Wrap -->
      <div class="container-fluid">
        <div class="row">
          <!-- Left Column for Welcome Message and Organization Info Box -->
          <div class="col-md-8">
            <!-- Welcome Message with adjusted left margin -->
            <h1 class="welcome-message h5 fw-bold mb-4">
              <span class="text-warning fw-bold me-2">|</span>Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!
            </h1>
            
            <!-- Organization Info Box with adjusted left margin -->
            <div class="organization-card p-4 rounded shadow-sm bg-white border mb-4">
              <img class="organization-logo me-3" src="byte.png" alt="BYTE Logo" />
              <div class="organization-details">
                <p class="text-muted small-text mb-1">Name of Student Organization</p>
                <h1 class="fw-semibold h5 mb-2">Beacon of Youth Technology Enthusiasts</h1>
                <div class="d-flex gap-5">
                  <div>
                    <p class="text-muted small-text mb-1">No. of Members</p>
                    <h1 class="fw-semibold h6">575</h1>
                  </div>
                  <div>
                    <p class="text-muted small-text mb-1">Status</p>
                    <h1 class="fw-semibold h6">Level I</h1>
                  </div>
                </div>
              </div>
            </div>
            <!-- End of Organization Info Box -->

            <!-- Financial Summary Cards Row -->
            <div class="row">
              <!-- Balance Card -->
              <div class="col-md-4 mb-3">
                <div class="card financial-card p-3 shadow-sm bg-purple-200 mx-2">
                  <h7 class="text-gray-500 text-start d-block" style="margin-left: 2px;">Balance</h7>
                  <div class="d-flex align-items-center">
                    <h1 class="fw-bold" style="margin-left: 2px;">₱59,690</h1>
                    <i class="bx bx-trending-up" style="color: green; font-size: 3.5rem; margin-left: 5px;"></i>
                  </div>
                  <div class="d-flex justify-content-end">
                    <div class="badge bg-warning text-white fw-medium percentage-box"
                      style="font-size: 0.75rem; padding: 2px 6px;">13.4% *</div>
                  </div>
                </div>
              </div>

              <!-- Income Card -->
              <div class="col-md-4 mb-3">
                <div class="card financial-card p-3 shadow-sm bg-pink-200 mx-2">
                  <h7 class="text-gray-500 text-start d-block" style="margin-left: 2px;">Income</h7>
                  <div class="d-flex align-items-center">
                    <h1 class="fw-bold" style="margin-left: 2px;">₱59,690</h1>
                    <i class="bx bx-trending-up" style="color: green; font-size: 3.5rem; margin-left: 5px;"></i>
                  </div>
                  <div class="d-flex justify-content-end">
                    <div class="badge bg-warning text-white fw-medium percentage-box"
                      style="font-size: 0.75rem; padding: 2px 6px;">13.4% *</div>
                  </div>
                </div>
              </div>

              <!-- Expense Card -->
              <div class="col-md-4 mb-3">
                <div class="card financial-card p-3 shadow-sm bg-blue-200 mx-2">
                  <h7 class="text-gray-500 text-start d-block" style="margin-left: 2px;">Expense</h7>
                  <div class="d-flex align-items-center">
                    <h1 class="fw-bold" style="margin-left: 2px;">₱59,690</h1>
                    <i class="bx bx-trending-up" style="color: green; font-size: 3.5rem; margin-left: 5px;"></i>
                  </div>
                  <div class="d-flex justify-content-end">
                    <div class="badge bg-warning text-white fw-medium percentage-box"
                      style="font-size: 0.75rem; padding: 2px 6px;">13.4% *</div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End of Financial Summary Cards Row -->

            <!-- Balance Report Section -->
            <div class="p-4 bg-white mx-auto rounded border shadow-md justify-center balance-report"
              style="width: 725px; margin: 20px;">
              <div class="d-flex justify-content-start gap-5">
                <h2 class="text-lg fw-bold">Balance Report</h2>
                <div class="d-flex gap-3">
                  <button class="btn btn-secondary btn-sm">Monthly</button>
                  <button class="btn btn-secondary btn-sm">Quarterly</button>
                  <button class="btn btn-secondary btn-sm">Yearly</button>
                </div>
              </div>
              <div class="mt-2">
                <p class="fw-semibold">Average per month</p>
                <h1 class="fw-bold h5 text-success">₱5,500</h1>
                <p class="fw-medium mt-1" style="color: #5C5C5C;">Median ₱45,000</p>
              </div>

              <div class="container mx-auto mt-3">
                <!-- Bar Graph Container -->
                <div class="row g-3">
                  <!-- Bar for January-->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 90px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Jan</p>
                  </div>

                  <!-- Bar for February -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 70px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Feb</p>
                  </div>

                  <!-- Bar for March -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 50px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Mar</p>
                  </div>

                  <!-- Bar for April -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 30px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Apr</p>
                  </div>

                  <!-- Bar for May -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 10px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">May</p>
                  </div>

                  <!-- Bar for June -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 20px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Jun</p>
                  </div>

                  <!-- Bar for July -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 40px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Jul</p>
                  </div>

                  <!-- Bar for August -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 30px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Aug</p>
                  </div>

                  <!-- Bar for September -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 40px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Sep</p>
                  </div>

                  <!-- Bar for October -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 50px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Oct</p>
                  </div>

                  <!-- Bar for November -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 60px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Nov</p>
                  </div>

                  <!-- Bar for December -->
                  <div class="col-1">
                    <div class="d-flex flex-column-reverse align-items-center" style="height: 100px;">
                      <div class="w-100 bg-success" style="height: 70px;"></div>
                    </div>
                    <p class="mt-1 text-sm font-medium text-center">Dec</p>
                  </div>

                </div>
              </div>
            </div>
            <!-- Balance Report End -->
          </div>

          <!-- Right Column for Advisers and Financial Deadlines (Third Container) -->
          <div class="col-md-4">
            <div class="mx-3">
              <div class="p-4 bg-white rounded shadow-sm mt-5">
                <div class="d-flex justify-content-between">
                  <p class="fw-semibold">Advisers</p>
                  <a href="#">
                    <p class="fw-semibold text-success">See All</p>
                  </a>
                </div>

                <div class="d-flex justify-content-evenly mt-3">
                  <div class="text-center">
                    <img class="rounded-circle border border-dark h-20" src="Sir Renato.jpg" alt=""
                      style="width: 40px; height: 40px;" />
                    <p class="fw-semibold text-sm">Renato Bautista</p>
                    <p class="text-xs text-secondary">Instructor, DCS</p>
                  </div>

                  <div class="text-center">
                    <img class="rounded-circle border border-dark h-20" src="Maam Janessa.jpg" alt=""
                      style="width: 40px; height: 40px;" />
                    <p class="fw-semibold text-sm">Janessa Dela Cruz</p>
                    <p class="text-xs text-secondary">Instructor, DCS</p>
                  </div>
                </div>
              </div>

              <!-- Fourth Container -->
              <div class="p-4 bg-white rounded mt-4 shadow-sm">
                <div class="d-flex align-items-center">
                  <lord-icon src="https://cdn.lordicon.com/ysqeagpz.json" trigger="loop" colors="primary:#6acbff"
                    style="width:40px;height:40px;transform: rotate(360deg);"></lord-icon>
                  <h1 class="text-secondary fw-bold h5 ms-2">Financial Deadlines</h1>
                </div>

                <div class="ms-2 mt-3">
                  <div class="mt-1">
                    <h1 class="fw-bold text-xs fs-5">Office Supplies</h1>
                    <p class="text-secondary fw-semibold text-xs">October 12, 2024</p>
                  </div>

                  <div class="mt-1">
                    <h1 class="fw-bold text-xs fs-5">Transportation</h1>
                    <p class="text-secondary fw-semibold text-xs">L-300</p>
                  </div>

                  <div class="mt-1">
                    <h1 class="fw-bold text-xs fs-5">Speakers</h1>
                    <p class="text-secondary fw-semibold text-xs">November 11, 2024</p>
                  </div>
                </div>
              </div>

              <!-- Radial Progress -->
              <div class="d-flex justify-evenly gap-5 p-4 bg-white rounded mt-4 shadow-sm">
                <div class="d-flex align-items-center">
                  <div>
                    <h1 class="fw-bold fs-7">Balance</h1>
                    <p class="fw-semibold text-secondary">Total Monthly</p>
                    <h1 class="fw-bold h6">₱ 50,000 <span
                        class="bg-warning text-white rounded-pill mx-auto px-3 py-1">73.4%</span></h1>
                  </div>
                </div>

                <div>
                  <div class="d-flex flex-column align-items-center">
                    <div class="position-relative d-flex align-items-center justify-content-center"
                      style="width: 120px; height: 120px;">
                      <!-- Circle Background -->
                      <div class="position-absolute w-100 h-100 rounded-circle bg-light"></div>

                      <!-- Radial Progress Circle -->
                      <svg class="position-absolute w-100 h-100" style="transform: rotate(-90deg);">
                        <circle cx="50%" cy="50%" r="45%" stroke="currentColor" stroke-width="8" class="text-purple-500"
                          fill="none" stroke-dasharray="283" stroke-dashoffset="85">
                        </circle>
                      </svg>

                      <!-- Centered Text -->
                      <div class="position-absolute text-center">
                        <p class="h6 fw-bold text-dark">₱27,500</p>
                        <p class="text-sm fw-semibold text-secondary">Remaining balance</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>



          <!-- Transaction Chart -->
          <div class="container-fluid">
            <div class="h5 fw-black fs-10 ps-5 mx-xs-5">
              <h2 class="mr-5"><span class="text-warning fw-bold me-2 fs-10">|</span>Transactions</h2>
            </div>

            <div class="p-4 bg-white rounded border shadow-md justify-center card"
              style="height: 450px; width: 1150px; margin: 1px 200px 5px 0;">
              <div class="card-body">
                <div class="d-sm-flex d-block align-items-center justify-content-between mb-4">
                  <h5 class="card-title fw-semibold mb-0">This Month 53 | P350</h5>
                  <div>
                    <select class="form-select">
                      <option value="1">March 2024</option>
                      <option value="2">April 2024</option>
                      <option value="3">May 2024</option>
                      <option value="4">June 2024</option>
                    </select>
                  </div>
                </div>
                <div id="sales-profit" style="height: 300px;"></div> <!-- Placeholder for chart -->
              </div>
            </div>
          </div>
          <!-- End of Transaction Chart -->

          <!--Recent Transaction dashboard-->
          <div>
            <div class="h5 fw-black fs-10 ps-5 mx-xs-5">
              <h2 class="mr-5"><span class="text-warning fw-bold me-2 fs-10">|</span>Recent Transactions</h2>
            </div>
            <div class="container mt-5">
              <button id="printButton" class="btn btn-primary mb-3">Print</button>
              <button id="pdfButton" class="btn btn-success mb-3">Download PDF</button>

              <div id="tableContent">
                <table class="table table-bordered">
                  <thead class="thead-light fw-bold">
                    <tr class="fw-bold fs-4 text-dark">
                      <th>Type</th>
                      <th>Due Date</th>
                      <th>Description</th>
                      <th>Amount</th>
                      <th>Payer</th>
                      <th>Reference</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Invoice</td>
                      <td>2023-10-20</td>
                      <td>Payment for services rendered</td>
                      <td>$1,000.00</td>
                      <td>Company A</td>
                      <td>INV-001</td>
                      <td>Paid</td>
                    </tr>
                    <tr>
                      <td>Expense</td>
                      <td>2023-10-22</td>
                      <td>Office supplies purchase</td>
                      <td>$250.00</td>
                      <td>Tom</td>
                      <td>EXP-002</td>
                      <td>Pending</td>
                    </tr>
                    <tr>
                      <td>Invoice</td>
                      <td>2023-10-25</td>
                      <td>Consultation services</td>
                      <td>$500.00</td>
                      <td>Company B</td>
                      <td>INV-003</td>
                      <td>Paid</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

            <script>
              // Print function
              document.getElementById('printButton').addEventListener('click', function () {
                window.print();
              });

              // PDF download function
              document.getElementById('pdfButton').addEventListener('click', function () {
                const element = document.getElementById('tableContent');
                html2pdf()
                  .from(element)
                  .save('transaction_report.pdf');
              });
            </script>
          </div>
          <!--Recent Transaction End-->

          <!--Upcoming Events-->
          <div>
            <div class="h5 fw-black fs-10 ps-5 mx-xs-5 mt-5">
              <h2><span class="text-warning fw-bold me-2 fs-10">|</span>Upcoming Events</h2>
            </div>
            <div class="mx-auto">
              <!--event boxes-->
              <div class="container mt-5">
                <div class="row">
                  <!-- Event Box 1 -->
                  <div class="col-md-4">
                    <div class="container-white">
                      <div class="event-box">
                        <div class="d-flex justify-content-between align-items-center">
                          <h6 class="event-title">Beacon of Youth Technology Enthusiasts</h6>
                          <p class="event-duration">3 Hours</p>
                        </div>
                        <h5>TechFest</h5>
                        <div class="event-details">
                          <p class="event-date"><i class="fa-regular fa-calendar" aria-hidden="true"></i> 28 Sep 2024
                          </p>
                          <p class="event-time"><i class="fa-regular fa-clock" aria-hidden="true"></i> 10:00 AM</p>
                        </div>
                        <div class="text-end mt-auto">
                          <button class="btn btn-warning details-btn">Details</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Event Box 2 -->
                  <div class="col-md-4">
                    <div class="container-white">
                      <div class="event-box">
                        <div class="d-flex justify-content-between align-items-center">
                          <h6 class="event-title">The CvSU-R Nexus</h6>
                          <p class="event-duration">3 Hours</p>
                        </div>
                        <h5>Schoolwide Press Conference</h5>
                        <div class="event-details">
                          <p class="event-date"><i class="fa-regular fa-calendar" aria-hidden="true"></i> 15 Oct 2024
                          </p>
                          <p class="event-time"><i class="fa-regular fa-clock" aria-hidden="true"></i> 10:00 AM</p>
                        </div>
                        <div class="text-end mt-auto">
                          <button class="btn btn-warning details-btn">Details</button>
                        </div>
                      </div>
                    </div>
                  </div>

                  <style>
                    .container-white {
                      background-color: white;
                      padding: 20px;
                      border-radius: 15px;
                      border: 1px solid #ddd;
                      /* Light gray border */
                      margin-bottom: 20px;
                      height: 250px;
                      /* Fixed height */
                      display: flex;
                      flex-direction: column;
                    }

                    .event-box {
                      color: #333;
                      display: flex;
                      flex-direction: column;
                      height: 100%;
                    }

                    .event-box h6 {
                      font-weight: bold;
                      color: #666;
                      margin-bottom: 30px;
                      /* Adds spacing below h6 */
                    }

                    .event-title {
                      font-size: 1rem;
                      /* Adjust font size for fitting */
                      flex: 1;
                      /* Ensures the title takes up available space */
                      margin: 0;
                      /* Removes any extra margin */
                      font-weight: bold;
                      /* Makes text bold */
                    }

                    .event-duration {
                      font-size: 1rem;
                      /* Matches title size */
                      font-weight: bold;
                      /* Makes text bold */
                      color: #666;
                      margin-left: 10px;
                      /* Adds spacing from title */
                    }

                    .event-box h5 {
                      font-weight: bold;
                      color: #000;
                      margin: 5px 0 20px 0;
                      /* Adds 25px spacing below h5 */
                    }


                    .event-details {
                      display: flex;
                      gap: 15px;
                      /* Increases spacing between date and time */
                      align-items: center;
                      color: #2e7d32;
                      /* Green color for icons and text */
                      margin-bottom: 10px;
                      /* Adds spacing below event details */
                    }

                    .event-date,
                    .event-time {
                      font-size: 1.1rem;
                      /* Increases font size for emphasis */
                      font-weight: bold;
                      /* Makes text bold */
                      margin-top: 5px;
                      /* Creates distance from top */
                      display: flex;
                      align-items: center;
                    }

                    .event-details i {
                      margin-right: 5px;
                    }

                    .details-btn {
                      background-color: #ffc107;
                      /* Yellow color for button */
                      color: #fff;
                      border: none;
                      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
                      width: 100px;
                    }

                    .details-btn:hover {
                      background-color: #e0a800;
                    }
                  </style>

                  <!-- Calendar Box -->
                  <div class="col-md-4">
                    <div class="calendar">
                      <div class="header">
                        <!-- Month Dropdown -->
                        <div class="dropdown">
                          <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="monthDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-calendar"></i> <span id="selectedMonth">August</span>
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="monthDropdown">
                            <!-- Month Options -->
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 0)">January</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 1)">February</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 2)">March</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 3)">April</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 4)">May</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 5)">June</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 6)">July</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 7)">August</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 8)">September</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 9)">October</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 10)">November</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectMonth(event, 11)">December</a></li>
                          </ul>
                        </div>

                        <!-- Year Dropdown -->
                        <div class="dropdown">
                          <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="yearDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-calendar"></i> <span id="selectedYear">2024</span>
                          </button>
                          <ul class="dropdown-menu" aria-labelledby="yearDropdown">
                            <!-- Year Options -->
                            <li><a class="dropdown-item" href="#" onclick="selectYear(event, 2023)">2023</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectYear(event, 2024)">2024</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectYear(event, 2025)">2025</a></li>
                            <li><a class="dropdown-item" href="#" onclick="selectYear(event, 2026)">2026</a></li>
                          </ul>
                        </div>
                      </div>

                      <!-- Days of the Week Headers -->
                      <div class="days-of-week">
                        <div class="day-header">Sun</div>
                        <div class="day-header">Mon</div>
                        <div class="day-header">Tue</div>
                        <div class="day-header">Wed</div>
                        <div class="day-header">Thu</div>
                        <div class="day-header">Fri</div>
                        <div class="day-header">Sat</div>
                      </div>

                      <!-- Days Container -->
                      <div id="days" class="days"></div>
                    </div>
                  </div>

                  <!-- CSS for Scrollable Dropdown -->
                  <style>
                    .calendar {
                      border: 1px solid #ccc;
                      background-color: #fff;
                      border-radius: 8px;
                      padding: 10px;
                      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                      height: 250px;
                      overflow: hidden;
                    }

                    .header {
                      display: flex;
                      gap: 10px;
                      justify-content: space-between;
                      align-items: center;
                      margin-bottom: 5px;
                    }

                    .days-of-week {
                      display: grid;
                      grid-template-columns: repeat(7, 1fr);
                      margin-bottom: 5px;
                    }

                    .day-header {
                      font-weight: bold;
                      text-align: center;
                      color: black;
                    }

                    .days {
                      display: grid;
                      grid-template-columns: repeat(7, 1fr);
                      grid-auto-rows: 30px;
                      overflow-y: auto;
                      height: calc(100% - 90px);
                    }

                    .day {
                      padding: 5px;
                      text-align: center;
                      border: 1px solid #ddd;
                      transition: background-color 0.2s;
                      cursor: pointer;
                    }

                    .day:hover {
                      background-color: #e9ecef;
                    }

                    .dropdown-menu {
                      max-height: 200px;
                      overflow-y: auto;
                      color: #00542F;
                    }

                    /* Button and icon color */
                    .btn-outline-secondary.dropdown-toggle {
                      color: #00542F;
                      /* Text color */
                      border-color: #00542F;
                      /* Border color */
                    }

                    /* Button hover state */
                    .btn-outline-secondary.dropdown-toggle:hover {
                      background-color: #00542F;
                      /* Background color on hover */
                      color: #fff;
                      /* Text color on hover */
                    }

                    /* Dropdown menu item color */
                    .dropdown-menu .dropdown-item {
                      color: #00542F;
                    }

                    /* Dropdown menu item hover color */
                    .dropdown-menu .dropdown-item:hover {
                      background-color: #00542F;
                      color: #fff;
                    }
                  </style>

                  <!-- JavaScript -->
                  <script>
                    let currentDate = new Date();

                    function renderCalendar() {
                      const month = currentDate.getMonth();
                      const year = currentDate.getFullYear();

                      // Update displayed month and year in the dropdown buttons
                      document.getElementById('selectedMonth').textContent = currentDate.toLocaleString('default', { month: 'long' });
                      document.getElementById('selectedYear').textContent = year;

                      // Clear previous days
                      const daysContainer = document.getElementById('days');
                      daysContainer.innerHTML = '';

                      const firstDay = new Date(year, month, 1).getDay();
                      const lastDay = new Date(year, month + 1, 0).getDate();

                      // Blank days before the first day of the month
                      for (let i = 0; i < firstDay; i++) {
                        const emptyDay = document.createElement('div');
                        emptyDay.className = 'day empty';
                        daysContainer.appendChild(emptyDay);
                      }

                      // Days of the month
                      for (let i = 1; i <= lastDay; i++) {
                        const day = document.createElement('div');
                        day.className = 'day';
                        day.textContent = i;
                        day.addEventListener('click', () => selectDay(i));
                        daysContainer.appendChild(day);
                      }
                    }

                    function selectMonth(event, month) {
                      event.preventDefault(); // Prevents the page from scrolling to the top
                      currentDate.setMonth(month);
                      renderCalendar();
                    }

                    function selectYear(event, year) {
                      event.preventDefault(); // Prevents the page from scrolling to the top
                      currentDate.setFullYear(year);
                      renderCalendar();
                    }
                    // Initial render
                    renderCalendar();
                  </script>

                </div>
              </div>

              <div>
                <div class="container mt-5">
                  <div class="row align-items-center">
                    <div class="col-md-8 h5 fw-black fs-10 ps-5">
                      <h2 class="mr-5"><span class="text-warning fw-bold me-2 fs-10">|</span>Activities</h2>
                    </div>
                    <div class="col-md-4 d-flex justify-content-end">
                      <button class="btn btn-primary mx-2">Beacon of Youth Technology Enthusia</button>
                    </div>
                  </div>
                </div>

                <div class="container pt-5 mx-auto">
                  <div id="tableContent">
                    <table class="table table-bordered">
                      <thead class="thead-light fw-bold">
                        <tr class="fw-bold fs-4 text-dark">
                          <th>Title</th>
                          <th>from</th>
                          <th>to</th>
                          <th>Type</th>
                          <th>Venue</th>
                          <th>Status</th>
                          <th>Accomplishment Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>TechFest</td>
                          <td>03-21</td>
                          <td>03-22</td>
                          <td>Co-Curricular</td>
                          <td>Court 1</td>
                          <td>Approved</td>
                          <td>Accomplished</td>
                        </tr>
                        <tr>
                          <td>TechFest</td>
                          <td>03-21</td>
                          <td>03-22</td>
                          <td>Co-Curricular</td>
                          <td>Court 1</td>
                          <td>Approved</td>
                          <td>Accomplished</td>
                        </tr>
                        <tr>
                          <td>TechFest</td>
                          <td>03-21</td>
                          <td>03-22</td>
                          <td>Co-Curricular</td>
                          <td>Court 1</td>
                          <td>Approved</td>
                          <td>Accomplished</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
                <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

                <script>
                  // Print function
                  document.getElementById('printButton').addEventListener('click', function () {
                    window.print();
                  });

                  // PDF download function
                  document.getElementById('pdfButton').addEventListener('click', function () {
                    const element = document.getElementById('tableContent');
                    html2pdf()
                      .from(element)
                      .save('transaction_report.pdf');
                  });
                </script>
              </div>

              <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
              <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
              <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

            </div>
          </div>
          <!--Upcoming Events end-->
        </div>
      </div>
      <!-- End of First Main Wrap -->
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
</body>

</html>