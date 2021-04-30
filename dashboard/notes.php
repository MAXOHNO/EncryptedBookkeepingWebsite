<?php
    require_once("methods.php");
    require_once("webpageBuilder.php");
    require_once("../mysql.php");

    session_start();
    verifyUser();

    if (!(isBooker($_SESSION["userid"]))) {
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="../logo_black.svg">
        <title> Notes / BUCH.HALTUNG</title>
    </head>
    <body>  

        <?php startPage("85vw", "notes"); ?>

                <!-- *************************************************************************************************************** -->

                <?php
                    // Session User (Myself)
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();

                ?>

                <?php if (hasCrypter()) {
                    ?>
                    <div class="labelNorm" style="display: inline-block; width: 79vw; margin-left: 3vw">

                        <?php randomColorLabelTitle("Personal Notes"); ?>

                        <form spellcheck="false" action="notes.php" method="post">

                            <?php
                                $stmt = $mysql->prepare("SELECT * FROM notes WHERE owner = :userid");
                                $stmt->bindParam(":userid", $_SESSION["userid"]);
                                $stmt->execute();
                                $row = $stmt->fetch();
                            ?>
                            <!-- Todo Decrypt -->
                            <textarea name="text" class="boxsizingBorder mediumtext mainfont" maxlength="42000" style="margin-left: 1vw; width: 96.7%; height: 30vw; resize: vertical"><?php echo decrypt($row["note"]); ?></textarea>

                            <p> <button type="submit" class="newbutton adminsubmit smalltext" id="save" name="save" style="width: 76.7vw; margin-top: 1vw"> Save </button> </p>

                        </form>

                    </div>
                <?php
                } else {
                    ?>
                    <!-- Dashboard Header -->
                    <div class="labelHeader">
                        <h1 class="mainfont" style="text-align: left"> Personal Notes </a> </h1> <br>
                    </div>

                   <div class="labelNorm" style="display: inline-block; width: 25.2vw; margin-left: 30vw">

                        <?php randomColorLabelTitle("Encryption"); ?>

                        <table clas="adminTable" cellspacing="0">

                            <tr> 
                                <td style="width: 30vw"> <!-- Books Anzahl -->
                                    <center> <p class="smalltext mainfont"> Encryption Key Set:</p> </center>
                                </td>
                            </tr>

                            <tr> 
                                <td style="width: 30vw"> <!-- Books Anzahl -->
                                    <center> <p class="labelHighlight smalltext mainfont"> <?php if (hasCrypter()) { echo "True"; } else { echo "False"; }?> </p> </center>
                                </td>
                            </tr>

                            <tr> 
                                <td style="width: 30vw"> <!-- Books Anzahl -->
                                    <center> <p class="smalltext mainfont"> <?php if (hasCrypter()) { echo "<a class='flatgreen'> Your Information is safe. </a>"; } else { echo "<a href='encryption' class='linktext flatred'> Please set a encryption key for your safety. </a>"; }?> </p> </center>
                                </td>
                            </tr>

                            <tr>
                            <td style="padding-right: 3vw">

                                <p class="labelHighlight smalltext mainfont"> <wbr> </p>
                                </td>
                            </tr>

                        </table>
                        </div>
                   <?php
                }

                ?>

                <?php

                    if (isset($_POST["save"])) {

                        if (!(hasCrypter())) {
                            return;
                        }

                        $stmt = $mysql->prepare("SELECT * FROM notes WHERE owner = :userid");
                        $stmt->bindParam(":userid", $_SESSION["userid"]);
                        $stmt->execute();
                        $count = $stmt->rowCount();

                        if ($count == 0) {
                            echo $count;
                            $stmt = $mysql->prepare("INSERT INTO notes (owner, note) VALUES (:userid, :note)");
                            $stmt->bindParam(":userid", $_SESSION["userid"]);
                            @$stmt->bindParam(":note", encrypt($_POST["text"]));
                            $stmt->execute();

                            addlog("SYSTEM", $_SESSION["userid"], "PERSONAL NOTE CREATED");

                            echo "<meta http-equiv='refresh' content='0'>";
                        } else {
                            echo $count;
                            $stmt = $mysql->prepare("UPDATE notes SET note = :note WHERE owner = :userid");
                            $stmt->bindParam(":userid", $_SESSION["userid"]);
                            @$stmt->bindParam(":note", encrypt($_POST["text"]));
                            $stmt->execute();

                            addlog("SYSTEM", $_SESSION["userid"], "PERSONAL NOTE EDITED");

                            echo "<meta http-equiv='refresh' content='0'>";
                        }

                    }

                ?>

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