<?php
// Database connection
$servername = "localhost";
$username = "root";     // change if needed
$password = "";         // change if needed
$dbname = "vetGroomList";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check DB connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "Error: Email already registered.";
        $check->close();
        $conn->close();
        exit;
    }
    $check->close();

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, verified) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        echo "Registration successful! Please check your email for verification.";
    } else {
        echo "Error: Could not register user.";
    }

    $stmt->close();
}

$conn->close();
?>
