<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

if(!empty($_POST["info_id"])){ 
    // Fetch state data based on the specific info
    $query = "SELECT info FROM survey_info WHERE id=" . $_POST['info_id']; 
    $result = $conn->query($query); 

    // Generate HTML of state options list 
    if($result->num_rows > 0){ 
        while($row = $result->fetch_assoc()){  
            echo utf8_decode($row['info']) . "<input type='hidden' name='infoIDs[]' value='" . htmlspecialchars($_POST['info_id']) . "'/>"; 
        } 
    }
}
?>