<!-- 2nd Body wrapper -->
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

            <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                <div class="container-fluid d-flex justify-content-end align-items-center" style="padding: 0 1rem;">
                    <!-- Notification Icon -->
                    <div style="position: relative; display: inline-block;">
                            <button id="notificationBtn" style="background-color: transparent; border: none; padding: 0; position: relative;">
                                <lord-icon
                                    src="https://cdn.lordicon.com/lznlxwtc.json"
                                    trigger="hover"
                                    colors="primary:#004024"
                                    style="width:30px; height:30px;">
                                </lord-icon>
                                <!-- Notification Count Badge -->
                                <span id="notificationCount" style="
                                    position: absolute;
                                    top: -5px;
                                    right: -5px;
                                    background-color: red;
                                    color: white;
                                    font-size: 12px;
                                    padding: 2px 6px;
                                    border-radius: 50%;
                                    display: none;">0</span>
                            </button>


                                <!-- Notification Dropdown -->
                                <div id="notificationDropdown" class="dropdown-menu p-2 shadow" 
                                    style="display: none; position: absolute; right: 0; top: 35px; width: 300px; max-height: 400px; 
                                    overflow-y: auto; background-color: white; border-radius: 5px; z-index: 1000;">
                                    <p style="margin: 0; font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 5px;">
                                        Notifications
                                    </p>
                                    <div id="notificationList">
                                        <!-- Notifications will be dynamically loaded here -->
                                    </div>
                                    <p id="noNotifications" style="text-align: center; margin-top: 10px; color: gray; display: none;">
                                        No new notifications
                                    </p>
                                </div>
                            </div>

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown"
                            aria-expanded="false" style="text-decoration: none;">
                            <img class="border border-dark rounded-circle" src="<?php echo !empty($profile_picture) ? '../user/uploads/' . htmlspecialchars($profile_picture) : '../user/uploads/default.png'; ?>" alt="Profile"
                                style="width: 40px; height: 40px; margin-left: 10px;">
                            <span class="visually-hidden"><?php echo htmlspecialchars($user['username']); ?></span>
                            <div class="d-flex flex-column align-items-start ms-2">
                                <span style="font-weight: bold; color: #004024;"><?php echo htmlspecialchars($fullname); ?></span>
                                <span style="font-size: 0.85em; color: #6c757d;"><?php echo htmlspecialchars($email); ?></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../user/profile.html"><i class="bx bx-user"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="../user/logout.php"><i class="bx bx-log-out"></i> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
    </header>
    <!-- Header End -->
</div>
<!-- End of 2nd Body Wrapper -->

<style>
    /* Navbar styles */
    .app-header {
        position: fixed; /* Fix the position of the navbar */
        width: calc(100% - 250px); /* Adjust width based on sidebar */
        top: 0; /* Align to the top */
        left: 250px; /* Align next to the sidebar */
        transition: width 0.3s ease-in-out, left 0.3s ease-in-out; /* Smooth transition */
        z-index: 1000; /* Keep navbar above other content */
    }

    /* Adjust navbar width when sidebar is collapsed */
    .left-sidebar.collapsed + .app-header {
        width: calc(100% - 70px);
        left: 70px;
    }
</style>