<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");



 
if(!empty($_POST["titleID"]) && !empty($_POST["infoName"])) { 

    $query = "INSERT INTO survey_info (title_id, info, userID) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', addslashes($_POST['titleID']), addslashes($_POST['infoName']), $_SESSION['userID']);
    $stmt->execute();
}