<?php
printSession();
?>
<h1 class="marker">Comalyser5000</h1>
    
<span>Hier kannst du Punkte und Marktwerte deines Teams nachverfolgen.</span>

<h3>Login</h3>
<form name="Login" method="POST" action="index.php?site=home">
	Name: <input type="text" name="user">
	Passwort: <input type="password" name="pw">
	<input type="submit" value="Login" name="login">    
</form>
	
<h3>Neu anmelden</h3>
<p>Wähle einen Login-Namen und ein gutes Passwort - dann kanns bald losgehen...:</p>
<form name="signup" method="post" action="index.php?site=signup">
	Name: <input type="text" name="signup_login">
	Passwort: <input type="password" name="signup_pw">
	<input type="submit" value="Anmelden" name="signup_submit">
</form>