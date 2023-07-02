<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");
	check_login($conn);
    $userID = $_SESSION['userID'];

    $new_q5 = addslashes($_POST["q5"]);
    $spid = $_POST["spid"];

    $conn->query("UPDATE survey_teacher SET q5 = '$new_q5' WHERE `spid` = '$spid' && teacher = '$userID'");
    
?>