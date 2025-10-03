<?php
session_start();

// Check if user is admin or staff
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] != 'admin' && $_SESSION['user_role'] != 'staff')) {
    header("Location: signIn.php");
    exit();
}

$userName = $_SESSION['user_name'];
$userRole = $_SESSION['user_role'];
$userId = $_SESSION['user_id'];

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

$appointment = [];
$message = "";
$error = "";
$services = [];
$staffMembers = [];

// Fetch all services for dropdown
$service_stmt = $conn->prepare("SELECT id, name, price FROM services WHERE is_active = 1 ORDER BY name");
$service_stmt->execute();
$service_result = $service_stmt->get_result();
while ($row = $service_result->fetch_assoc()) {
    $services[] = $row;
}
$service_stmt->close();

// Fetch staff members for admin dropdown
if ($userRole == 'admin') {
    $staff_stmt = $conn->prepare("SELECT id, username FROM users WHERE role = 'staff' ORDER BY username");
    $staff_stmt->execute();
    $staff_result = $staff_stmt->get_result();
    while ($row = $staff_result->fetch_assoc()) {
        $staffMembers[] = $row;
    }
    $staff_stmt->close();
}

// Check if appointment ID is provided
if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];
    
    // Fetch appointment data
    if ($userRole == 'admin') {
        $sql = "SELECT a.*, u.username as customer_name, u.email, s.username as staff_name 
                FROM appointments a 
                LEFT JOIN users u ON a.user_id = u.id 
                LEFT JOIN users s ON a.staff_id = s.id 
                WHERE a.id = ?";
    } else {
        $sql = "SELECT a.*, u.username as customer_name, u.email, s.username as staff_name 
                FROM appointments a 
                LEFT JOIN users u ON a.user_id = u.id 
                LEFT JOIN users s ON a.staff_id = s.id 
                WHERE a.id = ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
    } else {
        $error = "Appointment not found!";
    }
    
    $stmt->close();
} else {
    $error = "No appointment ID provided!";
}

// Handle form submission for editing appointment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    $appointment_time = $_POST['appointment_time'];
    $service_type = $_POST['service_type'];
    $special_instructions = $_POST['special_instructions'];
    
    // Validation
    if (empty($appointment_time) || empty($service_type)) {
        $error = "Please fill in all required fields!";
    } else {
        if ($userRole == 'admin') {
            // Admin can edit staff assignment
            $staff_id = $_POST['staff_id'];
            $stmt = $conn->prepare("UPDATE appointments SET appointment_time = ?, service_type = ?, special_instructions = ?, staff_id = ? WHERE id = ?");
            $stmt->bind_param("sssii", $appointment_time, $service_type, $special_instructions, $staff_id, $appointment_id);
        } else {
            // Staff can only edit time, service, and notes
            $stmt = $conn->prepare("UPDATE appointments SET appointment_time = ?, service_type = ?, special_instructions = ? WHERE id = ?");
            $stmt->bind_param("sssi", $appointment_time, $service_type, $special_instructions, $appointment_id);
        }
        
        if ($stmt->execute()) {
            $message = "Appointment updated successfully!";
            // Refresh appointment data
            if ($userRole == 'admin') {
                $sql = "SELECT a.*, u.username as customer_name, u.email, s.username as staff_name 
                        FROM appointments a 
                        LEFT JOIN users u ON a.user_id = u.id 
                        LEFT JOIN users s ON a.staff_id = s.id 
                        WHERE a.id = ?";
            } else {
                $sql = "SELECT a.*, u.username as customer_name, u.email, s.username as staff_name 
                        FROM appointments a 
                        LEFT JOIN users u ON a.user_id = u.id 
                        LEFT JOIN users s ON a.staff_id = s.id 
                        WHERE a.id = ?";
            }
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $appointment = $result->fetch_assoc();
            $stmt->close();
        } else {
            $error = "Error updating appointment: " . $conn->error;
        }
    }
}

// Handle appointment deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_appointment'])) {
    $appointment_id = $_POST['appointment_id'];
    
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $appointment_id);
    
    if ($stmt->execute()) {
        $message = "Appointment deleted successfully!";
        // Redirect to view appointments after deletion
        header("Location: viewAppointment.php?success=Appointment+deleted+successfully");
        exit();
    } else {
        $error = "Error deleting appointment: " . $conn->error;
    }
    
    $stmt->close();
}

$conn->close();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Edit Appointment - VetGroom Hub</title>
    <meta name="description" content="Edit appointment details">
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
        
        /* Appointment form container */
        .appointment-form-container {
            max-width: 800px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .appointment-form-container h2 {
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
            margin-right: 10px;
        }
        
        .btn-submit:hover {
            background-color: #218838;
        }
        
        .btn-delete {
            background-color: #dc3545;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 16px;
            transition: background-color 0.3s;
            margin-right: 10px;
        }
        
        .btn-delete:hover {
            background-color: #c82333;
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
        
        .appointment-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .appointment-info h4 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: 600;
            min-width: 120px;
            color: #555;
        }
        
        .info-value {
            color: #333;
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
            margin-left: auto;
        }
        
        /* Ensure header container has proper spacing */
        .container-fluid {
            padding: 0 20px;
        }
        
        @media (max-width: 768px) {
            .appointment-form-container {
                margin: 60px 15px;
                padding: 20px;
            }
            
            .btn-cancel, .btn-submit, .btn-delete {
                width: 100%;
                margin-bottom: 10px;
                margin-right: 0;
            }
            
            .info-row {
                flex-direction: column;
            }
            
            .info-label {
                min-width: auto;
                margin-bottom: 5px;
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
                                            <a href="viewAppointment.php">View Appointments</a>
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
    
    <div class="appointment-form-container">
        <h2>Edit Appointment</h2>
        
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
        
        <?php if (!empty($appointment)): ?>
            <!-- Appointment Information -->
            <div class="appointment-info">
                <h4>Appointment Details</h4>
                <div class="info-row">
                    <span class="info-label">Reference:</span>
                    <span class="info-value">APT<?php echo str_pad($appointment['id'], 5, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Customer:</span>
                    <span class="info-value"><?php echo htmlspecialchars($appointment['customer_name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email:</span>
                    <span class="info-value"><?php echo htmlspecialchars($appointment['email']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Date:</span>
                    <span class="info-value"><?php echo htmlspecialchars($appointment['appointment_date']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status:</span>
                    <span class="info-value"><?php echo ucfirst($appointment['status']); ?></span>
                </div>
                <?php if ($userRole == 'admin' && !empty($appointment['staff_name'])): ?>
                    <div class="info-row">
                        <span class="info-label">Assigned Staff:</span>
                        <span class="info-value"><?php echo htmlspecialchars($appointment['staff_name']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Edit Form -->
            <form method="POST" action="editAppointment.php?id=<?php echo $appointment['id']; ?>" novalidate>
                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                
                <div class="form-group">
                    <label for="appointment_time">Appointment Time *</label>
                    <input type="time" class="form-control" id="appointment_time" name="appointment_time" 
                           value="<?php echo htmlspecialchars($appointment['appointment_time']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="service_type">Service Type *</label>
                    <select class="form-control" id="service_type" name="service_type" required>
                        <option value="">Select a service</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?php echo htmlspecialchars($service['name']); ?>" 
                                <?php echo ($appointment['service_type'] == $service['name']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($service['name']); ?> - $<?php echo number_format($service['price'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <?php if ($userRole == 'admin'): ?>
                    <div class="form-group">
                        <label for="staff_id">Assign Staff</label>
                        <select class="form-control" id="staff_id" name="staff_id">
                            <option value="">Unassigned</option>
                            <?php foreach ($staffMembers as $staff): ?>
                                <option value="<?php echo $staff['id']; ?>" 
                                    <?php echo ($appointment['staff_id'] == $staff['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($staff['username']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="special_instructions">Special Instructions / Notes</label>
                    <textarea class="form-control" id="special_instructions" name="special_instructions" rows="4"><?php echo htmlspecialchars($appointment['special_instructions']); ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="edit_appointment" class="btn-submit">Save Changes</button>
                    <a href="viewAppointment.php" class="btn-cancel">Cancel</a>
                    <button type="button" class="btn-delete" onclick="confirmDelete()">Delete Appointment</button>
                </div>
            </form>
            
            <!-- Delete Form (Hidden) -->
            <form method="POST" action="editAppointment.php?id=<?php echo $appointment['id']; ?>" id="deleteForm" style="display: none;">
                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                <input type="hidden" name="delete_appointment" value="1">
            </form>
            
        <?php else: ?>
            <div class="alert alert-warning">Appointment not found.</div>
            <a href="viewAppointment.php" class="btn-cancel">Back to Appointments</a>
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
    function confirmDelete() {
        if (confirm("Are you sure you want to delete this appointment?\nThis action cannot be undone.")) {
            document.getElementById('deleteForm').submit();
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