<?php
session_start();

// Check login session
$isLoggedIn = isset($_SESSION['user_name']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : "Guest";
$userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

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

// Database connection
$servername = "localhost";
$username = "root"; // Change if needed
$password = ""; // Change if needed
$dbname = "vetgroomhub";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch feedback data
$sql = "SELECT name, email, rating, message, created_at FROM feedback ORDER BY created_at DESC";
$result = $conn->query($sql);

$feedbackData = [];
if ($result->num_rows > 0) {
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
    
    /* Feedback specific styles */
    .feedback-container {
        max-width: 1200px;
        margin: 20px auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    
    .role-notice {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
    }
    
    .admin-notice {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    
    .staff-notice {
        background-color: #cce5ff;
        color: #004085;
        border: 1px solid #b8daff;
    }
    
    .customer-notice {
        background-color: #fff3cd;
        color: #856404;
        border: 1px solid #ffeeba;
    }
    
    .guest-notice {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
    
    .feedback-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }
    
    .feedback-table th, .feedback-table td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    
    .feedback-table th {
        background-color: #3aa9e4;
        color: white;
    }
    
    .feedback-table tr:hover {
        background-color: #f5f5f5;
    }
    
    .rating {
        color: #ffc107;
        font-weight: bold;
    }
    
    .no-access {
        text-align: center;
        padding: 40px;
        color: #d9534f;
    }
    
    .user-info {
        text-align: center;
        margin-bottom: 20px;
        font-style: italic;
        color: #6c757d;
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
  <li class="active"><a href="viewService.php">Services</a></li>
  <li class="active"><a href="feedback.php">Feedback</a></li>
  <a href="emailVerification.php">Verification</a>
  <?php if ($userRole === 'admin' || $userRole === 'staff'): ?>
    <a href="viewFeedback.php" style="background-color: #2a7ca4; color: white;">View Feedback</a>
  <?php endif; ?>
</nav>

<main>
  <div class="feedback-container">
    <h2>Customer Feedback</h2>
    
    <div class="user-info">
      <?php if ($isLoggedIn): ?>
        Logged in as: <?php echo htmlspecialchars($userEmail); ?> 
        (Role: <?php echo ucfirst($userRole); ?>)
      <?php else: ?>
        You are browsing as a guest. <a href="signIn.php">Sign in</a> for more features.
      <?php endif; ?>
    </div>
    
    <?php if ($userRole === 'admin'): ?>
      <!-- Admin view: Feedback listed with name, rating, message. Sorted by date. -->
      <div class="role-notice admin-notice">
        Administrator View: All feedback details sorted by date
      </div>
      
      <table class="feedback-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Customer Name</th>
            <th>Rating</th>
            <th>Message</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feedbackData as $feedback): ?>
          <tr>
            <td><?php echo date('M j, Y g:i A', strtotime($feedback['created_at'])); ?></td>
            <td><?php echo htmlspecialchars($feedback['name']); ?></td>
            <td><span class="rating"><?php echo str_repeat('★', $feedback['rating']); ?></span></td>
            <td><?php echo htmlspecialchars($feedback['message']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
    <?php elseif ($userRole === 'staff'): ?>
      <!-- Staff view: List of feedback shown. Includes customer name, comment, rating. -->
      <div class="role-notice staff-notice">
        Staff View: Customer feedback with name, rating, and comment
      </div>
      
      <table class="feedback-table">
        <thead>
          <tr>
            <th>Customer Name</th>
            <th>Rating</th>
            <th>Comment</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($feedbackData as $feedback): ?>
          <tr>
            <td><?php echo htmlspecialchars($feedback['name']); ?></td>
            <td><span class="rating"><?php echo str_repeat('★', $feedback['rating']); ?></span></td>
            <td><?php echo htmlspecialchars($feedback['message']); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      
    <?php elseif ($userRole === 'customer'): ?>
      <!-- Customer view -->
      <div class="role-notice customer-notice">
        Customer View: You don't have access to view feedback data.
      </div>
      
      <div class="no-access">
        <h3>Access Denied</h3>
        <p>You need staff or administrator privileges to view this page.</p>
        <p>If you believe this is an error, please contact support.</p>
      </div>
      
    <?php else: ?>
      <!-- Guest view -->
      <div class="role-notice guest-notice">
        Guest View: Please sign in to access system features.
      </div>
      
      <div class="no-access">
        <h3>Access Restricted</h3>
        <p>You need to <a href="signIn.php">sign in</a> to view this page.</p>
        <p>If you don't have an account, you can <a href="registerGuest.php">register here</a>.</p>
      </div>
    <?php endif; ?>
  </div>
</main>

</body>
</html>