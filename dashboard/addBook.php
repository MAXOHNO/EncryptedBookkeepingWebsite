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
        <title> Add Book / bats.li</title>
    </head>
    <body>  

        <?php startPage("85vw", "addBook"); ?>

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
                    <h1 class="mainfont" style="text-align: left"> <a class="linktext" href="books.php"> Go back? </a> - Add Book </h1> <br>
                </div>

                <!-- PHP Logic -->
                <div class="mainfont" style="margin-bottom: 1vw; margin-left: 3vw; text-align: left">
                <?php
                
                    if (isset($_POST["addRef"])) {

                        if (!hasCrypter()) {
                            output("No Encryption Key is set. <a href='encryption' class='mediumtext mainfont'> Please set a Encryption Key</a>.", "ERROR");
                        } else {

                            $found = false;
                            $refid = "";
                            while ($found == false) {
                                $refid = getRandomString(8);
                                $stmt = $mysql->prepare("SELECT * FROM books WHERE refid = :refid");
                                $stmt->bindParam(":refid", $refid);
                                $stmt->execute();
                                $count = $stmt->rowCount();
                                if ($count == 0) {
                                    $found = true;
                                }
                            }

                            //$stmt = $mysql->prepare("INSERT INTO books (refid, owner, date, shop, progress, method, item, value, profit, name, email, address, payment) VALUES (:refid, :owner, :date, :shop, :progress, :method, :item, :value, :profit, :name, :email, :address, :payment)");
                            $stmt = $mysql->prepare("INSERT INTO books (refid, owner, date, shop, method, progress, todo, item, value, profit, note, email, name, address, payment) VALUES (:refid, :owner, :date, :shop, :method, :progress, :todo, :item, :value, :profit, :note, :email, :name, :address, :payment)");
                            $stmt->bindParam(":refid", $refid);
                            $stmt->bindParam(":owner", $_SESSION["userid"]);


                            $stmt->bindParam(":date", $_POST["date"]);
                            @$stmt->bindParam(":shop", encrypt($_POST["shop"]));
                                $progInt = ProgressToInt($_POST["progress"]);
                            $stmt->bindParam(":progress", $progInt);
                            @$stmt->bindParam(":method", encrypt($_POST["method"]));

                            @$stmt->bindParam(":todo", encrypt($_POST["todo"]));
                            @$stmt->bindParam(":note", encrypt($_POST["note"]));

                            @$stmt->bindParam(":name", encrypt($_POST["name"]));
                            @$stmt->bindParam(":email", encrypt($_POST["email"]));
                            @$stmt->bindParam(":address", encrypt($_POST["address"]));
                            @$stmt->bindParam(":payment", encrypt($_POST["payment"]));


                            @$stmt->bindParam(":item", encrypt($_POST["item"]));
                            $stmt->bindParam(":value", $_POST["value"]);
                            $stmt->bindParam(":profit", $_POST["profit"]);
                            @$stmt->execute();

                            addlog("SYSTEM", $_SESSION["userid"], "BOOK ADDED", $refid);

                            ?> <meta http-equiv="Refresh" content="0; url='openBook.php?refid=<?php echo $refid; ?>'" /> <?php

                        }
                    }

                ?>
                </div>

                <!-- User Profile -->
                <div class="labelNorm" style="display: inline-block; width: 35vw; margin-left: 25vw">

                    <?php randomColorLabelTitle("Profile"); ?>

                    <form action="addBook.php" method="post">

                        <table clas="adminTable" cellspacing="0">

                            <tr> <!-- Date -->
                                <td>
                                    <p class="smalltext mainfont"> Date: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="date" type="date" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo date('Y-m-d'); ?>"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Shop -->
                                <td>
                                    <p class="smalltext mainfont"> Shop: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="shop" type="text" step="1" class="adminModification smalltext labelHighlight mainfont"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Method -->
                                <td>
                                    <p class="smalltext mainfont"> Method: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="method" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
                                </td>
                            </tr>

                            <tr> <!-- Progress -->
                                <td>
                                    <p class="smalltext mainfont"> Progress: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="progress" list="progress" class="adminModification smalltext labelHighlight mainfont" > </input>

                                    <datalist id="progress">
                                        <select name ="progressSelect">
                                            <option value="<?php echo IntToProgress(0);?>">
                                            <option value="<?php echo IntToProgress(1);?>">
                                            <option value="<?php echo IntToProgress(2);?>">
                                            <option value="<?php echo IntToProgress(3);?>">
                                            <option value="<?php echo IntToProgress(4);?>">
                                            <option value="<?php echo IntToProgress(5);?>">
                                        </select>
                                    </datalist>
                                </td>
                            </tr>

                            <tr> <!-- Spacer -->
                                <td>
                                    <p> </p>
                                </td>
                            </tr>

                            <tr> <!-- Method -->
                                <td>
                                    <p class="smalltext mainfont"> Todo: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="note" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
                                </td>
                            </tr>

                            <tr> <!-- Method -->
                                <td>
                                    <p class="smalltext mainfont"> Note: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="todo" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
                                </td>
                            </tr>

                            <tr> <!-- Spacer -->
                                <td>
                                    <p> </p>
                                </td>
                            </tr>

                            <tr> <!-- Item -->
                                <td>
                                    <p class="smalltext mainfont"> Name: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="name" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
                                </td>
                            </tr>

                            <tr> <!-- Item -->
                                <td>
                                    <p class="smalltext mainfont"> Email: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="email" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
                                </td>
                            </tr>

                            <tr> <!-- Item -->
                                <td>
                                    <p class="smalltext mainfont"> Address: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="address" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
                                </td>
                            </tr>

                            <tr> <!-- Item -->
                                <td>
                                    <p class="smalltext mainfont"> Payment: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="payment" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
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
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="item" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
                                </td>
                            </tr>

                            <tr> <!-- Value -->
                                <td>
                                    <p class="smalltext mainfont"> Value: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="value" type="number" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
                                </td>
                            </tr>

                            <tr> <!-- Profit -->
                                <td>
                                    <p class="smalltext mainfont"> Profit: </p>
                                </td>

                                <td>
                                    <a class="smalltext labelHighlight mainfont"> </a> <input maxlength="120" name="profit" type="number" step="1" class="adminModification smalltext labelHighlight mainfont" > </input>
                                </td>
                            </tr>

                        </table>

                        <p> <button style="width: 33vw" type="submit" class="newbutton smalltext button" id="addRef" name="addRef"> Add Book </button> </p>
                        
                    </form>

                </div>

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