<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toggleable Green Sidebar with Bars Icon</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CSS for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        /* Sidebar Styles */
        #sidebar {
            height: 100vh;
            background-color: #28a745; /* Green background */
            color: white;
            padding: 20px;
            position: fixed;
            top: 0;
            left: -250px;
            width: 250px;
            transition: all 0.3s;
            z-index: 1000;
        }

        /* Toggled Sidebar */
        #sidebar.active {
            left: 0;
        }

        /* Content Overlay */
        #content {
            transition: all 0.3s;
        }

        #content.active {
            margin-left: 250px;
        }

        /* Bars Icon Toggle */
        #sidebarToggle {
            position: fixed;
            top: 15px;
            left: 15px;
            font-size: 24px;
            cursor: pointer;
            z-index: 1100;
            color: #28a745;
        }

        #sidebarToggle.active {
            left: 265px;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Bars Icon Toggle -->
    <i id="sidebarToggle" class="fa-solid fa-bars"></i>

    <!-- Sidebar -->
    <div id="sidebar">
        <h4>Green Sidebar</h4>
        <ul class="list-unstyled">
            <li><a href="#" class="text-white">Dashboard</a></li>
            <li><a href="#" class="text-white">Budget</a></li>
            <li><a href="#" class="text-white">Activities</a></li>
            <li><a href="#" class="text-white">Transactions</a></li>
            <li><a href="#" class="text-white">Reports</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div id="content">
        <div class="container">
            <h1 class="mt-4">Main Content Area</h1>
            <p>This is the main content area. The sidebar can be toggled using the bars icon.</p>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for toggling) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script>
        $(document).ready(function () {
            // Toggle the sidebar using the bars icon
            $('#sidebarToggle').on('click', function () {
                $('#sidebar').toggleClass('active');
                $('#content').toggleClass('active');
                $(this).toggleClass('active');
            });
        });
    </script>
</body>
</html>
