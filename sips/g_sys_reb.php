<?php
############################################################################################
####  Name:             g_sys_reb.php                                                   ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

if ( file_exists("/etc/sysconfig/network-scripts/ifcfg-eth0.bak.gad") )
{
exec('/usr/share/goautodial/goautodialc.pl "/bin/mv -f /etc/sysconfig/network-scripts/ifcfg-eth0.bak.gad /etc/sysconfig/network-scripts/eth0.bak.gad"');	
}	

if ( file_exists("/etc/sysconfig/network-scripts/ifcfg-eth1.bak.gad") )
{
exec('/usr/share/goautodial/goautodialc.pl "/bin/mv -f /etc/sysconfig/network-scripts/ifcfg-eth1.bak.gad /etc/sysconfig/network-scripts/eth1.bak.gad"');	
}
echo "<br><FONT style='font-family: Verdana, Helvetica, sans-serif; font-size: 12px;' COLOR=BLACK>&nbsp;&nbsp;&nbsp;Your system is now rebooting...<br />\n";
exec('/usr/share/goautodial/goautodialc.pl "/usr/bin/reboot"');
exit;	
?> 