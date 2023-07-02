<!DOCTYPE html>
<html>
<head>
    <title>goed zo</title>
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <link rel="stylesheet" href="survey.css"/>
</head>

<body>
<?php echo $_GET['message'] ?> <br/> <br/>
<a href="index.php" id='back'> &lt; Terug</a>

<script>
    // change colors
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
        }
    }

    changeBackground();
</script>
</body>