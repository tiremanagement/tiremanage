<?php
// Simple PHPMailer test script.
// Edit the variables below with your SMTP credentials and run: php test_mail.php

require __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

$smtpHost = 'smtp.gmail.com';
$smtpPort = 587;
$smtpUser = 'your.email@gmail.com';
$smtpPass = 'your_app_password_here'; // 16-char Gmail app password
$fromEmail = 'your.email@gmail.com';
$fromName  = 'You';
$toEmail   = 'recipient@example.com';
$toName    = 'Recipient';

echo "Using SMTP host: $smtpHost:$smtpPort\n";

$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Host = $smtpHost;
    $mail->SMTPAuth = true;
    $mail->Username = $smtpUser;
    $mail->Password = $smtpPass;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = $smtpPort;

    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($toEmail, $toName);

    $mail->isHTML(true);
    $mail->Subject = 'PHPMailer test from tiremanage';
    $mail->Body    = '<p>This is a test email from <strong>tiremanage</strong>.</p>';

    $mail->send();
    echo "Email sent successfully to $toEmail\n";
} catch (Exception $e) {
    echo "Mailer Error: " . $mail->ErrorInfo . "\n";
}
