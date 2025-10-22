<?php
require_once 'config.php';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Check if email+token exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND token = ? AND verified = 0");
    $stmt->bind_param("ss", $email, $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Mark user as verified
        $update = $conn->prepare("UPDATE users SET verified = 1, token = '' WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();

        echo "<div style='text-align:center;'>
                <h2 style='color:green;'>Your email has been verified successfully. You can now log in!</h2>
                <a href='index.php' style='
                    display:inline-block;
                    margin-top:20px;
                    padding:10px 20px;
                    background-color:#28a745;
                    color:white;
                    text-decoration:none;
                    border-radius:6px;
                    font-weight:bold;
                '>Go to Homepage</a>
              </div>";
    } else {
        echo "<h2 style='color:red; text-align:center;'>Invalid or expired verification link.</h2>";
    }
}
?>