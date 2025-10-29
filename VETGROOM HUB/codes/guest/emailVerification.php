<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verification - VetGroom Hub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Minimal inline style for verification card */
        body { display:flex; justify-content:center; align-items:center; height:100vh; font-family:sans-serif; }
        .verification-card { background:#fff; padding:30px; border-radius:10px; box-shadow:0 5px 20px rgba(0,0,0,0.1); width:350px; }
        .verification-card h2 { text-align:center; margin-bottom:20px; }
        input { width:100%; padding:12px; margin-bottom:15px; border-radius:6px; border:1px solid #ddd; }
        button { width:100%; padding:12px; background:#dc3545; color:#fff; border:none; border-radius:6px; cursor:pointer; }
        #message { text-align:center; margin-top:10px; padding:8px; border-radius:5px; }
        #message.success { background:#eaf7ea; color:green; }
        #message.error { background:#fdecea; color:#e74c3c; }
    </style>
</head>
<body>
    <div class="verification-card">
        <h2>Email Verification</h2>
        <p>Enter the OTP sent to your email</p>
        <form id="verifyForm">
            <input type="text" id="otp" name="otp" placeholder="6-digit OTP" required>
            <button type="submit">Verify Email</button>
        </form>
        <div id="message"></div>
    </div>

    <script>
        document.getElementById("verifyForm").addEventListener("submit", function(e){
            e.preventDefault();
            const otpValue = document.getElementById("otp").value.trim();
            const message = document.getElementById("message");

            fetch("verify.php", {
                method:"POST",
                headers:{"Content-Type":"application/x-www-form-urlencoded"},
                body:"otp="+encodeURIComponent(otpValue)
            })
            .then(res => res.json())
            .then(data => {
                message.className = data.status==="success"?"success":"error";
                message.textContent = data.message;
                if(data.status==="success") setTimeout(()=>{window.location.href="signIn.php";}, 2000);
            })
            .catch(err => { message.className="error"; message.textContent="Server error"; });
        });
    </script>
</body>
</html>
