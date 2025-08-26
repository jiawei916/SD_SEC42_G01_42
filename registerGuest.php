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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Registration - VetGroom Hub</title>
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
        
        /* Registration container styling */
        .registration-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 20px;
        }
        
        .registration-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            margin: 40px 0;
        }
        
        .registration-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .registration-header h2 {
            color: #333; /* Changed to default dark color */
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .registration-header p {
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
        
        .password-wrapper {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #999;
        }
        
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }
        
        .registration-btn {
            width: 100%;
            padding: 15px;
            background: #dc3545; /* Same blue as signIn button */
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
        
        .registration-btn:hover {
            background: #ff707f; /* Darker blue on hover */
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
        
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .login-link a {
            color: #dc3543; /* Same blue as signIn button */
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        #message {
            text-align: center;
            margin-top: 15px;
            padding: 10px;
            border-radius: 5px;
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
        
        #message.warning { 
            color: orange; 
            background: #fff4e6;
            border: 1px solid #ffe0b2;
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
        
        header {
            position: relative;
            padding: 15px;
            color: white;
            text-align: center;
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
                            </div>   
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>

    <!-- Main registration content -->
    <main>
        <div class="registration-container">
            <div class="registration-card">
                <div class="registration-header">
                    <h2>Create Account</h2>
                    <p>Register to access all our services</p>
                </div>
                
                <form id="registerForm" method="POST">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="text" id="name" name="name" placeholder=" " required>
                            <label for="name">Full Name</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" placeholder=" " required>
                            <label for="email">Email Address</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="input-wrapper password-wrapper">
                            <input type="password" id="password" name="password" placeholder=" " required>
                            <label for="password">Password</label>
                            <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                                <span class="eye-icon" id="eyeIcon"></span>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="input-wrapper password-wrapper">
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder=" " required>
                            <label for="confirmPassword">Confirm Password</label>
                        </div>
                    </div>
                    
                    <button type="submit" class="registration-btn">
                        <span class="btn-text">Register</span>
                        <span class="btn-loader"></span>
                    </button>
                    
                    <div id="message"></div>
                </form>
                
                <div class="login-link">
                    <p>Already have an account? <a href="signIn.php">Sign In</a></p>
                </div>
            </div>
        </div>
    </main>

    <!-- JavaScript for form handling -->
    <script>
    document.getElementById("registerForm").addEventListener("submit", function(event) {
        event.preventDefault();

        let name = document.getElementById("name").value.trim();
        let email = document.getElementById("email").value.trim();
        let password = document.getElementById("password").value.trim();
        let confirmPassword = document.getElementById("confirmPassword").value.trim();
        let message = document.getElementById("message");

        message.textContent = "";
        message.className = "";

        // Email format validation
        let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;

        if (name === "" || email === "" || password === "" || confirmPassword === "") {
            message.textContent = "All fields are required.";
            message.className = "error";
            return;
        }

        if (!emailPattern.test(email)) {
            message.textContent = "Please enter a valid email address.";
            message.className = "error";
            return;
        }

        if (password.length < 5) {
            message.textContent = "Password must be at least 5 characters.";
            message.className = "error";
            return;
        }

        if (password !== confirmPassword) {
            message.textContent = "Passwords do not match.";
            message.className = "error";
            return;
        }

        // Show loading animation
        const btnText = document.querySelector('.btn-text');
        const btnLoader = document.querySelector('.btn-loader');
        btnText.style.opacity = '0.5';
        btnLoader.style.display = 'block';

        // Prepare data to send
        let formData = new FormData(this);

        fetch("registerBackend.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json()) 
        .then(data => {
            // Reset loading animation
            btnText.style.opacity = '1';
            btnLoader.style.display = 'none';
            
            // ✅ Always expect JSON with {status, message}
            message.textContent = data.message;

            if (data.status === "success") {
                message.className = "success";
                this.reset();
            } else if (data.status === "warning") {
                message.className = "warning";
            } else {
                message.className = "error";
            }
        })
        .catch(error => {
            // Reset loading animation
            btnText.style.opacity = '1';
            btnLoader.style.display = 'none';
            
            message.textContent = "⚠️ Network error: " + error;
            message.className = "error";
        });
    });
    
    // Password visibility toggle
    document.getElementById("passwordToggle").addEventListener("click", function() {
        const passwordInput = document.getElementById("password");
        const eyeIcon = document.getElementById("eyeIcon");
        
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            eyeIcon.textContent = "🔒";
        } else {
            passwordInput.type = "password";
            eyeIcon.textContent = "👁️";
        }
    });
    </script>

</body>
</html>