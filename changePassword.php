<?php
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
require_once 'config.php';

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        $conn->set_charset("utf8mb4");

        // ✅ Get data from request
        $userId         = $_POST["user_id"] ?? "";
        $newPassword    = $_POST["newPassword"] ?? "";
        $confirmPassword= $_POST["confirmPassword"] ?? "";

        if (empty($userId) || empty($newPassword) || empty($confirmPassword)) {
            echo json_encode(["status" => "error", "message" => "All fields are required."]);
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
            exit;
        }

        // ✅ Find user by ID
        $stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $userEmail = $row["email"];

            // ✅ Hash new password
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // ✅ Update password
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->bind_param("si", $newHashedPassword, $userId);

            if ($update->execute()) {
                echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update password."]);
            }

            $update->close();
        } else {
            echo json_encode(["status" => "error", "message" => "User not found."]);
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
