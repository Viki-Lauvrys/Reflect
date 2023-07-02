<?php 

session_start();
include("database.php");

if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

    if(!empty($_POST["period_id"]) && ($_POST["period_id"] != "add")){ 
        
        // Fetch state data based on the specific title 
        $query = "SELECT * FROM survey_period WHERE id = ".$_POST['period_id']; 
        $result = $conn->query($query); 
        
        // Generate HTML of state options list 
        if($result->num_rows == 1){ 
            while($row = $result->fetch_assoc()){  
                echo "<label>";
                    echo "<br/> <b>" . $row['name'] . "</b>";
                    if($row['userID'] == $_SESSION['userID']) {
                        echo "<img src='img/delete_icon.png' alt='Delete' class='delete-icon' onclick='deletePeriod(" . $row['id'] . ")'>"; 
                    } 
                echo "</label>";
                echo "<br/>";
                echo "Zelfevaluatie 1 : <br/>" .  $row['start_date1'] . " tot " . $row['end_date1'] . "<br/><br/>";
                echo "Zelfevaluatie 2 : <br/>" .  $row['start_date2'] . " tot " . $row['end_date2'] . "<br/>";
            } 
        }
    } else {
        // Add period form
        ?>
        <input id='addPeriod' placeholder='Naam periode...'/> <br/> <br/>
            <b>Eerste evaluatie:</b> <br/>
                Startdatum: <input id="date" type="date"/>
                Einddatum: <input id="date1" type="date"/> <br/> <br/> <br/>
            <b>Tweede evaluate:</b> <br/>
                Startdatum: <input id="date2" type="date"/>
                Einddatum: <input id="date3" type="date"/> <br/> <br/>
        <input type='button' value='Opslaan' onClick='savePeriod()'/>
        <?php
    }
?>