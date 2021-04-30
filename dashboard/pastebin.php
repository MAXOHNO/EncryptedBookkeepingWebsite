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
        <title> Pastebin / BUCH.HALTUNG </title>
    </head>
    <body>  

        <?php startPage("75vw", "pastebin"); ?>

                <!-- *************************************************************************************************************** -->

                <!-- *************************************************************************************************************** -->

                <?php
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();
                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> Bats' Pastebin </h1> <br>
                </div>

                <!-- PHP Logic -->
                <div class="mainfont" style="margin-bottom: 1vw; margin-left: 3vw; text-align: left">
                
                </div>

                <div style="margin-left: 10vw">

                <!-- Sorting Options -->
                <div class="labelNorm" style="display: inline-block; width: 40vw">

                    <?php randomColorLabelTitle("Sorting Options"); ?>

                    <form spellcheck="false" action="books.php">

                        <div style="display: flex; float: left">
                            <table cellspacing="0" style="margin-left: 1vw">

                                <tr class="smalltext mainfont">
                                    
                                    <td> Book Filter </td>

                                    <td> Time Filter </td>

                                </tr>

                                <tr class="mediumtext mainfont">
                                
                                    <td> <select class="mediumtext" id="filterBook" value="Active" name="filter" style="padding-right: 2vw; margin-right: 1vw">
                                        <?php if (isset($_GET["filter"])) { $getfilter = $_GET["filter"]; } else { $getfilter = "DEFAULT"; } ?>
                                        <option value="0a1a2a3a4a5" <?php if ($getfilter == "0a1a2a3a4a5") { echo "selected"; } ?> > Show All </option>
                                        <option value="0a1a2" <?php if ($getfilter == "0a1a2" || $getfilter == "DEFAULT") { echo "selected"; } ?> > Active </option>
                                        <option value="3a4a5" <?php if ($getfilter == "3a4a5") { echo "selected"; } ?> > Inactive </option>
                                    </select> </td>

                                    <td> <select class="mediumtext" id="filterBook" name="time" style="padding-right: 2vw; margin-right: 1vw">
                                        <?php if (isset($_GET["time"])) { $gettime = $_GET["time"]; } else { $gettime = ""; } ?>
                                        <option value="99999" <?php if ($gettime == "99999") { echo "selected"; } ?> > All time </option>
                                        <option value="30" <?php if ($gettime == "30") { echo "selected"; } ?> > Last month </option>
                                        <option value="90" <?php if ($gettime == "90") { echo "selected"; } ?> > Last 3 months </option>
                                        <option value="180" <?php if ($gettime == "180") { echo "selected"; } ?> > Last 6 months </option>
                                        <option value="365" <?php if ($gettime == "365") { echo "selected"; } ?> > Last year </option>
                                    </select> </td>

                                    <td>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div style="float: right; margin-right: 2vw">
                            <p class="smalltext mainfont"> <input class="newbutton adminsubmit" type="submit" style="width: 10vw; padding: 1vw" value="Search" /> </p>
                        </div>

                    </form>

                </div>

                <!-- Sorting Options -->
                <div class="labelNorm" style="display: inline-block; width: 20vw">

                    <?php randomColorLabelTitle("Create new Pastebin"); ?>

                    <form spellcheck="false" action="addPastebin.php">

                        <div style="float: right; margin-right: 5vw">
                            <p class="smalltext mainfont"> <input class="newbutton adminsubmit" type="submit" style="width: 10vw; padding: 1vw" value="Add Pastebin" /> </p>
                        </div>

                    </form>

                </div>

                <!-- Book Card Generation -->
                <?php 
                    $stmt = $mysql->prepare("SELECT * FROM pastes WHERE owner = :userid ORDER BY created DESC");
                    $stmt->bindParam(":userid", $_SESSION["userid"]);
                    $stmt->execute();
                    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    for ($i = 0; $i < count($row); $i++) {

                        displayPasteInfo($row[$i]["pasteid"]);
                         
                    }
                ?>

                </div>
                        
                <!-- *************************************************************************************************************** -->

        <?php endPage(); ?>  
        
    </body>
</html>