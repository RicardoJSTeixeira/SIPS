<?php
############################################################################################
####  Name:             g_net_res.php                                                   ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

$startme = exec('/usr/share/goautodial/goautodialc.pl "/usr/bin/nohup /etc/init.d/iptables restart"');
if (preg_match("/FAILED/i", "$startme")) 
	{
		echo "<br><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;' COLOR=BLACK>&nbsp;&nbsp;&nbsp;Firewall restart failed! Please contact your systems administrator!<br />\n";
	} 
	else 
	{ 
		echo "<br><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;' COLOR=BLACK>&nbsp;&nbsp;&nbsp;Firewall was successfully restarted!<br />\n";
	}   
exit;	
?>