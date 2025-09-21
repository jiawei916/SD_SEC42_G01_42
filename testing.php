<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . "/vendor/phpmailer/src/Exception.php";
require __DIR__ . "/vendor/phpmailer/src/PHPMailer.php";
require __DIR__ . "/vendor/phpmailer/src/SMTP.php";

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.mail.yahoo.com'; 
    $mail->SMTPAuth   = true;
    $mail->Username   = 'your_yahoo_email@yahoo.com';  // replace with your Yahoo address
    $mail->Password   = 'your_app_password';          // generated App Password, not your login
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom('your_yahoo_email@yahoo.com', 'VetGroom');
    $mail->addAddress('receiver@gmail.com', 'Receiver Name'); // change to test email

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email via Yahoo SMTP';
    $mail->Body    = '<p>Hello, this is a <b>test email</b> sent with Yahoo SMTP and PHPMailer.</p>';

    $mail->send();
    echo '✅ Message sent successfully!';
} catch (Exception $e) {
    echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
