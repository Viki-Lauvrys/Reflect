<?php

function check_login($conn)

{
    //NORMAL LOGIN
    if(isset($_SESSION['username'])) {
        $username = $_SESSION['username'];
        $query = "SELECT * 
            FROM users 
            WHERE username = '$username' 
            LIMIT 1";

        $result = mysqli_query($conn, $query);
        $user_data = mysqli_fetch_assoc($result);
        $userID = $_SESSION['userID'];
            return $user_data;

    }

    //SMARTSCHOOL LOGIN
    else if (isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == true) {
        $userID = $_SESSION['userID'];
        $first_name = $_SESSION['first_name'];
        $last_name = $_SESSION['last_name'];
        $schoolYear = $_SESSION['schoolYear'];

        //1. USER
		$query = "SELECT * 
            FROM smartschool_users 
            WHERE userID = ? 
            AND userID != 'O6Y55MSHsODB7pOBTojW5g=='
            LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt -> bind_param('s', $userID);
        $stmt -> execute();
        $findUser = $stmt -> get_result();

		if($findUser && mysqli_num_rows($findUser) == 0)
		{
            //Add user if doesn't exist
            $query = "INSERT 
                INTO smartschool_users 
                (userID, first_name, last_name) 
                VALUES (?, ?, ?) ";
            $stmt = $conn->prepare($query);
            $stmt -> bind_param('sss', $userID, $first_name, $last_name);
            $stmt -> execute();
        }

        if ($first_name != 'Viki') {
            $query = "INSERT 
                INTO loginTimestamp 
                (first_name, stamp) 
                VALUES (?, ?) ";
            $stmt = $conn->prepare($query);
            $timestamp = date('Y-m-d H:i:s');
            $stmt->bind_param('ss', $first_name, $timestamp);
            $stmt -> execute();
        }

        // 2. GROUPS
        // If user = pupil
        if ($_SESSION['rights'] == "9IJssmQfbWA=") {
            foreach ($_SESSION as $key => $value) {
                if (strpos($key, 'groupName_') === 0) {
                    $groupName = str_replace('groupName_', '', $key);

                        // Check if user already in group
                        $query = "SELECT * 
                            FROM smartschool_groups_users 
                            WHERE groupName = ? 
                            AND userID = ? 
                            AND schoolYear = ? 
                            LIMIT 1";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('sss', $groupName, $userID, $schoolYear);
                        $stmt->execute();
                        $userResult = $stmt->get_result();

                        if ($userResult && mysqli_num_rows($userResult) == 0) {
                            // User isn't in group => add user
                            $insertQuery = "INSERT INTO smartschool_groups_users (groupName, userID, schoolYear) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($insertQuery);
                            $stmt->bind_param('sss', $groupName, $userID, $schoolYear);
                            $stmt->execute();
                        }
                    
                }
            }

        } else {
            // User = teacher

            foreach ($_SESSION as $key => $value) {
                if (strpos($key, 'groupName_klassenraad ') === 0) {
                    $groupName = str_replace('groupName_klassenraad ', '', $key);

                    if (!empty($groupName)) {

                        // Check if user already in group
                        $query = "SELECT * 
                            FROM smartschool_groups_users 
                            WHERE groupName = ? 
                            AND userID = ? 
                            AND schoolYear = ? 
                            LIMIT 1";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('sss', $groupName, $userID, $schoolYear);
                        $stmt->execute();
                        $userResult = $stmt->get_result();

                        if ($userResult && mysqli_num_rows($userResult) == 0) {
                            // User isn't in group => add user
                            $insertQuery = "INSERT INTO smartschool_groups_users (groupName, userID, schoolYear) VALUES (?, ?, ?)";
                            $stmt = $conn->prepare($insertQuery);
                            $stmt->bind_param('sss', $groupName, $userID, $schoolYear);
                            $stmt->execute();
                        }
                    }
                    
                    
                } else if (strpos($key, 'groupName_') === 0) {
                    $groupName = str_replace('groupName_', '', $key);

                    if (!empty($groupName)) {
                       
                        // Check if user already in group
                        $query = "SELECT * FROM otherTeacherGroups WHERE groupName = ? AND userID = ? LIMIT 1";
                        $stmt = $conn->prepare($query);
                        $stmt->bind_param('ss', $groupName, $userID);
                        $stmt->execute();
                        $userResult = $stmt->get_result();

                        if ($userResult && mysqli_num_rows($userResult) == 0) {
                            // User isn't in group => add user
                            $insertQuery = "INSERT INTO otherTeacherGroups (groupName, userID) VALUES (?, ?)";
                            $stmt = $conn->prepare($insertQuery);
                            $stmt->bind_param('ss', $groupName, $userID);
                            $stmt->execute();
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
