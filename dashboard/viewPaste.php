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
        <title> <?php echo $_GET["p"]; ?> / BUCH.HALTUNG</title>
    </head>
    <body>  

        <?php startPage("85vw", "pastebin"); ?>

                <!-- *************************************************************************************************************** -->

                <?php
                    // Session User (Myself)
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();

                    // Pastebin GET
                    $qr = $mysql->prepare("SELECT * FROM pastes WHERE pasteid = :pasteid");
                    $qr->bindParam(":pasteid", $_GET["p"]);
                    $qr->execute();
                    $ss = $qr->fetch();

                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> Paste: <?php echo $_GET["p"];?> </h1> <br>
                </div>
                <form spellcheck="false" action="viewPaste.php?p=<?php echo $_GET["p"]?>" method="post">

                    <?php if ($ss["owner"] == $_SESSION["userid"]) { ?>
                        <!-- Paste Settings -->
                        <div class="labelNorm" style="display: inline-block; width: 58vw; margin-left: 3vw">

                            <?php randomColorLabelTitle("Paste Settings"); ?>

                            <div style="display: flex; float: left">
                                <table cellspacing="0" style="margin-left: 1vw">

                                    <tr class="smalltext mainfont">

                                        <td> Title </td>
                                        
                                        <td> Password </td>

                                        <td> Visibility </td>

                                        <td> Type </td>

                                    </tr>

                                    <tr class="mediumtext mainfont">

                                        <td> <input value="<?php echo $ss["title"]?>" maxlength="254" name="title" type="text" id="filterBook" class="mediumtext labelHighlight mainfont" style="padding-right: 2vw; margin-right: 1vw; width: 10vw"> </input> </td>

                                        <td> <input value="<?php echo $ss["password"]?>" maxlength="254" name="password" type="text" id="filterBook" class="mediumtext labelHighlight mainfont" style="padding-right: 2vw; margin-right: 1vw; width: 10vw"> </input> </td>
                                        
                                        <td> <select class="mediumtext" id="filterBook" name="visibility" style="padding-right: 2vw; margin-right: 1vw">
                                            <option <?php if ($ss["visibility"] == "public") { echo "selected"; }?> value="public"> Public </option>
                                            <option <?php if ($ss["visibility"] == "private") { echo "selected"; }?> value="private"> Private </option>
                                        </select> </td>

                                        <td> <select class="mediumtext" id="filterBook" name="type" style="padding-right: 2vw; margin-right: 1vw">
                                            <option <?php if ($ss["type"] == "normal") { echo "selected"; }?> value="normal"> Normal </option>
                                            <option <?php if ($ss["type"] == "privnote") { echo "selected"; }?> value="privnote"> Self Destruct </option>
                                        </select> </td>

                                    </tr>
                                </table>
                            </div>

                            <div style="float: right; margin-right: 2vw; padding-bottom: 3.8vw">
                                <!-- <p class="smalltext mainfont"> <input class="newbutton adminsubmit" type="submit" style="width: 10vw; padding: 1vw;" value="Search" /> </p> -->
                            </div>

                            </div>

                            <!-- Add Paste -->
                            <div class="labelNorm" style="display: inline-block; width: 20vw">

                            <?php randomColorLabelTitle("Confirm Changes"); ?>

                            <div style="float: right; margin-right: 5vw">
                                <p class="smalltext mainfont"> <input class="newbutton adminsubmit" name="save" type="submit" style="width: 10vw; padding: 1vw" value="Save" /> </p>
                            </div>

                        </div>
                        
                    <?php } ?>

                    <?php 
                        $dontShow = 0;

                        // Private Requirement
                        if ($ss["visibility"] == "private" && $ss["owner"] != $_SESSION["userid"]) {
                            $dontShow++;
                        }

                        // Password Requirement
                        if (!(isset($_GET["pw"]))) { $_GET["pw"] = ""; }
                        if ($ss["password"] != "" && $ss["password"] != $_GET["pw"] && $ss["owner"] != $_SESSION["userid"]) {
                            $dontShow++;
                        }

                        if ($dontShow <= 0) { ?>

                            <!-- PASTE TEXT HERE -->
                            <div class="labelNorm" style="display: inline-block; width: 80vw; margin-left: 3vw">

                                <?php
                                if ($ss["title"] != "") {
                                    randomColorLabelTitle($ss["title"]); 
                                } else {
                                    randomColorLabelTitle($_GET["p"]); 
                                }
                                ?>

                                <p class="mainfont">
                                    <?php
                                        if ($ss["type"] == "privnote") {
                                            output("This is a self destructing paste :)", "NOTICE");
                                            $stmt = $mysql->prepare("DELETE FROM pastes WHERE pasteid = :pid");
                                            $stmt->bindParam(":pid", $ss["pasteid"]);
                                            $stmt->execute();
                                        }
                                    ?>
                                </p>

                                <!-- Todo Decrypt -->
                                <textarea <?php if ($ss["owner"] != $_SESSION["userid"]) { echo "readonly"; } ?> name="text" class="boxsizingBorder mediumtext mainfont" maxlength="42000" style="margin-left: 1vw; width: 96.7%; height: 30vw; resize: vertical"><?php echo $ss["text"]; ?></textarea>
                                <br><br>
                            </div>
                        <?php } ?>
                    </form>

                    <?php
                        if ($ss["password"] != "" && $ss["password"] != $_GET["pw"] && $ss["owner"] != $_SESSION["userid"]) {
                            ?>
                            <!-- Enter Password Form -->
                            <form spellcheck="false" action="viewPaste.php?p=<?php echo $_GET["p"]; ?>" method="post">
                                <!-- Sorting Options -->
                                <div class="labelNorm" style="display: inline-block; width: 58vw; margin-left: 3vw">
                
                                    <?php randomColorLabelTitle("Enter Password"); ?>
                
                                        <div style="display: flex; float: left">
                                            <table cellspacing="0" style="margin-left: 1vw">
                
                                                <tr class="smalltext mainfont">
                                                    
                                                    <td> Password </td>
                
                                                </tr>
                
                                                <tr class="mediumtext mainfont">
                                                
                                                    <td> <input maxlength="254" name="pw" type="text" id="filterBook" class="mediumtext labelHighlight mainfont" style="padding-right: 2vw; margin-right: 1vw; width: 50vw"> </input> </td></td>
                
                                                </tr>
                                            </table>

                                            <div style="float: right; margin-right: 2vw; padding-bottom: 3.8vw">
                                                <!-- <p class="smalltext mainfont"> <input class="newbutton adminsubmit" type="submit" style="width: 10vw; padding: 1vw;" value="Search" /> </p> -->
                                            </div>
                                        </div>
                
                                </div>
                
                                <!-- Add Book -->
                                <div class="labelNorm" style="display: inline-block; width: 20vw">
                
                                    <?php randomColorLabelTitle("Enter Password"); ?>
                
                                        <div style="float: right; margin-right: 5vw">
                                            <p class="smalltext mainfont"> <input class="newbutton adminsubmit" name="enteredpw" type="submit" style="width: 10vw; padding: 1vw" value="Enter" /> </p>
                                        </div>
                
                                </div>
                            </form>
                            <?php
                        }
                    ?>

                <?php   
                    if (isset($_POST["enteredpw"])) {
                        echo "jaa";
                        ?> <meta http-equiv="Refresh" content="0; url='viewPaste.php?p=<?php echo $_GET["p"] . "&pw=" . $_POST["pw"]; ?>'" /> <?php
                    }

                    if (isset($_POST["save"])) {

                        if ($ss["owner"] != $_SESSION["userid"]) {
                            return;
                        }

                        $found = false;
                        $pasteid = $_GET["p"];

                        $stmt = $mysql->prepare("UPDATE pastes SET title = :title, password = :pw, visibility = :vis, type = :type, text = :text WHERE pasteid = :pasteid");
                        $stmt->bindParam(":pasteid", $pasteid);
                        $stmt->bindParam(":title", $_POST["title"]);
                        $stmt->bindParam(":pw", $_POST["password"]);
                        $stmt->bindParam(":vis", $_POST["visibility"]);
                        $stmt->bindParam(":type", $_POST["type"]);
                        $stmt->bindParam(":text", $_POST["text"]);
                        $stmt->execute();

                        ?> <meta http-equiv="Refresh" content="0; url='viewPaste.php?p=<?php echo $pasteid; ?>'" /> <?php

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