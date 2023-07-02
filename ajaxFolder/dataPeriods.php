<?php 

session_start();
include("database.php");

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");
    $schoolYear = $_SESSION['schoolYear'];
    $nextYear = $schoolYear + 1;

    // Fetch all periods
    $query = "SELECT * 
        FROM survey_period
        WHERE (userID = 'IH786SWX8C76IZU38='
        OR userID = '" . $_SESSION['userID'] . "') 
        AND (
                (
                    MONTH(start_date1) >= 9 AND MONTH(start_date1) <= 12 
                    AND YEAR(start_date1) = '" . $schoolYear . "'
                )
                OR 
                (
                    MONTH(start_date1) >= 1 AND MONTH(start_date1) <= 8 
                    AND YEAR(start_date1) = '" . $nextYear  . "'
                )
            )"; 
    $result = $conn->query($query); 
    
    echo "<option value='0'>Selecteer Periode</option>";

    // Generate HTML
    if($result->num_rows > 0){ 
        while($row = $result->fetch_assoc()){  
            echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>'; 
        } 
    }

    echo '<option name="addTitle" class="add" value="add">+ Toevoegen</option>'; 
    echo "<br/> <br/>";

?>
