<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");
	$user_data = check_login($conn);

    // test if user from ss
    $query9 = 'SELECT * FROM survey_teacher st
        JOIN smartschool_users ON st.teacher = smartschool_users.userID
        WHERE smartschool_users.userID = "' . $_SESSION['userID'] . '"';

    $result9 = $conn->query($query9);
    if ($result9->num_rows > 0) {
        $userID = $_SESSION['userID'];
    } else {
        $userID = $_SESSION['username'];
    }

    //Get userinfo
    if ($_SESSION['rights'] == "9IJssmQfbWA=" || $user_data['rights'] == "2" ) {
        $rights = '2'; //leerling
    } else if ($_SESSION['rights'] == "KamdM9nGxWA=" || $user_data['rights'] == "1") {
        $rights = '1'; //leerkracht
    } else if ($_SESSION['rights'] == '0. Admins' || $user_data['rights'] == "0"){
        $rights = '0'; //admin
    }

?>

<!DOCTYPE HTML>
<html lang='nl'>
<head>
    <title>Mijn Modules</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" href="surveyData.css">
    <script type="text/javascript" src="changeFavicon.js"></script>
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&display=swap" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-2.1.3.js"></script>
</head>

<body>
        <!--Transition-->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"> </script>

<script>   
$( document ).ready(function() {    
    $("#left-side, #right-side").fadeIn(1000);
})

// change colors => has to happen before anything else otherwise weird glitchy feeling
// Get different shades color
function adjust(color, amount) {
    return '#' + color.replace(/^#/, '').replace(/../g, color => ('0'+Math.min(255, Math.max(0, parseInt(color, 16) + amount)).toString(16)).substr(-2));
}
colorrr = localStorage.getItem('colour');
adjust(colorrr, -20);

function changeBackground() {
    if (localStorage.getItem('colour')) {
        y = "linear-gradient(to right, #222 0%, #222 25%, " + localStorage.getItem('colour') + " 25%, " + localStorage.getItem('colour') + " 100%)";
        document.getElementById('btn-convert').style.backgroundColor = localStorage.getItem('colour');
        document.body.style.background = y;

        z = document.querySelectorAll('.input');
        z.forEach(z => {
            z.addEventListener("focusin", function () {
                this.style.boxShadow = "0 0 5px " + localStorage.getItem('colour');
                this.style.borderColor = localStorage.getItem('colour');
            });
            z.addEventListener("focusout", function () {
                this.style.boxShadow = "none";
                this.style.borderColor = "black";
            });
        });

        $('.color1').css('background-color', adjust(colorrr, 45));
        $('.color2').css('background-color', adjust(colorrr, 45)); // Was origineel 15, dit was voorkeur gebruiker
        $('.color3').css('background-color', adjust(colorrr, -15));
        $('.color4').css('background-color', adjust(colorrr, -45));

        document.getElementById("color1").value = adjust(colorrr, 45);
        document.getElementById("color3").value = adjust(colorrr, -15);
        document.getElementById("color4").value = adjust(colorrr, -45);

        $('.title').css('color', adjust(colorrr, -45));
        $('.info').css('color', adjust(colorrr, -35));
        

        // jquery not working for pseudo-elements (slider webkit)
        for(var j = 0; j < document.styleSheets[1].rules.length; j++) {
            var rule = document.styleSheets[1].rules[j];
            if(rule.cssText.match("webkit-slider-thumb")) {
                rule.style.boxShadow= "-500px 0 0 500px " + adjust(colorrr, -15);
            }
        } 

        // Change favicon-color
        var faviconPath = 'img/favicons/' + localStorage.getItem('colour').slice(1) + '.ico';
        changeFavicon(faviconPath);

        // Change highlight-color
        var selectionStyle = document.createElement('style');
        selectionStyle.textContent = '::selection { background: ' + colorrr + '; }';
        document.head.appendChild(selectionStyle);

        // Change slider color
        var style = document.createElement('style');
        style.innerHTML = '.range::-webkit-slider-thumb { box-shadow: -500px 0 0 500px' + adjust(colorrr, -15) + '; }';
        document.head.appendChild(style);
    }
}
changeBackground(); 
</script>

    <span>
    <script>
        var myBtn = "";
    </script>
    <?php

    //Gets all answers


        if ($rights == '1') {
            //user = teacher
            
            ?> 
            <div id="sides-container" class="fadeIn" style="display:none;">
                <div id="left-side">
                    <a id="back" href="#" onclick="goBack()"> &lt; Terug</a>
                    <form action="pdfMaker/pdfMaker.php?" method="get" target="_blank">
                        <h1 style='color:white'>Filter</h1>
                        <input id="inputId" class="input2" value="<?= urlencode($userID) ?>" type="hidden" name="id"/>
                        <input id="inputName" class="input2" placeholder="Naam module" type="text" name="inputName"/>
                        <input id="inputTitle" class="input2" placeholder="Competentie" type="text" name="inputTitle">
                        <input id="inputInfo" class="input2" placeholder="Focusdoel(en)" type="text" name="inputInfo">
                        <input id="inputGroup" class="input2" placeholder="Groep" type="text" name="inputGroup" />
                        <input id="inputPupil" class="input2" placeholder="Leerling" type="text" name="inputPupil">
                        <input id="inputPeriod" class="input2" placeholder="Periode" type="text" name="inputPeriod">
                        <input id="color1" type="hidden" name="color1">
                        <input id="color3" type="hidden" name="color3">
                        <input id="color4" type="hidden" name="color4">
                        <input id="inputYear" type="hidden" name="inputYear">
                        <select id="thisIsTheChosenYear">
                            <?php 
                                //Get all schoolYears
                                $result = $conn->query(
                                    "SELECT DISTINCT schoolYear AS year0,
                                    schoolYear +1 AS year1
                                    FROM smartschool_groups_users");
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<option value='" . $row['year0'] . "' >" . $row['year0'] . "-" . $row['year1'] . "</option>";
                                    }
                                }
                            ?>
                        </select>
                        <!--pdf maker button-->
                        <button id="btn-convert" class='input2' type="submit">
                            Maak PDF
                        </button>
                    </form>
                </div>
                
                <div id="right-side">
                    <div id="titleWrapper">
                        <h1>Mijn modules</h3>
                    </div>
                    <div id="print-this">
                        <?php

                    $i = 0;
                        //display list of (chosen) surveys
                        if ($result9->num_rows > 0) {
                            $query = "SELECT *, surveys.name AS survey_name, survey_period.name AS `period`, surveys.id AS surveyID 
                                FROM surveys
                                LEFT JOIN survey_group ON survey_group.sid = surveys.id
                                LEFT JOIN smartschool_groups_users ON smartschool_groups_users.groupName = survey_group.group_name
                                LEFT JOIN smartschool_users ON smartschool_groups_users.userID = smartschool_users.userID
                                LEFT JOIN survey_period ON survey_period.id = surveys.period_id
                                WHERE smartschool_groups_users.userID = '$userID'
                                GROUP BY surveys.id
                                ORDER BY surveys.name ASC";

                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $surveyResult = $stmt->get_result();

                            if ($surveyResult && mysqli_num_rows($surveyResult) > 0) {
                                echo '<div id="grid-container" class="">';

                                while ($row = $surveyResult->fetch_assoc()) {
                                    $name = htmlspecialchars($row['survey_name']);
                                    $sid = $row['surveyID'];

                                    echo <<<HTML
                                    <div class="partsContainer grid-item">
                                        <div class="partOne collapsible">
                                            <div class="name"><b>$name</b></div>
                                    HTML;

                                    
                                    // Show all groups of survey
                                    $stmt1 = $conn->prepare(
                                        "SELECT group_name FROM survey_group
                                        JOIN smartschool_groups_users ON smartschool_groups_users.groupName = survey_group.group_name
                                        WHERE survey_group.sid = ?
                                        GROUP BY survey_group.group_name");
                                    $stmt1->bind_param('s', $sid);
                                    $stmt1->execute();
                                    $result1 = $stmt1->get_result();
        
                                    $groupHtml = '';
                                    if ($result1->num_rows > 0) {
                                        while ($row1 = $result1->fetch_assoc()) {
                                        $groupHtml .= htmlspecialchars($row1['group_name']) . '&nbsp;&nbsp;&nbsp;';
                                        }
                                    }
        
                                    $periodHtml = '<div class="period">' . htmlspecialchars($row['period']) . ' ( ';
                                    $periodHtml .= '<div class="thisIsTheRightYear">' . date('d M Y', strtotime($row['start_date1'])) . '</div> &nbsp; - &nbsp; ';
                                    $periodHtml .= date('d M Y', strtotime($row['end_date1'])) . ' &nbsp; en &nbsp; ';
                                    $periodHtml .= date('d M Y', strtotime($row['start_date2'])) . ' &nbsp; - &nbsp; ';
                                    $periodHtml .= date('d M Y', strtotime($row['end_date2'])) . ' )</div>';
                                    
                                    echo <<<HTML
                                                <div class="group">$groupHtml</div>
                                                $periodHtml
                                            </div> <!--Close .partOne-->
                                    HTML;

                                    $stmt2 = $conn->prepare(
                                        "SELECT *, survey_title.id AS titleID 
                                        FROM surveys
                                        JOIN survey_info_info ON survey_info_info.sid = surveys.id
                                        JOIN survey_info ON survey_info.id = survey_info_info.info_id 
                                        JOIN survey_title ON survey_info.title_id = survey_title.id
                                        WHERE surveys.id = ?
                                        GROUP BY survey_title.id"
                                    );
                                    $stmt2->bind_param('s', $sid);
                                    $stmt2->execute();
                                    $result2 = $stmt2->get_result();
                                    
                                    if ($result2->num_rows > 0) {
                                        $aantalTables = 0;
                                        echo "<div class='partTwo content'>";
                                        while($row2 = $result2->fetch_assoc()) {

                                                    // Get all titles + infos
                                                    $titleID = $row2['titleID'];
                                                    $result3 = $conn->query(
                                                        "SELECT *, survey_info_info.id AS siid FROM survey_info 
                                                        JOIN survey_title ON survey_info.title_id = survey_title.id
                                                        JOIN survey_info_info ON survey_info_info.info_id = survey_info.id
                                                        WHERE survey_title.id = '$titleID'
                                                        AND survey_info_info.sid = '$sid'
                                                        GROUP BY survey_info.id");
                                                        if ($result3->num_rows > 0) {
                                                            while($row3 = $result3->fetch_assoc()) {
                    echo "<div class='infoRow'>";
                    echo "<div class='title'>" . htmlspecialchars($row2['title']) . "</div>";
                    echo "<div class='info'>" . htmlspecialchars($row3['info']) . "</div> <br/>";
                                                                $siid = $row3['siid'];
                                                                
                                                                    $result4 = $conn->query( 
                                                                        "SELECT *, survey_pupil.id AS spid
                                                                        FROM survey_pupil
                                                                        JOIN smartschool_users ON survey_pupil.pupil = smartschool_users.userID
                                                                        WHERE survey_pupil.siid = '$siid'");
                                                                        if ($result4->num_rows > 0) {
                                                                            while($row4 = $result4->fetch_assoc()) {
                                                                                echo "YES";
                            echo "<div class='userWrapper'>";
                                echo "<a href='pupilOverview.php?id=" . urlencode($row4['userID']) . "' target='_blank' class='username'><b>" . utf8_decode($row4['first_name']) . " " . utf8_decode($row4['last_name']) . " &gt;</b></a><br/>";
                                                                                $spid = $row4['spid'];
                                                                                $feedback = $row4['feedback'];
                                                                                $totaal = 0;
                                                                                $aantal = 0;
                                echo "<div class='tableWrapper'>";
                                echo "<table>";
                                    ?>
                                    <table id='numbers'>
                                    <tr>
                                        <td class='emptyTD'></td>
                                        <td class='battery_container'><img src="img/batteries/0.png" class='battery'/></td>
                                        <td class='battery_container'><img src="img/batteries/1.png" class='battery'/></td>
                                        <td class='battery_container'><img src="img/batteries/2.png" class='battery'/></td>
                                        <td class='battery_container'><img src="img/batteries/3.png" class='battery'/></td>
                                    </tr> 
                                    <table>
                                    <?php
                                    $aantalTables++;
                                        echo "<tr>";
                                            echo "<td>Zelfevaluatie1:</td><td> <span class='bar color1' style='width: calc(" . $row4['q1'] . " * 25%) '></span></td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                            echo "<td>Plan van aanpak:</td><td>" . htmlspecialchars($row4['q2']) . "</td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                            echo "<td>Wie/wat kan je hierbij helpen:</td><td>" . htmlspecialchars($row4['q3']) . "</td>";
                                        echo "<tr>";
                                            echo " <td>Zelfevaluatie2:</td><td> <span class='bar color2' style='width: calc(" . $row4['q4'] . " * 25%) '></span></td>";
                                        echo "<tr/>";
                                        echo "<tbody style='border: none;'>";
                                            echo "<tr class='separator' colspan='2'></tr>";
                                        echo "</tbody>";
                                        ?>
                                    <table id='numbers'>
                                    <tr>
                                        <td class='emptyTD'></td>
                                        <td class='battery_container'><img src="img/batteries/0.png" class='battery'/></td>
                                        <td class='battery_container'><img src="img/batteries/1.png" class='battery'/></td>
                                        <td class='battery_container'><img src="img/batteries/2.png" class='battery'/></td>
                                        <td class='battery_container'><img src="img/batteries/3.png" class='battery'/></td>
                                    </tr> 
                                    <table><?php
                                                                                        // Smartschool other teachers
                                                                                $result5 = $conn->query( 
                                                                                    "SELECT * FROM survey_teacher
                                                                                    RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id 
                                                                                    INNER JOIN smartschool_users ON survey_teacher.teacher = smartschool_users.userID
                                                                                    WHERE teacher != '$userID'
                                                                                    AND survey_pupil.id = '$spid'
                                                                                    AND q5 != 0");
                                                                                if ($result5->num_rows > 0) {
                                                                                    while($row5 = $result5->fetch_assoc()) {
                                                                                        $totaal += $row5['q5'];
                                                                                        $aantal += 1;
                                //Beoordeling leerkracht
                                echo "<tr>";
                                    echo "<td>" . substr(utf8_decode($row5['first_name']), 0, 1) . ". " . utf8_decode($row5['last_name']) . "</td>";
                                    echo "<td><span class='bar color3' style='width: calc(" . $row5['q5'] . " * 25%) '></span></td>";
                                echo "</tr>";
                                                                                    }
                                                                                }

                                                                                //non-smartschool other teachers
                                                                                $result5 = $conn->query( 
                                                                                    "SELECT * FROM survey_teacher
                                                                                    RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id 
                                                                                    INNER JOIN users ON survey_teacher.teacher = users.username
                                                                                    WHERE teacher != '$userID'
                                                                                    AND survey_pupil.id = '$spid'
                                                                                    AND q5 != 0");
                                                                                if ($result5->num_rows > 0) {
                                                                                    while($row5 = $result5->fetch_assoc()) {
                                                                                        $totaal += $row5['q5'];
                                                                                        $aantal += 1;
                                //Beoordeling leerkracht
                                echo "<tr>";
                                    echo "<td>" . substr(utf8_decode($row5['first_name']), 0, 1) . ". " . utf8_decode($row5['last_name']) . "</td>";
                                    echo "<td><span class='bar color3' style='width: calc(" . $row5['q5'] . " * 25%) '></span></td>";
                                echo "</tr>";
                                                                                    }
                                                                                }

                                        echo "<tr>";
                                            $result6 = $conn->query( 
                                                "SELECT first_name, last_name
                                                FROM smartschool_users
                                                WHERE userID = '$userID'");
                                            if ($result6->num_rows > 0) {
                                                while($row6 = $result6->fetch_assoc()) {
                                                    echo "<td>" . substr(utf8_decode($row6['first_name']), 0, 1) . ". " . utf8_decode($row6['last_name']) . "</td>";
                                                }
                                            }
                                                                                        $result7 = $conn->query( 
                                                                                            "SELECT * FROM survey_teacher
                                                                                            RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id
                                                                                            WHERE teacher = '$userID'
                                                                                            AND survey_pupil.id = '$spid'");
            echo "<form class='my-form' method='POST'>";
                                                                                            if ($result7->num_rows > 0) {
                                                                                                while($row7 = $result7->fetch_assoc()) {
                                                                                                    if ($row7['q5'] > 0) {
                                                                                                        $totaal += $row7['q5'];
                                                                                                        $aantal += 1;
                                                                                                    }
                                            echo "<td class='rangeChange'> <input name='q5[" . $i . "]' class='range' type='range' min='0' max='4' step='1' value='" . $row7['q5'] . "'/></td>";
                                            echo "<td class='rangeChange show'> <span class='bar color3' style='width: calc(" . $row7['q5'] . " * 24.6%) '></span></td>";
                                                                                                }
                                                                                            } else {
                                            echo "<td class='rangeChange'> <input name='q5[" . $i . "]' class='range' type='range' min='0' max='4' step='1' value='0'/></td>";
                                            echo "<td class='rangeChange show'> <span class='bar color3' style='width: 0 '></span></td>";                              
                                                                                            }
                                        echo "</tr>";
                                        echo "<tr>";
                                            //Gemiddelde leerkrachten
                                            echo "<td> Gemiddelde leerkrachten: </td>";
                            if ($aantal != 0) {
                            $som = $totaal / $aantal;
                            } else {
                            $som = 0;
                            }
                                            echo "<td><span class='bar color4' style='width: calc(" . $som . "  * 25%) '></span></td>";
                                        echo "</tr>";
                                        echo "<tr>";
                                            echo "<td>Feedback leerkrachten</td>";
                                            echo "<td><input class='feedback' name='feedback[" . $i . "]' value='" . htmlspecialchars($feedback) . "'></input></td>";
                                            echo "<input name='spid[" . $i . "]' type='hidden' value='" . $spid . "'/>";
            echo "</form>";
                                        echo "</tr>";
                                    echo "</table>";
                                    $i++;
                                echo "</div>";
                            echo "</div>";
                            echo "<div class='pageBreak'></div>";
                            echo "<br/>";
                                                                            }
                                                                        }



                                                                // get non-smartschool user data

                                                                $result4 = $conn->query( 
                                                                    "SELECT *, survey_pupil.id AS spid
                                                                    FROM survey_pupil
                                                                    JOIN users ON survey_pupil.pupil = users.username
                                                                    WHERE survey_pupil.siid = '$siid'");
                                                                    if ($result4->num_rows > 0) {
                                                                        while($row4 = $result4->fetch_assoc()) {
                        echo "<div class='userWrapper'>";
                            echo "<a href='pupilOverview.php?id=" . urlencode($row4['username']) . "' target='_blank' class='username'><b>" . utf8_decode($row4['first_name']) . " " . utf8_decode($row4['last_name']) . " &gt;</b></a><br/>";
                                                                            $spid = $row4['spid'];
                                                                            $feedback = $row4['feedback'];
                                                                            $totaal = 0;
                                                                            $aantal = 0;
                            echo "<div class='tableWrapper'>";
                            echo "<table>";
                                ?>
                                <table id='numbers'>
                                <tr>
                                    <td class='emptyTD'></td>
                                    <td class='battery_container'><img src="img/batteries/0.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/1.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/2.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/3.png" class='battery'/></td>
                                </tr> 
                                <table>
                                <?php
                                $aantalTables++;
                                    echo "<tr>";
                                        echo "<td>Zelfevaluatie1:</td><td> <span class='bar color1' style='width: calc(" . $row4['q1'] . " * 25%) '></span></td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>Plan van aanpak:</td><td>" . htmlspecialchars($row4['q2']) . "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>Wie/wat kan je hierbij helpen:</td><td>" . htmlspecialchars($row4['q3']) . "</td>";
                                    echo "<tr>";
                                        echo " <td>Zelfevaluatie2:</td><td> <span class='bar color2' style='width: calc(" . $row4['q4'] . " * 25%) '></span></td>";
                                    echo "<tr/>";
                                    echo "<tbody style='border: none;'>";
                                        echo "<tr class='separator' colspan='2'></tr>";
                                    echo "</tbody>";
                                    ?>
                                <table id='numbers'>
                                <tr>
                                    <td class='emptyTD'></td>
                                    <td class='battery_container'><img src="img/batteries/0.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/1.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/2.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/3.png" class='battery'/></td>
                                </tr> 
                                <table><?php
                                                                                    // Smartschool other teachers
                                                                                $result5 = $conn->query( 
                                                                                    "SELECT * FROM survey_teacher
                                                                                    RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id 
                                                                                    INNER JOIN smartschool_users ON survey_teacher.teacher = smartschool_users.userID
                                                                                    WHERE teacher != '$userID'
                                                                                    AND survey_pupil.id = '$spid'
                                                                                    AND q5 != 0");
                                                                                if ($result5->num_rows > 0) {
                                                                                    while($row5 = $result5->fetch_assoc()) {
                                                                                        $totaal += $row5['q5'];
                                                                                        $aantal += 1;
                                //Beoordeling leerkracht
                                echo "<tr>";
                                    echo "<td>" . substr(utf8_decode($row5['first_name']), 0, 1) . ". " . utf8_decode($row5['last_name']) . "</td>";
                                    echo "<td><span class='bar color3' style='width: calc(" . $row5['q5'] . " * 25%) '></span></td>";
                                echo "</tr>";
                                                                                    }
                                                                                }

                                                                                //non-smartschool other teachers
                                                                                $result5 = $conn->query( 
                                                                                    "SELECT * FROM survey_teacher
                                                                                    RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id 
                                                                                    INNER JOIN users ON survey_teacher.teacher = users.username
                                                                                    WHERE teacher != '$userID'
                                                                                    AND survey_pupil.id = '$spid'
                                                                                    AND q5 != 0");
                                                                                if ($result5->num_rows > 0) {
                                                                                    while($row5 = $result5->fetch_assoc()) {
                                                                                        $totaal += $row5['q5'];
                                                                                        $aantal += 1;
                                //Beoordeling leerkracht
                                echo "<tr>";
                                    echo "<td>" . substr(utf8_decode($row5['first_name']), 0, 1) . ". " . utf8_decode($row5['last_name']) . "</td>";
                                    echo "<td><span class='bar color3' style='width: calc(" . $row5['q5'] . " * 25%) '></span></td>";
                                echo "</tr>";
                                                                                    }
                                                                                }

                                    echo "<tr>";
                                        $result6 = $conn->query( 
                                            "SELECT first_name, last_name
                                            FROM smartschool_users
                                            WHERE userID = '$userID'");
                                        if ($result6->num_rows > 0) {
                                            while($row6 = $result6->fetch_assoc()) {
                                                echo "<td>" . substr(utf8_decode($row6['first_name']), 0, 1) . ". " . utf8_decode($row6['last_name']) . "</td>";
                                            }
                                        }
                                                                                    $result7 = $conn->query( 
                                                                                        "SELECT * FROM survey_teacher
                                                                                        RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id
                                                                                        WHERE teacher = '$userID'
                                                                                        AND survey_pupil.id = '$spid'");
        echo "<form class='my-form' method='POST'>";
                                                                                        if ($result7->num_rows > 0) {
                                                                                            while($row7 = $result7->fetch_assoc()) {
                                                                                                if ($row7['q5'] > 0) {
                                                                                                    $totaal += $row7['q5'];
                                                                                                    $aantal += 1;
                                                                                                }
                                        echo "<td class='rangeChange'> <input name='q5[" . $i . "]' class='range' type='range' min='0' max='4' step='1' value='" . $row7['q5'] . "'/></td>";
                                        echo "<td class='rangeChange show'> <span class='bar color3' style='width: calc(" . $row7['q5'] . " * 24.6%) '></span></td>";
                                                                                            }
                                                                                        } else {
                                        echo "<td class='rangeChange'> <input name='q5[" . $i . "]' class='range' type='range' min='0' max='4' step='1' value='0'/></td>";
                                        echo "<td class='rangeChange show'> <span class='bar color3' style='width: 0 '></span></td>";                              
                                                                                        }
                                    echo "</tr>";
                                    echo "<tr>";
                                        //Gemiddelde leerkrachten
                                        echo "<td> Gemiddelde leerkrachten: </td>";
                        if ($aantal != 0) {
                        $som = $totaal / $aantal;
                        } else {
                        $som = 0;
                        }
                                        echo "<td><span class='bar color4' style='width: calc(" . $som . "  * 25%) '></span></td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>Feedback leerkrachten</td>";
                                        echo "<td><input class='feedback' name='feedback[" . $i . "]' value='" . htmlspecialchars($feedback) . "'></input></td>";
                                        echo "<input name='spid[" . $i . "]' type='hidden' value='" . $spid . "'/>";
        echo "</form>";
                                    echo "</tr>";
                                echo "</table>";
                                $i++;
                            echo "</div>";
                        echo "</div>";
                        echo "<div class='pageBreak'></div>";
                        echo "<br/>";
                                                                        }
                                                                    }


                                                                
                                                                    
                                                                }
                echo "</div>";
                                                            }
                                                            
                                                        }
                echo "</div>";
                                                }
            echo "</div>";
                                            }
        echo "</div>";
                                }
    echo "</div>";

} else {




                            // non-smartschool user
                            $query = "SELECT *, surveys.name AS survey_name, survey_period.name AS `period`, surveys.id AS surveyID 
                                FROM surveys
                                LEFT JOIN survey_group ON survey_group.sid = surveys.id
                                LEFT JOIN smartschool_groups_users ON smartschool_groups_users.groupName = survey_group.group_name
                                LEFT JOIN users ON smartschool_groups_users.userID = users.username
                                LEFT JOIN survey_period ON survey_period.id = surveys.period_id
                                WHERE smartschool_groups_users.userID = '$userID'
                                GROUP BY surveys.id
                                ORDER BY surveys.name ASC";

                            $stmt = $conn->prepare($query);
                            $stmt->execute();
                            $surveyResult = $stmt->get_result();

                            if ($surveyResult && mysqli_num_rows($surveyResult) > 0) {
                                echo '<div id="grid-container" class="">';

                                while ($row = $surveyResult->fetch_assoc()) {
                                    $name = htmlspecialchars($row['survey_name']);
                                    $sid = $row['surveyID'];

                                    echo <<<HTML
                                    <div class="partsContainer grid-item">
                                        <div class="partOne collapsible">
                                            <div class="name"><b>$name</b></div>
                                    HTML;

                                    
                                    // Show all groups of survey
                                    $stmt1 = $conn->prepare(
                                        "SELECT group_name FROM survey_group
                                        JOIN smartschool_groups_users ON smartschool_groups_users.groupName = survey_group.group_name
                                        WHERE survey_group.sid = ?
                                        GROUP BY survey_group.group_name");
                                    $stmt1->bind_param('s', $sid);
                                    $stmt1->execute();
                                    $result1 = $stmt1->get_result();
        
                                    $groupHtml = '';
                                    if ($result1->num_rows > 0) {
                                        while ($row1 = $result1->fetch_assoc()) {
                                        $groupHtml .= htmlspecialchars($row1['group_name']) . '&nbsp;&nbsp;&nbsp;';
                                        }
                                    }
        
                                    $periodHtml = '<div class="period">' . htmlspecialchars($row['period']) . ' ( ';
                                    $periodHtml .= '<div class="thisIsTheRightYear">' . date('d M Y', strtotime($row['start_date1'])) . '</div> &nbsp; - &nbsp; ';
                                    $periodHtml .= date('d M Y', strtotime($row['end_date1'])) . ' &nbsp; en &nbsp; ';
                                    $periodHtml .= date('d M Y', strtotime($row['start_date2'])) . ' &nbsp; - &nbsp; ';
                                    $periodHtml .= date('d M Y', strtotime($row['end_date2'])) . ' )</div>';
                                    
                                    echo <<<HTML
                                                <div class="group">$groupHtml</div>
                                                $periodHtml
                                            </div> <!--Close .partOne-->
                                    HTML;

                                    $stmt2 = $conn->prepare(
                                        "SELECT *, survey_title.id AS titleID 
                                        FROM surveys
                                        JOIN survey_info_info ON survey_info_info.sid = surveys.id
                                        JOIN survey_info ON survey_info.id = survey_info_info.info_id 
                                        JOIN survey_title ON survey_info.title_id = survey_title.id
                                        WHERE surveys.id = ?
                                        GROUP BY survey_title.id"
                                    );
                                    $stmt2->bind_param('s', $sid);
                                    $stmt2->execute();
                                    $result2 = $stmt2->get_result();
                                    
                                    if ($result2->num_rows > 0) {
                                        $aantalTables = 0;
                                        echo "<div class='partTwo content'>";
                                        while($row2 = $result2->fetch_assoc()) {

                                                    // Get all titles + infos
                                                    $titleID = $row2['titleID'];
                                                    $result3 = $conn->query(
                                                        "SELECT *, survey_info_info.id AS siid FROM survey_info 
                                                        JOIN survey_title ON survey_info.title_id = survey_title.id
                                                        JOIN survey_info_info ON survey_info_info.info_id = survey_info.id
                                                        WHERE survey_title.id = '$titleID'
                                                        AND survey_info_info.sid = '$sid'
                                                        GROUP BY survey_info.id");
                                                        if ($result3->num_rows > 0) {
                                                            while($row3 = $result3->fetch_assoc()) {
                    echo "<div class='infoRow'>";
                    echo "<div class='title'>" . htmlspecialchars($row2['title']) . "</div>";
                    echo "<div class='info'>" . htmlspecialchars($row3['info']) . "</div> <br/>";
                                                                $siid = $row3['siid'];
                                                                
                                                                $result4 = $conn->query( 
                                                                    "SELECT *, survey_pupil.id AS spid
                                                                    FROM survey_pupil
                                                                    JOIN smartschool_users ON survey_pupil.pupil = smartschool_users.userID
                                                                    WHERE survey_pupil.siid = '$siid'");
                                                                    if ($result4->num_rows > 0) {
                                                                        while($row4 = $result4->fetch_assoc()) {
                        echo "<div class='userWrapper'>";
                            echo "<a href='pupilOverview.php?id=" . urlencode($row4['userID']) . "' target='_blank' class='username'><b>" . utf8_decode($row4['first_name']) . " " . utf8_decode($row4['last_name']) . " &gt;</b></a><br/>";
                                                                            $spid = $row4['spid'];
                                                                            $feedback = $row4['feedback'];
                                                                            $totaal = 0;
                                                                            $aantal = 0;
                            echo "<div class='tableWrapper'>";
                            echo "<table>";
                                ?>
                                <table id='numbers'>
                                <tr>
                                    <td class='emptyTD'></td>
                                    <td class='battery_container'><img src="img/batteries/0.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/1.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/2.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/3.png" class='battery'/></td>
                                </tr> 
                                <table>
                                <?php
                                $aantalTables++;
                                    echo "<tr>";
                                        echo "<td>Zelfevaluatie1:</td><td> <span class='bar color1' style='width: calc(" . $row4['q1'] . " * 25%) '></span></td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>Plan van aanpak:</td><td>" . htmlspecialchars($row4['q2']) . "</td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>Wie/wat kan je hierbij helpen:</td><td>" . htmlspecialchars($row4['q3']) . "</td>";
                                    echo "<tr>";
                                        echo " <td>Zelfevaluatie2:</td><td> <span class='bar color2' style='width: calc(" . $row4['q4'] . " * 25%) '></span></td>";
                                    echo "<tr/>";
                                    echo "<tbody style='border: none;'>";
                                        echo "<tr class='separator' colspan='2'></tr>";
                                    echo "</tbody>";
                                    ?>
                                <table id='numbers'>
                                <tr>
                                    <td class='emptyTD'></td>
                                    <td class='battery_container'><img src="img/batteries/0.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/1.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/2.png" class='battery'/></td>
                                    <td class='battery_container'><img src="img/batteries/3.png" class='battery'/></td>
                                </tr> 
                                <table><?php
                                                                                    // Smartschool other teachers
                                                                                $result5 = $conn->query( 
                                                                                    "SELECT * FROM survey_teacher
                                                                                    RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id 
                                                                                    INNER JOIN smartschool_users ON survey_teacher.teacher = smartschool_users.userID
                                                                                    WHERE teacher != '$userID'
                                                                                    AND survey_pupil.id = '$spid'
                                                                                    AND q5 != 0");
                                                                                if ($result5->num_rows > 0) {
                                                                                    while($row5 = $result5->fetch_assoc()) {
                                                                                        $totaal += $row5['q5'];
                                                                                        $aantal += 1;
                                //Beoordeling leerkracht
                                echo "<tr>";
                                    echo "<td>" . substr(utf8_decode($row5['first_name']), 0, 1) . ". " . utf8_decode($row5['last_name']) . "</td>";
                                    echo "<td><span class='bar color3' style='width: calc(" . $row5['q5'] . " * 25%) '></span></td>";
                                echo "</tr>";
                                                                                    }
                                                                                }

                                                                                //non-smartschool other teachers
                                                                                $result5 = $conn->query( 
                                                                                    "SELECT * FROM survey_teacher
                                                                                    RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id 
                                                                                    INNER JOIN users ON survey_teacher.teacher = users.username
                                                                                    WHERE teacher != '$userID'
                                                                                    AND survey_pupil.id = '$spid'
                                                                                    AND q5 != 0");
                                                                                if ($result5->num_rows > 0) {
                                                                                    while($row5 = $result5->fetch_assoc()) {
                                                                                        $totaal += $row5['q5'];
                                                                                        $aantal += 1;
                                //Beoordeling leerkracht
                                echo "<tr>";
                                    echo "<td>" . substr(utf8_decode($row5['first_name']), 0, 1) . ". " . utf8_decode($row5['last_name']) . "</td>";
                                    echo "<td><span class='bar color3' style='width: calc(" . $row5['q5'] . " * 25%) '></span></td>";
                                echo "</tr>";
                                                                                    }
                                                                                }

                                    echo "<tr>";
                                        $result6 = $conn->query( 
                                            "SELECT first_name, last_name
                                            FROM users
                                            WHERE username = '$userID'");
                                        if ($result6->num_rows > 0) {
                                            while($row6 = $result6->fetch_assoc()) {
                                                echo "<td>" . substr(utf8_decode($row6['first_name']), 0, 1) . ". " . utf8_decode($row6['last_name']) . "</td>";
                                            }
                                        }
                                                                                    $result7 = $conn->query( 
                                                                                        "SELECT * FROM survey_teacher
                                                                                        RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id
                                                                                        WHERE teacher = '$userID'
                                                                                        AND survey_pupil.id = '$spid'");
        echo "<form class='my-form' method='POST'>";
                                                                                        if ($result7->num_rows > 0) {
                                                                                            while($row7 = $result7->fetch_assoc()) {
                                                                                                if ($row7['q5'] > 0) {
                                                                                                    $totaal += $row7['q5'];
                                                                                                    $aantal += 1;
                                                                                                }
                                        echo "<td class='rangeChange'> <input name='q5[" . $i . "]' class='range' type='range' min='0' max='4' step='1' value='" . $row7['q5'] . "'/></td>";
                                        echo "<td class='rangeChange show'> <span class='bar color3' style='width: calc(" . $row7['q5'] . " * 24.6%) '></span></td>";
                                                                                            }
                                                                                        } else {
                                        echo "<td class='rangeChange'> <input name='q5[" . $i . "]' class='range' type='range' min='0' max='4' step='1' value='0'/></td>";
                                        echo "<td class='rangeChange show'> <span class='bar color3' style='width: 0 '></span></td>";                              
                                                                                        }
                                    echo "</tr>";
                                    echo "<tr>";
                                        //Gemiddelde leerkrachten
                                        echo "<td> Gemiddelde leerkrachten: </td>";
                        if ($aantal != 0) {
                        $som = $totaal / $aantal;
                        } else {
                        $som = 0;
                        }
                                        echo "<td><span class='bar color4' style='width: calc(" . $som . "  * 25%) '></span></td>";
                                    echo "</tr>";
                                    echo "<tr>";
                                        echo "<td>Feedback leerkrachten</td>";
                                        echo "<td><input class='feedback' name='feedback[" . $i . "]' value='" . htmlspecialchars($feedback) . "'></input></td>";
                                        echo "<input name='spid[" . $i . "]' type='hidden' value='" . $spid . "'/>";
        echo "</form>";
                                    echo "</tr>";
                                echo "</table>";
                                $i++;
                            echo "</div>";
                        echo "</div>";
                        echo "<div class='pageBreak'></div>";
                        echo "<br/>";
                                                                        }
                                                                    }



                                                            // get non-smartschool user data

                                                            $result4 = $conn->query( 
                                                                "SELECT *, survey_pupil.id AS spid
                                                                FROM survey_pupil
                                                                JOIN users ON survey_pupil.pupil = users.username
                                                                WHERE survey_pupil.siid = '$siid'");
                                                                if ($result4->num_rows > 0) {
                                                                    while($row4 = $result4->fetch_assoc()) {
                    echo "<div class='userWrapper'>";
                        echo "<a href='pupilOverview.php?id=" . urlencode($row4['username']) . "' target='_blank' class='username'><b>" . utf8_decode($row4['first_name']) . " " . utf8_decode($row4['last_name']) . " &gt;</b></a><br/>";
                                                                        $spid = $row4['spid'];
                                                                        $feedback = $row4['feedback'];
                                                                        $totaal = 0;
                                                                        $aantal = 0;
                        echo "<div class='tableWrapper'>";
                        echo "<table>";
                            ?>
                            <table id='numbers'>
                            <tr>
                                <td class='emptyTD'></td>
                                <td class='battery_container'><img src="img/batteries/0.png" class='battery'/></td>
                                <td class='battery_container'><img src="img/batteries/1.png" class='battery'/></td>
                                <td class='battery_container'><img src="img/batteries/2.png" class='battery'/></td>
                                <td class='battery_container'><img src="img/batteries/3.png" class='battery'/></td>
                            </tr> 
                            <table>
                            <?php
                            $aantalTables++;
                                echo "<tr>";
                                    echo "<td>Zelfevaluatie1:</td><td> <span class='bar color1' style='width: calc(" . $row4['q1'] . " * 25%) '></span></td>";
                                echo "</tr>";
                                echo "<tr>";
                                    echo "<td>Plan van aanpak:</td><td>" . htmlspecialchars($row4['q2']) . "</td>";
                                echo "</tr>";
                                echo "<tr>";
                                    echo "<td>Wie/wat kan je hierbij helpen:</td><td>" . htmlspecialchars($row4['q3']) . "</td>";
                                echo "<tr>";
                                    echo " <td>Zelfevaluatie2:</td><td> <span class='bar color2' style='width: calc(" . $row4['q4'] . " * 25%) '></span></td>";
                                echo "<tr/>";
                                echo "<tbody style='border: none;'>";
                                    echo "<tr class='separator' colspan='2'></tr>";
                                echo "</tbody>";
                                ?>
                            <table id='numbers'>
                            <tr>
                                <td class='emptyTD'></td>
                                <td class='battery_container'><img src="img/batteries/0.png" class='battery'/></td>
                                <td class='battery_container'><img src="img/batteries/1.png" class='battery'/></td>
                                <td class='battery_container'><img src="img/batteries/2.png" class='battery'/></td>
                                <td class='battery_container'><img src="img/batteries/3.png" class='battery'/></td>
                            </tr> 
                            <table><?php                                        // Smartschool other teachers
                                                                                $result5 = $conn->query( 
                                                                                    "SELECT * FROM survey_teacher
                                                                                    RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id 
                                                                                    INNER JOIN smartschool_users ON survey_teacher.teacher = smartschool_users.userID
                                                                                    WHERE teacher != '$userID'
                                                                                    AND survey_pupil.id = '$spid'
                                                                                    AND q5 != 0");
                                                                                if ($result5->num_rows > 0) {
                                                                                    while($row5 = $result5->fetch_assoc()) {
                                                                                        $totaal += $row5['q5'];
                                                                                        $aantal += 1;
                                //Beoordeling leerkracht
                                echo "<tr>";
                                    echo "<td>" . substr(utf8_decode($row5['first_name']), 0, 1) . ". " . utf8_decode($row5['last_name']) . "</td>";
                                    echo "<td><span class='bar color3' style='width: calc(" . $row5['q5'] . " * 25%) '></span></td>";
                                echo "</tr>";
                                                                                    }
                                                                                }

                                                                                //non-smartschool other teachers
                                                                                $result5 = $conn->query( 
                                                                                    "SELECT * FROM survey_teacher
                                                                                    RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id 
                                                                                    INNER JOIN users ON survey_teacher.teacher = users.username
                                                                                    WHERE teacher != '$userID'
                                                                                    AND survey_pupil.id = '$spid'
                                                                                    AND q5 != 0");
                                                                                if ($result5->num_rows > 0) {
                                                                                    while($row5 = $result5->fetch_assoc()) {
                                                                                        $totaal += $row5['q5'];
                                                                                        $aantal += 1;
                                //Beoordeling leerkracht
                                echo "<tr>";
                                    echo "<td>" . substr(utf8_decode($row5['first_name']), 0, 1) . ". " . utf8_decode($row5['last_name']) . "</td>";
                                    echo "<td><span class='bar color3' style='width: calc(" . $row5['q5'] . " * 25%) '></span></td>";
                                echo "</tr>";
                                                                                    }
                                                                                }

                                echo "<tr>";
                                    $result6 = $conn->query( 
                                        "SELECT first_name, last_name
                                        FROM users
                                        WHERE username = '$userID'");
                                    if ($result6->num_rows > 0) {
                                        while($row6 = $result6->fetch_assoc()) {
                                            echo "<td>" . substr(utf8_decode($row6['first_name']), 0, 1) . ". " . utf8_decode($row6['last_name']) . "</td>";
                                        }
                                    }
                                                                                $result7 = $conn->query( 
                                                                                    "SELECT * FROM survey_teacher
                                                                                    RIGHT JOIN survey_pupil ON survey_teacher.spid = survey_pupil.id
                                                                                    WHERE teacher = '$userID'
                                                                                    AND survey_pupil.id = '$spid'");
    echo "<form class='my-form' method='POST'>";
                                                                                    if ($result7->num_rows > 0) {
                                                                                        while($row7 = $result7->fetch_assoc()) {
                                                                                            if ($row7['q5'] > 0) {
                                                                                                $totaal += $row7['q5'];
                                                                                                $aantal += 1;
                                                                                            }
                                    echo "<td class='rangeChange'> <input name='q5[" . $i . "]' class='range' type='range' min='0' max='4' step='1' value='" . $row7['q5'] . "'/></td>";
                                    echo "<td class='rangeChange show'> <span class='bar color3' style='width: calc(" . $row7['q5'] . " * 24.6%) '></span></td>";
                                                                                        }
                                                                                    } else {
                                    echo "<td class='rangeChange'> <input name='q5[" . $i . "]' class='range' type='range' min='0' max='4' step='1' value='0'/></td>";
                                    echo "<td class='rangeChange show'> <span class='bar color3' style='width: 0 '></span></td>";                              
                                                                                    }
                                echo "</tr>";
                                echo "<tr>";
                                    //Gemiddelde leerkrachten
                                    echo "<td> Gemiddelde leerkrachten: </td>";
                    if ($aantal != 0) {
                    $som = $totaal / $aantal;
                    } else {
                    $som = 0;
                    }
                                    echo "<td><span class='bar color4' style='width: calc(" . $som . "  * 25%) '></span></td>";
                                echo "</tr>";
                                echo "<tr>";
                                    echo "<td>Feedback leerkrachten</td>";
                                    echo "<td><input class='feedback' name='feedback[" . $i . "]' value='" . htmlspecialchars($feedback) . "'></input></td>";
                                    echo "<input name='spid[" . $i . "]' type='hidden' value='" . $spid . "'/>";
    echo "</form>";
                                echo "</tr>";
                            echo "</table>";
                            $i++;
                        echo "</div>";
                    echo "</div>";
                    echo "<div class='pageBreak'></div>";
                    echo "<br/>";
                                                                    }
                                                                }


                                                                }
                echo "</div>";
                                                            }
                                                            
                                                        }
                echo "</div>";
                                                }
            echo "</div>";
                                            }
        echo "</div>";
                                }
    echo "</div>";
}
                        

                        ?>
                    </div>
                </div>
            </div>
            
            </a>

            </div>

            <?php



        } else {
            //user = student

        }
    
    $conn->close();
    ?>
    </span>








    

<script>

    changeBackground();


    
</script>





<script>
//TRANSITION
$( document ).ready(function() {
    $(".fadeIn").fadeIn(1000);
    if (window.matchMedia('(max-width: 767px)').matches) {
        $(".fadeInMobile").fadeIn(1000);
    }
});

$( "#back").click(function() {
    setTimeout(function(){location.href="index.php?i=1"} , 1000);  
    $(".fadeIn").fadeOut(950);
});

</script>








<script>


//LIVE SEARCH NAME
$("#inputName").on('keyup', function(){
    liveSearch();
})

//LIVE SEARCH GROUP
$("#inputGroup").on('keyup', function(){
    liveSearch();
})

//LIVE SEARCH PERIOD
$("#inputPeriod").on('keyup', function(){
    liveSearch();
})

//LIVE SEARCH TITLE
$("#inputTitle").on('keyup', function(){
    liveSearch();
})

//LIVE SEARCH INFO
$("#inputInfo").on('keyup', function(){
    liveSearch();
})

//LIVE SEARCH PUPIL
$("#inputPupil").on('keyup', function(){
    liveSearch();
})


function liveSearch() {
    var value = $('#inputName').val().toLowerCase();
    var value1 = $('#inputTitle').val().toLowerCase();
    var value2 = $('#inputInfo').val().toLowerCase();
    var value3 = $('#inputGroup').val().toLowerCase();
    var value4 = $('#inputPupil').val().toLowerCase();
    var value5 = $('#inputPeriod').val().toLowerCase();


    // Filter on titles and infos
    $(".infoRow").each(function () {
        $(this).hide();
        if ($(this).find('.title').text().toLowerCase().search(value1) > -1) {
            if ($(this).find('.info').text().toLowerCase().search(value2) > -1) {
                $(this).find(".userWrapper").each(function () {
                    if ($(this).find('.username').text().toLowerCase().search(value4) > -1) {
                        $(this).show();
                        $(this).parent().show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        }

    });

    // Hide all containers who are empty now
    $(".partsContainer").each(function () {
        if (($(this).find('.userWrapper').find('.username').text().toLowerCase().search(value4) > -1) && ($(this).find('.name').text().toLowerCase().search(value) > -1) && ($(this).find('.title').text().toLowerCase().search(value1) > -1) && ($(this).find('.info').text().toLowerCase().search(value2) > -1) && ($(this).find('.group').text().toLowerCase().search(value3) > -1) && ($(this).find('.period').text().toLowerCase().search(value5) > -1)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });


}





//COLLAPSIBLE

var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
    coll[i].style.cssText += 'border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;';
    coll[i].addEventListener("click", function() {
        this.classList.toggle("active");
        var content = this.nextElementSibling;
        if (content.style.maxHeight){
            content.style.maxHeight = null;
            this.style.cssText += 'border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;';
        } else {
            content.style.maxHeight = content.scrollHeight + "px";
            this.style.cssText -= 'border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;';
        }
    });
}

</script>

<script>

    //delete doubles from year-picker
    function deleteDoubles() {
        const options = []

        document.querySelectorAll('#thisIsTheChosenYear > option').forEach((option) => {
            if (options.includes(option.value)) option.remove()
            else options.push(option.value)
        })
    }

      
    function checkYear() {
        $('.thisIsTheRightYear').each(function () {

            var startYear = +$( "#thisIsTheChosenYear option:selected" ).val();
            var endYear = +$( "#thisIsTheChosenYear option:selected" ).val() +1;

            var startDate = Date.parse('09 01 ' + startYear);
            var endDate = Date.parse('08 31 ' + endYear); // format: month day year !!!
            var thisDate = Date.parse($(this).text());

            if (thisDate >= startDate && thisDate <= endDate) {
                $(this).parent().parent().parent().show();
            } else {
                $(this).parent().parent().parent().hide();
            }

            //PDF year
            document.getElementById("inputYear").value = startYear;

        });
    }

    $( document ).ready(function() {
        deleteDoubles();
    });

    $('#thisIsTheChosenYear').on('change', function(){
        checkYear();
    });

    // When input feedback unselected => save in db
    $('.feedback').blur(function() {
        var feedback = $(this).val();
        var index = $(this).attr('name').match(/\d+/)[0];
        var spid = $('input[name="spid[' + index + ']"]').val();

        console.log(feedback);
        console.log(spid);

        $.ajax({
            type:'POST',
            url:'ajaxFolder/saveFeedback.php',
            data: {feedback: feedback, spid: spid},
            async: false // wait till ready
        });
    });


    $('.range').change(function() {
        var q5 = $(this).val();
        var index = $(this).attr('name').match(/\[(\d+)\]/)[1];
        var spid = $('input[name="spid[' + index + ']"]').val();

        $.ajax({
            type:'POST',
            url:'ajaxFolder/saveQ5.php',
            data: {q5: q5, spid: spid},
            async: false // wait till ready
        });
    });

    

    function goBack() {
        $("#left-side, #right-side").fadeOut(1000);
        setTimeout(function() {
            window.location.href = "index.php?i=1";
        }, 1000);
    }


    // AUTOMATE PRESENTATION
    // Add the event listener
    document.addEventListener('keydown', function (event) {
        if (event.key === 'PageDown' || event.keyCode === 34 || event.code === "ArrowDown") {
            // Create the div element
            var goDiv = document.createElement("div");
            goDiv.id = "GO";
            document.body.appendChild(goDiv);

            // Remove div after 1 second
            setTimeout(function () {
                document.body.removeChild(document.getElementById("GO"));
            }, 100);
        }
    });


</script>


</body>
</html>