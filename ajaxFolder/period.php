<?php 

session_start();
include("database.php");

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

if(!empty($_POST["period_id"])){ 
    // Fetch state data based on the specific period 
    $query = "SELECT * FROM survey_period WHERE id = ".$_POST['period_id']; 
    $result = $conn->query($query); 
     
    // Generate HTML of state options list 
    if($result->num_rows == 1){ 
        while($row = $result->fetch_assoc()){  
            echo "<br/> <b>" . $row['name'] . "</b> <br/>";
            echo "Zelfevaluatie 1 : " .  $row['start_date1'] . " tot " . $row['end_date1'] . "<br/>";
            echo "Zelfevaluatie 2 : " .  $row['start_date2'] . " tot " . $row['end_date2'] . "<br/>";
        } 
    }
}
?>