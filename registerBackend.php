<?php
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
error_reporting(E_ALL);
ini_set('display_errors', 0);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "vetGroomlist"; // Fixed database name

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        $conn->set_charset("utf8mb4");

        // Match form field names
        $name     = trim($_POST["name"] ?? "");
        $email    = trim($_POST["email"] ?? "");
        $rawPass  = $_POST["password"] ?? "";

        if (empty($name) || empty($email) || empty($rawPass)) {
            echo json_encode(["status" => "error", "message" => "All flds are required."]);
            exit;
        }

        // Add email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => "error", "message" => "Invalid email format."]);
            exit;
        }

        $hashedPass = password_hash($rawPass, PASSWORD_DEFAULT);
        $token      = bin2hex(random_bytes(16));

        // Use 'name' column instead of 'username' to match your preset
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, verification_token, email_verified) VALUES (?, ?, ?, ?, 0)");
        $stmt->bind_param("ssss", $name, $email, $hashedPass, $token);
        $stmt->execute();

        $verifyLink = "http://localhost/vetgroom/verify.php?email=" . urlencode($email) . "&token=" . $token;

        // Email sending code...
        if (@mail($email, "Verify your VetGroom account",
            "Click here to verify your account: $verifyLink",
            "From: no-reply@vetgroom.com")) {
            echo json_encode(["status" => "success", "message" => "Registration successful! Please check your email."]);
        } else {
            echo json_encode(["status" => "warning", "message" => "⚠️ Email not sent (dev mode). Use this link: $verifyLink"]);
        }

    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) { 
            echo json_encode([
                "status" => "error",
                "message" => "This email is already registered. Please sign in instead."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Unexpected DB error: " . $e->getMessage()
            ]);
        }
    }
}