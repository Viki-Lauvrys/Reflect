<?php 

session_start();
include("database.php");

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

$id = $_POST['id'];

 
if(!empty($id)){ 
    // Delete data with given id
    $query = "DELETE FROM users WHERE id = '$id'"; 
    $result = $conn->query($query); 
}
?>