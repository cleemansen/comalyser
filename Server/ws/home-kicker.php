<?php
include('../database.php');
include('../functions.php');
connect_to_db();

$uid = $_GET['uid'];
$data = Array();
$cnt = 0;

/*
//since buy of kicker
$q = "SELECT * FROM comunio_users AS u JOIN comunio_team AS t ON u.uid=t.uid ".
    "JOIN comunio_kicker AS k ON t.name = k.name ".
    "JOIN comunio_values AS v ON k.name = v.name ".
    "WHERE u.uid = " . $uid .
    " AND date = (SELECT MAX(date) FROM comunio_values WHERE name = k.name) ".
	"ORDER BY k.position";
    //echo($q);

$result = do_query($q);
if($result){
	while($row = mysql_fetch_assoc($result)) {
		$o['update'] = $row['date'];
		$o['name'] = $row['name'];
		$o['club'] = $row['club'];
		$o['pos'] = position_translater((int)$row['position'], 'string');
		$o['points'] = (int)$row['points'];
		$o['price'] = (int)$row['price'];
		$o['purchase_price'] = $row['purchase_price'];
		$o['purchase_date'] = $row['purchase_date'];
		
		$n = 0;
		$pd = $row['purchase_price'];
		$p = $row['price'];
		if(is_numeric($pd) && $pd != 0 && $p - $pd != 0) {
			$n = (int)((100/$pd)*$p);
			$n = $n - 100;
		}
		
		$o['change'] = $n;
		
		$o['change_absolute'] = ($p - $pd);
		
		array_push($data, $o);
		$cnt++;
	}
	
}
 */
/*
SELECT v1.date AS last_date, 
k.name, 
k.club, 
k.position, 
v1.points AS points_today, 
v1.price AS price_today, 
t.purchase_price, 
t.purchase_date, 
v2.date AS yesterday, 
v2.points AS points_yesterday, 
v2.price AS price_yesterday 
FROM comunio_users AS u 
JOIN comunio_team AS t ON u.uid=t.uid 
JOIN comunio_kicker AS k ON t.name = k.name 
JOIN comunio_values AS v1 ON k.name = v1.name 
JOIN comunio_values AS v2 ON v1.name = v2.name 
WHERE u.uid = 25 
AND v1.date = (SELECT MAX(date) AS max_date FROM comunio_values WHERE name = k.name) 
AND v2.date = (SELECT date FROM comunio_values WHERE date = '2011-07-12' AND name = k.name) 
ORDER BY k.position

//WORKS:
SELECT *
FROM comunio_values AS v1
JOIN comunio_values AS v2 ON (v1.name) = (v2.name)
WHERE v1.name = 'Gomez' AND v1.date = '2011-07-13' AND v2.date = '2011-07-12'
*/


$q = "SELECT v1.date AS last_date, k.name, k.club, k.position, k.status, v1.points AS points_today, v1.price AS price_today, t.purchase_price, t.purchase_date ".//, v2.date AS yesterday, v2.points AS points_yesterday, v2.price AS price_yesterday ".
	"FROM comunio_users AS u JOIN comunio_team AS t ON u.uid=t.uid ".
    "JOIN comunio_kicker AS k ON t.name = k.name ".
    "JOIN comunio_values AS v1 ON k.name = v1.name ".
	//"JOIN comunio_values AS v2 ON v1.name = v2.name ".
    "WHERE u.uid = " . $uid .
    " AND v1.date = (SELECT MAX(date) AS max_date FROM comunio_values WHERE name = k.name) ".
	//" AND v2.date = (SELECT date FROM comunio_values WHERE date = '".$yesterday."' AND name = k.name) ".
	"ORDER BY k.position";
    //echo($q . "<br>");
$result = do_query($q);
if($result){
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		/* PRINT
		foreach($row as $key => $value) {
			echo("--[".$key."] ".$value."<br>");
		}
		echo("<br>");
		*/
		$o['update'] = $row['last_date'];
		$o['name'] = $row['name'];
		$o['club'] = $row['club'];
		$o['status'] = $row['status'];
		$o['pos'] = position_translater((int)$row['position'], 'string');
		$o['points'] = (int)$row['points_today'];
		$o['price'] = (int)$row['price_today'];
		$o['purchase_price'] = $row['purchase_price'];
		$o['purchase_date'] = $row['purchase_date'];
		
		$n = 0;
		$pd = $row['purchase_price'];
		$p = $row['price_today'];
		if(is_numeric($pd) && $pd != 0 && $p - $pd != 0) {
			$n = (int)((100/$pd)*$p);
			$n = $n - 100;
		}
		$o['change'] = $n;
		$o['change_absolute'] = ($p - $pd);
		
		// in case of no values for yesterday
		$o['change_yesterday'] = 0;
		$o['change_absolute_yesterday'] = 0;
		
		array_push($data, $o);
		$cnt++;
	}
}

//since yesterday
$yesterday = date("Y-m-d", mktime(0,0,0,date("m"),date("d")-1,date("Y")));
$q = "SELECT u.uid, 
	k.name, 
	t.purchase_price, 
	t.purchase_date, 
	v2.date AS yesterday, 
	v2.points AS points_yesterday, 
	v2.price AS price_yesterday 
	FROM comunio_users AS u 
	JOIN comunio_team AS t ON u.uid=t.uid 
	JOIN comunio_kicker AS k ON t.name = k.name 
	JOIN comunio_values AS v2 ON k.name = v2.name 
	WHERE u.uid = ". $uid ." 
	AND v2.date = (SELECT date FROM comunio_values WHERE date = '".$yesterday."' AND name = k.name)
	ORDER BY k.position";
    //echo($q . "<br>");
$result = do_query($q);
if($result){
	while($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
		$name = $row['name'];
		
		for($c = 0; $c < sizeof($data); $c++) {
			if($data[$c]['name'] == $name) {
				// YESTERDAY
				$price_yesterday = $row['price_yesterday'];
				$points_yesterday = $row['points_yesterday'];
				$n = 0;
				$pd = $price_yesterday;
				$p = $data[$c]['price'];
				//echo($name . " today: " . $p . " yesterday: " . $pd . "<br>");
				if(is_numeric($pd) && $pd != 0 && $p - $pd != 0) {
					$n = (int)((100/$pd)*$p);
					$n = $n - 100;
				}
				$data[$c]['change_yesterday'] = $n;
				$data[$c]['change_absolute_yesterday'] = ($p - $pd);
				
				break;
				
				//array_push($data[$c], $y);
			}
		}
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