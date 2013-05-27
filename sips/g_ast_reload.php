<?php
############################################################################################
####  Name:             g_ast_reload.php                                                ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################

require("includes/g_authenticate.php");
require("includes/g_hpage.php");

$stmt = "SELECT ASTmgrUSERNAME,ASTmgrSECRET,telnet_port FROM servers;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$m_ct = mysql_num_rows($rslt);
if ($m_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$m_user = $row[0];
	$m_pass	= $row[1];
	$m_port = $row[2];
	}
	
$result = exec('ls /var/run/ | grep asterisk.ctl');

if ($result=='asterisk.ctl')
	{
	echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>O Asterisk Telephony foi actualizado com sucesso.<br />\n";
	$socket = fsockopen("127.0.0.1", $m_port, $errno, $errstr, $timeout);
	fputs($socket, "Action: Login\r\n");
	fputs($socket, "UserName: $m_user\r\n");
	fputs($socket, "Secret: $m_pass\r\n\r\n");
	fputs($socket, "Action: Command\r\n");
	fputs($socket, "Command: reload\r\n\r\n");
	$wrets=fgets($socket,128);
	sleep(3);
	}
	else
	{
	echo "<br><br><FONT FACE='VERDANA' COLOR=BLACK SIZE=2>O Asterisk Telephony não se encontra em execução no seu servidor.<br />\n";
	//echo "<br>Please contact your dialer administrator!<br />\n";
	#echo "<br>Try <a href='g_confirmation.php?evnt_cmd=ast_sta'   TARGET='_parent'><i><b>STARTING</b></i></a> Asterisk telephony or contact your dialer administrator!<br />\n";
	}
exit;	
?> 
