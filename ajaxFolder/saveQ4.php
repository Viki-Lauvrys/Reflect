<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}

include("functions.php");
check_login($conn);

$q4 = $_POST["q4"];
$siid = $_POST["siid"];
$userID = $_SESSION['userID'];

$query = "UPDATE survey_pupil 
    SET q4 = ?
    WHERE `siid` = ?
    AND pupil = ?";
$stmt = $conn->prepare($query);
$stmt -> bind_param('sss', $q4, $siid, $userID);
$stmt -> execute();

?>