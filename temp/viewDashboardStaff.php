<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";

// Determine user role based on session or default to guest
if (!$isLoggedIn) {
    $userRole = 'guest';
} else {
    // Check if role is stored in session (you should set this during login)
    if (isset($_SESSION['user_role'])) {
        $userRole = $_SESSION['user_role'];
    } else {
        // Default role for logged-in users without a specific role
        $userRole = 'customer';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Staff Dashboard - VetGroom Hub</title>
    <meta name="description" content="Staff Dashboard for VetGroom Hub">
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
        
        /* Dashboard container styling */
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 100px auto;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 20px 0;
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .dashboard-header h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .dashboard-header p {
            color: #666;
            font-size: 16px;
        }
        
        /* Cards grid */
        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .card h2 {
            margin: 0;
            font-size: 2.2em;
        }

        .card p {
            margin: 10px 0 0;
            color: #666;
            font-weight: 500;
        }

        /* Navigation buttons */
        .nav-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .nav-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 16px;
            font-weight: 600;
        }

        .nav-btn:hover {
            background: #ff707f;
        }

        /* Back button */
        .back-btn {
            text-align: center;
            margin-top: 30px;
        }

        .back-btn a {
            display: inline-block;
            background: #f8f9fa;
            color: #333;
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: background 0.3s;
            font-weight: 600;
        }

        .back-btn a:hover {
            background: #e9ecef;
        }
        
        /* Profile dropdown */
        .profile-dropdown {
            position: absolute;
            top: 15px;
            right: 20px;
            display: flex;
            align-items: center;
            cursor: pointer;
            background-color: #3aa9e4;
            padding: 6px 10px;
            border-radius: 6px;
            box-shadow: 0px 2px 6px rgba(0,0,0,0.2);
        }
        
        .profile-icon {
            font-size: 26px;
            margin-right: 8px;
        }
        
        .profile-name {
            font-size: 16px;
            font-weight: bold;
            color: white;
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
        

        /* Show dropdown on hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }
                
        .dropdown-content a {
            display: block;
            padding: 8px 12px;
            font-size: 14px;
            text-decoration: none;
            color: #333;
            transition: background 0.2s ease;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Use existing .header-btn styling */
        .dropdown > .header-btn {
            display: inline-block;
            text-align: center;
        }
        .profile-dropdown:hover .dropdown-content {
            display: block;
        }
        
        /* Navigation styling */
        .main-menu ul {
            display: flex;
            gap: 20px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .main-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .main-menu a:hover {
            color: #f8f9fa;
        }

        /* Header adjustments for better spacing */
        .header-area {
            position: relative;
        }
        
        .main-header {
            padding: 10px 0;
        }
        
        .menu-main {
            gap: 30px; /* Add space between navigation and profile dropdown */
        }
        
        /* Profile dropdown positioning adjustments */
        .profile-dropdown {
            position: relative;
            top: 0;
            right: 0;
            margin-left: auto; /* Push to the far right */
        }
        
        /* Navigation menu adjustments */
        .main-menu {
            margin-right: 20px; /* Add space between menu and profile */
        }
        
        .main-menu ul {
            display: flex;
            gap: 25px; /* Increase spacing between menu items */
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
        }
        
        .main-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .main-menu a:hover {
            color: #f8f9fa;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        /* Ensure header container has proper spacing */
        .container-fluid {
            padding: 0 20px;
        }
        
        /* Adjust dashboard container margin to account for fixed header */
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 80px auto; /* Reduced from 100px to account for header */
        }
    </style>
</head>

<body>
    <header>
        <!-- Header Start -->
        <div class="header-area header-transparent">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <!-- Logo -->
                        <div class="col-xl-2 col-lg-2 col-md-1">
                            <div class="logo">
                                <a href="homepage.php"><img src="assets/img/logo/logo.png" alt="VetGroom Hub Logo"></a>
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
                                            <li><a href="contact.php">Contact</a></li>
                                        </ul>
                                    </nav>
                                </div><div class="header-right-btn f-right d-none d-lg-block ml-30">
    <div class="dropdown">
        <a href="#" class="header-btn">
            <?php echo $isLoggedIn ? "Welcome, " . htmlspecialchars($userName) : "Welcome, Guest"; ?> ▼
        </a>
        <div class="dropdown-content">
            <?php if ($isLoggedIn): ?>
                <a href="profile.php">Profile</a>
                <a href="signOut.php">Sign Out</a>
            <?php elseif ($userRole == 'admin'): ?>
                <a href="profile.php">Profile</a>
                <a href="viewDashboardAdmin.php">Dashboard</a>
                <a href="viewFeedBack.php">View Feedback</a>
                <a href="signOut.php">Sign Out</a>                
            <?php elseif ($userRole == 'staff'): ?>
                <a href="profile.php">Profile</a>
                <a href="viewDashboardStaff.php">Dashboard</a>
                <a href="viewFeedBack.php">View Feedback</a>
                <a href="signOut.php">Sign Out</a> 
            <?php else: ?>
                <a href="signIn.php">Sign In</a>
                <a href="registerGuest.php">Register</a>
            <?php endif ?>
        </div>
    </div>
</div>
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>

    <!-- Main dashboard content -->
    <main class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <h2>Welcome, Staff Member!</h2>
                <p>Manage appointments, services, and feedback from your dashboard</p>
            </div>

            <!-- Dashboard Stats -->
            <div class="cards">
                <div class="card">
                    <h2 id="totalAppointments">25</h2>
                    <p>Total Appointments</p>
                </div>
                <div class="card">
                    <h2 id="totalServices">12</h2>
                    <p>Total Services</p>
                </div>
                <div class="card">
                    <h2 id="totalFeedback">8</h2>
                    <p>Total Feedback</p>
                </div>
            </div>

            <!-- Quick Access Buttons -->
            <div class="nav-buttons">
                <button class="nav-btn" onclick="location.href='appointments.html'">Manage Appointments</button>
                <button class="nav-btn" onclick="location.href='services.html'">Manage Services</button>
                <button class="nav-btn" onclick="location.href='viewFeedBack.php'">View Feedback</button>
            </div>

            <!-- Back Button -->
            <div class="back-btn">
                <a href="homepage.php">⬅ Back to Homepage</a>
            </div>
        </div>
    </main>

</body>
</html>