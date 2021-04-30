<?php

    require_once("../mysql.php");

    function verifyUser() {

        global $mysql;

        if (isset($_COOKIE["session"])) {
            $stmt = $mysql->prepare("SELECT * FROM accounts WHERE rememberToken = :tkn");
            $stmt->bindParam(":tkn", $_COOKIE["session"]);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch();
                if (!isset($_COOKIE["PHPSESSID"])) {
                    @session_start();
                }
                $_SESSION["userid"] = $row["userid"];

            } else {
                setcookie("session", "", time() - 1);
            }
        }

        if(!isset($_SESSION["userid"])) {
            echo "You are not logged in.";
            ?> <a href="../login.php"> Go back? </a> <?php
            exit;
        }

        if (getRole($_SESSION["userid"]) == -1) {
            echo "You are banned.";
            exit;
        }

        // Session Cookie Token wird immer neu gesetzt um Cookie Stealing zu vermeiden.
        if (isset($_COOKIE["session"])) {
            $stmt = $mysql->prepare("UPDATE accounts SET rememberToken = :tkn WHERE userid = :user");
            $token = bin2hex(random_bytes(36));
            $stmt->bindParam(":tkn", $token);
            $stmt->bindParam(":user", $_SESSION["userid"]);
            $stmt->execute();

            setcookie("session", $token, time() + 3600*24*360, "/");

        }
    }

    function getUsername($userid) {

        global $mysql;

        if (strlen($userid < 17)) {
            return $userid;
        }

        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
        $stmt->bindParam(":userid", $userid);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row["username"];
    }

    function getTelegram($userid) {

        global $mysql;

        if (strlen($userid < 17)) {
            return $userid;
        }

        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
        $stmt->bindParam(":userid", $userid);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row["telegram"];
    }

    function getEmail($userid) {

        global $mysql;

        if (strlen($userid < 17)) {
            return $userid;
        }

        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
        $stmt->bindParam(":userid", $userid);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row["email"];
    }

    function getUserID($username) {

        global $mysql;

        if (strlen($username > 17)) {
            return $username;
        }

        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE username = :username");
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row["userid"];
    }

    function getRole($userid) {
        /* !!!!!!!!!!!!!! */ global $mysql;

        // 3 = superadmin (NOT IMPLEMENTED DONT USE)
        // 2 = admin
        // 1 = mod
        // 0 = user
        // -1 = banned

        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
        $stmt->bindParam(":userid", $userid);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row["role"];

    }

    function getRoleID($role) {

        if ($role == -1) {
            return "Banned";
        } else if ($role == 0) {
            return "Member";
        } else if ($role == 1) {
            return "Moderator";
        } else if ($role == 2) {
            return "Administrator";
        } else if ($role == 3) {
            return "Superadmin";
        }

    }

    function isBooker($userid) {
        /* !!!!!!!!!!!!!! */ global $mysql;

        $stmt = $mysql->prepare("SELECT * FROM accounts WHERE userid = :userid");
        $stmt->bindParam(":userid", $userid);
        $stmt->execute();
        $row = $stmt->fetch();

        return $row["booker"];
    }

    function randomColorLabelTitle($title, $random = false) {

        if ($random == false) {
            $rand = rand(1, 1);
        } else {
            $rand = rand(1, 4);
        }
        

        if ($rand == 1) {
            doTitle("#663399", $title); //purple
        } else if ($rand == 2) {
            doTitle("#309047", $title); //green
        } else if ($rand == 3) {
            doTitle("#872D30", $title); //rot
        } else if ($rand == 4) {
            doTitle("#2A447F", $title); //blau
        } 
        else {
            doTitle("#663399", $title); //purple
        }  
        
    }

    function bookColorTitle($ref, $title) {

        // ref = 0 = Vorbereitung
        // ref = 1 = Laufend
        // ref = 2 = Verkaufen
        // ref = 3 = Erfolgreich
        // ref = 4 = Abbruch
        // ref = 5 = Fehlgeschlagen

        if ($ref == 0) {
            doTitle("#2A447F", $title); // blau vorbereitung
        } else if ($ref == 1) {
            doTitle("#ffa500", $title); // orange laufend
        } else if ($ref == 2) {
            doTitle("#663399", $title); // purple verkaufen
        } else if ($ref == 3) {
            doTitle("#309047", $title); // grün erfolgreich
        } else if ($ref == 4) {
            doTitle("#424242", $title); // grau abbruch
        } else if ($ref == 5) {
            doTitle("#872D30", $title); // fail rot
        }
        
    }

    function doTitle($color, $title) {
        ?> <div class="labelTitle" style="padding-top: 0.5vw; background-color: <?php echo $color?>"> <p class="mediumtext mainfont"> <?php echo $title; ?> </p> </div> <br> <?php
    }

    function addlog($admin, $target, $action, $note = "") {
        global $mysql;

        $log = $mysql->prepare("INSERT INTO adminlog (admin, target, action, note) VALUES (:admin, :target, :action, :note)");
        $log->bindParam(":admin", $admin);
        $log->bindParam(":target", $target);
        $log->bindParam(":action", $action);
        $log->bindParam(":note", $note);
        $log->execute();

    }

    function displayAdminLog($date, $admin, $target, $action, $note) {
        ?>

            <!--  -->
            <div class="labelNorm" style="display: inline-block; width: 62vw;">

                <?php bookColorTitle(0, "" ); ?>

                <table clas="bookTable" cellspacing="0">

                    <tr>

                        <td class="bookTDInfo" style="padding: 0">
                            <p style="width: 10vw" class="smalltext mainfont"> <a style="text-decoration: none;"> <?php echo date("jS F, Y", strtotime($date)) ?> </a> </p>                                    
                        </td>

                        <td class="bookTDInfo" style="padding: 0">
                            <p style="width: 13vw" class="smalltext mainfont"> <a style="text-decoration: none;"> <?php echo getUsername($admin) ?> -> </a> </p>                                    
                        </td>

                        <td class="bookTDInfo" style="padding: 0">
                           <p style="width: 10vw" class="smalltext mainfont"> <a style="text-decoration: none;"> <?php echo $action ?> -> </a> </p>  
                        </td>

                        <td class="bookTDInfo" style="padding: 0">
                            <p style="width: 13vw" class="smalltext mainfont"> <a style="text-decoration: none;"> <?php echo getUsername($target) ?> </a> </p>                                    
                        </td>

                        <td class="bookTDInfo" style="padding: 0">
                            <p style="width: 30vw" class="smalltext mainfont"> <a style="text-decoration: none;"> <?php if ($note != "") { echo " : " . $note; } ?> </a> </p>                                    
                        </td>

                    </tr>

                </table>
            </div>

        <?php
    }
    
    function displayPasteInfo($pasteid) {
        ?>

            <!--  -->
            <div class="<?php if ($_SESSION["lastRefID"] == $refid) { echo "lastbook"; }?> labelNorm" style="display: inline-block; width: 30vw;">

                <?php
                    global $mysql;

                    $stmt = $mysql->prepare("SELECT * FROM pastes WHERE pasteid = :pasteid");
                    $stmt->bindParam(":pasteid", $pasteid);
                    $stmt->execute();
                    $row = $stmt->fetch();
                ?>

                <?php 
                if ($row["title"] != "") {
                    $title = $row["title"];
                } else {
                    $title = $row["pasteid"];
                }
                randomColorLabelTitle("<a style='color: white' class='linktext' href='viewpaste.php?p=" . $pasteid. "'>" . $title . "</a>", "random"); ?>

                <table clas="bookTable" cellspacing="0">

                    <tr>
                        <td class="bookTDInfo">
                            <p class="mediumtext mainfont" style="width: 28vw"> <a style="width: 28vw; background-color: lightgray"> <?php echo $row["text"] ?> </a> </p> 
                        </td>
                    </tr>

                </table>
            </div>

        <?php
    }

    function displaybookInfo($refid) {
        ?>

            <!--  -->
            <div class="<?php if ($_SESSION["lastRefID"] == $refid) { echo "lastbook"; }?> labelNorm" style="display: inline-block; width: 30vw;">

                <?php
                    global $mysql;

                    $stmt = $mysql->prepare("SELECT * FROM books WHERE refid = :refid");
                    $stmt->bindParam(":refid", $refid);
                    $stmt->execute();
                    $row = $stmt->fetch();
                ?>

                <?php bookColorTitle($row["progress"], decrypt($row["shop"]) . " - " . date("jS F, Y", strtotime($row["date"])) ); ?>

                <table clas="bookTable" cellspacing="0">

                    <tr>
                        
                        <td class="bookTDInfo">
                            <p style="width: 6.5vw" class="smalltext mainfont"> <a style="text-decoration: none;" href="openbook.php?refid=<?php echo $refid;?>"> Open book </a> </p>                                    
                        </td>

                        <td class="bookTDInfo">
                            <p style="width: 5vw" class="smalltext mainfont"> <?php echo htmlspecialchars($row["value"] . " €")?> </p>                                    
                        </td>

                    </tr>

                    <tr>
                        <td class="bookTDInfo">
                            <p style="width: 9vw" class="smalltext mainfont"> <?php displayColoredbookProgress($row["progress"]); ?> </p> 
                        </td>

                        <td class="bookTDInfo">
                            <p style="width: 7vw" class="smalltext mainfont"> <?php profitColoring($row["profit"]); ?> </p> 
                        </td>

                    </tr>

                    <tr>
                        <td class="bookTDInfo">
                            <p style="width: 13vw" class="smalltext mainfont"> <?php echo htmlspecialchars(decrypt($row["item"])); ?> </p> 
                        </td>

                        <td class="bookTDInfo">
                            <p style="width: 7vw" class="smalltext mainfont"> <a style="text-decoration: none" href="https://parcelsapp.com/en/tracking/<?php echo htmlspecialchars(decrypt($row["ftid"]));?>"> <?php echo "#" . $row["refid"]; ?> </a> </p> 
                        </td>
                    </tr>

                </table>
            </div>

        <?php
    }

    function displayAccounts($shop) {
        ?>
            <!--  -->
            <div class="labelNorm" style="display: inline-block; width: 19.3vw;">

                <?php
                    global $mysql;

                    $stmt = $mysql->prepare("SELECT * FROM books WHERE owner = :userid AND shop = :shop");
                    $stmt->bindParam(":userid", $_SESSION["userid"]);
                    $stmt->bindParam(":shop", $shop);
                    $stmt->execute();
                    $row = $stmt->fetch();

                    $count = $stmt->rowCount();
                ?>

                <?php randomColorLabelTitle(htmlspecialchars(decrypt($shop)) . " - " . $count . " ", true); ?>

                <center> <p style="padding: 1vw" class="smalltext mainfont"> <a style="text-decoration: none; font-size: 1.3vw" href="showAccounts.php?shop=<?php echo decrypt($shop);?>"> Open Accounts </a> </p> </center>

            </div>

        <?php
    }

    function displayColoredbookProgress($progress) {
        if ($progress == 0) {
            ?> <a style="color: #2A447F"> <?php echo IntToProgress($progress); ?> </a> <?php
        } else if ($progress == 1) {
            ?> <a style="color: #ffa500"> <?php echo IntToProgress($progress); ?> </a> <?php
        } else if ($progress == 2) {
            ?> <a style="color: #663399"> <?php echo IntToProgress($progress); ?> </a> <?php
        } else if ($progress == 3) {
            ?> <a style="color: #309047"> <?php echo IntToProgress($progress); ?> </a> <?php
        } else if ($progress == 4) {
            ?> <a style="color: #424242"> <?php echo IntToProgress($progress); ?> </a> <?php
        } else if ($progress == 5) {
            ?> <a style="color: #872D30"> <?php echo IntToProgress($progress); ?> </a> <?php
        } 
    }

    function getRandomString($length = 6, $type = "string") {
        if ($type == "string") {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        } else if ($type == "int") {
            $characters = '0123456789';
        } else {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        }
        
        $string = '';
    
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
    
        return $string;
    }

    function ProgressToInt($progress) {
        if ($progress == "Vorbereitung") {
            return 0;
        } else if ($progress == "Laufend") {
            return 1;
        } else if ($progress == "Verkaufen") {
            return 2;
        } else if ($progress == "Erfolgreich") {
            return 3;
        } else if ($progress == "Abbruch") {
            return 4;
        } else if ($progress == "Fehlgeschlagen") {
            return 5;
        } 

        return "n/A";
    }

    function IntToProgress($progress) {
        if ($progress == 0) {
            return "Vorbereitung";
        } else if ($progress == 1) {
            return "Laufend";
        } else if ($progress == 2) {
            return "Verkaufen";
        } else if ($progress == 3) {
            return "Erfolgreich";
        } else if ($progress == 4) {
            return "Abbruch";
        } else if ($progress == 5) {
            return "Fehlgeschlagen";
        } 

        return -1;
    }

    function profitColoring($number) {
        if ($number > 0) {
            ?> <a style="color: green"> <?php echo $number; ?> </a> <?php 
        } else if ($number < 0) {
            ?> <a style="color: red"> <?php echo $number; ?> </a> <?php 
        } else {
            ?> <a style=""> <?php echo $number; ?> </a> <?php 
        }
    }

    function genKey($data = null) {
		// Generate 16 bytes (128 bits) of random data or use the data passed into the function.
		$data = $data ?? random_bytes(16);
		assert(strlen($data) == 16);

		// Set version to 0100
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		// Set bits 6-7 to 10
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		// Output the 36 character UUID.
		return vsprintf('%s-%s-%s-%s', str_split(bin2hex($data), 4));
	}

    function genSerialKey() {
        global $mysql;

        $key = "";
        $found = false;

        while(!$found) {
            $key = genKey();
            $stmt = $mysql->prepare("SELECT * FROM serialkeys WHERE serialkey = :key");
            $stmt->bindParam(":key", $key);
            $stmt->execute();

            $count = $stmt->rowCount();
            if ($count == 0) {
                $found = true;
            }
        }

        return $key;
        
    }

    function encrypt($string) {

        // CHECK FEHLT OB ÜBERHAUPT getCrypter was returned!!!!!!!!!!!!!!!!!
        // NUR THEORIE HIER NICHT IMPLEMENTEN

        $algo = "aes-128-cbc";
        $pass = getCrypter();
        // IV EINBAUEN WEIL SECURITY RISK ABER FUCK IT
        //$iv = crc32($_SESSION["userid"]);
        $encrypted = @openssl_encrypt($string, $algo, $pass);

        return $encrypted;
    }

    function decrypt($string) {

        // CHECK FEHLT OB ÜBERHAUPT getCrypter was returned!!!!!!!!!!!!!!!!!
        // NUR THEORIE HIER NICHT IMPLEMENTEN

        if (!hasCrypter()) {
            return $string;
        }

        $algo = "aes-128-cbc";
        $pass = getCrypter();
        // IV EINBAUEN WEIL SECURITY RISK ABER FUCK IT
        //$iv = crc32($_SESSION["userid"]);
        $decrypted = @openssl_decrypt($string, $algo, $pass);

        if ($decrypted == null) {
            //return $string;
        }

        return $decrypted;
    }

    function hasCrypter() {
        if (isset($_SESSION["ccc"])) {
            return true;
        }

        if (isset($_COOKIE["cloudf"])) {
            return true;
        }

        return false;
    }

    function getCrypter() {

        
        if (isset($_COOKIE["cloudf"])) {
            return $_COOKIE["cloudf"];
        }
        

        return $_SESSION["ccc"];
    }

    

    function output($string, $type = "NEUTRAL") {
        if ($type == "NEUTRAL") {
            ?> <a class="mediumtext"> <?php echo $string . "<br>"; ?> </a> <?php
        } else if ($type == "ERROR") {
            ?> <a class="mediumtext flatred"> <?php echo "ERROR: "; ?> </a> <a class="mediumtext"> <?php echo $string . "<br>"; ?></a> <?php
        } else if ($type == "GOOD") {
            ?> <a class="mediumtext flatgreen"> <?php echo $string . "<br>"; ?> </a> <?php
        } else if ($type == "NOTICE") {
            ?> <a class="mediumtext flatpurple"> <?php echo "NOTICE: "; ?> </a> <a class="mediumtext"> <?php echo $string . "<br>"; ?></a> <?php
        } else {
            ?> <a class="mediumtext"> <?php echo $string . "<br>"; ?> </a> <?php
        }
    }

    function getTypos($str) {
  
        $typosArr = array();
          
          $strArr = str_split($str);
                 
          //Proximity of keys on keyboard
          $arr_prox = array();
          $arr_prox['a'] = array('q', 'w', 'z', 'x');
          $arr_prox['b'] = array('v', 'f', 'g', 'h', 'n');
          $arr_prox['c'] = array('x', 's', 'd', 'f', 'v');
          $arr_prox['d'] = array('x', 's', 'w', 'e', 'r', 'f', 'v', 'c');
          $arr_prox['e'] = array('w', 's', 'd', 'f', 'r');
          $arr_prox['f'] = array('c', 'd', 'e', 'r', 't', 'g', 'b', 'v');
          $arr_prox['g'] = array('r', 'f', 'v', 't', 'b', 'y', 'h', 'n');
          $arr_prox['h'] = array('b', 'g', 't', 'y', 'u', 'j', 'm', 'n');
          $arr_prox['i'] = array('u', 'j', 'k', 'l', 'o');
          $arr_prox['j'] = array('n', 'h', 'y', 'u', 'i', 'k', 'm');
          $arr_prox['k'] = array('u', 'j', 'm', 'l', 'o');
          $arr_prox['l'] = array('p', 'o', 'i', 'k', 'm');
          $arr_prox['m'] = array('n', 'h', 'j', 'k', 'l');
          $arr_prox['n'] = array('b', 'g', 'h', 'j', 'm');
          $arr_prox['o'] = array('i', 'k', 'l', 'p');
          $arr_prox['p'] = array('o', 'l');
          $arr_prox['r'] = array('e', 'd', 'f', 'g', 't');
          $arr_prox['s'] = array('q', 'w', 'e', 'z', 'x', 'c');
          $arr_prox['t'] = array('r', 'f', 'g', 'h', 'y');
          $arr_prox['u'] = array('y', 'h', 'j', 'k', 'i');
          $arr_prox['v'] = array('', 'c', 'd', 'f', 'g', 'b');    
          $arr_prox['w'] = array('q', 'a', 's', 'd', 'e');
          $arr_prox['x'] = array('z', 'a', 's', 'd', 'c');
          $arr_prox['y'] = array('t', 'g', 'h', 'j', 'u');
          $arr_prox['z'] = array('x', 's', 'a');
          $arr_prox['1'] = array('q', 'w');
          $arr_prox['2'] = array('q', 'w', 'e');
          $arr_prox['3'] = array('w', 'e', 'r');
          $arr_prox['4'] = array('e', 'r', 't');
          $arr_prox['5'] = array('r', 't', 'y');
          $arr_prox['6'] = array('t', 'y', 'u');
          $arr_prox['7'] = array('y', 'u', 'i');
          $arr_prox['8'] = array('u', 'i', 'o');
          $arr_prox['9'] = array('i', 'o', 'p');
          $arr_prox['0'] = array('o', 'p');
                                                   
          foreach($strArr as $key=>$value){
              @$temp = $strArr;
              foreach ($arr_prox[$value] as $proximity){
                  @$temp[$key] = $proximity;
                  @$typosArr[] = join("", $temp);
              }
          }   

          @$number = rand(0, count($typosArr));
    
          return $typosArr[$number];
      }
    
?>