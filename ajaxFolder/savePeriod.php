<?php 

session_start();
include("database.php");

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");



 
if(!empty($_POST["periodName"]) && !empty($_POST["date"]) && !empty($_POST["date1"]) && !empty($_POST["date2"]) && !empty($_POST["date3"])) { 
    $query = "INSERT INTO survey_period (`name`, start_date1, end_date1, start_date2, end_date2, userID, schoolYear) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param('sssssss', $_POST["periodName"], $_POST["date"], $_POST["date1"], $_POST["date2"], $_POST["date3"], $_SESSION['userID'], $_SESSION['schoolYear']);
    $stmt->execute();
}