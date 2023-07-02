<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");


    // Delete title
    $query = "DELETE
        FROM survey_title
        WHERE id = ? ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $_POST['titleID']);
    $stmt->execute();

?>