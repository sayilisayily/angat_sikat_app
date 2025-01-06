<?php
include("../connection.php");
include '../session_check.php';

// Check if user is logged in and has officer role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'officer') {
    header("Location: ../user/login.html");
    exit();
}

include '../user_query.php';
include '../organization_query.php';
?>

<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link rel="shortcut icon" type="image/png" href="../assets/images/logos/angat sikat.png" />
    <link rel="stylesheet" href="../assets/css/styles.min.css" />
    <!--Custom CSS for Activities-->
    <link rel="stylesheet" href="../activity_management/css/activities.css" />
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
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <!-- Overall Body Wrapper -->
    <?php include '../navbar.php';?>
    <div class="page-wrapper d-flex flex-column h-100" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
      <?php include '../sidebar.php'; ?>
      

        <!--  2nd Body wrapper -->
        <div class="body-wrapper">
            <!-- First Main Wrap -->
            <div class="container-fluid">
                <div class="row">
                    <!-- Left Column for Welcome Message and Organization Info Box -->
                        <div class="col-md-8 flex-grow-1">
                        <!-- Welcome Message with adjusted left margin -->
                        <h1 class="welcome-message h5 fw-bold mb-4">
                            <span class="text-warning fw-bold me-2">|</span>Welcome,
                            <?php echo htmlspecialchars($user['first_name']); ?>!
                        </h1>

                        <!-- Organization Info Box -->
                        <div class="organization-card p-4 rounded shadow-sm bg-white border mb-4">
                            <!-- Display organization logo, fallback to a default image if empty -->
                            <div class="d-flex align-items-start">
                                <img class="organization-logo me-3"
                                    src="<?= !empty($org_logo) ? '../organization_management/uploads/' . $org_logo : '../organization_management/uploads/default_logo.png' ?>"
                                    alt="<?= $org_name ?> Logo" 
                                    style="max-width: 100%; height: auto; max-height: 125px; object-fit: contain;"
                                    class="img-fluid" /> <!-- Ensure logo is responsive -->
                                <div class="organization-details">
                                    <p class="text-muted small-text mb-1">Name of Student Organization</p>
                                    <h1 class="fw-semibold h5 mb-2">
                                        <?= htmlspecialchars($org_name) ?>
                                    </h1>
                                    <div class="d-flex gap-5">
                                        <div>
                                            <p class="text-muted small-text mb-1">No. of Members</p>
                                            <h1 class="fw-semibold h6">
                                                <?= htmlspecialchars($org_members) ?>
                                            </h1>
                                        </div>
                                        <div>
                                            <p class="text-muted small-text mb-1">Status</p>
                                            <h1 class="fw-semibold h6">
                                                <?= htmlspecialchars($org_status) ?>
                                            </h1>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    
                        <!-- End of Organization Info Box -->

                        <!-- Financial Summary Cards Row -->
                        <div class="row justify-content-center mx-1">

                        <?php
                            // Fetch the latest two balance records from the balance_history table
                            $query_balance = "SELECT balance, updated_at FROM balance_history 
                                            WHERE organization_id = ? 
                                            ORDER BY updated_at DESC 
                                            LIMIT 2";

                            $stmt_balance = $conn->prepare($query_balance);
                            $stmt_balance->bind_param("i", $organization_id);
                            $stmt_balance->execute();
                            $result_balance = $stmt_balance->get_result();

                            $latest_balances = [];
                            while ($row = $result_balance->fetch_assoc()) {
                                $latest_balances[] = $row['balance'];
                            }

                            // Calculate percentage difference for balance
                            $balance_percentage_change = 0;
                            if (count($latest_balances) === 2) {
                                $latest_balance = $latest_balances[0];
                                $previous_balance = $latest_balances[1];
                                
                                if ($previous_balance > 0) {
                                    $balance_percentage_change = (($latest_balance - $previous_balance) / $previous_balance) * 100;
                                }
                            }

                            $stmt_balance->close();
                            ?>

                            <!-- Balance Card -->
                            <div class="col-md-4 mb-3">
                                <div class="card gradient-card-2 p-3 shadow-sm mx-2">
                                    <h7 class="text-white text-start d-block" style="margin-left: 2px;">Balance</h7>
                                    <div class="d-flex align-items-center">
                                        <h1 class="fw-bold text-white" style="margin-left: 2px;">
                                            ₱<?php echo number_format($balance); ?>
                                        </h1>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div class="badge bg-warning text-white fw-medium percentage-box"
                                            style="font-size: 0.75rem; padding: 2px 6px;"><?php echo number_format(abs($balance_percentage_change), 1); ?>% 
                                            <?php echo $balance_percentage_change >= 0 ? '▲' : '▼'; ?></div>
                                    </div>
                                </div>
                            </div>

                        <?php
                            // Fetch the latest two income records from the income_history table
                            $query_income = "SELECT income, updated_at FROM income_history 
                                            WHERE organization_id = ? 
                                            ORDER BY updated_at DESC 
                                            LIMIT 2";

                            $stmt_income = $conn->prepare($query_income);
                            $stmt_income->bind_param("i", $organization_id);
                            $stmt_income->execute();
                            $result_income = $stmt_income->get_result();

                            $latest_incomes = [];
                            while ($row = $result_income->fetch_assoc()) {
                                $latest_incomes[] = $row['income'];
                            }

                            // Calculate percentage difference for income
                            $income_percentage_change = 0;
                            $income = 0; // Initialize $income

                            if (count($latest_incomes) === 2) {
                                $latest_income = $latest_incomes[0];
                                $previous_income = $latest_incomes[1];
                                
                                $income = $latest_income; // Assign latest income to $income
                                
                                if ($previous_income > 0) {
                                    $income_percentage_change = (($latest_income - $previous_income) / $previous_income) * 100;
                                }
                            } elseif (count($latest_incomes) === 1) {
                                $income = $latest_incomes[0]; // If only one record, assign it to $income
                            }

                            $stmt_income->close();
                            ?>

                            <!-- Income Card -->
                            <div class="col-md-4 mb-3">
                                <div class="card gradient-card-1 p-3 shadow-sm mx-2">
                                    <h7 class="text-white text-start d-block" style="margin-left: 2px;">Income</h7>
                                    <div class="d-flex align-items-center">
                                        <h1 class="fw-bold text-white" style="margin-left: 2px;">₱ <?php echo number_format($income); ?></h1>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div class="badge bg-warning text-white fw-medium percentage-box"
                                            style="font-size: 0.75rem; padding: 2px 6px;"><?php echo number_format(abs($income_percentage_change), 1); ?>% 
                                            <?php echo $income_percentage_change >= 0 ? '▲' : '▼'; ?></div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            // Fetch the latest two expense records from the expense_history table
                            $query = "SELECT expense, updated_at FROM expense_history 
                                    WHERE organization_id = ? 
                                    ORDER BY updated_at DESC 
                                    LIMIT 2";

                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $organization_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            $latest_expenses = [];
                            while ($row = $result->fetch_assoc()) {
                                $latest_expenses[] = $row['expense'];
                            }

                            // Calculate percentage difference
                            $percentage_change = 0;
                            $expense = 0; // Initialize $expense

                            if (count($latest_expenses) === 2) {
                                $latest = $latest_expenses[0];
                                $previous = $latest_expenses[1];
                                
                                $expense = $latest; // Assign latest expense to $expense
                                
                                if ($previous > 0) {
                                    $percentage_change = (($latest - $previous) / $previous) * 100;
                                }
                            } elseif (count($latest_expenses) === 1) {
                                $expense = $latest_expenses[0]; // If only one record, assign it to $expense
                            }

                            $stmt->close();
                            ?>

                            <!-- Expense Card -->
                            <div class="col-md-4 mb-3">
                                <div class="card gradient-card-3 p-3 shadow-sm mx-2">
                                    <h7 class="text-white text-start d-block" style="margin-left: 2px;">Expense</h7>
                                    <div class="d-flex align-items-center">
                                        <h1 class="fw-bold text-white" style="margin-left: 2px;">₱<?php echo number_format($expense); ?></h1>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <div class="badge bg-warning text-white fw-medium percentage-box"
                                            style="font-size: 0.75rem; padding: 2px 6px;"><?php echo number_format(abs($percentage_change), 1); ?>% 
                                            <?php echo $percentage_change >= 0 ? '▲' : '▼'; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End of Financial Summary Cards Row -->
                        
                        <!-- Balance Report Section -->
                        <?php 
                        $query = "SELECT MONTH(updated_at) AS month, YEAR(updated_at) AS year, balance 
                                FROM balance_history 
                                WHERE organization_id = ? 
                                ORDER BY year ASC, month ASC";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('i', $organization_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        $balances = [];
                        while ($row = $result->fetch_assoc()) {
                            $balances[] = $row; // Store all rows for rendering in the graph
                        }
                        $stmt->close();

                        // Prepare data for visualization
                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                        $monthly_balances = array_fill(1, 12, 0); // Initialize balances for all months
                        $max_balance = 0;

                        foreach ($balances as $balance) {
                            $monthly_balances[(int)$balance['month']] = $balance['balance'];
                            $max_balance = max($max_balance, $balance['balance']); // Determine max balance
                        }
                        ?>

                        <!-- Balance Report Section -->
                        <div class="responsive-container" style="margin: 20px; margin-left: 5px; margin-right: 65px;">
                            <div class="p-4 bg-white rounded border shadow-md justify-center mx-2 balance-report"
                                style="max-width: 700px; width: 111%;">
                                <div class="d-flex justify-content-start gap-5">
                                    <h2 class="text-lg fw-bold">Balance Report</h2>
                                    <div class="d-flex gap-3">
                                        <button class="btn btn-secondary btn-sm" onclick="switchView('monthly')">Monthly</button>
                                        <button class="btn btn-secondary btn-sm" onclick="switchView('quarterly')">Quarterly</button>
                                        <button class="btn btn-secondary btn-sm" onclick="switchView('yearly')">Yearly</button>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="fw-semibold">Average per month</p>
                                    <?php
                                        if (count($balances) > 0) {
                                            $total_balance = array_sum($monthly_balances);
                                            $average_balance = $total_balance / count($balances);
                                            echo "<h1 class='fw-bold h5 text-success'>₱" . number_format($average_balance, 2) . "</h1>";
                                        } else {
                                            echo "<h1 class='fw-bold h5 text-muted'>No data available</h1>";
                                        }
                                    ?>
                                </div>

                                <div class="container mx-auto mt-3">
                                    <!-- Bar Graph Container -->
                                    <div class="row g-3">
                                        <?php
                                            foreach ($months as $index => $month) {
                                                $month_number = $index + 1;
                                                $month_balance = $monthly_balances[$month_number];
                                                $height = $max_balance > 0 ? ($month_balance / $max_balance) * 100 : 0; // Scale height
                                                echo "
                                                <div class='col-1'>
                                                    <div class='d-flex flex-column-reverse align-items-center' style='height: 100px;'>
                                                        <div class='w-100 bg-success' style='height: {$height}px;' 
                                                            data-bs-toggle='tooltip' 
                                                            title='₱" . number_format($month_balance, 2) . "'></div>
                                                    </div>
                                                    <p class='mt-1 text-sm font-medium text-center'>{$month}</p>
                                                </div>";
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Balance Report End -->

                        <style>
                            @media (max-width: 768px) {
                                .responsive-container {
                                    overflow-x: auto; /* Enable horizontal scrolling */
                                    width: 100%; /* Full width on smaller screens */
                                }
                                .balance-report {
                                    width: 725px; /* Maintain original width on larger screens */
                                }
                            }
                        </style>
                    

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                                new bootstrap.Tooltip(tooltipTriggerEl);
                            });
                        });
                    </script>

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
                                    <img class="rounded-circle mb-3 border border-dark d-block mx-auto" src="Sir Renato.jpg" alt=""
                                        style="width: 40px; height: 40px;" />
                                    <p class="fw-semibold text-sm text-black">Renato Bautista</p>
                                    <p class="text-xs text-gray">Instructor, DCS</p>
                                </div>

                                <div class="text-center">
                                    <img class="rounded-circle mb-3 border border-dark d-block mx-auto" src="Maam Janessa.jpg" alt=""
                                        style="width: 40px; height: 40px;" />
                                    <p class="fw-semibold text-sm text-black">Janessa Dela Cruz</p>
                                    <p class="text-xs text-gray">Instructor, DCS</p>
                                </div>
                                </div>
                            </div>

                            <!-- Fourth Container -->
                            <div class="p-4 bg-white rounded mt-4 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <lord-icon src="https://cdn.lordicon.com/ysqeagpz.json" trigger="loop"
                                        colors="primary:#6acbff"
                                        style="width:40px;height:40px;transform: rotate(360deg);"></lord-icon>
                                    <h1 class="text-black fw-bold h5 ms-2">Financial Deadlines</h1>
                                </div>

                                <div class="ms-2 mt-3">
                                    <div class="mt-1">
                                        <h1 class="fw-bold text-xs fs-5 text-black">Office Supplies</h1>
                                        <p class="text-gray fw-semibold text-xs mb-3">October 12, 2024</p>
                                    </div>

                                    <div class="mt-1">
                                        <h1 class="fw-bold text-xs fs-5 text-black">Transportation</h1>
                                        <p class="text-gray fw-semibold text-xs mb-3">L-300</p>
                                    </div>

                                    <div class="mt-1">
                                        <h1 class="fw-bold text-xs fs-5 text-black">Speakers</h1>
                                        <p class="text-gray fw-semibold text-xs mb-3">November 11, 2024</p>
                                    </div>
                                </div>
                            </div>

                            <?php
                            // Query to fetch the first and latest balance for the current month
                            $query = "
                                SELECT balance, updated_at 
                                FROM balance_history 
                                WHERE organization_id = ? 
                                AND MONTH(updated_at) = MONTH(CURRENT_DATE()) 
                                AND YEAR(updated_at) = YEAR(CURRENT_DATE())
                                ORDER BY updated_at ASC
                            ";

                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $organization_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            $first_balance = null;
                            $latest_balance = null;

                            if ($result->num_rows > 0) {
                                $rows = $result->fetch_all(MYSQLI_ASSOC);
                                $first_balance = $rows[0]['balance']; // First balance of the month
                                $latest_balance = $rows[count($rows) - 1]['balance']; // Latest balance of the month
                            }

                            // Compute the percentage change
                            $percentage_change = ($latest_balance && $first_balance) ? 
                                (($latest_balance - $first_balance) / $first_balance) * 100 : 0;

                            $remaining_balance = $latest_balance ?: 0;
                            ?>

                            <!-- Radial Progress -->
                            <div class="d-flex justify-evenly gap-7 p-6 bg-white rounded mt-4 shadow-sm">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <h1 class="fw-bold fs-7">Balance</h1>
                                        <p class="fw-semibold text-secondary">Total Monthly</p>
                                        <h1 class="fw-bold h6">₱ <?php echo number_format($latest_balance, 2); ?> 
                                            <span class="badge bg-warning text-white fw-medium percentage-box"
                                            style="font-size: 0.75rem; padding: 2px 6px;">
                                            <?php echo number_format($percentage_change, 1); ?>%
                                            </span>
                                        </h1>
                                    </div>
                                </div>


                                <div>
                                    <div class="d-flex flex-column align-items-center">
                                        <div class="position-relative d-flex align-items-center justify-content-center"
                                            style="width: 120px; height: 120px;">
                                            <!-- Circle Background -->
                                            <div class="position-absolute w-100 h-100 rounded-circle bg-light"></div>

                                            <!-- Radial Progress Circle -->
                                            <svg class="position-absolute w-100 h-100"
                                                style="transform: rotate(-90deg);">
                                                <circle cx="50%" cy="50%" r="45%" stroke="currentColor" stroke-width="10"
                                                    class="text-purple-500" fill="none" stroke-dasharray="283"
                                                    stroke-dashoffset="<?php echo 283 - (283 * $percentage_change / 100); ?>">
                                                </circle>
                                            </svg>

                                            <!-- Centered Text -->
                                            <div class="position-absolute text-center">
                                                <p class="h6 fw-bold text-dark">₱ <?php echo number_format($remaining_balance, 2); ?></p>
                                                <p class="text-sm fw-semibold text-secondary">Remaining balance</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Transaction Chart -->
                    <?php
                    // Fetch income records
                    $income_query = "
                        SELECT 
                            'Income' AS type, 
                            title AS description, 
                            amount, 
                            created_at AS date 
                        FROM income 
                        WHERE organization_id = ? AND archived = 0
                        ORDER BY created_at DESC
                        LIMIT 5
                    "; // Fetching latest 5 income records

                    $expenses_query = "
                        SELECT 
                            'Expense' AS type, 
                            title AS description, 
                            amount, 
                            created_at AS date 
                        FROM expenses 
                        WHERE organization_id = ? AND archived = 0
                        ORDER BY created_at DESC
                        LIMIT 5
                    "; // Fetching latest 5 expense records

                    // Prepare and execute income query
                    $stmt_income = $conn->prepare($income_query);
                    $stmt_income->bind_param("i", $organization_id);
                    $stmt_income->execute();
                    $income_results = $stmt_income->get_result()->fetch_all(MYSQLI_ASSOC);

                    // Prepare and execute expenses query
                    $stmt_expenses = $conn->prepare($expenses_query);
                    $stmt_expenses->bind_param("i", $organization_id);
                    $stmt_expenses->execute();
                    $expenses_results = $stmt_expenses->get_result()->fetch_all(MYSQLI_ASSOC);

                    // Combine results
                    $transactions = array_merge($income_results, $expenses_results);

                    // Sort transactions by date (newest first)
                    usort($transactions, function ($a, $b) {
                        return strtotime($b['date']) - strtotime($a['date']);
                    });

                    // Limit to the latest 10 transactions
                    $transactions = array_slice($transactions, 0, 10);
                    ?>
                    <!-- End of Transaction Chart -->

                    <!--Recent Transaction dashboard-->
                    <div class="container">
                        <h3 class="welcome-message h5 fw-bold mb-4 mt-5">
                            <span class="text-warning fw-bold me-2">|</span>Recent Transactions
                        </h3>
                        <div class="container mt-5">
                            <button id="pdfButton" class="btn btn-success mb-3"><i class="fas fa-download"></i> Download PDF</button>

                            <div id="tableContent">
                                <table class="table table-bordered">
                                    <thead class="thead-light fw-bold">
                                        <tr class="fw-bold fs-4 text-dark">
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($transactions)): ?>
                                            <?php foreach ($transactions as $transaction): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($transaction['type']); ?></td>
                                                    <td><?php echo htmlspecialchars($transaction['description']); ?></td>
                                                    <td>₱<?php echo number_format($transaction['amount'], 2); ?></td>
                                                    <td><?php echo htmlspecialchars(date("F j, Y", strtotime($transaction['date']))); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center">No transactions found for this organization.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
                    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

                    <script>

                        // PDF download function
                        document.getElementById('pdfButton').addEventListener('click', function () {
                            const element = document.getElementById('tableContent');
                            html2pdf()
                                .from(element)
                                .save('recent_transact.pdf'); // PDF named as "liquidation_report.pdf"
                        });
                    </script>
                    <!--Recent Transaction dashboard end-->

                    <?php
                    // Fetch the two nearest upcoming events
                    $query = "
                        SELECT e.title, e.event_start_date, e.event_end_date, e.event_venue, o.organization_name
                        FROM events e
                        JOIN organizations o ON e.organization_id = o.organization_id
                        WHERE e.event_start_date > CURDATE() 
                        ORDER BY e.event_start_date ASC
                        LIMIT 2
                    ";
                    $result = $conn->query($query);

                    // Check if the query was successful and fetch the events
                    if ($result->num_rows > 0) {
                        $events = $result->fetch_all(MYSQLI_ASSOC);
                    } else {
                        $events = [];
                    }
                    ?>

                    <!--Upcoming Events-->
                    <div>
                        <h3 class="welcome-message h5 fw-bold mb-4 mt-5">
                            <span class="text-warning fw-bold me-2">|</span>Upcoming Events
                        </h3>
                        <div class="mx-auto">
                            <!--event boxes-->
                            <div class="container mt-5">
                                <div class="row">
                                    <?php if (!empty($events)) : ?>
                                        <?php foreach ($events as $event) : ?>
                                            <div class="col-md-4">
                                                <div class="container-white">
                                                    <div class="event-box">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <h6 class="event-title"><?php echo htmlspecialchars($event['organization_name']); ?></h6>

                                                            <?php
                                                            // Get today's date
                                                            $today = new DateTime();
                                                            // Event's start date
                                                            $eventStartDate = new DateTime($event['event_start_date']);
                                                            // Calculate the difference between today's date and the event start date
                                                            $interval = $today->diff($eventStartDate);
                                                            // Format the number of days left
                                                            $daysLeft = $interval->format('%r%a'); // %r gives the sign (+ or -) and %a gives the total number of days
                                                            $daysLeftText = ($daysLeft >= 0) ? $daysLeft . " Days Left" : "Event Passed"; // Show if event has passed
                                                            ?>
                                                            <p class="event-duration text-sm"><?php echo $daysLeftText; ?></p>
                                                        </div>
                                                        <h5><?php echo htmlspecialchars($event['title']); ?></h5>
                                                        <div class="event-details">
                                                            <p class="event-date"><i class="fa-regular fa-calendar" aria-hidden="true"></i> 
                                                                <?php echo date("d M Y", strtotime($event['event_start_date'])) . ' - ' . date("d M Y", strtotime($event['event_end_date'])); ?>
                                                            </p>
                                                        </div>
                                                        <div class="text-end mt-auto">
                                                            <button class="btn btn-warning details-btn">Details</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <p>No upcoming events.</p>
                                    <?php endif; ?>

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
                                                    <button class="btn btn-outline-secondary dropdown-toggle"
                                                        type="button" id="monthDropdown" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fa-regular fa-calendar"></i> <span
                                                            id="selectedMonth">August</span>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="monthDropdown">
                                                        <!-- Month Options -->
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 0)">January</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 1)">February</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 2)">March</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 3)">April</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 4)">May</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 5)">June</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 6)">July</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 7)">August</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 8)">September</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 9)">October</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 10)">November</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectMonth(event, 11)">December</a></li>
                                                    </ul>
                                                </div>

                                                <!-- Year Dropdown -->
                                                <div class="dropdown">
                                                    <button class="btn btn-outline-secondary dropdown-toggle"
                                                        type="button" id="yearDropdown" data-bs-toggle="dropdown"
                                                        aria-expanded="false">
                                                        <i class="fa-regular fa-calendar"></i> <span
                                                            id="selectedYear">2024</span>
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="yearDropdown">
                                                        <!-- Year Options -->
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectYear(event, 2023)">2023</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectYear(event, 2024)">2024</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectYear(event, 2025)">2025</a></li>
                                                        <li><a class="dropdown-item" href="#"
                                                                onclick="selectYear(event, 2026)">2026</a></li>
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
                                            height: 265px;
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
                             <!--Upcoming Events end-->

                            <!--Activities start-->
                            <div>
                                <h3 class="welcome-message h5 fw-bold mb-4 mt-5">
                                    <span class="text-warning fw-bold me-2">|</span>Activities
                                </h3>

                                <?php 
                                    $sql = "SELECT * FROM events WHERE archived = 0 AND organization_id = $organization_id ORDER BY event_id DESC LIMIT 5";
                                    $result = $conn->query($sql);
                                ?>
                                    <div class="container">
                                        <div id="tableContent">
                                            <table class="table table-bordered">
                                                <thead class="thead-light fw-bold">
                                                    <tr>
                                                        <th rowspan=2>Title</th>
                                                        <th rowspan=2>Venue</th>
                                                        <th colspan=2 style="text-align: center;"> Date </th>
                                                        <th rowspan=2>Type</th>
                                                        <th rowspan=2>Status</th>
                                                        <th rowspan=2>Accomplished</th>
                                                    </tr>
                                                    <tr>
                                                        <th>Start</th>
                                                        <th>End</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    if ($result->num_rows > 0) {
                                                        while ($row = $result->fetch_assoc()) {
                                                            $checked = $row['accomplishment_status'] ? 'checked' : '';
                                                            $disabled = ($row['event_status'] !== 'Approved') ? 'disabled' : '';
                                                            echo "<tr>
                                                                    <td><a class='link-offset-2 link-underline link-underline-opacity-0' href='event_details.php?event_id={$row['event_id']}'>{$row['title']}</a></td>
                                                                    <td>{$row['event_venue']}</td>
                                                                    <td>" . date('F j, Y', strtotime($row['event_start_date'])) . "</td>
                                                                    <td>" . date('F j, Y', strtotime($row['event_end_date'])) . "</td>
                                                                    <td>{$row['event_type']}</td>
                                                                    <td>";
                                                            // Handle event status with badges
                                                            if ($row['event_status'] == 'Pending') {
                                                                echo "<span class='badge rounded-pill pending'>Pending</span>";
                                                            } elseif ($row['event_status'] == 'Approved') {
                                                                echo "<span class='badge rounded-pill approved'>Approved</span>";
                                                            } elseif ($row['event_status'] == 'Disapproved') {
                                                                echo "<span class='badge rounded-pill disapproved'>Disapproved</span>";
                                                            }
                                                            echo "</td>";
                                                            // Render accomplishment status
                                                            echo "<td>";
                                                            echo ($row['accomplishment_status'] == 1) 
                                                                ? "Accomplished" 
                                                                : "Not Accomplished";
                                                            echo "</td>";
                                                            echo "</tr>";
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

                                <style>
                                    .custom-btn {
                                        background-color: #00542F;
                                        border-color: #00542F;
                                        color: #ffffff;
                                        /* Text color */
                                    }

                                    .custom-btn:hover {
                                        background-color: #004026;
                                        /* Darker shade for hover */
                                        border-color: #004026;
                                    }
                                </style>

                                <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
                                <script
                                    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
                                <script
                                    src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
                                <script
                                    src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

                                <script>
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
                            <script
                                src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
                            <script
                                src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

                        </div>
                    </div>
                    <!--Activities end-->
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