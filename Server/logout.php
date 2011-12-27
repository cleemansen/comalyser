<?php
session_unset();
//session_destroy();
$_SESSION=array();

/*
$_SESSION['logged_in'] = false;
$_SESSION['user'] = null;
$_SESSION['pw'] = null;
$_SESSION['uid'] = null;
*/
printSession();
echo "<a href='index.php'>re-login</a>";
?>