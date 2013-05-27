<?php
$myFile = "../../../../../etc/asterisk/extensions.conf";
$fh = fopen($myFile, 'r');

header("Status: 404 Not Found");

while (!feof($fh)) {
   $line = fgets($fh);
	if (eregi('^GSM2', $line) OR eregi('^p2', $line)) {
		$pieces = explode("=", $line);
		echo "<p><b>$pieces[0]</b></p>";
	}
	 #echo "<p>$line</p>";
}

fclose($fh);
?>

