<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}

$userName = $_SESSION['user_name'];
$userRole = $_SESSION['user_role'];
$userId = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetgroomlist";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user's receipts
if ($userRole == 'admin') {
    $sql = "SELECT a.*, u.username, u.first_name, u.last_name, s.price 
            FROM appointments a 
            LEFT JOIN users u ON a.user_id = u.id 
            LEFT JOIN services s ON a.service_type = s.name 
            WHERE a.status IN ('confirmed', 'completed') 
            ORDER BY a.created_at DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT a.*, s.price 
            FROM appointments a 
            LEFT JOIN services s ON a.service_type = s.name 
            WHERE a.user_id = ? AND a.status IN ('confirmed', 'completed') 
            ORDER BY a.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
}

$stmt->execute();
$result = $stmt->get_result();
$receipts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $receipts[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>My Receipts - VetGroom Hub</title>
    <meta name="description" content="View your receipts">
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
        
        /* Header adjustments */
        .header-area {
            position: relative;
        }
        
        .main-header {
            padding: 20px 0;
        }
        
        .menu-main {
            gap: 0px;
        }
        
        /* Profile dropdown positioning adjustments */
        .profile-dropdown {
            position: relative;
            top: 0;
            right: 0;
            margin-left: auto;
        }
        
        /* Main content styling */
        .receipts-section {
            padding: 80px 0 50px 0;
        }
        
        .table-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .page-header {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .btn-custom {
            background-color: #dc3545;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        
        .btn-custom:hover {
            background-color: #ff707f;
            color: white;
        }
        
        .btn-secondary-custom {
            background-color: #6c757d;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        
        .btn-secondary-custom:hover {
            background-color: #5a6268;
            color: white;
        }
        
        .btn-view {
            background-color: #dc3545;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
            font-size: 14px;
            margin-right: 5px;
        }
        
        .btn-view:hover {
            background-color: #ff707f;
            color: white;
        }
        
        .btn-print {
            background-color: #17a2b8;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background-color 0.3s;
            font-size: 14px;
        }
        
        .btn-print:hover {
            background-color: #138496;
            color: white;
        }
        
        .badge-paid {
            background-color: #28a745;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }
        
        .receipt-number {
            font-weight: 600;
            color: #dc3545;
        }
        
        .no-receipts {
            text-align: center;
            padding: 60px 40px;
            color: #6c757d;
        }
        
        .no-receipts i {
            font-size: 64px;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .no-receipts h3 {
            color: #6c757d;
            margin-bottom: 15px;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .receipts-section {
                padding: 60px 0 30px 0;
            }
            
            .table-container {
                padding: 20px;
                margin-top: 20px;
            }
            
            .page-header {
                padding: 15px;
                text-align: center;
            }
            
            .btn-custom, .btn-secondary-custom {
                display: block;
                width: 100%;
                margin-bottom: 10px;
                margin-right: 0;
            }
            
            .action-buttons {
                width: 100%;
                justify-content: center;
            }
            
            .table-responsive {
                font-size: 14px;
            }
            
            .btn-view, .btn-print {
                width: 100%;
                margin-bottom: 5px;
                margin-right: 0;
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .table th, .table td {
                padding: 10px 8px;
                font-size: 13px;
            }
            
            .btn-view, .btn-print {
                font-size: 12px;
                padding: 6px 10px;
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
                                            <a href="profile.php">Profile</a>
                                            <?php if ($userRole == 'admin'): ?>
                                                <a href="viewDashboardAdmin.php">Dashboard</a>
                                                <a href="viewFeedBack.php">View Feedback</a>
                                                <a href="viewCustomer.php">View Customer</a>
                                                <a href="viewStaff.php">View Staff</a>
                                            <?php elseif ($userRole == 'staff'): ?>
                                                <a href="viewDashboardStaff.php">Dashboard</a>
                                                <a href="viewFeedBack.php">View Feedback</a>
                                                <a href="viewCustomer.php">View Customer</a>
                                            <?php endif; ?>
                                            <a href="viewAppointment.php">My Appointments</a>
                                            <?php if ($userRole == 'customer'): ?>
                                                <a href="viewReceipts.php">My Receipts</a>
                                            <?php endif; ?>
                                            <a href="signOut.php">Sign Out</a>
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
    
    <!-- ✅ Receipts Section -->
    <main class="container receipts-section">
        <div class="page-header">
            <h2>Welcome, <?php echo htmlspecialchars($userName); ?></h2>
            <div class="mt-3">
                <a href="homepage.php" class="btn-secondary-custom">Home</a>
                <a href="viewAppointment.php" class="btn-custom">Back to Appointments</a>
            </div>
        </div>

        <div class="table-container">
            <h3><?php echo ($userRole == 'admin') ? 'All Receipts' : 'My Receipts'; ?></h3>
            <p class="text-muted mb-4"><?php echo count($receipts); ?> receipt(s) found</p>

            <?php if (count($receipts) === 0): ?>
                <!-- No Receipts Message -->
                <div class="no-receipts">
                    <i class="fas fa-receipt"></i>
                    <h3>No Receipts Found</h3>
                    <p>You don't have any receipts yet. Receipts are generated automatically after your appointments are confirmed and completed.</p>
                    <a href="bookAppointment.php" class="btn-custom" style="margin-top: 20px;">
                        <i class="fas fa-calendar-plus"></i> Book an Appointment
                    </a>
                </div>
            <?php else: ?>
                <!-- Receipts Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <?php if ($userRole == 'admin'): ?>
                                    <th>Customer Name</th>
                                <?php endif; ?>
                                <th>Receipt Number</th>
                                <th>Service</th>
                                <th>Appointment Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($receipts as $receipt): ?>
                                <tr>
                                    <?php if ($userRole == 'admin'): ?>
                                        <td>
                                            <strong><?php echo htmlspecialchars($receipt['first_name'] . ' ' . $receipt['last_name']); ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo htmlspecialchars($receipt['username']); ?></small>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <span class="receipt-number">RCP<?php echo str_pad($receipt['id'], 6, '0', STR_PAD_LEFT); ?></span>
                                    </td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($receipt['service_type']); ?></strong>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo date('g:i A', strtotime($receipt['appointment_time'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php echo date('M j, Y', strtotime($receipt['appointment_date'])); ?>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo date('D', strtotime($receipt['appointment_date'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong>$<?php echo number_format($receipt['price'], 2); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge-paid">PAID</span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a href="viewReceipt.php?id=<?php echo $receipt['id']; ?>" class="btn-view">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="viewReceipt.php?id=<?php echo $receipt['id']; ?>&print=1" class="btn-print">
                                                <i class="fas fa-print"></i> Print
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div class="mt-4 p-3" style="background: #f8f9fa; border-radius: 8px;">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h4 class="text-primary"><?php echo count($receipts); ?></h4>
                            <p class="text-muted mb-0">Total Receipts</p>
                        </div>
                        <div class="col-md-4">
                            <h4 class="text-success">
                                $<?php 
                                    $totalAmount = array_sum(array_column($receipts, 'price'));
                                    echo number_format($totalAmount, 2); 
                                ?>
                            </h4>
                            <p class="text-muted mb-0">Total Amount</p>
                        </div>
                        <div class="col-md-4">
                            <h4 class="text-info">
                                $<?php 
                                    $averageAmount = count($receipts) > 0 ? $totalAmount / count($receipts) : 0;
                                    echo number_format($averageAmount, 2); 
                                ?>
                            </h4>
                            <p class="text-muted mb-0">Average per Receipt</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
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
</body>
</html>