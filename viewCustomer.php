<?php
session_start();

// Check if user is logged in and has appropriate role
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'staff')) {
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

// Handle add customer (staff only)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_customer']) && $userRole == 'staff') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        $_SESSION['message'] = "Email already exists!";
        $_SESSION['message_type'] = "error";
    } else {
        $insert_sql = "INSERT INTO users (name, email, password, role, verified, created_at) VALUES (?, ?, ?, 'customer', 1, NOW())";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("sss", $name, $email, $password);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Customer added successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error adding customer: " . $stmt->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }
    $check_email->close();
    
    header("Location: viewCustomer.php");
    exit();
}

// Handle update customer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_customer'])) {
    $customerId = $_POST['customer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    // Check if email already exists (excluding current customer)
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check_email->bind_param("si", $email, $customerId);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        $_SESSION['message'] = "Email already exists!";
        $_SESSION['message_type'] = "error";
    } else {
        $updateSql = "UPDATE users SET name = ?, email = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssi", $name, $email, $customerId);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Customer updated successfully!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Error updating customer: " . $conn->error;
            $_SESSION['message_type'] = "error";
        }
        $stmt->close();
    }
    $check_email->close();
    
    header("Location: viewCustomer.php?customer_id=" . $customerId);
    exit();
}

// Handle delete customer
if (isset($_GET['delete_customer'])) {
    $customerId = $_GET['delete_customer'];
    
    $deleteCustomerSql = "DELETE FROM users WHERE id = ? AND role = 'customer'";
    $stmt = $conn->prepare($deleteCustomerSql);
    $stmt->bind_param("i", $customerId);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Customer deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting customer: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
    
    header("Location: viewCustomer.php");
    exit();
}

// Fetch all customers
$sql = "SELECT id, name, email, role, verified, created_at FROM users WHERE role = 'customer' ORDER BY name";
$result = $conn->query($sql);
$customers = $result->fetch_all(MYSQLI_ASSOC);

// Fetch customer details if a specific customer is selected
$selectedCustomer = null;
if (isset($_GET['customer_id'])) {
    $customerId = $_GET['customer_id'];
    
    // Get customer details
    $stmt = $conn->prepare("SELECT id, name, email, role, verified, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $selectedCustomer = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$conn->close();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Customer Management - VetGroom Hub</title>
    <meta name="description" content="Admin customer management dashboard">
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

        /* Dropdown links */
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
        
        /* Customer list styling */
        .customer-list {
            margin-bottom: 30px;
        }
        
        .customer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .customer-table th, .customer-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .customer-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .customer-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .view-btn {
            background-color: #4a90e2;
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
        
        .view-btn:hover {
            background-color: #357abd;
        }
        
        /* Customer details styling */
        .customer-details {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .customer-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-size: 16px;
            color: #333;
        }
        
        .back-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            margin-bottom: 20px;
            transition: background-color 0.3s;
        }
        
        .back-btn:hover {
            background-color: #5a6268;
        }
        
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
        
        /* Form styling */
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .btn-primary {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #357abd;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .btn-add {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
        }
        
        .btn-add:hover {
            background-color: #218838;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        
        .message {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: 500;
        }
        
        .message-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .edit-form {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .edit-toggle {
            background: none;
            border: none;
            color: #4a90e2;
            cursor: pointer;
            font-size: 14px;
            margin-left: 10px;
        }
        
        .edit-toggle:hover {
            text-decoration: underline;
        }
        
        .add-customer-form {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .customer-info {
                grid-template-columns: 1fr;
            }
            
            .customer-table {
                font-size: 14px;
            }
            
            .customer-table th, .customer-table td {
                padding: 8px 10px;
            }
            
            .action-buttons {
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
        
        .menu-main {
            gap: 30px;
        }
        
        .main-menu {
            margin-right: 20px;
        }
        
        .main-menu ul {
            display: flex;
            gap: 25px;
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
                                            <li><a href="services.php">Services</a></li>
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
                                            <a href="profile.php">Profile</a>
                                            <?php if ($userRole == 'admin'): ?>
                                                <a href="viewDashboardAdmin.php">Dashboard</a>
                                                <a href="viewFeedBack.php">View Feedback</a>
                                                <a href="viewCustomer.php">View Customer</a>
                                                <a href="viewStaff.php">View Staff</a>
                                            <?php elseif ($userRole == 'staff'): ?>
                                                <a href="viewDashboardStaff.php">Dashboard</a>
                                                <a href="viewFeedBack.php">View Feedback</a>
                                                <a href="viewCustomer.php">View Customer</a>
                                            <?php endif; ?>
                                            <a href="signOut.php">Sign Out</a>
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

    <!-- Customer Management Section -->
    <main class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <h2>Customer Management</h2>
                <p>View and manage all registered customers</p>
            </div>

            <!-- Display messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="message message-<?php echo $_SESSION['message_type']; ?>">
                    <?php 
                    echo $_SESSION['message']; 
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if ($userRole == 'staff'): ?>
            <!-- Add Customer Form (Staff only) -->
            <div class="add-customer-form">
                <h3>Add New Customer</h3>
                <form method="POST" action="viewCustomer.php">
                    <input type="hidden" name="add_customer" value="1">
                    
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" class="form-input" id="name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" class="form-input" id="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-input" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn-add">Add Customer</button>
                </form>
            </div>
            <?php endif; ?>

            <?php if ($selectedCustomer): ?>
                <!-- Back button -->
                <button class="back-btn" onclick="window.location.href='viewCustomer.php'">← Back to Customer List</button>

                <!-- Customer Details -->
                <div class="customer-details">
                    <h3>Customer Details 
                        <button class="edit-toggle" onclick="toggleEditForm('customer')">✏️ Edit</button>
                    </h3>
                    
                    <!-- Customer Edit Form -->
                    <form id="customer-edit-form" class="edit-form" method="POST" action="viewCustomer.php" style="display: none;">
                        <input type="hidden" name="customer_id" value="<?php echo $selectedCustomer['id']; ?>">
                        <input type="hidden" name="update_customer" value="1">
                        
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name</label>
                            <input type="text" class="form-input" id="name" name="name" value="<?php echo htmlspecialchars($selectedCustomer['name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input type="email" class="form-input" id="email" name="email" value="<?php echo htmlspecialchars($selectedCustomer['email']); ?>" required>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn-primary">Save Changes</button>
                            <button type="button" class="btn-danger" onclick="toggleEditForm('customer')">Cancel</button>
                        </div>
                    </form>
                    
                    <div class="customer-info">
                        <div class="info-item">
                            <div class="info-label">Customer ID</div>
                            <div class="info-value"><?php echo $selectedCustomer['id']; ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Full Name</div>
                            <div class="info-value"><?php echo htmlspecialchars($selectedCustomer['name']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Email Address</div>
                            <div class="info-value"><?php echo htmlspecialchars($selectedCustomer['email']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Account Status</div>
                            <div class="info-value">
                                <span class="status-badge <?php echo $selectedCustomer['verified'] ? 'status-verified' : 'status-pending'; ?>">
                                    <?php echo $selectedCustomer['verified'] ? 'Verified' : 'Pending Verification'; ?>
                                </span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Role</div>
                            <div class="info-value"><?php echo ucfirst($selectedCustomer['role']); ?></div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Member Since</div>
                            <div class="info-value"><?php echo date('F j, Y', strtotime($selectedCustomer['created_at'])); ?></div>
                        </div>
                    </div>

                    <!-- Action buttons -->
                    <div class="action-buttons">
                        <a href="viewCustomer.php?delete_customer=<?php echo $selectedCustomer['id']; ?>" 
                           class="btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this customer? This action cannot be undone.')">
                            Delete Customer
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <!-- Customer List -->
                <div class="customer-list">
                    <h3>Registered Customers (<?php echo count($customers); ?>)</h3>
                    
                    <?php if (count($customers) > 0): ?>
                        <table class="customer-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Member Since</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td><?php echo $customer['id']; ?></td>
                                        <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $customer['verified'] ? 'status-verified' : 'status-pending'; ?>">
                                                <?php echo $customer['verified'] ? 'Verified' : 'Pending'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($customer['created_at'])); ?></td>
                                        <td>
                                            <a href="viewCustomer.php?customer_id=<?php echo $customer['id']; ?>" class="view-btn">View</a>
                                            <a href="viewCustomer.php?delete_customer=<?php echo $customer['id']; ?>" 
                                               class="btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this customer? This action cannot be undone.')">
                                                Delete
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No customers found.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- JS here -->
    <script src="assets/js/vendor/modernizr-3.5.0.min.js"></script>
    <script src="assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="assets/js/popper.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/jquery.slicknav.min.js"></script>
    <script src="assets/js/owl.carousel.min.js"></script>
    <script src="assets/js/slick.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
    <script src="assets/js/jquery.magnific-popup.js"></script>
    <script src="assets/js/jquery.nice-select.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/contact.js"></script>
    <script src="assets/js/jquery.form.js"></script>
    <script src="assets/js/jquery.validate.min.js"></script>
    <script src="assets/js/mail-script.js"></script>
    <script src="assets/js/jquery.ajaxchimp.min.js"></script>
    <script src="assets/js/plugins.js"></script>
    <script src="assets/js/main.js"></script>
    
    <script>
        // Toggle edit form
        function toggleEditForm(type) {
            const form = document.getElementById(`${type}-edit-form`);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
        
        // Auto-hide messages after 5 seconds
        setTimeout(() => {
            const messages = document.querySelectorAll('.message');
            messages.forEach(message => {
                message.style.display = 'none';
            });
        }, 5000);
    </script>
</body>
</html>