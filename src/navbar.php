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
                            
                            <!-- Notification Icon -->
                            <button id="notificationBtn"
                                style="background-color: transparent; border: none; padding: 0;">
                                <lord-icon src="https://cdn.lordicon.com/lznlxwtc.json" trigger="hover"
                                    colors="primary:#004024" style="width:30px; height:30px;">
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
                                        <span style="font-weight: bold; color: #004024; text-decoration: none;"><?php echo htmlspecialchars($fullname); ?></span>
                                        <span
                                            style="font-size: 0.85em; color: #6c757d; text-decoration: none;"><?php echo htmlspecialchars($email); ?></span>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="../user/profile.php"><i class="bx bx-user"></i> My
                                            Profile</a></li>
                                    <li><a class="dropdown-item" href="../user/logout.php"><i
                                                class="bx bx-log-out"></i>
                                            Logout</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </nav>
            </header>
            <!-- Header End -->

        
        <!-- End of 2nd Body Wrapper -->