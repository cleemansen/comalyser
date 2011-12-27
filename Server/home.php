<?php
//Logindaten auslesen
$user = $_POST["user"];
$pw = $_POST["pw"];
$login = $_POST["login"];

//OK - decrypt the comunio pass
$comunio_pass = null;
include("blowfish/blowfish.class.php");


//Login
if($login=="Login"){
	//do the login now
	if(($uid = checkPW($user,md5($pw))) != false){
		$blowfish = new Blowfish($pw);
		$q = "SELECT comunio_login, comunio_pass FROM comunio_users WHERE uid = " . $uid;
		$result = do_query($q);
		if($result) {
			$row = mysql_fetch_array($result);
			$_SESSION['comunio_login'] = $row[0];
			
			$comunio_pass = $blowfish->Decrypt($row[1]);
			
			$_SESSION['comunio_pass'] = $comunio_pass;
			$_SESSION['comunio_pass_db'] = $row[1];
			
			$_SESSION['uid']=$uid;
			$_SESSION['pw']=null;
			$_SESSION['pw_md5']=md5($pw);
			$_SESSION["logged_in"]=true;
			$_SESSION["user"]=$user;
			
			require_once "Mail.php";
 
			$from = $user . "<sender@example.com>";
			$to = "<cleemansen@gmail.com>";
			
			$subject = $user . " @ " . date('d.m.Y - H:i.s') . " says hello to you";
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
			/*
			if (PEAR::isError($mail)) {
			  echo("<p>" . $mail->getMessage() . "</p>");
			 } else {
			  echo("<p>Message successfully sent!</p>");
			 }*/
		}
		
	} else {
		$_SESSION["logged_in"]=false;
		die("<p class='error'>äääääh, login fail:(<br><a href='index.php'>Versuchs nochmal.</a></p>");
		
	}
}
else if($logout=="Logout"){
	echo "OOOLD";
}

//printSession();

echo "<h1 class='marker'>Das ist dein HOME-Screen!<span id='uid' style='display: none'>".$_SESSION['uid']."</span></h1>";

printNavi();

echo "<div id='home'></div>";
//printMyTeam($_SESSION['uid']);
//echo "<div id='balance_div'>";
//printMyActualBalance($_SESSION['uid']);
//echo "<p>Das ist einfach nur Text :)</p></div>";
//echo "<div id='first_chart'></div>";
//echo "</div>";

printNavi("bottom");
?>