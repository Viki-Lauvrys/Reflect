<?php 

session_start();
include("database.php");

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

if(!empty($_POST["title_id"])){ 
    // Fetch state data based on the specific title 
    $query = "SELECT title FROM survey_title WHERE id = ".$_POST['title_id']; 
    $result = $conn->query($query); 
     
    // Generate HTML of state options list 
    if($result->num_rows == 1){ 
        while($row = $result->fetch_assoc()){  
            echo "<div><b>" . $row['title'] . "</b></div>"; 
        } 
    }
}
?>