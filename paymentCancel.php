<?php
session_start();

// Optional: Check if user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Cancelled - VetGroom Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        .return-btn {
            width: 100%;
            padding: 15px;
            background: #dc3545;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .return-btn:hover {
            background: #ff707f;
        }
    </style>
</head>
<body>
    <div class="verification-card">
        <h2>Payment Cancelled</h2>
        <p>Your payment was cancelled. You’ll be redirected shortly...</p>
        <button class="return-btn" id="returnBtn">Return to Booking</button>
    </div>

    <script>
        // Redirect automatically after 3 seconds
        setTimeout(() => {
            window.location.href = "bookAppointment.php";
        }, 3000);

        // Allow user to click button to return immediately
        document.getElementById("returnBtn").addEventListener("click", () => {
            window.location.href = "bookAppointment.php";
        });
    </script>
</body>
</html>
