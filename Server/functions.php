<?php

function printHtmlHeader($pageTitle = 'Comalyser5000', $requirements = null) {
    
    $header = "<!--?xml version='1.0' encoding='UTF-8'?-->
    <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'
    'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
    <html xmlns='http://www.w3.org/1999/xhtml'>
        <head>
            <meta http-equiv='Content-Language' content='de'>
            <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
            <title>".$pageTitle."</title>
            
            <script type='text/javascript'>
            function openSite(url){
                document.location.href = url;
            }
            </script>";
	if($requirements['extJS']) {
		
		$header = $header .	"<link rel='stylesheet' type='text/css' href='ext-4.0/resources/css/ext-all.css' />";
		$header = $header .	"<script type='text/javascript' src='ext-4.0/ext.js'></script>";
		$header = $header .	"<script type='text/javascript' src='app-all.js'></script>";
		
		
		/* DIRTY - NOT MVC:
		$header = $header .	"<script type='text/javascript' src='http://dev.sencha.com/deploy/ext-4.0.2a/bootstrap.js'></script>";
		$header = $header .	"<link rel='stylesheet' type='text/css' href='http://dev.sencha.com/deploy/ext-4.0.2a/resources/css/ext-all.css' />";
		*/
		$header = $header .	"<script type='text/javascript' src='ext-4.0/locale/ext-lang-de.js'></script>";
	}
	if($requirements['home_grid']) {
		/*
		//$header = $header . "<script type='text/javascript' src='js/store_team.js'></script>";
		$header = $header . "<script type='text/javascript' src='js/home_overview.js'></script>";
		//$header = $header . "<script type='text/javascript' src='js/chart_change_buy.js'></script>";
		$header = $header . "<script type='text/javascript' src='js/chart_change_yesterday.js'></script>";
		$header = $header . "<script type='text/javascript' src='js/home_grid.js'></script>";
		*/
	}
	if($requirements['home_change']) {
		//$header = $header . "<script type='text/javascript' src='js/first_chart.js'></script>";
	}
	$header = $header .	"<link rel='stylesheet' type='text/css' href='css/home_grid.css'>
			<link rel='stylesheet' type='text/css' href='css/base.css'>
        </head>
    <body>";
	
    echo $header;
}

function printHtmlFooter() {
    $footer = "<div id='footer'>Copyright bei uns und überhaupt - die fünftausender deluxe starQuay :)</div></body></html>";
    print $footer;
}
 
function checkPW($user,$pw){
    $query = "SELECT uid FROM comunio_users WHERE login = '".$user."' AND pass = '".$pw."'";
    // echo $query;
    $result = do_query($query);
    
    if($result){
        $row = mysql_fetch_row($result);
        return $row[0];
    }
    else return false;
}

/*
 * Function for translating between human readable an the martix
 */
function position_translater($pos, $target = int) {
	if(is_numeric($pos) && $target == 'string') {
		// from int to string
		switch($pos) {
			case 0:
				return "Tor";
			case 1:
				return "Abwehr";
			case 2:
				return "Mittelfeld";
			case 3:
				return "Sturm";
			default:
				//do nothing
		}
	}else if($target == 'int'){
		// from string to int
		switch($pos) {
			case "Tor":
				return 0;
			case "Abwehr":
				return 1;
			case "Mittelfeld":
				return 2;
			case "Sturm":
				return 3;
			default:
				//do nothing
		}
	}
	echo "<span class='error'>Problems with kicker position: ".$pos."</span>";
}

function printSession() {
    $logged_in = (isset($_SESSION['logged_in'])) ? $_SESSION['logged_in'] : 'not set';
    $user = (isset($_SESSION['user'])) ? $_SESSION['user'] : 'not set';
    $pw = (isset($_SESSION['pw'])) ? '&#9824;&#9824;&#9824;&#9824;&#9824;&#9824;&#9824;' : 'not set';
	$pw_md5 = (isset($_SESSION['pw_md5'])) ? $_SESSION['pw_md5'] : 'not set';
	
	$comunio_login = (isset($_SESSION['comunio_login'])) ? $_SESSION['comunio_login'] : 'not set';
	$comunio_pass_db = (isset($_SESSION['comunio_pass_db'])) ? $_SESSION['comunio_pass_db'] : 'not set';
	$comunio_pass = (isset($_SESSION['comunio_pass'])) ? '&#9824;&#9824;&#9824;&#9824;&#9824;&#9824;&#9824;' : 'not set';
    
	$uid = (isset($_SESSION['uid'])) ? $_SESSION['uid'] : 'not set';
    
    $p = "<div id='session'>".
    "<div id='session_info'>".
    "logged_in_____________ ". $logged_in ."<br>".
	"Your actual uid_______ ". $uid ."<br>".
    "Your login name_______ ". $user ."<br>".
    "Your password_@DB_____ ". $pw_md5 ." - clear___ ".$pw."<br>".
	"Your comunio login____ ". $comunio_login ."<br>".
	"Your comunio pass_@DB_ ". $comunio_pass_db ." - clear___ ".$comunio_pass."<br>";
    $p = $p . "</div>";
    /*
    if($_SESSION['logged_in'] == true) {
        $p = $p . "<div id='logout'><form name='logout' method='POST' action='index.php?site=logout'>".
            "<input type='submit' value='Logout' name='logout'>".
        "</form></div>";
    }*/
    $p = $p . "</div>";
    echo $p;
}

function printMyTeam($uid) {
    $q = "SELECT * FROM comunio_users AS u JOIN comunio_team AS t ON u.uid=t.uid ".
    "JOIN comunio_kicker AS k ON t.name = k.name ".
    "JOIN comunio_values AS v ON k.name = v.name ".
    "WHERE u.uid = " . $uid .
    " AND date = (SELECT MAX(date) FROM comunio_values WHERE name = k.name) ".
	"ORDER BY k.position";
    //echo($q);
    $result = do_query($q);
    if($result){
        echo "<div id='myTeam'>";
        echo "<table><tr><th>UPDATE</th><th>NAME</th><th>CLUB</th><th>Pos</th><th>PUNKTE</th><th>WERT</th><th>&lt; % &gt;</th><th>KAUFPREIS</th><th>GEKAUFT</th></tr>";
        while($row = mysql_fetch_assoc($result)) {
            $n = 0;
            $pd = $row['purchase_price'];
            $p = $row['price'];
            if(is_numeric($pd) && $pd != 0)
                $n = (int)((100/$pd)*$p);
            else
                $n = "?";
                
            if($n<100)$c="red";
            else if($n==100)$c="gray";
            else if($n>100)$c="green";    
          //  $c = ($n != 0) ? (($n >= 100) ? "green" : "red") : "gray";
            
            echo "<tr class='myTeam' onclick='openSite(\"index.php?site=kicker&name=".$row['name']."\");'>";     
            echo "<td class='date'>".$row['date']."</td>";
            echo "<td>".$row['name']."</td>";
            echo "<td>".$row['club']."</td>";
			echo "<td class='right'>".$row['position']."</td>";
            echo "<td class='right'>".$row['points']."</td>";
            echo "<td class='right'>".$row['price']."</td>";
            echo "<td class='right' style='background-color: ".$c.";'>".$n."%</td>";
            echo "<td class='right'>".$row['purchase_price']."</td>";
            echo "<td class='date'>".$row['purchase_date']."</td>";
            echo "</tr>";
        }
        echo "</table></div>";
    }
}

function printMyActualBalance($uid) {
    //SELECT * FROM `db1054551-kick2go`.comunio_users_stats WHERE uid = 3 AND date = '(SELECT date FROM `db1054551-kick2go`.comunio_users_stats WHERE uid = 3 AND MAX(date))'
    $q = "SELECT * FROM comunio_users_stats WHERE uid = ".$uid." AND date = (SELECT MAX(date) FROM comunio_users_stats WHERE uid = ".$uid.")";
    $result = do_query($q);
    if($result) {
        $row = mysql_fetch_assoc($result);
        echo $row->length;
        echo "<div id='balance'><table>";
            echo "<tr><th colspan='2'>Deine Kohlen (@ ".$row['date'].")</th></tr>";
            echo "<tr><td>Kontostand:</td><td class='right'>".$row['balance']."</td></tr>";
            echo "<tr><td>Mannschaftswert:</td><td class='right'>".$row['team_value']."</td></tr>";
        echo "</table></div>";
    }
}

function printNavi($id = top) {
    echo "<div id='navi_div'><ul class='navi' id=".$id.">";
    echo "<li><a href='index.php?site=home'>my HOME</a></li>";
    echo "<li><a href='index.php?site=synchronice'>SYNCHRONICE me</a></li>";
    if($_SESSION['logged_in'] == true)
        echo "<li><a style='background-color: red;' href='index.php?site=logout'>BYE bye</a></li>";
	echo "<li id='navi_word'><span style='font-size: 30pt; color: white'>⇇</span>NAVIGATION</li>";
    echo "</ul>";
	echo "</div>";
}


function getKickerInfoFromName($name){
    $query = "SELECT name, price, points, date FROM comunio_values WHERE name = '".$name."'";
     $result = do_query($query);
    $t=0;
    if($result){
        while($row = mysql_fetch_row($result)){
        $erg[$t]['name'] = $row[0];
        $erg[$t]['price'] = $row[1];
        $erg[$t]['points'] = $row[2];
        $erg[$t]['date'] = $row[3];
        $t++;
    }
    return $erg;
    }
    else return false;
}


?>