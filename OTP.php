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

// Handle OTP verification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header("Content-Type: application/json");
    
    $enteredOtp = trim($_POST["otp"] ?? "");
    $storedOtp = $_SESSION['otp'] ?? null;
    $otpExpiry = $_SESSION['otp_expiry'] ?? null;

    if (empty($enteredOtp)) {
        echo json_encode(["status" => "error", "message" => "OTP is required."]);
        exit;
    }

    if ($storedOtp && $otpExpiry && time() < $otpExpiry) {
        if ($enteredOtp === $storedOtp) {
            // OTP verified successfully
            unset($_SESSION['otp']);
            unset($_SESSION['otp_expiry']);
            $_SESSION['otp_verified'] = true;
            
            echo json_encode([
                "status" => "success", 
                "message" => "OTP verified successfully!",
                "redirect" => "homepage.php" // Redirect to appropriate page
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid OTP code."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "OTP has expired. Please request a new one."]);
    }
    exit;
}

// Generate OTP if not already set (for demo purposes)
if (!isset($_SESSION['otp'])) {
    $_SESSION['otp'] = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['otp_expiry'] = time() + 300; // 5 minutes expiry
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>OTP Verification - VetGroom Hub</title>
    <meta name="description" content="Verify your identity with One-Time Password">
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
        
        /* OTP container styling */
        .otp-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            padding: 20px;
        }
        
        .otp-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            padding: 40px;
            margin: 40px 0;
        }
        
        .otp-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .otp-header h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }
        
        .otp-header p {
            color: #666;
            font-size: 16px;
            line-height: 1.5;
        }
        
        .otp-instructions {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .otp-instructions p {
            margin: 0;
            color: #666;
            font-size: 14px;
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
            font-size: 18px;
            text-align: center;
            letter-spacing: 8px;
            transition: border-color 0.3s;
            box-sizing: border-box;
            font-weight: bold;
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
        
        .otp-btn {
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
        
        .otp-btn:hover {
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
        
        .resend-link {
            text-align: center;
            margin-top: 20px;
        }
        
        .resend-link a {
            color: #dc3545;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }
        
        .resend-link a:hover {
            text-decoration: underline;
        }
        
        .resend-link .disabled {
            color: #999;
            cursor: not-allowed;
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
        
        /* Countdown timer */
        .countdown {
            color: #dc3545;
            font-weight: bold;
            margin-top: 5px;
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
        
        /* Header adjustments */
        .header-area {
            position: relative;
        }
        
        .main-header {
            padding: 10px 0;
        }
        
        .menu-main {
            gap: 30px;
        }
        
        .profile-dropdown {
            position: relative;
            top: 0;
            right: 0;
            margin-left: auto;
        }
        
        .main-menu {
            margin-right: 20px;
        }
        
        .main-menu ul {
            display: flex;
            gap: 25px;
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
        
        .container-fluid {
            padding: 0 20px;
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

    <main>
        <div class="otp-container">
            <div class="otp-card">
                <div class="otp-header">
                    <h2>OTP Verification</h2>
                    <p>Enter the 6-digit code sent to your email</p>
                </div>
                
                <div class="otp-instructions">
                    <p>For demo purposes, your OTP is: <strong><?php echo $_SESSION['otp'] ?? 'Not generated'; ?></strong></p>
                    <p>This code will expire in <span id="countdown" class="countdown">5:00</span></p>
                </div>
                
                <form id="otpForm" method="POST">
                    <div class="form-group">
                        <div class="input-wrapper">
                            <input type="text" id="otp" name="otp" placeholder="Enter 6-digit code" required maxlength="6" pattern="[0-9]{6}">
                        </div>
                    </div>
                    
                    <button type="submit" class="otp-btn">
                        <span class="btn-text">Verify OTP</span>
                        <span class="btn-loader"></span>
                    </button>
                    
                    <div id="message"></div>
                </form>
                
                <div class="resend-link">
                    <p>Didn't receive the code? <a href="#" id="resendOtp">Resend OTP</a></p>
                    <p id="resendTimer" class="countdown" style="display: none;">Resend available in <span id="resendCountdown">60</span>s</p>
                </div>
            </div>
        </div>
    </main>

<script>
document.getElementById("otpForm").addEventListener("submit", async function(event) {
    event.preventDefault();

    const otp = document.getElementById("otp").value.trim();
    const message = document.getElementById("message");
    const btnText = document.querySelector('.btn-text');
    const btnLoader = document.querySelector('.btn-loader');

    message.textContent = "";
    message.className = "";

    if (otp.length !== 6 || !/^\d+$/.test(otp)) {
        message.textContent = "Please enter a valid 6-digit OTP code.";
        message.className = "error";
        return;
    }

    // Show loading animation
    btnText.style.opacity = '0.5';
    btnLoader.style.display = 'block';

    try {
        const formData = new FormData(this);
        const response = await fetch("otpVerification.php", {
            method: "POST",
            body: formData
        });

        const result = await response.json();

        // Reset loading animation
        btnText.style.opacity = '1';
        btnLoader.style.display = 'none';

        if (result.status === "success") {
            message.textContent = result.message;
            message.className = "success";
            
            // Redirect after success
            setTimeout(() => {
                window.location.href = result.redirect;
            }, 2000);
        } else {
            message.textContent = result.message;
            message.className = "error";
        }
    } catch (error) {
        // Reset loading animation
        btnText.style.opacity = '1';
        btnLoader.style.display = 'none';
        
        message.textContent = "⚠️ Server error. Please try again later.";
        message.className = "error";
        console.error("Error:", error);
    }
});

// Countdown timer for OTP expiry
function startCountdown(minutes, seconds, elementId) {
    let totalSeconds = minutes * 60 + seconds;
    const countdownElement = document.getElementById(elementId);
    
    const interval = setInterval(() => {
        if (totalSeconds <= 0) {
            clearInterval(interval);
            countdownElement.textContent = "0:00";
            return;
        }
        
        totalSeconds--;
        const mins = Math.floor(totalSeconds / 60);
        const secs = totalSeconds % 60;
        countdownElement.textContent = `${mins}:${secs.toString().padStart(2, '0')}`;
    }, 1000);
}

// Start the countdown timer (5 minutes)
startCountdown(5, 0, 'countdown');

// Resend OTP functionality
document.getElementById("resendOtp").addEventListener("click", function(e) {
    e.preventDefault();
    
    // Disable resend button and show timer
    this.classList.add('disabled');
    this.style.pointerEvents = 'none';
    document.getElementById('resendTimer').style.display = 'block';
    
    // Start resend countdown (60 seconds)
    let resendTime = 60;
    const resendCountdown = document.getElementById('resendCountdown');
    resendCountdown.textContent = resendTime;
    
    const resendInterval = setInterval(() => {
        resendTime--;
        resendCountdown.textContent = resendTime;
        
        if (resendTime <= 0) {
            clearInterval(resendInterval);
            document.getElementById('resendOtp').classList.remove('disabled');
            document.getElementById('resendOtp').style.pointerEvents = 'auto';
            document.getElementById('resendTimer').style.display = 'none';
        }
    }, 1000);
    
    // Simulate OTP resend (in real application, this would call your backend)
    fetch("resendOtp.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "action=resend"
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            // Update the demo OTP display
            document.querySelector('.otp-instructions p strong').textContent = data.new_otp;
            // Restart main countdown
            startCountdown(5, 0, 'countdown');
        }
    })
    .catch(error => {
        console.error("Error resending OTP:", error);
    });
});

// Auto-focus OTP input and auto-tab between digits
document.getElementById("otp").addEventListener("input", function(e) {
    if (this.value.length === 6) {
        document.getElementById("otpForm").dispatchEvent(new Event('submit'));
    }
});

// Only allow numbers in OTP field
document.getElementById("otp").addEventListener("keypress", function(e) {
    if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
    }
});
</script>

</body>
</html>