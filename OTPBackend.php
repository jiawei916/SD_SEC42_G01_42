<?php
$host = "localhost";
$user = "root";   // Change if your MySQL has another user
$pass = "";       // Your MySQL password
$db   = "vetgroomlist";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
