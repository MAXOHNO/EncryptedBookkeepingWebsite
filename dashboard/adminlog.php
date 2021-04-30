<?php
    require_once("methods.php");
    require_once("webpageBuilder.php");
    require_once("../mysql.php");


    session_start();
    verifyUser();

    if (isBooker($_SESSION["userid"]) == 0) {
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
        <title> Adminlog / bats.li </title>
    </head>
    <body>  

        <?php startPage("75vw", "adminlog"); ?>

                <!-- *************************************************************************************************************** -->

                <?php
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();
                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> Adminlog</h1> <br>
                </div>

                <!-- PHP Logic -->
                <div class="mainfont" style="margin-bottom: 1vw; margin-left: 3vw; text-align: left">
                
                </div>

                <div style="margin-left: 10vw">

                <!-- Sorting Options -->
                <div class="labelNorm" style="display: inline-block; width: 62vw">

                    <?php randomColorLabelTitle("Sorting Options"); ?>

                    <form spellcheck="false" action="adminlog.php">

                        <div style="display: flex; float: left">
                            <table cellspacing="0" style="margin-left: 1vw">

                                <tr class="smalltext mainfont">
                                    
                                    <td> Order by </td>

                                    <td> Time Filter </td>

                                    <td> From UserID </td>

                                </tr>

                                <tr class="mediumtext mainfont">
                                
                                    <td> <select class="mediumtext" id="filterBook" value="Active" name="filter" style="padding-right: 2vw; margin-right: 1vw">
                                        <?php if (isset($_GET["filter"])) { $getfilter = $_GET["filter"]; } else { $getfilter = "DEFAULT"; } ?>
                                        <option value="DESC" <?php if ($getfilter == "DESC" || $getfilter == "DEFAULT") { echo "selected"; } ?> > Descending </option>
                                        <option value="ASC" <?php if ($getfilter == "ASC") { echo "selected"; } ?> > Ascending </option>
                                    </select> </td>

                                    <td> <select class="mediumtext" id="filterBook" name="time" style="padding-right: 2vw; margin-right: 1vw">
                                        <?php if (isset($_GET["time"])) { $gettime = $_GET["time"]; } else { $gettime = "DEFAULT"; } ?>
                                        <option value="7" <?php if ($gettime == "7" || $getfilter == "DEFAULT") { echo "selected"; } ?> > Last week </option>
                                        <option value="30" <?php if ($gettime == "30") { echo "selected"; } ?> > Last month </option>
                                        <option value="90" <?php if ($gettime == "90") { echo "selected"; } ?> > Last 3 months </option>
                                        <option value="180" <?php if ($gettime == "180") { echo "selected"; } ?> > Last 6 months </option>
                                        <option value="365" <?php if ($gettime == "365") { echo "selected"; } ?> > Last year </option>
                                        <option value="99999" <?php if ($gettime == "99999") { echo "selected"; } ?> > All time </option>
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

                <!-- Book Card Generation -->
                <?php 
                    $requirements = 0;

                    /* ------------------ Search Logic Setup ------------------ */

                    if (!isset($_GET["filter"])) {
                        $_GET["filter"] = "DESC";
                    }

                    if (isset($_GET["filter"])) {
                        if ($_GET["filter"] == "ASC") {
                            $getSortBy = "ASC";
                        } else if ($_GET["filter"] == "DESC") {
                            $getSortBy = "DESC";
                        } else {
                            $getSortBy = "DESC";
                        }


                    }

                    if (isset($_GET["time"])) {
                        $startDate = date("Y-m-d", time() - $_GET["time"] * 60 * 60 * 24);
                        $endDate = date("Y-m-d", time());


                        $requirements++;
                        $_SESSION["time"] = $_GET["time"];
                    }

                    if (isset($_GET["uid"])) {
                        $requirements++;
                        $target = $_GET["uid"];
                    }


                    $stmt = $mysql->prepare("SELECT * FROM adminlog ORDER BY date " . $getSortBy);
                    $stmt->execute();
                    $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    /* ------------------------------------------------------ */

                    for ($i = 0; $i < count($row); $i++) {

                        $founds = 0;
                        /* ------------------ UID Filter ------------ */
                        if (isset($_GET["uid"])) {

                            if ($row[$i]["target"] == $target) {
                                $founds++;
                            }

                        } 
                        /* ------------------------------------------------------ */

                        /* ------------------ Book Time Filter ---------------- */
                        if (isset($_GET["time"])) {

                            $refDate = date("Y-m-d", strtotime($row[$i]["date"]));
                            if ($refDate >= $startDate) {
                                $founds++;
                            }

                        } 
                        /* ------------------------------------------------------ */

                        if ($requirements <= $founds) {
                            //displayBookInfo($row[$i]["refid"]);
                            displayAdminLog($row[$i]["date"], $row[$i]["admin"], $row[$i]["target"], $row[$i]["action"], $row[$i]["note"]);
                        }
                         
                    }
                ?>

                </div>
                        
                <!-- *************************************************************************************************************** -->

        <?php endPage(); ?>  
        
    </body>
</html>