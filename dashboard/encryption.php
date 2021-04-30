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
        <title> Encryption / bats.li</title>
    </head>
    <body> 

        <?php
            if (isset($_POST["crypt"])) {
                if (stripos($_POST["cookie"], "yes") !== false) {
                    setcookie("cloudf", $_POST["key"], time() + 60 * 60 * 24 * 30);
                }
            }
        ?>

        <?php startPage("85vw", "encryption"); ?>

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
                    <h1 class="mainfont" style="text-align: left"> Encryption </h1> <br>
                </div>

                <!-- PHP Logic -->
                <div class="mainfont" style="margin-bottom: 1vw; margin-left: 3vw; text-align: left">
                <?php

                    if (isset($_POST["crypt"])) {

                        //if (stripos($_POST["cookie"], "yes") !== false) { // Key soll in Cookie gespeichert werden
                            //setcookie("cloudf", "123", time() + 60 * 60 * 24 * 30);
                        //}

                        $_SESSION["ccc"] = $_POST["key"];

                        echo "<meta http-equiv='refresh' content='0'>";
                    }

                ?>
                </div>

                <!-- Encryption Label -->
                <div class="labelNorm" style="display: inline-block; width: 35vw;margin-left: 24vw">

                    <?php if (hasCrypter()) { $crypted = "<a class='flatgreen'> True </a>"; } else { $crypted = "<a class='flatred'> False </a>"; }
                    randomColorLabelTitle("Encrypted: " . $crypted ); ?>

                    <form action="encryption.php" method="post">

                        <table clas="adminTable" cellspacing="0">

                            <tr> <!-- Username -->
                                <td>
                                    <p class="smalltext mainfont"> Key: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input id="passwordToggle" style="width: 25.5vw" name="key" value="<?php if (hasCrypter()) { echo getCrypter(); } ?>" type="password" step="1" class="adminModification smalltext labelHighlight mainfont"> <img id="pwBTN" style="height: 1vw" src="img/hidePW.png" onclick="togglePW()"> </input>

                                    <script>
                                        var type = 1;
                                        function togglePW() {
                                            var x = document.getElementById("passwordToggle");
                                            if (x.type === "password") {
                                                x.type = "text";
                                            } else {
                                                x.type = "password";
                                            }

                                            var y = document.getElementById("pwBTN");
                                            if (type == 1) {
                                                y.src = "img/showPW.png";
                                                type = 2;
                                            } else {
                                                y.src = "img/hidePW.png";
                                                type = 1;
                                            }

                                        }
                                    </script>
                                </td>
                            </tr>

                            <tr> <!--  Cookie -->
                                <td>
                                    <p class="smalltext mainfont"> Cookie: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input style="width: 27vw" name="cookie" type="text" step="1" placeholder="type 'yes' to save as cookie" class="adminModification smalltext labelHighlight mainfont"> </input>
                                </td>
                            </tr> 
                            
                            
                            <tr style="MARGIN-top: 0.5vw"> <!-- Telegram -->
                                <td colspan="2">
                                    <p class="smalltext mainfont"> The Key is used for End to End Encryption and is only stored locally. </p>
                                </td>

                            </tr>

                            <tr> <!-- Telegram -->
                                <td colspan="2">
                                    <p class="smalltext mainfont"> <?php if (isBooker($_SESSION["userid"])) { echo "The Key is used to encrypt Accounts, Books and Notes. "; } ?> </p>
                                </td>

                            </tr>

                        </table>

                        <p> <button type="submit" class="newbutton adminsubmit smalltext" id="crypt" name="crypt"> Set Key </button> </p>
                        
                    </form>

                </div>

                <?php 
                
                /* TEMPLATE
                <div class="labelNorm" style="display: inline-block; width: 35vw">

                    <?php randomColorLabelTitle("Template:"); ?>

                    <p class="smalltext mainfont"> Template: <a class="labelHighlight"> <?php echo "template"; ?> </a> </p>
                </div>
                */ ?>
                        
                <!-- *************************************************************************************************************** -->

        <?php endPage(); ?>  
        
    </body>

</html>