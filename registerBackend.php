<?php
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $servername = "localhost";
    $usernameDB = "root";
    $passwordDB = "";
    $dbname     = "vetGroomlist";

    $errors = [];

    // Collect inputs (match your form names)
    $username = trim($_POST["username"] ?? "");
    $email    = trim($_POST["email"] ?? "");
    $rawPass  = $_POST["password"] ?? "";
    $confirm  = $_POST["confirmPassword"] ?? "";

    // Basic validation
    if ($username === "") {
        $errors["username"] = "Username is required.";
    }
    if ($email === "") {
        $errors["email"] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Invalid email format.";
    }
    if ($rawPass === "") {
        $errors["password"] = "Password is required.";
    } elseif (strlen($rawPass) < 5) {
        $errors["password"] = "Password must be at least 5 characters.";
    }
    if ($confirm === "") {
        $errors["confirmPassword"] = "Please confirm your password.";
    } elseif ($rawPass !== $confirm) {
        $errors["confirmPassword"] = "Passwords do not match.";
    }

    if (!empty($errors)) {
        echo json_encode(["status" => "error", "errors" => $errors]);
        exit;
    }

    try {
        // DB connection
        $conn = new mysqli($servername, $usernameDB, $passwordDB, $dbname);
        $conn->set_charset("utf8mb4");

        // Check duplicate email
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo json_encode(["status" => "error", "errors" => ["email" => "This email is already registered."]]);
            exit;
        }
        $stmt->close();

        // Insert new user
        $hashedPass = password_hash($rawPass, PASSWORD_DEFAULT);
        $otp        = random_int(100000, 999999);
        $otpExpiry  = date("Y-m-d H:i:s", strtotime("+15 minutes"));

        $stmt = $conn->prepare(
            "INSERT INTO users (username, email, password, verified, otp, otp_expires) VALUES (?, ?, ?, 0, ?, ?)"
        );
        $stmt->bind_param("sssss", $username, $email, $hashedPass, $otp, $otpExpiry);
        $stmt->execute();
        $stmt->close();

        // Send OTP email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'tanqiheng68@gmail.com'; // use env var in production
            $mail->Password   = 'crixuuvzceypfjxu';      // app password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('no-reply@vetgroom.com', 'VetGroom Hub');
            $mail->addAddress($email, $username);

            $mail->isHTML(false);
            $mail->Subject = 'Your VetGroom Verification OTP';
            $mail->Body    = "Hello $username,\n\nYour OTP for email verification is: $otp\nIt will expire in 15 minutes.\n\nThank you!";

            $mail->send();
session_start();
$_SESSION['otp_email'] = $email;
$response = [
    "status"  => "success",
    "message" => "Registration successful! Please check your email for the OTP.",
    "redirect"=> "OTP.php"
];

echo json_encode($response);
exit;

        } catch (Exception $e) {
            // Mail failed but user created
            echo json_encode([
                "status"   => "warning",
                "message"  => "Registration successful, but OTP email could not be sent. Error: " . $mail->ErrorInfo,
                "redirect" => "emailVerification.php"
            ]);
        }

    } catch (mysqli_sql_exception $e) {
        echo json_encode([
            "status"  => "error",
            "message" => "Database error: " . $e->getMessage()
        ]);
    }
}
