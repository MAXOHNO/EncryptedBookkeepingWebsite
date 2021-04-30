<?php
    require_once("mysql.php");
    require_once("functions.php");

    verifyUser();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Home / BUCH.HALTUNG</title>
        <link rel="stylesheet" href="styleindex.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="logo_black.svg">
    </head>
    <body>

        <?php
            drawTopsection();
        ?>

        <section>

            <div style="padding-top: 5vw;">
            <center> <img src="logo_text.png" style="width: 30%"> </center>
            </div>

        </section>

        <section class="footersection">


            <center> <a style="float: center; color: white" class="footertabletext mainfont"> Copyright © 2021 BUCH.HALTUNG. All Rights Reserved </a> </center>

        </section>

    </body>
</html>