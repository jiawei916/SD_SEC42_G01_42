<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Email Verification</title>
  <link rel="stylesheet" href="style.css">
      <style>
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
    <a href="emailVerification.html">Verification</a>
  </nav>

<main>
  <div class="container">
    <h2>Email Verification</h2>
    
    <!-- User pastes token or auto-fills from URL -->
    <form id="verifyForm">
      <input type="text" id="token" name="token" placeholder="Enter verification token" required>
      <button type="submit">Verify Email</button>
      <p id="message" class="error"></p>
    </form>
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

    fetch("verifyEmail.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: "token=" + encodeURIComponent(tokenValue)
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === "success") {
        message.style.color = "green";
        message.textContent = "✅ Email verified successfully! You may now log in.";
      } else {
        message.style.color = "red";
        message.textContent = "❌ Verification failed: " + data.message;
      }
    })
    .catch(err => {
      message.style.color = "red";
      message.textContent = "⚠️ Error connecting to server.";
    });
  });
</script>

</body>
</html>
