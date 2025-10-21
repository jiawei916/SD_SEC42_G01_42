<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName   = $isLoggedIn ? $_SESSION['user_name'] : "Guest";

// Determine user role
if (!$isLoggedIn) {
    $userRole = 'guest';
} else {
    $userRole = $_SESSION['user_role'] ?? 'customer';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Feedback - VetGroom Hub</title>
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
    /* Feedback box (slightly orange, centered, no card border) */
    main {
        display: flex;
        justify-content: center;
        padding: 40px 20px;
    }
    .feedback-box {
        background: rgba(255, 252, 247, 1)ff; /* light orange */
        padding: 30px;
        border-radius: 10px;
        width: 100%;
        max-width: 600px;
        text-align: center;
    }
    .feedback-box h2 {
        margin-bottom: 10px;
        color: #333;
    }
    .feedback-box p {
        margin-bottom: 20px;
        color: #555;
    }

    /* Inputs & Textarea */
    input, textarea {
        width: 100%;
        padding: 10px;
        margin-top: 8px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        box-sizing: border-box;
    }
    textarea {
        height: 150px;
        resize: vertical;
    }

    button {
        margin-top: 15px;
        padding: 10px 20px;
        background: #ff9933;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }
    button:hover {
        background: #e68a00;
    }
  </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="header-area header-transparent">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <!-- Logo -->
                        <div class="col-xl-2 col-lg-2 col-md-1">
                            <div class="logo">
                                <a href="index.php"><img src="assets/img/logo/logo.png" alt="VetGroom Hub"></a>
                            </div>
                        </div>
                        <!-- Nav -->
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav>
                                        <ul id="navigation">
                                            <li><a href="index.php">Home</a></li>
                                            <li><a href="aboutUs.php">About</a></li>
                                            <li class="active"><a href="feedback.php">Feedback</a></li>
                                            <li><a href="contact.php">Contact</a></li>
                                            <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                                <li><a href="viewFeedBack.php">View Feedback</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                                <!-- Profile Dropdown -->
                                <div class="header-right-btn f-right d-none d-lg-block ml-30">
                                    <div class="dropdown">
                                        <a href="#" class="header-btn">
                                            <?php echo $isLoggedIn ? "Welcome, " . htmlspecialchars($userName) : "Welcome, Guest"; ?> ▼
                                        </a>
                                        <div class="dropdown-content">
                                            <?php if ($userRole == 'customer'): ?>
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

    <!-- Main Content -->
    <main>
        <div class="feedback-box">
            <h2>We Value Your Feedback</h2>
            <p>Your thoughts help us improve our services.</p>

            <form action="submitFeedback.php" method="POST">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Your Name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="your@email.com" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Write your feedback here..." required></textarea>

                <button type="submit">Submit Feedback</button>
            </form>
        </div>
    </main>

    <!-- JS -->
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery.slicknav.min.js"></script>
    <script src="./assets/js/main.js"></script>
</body>
</html>
