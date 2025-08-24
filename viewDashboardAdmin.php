<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Extra styles specific to dashboard (while reusing global style.css) */
    .cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      text-align: center;
      box-shadow: 0px 2px 6px rgba(0,0,0,0.1);
    }

    .card h2 {
      margin: 0;
      font-size: 2em;
      color: #004d80;
    }

    .card p {
      margin: 10px 0 0;
      color: #666;
    }

    .nav-buttons {
      display: flex;
      justify-content: center;
      gap: 15px;
      flex-wrap: wrap;
      margin-top: 20px;
    }

    .nav-buttons button {
      background: #0077cc;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .nav-buttons button:hover {
      background: #005fa3;
    }

    .back-btn {
      margin-top: 30px;
      text-align: center;
    }

    .back-btn a {
      text-decoration: none;
      background: #2ecc71;
      color: white;
      padding: 10px 20px;
      border-radius: 6px;
      transition: background 0.3s;
    }

    .back-btn a:hover {
      background: #27ae60;
    }
    /* Profile dropdown (copied from Staff Dashboard) */
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
    
    /* Welcome message styling */
    .welcome-message {
      text-align: center;
      margin-bottom: 30px;
    }
  </style>
</head>
<body>

  <!-- Header -->
  <header>
    <h1>Admin Dashboard</h1>
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
    <a href="appointmentsAdmin.html">Appointments</a>
    <a href="customersAdmin.html">Customers</a>
    <a href="salesAdmin.html">Sales</a>
    <a href="feedbackAdmin.html">Feedback</a>
  </nav>
  
  <!-- Main dashboard content -->
  <main>

    <!-- Welcome message from staff dashboard -->
     <div class="welcome-message">
      <h2>Welcome, Admin!</h2>
      <p>View appointments, customers, sales and feedback from here.</p>
    </div>

    <!-- Dashboard Stats -->
    <div class="cards">
      <div class="card">
        <h2>25</h2>
        <p>Total Appointments</p>
      </div>
      <div class="card">
        <h2>120</h2>
        <p>Total Customers</p>
      </div>
      <div class="card">
        <h2>$8,500</h2>
        <p>Total Sales</p>
      </div>
      <div class="card">
        <h2>35</h2>
        <p>Feedbacks</p>
      </div>
    </div>

    <!-- Quick Access Buttons -->
    <div class="nav-buttons">
      <button onclick="location.href='appointmentsAdmin.html'">Appointments</button>
      <button onclick="location.href='customersAdmin.html'">Customers</button>
      <button onclick="location.href='salesAdmin.html'">Sales</button>
      <button onclick="location.href='feedbackAdmin.html'">Feedback</button>
    </div>

    <!-- Back Button -->
    <div class="back-btn">
      <a href="homepage.php">⬅ Back to Homepage</a>
    </div>
  </main>

</body>
</html>

