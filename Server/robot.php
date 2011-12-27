<?php
/*
 * Cookies:
 * http://coderscult.com/php/php-curl/2008/05/20/php-curl-cookies-example/
 *
 * Redirect:
 * http://php.net/manual/en/function.curl-setopt.php
 */
function curl_download($Url){
 
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
    curl_setopt($ch, CURLOPT_REFERER, "http://www.comunio.de/team_news.phtml");
 
    // User agent
    curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");
 
    // Include header in result? (0 = yes, 1 = no)
    curl_setopt($ch, CURLOPT_HEADER, 0);
 
    // Should cURL return or print out the data? (true = return, false = print)
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
    // Timeout in seconds
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	
	// Ok, let's do the cookie stuff
	$cookie = "./cookie/c1.txt";
	curl_setopt ($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt ($ch, CURLOPT_COOKIEFILE, $cookie);
	
	// Perhaps we need a redirection
	$cnt = 0;
	$d = curl_redirect_exec($ch, $cnt, true);
	
	/* DEBUG */
	/*
	curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'dbg_curl_data');
	curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'dbg_curl_data');
	curl_setopt($ch, CURLINFO_HEADER_OUT, true);
	
	echo '<fieldset><legend>request headers</legend><pre>', htmlspecialchars(curl_getinfo($ch, CURLINFO_HEADER_OUT)), '</pre></fieldset>';
	echo '<fieldset><legend>response</legend><pre>', htmlspecialchars(dbg_curl_data(null)), '</pre></fieldset>';
	*/
	
 
    // Download the given URL, and return output
    $output = curl_exec($ch);
 
    // Close the cURL resource, and free system resources
    curl_close($ch);
 
    return $output;
}

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
			echo $url . "<br>";
            curl_setopt($ch, CURLOPT_URL, $url);
            $redirects++;
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

/* Step 1 - create the cookie file by visiting the welcome page */
//curl_download('http://www.comunio.de');

/* Step 2 - do the login with the cookie */
//curl_download("http://www.comunio.de/login.phtml?login=cleemansen&pass=publicPW");

/* Step 3 - go to the final page */
//curl_download("http://www.comunio.de/putOnExchangemarket.phtml");

/* Final step - say bye bye */
//curl_download("http://www.comunio.de/logout.phtml");

//print curl_download('http://www.comunio.de/');


/* STEP 1. let’s create a cookie file */
//$ckfile = tempnam ("./cookie", "CURLCOOKIE");
$myFile = "./cookie/c1.txt";
/*
$ourFileName = "/is/htdocs/wp1054551_WLY7VRQIQF/www/comunio/5000/testFile.txt";
$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
fclose($ourFileHandle);
*/
/* STEP 2. visit the homepage to set the cookie properly */
$ch = curl_init ("http://www.comunio.de/");
curl_setopt ($ch, CURLOPT_COOKIEJAR, $myFile);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
/* DEBUG */
curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'dbg_curl_data');
curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'dbg_curl_data');
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

$output = curl_exec ($ch);


echo '<fieldset><legend>request headers</legend>
  <pre>', htmlspecialchars(curl_getinfo($ch, CURLINFO_HEADER_OUT)), '</pre>
</fieldset>';
/*
echo '<fieldset><legend>response</legend>
  <pre>', htmlspecialchars(dbg_curl_data(null)), '</pre>
</fieldset>';
*/

echo '<br><br><h1>********************************************************</h1><br><br>';

/* STEP 3. visit cookiepage.php */
$ch = curl_init ("http://www.comunio.de/login.phtml?login=cleemansen&pass=publicPW");
// Set a referer
curl_setopt($ch, CURLOPT_REFERER, "http://www.comunio.de/");
//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"/*, "Content-Length: 61"*/));
curl_setopt ($ch, CURLOPT_COOKIEFILE, $myFile);
curl_setopt ($ch, CURLOPT_COOKIEJAR, $myFile);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);

/* DEBUG */
curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'dbg_curl_data');
curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'dbg_curl_data');
curl_setopt($ch, CURLINFO_HEADER_OUT, true);

/*
curl_setopt($ch, CURLOPT_POST, true);
$data = array(
    'login' => 'cleemansen',
    'pass' => 'publicPW',
    '&gt;&gt; Login_x' => '-1',
	'action' => 'login'
);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//curl_setopt ($ch, CURLOPT_POSTFIELDS, "login='cleemansen'&pass='publicPW'&action='login'&%3E%3E+Login_x=33");
*/

echo '<fieldset><legend>request headers</legend>
  <pre>', htmlspecialchars(curl_getinfo($ch, CURLINFO_HEADER_OUT)), '</pre>
</fieldset>';

echo '<fieldset><legend>response</legend>
  <pre>', htmlspecialchars(dbg_curl_data(null)), '</pre>
</fieldset>';

// allowing the script to redirect
//curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
$cnt = 0;
$d = curl_redirect_exec($ch, $cnt, true);

echo "CNT: " . $cnt . "<br>";
//echo $d;

//$output = curl_exec ($ch);

/*
if(!curl_errno($ch)){ 
  $info = curl_getinfo($ch); 
  echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'] . "<br>";
  echo "HTTP Code: " . $info['http_code'];
} else { 
  echo 'Curl error: ' . curl_error($tuCurl); 
}
*/


/* here you can do whatever you want with $output */
//print($output);




curl_close($ch);
/*
$fh = fopen($myFile, 'r');
$theData = fread($fh, filesize($myFile));
fclose($fh);
echo $theData;
*/

/* the goal*/
$ch = curl_init ("http://www.comunio.de/putOnExchangemarket.phtml");
// Set a referer
curl_setopt($ch, CURLOPT_REFERER, "http://www.comunio.de/team_news.phtml");
//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"/*, "Content-Length: 61"*/));
curl_setopt ($ch, CURLOPT_COOKIEFILE, $myFile);
curl_setopt ($ch, CURLOPT_COOKIEJAR, $myFile);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);

$output = curl_exec ($ch);
echo $output;

/* logout */
$ch = curl_init ("http://www.comunio.de/logout.phtml");
// Set a referer
curl_setopt($ch, CURLOPT_REFERER, "http://www.comunio.de/team_news.phtml");
//curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"/*, "Content-Length: 61"*/));
curl_setopt ($ch, CURLOPT_COOKIEFILE, $myFile);
curl_setopt ($ch, CURLOPT_COOKIEJAR, $myFile);
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);

$output = curl_exec ($ch);
echo $output;

?>