<?php
/*
 * http://stackoverflow.com/questions/815910/what-steps-do-you-take-to-troubleshoot-problems-with-php-curl
 */

$ourFileName = "testFile.txt";
$ourFileHandle = fopen($ourFileName, 'w') or die("can't open file");
fclose($ourFileHandle);
?>