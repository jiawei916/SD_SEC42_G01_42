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
  <title>About Us - VetGroom Hub</title>
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
        max-width: 900px;
        margin: 30px auto;
        padding: 20px;
        line-height: 1.6;
    }

    main h2 {
        color: #3aa9e4;
        margin-bottom: 15px;
    }

    main p {
        margin-bottom: 15px;
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
        <span class="profile-name"><?php echo htmlspecialchars($userName); ?>  ▼</span>
<div class="dropdown-content">
    <?php if (isset($_SESSION['user_role'])): ?>
        <a href="profile.php">Profile</a>
    <?php endif; ?>
<?php if ($_SESSION['user_role'] == 'customer'): ?>
    <a href="bookAppointment.php">Book Appointment</a>
    <a href="viewAppointment.php">View Appointments</a> 
<?php elseif ($_SESSION['user_role'] == 'admin'): ?>
    <a href="viewDashboardAdmin.php">Dashboard</a>
    <a href="viewFeedBack.php">View Feedback</a>
    <a href="viewCustomer.php">View Customer</a>
    <a href="viewStaff.php">View Staff</a>
<?php elseif ($_SESSION['user_role'] == 'staff'): ?>
    <a href="viewDashboardStaff.php">Dashboard</a>
    <a href="viewFeedBack.php">View Feedback</a>
    <a href="viewCustomer.php">View Customer</a>
<?php endif; ?>
<?php if (isset($_SESSION['user_role'])): ?>
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
    <a href="aboutUs.php"><strong>About</strong></a>
    <a href="contact.php">Contact</a>
    <a href="feedback.php">Feedback</a>
    <a href="emailVerification.php">Verification</a>
  </nav>

  <!-- Main content -->
  <main>
    <h2>About Us</h2>
    <p>Welcome to <strong>VetGroom Hub</strong>, your trusted partner in pet care and grooming. Our mission is to create a seamless platform that connects pet owners with professional groomers and veterinary services.</p>
    
    <p>We believe every pet deserves the best care. That’s why we’ve built a hub where owners can book appointments, track grooming schedules, and receive reminders — all in one place.</p>
    
    <p>Our team is passionate about animals and technology, working together to make pet care more accessible, reliable, and stress-free.</p>
    
    <p><strong>Why choose VetGroom Hub?</strong></p>
    <ul>
      <li>✔ Easy-to-use booking system</li>
      <li>✔ Verified and trusted professionals</li>
      <li>✔ Personalized care for your pets</li>
      <li>✔ Secure and user-friendly platform</li>
    </ul>

    <p>Whether you’re a first-time pet owner or a seasoned pet lover, we’re here to support you and your furry friends.</p>
  </main>

</body>
</html>
