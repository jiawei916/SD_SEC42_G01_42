<?php
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/phpmailer/src/Exception.php';
require 'vendor/phpmailer/src/PHPMailer.php';
require 'vendor/phpmailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
require_once 'config.php';

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        $conn->set_charset("utf8mb4");

        $email = trim($_POST["email"] ?? "");
        if (empty($email)) {
            echo json_encode(["status" => "error", "message" => "Email is required."]);
            exit;
        }

        // Check if user exists
        $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $userId = $row["id"];
            $userName = $row["username"];

            // Generate reset token + OTP
            $token = bin2hex(random_bytes(16));
            $otp = random_int(100000, 999999);
            $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));

            // Store token + OTP in DB
$update = $conn->prepare("UPDATE users SET otp = ?, otp_expires = ? WHERE id = ?");
$update->bind_param("ssi", $otp, $expires, $userId);
$update->execute();


            // Send OTP via PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'tanqiheng68@gmail.com'; // replace with your SMTP email
                $mail->Password   = 'crixuuvzceypfjxu';    // replace with your app password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('no-reply@vetgroom.com', 'VetGroom Hub');
                $mail->addAddress($email, $userName);

                $mail->isHTML(false);
                $mail->Subject = 'Your VetGroom Password Reset OTP';
                $mail->Body    = "Hello $userName,\n\nYour OTP for password reset is: $otp\nIt will expire in 15 minutes.\n\nThank you!";

                $mail->send();

                echo json_encode([
                    "status" => "success",
                    "message" => "OTP sent to your email. Please check your inbox.",
                    "redirect" => "forgetPasswordotp.html?token=" . urlencode($token)
                ]);

            } catch (Exception $e) {
                // Email failed, but token + OTP still stored
                echo json_encode([
                    "status" => "warning",
                    "message" => "OTP not sent via email. Use this OTP for testing: $otp",
                    "redirect" => "forgetPasswordotp.html?token=" . urlencode($token)
                ]);
            }

        } else {
            echo json_encode(["status" => "error", "message" => "No account found with that email."]);
        }

        $stmt->close();
        $conn->close();

    } catch (mysqli_sql_exception $e) {
        echo json_encode([
            "status" => "error",
            "message" => "Unexpected error: " . $e->getMessage()
        ]);
    }
}
