<?php 

session_start();
include("database.php");

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");
 
if(!empty($_POST["titleName"])){ 
    $title = $_POST["titleName"];
    $query = "INSERT INTO survey_title (title, userID) VALUES (?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ss', $_POST['titleName'], $_SESSION['userID']);
    $stmt->execute();
}