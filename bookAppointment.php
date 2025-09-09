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

// Check if appointments table exists, if not create it
$tableCheck = $conn->query("SHOW TABLES LIKE 'appointments'");
if ($tableCheck->num_rows == 0) {
    // Create appointments table
    $createTableSQL = "CREATE TABLE appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        service_type VARCHAR(255) NOT NULL,
        appointment_date DATE NOT NULL,
        appointment_time TIME NOT NULL,
        address TEXT NOT NULL,
        special_instructions TEXT,
        status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";
    
    if (!$conn->query($createTableSQL)) {
        die("Error creating appointments table: " . $conn->error);
    }
}

// Check if services table exists, if not create it with some sample data
$tableCheck = $conn->query("SHOW TABLES LIKE 'services'");
if ($tableCheck->num_rows == 0) {
    // Create services table
    $createServicesSQL = "CREATE TABLE services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        duration INT NOT NULL,
        is_active TINYINT(1) DEFAULT 1
    )";
    
    if (!$conn->query($createServicesSQL)) {
        die("Error creating services table: " . $conn->error);
    }
    
    // Insert sample services
    $sampleServices = [
        ["Basic Grooming", "Bath, brush, nail trim, and ear cleaning", 45.00, 60],
        ["Full Grooming", "Basic grooming plus haircut and styling", 65.00, 90],
        ["Veterinary Checkup", "General health examination and consultation", 55.00, 30],
        ["Vaccination", "Essential vaccinations for your pet", 35.00, 20],
        ["Dental Cleaning", "Teeth cleaning and oral health check", 75.00, 45]
    ];
    
    $stmt = $conn->prepare("INSERT INTO services (name, description, price, duration) VALUES (?, ?, ?, ?)");
    foreach ($sampleServices as $service) {
        $stmt->bind_param("ssdi", $service[0], $service[1], $service[2], $service[3]);
        $stmt->execute();
    }
    $stmt->close();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serviceType = $_POST['service_type'];
    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];
    $address = $_POST['address'];
    $specialInstructions = $_POST['special_instructions'] ?? '';
    
    // Validate date is not in the past
    $currentDate = date('Y-m-d');
    if ($appointmentDate < $currentDate) {
        $errorMessage = "Please select a current or future date for your appointment.";
    } else {
        // Insert appointment into database
        $stmt = $conn->prepare("INSERT INTO appointments (user_id, service_type, appointment_date, appointment_time, address, special_instructions, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("isssss", $userId, $serviceType, $appointmentDate, $appointmentTime, $address, $specialInstructions);
        
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
            background-color: #4a90e2;
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
            background-color: #357abd;
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
            color: #666;
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
                                            <a href="profile.php">Profile</a>
                                            <?php if ($userRole == 'admin'): ?>
                                                <a href="viewDashboardAdmin.php">Dashboard</a>
                                                <a href="viewFeedBack.php">View Feedback</a>
                                                <a href="viewCustomer.php">View Customer</a>
                                                <a href="viewStaff.php">View Staff</a>
                                            <?php elseif ($userRole == 'staff'): ?>
                                                <a href="viewDashboardStaff.php">Dashboard</a>
                                                <a href="viewFeedBack.php">View Feedback</a>
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

    <!-- ✅ Booking Section -->
    <main class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="booking-section">
                    <h2>Book an Appointment</h2>
                    
                    <?php if (!empty($successMessage)): ?>
                        <div class="alert alert-success">
                            <?php echo $successMessage; ?>
                            <br><br>
                            <a href="profile.php" class="btn btn-primary">View My Appointments</a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errorMessage)): ?>
                        <div class="alert alert-danger">
                            <?php echo $errorMessage; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form id="appointmentForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <div class="form-group">
                            <label for="service_type">Service Type</label>
                            <select class="form-control" id="service_type" name="service_type" required>
                                <option value="">Select a service</option>
                                <?php foreach ($services as $service): ?>
                                    <option value="<?php echo htmlspecialchars($service['name']); ?>" data-price="<?php echo $service['price']; ?>">
                                        <?php echo htmlspecialchars($service['name']); ?> - 
                                        $<?php echo number_format($service['price'], 2); ?>
                                    </option>
                                    <div class="service-info">
                                        <?php echo htmlspecialchars($service['description']); ?>
                                    </div>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Select the service you need for your pet</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment_date">Date</label>
                            <input type="text" class="form-control" id="appointment_date" name="appointment_date" placeholder="Select date" required>
                            <small class="text-muted">Select a date for your appointment</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment_time">Time</label>
                            <input type="text" class="form-control" id="appointment_time" name="appointment_time" placeholder="Select time" required>
                            <small class="text-muted">Our operating hours are 9:00 AM to 6:00 PM</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" placeholder="Enter address where service is needed" required><?php 
                                // Try to prefill with user's address if available
                                if (isset($_SESSION['user_address'])) {
                                    echo htmlspecialchars($_SESSION['user_address']);
                                }
                            ?></textarea>
                            <small class="text-muted">Where would you like the service to be performed?</small>
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
                    <!-- Add more footer columns if needed -->
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
    
    <!-- Date and time picker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flatpickr/4.6.13/flatpickr.min.js"></script>
    
    <script>
        // Initialize date picker
        flatpickr("#appointment_date", {
            minDate: "today",
            dateFormat: "Y-m-d",
            disableMobile: true,
            onChange: function(selectedDates, dateStr, instance) {
                // Update time picker min/max based on selected date
                updateTimePicker(selectedDates[0]);
            }
        });
        
        // Initialize time picker
        const timePicker = flatpickr("#appointment_time", {
            enableTime: true,
            noCalendar: true,
            dateFormat: "H:i",
            time_24hr: true,
            minuteIncrement: 30,
            disableMobile: true,
            minTime: "09:00",
            maxTime: "17:30" // Last appointment at 5:30 PM
        });
        
        // Function to update time picker based on selected date
        function updateTimePicker(selectedDate) {
            const today = new Date();
            const isToday = selectedDate.toDateString() === today.toDateString();
            
            if (isToday) {
                // If today is selected, set minTime to current time + 1 hour
                const currentHour = today.getHours();
                const currentMinute = today.getMinutes();
                let minHour = currentHour;
                
                // If current time is after 5 PM, disable today
                if (currentHour >= 17) {
                    alert("Sorry, no more appointments available for today. Please select another date.");
                    document.getElementById("appointment_date").value = "";
                    return;
                }
                
                // Set minimum time to next half hour
                let minTime = `${String(minHour).padStart(2, '0')}:30`;
                if (currentMinute < 30) {
                    minTime = `${String(minHour).padStart(2, '0')}:30`;
                } else {
                    minHour += 1;
                    minTime = `${String(minHour).padStart(2, '0')}:00`;
                }
                
                timePicker.set("minTime", minTime);
            } else {
                // For future dates, use normal business hours
                timePicker.set("minTime", "09:00");
            }
        }
        
        // Form validation
        document.getElementById("appointmentForm").addEventListener("submit", function(e) {
            let isValid = true;
            const requiredFields = document.querySelectorAll("#appointmentForm [required]");
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = "red";
                } else {
                    field.style.borderColor = "#ddd";
                }
            });
            
            // Validate date is not in the past
            const appointmentDate = new Date(document.getElementById("appointment_date").value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (appointmentDate < today) {
                isValid = false;
                document.getElementById("appointment_date").style.borderColor = "red";
                alert("Please select a current or future date for your appointment.");
            }
            
            if (!isValid) {
                e.preventDefault();
                alert("Please fill in all required fields correctly.");
            }
        });
    </script>
</body>
</html>