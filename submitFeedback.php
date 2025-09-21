<?php
// Database connection settings
$servername = "localhost";
$username   = "root";     // default in XAMPP
$password   = "";         // default in XAMPP
$dbname     = "vetgroomlist";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<script>alert('Database connection failed.'); window.location.href='feedback.php';</script>");
}

// Get form data safely
$name    = $conn->real_escape_string($_POST['username']);
$email   = $conn->real_escape_string($_POST['email']);
$message = $conn->real_escape_string($_POST['message']);

// Insert into database
$sql = "INSERT INTO feedback (username, email, feedback) 
        VALUES ('$name', '$email', '$message')";

if ($conn->query($sql) === TRUE) {
    // ✅ Alert then redirect back
    echo "<script>
            alert('Thank you! Your feedback has been submitted.');
            window.location.href = 'feedback.php';
          </script>";
} else {
    // ❌ Show error if something went wrong
    echo "<script>
            alert('Error submitting feedback: " . $conn->error . "');
            window.location.href = 'feedback.php';
          </script>";
}

$conn->close();
?>
