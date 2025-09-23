<?php
session_start();
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "vetgroomlist";

$conn = new mysqli($host, $user, $pass, $db);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Make sure session has email
if (!isset($_SESSION['otp_email'])) {
    echo json_encode(["status" => "error", "message" => "No OTP session found. Please request a new one."]);
    exit;
}

$email = $_SESSION['otp_email'];

// ----- Verify OTP -----
if (isset($_POST['otp'])) {
    $otp_input = trim($_POST['otp']);

    $stmt = $conn->prepare("SELECT id, otp FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $otp_db);
    $stmt->fetch();
    $stmt->close();

    if (!$otp_db) {
        echo json_encode(["status" => "error", "message" => "No OTP found. Please request a new one."]);
        exit;
    }

    if ($otp_input === $otp_db) {
        // Clear OTP
        $stmt = $conn->prepare("UPDATE users SET otp=NULL WHERE id=?");
        $stmt = $conn->prepare("UPDATE users SET verified=1 WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        echo json_encode([
            "status" => "success",
            "message" => "✅ OTP verified successfully!",
            "redirect" => "signIn.php" . urlencode($email)
        ]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "❌ Invalid OTP."]);
        exit;
    }
}

// ----- Resend OTP -----
if (isset($_POST['resend'])) {

    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($user_id, $username);
    $stmt->fetch();
    $stmt->close();

    if (!$user_id) {
        echo json_encode(["status" => "error", "message" => "Email not found"]);
        exit;
    }

    // Generate new OTP
    $otp_new = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

    $stmt = $conn->prepare("UPDATE users SET otp=? WHERE id=?");
    $stmt->bind_param("si", $otp_new, $user_id);
    $stmt->execute();
    $stmt->close();

    // Send OTP via PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tanqiheng68@gmail.com';
        $mail->Password   = 'crixuuvzceypfjxu';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('no-reply@vetgroom.com', 'VetGroom Hub');
        $mail->addAddress($email, $username);

        $mail->isHTML(false);
        $mail->Subject = 'Your VetGroom OTP';
        $mail->Body    = "Hello $username,\nYour OTP is: $otp_new";

        $mail->send();

        echo json_encode(["status" => "success", "message" => "📧 OTP sent to your email."]);
        exit;

    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "⚠️ Mail could not be sent."]);
        exit;
    }
}

// Default fallback
echo json_encode(["status" => "error", "message" => "Invalid request"]);
exit;
