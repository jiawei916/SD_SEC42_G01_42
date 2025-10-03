<?php
session_start();
// Check if user is admin or staff
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'staff')) {
    header("Location: signIn.php");
    exit();
}

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

$service = [];
$message = "";
$error = "";
$isEditMode = false;
$errors = [];

// Check if ID is provided for editing
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $isEditMode = true;
    
    // Fetch service data
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        $error = "Service not found!";
    }
    
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = [];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];

    // Validation
    if (empty($name)) {
        $errors['name'] = "Service name is required.";
    }
    if (empty($description)){
        $errors['description'] = "Description is required.";
    }
    if (empty($price)){
        $errors['price'] = "Price is required.";
    } else if (!is_numeric($price)){
        $errors['price'] = "Please enter a valid price.";
    }
    if (empty($duration)){
        $errors['duration'] = "Duration is required.";
    } else if (!is_numeric($duration)){
        $errors['duration'] = "Please enter a valid duration.";
    }
    
    // Image upload handling
    $image_path = $service['image_path'] ?? ''; // Keep existing image if no new upload

    if (isset($_FILES['service_image']) && $_FILES['service_image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        $file_type = $_FILES['service_image']['type'];
        $file_size = $_FILES['service_image']['size'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors['service_image'] = "Only JPG, PNG, and GIF images are allowed.";
        } elseif ($file_size > $max_size) {
            $errors['service_image'] = "Image size must be less than 2MB.";
        } else {
            // Create uploads directory if it doesn't exist
            $upload_dir = 'assets/img/services/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            // Generate unique filename
            $file_extension = pathinfo($_FILES['service_image']['name'], PATHINFO_EXTENSION);
            $filename = 'service_' . uniqid() . '.' . $file_extension;
            $upload_path = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['service_image']['tmp_name'], $upload_path)) {
                $image_path = $upload_path;
                
                // Delete old image if it exists and we're updating
                if ($isEditMode && !empty($service['image_path']) && file_exists($service['image_path'])) {
                    unlink($service['image_path']);
                }
            } else {
                $errors['service_image'] = "Failed to upload image. Please try again.";
            }
        }
    } elseif (isset($_FILES['service_image']) && $_FILES['service_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $errors['service_image'] = "Error uploading image: " . $_FILES['service_image']['error'];
    }
    
    // Validate inputs
    if (empty($errors)) {
        if ($isEditMode && isset($_POST['id'])) {
            // Update existing service
            $id = $_POST['id'];
            $stmt = $conn->prepare("UPDATE services SET name = ?, description = ?, price = ?, duration = ?, image_path = ? WHERE id = ?");
            $stmt->bind_param("ssdisi", $name, $description, $price, $duration, $image_path, $id);
            
            if ($stmt->execute()) {
                $message = "Service updated successfully!";
                // Refresh service data
                $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $service = $result->fetch_assoc();
            } else {
                $error = "Error updating service: " . $conn->error;
            }
        } else {
            // Add new service
            $stmt = $conn->prepare("INSERT INTO services (name, description, price, duration, image_path, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("ssdis", $name, $description, $price, $duration, $image_path);
            
            if ($stmt->execute()) {
                $message = "Service added successfully!";
                // Clear form if not in edit mode
                if (!$isEditMode) {
                    $name = $description = $price = $duration = "";
                    $image_path = "";
                }
            } else {
                $error = "Error adding service: " . $conn->error;
            }
        }
        
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
    <title><?php echo $isEditMode ? 'Edit' : 'Add'; ?> Service - VetGroom Hub</title>
    <meta name="description" content="<?php echo $isEditMode ? 'Edit' : 'Add'; ?> service details">
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
        
        /* Service form container */
        .service-form-container {
            max-width: 600px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .service-form-container h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            width: 100%;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #dc3545;
            outline: none;
            box-shadow: 0 0 0 2px rgba(220, 53, 69, 0.2);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-submit {
            background-color: #28a745;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #218838;
        }
        
        .btn-cancel {
            background-color: #6c757d;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn-cancel:hover {
            background-color: #5a6268;
            color: white;
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
        
        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeaa7;
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
        
        /* Image preview styling */
        .image-preview {
            max-width: 200px;
            height: auto;
            border-radius: 6px;
            margin-top: 10px;
            border: 1px solid #ddd;
        }
        
        /* Header adjustments for better spacing */
        .header-area {
            position: relative;
        }
        
        .main-header {
            padding: 10px 0;
        }
        
        /* Profile dropdown positioning adjustments */
        .profile-dropdown {
            position: relative;
            top: 0;
            right: 0;
            margin-left: auto; /* Push to the far right */
        }
        
        /* Ensure header container has proper spacing */
        .container-fluid {
            padding: 0 20px;
        }
        
        @media (max-width: 768px) {
            .service-form-container {
                margin: 60px 15px;
                padding: 20px;
            }
            
            .btn-cancel, .btn-submit {
                width: 100%;
                margin-bottom: 10px;
                margin-right: 0;
            }
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
                                            Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> ▼
                                        </a>
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
    <a href="viewAppointment.php">View Appointments</a> 
<?php elseif ($_SESSION['user_role'] == 'staff'): ?>
    <a href="viewDashboardStaff.php">Dashboard</a>
    <a href="viewFeedBack.php">View Feedback</a>
    <a href="viewCustomer.php">View Customer</a>
    <a href="viewAppointment.php">View Appointments</a> 
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
    
    <div class="service-form-container">
        <h2><?php echo $isEditMode ? 'Edit Service' : 'Add New Service'; ?></h2>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($error); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="editService.php<?php echo $isEditMode ? '?id=' . $service['id'] : ''; ?>" enctype="multipart/form-data" novalidate>
            <?php if ($isEditMode && !empty($service)): ?>
                <input type="hidden" name="id" value="<?php echo $service['id']; ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label for="name">Service Name *</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?php echo !empty($service['name']) ? htmlspecialchars($service['name']) : (isset($name) ? htmlspecialchars($name) : ''); ?>" required>
                <?php if (!empty($errors['name'])): ?>
                    <span class="text-danger"><?php echo $errors['name']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?php echo !empty($service['description']) ? htmlspecialchars($service['description']) : (isset($description) ? htmlspecialchars($description) : ''); ?></textarea>
                <?php if (!empty($errors['description'])): ?>
                    <span class="text-danger"><?php echo $errors['description']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="price">Price ($) *</label>
                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" 
                       value="<?php echo !empty($service['price']) ? $service['price'] : (isset($price) ? $price : ''); ?>" required>
                <?php if (!empty($errors['price'])): ?>
                    <span class="text-danger"><?php echo $errors['price']; ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="duration">Duration (minutes) *</label>
                <input type="number" class="form-control" id="duration" name="duration" min="1" 
                       value="<?php echo !empty($service['duration']) ? $service['duration'] : (isset($duration) ? $duration : ''); ?>" required>
                <?php if (!empty($errors['duration'])): ?>
                    <span class="text-danger"><?php echo $errors['duration']; ?></span> 
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="service_image">Service Image</label>
                <input type="file" class="form-control" id="service_image" name="service_image" accept="image/*">
                <small class="text-muted">Recommended size: 400x300px. Supported formats: JPG, PNG, GIF (Max: 2MB)</small>
                <?php if (!empty($errors['service_image'])): ?>
                    <span class="text-danger"><?php echo $errors['service_image']; ?></span>
                <?php endif; ?>
                <?php if (!empty($service['image_path'])): ?>
                    <div class="mt-2">
                        <p>Current Image:</p>
                        <img src="<?php echo htmlspecialchars($service['image_path']); ?>" alt="Current service image" class="image-preview">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <a href="viewService.php" class="btn-cancel">Cancel</a>
                <button type="submit" class="btn-submit"><?php echo $isEditMode ? 'Save Changes' : 'Add Service'; ?></button>
            </div>
        </form>
        
        <?php if ($isEditMode && empty($service)): ?>
            <div class="alert alert-warning">Service not found.</div>
            <a href="viewService.php" class="btn-cancel">Back to Services</a>
        <?php endif; ?>
    </div>
    
    <!-- ✅ Footer -->
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
    
    <script>
    // Dismiss alert when close button is clicked
    $(document).ready(function() {
        $('.alert .close').on('click', function() {
            $(this).closest('.alert').alert('close');
        });
    });
    </script>
</body>
</html>