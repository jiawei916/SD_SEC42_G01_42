<?php
session_start();

$userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : null;
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$isLoggedIn = isset($_SESSION['user_name']);

// Display success/error messages
$successMessage = isset($_GET['success']) ? $_GET['success'] : '';
$errorMessage = isset($_GET['error']) ? $_GET['error'] : '';

// Database connection
require_once 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build query based on user role and filters
if ($userRole == 'admin') {
    // Admin can see all services with full management options
    $sql = "SELECT * FROM services WHERE 1=1";
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (name LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $types .= "s";
    }
    
    $sql .= " ORDER BY name";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
} else {
    // Customers, staff, and guests see only active services
    $sql = "SELECT * FROM services WHERE is_active = 1";
    $params = [];
    $types = "";
    
    if (!empty($search)) {
        $sql .= " AND (name LIKE ?)";
        $searchTerm = "%$search%";
        $params[] = $searchTerm;
        $types .= "s";
    }
    
    $sql .= " ORDER BY name";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_service'])) {
    $deleteId = intval($_POST['delete_id']);

    // Delete the service
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
    exit;
}


$stmt->execute();
$result = $stmt->get_result();
$services = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Our Services - VetGroom Hub</title>
    <meta name="description" content="Professional pet grooming and veterinary services">
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
        
        /* Services section styles */
        .services-section {
            padding: 50px 0;
        }
        
        .services-section h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .service-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .service-image {
            height: 200px;
            background-color: #dc3545;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 48px;
        }
        
        .service-content {
            padding: 20px;
        }
        
        .service-title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }
        
        .service-description {
            color: #666;
            margin-bottom: 15px;
            min-height: 60px;
        }
        
        .service-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        
        .service-price {
            font-weight: 700;
            color: #dc3545;
            font-size: 18px;
        }
        
        .service-duration {
            color: #6c757d;
            font-size: 14px;
        }
        
        .btn-book {
            background-color: #dc3545;
            border: none;
            padding: 12px 25px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s;
            min-height: 45px;
        }
        
        .btn-book:hover {
            background-color: #ff707f;
            color: white;
        }
        
        .btn-admin {
            background-color: #28a745;
            border: none;
            padding: 10px 18px;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            margin-right: 5px;
            margin-bottom: 5px;
        }
        
        .btn-admin:hover {
            background-color: #218838;
            color: white;
        }
        
        .btn-deactivate {
            background-color: #007bff;
        }
        
        .btn-deactivate:hover {
            background-color: #0056b3;
        }
        .btn-danger {
            background-color: #dc3545; 
        }

        .btn-danger:hover {
            background-color: #ff707f; 
        }

        .search-filter-section {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .admin-controls {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        
        .no-services {
            text-align: center;
            padding: 40px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        /* Improved search bar styling */
        .search-container {
            display: flex;
            gap: 10px; /* Space between input and button */
            align-items: center;
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
            background-color: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        
        .search-btn:hover {
            background-color: #ff707f;
        }
        
        .clear-btn {
            padding: 10px 15px;
            background-color: #6c757d;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .clear-btn:hover {
            background-color: #5a6268;
            color: white;
        }
        
        /* Edit service button styling */
        .btn-edit-service {
            background-color: #ffc107;
            color: #212529;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-edit-service:hover {
            background-color: #e0a800;
            color: #212529;
        }
        
        /* Alert styling */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 6px;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .close {
            float: right;
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
            color: #000;
            text-shadow: 0 1px 0 #fff;
            opacity: .5;
            background: transparent;
            border: 0;
        }
        
        .close:hover {
            opacity: .75;
        }
        
        @media (max-width: 768px) {
            .service-card {
                margin-bottom: 20px;
            }
            
            .service-image {
                height: 150px;
                font-size: 36px;
            }
            
            .search-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-input {
                width: 100%;
            }
            
            .search-btn, .clear-btn, .btn-edit-service {
                width: 100%;
            }
        }
        
        /* Header adjustments for better spacing */
        .header-area {
            position: relative;
        }
        
        .main-header {
            padding: 20px 0;
        }
        
        .menu-main {
            gap: 0px; /* Add space between navigation and profile dropdown */
        }
        
        /* Profile dropdown positioning adjustments */
        .profile-dropdown {
            position: relative;
            top: 0;
            right: 0;
            margin-left: auto; /* Push to the far right */
        }
        
        .service-image {
        height: 200px;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
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
                                <a href="index.php"><img src="assets/img/logo/logo.png" alt="VetGroom Hub"></a>
                            </div>
                        </div>
                        <div class="col-xl-10 col-lg-10 col-md-10">
                            <div class="menu-main d-flex align-items-center justify-content-end">
                                <!-- Main-menu -->
                                <div class="main-menu f-right d-none d-lg-block">
                                    <nav> 
                                        <ul id="navigation">
                                            <li><a href="index.php">Home</a></li>
                                            <li><a href="aboutUs.php">About</a></li>
                                            <li class="active"><a href="viewService.php">Services</a></li>
                                            <li class="active"><a href="feedback.php">Feedback</a></li>
                                            <li><a href="contact.php">Contact</a></li>
                                        </ul>
                                    </nav>
                                </div>
                                <!-- Dropdown - Updated to match viewDashboardStaff.php -->
                                <div class="header-right-btn f-right d-none d-lg-block ml-30">
                                    <div class="dropdown">
                                        <a href="#" class="header-btn">
                                            <?php echo $isLoggedIn ? "Welcome, " . htmlspecialchars($userName) : "Welcome, Guest"; ?> ▼
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

    <!-- ✅ Services Section -->
    <main class="container services-section">
        <div class="row">
            <div class="col-12">
                <h2>Our Services</h2>
                
                <!-- Success/Error Messages -->
                <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($successMessage); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($errorMessage); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
                
                <!-- Search and Filter Section -->
                <div class="search-filter-section">
                    <div class="row">
                        <div class="col-md-8">
                            <form method="GET" action="viewService.php" class="search-container">
                                <input type="text" class="search-input" placeholder="Search services by name..." name="search" value="<?php echo htmlspecialchars($search); ?>">
                                <button class="search-btn" type="submit">Search</button>
                                <?php if (!empty($search)): ?>
                                    <a href="viewService.php" class="clear-btn">Clear</a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="col-md-4 text-right">
                            <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                <a href="editService.php" class="btn btn-success">Add New Service</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Services Grid -->
                <div class="row">
                    <?php if (count($services) > 0): ?>
                        <?php foreach ($services as $service): ?>
                            <div class="col-lg-4 col-md-6">
                                <div class="service-card">
<div class="service-image">
    <?php if (!empty($service['image'])): ?>
        <img src="<?php echo htmlspecialchars($service['image']); ?>" 
             alt="<?php echo htmlspecialchars($service['name']); ?>" 
             style="width: 100%; height: 100%; object-fit: cover;">
    <?php else: ?>
        <i class="fas fa-paw"></i>
    <?php endif; ?>
</div>
                                    <div class="service-content">
                                        <h3 class="service-title"><?php echo htmlspecialchars($service['name']); ?></h3>
                                        <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                                        
                                        <div class="service-details">
                                            <span class="service-price">$<?php echo number_format($service['price'], 2); ?></span>
                                            <span class="service-duration"><?php echo $service['duration']; ?> mins</span>
                                        </div>
                                        
                                        <?php if ($isLoggedIn && $userRole == 'customer'): ?>
                                            <a href="bookAppointment.php?service=<?php echo $service['id']; ?>" class="btn-book">Book Now</a>
                                        <?php elseif (!$isLoggedIn): ?>
                                            <a href="signIn.php" class="btn-book">Login to Book</a>
                                        <?php endif; ?>
                                        
                                        <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                            <div class="admin-controls">
                                                <small class="text-muted d-block mb-2">Admin Controls:</small>
                                                <a href="editService.php?id=<?php echo $service['id']; ?>" class="btn btn-admin">Edit</a>
                                                <a href="toggleService.php?id=<?php echo $service['id']; ?>&action=<?php echo $service['is_active'] ? 'deactivate' : 'activate'; ?>" class="btn btn-admin btn-deactivate">
                                                    <?php echo $service['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                </a>
                                                <!-- Add delete button with confirmation -->
                                                <button class="btn btn-admin btn-danger" onclick="confirmDelete(<?php echo $service['id']; ?>, '<?php echo htmlspecialchars($service['name']); ?>')">Delete</button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="no-services">
                                <h3>No services found</h3>
                                <p>Try adjusting your search criteria or check back later.</p>
                                <?php if ($userRole == 'admin'): ?>
                                    <a href="editService.php" class="btn btn-primary">Add New Service</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
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
    // Delete confirmation function
function confirmDelete(id, name) {
    if (confirm("Are you sure you want to delete the service: " + name + "?\nThis action cannot be undone.")) {
        $.post("viewService.php", { delete_service: true, delete_id: id }, function(response) {
            if (response === "success") {
                alert("Service deleted successfully.");
                location.reload();
            } else {
                alert("Error deleting service.");
            }
        });
    }
}
    
    // Dismiss alert when close button is clicked
    $(document).ready(function() {
        $('.alert .close').on('click', function() {
            $(this).closest('.alert').alert('close');
        });
    });
    </script>
</body>
</html>
