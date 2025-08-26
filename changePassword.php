<?php
header("Content-Type: application/json");
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "vetGroomList";

    try {
        $conn = new mysqli($servername, $username, $password, $dbname);
        $conn->set_charset("utf8mb4");

        // ✅ Get data from request
        $token          = $_POST["token"] ?? "";
        $newPassword    = $_POST["newPassword"] ?? "";
        $confirmPassword= $_POST["confirmPassword"] ?? "";

        if (empty($token) || empty($newPassword) || empty($confirmPassword)) {
            echo json_encode(["status" => "error", "message" => "All fields are required."]);
            exit;
        }

        if ($newPassword !== $confirmPassword) {
            echo json_encode(["status" => "error", "message" => "Passwords do not match."]);
            exit;
        }

        // ✅ Find user by reset token
        $stmt = $conn->prepare("SELECT email FROM users WHERE verification_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $userEmail = $row["email"];

            // ✅ Hash new password
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // ✅ Update password & clear reset token
            $update = $conn->prepare("UPDATE users SET password = ?, verification_token = NULL WHERE email = ?");
            $update->bind_param("ss", $newHashedPassword, $userEmail);

            if ($update->execute()) {
                echo json_encode(["status" => "success", "message" => "Password updated successfully."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update password."]);
            }

            $update->close();
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid or expired token."]);
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
?>
