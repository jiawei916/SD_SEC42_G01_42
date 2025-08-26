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

        $email = trim($_POST["email"] ?? "");

        if (empty($email)) {
            echo json_encode(["status" => "error", "message" => "Email is required."]);
            exit;
        }

        // ✅ Check if user exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $userId = $row["id"];

            // ✅ Generate token
            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

            // Store token in DB (make sure users table has reset_token + reset_expires)
            $update = $conn->prepare("UPDATE users SET verification_token = ?, reset_expires = ? WHERE id = ?");
            $update->bind_param("ssi", $token, $expires, $userId);
            $update->execute();

            // ✅ Return redirect URL in JSON instead of header()
            echo json_encode([
                "status" => "success",
                "redirect" => "changePassword.html?token=" . urlencode($token)
            ]);
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
