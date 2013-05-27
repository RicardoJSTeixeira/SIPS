<?php
############################################################################################
####  Name:             g_my_restart.php                                                ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

#$startme = exec('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/go_db_backup.pl"');	

$startme = exec('/usr/share/goautodial/goautodialc.pl "/usr/share/goautodial/ADMIN_backup.pl"');	


#$sock = fsockopen('tcp://$VARSERVHOST', $VARSERVPORT, $errno, $errstr);
#fwrite($sock, '/usr/bin/nohup /etc/init.d/mysqld restart'."\r\n");
#$startme=fread($sock, 128);
#fclose($sock);

if (preg_match("/FAILED/i", "$startme"))
	{
		echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Systems backup failed! Please contact your systems administrator!<br />\n";
	} 
	else 
	{ 
		echo "<br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Systems backup successful!<br />\n";
	}   
exit;		
?> 
