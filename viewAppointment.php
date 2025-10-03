<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}

$userName  = $_SESSION['user_name'];
$userRole  = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "customer";
$userId    = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetgroomlist";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch appointments
if ($userRole == 'admin' || $userRole == 'staff') {
    $sql = "SELECT a.*, u.username, u.email FROM appointments a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC, appointment_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
}

$stmt->execute();
$result = $stmt->get_result();
$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments - VetGroom Hub</title>
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
        .appointments-section {
            padding: 80px 0 50px 0;
        }
        
        .table-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-top: 30px;
        }
        
        .status-pending { color: #ff9800; font-weight: 600; }
        .status-confirmed { color: #4caf50; font-weight: 600; }
        .status-completed { color: #2196f3; font-weight: 600; }
        .status-cancelled { color: #f44336; font-weight: 600; }
        
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
        
        .btn-edit-custom {
            background-color: #ffc107;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            color: #212529;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        
        .btn-edit-custom:hover {
            background-color: #e0a800;
            color: #212529;
        }
        
        @media (max-width: 768px) {
            .appointments-section {
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
            
            .btn-custom, .btn-secondary-custom, .btn-edit-custom {
                display: block;
                width: 100%;
                margin-bottom: 10px;
                margin-right: 0;
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
                                            <?php if ($userRole == 'customer'): ?>
                                                <a href="bookAppointment.php">Book Appointment</a>
                                                <a href="viewAppointment.php">View Appointments</a> 
                                            <?php elseif ($userRole == 'admin'): ?>
                                                <a href="viewDashboardAdmin.php">Dashboard</a>
                                                <a href="viewFeedBack.php">View Feedback</a>
                                                <a href="viewCustomer.php">View Customer</a>
                                                <a href="viewStaff.php">View Staff</a>
                                                <a href="viewAppointment.php">View Appointments</a> 
                                                <a href="viewSalesReport.php">View Sales Report</a> 
                                            <?php elseif ($userRole == 'staff'): ?>
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

    <!-- ✅ Appointments Section -->
    <main class="container appointments-section">
        <div class="page-header">
            <h2>Welcome, <?php echo htmlspecialchars($userName); ?></h2>
            <div class="mt-3">
                <a href="homepage.php" class="btn-secondary-custom">Home</a>
                <a href="bookAppointment.php" class="btn-custom">Book New Appointment</a>
                <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                    <a href="editAppointment.php" class="btn-edit-custom">
                        Edit Appointments
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-container">
            <h3><?php echo ($userRole == 'admin' || $userRole == 'staff') ? 'All Appointments' : 'My Appointments'; ?></h3>
            <?php if (count($appointments) === 0): ?>
                <p>No appointments found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                <?php endif; ?>
                                <th>Reference</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Instructions</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appt): ?>
                                <tr>
                                    <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                        <td><?php echo htmlspecialchars($appt['username']); ?></td>
                                        <td><?php echo htmlspecialchars($appt['email']); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo 'APT' . str_pad($appt['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($appt['service_type']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['appointment_time']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['address']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['special_instructions']); ?></td>
                                    <td class="status-<?php echo $appt['status']; ?>"><?php echo ucfirst($appt['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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