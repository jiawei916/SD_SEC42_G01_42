<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != 'admin') {
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

// Handle search and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$roleFilter = isset($_GET['role']) ? $_GET['role'] : '';

// Fetch all staff members (staff and admin roles)
$sql = "SELECT id, name, role, email, verified, created_at FROM users WHERE role IN ('staff', 'admin')";
$params = [];
$types = "";

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR email LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

if (!empty($roleFilter)) {
    $sql .= " AND role = ?";
    $params[] = $roleFilter;
    $types .= "s";
}

$sql .= " ORDER BY role, name";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$staffMembers = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$conn->close();
?>

<!doctype html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Staff Management - VetGroom Hub</title>
    <meta name="description" content="Admin staff management dashboard">
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
        
        /* Search and filter styling */
        .filter-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .filter-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }
        
        .search-input, .filter-select {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 16px;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
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
            display: inline-flex;
            align-items: center;
        }
        
        .clear-btn:hover {
            background-color: #5a6268;
            color: white;
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
            cursor: pointer;
        }
        
        .staff-table th:hover {
            background-color: #e9ecef;
        }
        
        .staff-table tr:hover {
            background-color: #f8f9fa;
        }
        
        .action-btn {
            background-color: #4a90e2;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }
        
        .action-btn:hover {
            background-color: #357abd;
        }
        
        .btn-edit {
            background-color: #28a745;
        }
        
        .btn-edit:hover {
            background-color: #218838;
        }
        
        .btn-deactivate {
            background-color: #dc3545;
        }
        
        .btn-deactivate:hover {
            background-color: #c82333;
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
        
        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
            }
            
            .filter-group {
                min-width: 100%;
            }
            
            .staff-table {
                font-size: 14px;
            }
            
            .staff-table th, .staff-table td {
                padding: 8px 10px;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
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
        
        .no-staff {
            text-align: center;
            padding: 40px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
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
                                            <?php if ($isLoggedIn): ?>
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

    <!-- ✅ Staff Management Section -->
    <main class="dashboard-container">
        <div class="dashboard-card">
            <div class="dashboard-header">
                <h2>Staff Management</h2>
                <p>View and manage all staff members and administrators</p>
            </div>

            <!-- Stats Cards -->
            <?php
            $adminCount = 0;
            $staffCount = 0;
            $verifiedCount = 0;
            
            foreach ($staffMembers as $member) {
                if ($member['role'] == 'admin') $adminCount++;
                if ($member['role'] == 'staff') $staffCount++;
                if ($member['verified']) $verifiedCount++;
            }
            ?>
            
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($staffMembers); ?></div>
                    <div class="stat-label">Total Staff</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $adminCount; ?></div>
                    <div class="stat-label">Administrators</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $staffCount; ?></div>
                    <div class="stat-label">Staff Members</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $verifiedCount; ?></div>
                    <div class="stat-label">Verified Accounts</div>
                </div>
            </div>

            <!-- Search and Filter Section -->
            <div class="filter-section">
                <form method="GET" action="viewStaff.php">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label class="filter-label">Search by Name or Email</label>
                            <input type="text" class="search-input" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Enter name or email...">
                        </div>
                        
                        <div class="filter-group">
                            <label class="filter-label">Filter by Role</label>
                            <select class="filter-select" name="role">
                                <option value="">All Roles</option>
                                <option value="admin" <?php echo $roleFilter == 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                <option value="staff" <?php echo $roleFilter == 'staff' ? 'selected' : ''; ?>>Staff</option>
                            </select>
                        </div>
                        
                        <div class="filter-buttons">
                            <button type="submit" class="search-btn">Apply Filters</button>
                            <?php if (!empty($search) || !empty($roleFilter)): ?>
                                <a href="viewStaff.php" class="clear-btn">Clear Filters</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Staff List -->
            <div class="staff-list">
                <h3>Staff Members (<?php echo count($staffMembers); ?>)</h3>
                
                <?php if (count($staffMembers) > 0): ?>
                    <table class="staff-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Member Since</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staffMembers as $staff): ?>
                                <tr>
                                    <td><?php echo $staff['id']; ?></td>
                                    <td><?php echo htmlspecialchars($staff['name']); ?></td>
                                    <td>
                                        <span class="role-badge <?php echo $staff['role'] == 'admin' ? 'role-admin' : 'role-staff'; ?>">
                                            <?php echo ucfirst($staff['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($staff['email']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $staff['verified'] ? 'status-verified' : 'status-pending'; ?>">
                                            <?php echo $staff['verified'] ? 'Verified' : 'Pending'; ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($staff['created_at'])); ?></td>
                                    <td>
                                        <a href="editStaff.php?id=<?php echo $staff['id']; ?>" class="action-btn btn-edit">Edit</a>
                                        <button class="action-btn btn-deactivate">Deactivate</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-staff">
                        <h3>No staff members found</h3>
                        <p>Try adjusting your search or filter criteria.</p>
                    </div>
                <?php endif; ?>
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
        // Simple table sorting functionality
        document.addEventListener('DOMContentLoaded', function() {
            const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
            
            const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
                v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
            )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
            
            document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
                const table = th.closest('table');
                const tbody = table.querySelector('tbody');
                Array.from(tbody.querySelectorAll('tr'))
                    .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                    .forEach(tr => tbody.appendChild(tr));
            })));
        });
    </script>
</body>
</html>