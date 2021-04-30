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
        <title> Accounts / bats.li </title>
    </head>
    <body>  

        <?php startPage("75vw", "accounts"); ?>

                <!-- *************************************************************************************************************** -->

                <?php
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();
                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> Accounts </a> </h1> <br>
                </div>

                <div style="margin-left: 10vw">

                <?php 

                $stmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid AND method != :service ORDER BY date DESC");
                $stmt->bindParam(":userid", $_SESSION["userid"]);
                $cryptService = encrypt("Service");
                $stmt->bindParam(":service", $cryptService);
                $stmt->execute();
                $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $shops = "";

                for ($i = 0; $i < count($row); $i++) {


                    $splitSymbol = "))((";
                    $shops_splitted = preg_split("/(['" . $splitSymbol . "'])/", $shops, -1, PREG_SPLIT_NO_EMPTY);

                    // dupe logic

                    $found = false;

                    for ($k = 0; $k < count($shops_splitted); $k++) {
                        if ($shops_splitted[$k] == $row[$i]["shop"]) {
                            $k = 100000;
                            $found = true;
                        }
                    }

                    // display accounts for shop if not already shown before

                    if ($found == false) {
                        displayAccounts($row[$i]["shop"]);


                        $shops = $shops . $row[$i]["shop"] . $splitSymbol;
                    } 

                }

                ?>

                </div>

                <?php
                    
    
                ?>
                        
                <!-- *************************************************************************************************************** -->

        <?php endPage(); ?>  
        
    </body>
</html>