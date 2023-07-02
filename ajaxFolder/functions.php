<?php

function check_login($con)
{
    //NORMAL LOGIN
	if($_SESSION['userID'] == 'IH786SWX8C76IZU38=')
	{
		return;

	}

    //SMARTSCHOOL LOGIN
    else if (isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == true) {
        $userID = $_SESSION['userID'];
        $first_name = $_SESSION['first_name'];
        $last_name = $_SESSION['last_name'];

        //1. USER
		$query = "SELECT * 
		FROM smartschool_users 
		WHERE userID = '$userID' limit 1";

		$result = mysqli_query($con,$query);
		if($result && mysqli_num_rows($result) == 0)
		{
            //Add user if doesn't exist
            $con->query("INSERT INTO smartschool_users (userID, first_name, last_name) VALUES ('$userID', '$first_name', '$last_name') ");
        }

        // 2. GROUPS
        // If user = pupil
        if ($_SESSION['rights'] == "9IJssmQfbWA=") {
            foreach ($_SESSION as $key => $value) {
                if (strpos($key, 'groupID_') === 0) {
                    $groupID = str_replace('groupID_', '', $key);
                } else if (strpos($key, 'groupName_') === 0) {
                    $groupName = str_replace('groupName_', '', $key);

                    if (!empty($groupID) && !empty($groupName)) {
                        // Check if group already exists
                        $query = "SELECT * FROM smartschool_groups_users WHERE groupName = ? LIMIT 1";
                        $stmt = $con->prepare($query);
                        $stmt->bind_param('s', $groupName);
                        $stmt->execute();
                        $groupResult = $stmt->get_result();

                        if ($groupResult && mysqli_num_rows($groupResult) == 0) {
                            // Group doesn't exist => make new group
                            $insertQuery = "INSERT INTO smartschool_groups_users (groupName, userID) VALUES (?, ?)";
                            $stmt = $con->prepare($insertQuery);
                            $stmt->bind_param('ss', $groupName, $userID);
                            $stmt->execute();

                        } else {
                            // Check if user already in group
                            $query = "SELECT * FROM smartschool_groups_users WHERE groupName = ? AND userID = ? LIMIT 1";
                            $stmt = $con->prepare($query);
                            $stmt->bind_param('ss', $groupName, $userID);
                            $stmt->execute();
                            $userResult = $stmt->get_result();

                            if ($userResult && mysqli_num_rows($userResult) == 0) {
                                // User isn't in group => add user
                                $insertQuery = "INSERT INTO smartschool_groups_users (groupName, userID) VALUES (?, ?)";
                                $stmt = $con->prepare($insertQuery);
                                $stmt->bind_param('ss', $groupName, $userID);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }
        } else {
            // User = teacher

            foreach ($_SESSION as $key => $value) {
                if (strpos($key, 'groupname_klassenraad ') === 0) {
                    $groupName = str_replace('groupname_klassenraad ', '', $key);

                    if (!empty($groupName)) {
                        // Check if group already exists
                        $query = "SELECT * FROM smartschool_groups_users WHERE groupName = ? LIMIT 1";
                        $stmt = $con->prepare($query);
                        $stmt->bind_param('s', $groupName);
                        $stmt->execute();
                        $groupResult = $stmt->get_result();

                        if ($groupResult && mysqli_num_rows($groupResult) == 0) {
                            // Group doesn't exist => make new group
                            $insertQuery = "INSERT INTO smartschool_groups_users (groupName, userID) VALUES (?, ?)";
                            $stmt = $con->prepare($insertQuery);
                            $stmt->bind_param('ss', $groupName, $userID);
                            $stmt->execute();
                            
                        } else {
                            // Check if user already in group
                            $query = "SELECT * FROM smartschool_groups_users WHERE groupName = ? AND userID = ? LIMIT 1";
                            $stmt = $con->prepare($query);
                            $stmt->bind_param('ss', $groupName, $userID);
                            $stmt->execute();
                            $userResult = $stmt->get_result();

                            if ($userResult && mysqli_num_rows($userResult) == 0) {
                                // User isn't in group => add user
                                $insertQuery = "INSERT INTO smartschool_groups_users (groupName, userID) VALUES (?, ?)";
                                $stmt = $con->prepare($insertQuery);
                                $stmt->bind_param('ss', $groupName, $userID);
                                $stmt->execute();
                            }
                        }
                    }
                }
            }

        }
        return;
    }

	//NOT LOGGED IN
	header("Location: login.php");
	die;
}
?>
