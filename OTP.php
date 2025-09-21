<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";

// Determine user role
if (!$isLoggedIn) {
    $userRole = 'guest';
} else {
    $userRole = $_SESSION['user_role'] ?? 'customer';
}
$user_email = $_GET['email'] ?? '';
// Handle AJAX OTP check
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    header("Content-Type: application/json");
    $response = [];

    if ($_POST['otp'] === $_SESSION['otp']) {
        $response = ["status" => "success", "message" => "✅ OTP verified successfully!"];
    } else {
        $response = ["status" => "error", "message" => "❌ Invalid OTP code. Please try again."];
    }

    echo json_encode($response);
    exit;
}

// Handle resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'])) {
    header("Content-Type: application/json");

    // Generate new OTP
    $_SESSION['otp'] = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['otp_expiry'] = time() + 300; // 5 mins expiry

    // send email here with PHPMailer using $_SESSION['otp']

    echo json_encode([
        "status" => "success",
        "message" => "📧 A new OTP has been sent to your email."
    ]);
    exit;
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
        /* 🔴 DO NOT CHANGE YOUR EXISTING STYLE */
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
        /* keep all your styles exactly as before ... */
        

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
    font-size: 20px;
    text-align: center;
    letter-spacing: 8px;
    transition: border-color 0.3s;
    box-sizing: border-box;
    font-weight: 400; /* lighter than bold */
    color: rgba(0, 0, 0, 0.7); /* softer text */
    background: rgba(255, 255, 255, 0.85); /* subtle transparent background */
}

        
        .input-wrapper input:focus {
            border-color: #dc3545;
            outline: none;
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
            background-color: #dc3545;
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
<div class="otp-card">
    <h2>OTP Verification</h2>
    <input type="text" id="otp" placeholder="Enter 6-digit code" maxlength="6">
    <button id="verifyBtn">Verify OTP</button>
    <div id="message"></div>
    <div class="resend">
        Didn't receive the code? <a id="resendOtp">Resend OTP</a>
        <div id="timer" class="countdown" style="display:none;">Resend in <span id="countdown">60</span>s</div>
    </div>
</div>

<script>
const verifyBtn = document.getElementById('verifyBtn');
const otpInput = document.getElementById('otp');
const messageEl = document.getElementById('message');
const resendLink = document.getElementById('resendOtp');
const timerEl = document.getElementById('timer');
const countdownEl = document.getElementById('countdown');
let countdownInterval;

function showMessage(text, type) {
    messageEl.textContent = text;
    messageEl.className = type;
}

// Verify OTP
verifyBtn.addEventListener('click', () => {
    const otp = otpInput.value.trim();
    if(!/^\d{6}$/.test(otp)) {
        showMessage("Please enter a valid 6-digit OTP.", "error");
        return;
    }

    fetch('OTPBackend.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'otp=' + encodeURIComponent(otp)
    })
    .then(res => res.json())
    .then(data => {
        showMessage(data.message, data.status);
        if(data.status === 'success'){
            setTimeout(()=>{ window.location.href = 'changePassword.php'; }, 2000);
        }
    })
    .catch(()=> showMessage("⚠️ Something went wrong!", "error"));
});

// Resend OTP
resendLink.addEventListener('click', () => {
    resendLink.style.pointerEvents = 'none';
    fetch('OTPBackend.php', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:'resend=1'
    })
    .then(res=>res.json())
    .then(data=>{
        showMessage(data.message, data.status);
        if(data.status==='success') startCountdown();
        else resendLink.style.pointerEvents = 'auto';
    })
    .catch(()=> { showMessage("⚠️ Something went wrong!", "error"); resendLink.style.pointerEvents='auto'; });
});

function startCountdown(){
    let time = 60;
    timerEl.style.display = 'block';
    countdownEl.textContent = time;
    countdownInterval = setInterval(()=>{
        time--;
        countdownEl.textContent = time;
        if(time<=0){
            clearInterval(countdownInterval);
            timerEl.style.display = 'none';
            resendLink.style.pointerEvents='auto';
        }
    },1000);
}
</script>
</body>
</html>