<?php
############################################################################################
####  Name:             g_ast_optimize.php                                              ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

$ast_tab_opt = exec('/usr/share/astguiclient/AST_DB_optimize.pl');	
	
if ($ast_tab_opt)
	{          
		sleep (20);
		echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Asterisk/Vicidial database tables were successfully optimized!<br />\n";
	}
	else
	{
		echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>Error occured while optimizing Asterisk/Vicidial database tables! Please contact your dialer administrator!<br />\n";		
	}
exit;	
?>