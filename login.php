<?php
    require_once("mysql.php");
    require_once("functions.php");

    verifyUser();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Login / BUCH.HALTUNG</title>
        <link rel="stylesheet" href="styleindex.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="logo_black.svg">
    </head>
    <body>

        <?php
            drawTopsection();
        ?>

        <section style="height: 38vw">

            <div style="padding-top: 2vw; padding-left: 30vw; padding-right: 30vw">
                <h1 class="mainfont" > Login </h1>
                        <form action="login.php" method="post">

                            <div class="field mainfont"> Username </div>
                            <input type="text" class="input mainfont" name="username" placeholder="Enter username" required><br>

                            <div class="field mainfont"> Password </div>
                            <input type="password" class="input mainfont" name="pw" placeholder="Enter password" required><br>

                            <input style="transform: scale(2); float: left; margin-top: 35px; padding-right: 20px" type="checkbox" name="rememberme"> 

                            <div style="margin-top: 30px; padding-left: 15px"> 
                                <a class="mainfont" style="padding-left: 20px"> Remember me </a>
                            </div> <br>

                            <button type="submit" class="mainfont button" id="login" name="log"> Login </button>

                        </form>

                <div style="font-size: 20px; margin-top: 20px;" class="mainfont">
                    <a> New here? </a> <a href="register.php"> Register here. </a>
                </div>
            </div>

        <?php
            if (isset($_POST["log"])) {

                $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :user"); //Username überprüfen ob existiert
                $stmt->bindParam(":user", $_POST["username"]);
                $stmt->execute();
                $count = $stmt->rowCount();
                if ($count == 1) { // Username ist frei
                    $row = $stmt->fetch();
                    if (password_verify($_POST["pw"], $row["password"])) {

                        if (isset($_POST["rememberme"])) {
                            
                            $stmt = $mysql->prepare("UPDATE accounts SET rememberToken = :tkn WHERE username = :user");
                            $token = bin2hex(random_bytes(36));
                            $stmt->bindParam(":tkn", $token);
                            $stmt->bindParam(":user", $row["username"]);
                            $stmt->execute();

                            setcookie("session", $token, time() + 3600*24*360, "/");

                        }

                        session_start();
                        //$_SESSION["username"] = $row["username"];
                        $_SESSION["userid"] = $row["userid"];

                        //require_once("dashboard/methods.php");
                        //addlog("SYSTEM", $_SESSION["userid"], "USER LOGIN", $_SERVER['REMOTE_ADDR']);

                        header("Location: dashboard/index.php");
                    } else {
                        echo "Error: Wrong password.";
                    }
                } else {
                    echo "Error: Username not found.";
                }
            }

        ?>

        </section>

        <section class="footersection">


            <center> <a style="float: center; color: white" class="footertabletext mainfont"> Copyright © 2021 BUCH.HALTUNG. All Rights Reserved </a> </center>

        </section>

    </body>
</html>