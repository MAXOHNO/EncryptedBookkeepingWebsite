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
        <title> Dashboard / bats.li</title>
    </head>
    <body>  

        <?php startPage("85vw", "index"); ?>

                <!-- *************************************************************************************************************** -->

                <?php
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();
                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> Dashboard </h1> <br>
                </div>

                <!-- Container wo alles außer Notifications drinne ist, damit Notifications immer ganz rechts ist -->
                <div style="display: inline-block; width: 58vw; float: left; margin-left: 3vw"> 
                    <!-- Profile -->
                    <div class="labelNorm" style="display: inline-block; width: 33vw">

                        <?php randomColorLabelTitle("Profile"); ?>

                        <table clas="adminTable" cellspacing="0">

                            <tr> <!-- UUID -->
                                <td>
                                    <p class="smalltext mainfont"> UUID: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($rs["userid"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Username -->
                                <td>
                                    <p class="smalltext mainfont"> Username: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($rs["username"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Permissions -->
                                <td>
                                    <p class="smalltext mainfont"> Permissions: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars(getRoleID($rs["role"])); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Telegram -->
                                <td>
                                    <p class="smalltext mainfont"> Telegram: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> @<?php echo htmlspecialchars($rs["telegram"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Email -->
                                <td>
                                    <p class="smalltext mainfont"> Email: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($rs["email"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Joined -->
                                <td>
                                    <p class="smalltext mainfont"> Joined: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($rs["joined"]); ?> </p>
                                </td>
                            </tr>

                        </table>
                    </div>

                    <!-- Statistics -->
                    <div class="labelNorm" style="display: inline-block; width: 20vw">

                        <?php randomColorLabelTitle("Statistics"); ?>

                        <table clas="adminTable" cellspacing="0">

                            <tr> <!-- Deposited -->
                                <td>
                                    <p class="smalltext mainfont"> Deposited: </p>
                                </td>
                                <td>
                                <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($rs["deposited"]); ?> € </p>
                                </td>
                            </tr>

                            <tr> <!-- Balance -->
                                <td>
                                    <p class="smalltext mainfont"> Balance: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($rs["balance"]); ?> € </p>
                                </td>
                            </tr>

                            <tr> <!-- Spacer -->
                                <td>
                                    <p class="smalltext mainfont"> <wbr> </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <wbr> </p>
                                </td>
                            </tr>

                            <tr> <!-- Deposits -->
                                <td>
                                    <p class="smalltext mainfont"> Deposits: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($rs["deposits"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Tickets -->
                                <td>
                                    <p class="smalltext mainfont"> Tickets: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($rs["tickets"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Pastes -->
                                <td>
                                    <p class="smalltext mainfont"> Pastes: </p>
                                </td>
                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php 
                                    $query = $mysql->prepare("SELECT * FROM pastes WHERE owner = :userid");
                                    $query->bindParam(":userid", $_SESSION["userid"]);
                                    $query->execute();
                                    $count = $query->rowCount();
                                    echo $count;
                                    ?> </p>
                                </td>
                            </tr>

                        </table>                    

                    </div>

                    <!-- Books -->
                    <?php
                     
                    $stmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid");
                    $stmt->bindParam(":userid", $_SESSION["userid"]);
                    $stmt->execute();
                    $count = $stmt->rowCount();

                    if (isBooker($_SESSION["userid"]) && $count > 0) { ?>
                        <div class="labelNorm" style="display: inline-block; width: 28vw">

                            <?php randomColorLabelTitle("Books"); ?>

                            <table clas="adminTable" cellspacing="0">

                                <tr> 
                                    <td> <!-- Books Anzahl -->
                                        <p class="smalltext mainfont"> Books: </p>
                                    </td>
                                    <td style="padding-right: 3vw">
                                        <p class="labelHighlight smalltext mainfont"> <?php 
                                            $stmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid");
                                            $stmt->bindParam(":userid", $_SESSION["userid"]);
                                            $stmt->execute();
                                            $count = $stmt->rowCount();

                                            echo $count;
                                        ?> </p>
                                    </td>

                                    <td> <!-- Summe Value -->
                                        <p class="smalltext mainfont"> Value: </p>
                                    </td>
                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php 
                                            $stmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid AND progress = 3 OR progress = 2");
                                            $stmt->bindParam(":userid", $_SESSION["userid"]);
                                            $stmt->execute();
                                            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            $summeValue = 0;

                                            for ($i = 0; $i < count($row); $i++) {
                                                $summeValue += $row[$i]["value"];
                                            }

                                            echo $summeValue . " €";
                                        ?> </p>
                                    </td>
                                </tr>

                                <tr> 
                                    <td> <!-- Erfolgreiche Books -->
                                        <p class="smalltext mainfont"> Abgeschlossen: </p>
                                    </td>
                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php 
                                            $stmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid AND progress = 3");
                                            $stmt->bindParam(":userid", $_SESSION["userid"]);
                                            $stmt->execute();
                                            echo $stmt->rowCount();
                                        ?> </p>
                                    </td>

                                    <td> <!-- Profit Summe -->
                                        <p class="smalltext mainfont"> Profit: </p>
                                    </td>
                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php 
                                            $stmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid");
                                            $stmt->bindParam(":userid", $_SESSION["userid"]);
                                            $stmt->execute();
                                            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                            $summeProfit = 0;

                                            for ($i = 0; $i < count($row); $i++) {
                                                $summeProfit += $row[$i]["profit"];
                                            }

                                            echo $summeProfit . " €";
                                        ?> </p>
                                    </td>
                                </tr>

                                <tr> 
                                    <td> <!-- Laufende books -->
                                        <p class="smalltext mainfont"> Laufend: </p>
                                    </td>
                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php 
                                            $stmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid AND progress = 1 OR progress = 2");
                                            $stmt->bindParam(":userid", $_SESSION["userid"]);
                                            $stmt->execute();
                                            echo $stmt->rowCount();
                                        ?> </p>
                                    </td>

                                    <td> <!-- Monatslohn Profit -->
                                        <p class="smalltext mainfont"> Ausgaben: </p>
                                    </td>
                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php 

                                            $daysstmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid ORDER BY DATE ASC");
                                            $daysstmt->bindParam(":userid", $_SESSION["userid"]);
                                            $daysstmt->execute();
                
                                            $daysR = $daysstmt->fetchAll(PDO::FETCH_ASSOC);

                                            $curdate = $mysql->prepare("SELECT CURDATE()");
                                            $curdate->execute();
                                            $curdate = $curdate->fetch();

                                            $dayStart = $curdate["CURDATE()"];

                                            $dayEnd = $daysR[0]["date"];
                
                                            $mysqlDiff = $mysql->prepare("SELECT DATEDIFF(:date1, :date2);");
                                            $mysqlDiff->bindParam(":date1", $dayStart);
                                            $mysqlDiff->bindParam(":date2", $dayEnd);
                                            $mysqlDiff->execute();
                
                                            $diff = $mysqlDiff->fetchAll(PDO::FETCH_ASSOC);
                
                                            $daysPast = $diff[0]["DATEDIFF('" . $dayStart . "', '" . $dayEnd . "')"];
                
                                            $monthsPast = $daysPast / 30.436875;
                
                                            $income = $summeProfit / $monthsPast;
                
                                            echo intval($income) . " €/mo";
                                        ?> </p>
                                    </td>
                                </tr>

                                <tr> 
                                    <td> <!-- Vorbereitung books -->
                                        <p class="smalltext mainfont"> Vorbereitung: </p>
                                    </td>
                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php 
                                            $stmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid AND progress = 0");
                                            $stmt->bindParam(":userid", $_SESSION["userid"]);
                                            $stmt->execute();
                                            echo $stmt->rowCount();
                                        ?> </p>
                                    </td>

                                    <td> <!-- Monatslohn Value -->
                                        <p class="smalltext mainfont"> Monatsvalue: </p>
                                    </td>
                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php 

                                            $daysstmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid ORDER BY DATE ASC");
                                            $daysstmt->bindParam(":userid", $_SESSION["userid"]);
                                            $daysstmt->execute();
                
                                            $daysR = $daysstmt->fetchAll(PDO::FETCH_ASSOC);

                                            $curdate = $mysql->prepare("SELECT CURDATE()");
                                            $curdate->execute();
                                            $curdate = $curdate->fetch();
                                            
                                            $dayStart = $curdate["CURDATE()"];

                                            $dayEnd = $daysR[0]["date"];
                
                                            $mysqlDiff = $mysql->prepare("SELECT DATEDIFF(:date1, :date2);");
                                            $mysqlDiff->bindParam(":date1", $dayStart);
                                            $mysqlDiff->bindParam(":date2", $dayEnd);
                                            $mysqlDiff->execute();
                
                                            $diff = $mysqlDiff->fetchAll(PDO::FETCH_ASSOC);
                
                                            $daysPast = $diff[0]["DATEDIFF('" . $dayStart . "', '" . $dayEnd . "')"];
                
                                            $monthsPast = $daysPast / 30.436875;
                
                                            $income = $summeValue / $monthsPast;
                
                                            echo intval($income) . " €/mo";
                                        ?> </p>
                                    </td>
                                </tr>

                            </table>
                        </div>

                        <div class="labelNorm" style="display: inline-block; width: 25.2vw">

                            <?php randomColorLabelTitle("Encryption"); ?>

                            <table clas="adminTable" cellspacing="0">

                                <tr> 
                                    <td style="width: 30vw"> <!-- books Anzahl -->
                                        <center> <p class="smalltext mainfont"> Encryption Key Set:</p> </center>
                                    </td>
                                </tr>

                                <tr> 
                                    <td style="width: 30vw"> <!-- books Anzahl -->
                                        <center> <p class="labelHighlight smalltext mainfont"> <?php if (hasCrypter()) { echo "True"; } else { echo "False"; }?> </p> </center>
                                    </td>
                                </tr>

                                <tr> 
                                    <td style="width: 30vw"> <!-- books Anzahl -->
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
                    <?php } ?>

                </div>
                

                <!-- Notifications -->
                <div id="notis" class="labelNorm" style="display: inline-block; width: 22vw; margin-right: 0vw">

                    <?php randomColorLabelTitle("Notifications"); ?>

                    <p class="smalltext mainfont"> <i> Nothing new... </i> </a> </p>

                    <p class="smalltext mainfont"> DATE: <a class="labelHighlight"> <?php echo "aaa (WIP)"; ?> </a> </p>
                    <p class="smalltext mainfont"> DATE: <a class="labelHighlight"> <?php echo "bbb (WIP)"; ?> </a> </p>
                    <p class="smalltext mainfont"> DATE: <a class="labelHighlight"> <?php echo "ccc (WIP)"; ?> </a> </p>

                </div>
                        
                <!-- *************************************************************************************************************** -->

        <?php endPage(); ?>  
        
    </body>
</html>