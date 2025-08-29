<?php
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $otp = trim($_POST['otp'] ?? '');

    if (empty($otp)) {
        echo json_encode(["status"=>"error","message"=>"OTP is required"]);
        exit;
    }

    $conn = new mysqli("localhost","root","","vetGroomlist");
    $conn->set_charset("utf8mb4");

    $stmt = $conn->prepare("SELECT id, otp_expires FROM users WHERE otp=? AND verified=0");
    $stmt->bind_param("s",$otp);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows===0) {
        echo json_encode(["status"=>"error","message"=>"Invalid OTP or already verified"]);
        exit;
    }

    $stmt->bind_result($userId, $otpExpiry);
    $stmt->fetch();

    if ($otpExpiry && strtotime($otpExpiry) < time()) {
        echo json_encode(["status"=>"error","message"=>"OTP expired"]);
        exit;
    }

    $update = $conn->prepare("UPDATE users SET verified=1, otp=NULL, otp_expires=NULL WHERE id=?");
    $update->bind_param("i",$userId);
    $update->execute();

    echo json_encode(["status"=>"success","message"=>"✅ Email verified successfully! You may now log in."]);
}
