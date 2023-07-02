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

    $new_feedback = addslashes($_POST["feedback"]);
    $spid = $_POST["spid"];

    $conn->query("UPDATE survey_pupil SET feedback = '$new_feedback' WHERE `id` = '$spid'");


?>