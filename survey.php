<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}

include("functions.php");
check_login($conn);
$userID = $_SESSION['userID'];

//get survey info
$sid = $_GET['passId'];
$title_id = $_GET['passTitle'];

//something was posted
if($_SERVER['REQUEST_METHOD'] == "POST")
{

    //Save answers to survey
    $index = $_POST['index'];
    $saved = 0;
    $submitted = 0;
    for ($i = 0; $i <= $index; $i++) {
        
        //Submit answers to survey
        if(isset($_POST["submitSurvey"])) {
            if(isset($_POST["q1" . strval($i)])) {
                $q1 = $_POST["q1" .  strval($i)];
                $q2 = addslashes($_POST["q2" .  strval($i)]);
                $q3 = addslashes($_POST["q3" .  strval($i)]);
                $siid = $_POST["siid" . strval($i)];

                //if everything filled in -> submit
                $conn->query("UPDATE survey_pupil SET q1 = '$q1', q2 = '$q2', q3 = '$q3', status = status + 1 WHERE `siid` = '$siid' AND pupil = '$userID'");
                $message = 'verzonden';

            } else if(isset($_POST["q4" . strval($i)])) {
                $q4 = $_POST["q4" .  strval($i)];
                $siid = $_POST["siid" . strval($i)];
    
                //if everything filled in -> submit
                $conn->query("UPDATE survey_pupil SET q4 = '$q4', status = status + 1 WHERE `siid` = '$siid' AND pupil = '$userID'");
                $message = 'verzonden';
            }
        }
    }
    

    header("location: submitted.php?message=Uw antwoorden zijn " . $message);

}

?>

<!DOCTYPE HTML>
<html lang='nl'>
<head>
    <title>Module</title>
    <meta name="description" content="Reflect is een online tool waarmee leerlingen zichzelf kunnen reflecteren op basis van competenties en focusdoelen. Leerkrachten kunnen hun leerlingen beoordelen en helpen groeien.">
    <meta name="keywords" content="Reflect, zelfreflectie, leerlingen, competenties, focusdoelen, beoordeling, groei, online tool">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <script type="text/javascript" src="changeFavicon.js"></script>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="survey.css"/>
    <style>
        .range::-webkit-slider-thumb { box-shadow: 0 0 5px 0 rgba(0, 40, 0, 0.75); }

</style>
</head>

<body>
<script>
// change colors => has to happen before anything else otherwise weird glitchy feeling
function changeBackground() {
    if (localStorage.getItem('colour')) {
        colorrr = localStorage.getItem('colour');
        y = localStorage.getItem('colour');
        document.body.style.background = y;

        $('.input, .arrow, button, select').css('border-color', adjust(colorrr, -50));
        $('.step').css('background-color', adjust(colorrr, -50));
        $('h1, p, .tab, input, button, select').css('color', adjust(colorrr, -50));

        // jquery not working for pseudo-elements (slider webkit)
        for(var j = 0; j < document.styleSheets[1].rules.length; j++) {
            var rule = document.styleSheets[1].rules[j];
            if(rule.cssText.match("webkit-slider-thumb")) {
                rule.style.boxShadow= "-500px 0 0 500px " + adjust(colorrr, -15);
            }
        } 

        // Change slider color
        var style = document.createElement('style');
        style.innerHTML = '.range::-webkit-slider-thumb { box-shadow: -500px 0 0 500px' + adjust(colorrr, -15) + '; }';
        document.head.appendChild(style);

        // Change favicon-color
        var faviconPath = 'img/favicons/' + localStorage.getItem('colour').slice(1) + '.ico';
        changeFavicon(faviconPath);

        // Change highlight-color
        var selectionStyle = document.createElement('style');
        selectionStyle.textContent = '::selection { background: ' + colorrr + '; }';
        document.head.appendChild(selectionStyle);
    }
}
changeBackground(); </script>
    <a id='back' href="index.php"> &lt; Terug</a>

    <div id="right-side">
            <form id="regForm" method="POST">

                <!-- Circles which indicates the steps of the form: -->
                <div id="steps" class="fadeIn fadeInMobile" style="display:none;">
                </div>

        <div id="form-container" class="fadeIn fadeInMobile" style="display:none;">
            <form method="POST">
                        <?php
                            $result = $conn->query(
                                "SELECT * FROM survey_info_info 
                                LEFT JOIN survey_pupil ON survey_pupil.siid = survey_info_info.id
                                LEFT JOIN survey_info ON survey_info_info.info_id = survey_info.id
                                LEFT JOIN survey_title ON survey_info.title_id = survey_title.id
                                WHERE survey_info_info.sid = '$sid'
                                    AND survey_pupil.pupil = '$userID' 
                                    AND survey_title.id = '$title_id' 
                                    AND survey_pupil.status < 2");
                            //Gets all answers
                            $index = 0;

                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                        ?>
                        

                        <?php 

                        //Check if zelfevaluatie 1
                        $status = $row['status'];
                        if ($status == 0) {

                            $q1 = $row['q1'];
                            $q2 = htmlspecialchars($row['q2']);
                            $q3 = htmlspecialchars($row['q3']); ?>


                            
                    <!-- One "tab" for each step in the form: -->
                    <div class="tab">
                        <H2><?php echo htmlspecialchars($row["title"]); ?></H2>
                        <H3> <?php echo htmlspecialchars($row["info"]); ?></H3> 
                        <BR/>
                        <input type="hidden" name="siid<?php echo $index ?>" value="<?php echo $row['siid'] ?>"/>
                        <table>
                            <tr>
                                <td class='battery_container'><img src="img/batteries/0.png" class='battery'/></td>
                                <td class='battery_container'><img src="img/batteries/1.png" class='battery'/></td>
                                <td class='battery_container'><img src="img/batteries/2.png" class='battery'/></td>
                                <td class='battery_container'><img src="img/batteries/3.png" class='battery'/></td>
                            </tr>
                            <tr>
                                <td colspan='4' class='rangeChange'> <input name='q1<?php echo $index ?>' class='range range::-webkit-slider-thumb' type='range' min='0' max='4' step='1' value='<?php echo $q1 ?>'/></td>
                            </tr>
                        </table> <br/>
                        <H4>Wat is je plan van aanpak? </H4><input class='input' type="text" name="q2<?php echo $index ?>" value="<?php echo $q2; ?>" /> <br/> <br/> 
                        <H4>Wie/wat kan je daarbij helpen? </H4><input class='input' type="text" name="q3<?php echo $index ?>" value="<?php echo $q3; ?>" /> <br/>
                    </div>

   



 
                        <?php
                            
                        //if zelfevaluatie 2
                        } else if ($status == 1) {
                            $q4 = $row['q4'];
                        ?>
                        
                    <div class="tab">
                        <H1><?php echo htmlspecialchars($row["title"]); ?></H1>
                        <p> <?php echo htmlspecialchars($row["info"]); ?></p> 
                        <BR/>
                        <input type="hidden" name="siid<?php echo $index ?>" value="<?php echo $row['siid'] ?>"/>
                        <table>
                            <tr>
                                <td>1</td>
                                <td>2</td>
                                <td>3</td>
                                <td>4</td>
                            </tr>
                            <tr>
                                <td colspan='4' class='rangeChange'> <input name='q4<?php echo $index ?>' class='range' type='range' min='0' max='4' step='1' value='<?php echo $q4 ?>'/></td>
                            </tr>
                        </table>
                    </div>

                            <?php
                        } $index++;
                }
                
                    ?>
                <input type="hidden" name="index" value="<?php echo $index ?>"/>
    <?php
        $index = 0;
            }  ?>
                    <div>
                    <div id="buttons">
                        <div class='buttonWrapper'>
                            <button type="button" onclick="nextPrev(-1)"><i id="prevBtn" class="arrow left"></i></button>
                        </div>
                        <div class='buttonWrapper'>
                            <button id="parentBtn1" type="button" name="saveSurvey"></button>
                        </div>
                        <div class='buttonWrapper'>
                            <button id="parentBtn" type="button" onclick="nextPrev(1)" name="submitSurvey"><i id="nextBtn" class="arrow right"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div> 
    </form>
    </body>
</html>




<script>
//On typing, change table on left side with info

let newInput = document.getElementById("inputName");

//Execute function on keyup
newInput.addEventListener("keyup", (e) => {

    //clear all the items
    let item = document.getElementById("newName");
    item.remove();

    //Set new value
    let typingName = document.getElementById("typingName");
    const newNode = document.createElement("div");
    newNode.setAttribute("id", "newName");
    newNode.innerText = newInput.value;
    typingName.appendChild(newNode);

    let overzicht = document.getElementById('overzicht');
    overzicht.style.display = "block";
    document.getElementById('hr1').style.display = "block";

})


</script>





<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>


<script>
    //jquery + ajax
function loadDoc(e) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange=function() {
        document.getElementById("demo").innerHTML = e;
    };
    var surveyList = <?php echo json_encode($surveyList[0]); ?>;
    xhttp.open("GET", "index.php?t=" + surveyList, true);
    xhttp.send();

}

function hideScrollbar() {
    // Hide all <br> so scrollbar isn't necessary on small laptops
    const tab = document.querySelector('.tab');
    const contentHeight = tab.scrollHeight;
    const containerHeight = tab.clientHeight;
    if (contentHeight > containerHeight) {
        const brTags = tab.querySelectorAll('br');
        brTags.forEach(br => br.style.display = 'none');
    }
}

document.addEventListener("webkitfullscreenchange", function() {
    hideScrollbar();
});

var currentTab = 0; // Current tab is set to be the first tab (0)
window.onload = function() {
    generateSteps();
    showTab(currentTab); // Display the current tab
    changeBackground();
    hideScrollbar();
}

function generateSteps() {
    var tabs = document.getElementsByClassName("tab");
    var stepWrapper = document.getElementById("steps");

    //Fix number of steps
    for (i = 0; i < tabs.length; i++) {
        const newNode = document.createElement("span");
        newNode.setAttribute("class", "step");
        stepWrapper.appendChild(newNode);
    }
}

function showTab(n) {
    //display the specified tab of the form
    var x = document.getElementsByClassName("tab");
    x[n].style.display = "block";

    // fix Previous/Next buttons
    if (n == 0) {
        document.getElementById("prevBtn").style.display = "none";
        document.getElementById("prevBtn").disabled = true;
    } else {
        document.getElementById("prevBtn").style.display = "inline-block";
        document.getElementById("prevBtn").disabled = false;
    }

    if (n == (x.length - 1)) {
        document.getElementById("parentBtn").parentElement.innerHTML = "<input id='parentBtn'  type='submit' name='submitSurvey' value='Verzenden' style='color:" + adjust(colorrr, -50) + "'/>";
    } else {
        document.getElementById("parentBtn").parentElement.innerHTML = "<button id='parentBtn' type='button' onclick='nextPrev(1)' name='submitSurvey1'><i id='nextBtn' class='arrow right' style='border-color:" + adjust(colorrr, -50) + "'/></i></button>";
        document.getElementById("parentBtn1").parentElement.innerHTML = "<button id='parentBtn1' type='button'style='color:" + adjust(colorrr, -50) + "'/></button>";
    }
    //run a function that displays the correct step indicator:
    fixStepIndicator(n);
}

function nextPrev(n) {
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("tab");
    // Hide the current tab:
    x[currentTab].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentTab = currentTab + n;
    // if you have reached the end of the form... :
    if (currentTab >= x.length) {
        //...the form gets submitted:
        document.getElementById("regForm").submit();
        return false;
    }
    // Otherwise, display the correct tab:
    showTab(currentTab);
}

function fixStepIndicator(n) {
    // This function removes the "active" class of all steps...
    var i, x = document.getElementsByClassName("step");
    for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");
    }
    //... and adds the "active" class to the current step:
    x[n].className += " active";
    x[n].className += " finish";
}

//typing recommendation
let names = <?php echo json_encode($usernameList); ?>;
let sortedNames = names.sort();
let input = document.getElementById("username");

input.addEventListener("keyup", (e) => {

    //Initially remove all elements ( so if user erases a letter or adds new letter then clean previous outputs)
    removeElements();
    for (let i of sortedNames) {
        //convert input to lowercase and compare with each string
        if (
            i.toLowerCase().startsWith(input.value.toLowerCase()) &&
            input.value != ""
        ) {
            //create li element
            let listItem = document.createElement("li");
            //One common class name
            listItem.classList.add("list-items");
            listItem.style.cursor = "pointer";
            listItem.setAttribute("onclick", "displayNames('" + i + "')");
            //Display matched part in bold
            let word = "<b>" + i.substr(0, input.value.length) + "</b>";
            word += i.substr(input.value.length);
            //display value in array
            listItem.innerHTML = word;
            document.querySelector(".list").appendChild(listItem);
        }
    }

    this.getGroups();
        return;
});

function displayNames(value) {

    usernameListContainer = document.querySelector("div#groupsList ul");
    usernameListContainerCopy = document.querySelector("div#groupsListCopy ul");
    

    var groups = [];
    document.querySelectorAll("div#groupsList ul li").forEach((ele) => {
        groups.push(ele.innerHTML.replace(/ /g, ""));
    });

    //add to selected-list 
    let li0 = document.createElement('li');
    li0.innerHTML = value + "<input type='hidden' name='groups[]' value='" + value + "'/>";
    usernameListContainer.appendChild(li0);

    let li1 = document.createElement('li');
    li1.innerHTML = value + "<input type='hidden' name='groups[]' value='" + value + "'/>";
    usernameListContainerCopy.appendChild(li1);
    input.value = "";

    let selected = document.getElementById('selected');
    //let selectedCopy = document.getElementById('selectedCopy');
    let overzicht = document.getElementById('overzicht');
    selected.style.display = "block";
    //selectedCopy.style.display = "block";
    overzicht.style.display = "block";
    document.getElementById('hr3').style.display = "block";

    //Remove from array so can't be added twice
    const index = names.indexOf(value);
    const newArray = names.splice(index, 1);
    console.log(newArray);

    // removing username from the list
    li0.addEventListener("click", function () {
        names.push(value);
        console.log(sortedNames);
        li0.remove();
        li1.remove();
    });

    removeElements();
}

function removeElements() {
    //clear all the items
    let items = document.querySelectorAll(".list-items");
    items.forEach((item) => {
        item.remove();
    });
}

</script>






<script>
//TRANSITION
$( document ).ready(function() {
    if (window.matchMedia('(min-width: 767px)').matches) {
        $(".fadeIn").fadeIn(1000);
    } else {
        $(".fadeInMobile").fadeIn(1000);
    }
});

$( "#back" ).click(function() {
    setTimeout(function(){location.href="index.php?i=2"} , 1000); 
    $(".fadeIn, .fadeOut, hr").fadeOut(1000); 
});

$( "#back" ).click(function() {
    setTimeout(function(){location.href="index.php?i=2"} , 1000); 
    $(".fadeIn, .fadeOut, hr").fadeOut(1000); 
});

$( "#backMobile" ).click(function() {
    setTimeout(function(){location.href="index.php?i=2"} , 1000); 
    $(".fadeInMobile").fadeOut(1000);
});



// Get different shades color
function adjust(color, amount) {
    return '#' + color.replace(/^#/, '').replace(/../g, color => ('0'+Math.min(255, Math.max(0, parseInt(color, 16) + amount)).toString(16)).substr(-2));
}

changeBackground();

// When input unselected => save in db
$('input[name^="q1"].range').change(function() {
    var q1 = $(this).val();
    var index = $(this).attr('name').match(/q1(.+)/)[1];
    var siid = $('input[name="siid' + index + '"]').val();

    $.ajax({
        type:'POST',
        url:'ajaxFolder/saveQ1.php',
        data: {q1: q1, siid: siid},
        async: false // wait till ready
    });
});

$('input[name^="q2"].input').blur(function() {
    var q2 = $(this).val();
    var index = $(this).attr('name').match(/q2(.+)/)[1];
    var siid = $('input[name="siid' + index + '"]').val();

    $.ajax({
        type:'POST',
        url:'ajaxFolder/saveQ2.php',
        data: {q2: q2, siid: siid},
        async: false // wait till ready
    });
});

$('input[name^="q3"].input').blur(function() {
    var q3 = $(this).val();
    var index = $(this).attr('name').match(/q3(.+)/)[1];
    var siid = $('input[name="siid' + index + '"]').val();

    $.ajax({
        type:'POST',
        url:'ajaxFolder/saveQ3.php',
        data: {q3: q3, siid: siid},
        async: false // wait till ready
    });
});

$('input[name^="q4"].range').blur(function() {
    var q4 = $(this).val();
    var index = $(this).attr('name').match(/q4(.+)/)[1];
    var siid = $('input[name="siid' + index + '"]').val();

    $.ajax({
        type:'POST',
        url:'ajaxFolder/saveQ4.php',
        data: {q4: q4, siid: siid},
        async: false // wait till ready
    });
});

</script>