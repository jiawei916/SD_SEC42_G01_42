<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}

$userName  = $_SESSION['user_name'];
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : "Not provided";
$userRole  = isset($_SESSION['role']) ? $_SESSION['role'] : "customer";
$isLoggedIn = true;
?>
<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Profile - VetGroom Hub</title>
    <meta name="description" content="Your user profile at VetGroom Hub">
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
        
        /* Profile section styles */
        .profile-section {
            background: #fff3e6; /* light orange background */
            padding: 60px 30px;
            border-radius: 12px;
            border: 1px solid #f5c48c;
            margin-top: 50px;
        }
        .profile-section h2 {
            color: #d35400;
            font-weight: 700;
            margin-bottom: 30px;
        }
        .profile-section ul {
            list-style: none;
            padding: 0;
        }
        .profile-section ul li {
            margin: 8px 0;
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
                                            <li><a href="feedback.php">Feedback</a></li>
                                            <li class="active"><a href="profile.php">Profile</a></li>
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

    <!-- ✅ Profile Section -->
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-section">
                    <h2 class="text-center">User Profile</h2>

                    <p><strong>Username:</strong> <?php echo htmlspecialchars($userName); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($userEmail); ?></p>
                    <p><strong>Role:</strong> <?php echo ucfirst(htmlspecialchars($userRole)); ?></p>

                    <hr>

<?php if ($userRole == 'admin'): ?>
    <h4>Admin Tools</h4>
    <div class="d-grid gap-2">
        <a href="changePassword.html?user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-warning">Change Password</a>
        <a href="viewDashboardAdmin.php" class="btn btn-primary">Admin Dashboard</a>
        <a href="viewFeedBack.php" class="btn btn-info">View Feedback</a>
    </div>

<?php elseif ($userRole == 'staff'): ?>
    <h4>Staff Tools</h4>
    <div class="d-grid gap-2">
        <a href="changePassword.html?user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-warning">Change Password</a>
        <a href="viewDashboardStaff.php" class="btn btn-primary">Staff Dashboard</a>
        <a href="viewFeedBack.php" class="btn btn-info">View Feedback</a>
    </div>

<?php else: ?>
    <h4>Customer Options</h4>
    <div class="d-grid gap-2">
        <a href="changePassword.html?user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-warning">Change Password</a>
        <a href="services.php" class="btn btn-success">View Services</a>
        <a href="feedback.php" class="btn btn-info">Submit Feedback</a>
        <a href="index.php" class="btn btn-primary">Book Appointment</a>
    </div>
<?php endif; ?>

                    <div class="text-center mt-4">
                        <a href="signOut.php" class="btn btn-danger">Sign Out</a>
                    </div>
                </div>
            </div>
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
                    <!-- Add more footer columns if needed -->
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