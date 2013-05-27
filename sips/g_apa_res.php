<?php
############################################################################################
####  Name:             g_apa_res.php                                                   ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

if (isset($_GET["action"]))				{$action=$_GET["action"];}
	elseif (isset($_POST["action"]))	{$action=$_POST["action"];}

if ($action=="result") {
	$filename = "/var/www/html/goautodial/gorestart.txt";
	$handle = fopen($filename, "rt");
	$result = fread($handle, filesize($filename));
//	list($stop, $start) = explode(']', $result);
	echo "<pre>" . $result . "</pre>";
	fclose($handle);
}

if ($action=="check") {
	echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2><strong>Apache Webserver is now being restarted!</strong><br />\n";
}

//sleep(20);
if ($action=="restart") {
	$startme = shell_exec('/usr/share/goautodial/goautodialc.pl "/usr/bin/nohup /etc/init.d/httpd restart"');
	$filename = "/var/www/html/goautodial/gorestart.txt";
	$handle = fopen($filename, "w");
	fwrite($handle, $startme);
//	list($stop, $start) = explode(']', $result);
	fclose($handle);
}

#$sock = fsockopen('tcp://$VARSERVHOST', $VARSERVPORT, $errno, $errstr);
#fwrite($sock, '/usr/bin/nohup /etc/init.d/httpd restart'."\r\n");
#$startme=fread($sock, 128);
#fclose($sock);

#if (preg_match("/FAILED/i", "$startme")) 
#	{
#		echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Apache Webserver restart failed! Please contact your systems administrator!<br />\n";
#	} 
#	else 
#	{ 
#		echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Apache Webserver was successfully restarted!<br />\n";
#	}   
#exit;		
?> 
