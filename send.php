<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/includes/Exception.php';
require 'phpmailer/includes/PHPMailer.php';
require 'phpmailer/includes/SMTP.php';

$mail = new PHPMailer(true);

$user = $_POST['username'];
echo $user;
$result = $conn->query("SELECT email FROM users WHERE username = '$user' LIMIT 1");
if ($result->num_rows == 1) {
    $row = mysqli_fetch_assoc($result);
    $email = $row['email'];

    $tokenEmail = md5(rand());
    $token = password_hash($tokenEmail, PASSWORD_DEFAULT);
    $update_token = $conn->query("UPDATE users SET verify_token = '$token', created_at = CURRENT_TIMESTAMP(), verify_status = '1' WHERE username = '$user' LIMIT 1");

    $link = "https://reflect.ict.campussintursula.be/resetPassword.php?token=$tokenEmail&username=$user";


    try {
        //Server settings
        $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'reflect.school@gmail.com';                     //SMTP username
        $mail->Password   = 'linpzvpclgqefcdj';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom('reflect.school@gmail.com', 'Reflect');
        $mail->addAddress($email, $user);     //Add a recipient


        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'Wachtwoord herstellen';
        $mail->Body    = "Beste, <br/> <br/> Ben je je wachtwoord vergeten? Geen probleem! Via onderstaande link kun je je wachtwoord makkelijk wijzigen. <br/> <br/> Met vriendelijke groeten, <br/> het Reflect-team <br/> <br/> <br/> <button><a href='$link'>wachtwoord wijzigen</a></button>";
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->send();
        echo 'Email verzonden, check uw inbox en spam-folder';

    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

<script type='text/javascript'>window.location.href='submitted.php?message=E-mail is succesvol verzonden. Check uw inbox.'</script>