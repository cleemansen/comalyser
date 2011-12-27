<?php
include("../database.php");
connect_to_db();

$uid = $_GET['uid'];

$data = Array();
$cnt = 0;

$q = "SELECT * FROM comunio_users_stats WHERE uid = " . $uid;
$result = do_query($q);
if($result){	
	while($row = mysql_fetch_assoc($result)) {
		$o['date'] = $row['date'];
		$o['team_value'] = $row['team_value'];
		$o['balance'] = $row['balance'];
		array_push($data, $o);
		$cnt++;
	}
}
$json['total'] = $cnt;
$json['data'] = $data;

// this is for jsonp: http://docs.sencha.com/ext-js/4-0/#/api/Ext.data.proxy.JsonP

$callback = $_REQUEST['callback'];

$output = $json;

//start output
if ($callback) {
    header('Content-Type: text/javascript');
    echo $callback . '(' . json_encode($output) . ');';
} else {
    header('Content-Type: application/x-json');
    echo json_encode($output);
}
?>