<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

$user = $_GET['username'];
$token = $_GET['token'];

$result = $conn->query("SELECT * FROM users WHERE username = '$user'");
$row = mysqli_fetch_assoc($result);
$getToken = $row['verify_token'];
$status = $row['verify_status'];

$timePassed = strtotime($row['created_at']) - strtotime('-15 minutes');

if(password_verify($token, $getToken) && $status == 1) {
    ?>

    <html>
    <head>
        <title>Reset wachtwoord</title>
        <link rel="stylesheet" href="resetPassword.css">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
    </head>
    
    <body>
            <form method="post" action="newPassword.php">
                <input type="hidden" name="user" value="<?php echo $user ?>"/>
                nieuw wachtwoord <br/> <input type="password" name="password0" value=""> <br/> <br/>
                herhaal nieuw wachtwoord <br/> <input type="password" name="password1" value=""> <br/> <br/> <br/>
                <button type="submit" name="send">Verzend</button>
            </form>
    </body>
    </html>

<?php
    
} else {
    echo 'error, link is vervallen';
}

?>