<?php
# 
# Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#

if ( file_exists("/etc/astguiclient.conf") )
{
$DBCagc = file("/etc/astguiclient.conf");
foreach ($DBCagc as $DBCline) 
	{
	$DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$DBCline);
	if (ereg("^PATHlogs", $DBCline))
		{$PATHlogs = $DBCline;   $PATHlogs = preg_replace("/.*=/","",$PATHlogs);}
	if (ereg("^PATHweb", $DBCline))
		{$WeBServeRRooT = $DBCline;   $WeBServeRRooT = preg_replace("/.*=/","",$WeBServeRRooT);}
	if (ereg("^VARDB_server", $DBCline))
		{$VARDB_server = $DBCline;   $VARDB_server = preg_replace("/.*=/","",$VARDB_server);}
	if (ereg("^VARDB_database", $DBCline))
		{$VARDB_database = $DBCline;   $VARDB_database = preg_replace("/.*=/","",$VARDB_database);}
	if (ereg("^VARDB_user", $DBCline))
		{$VARDB_user = $DBCline;   $VARDB_user = preg_replace("/.*=/","",$VARDB_user);}
	if (ereg("^VARDB_pass", $DBCline))
		{$VARDB_pass = $DBCline;   $VARDB_pass = preg_replace("/.*=/","",$VARDB_pass);}
	if (ereg("^VARDB_port", $DBCline))
		{$VARDB_port = $DBCline;   $VARDB_port = preg_replace("/.*=/","",$VARDB_port);}
	}	
}
else
{
#defaults for DB connection
$VARDB_server = 'localhost';
$VARDB_user = 'cron';
$VARDB_pass = '1234';
$VARDB_database = 'asterisk_vreporter';
$WeBServeRRooT = '/usr/local/apache2/htdocs';
}

$link=mysql_connect("$VARDB_server", "$VARDB_user", "$VARDB_pass");
mysql_select_db("$VARDB_database");

$local_DEF = 'Local/';
$conf_silent_prefix = '7';
$local_AMP = '@';
$ext_context = 'demo';
$recording_exten = '8309';
$WeBRooTWritablE = '0';
$non_latin = '0';	# set to 1 for UTF rules

if ( file_exists("/etc/goautodial.conf") )
{
$DBCgo = file("/etc/goautodial.conf");
foreach ($DBCgo as $DBgoline) 
	{
	$DBgoline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/","",$DBgoline);
	if (ereg("^VARADMINSHELLIP", $DBgoline))	{$VARADMINSHELLIP = $DBgoline;   	$VARADMINSHELLIP = preg_replace("/.*=/","",$VARADMINSHELLIP);}
	if (ereg("^VARSERVHOST", $DBgoline))		{$VARSERVHOST = $DBgoline;   		$VARSERVHOST = preg_replace("/.*=/","",$VARSERVHOST);}
	if (ereg("^VARSERVPORT", $DBgoline))		{$VARSERVPORT = $DBgoline;   		$VARSERVPORT = preg_replace("/.*=/","",$VARSERVPORT);}
	if (ereg("^VARSERVLISTEN", $DBgoline))		{$VARSERVLISTEN = $DBgoline;   		$VARSERVLISTEN = preg_replace("/.*=/","",$VARSERVLISTEN);}
	if (ereg("^VARSERVLOGGING", $DBgoline))		{$VARSERVLOGGING = $DBgoline;   	$VARSERVLOGGING = preg_replace("/.*=/","",$VARSERVLOGGING);}
	if (ereg("^VARSERVLOGS", $DBgoline))		{$VARSERVLOGS = $DBgoline;   		$VARSERVLOGS = preg_replace("/.*=/","",$VARSERVLOGS);}	
	}	
}
?>
