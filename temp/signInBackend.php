<?php
header("Content-Type: application/json");
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "vetGroomList";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        $conn->set_charset("utf8mb4");

        $email   = trim($_POST["email"] ?? "");
        $rawPass = $_POST["password"] ?? "";

        if (empty($email) || empty($rawPass)) {
            echo json_encode(["status" => "error", "message" => "Email and password are required."]);
            exit;
        }

        // Check if user exists
        $stmt = $conn->prepare("SELECT id, name, email, password, verified, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if (!password_verify($rawPass, $row["password"])) {
                echo json_encode(["status" => "error", "message" => "Invalid password."]);
                exit;
            }

            if ($row["verified"] == 0) {
                echo json_encode(["status" => "error", "message" => "Please verify your email before signing in."]);
                exit;
            }

            // Store session data
            $_SESSION["user_id"]    = $row["id"];
            $_SESSION["user_name"]  = $row["name"];
            $_SESSION["user_email"] = $row["email"];
            $_SESSION["user_role"]  = $row["role"];
            $_SESSION["logged_in"]  = true;
            error_log("DEBUG: User role is " . $row['role']);
            // Determine redirect page based on role
            switch (strtolower($row["role"])) {
                case 'admin':
                    $redirect = "viewDashboardAdmin.php";
                    break;
                case 'staff':
                    $redirect = "viewDashboardStaff.php";
                    break;
                default:
                    $redirect = "homepage.php";
            }

            echo json_encode(["status" => "success", "message" => "Login successful!", "redirect" => $redirect]);

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
