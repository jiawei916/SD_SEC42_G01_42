<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    header("Location: signIn.php");
    exit();
}

require 'vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51SJPi3HJTCD7wkr0yVLCvVEvu9QBgmiEYhtxRaywBd8k9owTItGR0IrnKMWoZ8egBklzZZj51LQ2yewft6Jcmrdk00RfjDHjxm'); // 🔑 Replace with your Stripe key

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $service = isset($_POST['service']) ? htmlspecialchars($_POST['service']) : '';
    $appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : 0;

    if ($amount <= 0 || empty($service) || $appointment_id <= 0) {
        die("Invalid payment request. Please go back and try again.");
    }

    $amount_cents = intval($amount * 100);

    try {
        // ✅ Create Stripe Checkout Session (supports FPX)
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['fpx'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => 'myr',
                    'product_data' => [
                        'name' => 'VetGroom Hub - ' . $service,
                    ],
                    'unit_amount' => $amount_cents,
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'service' => $service,
                'appointment_id' => $appointment_id,
                'user' => $_SESSION['user_name']
            ],
            'success_url' => 'http://localhost/vetgroom/paymentSuccess.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => 'http://localhost/vetgroom/paymentCancel.php',
        ]);

        // Redirect user to Stripe-hosted FPX payment page
        header("Location: " . $checkout_session->url);
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
} else {
    $error = "Invalid access method.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Portal - VetGroom Hub</title>
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
        <?php if (!empty($error)): ?>
            <h2>⚠️ Payment Error</h2>
            <p><?php echo htmlspecialchars($error); ?></p>
            <button class="return-btn" onclick="window.location.href='bookAppointment.php'">Back to Booking</button>
        <?php else: ?>
            <h2>Redirecting to FPX Payment...</h2>
            <p>Please wait while we redirect you to Stripe’s secure FPX page.</p>
        <?php endif; ?>
    </div>
</body>
</html>
