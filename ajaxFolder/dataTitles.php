<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");


    // Fetch all titles
    $query = "SELECT *, survey_title.id AS stid 
        FROM survey_title
        WHERE (userID = 'IH786SWX8C76IZU38=' OR userID = ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $_SESSION['userID']);
    $stmt->execute();
    $titleResult = $stmt->get_result();

    echo '<option selected="selected" disabled="disabled" value="">Selecteer competentie</option>';

        if ($titleResult->num_rows > 0) { 
            while ($row = $titleResult->fetch_assoc()) {  
                echo '<option id="title" name="inputTitle" value="' .$row['id'] . '">' . utf8_decode($row['title']) . '</option>'; 
            } 
        }

    echo '<option name="addTitle" class="add" value="add">+ Toevoegen</option>'; 
    echo "<br/> <br/>";

?>