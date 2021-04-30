<?php
    require("../mysql.php");
    require_once("methods.php");

    function startPage($width = "85vw", $current = "none") {

        $showWarning = false;
        if (!(hasCrypter()) && isBooker($_SESSION["userid"])) {
            $showWarning = true;
        }
        if (isset($_SESSION["encwarn"])) {
            if ($_SESSION["encwarn"] == "hide") {
                $showWarning = false;
            }
        }

        ?>
            <table cellspacing="0">
            <?php
                if ($showWarning) {
                    ?>
                    <tr>
                        <th class="sidenavtop"> </th>

                        <th colspan="4" style="width: 200vw; margin: 0; padding: 0">
                            <div id="encrypt_warning" style="background-color: red; color: white"> 
                                <a style="padding-left: 15vw;" class="smalltext mainfont"> WARNING: No Encryption Key Set. <a> 
                                <a style="color: blue" class="linktext smalltext mainfont" href="encryption.php"> Please set one here.</a> 
                                <button style="margin-left: 56vw; height: 1.5vw" class="smalltext mainfont" onclick="close_Warning(true)"> X </button>
                            </div>
                        </th>
                    </tr>
                <?php } ?>

                <tr>
                    <th style="height: 50vw" class="sidenavtop" >
                        <center> <a href="../"> <img style="margin-top: 0.8vw" src="../logo_white.png" alt="logo" width="60%"> </a> </center>
                    </th>

                    <th class="navtop" style="height: 5vw">
                        <a> <?php drawNavtop( $_SESSION["userid"] ); ?> </a>
                    </th>
                </tr>
                    <th class="sidebar">
                        <div style="overflow-y: scroll; height: 85%; top: 0%; bottom: 0%">
                            <?php drawSidebar($_SESSION["userid"], $current); ?>
                        </div>
                    </th>

                    <th class="main">

                    <div style="height: 0.1vw; width: 14vw; display: inline-block; float: left"> aaaaa </div> 

                    <div style="width: <?php echo $width?>; display: block; float: left; margin-top: 1vw;"> 
        <?php
    }

    function endPage() {
        ?>

                        </div> 
                                 
                    </th>
                <tr>

                </tr>
            </table>
        <?php
    }

    function drawNavtop($userid) {
        ?>
            <table>
                <tr>
                    <th>
                        <a class="mainfont"> <?php echo "Welcome " . getUsername($userid) . "!"; ?> </a>
                    </th>

                    <th>
                        <a style="margin-left: 64vw" class="mainfont" style="color: #D2D2D2; text-decoration: none" href="logout.php"> Logout </a>
                    </th>
                </tr>
            </table>
        <?php
    }

    function drawSidebar($userid, $current = "none") {
        ?>
            <ul class="mainfont" style="font-size: 1.2vw">
            
                <?php
                // Admin
                    if (getRole($userid) >= 2) {
                        ?>
                            <li class="sidetitle"> <a class="flatred"> Admin </a> </li>
                            <li class="sidetext"> <a> <img style="height: 1vw" src="img/adminpower.png"> Ticketpanel (WIP)</li>
                            <li class="sidetext"> <a <?php if ($current == "adminpanel") { echo "id='current'"; }?> href="adminpanel.php"> <img style="height: 1vw" src="img/adminpower.png"> Adminpanel</a> </li>
                            <li class="sidetext"> <a <?php if ($current == "adminlog") { echo "id='current'"; }?> href="adminlog.php" ><img style="height: 1vw" src="img/adminpower.png"> Adminlog</li>
                            <br>
                        <?php
                    }
                ?>

                <!-- Personal -->
                <li class="sidetitle"> <a class="flatred"> Personal</a> </li>
                <li class="sidetext"> <a <?php if ($current == "account") { echo "id='current'"; }?> href="account.php"> <img style="height: 1vw" src="img/account.png"> My Account</a> </li>
                <li class="sidetext"> <a <?php if ($current == "index") { echo "id='current'"; }?> href="../dashboard"> <img style="height: 1vw" src="img/dashboard.png"> Dashboard </a> </li>
                <li class="sidetext"> <a> <img style="height: 1vw" src="img/deposit.png"> Deposit (WIP)</a> </li>
                <li class="sidetext"> <a> <img style="height: 1vw" src="img/tickets.png"> Tickets (WIP)</a> </li>
                <br>

                <?php if (isBooker($userid)) {
                    ?>
                        <li class="sidetitle"> <a class="flatred"> Booking </a> </li>
                        <li class="sidetext"> <a <?php if ($current == "encryption") { echo "id='current'"; }?> href="encryption.php"> <img style="height: 1vw" src="img/encryption.png"> Encryption</a> </li>
                        <li class="sidetext"> <a <?php if ($current == "accounts") { echo "id='current'"; }?> href="accounts.php"> <img style="height: 1vw" src="img/accounts.png"> Accounts</a> </li>
                        <li class="sidetext"> <a <?php if ($current == "books") { echo "id='current'"; }?> href="books.php"> <img style="height: 1vw" src="img/books.png"> Books</a> </li>
                        <li class="sidetext"> <a <?php if ($current == "notes") { echo "id='current'"; }?> href="notes.php"> <img style="height: 1vw" src="img/notes.png"> Notes </a> </li>
                        <br>
                    <?php
                } ?>


                
                <!-- Services -->
                <li class="sidetitle"> <a class="flatred"> Services </a> </li>
                <li class="sidetext"> <a target="_blank" rel="noopener noreferrer" href="https://haron.gay"> <img style="height: 1vw" src="img/harongay.png"> Haron Uploads</a> </li>
                <li class="sidetext"> <a <?php if ($current == "pastebin") { echo "id='current'"; }?> href="pastebin.php"> <img style="height: 1vw" src="img/pastebin.png"> Bats' Pastebin</a> </li>
                <!-- <li class="sidetext"> <a> <img style="height: 1vw" src="img/privnote.png"> Bats' Privnote (WIP) </a> </li> -->
                <br>

                <!-- Help -->
                <li class="sidetitle"> <a class="flatred"> Help </a> </li>
                <li class="sidetext"> <a target="_blank" rel="noopener noreferrer" href="https://t.me/batscs"> <img style="height: 1vw" src="img/contactme.png"> Contact me </a> </li>
                <li class="sidetext"> <a target="_blank" rel="noopener noreferrer" href="../canary.txt"> <img style="height: 1vw" src="img/canary.png"> Canary</a> </li>
                <li class="sidetext"> <a <?php if ($current == "faq") { echo "id='current'"; }?> href="faq.php"> <img style="height: 1vw" src="img/faq.png"> FAQ</a> </li>

                <br><br>
                
            </ul>

        <?php
    }
?>

<script>
    function close_Warning(run = false) {
        var warn = document.getElementById('encrypt_warning');
        warn.style.display = "none";
        <?php $_SESSION["encwarn"] = "hide"; ?>
    }
</script>