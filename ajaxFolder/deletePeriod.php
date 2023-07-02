<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");


    // Fetch all info
    $query = "SELECT *
        FROM surveys
        LEFT JOIN survey_period sp ON sp.id = surveys.period_id
        WHERE sp.userID = ?
        AND sp.id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $_SESSION['userID'], $_POST['periodID']);
    $stmt->execute();
    $infoResult = $stmt->get_result();

        if ($infoResult->num_rows > 0) { 
            // Can't delete
            echo "alert";
        } else {
            $query = "DELETE
                FROM survey_period
                WHERE userID = ? AND id = ? ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ss', $_SESSION['userID'], $_POST['periodID']);
            $stmt->execute();
        }

?>