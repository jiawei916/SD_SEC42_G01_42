<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}

$userName  = $_SESSION['user_name'];
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : "Not provided";
$userRole  = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "customer";
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
        
        /* Profile section styles - UPDATED to match the image */
        .profile-section {
            background: #fff; /* White background */
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        
        .profile-section h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .profile-info {
            margin-bottom: 25px;
        }
        
        .profile-info label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
            font-size: 14px;
        }
        
        .profile-info .form-control {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px 15px;
            background-color: #f9f9f9;
            margin-bottom: 15px;
        }
        
        .profile-info .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
        }
        
        .email-confirmation {
            color: #e74c3c;
            font-size: 14px;
            margin-top: -10px;
            margin-bottom: 20px;
        }
        
        .btn-resend {
            background: none;
            border: none;
            color: #4a90e2;
            text-decoration: underline;
            cursor: pointer;
            padding: 0;
            font-size: 14px;
        }
        
        .btn-resend:hover {
            color: #357abd;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            margin-top: 30px;
        }
        
        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }
        
        .btn-save {
            background-color: #4a90e2;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
        }
        
        .btn-save:hover {
            background-color: #357abd;
        }
        
        .btn-cancel {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 10px 20px;
            border-radius: 6px;
            color: #555;
            font-weight: 600;
        }
        
        .btn-cancel:hover {
            background-color: #e0e0e0;
        }

        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
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
                                            <li class="active"><a href="viewService.php">Services</a></li>
                                            <li class="active"><a href="feedback.php">Feedback</a></li>
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
<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'customer'): ?>
    <a href="bookAppointment.php">Book Appointment</a>
    <a href="viewAppointment.php">View Appointments</a> 
<?php elseif ((isset($_SESSION['user_role'])) && $_SESSION['user_role'] == 'admin'): ?>
    <a href="viewDashboardAdmin.php">Dashboard</a>
    <a href="viewFeedBack.php">View Feedback</a>
    <a href="viewCustomer.php">View Customer</a>
    <a href="viewStaff.php">View Staff</a>
<?php elseif ((isset($_SESSION['user_role'])) && $_SESSION['user_role'] == 'staff'): ?>
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
    <main class="container" style="margin-top: 50px;">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="profile-section">
                    <h2>Account settings</h2>

<form id="profileForm" novalidate>
    <div class="profile-info">
        <label for="username">Username</label>
        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($userName); ?>" readonly>
        
        <label for="name">Name</label>
        <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($userName); ?>">
        <span class="error-message" id="nameError"></span>
        
        <label for="email">E-mail</label>
        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>">
        <span class="error-message" id="emailError"></span>
    </div>

    <div class="action-buttons">
        <button type="submit" class="btn-save">Save changes</button>
        <button type="button" class="btn-cancel">Cancel</button>
    </div>
</form>
                    <hr>

                    <h4 class="section-title">Account Options</h4>

                    <?php if ($userRole == 'admin'): ?>
                    <div class="d-grid gap-2">
                        <a href="changePassword.html?user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-warning">Change Password</a>
                        <a href="viewDashboardAdmin.php" class="btn btn-primary">Admin Dashboard</a>
                        <a href="viewService.php" class="btn btn-success">View Services</a>
                        <a href="viewCustomer.php" class="btn btn-success">View Customer</a>
                        <a href="viewFeedBack.php" class="btn btn-info">View Feedback</a>
                    </div>

                    <?php elseif ($userRole == 'staff'): ?>
                    <div class="d-grid gap-2">
                        <a href="changePassword.html?user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-warning">Change Password</a>
                        <a href="viewDashboardStaff.php" class="btn btn-primary">Staff Dashboard</a>
                        <a href="viewService.php" class="btn btn-success">View Services</a>
                        <a href="viewFeedBack.php" class="btn btn-info">View Feedback</a>
                    </div>

                    <?php else: ?>
                    <div class="d-grid gap-2">
                        <a href="changePassword.html?user_id=<?php echo $_SESSION['user_id']; ?>" class="btn btn-warning">Change Password</a>
                        <a href="viewService.php" class="btn btn-success">View Services</a>
                        <a href="feedback.php" class="btn btn-info">Submit Feedback</a>
                        <a href="bookAppointment.php" class="btn btn-primary">Book Appointment</a>
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
<script>
document.getElementById("profileForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const nameError = document.getElementById("nameError");
    const emailError = document.getElementById("emailError");
    emailError.textContent = ""; // reset error

    // Simple email regex pattern
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    document.getElementById("nameError").textContent = "";
    document.getElementById("emailError").textContent = "";

    let hasError = false;
if (!name) {
    document.getElementById("nameError").textContent = "Name is required.";
    hasError = true;
}
if (!email) {
    document.getElementById("emailError").textContent = "Email is required.";
    hasError = true;
} else {
    const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    if (!emailPattern.test(email)) {
        document.getElementById("emailError").textContent = "Please enter a valid email address.";
        hasError = true;
    }
}

    if (hasError) return; // stop if client-side validation fails

    try {
        const response = await fetch("profileUpdate.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        if (result.status === "success") {
            alert("✅ Profile updated successfully!");
            window.location.reload(); 
        } else {
            // Show server error in the emailError span instead of alert
            emailError.textContent = result.message;
        }
    } catch (err) {
        emailError.textContent = "⚠️ Server error. Please try again later.";
    }
});
</script>
</html>