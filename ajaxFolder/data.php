<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");
    header('Content-Type: text/html; charset=utf-8');

 
if(!empty($_POST["title_id"]) && ($_POST["title_id"] != "add")){ 
    // Fetch state data based on the specific title 
    $query = "SELECT * FROM survey_info 
        WHERE title_id = " . $_POST['title_id'] . " AND (userID = 'IH786SWX8C76IZU38=' OR userID = '" . $_SESSION['userID'] . "')"; 
    $result = $conn->query($query); 
     
    // Generate HTML of state options list 
    if($result->num_rows > 0){ 
        echo '<b>Focusdoel(en)</b> <br/>'; 
        while($row = $result->fetch_assoc()){  
            echo "<label>";
                echo "<input class='info' type='checkbox' name='info' value='" . $row['id'] . "'>";
                echo "<span class='checkbox-text'>" . utf8_decode($row['info']) . "</span>";
                if($row['userID'] == $_SESSION['userID']) {
                    echo "<img src='img/delete_icon.png' alt='Delete' class='delete-icon' onclick='deleteInfo(" . $row['id'] . ")'>"; 
                }
            echo "</label>";
        } 
    }else{ 
        echo '<div class="extraPadding">Geen focusdoelen gevonden</div><br/>'; 

        // if user made title => can delete
        $query = "SELECT * 
            FROM survey_title 
            WHERE id = ? 
            AND userID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ss', $_POST['title_id'], $_SESSION['userID']);
        $stmt->execute();
        $titleResult = $stmt->get_result();

        // dees ziet er beetje spastisch uit, en aesthetics is belangrijker dan functionaliteit dus ja 
        //if ($titleResult->num_rows > 0) { 
        //    echo "<div class='deleteTitle' onclick='deleteTitle(" . $_POST['title_id'] . ")' style='margin-left: -7px'>";
        //    echo "<img src='img/delete_icon.png' alt='Delete' class='delete-icon noHide'>Competentie verwijderen?"; 
        //    echo "</div>";
        //}
    } 
    echo '<button id="addInfoBtn" type="button" class="info add extraPadding" value="' . $_POST["title_id"] . '" style="
        background: none;
        margin: 0;
        text-align: left;
        border: none;
        cursor: pointer;
        " onclick="openAddInfo()">+ Toevoegen</button> <br/>'; 
    echo "<div id='addInfoContainer'>";
        echo "<input id='addInfo' class='INPUT' placeholder='Naam focusdoel...'/>";
        echo "<input type='button' onClick='saveInfo()' value='Voeg toe'/>";
    echo "</div>";

} else {
    // Add title form
    echo "<br/><br/><input id='addTitle' class='INPUT' placeholder='Naam competentie...'/>";
    echo "<input type='button' value='Voeg toe' onClick='saveTitle()'/>";
}
?>