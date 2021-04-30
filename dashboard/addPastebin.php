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
        <title> Notes / bats.li</title>
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

                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> Add new Paste </h1> <br>
                </div>

                <?php

                    if (isset($_POST["save"])) {

                        $found = false;
                        $pasteid = "";
                        while ($found == false) {
                            $pasteid = getRandomString(8, "int");
                            $stmt = $mysql->prepare("SELECT * FROM pastes WHERE pasteid = :pasteid");
                            $stmt->bindParam(":pasteid", $pasteid);
                            $stmt->execute();
                            $count = $stmt->rowCount();
                            if ($count == 0) {
                                $found = true;
                            }
                        }

                        $stmt = $mysql->prepare("INSERT INTO pastes (owner, pasteid, title, password, visibility, type, text) VALUE (:userid, :pasteid, :title, :pw, :vis, :type, :text)");
                        $stmt->bindParam(":userid", $_SESSION["userid"]);
                        $stmt->bindParam(":pasteid", $pasteid);
                        $stmt->bindParam(":title", $_POST["title"]);
                        $stmt->bindParam(":pw", $_POST["password"]);
                        $stmt->bindParam(":vis", $_POST["visibility"]);
                        $stmt->bindParam(":type", $_POST["type"]);
                        $stmt->bindParam(":text", $_POST["text"]);
                        $stmt->execute();

                        if ($_POST["type"] != "privnote") {
                            ?> <meta http-equiv="Refresh" content="0; url='viewPaste.php?p=<?php echo $pasteid; ?>'" /> <?php
                        } else {
                            ?> <a class="mediumtext mainfont"> <?php output("Your self-destruction paste has been created at: dashboard/viewPaste.php?p=" . $pasteid); ?> </a> <?php
                        }
                        

                    }

                ?>

                <form spellcheck="false" action="addPastebin.php" method="post">

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

                                    <td> <input maxlength="254" name="title" type="text" id="filterBook" class="mediumtext labelHighlight mainfont" style="padding-right: 2vw; margin-right: 1vw; width: 10vw"> </input> </td>

                                    <td> <input maxlength="254" name="password" type="text" id="filterBook" class="mediumtext labelHighlight mainfont" style="padding-right: 2vw; margin-right: 1vw; width: 10vw"> </input> </td>
                                    
                                    <td> <select class="mediumtext" id="filterBook" name="visibility" style="padding-right: 2vw; margin-right: 1vw">
                                        <option value="public"> Public </option>
                                        <option value="private"> Private </option>
                                    </select> </td>

                                    <td> <select class="mediumtext" id="filterBook" name="type" style="padding-right: 2vw; margin-right: 1vw">
                                        <option value="normal"> Normal </option>
                                        <option value="privnote"> Self Destruct </option>
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

                        <?php randomColorLabelTitle("Submit new Paste"); ?>

                        <div style="float: right; margin-right: 5vw">
                            <p class="smalltext mainfont"> <input class="newbutton adminsubmit" name="save" type="submit" style="width: 10vw; padding: 1vw" value="Confirm" /> </p>
                        </div>

                    </div>

                    <div class="labelNorm" style="display: inline-block; width: 80vw; margin-left: 3vw;">

                        <?php randomColorLabelTitle("New Pasta"); ?>

                            <?php
                                $stmt = $mysql->prepare("SELECT * FROM notes WHERE owner = :userid");
                                $stmt->bindParam(":userid", $_SESSION["userid"]);
                                $stmt->execute();
                                $row = $stmt->fetch();
                            ?>
                            <!-- Todo Decrypt -->
                                <textarea required name="text" class="boxsizingBorder mediumtext mainfont" maxlength="42000" style="margin-left: 1vw; width: 96.7%; height: 20vw; resize: vertical"> </textarea>
                            <br><br>

                    </div>

                </form>

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