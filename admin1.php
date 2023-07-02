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

    //Get userinfo

if ($_SESSION['rights'] == "9IJssmQfbWA=") {
    $rights = '2'; //leerling
} else if ($_SESSION['rights'] == "KamdM9nGxWA=") {
    $rights = '1'; //leerkracht
} else if ($_SESSION['rights'] == '0. Admins'){
    $rights = '0'; //admin
}

//something was posted
if($_SERVER['REQUEST_METHOD'] == "POST")
{

    //add users tot database (admin function)
    if(isset($_POST["new_firstname"]) && isset($_POST["new_lastname"]) && isset($_POST["new_email"]) && isset($_POST["new_password"]) && isset($_POST["new_rights"])) {
        $new_firstname = $_POST["new_firstname"];
        $new_lastname = $_POST["new_lastname"];
        $new_email = $_POST["new_email"];
        $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $new_rights = $_POST['new_rights'];
        
        //Check if username already exists
        $key = 1;
        while($key == 1){
            $new_username = $_POST['new_firstname'] . mb_substr($_POST['new_lastname'], 0, 1) . rand(1, 9);
            $result = $conn->query("SELECT id FROM users WHERE username = '$new_username'");
            if($result->num_rows > 0) {
                $key = 1;
            } else {
                $key = 0;
            }
        }

        $conn->query("INSERT INTO users (username, first_name, last_name, email, `password`, rights) VALUES ('$new_username', '$new_firstname', '$new_lastname', '$new_email', '$new_password', '$new_rights')");
        header('location: admin1.php');
    }
}


?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Login</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" href="admin1.css">
    <link rel="stylesheet" href="filters.css">

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

        if ($rights == 0) {

            //user = admin

            $result = $conn->query("SELECT * FROM users WHERE rights != '0' ORDER BY username");
            ?>
            
            <div id="sides-container">
                <div id="left-side">
                    <a id="back" href="#" onclick="goBack()"> &lt; Terug</a>
                    <form>
                        <h1 style='color:white'>Filter</h1>
                        <input id="inputUsername" class="inputName" placeholder="Gebruikersnaam" type="text" name="inputUsername"/>
                        <input id="inputFirstname" class="inputName" placeholder="Voornaam" type="text" name="inputTitle">
                        <input id="inputLastname" class="inputName" placeholder="Achternaam" type="text" name="inputInfo">
                        <input id="inputEmail" class="inputName" placeholder="E-mailadres" type="text" name="inputGroup"/>
                        <div id="roleContainer">
                            <div class="inputRole">
                                <input id="inputLlk" type="checkbox" name="inputRole1" checked="true"/> Leerkracht
                            </div>
                            <div class="inputRole">
                            <input id="inputLln" type="checkbox" name="inputRole2" checked="true"/> Leerling
                            </div>
                        </div>
                    </form>
                </div>

                <div id="right-side">
                    <div id="titleWrapper">
                        <h1>Non-Smartschool Gebruikers</h3>
                        <img id="addBtn" src="img/add_icon.png">
                    </div>
                    <div id="addUser">
                        <h3>Gebruiker toevoegen</h3>

                        <form method="POST">
                            <input type="text" name="new_firstname" placeholder="Voornaam" class="input1" required/> <br/>
                            <input type="text" name="new_lastname" placeholder="Achternaam" class="input1" required/> <br/>
                            <input type="email" name="new_email" placeholder="E-mailadres" class="input1" required/> <br/>
                            <input type="password" name="new_password" placeholder="Wachtwoord" class="input1" required/> <br/>

                            <div class="textChoise">
                                Leerkracht: <input type="radio" name="new_rights" value="1" required/>
                            </div>
                            <div class="textChoise">
                                Leerling: <input type="radio" name="new_rights" value="2" required/>
                            </div>
                            <input type="submit" value="Voeg toe"/>
                        </form>
                    </div>
                    <div class="table-wrapper">
                        <table class="fl-table">
                            <thead>
                                <tr>
                                    <th>Gebruikersnaam</th>
                                    <th>Voornaam</th>
                                    <th>Achternaam</th>
                                    <th>E-mailadres</th>
                                    <th>Rol</th>
                                    <th class="icon"></th>
                                    <th class="icon"></th>
                                </tr>
                            </thead>
                            <tbody>

                            <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    if ($row["rights"] == 1) {
                                        $role = "Leerkracht";
                                    } else {
                                        $role = "Leerling";
                                    } ?>

                                <tr class="userInfo">
                                    <td class="username"> <?= htmlspecialchars($row["username"]) ?> </td>
                                    <td class="firstname"> <?= htmlspecialchars($row["first_name"]) ?> </td>
                                    <td class="lastname"> <?= htmlspecialchars($row["last_name"]) ?> </td>
                                    <td class="email"> <?= htmlspecialchars($row["email"]) ?> </td>
                                    <td class="role"> <?= $role ?> </td>
                                    <td class='edit icon' edit-id="<?= $row["id"] ?>"> <img src="img/edit_icon.png"> </td>
                                    <td class='delete icon' delete-id="<?= $row["id"] ?>"><img src="img/delete_icon.png"></td>
                                </tr>
                                

                                <?php
                                }
                            } ?>
                            <tr class=" info periodInfo">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
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


    //show 'add-user-form' on click
    document.getElementById("addBtn").addEventListener("click", function() {
        var form = document.getElementById("addUser");
        if (form.style.maxHeight){
            form.style.maxHeight = null;
        } else {
            form.style.maxHeight = form.scrollHeight + "px";
        }
    });


</script>


<script>


//LIVE SEARCH
$("#inputUsername").on('keyup', function(){
    liveSearch();
})

$("#inputFirstname").on('keyup', function(){
    liveSearch();
})

$("#inputLastname").on('keyup', function(){
    liveSearch();
})

$("#inputEmail").on('keyup', function(){
    liveSearch();
})

$("#inputLln").change(function () {
    liveSearch();
});

$("#inputLlk").change(function () {
    liveSearch();
});

function liveSearch() {


    var value = $('#inputUsername').val().toLowerCase();
    var value1 = $('#inputFirstname').val().toLowerCase();
    var value2 = $('#inputLastname').val().toLowerCase();
    var value3 = $('#inputEmail').val().toLowerCase();


    $(".userInfo").each(function () {
        if (((($(this).find('.role').text().toLowerCase().search("leerling") > -1) && $("#inputLln").is(":checked")) || 
            (($(this).find('.role').text().toLowerCase().search("leerkracht") > -1) && $("#inputLlk").is(":checked"))) && 
            ($(this).find('.username').text().toLowerCase().search(value) > -1) && 
            ($(this).find('.firstname').text().toLowerCase().search(value1) > -1) && 
            ($(this).find('.lastname').text().toLowerCase().search(value2) > -1) && 
            ($(this).find('.email').text().toLowerCase().search(value3) > -1)) 
        {
            $(this).show();
        } else {
            $(this).hide();
        }
    });


}



//DELETE USER
$(".delete").click(function() {
    if (confirm("Bent u zeker dat u deze gebruiker wilt verwijderen?")) {
        $.ajax({        
            url: 'ajaxFolder/deleteUser.php',
            type: 'post',             
            data: {'id' : $(this).attr("delete-id")},
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

        //Change all fields to input
        var value1 = $(this).parent().find('.firstname').text().trim();
        var value2 = $(this).parent().find('.lastname').text().trim();
        var value3 = $(this).parent().find('.email').text().trim();

        $(this).parent().find('.firstname').html("<input type='text' name='firstname' value='" + value1 + "'>");
        $(this).parent().find('.lastname').html("<input type='text' name='lastname' value='" + value2 + "'>");
        $(this).parent().find('.email').html("<input type='text' name='email' value='" + value3 + "'>");

    } else {
        //change btn icon
        $(this).find("img").attr("src", "img/edit_icon.png");

        $.ajax({        
            url: 'ajaxFolder/editUser.php',
            type: 'post',             
            data: { 'firstname' : $('input[name="firstname"]').val(),
                    'lastname' : $('input[name="lastname"]').val(),
                    'email' : $('input[name="email"]').val(),
                    'id' : $(this).attr("edit-id")},
            success: function(html){
                    location.reload();
                }          
        });

    }
});

// ASC/DESC table
$('th').click(function() {
    // Get the table element
    var table = $(this).parents('table').eq(0);
    // Get the rows of the table except the first row (header row)
    var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
    // Store the ascending/descending state for the clicked column
    this.asc = !this.asc;
    // If the state is set to descending, reverse the sorted rows
    if (!this.asc){
        rows = rows.reverse();
    }
    // Append the sorted rows back to the table
    for (var i = 0; i < rows.length; i++){
        table.append(rows[i]);
    }
    // Determine the appropriate icon to display (ascending or descending)
    var icon = this.asc ? 'asc_icon.png' : 'desc_icon.png';
    // Remove any previous icon that might have been added
    $(this).find('span').remove();
    // Add the new icon
    $(this).append('<span><img src="img/' + icon + '"/></span>');
});

// Function to compare the values in two rows of the table
function comparer(index) {
    return function(a, b) {
        // Get the values of the cells in the column being sorted
        var valA = getCellValue(a, index), valB = getCellValue(b, index);
        // If the values are numeric, return the difference
        // If the values are not numeric, return the result of localeCompare()
        return $.isNumeric(valA) && $.isNumeric(valB) ? valA - valB : valA.toString().localeCompare(valB);
    };
}

// Function to get the value of a cell in a table row
function getCellValue(row, index){ 
    return $(row).children('td').eq(index).text(); 
}

function goBack() {
    $("#left-side, #right-side").fadeOut(1000);
    setTimeout(function() {
        window.location.href = "index.php?i=2";
    }, 1000);
}










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

</script>

</body>
</html>