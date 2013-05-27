<?php
############################################################################################
####  Name:             g_if_stats.php                                                  ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>                                                                      
<head>       
  <meta http-equiv="refresh" content="60" />
  <title>GoAutoDial if_eth0/if_eth1 Traffic</title>
  <meta http-equiv="content-type" content="application/xhtml+xml; charset=iso-8859-1" />
  <link href="csslib/gadi_content.css" rel="stylesheet" type="text/css">
</head> 
<?
echo "<body onLoad='return sendRequest();' />\n";
?>
<div id="webtemp21">
<div id="webtemp19">
<?require("g_menu.php");?>
</div>
<br>
<div id="webtemp20">
</div>
<br>
<br>
<br>
<div>
<?
if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))   	
    {
        $startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
        if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
	    {
		include("g_if_eth0_goautodial.php");
	    }
    }					
if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1"))&&(!file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0")))   	
    {
        $startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
        if (preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
	    {
		include("g_if_eth1_goautodial.php");
	    }
    }		
if ((file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0"))&&(file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1")))   	
    {
        $startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
        if (preg_match_all("/[\w ]*eth0[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
	    {
		include("g_if_eth0_goautodial.php");
	    }
        $startme = exec('/usr/share/goautodial/goautodialc.pl "/etc/init.d/network status"');
        if (preg_match_all("/[\w ]*eth1[\w ]*/", $startme, $matches, PREG_OFFSET_CAPTURE)) 
	    {
		?><br><br><?
                include("g_if_eth1_goautodial.php");
	    }
    }
?>
</div>
</div>
</body>                                                                     
</html>