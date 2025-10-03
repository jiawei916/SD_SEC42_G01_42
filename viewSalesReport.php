<?php
session_start();

// Check if user is admin or staff
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'staff')) {
    header("Location: signIn.php");
    exit();
}

$userName = $_SESSION['user_name'];
$userRole = $_SESSION['user_role'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetgroomlist";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default date range
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01'); // First day of current month
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t'); // Last day of current month
$period = isset($_GET['period']) ? $_GET['period'] : 'month';

// Adjust dates based on period for admin
if ($userRole == 'admin' && $period != 'custom') {
    switch ($period) {
        case 'today':
            $start_date = date('Y-m-d');
            $end_date = date('Y-m-d');
            break;
        case 'week':
            $start_date = date('Y-m-d', strtotime('monday this week'));
            $end_date = date('Y-m-d', strtotime('sunday this week'));
            break;
        case 'month':
            $start_date = date('Y-m-01');
            $end_date = date('Y-m-t');
            break;
    }
}

// Fetch sales data
$total_earnings = 0;
$total_appointments = 0;
$service_breakdown = [];

// Get total earnings and appointment count
$sql_total = "SELECT COUNT(*) as total_appointments, 
                     COALESCE(SUM(s.price), 0) as total_earnings
              FROM appointments a 
              LEFT JOIN services s ON a.service_type = s.name 
              WHERE a.status IN ('confirmed', 'completed') 
              AND a.appointment_date BETWEEN ? AND ?";
              
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("ss", $start_date, $end_date);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_data = $result_total->fetch_assoc();
$stmt_total->close();

$total_appointments = $total_data['total_appointments'];
$total_earnings = $total_data['total_earnings'];

// Get service breakdown
$sql_breakdown = "SELECT s.name as service_name, 
                         COUNT(a.id) as appointment_count,
                         COALESCE(SUM(s.price), 0) as total_earnings
                  FROM appointments a 
                  LEFT JOIN services s ON a.service_type = s.name 
                  WHERE a.status IN ('confirmed', 'completed') 
                  AND a.appointment_date BETWEEN ? AND ?
                  GROUP BY s.name 
                  ORDER BY total_earnings DESC";
                  
$stmt_breakdown = $conn->prepare($sql_breakdown);
$stmt_breakdown->bind_param("ss", $start_date, $end_date);
$stmt_breakdown->execute();
$result_breakdown = $stmt_breakdown->get_result();

while ($row = $result_breakdown->fetch_assoc()) {
    $service_breakdown[] = $row;
}
$stmt_breakdown->close();

$conn->close();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Sales Report - VetGroom Hub</title>
    <meta name="description" content="View sales and appointment reports">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
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
            0% { opacity: 0; }
            100% { opacity: 1; }
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
        
        /* Sales report container */
        .sales-report-container {
            max-width: 1200px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .sales-report-container h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }
        
        /* Filter section */
        .filter-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }
        
        .form-control {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #dc3545;
            outline: none;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
        }
        
        .btn-generate {
            background-color: #dc3545;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn-generate:hover {
            background-color: #ff707f;
        }
        
        .btn-export {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            transition: background-color 0.3s;
        }
        
        .btn-export:hover {
            background-color: #218838;
            color: white;
        }
        
        .btn-print {
            background-color: #17a2b8;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }
        
        .btn-print:hover {
            background-color: #138496;
            color: white;
        }
        
        /* Summary cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .summary-card h3 {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 10px;
            opacity: 0.9;
        }
        
        .summary-card .value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .summary-card .label {
            font-size: 14px;
            opacity: 0.8;
        }
        
        /* Table styling */
        .table-container {
            background: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .table th {
            background-color: #343a40;
            color: white;
            font-weight: 600;
            border: none;
        }
        
        .table td {
            vertical-align: middle;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .no-data i {
            font-size: 48px;
            margin-bottom: 15px;
            color: #dee2e6;
        }
        
        /* Header adjustments */
        .header-area {
            position: relative;
        }
        
        .main-header {
            padding: 10px 0;
        }
        
        .profile-dropdown {
            position: relative;
            top: 0;
            right: 0;
            margin-left: auto;
        }
        
        .container-fluid {
            padding: 0 20px;
        }
        
        @media (max-width: 768px) {
            .sales-report-container {
                margin: 60px 15px;
                padding: 20px;
            }
            
            .summary-cards {
                grid-template-columns: 1fr;
            }
            
            .btn-export, .btn-print {
                width: 100%;
                margin-bottom: 10px;
                margin-right: 0;
            }
            
            .filter-section .row > div {
                margin-bottom: 15px;
            }
        }
        
        @media print {
            .header-area, .filter-section, .btn-export, .btn-print {
                display: none;
            }
            
            body {
                background: white;
            }
            
            .sales-report-container {
                box-shadow: none;
                margin: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- ✅ Header Start -->
    <header>
        <div class="header-area header-transparent">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <!-- Logo -->
                        <div class="col-xl-2 col-lg-2 col-md-1">
                            <div class="logo">
                                <a href="homepage.php"><img src="assets/img/logo/logo.png" alt="VetGroom Hub"></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <!-- Main-menu -->
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav> 
                                        <ul id="navigation">
                                            <li><a href="homepage.php">Home</a></li>
                                            <li><a href="aboutUs.php">About</a></li>
                                            <li><a href="viewService.php">Services</a></li>
                                            <li><a href="feedback.php">Feedback</a></li>
                                            <li><a href="contact.php">Contact</a></li>
                                        </ul>
                                    </nav>
                                </div>
                                <!-- Dropdown -->
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
    <a href="viewReceipt.php">View Receipt</a> 
<?php elseif ($_SESSION['user_role'] == 'admin'): ?>
    <a href="viewDashboardAdmin.php">Dashboard</a>
    <a href="viewFeedBack.php">View Feedback</a>
    <a href="viewCustomer.php">View Customer</a>
    <a href="viewStaff.php">View Staff</a>
    <a href="viewAppointment.php">View Appointments</a> 
    <a href="viewSalesReport.php">View Sales Report</a> 
    <a href="viewReceipt.php">View Receipt</a> 
<?php elseif ($_SESSION['user_role'] == 'staff'): ?>
    <a href="viewDashboardStaff.php">Dashboard</a>
    <a href="viewFeedBack.php">View Feedback</a>
    <a href="viewCustomer.php">View Customer</a>
    <a href="viewAppointment.php">View Appointments</a> 
    <a href="viewSalesReport.php">View Sales Report</a> 
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
                        <!-- Mobile Menu -->
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- ✅ Header End -->
    
    <div class="sales-report-container">
        <h2>Sales Report</h2>
        
        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" action="viewSalesReport.php">
                <div class="row">
                    <?php if ($userRole == 'admin'): ?>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="period">Report Period</label>
                                <select class="form-control" id="period" name="period" onchange="this.form.submit()">
                                    <option value="today" <?php echo $period == 'today' ? 'selected' : ''; ?>>Today</option>
                                    <option value="week" <?php echo $period == 'week' ? 'selected' : ''; ?>>This Week</option>
                                    <option value="month" <?php echo $period == 'month' ? 'selected' : ''; ?>>This Month</option>
                                    <option value="custom" <?php echo $period == 'custom' ? 'selected' : ''; ?>>Custom Range</option>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="start_date">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                   value="<?php echo htmlspecialchars($start_date); ?>" 
                                   <?php echo ($userRole == 'admin' && $period != 'custom') ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="end_date">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                   value="<?php echo htmlspecialchars($end_date); ?>" 
                                   <?php echo ($userRole == 'admin' && $period != 'custom') ? 'readonly' : ''; ?>>
                        </div>
                    </div>
                    
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn-generate" style="width: 100%;">Generate</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Export/Print Buttons -->
        <div class="mb-4 text-right">
            <button onclick="exportToExcel()" class="btn-export">
                <i class="fas fa-download"></i> Export to Excel
            </button>
            <button onclick="window.print()" class="btn-print">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>

        <?php if ($total_appointments > 0): ?>
            <!-- Summary Cards -->
            <div class="summary-cards">
                <div class="summary-card">
                    <h3>TOTAL EARNINGS</h3>
                    <div class="value">$<?php echo number_format($total_earnings, 2); ?></div>
                    <div class="label">From <?php echo $total_appointments; ?> appointments</div>
                </div>
                
                <div class="summary-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h3>TOTAL APPOINTMENTS</h3>
                    <div class="value"><?php echo $total_appointments; ?></div>
                    <div class="label">Confirmed & Completed</div>
                </div>
                
                <div class="summary-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <h3>AVERAGE PER APPOINTMENT</h3>
                    <div class="value">$<?php echo $total_appointments > 0 ? number_format($total_earnings / $total_appointments, 2) : '0.00'; ?></div>
                    <div class="label">Average revenue</div>
                </div>
                
                <div class="summary-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <h3>SERVICE TYPES</h3>
                    <div class="value"><?php echo count($service_breakdown); ?></div>
                    <div class="label">Different services</div>
                </div>
            </div>

            <!-- Service Breakdown Table -->
            <div class="table-container">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Number of Appointments</th>
                            <th>Total Earnings</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($service_breakdown as $service): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                <td><?php echo $service['appointment_count']; ?></td>
                                <td>$<?php echo number_format($service['total_earnings'], 2); ?></td>
                                <td>
                                    <?php if ($total_earnings > 0): ?>
                                        <?php echo number_format(($service['total_earnings'] / $total_earnings) * 100, 1); ?>%
                                    <?php else: ?>
                                        0%
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #f8f9fa; font-weight: 600;">
                            <td>Total</td>
                            <td><?php echo $total_appointments; ?></td>
                            <td>$<?php echo number_format($total_earnings, 2); ?></td>
                            <td>100%</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <!-- No Data Message -->
            <div class="no-data">
                <i class="fas fa-chart-bar"></i>
                <h3>No Data Found</h3>
                <p>No appointments found for the selected period (<?php echo htmlspecialchars($start_date); ?> to <?php echo htmlspecialchars($end_date); ?>).</p>
                <p>Please try adjusting your date range.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- ✅ Footer -->
    <footer>
        <div class="footer-area footer-padding">
            <div class="container">
                <div class="row d-flex justify-content-between">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-logo mb-25">
                                <a href="homepage.php"><img src="assets/img/logo/logo2.png" alt="VetGroom Hub"></a>
                            </div>
                            <div class="footer-tittle">
                                <p>Professional grooming and veterinary services for your beloved pets.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom-area">
            <div class="container">
                <div class="footer-border">
                    <div class="row d-flex align-items-center">
                        <div class="col-xl-12">
                            <div class="footer-copy-right text-center">
                                <p>&copy; <?php echo date("Y"); ?> VetGroom Hub. All Rights Reserved.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery.slicknav.min.js"></script>
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/slick.min.js"></script>
    <script src="./assets/js/main.js"></script>
    
    <script>
    function exportToExcel() {
        // Create a simple CSV export
        let csv = 'Service Name,Appointments,Total Earnings,Percentage\\n';
        
        <?php foreach ($service_breakdown as $service): ?>
            csv += '<?php echo addslashes($service['service_name']); ?>,';
            csv += '<?php echo $service['appointment_count']; ?>,';
            csv += '$<?php echo number_format($service['total_earnings'], 2); ?>,';
            csv += '<?php echo $total_earnings > 0 ? number_format(($service['total_earnings'] / $total_earnings) * 100, 1) : 0; ?>%\\n';
        <?php endforeach; ?>
        
        csv += 'Total,<?php echo $total_appointments; ?>,$<?php echo number_format($total_earnings, 2); ?>,100%';
        
        // Create and download the file
        const blob = new Blob([csv], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.setAttribute('hidden', '');
        a.setAttribute('href', url);
        a.setAttribute('download', 'sales_report_<?php echo date('Y-m-d'); ?>.csv');
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
    
    // Auto-submit form when period changes for admin
    document.getElementById('period')?.addEventListener('change', function() {
        if (this.value !== 'custom') {
            this.form.submit();
        }
    });
    </script>
</body>
</html>