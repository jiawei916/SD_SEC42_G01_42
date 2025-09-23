<?php
header("Content-Type: application/json");
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new mysqli("localhost", "root", "", "vetGroomList");
    $conn->set_charset("utf8mb4");

    $userId = $_SESSION["user_id"];
    $name   = trim($_POST["name"] ?? "");
    $email  = trim($_POST["email"] ?? "");

    if (empty($name) || empty($email)) {
        echo json_encode(["status" => "error", "message" => "Name and email are required"]);
        exit;
    }

    // Check if email already exists for another user
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $check->bind_param("si", $email, $userId);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Email already taken"]);
        exit;
    }
    $check->close();

    // Update DB
    $stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
    $stmt->bind_param("ssi", $name, $email, $userId);

    if ($stmt->execute()) {
        // ✅ Update session to reflect new values
        $_SESSION["user_name"]  = $name;
        $_SESSION["email"] = $email;

        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Update failed"]);
    }

    $stmt->close();
    $conn->close();
}
