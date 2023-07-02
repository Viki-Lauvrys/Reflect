<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

// Create a new PHPMailer object
$mail = new PHPMailer(true); // Set "true" to enable exceptions
$param1 = $_GET['param1'];
$param2 = $_GET['param2'];

try {
    // Enable verbose debugging
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;

    // Set mailer to use SMTP
    $mail->isSMTP();

    // SMTP configuration
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'reflect.school@gmail.com';
    $mail->Password   = 'linpzvpclgqefcdj';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    // Set sender and recipient
    $mail->setFrom('reflect.school@gmail.com', 'Reflect');
    $mail->addAddress('viki.lauvrys@gmail.com', 'viki');

    // Set email content
    $mail->isHTML(true);
    $mail->Subject = 'FOUND ONE';
    $mail->Body    = "User " . $param1 . " " . $param2;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    // Send the email
    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo 'Email could not be sent. Error: ', $mail->ErrorInfo;
}
?>