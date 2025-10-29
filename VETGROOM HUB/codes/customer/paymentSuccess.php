<?php
session_start();

require 'vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51SJPi3HJTCD7wkr0yVLCvVEvu9QBgmiEYhtxRaywBd8k9owTItGR0IrnKMWoZ8egBklzZZj51LQ2yewft6Jcmrdk00RfjDHjxm'); // Replace with your actual Stripe secret key

// Check if user is logged in
if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}

// Check if Stripe session ID is available
if (!isset($_GET['session_id'])) {
    die("Transaction ID missing.");
}

try {
    // Retrieve checkout session and payment details
    $session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);
    $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

    $txn_id = $paymentIntent->id; // Stripe PaymentIntent ID (acts as transaction ID)
    $amount_paid = number_format($paymentIntent->amount / 100, 2);
    $currency = strtoupper($paymentIntent->currency);
    $status = ucfirst($paymentIntent->status);
    $service = $session->metadata->service ?? "N/A";
    $appointment_id = $session->metadata->appointment_id ?? "N/A";

    // Optionally save payment info to DB
    /*
    $conn = new mysqli("localhost", "root", "", "vetgroomlist");
    $stmt = $conn->prepare("UPDATE appointments SET payment_id=?, status='paid' WHERE id=?");
    $stmt->bind_param("si", $txn_id, $appointment_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    */

} catch (Exception $e) {
    die("Error verifying payment: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful - VetGroom Hub</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', sans-serif;
            background-image: url('assets/img/hero/hero2.png');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
            margin: 0;
        }
        .receipt-card {
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 450px;
        }
        .receipt-card h2 {
            color: #28a745;
        }
        .receipt-details {
            text-align: left;
            margin-top: 20px;
            font-size: 16px;
        }
        .return-btn {
            margin-top: 20px;
            padding: 12px;
            background: #28a745;
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .return-btn:hover {
            background: #34c759;
        }
    </style>
</head>
<body>
    <div class="receipt-card">
        <h2>Payment Successful ✅</h2>
        <p>Thank you for your payment!</p>
        <div class="receipt-details">
            <p><strong>Service:</strong> <?php echo htmlspecialchars($service); ?></p>
            <p><strong>Transaction ID:</strong> <?php echo htmlspecialchars($txn_id); ?></p>
            <p><strong>Amount Paid:</strong> RM <?php echo htmlspecialchars($amount_paid . ' ' . $currency); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($status); ?></p>
            <p><strong>User:</strong> <?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
            <p><strong>Appointment Ref:</strong> APT<?php echo str_pad($appointment_id, 5, '0', STR_PAD_LEFT); ?></p>
        </div>
        <button class="return-btn" onclick="window.location.href='viewAppointment.php'">View Appointment</button>
    </div>
</body>
</html>
