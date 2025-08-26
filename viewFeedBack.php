<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

// Determine user role
if (!$isLoggedIn) {
    $userRole = 'guest';
} else {
    $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'customer';
}

// Database connection
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "vetgroomlist";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch feedback
$sql = "SELECT username, email, feedback, created_at FROM feedback ORDER BY created_at DESC";
$result = $conn->query($sql);
$feedbackData = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feedbackData[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>View Feedback - VetGroom Hub</title>
  <link rel="stylesheet" href="style.css">
  <style>
        .profile-dropdown {
        position: absolute;
        top: 15px;
        right: 20px;
        display: flex;
        align-items: center;
        cursor: pointer;
        background-color: #3aa9e4;   /* distinct background */
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

    /* Ensure header is positioned relative so dropdown can be placed */
    header {
        position: relative;
        padding: 15px;
        color: white;
        text-align: center;
    }
    /* Feedback page specific */
    .feedback-container {
        max-width: 1000px;
        margin: 30px auto;
        padding: 20px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .feedback-container h2 {
        text-align: center;
        margin-bottom: 15px;
        color: #333;
    }
    .feedback-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    .feedback-table th, .feedback-table td {
        padding: 12px 15px;
        border-bottom: 1px solid #ddd;
        text-align: left;
    }
    .feedback-table th {
        background-color: #3aa9e4;
        color: white;
    }
    .feedback-table tr:hover {
        background-color: #f9f9f9;
    }
    .role-notice {
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    }
    .admin-notice { background:#d4edda; color:#155724; border:1px solid #c3e6cb; }
    .staff-notice { background:#cce5ff; color:#004085; border:1px solid #b8daff; }
    .customer-notice { background:#fff3cd; color:#856404; border:1px solid #ffeeba; }
    .guest-notice { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; }
    .no-access {
        text-align: center;
        padding: 40px;
        color: #d9534f;
    }
    .user-info {
        text-align: center;
        margin-bottom: 15px;
        color: #6c757d;
        font-style: italic;
    }
  </style>
</head>
<body>

<header>
  <h1>VetGroom Hub</h1>
  <!-- Profile Dropdown -->
  <div class="profile-dropdown">
      <span class="profile-icon">👤</span>
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
    <a href="viewFeedback.php" style="background:#2a7ca4; color:white;">View Feedback</a>
  <?php endif; ?>
</nav>

<main>
  <div class="feedback-container">
    <h2>Customer Feedback</h2>
    
    <div class="user-info">
      <?php if ($isLoggedIn): ?>
        Logged in as <?php echo htmlspecialchars($userName); ?> (Role: <?php echo ucfirst($userRole); ?>)
      <?php else: ?>
        You are browsing as a guest. <a href="signIn.php">Sign in</a> for more features.
      <?php endif; ?>
    </div>

    <?php if ($userRole === 'admin'): ?>
      <div class="role-notice admin-notice">Admin View: Full feedback details</div>
      <table class="feedback-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Email</th>
            <th>Feedback</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feedbackData as $fb): ?>
          <tr>
            <td><?php echo date("M j, Y g:i A", strtotime($fb['created_at'])); ?></td>
            <td><?php echo htmlspecialchars($fb['username']); ?></td>
            <td><?php echo htmlspecialchars($fb['email']); ?></td>
            <td><?php echo htmlspecialchars($fb['feedback']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    <?php elseif ($userRole === 'staff'): ?>
      <div class="role-notice staff-notice">Staff View: Limited feedback details</div>
      <table class="feedback-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Feedback</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feedbackData as $fb): ?>
          <tr>
            <td><?php echo htmlspecialchars($fb['username']); ?></td>
            <td><?php echo htmlspecialchars($fb['feedback']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    <?php elseif ($userRole === 'customer'): ?>
      <div class="role-notice customer-notice">Customer View</div>
      <div class="no-access">
        <h3>Access Denied</h3>
        <p>Only staff and admins can view feedback.</p>
      </div>

    <?php else: ?>
      <div class="role-notice guest-notice">Guest View</div>
      <div class="no-access">
        <h3>Access Restricted</h3>
        <p>You need to <a href="signIn.php">sign in</a> to view this page.</p>
      </div>
    <?php endif; ?>
  </div>
</main>

</body>
</html>
