<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}

$userName  = $_SESSION['user_name'];
$userRole  = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : "customer";
$userId    = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "vetgroomlist";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch appointments
if ($userRole == 'admin' || $userRole == 'staff') {
    $sql = "SELECT a.*, u.username, u.email FROM appointments a 
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
$appointments = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $appointments[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments - VetGroom Hub</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: #f9f9f9;
            padding-top: 80px;
        }
        .table-container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .status-pending { color: #ff9800; font-weight: 600; }
        .status-confirmed { color: #4caf50; font-weight: 600; }
        .status-completed { color: #2196f3; font-weight: 600; }
        .status-cancelled { color: #f44336; font-weight: 600; }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <h2>Welcome, <?php echo htmlspecialchars($userName); ?></h2>
            <a href="homepage.php" class="btn btn-secondary btn-sm">Home</a>
            <a href="bookAppointment.php" class="btn btn-primary btn-sm">Book New Appointment</a>
        </div>
    </header>

    <main class="container mt-4">
        <div class="table-container">
            <h3>My Appointments</h3>
            <?php if (count($appointments) === 0): ?>
                <p>No appointments found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
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
                                    <td class="status-<?php echo $appt['status']; ?>"><?php echo ucfirst($appt['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
