<?php 

if ($_SESSION['logged_in']) {
    // smartschool user
    $userID = $_SESSION['userID'];

    foreach ($_SESSION as $key => $value) {
        if (strpos($key, 'groupName_klassenraad ') === 0) {
            $groupName = str_replace('groupName_klassenraad ', '', $key);

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
                    $query = "SELECT sp.id AS spid
                        FROM survey_info_info
                        LEFT JOIN survey_pupil sp ON sp.siid = survey_info_info.id
                        WHERE survey_info_info.sid = ?";
                    $stmt = $conn -> prepare($query);
                    $stmt -> bind_param('s', $row['surveyID']);
                    $stmt -> execute();
                    $surveyInfoInfoResult = $stmt -> get_result();

                    if ($surveyInfoInfoResult && mysqli_num_rows($surveyInfoInfoResult) > 0) {
                        while ($row = $surveyInfoInfoResult->fetch_assoc()) {

                            // Check if survey_teacher exists for user
                            $query = "SELECT *
                                FROM survey_teacher
                                WHERE teacher = ?
                                    AND spid = ?";
                            $stmt = $conn -> prepare($query);
                            $stmt -> bind_param('ss', $userID, $row['spid']);
                            $stmt -> execute();
                            $surveyPupilResult = $stmt -> get_result();

                            if ($surveyPupilResult && mysqli_num_rows($surveyPupilResult) == 0) {

                                // survey_teacher doesn't exist yet => make new one
                                $query = "INSERT INTO survey_teacher (`spid`, teacher, q5) 
                                    VALUES (?, ?, ?)";
                                $q5 = '0'; // Has to be in var
                                $stmt = $conn -> prepare($query);
                                $stmt -> bind_param('sss', $row['spid'], $userID, $q5);
                                $stmt -> execute();
                            }
                        }
                    }
                }
            }

        } else if (strpos($key, 'groupName_') === 0) {
            $groupName = str_replace('groupName_', '', $key);

            // Select all surveys for user
            $query = "SELECT surveys.id AS surveyID
                FROM surveys
                LEFT JOIN survey_group ON survey_group.sid = surveys.id
                WHERE survey_group.group_name = ? ";
            $stmt = $conn->prepare($query);
            $stmt -> bind_param('s', $groupName);
            $stmt->execute();
            $surveyResult = $stmt->get_result();

            if ($surveyResult && mysqli_num_rows($surveyResult) > 0) {
                while ($row = $surveyResult->fetch_assoc()) {

                    // Get all survey_info_info from survey
                    $query = "SELECT sp.id AS spid
                        FROM survey_info_info
                        LEFT JOIN survey_pupil sp ON sp.siid = survey_info_info.id
                        WHERE survey_info_info.sid = ?";
                    $stmt = $conn -> prepare($query);
                    $stmt -> bind_param('s', $row['surveyID']);
                    try {
                        $stmt->execute();
                        $surveyInfoInfoResult = $stmt->get_result();

                        // Process the result here

                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage(); // Display the error message
                    }

                    if ($surveyInfoInfoResult && mysqli_num_rows($surveyInfoInfoResult) > 0) {
                        while ($row = $surveyInfoInfoResult->fetch_assoc()) {

                            // Check if survey_teacher exists for user
                            $query = "SELECT *
                                FROM survey_teacher
                                WHERE teacher = ?
                                    AND spid = ?";
                            $stmt = $conn -> prepare($query);
                            $stmt -> bind_param('ss', $userID, $row['spid']);
                            $stmt -> execute();
                            $surveyPupilResult = $stmt -> get_result();

                            if ($surveyPupilResult && mysqli_num_rows($surveyPupilResult) == 0) {

                                // survey_teacher doesn't exist yet => make new one
                                $query = "INSERT INTO survey_teacher (`spid`, teacher, q5) 
                                    VALUES (?, ?, ?)";
                                $q5 = '0'; // Has to be in var
                                $stmt = $conn -> prepare($query);
                                $stmt -> bind_param('sss', $row['spid'], $userID, $q5);
                                $stmt -> execute();
                            }
                        }
                    }
                }
            }

        } 
    }
} else {
    // non-smartschool user
    $userID = $_SESSION['username'];

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
            $stmt->execute();
            $surveyResult = $stmt->get_result();

            if ($surveyResult && mysqli_num_rows($surveyResult) > 0) {
                while ($row = $surveyResult->fetch_assoc()) {

                    // Get all survey_info_info from survey
                    $query = "SELECT sp.id AS spid
                        FROM survey_info_info
                        LEFT JOIN survey_pupil sp ON sp.siid = survey_info_info.id
                        WHERE survey_info_info.sid = ?";
                    $stmt = $conn -> prepare($query);
                    $stmt -> bind_param('s', $row['surveyID']);
                    try {
                        $stmt->execute();
                        $surveyInfoInfoResult = $stmt->get_result();

                        // Process the result here

                    } catch (Exception $e) {
                        echo "Error: " . $e->getMessage(); // Display the error message
                    }

                    if ($surveyInfoInfoResult && mysqli_num_rows($surveyInfoInfoResult) > 0) {
                        while ($row = $surveyInfoInfoResult->fetch_assoc()) {

                            // Check if survey_teacher exists for user
                            $query = "SELECT *
                                FROM survey_teacher
                                WHERE teacher = ?
                                    AND spid = ?";
                            $stmt = $conn -> prepare($query);
                            $stmt -> bind_param('ss', $userID, $row['spid']);
                            $stmt -> execute();
                            $surveyPupilResult = $stmt -> get_result();

                            if ($surveyPupilResult && mysqli_num_rows($surveyPupilResult) == 0) {

                                // survey_teacher doesn't exist yet => make new one
                                $query = "INSERT INTO survey_teacher (`spid`, teacher, q5) 
                                    VALUES (?, ?, ?)";
                                $q5 = '0'; // Has to be in var
                                $stmt = $conn -> prepare($query);
                                $stmt -> bind_param('sss', $row['spid'], $userID, $q5);
                                $stmt -> execute();
                            }
                        }
                    }
                }
            }

        } 
    }
}
?>
