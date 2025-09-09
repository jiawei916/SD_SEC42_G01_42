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

// Check if pets table exists, if not create it
$tableCheck = $conn->query("SHOW TABLES LIKE 'pets'");
if ($tableCheck->num_rows == 0) {
    // Create pets table
    $createPetsSQL = "CREATE TABLE pets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        species VARCHAR(50) NOT NULL,
        breed VARCHAR(100),
        age INT,
        weight DECIMAL(5,2),
        medical_notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    if (!$conn->query($createPetsSQL)) {
        die("Error creating pets table: " . $conn->error);
    }
    
    // Insert sample pet data
    $samplePets = [
        [2, 'Buddy', 'Dog', 'Golden Retriever', 3, 25.5, 'Allergic to chicken'],
        [3, 'Whiskers', 'Cat', 'Siamese', 5, 4.2, 'Regular checkups needed'],
        [39, 'Speedy', 'Rabbit', 'Holland Lop', 2, 1.8, 'None'],
        [40, 'Max', 'Dog', 'Labrador', 4, 28.0, 'None'],
        [69, 'Coco', 'Bird', 'Parrot', 1, 0.5, 'None'],
        [70, 'Milo', 'Cat', 'Maine Coon', 2, 5.1, 'None']
    ];
    
    $stmt = $conn->prepare("INSERT INTO pets (user_id, name, species, breed, age, weight, medical_notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    foreach ($samplePets as $pet) {
        $stmt->bind_param("isssids", $pet[0], $pet[1], $pet[2], $pet[3], $pet[4], $pet[5], $pet[6]);
        $stmt->execute();
    }
    $stmt->close();
}

// Handle update customer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_customer']) && $userRole == 'admin') {
    $customerId = $_POST['customer_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    
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
    
    header("Location: viewCustomer.php?customer_id=" . $customerId);
    exit();
}

// Handle update pet
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pet']) && $userRole == 'admin') {
    $petId = $_POST['pet_id'];
    $name = $_POST['pet_name'];
    $species = $_POST['species'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $weight = $_POST['weight'];
    $medical_notes = $_POST['medical_notes'];
    
    $updateSql = "UPDATE pets SET name = ?, species = ?, breed = ?, age = ?, weight = ?, medical_notes = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssidssi", $name, $species, $breed, $age, $weight, $medical_notes, $petId);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Pet updated successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error updating pet: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
    
    header("Location: viewCustomer.php?customer_id=" . $_POST['customer_id']);
    exit();
}

// Handle delete customer
if (isset($_GET['delete_customer']) && $userRole == 'admin') {
    $customerId = $_GET['delete_customer'];
    
    // First delete associated pets
    $deletePetsSql = "DELETE FROM pets WHERE user_id = ?";
    $stmt = $conn->prepare($deletePetsSql);
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $stmt->close();
    
    // Then delete the customer
    $deleteCustomerSql = "DELETE FROM users WHERE id = ?";
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

// Handle delete pet
if (isset($_GET['delete_pet']) && $userRole == 'admin') {
    $petId = $_GET['delete_pet'];
    $customerId = $_GET['customer_id'];
    
    $deleteSql = "DELETE FROM pets WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $petId);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Pet deleted successfully!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error deleting pet: " . $conn->error;
        $_SESSION['message_type'] = "error";
    }
    $stmt->close();
    
    header("Location: viewCustomer.php?customer_id=" . $customerId);
    exit();
}

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch all customers
$sql = "SELECT id, name, email, role, verified FROM users WHERE role = 'customer'";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

$sql .= " ORDER BY name";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$customers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch customer details if a specific customer is selected
$selectedCustomer = null;
$customerPets = [];
if (isset($_GET['customer_id'])) {
    $customerId = $_GET['customer_id'];
    
    // Get customer details
    $stmt = $conn->prepare("SELECT id, name, email, role, verified, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $customerId);
    $stmt->execute();
    $selectedCustomer = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    // Get customer's pets
    if ($selectedCustomer) {
        $stmt = $conn->prepare("SELECT * FROM pets WHERE user_id = ?");
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $customerPets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
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
        
        /* Search bar styling */
        .search-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        
        .search-btn {
            padding: 10px 20px;
            background-color: #4a90e2;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .search-btn:hover {
            background-color: #357abd;
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
        
        /* Pets section styling */
        .pets-section {
            margin-top: 30px;
        }
        
        .pets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 15px;
        }
        
        .pet-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .pet-name {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .pet-details {
            color: #666;
            margin-bottom: 8px;
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
        
        .form-textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            min-height: 100px;
            resize: vertical;
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
        
        @media (max-width: 768px) {
            .customer-info {
                grid-template-columns: 1fr;
            }
            
            .pets-grid {
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

    <!-- ✅ Header Start -->
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
    <!-- ✅ Header End -->

    <!-- ✅ Customer Management Section -->
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

            <!-- Search Section -->
            <form method="GET" action="viewCustomer.php" class="search-container">
                <input type="text" class="search-input" placeholder="Search customers by name or email..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                <button class="search-btn" type="submit">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="viewCustomer.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>

            <?php if ($selectedCustomer): ?>
                <!-- Back button -->
                <button class="back-btn" onclick="window.location.href='viewCustomer.php'">← Back to Customer List</button>

                <!-- Customer Details -->
                <div class="customer-details">
                    <h3>Customer Details 
                        <?php if ($userRole == 'admin'): ?>
                            <button class="edit-toggle" onclick="toggleEditForm('customer')">✏️ Edit</button>
                        <?php endif; ?>
                    </h3>
                    
                    <!-- Customer Edit Form (Admin only) -->
                    <?php if ($userRole == 'admin'): ?>
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
                    <?php endif; ?>
                    
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

                    <!-- Admin actions -->
                    <?php if ($userRole == 'admin'): ?>
                    <div class="action-buttons">
                        <a href="viewCustomer.php?delete_customer=<?php echo $selectedCustomer['id']; ?>" 
                           class="btn-danger" 
                           onclick="return confirm('Are you sure you want to delete this customer? This action cannot be undone.')">
                            Delete Customer
                        </a>
                    </div>
                    <?php endif; ?>

                    <!-- Pets Information -->
                    <div class="pets-section">
                        <h3>Pet Information</h3>
                        <?php if (count($customerPets) > 0): ?>
                            <div class="pets-grid">
                                <?php foreach ($customerPets as $pet): ?>
                                    <div class="pet-card">
                                        <h4 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?>
                                            <?php if ($userRole == 'admin'): ?>
                                                <button class="edit-toggle" onclick="togglePetEditForm(<?php echo $pet['id']; ?>)">✏️ Edit</button>
                                            <?php endif; ?>
                                        </h4>
                                        
                                        <!-- Pet Edit Form (Admin only) -->
                                        <?php if ($userRole == 'admin'): ?>
                                        <form id="pet-edit-form-<?php echo $pet['id']; ?>" class="edit-form" method="POST" action="viewCustomer.php" style="display: none;">
                                            <input type="hidden" name="pet_id" value="<?php echo $pet['id']; ?>">
                                            <input type="hidden" name="customer_id" value="<?php echo $selectedCustomer['id']; ?>">
                                            <input type="hidden" name="update_pet" value="1">
                                            
                                            <div class="form-group">
                                                <label class="form-label" for="pet_name_<?php echo $pet['id']; ?>">Pet Name</label>
                                                <input type="text" class="form-input" id="pet_name_<?php echo $pet['id']; ?>" name="pet_name" value="<?php echo htmlspecialchars($pet['name']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label" for="species_<?php echo $pet['id']; ?>">Species</label>
                                                <input type="text" class="form-input" id="species_<?php echo $pet['id']; ?>" name="species" value="<?php echo htmlspecialchars($pet['species']); ?>" required>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label" for="breed_<?php echo $pet['id']; ?>">Breed</label>
                                                <input type="text" class="form-input" id="breed_<?php echo $pet['id']; ?>" name="breed" value="<?php echo htmlspecialchars($pet['breed'] ?? ''); ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label" for="age_<?php echo $pet['id']; ?>">Age</label>
                                                <input type="number" class="form-input" id="age_<?php echo $pet['id']; ?>" name="age" value="<?php echo $pet['age']; ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label" for="weight_<?php echo $pet['id']; ?>">Weight (kg)</label>
                                                <input type="number" step="0.1" class="form-input" id="weight_<?php echo $pet['id']; ?>" name="weight" value="<?php echo $pet['weight']; ?>">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="form-label" for="medical_notes_<?php echo $pet['id']; ?>">Medical Notes</label>
                                                <textarea class="form-textarea" id="medical_notes_<?php echo $pet['id']; ?>" name="medical_notes"><?php echo htmlspecialchars($pet['medical_notes'] ?? ''); ?></textarea>
                                            </div>
                                            
                                            <div class="action-buttons">
                                                <button type="submit" class="btn-primary">Save Changes</button>
                                                <button type="button" class="btn-danger" onclick="togglePetEditForm(<?php echo $pet['id']; ?>)">Cancel</button>
                                            </div>
                                        </form>
                                        <?php endif; ?>
                                        
                                        <div class="pet-details"><strong>Species:</strong> <?php echo htmlspecialchars($pet['species']); ?></div>
                                        <div class="pet-details"><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed'] ?? 'Not specified'); ?></div>
                                        <div class="pet-details"><strong>Age:</strong> <?php echo $pet['age']; ?> years</div>
                                        <div class="pet-details"><strong>Weight:</strong> <?php echo $pet['weight']; ?> kg</div>
                                        <?php if (!empty($pet['medical_notes'])): ?>
                                            <div class="pet-details"><strong>Medical Notes:</strong> <?php echo htmlspecialchars($pet['medical_notes']); ?></div>
                                        <?php endif; ?>
                                        
                                        <!-- Admin actions for pet -->
                                        <?php if ($userRole == 'admin'): ?>
                                        <div class="action-buttons">
                                            <a href="viewCustomer.php?delete_pet=<?php echo $pet['id']; ?>&customer_id=<?php echo $selectedCustomer['id']; ?>" 
                                               class="btn-danger" 
                                               onclick="return confirm('Are you sure you want to delete this pet? This action cannot be undone.')">
                                                Delete Pet
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p>No pets registered for this customer.</p>
                        <?php endif; ?>
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
                                        <td>
                                            <a href="viewCustomer.php?customer_id=<?php echo $customer['id']; ?>" class="view-btn">View Details</a>
                                            <?php if ($userRole == 'admin'): ?>
                                                <a href="viewCustomer.php?delete_customer=<?php echo $customer['id']; ?>" 
                                                   class="btn-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this customer? This action cannot be undone.')">
                                                    Delete
                                                </a>
                                            <?php endif; ?>
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
        // Toggle edit forms
        function toggleEditForm(type) {
            const form = document.getElementById(`${type}-edit-form`);
            if (form.style.display === 'none') {
                form.style.display = 'block';
            } else {
                form.style.display = 'none';
            }
        }
        
        function togglePetEditForm(petId) {
            const form = document.getElementById(`pet-edit-form-${petId}`);
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