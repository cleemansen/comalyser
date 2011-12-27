<?php
include('database.php');
$db = connect_to_db();

$query = "SELECT DISTINCT PID FROM Comunio_Values";
$result = do_query($query);
if($result) {
    while($row=mysql_fetch_row($result)) {
	  $query = "SELECT PID, Price, Points, MIN(Time) FROM Comunio_Values WHERE PID = ".$row[0];
	  $result2 = do_query($query);
	  $row2=mysql_fetch_row($result2);
	  $pid = $row2[0];
	  $price = $row2[1];
	  $points = $row2[2];
	  
	  //!!!!!!!!!!!!!!!!!!!!!!!!!!
	  //good article: http://www.richardlord.net/blog/dates-in-php-and-mysql
	  $time = strtotime($row2[3]);
	  $month = date("m", $time);
	  $day = date("d", $time);
	  $year = date("Y", $time);
	  
	  //create for 90 days random values
	  for($i = 0; $i < 30; $i++) {
		$oneDayBack = date("Y-m-d", mktime(0, 0, 0, $month, $day-$i, $year));
		$randPrice = rand($price-100000, $price+999000);
		$randPoints = $points;
		if($i % 7 == 0) {
			$randPoints = rand(-10, 30);
		}
		$query4 = "INSERT INTO Comunio_Values (Time, Points, Price, PID) VALUES ('".$oneDayBack."', ".$randPoints.", ".$randPrice.", ".$pid.")";
		$insert = do_query($query4);
		echo($i."-".$pid."    ".$oneDayBack.": ".$randPrice." - ".$randPoints."<br>");
	  }
	  echo("<br><br>");
    }
}
?>