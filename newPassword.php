<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

$user = $_POST['user'];
$password0 = $_POST['password0'];
$password1 = $_POST['password1'];

$password = password_hash($password0, PASSWORD_DEFAULT);

if ($password0 == $password1) {
    $conn->query("UPDATE users SET `password` = '$password', verify_status = '0' WHERE username = '$user'");
    ?> 
        <script type='text/javascript'>window.location.href='submitted.php?message=Wachtwoord gewijzigd.'</script>
    <?php
} else {
    echo 'Opgegeven wachtwoorden komen niet overeen.';
}

?>