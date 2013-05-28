<?php
if ($ADD==21111111111)
	{
	##### BEGIN ID override optional section, if enabled it increments user by 1 ignoring entered value #####
	$stmt = "SELECT value FROM vicidial_override_ids where id_table='phones' and active='1';";
	$rslt=mysql_query($stmt, $link);
	$voi_ct = mysql_num_rows($rslt);
	if ($voi_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$extension = ($row[0] + 1);

		$stmt="UPDATE vicidial_override_ids SET value='$extension' where id_table='phones' and active='1';";
		$rslt=mysql_query($stmt, $link);
		}
	##### END ID override optional section #####

	echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";
	$stmt="SELECT count(*) from phones where extension='$extension' and server_ip='$server_ip';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{echo "<br>PHONE NOT ADDED - there is already a Phone in the system with this extension/server\n";}
	else
		{
		$stmt="SELECT count(*) from phones where login='$login';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{echo "<br>PHONE NOT ADDED - there is already a Phone in the system with this login\n";}
		else
			{
			$stmt="SELECT count(*) from phones_alias where alias_id='$login';";
			$rslt=mysql_query($stmt, $link);
			$row=mysql_fetch_row($rslt);
			if ($row[0] > 0)
				{echo "<br>PHONE NOT ADDED - there is already a Phone alias in the system with this login\n";}
			else
				{
				$stmt="SELECT count(*) from vicidial_voicemail where voicemail_id='$voicemail_id';";
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				if ($row[0] > 0)
					{echo "<br>PHONE NOT ADDED - there is already a Voicemail ID in the system with this ID\n";}
				else
					{
					if ( (strlen($extension) < 1) or (strlen($server_ip) < 7) or (strlen($dialplan_number) < 1) or (strlen($voicemail_id) < 1) or (strlen($login) < 1)  or (strlen($pass) < 1))
						{
						echo "<BR>PHONE NOT ADDED - Please go back and look at the data you entered\n";
						echo "<BR>The following fields must have data: extension, server_ip, dialplan_number, voicemail_id, login, pass\n";
						}
					else
						{
						echo "<br>PHONE ADDED\n";

						$stmt="INSERT INTO phones (extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,protocol,local_gmt,outbound_cid,conf_secret) values('$extension','$dialplan_number','$voicemail_id','$phone_ip','$computer_ip','$server_ip','$login','$pass','$status','$active','$phone_type','$fullname','$company','$picture','$protocol','$local_gmt','$outbound_cid','$conf_secret');";
						$rslt=mysql_query($stmt, $link);

						$stmtA="UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
						$rslt=mysql_query($stmtA, $link);

						### LOG INSERTION Admin Log Table ###
						$SQL_log = "$stmt|";
						$SQL_log = ereg_replace(';','',$SQL_log);
						$SQL_log = addslashes($SQL_log);
						$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='PHONES', event_type='ADD', record_id='$extension', event_code='ADMIN ADD PHONE', event_sql=\"$SQL_log\", event_notes='';";
						if ($DB) {echo "|$stmt|\n";}
						$rslt=mysql_query($stmt, $link);
						}
					}
				}
			}
		}
	$ADD=31111111111;
	}

	if ($LOGast_admin_access==1)
		{
		##### BEGIN ID override optional section, if enabled it increments user by 1 ignoring entered value #####
		$stmt = "SELECT count(*) FROM vicidial_override_ids where id_table='phones' and active='1';";
		$rslt=mysql_query($stmt, $link);
		$voi_ct = mysql_num_rows($rslt);
		if ($voi_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$voi_count = "$row[0]";
			}
		##### END ID override optional section #####
		
		
		echo "<table id='cc-sub-menu'>";
		echo "<tr>";
		
		echo "<td style='border-right:1px solid #6678b1; font-weight: bolder;'><a style='text-decoration:none;' href=\"$PHP_SELF?ADD=10000000000\"> TELEFONES </a></td>";
		echo "<td><a href=\"$PHP_SELF?ADD=11111111111\"> Novo Telefone </a></td>";
		echo "<td><a href=\"$PHP_SELF?ADD=12000000000\"> Phone Alias </a></td>";
		echo "<td><a href=\"$PHP_SELF?ADD=12111111111\"> New Phone Alias </a></td>";
		echo "<td><a href=\"$PHP_SELF?ADD=13000000000\"> Group Phone Alias </a></td>";
		echo "<td><a href=\"$PHP_SELF?ADD=13111111111\"> New Group Phone Alias </a></td>";
		
		echo "</tr>";	
		echo "</table>";

		echo "<table id='frame-container'>";

		echo "<form action=$PHP_SELF method=POST>";
		echo "<input type=hidden name=ADD value=21111111111>";
		echo "<table id='form-list'>";

		if ($voi_count > 0)
			{
			echo "<tr><td>Phone Extension: </td><td align=left>Auto-Generated $NWB#phones-extension$NWE</td></tr>\n";
			}
		else
			{
			echo "<tr><td>Phone Extension: </td><td align=left><input type=text name=extension size=20 maxlength=100 value=\"\">$NWB#phones-extension$NWE</td></tr>\n";
			}
		echo "<tr><td>Dial Plan Number: </td><td align=left><input type=text name=dialplan_number size=15 maxlength=20> (digits only)$NWB#phones-dialplan_number$NWE</td></tr>\n";
		echo "<tr><td>Voicemail Box: </td><td align=left><input type=text name=voicemail_id size=10 maxlength=10> (digits only)$NWB#phones-voicemail_id$NWE</td></tr>\n";
		echo "<tr><td>Outbound CallerID: </td><td align=left><input type=text name=outbound_cid size=10 maxlength=20> (digits only)$NWB#phones-outbound_cid$NWE</td></tr>\n";
#		echo "<tr><td>Phone IP address: </td><td align=left><input type=text name=phone_ip size=20 maxlength=15> (optional)$NWB#phones-phone_ip$NWE</td></tr>\n";
#		echo "<tr><td>Computer IP address: </td><td align=left><input type=text name=computer_ip size=20 maxlength=15> (optional)$NWB#phones-computer_ip$NWE</td></tr>\n";
		echo "<tr><td>Server IP: </td><td align=left><select size=1 name=server_ip>\n";

		echo "$servers_list";
		echo "</select>$NWB#phones-server_ip$NWE</td></tr>\n";
		echo "<tr><td>Agent Screen Login: </td><td align=left><input type=text name=login size=15 maxlength=15>$NWB#phones-login$NWE</td></tr>\n";
		echo "<tr><td>Login Password: </td><td align=left><input type=text name=pass size=10 maxlength=20 value=\"$SSdefault_phone_login_password\">$NWB#phones-pass$NWE</td></tr>\n";
		echo "<tr><td>Registration Password: </td><td align=left style=\"display:table-cell; vertical-align:middle;\"><input type=text id=reg_pass name=conf_secret size=20 maxlength=20 value=\"$SSdefault_phone_registration_password\" onkeyup=\"return pwdChanged('reg_pass','reg_pass_img');\">$NWB#phones-conf_secret$NWE &nbsp; &nbsp; Strength: <IMG id=reg_pass_img src='images/pixel.gif' style=\"vertical-align:middle;\" onLoad=\"return pwdChanged('reg_pass','reg_pass_img');\"></td></tr>\n";
		echo "<tr><td>Status: </td><td align=left><select size=1 name=status><option SELECTED>ACTIVE</option><option>SUSPENDED</option><option>CLOSED</option><option>PENDING</option><option>ADMIN</option></select>$NWB#phones-status$NWE</td></tr>\n";
		echo "<tr><td>Active Account: </td><td align=left><select size=1 name=active><option SELECTED>Y</option><option>N</option></select>$NWB#phones-active$NWE</td></tr>\n";
		echo "<tr><td>Phone Type: </td><td align=left><input type=text name=phone_type size=20 maxlength=50>$NWB#phones-phone_type$NWE</td></tr>\n";
		echo "<tr><td>Full Name: </td><td align=left><input type=text name=fullname size=20 maxlength=50>$NWB#phones-fullname$NWE</td></tr>\n";
#		echo "<tr><td>Company: </td><td align=left><input type=text name=company size=10 maxlength=10>$NWB#phones-company$NWE</td></tr>\n";
#		echo "<tr><td>Picture: </td><td align=left><input type=text name=picture size=20 maxlength=19>$NWB#phones-picture$NWE</td></tr>\n";
		echo "<tr><td>Client Protocol: </td><td align=left><select size=1 name=protocol><option SELECTED>SIP</option><option>Zap</option><option>IAX2</option><option>EXTERNAL</option></select>$NWB#phones-protocol$NWE</td></tr>\n";
		echo "<tr><td>Local GMT: </td><td align=left><select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option SELECTED>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option></select> (Do NOT Adjust for DST)$NWB#phones-local_gmt$NWE</td></tr>\n";
		echo "<tr><td style='border-bottom:none' colspan=2><input type=submit class='styled-button' name=submit VALUE=SUBMIT></td></tr>\n";
		echo "</table>";
		}
	else
		{
		echo "<script type=text/javascript> alert('Não tem permissão para visualizar esta página.'); </script>";
		exit;
		}//yellow
?>
