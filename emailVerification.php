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
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Email Verification - VetGroom Hub</title>
    <meta name="description" content="Verify your email address with VetGroom Hub">
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
        
        /* Verification container styling */
        .verification-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 20px;
        }
        
        .verification-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            margin: 40px 0;
        }
        
        .verification-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .verification-header h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .verification-header p {
            color: #666;
            font-size: 16px;
        }
        
        .form-group {
            margin-bottom: 20px;
            position: relative;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-wrapper input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        
        .input-wrapper input:focus {
            border-color: #3aa9e4;
            outline: none;
        }
        
        .input-wrapper label {
            position: absolute;
            top: 15px;
            left: 15px;
            color: #999;
            pointer-events: none;
            transition: 0.3s;
            font-size: 16px;
        }
        
        .input-wrapper input:focus + label,
        .input-wrapper input:not(:placeholder-shown) + label {
            top: -10px;
            left: 10px;
            font-size: 12px;
            background: white;
            padding: 0 5px;
            color: #3aa9e4;
        }
        
        .verification-btn {
            width: 100%;
            padding: 15px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
            position: relative;
            overflow: hidden;
            font-weight: 600;
        }
        
        .verification-btn:hover {
            background: #ff707f;
        }
        
        .btn-text {
            display: block;
        }
        
        .btn-loader {
            display: none;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            margin-top: -10px;
            margin-left: -10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        #message {
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
            font-weight: 500;
        }
        
        #message.success { 
            color: green; 
            background: #eaf7ea;
            border: 1px solid #c3e6c3;
        }
        
        #message.error { 
            color: #e74c3c; 
            background: #fdecea;
            border: 1px solid #f5c6cb;
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
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 40px;
            background: white;
            min-width: 140px;
            box-shadow: 0px 0px 8px rgba(0,0,0,0.2);
            border-radius: 5px;
            z-index: 1;
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
        
        .profile-dropdown:hover .dropdown-content {
            display: block;
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
                                            <li class="active"><a href="viewService.php">Services</a></li>
                                            <li class="active"><a href="feedback.php">Feedback</a></li>
                                            <li><a href="contact.php">Contact</a></li>
                                            <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                                <li><a href="viewFeedBack.php">View Feedback</a></li>
                                            <?php endif; ?>
                                        </ul>
                                    </nav>
                                </div>
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>

    <main>
        <div class="verification-container">
            <div class="verification-card">
                <div class="verification-header">
                    <h2>Email Verification</h2>
                    <p>Verify your email address to complete registration</p>
                </div>
                
                <!-- User pastes token or auto-fills from URL -->
                <form id="verifyForm">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="text" id="token" name="token" placeholder=" " required>
                            <label for="token">Verification Token</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="verification-btn">
                        <span class="btn-text">Verify Email</span>
                        <span class="btn-loader"></span>
                    </button>
                    
                    <div id="message"></div>
                </form>
            </div>
        </div>
    </main>

<script>
  // Auto-fill token from URL if present
  const urlParams = new URLSearchParams(window.location.search);
  const token = urlParams.get("token");
  if (token) {
    document.getElementById("token").value = token;
  }

  // Handle form submission with AJAX
  document.getElementById("verifyForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const tokenValue = document.getElementById("token").value.trim();
    const message = document.getElementById("message");
    const btnText = document.querySelector('.btn-text');
    const btnLoader = document.querySelector('.btn-loader');

    // Show loading animation
    btnText.style.opacity = '0.5';
    btnLoader.style.display = 'block';

    fetch("verifyEmail.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "token=" + encodeURIComponent(tokenValue)
    })
    .then(res => res.json())
    .then(data => {
        // Reset loading animation
        btnText.style.opacity = '1';
        btnLoader.style.display = 'none';
        
        if (data.status === "success") {
            message.className = "success";
            message.textContent = "✅ Email verified successfully! You may now log in.";
            
            // Redirect to login after a delay
            setTimeout(() => {
                window.location.href = "signIn.php";
            }, 2000);
        } else {
            message.className = "error";
            message.textContent = "❌ Verification failed: " + data.message;
        }
    })
    .catch(err => {
        // Reset loading animation
        btnText.style.opacity = '1';
        btnLoader.style.display = 'none';
        
        message.className = "error";
        message.textContent = "⚠️ Error connecting to server.";
    });
  });
</script>

</body>
</html>