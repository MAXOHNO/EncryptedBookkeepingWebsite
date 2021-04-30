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

    if ($owner["owner"] != $_SESSION["userid"] ) {
        ?> <p> Book ID <?php echo $_GET["refid"]; ?> does not exist, has been deleted or not enough permission. <a href="books.php"> Go Back? </a> </p> <?php
        header("Location: books.php");
        exit;
    }

    $_SESSION["lastRefID"] = $_GET["refid"];

?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" type="image/x-icon" href="../logo_black.svg">
        <title> #<?php echo $_GET["refid"]?> / BUCH.HALTUNG</title>
    </head>
    <body>  

        <?php startPage("85vw", "openBook"); ?>

                <!-- *************************************************************************************************************** -->

                <?php
                    // Session User (Myself)
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();

                    // Search User
                    $qr = $mysql->prepare("SELECT * FROM books WHERE refid = :refid");
                    $qr->bindParam(":refid", $_GET["refid"]);
                    $qr->execute();
                    $ss = $qr->fetch();

                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> <a style="text-decoration:none" href="books.php"> books </a> - Book #<?php echo $_GET["refid"]?> - <a style="text-decoration:none" href="accounts.php"> Accounts </a> </h1> <br>
                </div>

                <!-- PHP Logic -->
                <div class="mainfont" style="margin-bottom: 1vw; margin-left: 3vw; text-align: left">
                <?php

                    if (isset($_POST["editref"])) {

                        if (!hasCrypter()) {
                            output("No Encryption Key Set. <a href='encryption.php'> Please set a Encryption Key </a>.", "ERROR");
                        } else {
                            $stmt = $mysql->prepare("UPDATE books SET progress = :progress, date = :date, shop = :shop, method = :method, todo = :todo, email = :email, note = :note, ftid = :ftid, item = :item, name = :name, address = :address, payment = :payment, value = :value, profit = :profit WHERE refid = :refid");
                            $temp = "bg0hg0zb";
    
                            $stmt->bindParam(":refid", $_GET["refid"]);
    
                            $stmt->bindParam(":date", $_POST["date"]);
                            @$stmt->bindParam(":shop", encrypt($_POST["shop"]));
                            @$stmt->bindParam(":method", encrypt($_POST["method"]));
    
                            $progress = ProgressToInt($_POST["progress"]);
                            $stmt->bindParam(":progress", $progress);
    
                            
    
                            @$stmt->bindParam(":todo", encrypt($_POST["todo"]));
                            @$stmt->bindParam(":note", encrypt($_POST["note"]));
                            @$stmt->bindParam(":ftid", encrypt($_POST["ftid"]));
    
                            @$stmt->bindParam(":email", encrypt($_POST["email"]));
                            @$stmt->bindParam(":name", encrypt($_POST["name"]));
                            @$stmt->bindParam(":address", encrypt($_POST["address"]));
                            @$stmt->bindParam(":payment", encrypt($_POST["payment"]));
    
                            @$stmt->bindParam(":item", encrypt($_POST["item"]));
                            $stmt->bindParam(":value", $_POST["value"]);
                            $stmt->bindParam(":profit", $_POST["profit"]);
    
                            $stmt->execute();
    
                            addlog("SYSTEM", $_SESSION["userid"], "BOOK EDITED", $_GET["refid"]);
    
                            echo "<meta http-equiv='refresh' content='0'>";
                        }

                        
                    }

                    if (isset($_POST["removeref"])) {
                        if ($_POST["refidconfirm"] == $_GET["refid"]) {
                            $stmt = $mysql->prepare("DELETE FROM books WHERE refid = :refid");
                            $stmt->bindParam(":refid", $_GET["refid"]);
                            $stmt->execute();

                            addlog("SYSTEM", $_SESSION["userid"], "BOOK REMOVED", $_GET["refid"]);

                            echo "<meta http-equiv='refresh' content='0'>";
                        } else {
                            output("Book ID does not match.", "ERROR");
                        }
                    }

                ?>
                </div>

                <!-- User Profile -->
                <div class="labelNorm" style="display: inline-block; width: 35vw; margin-left: 25vw">

                    <?php BookColorTitle($ss["progress"], "Book"); ?>

                    <form action="openBook.php?refid=<?php echo $_GET["refid"]; ?>" method="post">

                        <table clas="adminTable" cellspacing="0">

                            <tr> <!-- RefID -->
                                <td>
                                    <p class="smalltext mainfont"> Book ID: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont">#<?php echo $ss["refid"]; ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Owner -->
                                <td>
                                    <p class="smalltext mainfont"> Owner: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo $ss["owner"]; ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Date -->
                                <td>
                                    <p style="margin-right: 1vw" class="smalltext mainfont"> Date: </p>
                                </td>

                                <td>
                                    <input maxlength="120" name="date" type="date" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo $ss["date"]; ?>"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Shop -->
                                <td>
                                    <p class="smalltext mainfont"> Shop: </p>
                                </td>

                                <td>
                                    <input maxlength="120" name="shop" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["shop"])); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Method -->
                                <td>
                                    <p class="smalltext mainfont"> Method: </p>
                                </td>

                                <td>
                                    <input maxlength="120" name="method" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["method"])); ?>"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Progress -->
                                <td>
                                    <p class="smalltext mainfont"> Progress: </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="progress" list="progress" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo IntToProgress($ss["progress"]); ?>"> </input>

                                   <datalist id="progress">
                                        <option value="<?php echo IntToProgress(0);?>">
                                        <option value="<?php echo IntToProgress(1);?>">
                                        <option value="<?php echo IntToProgress(2);?>">
                                        <option value="<?php echo IntToProgress(3);?>">
                                        <option value="<?php echo IntToProgress(4);?>">
                                        <option value="<?php echo IntToProgress(5);?>">
                                    </datalist>
                                </td>
                            </tr>

                            <tr> <!-- Spacer -->
                                <td>
                                    <p> </p>
                                </td>
                            </tr>

                            <tr> <!-- Note -->
                                <td>
                                    <p class="smalltext mainfont"> Note: </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="note" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["note"])); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- ToDo -->
                                <td>
                                    <p class="smalltext mainfont"> Todo: </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="todo" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["todo"])); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- FTID -->
                                <td>
                                    <p class="smalltext mainfont"> F/TID: </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="ftid" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["ftid"])); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Spacer -->
                                <td>
                                    <p> </p>
                                </td>
                            </tr>

                            <tr> <!-- Name -->
                                <td>
                                    <p class="smalltext mainfont"> <a style="text-decoration: none;" href="showAccounts.php?shop=<?php echo $ss["shop"] ?>""> Name: </a> </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="name" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["name"])); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Email -->
                                <td>
                                    <p class="smalltext mainfont"> <a style="text-decoration: none;" href="showAccounts.php?shop=<?php echo $ss["shop"] ?>""> Email: </a> </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="email" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["email"])); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Address -->
                                <td>
                                    <p class="smalltext mainfont"> <a style="text-decoration: none;" href="showAccounts.php?shop=<?php echo $ss["shop"] ?>""> Address: </a> </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="address" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["address"])); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Payment -->
                                <td>
                                    <p class="smalltext mainfont"> <a style="text-decoration: none;" href="showAccounts.php?shop=<?php echo $ss["shop"] ?>""> Payment: </a> </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="payment" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["payment"])); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Spacer -->
                                <td>
                                    <p> </p>
                                </td>
                            </tr>

                            <tr> <!-- Item -->
                                <td>
                                    <p class="smalltext mainfont"> Item: </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="item" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars(decrypt($ss["item"])); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Value -->
                                <td>
                                    <p class="smalltext mainfont"> Value: </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="value" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($ss["value"]); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Profit -->
                                <td>
                                    <p class="smalltext mainfont"> Profit: </p>
                                </td>

                                <td>
                                   <input maxlength="120" name="profit" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($ss["profit"]); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Spacer -->
                                <td>
                                    <p> </p>
                                </td>
                            </tr>

                        </table>

                        <p class="mainfont"> <button type="submit" style="margin-top: 1vw" class="newbutton adminsubmit smalltext mainfont" id="editbook" name="editref"> Edit Book </button> </p>
                        
                    </form>

                </div>

                <!-- Remove Book -->
                <div class="labelNorm" style="display: inline-block; width: 35vw; margin-left: 25vw">

                    <?php randomColorLabelTitle("Remove Book"); ?>

                    <form action="openBook?refid=<?php echo $_GET["refid"]; ?>" method="post">
                        <p class="smalltext mainfont"> Book ID: <input class="adminModification smalltext labelHighlight mainfont" type="text" name="refidconfirm" placeholder="Enter Book ID for Confirmation" /> </p>

                        <p class="mainfont"> <button type="submit" style="margin-top: 1vw" class="newbutton adminsubmit smalltext mainfont" id="removebook" name="removeref"> Remove Book </button> </p>
                    </form>
                </div>
                        
                <!-- *************************************************************************************************************** -->

        <?php endPage(); ?>  
        
    </body>
</html>