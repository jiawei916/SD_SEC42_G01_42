<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

if (!$isLoggedIn) {
    $userRole = 'guest';
} else {
    $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'customer';
}

// Restrict access to admin/staff
if ($userRole !== 'admin' && $userRole !== 'staff') {
    echo "<script>alert('Access Denied! Only Admin or Staff can view sales reports.'); window.location='index.php';</script>";
    exit();
}

// Database connection
require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Query 1: Total Sales by Month (using service_id)
$sqlMonthly = "
SELECT 
    DATE_FORMAT(a.appointment_date, '%Y-%m') AS month,
    COUNT(a.id) AS total_appointments,
    SUM(s.price) AS total_sales
FROM appointments a
JOIN services s ON a.service_id = s.id
WHERE a.status = 'completed'
GROUP BY month
ORDER BY month DESC;
";

$monthlyResult = $conn->query($sqlMonthly);
$monthlyData = [];
if ($monthlyResult && $monthlyResult->num_rows > 0) {
    while ($row = $monthlyResult->fetch_assoc()) {
        $monthlyData[] = $row;
    }
}

// ✅ Query 2: Top Services Sold (using service_id)
$sqlTopServices = "
SELECT 
    s.name AS service_name,
    COUNT(a.id) AS total_sold,
    SUM(s.price) AS total_revenue
FROM appointments a
JOIN services s ON a.service_id = s.id
WHERE a.status = 'completed'
GROUP BY s.id
ORDER BY total_revenue DESC;
";

$serviceResult = $conn->query($sqlTopServices);
$serviceData = [];
if ($serviceResult && $serviceResult->num_rows > 0) {
    while ($row = $serviceResult->fetch_assoc()) {
        $serviceData[] = $row;
    }
}

$conn->close();
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sales Report - VetGroom Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body {
            opacity: 0;
            animation: fadeInAnimation ease 1s;
            animation-fill-mode: forwards;
            background-image: url('assets/img/hero/hero2.png'); 
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
        }
        @keyframes fadeInAnimation {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 80px auto;
        }
        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            padding: 30px;
        }
        th {
            background-color: #4a90e2;
            color: white;
        }

                /* Dropdown container */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Use existing .header-btn styling */
        .dropdown > .header-btn {
            display: inline-block;
            text-align: center;
        }

        /* Dropdown box */
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: #fff;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border-radius: 6px;
            z-index: 1000;
        }

        /* Dropdown links */
        .dropdown-content a {
            color: #333;
            padding: 10px 14px;
            text-decoration: none;
            display: block;
            transition: background 0.2s ease;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Show dropdown on hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>

<body>
    <!-- ✅ Header -->
    <header>
        <div class="header-area header-transparent">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-xl-2 col-lg-2">
                            <div class="logo">
                                <a href="index.php"><img src="assets/img/logo/logo.png" alt="VetGroom Hub"></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a href="index.php">Home</a></li>
                                            <li><a href="aboutUs.php">About</a></li>
                                            <li><a href="viewService.php">Services</a></li>
                                            <li><a href="viewFeedBack.php">Feedback</a></li>
                                            <li><a href="contact.php">Contact</a></li>
                                        </ul>
                                    </nav>
                                </div>
                                <div class="header-right-btn f-right d-none d-lg-block ml-30">
                                    <div class="dropdown">
                                        <a href="#" class="header-btn">
                                            Welcome, <?php echo htmlspecialchars($userName); ?> ▼
                                        </a>
                                        <div class="dropdown-content">
                                            <?php if (isset($_SESSION['user_role'])): ?>
                                                <a href="profile.php">Profile</a>
                                            <?php endif; ?>
                                            <?php if ($_SESSION['user_role'] == 'customer'): ?>
                                                <a href="bookAppointment.php">Book Appointment</a>
                                                <a href="viewAppointment.php">View Appointments</a> 
                                            <?php elseif ($_SESSION['user_role'] == 'admin'): ?>
                                                <a href="viewDashboardAdmin.php">Dashboard</a>
                                                <a href="viewFeedBack.php">View Feedback</a>
                                                <a href="viewCustomer.php">View Customer</a>
                                                <a href="viewStaff.php">View Staff</a>
                                            <?php elseif ($_SESSION['user_role'] == 'staff'): ?>
                                                <a href="viewDashboardStaff.php">Dashboard</a>
                                                <a href="viewFeedBack.php">View Feedback</a>
                                                <a href="viewCustomer.php">View Customer</a>
                                            <?php endif; ?>
                                            <?php if (isset($_SESSION['user_role'])): ?>
                                                <a href="signOut.php">Sign Out</a>
                                            <?php else: ?>
                                                <a href="signIn.php">Sign In</a>
                                                <a href="registerGuest.php">Register</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>   
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ✅ Sales Report Section -->
    <main class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header text-center">
                <h2>Sales Report</h2>
                <p>Overview of monthly and service-based sales</p>
            </div>

            <!-- Monthly Sales Table -->
            <h4 class="mt-4 mb-3">📅 Monthly Sales Summary</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Month</th>
                        <th>Total Appointments</th>
                        <th>Total Sales (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($monthlyData)): ?>
                        <?php foreach ($monthlyData as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['month']); ?></td>
                                <td><?php echo htmlspecialchars($m['total_appointments']); ?></td>
                                <td>RM <?php echo number_format($m['total_sales'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center">No completed sales yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Service Sales Table -->
            <h4 class="mt-5 mb-3">🐶 Sales by Service</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Service Name</th>
                        <th>Times Sold</th>
                        <th>Total Revenue (RM)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($serviceData)): ?>
                        <?php foreach ($serviceData as $s): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['service_name']); ?></td>
                                <td><?php echo htmlspecialchars($s['total_sold']); ?></td>
                                <td>RM <?php echo number_format($s['total_revenue'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="text-center">No service sales recorded.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- ✅ Footer -->
    <footer>
        <div class="footer-area footer-padding">
            <div class="container">
                <p class="text-center">&copy; <?php echo date("Y"); ?> VetGroom Hub. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/main.js"></script>
</body>
</html>
