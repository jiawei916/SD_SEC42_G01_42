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
    <link rel="stylesheet" href="style.css">
    <style>
        /* Extra quick styles for messages */
        #message.success { color: green; }
        #message.error { color: red; }
        #message.warning { color: orange; }
        .container {
            margin-top: 40px;   /* moves the box higher (default is usually larger) */
            max-width: 400px;   /* keeps it neat */
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        main {
    display: flex;
    justify-content: center;   /* centers horizontally */
    align-items: flex-start;   /* keep it at the top */
    min-height: auto;          /* 🔑 no forced tall height */
    padding: 20px;             /* some breathing space */
}
/* Profile dropdown (same as About Us page) */
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

    <!-- Site title -->
    <header>
        <h1>VetGroom Hub</h1>    
    
    <!-- Profile Dropdown -->
    <div class="profile-dropdown">
        <span class="profile-icon"><?php echo $isLoggedIn ? "👤" : "👤"; ?></span>
        <span class="profile-name"><?php echo htmlspecialchars($userName); ?> ▼</span>
        <div class="dropdown-content">
            <?php if ($isLoggedIn): ?>
                <a href="profile.html">Profile</a>
                <a href="signOut.php">Sign Out</a>
            <?php else: ?>
                <a href="signIn.php">Sign In</a>
                <a href="registerGuest.php">Register</a>
            <?php endif; ?>
        </div>
    </div>
    </header>

  <!-- Navigation bar -->
  <nav>
    <a href="homepage.php">Homepage</a>
    <a href="aboutUs.php">About</a>
    <a href="contact.php">Contact</a>
    <a href="feedback.php">Feedback</a>
    <a href="emailVerification.php">Verification</a>
    <?php if ($userRole === 'admin' || $userRole === 'staff'): ?>
      <a href="viewFeedback.php">View Feedback</a>
    <?php endif; ?>
  </nav>

    <!-- Main registration content -->
    <main>
        <div class="container">
            <h2>Guest Registration</h2>
            <form id="registerForm" method="POST">
                <input type="text" id="name" name="name" placeholder="Full Name" required>
                <input type="email" id="email" name="email" placeholder="Email Address" required>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                <button type="submit" style="margin-top: 8px;">Register</button>
                <p id="message"></p>
            </form>
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

        // Prepare data to send
        let formData = new FormData(this);

        fetch("register.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json()) 
        .then(data => {
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
            message.textContent = "⚠️ Network error: " + error;
            message.className = "error";
        });
    });
    </script>

</body>
</html>
