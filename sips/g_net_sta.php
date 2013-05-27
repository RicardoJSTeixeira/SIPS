<?php
############################################################################################
####  Name:             g_net_sta.php                                                   ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml2/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>goaddons v1.0 © by  | GOAutoDial Inc.</title>
<style type="text/css">    
#bbg {
     background: url(images/vstripe3.png);
     width: 800px;
	 min-height: 800px;     
 	 border: 0px;
	overflow:scroll; 
}
</style>
</head>
<body>
<div id='bbg'>
<br>
<?
$startme = system('/bin/netstat -antpu > "netstat.log"');
$startme = system('echo "" >> "netstat.log"');
$startme = system('echo "" >> "netstat.log"');
$startme = system('echo "<br><FONT FACE=VERDANA COLOR=BLACK SIZE=2><b><i>netstat -i</i></b><br><br>" >> "netstat.log"');
$startme = system('/bin/netstat -i >> "netstat.log"');
$file = fopen("netstat.log", "r") or exit("Unable to open file!");
echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2><b><i>netstat -antpu</i></b><br><br>\n";
while(!feof($file))
  {
  echo fgets($file). "<FONT FACE=VERDANA COLOR=BLACK SIZE=2 /><br />";
  }
fclose($file);  
exit;	
?> 
</div>
</body> 
</html>