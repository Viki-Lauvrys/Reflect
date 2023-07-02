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
} else {
    $rights = '0'; //admin
    $_SESSION['userID'] = "IH786SWX8C76IZU38=";
    $userID = $_SESSION['userID'];
}


//something was posted

if($_SERVER['REQUEST_METHOD'] == "POST") {
    //add period to database (admin function)
    $new_period = $_POST["name"];
    $start_date1 = date('Y-m-d', strtotime($_POST["start_date1"]));
    $end_date1 = date('Y-m-d', strtotime($_POST["end_date1"]));
    $start_date2 = date('Y-m-d', strtotime($_POST["start_date2"]));
    $end_date2 = date('Y-m-d', strtotime($_POST["end_date2"]));

    // Get correct schoolYear
    $maand = date('m', strtotime($end_date1));
    $jaar = date('Y', strtotime($end_date1));

    if ($maand >= 8 && $maand <= 12) {
        $schoolYear = $jaar;
    } else if ($maand >= 1 && $maand <= 7) {
        $schoolYear = $jaar - 1;
    }

    $conn->query("INSERT INTO survey_period (`name`, start_date1, end_date1, start_date2, end_date2, userID, schoolYear) VALUES ('$new_period', '$start_date1', '$end_date1', '$start_date2', '$end_date2', '$userID', '$schoolYear')");
    header('location: admin4.php?i=2');
}

?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Periodes</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <link rel="stylesheet" href="admin4.css">
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


        
            $query = "SELECT * 
                FROM survey_period
                WHERE userID = ?";
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
                        <input id="inputName" placeholder="Naam" type="text" name="inputUsername" />
                        <select id="thisIsTheChosenYear" name="inputPeriod">
                            <option value='all' >Alle jaren</option>
                            <?php 
                                //Get all schoolYears
                                $query = $conn->query(
                                    "SELECT DISTINCT schoolYear AS year0,
                                    schoolYear +1 AS year1
                                    FROM survey_period
                                    ORDER BY schoolYear DESC");
                                if ($query->num_rows > 0) {
                                    while($row = $query->fetch_assoc()) {
                                        echo "<option value='" . $row['year0'] . "' >" . $row['year0'] . "-" . $row['year1'] . "</option>";
                                    }
                                }
                            ?>
                        </select>
                    </form>
                </div>

                <div id="right-side">
                    <div id="titleWrapper">
                        <h1>Periodes</h3>
                            <img id="addBtn" src="img/add_icon.png">
                    </div>
                    <div id="addPeriod">
                        <h3>Periode toevoegen</h3>

                        <form method="POST">
                            Naam Periode: <br/>
                            <input type="text" name="name" placeholder="Naam" class="input1" required /><br />
                            <b>Eerste evaluatie:</b><br/>
                            <div class='dateWrapper'>
                                <div>
                                    startdatum:<br/>
                                    <input type="date" name="start_date1" class="input1" required />
                                </div>
                                <div>    
                                    einddatum:<br/>
                                    <input type="date" name="end_date1" placeholder="Eind datum 1" class="input1" required /><br />
                                </div>
                            </div>
                            <b>Tweede evaluatie:</b><br/>
                            <div class='dateWrapper'>
                                <div>
                                    startdatum:<br />
                                    <input type="date" name="start_date2" placeholder="Start datum 2" class="input1"
                                        required />
                                </div>
                                <div>
                                    einddatum:</br />
                                    <input type="date" name="end_date2" placeholder="Eind datum 2" class="input1"
                                        required /> <br />
                                </div>
                            </div>
                            <input type="submit" value="Voeg toe" />
                        </form>
                    </div>

                    <div class="table-wrapper">
                        <table class="fl-table">
                            <thead>
                                <tr>
                                    <th>Naam</th>
                                    <th>Startdatum 1</th>
                                    <th>Einddatum 1</th>
                                    <th>Startdatum 2</th>
                                    <th>Einddatum 2</th>
                                    <th class="icon"></th>
                                    <th class="icon"></th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                            if ($result->num_rows > 0) {
                                while($row = $result->fetch_assoc()) {
                                    
                                ?>

                                <tr class="periodInfo">
                                    <input type='hidden' class='schoolYear' value='<?= $row['schoolYear'] ?>'/>
                                    <td class="name"> <?= utf8_decode($row["name"]) ?> </td>
                                    <td class="start_date1"> <?= utf8_decode(date("d-m-Y", strtotime($row["start_date1"]))) ?> </td>
                                    <td class="end_date1"> <?= utf8_decode(date("d-m-Y", strtotime($row["end_date1"]))) ?> </td>
                                    <td class="start_date2"> <?= utf8_decode(date("d-m-Y", strtotime($row["start_date2"]))) ?> </td>
                                    <td class="end_date2"> <?= utf8_decode(date("d-m-Y", strtotime($row["end_date2"]))) ?> </td>
                                    <td class='edit icon' edit-id="<?= $row["id"] ?>"> <img src="img/edit_icon.png">
                                    </td>
                                    <td class='delete icon' delete-id="<?= $row["id"] ?>"><img
                                            src="img/delete_icon.png"></td>
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


    //show 'add-period-form' on click
    document.getElementById("addBtn").addEventListener("click", function() {
        var form = document.getElementById("addPeriod");
        if (form.style.maxHeight){
            form.style.maxHeight = null;
        } else {
            form.style.maxHeight = form.scrollHeight + "px";
        }
    });


</script>


<script>


//LIVE SEARCH
$("#inputName").on('keyup', function(){
    liveSearch();
});

$("#thisIsTheChosenYear").change(function () {
    liveSearch();
});


function liveSearch() {

    var value = $('#inputName').val().toLowerCase();
    var value1 = $('#thisIsTheChosenYear').val();

    $(".periodInfo").each(function () {
        if (
            ($(this).find('.name').text().toLowerCase().search(value) > -1) &&
            (
                ($(this).find('.schoolYear').val().search(value1) > -1) ||
                (value1 === 'all')
            )
           )
        {
            $(this).show();
        } else {
            $(this).hide();
        }
    });


}



//DELETE PERIOD
$(".delete").click(function () {
    if (confirm("Bent u zeker dat u deze periode wilt verwijderen?")) {
        $.ajax({
            url: 'ajaxFolder/deletePeriod.php',
            type: 'post',
            data: {
                'periodID': $(this).attr("delete-id")
            },
            success:function(response){
                if (response == 'alert') {
                    alert("U kunt deze periode niet verwijderen, omdat deze reeds in gebruik is.");
                } else {
                    location.reload();
                }
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
        var value1 = $(this).parent().find('.name').text().trim();
        var value2 = $(this).parent().find('.start_date1').text().trim();
        var value3 = $(this).parent().find('.end_date1').text().trim();
        var value4 = $(this).parent().find('.start_date2').text().trim();
        var value5 = $(this).parent().find('.end_date2').text().trim();

        [day, month, year] = value2.split('-');
        value2 = `${year}-${month}-${day}`;
        [day, month, year] = value3.split('-');
        value3 = `${year}-${month}-${day}`;
        [day, month, year] = value4.split('-');
        value4 = `${year}-${month}-${day}`;
        var [day, month, year] = value5.split('-');
        value5 = `${year}-${month}-${day}`;

        $(this).parent().find('.name').html(`<input type='text' name='period_name' value='${value1}'>`);
        $(this).parent().find('.start_date1').html(`<input type='date' id='start_date1' name='start_date1' value='${value2}'>`);
        $(this).parent().find('.end_date1').html(`<input type='date' id='end_date1' name='end_date1' value='${value3}'>`);
        $(this).parent().find('.start_date2').html(`<input type='date' id='start_date2' name='start_date2' value='${value4}'>`);
        $(this).parent().find('.end_date2').html(`<input type='date'id='end_date2' name='end_date2' value='${value5}'>`);

    } else {
        //change btn icon
        $(this).find("img").attr("src", "img/edit_icon.png");

        $.ajax({        
            url: 'ajaxFolder/editPeriod.php',
            type: 'post',             
            data: { 'id' : $(this).attr("edit-id"),
                    'name' : $('input[name="period_name"]').val(),
                    'start_date1' : $('#start_date1').val(),
                    'end_date1' : $('#end_date1').val(),
                    'start_date2' : $('#start_date2').val(),
                    'end_date2' : $('#end_date2').val(),
                },
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

function goBack() {
    $("#left-side, #right-side").fadeOut(1000);
    setTimeout(function() {
        window.location.href = "index.php?i=2";
    }, 1000);
}

</script>



</body>

</html>