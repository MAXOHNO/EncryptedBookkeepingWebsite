<?php

    require_once("../mysql.php");
    // -> require_once("methods.php");

    session_start();

    // -> addlog("SYSTEM", $_SESSION["userid"], "USER LOGOUT");

    if (isset($_COOKIE["session"])) {
        setcookie("session", "", time() - 100, "/");
        setcookie("PHPSESSID", "", time() - 100, "/");
        setcookie("cloudf", "", time() - 100, "/");
    }

    $stmt = $mysql->prepare("UPDATE accounts SET rememberToken = 'pöklsgj985eewunsfuisgifb48sw67fg43tikhsbg684g6tiuhbfs753quvdsymjhfbkjgsb' WHERE userid = :userid");
    $stmt->bindParam(":userid", $_SESSION["userid"]);
    $stmt->execute();

    session_destroy();
    
    ?> Successfully logged out. <a href="../index.php"> Return to Homepage. </a> <?php
?>