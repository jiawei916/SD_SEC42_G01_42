<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

// Determine user role
if (!$isLoggedIn) {
    $userRole = 'guest';
} else {
    $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'customer';
}

// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "vetgroomlist";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch feedback
$sql = "SELECT username, email, feedback, created_at FROM feedback ORDER BY created_at DESC";
$result = $conn->query($sql);
$feedbackData = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feedbackData[] = $row;
    }
}
$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>View Feedback - VetGroom Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- CSS assets -->
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
            0% { opacity: 0; }
            100% { opacity: 1; }
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
        .feedback-table th {
            background-color: #dc3545;
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
    <!-- ✅ Header Start -->
    <header>
        <div class="header-area header-transparent">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-xl-2 col-lg-2">
                            <div class="logo">
                                <a href="homepage.php"><img src="assets/img/logo/logo.png" alt="VetGroom Hub"></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a href="homepage.php">Home</a></li>
                                            <li><a href="aboutUs.php">About</a></li>
                                            <li><a href="viewService.php">Services</a></li>
                                            <li class="active"><a href="feedback.php">Feedback</a></li>
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
    <!-- ✅ Header End -->

    <!-- ✅ Feedback Section -->
    <main class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header text-center">
                <h2>Customer Feedback</h2>
                <p>Read what our customers are saying</p>
            </div>
            
            <?php if ($userRole === 'admin' || $userRole === 'staff'): ?>
                <table class="table feedback-table">
                    <thead>
                        <tr>
                            <?php if ($userRole === 'admin'): ?>
                                <th>Date</th><th>Name</th><th>Email</th><th>Feedback</th>
                            <?php else: ?>
                                <th>Name</th><th>Feedback</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($feedbackData as $fb): ?>
                        <tr>
                            <?php if ($userRole === 'admin'): ?>
                                <td><?php echo date("M j, Y g:i A", strtotime($fb['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($fb['username']); ?></td>
                                <td><?php echo htmlspecialchars($fb['email']); ?></td>
                                <td><?php echo htmlspecialchars($fb['feedback']); ?></td>
                            <?php else: ?>
                                <td><?php echo htmlspecialchars($fb['username']); ?></td>
                                <td><?php echo htmlspecialchars($fb['feedback']); ?></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-danger">Access restricted. Please sign in as staff or admin.</p>
            <?php endif; ?>
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
