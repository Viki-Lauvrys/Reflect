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
        FROM survey_pupil sp
        LEFT JOIN survey_info_info sii ON sii.id = sp.siid
        LEFT JOIN survey_info si ON si.id = sii.info_id
        WHERE si.userID = ? AND si.id = ? ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ss', $_SESSION['userID'], $_POST['infoID']);
    $stmt->execute();
    $infoResult = $stmt->get_result();

        if ($infoResult->num_rows > 0) { 
            // Can't delete
            echo "alert";
        } else {
            $query = "DELETE
                FROM survey_info
                WHERE userID = ? AND id = ? ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ss', $_SESSION['userID'], $_POST['infoID']);
            $stmt->execute();
        }

?>