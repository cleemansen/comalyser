<?php
include('database.php');
connect_to_db();

$action = $_GET['action'];
switch($action) {
	case "kicker_price":
		kicker_price($_GET['kicker']);
		break;
	default:
		echo "I don't know";
		break;
}

function kicker_price($kicker_name) {
	$q = "SELECT * FROM comunio_values WHERE name = '".$kicker_name."'";
	$result = do_query($q);
	if($result) {
		while($row = mysql_fetch_assoc($result)) {
			echo $row['price'] . "-";
		}
	}
}
?>