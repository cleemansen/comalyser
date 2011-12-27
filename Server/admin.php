<?php
include('database.php');
include('functions.php');
connect_to_db();
printHtmlHeader('admin 5000');

$action = $_GET['action'];
$now = date('Y-m-d');
switch($action) {
	case "cron_job":
		cron_job();
		break;
	case "kicker_status":
		kicker_status();
		break;
	case "calendar":
		calendar();
		break;
	case "del_user_completely":
		del_user_completely();
		break;
	case "del_user_team":
		del_user_team();
		break;
	default:
	?>
		<h1 class='marker'>ADMIN</h1>
		<script type="text/javascript">
			function buildUrl(base, obj) {
				var url = base + "?";
				for (var key in obj) {
					url += key + "=" + obj[key] + "&";
					//alert(prop + " = " + obj[prop]);
				}
				url = url.substr(0, url.length-1);
				var sure = confirm("Really call '"+url+"'?");
				if(sure)
					document.location.href = url;
				
			}
		</script>
		
		<h3>Cron Job</h3>
		<p><a href='admin.php?action=cron_job'>Starte crawler für -bender, -fry, -lee la und cleemansen</a></p>
		
		<h3>Verletzungen</h3>
		<form action="admin.php?action=kicker_status" method="POST">
            <textarea name="pasteArea" cols="100" rows="20"></textarea><br>
            <input type="submit" value="Absenden">
        </form>
		
		<h3>Spieltag</h3>
		<p>Wann wurden Punkte veröffentlicht?</p>
		<p>
			<form action="admin.php?action=calendar" method="POST">
				<select name="day">
					<option value="heute">heute</option>
					<option value="gestern">gestern</option>
					<option value="vorgestern">vorgestern</option>
				</select>
				<input type="submit" name="test" value="gogogo">
			</form>
		</p>
		
		<h3>Delete a user.</h3>
		<?php
		$q = "SELECT * FROM comunio_users AS u JOIN comunio_users_stats AS us ON u.uid = us.uid WHERE us.date = (SELECT MAX(date) AS last FROM comunio_users_stats WHERE uid = u.uid) ORDER BY u.uid";
		$result = do_query($q);
		if($result) {
			echo "<table>";
			while($row = mysql_fetch_array($result)) {
				$uid = $row[0];
				echo "<tr>";
				echo "<td>".$row[0]."</td>";
				echo "<td>".$row[1]."</td>";
				echo "<td>".$row[7]."</td>";
				?>
				<td><input type='button' value='del completely' onclick="buildUrl('admin.php', {'action': 'del_user_completely', 'uid': <?php echo $uid; ?>})"></td>
				<td><input type='button' value='remove my team' onclick="buildUrl('admin.php', {'action': 'del_user_team', 'uid': <?php echo $uid; ?>})"></td>
				<?php
			
				echo "</tr>";
			}
			echo "</table>";
		}
		?>
	
	<?php
}//end switch

function del_user_completely() {
	$uid = $_GET['uid'];
	
	if(is_numeric($uid)) {
		$q = "DELETE FROM comunio_users WHERE uid = ".$uid;
		$result = do_query($q);
		if($result) {
			$cnt = mysql_affected_rows();
			echo "<p>".$cnt." rows deleted from comunio_users</p>";
		}
		$q = "DELETE FROM comunio_users_stats WHERE uid = ".$uid;
		$result = do_query($q);
		if($result) {
			$cnt = mysql_affected_rows();
			echo "<p>".$cnt." rows deleted from comunio_users_stats</p>";
		}
		$q = "DELETE FROM comunio_team WHERE uid = ".$uid;
		$result = do_query($q);
		if($result) {
			$cnt = mysql_affected_rows();
			echo "<p>".$cnt." rows deleted from comunio_team</p>";
		}
		
	}
	?>
	<p>Automatische Weiterleitung in 3 Sekunden ... </p>
	<script type="text/javascript">
		setTimeout(window.location.href='admin.php', 100000);
	</script>
	<?php
}

function del_user_team() {
	$uid = $_GET['uid'];
	
	if(is_numeric($uid)) {
		$q = "DELETE FROM comunio_team WHERE uid = ".$uid;
		$result = do_query($q);
		if($result) {
			$cnt = mysql_affected_rows();
			echo "<p>".$cnt." rows deleted from comunio_team</p>";
		}	
	}
	?>
	<script type="text/javascript">
		window.location.href='admin.php';
	</script>
	<?php
}

function kicker_status() {
	$text = $_POST['pasteArea'];
	if(isset($text)){
        //String anhand der Zeilenumbrüche trennen
        $row = explode("\r\n", $text);
		// Status RESET
		$q = "UPDATE comunio_kicker SET status = 'fit'";
		$result = do_query($q);
		if(!$result)
			echo("error during resetting the status of all kickers");
		
    } else {
		die("noting to do");
	}
	echo(count($row) . "<br>");
	echo("<table>");
	
	for($i = 0; $i < count($row); $i++) {
		if(strlen($row[$i]) < 1) {
			//echo('empty?');
			continue;
		}
		$pos = strpos($row[$i], ':');
		if($pos === false) {
			
			$name = '';
			$reason = '';
			
			$kickers = $row[$i];
			// spieler
			$start = strpos($kickers, '(');
			$end = strpos($kickers, ')');
			//echo($kickers . " start: " . $start . " xx " . $end . "<br>");
			$name = trim(substr($kickers, 0, $start-1));
			$reason = trim(substr($kickers, $start+1, $end-$start-1));
			
			$q = "UPDATE comunio_kicker SET status = '".$reason."' WHERE name = '".$name."'";
			$result = do_query($q);
			if($result) {
				$cnt = mysql_affected_rows();
				echo("<tr><td>" . $name . "</td><td>" . $reason . "</td><td>".$cnt."</td>");
			}
			
		}else{
			// nur vereinsnahme
			//echo($row[$i] . " only club<br>");
		}
	}
	echo("</table>");
}

function cron_job() {
	include('crawler.php');
	crawlProfile('-bender', 'bender', 27);
	crawlProfile('-fry', 'fry', 28);
	crawlProfile('-lee+la', 'leela', 31);
	crawlProfile('cleemansen', 'publicPW', 25);
}

function calendar() {
	/*
	foreach ($_POST as $key => $value)
		echo $key.'=>'.$value.'<br />';
	*/
	$day = $_POST['day'];
	switch ($day) {
		case 'heute':
			$day = date("Y-m-d");
			break;
		case 'gestern':
			$day = date("Y-m-d", mktime(0,0,0,date("m"),date("d")-1,date("Y")));
			break;
		case 'vorgestern':
			$day = date("Y-m-d", mktime(0,0,0,date("m"),date("d")-2,date("Y")));
			break;
		default:
			die("ERROR - switch not work");
	}
	$q = "INSERT INTO comunio_calendar VALUES ('".$day."')";
	$result = do_query($q);
	if($result) {
		echo "<p>Spieltag ".$day." eingetragen</p>";
	}
}

printHtmlFooter();
?>