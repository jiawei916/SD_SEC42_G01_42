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

// Fetch feedback - Updated to match your database structure
$sql = "SELECT name, email, subject, rating, message FROM feedback ORDER BY id DESC";
$result = $conn->query($sql);
$feedbackData = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feedbackData[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>View Feedback - VetGroom Hub</title>
    <meta name="description" content="View Customer Feedback - VetGroom Hub">
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
            max-width: 1400px;
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

        /* Feedback Stats Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid #dc3545;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .stats-card h3 {
            margin: 0;
            font-size: 2.2em;
            color: #dc3545;
            font-weight: 700;
        }

        .stats-card p {
            margin: 10px 0 0;
            color: #666;
            font-weight: 500;
        }

        /* Table styling */
        .feedback-table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-top: 20px;
        }

        .feedback-table {
            width: 100%;
            border-collapse: collapse;
        }

        .feedback-table th {
            background: #dc3545;
            color: white;
            padding: 18px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .feedback-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: top;
        }

        .feedback-table tr:hover {
            background-color: #f8f9fa;
        }

        .feedback-table tr:last-child td {
            border-bottom: none;
        }

        /* Role notice styling */
        .role-notice {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            border-left: 4px solid;
        }

        .admin-notice { 
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); 
            color: #155724; 
            border-left-color: #28a745;
        }
        .staff-notice { 
            background: linear-gradient(135deg, #cce5ff 0%, #b8daff 100%); 
            color: #004085; 
            border-left-color: #007bff;
        }
        .customer-notice { 
            background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%); 
            color: #856404; 
            border-left-color: #ffc107;
        }
        .guest-notice { 
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%); 
            color: #721c24; 
            border-left-color: #dc3545;
        }

        /* Access denied styling */
        .no-access {
            text-align: center;
            padding: 60px 40px;
            color: #dc3545;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .no-access h3 {
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: 700;
        }

        .no-access p {
            font-size: 16px;
            color: #666;
        }

        /* Rating badges */
        .rating-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .rating-satisfied { 
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%); 
            color: white; 
        }
        .rating-good { 
            background: linear-gradient(135deg, #007bff 0%, #6610f2 100%); 
            color: white; 
        }
        .rating-okay { 
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); 
            color: white; 
        }
        .rating-bad { 
            background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); 
            color: white; 
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
            padding: 15px 30px;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s;
            font-weight: 600;
            font-size: 16px;
        }

        .back-btn a:hover {
            background: #dc3545;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }

        /* Dropdown styling to match admin dashboard */
        .dropdown {
            position: relative;
            display: inline-block;
        }

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
        
        .dropdown:hover .dropdown-content {
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

        /* User info styling */
        .user-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            color: #495057;
            font-weight: 500;
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
                                </div>
                                <div class="header-right-btn f-right d-none d-lg-block ml-30">
                                    <div class="dropdown">
                                        <a href="#" class="header-btn">
                                            <?php echo $isLoggedIn ? "Welcome, " . htmlspecialchars($userName) : "Welcome, Guest"; ?> ▼
                                        </a>
                                        <div class="dropdown-content">
                                            <?php if ($isLoggedIn): ?>
                                                <a href="profile.html">Profile</a>
                                                <?php if ($userRole == 'admin'): ?>
                                                    <a href="viewDashboardAdmin.php">Dashboard</a>
                                                    <a href="viewFeedback.php">View Feedback</a>
                                                <?php elseif ($userRole == 'staff'): ?>
                                                    <a href="viewDashboardStaff.php">Dashboard</a>
                                                    <a href="viewFeedback.php">View Feedback</a>
                                                <?php endif; ?>
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
        <!-- Header End -->
    </header>

    <!-- Main dashboard content -->
    <main class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <h2>Customer Feedback Management</h2>
                <p>Monitor and analyze customer feedback to improve service quality</p>
            </div>
            
            <div class="user-info">
                <?php if ($isLoggedIn): ?>
                    Logged in as <?php echo htmlspecialchars($userName); ?> (Role: <?php echo ucfirst($userRole); ?>)
                <?php else: ?>
                    You are browsing as a guest. <a href="signIn.php" style="color: #dc3545; text-decoration: none; font-weight: 600;">Sign in</a> for more features.
                <?php endif; ?>
            </div>

            <?php if ($userRole === 'admin' || $userRole === 'staff'): ?>
                <!-- Feedback Statistics -->
                <div class="stats-cards">
                    <div class="stats-card">
                        <h3><?php echo count($feedbackData); ?></h3>
                        <p>Total Feedback</p>
                    </div>
                    <div class="stats-card">
                        <h3><?php 
                            $satisfiedCount = 0;
                            foreach ($feedbackData as $fb) {
                                if (strtolower($fb['rating']) === 'satisfied') $satisfiedCount++;
                            }
                            echo $satisfiedCount;
                        ?></h3>
                        <p>Satisfied Customers</p>
                    </div>
                    <div class="stats-card">
                        <h3><?php 
                            $badCount = 0;
                            foreach ($feedbackData as $fb) {
                                if (strtolower($fb['rating']) === 'bad') $badCount++;
                            }
                            echo $badCount;
                        ?></h3>
                        <p>Needs Attention</p>
                    </div>
                    <div class="stats-card">
                        <h3><?php 
                            $recentCount = 0;
                            // Since we don't have created_at, we'll show total as "recent"
                            echo min(count($feedbackData), 10);
                        ?></h3>
                        <p>Recent Reviews</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($userRole === 'admin'): ?>
                <div class="role-notice admin-notice">Admin View: Full feedback details and analytics</div>
                <div class="feedback-table-container">
                    <table class="feedback-table">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Email Address</th>
                                <th>Subject</th>
                                <th>Rating</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($feedbackData)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 40px; color: #666;">
                                        No feedback available at the moment.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($feedbackData as $fb): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($fb['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($fb['email']); ?></td>
                                    <td><?php echo htmlspecialchars($fb['subject']); ?></td>
                                    <td>
                                        <?php 
                                        $rating = strtolower($fb['rating']);
                                        $badgeClass = 'rating-' . $rating;
                                        ?>
                                        <span class="rating-badge <?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars($fb['rating']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($fb['message']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($userRole === 'staff'): ?>
                <div class="role-notice staff-notice">Staff View: Limited feedback details for quality improvement</div>
                <div class="feedback-table-container">
                    <table class="feedback-table">
                        <thead>
                            <tr>
                                <th>Customer Name</th>
                                <th>Subject</th>
                                <th>Rating</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($feedbackData)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 40px; color: #666;">
                                        No feedback available at the moment.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($feedbackData as $fb): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($fb['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($fb['subject']); ?></td>
                                    <td>
                                        <?php 
                                        $rating = strtolower($fb['rating']);
                                        $badgeClass = 'rating-' . $rating;
                                        ?>
                                        <span class="rating-badge <?php echo $badgeClass; ?>">
                                            <?php echo htmlspecialchars($fb['rating']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($fb['message']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($userRole === 'customer'): ?>
                <div class="role-notice customer-notice">Customer View</div>
                <div class="no-access">
                    <h3>🔒 Access Restricted</h3>
                    <p>This section is reserved for staff and administrators only.</p>
                    <p>If you have feedback to share, please visit our <a href="feedback.php" style="color: #dc3545; text-decoration: none; font-weight: 600;">feedback page</a>.</p>
                </div>

            <?php else: ?>
                <div class="role-notice guest-notice">Guest Access</div>
                <div class="no-access">
                    <h3>🔐 Authentication Required</h3>
                    <p>Please <a href="signIn.php" style="color: #dc3545; text-decoration: none; font-weight: 600;">sign in</a> to access this feature.</p>
                    <p>Don't have an account? <a href="registerGuest.php" style="color: #dc3545; text-decoration: none; font-weight: 600;">Register here</a> to get started.</p>
                </div>
            <?php endif; ?>

            <!-- Back Button -->
            <div class="back-btn">
                <a href="homepage.php">⬅ Back to Homepage</a>
            </div>
        </div>
    </main>>

</body>
</html>