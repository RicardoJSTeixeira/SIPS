<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>GO CONTACT CENTER</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <style>
            .chzn-select{
                width: 350px;
            }
            #loader{
                background: #f9f9f9;
                top: 0px;
                left: 0px;
                position: absolute;
                height: 100%;
                width: 100%;
                z-index: 2;
            }
            #loader > img{
                position:absolute;
                left:50%;
                top:50%;
                margin-left: -33px;
                margin-top: -33px;
            }
        </style>
    <body>

        <?php
        require("../dbconnect.php");


        $PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'];
        $PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
        $stmt = "SELECT use_non_latin FROM system_settings;";
        $rslt = mysql_query($stmt, $link);
        if ($DB) {
            echo "$stmt\n";
        }
        $qm_conf_ct = mysql_num_rows($rslt);
        if ($qm_conf_ct > 0) {
            $row = mysql_fetch_row($rslt);
            $non_latin = $row[0];
        }
##### END SETTINGS LOOKUP #####
###########################################

        $PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]", "", $PHP_AUTH_USER);
        $PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]", "", $PHP_AUTH_PW);

        $stmt = "SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 8 and ast_admin_access='1';";
        if ($DB) {
            echo "|$stmt|\n";
        }
        if ($non_latin > 0) {
            $rslt = mysql_query("SET NAMES 'UTF8'");
        }
        $rslt = mysql_query($stmt, $link);
        $row = mysql_fetch_row($rslt);
        $auth = $row[0];

        if ((strlen($PHP_AUTH_USER) < 2) or (strlen($PHP_AUTH_PW) < 2) or (!$auth)) {
            Header("WWW-Authenticate: Basic realm=\"GO CONTACT CENTER\"");
            Header("HTTP/1.0 401 Unauthorized");
            echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
            echo "<html><head><title>GO CONTACT CENTER - Logout</title><script>function update(){top.location='../../index.php';}var refresh=setInterval('update()',1000);</script></head><body onload=refresh></body></html>";
            exit;
        }

        ?>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <div class=content>

            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Phones</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
<div id="wr"></div>

<?php

if ($ADD==41111111111)
	{
	
		echo "<FONT FACE=\"ARIAL,HELVETICA\" COLOR=BLACK SIZE=2>";

		$stmt="SELECT count(*) from phones where extension='$extension' and server_ip='$server_ip';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ( ($row[0] > 0) && ( ($extension != $old_extension) or ($server_ip != $old_server_ip) ) )
			{echo "<br>PHONE NOT MODIFIED - there is already a Phone in the system with this extension/server\n";}
		else
			{
			if ( (strlen($extension) < 1) or (strlen($server_ip) < 7) or (strlen($dialplan_number) < 1) or (strlen($voicemail_id) < 1) or (strlen($login) < 1)  or (strlen($pass) < 1))
				{echo "<br>PHONE NOT MODIFIED - Please go back and look at the data you entered\n";}
			else
				{
				echo "<br>PHONE MODIFIED: $extension\n";

				$stmt="UPDATE phones set extension='$extension', dialplan_number='$dialplan_number', voicemail_id='$voicemail_id', phone_ip='$phone_ip', computer_ip='$computer_ip', server_ip='$server_ip', login='$login', pass='$pass', status='$status', active='$active', phone_type='$phone_type', fullname='$fullname', company='$company', picture='$picture', protocol='$protocol', local_gmt='$local_gmt', ASTmgrUSERNAME='$ASTmgrUSERNAME', ASTmgrSECRET='$ASTmgrSECRET', login_user='$login_user', login_pass='$login_pass', login_campaign='$login_campaign', park_on_extension='$park_on_extension', conf_on_extension='$conf_on_extension', VICIDIAL_park_on_extension='$VICIDIAL_park_on_extension', VICIDIAL_park_on_filename='$VICIDIAL_park_on_filename', monitor_prefix='$monitor_prefix', recording_exten='$recording_exten', voicemail_exten='$voicemail_exten', voicemail_dump_exten='$voicemail_dump_exten', ext_context='$ext_context', dtmf_send_extension='$dtmf_send_extension', call_out_number_group='$call_out_number_group', client_browser='$client_browser', install_directory='$install_directory', local_web_callerID_URL='" . mysql_real_escape_string($local_web_callerID_URL) . "', VICIDIAL_web_URL='" . mysql_real_escape_string($VICIDIAL_web_URL) . "', AGI_call_logging_enabled='$AGI_call_logging_enabled', user_switching_enabled='$user_switching_enabled', conferencing_enabled='$conferencing_enabled', admin_hangup_enabled='$admin_hangup_enabled', admin_hijack_enabled='$admin_hijack_enabled', admin_monitor_enabled='$admin_monitor_enabled', call_parking_enabled='$call_parking_enabled', updater_check_enabled='$updater_check_enabled', AFLogging_enabled='$AFLogging_enabled', QUEUE_ACTION_enabled='$QUEUE_ACTION_enabled', CallerID_popup_enabled='$CallerID_popup_enabled', voicemail_button_enabled='$voicemail_button_enabled', enable_fast_refresh='$enable_fast_refresh', fast_refresh_rate='$fast_refresh_rate', enable_persistant_mysql='$enable_persistant_mysql', auto_dial_next_number='$auto_dial_next_number', VDstop_rec_after_each_call='$VDstop_rec_after_each_call', DBX_server='$DBX_server', DBX_database='$DBX_database', DBX_user='$DBX_user', DBX_pass='$DBX_pass', DBX_port='$DBX_port', DBY_server='$DBY_server', DBY_database='$DBY_database', DBY_user='$DBY_user', DBY_pass='$DBY_pass', DBY_port='$DBY_port', outbound_cid='$outbound_cid', enable_sipsak_messages='$enable_sipsak_messages', email='$email', template_id='$template_id', conf_override='$conf_override',phone_context='$phone_context',phone_ring_timeout='$phone_ring_timeout',conf_secret='$conf_secret', delete_vm_after_email='$delete_vm_after_email',is_webphone='$is_webphone',use_external_server_ip='$use_external_server_ip',codecs_list='$codecs_list',codecs_with_template='$codecs_with_template',webphone_dialpad='$webphone_dialpad',on_hook_agent='$on_hook_agent' where extension='$old_extension' and server_ip='$old_server_ip';";
				$rslt=mysql_query($stmt, $link);

				$stmtA="UPDATE servers SET rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y' and server_ip='$server_ip';";
				$rslt=mysql_query($stmtA, $link);

				### LOG INSERTION Admin Log Table ###
				$SQL_log = "$stmt|";
				$SQL_log = ereg_replace(';','',$SQL_log);
				$SQL_log = addslashes($SQL_log);
				$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='PHONES', event_type='MODIFY', record_id='$extension', event_code='ADMIN MODIFY PHONE', event_sql=\"$SQL_log\", event_notes='Server IP: $server_ip';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				}
			}
	
	$ADD=31111111111;	# go to phone modification form below
	}
        

	
		
		$stmt="SELECT extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,messages,old_messages,protocol,local_gmt,ASTmgrUSERNAME,ASTmgrSECRET,login_user,login_pass,login_campaign,park_on_extension,conf_on_extension,VICIDIAL_park_on_extension,VICIDIAL_park_on_filename,monitor_prefix,recording_exten,voicemail_exten,voicemail_dump_exten,ext_context,dtmf_send_extension,call_out_number_group,client_browser,install_directory,local_web_callerID_URL,VICIDIAL_web_URL,AGI_call_logging_enabled,user_switching_enabled,conferencing_enabled,admin_hangup_enabled,admin_hijack_enabled,admin_monitor_enabled,call_parking_enabled,updater_check_enabled,AFLogging_enabled,QUEUE_ACTION_enabled,CallerID_popup_enabled,voicemail_button_enabled,enable_fast_refresh,fast_refresh_rate,enable_persistant_mysql,auto_dial_next_number,VDstop_rec_after_each_call,DBX_server,DBX_database,DBX_user,DBX_pass,DBX_port,DBY_server,DBY_database,DBY_user,DBY_pass,DBY_port,outbound_cid,enable_sipsak_messages,email,template_id,conf_override,phone_context,phone_ring_timeout,conf_secret,delete_vm_after_email,is_webphone,use_external_server_ip,codecs_list,codecs_with_template,webphone_dialpad,on_hook_agent from phones where extension='$extension' and server_ip='$server_ip';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		
	?>


		<form action="self" method=POST>
		<input type=hidden name=ADD value=41111111111>
		<input type=hidden name=old_extension value="<?=$row[0]?>">
		<input type=hidden name=old_server_ip value="<?=$row[5]?>">
		<input type=hidden name=client_browser value="<?=$row[34]?>">
		<input type=hidden name=install_directory value="<?=$row[35]?>">
                        <div class="formRow op fix">
                            <label>Phone Extension:</label>
                            <div class="formRight">
                                <input type=text name=extension size=20 maxlength=100 value="<?=$row[0]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Dial Plan Number:</label>
                            <div class="formRight">
                                <input type=text name=dialplan_number size=15 maxlength=20 value="<?=$row[1]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Voicemail Box:</label>
                            <div class="formRight">
                                <input type=text name=voicemail_id size=10 maxlength=10 value="<?=$row[2]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Outbound CallerID:</label>
                            <div class="formRight">
                                <input type=text name=outbound_cid size=10 maxlength=20 value="<?=$row[65]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Phone IP address:</label>
                            <div class="formRight">
                                <input type=text name=phone_ip size=20 maxlength=15 value="<?=$row[3]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Computer IP address:</label>
                            <div class="formRight">
                                <input type=text name=computer_ip size=20 maxlength=15 value="<?=$row[4]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Server IP:</label>
                            <div class="formRight">
                                <select size=1 name=server_ip>
                                    <?=$servers_list?>
                                <option SELECTED>$row[5]</option>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Agent Screen Login:</label>
                            <div class="formRight">
                                <input type=text name=login size=15 maxlength=15 value="<?=$row[6]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Login Password:</label>
                            <div class="formRight">
                                <input type=text name=pass size=10 maxlength=10 value="<?=$row[7]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Registration Password:</label>
                            <div class="formRight">
                                <input type=text id=reg_pass name=conf_secret size=20 maxlength=20 value="<?=$row[72]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Set As Webphone:</label>
                            <div class="formRight">
                                <select size=1 name=is_webphone><option>Y</option><option>N</option><option selected><?=$row[74]?></option></select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Webphone Dialpad:</label>
                            <div class="formRight">
                                <select size=1 name=webphone_dialpad><option>Y</option><option>N</option><option>TOGGLE</option><option>TOGGLE_OFF</option><option SELECTED><?=$row[78]?></option></select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Use External Server IP:</label>
                            <div class="formRight">
                                <select size=1 name=use_external_server_ip><option>Y</option><option>N</option><option selected><?=$row[75]?></option></select>
                            </div>
                        </div>
                        <div class="formRow op fix">
                            <label>Status:</label>
                            <div class="formRight">
                                <select size=1 name=status><option>ACTIVE</option><option>SUSPENDED</option><option>CLOSED</option><option>PENDING</option><option>ADMIN</option><option selected><?=$row[8]?></option></select>
                            </div>
                        </div>
                        <div class="formRow op fix">
                            <label>Active Account:</label>
                            <div class="formRight">
                                <select size=1 name=active><option>Y</option><option>N</option><option selected><?=$row[9]?></option></select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Phone Type:</label>
                            <div class="formRight">
                                <input type=text name=phone_type size=20 maxlength=50 value="<?=$row[10]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Full Name:</label>
                            <div class="formRight">
                                <input type=text name=fullname size=20 maxlength=50 value="<?=$row[11]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Email:</label>
                            <div class="formRight">
                                <input type=text name=email size=50 maxlength=100 value="<?=$row[67]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Delete Voicemail After Email:</label>
                            <div class="formRight">
                                <select size=1 name=delete_vm_after_email><option>Y</option><option>N</option><option selected><?=$row[73]?></option></select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Company:</label>
                            <div class="formRight">
                                <input type=text name=company size=10 maxlength=10 value="<?=$row[12]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Picture:</label>
                            <div class="formRight">
                                <input type=text name=picture size=20 maxlength=19 value="<?=$row[13]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>New Messages:</label>
                            <div class="formRight">
                                <b><?=$row[14]?></b>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Old Messages:</label>
                            <div class="formRight">
                                <b><?=$row[15]?></b>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Client Protocol:</label>
                            <div class="formRight">
                                <select size=1 name=protocol><option>SIP</option><option>Zap</option><option>IAX2</option><option>EXTERNAL</option><option selected><?=$row[16]?></option></select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Local GMT:</label>
                            <div class="formRight">
                                <select size=1 name=local_gmt><option>12.75</option><option>12.00</option><option>11.00</option><option>10.00</option><option>9.50</option><option>9.00</option><option>8.00</option><option>7.00</option><option>6.50</option><option>6.00</option><option>5.75</option><option>5.50</option><option>5.00</option><option>4.50</option><option>4.00</option><option>3.50</option><option>3.00</option><option>2.00</option><option>1.00</option><option>0.00</option><option>-1.00</option><option>-2.00</option><option>-3.00</option><option>-3.50</option><option>-4.00</option><option>-5.00</option><option>-6.00</option><option>-7.00</option><option>-8.00</option><option>-9.00</option><option>-10.00</option><option>-11.00</option><option>-12.00</option><option selected>$row[17]</option></select> (Do NOT Adjust for DST)
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Phone Ring Timeout:</label>
                            <div class="formRight">
                                <input type=text name=phone_ring_timeout size=4 maxlength=5 value="<?=$row[71]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>On-Hook Agent:</label>
                            <div class="formRight">
                                <select size=1 name=on_hook_agent><option>Y</option><option>N</option><option selected><?=$row[79]?></option></select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Manager Login:</label>
                            <div class="formRight">
                                <input type=text name=ASTmgrUSERNAME size=20 maxlength=20 value="<?=$row[18]?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Manager Secret:</label>
                            <div class="formRight">
                                <input type=text name=ASTmgrSECRET size=20 maxlength=20 value="<?=$row[19]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>VICIDIAL Default User:</label>
                            <div class="formRight">
                                <input type=text name=login_user size=20 maxlength=20 value="<?=$row[20]?>">
                            </div>
                        </div>  dave weckl
                        <div class="formRow op fix">
                            <label>VICIDIAL Default Pass:</label>
                            <div class="formRight">
                                <input type=text name=login_pass size=20 maxlength=20 value="<?=$row[21]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>VICIDIAL Default Campaign:</label>
                            <div class="formRight">
                                <input type=text name=login_campaign size=10 maxlength=10 value="<?=$row[22]?>">
                            </div>
                        </div>   
                        <div class="formRow op fix">
                            <label>Park Exten:</label>
                            <div class="formRight">
                                <input type=text name=park_on_extension size=10 maxlength=10 value="<?=$row[23]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Conf Exten:</label>
                            <div class="formRight">
                                <input type=text name=conf_on_extension size=10 maxlength=10 value="<?=$row[24]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>VICIDIAL Park Exten:</label>
                            <div class="formRight">
                                <input type=text name=VICIDIAL_park_on_extension size=10 maxlength=10 value="<?=$row[25]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>VICIDIAL Park File:</label>
                            <div class="formRight">
                                <input type=text name=VICIDIAL_park_on_filename size=10 maxlength=10 value="<?=$row[26]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Monitor Prefix:</label>
                            <div class="formRight">
                                <input type=text name=monitor_prefix size=10 maxlength=10 value="<?=$row[27]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Recording Exten:</label>
                            <div class="formRight">
                                <input type=text name=recording_exten size=10 maxlength=10 value="<?=$row[28]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>VMailMain Exten:</label>
                            <div class="formRight">
                                <input type=text name=voicemail_exten size=10 maxlength=10 value="<?=$row[29]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>VMailDump Exten:</label>
                            <div class="formRight">
                                <input type=text name=voicemail_dump_exten size=20 maxlength=20 value="<?=$row[30]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Exten Context:</label>
                            <div class="formRight">
                                <input type=text name=ext_context size=20 maxlength=20 value="<?=$row[31]?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Phone Context:</label>
                            <div class="formRight">
                                <input type=text name=phone_context size=20 maxlength=20 value="<?=$row[70]?>">
                            </div>
                        </div>    
                        <div class="formRow op fix">
                            <label>Allowed Codecs:</label>
                            <div class="formRight">
                                <input type=text name=codecs_list size=40 maxlength=100 value="<?=$row[76]?>">
                            </div>
                        </div>    
                        <div class="formRow op fix">
                            <label>Allowed Codecs With Template:</label>
                            <div class="formRight">
                                <select size=1 name=codecs_with_template><option>1</option><option>0</option><option selected>$row[77]</option></select>
                            </div>
                        </div>    
                        <div class="formRow op fix">
                            <label>DTMFSend Channel:</label>
                            <div class="formRight">
                                <input type=text name=dtmf_send_extension size=40 maxlength=100 value=\"$row[32]\">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Outbound Call Group:</label>
                            <div class="formRight">
                                <input type=text name=call_out_number_group size=40 maxlength=100 value=\"$row[33]\">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>CallerID URL:</label>
                            <div class="formRight">
                                <input type=text name=local_web_callerID_URL size=40 maxlength=255 value=\"$row[36]\">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>VICIDIAL Default URL:</label>
                            <div class="formRight">
                                <input type=text name=VICIDIAL_web_URL size=40 maxlength=255 value=\"$row[37]\">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Call Logging:</label>
                            <div class="formRight">
                                <select size=1 name=AGI_call_logging_enabled><option>1</option><option>0</option><option selected>$row[38]</option></select>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>User Switching:</label>
                            <div class="formRight">
                                <select size=1 name=user_switching_enabled><option>1</option><option>0</option><option selected>$row[39]</option></select>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Conferencing:</label>
                            <div class="formRight">
                                <select size=1 name=conferencing_enabled><option>1</option><option>0</option><option selected>$row[40]</option></select>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Admin Hang Up:</label>
                            <div class="formRight">
                                <select size=1 name=admin_hangup_enabled><option>1</option><option>0</option><option selected>$row[41]</option></select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Admin Hijack:</label>
                            <div class="formRight">
                                <select size=1 name=admin_hijack_enabled><option>1</option><option>0</option><option selected>$row[42]</option></select>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Admin Monitor:</label>
                            <div class="formRight">
                                <select size=1 name=admin_monitor_enabled><option>1</option><option>0</option><option selected>$row[43]</option></select>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Call Park:</label>
                            <div class="formRight">
                                <select size=1 name=call_parking_enabled><option>1</option><option>0</option><option selected>$row[44]</option></select>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Updater Check:</label>
                            <div class="formRight">
                                <select size=1 name=updater_check_enabled><option>1</option><option>0</option><option selected>$row[45]</option></select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>AF Logging:</label>
                            <div class="formRight">
                                <select size=1 name=AFLogging_enabled><option>1</option><option>0</option><option selected>$row[46]</option></select>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>Queue Enabled:</label>
                            <div class="formRight">
                                <select size=1 name=QUEUE_ACTION_enabled><option>1</option><option>0</option><option selected>$row[47]</option></select>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>CallerID Popup:</label>
                            <div class="formRight">
                                <select size=1 name=CallerID_popup_enabled><option>1</option><option>0</option><option selected>$row[48]</option></select>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label>VMail Button:</label>
                            <div class="formRight">
                                <select size=1 name=voicemail_button_enabled><option>1</option><option>0</option><option selected>$row[49]</option></select>
                            </div>
                        </div>  
                
		<tr><td>: </td><td align=left>$NWB#phones-CallerID_popup_enabled$NWE</td></tr>\n";
		<tr><td>: </td><td align=left>$NWB#phones-voicemail_button_enabled$NWE</td></tr>\n";
		<tr><td>Fast Refresh: </td><td align=left><select size=1 name=enable_fast_refresh><option>1</option><option>0</option><option selected>$row[50]</option></select>$NWB#phones-enable_fast_refresh$NWE</td></tr>\n";
		<tr><td>Fast Refresh Rate: </td><td align=left><input type=text size=5 name=fast_refresh_rate value=\"$row[51]\">(in ms)$NWB#phones-fast_refresh_rate$NWE</td></tr>\n";
		<tr><td>Persistant MySQL: </td><td align=left><select size=1 name=enable_persistant_mysql><option>1</option><option>0</option><option selected>$row[52]</option></select>$NWB#phones-enable_persistant_mysql$NWE</td></tr>\n";
		<tr><td>Auto Dial Next Number: </td><td align=left><select size=1 name=auto_dial_next_number><option>1</option><option>0</option><option selected>$row[53]</option></select>$NWB#phones-auto_dial_next_number$NWE</td></tr>\n";
		<tr><td>Stop Rec after each call: </td><td align=left><select size=1 name=VDstop_rec_after_each_call><option>1</option><option>0</option><option selected>$row[54]</option></select>$NWB#phones-VDstop_rec_after_each_call$NWE</td></tr>\n";
		<tr><td>Enable SIPSAK Messages: </td><td align=left><select size=1 name=enable_sipsak_messages><option>1</option><option>0</option><option selected>$row[66]</option></select>$NWB#phones-enable_sipsak_messages$NWE</td></tr>\n";
		<tr><td>DBX Server: </td><td align=left><input type=text name=DBX_server size=15 maxlength=15 value=\"$row[55]\"> (Primary DB Server)$NWB#phones-DBX_server$NWE</td></tr>\n";
		<tr><td>DBX Database: </td><td align=left><input type=text name=DBX_database size=15 maxlength=15 value=\"$row[56]\"> (Primary Server Database)$NWB#phones-DBX_database$NWE</td></tr>\n";
		<tr><td>DBX User: </td><td align=left><input type=text name=DBX_user size=15 maxlength=15 value=\"$row[57]\"> (Primary DB Login)$NWB#phones-DBX_user$NWE</td></tr>\n";
		<tr><td>DBX Pass: </td><td align=left><input type=text name=DBX_pass size=15 maxlength=15 value=\"$row[58]\"> (Primary DB Secret)$NWB#phones-DBX_pass$NWE</td></tr>\n";
		<tr><td>DBX Port: </td><td align=left><input type=text name=DBX_port size=6 maxlength=6 value=\"$row[59]\"> (Primary DB Port)$NWB#phones-DBX_port$NWE</td></tr>\n";
		<tr><td>DBY Server: </td><td align=left><input type=text name=DBY_server size=15 maxlength=15 value=\"$row[60]\"> (Secondary DB Server)$NWB#phones-DBY_server$NWE</td></tr>\n";
		<tr><td>DBY Database: </td><td align=left><input type=text name=DBY_database size=15 maxlength=15 value=\"$row[61]\"> (Secondary Server Database)$NWB#phones-DBY_database$NWE</td></tr>\n";
		<tr><td>DBY User: </td><td align=left><input type=text name=DBY_user size=15 maxlength=15 value=\"$row[62]\"> (Secondary DB Login)$NWB#phones-DBY_user$NWE</td></tr>\n";
		<tr><td>DBY Pass: </td><td align=left><input type=text name=DBY_pass size=15 maxlength=15 value=\"$row[63]\"> (Secondary DB Secret)$NWB#phones-DBY_pass$NWE</td></tr>\n";
		<tr><td>DBY Port: </td><td align=left><input type=text name=DBY_port size=6 maxlength=6 value=\"$row[64]\"> (Secondary DB Port)$NWB#phones-DBY_port$NWE</td></tr>\n";

		<tr><td><a href=\"$PHP_SELF?ADD=331111111111&template_id=$row[68]\">Template ID</a>: </td><td align=left><select size=1 name=template_id>\n";
		<?php
                            $stmt="SELECT template_id,template_name from vicidial_conf_templates order by template_id";
		$rslt=mysql_query($stmt, $link);
		$templates_to_print = mysql_num_rows($rslt);
		$templates_list='<option SELECTED>--NONE--</option>';
		$o=0;
		while ($templates_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$templates_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
			}?>
		$templates_list";
		<option SELECTED>$row[68]</option>\n";
		</select>$NWB#phones-template_id$NWE</td></tr>\n";

		<tr><td>Conf Override: </td><td align=left><TEXTAREA NAME=conf_override ROWS=10 COLS=70>$row[69]</TEXTAREA> $NWB#phones-conf_override$NWE</td></tr>\n";

		<tr><td style='border-bottom:none' colspan=2><input type=submit class='styled-button' name=submit VALUE=SUBMIT></td></tr>\n";
		</TABLE></center>\n";


		
?>
