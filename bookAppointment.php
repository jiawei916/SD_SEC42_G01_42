<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}

$userName  = $_SESSION['user_name'];
$userEmail = isset($_SESSION['email']) ? $_SESSION['email'] : "Not provided";
$userRole  = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "customer";
$userId    = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$isLoggedIn = true;

// Database connection
require_once 'config.php';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch available services
$services = [];
$sql = "SELECT * FROM services WHERE is_active = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Process form submission
$successMessage = "";
$errorMessage = "";

// Get the selected service name + price
if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $serviceId = $_POST['service_id']; // now contains the service ID
    $serviceQuery = $conn->prepare("SELECT name, price FROM services WHERE id = ?");
    $serviceQuery->bind_param("i", $serviceId);
    $serviceQuery->execute();
    $serviceResult = $serviceQuery->get_result();
    $serviceData = $serviceResult->fetch_assoc();
    $serviceType = $serviceData['name'];
    $servicePrice = $serviceData['price'];
    $serviceQuery->close();
    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];
    $address = $_POST['address'];
    $specialInstructions = $_POST['special_instructions'] ?? '';

    // Validate date is not in the past
    $currentDate = date('Y-m-d');
    if ($appointmentDate < $currentDate) {
        $errorMessage = "Please select a current or future date for your appointment.";
    } else {
        // Insert appointment into database using service_id
$stmt = $conn->prepare("INSERT INTO appointments 
    (user_id, service_id, service_type, appointment_date, appointment_time, address, special_instructions, status) 
    VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
$stmt->bind_param("iisssss", $userId, $serviceId, $serviceType, $appointmentDate, $appointmentTime, $address, $specialInstructions);

        if ($stmt->execute()) {
            $appointmentId = $stmt->insert_id;
            $successMessage = "Appointment booked successfully! Your reference number is: APT" . str_pad($appointmentId, 5, '0', STR_PAD_LEFT);
        } else {
            $errorMessage = "Error booking appointment: " . $conn->error;
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
    <title>Book Appointment - VetGroom Hub</title>
    <meta name="description" content="Book an appointment with VetGroom Hub">
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
    
    <!-- Date and time picker CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.css">
    
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
        
        /* Booking section styles */
        .booking-section {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-top: 50px;
            margin-bottom: 50px;
        }
        
        .booking-section h2 {
            color: #333;
            font-weight: 700;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
            font-size: 16px;
        }
        
        .form-control {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px 15px;
            width: 100%;
            font-size: 16px;
        }
        
        .form-control:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
            outline: none;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .btn-book {
            background-color: #dc3545;
            border: none;
            padding: 12px 24px;
            border-radius: 6px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-book:hover {
            background-color: #ff707f;
        }
        
        .btn-cancel {
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            padding: 12px 24px;
            border-radius: 6px;
            color: #555;
            font-weight: 600;
            font-size: 16px;
            margin-left: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-cancel:hover {
            background-color: #e0e0e0;
        }
        
        .service-info {
            margin-top: 5px;
            font-size: 14px;
            color: #ff6363ff;
        }
        #service_id {
    white-space: normal;   /* allow text to wrap */
    line-height: 1.4;      /* improve spacing */
    min-width: 100%;   /* full width of parent */
    width: auto;  /* adjust width based on content */
        height: 40px;         /* fixed height */
    line-height: 40px;    /* vertically centers text */
    padding: 0 12px;      /* balanced left/right spacing */
}
#service_id option {
    white-space: normal;   /* apply inside dropdown options too */
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
                                            <li><a href="viewService.php">Services</a></li>
                                            <li><a href="feedback.php">Feedback</a></li>
                                            <li class="active"><a href="profile.php">Profile</a></li>
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

    <!-- ✅ Booking Section -->
    <main class="container" style="margin-top:80px;">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="booking-section">
                    <h2>Book an Appointment</h2>
                    
<?php if (!empty($successMessage)): ?>
    <div class="alert alert-success">
        <?php echo $successMessage; ?>
        <br><br>

        <!-- ✅ Redirects to payment.php for FPX payment -->
        <form action="payment.php" method="POST">
            <input type="hidden" name="amount" value="<?php echo number_format($servicePrice, 2, '.', ''); ?>">
            <input type="hidden" name="service" value="<?php echo htmlspecialchars($serviceType); ?>">
            <input type="hidden" name="appointment_id" value="<?php echo $appointmentId; ?>">

            <button type="submit" class="btn btn-success">Pay Now with FPX</button>
        </form>
    </div>
<?php endif; ?>

                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="appointmentForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" novalidate>
<div class="form-group">
    <label for="service_id">Service Type</label>
        <select class="form-control" id="service_id" name="service_id" required>
            <option value="">Select a service</option>
            <?php foreach ($services as $service): ?>
            <option value="<?php echo $service['id']; ?>">
                <?php echo htmlspecialchars($service['name']); ?> - 
                $<?php echo number_format($service['price'], 2); ?>
            </option>
            <?php endforeach; ?>
        </select>
    <span class="error-message text-danger" id="serviceError"></span>
</div>

<div class="form-group">
    <label for="appointment_date">Date</label>
    <input type="text" class="form-control" id="appointment_date" name="appointment_date" placeholder="Select date" required>
    <span class="error-message text-danger" id="dateError"></span>
</div>

<div class="form-group">
    <label for="appointment_time">Time</label>
    <input type="text" class="form-control" id="appointment_time" name="appointment_time" placeholder="Select time" required>
    <span class="error-message text-danger" id="timeError"></span>
</div>

<div class="form-group">
    <label for="address">Address</label>
    <textarea class="form-control" id="address" name="address" required></textarea>
    <span class="error-message text-danger" id="addressError"></span>
</div>
                        
                        <div class="form-group">
                            <label for="special_instructions">Special Instructions (Optional)</label>
                            <textarea class="form-control" id="special_instructions" name="special_instructions" placeholder="Any special instructions for our staff (e.g., pet's behavior, specific requests, etc.)"></textarea>
                            <small class="text-muted">Any special notes about your pet or service requirements</small>
                        </div>
                        
                        <div class="action-buttons">
                            <button type="submit" class="btn-book">Book Appointment</button>
                            <button type="button" class="btn-cancel" onclick="window.location.href='services.php'">Cancel</button>
                        </div>
                    </form>
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
    
    <!-- Date and time picker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    
  <script>
document.getElementById("appointmentForm").addEventListener("submit", function(e) {
    let isValid = true;

    // Reset error messages
    document.querySelectorAll(".error-message").forEach(span => span.textContent = "");

    const service = document.getElementById("service_id");
    const date = document.getElementById("appointment_date");
    const time = document.getElementById("appointment_time");
    const address = document.getElementById("address");

    // Service validation
    if (!service.value.trim()) {
        document.getElementById("serviceError").textContent = "Please select a service.";
        isValid = false;
    }

    // Date validation
    if (!date.value.trim()) {
        document.getElementById("dateError").textContent = "Please select a date.";
        isValid = false;
    } else {
        const appointmentDate = new Date(date.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        if (appointmentDate < today) {
            document.getElementById("dateError").textContent = "Date cannot be in the past.";
            isValid = false;
        }
    }

    // Time validation
    if (!time.value.trim()) {
        document.getElementById("timeError").textContent = "Please select a time.";
        isValid = false;
    }

    // Address validation
    if (!address.value.trim()) {
        document.getElementById("addressError").textContent = "Please enter an address.";
        isValid = false;
    }

    if (!isValid) {
        e.preventDefault(); // Stop form submission
    }
});
</script>
<script>
  // ✅ Calendar for date
  flatpickr("#appointment_date", {
      dateFormat: "Y-m-d",  // Format: 2025-09-22
      minDate: "today",     // Disable past dates
      defaultDate: "today"  // Preselect today
  });

  // ✅ Time picker
  flatpickr("#appointment_time", {
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",   // Format: 14:30
      time_24hr: true
  });
</script>

</body>
</html>