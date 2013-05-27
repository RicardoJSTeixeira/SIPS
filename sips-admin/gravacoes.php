<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title></title>
<? require("admin_header.php"); ?>

</head>

<body>
<?php
require("dbconnect.php");
require("functions.php");

$report_name = 'User Stats';
$db_source = 'M';
if (isset($_GET["user"]))					{$user=$_GET["user"];}
	elseif (isset($_POST["user"]))			{$user=$_POST["user"];}
	
echo "<TABLE width=750 cellspacing=0 cellpadding=1 class='userlist' align='center'>\n";
echo "<tr><td><font size=1># </td><td align=left><font size=2> ID Lead</td><td><font size=2>Data/Hora </td><td align=left><font size=2>Segundos </td><td align=left><font size=2> &nbsp; ID Gravação</td><td align=center><font size=2>Nome ficheiro</td><td align=center><font size=2>URL &nbsp; </td></tr>\n";

	$stmt="select recording_id,channel,server_ip,extension,start_time,start_epoch,end_time,end_epoch,length_in_sec,length_in_min,filename,location,lead_id,user,vicidial_id from recording_log where user='" . mysql_real_escape_string($user) . "' and start_time >= '" . mysql_real_escape_string($begin_date) . " 0:00:01'  and start_time <= '" . mysql_real_escape_string($end_date) . " 23:59:59' order by recording_id desc limit 10000;";
	$rslt=mysql_query($stmt, $link);
	$logs_to_print = mysql_num_rows($rslt);

	$u=0;
	while ($logs_to_print > $u) 
		{
		$row=mysql_fetch_row($rslt);
		if (eregi("1$|3$|5$|7$|9$", $u))
			{$bgcolor='bgcolor="#B9CBFD"';} 
		else
			{$bgcolor='bgcolor="#9BB9FB"';}

		$location = $row[11];

		if (strlen($location)>2)
			{
			$URLserver_ip = $location;
			$URLserver_ip = eregi_replace('http://','',$URLserver_ip);
			$URLserver_ip = eregi_replace('https://','',$URLserver_ip);
			$URLserver_ip = eregi_replace("\/.*",'',$URLserver_ip);
			$stmt="select count(*) from servers where server_ip='$URLserver_ip';";
			$rsltx=mysql_query($stmt, $link);
			$rowx=mysql_fetch_row($rsltx);
			
			if ($rowx[0] > 0)
				{
				$stmt="select recording_web_link,alt_server_ip,external_server_ip from servers where server_ip='$URLserver_ip';";
				$rsltx=mysql_query($stmt, $link);
				$rowx=mysql_fetch_row($rsltx);
				
				if (eregi("ALT_IP",$rowx[0]))
					{
					$location = eregi_replace($URLserver_ip, $rowx[1], $location);
					}
				if (eregi("EXTERNAL_IP",$rowx[0]))
					{
					$location = eregi_replace($URLserver_ip, $rowx[2], $location);
					}
				}
			}

		if (strlen($location)>30)
			{$locat = substr($location,0,27);  $locat = "$locat...";}
		else
			{$locat = $location;}
		if ( (eregi("ftp",$location)) or (eregi("http",$location)) )
			{$location = "<a href=\"$location\">$locat</a>";}
		else
			{$location = $locat;}
		$u++;
		echo "<tr $bgcolor>";
		echo "<td><font size=1>$u</td>";
		echo "<td align=left><font size=2> <A HREF=\"admin_modify_lead.php?lead_id=$row[12]\" target=\"_blank\">$row[12]</A> </td>";
		echo "<td align=left><font size=2> $row[4] </td>\n";
		echo "<td align=left><font size=2> $row[8] </td>\n";
		echo "<td align=left><font size=2> $row[0] </td>\n";
		echo "<td align=center><font size=2> $row[10] </td>\n";
		echo "<td align=right><font size=2> $location &nbsp; </td>\n";
		echo "</tr>\n";
		}

echo "</TABLE><BR><BR>\n"; ?>


</body>
</html>