<?php 

session_start();
include("database.php");

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

//Get input
$id = $_POST['id'];
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];

 
if(!empty($id) && !empty($firstname) && !empty($lastname) && !empty($email)){ 

    //New random username
    $username = $firstname . mb_substr($lastname, 0, 1) . rand(1, 9);

    //Edit data with given id
    $query = "UPDATE users SET username = '$username', first_name = '$firstname', last_name = '$lastname', email = '$email' WHERE id='$id'";
    $result = $conn->query($query); 
}

?>