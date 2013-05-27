<?php
############################################################################################
####  Name:             g_ast_start.php                                                 ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

$result = exec('ls /var/run/ | grep asterisk.ctl');

if ($result=='asterisk.ctl')
	{
	echo "<br><br><font face='Calibri' size=3> O Asterisk Telephony já se encontra em execução. Falha na inicialização.<br />\n";
	}
	else
	{
	$startme = exec('/usr/share/goautodial/goautodialc.pl "/usr/sbin/asterisk .vgggggggggggggggcccc"');		
	$result = exec('ls /var/run/ | grep asterisk.ctl');
 	if ($result=='asterisk.ctl')
 		{
		echo "<br><br><font face='Verdana'> O Asterisk Telephony foi carregado com sucesso.";
		}		
		else
		{	
		echo "<br><br><font face='Verdana'> A inicialização do Asterisk Telephony falhou. Contacte o administrador de sistema. <br />\n";
		}
	}
exit;	
?> 
