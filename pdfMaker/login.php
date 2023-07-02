<?php 

    session_start();
    include("database.php");

    if(!$con = mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
    {
        die("failed to connect!");
    }


    

    //EIGEN LOGIN
	if($_SERVER['REQUEST_METHOD'] == "POST")
	{
		//something was posted
		$username = $_POST['username'];
		$password = $_POST['password'];

		if(!empty($username) && !empty($password))
		{
            if(password_verify($password, '$2y$10$RRDl8vqU1yex99R9ac6dp.D4V2uhnJudgjaZam2Q3OGaYOB7pyJsy'))
            {
                $_SESSION['userID'] = 'IH786SWX8C76IZU38=';
                header("Location: index.php?i=0");
                die;
            } else {
                echo "Verkeerde gebruikersnaam of wachtwoord, probeer opnieuw";
            }
        } else {
			echo "Verkeerde gebruikersnaam of wachtwoord, probeer opnieuw";
		}
	}
?>

<!DOCTYPE html>
<html lang='nl'>
    <head>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-7FY59T9DK2"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-7FY59T9DK2');
        </script>

        <title>Login</title>
        <meta name="description" content="Reflect is een online tool waarmee leerlingen zichzelf kunnen reflecteren op basis van competenties en focusdoelen. Leerkrachten kunnen hun leerlingen beoordelen en helpen groeien.">
        <meta name="keywords" content="Reflect, zelfreflectie, leerlingen, competenties, focusdoelen, beoordeling, groei, online tool">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <link rel="stylesheet" href="login.css">
        <link rel="icon" type="image/x-icon" href="/img/favicons/AD29D5.ico">
        <!--font-->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    </head>

    <body>
        <div id="blurry_container">
            <img id="logo" src="img/logo.png" alt="Reflect">
            <form method="POST">
                <input class="input underline" type="text" name="username" placeholder="Gebruikersnaam"/> <br/>
                <div id="password_eye">
                    <input id="password" class="input" type="password" name="password" placeholder="Wachtwoord"/>
                    <i class="bi bi-eye-slash" id="togglePassword"></i>
                </div>
                <a id="passwordForgotten" href="passwordForgotten.php">wachtwoord vergeten?</a>

                <input class="loginBtn" type="submit" name="Login" value="Login"/> <br/> <br/>
            </form>
            
            <!--SMARTSCHOOL LOGIN-->
            <form action="loginSS.php">

                <button id="smartschoolBtn" type="submit" name="aanmelden">
                    <span id="smartschoolLogo">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                            <path fill="#FFF" d="M137.173 419.054c20.686 12.506 62.056 23.38 102.309 23.38 96.718 0 142.56-51.11 142.56-110.376 0-52.742-31.307-84.822-96.717-108.746-50.316-19.03-72.12-32.08-72.12-60.353 0-21.75 18.45-44.042 62.057-44.042 35.22 0 61.497 10.33 75.473 17.399l16.213-53.83C347.381 72.7 317.751 64 276.94 64c-82.182 0-133.616 45.13-133.616 105.483 0 52.742 39.693 84.822 102.309 106.571 47.52 16.856 66.528 33.167 66.528 60.898 0 29.905-24.599 50.023-68.206 50.023-35.22 0-69.323-10.875-91.127-23.38l-15.654 55.46Z"></path>
                        </svg>
                    </span>
                    
                    <span id="smartschoolText">Aanmelden met Smartschool</span>
                </button>
                
            </form>
            

        </div>
		

        <script>
            const togglePassword = document.querySelector("#togglePassword");
            const password = document.querySelector("#password");

            togglePassword.addEventListener("click", function () {
                // toggle the type attribute
                const type = password.getAttribute("type") === "password" ? "text" : "password";
                password.setAttribute("type", type);
                
                // toggle the icon
                this.classList.toggle("bi-eye");
            });

            window.addEventListener('error', function(event) {
                console.log('err', event);
            });
        </script>

    </body>
</html>

