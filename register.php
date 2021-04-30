<?php
    require_once("mysql.php");
    require_once("functions.php");

    verifyUser();
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <title>Register / BUCH.HALTUNG</title>
        <link rel="stylesheet" href="styleindex.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="logo_black.svg">
    </head>
    <body>

        <?php
            drawTopsection();
        ?>

        <section style="height: 38vw">

            <div style="padding-top: 2vw; padding-left: 30vw">
                <h1 class="mainfont" > Register </h1>
                    <form action="register.php" method="post">

                        <div class="field mainfont"> Username </div>
                        <input type="text" class="input mainfont" name="username" placeholder="Enter username" required><br>

                        <div class="field mainfont"> Email </div>
                        <input type="text" class="input mainfont" name="email" placeholder="Enter Email" required><br>

                        <div class="field mainfont"> Password </div>
                        <input type="password" class="input mainfont" name="pw" placeholder="Enter password" required><br>

                        <div class="field mainfont"> Confirm Password </div>
                        <input type="password" class="input mainfont" name="pw2" placeholder="Enter password" required><br>

                        <button type="submit" class="mainfont button" id="register" name="reg"> Register </button>

                    </form>

                <div style="font-size: 20px; margin-top: 20px;" class="mainfont">
                    <a> Already registered? </a> <a href="login.php"> Login here. </a>
                </div>
            </div>

        <?php
            if(isset($_POST["reg"])) {

                $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :user"); //Username überprüfen ob existiert
                $stmt->bindParam(":user", $_POST["username"]);
                $stmt->execute();
                $count = $stmt->rowCount();

                if (!(preg_match("/^[A-Za-z]{1}[A-Za-z0-9]{3,16}$/", $_POST["username"]))) { 
                    echo "Username invalid. ";
                    return;
                }

                if (!(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
                    echo "Email address is considered invalid. ";
                }

                if ($count == 0) { // Username ist frei
                    if ($_POST["pw"] == $_POST["pw2"]) {
                        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE email = :email");
                        $stmt->bindParam(":email", $_POST["email"]);
                        $stmt->execute();
                        $count = $stmt->rowCount();
                        if ($count == 0) {
                            $stmt = $mysql->prepare("INSERT INTO accounts (userid, username, email, password) VALUES (:uid, :user, :email, :password)");
                            $stmt->bindParam(":user", $_POST["username"]);
                            $stmt->bindParam(":email", $_POST["email"]);
                            $hash = password_hash($_POST["pw"], PASSWORD_BCRYPT);
                            $stmt->bindParam(":password", $hash);
                            $uuid = "";
                            $found = false;
							
                            while($found == false) {
								
                                $uuid = genUUID();
								
                                $query = $mysql->prepare("SELECT * FROM accounts WHERE userid = :uuid");
                                $query->bindParam(":uuid", $uuid);
                                $query->execute();
                                $countq = $query->rowCount();
                                if ($countq == 0) {
                                    $found = true;
                                }
                                
                            }
							
                            $stmt->bindParam(":uid", $uuid);
                            $stmt->execute();
                            addlog("SYSTEM", $uuid, "REGISTERED");
                            ?> <meta http-equiv = "refresh" content = "2; url = login.php" /> <?php
                        } else {
                            echo "Error: Email already in use.";
                        }
                    } else {
                        echo "Error: Passwords don't match.";
                    }
                } else {
                    echo "Error: Username already in use.";
                }
            }
        ?>    

        </section>

        <section class="footersection">


            <center> <a style="float: center; color: white" class="footertabletext mainfont"> Copyright © 2021 BUCH.HALTUNG. All Rights Reserved </a> </center>

        </section>

    </body>
</html>