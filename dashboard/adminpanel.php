<?php
    require_once("methods.php");
    require_once("webpageBuilder.php");
    require_once("../mysql.php");


    session_start();
    verifyUser();

    if (getRole($_SESSION["userid"]) < 2) {
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
        <title> Adminpanel / bats.li</title>
    </head>
    <body>  

        <?php startPage("85.2vw", "adminpanel"); ?>

                <!-- *************************************************************************************************************** -->

                <!-- Searching Logic -->
                <?php
                    // Session User (Myself)
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
                    $qr->bindParam(":userid", $_SESSION["userid"]);
                    $qr->execute();
                    $rs = $qr->fetch();

                    // Search User
                    $qr = $mysql->prepare("SELECT * FROM accounts WHERE username = :user");
                    $qr->bindParam(":user", $_GET["user"]);
                    $qr->execute();
                    $count = $qr->rowCount();

                    if ($count != 1) { // Falls Search User nicht durch username gefunden wurde, wird nach userid gesucht
                        $qr = $mysql->prepare("SELECT * FROM accounts WHERE userid = :user");
                        $qr->bindParam(":user", $_GET["user"]);
                        $qr->execute();
                        $ss = $qr->fetch();
                    } else { // Search User wurde durch username gefunden, wird in $ss übergeben
                        $ss = $qr->fetch();
                    }

                ?>

                <!-- Dashboard Header -->
                <div class="labelHeader">
                    <h1 class="mainfont" style="text-align: left"> Adminpanel </h1> <br>
                </div>

                <!-- PHP Logic -->
                <div class="mainfont" style="margin-bottom: 1vw; margin-left: 3vw; text-align: left">
                <?php
                    if (isset($_POST["modify"])) {

                        if (strlen($_GET["user"]) > 32) {
                            $stmt = $mysql->prepare("UPDATE accounts SET role = :role, Booker = :Booker, balance = :balance, telegram = :telegram, seller = :seller WHERE userid = :user");
                            $uid = $_GET["user"];
                        } else {
                            $stmt = $mysql->prepare("UPDATE accounts SET role = :role, Booker = :Booker, balance = :balance, telegram = :telegram, seller = :seller WHERE username = :user");
                            $uid = getUserID($_GET["user"]);
                        }

                        $stmt->bindParam(":user", $_GET["user"]);

                        $perms = $_POST["role"];
                        if ($perms > getRole($_SESSION["userid"])) {
                            $perms = getRole($_SESSION["userid"]);
                        }

                        $stmt->bindParam(":role", $perms);
                        $stmt->bindParam(":Booker", $_POST["Booker"]);
                        $stmt->bindParam(":seller", $_POST["seller"]);
                        $stmt->bindParam(":balance", $_POST["balance"]);
                        $stmt->bindParam(":telegram", $_POST["telegram"]);
                        $stmt->execute();

                        addlog($_SESSION["userid"], $uid, "UPDATED ACCOUNT");

                        echo "<meta http-equiv='refresh' content='0'>";
                    }

                    if (isset($_POST["genkey"])) {

                            ?> <a class="mainfont"> Generated Keys Keys: </a> <br> <?php

                        for ($i = 0; $i < $_POST["amount"]; $i++) {

                            $stmt = $mysql->prepare("INSERT INTO serialkeys (skey, Booker, creator) VALUES (:serialkey, :Booker, :creator)");
                            $key = genSerialKey();
                            $stmt->bindParam(":serialkey", $key);
                            $Booker = 1;
                            $stmt->bindParam(":Booker", $Booker);
                            $stmt->bindParam(":creator", $_SESSION["userid"]);
                            $stmt->execute();

                            addlog("SYSTEM", $_SESSION["userid"], "KEY GENERATED", $key);

                            ?> <a class="mainfont"> <?php echo $key; ?> </a> <br> <?php
                        }
                    }

                    if (isset($_POST["findkey"])) {

                        ?> <a class="mainfont"> Found Book Keys: </a> <br> <?php

                            $stmt = $mysql->prepare("SELECT * FROM serialkeys WHERE claimer = ''");
                            $stmt->execute();
                            $row = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            for ($i = 0; $i < $_POST["amountFinder"]; $i++) {

                                if (isset($row[$i])) {
                                    ?> <a class="mainfont"> <?php echo $row[$i]["skey"]; ?> </a> <br> <?php
                                }
                                
                            }
                    }
                ?>
                </div>

                <!-- Div Container -->
                <div style="display: inline-block; width: 33vw; float: left; margin-left: 3vw"> 

                    <!-- Search User -->
                    <div class="labelNorm" style="display: inline-block; width: 33vw">

                        <?php randomColorLabelTitle("Search Engine"); ?>

                        <form action="adminpanel.php">

                            <table clas="adminTable" cellspacing="0">
                                <tr>
                                    <td>
                                        <p class="smalltext mainfont"> Username/UUID: </p>
                                    </td>
                                    <td>
                                        <input style="width: 20vw" class="adminModification smalltext labelHighlight mainfont" type="text" value="<?php echo $_GET["user"];?>" name="user" />
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <p class="smalltext mainfont"> Serialkey: </p>
                                    </td>
                                    <td>
                                        <?php 
                                            if (isset($_GET["key"])) {
                                                $keyValue = $_GET["key"];
                                            } else {
                                                $keyValue = "";
                                            }
                                        ?>
                                        <input style="width: 20vw" class="adminModification smalltext labelHighlight mainfont" type="text" value="<?php echo $keyValue;?>" name="key" />
                                    </td>
                                </tr>
                            

                            </table>

                            <p class="smalltext mainfont"> <input class="newbutton adminsubmit" type="submit" style="width: 30vw; margin-top: 1vw" value="search" /> </p>
                        </form>
                    </div>


                    <!-- Research Serialkey -->
                    <?php
                        $stmt = $mysql->prepare("SELECT * FROM serialkeys where skey = :key");
                        $stmt->bindParam(":key", $_GET["key"]);
                        $stmt->execute();

                        $sk = $stmt->fetch();
                    ?>
                    <div class="labelNorm" style="display: inline-block; width: 33vw">

                        <?php randomColorLabelTitle("Research Serialkey"); ?>

                        <form action="adminpanel.php?user=<?php echo htmlspecialchars($_GET["user"]); ?>" method="post">

                            <table clas="adminTable" cellspacing="0">

                                <tr> <!-- serialkey -->
                                    <td>
                                        <p class="smalltext mainfont"> Serialkey: </p>
                                    </td>

                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($sk["skey"]); ?> </p>
                                    </td>
                                </tr>

                                <tr> <!-- creator -->
                                    <td>
                                        <p class="smalltext mainfont"> Creator: </p>
                                    </td>

                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars(getUsername($sk["creator"])); ?> </p>
                                    </td>
                                </tr>

                                <tr> <!-- creationDate -->
                                    <td>
                                        <p class="smalltext mainfont"> Created: </p>
                                    </td>

                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($sk["creationDate"]); ?> </p>
                                    </td>
                                </tr>

                                <tr> <!-- Spacer -->
                                    <td>
                                        <p> </p>
                                    </td>
                                </tr>

                                <tr> <!-- claimDate -->
                                    <td>
                                        <p class="smalltext mainfont"> Claimed: </p>
                                    </td>

                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($sk["claimDate"]); ?> </p>
                                    </td>
                                </tr>

                                <tr> <!-- claimer -->
                                    <td>
                                        <p class="smalltext mainfont"> Claimer: </p>
                                    </td>

                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars(getUsername($sk["claimer"])); ?> </p>
                                    </td>
                                </tr>

                                <tr> <!-- Booker -->
                                    <td>
                                        <p class="smalltext mainfont"> Booker: </p>
                                    </td>

                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($sk["Booker"]); ?> </p>
                                    </td>
                                </tr>

                                <tr> <!-- role -->
                                    <td>
                                        <p class="smalltext mainfont"> Perms: </p>
                                    </td>

                                    <td>
                                        <p class="labelHighlight smalltext mainfont"> <?php //echo htmlspecialchars($sk["role"]); ?> </p>
                                    </td>
                                </tr>


                            </table>
                        </form>
                    </div>

                </div>

                <!-- Profile for XXXX -->
                <div class="labelNorm" style="display: inline-block; width: 45vw; margin-left: 2vw;">

                    <?php randomColorLabelTitle("Profile for " . getUsername($_GET["user"])); ?>

                    <form action="adminpanel.php?user=<?php echo htmlspecialchars($_GET["user"]); ?>" method="post">

                        <table clas="adminTable" cellspacing="0">

                            <tr> <!-- UUID -->
                                <td>
                                    <p class="smalltext mainfont"> UUID: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($ss["userid"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Username -->
                                <td>
                                    <p class="smalltext mainfont"> Username: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($ss["username"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Permissions -->
                                <td>
                                    <p style="margin-right: 1vw" class="smalltext mainfont"> Permissions: </p>
                                </td>

                                <td>
                                    <input name="role" type="number" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($ss["role"]); ?>"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Booker -->
                                <td>
                                    <p class="smalltext mainfont"> Booker: </p>
                                </td>

                                <td>
                                    <input name="Booker" type="number" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($ss["Booker"]); ?>"> </input>
                                </td>
                            </tr>

                            <tr> <!-- Seller -->
                                <td>
                                    <p class="smalltext mainfont"> Seller: </p>
                                </td>

                                <td>
                                    <input name="seller" type="number" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($ss["seller"]); ?>"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Telegram -->
                                <td>
                                    <p class="smalltext mainfont"> Telegram: </p>
                                </td>

                                <td>
                                   <input name="telegram" type="text" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($ss["telegram"]); ?>"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Email -->
                                <td>
                                    <p class="smalltext mainfont"> Email: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($ss["email"]); ?> </p>
                                </td>
                            </tr>
                            
                            <tr> <!-- Joined -->
                                <td>
                                    <p class="smalltext mainfont"> Joined: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($ss["joined"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Spacer -->
                                <td>
                                    <p> </p>
                                </td>
                            </tr>
                            
                            <tr> <!-- Deposited -->
                                <td>
                                    <p class="smalltext mainfont"> Deposited: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($ss["deposited"]); ?> </p>
                                </td>
                            </tr>
                            
                            <tr> <!-- Balance -->
                                <td>
                                    <p class="smalltext mainfont"> Balance: </p>
                                </td>

                                <td>
                                    <input name="balance" type="number" step="1" class="adminModification smalltext labelHighlight mainfont" value="<?php echo htmlspecialchars($ss["balance"]); ?>"> </input>
                                </td>
                            </tr>
                            
                            <tr> <!-- Deposits -->
                                <td>
                                    <p class="smalltext mainfont"> Deposits: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($ss["deposits"]); ?> </p>
                                </td>
                            </tr>
                            
                            <tr> <!-- Tickets -->
                                <td>
                                    <p class="smalltext mainfont"> Tickets: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($ss["tickets"]); ?> </p>
                                </td>
                            </tr>

                            <tr> <!-- Orders -->
                                <td>
                                    <p class="smalltext mainfont"> Orders: </p>
                                </td>

                                <td>
                                    <p class="labelHighlight smalltext mainfont"> <?php echo htmlspecialchars($ss["orders"]); ?> </p>
                                </td>
                            </tr>

                        </table>

                        <p class="mainfont"> <button style="margin-top: 1vw" type="submit" class="newbutton adminsubmit smalltext mainfont" id="modify" name="modify"> Save </button> </p>
                        
                    </form>

                </div>

                <!-- Container: Serialkey Generator & Finder -->
                <div style="display: inline-block; width: 85vw; float: left; margin-left: 3vw"> 

                    <!-- Serialkey Generator -->
                    <div class="labelNorm" style="display: inline-block; width: 39vw">

                        <?php randomColorLabelTitle("Serialkey Generator"); ?>

                        <form action="adminpanel.php?user=<?php echo htmlspecialchars($_GET["user"]); ?>" method="post">

                            <table clas="adminTable" cellspacing="0">

                                <tr> <!-- Booker -->
                                    <td>
                                        <p class="smalltext mainfont"> Amount: </p>
                                    </td>

                                    <td>
                                        <input name="amount" type="number" step="1" max="50" min="1" style="width: 10vw" class="adminModification smalltext labelHighlight mainfont" > </input>
                                    </td>

                                    <td>
                                        <p class="mainfont"> <button style="width: 17.5vw" type="submit" class="newbutton adminsubmit smalltext mainfont" id="genkey" name="genkey"> Generate </button> </p>
                                    </td>
                                </tr>

                            </table>
                        </form>
                    </div>

                        <!-- Serialkey Finder -->
                    <div class="labelNorm" style="display: inline-block; width: 39vw">

                        <?php randomColorLabelTitle("Serialkey Finder"); ?>

                        <form action="adminpanel.php?user=<?php echo $_GET["user"]; ?>&key=<?php echo $_GET["key"]; ?>" method="post">

                            <table clas="adminTable" cellspacing="0">

                                <tr> <!-- Booker -->
                                    <td>
                                        <p class="smalltext mainfont"> Amount: </p>
                                    </td>

                                    <td>
                                        <input name="amountFinder" type="number" step="1" min="1" style="width: 10vw" class="adminModification smalltext labelHighlight mainfont" > </input>
                                    </td>

                                    <td>
                                        <p class="mainfont"> <button style="width: 17.5vw" type="submit" class="newbutton adminsubmit smalltext mainfont" id="findkey" name="findkey"> Find </button> </p>
                                    </td>
                                </tr>

                            </table>
                        </form>
                    </div>
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