<?php
include("../database.php");
connect_to_db();

$uid = $_GET['uid'];
$name = $_GET['name'];

$data = Array();
$cnt = 0;
// eigentlist die UID ŸberflŸssig!!!
$q = "SELECT v.price, v.points, v.date, c.date AS x ".
	"FROM comunio_users AS u ".
	"JOIN comunio_team AS t ON u.uid = t.uid ".
	"JOIN comunio_values AS v ON t.name = v.name ".
	"LEFT OUTER JOIN comunio_calendar AS c ON v.date = c.date ".
	"WHERE u.uid = ".$uid." AND t.name = '".$name."' ".
	"ORDER BY v.date";
$result = do_query($q);
if($result){
	$last_points = 0;
	
	while($row = mysql_fetch_assoc($result)) {
		$o = Array();
		$o['date'] = $row['date'];
		$o['price'] = $row['price'];
		
		
		// FAKE
		/*
		if(date('N',strtotime($row['date'])) == 7 || date('N',strtotime($row['date'])) == 3) {
			$o['points'] = rand(-20, 20);
		}*/
		// REAL
		if($row['x'] != null) {
			$current_points = $row['points'];
			$this_day_points = $current_points - $last_points;
			$o['points'] = $this_day_points;
			
			// punkte verschieben auf samstag
			//if(date('N',strtotime($row['x'])) == 1) {
				// MONTAG
				//$one_day_before = date('Y-m-d', strtotime($row['x'] . ' -1 day'));
				//$data[$one_day_before]['points'] = $current_points;
			//}
			$last_points = $current_points;
		}
		
		array_push($data, $o);
		$cnt++;
	}
}
$json['total'] = $cnt;
$json['req_name'] = $name;
$json['data'] = $data;

// this is for jsonp: http://docs.sencha.com/ext-js/4-0/#/api/Ext.data.proxy.JsonP

$callback = $_REQUEST['callback'];

$output = $json;

//start output
if ($callback) {
    header('Content-Type: text/javascript');
    echo $callback . '(' . json_encode($output) . ');';
} else {
    //header('Content-Type: application/x-json');
    echo json_encode($output);
}
?>