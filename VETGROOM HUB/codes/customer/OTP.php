<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";
$userRole = $isLoggedIn ? ($_SESSION['user_role'] ?? 'customer') : 'guest';

// Handle AJAX OTP check
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    header("Content-Type: application/json");

    if (isset($_SESSION['otp']) && $_POST['otp'] === $_SESSION['otp']) {
        echo json_encode(["status" => "success", "message" => "✅ OTP verified successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Invalid OTP code. Please try again."]);
    }
    exit;
}

// Handle resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend'])) {
    header("Content-Type: application/json");

    // Generate new OTP
    $_SESSION['otp'] = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $_SESSION['otp_expiry'] = time() + 300; // 5 minutes expiry

    // TODO: Send email with PHPMailer ($_SESSION['otp'])

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
    <title>OTP Verification - VetGroom Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            background-image: url('assets/img/hero/hero2.png'); 
            background-repeat: no-repeat; 
            background-attachment: fixed; 
            background-size: cover; 
            background-position: center;
            margin: 0;
        }

        .verification-card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        .verification-card h2 {
            text-align: center;
            margin-bottom: 15px;
            font-size: 28px;
            color: #333;
        }

        .verification-card p {
            text-align: center;
            margin-bottom: 25px;
            font-size: 16px;
            color: #666;
        }

        .input-wrapper {
            position: relative;
            margin-bottom: 20px;
        }

        .input-wrapper input {
            width: 100%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            box-sizing: border-box;
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

        .verify-btn {
            width: 100%;
            padding: 15px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: background 0.3s;
        }

        .verify-btn:hover {
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

        .resend {
            text-align: center;
            margin-top: 20px;
        }

        .resend a {
            color: #dc3545;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
        }

        .resend a.disabled {
            color: #999;
            pointer-events: none;
        }

        .countdown {
            margin-top: 8px;
            color: #666;
            font-size: 14px;
        }
        .error-message {
    display: block;
    color: #e74c3c;
    font-size: 14px;
    margin-top: 5px;
    margin-bottom: 10px;
}

    </style>
</head>
<body>
<div class="verification-card">
    <h2>OTP Verification</h2>
    <p>Enter the OTP sent to your email to continue</p>

    <form id="otpForm" novalidate>
        <div class="input-wrapper">
            <input type="text" id="otp" name="otp" placeholder=" " maxlength="6" required>
            <label for="otp">6-digit OTP</label>
        </div>
        <span class="error-message" id="otpError"></span>

        <button type="submit" class="verify-btn">
            <span class="btn-text">Verify OTP</span>
            <span class="btn-loader"></span>
        </button>
    </form>

    <div id="message"></div>

    <div class="resend">
        Didn’t receive the code? <a id="resendOtp">Resend OTP</a>
        <div id="timer" class="countdown" style="display:none;">
            Resend available in <span id="countdown">60</span>s
        </div>
    </div>
</div>

<script>
const otpForm = document.getElementById('otpForm');
const otpInput = document.getElementById('otp');
const messageEl = document.getElementById('message');
const resendLink = document.getElementById('resendOtp');
const timerEl = document.getElementById('timer');
const countdownEl = document.getElementById('countdown');
const btnText = document.querySelector('.btn-text');
const btnLoader = document.querySelector('.btn-loader');
const otpError = document.getElementById('otpError');
let countdownInterval;

function showMessage(text, type) {
    messageEl.textContent = text;
    messageEl.className = type;
}

// ✅ Verify OTP
otpForm.addEventListener('submit', (e) => {
    e.preventDefault();
    otpError.textContent = "";
    const otp = otpInput.value.trim();

    if (!/^\d{6}$/.test(otp)) {
        showMessage("Please enter a valid 6-digit OTP.", "error");
        return;
    }

    btnText.style.opacity = "0.5";
    btnLoader.style.display = "block";

    fetch('OTPBackend.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'otp=' + encodeURIComponent(otp)
    })
    .then(res => res.json())
    .then(data => {
        btnText.style.opacity = "1";
        btnLoader.style.display = "none";

        showMessage(data.message, data.status);
        if (data.status === 'success') {
            setTimeout(()=>{ window.location.href = 'signIn.php'; }, 2000);
        }
    })
    .catch(()=> {
        btnText.style.opacity = "1";
        btnLoader.style.display = "none";
        showMessage("⚠️ Something went wrong!", "error");
    });
});

// ✅ Resend OTP
resendLink.addEventListener('click', () => {
    resendLink.classList.add('disabled');

    fetch('OTPBackend.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: 'resend=1'
    })
    .then(res=>res.json())
    .then(data=>{
        showMessage(data.message, data.status);
        if (data.status==='success') startCountdown();
        else resendLink.classList.remove('disabled');
    })
    .catch(()=> { 
        showMessage("⚠️ Something went wrong!", "error"); 
        resendLink.classList.remove('disabled'); 
    });
});

function startCountdown() {
    let time = 60;
    timerEl.style.display = 'block';
    countdownEl.textContent = time;
    countdownInterval = setInterval(()=>{
        time--;
        countdownEl.textContent = time;
        if(time <= 0){
            clearInterval(countdownInterval);
            timerEl.style.display = 'none';
            resendLink.classList.remove('disabled');
        }
    },1000);
}
</script>
</body>
</html>
