<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");

    $userID = $_POST['userID'];
    $year = $_POST['year'];

    $uniqueTitles = array();
    $surveyData = array();

    $query = "SELECT survey_title.title AS title, 
        q1, q4, q5
        FROM survey_pupil sp
        LEFT JOIN survey_teacher st ON st.spid = sp.id
        LEFT JOIN survey_info_info sii ON sii.id = sp.siid 
        LEFT JOIN survey_info si ON si.id = sii.info_id
        LEFT JOIN survey_title ON survey_title.id = si.title_id
        LEFT JOIN surveys ON surveys.id = sii.sid
        LEFT JOIN survey_period ON surveys.period_id = survey_period.id
        WHERE sp.pupil = ?
        AND sp.status != ?
        AND survey_period.schoolYear = ?
        ORDER BY title ASC";

    $status = 0;
    $stmt = $conn->prepare($query);
    $stmt -> bind_param('sss', $userID, $status, $year);
    $stmt -> execute();
    $surveyResult = $stmt -> get_result();

    if ($surveyResult && mysqli_num_rows($surveyResult) > 0) {
        while ($row = $surveyResult->fetch_assoc()) {
            $title = $row['title'];
            $q1 = $row['q1'];
            $q4 = $row['q4'];
            $q5 = $row['q5'];
            $surveyData[$title][] = array('q1' => $q1, 'q4' => $q4, 'q5' => $q5);
        }
    }

    $averages = array();

    foreach ($surveyData as $title => $data) {
        $totalQ1 = 0;
        $totalQ4 = 0;
        $totalQ5 = 0;
        $count = 0;
        foreach ($data as $row) {
            $totalQ1 += $row['q1'];
            $totalQ4 += $row['q4'];
            $totalQ5 += $row['q5'];
            $count++;
        }
        $averageQ1 = $totalQ1 / $count;
        $averageQ4 = $totalQ4 / $count;
        $averageQ5 = $totalQ5 / $count;
        $averages[$title] = array(
            'q1' => $averageQ1,
            'q4' => $averageQ4,
            'q5' => $averageQ5
        );
    }


    // Get all titles
    $getTitles = array_map(function($key) {
        return "'{$key}'";
    }, array_keys($averages));

    $titles = '[' . implode(', ', $getTitles) . ']';


    // Get all q1 in right format
    $q1Array = array();
    foreach ($averages as $array) {
        $q1 = $array['q1'];
        $q1 = number_format($q1, 2);
        $q1Array[] = $q1;
    }
    $q1String = "[" . implode(", ", $q1Array) . "]";

    // Get all q4 in right format
    $q4Array = array();
    foreach ($averages as $array) {
        $q4 = $array['q4'];
        $q4 = number_format($q4, 2);
        $q4Array[] = $q4;
    }
    $q4String = "[" . implode(", ", $q4Array) . "]";

    // Get all q5 in right format
    $q5Array = array();
    foreach ($averages as $array) {
        $q5 = $array['q5'];
        $q5 = number_format($q5, 2);
        $q5Array[] = $q5;
    }
    $q5String = "[" . implode(", ", $q5Array) . "]";

    $response = array(
        'q1' => $q1String,
        'q4' => $q4String,
        'q5' => $q5String
      );
      
      echo json_encode($response);


?>