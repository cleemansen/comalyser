<?php
include('../database.php');
include('../functions.php');
connect_to_db();

$uid = $_GET['uid'];

$q = "SELECT * FROM comunio_users AS u JOIN comunio_team AS t ON u.uid=t.uid ".
    "JOIN comunio_kicker AS k ON t.name = k.name ".
    "JOIN comunio_values AS v ON k.name = v.name ".
    "WHERE u.uid = " . $uid .
    " AND date = (SELECT MAX(date) FROM comunio_values WHERE name = k.name) ".
	"ORDER BY k.position";
    //echo($q);
$result = do_query($q);
if($result){
	$data = Array();
	$cnt = 0;
	$min = $max = 0;
	while($row = mysql_fetch_assoc($result)) {
		$o['name'] = $row['name'];
		$n = 0;
		$pd = $row['purchase_price'];
		$p = $row['price'];
		if(is_numeric($pd) && $pd != 0)
			$n = ((int)((100/$pd)*$p))-100;
		else
			$n = "?";
		$o['change'] = $n;
		array_push($data, $o);
		//array_push($data, $min);
		//array_push($data, $max);
		$cnt++;
	}
	$json['total'] = $cnt;

	$json['data'] = $data;
	echo json_encode($json);
}

?>