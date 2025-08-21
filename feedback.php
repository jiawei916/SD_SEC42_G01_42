<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";
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
                <a href="signIn.html">Sign In</a>
                <a href="registerGuest.html">Register</a>
            <?php endif; ?>
        </div>
    </div>
  </header>

  <!-- Navigation bar -->
  <nav>
    <a href="homepage.php">Homepage</a>
    <a href="aboutUs.php">About</a>
    <a href="contact.php">Contact</a>
    <a href="feedback.php"><strong>Feedback</strong></a>
    <a href="emailVerification.php">Verification</a>
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