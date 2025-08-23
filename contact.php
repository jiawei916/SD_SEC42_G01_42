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
  <title>Contact - VetGroom Hub</title>
  <link rel="stylesheet" href="style.css">
  <style>
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

    header {
        position: relative;
        padding: 15px;
        color: white;
        text-align: center;
    }

    main {
        max-width: 800px;
        margin: 30px auto;
        padding: 20px;
        line-height: 1.6;
    }

    main h2 {
        color: #3aa9e4;
        margin-bottom: 15px;
    }

    main p {
        margin-bottom: 10px;
    }

    .contact-info {
        margin-top: 20px;
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
    }

    .contact-info p {
        font-size: 16px;
    }

    .contact-info strong {
        color: #3aa9e4;
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
        <span class="profile-name"><?php echo htmlspecialchars($userName); ?></span>
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
    <a href="contact.php"><strong>Contact</strong></a>
    <a href="registerGuest.html">Register</a>
    <a href="emailVerification.html">Verification</a>
  </nav>

  <!-- Main content -->
  <main>
    <h2>Contact Us</h2>
    <p>Have questions or need assistance? We’d love to hear from you! You can reach us through the details below:</p>
    
    <div class="contact-info">
      <p><strong>Phone:</strong> +1 (555) 123-4567</p>
      <p><strong>Email:</strong> support@vetgroomhub.fake</p>
      <p><strong>Address:</strong> 123 Pet Care Street, Grooming City, PA 12345</p>
    </div>

    <p>Our support team is available Monday–Friday, 9:00 AM to 6:00 PM. We aim to respond to all inquiries within 24 hours.</p>
  </main>

</body>
</html>
