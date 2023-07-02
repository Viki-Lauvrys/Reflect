<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");
	$user_data = check_login($conn);
    $userID = 'IH786SWX8C76IZU38=';
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

//something was posted
if($_SERVER['REQUEST_METHOD'] == "POST")
{
    //add group
    if (isset($_POST["new_groupname"])) {
        $new_groupname = $_POST['new_groupname'];
        $conn->query("INSERT INTO smartschool_groups_users (groupName, userID, schoolYear) VALUES ('$new_groupname', '$userID', '$schoolYear')");
        
        header('location: admin2.php?i=2');
    }
}

    //List of all users for typing recommendation
    $usernameList = [];

    $result = $conn->query("SELECT first_name, last_name, userID
        FROM smartschool_users
        WHERE first_name != 'admin'
        ORDER BY last_name ASC, first_name ASC");

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $name = htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']);
            array_push($usernameList, $name);
            array_push($usernameList, $row['userID']);
        }
    }

    $result1 = $conn->query("SELECT first_name, last_name, username
        FROM users
        WHERE first_name != 'admin'
        ORDER BY last_name ASC, first_name ASC");

    if ($result1->num_rows > 0) {
        while($row = $result1->fetch_assoc()) {
            $name = htmlspecialchars($row['first_name']) . " " . htmlspecialchars($row['last_name']);
            array_push($usernameList, $name);
            array_push($usernameList, $row['username']);
        }
    }



?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Login</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link rel="stylesheet" href="filters.css">
    <link rel="stylesheet" href="admin2.css">
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
            $query = "SELECT * 
                FROM smartschool_groups_users 
                WHERE groupName != '0. Admins' 
                AND groupName != '2. Leerlingen'
                GROUP BY groupName 
                ORDER BY groupName";
            $stmt = $conn->prepare($query);
            //$stmt->bind_param('s', $_SESSION['userID']);
            $stmt->execute();
            $result = $stmt->get_result();

            ?>
           
            <div id="sides-container">
                <div id="left-side">
                <a id="back" href="#" onclick="goBack()"> &lt; Terug</a>
                    <form>
                        <h1 style='color:white'>Filter</h1>
                        <input id="inputGroupname" class="inputName" placeholder="Groepsnaam" type="text" name="inputGroupname"/>
                        <input id="inputFirstname" class="inputName" placeholder="Voornaam" type="text" name="inputFirstname">
                        <input id="inputLastname" class="inputName" placeholder="Achternaam" type="text" name="inputLastname"> 
                        <select id="thisIsTheChosenYear" name="inputPeriod">
                                <?php 
                                    //Get all schoolYears
                                    $query = $conn->query(
                                        "SELECT DISTINCT schoolYear AS year0,
                                        schoolYear +1 AS year1
                                        FROM smartschool_groups_users
                                        ORDER BY schoolYear DESC");
                                    if ($query->num_rows > 0) {
                                        while($row = $query->fetch_assoc()) {
                                            echo "<option value='" . $row['year0'] . "' >" . $row['year0'] . "-" . $row['year1'] . "</option>";
                                        }
                                    }
                                ?>
                            <option value='all' >Alle jaren</option>
                        </select>
                        <div id="roleContainer">
                            <div class="inputRole">
                                <input id="inputLlk" type="checkbox" name="inputRole1" checked="true"/> Leerkracht
                            </div>
                            <div class="inputRole">
                            <input id="inputLln" type="checkbox" name="inputRole2" checked="true"/> Leerling
                            </div>
                        </div>
                        <div class="inputEmpty">
                            <input id="inputEmpty" type="checkbox" name="inputEmpty" checked="true"/> Lege groepen tonen
                        </div>
                        <br/>
                    </form>
                </div>

                <div id="right-side">
                    <div id="titleWrapper">
                            <h1>Groepen</h3>
                            <img id="addBtn" src="img/add_icon.png">
                        </div>
                        <div id="addGroup">
                            <h3>Groep toevoegen</h3>

                            <form method="POST">
                                <input type="text" name="new_groupname" placeholder="Groepsnaam" class="input1" required/> <br/>
                                <input type="submit" value="Voeg toe"/>
                            </form>
                        </div>

                        <div class="table-wrapper">
                            <table class="fl-table">
                                <thead>
                                    <tr>
                                        <th>Groepsnaam</th>
                                        <th>Voornaam</th>
                                        <th>Achternaam</th>
                                        <th>Rol</th>
                                        <th class="icon"></th>
                                    </tr>
                                </thead>

                                <?php

                                if ($result->num_rows > 0) {
                                    $rowIndex = 0;
                                    while($row = $result->fetch_assoc()) {
                                        $groupName = $row['groupName'];
                                        $groupNameString = "'" . trim($row['groupName']) . "'";
                                        ?>
                                            <tr class=" info periodInfo">
                                                <td class="name"> <?= htmlspecialchars($groupName) ?> </td>
                                                <td class="name"></td>
                                                <td class="name"></td>
                                                <td class="name"></td>
                                                <td class='icon name1' add-id="<?= $row["id"] ?>"><img src="img/add_icon.png"></td>
                                            </tr>
                                            <tr class="addUser">
                                                <td class="top">Gebruiker toevoegen:</td>
                                                <td class="top"><input id="<?= $rowIndex ?>" value="" placeholder="Klik om te zoeken🔎" type="text" data-id="2" class="username_list" placeholder="naam"/><ul class="list"></ul></td>
                                                <td class="top"><button type="button" onclick="addUser(<?= htmlspecialchars($groupNameString) . ',' . $rowIndex ?>)">Voeg toe</button></td>
                                                <td class=""></td>
                                                <td class=""></td>
                                            </tr>

                                            <?php
                                            $result1 = $conn->query("SELECT *, smartschool_groups_users.id AS `uid`
                                                FROM smartschool_groups_users  
                                                JOIN smartschool_users ON smartschool_users.userID = smartschool_groups_users.userID
                                                WHERE groupName = '$groupName'
                                                AND smartschool_groups_users.userID != '$userID'
                                                ORDER BY last_name ASC, first_name ASC");
                                            if ($result1->num_rows > 0) {
                                                while($row1 = $result1->fetch_assoc()) {
                                                    $user_id = $row1['userID'];

                                                    $result2 = $conn->query("SELECT groupName
                                                        FROM smartschool_groups_users  
                                                        WHERE userID = '$user_id'");

                                                    $isLeerling = false;

                                                    if ($result2->num_rows > 0) {
                                                        while ($row2 = $result2->fetch_assoc()) {
                                                            $group_name = $row2['groupName'];

                                                            if ($group_name == "2. Leerlingen") {
                                                                $isLeerling = true;
                                                                break;
                                                            }
                                                        }
                                                    }

                                                    if ($isLeerling) {
                                                        $role = "Leerling";
                                                    } else {
                                                        $role = "Leerkracht";
                                                    }
                                                    ?>
                                                    <tr class="info groupInfo">
                                                        <input type='hidden' class='schoolYear' value='<?= $row['schoolYear'] ?>'/>
                                                        <td></td>
                                                        <td class="firstName"> <?= htmlspecialchars($row1['first_name']) ?> </td>
                                                        <td class="lastName"> <?= htmlspecialchars($row1['last_name']) ?> </td>
                                                        <td class="role"> <?= $role ?> </td>
                                                        <td class='delete icon' delete-id="<?= $row1["uid"] ?>"><img src="img/delete_icon.png"></td>
                                                    </tr>
                                                <?php
                                            }
                                        }

                                        $result1 = $conn->query("SELECT *, smartschool_groups_users.id AS `uid`
                                                FROM smartschool_groups_users
                                                JOIN users ON users.username = smartschool_groups_users.userID
                                                WHERE groupName = '$groupName'
                                                AND smartschool_groups_users.userID != '$userID'
                                                ORDER BY last_name ASC, first_name ASC");
                                            if ($result1->num_rows > 0) {
                                                while($row1 = $result1->fetch_assoc()) {

                                                    if ($row1['rights'] == '2') {
                                                        $role = "Leerling";
                                                    } else {
                                                        $role = "Leerkracht";
                                                    }
                                                    ?>
                                                    <tr class="info groupInfo">
                                                        <input type='hidden' class='schoolYear' value='<?= $row['schoolYear'] ?>'/>
                                                        <td></td>
                                                        <td class="firstName"> <?= htmlspecialchars($row1['first_name']) ?> </td>
                                                        <td class="lastName"> <?= htmlspecialchars($row1['last_name']) ?> </td>
                                                        <td class="role"> <?= $role ?> </td>
                                                        <td class='delete icon' delete-id="<?= $row1["uid"] ?>"><img src="img/delete_icon.png"></td>
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
$("#inputGroupname").on('keyup', function(){
    liveSearch();
})

$("#inputFirstname").on('keyup', function(){
    liveSearch();
})

$("#inputLastname").on('keyup', function(){
    liveSearch();
})

$("#inputLln").change(function () {
    liveSearch();
});

$("#inputLlk").change(function () {
    liveSearch();
});

$("#inputEmpty").change(function () {
    liveSearch();
});

$("#thisIsTheChosenYear").change(function () {
    liveSearch();
});


function liveSearch() {

    var value = $('#inputGroupname').val().toLowerCase();
    var value1 = $('#inputFirstname').val().toLowerCase();
    var value2 = $('#inputLastname').val().toLowerCase();
    var value3 = $('#thisIsTheChosenYear').val();

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
            if (((($(this).find('.role').text().toLowerCase().search("leerling") > -1) && $("#inputLln").is(":checked")) || 
                (($(this).find('.role').text().toLowerCase().search("leerkracht") > -1) && $("#inputLlk").is(":checked"))) && 
                ($(this).find('.firstName').text().toLowerCase().search(value1) > -1) && 
                ($(this).find('.lastName').text().toLowerCase().search(value2) > -1) &&
                (($(this).find('.schoolYear').val().search(value3) > -1) || (value3 === 'all')) &&
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



//DELETE USER
$(".delete").click(function() {
    if (confirm("Bent u zeker dat u deze gebruiker van de groep wilt verwijderen?")) {
        $.ajax({        
            url: 'ajaxFolder/deleteGroup.php',
            type: 'post',             
            data: {'id' : $(this).attr("delete-id")},
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
        $('.name, .name1').css('background-color', adjust(colorrr, 100));
        $('.addUser td').css('background-color', adjust(colorrr, 150));


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