<?php
include('../database.php');
include('../functions.php');
connect_to_db();

$uid = $_GET['uid'];
$kicker = $_GET['kicker'];

$q = "DELETE FROM comunio_team WHERE uid = " . $uid . " AND name = '" . $kicker . "'";
//echo($q . "<br>");


$result = do_query($q);
if(!$result){
	die("uuuups, can not delete!");
}

// this is for jsonp: http://docs.sencha.com/ext-js/4-0/#/api/Ext.data.proxy.JsonP

$callback = $_REQUEST['callback'];

$output = Array();

//start output
if ($callback) {
    header('Content-Type: text/javascript');
    echo $callback . '(' . json_encode($output) . ');';
} else {
    //header('Content-Type: application/x-json');
    echo json_encode($output);
}


?>