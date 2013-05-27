<?php
# 
# Copyright (C) 2006  Matt Florell <vicidial@gmail.com>    LICENSE: GPLv2
#

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
        if (ereg("^VARDBserver", $DBgoline))		{$VARDBserver = $DBgoline;   		$VARDBserver = preg_replace("/.*=/","",$VARDBserver);}
        if (ereg("^VARDBdatabase", $DBgoline))		{$VARDBdatabase = $DBgoline;   		$VARDBdatabase = preg_replace("/.*=/","",$VARDBdatabase);}
        if (ereg("^VARDBuser", $DBgoline))		{$VARDBuser = $DBgoline;   		$VARDBuser = preg_replace("/.*=/","",$VARDBuser);}
        if (ereg("^VARDBpass", $DBgoline))		{$VARDBpass = $DBgoline;   		$VARDBpass = preg_replace("/.*=/","",$VARDBpass);}
        if (ereg("^VARDBport", $DBgoline))		{$VARDBport = $DBgoline;   		$VARDBport = preg_replace("/.*=/","",$VARDBport);}
	}
}
$linkd=mysql_connect("$VARDBserver", "$VARDBuser", "$VARDBpass");
mysql_select_db("$VARDBdatabase");
?>
