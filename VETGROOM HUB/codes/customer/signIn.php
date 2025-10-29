
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
    <title>About Us - VetGroom Hub</title>
    <meta name="description" content="Learn about VetGroom Hub, your trusted partner in pet care and grooming services.">
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
    <link rel="stylesheet" href="forms/basic/style.css">
    <style>
        body {
            opacity: 0;
            animation: fadeInAnimation ease 1s;
            animation-fill-mode: forwards;
            }

            @keyframes fadeInAnimation {
            0% {
                opacity: 0;
              }
            100% {
              opacity: 1;
              }
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

<body style="background-image: url('assets/img/hero/hero2.png'); background-repeat: no-repeat; background-attachment: fixed; background-size: cover; background-position: center;">

    <header>
        <!--? Header Start -->
        <div class="header-area header-transparent">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <!-- Logo -->
                        <div class="col-xl-2 col-lg-2 col-md-1">
                            <div class="logo">
                                <a href="index.php"><img src="assets/img/logo/logo.png" alt="VetGroom Hub Logo"></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <!-- Main-menu -->
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav> 
                                        <ul id="navigation">
                                            <li><a href="index.php">Home</a></li>
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
    <main>
    <div class="login-container" style="margin-top: 60px;">
        <div class="login-card">
            <div class="login-header">
                <h2>Sign In</h2>
                <p>Enter your credentials to access your account</p>
            </div>
            
            <form class="login-form" id="loginForm" novalidate>
                <div class="form-group">
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" autocomplete="email">
                        <label for="email">Email Address</label>
                    </div>
                    <span class="error-message" id="emailError"></span>
                </div>

                <div class="form-group">
                    <div class="input-wrapper password-wrapper">
                        <input type="password" id="password" name="password" required autocomplete="current-password">
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" id="passwordToggle" aria-label="Toggle password visibility">
                        <span class="eye-icon" id="eyeIcon"></span>
                        </button>
                    </div>
                    <span class="error-message" id="passwordError"></span>
                </div>

                <div class="form-options">
                    <label class="remember-wrapper" for="rememberMe">
                        <input type="checkbox" id="rememberMe" name="remember">
                        <span class="checkbox-label">
                            <span class="checkmark"></span>
                            Remember me
                        </span>
                    </label>
                    <a href="forgetPassword.html" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="login-btn">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-loader"></span>
                </button>
            </form>

            <div class="signup-link">
                <p>Don't have an account? <a href="registerGuest.php">Create one</a></p>
            </div>

            <div class="success-message" id="successMessage">
                <div class="success-icon">✓</div>
                <h3>Login Successful!</h3>
                <p>Redirecting to your homepage...</p>
            </div>
        </div>
    </div>
</main>

<!-- ===== JS Section ===== -->
<script>
    document.getElementById("loginForm").addEventListener("submit", async function(event) {
        event.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();
        const emailError = document.getElementById("emailError");
        const passwordError = document.getElementById("passwordError");
        const successMessage = document.getElementById("successMessage");

        emailError.textContent = "";
        passwordError.textContent = "";

        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
    if (!email) {
        document.getElementById("emailError").textContent = "Email is required.";
        hasError = true;
    } else if (!emailPattern.test(email)) {
        document.getElementById("emailError").textContent = "Please enter a valid email.";
        hasError = true;
    }
        if (!password) {
            passwordError.textContent = "Password is required.";
            return;
        }

        try {
            const response = await fetch("signInBackend.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: new URLSearchParams({ email, password })
            });

            const result = await response.json();

            if (result.status === "success") {
                // Determine the correct redirection URL based on user role
                let redirectUrl;
                if (result.user_role === 'admin') {
                    redirectUrl = "viewDashboardAdmin.php";
                } else if (result.user_role === 'staff') {
                    redirectUrl = "viewDashboardStaff.php";
                } else {
                    // Default to homepage for customers and other roles
                    redirectUrl = "index.php";
                }

                alert("Sign-In Successful! Redirecting to your dashboard...");
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1500);
            } else {
                passwordError.textContent = result.message || "Login failed.";
            }
        } catch (error) {
            passwordError.textContent = "⚠️ Server error. Please try again later.";
            console.error("Error:", error);
        }
    });
</script>

<script src="../../shared/js/form-utils.js"></script>
<script src="script.js"></script>

    </body>
</html>