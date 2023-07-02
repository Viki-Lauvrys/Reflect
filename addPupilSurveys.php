<?php 

if ($_SESSION['logged_in']) {
    // smartschool user
    $userID = $_SESSION['userID'];

    foreach ($_SESSION as $key => $value) {
        if (strpos($key, 'groupName_') === 0) {
            $groupName = str_replace('groupName_', '', $key);

            // Select all surveys for user
            $query = "SELECT surveys.id AS surveyID
                FROM surveys
                LEFT JOIN survey_group ON survey_group.sid = surveys.id
                WHERE survey_group.group_name = ? ";
            $stmt = $conn->prepare($query);
            $stmt -> bind_param('s', $groupName);
            $stmt -> execute();
            $surveyResult = $stmt -> get_result();

            if ($surveyResult && mysqli_num_rows($surveyResult) > 0) {
                while ($row = $surveyResult->fetch_assoc()) {

                    // Get all survey_info_info from survey
                    $query = "SELECT id AS siid
                        FROM survey_info_info
                        WHERE `sid` = ?";
                    $stmt = $conn -> prepare($query);
                    $stmt -> bind_param('s', $row['surveyID']);
                    $stmt -> execute();
                    $surveyInfoInfoResult = $stmt -> get_result();

                    if ($surveyInfoInfoResult && mysqli_num_rows($surveyInfoInfoResult) > 0) {
                        while ($row = $surveyInfoInfoResult->fetch_assoc()) {

                            // Check if survey_pupil exists for user + survey_info_info !!
                            $query = "SELECT *
                                FROM survey_pupil
                                WHERE pupil = ?
                                    AND siid = ?";
                            $stmt = $conn -> prepare($query);
                            $stmt -> bind_param('ss', $userID, $row['siid']);
                            $stmt -> execute();
                            $surveyPupilResult = $stmt -> get_result();

                            if ($surveyPupilResult && mysqli_num_rows($surveyPupilResult) == 0) {

                                // survey_pupil doesn't exist yet => make new one
                                $query = "INSERT INTO survey_pupil (`siid`, pupil) 
                                        VALUES (?, ?)";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param('ss', $row['siid'], $userID);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }
        }
    }

} else {
    // non-smartschool user
    $userID = $_SESSION['userID'];

    // Select all groups of user
    $query = "SELECT *
        FROM smartschool_groups_users
        WHERE userID = ? ";
    $stmt = $conn->prepare($query);
    $stmt -> bind_param('s', $userID);
    $stmt -> execute();
    $result = $stmt -> get_result();

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row1 = $result->fetch_assoc()) {

            $groupName = $row1['groupName'];

            // Select all surveys for user
            $query = "SELECT surveys.id AS surveyID
                FROM surveys
                LEFT JOIN survey_group ON survey_group.sid = surveys.id
                WHERE survey_group.group_name = ? ";
            $stmt = $conn->prepare($query);
            $stmt -> bind_param('s', $groupName);
            $stmt -> execute();
            $surveyResult = $stmt -> get_result();

            if ($surveyResult && mysqli_num_rows($surveyResult) > 0) {
                while ($row = $surveyResult->fetch_assoc()) {

                    // Get all survey_info_info from survey
                    $query = "SELECT id AS siid
                        FROM survey_info_info
                        WHERE `sid` = ?";
                    $stmt = $conn -> prepare($query);
                    $stmt -> bind_param('s', $row['surveyID']);
                    $stmt -> execute();
                    $surveyInfoInfoResult = $stmt -> get_result();

                    if ($surveyInfoInfoResult && mysqli_num_rows($surveyInfoInfoResult) > 0) {
                        while ($row = $surveyInfoInfoResult->fetch_assoc()) {

                            // Check if survey_pupil exists for user + survey_info_info !!
                            $query = "SELECT *
                                                        FROM survey_pupil
                                                        WHERE pupil = ?
                                                            AND siid = ?";
                            $stmt = $conn -> prepare($query);
                            $stmt -> bind_param('ss', $userID, $row['siid']);
                            $stmt -> execute();
                            $surveyPupilResult = $stmt -> get_result();

                            if ($surveyPupilResult && mysqli_num_rows($surveyPupilResult) == 0) {

                                // survey_pupil doesn't exist yet => make new one
                                $query = "INSERT INTO survey_pupil (`siid`, pupil) 
                                                                VALUES (?, ?)";
                                $stmt = $conn->prepare($query);
                                $stmt->bind_param('ss', $row['siid'], $userID);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }
        }
    }

}

?>

