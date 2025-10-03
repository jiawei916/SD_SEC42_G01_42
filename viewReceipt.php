<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user_name'])) {
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

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user's receipts
if ($userRole == 'admin') {
    $sql = "SELECT a.*, u.username, u.first_name, u.last_name, s.price 
            FROM appointments a 
            LEFT JOIN users u ON a.user_id = u.id 
            LEFT JOIN services s ON a.service_type = s.name 
            WHERE a.status IN ('confirmed', 'completed') 
            ORDER BY a.created_at DESC";
    $stmt = $conn->prepare($sql);
} else {
    $sql = "SELECT a.*, s.price 
            FROM appointments a 
            LEFT JOIN services s ON a.service_type = s.name 
            WHERE a.user_id = ? AND a.status IN ('confirmed', 'completed') 
            ORDER BY a.created_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
}

$stmt->execute();
$result = $stmt->get_result();
$receipts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $receipts[] = $row;
    }
}
$stmt->close();
$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Receipts - VetGroom Hub</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f9f9f9; padding-top: 80px; }
        .table-container { background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .btn-view { background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 4px; text-decoration: none; }
        .btn-view:hover { background-color: #ff707f; color: white; }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h2>My Receipts</h2>
            <a href="viewAppointment.php" class="btn btn-secondary btn-sm">Back to Appointments</a>
        </div>
    </header>

    <main class="container mt-4">
        <div class="table-container">
            <?php if (count($receipts) === 0): ?>
                <p>No receipts found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="thead-light">
                            <tr>
                                <?php if ($userRole == 'admin'): ?>
                                    <th>Customer Name</th>
                                <?php endif; ?>
                                <th>Receipt No.</th>
                                <th>Service</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($receipts as $receipt): ?>
                                <tr>
                                    <?php if ($userRole == 'admin'): ?>
                                        <td><?php echo htmlspecialchars($receipt['first_name'] . ' ' . $receipt['last_name']); ?></td>
                                    <?php endif; ?>
                                    <td>RCP<?php echo str_pad($receipt['id'], 6, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo htmlspecialchars($receipt['service_type']); ?></td>
                                    <td><?php echo date('M j, Y', strtotime($receipt['appointment_date'])); ?></td>
                                    <td>$<?php echo number_format($receipt['price'], 2); ?></td>
                                    <td><span class="badge badge-success">Paid</span></td>
                                    <td>
                                        <a href="viewReceipt.php?id=<?php echo $receipt['id']; ?>" class="btn-view">
                                            View Receipt
                                        </a>
                                        <a href="viewReceipt.php?id=<?php echo $receipt['id']; ?>&print=1" class="btn-view" style="background-color: #17a2b8;">
                                            Print
                                        </a>
                                    </td>
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