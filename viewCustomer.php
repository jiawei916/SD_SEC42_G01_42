<?php
session_start();



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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_customer'])) {
    $deleteId = intval($_POST['delete_id']);

    // First delete pets linked to this customer (to avoid foreign key error)
    $stmt = $conn->prepare("DELETE FROM pets WHERE user_id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();

    // Then delete customer
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    $stmt->execute();
    $stmt->close();

    echo "success";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_customer'])) {
    $editId = intval($_POST['edit_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // ✅ Basic validation
    if (empty($name) || empty($email)) {
        echo "error";
        exit;
    }

    // ✅ Prevent duplicate emails
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->bind_param("si", $email, $editId);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "duplicate_email";
        exit;
    }
    $check->close();

    // ✅ Update customer
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $editId);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    exit;
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

// Handle search
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch all customers
$sql = "SELECT id, username, email, role, verified FROM users WHERE role = 'customer'";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (username LIKE ? OR email LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

$sql .= " ORDER BY username";

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
    $stmt = $conn->prepare("SELECT id, name, email, role, verified FROM users WHERE id = ?");
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
        }
        
        /* Header adjustments */
        .header-area {
            position: relative;
        }
        
        .main-header {
            padding: 10px 0;
        }
        
        .main-menu a:hover {
            color: #f8f9fa;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .container-fluid {
            padding: 0 20px;
        }
        
/* Overlay (dimmed background) */
.modal-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 9999;
}

/* Popup box */
.modal-content {
    background: #ffffff;
    border-radius: 12px;
    padding: 30px;
    width: 420px;
    max-width: 95%;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    animation: fadeInUp 0.3s ease;
}

/* Heading */
.modal-content h3 {
    margin-bottom: 20px;
    font-size: 20px;
    font-weight: 700;
    color: #333;
    text-align: center;
}

/* Form inputs */
.customer-form .form-group {
    margin-bottom: 15px;
}

.customer-form .form-label {
    font-weight: 600;
    font-size: 14px;
    color: #555;
    display: block;
    margin-bottom: 6px;
}

.customer-form .form-input {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 15px;
    transition: border-color 0.2s ease;
}

.customer-form .form-input:focus {
    border-color: #4a90e2;
    outline: none;
    box-shadow: 0 0 0 2px rgba(74,144,226,0.15);
}

/* Buttons */
.form-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

.action-btn {
    background-color: #4a90e2;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
}

.action-btn:hover {
    background-color: #357abd;
}

.btn-cancel {
    background-color: #6c757d;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 6px;
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-cancel:hover {
    background-color: #5a6268;
}

/* Animation */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(40px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
        .error-message {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
            display: block;
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
                                            <li class="active"><a href="viewService.php">Services</a></li>
                                            <li class="active"><a href="feedback.php">Feedback</a></li>
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
<?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'customer'): ?>
    <a href="bookAppointment.php">Book Appointment</a>
    <a href="viewAppointment.php">View Appointments</a> 
<?php elseif ((isset($_SESSION['user_role'])) && $_SESSION['user_role'] == 'admin'): ?>
    <a href="viewDashboardAdmin.php">Dashboard</a>
    <a href="viewFeedBack.php">View Feedback</a>
    <a href="viewCustomer.php">View Customer</a>
    <a href="viewStaff.php">View Staff</a>
<?php elseif ((isset($_SESSION['user_role'])) && $_SESSION['user_role'] == 'staff'): ?>
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

            <!-- Search Section -->
            <form method="GET" action="viewCustomer.php" class="search-container">
                <input type="text" class="search-input" placeholder="Search customers by name or email..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                <button class="search-btn" type="submit">Search</button>
                <?php if (!empty($search)): ?>
                    <a href="viewCustomer.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>

<!-- ✅ Edit Customer Modal -->
<div id="editCustomerModal" class="modal-overlay">
    <div class="modal-content">
        <div class="customer-form">
            <h3>Edit Customer</h3>
            <form id="editCustomerForm" novalidate>
                <input type="hidden" name="edit_id" id="editCustomerId">
                <input type="hidden" name="edit_customer" value="1">
                
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-input" name="name" id="editCustomerName" required>
                    <span class="error-message" id="nameError"></span>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" class="form-input" name="email" id="editCustomerEmail" required>
                    <span class="error-message" id="emailError"></span>
                </div>
                
                <div class="form-buttons">
                    <button type="submit" class="action-btn btn-add">Update Customer</button>
                    <button type="button" class="btn-cancel" onclick="closeEditCustomerModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
            

            <?php if ($selectedCustomer): ?>
                <!-- Back button -->
                <button class="back-btn" onclick="window.history.back()">← Back to Customer List</button>

                <!-- Customer Details -->
                <div class="customer-details">
                    <h3>Customer Details</h3>
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

                    <!-- Pets Information -->
                    <div class="pets-section">
                        <h3>Pet Information</h3>
                        <?php if (count($customerPets) > 0): ?>
                            <div class="pets-grid">
                                <?php foreach ($customerPets as $pet): ?>
                                    <div class="pet-card">
                                        <h4 class="pet-name"><?php echo htmlspecialchars($pet['name']); ?></h4>
                                        <div class="pet-details"><strong>Species:</strong> <?php echo htmlspecialchars($pet['species']); ?></div>
                                        <div class="pet-details"><strong>Breed:</strong> <?php echo htmlspecialchars($pet['breed'] ?? 'Not specified'); ?></div>
                                        <div class="pet-details"><strong>Age:</strong> <?php echo $pet['age']; ?> years</div>
                                        <div class="pet-details"><strong>Weight:</strong> <?php echo $pet['weight']; ?> kg</div>
                                        <?php if (!empty($pet['medical_notes'])): ?>
                                            <div class="pet-details"><strong>Medical Notes:</strong> <?php echo htmlspecialchars($pet['medical_notes']); ?></div>
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
                                        <td><?php echo htmlspecialchars($customer['username']); ?></td>
                                        <td><?php echo htmlspecialchars($customer['email']); ?></td>
                                        <td>
                                            <span class="status-badge <?php echo $customer['verified'] ? 'status-verified' : 'status-pending'; ?>">
                                                <?php echo $customer['verified'] ? 'Verified' : 'Pending'; ?>
                                            </span>
                                        </td>
                                        <td>
<button class="view-btn" onclick="openEditCustomerModal(
    <?php echo $customer['id']; ?>, 
    '<?php echo htmlspecialchars($customer['username']); ?>', 
    '<?php echo htmlspecialchars($customer['email']); ?>'
)">Edit</button>
        <button class="view-btn" style="background-color:#dc3545;" onclick="deleteCustomer(<?php echo $customer['id']; ?>)">Delete</button>
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

    <!-- ✅ Footer -->
    <footer>
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
<script>
function openEditCustomerModal(id, name, email) {
    document.getElementById("editCustomerId").value = id;
    document.getElementById("editCustomerName").value = name;
    document.getElementById("editCustomerEmail").value = email;
    document.getElementById("editCustomerModal").style.display = "flex";
}

function closeEditCustomerModal() {
    document.getElementById("editCustomerModal").style.display = "none";
}

document.getElementById("editCustomerForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);
    const name = document.getElementById("editCustomerName").value.trim();
    const email = document.getElementById("editCustomerEmail").value.trim();
    const nameError = document.getElementById("nameError");
    const emailError = document.getElementById("emailError");
    nameError.textContent = "";
    emailError.textContent = "";

    let hasError = false;

    if (!name) {
        document.getElementById("nameError").textContent = "Name is required.";
        hasError = true;
    }
    if (!email) {
        document.getElementById("emailError").textContent = "Email is required.";
        hasError = true;
    } else {
        const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,}$/;
        if (!emailPattern.test(email)) {
            document.getElementById("emailError").textContent = "Please enter a valid email address.";
            hasError = true;
        }
    }
    if (hasError) return;

    fetch("viewCustomer.php", {   // ✅ now points to customer handler
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        alert("Customer updated successfully!");
        location.reload();
    });
});

function deleteCustomer(id) {
    if (!confirm("Are you sure you want to delete this customer?")) return;

    let formData = new FormData();
    formData.append("delete_customer", "1");
    formData.append("delete_id", id);

    fetch("viewCustomer.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        alert("Customer deleted successfully!");
        location.reload();
    })
    .catch(err => {
        console.error(err);
        alert("Error deleting customer.");
    });
}
</script>
</body>
</html>
