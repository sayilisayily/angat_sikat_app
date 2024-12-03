<?php
include 'connection.php';
include '../session_check.php'; 
include '../user_query.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
    header("Location: ../user/login.html");
    exit();
}

$sql = "SELECT * FROM events WHERE archived = 0 AND organization_id = $organization_id";
$result = $conn->query($sql);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <title>Activities Table</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <!--Custom CSS for Sidebar-->
    <link rel="stylesheet" href="../html/sidebar.css" />
    <!--Custom CSS for Activities-->
    <link rel="stylesheet" href="css/activities.css" />
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
    <?php include '../navbar.php';?>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
      <?php include '../sidebar.php'; ?>

        <?php include '../navbar.php';?>
    </div>
    <!-- End of Overall Body Wrapper -->

    
    <div class="container mt-5 p-5">
        <h2 class="mb-4"><span class="text-warning fw-bold me-2">|</span> Activities
            <button class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#addEventModal"
             style="height: 40px; width: 200px; border-radius: 8px; font-size: 12px;">
                <i class="fa-solid fa-plus"></i> Add Activity
            </button>
        </h2>
        <table id="eventsTable" class="table">
            <thead>
                <tr>
                    <th rowspan=2>Title</th>
                    <th rowspan=2>Venue</th>
                    <th colspan=2 style="text-align: center;"> Date </th>
                    <th rowspan=2>Type</th>
                    <th rowspan=2>Status</th>
                    <th rowspan=2>Accomplished</th>
                    <th rowspan=2>Actions</th>
                </tr>
                <tr>
                    <th>Start</th>
                    <th>End</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $checked = $row['accomplishment_status'] ? 'checked' : '';
                        $disabled = ($row['event_status'] !== 'Approved') ? 'disabled' : '';
                        echo "<tr>
                                <td><a class='link-offset-2 link-underline link-underline-opacity-0' href='event_details.php?event_id={$row['event_id']}'>{$row['title']}</a></td>
                                <td>{$row['event_venue']}</td>
                                <td>" . date('F j, Y', strtotime($row['event_start_date'])) . "</td>
                                <td>" . date('F j, Y', strtotime($row['event_end_date'])) . "</td>
                                <td>{$row['event_type']}</td>
                                <td>";
                                  if ($row['event_status'] == 'Pending') {
                                    echo " <span class='badge rounded-pill pending'> ";
                                } elseif ($row['event_status'] == 'Approved') {
                                    echo " <span class='badge rounded-pill approved'> ";
                                } elseif ($row['event_status'] == 'Disapproved') {
                                    echo " <span class='badge rounded-pill disapproved'> ";
                                }
                                echo "
                                    {$row['event_status']}
                                    </span>
                                    </td>
                                    <td>
                                        <input type='checkbox' 
                                        class='form-check-input' 
                                        onclick='showConfirmationModal({$row['event_id']}, this.checked)' 
                                        $checked 
                                        $disabled>
                                    </td>
                                    <td>
                                        <button class='btn btn-primary btn-sm edit-btn mb-3' 
                                                data-bs-toggle='modal' 
                                                data-bs-target='#editEventModal' 
                                                data-id='{$row['event_id']}'>
                                            <i class='fa-solid fa-pen'></i> Edit
                                        </button>
                                        <button class='btn btn-danger btn-sm archive-btn mb-3' 
                                                data-id='{$row['event_id']}'>
                                            <i class='fa-solid fa-box-archive'></i> Archive
                                        </button>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='9' class='text-center'>No events found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- End of Overall Body Wrapper -->

    
    

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Accomplishment Status Change</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to change the accomplishment status of this event?
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

    <!-- Add Event Modal -->
    <div class="modal fade" id="addEventModal" tabindex="-1" role="dialog" aria-labelledby="addEventLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="addEventForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addEventLabel">Add New Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form fields -->
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="title">Event Title</label>
                                <!-- Plan ID -->
                                <input type="hidden" id="plan_id" name="plan_id">
                                <!-- Event title dropdown -->
                                <select class="form-control" id="title" name="title">
                                    <option value="">Select Event Title</option>
                                    <?php
                                    // Query to fetch titles with category 'Activities'
                                    $title_query = "SELECT plan_id, title, date, type, amount FROM financial_plan WHERE category = 'Activities' OR type = 'Income' AND organization_id = $organization_id";
                                    $result = mysqli_query($conn, $title_query);

                                    if ($result && mysqli_num_rows($result) > 0) {
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            echo '<option value="' . htmlspecialchars($row['title']) . '" 
                                                    data-plan-id="' . htmlspecialchars($row['plan_id']) . '"
                                                    data-date="' . htmlspecialchars($row['date']) . '" 
                                                    data-amount="' . htmlspecialchars($row['amount']) . '" 
                                                    data-type="' . htmlspecialchars($row['type']) . '">' 
                                                    . htmlspecialchars($row['title']) . '</option>';
                                        }
                                    } else {
                                        echo '<option value="">No titles available</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col">
                                <label for="venue">Venue</label>
                                <input type="text" class="form-control" id="venue" name="venue">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" readonly>
                            </div>
                            <div class="col">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="type">Event Type</label>
                                <input type="text" class="form-control" id="type" name="type" readonly>                                 
                            </div>
                            <div class="col">
                                <label for="amount">Event Total Amount</label>
                                <input type="text" class="form-control" id="amount" name="amount" readonly>                                 
                            </div>
                        </div>

                        <!-- Success Message Alert -->
                        <div id="successMessage1" class="alert alert-success d-none mt-3" role="alert">
                            Event added successfully!
                        </div>
                        <!-- Error Message Alert -->
                        <div id="errorMessage1" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList1"></ul> <!-- List for showing validation errors -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1" role="dialog" aria-labelledby="editEventModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form id="editEventForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editEventModalLabel">Edit Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Hidden field for event ID -->
                        <input type="hidden" id="editEventId" name="event_id">

                        <!-- Other form fields -->
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="editEventTitle">Event Title</label>
                                <input type="text" class="form-control" id="editEventTitle" name="title" required>
                            </div>
                            <div class="col">
                                <label for="editEventVenue">Event Venue</label>
                                <input type="text" class="form-control" id="editEventVenue" name="event_venue" required>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="editEventStartDate">Start Date</label>
                                <input type="date" class="form-control" id="editEventStartDate" name="event_start_date"
                                    required>
                            </div>
                            <div class="col">
                                <label for="editEventEndDate">End Date</label>
                                <input type="date" class="form-control" id="editEventEndDate" name="event_end_date"
                                    required>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <div class="col">
                                <label for="editEventType">Event Type</label>
                                <input class="form-control" id="editEventType" name="event_type" readonly>
                            </div>
                        </div>
                        <input type="hidden" id="editEventStatus" name="event_status">
                        <input type="hidden" id="editAccomplishmentStatus" name="accomplishment_status">

                        <!-- Success Message Alert -->
                        <div id="successMessage2" class="alert alert-success d-none mt-3" role="alert">
                            Event updated successfully!
                        </div>
                        <!-- Error Message Alert -->
                        <div id="errorMessage2" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList2"></ul> <!-- List for showing validation errors -->
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
                    <h5 class="modal-title" id="archiveModalLabel">Archive Event</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to archive this event?
                    <input type="hidden" id="archiveEventId">
                    <!-- Success Message Alert -->
                    <div id="successMessage3" class="alert alert-success d-none mt-3" role="alert">
                            Event archived successfully!
                        </div>
                        <!-- Error Message Alert -->
                        <div id="errorMessage3" class="alert alert-danger d-none mt-3" role="alert">
                            <ul id="errorList3"></ul> <!-- List for showing validation errors -->
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
    <script src="js/activities.js"></script>
</body>

</html>

<?php
$conn->close();
?>