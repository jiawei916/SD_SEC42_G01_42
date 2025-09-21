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

    // Check OTP validity only
    $stmt = $conn->prepare("SELECT id, otp_expires FROM users WHERE otp=?");
    $stmt->bind_param("s", $otp);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo json_encode(["status"=>"error","message"=>"Invalid OTP"]);
        exit;
    }

    $stmt->bind_result($userId, $expires);
    $stmt->fetch();

    if ($expires && strtotime($expires) < time()) {
        echo json_encode(["status"=>"error","message"=>"OTP expired. Please request a new password reset."]);
        exit;
    }

    // Mark OTP as verified
    $update = $conn->prepare("UPDATE users SET otp=NULL, otp_expires=NULL, reset_token=NULL, reset_expires=NULL WHERE id=?");
    $update->bind_param("i", $userId);
    $update->execute();

    echo json_encode([
        "status" => "success",
        "message" => "✅ OTP verified successfully! Redirecting to change password...",
        "redirect" => "changePassword.html?user_id=" . $userId
    ]);
}
