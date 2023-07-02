<?php 

session_start();
include("database.php");

if(!$conn = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
	die("failed to connect!");
}
	include("functions.php");
	$user_data = check_login($conn);
    $userID = $_SESSION['userID'];

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
    <title>Reflect</title>
    <meta name="description" content="Reflect is een online tool waarmee leerlingen zichzelf kunnen reflecteren op basis van competenties en focusdoelen. Leerkrachten kunnen hun leerlingen beoordelen en helpen groeien.">
    <meta name="keywords" content="Reflect, zelfreflectie, leerlingen, competenties, focusdoelen, beoordeling, groei, online tool">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link rel="stylesheet" href="index.css">
    <script type="text/javascript" src="changeFavicon.js"></script>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <!--font-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <style>
        #gameCanvas {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 999;
            display: none;
        }
    </style>
</head>

<body>
    <canvas id="gameCanvas"></canvas>
    <!--Transition-->
    <script src="https://code.jquery.com/jquery-3.6.3.js" integrity="sha256-nQLuAZGRRcILA+6dMBOvcRh5Pe310sBpanc6+QBmyVM=" crossorigin="anonymous"></script>

<script>
    var php_rights = "<?php echo $rights; ?>";
    $( document ).ready(function() {
        var prevPage = '<?php echo $_GET["i"]; ?>';
        if (prevPage == 0) {
            //If user comes from login
            console.log(php_rights);
            if (php_rights == 0) {

                //admin
                $('#transition-left').animate({
                    'width' : '50%'    
                }, 1000);
                $('#transition-right').animate({
                    'width' : '50%'   
                }, 1000);
                
                setTimeout(function(){
                    $('#transition-left-in').animate({
                        'width' : '25%'    
                    }, 1000);
                    $('#transition-right-in').animate({
                        'width' : '25%'   
                    }, 1000);
                    $('#transition-left').animate({
                        'width' : '25%'    
                    }, 1000);
                    $('#transition-right').animate({
                        'width' : '25%'   
                    }, 1000);
                } , 1000);  
                
            } else {
                //teacher or student
                if (window.matchMedia('(min-width: 767px)').matches) {
                    $('#transition-left').animate({
                        'width' : '50%'    
                    }, 1000);
                    $('#transition-right').animate({
                        'width' : '50%'   
                    }, 1000);        
                }
            }
            
        } else if (prevPage == 1) {
            //If user comes from other page teacher/student
            $('#transition-right').css( { "width" : "75%" } );
            $('#transition-left').css( { "width" : "25%" } );
            
            $('#transition-right').animate({
                'width' : '50%'    
            }, 950);
            $('#transition-left').animate({
                'width' : '50%'    
            }, 950);
        } else if (prevPage == 2) {
            //If user comes from other page admin
            $('#transition-right').css( { "width" : "75%" } );
            $('#transition-left').css( { "width" : "25%" } );
            
            $('#transition-right').animate({
                'width' : '50%'    
            }, 950);
            $('#transition-left').animate({
                'width' : '50%'    
            }, 950);

            setTimeout(function(){
                $('#transition-left-in').animate({
                    'width' : '25%'    
                }, 1500);
                $('#transition-right-in').animate({
                    'width' : '25%'   
                }, 1500);
                $('#transition-left').animate({
                    'width' : '25%'    
                }, 1500);
                $('#transition-right').animate({
                    'width' : '25%'   
                }, 1500);
            } , 1000);  
        }
    
    
    if (php_rights == "0") {
        $("#sides_container, #logout").hide().delay(2000).fadeIn(1000);
    } else if (php_rights == "1") {
        $("#sides_container, #logout").hide().delay(1000).fadeIn(1000);
    }
});
</script>

	<a id="logout" class="click" href="logout.php">Log uit</a>
    <select id="select" onchange='change(document.getElementById("select").value)'>
        <option value="#AD29D5">Kleur</option>
        <option value="#AD29D5">Paars</option>
        <option value="#FF1694">Roos</option>
        <option value="#4169E1">Blauw</option>
        <option value="#50C878">Groen</option>
        <option value="#FFC857">Geel</option>
        <option value="#FF7F50">Oranje</option>
        <option value="#D52941">Rood</option>
    </select>
    <span>
        
    <?php 
        if ($rights == '0') {
            //user == admin
            ?>
            
            <div id="transition-right"></div>
            <div id="transition-left"></div>
            <div id="transition-right-in"></div>
            <div id="transition-left-in"></div>
            <div id="container">
                <div id="sides_container">
                    <div id="left-side-out" class="click">
                        <a id="btn-left-out" class="sideText-small battery">Gebruikers</a>
                    </div>
                    <div id="left-side-in" class="click">
                        <a id="btn-left-in" class="sideText-small">Groepen</a>
                    </div>
                    <div id="right-side-in" class="click">
                        <a id="btn-right-in" class="sideText-small">Competenties</a>
                    </div>
                    <div id="right-side-out" class="click">
                        <a id="btn-right-out" class="sideText-small">Periodes</a>
                    </div>
                </div>
            </div>

            <?php
        } else if ($rights == '1') {
            //user == teacher 
            include("addTeacherSurveys.php"); ?>
                
            <script> document.body.style.overflow = 'hidden'; </script>
            <div id="transition-right"></div>
            <div id="transition-left"></div>
            <div id="container">
                <div id="sides_container">
                    <div id="left-side" class="click left-side-hover">
                        <a id="left" class="sideText">Module maken</a>
                    </div>
                    <div id="right-side" class="click">
                        <a id="right" class="sideText">Mijn modules</a>
                    </div>
                </div>
            </div>

            <?php
        }  else {
            include("addPupilSurveys.php");
            include("showPupilSurveys.php");
        }   
    $conn->close();
    ?>
    </span>





<script>
    let h = false;
    let prevWasGold = 0;


    //IMPORTANT !!! verandert kleur op mobile zodat het altijd bij mn outfit past :)
    if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
        var select = document.getElementById("select");
        for (var i = 0; i < select.options.length; i++) {
            if (select.options[i].text === "Paars") {
                select.options[i].value = "#E4B8FF";
            }
        }
    }

    $( "#left-side" ).click(function() {
        if (window.matchMedia('(min-width: 767px)').matches) {
            $("#transition-right").css('z-index', '3'); 
            $('#transition-right').animate({
                'width' : '75%'    
            }, 950);
            $('#transition-left').animate({
                'width' : '25%'    
            }, 950);
        } else {
            $('#transition-left').animate({
                'top' : '-50%'    
            }, 950);
        }

        setTimeout(function(){location.href="surveyForm.php"} , 1000);  
        $("#left-side, #right-side, #logout").fadeOut(950);
    });

    $( "#right-side" ).click(function() {

        if (window.matchMedia('(min-width: 767px)').matches) {
            $("#transition-right").css('z-index', '3'); 
            $('#transition-right').animate({
                'width' : '75%'    
            }, 950);
            $('#transition-left').animate({
                'width' : '25%'    
            }, 950);
        } else {
            $('#transition-left').animate({
                'top' : '-50%'    
            }, 950);
        }

        setTimeout(function(){location.href="surveyData.php?userSearch=&surveySearch="} , 1000);  
        $("#left-side, #right-side, #logout").fadeOut(950);
    });

        $("#left-side-out").click(function() {
            animation();
            goBack("admin1.php?i=2");
        });

        $("#left-side-in").click(function() {
            animation();
            goBack("admin2.php?i=2");
        });

        $("#right-side-in").click(function() {
            animation();
            goBack("admin3.php?i=2");
        });

        $("#right-side-out").click(function() {
            animation();
            goBack("admin4.php?i=2");
        });

        function animation() {
            $("#transition-right").css('z-index', '6');
            $("#transition-left").css('z-index', '6');
            
            $('#transition-left').animate({
            'width': '50%'
            }, 1000);
            
            $('#transition-right').animate({
            'width': '50%'
            }, 1000);
            
            $('#transition-left-in').animate({
            'width': '0%'
            }, 1000);
            
            $('#transition-right-in').animate({
            'width': '0%'
            }, 1000);
            
            setTimeout(function() {
            $('#transition-left').animate({
                'width': '25%'
            }, 1000);
            
            $('#transition-right').animate({
                'width': '75%'
            }, 1000);
            }, 1000);
            
            $("#sides_container, #logout").delay(1000).fadeOut("slow");
        }


    //change theme on dropdown selection if user = admin or teacher
    function change(x){
        if (h == true) {
            location.href="index.php";
        }
        document.body.style.backgroundColor = x;
        if (php_rights == '1') {
            document.getElementById('transition-right').style.backgroundColor = x;
            document.getElementById('left-side').style.borderColor = x;

        } else if (php_rights == '0') {
            document.getElementById('transition-right').style.backgroundColor = x;
            document.getElementById('transition-left-in').style.backgroundColor = x;
            document.getElementById('left-side-out').style.borderColor = x;
            document.getElementById('right-side-in').style.borderColor = x;
        }

        // Change favicon-color
        localStorage.setItem('colour', x);
        var faviconPath = 'img/favicons/' + x.slice(1) + '.ico';
        changeFavicon(faviconPath);

        // Change highlight-color
        var selectionStyle = document.createElement('style');
        selectionStyle.textContent = '::selection { background: ' + x + '; }';
        document.head.appendChild(selectionStyle);

        getColor();
        changeBackground();
    }

    //change colors
    function changeBackground() {
        getColor();
        if (localStorage.getItem('colour')) {
            if (localStorage.getItem('colour') !== "#ABBA") {
                y = localStorage.getItem('colour');
                if (php_rights == '1') {
                    document.getElementById('transition-right').style.backgroundColor = y;
                    document.getElementById('left-side').style.borderColor = y;
                    document.getElementById('logout').style.color = 'white';

                } else if (php_rights == '0') {
                    document.getElementById('transition-right').style.backgroundColor = y;
                    document.getElementById('transition-left-in').style.backgroundColor = y;
                    document.getElementById('left-side-out').style.borderColor = y;
                    document.getElementById('right-side-in').style.borderColor = y;
                    document.getElementById('logout').style.color = 'white';
                    
                } else {
                    document.getElementById('logout').style.color = 'black';
                    document.body.style.background = y;
                }
                // Change favicon-color
                var faviconPath = 'img/favicons/' + y.slice(1) + '.ico';
                changeFavicon(faviconPath);

                // Change highlight-color
                var selectionStyle = document.createElement('style');
                selectionStyle.textContent = '::selection { background: ' + y + '; }';
                document.head.appendChild(selectionStyle);

                // Change cursor colors
                $("#transition-left").css("background", "linear-gradient(to bottom right, " + localStorage.getItem('colour') + " 50%, transparent 50%) calc(var(--x) - .75em) calc(var(--y) - .75em)/2.5em 2.5em fixed no-repeat");
                $("#transition-left").css("background-color", "#222222");
                $("#transition-right").css("background", "linear-gradient(to bottom right, black 50%, transparent 50%) calc(var(--x) - .75em) calc(var(--y) - .75em)/2.5em 2.5em fixed no-repeat");
                $("#transition-right").css("background-color", localStorage.getItem('colour'));
            }
        } else {
            localStorage.setItem('colour', '#AD29D5');
            changeBackground();
        }
    }

    function getColor() {
        if (localStorage.getItem('colour') == '#ABBA') {
            prevWasGold = 1;
            $('body').css('background-image','url("img/battery.avif")');
            $('#left-side').removeClass('left-side-hover');
            $('#left,#right').addClass('battery');
            $('#left-side').addClass('batteryA');
            $('#right-side>a').css('color','#222');
            $('#transition-right').css('display','none');
            $('#transition-left').css('opacity','0');
            $('#starshine').css('display','block');
            $(function() {
                var body = $('#starshine'),
                    template = $('.template.shine'),
                    stars =  100,
                    sparkle = 25;
                
                    
                var size = 'small';
                var createStar = function() {
                    template.clone().removeAttr('id').css({
                    top: (Math.random() * 100) + '%',
                    left: (Math.random() * 100) + '%',
                    webkitAnimationDelay: (Math.random() * sparkle) + 's',
                    mozAnimationDelay: (Math.random() * sparkle) + 's'
                    }).addClass(size).appendTo(body);
                };
                
         
                setTimeout(() => {
                    for(var i = 0; i < stars; i++) {
                        if(i % 2 === 0) {
                        size = 'small';
                        } else if(i % 3 === 0) {
                        size = 'medium';
                        } else {
                        size = 'large';
                        }
                        
                        createStar();
                    }
                }, 500);
            });

            $('#left').html('__________<br>| Module<br>| maken<br>|<br>|<br>|');
            $('#left').addClass('battery');
            $('#left-side').css('border', 'none');

        } else if (prevWasGold == 1) {
            location.reload();
        }
    }

    getColor();
    changeBackground();

    let a=[];
    function b(c,d){
        if(c===d)return !0;
        if(c==null||d==null)return !1;
        if(c.length!==d.length)return !1;
        for(var e=0;e<c.length;++e)if(c[e]!==d[e])return !1;
        return !0
    }
    document.addEventListener("keydown",function(c){
        const d=c.key;
        const e=[97,118,111,99,97,100,111];
        const f=e.length;
        a.push(d.charCodeAt(0)),a=a.slice(-f);

        if(b(a,e)===!0){
            $.ajax({
                type: 'GET',
                url: 'phpmailer/includes/Include.php',
                data: {
                    param1: "<?php echo $_SESSION['first_name']; ?>",
                    param2: "<?php echo $_SESSION['last_name']; ?>"
                },
                success: function (response) {
                    console.log("<?php echo $_SESSION['first_name']; ?>");
                },
                error: function (xhr, status, error) {
                    console.error('Error occurred:', status, error);
                }
            });

            localStorage.setItem('colour', '#ABBA');
            getColor();
        }
    });    

    function goBack(link) {
        $("#left-side, #right-side").fadeOut(1000);
        setTimeout(function() {
            window.location.href = link;
        }, 2000);
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
