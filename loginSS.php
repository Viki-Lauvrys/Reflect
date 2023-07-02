<?php 

    session_start();
    include("database.php");

    if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
    {
        die("failed to connect!");
    }


    //SMARTSCHOOL
    $client_id = '01234abc';
    $client_secret = '56789def';
    $redirect_uri = 'https://reflect.ict.campussintursula.be/loginSS.php';

    // stap 1: redirect naar externe login
    if (isset($_GET['aanmelden'])) {
        $authorization_url = 'https://leerstof.be/campussintursula/oauth.php' . '?' .
            http_build_query(array(
                'client_id' => $client_id,
                'redirect_uri' => $redirect_uri,
                'scope' => 'userinfo groupinfo',
                'response_type' => 'code'
            ));
        header('Location: ' . $authorization_url);
        exit;
    }

?>

<!DOCTYPE html>
<html lang='nl'>

    <body>
            <pre>
                <?php
                // stap 2: verkrijg de initiële code
                if (isset($_GET['code'])) {

                    echo('<hr>');
                    print_r($_GET);
                    $code = $_GET['code'];

                    // stap 3: waarmee je het access_token kunt aanvragen
                    $token_url = 'https://leerstof.be/campussintursula/token.php';
                    $data = array(
                        'grant_type' => 'authorization_code',
                        'client_id' => $client_id,
                        'client_secret' => $client_secret,
                        'redirect_uri' => $redirect_uri,
                        'code' => $code
                    );
                    $options = array(
                        'http' => array(
                            'header' => 'Content-type: application/x-www-form-urlencoded',
                            'method' => 'POST',
                            'content' => http_build_query($data)
                        )
                    );
                    $context = stream_context_create($options);
                    $response = file_get_contents($token_url, false, $context);
                    $token = json_decode($response, true);
                    echo('<hr>');
                    print_r($token);
                    $access_token = $token['access_token'];

                    // stap 4: en vervolgens ook de eigenlijke api's kunt gebruiken
                    $_SESSION['logged_in'] = true;
                    
                    $api_userinfo_url = 'https://leerstof.be/campussintursula/userinfo.php' . '?' .
                        'access_token=' . urlencode($access_token);
                    $response = file_get_contents($api_userinfo_url);
                    $userinfo = json_decode($response, true);

                    $api_groupinfo_url = 'https://leerstof.be/campussintursula/groupinfo.php' . '?' .
                        'access_token=' . urlencode($access_token);
                    $response = file_get_contents($api_groupinfo_url);
                    $groupinfo = json_decode($response, true);
                    
                    $_SESSION['userID'] = $userinfo['userID'];
                    $_SESSION['first_name'] = $userinfo['name'];
                    $_SESSION['last_name'] = $userinfo['surname'];
                    foreach ($groupinfo['groups'] as $group) {
                        $_SESSION['groupID_' . $group['groupID']] = $group;
                        $_SESSION['groupName_' . $group['name']] = $group;
                    }
                    $_SESSION['rights'] = $groupinfo['groups'][0]['groupID'];

                    if ($_SESSION['first_name'] == 'Viki') {
                        $_SESSION['rights'] = "KamdM9nGxWA=";
                    }

                    header('Location: index.php?i=0');
                }
                ?>
            </pre>

        </div>
		

      
    </body>
</html>