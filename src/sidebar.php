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
                        <a class="sidebar-link" href="../dashboard/officer_dashboard.php" aria-expanded="false" data-tooltip="Dashboard">
                            <i class="bx bxs-dashboard"></i>
                            <span class="hide-menu">Dashboard</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" aria-expanded="false" data-tooltip="Budget">
                            <i class="bx bx-wallet"></i>
                            <span class="hide-menu">Budget</span>
                        </a>
                        <div class="submenu">
                            <a href="../budget_management/budget_overview.php">› Overview </a>
                            <a href="../budget_management/financial_plan.php">› Plan </a>
                            <a href="../budget_management/purchases/purchases.php">› Purchases</a>
                            <a href="../budget_management/maintenance/maintenance.php">› MOE</a>
                            <a href="../budget_management/budget_approval_table.php">› Approval</a>
                        </div>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="../activity_management/activities.php" aria-expanded="false"
                            data-tooltip="Manage Events">
                            <i class="bx bx-calendar"></i>
                            <span class="hide-menu">Manage Events</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="#transactions" aria-expanded="false" data-tooltip="Transactions">
                            <i class="bx bx-dollar-circle"></i>
                            <span class="hide-menu">Transactions</span>
                        </a>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" aria-expanded="false" data-tooltip="Income & Expenses">
                            <i class="bx bx-chart"></i>
                            <span class="hide-menu">Income & Expenses</span>
                        </a>
                        <div class="submenu">
                            <a href="#income">› Income</a>
                            <a href="../income_and_expenses/expenses.php">› Expenses</a>
                        </div>
                        </li>
                        <li class="sidebar-item">
                        <a class="sidebar-link" href="reports.php" aria-expanded="false" data-tooltip="Reports">
                            <i class="bx bx-file"></i>
                            <span class="hide-menu">Reports</span>
                        </a>
                        </li>
                        <li class="sidebar-item profile-container">
                        <a class="sidebar-link" href="../user/profile.html" aria-expanded="false" data-tooltip="Profile">
                            <div class="profile-pic-border">
                            <img src="<?php echo !empty($profile_picture) ? '../user/uploads/' . htmlspecialchars($profile_picture) : '../user/uploads/default.png'; ?>" alt="Profile Picture" class="profile-pic" />
                        </div>
                        <span class="profile-name"><?php echo htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']); ?></span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
<!-- Sidebar End -->

<!-- JavaScript to toggle submenu visibility -->
<script>
    document.querySelectorAll('.sidebar-item > .sidebar-link').forEach(item => {
        item.addEventListener('click', function (e) {
            const submenu = this.nextElementSibling; // Get the submenu if it exists
            if (submenu) {
                e.preventDefault(); // Prevent default only if there's a submenu
                this.parentElement.classList.toggle('show-submenu'); // Toggle submenu visibility
            }
        });
    });

    // Sidebar toggle functionality
    document.getElementById('toggleSidebar').addEventListener('click', function () {
        const sidebar = document.getElementById('sidebar');
        const mainWrapper = document.getElementById('main-wrapper');
        const appHeader = document.querySelector('.app-header');

        sidebar.classList.toggle('collapsed');
        mainWrapper.classList.toggle('expanded');

        // Adjust navbar width based on sidebar state
        if (sidebar.classList.contains('collapsed')) {
            appHeader.style.width = 'calc(100% - 70px)';
            appHeader.style.left = '70px';
        } else {
            appHeader.style.width = 'calc(100% - 250px)';
            appHeader.style.left = '250px';
        }
    });
</script>

<style>
    /* Sidebar initial styles */
    .left-sidebar {
        width: 250px;
        position: fixed;
        top: 0;
        left: 10px;
        height: 100%;
        background-color: #00542F;
        overflow: hidden;
        transition: width 0.3s ease-in-out;
    }

.left-sidebar.collapsed {
    width: 70px;
    border-radius: 0; /* Maintain no border radius when collapsed */
}

.top-bar {
    border-radius: 0; /* Remove border radius from top bar */
}

#main-wrapper {
    margin-left: 250px; /* Initial margin for the main wrapper */
    transition: margin-left 0.3s ease-in-out; /* Smooth transition for margin */
}

#main-wrapper.expanded {
    margin-left: 70px; /* Adjust margin when sidebar is collapsed */
}

.left-sidebar.collapsed .hide-menu {
    display: none; /* Hide menu items when collapsed */
}

.left-sidebar.collapsed i {
    text-align: center;
    width: 100%; /* Center icons when collapsed */
}

.submenu {
    display: none; /* Initially hide submenus */
    padding-left: 20px;
    background-color: #006f4e;
}

.sidebar-item.show-submenu .submenu {
    display: block; /* Show submenu when toggled */
}
</style>