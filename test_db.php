<?php
echo "Testing InfinityFree Database Connection...<br>";

// Include your config
require_once 'config.php';

// Test connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error);
} else {
    echo "✅ Database connected successfully!<br>";
}

// Test if users table exists
$sql = "SHOW TABLES LIKE 'users'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "✅ Users table exists!<br>";
    
    // Count users (optional)
    $count_result = $conn->query("SELECT COUNT(*) as total FROM users");
    if ($count_result) {
        $row = $count_result->fetch_assoc();
        echo "✅ Total users: " . $row['total'] . "<br>";
    }
} else {
    echo "❌ Users table not found!<br>";
}

$conn->close();
?>