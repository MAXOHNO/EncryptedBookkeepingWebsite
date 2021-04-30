<?php
    function drawTopsection() {
        ?>
        <section class="topsection">

            <table>
                <tr>
                    <td style="padding-left: 6vw">
                            <a href="../index.php" class="title toptext mainfont"> <img src="logo_black.svg" alt="logo" style="height: 2vw; margin-top: 0.8vw"> </a>
                    </td>

                    <td class="navbar_td">
                            <a href="../index.php" class="un title toptext toptextred mainfont"> Buchhaltung </a>
                    </td>

                    <td style="padding-left: 20vw" class="navbar_td">
                            <a href="dashboard" class="un toptext mainfont"> Dashboard </a>
                    <td>

                    <td class="navbar_td">
                            <a href="dashboard/account.php" class="un toptext mainfont"> Account </a>
                    <td>

                    <td class="navbar_td">
                        <a href="login.php" class="un toptext mainfont"> Login </a>
                    <td>

                    <td class="navbar_td">
                            <a href="canary.txt" class="un toptext mainfont"> Canary </a>
                    <td>

                </tr>
            </table>
                
        </section>

    <?php
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

    function verifyUser() {

        global $mysql;

        if (isset($_COOKIE["session"])) {
            $stmt = $mysql->prepare("SELECT * FROM accounts WHERE rememberToken = :tkn");
            $stmt->bindParam(":tkn", $_COOKIE["session"]);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch();
                session_start();
                $_SESSION["username"] = $row["username"];

            } else {
                setcookie("session", "", time() - 1);
            }
        }

        if (isset($_SESSION["username"])) {
            header("Location: dashboard/");
			echo "aaaaaa";
        }
    }
	
	function genUUID($data = null) {
		// Generate 16 bytes (128 bits) of random data or use the data passed into the function.
		$data = $data ?? random_bytes(16);
		assert(strlen($data) == 16);

		// Set version to 0100
		$data[6] = chr(ord($data[6]) & 0x0f | 0x40);
		// Set bits 6-7 to 10
		$data[8] = chr(ord($data[8]) & 0x3f | 0x80);

		// Output the 36 character UUID.
		return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	}

?>