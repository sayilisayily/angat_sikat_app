<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Preview</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <!-- Optional: Custom CSS for spacing, if needed -->
    <style>
        .navbar .dropdown-toggle .rounded-circle {
            width: 40px;
            height: 40px;
        }
    </style>
</head>
<body>

<!-- Navbar with Notification, Profile Picture, and Dropdown -->
<nav class="navbar navbar-expand-lg navbar-light bg-light justify-content-end p-3">
    <div class="container-fluid">
        <!-- Notification Bell Icon -->
        <button class="btn btn-link position-relative me-3">
            <i class="fa-solid fa-bell fa-lg"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                3 <!-- Sample notification count -->
            </span>
        </button>

        <!-- Profile Section -->
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://via.placeholder.com/40" alt="Profile" class="rounded-circle me-2">
                <div class="d-none d-md-block">
                    <span id="username">User's Name</span> <br>
                    <small id="useremail">useremail@example.com</small>
                </div>
            </a>

            <!-- Dropdown Menu -->
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                <li><a class="dropdown-item" href="#">Edit Profile</a></li>
                <li><a class="dropdown-item" href="#">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>
