<?php
//error_reporting(E_ALL);
ini_set('display_errors', 1);

$user = $_POST["signup_login"];
$pw = $_POST["signup_pw"];
//$login = $_POST["signup_submit"];

/*
// RESET everything
$q = "DELETE FROM comunio_users";
do_query($q);
$q = "DELETE FROM comunio_values";
do_query($q);
$q = "DELETE FROM comunio_team";
do_query($q);
$q = "DELETE FROM comunio_kicker";
do_query($q);
*/
// insert the new account in our DB

// Step 1 - check if this comunio login already exists
// We have to do this by our own, becouse PK is (uid, login)
// Sadly an auto-increment column must be PK

$q = "SELECT * FROM comunio_users WHERE login = '".$user."'";
$result = do_query($q);
if($result) {
	if(mysql_num_rows($result) > 0) {
		die("<span class='error'>Hey, your comunio account is already connected! - BREAK:(</span><br>");
	}else {
		// All fine :)
	}
}

$q = "INSERT INTO comunio_users (login, pass) VALUES ('".$user."', '".md5($pw)."')";
$result = do_query($q);
if(!$result) {
	die("<p>eieieieieieiei - DIE</p>");
}

//now, we need the UID, given by the DBMS.
$q = "SELECT uid FROM comunio_users WHERE login = '".$user."'";
$result = do_query($q);
if($result) {
	if(mysql_num_rows($result) > 0) {
		$uids = mysql_fetch_row($result);
		// automatic login
		$_SESSION["uid"] = $uids[0];
		$_SESSION["logged_in"]=true;
		$_SESSION["user"]=$user;
		$_SESSION["pw"]=$pw;
		$_SESSION["pw_md5"]=md5($pw);
		
		require_once "Mail.php";
 
			$from = $user . "<sender@example.com>";
			$to = "<cleemansen@gmail.com>";
			
			$subject = "NEW user: " . $user . " @ " . date('d.m.Y - H:i.s') . " says hello to you";
			$body = "Hi,\n\nHow are you?";
			
			$host = "ssl://smtp.gmail.com";
			$port = "465";
			$username = "starquay5000";
			$password = "just4comunio5000";
			
			$headers = array ('From' => $from,
			  'To' => $to,
			  'Subject' => $subject);
			$smtp = Mail::factory('smtp',
			  array ('host' => $host,
				'port' => $port,
				'auth' => true,
				'username' => $username,
				'password' => $password));
			
			$mail = $smtp->send($to, $headers, $body);
			
		//echo "<span class='info'>Your uid is " . $_SESSION['uid'] . "</span><br>";
	}
}else
	die("Uuuups, errors during fetching the new user id<br>BREAK:(<br>");


printSession();
?>
<script type="text/javascript"><?php include('js/blowfish.js'); ?></script>

<script type="text/javascript">
	function finish(name, key, pw) {
		alert(name + " - " + key + " - " + pw);
		save = encrypt(key, pw);
		alert(save);
	}
</script>

<h1 class='marker'>All right, deine Anmeldung bei uns war erfolgreich! :)</h1>
<p>Als nächstes solltest du dich mit deinem Comunio Profil verknüpfen, damit wir deine Mannschaft auslesen können
und dir dann Statistiken und Auswertungen deines Teams präsentieren zu können;)</p>
<p>Dazu benötigen wir deine Comunio Login-Daten.
<br><span style="color: red">Hinweis:</span> Wir werden dein Comunio Passwort nicht auf unserem Server speichern - 
Wir werden niemals Zugriff auf dein persönliches Konto bei Comunio haben.<br>
Damit du aber nicht jeden Tag dein Comunio Passwort wieder neu bei uns eingeben musst, <br>
werden wir es <strong>verschlüsselt</strong> abspeichern - aber nur du kannst es danach wieder entschlüsseln und nutzen!</p>

<h3>Comunio Login</h3>
<p>Gib bitte unten deine Comunio-Accountdaten ein, damit wir dein Comunio-Profil mit uns verknüpfen können.</p>
<form name="comunio_signup" method="post" action="index.php?site=connect_comunio">
	Comunio-Name: <input id="c_name" type="text" name="comunio_login">
	Comunio-Passwort: <input id="c_pw" type="password" name="comunio_pw">
	<input type="submit" value="Konten verknüpfen" name="connect_comunio_submit" >
	<input type="hidden" value="<?php echo $pw ?>" name="pass" >
</form>
<?php

printNavi();


?>