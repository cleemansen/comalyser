<?php
include('../database.php');
include('../functions.php');
connect_to_db();

$uid = $_GET['uid'];
$data = Array();
$buffer = Array();
$fields = Array();
array_push($fields, 'date');
$cnt = 0;

$q = "SELECT t.name, v.date, v.price, v.points ".
	"FROM comunio_users AS u ".
	"JOIN comunio_team AS t ON u.uid = t.uid ".
	"JOIN comunio_values AS v ON t.name = v.name ".
	"WHERE v.date > '2011-07-17' AND u.uid = " . $uid .
	" ORDER BY v.date";
    //echo($q . "<br>");
$result = do_query($q);
if($result){
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$cnt++;
		/* PRINT
		foreach($row as $key => $value) {
			echo("--[".$key."] ".$value."<br>");
		}
		echo("<br>");
		*/
		$buffer[$row['date']]['date'] = $row['date'];
		$buffer[$row['date']][$row['name']] = intval($row['price']);
		if(!in_array($row['name'], $fields))
			array_push($fields, $row['name']);
	}
}
/*
 * hier gibt es ein extJS Area Chart Problem:
 * alle Spieler brauchen für jeden Tag einen Wert!
 * Wenn der Sync versagt hat kann es zu Löchern kommen.
 * Hier muss bei fehlenden Tagen die Werte vom Vortag bzw. Nachtag
 * des jeweiligen Spielers kopiert und eingetragen werden.
 *
 * SELECT COUNT(t.name), v.date
 * FROM comunio_users AS u
 * JOIN comunio_team AS t ON u.uid = t.uid
 * JOIN comunio_values AS v ON t.name = v.name
 * GROUP BY v.date
 * ORDER BY v.date
 */


$json['total'] = $cnt;
$json['chart_fields'] = array_slice($fields, 1);
// building field objects
for($c = 0; $c < sizeof($fields); $c++) {
	$v = $fields[$c];
	$o = Array();
	if($c == 0) {
		$o['name'] = $v; $o['type'] = 'date'; $o['dateFormat'] = 'Y-m-d';
	} else{
		$o['name'] = $v;
	}
	$fields[$c] = $o;
}
$json['fields'] = $fields;

foreach($buffer as $key => $value) {
	//echo("--[".$key."] ".$value."<br>");
	array_push($data, $value);
}
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