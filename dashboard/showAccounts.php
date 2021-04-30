<?php
    require_once("methods.php");
    require_once("webpageBuilder.php");
    require_once("../mysql.php");


    session_start();
    verifyUser();

    if (isBooker($_SESSION["userid"]) == 0) {
        exit;
    }

    global $mysql;

    $stmt = $mysql->prepare("SELECT * FROM books WHERE refid = :refid");
    $stmt->bindParam(":refid", $_GET["refid"]);
    $stmt->execute();
    $owner = $stmt->fetch();

    $_SESSION["lastShop"] = $_GET["shop"];

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="../logo_black.svg">
        <title> <?php echo htmlspecialchars($_GET["shop"])?> / BUCH.HALTUNG</title>
    </head>
    <body>  

        <?php startPage("85vw", "showAccounts"); ?>

                <!-- *************************************************************************************************************** -->

                <?php
                    // Session User (Myself)
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();

                    // Search Shop
                    $qr = $mysql->prepare("SELECT * FROM books WHERE owner = :userid AND shop = :shop ORDER BY date DESC");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    @$qr->bindParam(":shop", encrypt($_GET["shop"]));
                    $qr->execute();
                    $ss = $qr->fetchAll(PDO::FETCH_ASSOC);

                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> <a style="text-decoration:none" href="accounts.php"> Accounts </a> - Book #<?php echo $_GET["refid"]?> - <a style="text-decoration:none" href="books.php"> Books </a> </h1> <br>
                </div>

                <!-- PHP Logic -->
                <div class="mainfont" style="margin-bottom: 1vw; margin-left: 3vw; text-align: left">
                <?php
                    if (isset($_POST["check"])) {
                        ?> <meta http-equiv="Refresh" content="0; url='showAccounts.php?<?php echo "shop=" . $_GET["shop"] . "&email=" . $_POST["email"] . "&name=" . $_POST["name"] . "&address=" . $_POST["address"] . "&payment=" . $_POST["payment"]; ?>" /> <?php
                    }

                    if (isset($_POST["gen"])) {
                        @$genName = getTypos($_POST["name"]);
                        @$genAddress = getTypos($_POST["address"]);
                        ?> <meta http-equiv="Refresh" content="0; url='showAccounts.php?<?php echo "shop=" . $_GET["shop"] . "&email=" . $_POST["email"] . "&name=" . $genName . "&address=" . $genAddress . "&payment=" . $_POST["payment"]; ?>" /> <?php
                    }

                ?>
                </div>

                <!--Account Details -->
                <div class="labelNorm" style="display: inline-block; width: 72vw; margin-left: 6vw">

                    <?php randomColorLabelTitle("Shop: " . $_GET["shop"]); ?>

                    <form action="openBook.php?refid=<?php echo $_GET["refid"]; ?>" method="post">

                        <table clas="adminTable" cellspacing="0">

                            <tr>
                                <td>
                                    <p class="smalltext mainfont"> Book ID: </p>
                                </td>

                                <td>
                                    <p class="smalltext mainfont"> Email: </p>
                                </td>

                                <td>
                                    <p class="smalltext mainfont"> Name: </p>
                                </td>

                                <td>
                                    <p class="smalltext mainfont"> Address: </p>
                                </td>

                                <td>
                                    <p class="smalltext mainfont"> Payment: </p>
                                </td>
                            </tr>

                            <?php for ($i = 0; $i < count($ss); $i++) { ?>

                                <tr>
                                    <td>
                                        <p class="smalltext mainfont"> <a class="linktext" href="openBook.php?refid=<?php echo $ss[$i]["refid"]?>"> <?php echo $ss[$i]["refid"]; ?> </a> </p>
                                    </td>

                                    <td>
                                            <?php
                                                $color = "";
                                                $count = 0;
                                                for ($g = 0; $g < count($ss); $g++) {
                                                    if ($ss[$i]["email"] == $ss[$g]["email"]) {
                                                        $count++;
                                                    }
                                                }
                                                if ($count > 3) {
                                                    $color = 'color: #C25347; text-decoration: underline';
                                                } else if ($count > 1) {
                                                    $color = 'color: #C25347';
                                                }
                                                
                                            ?>
                                        <input style="<?php echo $color; ?>" name="email" type="text" step="1" class="accountModification smalltext labelHighlight mainfont" readonly value="<?php echo htmlspecialchars(decrypt($ss[$i]["email"])); ?>"> </input>
                                    </td>

                                    <td>
                                            <?php
                                                $color = "";
                                                $count = 0;
                                                for ($g = 0; $g < count($ss); $g++) {
                                                    if ($ss[$i]["name"] == $ss[$g]["name"]) {
                                                        $count++;
                                                    }
                                                }
                                                if ($count > 3) {
                                                    $color = 'color: #C25347; text-decoration: underline';
                                                } else if ($count > 1) {
                                                    $color = 'color: #C25347';
                                                }
                                                
                                            ?>
                                        <input style="<?php echo $color; ?>" name="name" type="text" step="1" class="accountModification smalltext labelHighlight mainfont" readonly value="<?php echo htmlspecialchars(decrypt($ss[$i]["name"])); ?>"> </input>
                                    </td>

                                    <td>
                                            <?php
                                                $color = "";
                                                $count = 0;
                                                for ($g = 0; $g < count($ss); $g++) {
                                                    if ($ss[$i]["address"] == $ss[$g]["address"]) {
                                                        $count++;
                                                    }
                                                }
                                                if ($count > 3) {
                                                    $color = 'color: #C25347; text-decoration: underline';
                                                } else if ($count > 1) {
                                                    $color = 'color: #C25347';
                                                }
                                                
                                            ?>
                                        <input style="<?php echo $color; ?>" name="address" type="text" step="1" class="accountModification smalltext labelHighlight mainfont" readonly value="<?php echo htmlspecialchars(decrypt($ss[$i]["address"])); ?>"> </input>
                                    </td>

                                    <td>
                                            <?php
                                                $color = "";
                                                $count = 0;
                                                for ($g = 0; $g < count($ss); $g++) {
                                                    if ($ss[$i]["payment"] == $ss[$g]["payment"]) {
                                                        $count++;
                                                    }
                                                }
                                                if ($count > 3) {
                                                    $color = 'color: #C25347; text-decoration: underline';
                                                } else if ($count > 1) {
                                                    $color = 'color: #C25347';
                                                }
                                                
                                            ?>
                                        <input style="<?php echo $color; ?>" name="payment" type="text" step="1" class="accountModification smalltext labelHighlight mainfont" readonly value="<?php echo htmlspecialchars(decrypt($ss[$i]["payment"])); ?>"> </input>
                                    </td>
                                </tr>

                            <?php } ?>

                        </table>
                        
                    </form>

                </div>

                <!-- Check Account -->
                <div class="labelNorm" style="display: inline-block; width: 72vw; margin-left: 6vw">

                    <?php randomColorLabelTitle("Check Account"); ?>

                    <form action="showAccounts.php?shop=<?php echo $_GET["shop"]; ?>" method="post">

                        <table clas="adminTable" cellspacing="0">

                            <tr>
                                <td>
                                    <p class="smalltext mainfont"> Add Book: </p>
                                </td>

                                <td>
                                    <p class="smalltext mainfont"> Email: </p>
                                </td>

                                <td>
                                    <p class="smalltext mainfont"> Name: </p>
                                </td>

                                <td>
                                    <p class="smalltext mainfont"> Address: </p>
                                </td>

                                <td>
                                    <p class="smalltext mainfont"> Payment: </p>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <p> <a class="linktext smalltext mainfont" href="addBook.php" target="_blank" rel="noreferrer noopener"> Open Page </a> </p>
                                </td>

                                <td>
                                    <?php
                                        if (isset($_GET["email"])) {

                                            $value = $_GET["email"];
                                            $color = 'color: #4857C2';
                                            $count = 1;
                                            for ($g = 0; $g < count($ss); $g++) {
                                                if ($_GET["email"] == decrypt($ss[$g]["email"])) {
                                                    $count++;
                                                }
                                            }
                                            if ($count >= 3) {
                                                $color = 'color: #C25347; text-decoration: underline';
                                            } else if ($count > 1) {
                                                $color = 'color: #C25347';
                                            }
                                        } else {
                                            $color = 'color: #4857C2';
                                            $value = "";
                                        }
                                    ?>

                                    <input style="<?php echo $color; ?>" name="email" type="text" class="accountModification smalltext labelHighlight mainfont" value="<?php echo $value?>"> </input>
                                </td>

                                <td>
                                    <?php
                                        if (isset($_GET["name"])) {

                                            $value = $_GET["name"];
                                            $color = 'color: #4857C2';
                                            $count = 1;
                                            for ($g = 0; $g < count($ss); $g++) {
                                                if ($_GET["name"] == decrypt($ss[$g]["name"])) {
                                                    $count++;
                                                }
                                            }
                                            if ($count >= 3) {
                                                $color = 'color: #C25347; text-decoration: underline';
                                            } else if ($count > 1) {
                                                $color = 'color: #C25347';
                                            }
                                        } else {
                                            $color = 'color: #4857C2';
                                            $value = "";
                                        }
                                    ?>

                                    <input style="<?php echo $color; ?>" name="name" type="text" class="accountModification smalltext labelHighlight mainfont" value="<?php echo $value?>"> </input>
                                </td>

                                <td>

                                    <?php
                                        if (isset($_GET["address"])) {

                                            $value = $_GET["address"];
                                            $color = 'color: #4857C2';
                                            $count = 1;
                                            for ($g = 0; $g < count($ss); $g++) {
                                                if ($_GET["address"] == decrypt($ss[$g]["address"])) {
                                                    $count++;
                                                }
                                            }
                                            if ($count >= 3) {
                                                $color = 'color: #C25347; text-decoration: underline';
                                            } else if ($count > 1) {
                                                $color = 'color: #C25347';
                                            }
                                        } else {
                                            $color = 'color: #4857C2';
                                            $value = "";
                                        }
                                    ?>

                                    <input style="<?php echo $color; ?>" name="address" type="text" class="accountModification smalltext labelHighlight mainfont" value="<?php echo $value?>"> </input>
                                </td>

                                <td>
                                    <?php
                                        if (isset($_GET["payment"])) {

                                            $value = $_GET["payment"];
                                            $color = 'color: #4857C2';
                                            $count = 1;
                                            for ($g = 0; $g < count($ss); $g++) {
                                                if ($_GET["payment"] == decrypt($ss[$g]["payment"])) {
                                                    $count++;
                                                }
                                            }
                                            if ($count > 3) {
                                                $color = 'color: #C25347; text-decoration: underline';
                                            } else if ($count > 1) {
                                                $color = 'color: #C25347';
                                            }
                                        } else {
                                            $color = 'color: #4857C2';
                                            $value = "";
                                        }
                                    ?>

                                    <input style="<?php echo $color; ?>" name="payment" type="text" class="accountModification smalltext labelHighlight mainfont" value="<?php echo $value?>"> </input>
                                </td>
                            </tr>

                        </table>

                        <div style="width: 50.4vw; display: inline-block; float: left">
                            <p class="mainfont"> <button type="submit" style="margin-top: 1vw" class="newbutton adminsubmit smalltext mainfont" id="check" name="check"> Validate </button> </p>
                        </div>

                        <div style="width: 20vw; display: inline-block">
                            <p class="mainfont"> <button type="submit" style="margin-top: 1vw" class="newbutton adminsubmit smalltext mainfont" id="gen" name="gen"> Generate </button> </p>
                        </div>
                    
                    </form>
                </div>
                        
                <!-- *************************************************************************************************************** -->

        <?php endPage(); ?>  
        
    </body>
</html>