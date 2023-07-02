<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");
	$user_data = check_login($conn);
    $_SESSION['userID'] = 'IH786SWX8C76IZU38=';
    $userID = $_SESSION['userID'];
    $username = 'admin';
    $rights = '0';

    // Get correct schoolYear
    $maand = date('m');
    $jaar = date('Y');

    if ($maand >= 8 && $maand <= 12) {
        $_SESSION[schoolYear] = $jaar;
    } else if ($maand >= 1 && $maand <= 7) {
        $_SESSION[schoolYear] = $jaar - 1;
    }

    $schoolYear = $_SESSION['schoolYear'];


?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Login</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta charset="utf-8">
    <link rel="stylesheet" href="filters.css">
    <link rel="stylesheet" href="admin3.css">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300&display=swap" rel="stylesheet">
</head>

<body>
    <!--Transition-->
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"> </script>

<script>   
$( document ).ready(function() {    
    $("#left-side, #right-side").fadeIn(1000);
})
</script>

    <span>

    <?php

    $result = $conn->query("SELECT * FROM users WHERE username = '$username'");
    //Gets all answers

    if ($result->num_rows > 0) {
        $user_data = mysqli_fetch_assoc($result);





        if ($rights == 0) {
            //user = admin
            // Fetch all titles
            $query = "SELECT *, survey_title.id AS stid 
                FROM survey_title
                WHERE (userID = ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('s', $_SESSION['userID']);
            $stmt->execute();
            $result = $stmt->get_result();

            ?>
           
            <div id="sides-container">
                <div id="left-side">
                    <a id="back" href="#" onclick="goBack()"> &lt; Terug</a>
                    <form>
                        <h1 style='color:white'>Filter</h1>
                        <input id="inputTitle" class="inputName" placeholder="Competentie" type="text" name="inputTitle"/>
                        <input id="inputInfo" class="inputName" placeholder="Focusdoel" type="text" name="inputInfo">
                        
                        <div class="inputEmpty">
                            <input id="inputEmpty" type="checkbox" name="inputEmpty" checked="true"/> Lege competenties tonen
                        </div>
                        <br/>
                    </form>
                </div>

                <div id="right-side">
                    <div id="titleWrapper">
                            <h1>Competenties</h3>
                            <img id="addBtn" src="img/add_icon.png">
                        </div>
                        <div id="addGroup">
                            <h3>Competentie toevoegen</h3>

                            <form method="POST">
                                <input id='addTitle' class='INPUT' name="new_groupname" type="text" placeholder='Naam competentie...' required/>
                                <input type='button' value='Voeg toe' onClick='saveTitle()'/>
                            </form>
                        </div>

                        <div class="table-wrapper">
                            <table class="fl-table">
                                <thead>
                                    <tr>
                                        <th>Competentie</th>
                                        <th>Focusdoel</th>
                                        <th class="icon"></th>
                                        <th class="icon"></th>
                                    </tr>
                                </thead>

                                <?php

                                if ($result->num_rows > 0) {
                                    $rowIndex = 0;
                                    while($row = $result->fetch_assoc()) {
                                        $title = utf8_decode(htmlspecialchars($row['title']));
                                        $titleString = "'" . trim($row['title']) . "'"; //needs aphostrophes!!
                                        ?>
                                            <tr class=" info periodInfo">
                                                <td class="name"> <?= $title ?> </td>
                                                <td class="name1"></td>
                                                <td class='icon name1' add-id="<?= $row["id"] ?>"><img src="img/add_icon.png"></td>
                                                <td class='deleteTitle icon name1' delete-id="<?= $row["id"] ?>"><img src="img/delete_icon.png"></td>
                                            </tr>
                                            <tr class="addUser">
                                                <td class="top">Focusdoel toevoegen:</td>
                                                <td class="top"><input id="<?= $rowIndex ?>" value="" placeholder="Vul naam in" type="text" data-id="2" class="username_list" placeholder="naam"/><ul class="list"></ul></td>
                                                <td class="top"><button type="button" onclick="saveInfo(<?= $row['id'] . ',' . $rowIndex ?>)">Voeg toe</button></td>
                                                <td></td>
                                            </tr>

                                            <?php
                                            $query = "SELECT * FROM survey_info 
                                                WHERE title_id = " . $row['id'] . " AND userID = '" . $_SESSION['userID'] . "'"; 
                                            $result1 = $conn->query($query); 

                                            if ($result1->num_rows > 0) {
                                                while($row1 = $result1->fetch_assoc()) {
                                                    $user_id = $row1['userID'];

                                                    ?>
                                                    <tr class="info groupInfo">
                                                        <input type='hidden' class='schoolYear' value='<?= $row['schoolYear'] ?>'/>
                                                        <td></td>
                                                        <td class="infoText"> <?= utf8_decode(htmlspecialchars($row1['info'])) ?> </td>
                                                        <td class='edit icon' edit-id="<?= $row1["id"] ?>"><img src="img/edit_icon.png"></td>
                                                        <td class='deleteInfo icon' delete-id="<?= $row1["id"] ?>"><img src="img/delete_icon.png"></td>
                                                    </tr>
                                                <?php
                                            }
                                        }

                                        $rowIndex++;
                                    }
                                } ?>
                                <tr class=" info periodInfo">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <?php
        } 
    }

    $conn->close();
    ?>
    </span>



 <!--Transition-->
 <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"> </script>

<script>
$( document ).ready(function() {
    if (window.matchMedia('(min-width: 767px)').matches) {
        var prevPage = '<?php echo $_GET["i"]; ?>';
        if (prevPage == 1) {
            $('.transition-left').animate({
                    'left' : '0'    
                }, 1000);
                $('#transition-right').animate({
                    'left' : '50%'    
                }, 1000);
        } else {
            $('#transition-right').css( { "margin-right" : "75%" } );
            $('.transition-left').css( { "margin-left" : "25%" } );
            
            $('#transition-right').animate({
                'right' : '-100%'    
            }, 950);
            $('.transition-left').animate({
                'left' : '-25%'    
            }, 950);
        }
    }
    
    $("#sides_container, #logout").hide().delay(1000).fadeIn(1000);
})
</script>

<script>

    //IMPORTANT !!! verandert kleur op mobile zodat het altijd bij mn outfit past :)
    if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        var select = document.getElementById("select");
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].text === "Paars") {
                select.options[i].value = "#E4B8FF";
            }
        }
    }


    //show 'add-group-form' on click
    document.getElementById("addBtn").addEventListener("click", function() {
        var form = document.getElementById("addGroup");
        if (form.style.maxHeight){
            form.style.maxHeight = null;
        } else {
            form.style.maxHeight = form.scrollHeight + "px";
        }
    });

    $('.name1').click(function() {
        // Get the next sibling element with class 'addUser'
        var form = $(this).closest('tr').nextAll('.addUser').first();

        if (form.css('display') === 'none') {
            form.css('display', 'contents');
        } else {
            form.css('display', 'none');
        }
    });


//LIVE SEARCH
$("#inputTitle").on('keyup', function(){
    liveSearch();
})

$("#inputInfo").on('keyup', function(){
    liveSearch();
})

$("#inputEmpty").change(function () {
    liveSearch();
});

function liveSearch() {

    var value = $('#inputTitle').val().toLowerCase();
    var value1 = $('#inputInfo').val().toLowerCase();

    var hideGroups = true; 
    var theresAnAnswer = false;
    var groupIsEmpty = false;

    $(".info").each(function () {
        if ($(this).hasClass('periodInfo')) {


            if ($(this).find('.name').text().toLowerCase().search(value) > -1 &&
                (
                    ($(this).nextAll('tr:first').nextAll('tr:first').hasClass('periodInfo') && $("#inputEmpty").is(":checked") === true) ||
                    !$(this).nextAll('tr:first').nextAll('tr:first').hasClass('periodInfo')
                )
            ) {
                $(this).show();
                hideGroups = false;
            } else {
                $(this).hide();
                hideGroups = true;
            }

            if (theresAnAnswer === false && groupIsEmpty === false) {
                $(this).prevAll('.periodInfo:first').hide();
            }
            theresAnAnswer = false;
            if ($(this).nextAll('tr:first').nextAll('tr:first').hasClass('periodInfo') && $("#inputEmpty").is(":checked")) {
                groupIsEmpty = true;
            } else {
                groupIsEmpty = false;
            }

        } else {
            if (($(this).find('.infoText').text().toLowerCase().search(value1) > -1) &&
                hideGroups == false) 
            {
                $(this).show();
                theresAnAnswer = true;
            } else {
                $(this).hide();
            }
        }
    });



}


// Add new title
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
                location.reload();
            }
        }); 
    }
}

// Add new info
function saveInfo(titleID, inputIndex) {

    var infoName = $("#" + inputIndex ).val();

    if(titleID && infoName){
        $('#loading').css('display', 'block');
        $.ajax({
            type:'POST',
            url:'ajaxFolder/saveInfo.php',
            data: {titleID: titleID, 
                infoName: infoName},
            success:function(html){
                $('#loading').css('display', 'none');
                location.reload();
            }
        }); 
    }
}

//DELETE TITLE
$(".deleteTitle").click(function() {
    if (confirm("Bent u zeker dat u dit focusdoel wilt verwijderen?")) {
        $.ajax({        
            url: 'ajaxFolder/deleteTitle.php',
            type: 'post',             
            data: {'titleID' : $(this).attr("delete-id")},
            success: function(html){
                    location.reload();
                }          
        });
    
}
});

//DELETE INFO
$(".deleteInfo").click(function() {
    if (confirm("Bent u zeker dat u deze competentie wilt verwijderen?")) {
        $.ajax({        
            url: 'ajaxFolder/deleteInfo.php',
            type: 'post',             
            data: {'infoID' : $(this).attr("delete-id")},
            success: function(html){
                    location.reload();
                }          
        });
    
    }
});


//EDIT BUTTON
$(".edit").click(function() {

//Check if edit/save btn
if ($(this).find("img").attr("src") == "img/edit_icon.png") {

    //change btn icon
    $(this).find("img").attr("src", "img/save_icon.png");

    //Change all field to input
    var value = $(this).parent().find('.infoText').text().trim();

    $(this).parent().find('.infoText').html("<input type='text' name='infoText' value='" + value + "'>");

} else {
    //change btn icon
    $(this).find("img").attr("src", "img/edit_icon.png");

    $.ajax({        
        url: 'ajaxFolder/editInfo.php',
        type: 'post',             
        data: { 'infoText' : $('input[name="infoText"]').val(),
                'id' : $(this).attr("edit-id")},
        success: function(html){
                location.reload();
            }          
    });

}
});

// Get different shades color
function adjust(color, amount) {
    return '#' + color.replace(/^#/, '').replace(/../g, color => ('0'+Math.min(255, Math.max(0, parseInt(color, 16) + amount)).toString(16)).substr(-2));
}

colorrr = localStorage.getItem('colour');
adjust(colorrr, -20);



function changeBackground() {
    if (localStorage.getItem('colour')) {
        y = "linear-gradient(to right, #222 0%, #222 25%, " + localStorage.getItem('colour') + " 25%, " + localStorage.getItem('colour') + " 100%)";
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

        z = document.querySelectorAll('.input1');
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

        $('th').css('background-color', adjust(colorrr, -100));
        $('.name, .name1').css('background-color', adjust(colorrr, 150));
        $('.addUser td').css('background-color', adjust(colorrr, 100));


        // jquery not working for pseudo-elements (slider webkit)
        for(var j = 0; j < document.styleSheets[1].rules.length; j++) {
            var rule = document.styleSheets[1].rules[j];
            if(rule.cssText.match("webkit-slider-thumb")) {
                rule.style.boxShadow= "-500px 0 0 500px " + adjust(colorrr, -15);
            }
        } 
    }
}

changeBackground();




let sortedNames = <?php echo json_encode($usernameList); ?>;

const inputs = document.getElementsByClassName('username_list');

for (let i = 0; i < inputs.length; i++) {
  const input = inputs[i];
  input.addEventListener("keyup", (e) => {
    removeElements();
    let filteredNames = []; // New array to store filtered names
    for (let j = 0; j < sortedNames.length; j+= 2) {
      let name = sortedNames[j];
      let id = sortedNames[j +1];
      if (
        name.toLowerCase().indexOf(input.value.toLowerCase()) !== -1 &&
        input.value !== ""
      ) {
        filteredNames.push(name); // Store filtered names in the array
        let listItem = $("<li></li>");
        listItem.addClass("list-items");
        listItem.css("cursor", "pointer");
        listItem.on("click", function() {
            displayNames(i, j, id, sortedNames[j]);
        });

        let start = name.toLowerCase().indexOf(input.value.toLowerCase());
        let end = start + input.value.length;
        let word =
          name.substring(0, start) +
          "<b>" +
          name.substring(start, end) +
          "</b>" +
          name.substring(end);
        listItem.html(word);
        $(".list").append(listItem);
      }
      changeListColor();
    }
    return;
  });
}

function displayNames(index, index1, userID, username) {
  const input = document.getElementsByClassName('username_list')[index];
  if (input) {
    const listItemValue = username;
    console.log(userID);
    $('.username_list').attr('data-id', userID); 
    input.value = listItemValue;
  }
  removeElements();
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
        $(this).css("background-color", adjust(colorrr, 60));
    });
    $(".list-items").mouseleave(function () {
        $(this).css("background-color", "");
    });
}

function addUser(groupName, inputIndex) {

    console.log(inputIndex);
    var userID = $("#" + inputIndex ).data('id');
    console.log(userID);
    $.ajax({        
        url: 'ajaxFolder/addUserToGroup.php',
        type: 'post',             
        data: {
            'groupName' : groupName,
            'userID' : userID
        },
        success: function(html){
           location.reload();
        }, 
        error: function(html) {
            console.log('help');
        }          
    });
}

function goBack() {
    $("#left-side, #right-side").fadeOut(1000);
    setTimeout(function() {
        window.location.href = "index.php?i=2";
    }, 1000);
}


</script>

</body>
</html>