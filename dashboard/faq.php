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
        <title> FAQ / bats.li</title>
    </head>
    <body>  

        <?php startPage("85vw", "faq"); ?>

                <!-- *************************************************************************************************************** -->

                <?php
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();
                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> Frequently Asked Questions </h1> <br>
                </div>

                <!-- Container wo alles außer Notifications drinne ist, damit Notifications immer ganz rechts ist -->
                <div style="display: inline-block; width: 85vw; float: left; margin-left: 2.5vw"> 

                    <!-- Purpose FAQ -->
                    <div class="labelNorm" style="display: inline-block; width: 36vw">

                        <?php randomColorLabelTitle("Q: What is the purpose of this website?", true); ?>

                        <p class="faq smalltext mainfont"> The Website serves no public purpose. If you know what you want to do here feel free to stay and use our service, otherwise you can leave again :) For any questions you can contact <a class="linktext" target="_blank" rel="noopener noreferrer" href="https://t.me/batscs">Bats on Telegram</a>. <br><br></p>
                    </div>

                    <!-- Serialkeys FAQ -->
                    <div class="labelNorm" style="display: inline-block; width: 36vw">

                        <?php randomColorLabelTitle("Q: What are Serialkeys and how do I get them?", true); ?>

                        <p class="faq smalltext mainfont"> Serialkeys give access to exlusive features on the website and give the option to store data safely with END to END Encryption. They can be acquired on <a class="linktext" target="_blank" rel="noopener noreferrer" href="https://t.me/batscs">Bats' Telegram</a>. 
                        <br> Serialkeys can be activated on the <a class="linktext" target="_blank" rel="noopener noreferrer" href="account"> My Account </a> page.</p>
                    </div>

                    <!-- Books FAQ -->
                    <?php if (isBooker($_SESSION["userid"])) { ?>
                        <div class="labelNorm" style="display: inline-block; width: 36vw">

                            <?php randomColorLabelTitle("Q: Help all my information is blank!", true); ?>

                            <p class="faq smalltext mainfont"> Due to the decryption everything will be blank if you enter a wrong encryption key, this is due to how openssl is decrypting. Make sure to set one on the <a class="linktext" target="_blank" rel="noopener noreferrer" href="encryption">Encryption Page</a>. </p>
                        </div>

                        <div class="labelNorm" style="display: inline-block; width: 36vw;">

                            <?php randomColorLabelTitle("Q: I lost my Encryption Key, what should I do now?", true); ?>

                            <p class="faq smalltext mainfont"> As mentioned on the <a class="linktext" target="_blank" rel="noopener noreferrer" href="encryption">Encryption Page</a>, the key is only stored locally and never on the server. If you lost your key you need to reset everything. Make sure to remember your key.</p>
                        </div>
                    <?php } ?>

                </div>
                        
                <!-- *************************************************************************************************************** -->

        <?php endPage(); ?>  
        
    </body>
</html>