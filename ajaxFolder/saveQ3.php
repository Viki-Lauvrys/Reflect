<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}

include("functions.php");
check_login($conn);

$q3 = $_POST["q3"];
$siid = $_POST["siid"];
$userID = $_SESSION['userID'];

$query = "UPDATE survey_pupil 
    SET q3 = ?
    WHERE `siid` = ?
    AND pupil = ?";
$stmt = $conn->prepare($query);
$stmt -> bind_param('sss', $q3, $siid, $userID);
$stmt -> execute();

?>