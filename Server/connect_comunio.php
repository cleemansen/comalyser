<?php
$comunio_login = $_POST['comunio_login'];
$comunio_pw = $_POST['comunio_pw'];
$key = $_POST['pass'];

// because the login @comunio over GET
$comunio_login = str_replace(' ', '+', $comunio_login);
$comunio_pw = str_replace(' ', '+', $comunio_pw);

include("blowfish/blowfish.class.php");
$blowfish = new Blowfish($key);
$comunio_pass_db = $blowfish->Encrypt($comunio_pw);

$q = "UPDATE comunio_users SET comunio_login = '" . $comunio_login . "', comunio_pass = '" . $comunio_pass_db . "' WHERE uid = " . $_SESSION['uid'];
$result = do_query($q);
if(!$result) {
	die ("ooooh no - DIE");
}

$_SESSION['comunio_pass'] = $comunio_pw;
$_SESSION['comunio_pass_db'] = $comunio_pass_db;
$_SESSION['comunio_login'] = $comunio_login;

$_SESSION['pw'] = null;
/*
$q = "SELECT comunio_pass FROM comunio_users WHERE uid = " . $_SESSION['uid'];
$result = do_query($q);
if($result) {
	$row = mysql_fetch_array($result);
	$clear = $blowfish->Decrypt($row[0]);
	$_SESSION['comunio_login'] = $clear;
}
*/

printSession();
echo "<h1 class='marker'>YEEEEAAAHH - Anmeldung komplett!</h1>";
echo "<p>Am besten startest du jetzt den Synchronisationsvorgang - dann kannst du sofort alle Statistiken und Features von 5000 nutzen...<br>Viel Spaß - <a href='index.php?site=synchronice'>jetzt Synchronisation starten...</a></p>";

printNavi();

$_POST['comunio_pw'] = null;
$_POST['pass'] = null;
?>