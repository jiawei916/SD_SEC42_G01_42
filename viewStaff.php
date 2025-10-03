<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
    header("Location: signIn.php");
    exit();
}

$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';
$isLoggedIn = isset($_SESSION['user_name']);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetgroomlist";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete staff request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Prevent admin from deleting themselves
    if ($delete_id != $_SESSION['user_id']) {
        $delete_sql = "DELETE FROM users WHERE id = ? AND role IN ('staff', 'admin')";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $delete_id);
        
        if ($stmt->execute()) {
            $success_message = "Staff member deleted successfully!";
        } else {
            $error_message = "Error deleting staff member: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $error_message = "You cannot delete your own account!";
    }
}
$errors = [];
$success_message = "";
// Handle add staff form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_staff'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $passwordInput = $_POST['password'];

    // Validation
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    }

    if (empty($passwordInput)) {
        $errors['password'] = "Password is required";
    }

    if (empty($role)) {
        $errors['role'] = "Role is required";
    }

    // If no validation errors, proceed
    if (empty($errors)) {
        $password = password_hash($passwordInput, PASSWORD_DEFAULT);

        // Check if email already exists
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $error_message = "Email already exists!";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO users (username, email, password, role, verified) 
                VALUES (?, ?, ?, ?, 1)
            ");
            $stmt->bind_param("ssss", $name, $email, $password, $role);

            if ($stmt->execute()) {
                $success_message = "Staff member added successfully!";
            } else {
                $error_message = "Error adding staff member: " . $stmt->error;
            }
            $stmt->close();
        }
        $check_email->close();
    }
}

// Handle edit staff form submission


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_staff'])) {
    $edit_id = $_POST['edit_id'];
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $role    = $_POST['role'];

    // ✅ Validation
    if (empty($name)) {
        $errors['name'] = "Name is required";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address";
    } else {
        // Check if email already exists (excluding current user)
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_email->bind_param("si", $email, $edit_id);
        $check_email->execute();
        $check_email->store_result();

        if ($check_email->num_rows > 0) {
            $errors['email'] = "Email already exists!";
        }

        $check_email->close();
    }

    if (empty($role)) {
        $errors['role'] = "Role is required";
    }

    // ✅ If no errors, update DB
    if (empty($errors)) {
        $update_sql = "UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $name, $email, $role, $edit_id);

        if ($stmt->execute()) {
            $success_message = "Staff member updated successfully!";
        } else {
            $errors['general'] = "Error updating staff member: " . $stmt->error;
        }

        $stmt->close();
    }
}

// Fetch all staff members (staff and admin roles)
$sql = "SELECT id, username, role, email, verified FROM users WHERE role IN ('staff', 'admin') ORDER BY role, username";
$result = $conn->query($sql);
$staffMembers = $result->fetch_all(MYSQLI_ASSOC);

// Get staff member for editing if requested
$editStaff = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $edit_sql = "SELECT id, username, email, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($edit_sql);
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_result = $stmt->get_result();
    $editStaff = $edit_result->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Staff Management - VetGroom Hub</title>
    <meta name="description" content="Admin staff management dashboard">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">

    <!-- CSS here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slicknav.css">
    <link rel="stylesheet" href="assets/css/flaticon.css">
    <link rel="stylesheet" href="assets/css/animate.min.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/themify-icons.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/nice-select.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            opacity: 0;
            animation: fadeInAnimation ease 1s;
            animation-fill-mode: forwards;
            background-image: url('assets/img/hero/hero2.png'); 
            background-repeat: no-repeat; 
            background-attachment: fixed; 
            background-size: cover; 
            background-position: center;
        }

        @keyframes fadeInAnimation {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        /* Dashboard container styling */
        .dashboard-container {
            padding: 20px;
            max-width: 1400px;
            margin: 80px auto;
        }
        
        .dashboard-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 20px 0;
        }
        
        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .dashboard-header h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
            font-weight: 700;
        }

                /* Dropdown container */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Use existing .header-btn styling */
        .dropdown > .header-btn {
            display: inline-block;
            text-align: center;
        }

        /* Dropdown box */
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            top: 100%;
            background: #fff;
            width: 100%;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            border-radius: 6px;
            z-index: 1000;
        }

        .dropdown-content a {
            color: #333;
            padding: 10px 14px;
            text-decoration: none;
            display: block;
            transition: background 0.2s ease;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Show dropdown on hover */
        .dropdown:hover .dropdown-content {
            display: block;
        }
        
        /* Form styling */
        .staff-form {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .form-input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        
        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        /* Staff list styling */
        .staff-list {
            margin-bottom: 30px;
        }
        
        .staff-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .staff-table th, .staff-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .staff-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .staff-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .action-btn {
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
        }
        
        .action-btn:hover {
            opacity: 0.9;
        }
        
        .btn-edit {
            background-color: #28a745;
        }
        
        .btn-delete {
            background-color: #dc3545;
        }
        
        .btn-add {
            background-color: #4a90e2;
            padding: 10px 20px;
            margin-bottom: 20px;
        }
        
        .btn-cancel {
            background-color: #6c757d;
            padding: 10px 20px;
            text-decoration: none;
            display: inline-block;
        }
        
        /* Status badges */
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .status-verified {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .role-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .role-admin {
            background-color: #6f42c1;
            color: white;
        }
        
        .role-staff {
            background-color: #20c997;
            color: white;
        }
        
        /* Stats cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: 700;
            color: #4a90e2;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        
        /* Message alerts */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        @media (max-width: 768px) {
            .staff-table {
                font-size: 14px;
            }
            
            .staff-table th, .staff-table td {
                padding: 8px 10px;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .form-buttons {
                flex-direction: column;
            }
        }
        
        /* Header adjustments */
        .header-area {
            position: relative;
        }
        
        .main-header {
            padding: 10px 0;
        }
        
        .main-menu ul {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            align-items: center;
        }
        
        .main-menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .main-menu a:hover {
            color: #f8f9fa;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .container-fluid {
            padding: 0 20px;
        }
        
        .no-staff {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <!-- Header Start -->
    <header>
        <div class="header-area header-transparent">
            <div class="main-header header-sticky">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <!-- Logo -->
                        <div class="col-xl-2 col-lg-2 col-md-1">
                            <div class="logo">
                                <a href="homepage.php"><img src="assets/img/logo/logo.png" alt="VetGroom Hub"></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <!-- Main-menu -->
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav> 
                                        <ul id="navigation">
                                            <li><a href="homepage.php">Home</a></li>
                                            <li><a href="aboutUs.php">About</a></li>
                                            <li><a href="viewService.php">Services</a></li>
                                            <li><a href="feedback.php">Feedback</a></li>
                                            <li><a href="contact.php">Contact</a></li>
                                        </ul>
                                    </nav>
                                </div>
                                <!-- Dropdown -->
                                <div class="header-right-btn f-right d-none d-lg-block ml-30">
                                    <div class="dropdown">
                                        <a href="#" class="header-btn">
                                            Welcome, <?php echo htmlspecialchars($userName); ?> ▼
                                        </a>
<div class="dropdown-content">
    <?php if (isset($_SESSION['user_role'])): ?>
        <a href="profile.php">Profile</a>
    <?php endif; ?>
<?php if ($_SESSION['user_role'] == 'customer'): ?>
    <a href="bookAppointment.php">Book Appointment</a>
    <a href="viewAppointment.php">View Appointments</a> 
    <a href="viewReceipt.php">View Receipt</a> 
<?php elseif ($_SESSION['user_role'] == 'admin'): ?>
    <a href="viewDashboardAdmin.php">Dashboard</a>
    <a href="viewFeedBack.php">View Feedback</a>
    <a href="viewCustomer.php">View Customer</a>
    <a href="viewStaff.php">View Staff</a>
    <a href="viewAppointment.php">View Appointments</a> 
    <a href="viewSalesReport.php">View Sales Report</a> 
    <a href="viewReceipt.php">View Receipt</a> 
<?php elseif ($_SESSION['user_role'] == 'staff'): ?>
    <a href="viewDashboardStaff.php">Dashboard</a>
    <a href="viewFeedBack.php">View Feedback</a>
    <a href="viewCustomer.php">View Customer</a>
    <a href="viewAppointment.php">View Appointments</a> 
    <a href="viewSalesReport.php">View Sales Report</a> 
<?php endif; ?>
<?php if (isset($_SESSION['user_role'])): ?>
    <a href="signOut.php">Sign Out</a>
<?php else: ?>
    <a href="signIn.php">Sign In</a>
    <a href="registerGuest.php">Register</a>
<?php endif; ?>
</div>
                                    </div>
                                </div>
                            </div>
                        </div>   
                        <!-- Mobile Menu -->
                        <div class="col-12">
                            <div class="mobile_menu d-block d-lg-none"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- Header End -->

    <!-- Staff Management Section -->
    <main class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <h2>Staff Management</h2>
                <p>Manage all staff members and administrators</p>
            </div>

            <!-- Display messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-error"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <?php
            $adminCount = 0;
            $staffCount = 0;
            $verifiedCount = 0;
            
            foreach ($staffMembers as $member) {
                if ($member['role'] == 'admin') $adminCount++;
                if ($member['role'] == 'staff') $staffCount++;
                if ($member['verified']) $verifiedCount++;
            }
            ?>
            
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($staffMembers); ?></div>
                    <div class="stat-label">Total Staff</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $adminCount; ?></div>
                    <div class="stat-label">Administrators</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $staffCount; ?></div>
                    <div class="stat-label">Staff Members</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $verifiedCount; ?></div>
                    <div class="stat-label">Verified Accounts</div>
                </div>
            </div>

            <!-- Add/Edit Staff Form -->
            <div class="staff-form">
                <h3><?php echo isset($editStaff) ? 'Edit Staff Member' : 'Add New Staff Member'; ?></h3>
                <form method="POST" action="viewStaff.php" novalidate>
                    <?php if (isset($editStaff)): ?>
                        <input type="hidden" name="edit_id" value="<?php echo $editStaff['id']; ?>">
                        <input type="hidden" name="edit_staff" value="1">
                    <?php else: ?>
                        <input type="hidden" name="add_staff" value="1">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-input" name="name" 
                               value="<?php echo isset($editStaff) ? htmlspecialchars($editStaff['username']) : ''; ?>" 
                               required>
                    </div>
                    <?php if (!empty($errors['name'])): ?>
                        <div class="text-danger"><?php echo $errors['name']; ?></div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-input" name="email" 
                               value="<?php echo isset($editStaff) ? htmlspecialchars($editStaff['email']) : ''; ?>" 
                               required>
                    </div>
                    <?php if (!empty($errors['email'])): ?>
                        <div class="text-danger"><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                    
                    <?php if (!isset($editStaff)): ?>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-input" name="password" required>
                    </div>
                    <?php if (!empty($errors['password'])): ?>
                        <div class="text-danger"><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select class="form-input" name="role" required>
                            <option value="staff" <?php echo (isset($editStaff) && $editStaff['role'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                            <option value="admin" <?php echo (isset($editStaff) && $editStaff['role'] == 'admin') ? 'selected' : ''; ?>>Administrator</option>
                        </select>
                    </div>
                    <?php if (!empty($errors['role'])): ?>
                        <div class="text-danger"><?php echo $errors['role']; ?></div>
                    <?php endif; ?>
                    
                    <div class="form-buttons">
                        <button type="submit" class="action-btn btn-add">
                            <?php echo isset($editStaff) ? 'Update Staff' : 'Add Staff'; ?>
                        </button>
                        
                        <?php if (isset($editStaff)): ?>
                            <a href="viewStaff.php" class="btn-cancel">Cancel</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Staff List -->
            <div class="staff-list">
                <h3>Staff Members (<?php echo count($staffMembers); ?>)</h3>
                
                <?php if (count($staffMembers) > 0): ?>
                    <table class="staff-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staffMembers as $staff): ?>
                                <tr>
                                    <td><?php echo $staff['id']; ?></td>
                                    <td><?php echo htmlspecialchars($staff['username']); ?></td>
                                    <td>
                                        <span class="role-badge <?php echo $staff['role'] == 'admin' ? 'role-admin' : 'role-staff'; ?>">
                                            <?php echo ucfirst($staff['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $staff['verified'] ? 'status-verified' : 'status-pending'; ?>">
                                            <?php echo $staff['verified'] ? 'Verified' : 'Pending'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="viewStaff.php?edit_id=<?php echo $staff['id']; ?>" class="action-btn btn-edit">Edit</a>
                                        <a href="viewStaff.php?delete_id=<?php echo $staff['id']; ?>" 
                                           class="action-btn btn-delete" 
                                           onclick="return confirm('Are you sure you want to delete this staff member?')">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-staff">
                        <h3>No staff members found</h3>
                        <p>Click "Add Staff" to add your first staff member.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-area footer-padding">
            <div class="container">
                <div class="row d-flex justify-content-between">
                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                        <div class="single-footer-caption mb-50">
                            <div class="footer-logo mb-25">
                                <a href="homepage.php"><img src="assets/img/logo/logo2.png" alt="VetGroom Hub"></a>
                            </div>
                            <div class="footer-tittle">
                                <p>Professional grooming and veterinary services for your beloved pets.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom-area">
            <div class="container">
                <div class="footer-border">
                    <div class="row d-flex align-items-center">
                        <div class="col-xl-12">
                            <div class="footer-copy-right text-center">
                                <p>&copy; <?php echo date("Y"); ?> VetGroom Hub. All Rights Reserved.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JS -->
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery.slicknav.min.js"></script>
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/slick.min.js"></script>
    <script src="./assets/js/main.js"></script>
</body>

</html>
