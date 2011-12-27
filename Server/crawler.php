<?php
/*
 * This function request the given url with http (cUrl)
 * Cookies:
 * http://coderscult.com/php/php-curl/2008/05/20/php-curl-cookies-example/
 *
 * Redirect:
 * http://php.net/manual/en/function.curl-setopt.php
 */
function curl_download ($Url, $uid, $send_back = false, $ref = "http://www.comunio.de") {
 
    // is cURL installed yet?
    if (!function_exists('curl_init')){
        die('Sorry cURL is not installed!');
    }
 
    // OK cool - then let's create a new cURL resource handle
    $ch = curl_init();
 
    // Now set some options (most are optional)
 
    // Set URL to download
    curl_setopt($ch, CURLOPT_URL, $Url);
 
    // Set a referer
    curl_setopt($ch, CURLOPT_REFERER, $ref);
 
    // User agent
    //curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Timeout in seconds
    //curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
	// Ok, let's do the cookie stuff
	$cookie_path = "./cookie/".$uid.".txt";
	
	curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie_path);
	curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie_path);
			
	/* DEBUG */
	curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'dbg_curl_data');
	curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'dbg_curl_data');
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	
	// Perhaps we need a redirection
	$cnt = 0;
	// Download the given URL, and return output
	$d = curl_redirect_exec($ch, $cnt, true);
		
	//echo '<fieldset><legend>request headers</legend><pre>', htmlspecialchars(curl_getinfo($ch, CURLINFO_HEADER_OUT)), '</pre></fieldset>';
	//echo '<fieldset><legend>response</legend><pre>', htmlspecialchars(dbg_curl_data(null)), '</pre></fieldset>';
	
	
    // Close the cURL resource, and free system resources
    curl_close($ch);
	
	if($send_back) 
		return $d;
}

/*
 * Just for debug echos
 */
function dbg_curl_data($curl, $data=null) {
  static $buffer = '';

  if ( is_null($curl) ) {
    $r = $buffer;
    $buffer = '';
    return $r;
  }
  else {
    $buffer .= $data;
    return strlen($data);
  }
}

/*
 * Handles http redirects (301, 302)
 * http://php.net/manual/en/function.curl-setopt.php
 */
function curl_redirect_exec($ch, &$redirects, $curlopt_header = false) {
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code == 301 || $http_code == 302) {
        list($header) = explode("\r\n\r\n", $data, 2);
        $matches = array();
        preg_match('/(Location:|URI:)(.*?)\n/', $header, $matches);
        $url = trim(array_pop($matches));
        $url_parsed = parse_url($url);
        if (isset($url_parsed)) {
            curl_setopt($ch, CURLOPT_URL, $url);
            $redirects++;
			//echo "<p class='error'>".$redirects . " :" . $url . "</p>";
            return curl_redirect_exec($ch, $redirects);
        }
    }
    if ($curlopt_header)
        return $data;
    else {
        list(,$body) = explode("\r\n\r\n", $data, 2);
        return $body;
    }
}



/**
 * This function crawls the Comunio profile with given login and pass.
 * The crawled date will be commited to our DB.
 */
function crawlProfile($login, $pass, $uid) {
	$uid = $uid;
	date_default_timezone_set('GMT');
	
	// COOKIE
	$cookie_path = "./cookie/".$uid.".txt";
	if(!file_exists($cookie_path)) {
		$cookie = fopen($cookie_path, 'w') or die("can't open file");
		fclose($cookie);
		chmod($cookie_path, 0666);
	}
	
	echo "<h1 class='marker'>Das ist dein Crawler</h1>";
	printNavi();
	
	if($login == null || $pass == null)
		die("Sorry, I need login data");
		
	//echo "<p>Try to login @comunio with " . $login . " and " . $pass . ".</p>";
	
	echo("<p>Starte Synchronisation deiner Spieler...</p>");
	/* Step 1 - create the cookie file by visiting the welcome page */
	curl_download('http://www.comunio.de', $uid, false);
	//echo"setting the initial cookie ääääähh ?ßß... done!<br>";
	echo("<ol>");
	/* Step 2 - do the login with the cookie */
	curl_download("http://www.comunio.de/login.phtml?login=".$login."&pass=".$pass."", $uid, false);
	//echo"login with cookie ... done!<br>";
	echo("<li>login @comunio als user " . $login."</li>");
	
	/* Step 3 - go to the final pages */
	echo("<li>Synchronisiere dein Team und die Marktwerte und Punkte deiner <span style='color: blue'>(neuen)</span> Spieler:");
	$res = check_team("http://www.comunio.de/putOnExchangemarket.phtml", $uid);
	echo("<p>" . $res . "</p>");
	echo("</li>");
	
	//echo "<p class='hint'>Now - let us go to the Angebote site and try to receive the purchase date and price of your kickers..</p>";
	echo("<li>Gleiche den Einkaufspreis deiner (neuen) Spieler ab</li>");
	check_purchases("http://www.comunio.de/exchangemarket.phtml?viewoffers_x=0", $uid);
	
	echo("<li>Aktualisiere deinen Kontostand und den aktuellen Mannschaftswert</li>");
	checkBalance("http://www.comunio.de/team_news.phtml", $uid);
	
	//echo"parsing ... done!<br>";
	//echo"updating DB ... done!<br>";
	
	/* Final step - say bye bye */
	curl_download("http://www.comunio.de/logout.phtml", $uid, false, "http://www.comunio.de/team_news.phtml");
	//echo"logout ... done!:)<br>";
	echo("<li>Fertig - alle Daten erfolgreich synchronisiert.</li>");
	echo("</ol>");
	echo("<p>Tipp: Je öfters du diese Synchronisierung durchführst desto akurater und ausagekräftiger wird dein Comalyser5000 Profil.</p>");
	//printNavi();
		
}
	
/*
 * This function catchs the site putOnExchangemarket and reads the actual values of the kickers
 */
function check_team($url, $uid) {
	$echo = "";
	$html = curl_download($url, $uid, true, "http://www.comunio.de/team_news.phtml");
	//echo"catch the exchange market ... done!<br>";
	
	//echo $html;
	
	/* Now, parse the Comunio-HTML and find the table and extract the golden data */
	$doc = new DomDocument;
	// We need to validate our document before refering to the id
	$doc->validateOnParse = false;
	// Please, no warnings. It can't do anything.. comunio creates worse html :(
	libxml_use_internal_errors(true);
	
	$doc->loadHTML($html);
	//echo $doc->saveHTML();
	
	$table = $doc->getElementById('table');
	//echo "<table><thead><tr><th>ID</th><th>Name</th><th>Verein</th><th>Marktwert</th><th>Punkte</th><th>Position</th><th>Kaufdatum</th><th>Kaufpreis</th></tr></thead><tbody>";
	
	$rows = $table->getElementsByTagName('tr');
	// The first row is the headline of the table
	// We have to analyse it, because the tables are different in case of Basic- or Pro-Players
	$comunioID_col= $name_col= $club_col= $position_col= $price_col= $points_col= $state_col= $purchase_date_col= $purchase_price_col= -1;
	
	$row = $rows->item(0);
	$tds = $row->getElementsByTagName('td');
	for($j = 0; $j < $tds->length; $j++) {
		$item = $tds->item($j);
		$value = $item->nodeValue;
		switch($value) {
			case "Name":
				$name_col = $j;
				break;
			case "Verein":
				$club_col = $j;
				break;
			case "Marktwert":
				$price_col = $j;
				break;
			case "Punkte":
				$points_col = $j;
				break;
			case "Position":
				$position_col = $j;
				break;
			case "Richtpreis":
				$comunioID_col = $j;
				break;
			case "Status":
				$state_col = $j;
				break;
			case "Kaufpreis":
				$purchase_price_col = $j;
				break;
			case "Kaufdatum":
				$purchase_date_col = $j;
				break;
			default:
				echo "<span style='color:red'>Unexpected TH in col " . $j."</span><br>";
				break;
		}
	}
	
	// now, the table body
	for($c = 1; $c<$rows->length; $c++){
		$new_player = false; // is the actual kicker a new player in your team?
		$text = Array();
		$row = $rows->item($c);
		$tds = $row->getElementsByTagName('td');
		
		for($j = 0; $j < $tds->length; $j++) {
			$item = $tds->item($j);
			if($j == $comunioID_col) {
				// the ID
				$input = $item->getElementsByTagName('input');
				// in some case, we have no ID
				// 'Bereits verkauft'
				if($input->length > 0) {
					$ii = $input->item(0);
					$t = $ii->getAttribute('name');
					
					$start = strpos($t, '[');
					$start++;
					$end = strpos($t, ']');
					
					$id_tmp = substr($t, $start, $end-$start);
					
					$comunioID = (($comunioID_col == -1) ? 'null' : $id_tmp);
				} else {
					// we use our own kid
					// hopply we have already stored this kicker
					/*
					$q = "SELECT kid FROM comunio_kicker WHERE name = '".$name."'";
					$result = do_query($q);
					if($result) {
						$row = mysql_fetch_row($result);
						$kid = $row[0];
						echo "OK, for kicker " . $name . " I couldn't get the kid from Comunio.. looked up in our DB: ".$kid."<br>";
					}
					*/
				}
			} else if ($j == $club_col) {
				// oh - fucking pro player... club is only a img!
				$img = $item->getElementsByTagName('img');
				
				if($img->length > 0) {
					$ii = $img->item(0);
					$t = $ii->getAttribute('alt');
					$club = str_replace("'", "", utf8_decode($t));
				}else{
					$club = (($club_col == -1) ? 'null' : str_replace("'", "", utf8_decode($item->nodeValue)));
				}
			} else {
			//$text[($c-1)] =$doc->saveXML($td->item($j));
				$text[($j)] = $item->nodeValue;//$doc->saveXML($item);
			//echo $text[$c];
			}
		}
		
		//extract the data - clean it for DB (') and decode (utf-8):
		$name = (($name_col == -1) ? 'null' : str_replace("'", "_", utf8_decode($text[$name_col])));
		
		$price = (($price_col == -1) ? 'null' : (str_replace('.', '', $text[$price_col])));
		$points = (($points_col == -1) ? 'null' : $text[$points_col]);
		$position = (($position_col == -1) ? 'null' : utf8_decode($text[$position_col]));
		
		//wtf: http://www.daniweb.com/web-development/php/threads/193999
		$purchase_date = ((($purchase_date_col == -1) || ($text[$purchase_date_col] == '-')) ? null : $text[$purchase_date_col] );
		if($purchase_date != null) {
			$comunioDate = $text[$purchase_date_col];
			$tmp = explode(".", $comunioDate);
			$purchase_date = "20".$tmp[2] . "-" . $tmp[1] . "-" . $tmp[0];
			//$purchase_date = date("Y-m-d", $purchase_date);
		}
		
		$purchase_price = (($purchase_price_col == -1 || $text[$purchase_price_col] == '') ? null : (str_replace('.', '', $text[$purchase_price_col])));
		
		// OK - let's write the crawld data in our 5000 DB
		$q = "INSERT INTO comunio_kicker (name, club, position, comunioID, status) VALUES ('".$name."', '".$club."', ".position_translater($position, 'int').", '".$comunioID."', 'fit')";
		$result = do_query($q);
		if(mysql_errno()) {
			//echo "<p class='hint'>That doesn't matter: ";
			//echo mysql_errno() . ": ".mysql_error()." in table kicker</p>";
		}
		// old -- This is ugly - The Problem: NULL-Record for SQL (Date-type) is date = NULL; real date is date = ' xxxx-xx-xx '
		//$q_part = "";
		if($purchase_date == null) {
			// init this kicker @team with date = today
			$purchase_date = "'".date('Y-m-d')."'";//We use the date of today as default for new kickers in your team (in case of no pro player).
		} else {
			$purchase_date = "'".$purchase_date."'";
		}
		
		if($purchase_price == null) {
			// init this kicker @team with value = actual market price
			$purchase_price = $price;
		}
		$q = "INSERT INTO comunio_team (uid, name, purchase_date, purchase_price) VALUES (".$uid.", '".$name."', ".$purchase_date.", ".$purchase_price.")";
		
		$result = do_query($q);
		if(mysql_errno() == 1062) {
			//Don't do anything!!!
			/*
			$q = "UPDATE comunio_team SET purchase_date = ".$q_part.", purchase_price = ".$purchase_price." ".
					"WHERE uid = ".$_SESSION['uid']." AND name = '".$name."'";
			$result = do_query($q);
			*/
		} else {
			// case NEW KICKER IN A TEAM!!
			//echo "<p class='error'>OK, for me <span style='color: blue; font-weight: bold'>" . $name . "</span> is a new kicker in your team. I will set his purchase date to NOW and his puchase price to his actual price on the market. "
			//."It is possible, that I get the real date and price - mainly if you bought this kicker in the recent past.</p>";
			$new_player = true;
			$echo = $echo . "<span style='color: blue; font-weight: bold'>" . $name . "</span>, ";
		}
		
		$time = date("Y-m-d");
		$q = "INSERT INTO comunio_values (date, price, points, name) VALUES ('".$time."', ".$price.", ".$points.", '".$name."')";
		$result = do_query($q);
		// We have to update the date - comunio calcs the new points and prices not at 0:00 am
		if(mysql_errno() == 1062) {
			//echo "<p class='hint'>".mysql_errno() . ": OK, let's do an update for ".$name." in table values!</p>";
			$q = "UPDATE comunio_values SET price = ".$price.", points = ".$points." WHERE name = '".$name."' AND date = " . $time;
			$result = do_query($q);
		}
		//echo "<tr><td>".$comunioID."</td><td>".$name."</td><td>".$club."</td><td>".$price."</td><td>".$points."</td><td>".$position."</td><td>".$purchase_date."</td><td>".$purchase_price."</td></tr>";
		if(!$new_player) $echo = $echo . "" . $name . ", ";
	}
	//echo "</tbody></table>";
	libxml_use_internal_errors(false);
	return $echo;
}

/*
 * This function catchs the exchangemarket.phtml?viewoffers_x=0 (Angebote) site and loads the purchase date and price of listed available kickers
 */
function check_purchases($url, $uid) {
	$echo = "";
	//echo "crawling now: " . $url . "<br>";
	$html = curl_download($url, $uid, true, "http://www.comunio.de/team_news.phtml");
	//echo $html;
	/* Now, parse the Comunio-HTML and find the table and extract the golden data */
	$doc = new DomDocument;
	// We need to validate our document before refering to the id
	$doc->validateOnParse = true;
	// Please, no warnings. It can't do anything.. comunio creates worse html :(
	libxml_use_internal_errors(true);
	
	$doc->loadHTML($html);
	//echo $doc->saveHTML();
	
	$target_div = $doc->getElementById('contentleftex');
	// The two tables don't have an id :(
	$tables = $target_div->getElementsByTagName('table');
	$purchase_tab = 0;
	/*echo "<p class='error'>all right, I found " . $tables->length . " table(s) on this page (div: contentleftex)... which is the right, or is that one the right? ".
	"Sadly, I couldn't find a solution for that problem yet...:(</p>";
	if($tables->length > 1) {
		//$sells_tab = $tables->item(0);
		$purchase_tab = $tables->item(1);
		echo "<span style='color: green'>OK, we have more than at least one table in the main div - so I thing the second table is purchase table;)</span><br>";
	} else if ($tables->length == 1) {
		echo "<span style='color: red'>!STIER! I don't know. There is one table - without any IDs or info. This is a future TODO for me...</span><br>";
		// OK, there is only one table - so is this the sell or the purchase table???
		// Let's check it out
		$divs = $target_div->getElementsByTagName('div');
		//echo "searching.... go through " . $divs->length . " divs<br>";
		for($d = 0; $d < $divs->length; $d++) {
			// search the div with class = titleboxcontent
			// when there is only one table on the page, there have to be also only one titleboxcontent div...
			$item = $divs->item($d);
			$titleboxcontent = $item->getAttribute('class');
			//echo $titleboxcontent . "<br>";
			if($titleboxcontent == 'titleboxcontent') {
				// YEEEEAHHHH - thats the right!
				// now check the content of this titleboxcontent div
				$h2 = $item->getElementsByTagName('h2');
				$h2 = $h2->item(0);
				//echo $h2->nodeValue . "<br>";
			}
		}
		$purchase_tab = $tables->item(0);
	} else if ($tables->length == 0) {
		echo "<span style='color: green'>No tables - nothing to do.. DIE</span><br>";
		return;
	}
	echo "<p class='hint'>So, I really hope this is the right table and will analyse them now...:</p>";
	*/
	if ($tables->length == 0) {
		//echo "<span style='color: green'>No tables - nothing to do.. DIE</span><br>";
		return "no changes";
		return;
	}
	
	for($t_cnt = 0; $t_cnt < $tables->length; $t_cnt++) {
		// OK, which is the purchase table?
		// Let's check it out
		$table = $tables->item($t_cnt);
		$trs = $table->getElementsByTagName('tr');
		
		//Look in the headline if this is the right table:
		$tr = $trs->item(0);
		$tds = $tr->getElementsByTagName('td');
		for($td_cnt = 0; $td_cnt < $tds->length; $td_cnt++) {
			$t = $tds->item($td_cnt);
			$t = $t->nodeValue;
			if($t == 'An') {
				//Heureka - that's the right!!! :)
				$purchase_tab = $table;
				//echo "<p class='hint'>Heureka!!! Found the purchase table:</p>";
				break;
			}
		}
		
		
	}
	//echo $doc->saveXML($purchase_tab);
	//echo $doc->saveXML($bt_next);
	
	
	$rows = $purchase_tab->getElementsByTagName('tr');
	// The first row is the headline of the table
	// We have to analyse it, because the tables are different in case of Basic- or Pro-Players
	//$comunioID_col= $name_col= $club_col= $position_col= $price_col= $points_col= $state_col= $purchase_date_col= $purchase_price_col= -1;
	
	$row = $rows->item(0);
	$tds = $row->getElementsByTagName('td');
	
	// now, the table body
	for($c = 1; $c<$rows->length; $c++){
		//echo "analysing row " . $c . " of " . $rows->length;
		
		$text = Array();
		$row = $rows->item($c);
		$tds = $row->getElementsByTagName('td');
		
		$item = $tds->item(($tds->length-1));
		if($item->nodeValue != 'Vollzogen') {
			// only real transactions
			//echo " no real transaction - BREAK<br>";
		} else {
			// OK - this is a good row!!!
			
			$item = $tds->item(0);
			$text['name'] = utf8_decode($item->nodeValue);
			//echo "<p class='hint'>BINGOOOOOOOOOOO:) - ".$text['name']." is a transaction for real!!!</p>";
			$item = $tds->item(3);
			$text['purchase_price'] = str_replace('.', '', $item->nodeValue);
			$item = $tds->item(5);
			$text['purchase_date'] = $item->nodeValue;
			$date = explode('.', $text['purchase_date']);
			$date = "2011-".$date[1]."-".$date[0];
			//UPDATE `db1054551-kick2go`.`comunio_team` SET purchase_date = null, purchase_price = null WHERE uid = 1;
			
			$q = "UPDATE comunio_team SET purchase_price = " . $text['purchase_price'] . ", purchase_date = '" . $date . "' ".
			"WHERE name = '" . $text['name'] . "' AND uid = " .$uid;
			$result = do_query($q);
			//echo $q . "<br>";
			
		}
	}
	
	$bt_div = null;
	//the buttons!
	$bt_div = $doc->getElementById('newsnaviends');
	//echo $doc->saveXML($bt_div);
	$as = $bt_div->getElementsByTagName('a');
	// the as inside the span (usualy one one!)
	for($c = 0; $c < $as->length; $c++) {
		$a = $as->item($c);
		//echo $a->nodeValue . " aaaaaaaaaaaaaaaaa   as: ".$c."<br>";
		$a_attr = $a->getAttribute('title');
		if(utf8_decode($a_attr) == 'Nächste') {
			// this is the right button - next.
			$bt_next = $a;
		}
	}
	
	//the next page?
	//echo gettype($bt_div) . " tttyyyyypppppeeeeee ".$as->length."<br>";
	
	if($as->length > 0) {
		$href = $bt_next->getAttribute('href');
		//echo "<p class='hint'>OK, let's go to ".$href . "...</p>";
		check_purchases("http://www.comunio.de/".$href, $uid);
	}else {
		//echo "<p class='hint'>FINE ... that's the end.. I've got everything... Thanks a lot:)</p>";
	}
}


/*
 * Function to check the balance of the player
 */
function checkBalance($url, $uid) {
	$html = curl_download($url, $uid, true, "http://www.comunio.de/team_news.phtml");
	//echo $html;
	/* Now, parse the Comunio-HTML and find the table and extract the golden data */
	$doc = new DomDocument;
	// We need to validate our document before refering to the id
	$doc->validateOnParse = false;
	// Please, no warnings. It can't do anything.. comunio creates worse html :(
	libxml_use_internal_errors(true);
	
	$doc->loadHTML($html);
	//echo $doc->saveHTML();
	
	// Kontostand
	$balance_div = $doc->getElementById('userbudget');
	$balance_p = $balance_div->getElementsByTagName('p');
	$balance_p = $balance_p->item(0);
	
	$balance = $balance_p->nodeValue;
	echo $balance . "<br>";
	// fetch the number - look for the non-breaking space
	// http://www.php.net/manual/en/function.trim.php#98812
	$replace = array("Kontostand:", '.', '€', ' ', chr(0xC2).chr(0xA0));
	$balance = str_replace($replace, "", $balance);
	$balance = (int)$balance;
	//echo "Kontostand: " . $balance . "<br>";
	
	// Mannschaftswert
	$team_div = $doc->getElementById('teamvalue');
	$team_p = $team_div->getElementsByTagName('p');
	$team_p = $team_p->item(0);
	
	$team = $team_p->nodeValue;
	echo $team . "<br>";
	// fetch the number - look for the non-breaking space
	// http://www.php.net/manual/en/function.trim.php#98812
	$replace = array("Mannschaftswert:", '.', '€', ' ', chr(0xC2).chr(0xA0));
	$team = str_replace($replace, "", $team);
	$team = (int)$team;
	//echo "Mannschaftswert: " . $team . "<br>";
	
	$q = "INSERT INTO comunio_users_stats (uid, date, balance, team_value) VALUES (".$uid.", '".date('Y-m-d')."', ".$balance.", ".$team.")";
	$result = do_query($q);
	if(mysql_errno() == 1062) {
		
		$q = "UPDATE comunio_users_stats SET balance = ".$balance.", team_value = ".$team." WHERE uid = ".$uid." AND date = '".date('Y-m-d')."'";
		$result = do_query($q);
	}
	
	//echo "<p class='hint' style='color: green;'>Commited the actual balance with ".$balance." and your actual team value with ".$team_value."</p>";
}
?>