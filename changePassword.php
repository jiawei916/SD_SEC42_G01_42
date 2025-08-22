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

        // ✅ Ensure user is logged in
        if (empty($_SESSION["email"])) {
            echo json_encode(["status" => "error", "message" => "User not logged in."]);
            exit;
        }

        $userEmail       = $_SESSION["email"];
        $currentPassword = $_POST["currentPassword"] ?? "";
        $newPassword     = $_POST["newPassword"] ?? "";

        if (empty($currentPassword) || empty($newPassword)) {
            echo json_encode(["status" => "error", "message" => "Both current and new passwords are required."]);
            exit;
        }

        // ✅ Fetch stored password hash
        $stmt = $conn->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->bind_param("s", $userEmail);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verify current password
            if (!password_verify($currentPassword, $row["password"])) {
                echo json_encode(["status" => "error", "message" => "Current password is incorrect."]);
                exit;
            }

            // ✅ Hash new password
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // ✅ Update database
            $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->bind_param("ss", $newHashedPassword, $userEmail);

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
?>
