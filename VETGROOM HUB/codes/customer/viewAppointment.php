<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}

$userName = $_SESSION['user_name'];
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "customer";
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Database connection
require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$editAppointment = null;
$errors = [];

// Handle delete request
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $deleteId);
    
    if ($stmt->execute()) {
        header("Location: viewAppointment.php?deleted=1");
        exit();
    } else {
        $errors['form'] = "Failed to delete appointment. Please try again.";
    }
    
    $stmt->close();
}
// Handle edit request
if (isset($_GET['edit_id'])) {
    $editId = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $editId);
    $stmt->execute();
    $editAppointment = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_appointment'])) {
    $id = intval($_POST['id']);
    $service = trim($_POST['service_type']);
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $address = trim($_POST['address']);
    $instructions = trim($_POST['special_instructions']);
    $status = $_POST['status'];

    if (!$service || !$date || !$time) {
        $errors['form'] = "Please fill out all required fields.";
    } else {
        $stmt = $conn->prepare("UPDATE appointments SET service_type=?, appointment_date=?, appointment_time=?, address=?, special_instructions=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssi", $service, $date, $time, $address, $instructions, $status, $id);
        if ($stmt->execute()) {
            header("Location: viewAppointment.php?success=1");
            exit();
        } else {
            $errors['form'] = "Update failed. Please try again.";
        }
        $stmt->close();
    }
}

// Fetch appointments
if ($userRole == 'admin' || $userRole == 'staff') {
    $sql = "SELECT a.*, u.username, u.email 
            FROM appointments a 
            LEFT JOIN users u ON a.user_id = u.id 
            ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT * FROM appointments WHERE user_id = ? ORDER BY appointment_date DESC, appointment_time DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
}
$stmt->execute();
$result = $stmt->get_result();
$appointments = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Appointments - VetGroom Hub</title>
    <!-- Shared Global Styles -->
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
            padding-top: 10px;
        }

        @keyframes fadeInAnimation {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        /* Dashboard container styling */
        .dashboard-container {
            padding: 20px;
            max-width: 1400px;
            margin: auto;
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

        .header-sticky {
    top: 0;
    left: 0;
    right: 0;
    z-index: 999;
    position: sticky;
}
</style>

</head>
<body>

<!-- ✅ Header (Identical to viewStaff.php) -->
<header>
    <div class="header-area">
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
                                        <?php if ($userRole == 'customer'): ?>
                                            <a href="profile.php">Profile</a>
                                            <a href="bookAppointment.php">Book Appointment</a>
                                            <a href="viewAppointment.php">View Appointments</a>
                                        <?php elseif ($userRole == 'admin'): ?>
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

<!-- 🧾 Main Content -->
<main>
    <div class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <h2>View Appointments</h2>
            </div>
<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Appointment updated successfully!</div>
<?php endif; ?>
<?php if (isset($_GET['deleted'])): ?>
    <div class="alert alert-success">Appointment deleted successfully!</div>
<?php endif; ?>

<?php if ($editAppointment): ?>
<div class="staff-form">
    <h3>Edit Appointment</h3>

    <?php if (!empty($errors['form'])): ?>
        <div class="alert alert-error"><?php echo $errors['form']; ?></div>
    <?php endif; ?>

    <form method="POST" action="viewAppointment.php" novalidate>
        <input type="hidden" name="id" value="<?php echo $editAppointment['id']; ?>">
        <input type="hidden" name="update_appointment" value="1">

        <div class="form-group">
            <label class="form-label">Service Type</label>
            <input type="text" class="form-input" name="service_type" 
                   value="<?php echo htmlspecialchars($editAppointment['service_type']); ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Appointment Date</label>
            <input type="date" class="form-input" name="appointment_date"
                   value="<?php echo htmlspecialchars($editAppointment['appointment_date']); ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Appointment Time</label>
            <input type="time" class="form-input" name="appointment_time"
                   value="<?php echo htmlspecialchars($editAppointment['appointment_time']); ?>" required>
        </div>

        <div class="form-group">
            <label class="form-label">Address</label>
            <input type="text" class="form-input" name="address"
                   value="<?php echo htmlspecialchars($editAppointment['address']); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Special Instructions</label>
            <textarea class="form-input" name="special_instructions"><?php echo htmlspecialchars($editAppointment['special_instructions']); ?></textarea>
        </div>

        <div class="form-group">
            <label class="form-label">Status</label>
            <select class="form-input" name="status" required>
                <option value="pending" <?php if ($editAppointment['status']=='pending') echo 'selected'; ?>>Pending</option>
                <option value="confirmed" <?php if ($editAppointment['status']=='confirmed') echo 'selected'; ?>>Confirmed</option>
                <option value="completed" <?php if ($editAppointment['status']=='completed') echo 'selected'; ?>>Completed</option>
                <option value="cancelled" <?php if ($editAppointment['status']=='cancelled') echo 'selected'; ?>>Cancelled</option>
            </select>
        </div>

        <div class="form-buttons">
            <button type="submit" class="action-btn btn-add">Update Appointment</button>
            <a href="viewAppointment.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
<?php endif; ?>

            <h3>Appointment Records</h3>

            <?php if (empty($appointments)): ?>
                <p class="text-center text-muted">No appointments found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr>
                                <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                    <th>Customer Name</th>
                                    <th>Email</th>
                                <?php endif; ?>
                                <th>Reference</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Address</th>
                                <th>Instructions</th>
                                <th>Status</th>
                                <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                    <th>Actions</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($appointments as $appt): ?>
                                <tr>
                                    <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                        <td><?php echo htmlspecialchars($appt['username']); ?></td>
                                        <td><?php echo htmlspecialchars($appt['email']); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo 'APT' . str_pad($appt['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($appt['service_type']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['appointment_date']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['appointment_time']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['address']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['special_instructions']); ?></td>
<td>
    <span class="status-badge status-<?php echo htmlspecialchars($appt['status']); ?>">
        <?php echo ucfirst($appt['status']); ?>
    </span>
</td>
                                    <?php if ($userRole == 'admin' || $userRole == 'staff'): ?>
                                        <td>
                                            <a href="viewAppointment.php?edit_id=<?php echo $appt['id']; ?>" class="action-btn btn-edit">Edit</a>
<a href="viewAppointment.php?delete_id=<?php echo $appt['id']; ?>" 
   class="action-btn btn-delete" 
   onclick="return confirm('Are you sure you want to delete this appointment?');">
   Delete
</a>

                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

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
    <script src="./assets/js/vendor/jquery-1.12.4.min.js"></script>
    <script src="./assets/js/popper.min.js"></script>
    <script src="./assets/js/bootstrap.min.js"></script>
    <script src="./assets/js/jquery.slicknav.min.js"></script>
    <script src="./assets/js/owl.carousel.min.js"></script>
    <script src="./assets/js/slick.min.js"></script>
    <script src="./assets/js/main.js"></script>
</body>
</html>
