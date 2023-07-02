<?php

// Get all unanswered surveys
$query = "SELECT *, surveys.id AS `sid`, survey_title.id AS tid, survey_pupil.id AS spid
    FROM survey_pupil
    LEFT JOIN survey_info_info ON survey_info_info.id = survey_pupil.siid
    LEFT JOIN survey_info ON survey_info_info.info_id = survey_info.id
    LEFT JOIN survey_title ON survey_info.title_id = survey_title.id
    LEFT JOIN surveys ON surveys.id = survey_info_info.sid
    LEFT JOIN survey_period ON surveys.period_id = survey_period.id
    WHERE survey_pupil.pupil = ?
    AND (status = '0' AND start_date1 IS NOT NULL AND end_date1 IS NOT NULL AND start_date1 <= CURDATE() AND end_date1 >= CURDATE()) 
        OR 
        (status = '1' AND start_date2 IS NOT NULL AND end_date2 IS NOT NULL AND start_date2 <= CURDATE() AND end_date2 >= CURDATE())
    GROUP BY surveys.id, title";
$stmt = $conn -> prepare($query);
$stmt -> bind_param('s', $userID);
$stmt -> execute();
$allSurveys = $stmt -> get_result();
$a = 0;
function displaySurvey($passId, $passTitle, $title, $start_date, $end_date) {
    $days_left = (new DateTime($end_date))->diff(new DateTime())->days +1; 

    echo "<a href=survey.php?passId=" . $passId . "&passTitle=" . $passTitle . ">";
    echo "<div class='pupilBox'>";
    echo "<H2>" . utf8_decode($title) ."</H2> <br/>";
    echo "<H3>Tegen: " . $end_date . " ( " . $days_left . " dagen resterend) </H3><br/> <br/> <br/> <br/>";
    echo "</div>";
    echo "</a>";
    global $a;
    $a = 1;
}

//Gets unanswered surveys questions
if ($allSurveys->num_rows > 0) {
    while($row = $allSurveys->fetch_assoc()) {
        $passId = $row['sid'];
        $passTitle = $row['tid'];

        date_default_timezone_set('Europe/Brussels');
        $today = date("Y-m-d");
        $date1 = $row['start_date1'];
        $date2 = $row['end_date1'];
        $date3 = $row['start_date2'];
        $date4 = $row['end_date2'];

        //set right dates (first or second evaluation)
        if ($row['status'] == 0 && ($date1 <= $today) && ($date2 >= $today)) {
            $start_date = date('d-m-Y', strtotime($row["start_date1"]));
            $end_date = date('d-m-Y', strtotime($row["end_date1"]));
            displaySurvey($passId, $passTitle, $row["title"], $start_date, $end_date);

        } else if ($row['status'] == 1 && ($date3 <= $today) && ($date4 >= $today)) {
            $start_date = date('d-m-Y', strtotime($row["start_date2"]));
            $end_date = date('d-m-Y', strtotime($row["end_date2"]));
            displaySurvey($passId, $passTitle, $row["title"], $start_date, $end_date);
        }
    }  
    
    if ($a == 0) {
        echo "<div style='text-align:center;'><b>Er zijn nog geen enquêtes, kom later terug.</b></div>";
    }
} else {
    echo "<div style='text-align:center;'><b>Er zijn nog geen enquêtes, kom later terug.</b></div>";
}

?>