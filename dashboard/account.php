<?php
    require_once("methods.php");
    require_once("webpageBuilder.php");
    require_once("../mysql.php");


    session_start();
    verifyUser();

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="../logo_black.svg">
        <title> My Account / bats.li</title>
    </head>
    <body>  

        <?php startPage("85vw", "account"); ?>

                <!-- *************************************************************************************************************** -->

                <?php
                    // Session User (Myself)
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();

                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> Account </h1> <br>
                </div>

                <!-- PHP Logic -->
                <div class="mainfont" style="margin-bottom: 1vw; margin-left: 3vw; text-align: left">
                <?php

                    if (isset($_POST["modify"])) {

                        $errors = 0;
                        $refreshes = 0;

                        if (!(preg_match("/^[A-Za-z]{1}[A-Za-z0-9]{3,12}$/", $_POST["username"]))) { 
                            output("Username invalid.", "ERROR");
                            $errors++;
                        }
                        
                        if (!(preg_match("/^[A-Za-z]{1}[A-Za-z0-9]{3,12}$/", $_POST["telegram"]))) { 
                            output("Telegram invalid.", "ERROR");
                            $errors++;
                        }
                        
                        if (!(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))) {
                            output("Email invalid.", "ERROR");
                            $errors++;
                        }

                        // ********************************** CHANGE USERNAME ******************************************
                        if ($errors == 0) {
                            $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :newname");
                            $stmt->bindParam(":newname", $_POST["username"]);
                            $stmt->execute();
                            $count = $stmt->rowCount();

                            if ($count > 0) {
                                if ($_POST["username"] != getUsername($_SESSION["userid"])) {
                                    output("Username already in use.", "ERROR");
                                }
                            } else {
                                $stmt = $mysql->prepare("UPDATE accounts SET username = :username WHERE userid = :userid");
                                $stmt->bindParam(":userid", $_SESSION["userid"]);
                                $stmt->bindParam(":username", $_POST["username"]);
                                $stmt->execute();
                                addlog("SYSTEM", $_SESSION["userid"], "SELF: USERNAME UPDATE");
                                $refreshes++;
                            }

                            // ********************************** CHANGE TELEGRAM ******************************************
                            $stmt = $mysql->prepare("SELECT * FROM accounts WHERE telegram = :telegram");
                            $stmt->bindParam(":telegram", $_POST["telegram"]);
                            $stmt->execute();
                            $count = $stmt->rowCount();

                            if ($count > 0) {
                                if ($_POST["telegram"] != getTelegram($_SESSION["userid"])) {
                                    output("Telegram already in use.", "ERROR");
                                }
                            } else {
                                $stmt = $mysql->prepare("UPDATE accounts SET telegram = :telegram WHERE userid = :userid");
                                $stmt->bindParam(":userid", $_SESSION["userid"]);
                                $stmt->bindParam(":telegram", $_POST["telegram"]);
                                $stmt->execute();
                                addlog("SYSTEM", $_SESSION["userid"], "SELF: TELEGRAM UPDATE");
                                $refreshes++;
                            }

                            // ********************************** CHANGE EMAIL ******************************************
                            $stmt = $mysql->prepare("SELECT * FROM accounts WHERE email = :email");
                            $stmt->bindParam(":email", $_POST["email"]);
                            $stmt->execute();
                            $count = $stmt->rowCount();

                            if ($count > 0) {
                                if ($_POST["email"] != getEmail($_SESSION["userid"])) {
                                    output("Email already in use.", "ERROR");
                                }
                            } else {
                                $stmt = $mysql->prepare("UPDATE accounts SET email = :email WHERE userid = :userid");
                                $stmt->bindParam(":userid", $_SESSION["userid"]);
                                $stmt->bindParam(":email", $_POST["email"]);
                                $stmt->execute();
                                addlog("SYSTEM", $_SESSION["userid"], "SELF: EMAIL UPDATE");
                                $refreshes++;
                            }

                            if ($refreshes > 0) {
                                echo "<meta http-equiv='refresh' content='0'>";
                            }
                            
                        }
                    }

                    if (isset($_POST["changepw"])) {
                        if (password_verify($_POST["oldpw"], $rs["password"])) {
                            if ($_POST["pw1"] == $_POST["pw2"]) {
                                $stmt = $mysql->prepare("UPDATE accounts SET password = :pw WHERE userid = :userid");
                                $hash = password_hash($_POST["pw1"], PASSWORD_BCRYPT);
                                $stmt->bindParam(":pw", $hash);
                                $stmt->bindParam(":userid", $_SESSION["userid"]);
                                $stmt->execute();

                                output("Password has been updated.", "GOOD");

                                addlog("SYSTEM", $_SESSION["userid"], "PASSWORD UPDATE");
                            } else {
                                output("Passwords do not match.", "ERROR");
                            }
                        } else {
                            output("Wrong Password entered.", "ERROR");
                        }
                    }

                    if (isset($_POST["skeyActivate"])) {
                        $stmt = $mysql->prepare("SELECT * FROM serialkeys where skey = :skey");
                        $stmt->bindParam(":skey", $_POST["skey"]);
                        $stmt->execute();
                        $row = $stmt->fetch();
                        $count = $stmt->rowCount();

                        if ($count == 0) {
                            output("Serialkey does not exist.", "ERROR");
                        } else if ($row["claimer"] == "") {
                            $stmt = $mysql->prepare("UPDATE serialkeys SET claimer = :userid, claimDate = :cDate WHERE skey = :skey");
                            $stmt->bindParam(":skey", $_POST["skey"]);
                            $stmt->bindParam(":userid", $_SESSION["userid"]);
                            $current_timestamp = date("Y-m-d H:i:s");
                            $stmt->bindParam(":cDate", $current_timestamp);
                            $stmt->execute();

                            if ($row["booker"] == 1) {

                                $stmt = $mysql->prepare("UPDATE accounts SET booker = 1 WHERE userid = :userid");
                                $stmt->bindParam(":userid", $_SESSION["userid"]);
                                $stmt->execute();

                                addlog("SYSTEM", $_SESSION["userid"], "KEY CLAIMED", $_POST["skey"]);

                                output("Successfully claimed key.", "GOOD");

                            } else {
                                output("Serialkey is invalid. Please contact Support.", "ERROR");
                            }
                            
                        } else {
                            output("Serialkey has been claimed already.", "ERROR");
                        }
                    }

                ?>
                </div>

                <!-- User Profile -->
                <div class="labelNorm" style="display: inline-block; width: 35vw;margin-left: 3vw">

                    <?php randomColorLabelTitle("Profile"); ?>

                    <form action="account.php" method="post">

                        <table clas="adminTable" cellspacing="0">

                            <tr> <!-- Username -->
                                <td>
                                    <p class="smalltext mainfont"> Username: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input name="username" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($rs["username"]); ?>"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Telegram -->
                                <td>
                                    <p class="smalltext mainfont"> Telegram: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input name="telegram" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($rs["telegram"]); ?>"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Email -->
                                <td>
                                    <p class="smalltext mainfont"> Email: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input name="email" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($rs["email"]); ?>"> </input>
                                </td>
                            </tr>

                        </table>

                        <p> <button type="submit" class="newbutton adminsubmit smalltext" id="modify" name="modify" style="margin-top: 1vw"> Save </button> </p>
                        
                    </form>

                </div>

                <!-- Change Password -->
                <div class="labelNorm" style="display: inline-block; width: 35vw">

                    <?php randomColorLabelTitle("Change Password"); ?>

                    <form action="account.php" method="post">

                        <table clas="adminTable" cellspacing="0">

                            <tr> <!-- Old Password -->
                                <td>
                                    <p class="smalltext mainfont"> Old Password: </p>
                                </td>

                                <td>
                                    <input name="oldpw" type="password" step="1" class="adminModification smalltext labelHighlight mainfont" placeholder="Enter Old Password"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- New Password -->
                                <td>
                                    <p class="smalltext mainfont"> New Password: </p>
                                </td>

                                <td>
                                    <input name="pw1" type="password" step="1" class="adminModification smalltext labelHighlight mainfont" placeholder="Enter New Password"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- New Password Confirm -->
                                <td>
                                    <p class="smalltext mainfont"> New Password: </p>
                                </td>

                                <td>
                                    <input name="pw2" type="password" step="1" class="adminModification smalltext labelHighlight mainfont" placeholder="Confirm New Password"> </input>
                                </td>
                            </tr>

                        </table>

                        <p> <button type="submit" class="newbutton adminsubmit smalltext" id="changepw" name="changepw" style="margin-top: 1vw"> Change Password </button> </p>
                        
                    </form>
                </div>

                <!-- User Profile -->
                <div class="labelNorm" style="display: inline-block; width: 35vw;margin-left: 3vw">

                    <?php randomColorLabelTitle("Serialkey Activator"); ?>

                    <form action="account.php" method="post">

                        <table clas="adminTable" cellspacing="0">

                            <tr> <!-- Username -->
                                <td>
                                    <p class="smalltext mainfont"> Serialkey: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input name="skey" type="text" step="1" class="adminModification smalltext labelHighlight mainfont"> </input>
                                </td>
                            </tr>
                        </table>

                        <p> <button type="submit" class="newbutton adminsubmit smalltext" id="skeyActivate" name="skeyActivate" style="margin-top: 1vw"> Activate </button> </p>
                        
                    </form>

                </div>

                <?php /* TEMPLATE
                <div class="labelNorm" style="display: inline-block; width: 35vw">

                    <?php randomColorLabelTitle("Template:"); ?>

                    <p class="smalltext mainfont"> Template: <a class="labelHighlight"> <?php echo "template"; ?> </a> </p>
                </div>
                */ ?>
                        
                <!-- *************************************************************************************************************** -->

        <?php endPage(); ?>  
        
    </body>
</html>