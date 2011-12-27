<?php
include('database.php');
$link = mysqli_connect("wp1054551.wp083.webpack.hosteurope.de", "dbu1054551", "kick2go", "db1054551-kick2go");

/* check connection */
if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}


//fields:[day, pid1, ... , pidX],
//data:{day: 30, pid1: 34, pid2: 23, ... , pidX: 324},{day: 29 ..}
$query1 = "SELECT DISTINCT Time FROM Comunio_Values ORDER BY Time DESC LIMIT 0, 35";
$fields[] = "day";

if ($timeRes = mysqli_query($link, $query1)) {
	while($rowTime = mysqli_fetch_assoc($timeRes)) {
		$query = "SELECT * FROM Comunio_Player AS p JOIN Comunio_Values AS v ON p.PID = v.PID WHERE v.Time = '".$rowTime["Time"]."'";
		$oneDay["day"] = $rowTime["Time"];
		if ($result = mysqli_query($link, $query)) {
			/* fetch associative array */
			while ($row = mysqli_fetch_assoc($result)) {
				//all names
				if(!in_array($row["PID"], $fields)) {
					$fields[] = $row["PID"];	
				}
				
				//printf ("%s %s (%s)<br>",$row["Time"], $row["Name"], $row["Price"]);
				$oneDay[$row["PID"]] = (int)$row["Price"];
				
			}
			
			$data[] = $oneDay;
			/* free result set */
			mysqli_free_result($result);
		}
	
	}
	$json["data"] = $data;
	$json["fields"] = $fields;
	/* free result set */
	mysqli_free_result($timeRes);
}

/* close connection */
mysqli_close($link);

echo json_encode($json);
?>