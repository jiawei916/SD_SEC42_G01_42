<?php
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
error_reporting(E_ALL);
ini_set('display_errors', 1); // Show errors for debugging

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "vetGroomlist";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        $conn->set_charset("utf8mb4");

        $name    = trim($_POST["name"] ?? "");
        $email   = trim($_POST["email"] ?? "");
        $rawPass = $_POST["password"] ?? "";

        if (empty($name) || empty($email) || empty($rawPass)) {
            echo json_encode(["status" => "error", "message" => "All fields are required."]);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email format."]);
            exit;
        }

        $hashedPass = password_hash($rawPass, PASSWORD_DEFAULT);
        $otp = random_int(100000, 999999); // 6-digit OTP
        $otpExpiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        $stmt = $conn->prepare("INSERT INTO users (name, email, password, verified, otp, otp_expires) VALUES (?, ?, ?, 0, ?, ?)");
        $stmt->bind_param("sssss", $name, $email, $hashedPass, $otp, $otpExpiry);
        $stmt->execute();

        // PHPMailer setup
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';       // SMTP host
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tanqiheng68@gmail.com'; // SMTP email
            $mail->Password   = 'awxbgunjtrmayqul';     // App password
            $mail->SMTPSecure = 'tls';                  
            $mail->Port       = 587;                    

            $mail->setFrom('no-reply@vetgroom.com', 'VetGroom Hub');
            $mail->addAddress($email, $name);

            $mail->isHTML(false);
            $mail->Subject = 'Your VetGroom Verification OTP';
            $mail->Body    = "Hello $name,\n\nYour OTP for email verification is: $otp\nIt will expire in 15 minutes.\n\nThank you!";

            $mail->send();

            // Success response
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful! Check your email for the OTP.",
                "redirect" => "emailVerification.php" // your OTP page
            ]);

        } catch (Exception $e) {
            // Email sending failed, still return success but warn user
            echo json_encode([
                "status" => "warning",
                "message" => "Registration successful! ⚠️ Email not sent. Mailer Error: " . $mail->ErrorInfo,
                "otp" => $otp, // For testing only
                "redirect" => "emailVerification.php"
            ]);
        }

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            echo json_encode(["status" => "error","message" => "This email is already registered."]);
        } else {
            echo json_encode(["status" => "error","message" => "DB error: " . $e->getMessage()]);
        }
    }
}
