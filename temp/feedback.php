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

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Feedback - VetGroom Hub</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Input Fields & Textarea (Unified Style) */
input,
.feedback-box textarea {
    width: 100%;
    padding: 10px;
    margin-top: 8px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    font-family: inherit;
    box-sizing: border-box;
    background-color: #fff;
    line-height: 1.4;
}

/* Specific Textarea Adjustments */
.feedback-box textarea {
    height: 150px;      /* Taller than inputs */
    resize: vertical;   /* Allow manual vertical resize */
    margin-bottom: 10px;
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
    <a href="viewFeedback.php" style="background-color: #2a7ca4; color: white;">View Feedback</a>
  <?php endif; ?>
</nav>

  <!-- Main content -->
  <main>
    <div class="feedback-box">
      <h2>We Value Your Feedback</h2>
      <p>Your thoughts help us improve our services.</p>
      
      <form action="submitFeedback.php" method="POST">
        <label for="name">Name</label>
        <input type="text" id="name" name="name" placeholder="Your Name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="your@email.com" required>

        <label for="message">Message</label>
        <textarea id="message" name="message" placeholder="Write your feedback here..." required></textarea>

        <button type="submit">Submit Feedback</button>
      </form>
    </div>
  </main>
</body>
</html>
