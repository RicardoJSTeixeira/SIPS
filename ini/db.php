<?php

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
		if (ereg("^VARserver_ip", $DBCline))
			{$WEBserver_ip = $DBCline;   $WEBserver_ip = preg_replace("/.*=/","",$WEBserver_ip);}
		if (ereg("^VARDB_server", $DBCline))
			{$VARDB_server = $DBCline;   $VARDB_server = preg_replace("/.*=/","",$VARDB_server);}
		if (ereg("^VARDB_database", $DBCline))
			{$VARDB_database = $DBCline;   $VARDB_database = preg_replace("/.*=/","",$VARDB_database);}
		if (ereg("^VARDB_user", $DBCline))
			{$VARDB_user = $DBCline;   $VARDB_user = preg_replace("/.*=/","",$VARDB_user);}
		if (ereg("^VARDB_pass", $DBCline))
			{$VARDB_pass = $DBCline;   $VARDB_pass = preg_replace("/.*=/","",$VARDB_pass);}
		if (ereg("^VARDB_custom_user", $DBCline))
			{$VARDB_custom_user = $DBCline;   $VARDB_custom_user = preg_replace("/.*=/","",$VARDB_custom_user);}
		if (ereg("^VARDB_custom_pass", $DBCline))
			{$VARDB_custom_pass = $DBCline;   $VARDB_custom_pass = preg_replace("/.*=/","",$VARDB_custom_pass);}
		if (ereg("^VARDB_port", $DBCline))
			{$VARDB_port = $DBCline;   $VARDB_port = preg_replace("/.*=/","",$VARDB_port);}
		}
	}

$host = "mysql:host=".$VARDB_server .";dbname=" . $VARDB_database . ";charset=utf8";

try {
    $db = new PDO($host, $VARDB_user, $VARDB_pass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

