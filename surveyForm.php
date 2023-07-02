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
    $schoolYear = $_SESSION['schoolYear'];

  //something was posted
  if($_SERVER['REQUEST_METHOD'] == "POST") {
    if(isset($_POST["inputName"])) {
        $new_name = addslashes(utf8_encode($_POST['inputName']));
        $period_id = $_POST['inputPeriod'];
        
        //save new survey
        $conn->query("INSERT INTO surveys (`name`, period_id) VALUES ('$new_name', '$period_id') ");

        //get id of submitted survey
        $sid = mysqli_insert_id($conn);


        $iidList = [];

        //Link infos to survey
        $info_ids = $_POST['infoIDs'];
        foreach ($info_ids as $info_id) {
            $conn->query("INSERT INTO survey_info_info (`sid`, info_id) VALUES ('$sid', '$info_id')");
            array_push($iidList, mysqli_insert_id($conn));
        }

        //Link group names to survey
        $groups = $_POST['groups'];
        foreach ($groups as $group) {
            $conn->query("INSERT INTO survey_group (`sid`, group_name) VALUES ('$sid', '$group')");
        }

    }

    //refresh -> no double submitting
    header('location: index.php?i=1');
  }



    //List of all users for typing recommendation
    $list = [];

    //select all groups where teacher is in
    $result = $conn->query("SELECT groupName AS gName
        FROM smartschool_groups_users
        WHERE userID = '$userID'
        AND schoolYear = '$schoolYear'");

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            array_push($list, $row['gName']);
        }
    }

    $usernameList = array_unique($list);

?>
<!DOCTYPE html>
<html lang='nl'>
<head>
    <title>Module Maken</title>
    <link rel="stylesheet" href="surveyForm.css"/>
    <link rel="stylesheet" href="highlight.css"/>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta http-equiv="Content-Type" content="text/html; charset = utf8mb4">
    <script type="text/javascript" src="changeFavicon.js"></script>
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <script src="https://code.jquery.com/jquery-2.1.3.js"></script>
</head>

<body>

<script>
// Get different shades color
function adjust(color, amount) {
    return '#' + color.replace(/^#/, '').replace(/../g, color => ('0'+Math.min(255, Math.max(0, parseInt(color, 16) + amount)).toString(16)).substr(-2));
}

// change colors => has to happen before anything else otherwise weird glitchy feeling
function changeBackground() {
    if (localStorage.getItem('colour')) {
        colorrr = localStorage.getItem('colour');
        y = "linear-gradient(to right, #222 0%, #222 25%, " + colorrr + " 25%, " + colorrr + " 100%)";
        document.body.style.background = y;

        z = document.querySelectorAll('.input');
        z.forEach(z => {
            z.addEventListener("focus", function () {
                this.style.boxShadow = "0 0 1px " + colorrr;
            });
        });

        a = document.querySelectorAll('#voegToe');
        a.forEach(a => {
            a.addEventListener("mouseover", function () {
                this.style.backgroundColor = adjust(colorrr, 0);
                this.style.color = "white";
            });
            a.addEventListener("mouseout", function () {
                this.style.backgroundColor = "white";
                this.style.color = colorrr;
            });
        });
    
        $('.input, .arrow, button, select').css('border-color', adjust(colorrr, -50));
        $('.step').css('background-color', adjust(colorrr, -50));
        $('.tab, input, button, select, #parentBtn').css('color', adjust(colorrr, -50));
        $('.add').css('color', adjust(colorrr, 10));
        $('input:checkbox.add, .deleteTitle').css('color', adjust(colorrr, 10));

        // Change favicon-color
        var faviconPath = 'img/favicons/' + colorrr.slice(1) + '.ico';
        changeFavicon(faviconPath);

        // Change highlight-color
        var selectionStyle = document.createElement('style');
        selectionStyle.textContent = '::selection { background: ' + colorrr + '; }';
        document.head.appendChild(selectionStyle);
    }
}
changeBackground(); 
</script>

    <div id="container">
        <img id="loading" src='img/loading.gif' style="height: 100px; 
            width: 100px; 
            position: absolute;
            top: calc(50vh - 60px);
            left: calc(50vw - 60px);
            z-index: 9999;
            display: none;">
        <div id="left-side">
            <a id="back" class="click" style="display:none;"> &lt; Terug</a>
            <h2 id="overzicht" class="fadeOut" >Overzicht:</h2>
            <div id="typingName" class="item fadeOut">
                <div id="newName"></div>
            </div><hr id="hr1" />

            <div id="chosenCopy" class="item fadeOut">
                <div id="clone"></div>
            </div><hr id="hr2" />

            <div id="groupsListCopy" class="item fadeOut">
                <!--<p id="selectedCopy" class="selected">Groepen:</p>-->
                <ul id='ulCopy'></ul>
            </div><hr id="hr3" />

            <div id="infoPeriodCopy" class="item fadeOut">
                <div id="copy"></div>
            </div>

        </div>
        <div id="right-side">
            <a id="backMobile" class="fadeInMobile" style="display:none;"> &lt; Terug</a>
            <form id="regForm" method="POST">

                <!-- Circles which indicates the steps of the form: -->
                <div id="steps" class="fadeIn fadeInMobile" style="display:none;">
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                    <span class="step"></span>
                </div>

                <div id="form-container" class="fadeIn fadeInMobile" style="display:none;">
                    <!-- One "tab" for each step in the form: -->
                    <div class="tab">
                        <h2>1. Naam:</h2>
                        <br/>
                        <p><input id="inputName" class="input" name="inputName" placeholder="Naam module" required></p>
                    </div>


                    <div class="tab">
                        <h2>2. Competenties + Focusdoelen: </h2>
                        <br/> <br/>

                        <!-- Trigger/Open The Modal -->
                        <button class="broadWidth" type="button" id="myBtn">Competentie toevoegen</button>

                        <!-- The Modal -->
                        <div id="myModal" class="modal">

                            <!-- Modal content -->
                            <div class="modal-content">
                                <span class="close">&times;</span>
                                <b class='extraPadding'>Competentie</b><br/>
                                <!-- Title dropdown -->
                                <select id="title" required>
                                </select>
                                
                                <!-- info dropdown -->
                                <div id="info">
                                </div>
                                
                                <button id='voegToe' type="button" onclick="setChosen()">Voeg toe</button>
                            </div>

                        </div> <br/> <br/>

                        <div id="chosen"></div>
                        <br/>
                    </div> 


                    <div class="tab">
                        <h2>3. Ontvangers:</h2>
                        <br/><br/>
                        <input value="" placeholder="Groep zoeken" type="text" id="username" class="input">
                        <ul class="list" id="username_list"></ul>
                        <div id="groupsList">
                            <p id="selected" class="selected">Geselecteerd:</p>
                            <ul id='ul'></ul>
                        </div> <br/>
                    </div>

                    <div class="tab">
                        <h2>4. Periode:</h2>
                        <br/><br/>
                        <select id="period" name="inputPeriod" class="broadWidth">
                        </select>

                        <!-- info dropdown -->
                        <div id="infoPeriod"> </div> <br/>
                    </div>
                    <div style="overflow:auto;">
                        <div id="buttons">
                            <button type="button" onclick="nextPrev(-1)"><i id="prevBtn" class="arrow left"></i></button>
                            <button id="parentBtn" type="button" onclick="nextPrev(1)"><i id="nextBtn" class="arrow right"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
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
//CHAINED DROPDOWN

function getTitles() {
    // Get all titles
    $('#loading').css('display', 'block');
    $.ajax({
        url: 'ajaxFolder/dataTitles.php',
        success: function(data) {
            $('#loading').css('display', 'none');
            $('#title').html(data);
        }
    });
}

function getInfos() {
    var titleID = $('#title').val();
    if(titleID){
        $('#loading').css('display', 'block');
        $.ajax({
            type:'POST',
            url:'ajaxFolder/data.php',
            data:'title_id=' + titleID,
            success:function(html){
                $('#info').html(html);
                $('#loading').css('display', 'none');
                // Apply js on new elements
                changeBackground();
            }
        }); 

    } else {
        $('#info').html('');
    }
}

function getPeriods() {
    // Get all periods
    $('#loading').css('display', 'block');
    $.ajax({
        url: 'ajaxFolder/dataPeriods.php',
        success: function(data) {
            $('#loading').css('display', 'none');
            $('#period').html(data);
        }
    });
}


function getPeriodInfo() {
    var periodID = $('#period').val();

    if(periodID){
        $('#loading').css('display', 'block');
        $.ajax({
            type:'POST',
            url:'ajaxFolder/getPeriodInfo.php',
            data:'period_id=' + periodID,
            success:function(html){
                $('#loading').css('display', 'none');
                $('#infoPeriod').html(html);

                if ($("#infoPeriod #addPeriod").length == 0) { // If user chose '+ Toevoegen' => don't clone into summary

                    let parent1 = document.getElementById('infoPeriodCopy');
                    let clone = document.getElementById('copy');
                    clone.remove();

                    let chosen = document.querySelector('#infoPeriod');
                    let clonedPeriod = chosen.cloneNode(true);

                    clonedPeriod.id = 'copy';
                    parent1.appendChild(clonedPeriod);
                }
            }
        }); 
    } else {
        $('#info').html('');
    }
}


function deleteInfo(infoID) {
    if(infoID){
        if (confirm('Bent u zeker dat u dit focusdoel wilt verwijderen?')) {
            $('#loading').css('display', 'block');
            $.ajax({
                type:'POST',
                url:'ajaxFolder/deleteInfo.php',
                data:'infoID=' + infoID,
                success:function(response){
                    if (response == 'alert') {
                        alert("U kunt dit focusdoel niet verwijderen, omdat het reeds in gebruik is.");
                    }
                    $('#loading').css('display', 'none');
                    getInfos();
                }
            }); 
        }
    }
}


function deleteTitle(titleID) {
    if(titleID){
        if (confirm('Bent u zeker dat u deze competentie wilt verwijderen?')) {
            $('#loading').css('display', 'block');
            $.ajax({
                type:'POST',
                url:'ajaxFolder/deleteTitle.php',
                data:'titleID=' + titleID,
                success:function(response){
                    $('#loading').css('display', 'none');
                    getTitles();
                    $('#info').html('');
                }
            }); 
        }
    }
}


function deletePeriod(periodID) {
    if(periodID){
        if (confirm('Bent u zeker dat u deze periode wilt verwijderen?')) {
            $('#loading').css('display', 'block');
            $.ajax({
                type:'POST',
                url:'ajaxFolder/deletePeriod.php',
                data:'periodID=' + periodID,
                success:function(response){
                    if (response == 'alert') {
                        alert("U kunt deze periode niet verwijderen, omdat het reeds in gebruik is.");
                    }
                    getPeriods();
                    getPeriodInfo();
                }
            }); 
        }
    }
}


// User can't select first select option => error
$('#title').on('change', function() {
    $(this).find('option:first').prop('disabled', true);
});

$('#period').on('change', function() {
    $(this).find('option:first').prop('disabled', true);
});


$(document).ready(function(){

    getTitles();
    getPeriods();

    $(document).on('change', '#title', function(){
        getInfos();
    });

    $('#period').on('change', function(){
        getPeriodInfo();
    });
});

</script>

<script>

let index = -1;

//ChosenOne and -Two
//Put a loop around this one for multiple id's and stuff


function setChosen() {
  index++;
  //Set Chosen div
  let div = document.getElementById("chosen");
  const node = document.createElement("div");
  node.setAttribute("id", "chosen" + index);
  node.className += "chosen";
  node.addEventListener("click", function () {
    node.remove();
    cloneFunction();
  });
  div.appendChild(node);


  let input = document.getElementById("title").value;
  let out = document.getElementById("chosen" + index);

  //get title from id
  var titleID = input;

  if(titleID){
    $('#loading').css('display', 'block');
    $.ajax({
        type:'POST',
        url:'ajaxFolder/getTitle.php',
        data:'title_id=' + titleID,
        success:function(html){
                $('#loading').css('display', 'none');
                out.innerHTML = html;
        }
    }); 

  } else {
      $('#info').html('');
  }


  //set info
  let input1 = document.getElementsByClassName('info').value;
  let out1 = document.getElementById("chosen" + index);

  //Get all checked boxes
  $('input[name="info"]:checked').each(function() {
    //get title from id
    var infoID = this.value;
    if(infoID){
        $('#loading').css('display', 'block');
        $.ajax({
            type:'POST',
            url:'ajaxFolder/getInfo.php',
            data:'info_id=' + infoID,
            success:function(html){
                $('#loading').css('display', 'none');
                $('#chosen' + index).append("<li class='listClass'>"+ html +"</li>");
                console.log(chosenID);
            }
        }); 
    } else {
        $('#info').html('');
    }
  });

  //close modal
  modal.style.display = "none";

    //timeout 300ms prevents uitvoeren before ajax files are done
    setTimeout(function() {
        let parent = document.getElementById('chosenCopy');
        let clone = document.getElementById('clone');
        clone.remove();

        let chosen = document.querySelector('#chosen');
        let clonedChosen = chosen.cloneNode(true);

        clonedChosen.id = 'clone';
        parent.appendChild(clonedChosen);
        document.getElementById('hr2').style.display = "block";
    }, 300);

}


function remove() {
  this.remove();
}


function cloneFunction() {
    let parent = document.getElementById('chosenCopy');
    let clone = document.getElementById('clone');
    clone.remove();

    let chosen = document.querySelector('#chosen');
    let clonedChosen = chosen.cloneNode(true);

    clonedChosen.id = 'clone';
    parent.appendChild(clonedChosen);
}

// Teacher can add new title
function saveTitle() {
    var titleName = addTitle.value;
    if(titleName){
        $('#loading').css('display', 'block');
        $.ajax({
            type:'POST',
            url:'ajaxFolder/saveTitle.php',
            data:'titleName=' + titleName,
            success:function(html){
                $('#loading').css('display', 'none');
                getTitles();
            }
        }); 
    }
}

// Teacher can add new info
function saveInfo() {
    var titleID = addInfoBtn.value;
    var infoName = addInfo.value;
    if(titleID && infoName){
        $('#loading').css('display', 'block');
        $.ajax({
            type:'POST',
            url:'ajaxFolder/saveInfo.php',
            data: {titleID: titleID, 
                infoName: infoName},
            success:function(html){
                $('#loading').css('display', 'none');
                getInfos();
            }
        }); 
    }
}

// Teacher can add new period
function savePeriod() {

    var periodName = addPeriod.value;
    var date = document.getElementById("date").value;
    var date1 = document.getElementById("date1").value;
    var date2 = document.getElementById("date2").value;
    var date3 = document.getElementById("date3").value;

    if(periodName && date && date1 && date2 && date3) {
        $('#loading').css('display', 'block');
        $.ajax({
            type:'POST',
            url:'ajaxFolder/savePeriod.php',
            data: { periodName: periodName,
                date: date, 
                date1: date1, 
                date2: date2,  
                date3: date3},
            success:function(html){
                getPeriods();

                // Go to newest Period
                setTimeout(function() {
                    var selectElement = document.getElementById("period");
                    var lastOptionIndex = selectElement.options.length - 2;
                    selectElement.selectedIndex = lastOptionIndex;

                    // Create and dispatch a new "change" event
                    var changeEvent = new Event("change");
                    selectElement.dispatchEvent(changeEvent);

                    $('#loading').css('display', 'none');
                }, 500);
            }
        }); 
    }
}


</script>


<!--modal -->

<script>

var modal = document.getElementById("myModal");
var btn = document.getElementById("myBtn");
var span = document.getElementsByClassName("close")[0];

btn.onclick = function() {
  modal.style.display = "block";
}

span.onclick = function() {
  modal.style.display = "none";
}

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
  }
}

</script>


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

</script>



<script>


var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab

function showTab(n) {
  //display the specified tab of the form
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "inline";
  // fix Previous/Next buttons
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline-block";
  }
  if (n == (x.length - 1)) {
    document.getElementById("parentBtn").innerHTML = "Verzend";
  } else {
    document.getElementById("parentBtn").innerHTML = "<i id='nextBtn' class='arrow right'></i>";
  }
  //run a function that displays the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");

  // if you have reached the end of the form and everything filled in :
  if (currentTab >= x.length -1 && n == 1 && document.getElementById("copy").children.length > 0 && document.getElementById("ulCopy").children.length > 0 && document.getElementById("clone").children.length > 0 && document.getElementById("newName").innerHTML) {
    document.getElementById("regForm").submit();
    return;

  } else if (currentTab >= x.length -1 && n == 1) {
    alert ("Verzenden gaat niet, je hebt niet alles ingevuld.");
    return;

  } else {
    x[currentTab].style.display = "none";
    currentTab = currentTab + n;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
  changeBackground();
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

</script>







<script type="text/javascript">
//typing recommendation

let names = <?php echo json_encode($usernameList); ?>;

//Sort names in ascending order
let sortedNames = names.sort();

let input = document.getElementById("username");
//Execute function on keyup
input.addEventListener("keyup", (e) => {
//loop through above array
//Initially remove all elements ( so if user erases a letter or adds new letter then clean previous outputs)
removeElements();
for (let i of sortedNames) {
    //convert input to lowercase and compare with each string
    if (
        i.toLowerCase().indexOf(input.value.toLowerCase()) !== -1 &&
        input.value != ""
    ) {
        let listItem = $("<li></li>");
        // One common class name
        listItem.addClass("list-items");
        listItem.css("cursor", "pointer");
        listItem.attr("onclick", "displayNames('" + i + "')");
        // Display matched part in bold
        let start = i.toLowerCase().indexOf(input.value.toLowerCase());
        let end = start + input.value.length;
        let word = i.substring(0, start) + "<b>" + i.substring(start, end) + "</b>" + i.substring(end);
        // Display value in array
        listItem.html(word);
        $(".list").append(listItem);
    }
    changeListColor();
}

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
    changeListColor();
}

function removeElements() {
    //clear all the items
    let items = document.querySelectorAll(".list-items");
    items.forEach((item) => {
        item.remove();
    });
}

// Change list color to chosen color
function changeListColor() {
    $(".list-items").mouseover(function () {
        $(this).css("background-color", adjust(colorrr, 100));
    });
    $(".list-items").mouseleave(function () {
        $(this).css("background-color", "");
    });
}

</script>






<script>
//TRANSITION
$( document ).ready(function() {
    if (window.matchMedia('(min-width: 767px)').matches) {
        $(".fadeIn, #back").fadeIn(1000);
    } else {
        $(".fadeInMobile").fadeIn(1000);
    }
});

$( "#back" ).click(function() {
    setTimeout(function(){location.href="index.php?i=1"} , 1000); 
    $(".fadeIn, .fadeOut, hr, #back").fadeOut(1000); 
});

$( "#backMobile" ).click(function() {
    setTimeout(function(){location.href="index.php?i=1"} , 1000); 
    $(".fadeInMobile").fadeOut(1000);
});

    changeBackground();


    //show 'add-info-form' on click
    function openAddInfo() {
        var form = document.getElementById("addInfoContainer");
        if (form.style.maxHeight) {
            form.style.maxHeight = null;
        } else {
            form.style.maxHeight = form.scrollHeight + "px";
            setTimeout(function () {
                document.getElementById("addInfoContainer").scrollIntoView({
                    behavior: "smooth"
                });
            }, 500);
        }
    }

</script>

<script>

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