<?php
include '../connection.php';
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/angat sikat.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <!--Custom CSS for Sidebar-->
    <link rel="stylesheet" href="../html/sidebar.css" />
    <!--Custom CSS for Activities-->
    <link rel="stylesheet" href="../activity_management/activities.css" />
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
    <script src="../assets/js/dashboard.js"></script>
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

    <!-- CSS for full calender -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.css" rel="stylesheet" />
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- JS for full calender -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.9.0/fullcalendar.min.js"></script>
</head>

<body>
    <!-- Overall Body Wrapper -->
    <?php include '../navbar.php';?>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <?php include '../sidebar.php'; ?>

        <!--  2nd Body wrapper -->
        <div class="body-wrapper">
            <div class="container mt-5">
                <h2>Activities</h2>
                <div id="calendar"></div>
            </div>

            <!-- FullCalendar JS -->
            <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

            <script>
                $(document).ready(function () {
                    display_events();
                });

                function display_events() {
                    var events = new Array();
                    $.ajax({
                        url: 'display_event.php',
                        dataType: 'json',
                        success: function (response) {
                            var result = response.data;
                            $.each(result, function (i, item) {
                                events.push({
                                    event_id: result[i].event_id,
                                    title: result[i].title,
                                    start: result[i].start,
                                    end: result[i].end,
                                    color: result[i].color,
                                    url: result[i].url
                                });
                            });

                            // Calendar
                            var calendar = $('#calendar').fullCalendar({
                                header: {
                                    left: 'prev,next today', // Add navigation buttons
                                    center: 'title',
                                    right: 'month,agendaWeek,agendaDay' // Toggle buttons for month, week, and day views
                                },
                                initialView: 'month',
                                timeZone: 'local',
                                disableDragging: true,
                                selectable: true,
                                selectHelper: true,
                                select: function (start, end) {
                                    $('#event_start_date').val(moment(start).format('YYYY-MM-DD'));
                                    $('#event_end_date').val(moment(end).format('YYYY-MM-DD'));
                                    $('#event_entry_modal').modal('show');
                                },
                                events: events,
                                eventRender: function (event, element, view) {
                                    element.bind('click', function () {
                                        alert(event.event_id);
                                    });
                                }
                            }); // End of fullCalendar block
                        }, // End of success block
                        error: function (xhr, status) {
                            alert(response.msg);
                        }
                    }); // End of ajax block
                }

            </script>
        </div>
    </div>
</body>

</html>