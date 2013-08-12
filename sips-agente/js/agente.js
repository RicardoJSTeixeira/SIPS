

// ################################################################################
// Send Hangup command for Live call connected to phone now to Manager
	function livehangup_send_hangup(taskvar)
{
    var queryCID = "HLagcW" + epoch_sec + user_abb;
    var hangupvalue = taskvar;
    livehangup_query = {server_ip: server_ip, session_name: session_name, user: user, pass: pass, ACTION: "Hangup", format: "text", channel: hangupvalue, queryCID: queryCID, log_campaign: campaign};
    $.post('manager_send.php', livehangup_query, function(data) {
        Nactiveext = data;
        alert_box(data);
    });
}

// ################################################################################
// Send volume control command for meetme participant
	function volume_control(taskdirection,taskvolchannel,taskagentmute) 
		{
		if (taskagentmute=='AgenT')
			{
			taskvolchannel = agentchannel;
			}
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var queryCID = "VCagcW" + epoch_sec + user_abb;
			var volchanvalue = taskvolchannel;
			livevolume_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=VolumeControl&format=text&channel=" + volchanvalue + "&stage=" + taskdirection + "&exten=" + session_id + "&ext_context=" + ext_context + "&queryCID=" + queryCID;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(livevolume_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		if (taskagentmute=='AgenT')
			{
			if (taskdirection=='MUTING')
				{
                document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('UNMUTE','" + agentchannel + "','AgenT');return false;\"><img src=\"./images/vdc_volume_UNMUTE.gif\" border=\"0\" /></a>";
				}
			else
				{
                document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><img src=\"./images/vdc_volume_MUTE.gif\" border=\"0\" /></a>";
				}
			}

		}
                
                var inOUT_hack,lead_id_hack;
        function nc_log(){
        
            $.post("/client_files/acusticamedica/novocliente/nc_log.php",{inOUT:inOUT_hack,agent_log_id:agent_log_id,lead_id:nc_live_id,lead_id_o:lead_id_hack},function(data){console.log(data);});
        }
// ################################################################################
// Send alert control command for agent
	function alert_control(taskalert) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			alert_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=AlertControl&format=text&stage=" + taskalert;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(alert_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		if (taskalert=='ON')
			{
			alert_enabled = 'ON';
			document.getElementById("AgentAlertSpan").innerHTML = "<a href=\"#\" onclick=\"alert_control('OFF');return false;\">Alert is ON</a>";
			}
		else
			{
			alert_enabled = 'OFF';
			document.getElementById("AgentAlertSpan").innerHTML = "<a href=\"#\" onclick=\"alert_control('ON');return false;\">Alert is OFF</a>";
			}

		}


// ################################################################################
// park customer and place 3way call
	function xfer_park_dial()
		{
		conf_dialed=1;

		mainxfer_send_redirect('ParK',lastcustchannel,lastcustserverip);

		SendManualDial('YES');
		}

// ################################################################################
// place 3way and customer into other conference and fake-hangup the lines
	function leave_3way_call(tempvarattempt)
		{
		threeway_end=0;
		leaving_threeway=1;

		if (customerparked > 0)
			{
			mainxfer_send_redirect('FROMParK',lastcustchannel,lastcustserverip);
			}

		mainxfer_send_redirect('3WAY','','',tempvarattempt);

		if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
		}

// ################################################################################
// filter manual dialstring and pass on to originate call
	function SendManualDial(taskFromConf)
		{
		conf_dialed=1;
		var sending_group_alias = 0;
		// Dial With Customer button
		if (taskFromConf == 'YES')
			{
			xfer_in_call=1;
			agent_dialed_number='1';
			agent_dialed_type='XFER_3WAY';
            var manual_number = document.vicidial_form.xfernumber.value;            
            document.vicidial_form.xferchannel.value = 'Local/' + manual_number + '@default';
            document.getElementById("DialWithCustomer").innerHTML ="<img src=\"./images/vdc_XB_dialwithcustomer_OFF.gif\" border=\"0\" alt=\"Dial With Customer\" style=\"vertical-align:middle\" /></a>";
            document.getElementById("HangupXferLine").innerHTML ="<a href=\"#\" onclick=\"DialTimeHangup('HangupTransfer',"+manual_number+");return false;\"><img src=\"/images/icons/telephone_delete_32.png\"  alt=\"Hangup Xfer Line\" style=\"vertical-align:middle\" />Desligar o solicitado</a>";
								document.getElementById("HangupXferLine").style.display="block";
            document.getElementById("ParkCustomerDial").innerHTML ="<img src=\"/images/icons/telephone_add_32.png\" alt=\"Park Customer Dial\" style=\"vertical-align:middle\" />Solicitar Transferência</a>";
			document.getElementById("ParkCustomerDial").style.display="none";
			
			var manual_number = document.vicidial_form.xfernumber.value;
			var manual_number_hidden = document.vicidial_form.xfernumhidden.value;
			if ( (manual_number.length < 1) && (manual_number_hidden.length > 0) )
				{manual_number=manual_number_hidden;}
			var manual_string = manual_number.toString();
			var dial_conf_exten = session_id;
			threeway_cid = '';
			if (three_way_call_cid == 'CAMPAIGN')
				{threeway_cid = campaign_cid;} 
			if (three_way_call_cid == 'AGENT_PHONE')
				{threeway_cid = outbound_cid;}
			if (three_way_call_cid == 'CUSTOMER')
				{threeway_cid = document.vicidial_form.phone_number.value;}
			if (three_way_call_cid == 'CUSTOM_CID')
				{threeway_cid = document.vicidial_form.security_phrase.value;}
			if (three_way_call_cid == 'AGENT_CHOOSE')
				{
				threeway_cid = cid_choice;
				if (active_group_alias.length > 1)
					{var sending_group_alias = 1;}
				}
			}
		else
			{
			var manual_number = document.vicidial_form.xfernumber.value;
			var manual_string = manual_number.toString();
			var threeway_cid='1';
			if (manual_dial_cid == 'AGENT_PHONE')
				{threeway_cid = outbound_cid;}
			}
		var regXFvars = new RegExp("XFER","g");
		if (manual_string.match(regXFvars))
			{
			var donothing=1;
			}
		else
			{
			if (document.vicidial_form.xferoverride.checked==false)
				{
				if (three_way_dial_prefix == 'X') {var temp_dial_prefix = '';}
				else {var temp_dial_prefix = three_way_dial_prefix;}
				if (omit_phone_code == 'Y') {var temp_phone_code = '';}
				else {var temp_phone_code = document.vicidial_form.phone_code.value;}

				if (manual_string.length > 1)
					{manual_string = temp_dial_prefix + "" + temp_phone_code + "" + manual_string;}
				}
			else
				{agent_dialed_type='XFER_OVERRIDE';}
			// due to a bug in Asterisk, these call variables do not actually work
			call_variables = '__vendor_lead_code=' + document.vicidial_form.vendor_lead_code.value + ',__lead_id=' + document.vicidial_form.lead_id.value;
			}
		var sending_preset_name = document.vicidial_form.xfername.value;
		if (taskFromConf == 'YES')
			{basic_originate_call(manual_string,'NO','YES',dial_conf_exten,'NO',taskFromConf,threeway_cid,sending_group_alias,'',sending_preset_name,call_variables);}
		else
			{basic_originate_call(manual_string,'NO','NO','','','',threeway_cid,sending_group_alias,sending_preset_name,call_variables);}

		MD_ring_secondS=0;
		}

// ################################################################################
// Send Originate command to manager to place a phone call
	function basic_originate_call(tasknum,taskprefix,taskreverse,taskdialvalue,tasknowait,taskconfxfer,taskcid,taskusegroupalias,taskalert,taskpresetname,taskvariables) 
		{
		if (taskalert == '1')
			{
			var TAqueryCID = tasknum;
			tasknum = '83047777777777';
			taskdialvalue = '7' + taskdialvalue;
			var alertquery = 'alertCID=1';
			}
		else
			{var alertquery = 'alertCID=0';}
		var usegroupalias=0;
		var consultativexfer_checked = 0;
		if (document.vicidial_form.consultativexfer.checked==true)
			{consultativexfer_checked = 1;}
		var regCXFvars = new RegExp("CXFER","g");
		var tasknum_string = tasknum.toString();
		if ( (tasknum_string.match(regCXFvars)) || (consultativexfer_checked > 0) )
			{
			if (tasknum_string.match(regCXFvars))
				{
				var Ctasknum = tasknum_string.replace(regCXFvars, '');
				if (Ctasknum.length < 2)
					{Ctasknum = '90009';}
				var agentdirect = '';
				}
			else
				{
				Ctasknum = '90009';
				var agentdirect = tasknum_string;
				}
			var XfeRSelecT = document.getElementById("XfeRGrouP");
			var XfeR_GrouP = XfeRSelecT.value;
			if (API_selected_xfergroup.length > 1)
				{var XfeR_GrouP = API_selected_xfergroup;}
			tasknum = Ctasknum + "*" + XfeR_GrouP + '*CXFER*' + document.vicidial_form.lead_id.value + '**' + dialed_number + '*' + user + '*' + agentdirect + '*' + VD_live_call_secondS + '*';

			CustomerData_update();
			}
		var regAXFvars = new RegExp("AXFER","g");
		if (tasknum_string.match(regAXFvars))
			{
			var Ctasknum = tasknum_string.replace(regAXFvars, '');
			if (Ctasknum.length < 2)
				{Ctasknum = '83009';}
			var closerxfercamptail = '_L';
			if (closerxfercamptail.length < 3)
				{closerxfercamptail = 'IVR';}
			tasknum = Ctasknum + '*' + document.vicidial_form.phone_number.value + '*' + document.vicidial_form.lead_id.value + '*' + campaign + '*' + closerxfercamptail + '*' + user + '**' + VD_live_call_secondS + '*';

			CustomerData_update();

			}


		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{
			if (taskprefix == 'NO') {var call_prefix = '';}
			  else {var call_prefix = agc_dial_prefix;}

			if (prefix_choice.length > 0)
				{var call_prefix = prefix_choice;}

			if (taskreverse == 'YES')
				{
				if (taskdialvalue.length < 2)
					{var dialnum = dialplan_number;}
				else
					{var dialnum = taskdialvalue;}
				var call_prefix = '';
				var originatevalue = "Local/" + tasknum + "@" + ext_context;
				}
			  else 
				{
				var dialnum = tasknum;
				if ( (protocol == 'EXTERNAL') || (protocol == 'Local') )
					{
					var protodial = 'Local';
					var extendial = extension;
					}
				else
					{
					var protodial = protocol;
					var extendial = extension;
					}
				var originatevalue = protodial + "/" + extendial;
				}

			var leadCID = document.vicidial_form.lead_id.value;
			var epochCID = epoch_sec;
			if (leadCID.length < 1)
				{leadCID = user_abb;}
			leadCID = set_length(leadCID,'10','left');
			epochCID = set_length(epochCID,'6','right');
			if (taskconfxfer == 'YES')
				{var queryCID = "DC" + epochCID + 'W' + leadCID + 'W';}
			else
				{var queryCID = "DV" + epochCID + 'W' + leadCID + 'W';}

			if (taskalert == '1')
				{
				queryCID = TAqueryCID;
				}

			if (cid_choice.length > 3) 
				{
				var call_cid = cid_choice;
				usegroupalias=1;
				}
			else 
				{
				if (taskcid.length > 3) 
					{var call_cid = taskcid;}
				else 
					{var call_cid = campaign_cid;}
				}

			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Originate&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + call_prefix + "" + dialnum + "&ext_context=" + ext_context + "&ext_priority=1&outbound_cid=" + call_cid + "&usegroupalias="+ usegroupalias + "&preset_name=" + taskpresetname + "&campaign=" + campaign + "&account=" + active_group_alias + "&agent_dialed_number=" + agent_dialed_number + "&agent_dialed_type=" + agent_dialed_type + "&lead_id=" + document.vicidial_form.lead_id.value + "&stage=" + CheckDEADcallON + "&" + alertquery + "&call_variables=" + taskvariables;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var regBOerr = new RegExp("ERROR","g");
					var BOresponse = xmlhttp.responseText;
					if (BOresponse.match(regBOerr))
						{
						alert_box(BOresponse);
						}

					if ((taskdialvalue.length > 0) && (tasknowait != 'YES'))
						{
						XDnextCID = queryCID;
						MD_channel_look=1;
						XDcheck = 'YES';

              				}
					}
				}
			delete xmlhttp;
			active_group_alias='';
			cid_choice='';
			prefix_choice='';
			agent_dialed_number='';
			agent_dialed_type='';
			CalL_ScripT_id='';
			call_variables='';
			}
		}


// ################################################################################
// zero-pad numbers or chop them to get to the desired length
function set_length(SLnumber,SLlength_goal,SLdirection)
	{
	var SLnumber = SLnumber + '';
	var begin_point=0;
	var number_length = SLnumber.length;
	if (number_length > SLlength_goal)
		{
		if (SLdirection == 'right')
			{
			begin_point = (number_length - SLlength_goal);
			SLnumber = SLnumber.substr(begin_point,SLlength_goal);
			}
		else
			{
			SLnumber = SLnumber.substr(0,SLlength_goal);
			}
		}
	var result = SLnumber + '';
	while(result.length < SLlength_goal)
		{
		result = "0" + result;
		}
	return result;
	}


// ################################################################################
// filter conf_dtmf send string and pass on to originate call
	function SendConfDTMF(taskconfdtmf)
		{
		var dtmf_number = document.vicidial_form.conf_dtmf.value;
		var dtmf_string = dtmf_number.toString();
		var conf_dtmf_room = taskconfdtmf;

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			var queryCID = dtmf_string;
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=SysCIDdtmfOriginate&format=text&channel=" + dtmf_send_extension + "&queryCID=" + queryCID + "&exten=" + dtmf_silent_prefix + '' + conf_dtmf_room + "&ext_context=" + ext_context + "&ext_priority=1";
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
			//		alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		document.vicidial_form.conf_dtmf.value = '';
		}
String.prototype.toHHMMSS = function () {
    var sec_num = parseInt(this, 10); // don't forget the second parm
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    var time    = hours+':'+minutes+':'+seconds;
    return time;
    };   
    
function stats_update(){
    var statsDiv = document.getElementById("statsTimer");
    
    if ((VD_live_call_secondS + dead_time + AgentDispoing) < 1 ) {
        statsDiv.style.backgroundColor = "#FFFFFF";
    } else {
    if (VD_live_call_secondS > 1 && dead_time < 1) { statsDiv.style.backgroundColor = "#66FF66"; }
    if (dead_time > 1) { statsDiv.style.backgroundColor = "#FF3366"; }
    if (AgentDispoing > 1) { statsDiv.style.backgroundColor = "#66CCFF"; 
        
        $("#stats_feedback").html(AgentDispoing.toString().toHHMMSS());
        
        
    } else { $("#stats_incall").html(VD_live_call_secondS.toString().toHHMMSS());
    $("#stats_dead").html(dead_time.toString().toHHMMSS());
    $("#stats_feedback").html(AgentDispoing.toString().toHHMMSS());}
    } 


    
    
   
}
// ################################################################################
// Send MonitorConf/StopMonitorConf command for recording of conferences
	function conf_send_recording(taskconfrectype,taskconfrec,taskconffile) 
		{
		if (inOUT == 'OUT')
			{
			tmp_vicidial_id = document.vicidial_form.uniqueid.value;
			}
		else
			{
			tmp_vicidial_id = 'IN';
			}
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			if (taskconfrectype == 'MonitorConf')
				{
				var REGrecCLEANvlc = new RegExp(" ","g");
				var recVendorLeadCode = document.vicidial_form.vendor_lead_code.value;
				recVendorLeadCode = recVendorLeadCode.replace(REGrecCLEANvlc, '');
				var recLeadID = document.vicidial_form.lead_id.value;

				//	CAMPAIGN CUSTPHONE FULLDATE TINYDATE EPOCH AGENT VENDORLEADCODE LEADID
				var REGrecCAMPAIGN = new RegExp("CAMPAIGN","g");
				var REGrecCUSTPHONE = new RegExp("CUSTPHONE","g");
				var REGrecFULLDATE = new RegExp("FULLDATE","g");
				var REGrecTINYDATE = new RegExp("TINYDATE","g");
				var REGrecEPOCH = new RegExp("EPOCH","g");
				var REGrecAGENT = new RegExp("AGENT","g");
				var REGrecVENDORLEADCODE = new RegExp("VENDORLEADCODE","g");
				var REGrecLEADID = new RegExp("LEADID","g");
				filename = LIVE_campaign_rec_filename;
				filename = filename.replace(REGrecCAMPAIGN, campaign);
				filename = filename.replace(REGrecCUSTPHONE, lead_dial_number);
				filename = filename.replace(REGrecFULLDATE, filedate);
				filename = filename.replace(REGrecTINYDATE, tinydate);
				filename = filename.replace(REGrecEPOCH, epoch_sec);
				filename = filename.replace(REGrecAGENT, user);
				filename = filename.replace(REGrecVENDORLEADCODE, recVendorLeadCode);
				filename = filename.replace(REGrecLEADID, recLeadID);
				var query_recording_exten = recording_exten;
				var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
                var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('StopMonitorConf','" + taskconfrec + "','" + filename + "');return false;\"><img src=\"./images/vdc_LB_stoprecording.gif\" border=\"0\" alt=\"Stop Recording\" /></a>";

				if (LIVE_campaign_recording == 'ALLFORCE')
					{
                    document.getElementById("RecorDControl").innerHTML = "<img src=\"./images/vdc_LB_startrecording_OFF.gif\" border=\"0\" alt=\"Start Recording\" />";
					}
				else
					{
					document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;
					}
			}
			if (taskconfrectype == 'StopMonitorConf')
				{
				filename = taskconffile;
				var query_recording_exten = session_id;
				var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;
                var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + taskconfrec + "','');return false;\"><img src=\"./images/vdc_LB_startrecording.gif\" border=\"0\" alt=\"Start Recording\" /></a>";
				if (LIVE_campaign_recording == 'ALLFORCE')
					{
                    document.getElementById("RecorDControl").innerHTML = "<img src=\"./images/vdc_LB_startrecording_OFF.gif\" border=\"0\" alt=\"Start Recording\" />";
					}
				else
					{
					document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;
					}
				}
			confmonitor_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=" + taskconfrectype + "&format=text&channel=" + channelrec + "&filename=" + filename + "&exten=" + query_recording_exten + "&ext_context=" + ext_context + "&lead_id=" + document.vicidial_form.lead_id.value + "&ext_priority=1&FROMvdc=YES&uniqueid=" + tmp_vicidial_id;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(confmonitor_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var RClookResponse = null;
						RClookResponse = xmlhttp.responseText;
					var RClookResponse_array=RClookResponse.split("\n");
					var RClookFILE = RClookResponse_array[1];
					var RClookID = RClookResponse_array[2];
					var RClookFILE_array = RClookFILE.split("Filename: ");
					var RClookID_array = RClookID.split("RecorDing_ID: ");
					if (RClookID_array.length > 0)
						{
						recording_filename = RClookFILE_array[1];
						recording_id = RClookID_array[1];

						if (delayed_script_load == 'YES')
							{
							RefresHScript();
							delayed_script_load='NO';
							}

						var RecDispNamE = RClookFILE_array[1];
						if (RecDispNamE.length > 25)
							{
							RecDispNamE = RecDispNamE.substr(0,22);
							RecDispNamE = RecDispNamE + '...';
							}
						document.getElementById("RecorDingFilename").innerHTML = RecDispNamE;
						document.getElementById("RecorDID").innerHTML = RClookID_array[1];
						}
					}
				}
			delete xmlhttp;
			}
		}

// ################################################################################
// Send MonitorConf/StopMonitorConf command for recording of conferences
	function hangup_recordings(taskconfrec) 
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{
			var query_recording_exten = session_id;
			var channelrec = "Local/" + conf_silent_prefix + '' + taskconfrec + "@" + ext_context;

			confhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=HangupRecordings&format=text&channel=" + channelrec + "&exten=" + query_recording_exten + "&ext_context=" + ext_context + "&lead_id=" + document.vicidial_form.lead_id.value + "&ext_priority=1&FROMvdc=YES";
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(confhangup_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					// Nothing to do here...
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Send Redirect command for live call to Manager sends phone name where call is going to
// Covers the following types: XFER, VMAIL, ENTRY, CONF, PARK, FROMPARK, XfeRLOCAL, XfeRINTERNAL, XfeRBLIND, VfeRVMAIL
	function mainxfer_send_redirect(taskvar,taskxferconf,taskserverip,taskdebugnote,taskdispowindow,tasklockedquick) 
		{
		blind_transfer=1;
		var consultativexfer_checked = 0;
		if (document.vicidial_form.consultativexfer.checked==true)
			{consultativexfer_checked = 1;}

		if (auto_dial_level == 0) {RedirecTxFEr = 1;}
		var xmlhttpXF=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttpXF = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttpXF = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttpXF = false;
		  }
		 }
		@end @*/
		if (!xmlhttpXF && typeof XMLHttpRequest!='undefined')
			{
			xmlhttpXF = new XMLHttpRequest();
			}
		if (xmlhttpXF) 
			{ 
			var redirectvalue = MDchannel;
			var redirectserverip = lastcustserverip;
			if (redirectvalue.length < 2)
				{redirectvalue = lastcustchannel}
			if ( (taskvar == 'XfeRBLIND') || (taskvar == 'XfeRVMAIL') )
				{
				if (tasklockedquick > 0)
					{document.vicidial_form.xfernumber.value = quick_transfer_button_orig;}
				var queryCID = "XBvdcW" + epoch_sec + user_abb;
				var blindxferdialstring = document.vicidial_form.xfernumber.value;
				var blindxferhiddendialstring = document.vicidial_form.xfernumhidden.value;
				if ( (blindxferdialstring.length < 1) && (blindxferhiddendialstring.length > 0) )
					{blindxferdialstring=blindxferhiddendialstring;}
				var regXFvars = new RegExp("XFER","g");
				if (blindxferdialstring.match(regXFvars))
					{
					var regAXFvars = new RegExp("AXFER","g");
					if (blindxferdialstring.match(regAXFvars))
						{
						var Ctasknum = blindxferdialstring.replace(regAXFvars, '');
						if (Ctasknum.length < 2)
							{Ctasknum = '83009';}
						var closerxfercamptail = '_L';
						if (closerxfercamptail.length < 3)
							{closerxfercamptail = 'IVR';}
						blindxferdialstring = Ctasknum + '*' + document.vicidial_form.phone_number.value + '*' + document.vicidial_form.lead_id.value + '*' + campaign + '*' + closerxfercamptail + '*' + user + '**' + VD_live_call_secondS + '*';
						}
					}
				else
					{
					if (document.vicidial_form.xferoverride.checked==false)
						{
						if (three_way_dial_prefix == 'X') {var temp_dial_prefix = '';}
						else {var temp_dial_prefix = three_way_dial_prefix;}
						if (omit_phone_code == 'Y') {var temp_phone_code = '';}
						else {var temp_phone_code = document.vicidial_form.phone_code.value;}

						if (blindxferdialstring.length > 7)
							{blindxferdialstring = temp_dial_prefix + "" + temp_phone_code + "" + blindxferdialstring;}
						}
					}
				if (API_selected_callmenu.length > 0)
					{
					var blindxferdialstring = 's';
					var blindxfercontext = document.vicidial_form.xfernumber.value;
					}
				else
					{var blindxfercontext = ext_context;}
				no_delete_VDAC=0;
				if (taskvar == 'XfeRVMAIL')
					{
					var blindxferdialstring = campaign_am_message_exten + '*' + campaign + '*' + document.vicidial_form.phone_code.value + '*' + document.vicidial_form.phone_number.value + '*' + document.vicidial_form.lead_id.value;
					no_delete_VDAC=1;
					}
				if (blindxferdialstring.length<'1')
					{
					xferredirect_query='';
					taskvar = 'NOTHING';
					alert_box("O nº de transferência tem que ter pelo menos 1 digito:" + blindxferdialstring);
					}
				else
					{
					xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectVD&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + blindxferdialstring + "&ext_context=" + blindxfercontext + "&ext_priority=1&auto_dial_level=" + auto_dial_level + "&campaign=" + campaign + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&secondS=" + VD_live_call_secondS + "&session_id=" + session_id + "&nodeletevdac=" + no_delete_VDAC + "&preset_name=" + document.vicidial_form.xfername.value;
					}
				}
			if (taskvar == 'XfeRINTERNAL') 
				{
				var closerxferinternal = '';
				taskvar = 'XfeRLOCAL';
				}
			else 
				{
				var closerxferinternal = '9';
				}
			if (taskvar == 'XfeRLOCAL')
				{
				CustomerData_update();

				document.vicidial_form.xfername.value='';
				var XfeRSelecT = document.getElementById("XfeRGrouP");
				var XfeR_GrouP = XfeRSelecT.value;
				if (API_selected_xfergroup.length > 1)
					{var XfeR_GrouP = API_selected_xfergroup;}
				if (tasklockedquick > 0)
					{XfeR_GrouP = quick_transfer_button_orig;}
				var queryCID = "XLvdcW" + epoch_sec + user_abb;
				var redirectdestination = closerxferinternal + '90009*' + XfeR_GrouP + '**' + document.vicidial_form.lead_id.value + '**' + dialed_number + '*' + user + '*' + document.vicidial_form.xfernumber.value + '*';


				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectVD&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1&auto_dial_level=" + auto_dial_level + "&campaign=" + campaign + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&secondS=" + VD_live_call_secondS + "&session_id=" + session_id;
				}
			if (taskvar == 'XfeR')
				{
				var queryCID = "LRvdcW" + epoch_sec + user_abb;
				var redirectdestination = document.vicidial_form.extension_xfer.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectName&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&extenName=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;
				}
			if (taskvar == 'VMAIL')
				{
				var queryCID = "LVvdcW" + epoch_sec + user_abb;
				var redirectdestination = document.vicidial_form.extension_xfer.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectNameVmail&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + voicemail_dump_exten + "&extenName=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;
				}
			if (taskvar == 'ENTRY')
				{
				var queryCID = "LEvdcW" + epoch_sec + user_abb;
				var redirectdestination = document.vicidial_form.extension_xfer_entry.value;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=Redirect&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id;
				}
			if (taskvar == '3WAY')
				{
				xferredirect_query='';

				var queryCID = "VXvdcW" + epoch_sec + user_abb;
				var redirectdestination = "NEXTAVAILABLE";
				var redirectXTRAvalue = XDchannel;
				var redirecttype_test = document.vicidial_form.xfernumber.value;
				var XfeRSelecT = document.getElementById("XfeRGrouP");
				var XfeR_GrouP = XfeRSelecT.value;
				if (API_selected_xfergroup.length > 1)
					{var XfeR_GrouP = API_selected_xfergroup;}
				var regRXFvars = new RegExp("CXFER","g");
				if ( ( (redirecttype_test.match(regRXFvars)) || (consultativexfer_checked > 0) ) && (local_consult_xfers > 0) )
					{var redirecttype = 'RedirectXtraCXNeW';}
				else
					{var redirecttype = 'RedirectXtraNeW';}
				DispO3waychannel = redirectvalue;
				DispO3wayXtrAchannel = redirectXTRAvalue;
				DispO3wayCalLserverip = redirectserverip;
				DispO3wayCalLxfernumber = document.vicidial_form.xfernumber.value;
				DispO3wayCalLcamptail = '';

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=" + redirecttype + "&format=text&channel=" + redirectvalue + "&call_server_ip=" + redirectserverip + "&queryCID=" + queryCID + "&exten=" + redirectdestination + "&ext_context=" + ext_context + "&ext_priority=1&extrachannel=" + redirectXTRAvalue + "&lead_id=" + document.vicidial_form.lead_id.value + "&phone_code=" + document.vicidial_form.phone_code.value + "&phone_number=" + document.vicidial_form.phone_number.value + "&filename=" + taskdebugnote + "&campaign=" + XfeR_GrouP + "&session_id=" + session_id + "&agentchannel=" + agentchannel + "&protocol=" + protocol + "&extension=" + extension + "&auto_dial_level=" + auto_dial_level;

				if (taskdebugnote == 'FIRST') 
					{
					//document.getElementById("DispoSelectHAspan").innerHTML = "<a href=\"#\" onclick=\"DispoLeavE3wayAgaiN()\">Leave 3Way Call Again</a>";
					}
				}
			if (taskvar == 'ParK')
				{
				if (CalLCID.length < 1)
					{
					CalLCID = MDnextCID;
					}
				blind_transfer=0;
				var queryCID = "LPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;
				var parkedby = protocol + "/" + extension;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectToPark&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + park_on_extension + "&ext_context=" + ext_context + "&ext_priority=1&extenName=park&parkedby=" + parkedby + "&session_id=" + session_id + "&CalLCID=" + CalLCID + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;
                                
                document.getElementById("ParkControl").innerHTML ="<td onclick=\"mainxfer_send_redirect('FROMParK','" + redirectdestination + "','" + redirectdestserverip + "');return false;\" style='cursor:pointer'><img src='/images/icons/control_play_blue.png' /></td><td onclick=\"mainxfer_send_redirect('FROMParK','" + redirectdestination + "','" + redirectdestserverip + "');return false;\" style='cursor:pointer'><a href=\"#\" >Cancelar Espera</a></td>";
				if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
					{
                    document.getElementById("ivrParkControl").innerHTML ="<img src=\"./images/vdc_LB_grabivrparkcall_OFF.gif\" border=\"0\" alt=\"Grab IVR Parked Call\" />";
					}
				customerparked=1;
				customerparkedcounter=0;
				}
			if (taskvar == 'FROMParK')
				{
				blind_transfer=0;
				var queryCID = "FPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;

				if( (server_ip == taskserverip) && (taskserverip.length > 6) )
					{var dest_dialstring = session_id;}
				else
					{
					if(taskserverip.length > 6)
						{var dest_dialstring = server_ip_dialstring + "" + session_id;}
					else
						{var dest_dialstring = session_id;}
					}

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectFromPark&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + dest_dialstring + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id + "&CalLCID=" + CalLCID + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;

                document.getElementById("ParkControl").innerHTML ="<td onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\" style='cursor:pointer'><img src='/images/icons/control_pause_blue.png' /></td><td onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\" style='cursor:pointer'><a href=\"#\">Colocar em Espera</a></td>";
				if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
					{
                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKivr','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"./images/vdc_LB_ivrparkcall.gif\" border=\"0\" alt=\"IVR Park Call\" /></a>";
					}
				customerparked=0;
				customerparkedcounter=0;
				}
			if (taskvar == 'ParKivr')
				{
				if (CalLCID.length < 1)
					{
					CalLCID = MDnextCID;
					}
				blind_transfer=0;
				var queryCID = "LPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;
				var parkedby = protocol + "/" + extension;
				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectToParkIVR&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + park_on_extension + "&ext_context=" + ext_context + "&ext_priority=1&extenName=park&parkedby=" + parkedby + "&session_id=" + session_id + "&CalLCID=" + CalLCID + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;

                document.getElementById("ParkControl").innerHTML ="<td><img src='/images/icons/control_pause.png' /></td><td>Colocar em Espera</td>";
				if (ivr_park_call=='ENABLED_PARK_ONLY')
					{
                    document.getElementById("ivrParkControl").innerHTML ="<img src=\"./images/vdc_LB_grabivrparkcall_OFF.gif\" border=\"0\" alt=\"Grab IVR Parked Call\" />";
					}
				if (ivr_park_call=='ENABLED')
					{
                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('FROMParKivr','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"./images/vdc_LB_grabivrparkcall.gif\" border=\"0\" alt=\"Grab IVR Parked Call\" /></a>";
					}
				customerparked=1;
				customerparkedcounter=0;
				}
			if (taskvar == 'FROMParKivr')
				{
				blind_transfer=0;
				var queryCID = "FPvdcW" + epoch_sec + user_abb;
				var redirectdestination = taskxferconf;
				var redirectdestserverip = taskserverip;

				if( (server_ip == taskserverip) && (taskserverip.length > 6) )
					{var dest_dialstring = session_id;}
				else
					{
					if(taskserverip.length > 6)
						{var dest_dialstring = server_ip_dialstring + "" + session_id;}
					else
						{var dest_dialstring = session_id;}
					}

				xferredirect_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=RedirectFromParkIVR&format=text&channel=" + redirectdestination + "&call_server_ip=" + redirectdestserverip + "&queryCID=" + queryCID + "&exten=" + dest_dialstring + "&ext_context=" + ext_context + "&ext_priority=1" + "&session_id=" + session_id + "&CalLCID=" + CalLCID + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign;

                document.getElementById("ParkControl").innerHTML ="<td onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\" style='cursor:pointer'><img src='/images/icons/control_pause_blue.png' /></td><td onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\" style='cursor:pointer'><a href=\"#\">Colocar em Espera</a></td>";
				if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
					{
                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKivr','" + redirectdestination + "','" + redirectdestserverip + "');return false;\"><img src=\"./images/vdc_LB_ivrparkcall.gif\" border=\"0\" alt=\"IVR Park Call\" /></a>";
					}
				customerparked=0;
				customerparkedcounter=0;
				}

			var XFRDop = '';
			xmlhttpXF.open('POST', 'manager_send.php'); 
			xmlhttpXF.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttpXF.send(xferredirect_query); 
			xmlhttpXF.onreadystatechange = function() 
				{ 
				if (xmlhttpXF.readyState == 4 && xmlhttpXF.status == 200) 
					{
					var XfeRRedirecToutput = null;
					XfeRRedirecToutput = xmlhttpXF.responseText;
					var XfeRRedirecToutput_array=XfeRRedirecToutput.split("|");
					var XFRDop = XfeRRedirecToutput_array[0];
					if (XFRDop == "NeWSessioN")
						{
						threeway_end=1;
						document.getElementById("callchannel").innerHTML = '';
						document.vicidial_form.callserverip.value = '';
						dialedcall_send_hangup();

						document.vicidial_form.xferchannel.value = '';
						xfercall_send_hangup();

						session_id = XfeRRedirecToutput_array[1];
						document.getElementById("sessionIDspan").innerHTML = session_id;

						}
					}
				}
			delete xmlhttpXF;
			}

			// used to send second Redirect for manual dial calls
			if ( (auto_dial_level == 0) && (taskvar != '3WAY') )
			{
				RedirecTxFEr = 1;
				var xmlhttpXF2=false;
				/*@cc_on @*/
				/*@if (@_jscript_version >= 5)
				// JScript gives us Conditional compilation, we can cope with old IE versions.
				// and security blocked creation of the objects.
				 try {
				  xmlhttpXF2 = new ActiveXObject("Msxml2.XMLHTTP");
				 } catch (e) {
				  try {
				   xmlhttpXF2 = new ActiveXObject("Microsoft.XMLHTTP");
				  } catch (E) {
				   xmlhttpXF2 = false;
				  }
				 }
				@end @*/
				if (!xmlhttpXF2 && typeof XMLHttpRequest!='undefined')
				{
					xmlhttpXF2 = new XMLHttpRequest();
				}
				if (xmlhttpXF2) 
				{ 
					xmlhttpXF2.open('POST', 'manager_send.php'); 
					xmlhttpXF2.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xmlhttpXF2.send(xferredirect_query + "&stage=2NDXfeR"); 
					xmlhttpXF2.onreadystatechange = function() 
						{ 
						if (xmlhttpXF2.readyState == 4 && xmlhttpXF2.status == 200) 
							{
							Nactiveext = null;
							Nactiveext = xmlhttpXF2.responseText;
						}
				}
				delete xmlhttpXF2;
				}
			}

		if ( (taskvar == 'XfeRLOCAL') || (taskvar == 'XfeRBLIND') || (taskvar == 'XfeRVMAIL') )
			{
			if (auto_dial_level == 0) {RedirecTxFEr = 1;}
			document.getElementById("callchannel").innerHTML = '';
			document.vicidial_form.callserverip.value = '';
			if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
			dialedcall_send_hangup(taskdispowindow,'','',no_delete_VDAC);
			}

		}

// ################################################################################
// Finish the alternate dialing and move on to disposition the call
	function ManualDialAltDonE()
		{
		alt_phone_dialing=starting_alt_phone_dialing;
		alt_dial_active = 0;
		alt_dial_status_display = 0;
		open_dispo_screen=1;
		document.getElementById("MainStatuSSpan").innerHTML = "Marque o proximo nº";
		}
// ################################################################################
// Insert or update the vicidial_log entry for a customer call
	function DialLog(taskMDstage,nodeletevdac)
		{
		var alt_num_status = 0;
		if (taskMDstage == "start") 
			{
			var MDlogEPOCH = 0;
			var UID_test = document.vicidial_form.uniqueid.value;
			if (UID_test.length < 4)
				{
				UID_test = epoch_sec + '.' + random;
				document.vicidial_form.uniqueid.value = UID_test;
				}
			}
		else
			{
			if (alt_phone_dialing == 1)
				{
				if (document.vicidial_form.DiaLAltPhonE.checked==true)
					{
					alt_num_status = 1;
					reselect_alt_dial = 1;
					alt_dial_active = 1;
					alt_dial_status_display = 1;
					var man_status = "Dial Alt Phone Number: <a href=\"#\" onclick=\"ManualDialOnly('MaiNPhonE')\"><font class=\"preview_text\">MAIN PHONE</font></a> or <a href=\"#\" onclick=\"ManualDialOnly('ALTPhonE')\"><font class=\"preview_text\">ALT PHONE</font></a> or <a href=\"#\" onclick=\"ManualDialOnly('AddresS3')\"><font class=\"preview_text\">ADDRESS3</font></a> or <a href=\"#\" onclick=\"ManualDialAltDonE()\"><font class=\"preview_text_red\">FINISH LEAD</font></a>"; 
					document.getElementById("MainStatuSSpan").innerHTML = man_status;
					}
				}
			}
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{
			manDiaLlog_query = "format=text&server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLlogCaLL&stage=" + taskMDstage + "&uniqueid=" + document.vicidial_form.uniqueid.value + 
			"&user=" + user + "&pass=" + pass + "&campaign=" + campaign + 
			"&lead_id=" + document.vicidial_form.lead_id.value + 
			"&list_id=" + document.vicidial_form.list_id.value + 
			"&length_in_sec=0&phone_code=" + document.vicidial_form.phone_code.value + 
			"&phone_number=" + lead_dial_number + 
			"&exten=" + extension + "&channel=" + lastcustchannel + "&start_epoch=" + MDlogEPOCH + "&auto_dial_level=" + auto_dial_level + "&VDstop_rec_after_each_call=" + VDstop_rec_after_each_call + "&conf_silent_prefix=" + conf_silent_prefix + "&protocol=" + protocol + "&extension=" + extension + "&ext_context=" + ext_context + "&conf_exten=" + session_id + "&user_abb=" + user_abb + "&agent_log_id=" + agent_log_id + "&MDnextCID=" + LasTCID + "&inOUT=" + inOUT + "&alt_dial=" + dialed_label + "&DB=0" + "&agentchannel=" + agentchannel + "&conf_dialed=" + conf_dialed + "&leaving_threeway=" + leaving_threeway + "&hangup_all_non_reserved=" + hangup_all_non_reserved + "&blind_transfer=" + blind_transfer + "&dial_method" + dial_method + "&nodeletevdac=" + nodeletevdac + "&alt_num_status=" + alt_num_status;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
		//		document.getElementById("busycallsdebug").innerHTML = "vdc_db_query.php?" + manDiaLlog_query;
			xmlhttp.send(manDiaLlog_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var MDlogResponse = null;
					MDlogResponse = xmlhttp.responseText;
					var MDlogResponse_array=MDlogResponse.split("\n");
					MDlogLINE = MDlogResponse_array[0];
					if ( (MDlogLINE == "LOG NOT ENTERED") && (VDstop_rec_after_each_call != 1) )
						{
						}
					else
						{
						MDlogEPOCH = MDlogResponse_array[1];
						if ( (taskMDstage != "start") && (VDstop_rec_after_each_call == 1) )
							{
                            var conf_rec_start_html = "<a href=\"#\" onclick=\"conf_send_recording('MonitorConf','" + session_id + "','');return false;\"><img src=\"./images/vdc_LB_startrecording.gif\" border=\"0\" alt=\"Start Recording\" /></a>";
							if ( (LIVE_campaign_recording == 'NEVER') || (LIVE_campaign_recording == 'ALLFORCE') )
								{
                                document.getElementById("RecorDControl").innerHTML = "<img src=\"./images/vdc_LB_startrecording_OFF.gif\" border=\"0\" alt=\"Start Recording\" />";
								}
							else
								{document.getElementById("RecorDControl").innerHTML = conf_rec_start_html;}
							
							MDlogRecorDings = MDlogResponse_array[3];
							if (window.MDlogRecorDings)
								{
								var MDlogRecorDings_array=MDlogRecorDings.split("|");
						
								var RecDispNamE = MDlogRecorDings_array[2];
								if (RecDispNamE.length > 25)
									{
									RecDispNamE = RecDispNamE.substr(0,22);
									RecDispNamE = RecDispNamE + '...';
									}
								document.getElementById("RecorDingFilename").innerHTML = RecDispNamE;
								document.getElementById("RecorDID").innerHTML = MDlogRecorDings_array[3];
								}
							}
						}
					}
				}
			delete xmlhttp;
			}
		RedirecTxFEr=0;
		conf_dialed=0;
		}


// ################################################################################
// Request number of dialable leads left in this campaign
	function DiaLableLeaDsCounT()
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			DLcount_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=DiaLableLeaDsCounT&campaign=" + campaign + "&format=text";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(DLcount_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
						var DLcounT = xmlhttp.responseText;
                        document.getElementById("dialableleadsspan").innerHTML ="Dialable Leads:<br /> " + DLcounT;
						
					}
				}
			delete xmlhttp;
			}
		}
// ################################################################################
// Request number of USERONLY callbacks for this agent
	function CalLBacKsCounTCheck()
		{
                    $.post("vdc_db_query.php", 
                    {server_ip: server_ip,
                        session_name: session_name,
                        ACTION:"CalLBacKCounT",
                        format:"text",
                        user: user,
                        pass: pass,
                        campaign: campaign},
                    function(data) {
                        var CBpre = '',
                         CBpost = '',
                         Defer=0,

                         CBcounTtotal_array=data,
                         CBcounT = CBcounTtotal_array[1],
                         CBcounTex =(CBcounTtotal_array[0] == 0)? "Nenhum": CBcounTtotal_array[0],
                         cbexs=(CBcounTtotal_array[0] <= 1)? "": "s",
                         cblvs=(CBcounTtotal_array[1] <= 1)? "": "s";
                        
                        if (CBcounT == 0) {var CBprint = "Sem";}
                        else 
                                {
                                var CBprint = CBcounT;
                                if ( (LastCallbackCount < CBcounT) || (LastCallbackCount > CBcounT) )
                                        {
                                        LastCallbackCount = CBcounT;
                                        LastCallbackViewed=0;
                                        }

                                if ( (scheduled_callbacks_alert == 'RED_DEFER') || (scheduled_callbacks_alert == 'BLINK_DEFER') || (scheduled_callbacks_alert == 'BLINK_RED_DEFER') )
                                        {Defer=1;}

                                if ( (LastCallbackViewed > 0) && (Defer > 0) )
                                        {var do_nothing=1;}
                                else
                                        {
                                        if ( (scheduled_callbacks_alert == 'BLINK') || (scheduled_callbacks_alert == 'BLINK_DEFER') )
                                                {
                                                CBpre = '';
                                                CBpost = '';
                                                }
                                        if ( (scheduled_callbacks_alert == 'RED') || (scheduled_callbacks_alert == 'RED_DEFER') || (scheduled_callbacks_alert == 'BLINK_RED') || (scheduled_callbacks_alert == 'BLINK_RED_DEFER') )
                                                {
                                                CBpre = '<b><font color="red">';
                                                CBpost = '</font></b>';
                                                }
                                        }
                                }
                        CBlinkCONTENT ="<a href=\"#\" onclick=\"CalLBacKsLisTCheck();return false;\">" + CBpre + '' + CBprint + '' + " Callback"+cblvs+" Pronto"+cblvs+" e " +CBcounTex+" Expirado"+cbexs+" "+ CBpost + "</a>";	
                        $("#CBstatusSpan").html(CBlinkCONTENT);
                    },"json");
                    
		}
// ################################################################################
// Request list of USERONLY callbacks for this agent
	function CalLBacKsLisTCheck()
{
    var go_on = divchecker("cback");
    if (!go_on) {
        return;
    }
    if (AgentDispoing > 0) {
        alert_box('Termine Wrap-up da chamada.')
        return;
    }

    if (AutoDialReady == 0 && auto_dial_level > 0 && pause_code_counter == 0) {
        alert_box('Seleccione o motivo de pausa por favor.');
        return;
    }
    var move_on = 1;
    if ((AutoDialWaiting == 1) || (VD_live_customer_call == 1) || (alt_dial_active == 1) || (MD_channel_look == 1) || (in_lead_preview_state == 1))
    {
        if ((auto_pause_precall == 'Y') && ((agent_pause_codes_active == 'Y') || (agent_pause_codes_active == 'FORCE')) && (AutoDialWaiting == 1) && (VD_live_customer_call != 1) && (alt_dial_active != 1) && (MD_channel_look != 1) && (in_lead_preview_state != 1))
        {
            agent_log_id = AutoDial_ReSume_PauSe("VDADpause", '', '', '', '', '1', auto_pause_precall_code);
        }
        else
        {
            move_on = 0;
            alert_box("Tem que estar em pausa para ver os Call-Backs");
        }
    }
    if (move_on == 1)
    {
        LastCallbackViewed = 1;


        var cb_date_1 = document.vicidial_form.cb_date_1.value;
        var cb_date_2 = document.vicidial_form.cb_date_2.value;
        $.post("vdc_db_query.php",
                {server_ip: server_ip,
                    session_name: session_name,
                    ACTION: "CalLBacKLisT",
                    format: "text",
                    user: user,
                    pass: pass,
                    campaign: campaign,
                    cb_date_1: cb_date_1,
                    cb_date_2: cb_date_2},
        function(data) {
            var tbl_body = "", inactivos = "";
            $.each(data, function() {
                var tbl_row = "<td><time datetime=\"" + this.callback_time + "\" title=\""+moment(this.callback_time).format("dddd, MMMM Do YYYY, hh:mm:ss")+"\" >"+moment(this.callback_time).fromNow()+"</time></td><td>" + this.phone + "</td><td>" + this.comment + "<a href='#' class=\"btn btn-mini\" onclick=\"VieWLeaDInfO('" + this.lead_id + "','" + this.callback_id + "');return false;\"> mais</a></td><td>" + this.name + "</td><td>" + this.status + "</td><td>" + this.campaign_id + "</td><td><time datetime=\"" + this.callback_time + "\" title=\""+moment(this.entry_time).format("dddd, MMMM Do YYYY, hh:mm:ss")+"\" >"+moment(this.entry_time).fromNow()+"</time></td><td><button  onclick=\"new_callback_call('" + this.callback_id + "','" + this.lead_id + "','MAIN');\" class=\"btn btn-mini icon-alone \"><i class=\"icon-phone\"></i></button></td><td> <button onclick=\"ApagaCallback('" + this.callback_id + "');\" class=\"btn btn-mini icon-alone \"><i class=\"icon-trash\"></i></button> </td>";
                if (this.status == "Inativo") {
                    inactivos += "<tr style='opacity:0.5;' >" + tbl_row + "</tr>";
                } else {
                    tbl_body += "<tr>" + tbl_row + "</tr>";
                }

            });
            tbl_body += inactivos;
            $("#CallBacKsLisT").html(tbl_body);
            showDiv('CallBacKsLisTBox');
        }, "json");


    }
}

	function ApagaCallback(cb_id)
		{
                    $.post('vdc_db_query.php', {
	            server_ip: server_ip,
	            session_name: session_name,
	            user: user,
	            pass: pass,
                    campaign: campaign ,
	            ACTION: "apagacallback",
                    cb_id: cb_id
	        }, function (data) {
	            CalLBacKsLisTCheck();
	        });
			
		}
		
		// get intervalos

            function get_tempo_pausa()
                {
                    $.post('vdc_db_query.php', {
                        server_ip: server_ip,
                        session_name: session_name,
                        user: user,
                        pass: pass,
                        campaign: campaign,
                        ACTION: "get_tempo_pausa"
                    }, function(data) {
                        $('#tpausa').empty();
                        var time_label,tpausa= $('#tpausa');
                        $.each(data,function() {
                            if (this.exceed) {
                                time_label = $("<label>", {class: "label label-important"}).text(this.time);
                            } else {
                                time_label = $("<label>").text(this.time);
                            }
                           tpausa.append(
                                    $("<tr>")
                                    .append($("<th>").text(this.pause))
                                    .append($("<td>").html(time_label))
                                    );
                        });
                    },"json");


                }
// ################################################################################
// closes callback list screen
	function alert_box(temp_message)
		{
		$("#AlertBoxContent").html(temp_message);

		showDiv('AlertBox');

		$("#alert_button").focus();
		}


// ################################################################################
// closes callback list screen
	function CalLBacKsLisTClose()
		{
		if (auto_resume_precall == 'Y')
			{
			AutoDial_ReSume_PauSe("VDADready");
			}
		hideDiv('CallBacKsLisTBox');
		CalLBacKsCounTCheck();
		}


// ################################################################################
// closes call log display screen
	function CalLLoGVieWClose()
		{
		if (auto_resume_precall == 'Y')
			{
			AutoDial_ReSume_PauSe("VDADready");
			}
		hideDiv('CalLLoGDisplaYBox');
		}


// ################################################################################
// closes lead search screen
	function LeaDSearcHVieWClose()
		{
		if (auto_resume_precall == 'Y')
			{
			AutoDial_ReSume_PauSe("VDADready");
			}
		hideDiv('SearcHForMDisplaYBox');
		}


// ################################################################################
// Open up a callback customer record as manual dial preview mode
	function new_callback_call(taskCBid,taskLEADid,taskCBalt)
		{
	//	alt_phone_dialing=1;
		LastCallbackViewed=1;
		LastCallbackCount = (LastCallbackCount - 1);
		auto_dial_level=0;
		manual_dial_in_progress=1;
		MainPanelToFront();
		buildDiv('DiaLLeaDPrevieW');
		if (alt_phone_dialing == 1)
			{buildDiv('DiaLDiaLAltPhonE');}
	//	document.vicidial_form.DiaLAltPhonE.checked=true;
		hideDiv('CallBacKsLisTBox');
                document.vicidial_form.LeadPreview.checked=true;
		ManualDialNext(taskCBid,taskLEADid,'','','','0','',taskCBalt);
		ultimo_callback=taskCBid;
		}


// ################################################################################
// Finish Callback and go back to original screen
	function manual_dial_finished()
		{
		alt_phone_dialing=starting_alt_phone_dialing;
		auto_dial_level=starting_dial_level;
		MainPanelToFront();
		CalLBacKsCounTCheck();
		manual_dial_in_progress=0;
		}


// ################################################################################
// Open page to enter details for a new manual dial lead
	function NeWManuaLDiaLCalL(TVfast,TVphone_code,TVphone_number,TVlead_id,TVtype)
		{   var go_on = divchecker("mdial");
                    if (!go_on) { return; }
                    if ( AgentDispoing > 0) {
                        alert_box('Termine Wrap-up da chamada.')
                        return;
                    }
                
                    if  ( AutoDialReady == 0 && auto_dial_level > 0 && pause_code_counter == 0) {
                    alert_box('Seleccione o motivo de pausa por favor.');
                    return;
                    }
                
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("Tem que estar em pausa para fazer chamadas manuais");
				}
			}
                        if(document.vicidial_form.lead_id.value.length!==0)
                            {
                            move_on=0;
                            alert_box("Tem que estar em pausa para fazer chamadas manuais");				
                            } 
                        
		if (move_on == 1)
			{
			if (TVfast=='FAST')
				{
				NeWManuaLDiaLCalLSubmiTfast();
				}
			else
				{
				if (TVfast=='CALLLOG')
					{
					hideDiv('CalLLoGDisplaYBox');
					hideDiv('SearcHForMDisplaYBox');
					hideDiv('SearcHResultSDisplaYBox');
					hideDiv('LeaDInfOBox');
					document.vicidial_form.MDDiaLCodE.value = TVphone_code;
					document.vicidial_form.MDPhonENumbeR.value = TVphone_number;
					document.vicidial_form.MDLeadID.value = TVlead_id;
					document.vicidial_form.MDType.value = TVtype;
					}
				if (TVfast=='LEADSEARCH')
					{
					hideDiv('SearcHForMDisplaYBox');
					hideDiv('SearcHResultSDisplaYBox');
					hideDiv('LeaDInfOBox');
					document.vicidial_form.MDDiaLCodE.value = TVphone_code;
					document.vicidial_form.MDPhonENumbeR.value = TVphone_number;
					document.vicidial_form.MDLeadID.value = TVlead_id;
					document.vicidial_form.MDType.value = TVtype;
					}
				if (agent_allow_group_alias == 'Y')
					{
                    document.getElementById("ManuaLDiaLGrouPSelecteD").innerHTML = "<font size=\"2\" face=\"Arial,Helvetica\">Group Alias: " + active_group_alias + "</font>";
                    document.getElementById("ManuaLDiaLGrouP").innerHTML = "<a href=\"#\" onclick=\"GroupAliasSelectContent_create('0');\"><font size=\"1\" face=\"Arial,Helvetica\">Click Here to Choose a Group Alias</font></a>";
					}
				showDiv('NeWManuaLDiaLBox');
                                $("#MDPhonENumbeR").focus();

				
				}
			}
		}


// ################################################################################
// Insert the new manual dial as a lead and go to manual dial screen
	var portabilidade=0;
        function NeWManuaLDiaLCalLSubmiT(tempDiaLnow)
		{
		hideDiv('NeWManuaLDiaLBox');
		//document.getElementById("debugbottomspan").innerHTML = "DEBUG OUTPUT" + document.vicidial_form.MDPhonENumbeR.value + "|" + active_group_alias;

		var s_portabilidade = document.getElementById('portabilidade');
		
		portabilidade = s_portabilidade.options[s_portabilidade.selectedIndex].value;
		s_portabilidade.selectedIndex=0;
                
		var sending_group_alias = 0;
		var MDDiaLCodEform = document.vicidial_form.MDDiaLCodE.value;
		var MDPhonENumbeRform = document.vicidial_form.MDPhonENumbeR.value;
		var MDLeadIDform = document.vicidial_form.MDLeadID.value;
		var MDTypeform = document.vicidial_form.MDType.value;
		var MDDiaLOverridEform = document.vicidial_form.MDDiaLOverridE.value;
		var MDVendorLeadCode = document.vicidial_form.vendor_lead_code.value;
		var MDLookuPLeaD = 'new';
                
                if(MDPhonENumbeRform.length<2){
                    alert_box("Nº não inserido!");
                    return false;
                }
                
		if (document.vicidial_form.LeadLookuP.checked==true)
			{MDLookuPLeaD = 'lookup';}

		if (MDDiaLCodEform.length < 1)
			{MDDiaLCodEform = document.vicidial_form.phone_code.value;}

		if (MDDiaLOverridEform.length > 0)
			{
			agent_dialed_number=1;
			agent_dialed_type='MANUAL_OVERRIDE';
			basic_originate_call(session_id,'NO','YES',MDDiaLOverridEform,'YES','','1','0');
			}
		else
			{
			auto_dial_level=0;
			manual_dial_in_progress=1;
			agent_dialed_number=1;
			MainPanelToFront();

			if (tempDiaLnow == 'PREVIEW')
				{
			//	alt_phone_dialing=1;
				agent_dialed_type='MANUAL_PREVIEW';
				buildDiv('DiaLLeaDPrevieW');
				if (alt_phone_dialing == 1)
					{buildDiv('DiaLDiaLAltPhonE');}
				document.vicidial_form.LeadPreview.checked=true;
			//	document.vicidial_form.DiaLAltPhonE.checked=true;
				}
			else
				{
				agent_dialed_type='MANUAL_DIALNOW';
				document.vicidial_form.LeadPreview.checked=false;
				document.vicidial_form.DiaLAltPhonE.checked=false;
				}
			if (active_group_alias.length > 1)
				{var sending_group_alias = 1;}

			ManualDialNext("",MDLeadIDform,MDDiaLCodEform,MDPhonENumbeRform,MDLookuPLeaD,MDVendorLeadCode,sending_group_alias,MDTypeform);
			}

		document.vicidial_form.MDPhonENumbeR.value = '';
		document.vicidial_form.MDDiaLOverridE.value = '';
		document.vicidial_form.MDLeadID.value = '';
		document.vicidial_form.MDType.value = '';
		}

// ################################################################################
// Fast version of manual dial
		function NeWManuaLDiaLCalLSubmiTfast()
		{
		var MDDiaLCodEform = document.vicidial_form.phone_code.value;
		var MDPhonENumbeRform = document.vicidial_form.phone_number.value;
		var MDVendorLeadCode = document.vicidial_form.vendor_lead_code.value;

		if ( (MDDiaLCodEform.length < 1) || (MDPhonENumbeRform.length < 5) )
			{
			alert_box("Insira um nº de telefone e um indicativo");
			}
		else
			{
			var MDLookuPLeaD = 'new';
			if (document.vicidial_form.LeadLookuP.checked==true)
				{MDLookuPLeaD = 'lookup';}
		
			agent_dialed_number=1;
			agent_dialed_type='MANUAL_DIALFAST';
			auto_dial_level=0;
			manual_dial_in_progress=1;
			MainPanelToFront();
			buildDiv('DiaLLeaDPrevieW');
			if (alt_phone_dialing == 1)
				{buildDiv('DiaLDiaLAltPhonE');}
			document.vicidial_form.LeadPreview.checked=false;
			ManualDialNext("","",MDDiaLCodEform,MDPhonENumbeRform,MDLookuPLeaD,MDVendorLeadCode,'0');
			}
		}

// ################################################################################
// Request lookup of manual dial channel
	function ManualDialCheckChanneL(taskCheckOR)
		{
		if (taskCheckOR == 'YES')
			{
			var CIDcheck = XDnextCID;
			}
		else
			{
			var CIDcheck = MDnextCID;
			}
                 manDiaLlook_query = {server_ip:server_ip,session_name:session_name,ACTION:"manDiaLlookCaLL",conf_exten:session_id,user:user,pass:pass,MDnextCID:CIDcheck,agent_log_id:agent_log_id,lead_id:document.vicidial_form.lead_id.value,DiaL_SecondS:MD_ring_secondS};
			       
            $.post("vdc_db_query.php",manDiaLlook_query,function(data){
					var MDlookResponse_array=data;
					var MDlookCID = MDlookResponse_array[0];
					var regMDL = new RegExp("^Local","ig");
					if (MDlookCID == "NO")
						{
						MD_ring_secondS++;
						var dispnum = lead_dial_number;

						var status_display_number = phone_number_format(dispnum);

						if (alt_dial_status_display=='0')
							{
							document.getElementById("MainStatuSSpan").innerHTML = "A Marcar: " + status_display_number + " ID: " + CIDcheck + "  Á espera de ligação... " + MD_ring_secondS + " segundos";
							}
						}
					else
						{
						if (taskCheckOR == 'YES')
							{
							XDuniqueid = MDlookResponse_array[0];
							XDchannel = MDlookResponse_array[1];
							var XDalert = MDlookResponse_array[2];
							
							if (XDalert == 'ERROR')
								{
								var XDerrorDesc = MDlookResponse_array[3];
								var DiaLAlerTMessagE = "Chamada Rejeitada" + "\n" + XDerrorDesc; 
								TimerActionRun("DiaLAlerT",DiaLAlerTMessagE);
								}
							if ( (XDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') && (MD_ring_secondS < 10) )
								{
								// bad grab of Local channel, try again
								MD_ring_secondS++;
								}
							else
								{
								document.vicidial_form.xferuniqueid.value	= MDlookResponse_array[0];
								document.vicidial_form.xferchannel.value	= MDlookResponse_array[1];
								lastxferchannel = MDlookResponse_array[1];
								document.vicidial_form.xferlength.value		= 0;

								XD_live_customer_call = 1;
								XD_live_call_secondS = 0;
								MD_channel_look=0;

								var called3rdparty = document.vicidial_form.xfernumber.value;
								if (hide_xfer_number_to_dial=='ENABLED')
									{called3rdparty=' ';}
								document.getElementById("MainStatuSSpan").innerHTML = " Called 3rd party: " + called3rdparty /*+ " UID: " + CIDcheck*/;

                                document.getElementById("Leave3WayCall").innerHTML ="<a href=\"#\" onclick=\"leave_3way_call('FIRST');return false;\"><img src=\"/images/icons/telephone_go_32.png\" alt=\"LEAVE 3-WAY CALL\" style=\"vertical-align:middle\" />Tranferir a Chamada</a>";
								document.getElementById("Leave3WayCall").style.display="block";

                                document.getElementById("DialWithCustomer").innerHTML ="<img src=\"./images/vdc_XB_dialwithcustomer_OFF.gif\" border=\"0\" alt=\"Dial With Customer\" style=\"vertical-align:middle\" />";

                                document.getElementById("ParkCustomerDial").innerHTML ="<img src=\"/images/icons/telephone_add_32.png\" alt=\"Park Customer Dial\" style=\"vertical-align:middle\" />Solicitar Transferência";
								document.getElementById("ParkCustomerDial").style.display="none";
								
                                document.getElementById("HangupXferLine").innerHTML ="<a href=\"#\" onclick=\"xfercall_send_hangup();return false;\"><img src=\"/images/icons/telephone_delete_32.png\"  alt=\"Hangup Xfer Line\" style=\"vertical-align:middle\" />Desligar o solicitado</a>";
								document.getElementById("HangupXferLine").style.display="block";
								
                                document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><img src=\"./images/vdc_XB_hangupbothlines.gif\" border=\"0\" alt=\"Hangup Both Lines\" style=\"vertical-align:middle\" /></a>";

								xferchannellive=1;
								XDcheck = '';
								}
							}
						else
							{
							MDuniqueid = MDlookResponse_array[0];
							MDchannel = MDlookResponse_array[1];
							var MDalert = MDlookResponse_array[2];
							
							if (MDalert == 'ERROR')
								{
								var MDerrorDesc = MDlookResponse_array[3];
								var DiaLAlerTMessagE = "Chamada Rejeitada" + "\n" + MDerrorDesc;
								TimerActionRun("DiaLAlerT",DiaLAlerTMessagE);
								}
							if ( (MDchannel.match(regMDL)) && (asterisk_version != '1.0.8') && (asterisk_version != '1.0.9') )
								{
								// bad grab of Local channel, try again
								MD_ring_secondS++;
								}
							else
								{
								custchannellive=1;

								document.vicidial_form.uniqueid.value		= MDlookResponse_array[0];
								document.getElementById("callchannel").innerHTML	= MDlookResponse_array[1];
								lastcustchannel = MDlookResponse_array[1];
								if( document.images ) { document.images['livecall'].src = image_livecall_ON.src;}
								document.vicidial_form.SecondS.value		= 0;
								document.getElementById("SecondSDISP").innerHTML = '0';

								VD_live_customer_call = 1;
								VD_live_call_secondS = 0;

								MD_channel_look=0;
								var dispnum = lead_dial_number;
								var status_display_number = phone_number_format(dispnum);

								document.getElementById("MainStatuSSpan").innerHTML = " Chamado: " + status_display_number/* + " UID: " + CIDcheck*/; 

                                document.getElementById("ParkControl").innerHTML ="<td onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\" style='cursor:pointer'><img src='/images/icons/control_pause_blue.png' /></td><td onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\" style='cursor:pointer'><a href=\"#\">Colocar em Espera</a></td>";
								if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
									{
                                    document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKivr','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_LB_ivrparkcall.gif\" border=\"0\" alt=\"IVR Park Call\" /></a>";
									}

                                document.getElementById("HangupControl").innerHTML = "<td style='cursor:pointer' onclick='dialedcall_send_hangup();' width=32px><img src='/images/icons/control_eject_blue.png' /></td><td onclick='dialedcall_send_hangup();' style='cursor:pointer'><a href='#'>Desligar Chamada</a></td>";

                                document.getElementById("XferControl").innerHTML = "<td onclick=\"ShoWTransferMain('ON');\" style='cursor:pointer'><img src='/images/icons/control_repeat_blue.png' /></td><td onclick=\"ShoWTransferMain('ON');\" style='cursor:pointer'><a href=\"#\">Transferir Chamada</a></td>";

                                document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_localcloser.gif\" border=\"0\" alt=\"LOCAL CLOSER\" style=\"vertical-align:middle\" /></a>";

                                document.getElementById("DialBlindTransfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_blindtransfer.gif\" border=\"0\" alt=\"Dial Blind Transfer\" style=\"vertical-align:middle\" /></a>";

                                document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_ammessage.gif\" border=\"0\" alt=\"Blind Transfer VMail Message\" style=\"vertical-align:middle\" /></a>";

                                document.getElementById("VolumeUpSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('UP','" + MDchannel + "','');return false;\"><img src=\"./images/vdc_volume_up.gif\" border=\"0\"></a>";
                                document.getElementById("VolumeDownSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('DOWN','" + MDchannel + "','');return false;\"><img src=\"./images/vdc_volume_down.gif\" border=\"0\"></a>";

								if ( (quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP') )
									{
									quick_transfer_button_orig='';
									if (quick_transfer_button_locked > 0)
										{quick_transfer_button_orig = default_xfer_group;}

                                    document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;\"><img src=\"./images/vdc_LB_quickxfer.gif\" border=\"0\" alt=\"QUICK TRANSFER\" /></a>";
									}
								if (prepopulate_transfer_preset_enabled > 0)
									{
									if ( (prepopulate_transfer_preset == 'PRESET_1') || (prepopulate_transfer_preset == 'LOCKED_PRESET_1') )
										{document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
									if ( (prepopulate_transfer_preset == 'PRESET_2') || (prepopulate_transfer_preset == 'LOCKED_PRESET_2') )
										{document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
									if ( (prepopulate_transfer_preset == 'PRESET_3') || (prepopulate_transfer_preset == 'LOCKED_PRESET_3') )
										{document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
									if ( (prepopulate_transfer_preset == 'PRESET_4') || (prepopulate_transfer_preset == 'LOCKED_PRESET_4') )
										{document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
									if ( (prepopulate_transfer_preset == 'PRESET_5') || (prepopulate_transfer_preset == 'LOCKED_PRESET_5') )
										{document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
									}
								if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5') )
									{
									if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_1') )
										{document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
									if ( (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_2') )
										{document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
									if ( (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_3') )
										{document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
									if ( (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_4') )
										{document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
									if ( (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_5') )
										{document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
									quick_transfer_button_orig='';
									if (quick_transfer_button_locked > 0)
										{quick_transfer_button_orig = document.vicidial_form.xfernumber.value;}

                                    document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;\"><img src=\"./images/vdc_LB_quickxfer.gif\" border=\"0\" alt=\"QUICK TRANSFER\" /></a>";
									}

								if (call_requeue_button > 0)
									{
									var CloserSelectChoices = document.vicidial_form.CloserSelectList.value;
									var regCRB = new RegExp("AGENTDIRECT","ig");
									if ( (CloserSelectChoices.match(regCRB)) || (VU_closer_campaigns.match(regCRB)) )
										{
                                        document.getElementById("ReQueueCall").innerHTML =  "<a href=\"#\" onclick=\"call_requeue_launch();return false;\"><img src=\"./images/vdc_LB_requeue_call.gif\" border=\"0\" alt=\"Re-Queue Call\" /></a>";
										}
									else
										{
                                        document.getElementById("ReQueueCall").innerHTML =  "<img src=\"./images/vdc_LB_requeue_call_OFF.gif\" border=\"0\" alt=\"Re-Queue Call\" />";
										}
									}

								// Build transfer pull-down list
								var loop_ct = 0;
								var live_XfeR_HTML = '';
								var XfeR_SelecT = '';
								while (loop_ct < XFgroupCOUNT)
									{
									if (VARxfergroups[loop_ct] == LIVE_default_xfer_group)
										{XfeR_SelecT = 'selected ';}
									else {XfeR_SelecT = '';}
									live_XfeR_HTML = live_XfeR_HTML + "<option " + XfeR_SelecT + "value=\"" + VARxfergroups[loop_ct] + "\">" + VARxfergroups[loop_ct] + " - " + VARxfergroupsnames[loop_ct] + "</option>\n";
									loop_ct++;
									}
                                document.getElementById("XfeRGrouPLisT").innerHTML = "<select size=\"1\" name=\"XfeRGrouP\" id=\"XfeRGrouP\" class=\"cust_form\" onChange=\"XferAgentSelectLink();return false;\">" + live_XfeR_HTML + "</select>";

								// INSERT VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
								DialLog("start");

								custchannellive=1;
								}
							}
						}
					})
		

		if (MD_ring_secondS > 49) 
			{
			MD_channel_look=0;
			MD_ring_secondS=0;
			//alert_box("Existe um problema com o sistema. Contacte o administrador.\n");
			}

		}

// ################################################################################
// Update Agent screen with values from vicidial_list record
	function UpdateFieldsData()
		{
		var fields_list = update_fields_data + ',';
		update_fields=0;
		update_fields_data='';
		$.post('vdc_db_query.php',
            {server_ip:server_ip,
                session_name:session_name,
                ACTION:"UpdateFields",
                conf_exten:session_id,
                user:user,pass:pass,
                stage:update_fields_data},
            function(data) 
        { 
				
					

					var UDfieldsResponse_array=data;

					var UDresponse_status							= UDfieldsResponse_array[0];
					if (UDresponse_status == 'GOOD')
						{
						var regUDvendor_lead_code = new RegExp("vendor_lead_code,","ig");
						if (fields_list.match(regUDvendor_lead_code))
							{document.vicidial_form.vendor_lead_code.value	= UDfieldsResponse_array[1];}
						var regUDsource_id = new RegExp("source_id,","ig");
						if (fields_list.match(regUDsource_id))
							{source_id										= UDfieldsResponse_array[2];}
						var regUDgmt_offset_now = new RegExp("gmt_offset_now,","ig");
						if (fields_list.match(regUDgmt_offset_now))
							{document.vicidial_form.gmt_offset_now.value	= UDfieldsResponse_array[3];}
						var regUDphone_code = new RegExp("phone_code,","ig");
						if (fields_list.match(regUDphone_code))
							{document.vicidial_form.phone_code.value		= UDfieldsResponse_array[4];}
						var regUDphone_number = new RegExp("phone_number,","ig");
						if (fields_list.match(regUDphone_number))
							{
							if ( (disable_alter_custphone=='Y') || (disable_alter_custphone=='HIDE') )
								{
								var tmp_pn = $("#phone_numberDISP")[0];
								if (disable_alter_custphone=='Y')
									{
									tmp_pn.innerHTML						= UDfieldsResponse_array[5];
									redial_number							= UDfieldsResponse_array[5];
									}
								}
							document.vicidial_form.phone_number.value		= UDfieldsResponse_array[5];
							}
						var regUDtitle = new RegExp("title,","ig");
						if (fields_list.match(regUDtitle))
							{document.vicidial_form.title.value				= UDfieldsResponse_array[6];}
						var regUDfirst_name = new RegExp("first_name,","ig");
						if (fields_list.match(regUDfirst_name))
							{document.vicidial_form.first_name.value		= UDfieldsResponse_array[7];}
						var regUDmiddle_initial = new RegExp("middle_initial,","ig");
						if (fields_list.match(regUDmiddle_initial))
							{document.vicidial_form.middle_initial.value	= UDfieldsResponse_array[8];}
						var regUDlast_name = new RegExp("last_name,","ig");
						if (fields_list.match(regUDlast_name))
							{document.vicidial_form.last_name.value			= UDfieldsResponse_array[9];}
						var regUDaddress1 = new RegExp("address1,","ig");
						if (fields_list.match(regUDaddress1))
							{document.vicidial_form.address1.value			= UDfieldsResponse_array[10];}
						var regUDaddress2 = new RegExp("address2,","ig");
						if (fields_list.match(regUDaddress2))
							{document.vicidial_form.address2.value			= UDfieldsResponse_array[11];}
						var regUDaddress3 = new RegExp("address3,","ig");
						if (fields_list.match(regUDaddress3))
							{document.vicidial_form.address3.value			= UDfieldsResponse_array[12];}
						var regUDcity = new RegExp("city,","ig");
						if (fields_list.match(regUDcity))
							{document.vicidial_form.city.value				= UDfieldsResponse_array[13];}
						var regUDstate = new RegExp("state,","ig");
						if (fields_list.match(regUDstate))
							{document.vicidial_form.state.value				= UDfieldsResponse_array[14];}
						var regUDprovince = new RegExp("province,","ig");
						if (fields_list.match(regUDprovince))
							{document.vicidial_form.province.value			= UDfieldsResponse_array[15];}
						var regUDpostal_code = new RegExp("postal_code,","ig");
						if (fields_list.match(regUDpostal_code))
							{document.vicidial_form.postal_code.value		= UDfieldsResponse_array[16];}
						var regUDcountry_code = new RegExp("country_code,","ig");
						if (fields_list.match(regUDcountry_code))
							{document.vicidial_form.country_code.value		= UDfieldsResponse_array[17];}
						var regUDgender = new RegExp("gender,","ig");
						if (fields_list.match(regUDgender))
							{
							document.vicidial_form.gender.value				= UDfieldsResponse_array[18];
							if (hide_gender > 0)
								{
								document.vicidial_form.gender_list.value		= UDfieldsResponse_array[18];
								}
							else
								{
								var gIndex = 0;
								if (document.vicidial_form.gender.value == 'M') {var gIndex = 1;}
								if (document.vicidial_form.gender.value == 'F') {var gIndex = 2;}
								$("#gender_list")[0].selectedIndex = gIndex;
								var genderIndex = $("#gender_list")[0].selectedIndex;
								var genderValue =  $('#gender_list')[0].options[genderIndex].value;
								document.vicidial_form.gender.value = genderValue;
								}
							}
						var regUDdate_of_birth = new RegExp("date_of_birth,","ig");
						if (fields_list.match(regUDdate_of_birth))
							{document.vicidial_form.date_of_birth.value		= UDfieldsResponse_array[19];}
						var regUDalt_phone = new RegExp("alt_phone,","ig");
						if (fields_list.match(regUDalt_phone))
							{document.vicidial_form.alt_phone.value			= UDfieldsResponse_array[20];}
						var regUDemail = new RegExp("email,","ig");
						if (fields_list.match(regUDemail))
							{document.vicidial_form.email.value				= UDfieldsResponse_array[21];}
						var regUDsecurity_phrase = new RegExp("security_phrase,","ig");
						if (fields_list.match(regUDsecurity_phrase))
							{document.vicidial_form.security_phrase.value	= UDfieldsResponse_array[22];}
						var regUDcomments = new RegExp("comments,","ig");
						if (fields_list.match(regUDcomments))
							{
							var REGcommentsNL = new RegExp("!N","g");
							UDfieldsResponse_array[23] = UDfieldsResponse_array[23].replace(REGcommentsNL, "\n");
							document.vicidial_form.comments.value			= UDfieldsResponse_array[23];
							}
						var regUDrank = new RegExp("rank,","ig");
						if (fields_list.match(regUDrank))
							{document.vicidial_form.rank.value				= UDfieldsResponse_array[24];}
						var regUDowner = new RegExp("owner,","ig");
						if (fields_list.match(regUDowner))
							{document.vicidial_form.owner.value				= UDfieldsResponse_array[25];}
						var regUDformreload = new RegExp("formreload,","ig");
						if (fields_list.match(regUDformreload))
							{FormContentsLoad();}

						var regWFAcustom = new RegExp("^VAR","ig");
						if (VDIC_web_form_address.match(regWFAcustom))
							{
							TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
							TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
							}
						else
							{
							TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
							}

						if (VDIC_web_form_address_two.match(regWFAcustom))
							{
							TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
							TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
							}
						else
							{
							TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
							}

                        $("#WebFormSpan").html("<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\"><img src=\"./images/vdc_LB_webform.gif\" border=\"0\" alt=\"Web Form\" /></a>\n");
						if (enable_second_webform > 0)
							{
                            $("#WebFormSpanTwo").html("<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\"><img src=\"./images/vdc_LB_webform_two.gif\" border=\"0\" alt=\"Web Form 2\" /></a>\n");
							}
						}
					else
						{
						alert_box("Update Fields Error!: " + data);
						}
					
				},"json");
			}
		


	function redial() {
                var go_on = divchecker("redial");
                    if (!go_on) { return; }
                if ( AgentDispoing > 0) {
                        alert_box('Termine Wrap-up da chamada.')
                        return;
                    }
                
                    if  ( AutoDialReady == 0 && auto_dial_level > 0 && pause_code_counter == 0) {
                    alert_box('Seleccione o motivo de pausa por favor.');
                    return;
                    }
		var segue=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if (!((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) ))
			{
			segue=0;
			alert_box('Tem de estar em pausa para fazer a re-marcação.');
			} 
		}
		if (segue==1)
		{
			if (!redial_number==0) 
			{
				NewRedialSubmiT(redial_number);
			}
			else
			{
				alert_box('Ainda não fez a primeira chamada.');
			}
		} 
		}
        function divchecker(curDiv) {
            var rdytogo = 1;
                if ($("#SearcHForMDisplaYBox").css("display") != 'none' && curDiv != "search") { rdytogo = 0;}
                if ($("#NeWManuaLDiaLBox").css("display") != 'none' && curDiv != "mdial") { rdytogo = 0;}
                if ($("#CallBacKsLisTBox").css("display") != 'none' && curDiv != "cback") { rdytogo = 0;}
                if ($("#CloserSelectBox").css("display") != 'none' && curDiv != "closer") { rdytogo = 0;}
                if ($("#CallHistoryDialog").parent().css("display") != 'none' && curDiv != "CallHistoryDialog") { rdytogo = 0;}
            return rdytogo;
        }
	function NewRedialSubmiT(nr)
		{
		var sending_group_alias = 0;
		var MDDiaLCodEform = document.vicidial_form.MDDiaLCodE.value;
		var MDLeadIDform = document.vicidial_form.MDLeadID.value;
		var MDTypeform = document.vicidial_form.MDType.value;
		var MDDiaLOverridEform = document.vicidial_form.MDDiaLOverridE.value;
		var MDVendorLeadCode = document.vicidial_form.vendor_lead_code.value;
		var MDLookuPLeaD = 'lookup';

		if (MDDiaLCodEform.length < 1)
			{MDDiaLCodEform = document.vicidial_form.phone_code.value;}

		if (MDDiaLOverridEform.length > 0)
			{
			agent_dialed_number=1;
			agent_dialed_type='MANUAL_OVERRIDE';
			basic_originate_call(session_id,'NO','YES',MDDiaLOverridEform,'YES','','1','0');
			}
		else
			{
			auto_dial_level=0;
			manual_dial_in_progress=1;
			agent_dialed_number=1;
			MainPanelToFront();

				agent_dialed_type='MANUAL_DIALNOW';
				document.vicidial_form.LeadPreview.checked=false;
				document.vicidial_form.DiaLAltPhonE.checked=false;
				
			if (active_group_alias.length > 1)
				{var sending_group_alias = 1;}

			ManualDialNext("",MDLeadIDform,MDDiaLCodEform,nr,MDLookuPLeaD,MDVendorLeadCode,sending_group_alias,MDTypeform);
			}

		document.vicidial_form.MDPhonENumbeR.value = '';
		document.vicidial_form.MDDiaLOverridE.value = '';
		document.vicidial_form.MDLeadID.value = '';
		document.vicidial_form.MDType.value = '';
		}
// ################################################################################
// Send the Manual Dial Skip
	function ManualDialSkip()
		{
		if (manual_dial_in_progress==1)
			{
			reactive_last_callback();
                        $("#ResumeControl").html(ResumeControl_auto_ON_HTML);
			}
		
			in_lead_preview_state=0;
			if (dial_method == "INBOUND_MAN" || dial_method == "RATIO")
				{
				auto_dial_level=starting_dial_level;

                document.getElementById("DiaLControl").innerHTML = "<img src=\"./images/vdc_LB_pause_OFF.gif\" border=\"0\" alt=\" Pause \" /><img src=\"./images/vdc_LB_resume_OFF.gif\" border=\"0\" alt=\"Resume\" /><br /><img src=\"./images/vdc_LB_dialnextnumber_OFF.gif\" border=\"0\" alt=\"Dial Next Number\" />";
				}
			else
				{
                document.getElementById("DiaLControl").innerHTML = "<td><img src='/images/icons/control_end.png' /></td><td>Marcar Seguinte</td>";
				}
                                manDiaLskip_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLskip&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&lead_id=" + document.vicidial_form.lead_id.value + "&stage=" + previous_dispo + "&called_count=" + previous_called_count;
				
                                    $.post('vdc_db_query.php',manDiaLskip_query, function(data) 
					{ 
					

						MDSnextCID = data[0];
						if (MDSnextCID == "LEAD NOT REVERTED")
							{
							alert_box("Dados não guardados, houve um erro:\n" + data[1]);
							}
						else
							{
							document.vicidial_form.lead_id.value		='';
							document.vicidial_form.vendor_lead_code.value='';
							document.vicidial_form.list_id.value		='';
							document.vicidial_form.entry_list_id.value	='';
							document.vicidial_form.gmt_offset_now.value	='';
							document.vicidial_form.phone_code.value		='';
							if ( (disable_alter_custphone=='Y') || (disable_alter_custphone=='HIDE') )
								{
								var tmp_pn = document.getElementById("phone_numberDISP");
								tmp_pn.innerHTML			= '';
								}
							document.vicidial_form.phone_number.value	='';
							document.vicidial_form.title.value			='';
							document.vicidial_form.first_name.value		='';
							document.vicidial_form.middle_initial.value	='';
							document.vicidial_form.last_name.value		='';
							document.vicidial_form.address1.value		='';
							document.vicidial_form.address2.value		='';
							document.vicidial_form.address3.value		='';
							document.vicidial_form.city.value			='';
							document.vicidial_form.state.value			='';
							document.vicidial_form.province.value		='';
							document.vicidial_form.postal_code.value	='';
							document.vicidial_form.country_code.value	='';
							document.vicidial_form.gender.value			='';
							document.vicidial_form.date_of_birth.value	='';
							document.vicidial_form.alt_phone.value		='';
							document.vicidial_form.email.value			='';
							document.vicidial_form.security_phrase.value='';
							document.vicidial_form.comments.value		='';
							document.vicidial_form.called_count.value	='';
							document.vicidial_form.rank.value			='';
							document.vicidial_form.owner.value			='';
							document.vicidial_form.extra1.value			='';
							document.vicidial_form.extra2.value			='';
							document.vicidial_form.extra3.value			='';
							document.vicidial_form.extra4.value			='';
							document.vicidial_form.extra5.value			='';
							document.vicidial_form.extra6.value			='';
							document.vicidial_form.extra7.value			='';
							document.vicidial_form.extra8.value			='';
							document.vicidial_form.extra9.value			='';
							document.vicidial_form.extra10.value			='';
							document.vicidial_form.extra11.value			='';
							document.vicidial_form.extra12.value			='';
							document.vicidial_form.extra13.value			='';
							document.vicidial_form.extra14.value			='';
							document.vicidial_form.extra15.value			='';
							VDCL_group_id = '';
							fronter = '';
							previous_called_count = '';
							previous_dispo = '';
							custchannellive=1;

							if (post_phone_time_diff_alert_message.length > 10)
								{
								document.getElementById("post_phone_time_diff_span_contents").innerHTML = "";
								hideDiv('post_phone_time_diff_span');
								}

							document.getElementById("MainStatuSSpan").innerHTML = "Lead ignorada.";

							if (dial_method == "INBOUND_MAN")
								{
                                document.getElementById("DiaLControl").innerHTML = "<img src=\"./images/vdc_LB_pause_OFF.gif\" border=\"0\" alt=\" Pause \" /><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\"./images/vdc_LB_resume.gif\" border=\"0\" alt=\"Resume\" /></a><br /><a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><img src=\"./images/vdc_LB_dialnextnumber.gif\" border=\"0\" alt=\"Dial Next Number\" /></a>";
								}
							else
								{
                                document.getElementById("DiaLControl").innerHTML = "<td><img src='/images/icons/control_end_blue.png' /></td><td><a href='#' onclick=\"ManualDialNext('','','','','','0');\">Marcar Seguinte</a></td>";
								
								}
							}
						
					});
				
				active_group_alias='';
				cid_choice='';
				prefix_choice='';
				agent_dialed_number='';
				agent_dialed_type='';
				CalL_ScripT_id='';
				}


// ################################################################################
// Send the Manual Dial Only - dial the previewed lead
	function ManualDialOnly(taskaltnum)
		{
		in_lead_preview_state=0;
		inOUT = 'OUT';
		alt_dial_status_display = 0;
		all_record = 'NO';
		all_record_count=0;
		var usegroupalias=0;
		if (taskaltnum == 'ALTPhonE')
			{
			var manDiaLonly_num = document.vicidial_form.alt_phone.value;
			lead_dial_number = document.vicidial_form.alt_phone.value;
			dialed_number = lead_dial_number;
			dialed_label = 'ALT';
			WebFormRefresH('');
			}
		else
			{
			if (taskaltnum == 'AddresS3')
				{
				var manDiaLonly_num = document.vicidial_form.address3.value;
				lead_dial_number = document.vicidial_form.address3.value;
				dialed_number = lead_dial_number;
				dialed_label = 'ADDR3';
				WebFormRefresH('');
				}
			else
				{
				var manDiaLonly_num = document.vicidial_form.phone_number.value;
				lead_dial_number = document.vicidial_form.phone_number.value;
				dialed_number = lead_dial_number;
				dialed_label = 'MAIN';
				WebFormRefresH('');
				}
			}
		if (dialed_label == 'ALT')
            {document.getElementById("CusTInfOSpaN").innerHTML = " <b> A marcar nº alternativo: ALT </b>";}
		if (dialed_label == 'ADDR3')
            {document.getElementById("CusTInfOSpaN").innerHTML = " <b> A marcar nº alternativo: ADDRESS3 </b>";}
		var REGalt_dial = new RegExp("X","g");
		if (dialed_label.match(REGalt_dial))
			{
            document.getElementById("CusTInfOSpaN").innerHTML = " <b> A marcar nº alternativo: " + dialed_label + "</b>";
			document.getElementById("EAcommentsBoxA").innerHTML = "<b>Phone Code and Number: </b>" + EAphone_code + " " + EAphone_number;

			var EAactive_link = '';
			if (EAalt_phone_active == 'Y') 
				{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','N');\">Change this phone number to INACTIVE</a>";}
			else
				{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','Y');\">Change this phone number to ACTIVE</a>";}

            document.getElementById("EAcommentsBoxB").innerHTML = "<b>Active: </b>" + EAalt_phone_active + "<br />" + EAactive_link;
			document.getElementById("EAcommentsBoxC").innerHTML = "<b>Alt Count: </b>" + EAalt_phone_count;
            document.getElementById("EAcommentsBoxD").innerHTML = "<b>Notes: </b><br />" + EAalt_phone_notes;
			showDiv('EAcommentsBox');
			}
                        
            manDiaLonly_query = {server_ip:server_ip,session_name:session_name,ACTION:"manDiaLonly",conf_exten:session_id,user:user,pass:pass,lead_id:document.vicidial_form.lead_id.value,phone_number:manDiaLonly_num,phone_code:document.vicidial_form.phone_code.value,campaign:campaign,ext_context:ext_context,dial_timeout:dial_timeout,dial_prefix:call_prefix,campaign_cid:call_cid,omit_phone_code:omit_phone_code,usegroupalias:usegroupalias,account:active_group_alias,agent_dialed_number:agent_dialed_number,agent_dialed_type:agent_dialed_type,dial_method:dial_method,agent_log_id:agent_log_id,security:document.vicidial_form.security_phrase.value,portabilidade:portabilidade};
			
            $.post("vdc_db_query.php",manDiaLonly_query,function(data){
                            
                        })
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			if (cid_choice.length > 3) 
				{
				var call_cid = cid_choice;
				usegroupalias=1;
				}
			else 
				{
				var call_cid = campaign_cid;
				if (manual_dial_cid == 'AGENT_PHONE')
					{call_cid = outbound_cid;}
				}
			if (prefix_choice.length > 0)
				{var call_prefix = prefix_choice;}
			else
				{var call_prefix = manual_dial_prefix;}

			manDiaLonly_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=manDiaLonly&conf_exten=" + session_id + "&user=" + user + "&pass=" + pass + "&lead_id=" + document.vicidial_form.lead_id.value + "&phone_number=" + manDiaLonly_num + "&phone_code=" + document.vicidial_form.phone_code.value + "&campaign=" + campaign + "&ext_context=" + ext_context + "&dial_timeout=" + dial_timeout + "&dial_prefix=" + call_prefix + "&campaign_cid=" + call_cid + "&omit_phone_code=" + omit_phone_code + "&usegroupalias=" + usegroupalias + "&account=" + active_group_alias + "&agent_dialed_number=" + agent_dialed_number + "&agent_dialed_type=" + agent_dialed_type + "&dial_method=" + dial_method + "&agent_log_id=" + agent_log_id + "&security=" + document.vicidial_form.security_phrase.value +"&portabilidade="+portabilidade;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(manDiaLonly_query);
			xmlhttp.onreadystatechange = function() 
				{ 
                                    portabilidade=0;
                                
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var MDOnextResponse = null;
			//		alert(manDiaLonly_query);
			//		alert(xmlhttp.responseText);
					MDOnextResponse = xmlhttp.responseText;

					var MDOnextResponse_array=MDOnextResponse.split("\n");
					MDnextCID =		MDOnextResponse_array[0];
					LastCallCID =	MDOnextResponse_array[0];
					agent_log_id =	MDOnextResponse_array[1];
					if (MDnextCID == " CALL NOT PLACED")
						{
						alert_box("A chamada não foi feita devido a um erro:\n" + MDOnextResponse);
						}
					else
						{
						LasTCID =	MDOnextResponse_array[0];
						MD_channel_look=1;
						custchannellive=1;

						var dispnum = manDiaLonly_num;
						var status_display_number = phone_number_format(dispnum);

						if (alt_dial_status_display=='0')
							{
							document.getElementById("MainStatuSSpan").innerHTML = " A Marcar: " + status_display_number /*+ " ID: " + MDnextCID */+ " Á espera de ligação...";
							
                            document.getElementById("HangupControl").innerHTML = "<td onclick='dialedcall_send_hangup();' style='cursor:pointer' width=32px><img src='/images/icons/control_eject_blue.png' /></td><td onclick='dialedcall_send_hangup();' style='cursor:pointer'><a href='#' >Desligar Chamada</a></td>";
							}
						if ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') )
							{all_record = 'YES';}

						if ( (view_scripts == 1) && (campaign_script.length > 0) )
							{
							var SCRIPT_web_form = 'http://127.0.0.1/testing.php';
							var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');

							if ( (script_recording_delay > 0) && ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) )
								{
								delayed_script_load = 'YES';
								RefresHScript('CLEAR');
								}
							else
								{
								load_script_contents();
								}
							}
						if (limesurvey_enabled === 1) { FormContentsLoad(); }
						if (custom_fields_enabled > 0)
							{
							FormContentsLoad();
							}
						if (get_call_launch == 'SCRIPT')
							{
							if (delayed_script_load == 'YES')
								{
								load_script_contents();
								}
							ScriptPanelToFront();
							}
						if (get_call_launch == 'FORM')
							{
							FormPanelToFront();
							}
						if (get_call_launch == 'WEBFORM')
							{
							window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
							}
						if (get_call_launch == 'WEBFORMTWO')
							{
							window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
							}
						}
					}
				}
			delete xmlhttp;
			active_group_alias='';
			cid_choice='';
			prefix_choice='';
			agent_dialed_number='';
			agent_dialed_type='';
			CalL_ScripT_id='';
			}
		}


// ################################################################################
// Set the client to READY and start looking for calls (VDADready, VDADpause)
	function AutoDial_ReSume_PauSe(taskaction,taskagentlog,taskwrapup,taskstatuschange,temp_reason,temp_auto,temp_auto_code)
		{
					var go_on = divchecker("auto_dial");
                    if (!go_on) { return; }	
			get_tempo_pausa();
			
		var add_pause_code='';
		if (taskaction == 'VDADready')
			{
			VDRP_stage = 'READY';
			if (INgroupCOUNT > 0)
				{
				if (VICIDiaL_closer_blended == 0)
					{VDRP_stage = 'CLOSER';}
				else 
					{VDRP_stage = 'READY';}
				}
			AutoDialReady = 1;
			AutoDialWaiting = 1;
			if (dial_method == "INBOUND_MAN")
				{
				auto_dial_level=starting_dial_level;

                document.getElementById("DiaLControl").innerHTML = "<a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADpause');\"><img src=\"./images/vdc_LB_pause.gif\" border=\"0\" alt=\" Pause \" /></a><img src=\"./images/vdc_LB_resume_OFF.gif\" border=\"0\" alt=\"Resume\" /></a><br /><a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><img src=\"./images/vdc_LB_dialnextnumber.gif\" border=\"0\" alt=\"Dial Next Number\" /></a>";
				}
			else
				{
				//document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML_ready;
				document.getElementById("ResumeControl").innerHTML = ResumeControl_auto_OFF_HTML;
				document.getElementById("PauseControl").innerHTML = PauseControl_auto_ON_HTML;
				}
			}
		else
			{		
			VDRP_stage = 'PAUSED';
			AutoDialReady = 0;
			AutoDialWaiting = 0;
			pause_code_counter = 0;
			if (dial_method == "INBOUND_MAN")
				{
				auto_dial_level=starting_dial_level;

                document.getElementById("DiaLControl").innerHTML = "<img src=\"./images/vdc_LB_pause_OFF.gif\" border=\"0\" alt=\" Pause \" /><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\"./images/vdc_LB_resume.gif\" border=\"0\" alt=\"Resume\" /></a><br /><a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><img src=\"./images/vdc_LB_dialnextnumber.gif\" border=\"0\" alt=\"Dial Next Number\" /></a>";
				}
			else
				{
				//document.getElementById("DiaLControl").innerHTML = DiaLControl_auto_HTML;
                                if ( (agent_pause_codes_active=='FORCE') && (temp_reason != 'LOGOUT') && (temp_reason != 'REQUEUE') && (temp_reason != 'DIALNEXT') && (temp_auto != '1') )
				{
                                    document.getElementById("ResumeControl").innerHTML = ResumeControl_auto_OFF_HTML;
 				}else
                                {				
                                    document.getElementById("ResumeControl").innerHTML = ResumeControl_auto_ON_HTML;
                                }
				document.getElementById("PauseControl").innerHTML = PauseControl_auto_OFF_HTML;
				}

			if ( (agent_pause_codes_active=='FORCE') && (temp_reason != 'LOGOUT') && (temp_reason != 'REQUEUE') && (temp_reason != 'DIALNEXT') && (temp_auto != '1') )
				{
				PauseCodeSelectContent_create();
 				}
			if (temp_auto == '1')
				{
				add_pause_code = "&sub_status=" + temp_auto_code;
				}
			}

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			autoDiaLready_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=" + taskaction + "&user=" + user + "&pass=" + pass + "&stage=" + VDRP_stage + "&agent_log_id=" + agent_log_id + "&agent_log=" + taskagentlog + "&wrapup=" + taskwrapup + "&campaign=" + campaign + "&dial_method=" + dial_method + "&comments=" + taskstatuschange + add_pause_code;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(autoDiaLready_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var check_dispo = null;
					check_dispo = xmlhttp.responseText;
					var check_DS_array=check_dispo.split("\n");
				//	alert(xmlhttp.responseText + "\n|" + check_DS_array[1] + "\n|" + check_DS_array[2] + "|");
					if (check_DS_array[1] == 'Next agent_log_id:')
						{agent_log_id = check_DS_array[2];}
					}
				}
			delete xmlhttp;
			}
		return agent_log_id;
		}



// ################################################################################
// Check to see if there is a call being sent from the auto-dialer to agent conf
	function ReChecKCustoMerChaN()
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			recheckVDAI_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=VDADREcheckINCOMING" + "&agent_log_id=" + agent_log_id + "&lead_id=" + document.vicidial_form.lead_id.value;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(recheckVDAI_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var recheck_incoming = null;
					recheck_incoming = xmlhttp.responseText;
				//	alert(xmlhttp.responseText);
					var recheck_VDIC_array=recheck_incoming.split("\n");
					if (recheck_VDIC_array[0] == '1')
						{
						var reVDIC_data_VDAC=recheck_VDIC_array[1].split("|");
						if (reVDIC_data_VDAC[3] == lastcustchannel)
							{
						// do nothing
							}
						else
							{
				//	alert("Channel has changed from:\n" + lastcustchannel + '|' + lastcustserverip + "\nto:\n" + reVDIC_data_VDAC[3] + '|' + reVDIC_data_VDAC[4]);
							document.getElementById("callchannel").innerHTML	= reVDIC_data_VDAC[3];
							lastcustchannel = reVDIC_data_VDAC[3];
							document.vicidial_form.callserverip.value	= reVDIC_data_VDAC[4];
							lastcustserverip = reVDIC_data_VDAC[4];
							custchannellive = 1;
							}
						}
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// pull the script contents sending the webform variables to the script display script
	function load_script_contents()
		{
		var new_script_content = null;
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			NeWscript_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ScrollDIV=1&" + web_form_vars;
			xmlhttp.open('POST', 'vdc_script_display.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(NeWscript_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					new_script_content = xmlhttp.responseText;
					document.getElementById("ScriptContents").innerHTML = new_script_content;
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Alternate phone number change
	function alt_phone_change(APCphone,APCcount,APCleadID,APCactive)
		{

		var EAactive_link = '';
		if (APCactive == 'Y') 
			{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','N');\">Change this phone number to INACTIVE</a>";}
		else
			{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','Y');\">Change this phone number to ACTIVE</a>";}

        document.getElementById("EAcommentsBoxB").innerHTML = "<b>Active: </b>" + EAalt_phone_active + "<br />" + EAactive_link;

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			APC_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&ACTION=alt_phone_change" + "&phone_number=" + APCphone + "&lead_id=" + APCleadID + "&called_count=" + APCcount + "&stage=" + APCactive;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(APC_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Check to see if there is a call being sent from the auto-dialer to agent conf
	function check_for_auto_incoming()
		{
		
			all_record = 'NO';
			all_record_count=0;
			$.post("vdc_db_query.php",
                        {server_ip:server_ip,
                            session_name:session_name,
                            user:user,
                            pass:pass,
                            campaign:campaign,
                            ACTION:"VDADcheckINCOMING",
                            agent_log_id:agent_log_id},
                            function(data) 
					{ 
							var check_VDIC_array=data;
						if (check_VDIC_array[0] == '1')
							{
							AutoDialWaiting = 0;

							var VDIC_data_VDAC=check_VDIC_array[1].split("|");
							VDIC_web_form_address = VICIDiaL_web_form_address
							VDIC_web_form_address_two = VICIDiaL_web_form_address_two
							var VDIC_fronter='';

							var VDIC_data_VDIG=check_VDIC_array[2].split("|");
							if (VDIC_data_VDIG[0].length > 5)
								{VDIC_web_form_address = VDIC_data_VDIG[0];}
							var VDCL_group_name			= VDIC_data_VDIG[1];
							var VDCL_group_color		= VDIC_data_VDIG[2];
							var VDCL_fronter_display	= VDIC_data_VDIG[3];
							 VDCL_group_id				= VDIC_data_VDIG[4];
							 CalL_ScripT_id				= VDIC_data_VDIG[5];
							 CalL_AutO_LauncH			= VDIC_data_VDIG[6];
							 CalL_XC_a_Dtmf				= VDIC_data_VDIG[7];
							 CalL_XC_a_NuMber			= VDIC_data_VDIG[8];
							 CalL_XC_b_Dtmf				= VDIC_data_VDIG[9];
							 CalL_XC_b_NuMber			= VDIC_data_VDIG[10];
							if ( (VDIC_data_VDIG[11].length > 1) && (VDIC_data_VDIG[11] != '---NONE---') )
								{LIVE_default_xfer_group = VDIC_data_VDIG[11];}
							else
								{LIVE_default_xfer_group = default_xfer_group;}

							if ( (VDIC_data_VDIG[12].length > 1) && (VDIC_data_VDIG[12]!='DISABLED') )
								{LIVE_campaign_recording = VDIC_data_VDIG[12];}
							else
								{LIVE_campaign_recording = campaign_recording;}

							if ( (VDIC_data_VDIG[13].length > 1) && (VDIC_data_VDIG[13]!='NONE') )
								{LIVE_campaign_rec_filename = VDIC_data_VDIG[13];}
							else
								{LIVE_campaign_rec_filename = campaign_rec_filename;}

							if ( (VDIC_data_VDIG[14].length > 1) && (VDIC_data_VDIG[14]!='NONE') )
								{LIVE_default_group_alias = VDIC_data_VDIG[14];}
							else
								{LIVE_default_group_alias = default_group_alias;}

							if ( (VDIC_data_VDIG[15].length > 1) && (VDIC_data_VDIG[15]!='NONE') )
								{LIVE_caller_id_number = VDIC_data_VDIG[15];}
							else
								{LIVE_caller_id_number = default_group_alias_cid;}

							if (VDIC_data_VDIG[16].length > 0)
								{LIVE_web_vars = VDIC_data_VDIG[16];}
							else
								{LIVE_web_vars = default_web_vars;}

							if (VDIC_data_VDIG[17].length > 5)
								{VDIC_web_form_address_two = VDIC_data_VDIG[17];}

							var call_timer_action							= VDIC_data_VDIG[18];

							if ( (call_timer_action == 'NONE') || (call_timer_action.length < 2) )
								{
								timer_action = campaign_timer_action;
								timer_action_message = campaign_timer_action_message;
								timer_action_seconds = campaign_timer_action_seconds;
								timer_action_destination = campaign_timer_action_destination;
								}
							else
								{
								var call_timer_action_message				= VDIC_data_VDIG[19];
								var call_timer_action_seconds				= VDIC_data_VDIG[20];
								var call_timer_action_destination			= VDIC_data_VDIG[27];
								timer_action = call_timer_action;
								timer_action_message = call_timer_action_message;
								timer_action_seconds = call_timer_action_seconds;
								timer_action_destination = call_timer_action_destination;
								}

							CalL_XC_c_NuMber			= VDIC_data_VDIG[21];
							CalL_XC_d_NuMber			= VDIC_data_VDIG[22];
							CalL_XC_e_NuMber			= VDIC_data_VDIG[23];
							CalL_XC_e_NuMber			= VDIC_data_VDIG[23];
							uniqueid_status_display		= VDIC_data_VDIG[24];
							uniqueid_status_prefix		= VDIC_data_VDIG[26];
							did_id						= VDIC_data_VDIG[28];
							did_extension				= VDIC_data_VDIG[29];
							did_pattern					= VDIC_data_VDIG[30];
							did_description				= VDIC_data_VDIG[31];
							closecallid					= VDIC_data_VDIG[32];
							xfercallid					= VDIC_data_VDIG[33];

							var VDIC_data_VDFR=check_VDIC_array[3].split("|");
							if ( (VDIC_data_VDFR[1].length > 1) && (VDCL_fronter_display == 'Y') )
								{VDIC_fronter = VDIC_data_VDFR[0] + " - " + VDIC_data_VDFR[1] + " " + did_description;}
							
							document.vicidial_form.lead_id.value		= VDIC_data_VDAC[0];
							document.vicidial_form.uniqueid.value		= VDIC_data_VDAC[1];
							CIDcheck									= VDIC_data_VDAC[2];
							CalLCID										= VDIC_data_VDAC[2];
							LastCallCID									= VDIC_data_VDAC[2];
							document.getElementById("callchannel").innerHTML	= VDIC_data_VDAC[3];
							lastcustchannel = VDIC_data_VDAC[3];
							document.vicidial_form.callserverip.value	= VDIC_data_VDAC[4];
							lastcustserverip = VDIC_data_VDAC[4];
							if( document.images ) { document.images['livecall'].src = image_livecall_ON.src;}
							document.vicidial_form.SecondS.value		= 0;
							document.getElementById("SecondSDISP").innerHTML = '0';

							if (uniqueid_status_display=='ENABLED')
								{custom_call_id			= " Call ID " + VDIC_data_VDAC[1];}
							if (uniqueid_status_display=='ENABLED_PREFIX')
								{custom_call_id			= " Call ID " + uniqueid_status_prefix + "" + VDIC_data_VDAC[1];}
							if (uniqueid_status_display=='ENABLED_PRESERVE')
								{custom_call_id			= " Call ID " + VDIC_data_VDIG[25];}

							VD_live_customer_call = 1;
							VD_live_call_secondS = 0;

							// INSERT VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
					
							custchannellive=1;

							LasTCID											= check_VDIC_array[4];
							LeaDPreVDispO									= check_VDIC_array[6];
							fronter											= check_VDIC_array[7];
							document.vicidial_form.vendor_lead_code.value	= check_VDIC_array[8];
							document.vicidial_form.list_id.value			= check_VDIC_array[9];
							document.vicidial_form.gmt_offset_now.value		= check_VDIC_array[10];
							document.vicidial_form.phone_code.value			= check_VDIC_array[11];
							if ( (disable_alter_custphone=='Y') || (disable_alter_custphone=='HIDE') )
								{
								var tmp_pn = document.getElementById("phone_numberDISP");
								if (disable_alter_custphone=='Y')
									{
									tmp_pn.innerHTML						= "<p>"+check_VDIC_array[12]+"</p>";
									redial_number							= check_VDIC_array[12]
									}
								}
							document.vicidial_form.phone_number.value		= check_VDIC_array[12];
							document.vicidial_form.title.value				= check_VDIC_array[13];
							document.vicidial_form.first_name.value			= check_VDIC_array[14];
							document.vicidial_form.middle_initial.value		= check_VDIC_array[15];
							document.vicidial_form.last_name.value			= check_VDIC_array[16];
							document.vicidial_form.address1.value			= check_VDIC_array[17];
							document.vicidial_form.address2.value			= check_VDIC_array[18];
							document.vicidial_form.address3.value			= check_VDIC_array[19];
							document.vicidial_form.city.value				= check_VDIC_array[20];
							document.vicidial_form.state.value				= check_VDIC_array[21];
							document.vicidial_form.province.value			= check_VDIC_array[22];
							document.vicidial_form.postal_code.value		= check_VDIC_array[23];
							document.vicidial_form.country_code.value		= check_VDIC_array[24];
							document.vicidial_form.gender.value				= check_VDIC_array[25];
							document.vicidial_form.date_of_birth.value		= check_VDIC_array[26];
							document.vicidial_form.alt_phone.value			= check_VDIC_array[27];
							document.vicidial_form.email.value				= check_VDIC_array[28];
							document.vicidial_form.security_phrase.value	= check_VDIC_array[29];
							var REGcommentsNL = new RegExp("!N","g");
							check_VDIC_array[30] = check_VDIC_array[30].replace(REGcommentsNL, "\n");
							document.vicidial_form.comments.value			= check_VDIC_array[30];
							document.vicidial_form.called_count.value		= check_VDIC_array[31];
							CBentry_time									= check_VDIC_array[32];
							CBcallback_time									= check_VDIC_array[33];
							CBuser											= check_VDIC_array[34];
							CBcomments										= check_VDIC_array[35];
							dialed_number									= check_VDIC_array[36];
							dialed_label									= check_VDIC_array[37];
							source_id										= check_VDIC_array[38];
							EAphone_code									= check_VDIC_array[39];
							EAphone_number									= check_VDIC_array[40];
							EAalt_phone_notes								= check_VDIC_array[41];
							EAalt_phone_active								= check_VDIC_array[42];
							EAalt_phone_count								= check_VDIC_array[43];
							document.vicidial_form.rank.value				= check_VDIC_array[44];
							document.vicidial_form.owner.value				= check_VDIC_array[45];
							script_recording_delay							= check_VDIC_array[46];
							document.vicidial_form.entry_list_id.value		= check_VDIC_array[47];
							custom_field_names								= check_VDIC_array[48];
							custom_field_values								= check_VDIC_array[49];
							custom_field_types								= check_VDIC_array[50];
							document.vicidial_form.extra1.value				= check_VDIC_array[51];
							document.vicidial_form.extra2.value				= check_VDIC_array[52];
							document.vicidial_form.extra3.value				= check_VDIC_array[53];
							document.vicidial_form.extra4.value				= check_VDIC_array[54];
							document.vicidial_form.extra5.value				= check_VDIC_array[55];
							document.vicidial_form.extra6.value				= check_VDIC_array[56];
							document.vicidial_form.extra7.value				= check_VDIC_array[57];
							document.vicidial_form.extra8.value				= check_VDIC_array[58];
							document.vicidial_form.extra9.value				= check_VDIC_array[59];
							document.vicidial_form.extra10.value				= check_VDIC_array[60];
							document.vicidial_form.extra11.value				= check_VDIC_array[61];
							document.vicidial_form.extra12.value				= check_VDIC_array[62];
							document.vicidial_form.extra13.value				= check_VDIC_array[63];
							document.vicidial_form.extra14.value				= check_VDIC_array[64];
							document.vicidial_form.extra15.value				= check_VDIC_array[65];



							if (hide_gender > 0)
								{
								document.vicidial_form.gender_list.value	= check_VDIC_array[25];
								}
							else
								{
								var gIndex = 0;
								if (document.vicidial_form.gender.value == 'M') {var gIndex = 1;}
								if (document.vicidial_form.gender.value == 'F') {var gIndex = 2;}
								document.getElementById("gender_list").selectedIndex = gIndex;
								}

							lead_dial_number = document.vicidial_form.phone_number.value;
							var dispnum = document.vicidial_form.phone_number.value;
							var status_display_number = phone_number_format(dispnum);
							var callnum = dialed_number;
							var dial_display_number = phone_number_format(callnum);

							document.getElementById("MainStatuSSpan").innerHTML = " Chamada Inbound: " + dial_display_number + "Linha de Entrada: " + VDCL_group_name; 

							if (CBentry_time.length > 2)
								{
                                document.getElementById("CusTInfOSpaN").innerHTML = " <b> PREVIOUS CALLBACK </b>";
								document.getElementById("CusTInfOSpaN").style.background = CusTCB_bgcolor;
								document.getElementById("CBcommentsBoxA").innerHTML = CBentry_time;
								document.getElementById("CBcommentsBoxB").innerHTML = CBcallback_time;
								document.getElementById("CBcommentsBoxC").innerHTML = CBuser;
                                document.getElementById("CBcommentsBoxD").innerHTML = CBcomments;
								showDiv('CBcommentsBox');
								}
							if (dialed_label == 'ALT')
                                {document.getElementById("CusTInfOSpaN").innerHTML = " <b> ALT DIAL NUMBER: ALT </b>";}
							if (dialed_label == 'ADDR3')
                                {document.getElementById("CusTInfOSpaN").innerHTML = " <b> ALT DIAL NUMBER: ADDRESS3 </b>";}
							var REGalt_dial = new RegExp("X","g");
							if (dialed_label.match(REGalt_dial))
								{
                                document.getElementById("CusTInfOSpaN").innerHTML = " <b> ALT DIAL NUMBER: " + dialed_label + "</b>";
								document.getElementById("EAcommentsBoxA").innerHTML = "<b>Phone Code and Number: </b>" + EAphone_code + " " + EAphone_number;

								var EAactive_link = '';
								if (EAalt_phone_active == 'Y') 
									{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','N');\">Change this phone number to INACTIVE</a>";}
								else
									{EAactive_link = "<a href=\"#\" onclick=\"alt_phone_change('" + EAphone_number + "','" + EAalt_phone_count + "','" + document.vicidial_form.lead_id.value + "','Y');\">Change this phone number to ACTIVE</a>";}

                                document.getElementById("EAcommentsBoxB").innerHTML = "<b>Active: </b>" + EAalt_phone_active + "<br />" + EAactive_link;
								document.getElementById("EAcommentsBoxC").innerHTML = "<b>Alt Count: </b>" + EAalt_phone_count;
								document.getElementById("EAcommentsBoxD").innerHTML = "<b>Notes: </b>" + EAalt_phone_notes;
								showDiv('EAcommentsBox');
								}

							if (VDIC_data_VDIG[1].length > 0)
								{
								inOUT = 'IN';
								if (VDIC_data_VDIG[2].length > 2)
									{
									//document.getElementById("MainStatuSSpan").style.background = VDIC_data_VDIG[2];
									}
								var dispnum = document.vicidial_form.phone_number.value;
								var status_display_number = phone_number_format(dispnum);
								var callnum = dialed_number;
								var dial_display_number = phone_number_format(callnum);

								document.getElementById("MainStatuSSpan").innerHTML = "Chamada de Entrada: " + dial_display_number + " Linha de Entrada: " + VDCL_group_name; 
								}

                            document.getElementById("ParkControl").innerHTML ="<td onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\" style='cursor:pointer'><img src='/images/icons/control_pause_blue.png' /></td><td onclick=\"mainxfer_send_redirect('ParK','" + lastcustchannel + "','" + lastcustserverip + "');return false;\" style='cursor:pointer'><a href=\"#\" >Colocar em Espera</a></td>";
							if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
								{
                                document.getElementById("ivrParkControl").innerHTML ="<a href=\"#\" onclick=\"mainxfer_send_redirect('ParKivr','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_LB_ivrparkcall.gif\" border=\"0\" alt=\"IVR Park Call\" /></a>";
								}

                            document.getElementById("HangupControl").innerHTML = "<td onclick='dialedcall_send_hangup();' style='cursor:pointer' width=32px><img src='/images/icons/control_eject_blue.png' /></td><td onclick='dialedcall_send_hangup();' style='cursor:pointer'><a href='#'>Desligar Chamada</a></td>";

                            document.getElementById("XferControl").innerHTML = "<td onclick=\"ShoWTransferMain('ON');\" style='cursor:pointer'><img src='/images/icons/control_repeat_blue.png' /></td><td onclick=\"ShoWTransferMain('ON');\" style='cursor:pointer'><a href=\"#\">Transferir Chamada</a></td>";

                            document.getElementById("LocalCloser").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_localcloser.gif\" border=\"0\" alt=\"LOCAL CLOSER\" style=\"vertical-align:middle\" /></a>";

                            document.getElementById("DialBlindTransfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_blindtransfer.gif\" border=\"0\" alt=\"Dial Blind Transfer\" style=\"vertical-align:middle\" /></a>";

                            document.getElementById("DialBlindVMail").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRVMAIL','" + lastcustchannel + "','" + lastcustserverip + "');return false;\"><img src=\"./images/vdc_XB_ammessage.gif\" border=\"0\" alt=\"Blind Transfer VMail Message\" style=\"vertical-align:middle\" /></a>";
		
							if ( (quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP') )
								{
								if (quick_transfer_button_locked > 0)
									{quick_transfer_button_orig = default_xfer_group;}

                                document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;\"><img src=\"./images/vdc_LB_quickxfer.gif\" border=\"0\" alt=\"QUICK TRANSFER\" /></a>";
								}
							if (prepopulate_transfer_preset_enabled > 0)
								{
								if ( (prepopulate_transfer_preset == 'PRESET_1') || (prepopulate_transfer_preset == 'LOCKED_PRESET_1') )
									{document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
								if ( (prepopulate_transfer_preset == 'PRESET_2') || (prepopulate_transfer_preset == 'LOCKED_PRESET_2') )
									{document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
								if ( (prepopulate_transfer_preset == 'PRESET_3') || (prepopulate_transfer_preset == 'LOCKED_PRESET_3') )
									{document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
								if ( (prepopulate_transfer_preset == 'PRESET_4') || (prepopulate_transfer_preset == 'LOCKED_PRESET_4') )
									{document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
								if ( (prepopulate_transfer_preset == 'PRESET_5') || (prepopulate_transfer_preset == 'LOCKED_PRESET_5') )
									{document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
								}
							if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5') )
								{
								if ( (quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_1') )
									{document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;   document.vicidial_form.xfername.value='D1';}
								if ( (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_2') )
									{document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;   document.vicidial_form.xfername.value='D2';}
								if ( (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_3') )
									{document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;   document.vicidial_form.xfername.value='D3';}
								if ( (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_4') )
									{document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;   document.vicidial_form.xfername.value='D4';}
								if ( (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_5') )
									{document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;   document.vicidial_form.xfername.value='D5';}
								if (quick_transfer_button_locked > 0)
									{quick_transfer_button_orig = document.vicidial_form.xfernumber.value;}

                                document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;\"><img src=\"./images/vdc_LB_quickxfer.gif\" border=\"0\" alt=\"QUICK TRANSFER\" /></a>";
								}

							if (call_requeue_button > 0)
								{
								var CloserSelectChoices = document.vicidial_form.CloserSelectList.value;
								var regCRB = new RegExp("AGENTDIRECT","ig");
								if ( (CloserSelectChoices.match(regCRB)) || (VU_closer_campaigns.match(regCRB)) )
									{
                                    document.getElementById("ReQueueCall").innerHTML =  "<a href=\"#\" onclick=\"call_requeue_launch();return false;\"><img src=\"./images/vdc_LB_requeue_call.gif\" border=\"0\" alt=\"Re-Queue Call\" /></a>";
									}
								else
									{
                                    document.getElementById("ReQueueCall").innerHTML =  "<img src=\"./images/vdc_LB_requeue_call_OFF.gif\" border=\"0\" alt=\"Re-Queue Call\" />";
									}
								}

							// Build transfer pull-down list
							var loop_ct = 0;
							var live_XfeR_HTML = '';
							var XfeR_SelecT = '';
							while (loop_ct < XFgroupCOUNT)
								{
								if (VARxfergroups[loop_ct] == LIVE_default_xfer_group)
									{XfeR_SelecT = 'selected ';}
								else {XfeR_SelecT = '';}
								live_XfeR_HTML = live_XfeR_HTML + "<option " + XfeR_SelecT + "value=\"" + VARxfergroups[loop_ct] + "\">" + VARxfergroups[loop_ct] + " - " + VARxfergroupsnames[loop_ct] + "</option>\n";
								loop_ct++;
								}
                            document.getElementById("XfeRGrouPLisT").innerHTML = "<select size=\"1\" name=\"XfeRGrouP\" class=\"cust_form\" id=\"XfeRGrouP\" onChange=\"XferAgentSelectLink();return false;\">" + live_XfeR_HTML + "</select>";

							if (lastcustserverip == server_ip)
								{
                                document.getElementById("VolumeUpSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('UP','" + lastcustchannel + "','');return false;\"><img src=\"./images/vdc_volume_up.gif\" border=\"0\" /></a>";
                                document.getElementById("VolumeDownSpan").innerHTML = "<a href=\"#\" onclick=\"volume_control('DOWN','" + lastcustchannel + "','');return false;\"><img src=\"./images/vdc_volume_down.gif\" border=\"0\" /></a>";
								}

							if (dial_method == "INBOUND_MAN")
								{
                                document.getElementById("DiaLControl").innerHTML = "<img src=\"./images/vdc_LB_pause_OFF.gif\" border=\"0\" alt=\" Pause \" /><img src=\"./images/vdc_LB_resume_OFF.gif\" border=\"0\" alt=\"Resume\" /><br /><img src=\"./images/vdc_LB_dialnextnumber_OFF.gif\" border=\"0\" alt=\"Dial Next Number\" />";
								}
							else
								{
									document.getElementById("ResumeControl").innerHTML = ResumeControl_auto_OFF_HTML;
								document.getElementById("PauseControl").innerHTML = PauseControl_auto_OFF_HTML;
								}

							if (VDCL_group_id.length > 1)
								{var group = VDCL_group_id;}
							else
								{var group = campaign;}
							if ( (dialed_label.length < 2) || (dialed_label=='NONE') ) {dialed_label='MAIN';}

							if (hide_gender < 1)
								{
								var genderIndex = document.getElementById("gender_list").selectedIndex;
								var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
								document.vicidial_form.gender.value = genderValue;
								}

							LeaDDispO='';

							var regWFAcustom = new RegExp("^VAR","ig");
							if (VDIC_web_form_address.match(regWFAcustom))
								{
								TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
								TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
								}
							else
								{
								TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
								}

							if (VDIC_web_form_address_two.match(regWFAcustom))
								{
								TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
								TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
								}
							else
								{
								TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
								}


                            document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\"><img src=\"./images/vdc_LB_webform.gif\" border=\"0\" alt=\"Web Form\" /></a>\n";

							if (enable_second_webform > 0)
								{
                                document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\"><img src=\"./images/vdc_LB_webform_two.gif\" border=\"0\" alt=\"Web Form 2\" /></a>\n";
								}

							if ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') )
								{all_record = 'YES';}

							if ( (view_scripts == 1) && (CalL_ScripT_id.length > 0) )
								{
								var SCRIPT_web_form = 'http://127.0.0.1/testing.php';
								var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form,'YES','DEFAULT','1');

								if ( (script_recording_delay > 0) && ( (LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE') ) )
									{
									delayed_script_load = 'YES';
									RefresHScript('CLEAR');
									}
								else
									{
									load_script_contents();
									}
								}
							if (limesurvey_enabled === 1) { FormContentsLoad(); }
							if (custom_fields_enabled > 0)
								{
								FormContentsLoad();
								}
							if (CalL_AutO_LauncH == 'SCRIPT')
								{
								if (delayed_script_load == 'YES')
									{
									load_script_contents();
									}
								ScriptPanelToFront();
								}
							if (CalL_AutO_LauncH == 'FORM')
								{
								FormPanelToFront();
								}

							if (CalL_AutO_LauncH == 'WEBFORM')
								{
								window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
								}
							if (CalL_AutO_LauncH == 'WEBFORMTWO')
								{
								window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
								}

							if (useIE > 0)
								{
								var regCTC = new RegExp("^NONE","ig");
								if (CopY_tO_ClipboarD.match(regCTC))
									{var nothing=1;}
								else
									{
									var tmp_clip = document.getElementById(CopY_tO_ClipboarD);
									window.clipboardData.setData('Text', tmp_clip.value)
									}
								}

							if (alert_enabled=='ON')
								{
								var callnum = dialed_number;
								var dial_display_number = phone_number_format(callnum);
								alert("Chamada Entrada: " + dial_display_number + "\n Linha de Entrada: " + VDCL_group_name);
								}
							}
						
						
					},"json");
				}
			
		


// ################################################################################
// refresh or clear the SCRIPT frame contents
	function RefresHScript(temp_wipe)
		{
		if (temp_wipe == 'CLEAR')
			{
			document.getElementById("ScriptContents").innerHTML = '';
			}
		else
			{
			document.getElementById("ScriptContents").innerHTML = '';
			WebFormRefresH('','','1');
			load_script_contents();
			}
		}


// ################################################################################
// refresh the content of the web form URL
	function WebFormRefresH(taskrefresh,submittask,force_webvars_refresh) 
		{
		var webvars_refresh=0;

		if (VDCL_group_id.length > 1)
			{var group = VDCL_group_id;}
		else
			{var group = campaign;}
		if ( (dialed_label.length < 2) || (dialed_label=='NONE') ) {dialed_label='MAIN';}

		if (submittask != 'YES')
			{
			if (hide_gender < 1)
				{
				var genderIndex = document.getElementById("gender_list").selectedIndex;
				var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
				document.vicidial_form.gender.value = genderValue;
				}
			}

		var regWFAcustom = new RegExp("^VAR","ig");
		if (VDIC_web_form_address.match(regWFAcustom))
			{
			TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','CUSTOM');
			TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
			}
		else
			{webvars_refresh=1;}

		if ( (webvars_refresh > 0) || (force_webvars_refresh > 0) )
			{
			TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address,'YES','DEFAULT','1');
			}

		if (taskrefresh == 'OUT')
			{
            document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH('IN');\"><img src=\"./images/vdc_LB_webform.gif\" border=\"0\" alt=\"Web Form\" /></a>\n";
			}
		else 
			{
            document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOut=\"WebFormRefresH('OUT');\"><img src=\"./images/vdc_LB_webform.gif\" border=\"0\" alt=\"Web Form\" /></a>\n";
			}
		}


// ################################################################################
// refresh the content of the second web form URL
	function WebFormTwoRefresH(taskrefresh,submittask) 
		{
		if (VDCL_group_id.length > 1)
			{var group = VDCL_group_id;}
		else
			{var group = campaign;}
		if ( (dialed_label.length < 2) || (dialed_label=='NONE') ) {dialed_label='MAIN';}

		if (submittask != 'YES')
			{
			if (hide_gender < 1)
				{
				var genderIndex = document.getElementById("gender_list").selectedIndex;
				var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
				document.vicidial_form.gender.value = genderValue;
				}
			}

		var regWFAcustom = new RegExp("^VAR","ig");
		if (VDIC_web_form_address_two.match(regWFAcustom))
			{
			TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','CUSTOM');
			TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
			}
		else
			{
			TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two,'YES','DEFAULT','2');
			}

		if (enable_second_webform > 0)
			{
			if (taskrefresh == 'OUT')
				{
                document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH('IN');\"><img src=\"./images/vdc_LB_webform_two.gif\" border=\"0\" alt=\"Web Form 2\" /></a>\n";
				}
			else 
				{
                document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOut=\"WebFormTwoRefresH('OUT');\"><img src=\"./images/vdc_LB_webform_two.gif\" border=\"0\" alt=\"Web Form 2\" /></a>\n";
				}
			}
		}


// ################################################################################
// Send hangup a second time from the dispo screen 
	function DispoHanguPAgaiN() 
	{
	form_cust_channel = AgaiNHanguPChanneL;
	document.getElementById("callchannel").innerHTML = AgaiNHanguPChanneL;
	document.vicidial_form.callserverip.value = AgaiNHanguPServeR;
	lastcustchannel = AgaiNHanguPChanneL;
	lastcustserverip = AgaiNHanguPServeR;
	VD_live_call_secondS = AgainCalLSecondS;
	CalLCID = AgaiNCalLCID;

	document.getElementById("DispoSelectHAspan").innerHTML = "";

	dialedcall_send_hangup();
	}


// ################################################################################
// Send leave 3way call a second time from the dispo screen 
	function DispoLeavE3wayAgaiN() 
	{
	XDchannel = DispO3wayXtrAchannel;
	document.vicidial_form.xfernumber.value = DispO3wayCalLxfernumber;
	MDchannel = DispO3waychannel;
	lastcustserverip = DispO3wayCalLserverip;

	document.getElementById("DispoSelectHAspan").innerHTML = "";

	leave_3way_call('SECOND');

	DispO3waychannel = '';
	DispO3wayXtrAchannel = '';
	DispO3wayCalLserverip = '';
	DispO3wayCalLxfernumber = '';
	DispO3wayCalLcamptail = '';
	}


// ################################################################################
// Start Hangup Functions for both 
	function bothcall_send_hangup() 
		{
		if (lastcustchannel.length > 3)
			{dialedcall_send_hangup();}
		if (lastxferchannel.length > 3)
			{xfercall_send_hangup();}
		}

// ################################################################################
// Send Hangup command for customer call connected to the conference now to Manager WORKING
	function dialedcall_send_hangup(dispowindow,hotkeysused,altdispo,nodeletevdac) 
		{
		
                if (customerparked == 1) {
                    alert_box('Existe um cliente em espera.\nNão pode desligar');
                    return;
                }   
               
		dead_time = 0;		
		if (VDCL_group_id.length > 1)
			{var group = VDCL_group_id;}
		else
			{var group = campaign;}
		var form_cust_channel = document.getElementById("callchannel").innerHTML;
                console.log("form_cust_channel:" + form_cust_channel);
		var form_cust_serverip = document.vicidial_form.callserverip.value;
		var customer_channel = lastcustchannel;
		console.log("customer_channel:" + customer_channel);
                var customer_server_ip = lastcustserverip;
		AgaiNHanguPChanneL = lastcustchannel;
		AgaiNHanguPServeR = lastcustserverip;
		AgainCalLSecondS = VD_live_call_secondS;
		AgaiNCalLCID = CalLCID;
		var process_post_hangup=0;
		if ( (RedirecTxFEr < 1) && ( (MD_channel_look==1) || (auto_dial_level == 0) ) )
			{ console.log("1");
			MD_channel_look=0;
			DialTimeHangup('MAIN');
			}
		if (form_cust_channel.length > 3)
			{ console.log("2");
			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined')
				{ console.log("3");
				xmlhttp = new XMLHttpRequest();
				}
			if (xmlhttp) 
				{  console.log("4");
				var queryCID = "HLvdcW" + epoch_sec + user_abb;
				var hangupvalue = customer_channel;
				//		alert(auto_dial_level + "|" + CalLCID + "|" + customer_server_ip + "|" + hangupvalue + "|" + VD_live_call_secondS);
				custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Hangup&format=text&user=" + user + "&pass=" + pass + "&channel=" + hangupvalue + "&call_server_ip=" + customer_server_ip + "&queryCID=" + queryCID + "&auto_dial_level=" + auto_dial_level + "&CalLCID=" + CalLCID + "&secondS=" + VD_live_call_secondS + "&exten=" + session_id + "&campaign=" + group + "&stage=CALLHANGUP&nodeletevdac=" + nodeletevdac + "&log_campaign=" + campaign;
				console.log("custhangup_query:" + custhangup_query);
                                xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(custhangup_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;

						}
					}
				process_post_hangup=1;
				delete xmlhttp;
				}
			}
			else {process_post_hangup=1;}
			if (process_post_hangup==1)
			{
			VD_live_customer_call = 0;
			VD_live_call_secondS = 0;
			MD_ring_secondS = 0;
			CalLCID = '';
			MDnextCID = '';

		//	UPDATE VICIDIAL_LOG ENTRY FOR THIS CALL PROCESS
			DialLog("end",nodeletevdac);
			conf_dialed=0;
			if (dispowindow == 'NO')
				{
				open_dispo_screen=0;
				}
			else
				{
				if (auto_dial_level == 0)			
					{
					if (document.vicidial_form.DiaLAltPhonE.checked==true)
						{
						reselect_alt_dial = 1;
						open_dispo_screen=0;
						}
					else
						{
						reselect_alt_dial = 0;
						open_dispo_screen=1;
						}
					}
				else
					{
					if (document.vicidial_form.DiaLAltPhonE.checked==true)
						{
						reselect_alt_dial = 1;
						open_dispo_screen=0;
						auto_dial_level=0;
						manual_dial_in_progress=1;
						auto_dial_alt_dial=1;
						}
					else
						{
						reselect_alt_dial = 0;
						open_dispo_screen=1;
						}
					}
				}

		//  HANGUP RECORDINGS - BUG FIX
			hangup_recordings(session_id);

		//  DEACTIVATE CHANNEL-DEPENDANT BUTTONS AND VARIABLES
		
			document.getElementById("callchannel").innerHTML = '';
			document.vicidial_form.callserverip.value = '';
			lastcustchannel='';
			lastcustserverip='';
			MDchannel='';
			if (post_phone_time_diff_alert_message.length > 10)
				{
				document.getElementById("post_phone_time_diff_span_contents").innerHTML = "";
				hideDiv('post_phone_time_diff_span');
				post_phone_time_diff_alert_message='';
				}

			if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
            document.getElementById("WebFormSpan").innerHTML = "<img src=\"./images/vdc_LB_webform_OFF.gif\" border=\"0\" alt=\"Web Form\" />";
			if (enable_second_webform > 0)
				{
                document.getElementById("WebFormSpanTwo").innerHTML = "<img src=\"./images/vdc_LB_webform_two_OFF.gif\" border=\"0\" alt=\"Web Form 2\" />";
				}
            document.getElementById("ParkControl").innerHTML = "<td><img src='/images/icons/control_pause.png' /></td><td>Colocar em Espera</td>";
			if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
				{
                document.getElementById("ivrParkControl").innerHTML = "<img src=\"./images/vdc_LB_ivrparkcall_OFF.gif\" border=\"0\" alt=\"IVR Park Call\" />";
				}
				
            document.getElementById("HangupControl").innerHTML = "<td width=32px><img src='/images/icons/control_eject.png' /></td><td>Desligar Chamada</td>";
            document.getElementById("XferControl").innerHTML = "<td><img src='/images/icons/control_repeat.png' /></td><td>Transferir Chamada</td>";
            document.getElementById("LocalCloser").innerHTML = "<img src=\"./images/vdc_XB_localcloser_OFF.gif\" border=\"0\" alt=\"LOCAL CLOSER\" style=\"vertical-align:middle\" />";
            document.getElementById("DialBlindTransfer").innerHTML = "<img src=\"./images/vdc_XB_blindtransfer_OFF.gif\" border=\"0\" alt=\"Dial Blind Transfer\" style=\"vertical-align:middle\" />";
            document.getElementById("DialBlindVMail").innerHTML = "<img src=\"./images/vdc_XB_ammessage_OFF.gif\" border=\"0\" alt=\"Blind Transfer VMail Message\" style=\"vertical-align:middle\" />";
            document.getElementById("VolumeUpSpan").innerHTML = "<img src=\"./images/vdc_volume_up_off.gif\" border=\"0\" />";
            document.getElementById("VolumeDownSpan").innerHTML = "<img src=\"./images/vdc_volume_down_off.gif\" border=\"0\" />";

			if (quick_transfer_button_enabled > 0)
                {document.getElementById("QuickXfer").innerHTML = "<img src=\"./images/vdc_LB_quickxfer_OFF.gif\" border=\"0\" alt=\"QUICK TRANSFER\" />";}

			if (call_requeue_button > 0)
				{
                document.getElementById("ReQueueCall").innerHTML =  "<img src=\"./images/vdc_LB_requeue_call_OFF.gif\" border=\"0\" alt=\"Re-Queue Call\" />";
				}

			document.getElementById("custdatetime").innerHTML = '';

			if ( (auto_dial_level == 0) && (dial_method != 'INBOUND_MAN') )
				{
				if (document.vicidial_form.DiaLAltPhonE.checked==true)
					{
					reselect_alt_dial = 1;
					if (altdispo == 'ALTPH2')
						{
						ManualDialOnly('ALTPhonE');
						}
					else
						{
						if (altdispo == 'ADDR3')
							{
							ManualDialOnly('AddresS3');
							}
						else
							{
							if (hotkeysused == 'YES')
								{
								reselect_alt_dial = 0;
								manual_auto_hotkey = 1;
								}
							}
						}
					}
				else
					{
					if (hotkeysused == 'YES')
						{
						manual_auto_hotkey = 1;
						}
					else
						{
						document.getElementById("DiaLControl").innerHTML = DiaLControl_manual_HTML_OFF;
						
               				}
					reselect_alt_dial = 0;
					}
				}
			else
				{
				if (document.vicidial_form.DiaLAltPhonE.checked==true)
					{
					reselect_alt_dial = 1;
					if (altdispo == 'ALTPH2')
						{
						ManualDialOnly('ALTPhonE');
						}
					else
						{
						if (altdispo == 'ADDR3')
							{
							ManualDialOnly('AddresS3');
							}
						else
							{
							if (hotkeysused == 'YES')
								{
								manual_auto_hotkey = 1;
								alt_dial_active=0;

								//document.getElementById("MainStatuSSpan").style.background = panel_bgcolor;
								document.getElementById("MainStatuSSpan").innerHTML = '';
								if (dial_method == "INBOUND_MAN")
									{
                                    document.getElementById("DiaLControl").innerHTML = "<img src=\"./images/vdc_LB_pause_OFF.gif\" border=\"0\" alt=\" Pause \" /><img src=\"./images/vdc_LB_resume_OFF.gif\" border=\"0\" alt=\"Resume\" /><br /><img src=\"./images/vdc_LB_dialnextnumber_OFF.gif\" border=\"0\" alt=\"Dial Next Number\" />";
									}
								else
									{
									document.getElementById("ResumeControl").innerHTML = ResumeControl_auto_OFF_HTML;
									document.getElementById("PauseControl").innerHTML = PauseControl_auto_OFF_HTML;
									}
								reselect_alt_dial = 0;
								}
							}
						}
					}
				else
					{
					//document.getElementById("MainStatuSSpan").style.background = panel_bgcolor;
					if (dial_method == "INBOUND_MAN")
						{
                        document.getElementById("DiaLControl").innerHTML = "<img src=\"./images/vdc_LB_pause_OFF.gif\" border=\"0\" alt=\" Pause \" /><img src=\"./images/vdc_LB_resume_OFF.gif\" border=\"0\" alt=\"Resume\" /><br /><img src=\"./images/vdc_LB_dialnextnumber_OFF.gif\" border=\"0\" alt=\"Dial Next Number\" />";
						}
					else
						{
						document.getElementById("ResumeControl").innerHTML = ResumeControl_auto_OFF_HTML;
						document.getElementById("PauseControl").innerHTML = PauseControl_auto_OFF_HTML;
						}
					reselect_alt_dial = 0;
					}
				}

			ShoWTransferMain('OFF');

			}

		}


// ################################################################################
// Send Hangup command for 3rd party call connected to the conference now to Manager
	function xfercall_send_hangup() 
		{
		var xferchannel = document.vicidial_form.xferchannel.value;
                console.log("xferchannel = " + xferchannel + "valor?");
		var xfer_channel = lastxferchannel;
                
                
		var process_post_hangup=0;
		xfer_in_call=0;
		if ( (MD_channel_look==1) && (leaving_threeway < 1) )
			{
			MD_channel_look=0;
			DialTimeHangup('XFER');
			}
		if (xferchannel.length > 3)
			{ console.log("entrou 2");
			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined')
				{
				xmlhttp = new XMLHttpRequest();
				}
			if (xmlhttp) 
				{ 
				var queryCID = "HXvdcW" + epoch_sec + user_abb;
				var hangupvalue = xfer_channel;
				custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Hangup&format=text&user=" + user + "&pass=" + pass + "&channel=" + hangupvalue + "&queryCID=" + queryCID + "&log_campaign=" + campaign;
				console.log(custhangup_query);
                                xmlhttp.open('POST', 'manager_send.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(custhangup_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
						Nactiveext = null;
						Nactiveext = xmlhttp.responseText;
				//		alert(xmlhttp.responseText);
						}
					}
				process_post_hangup=1;
				delete xmlhttp;
				}
			}
		else {process_post_hangup=1;}
		if (process_post_hangup==1)
			{
			XD_live_customer_call = 0;
			XD_live_call_secondS = 0;
			MD_ring_secondS = 0;
			MD_channel_look=0;
			XDnextCID = '';
			XDcheck = '';
			xferchannellive=0;

		//  DEACTIVATE CHANNEL-DEPENDANT BUTTONS AND VARIABLES
			document.vicidial_form.xferchannel.value = "";
			lastxferchannel='';

        		document.getElementById("Leave3WayCall").style.display="none";

            document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\"><img src=\"./images/vdc_XB_dialwithcustomer.gif\" border=\"0\" alt=\"Dial With Customer\" style=\"vertical-align:middle\" /></a>";

            document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\"><img src=\"/images/icons/telephone_add_32.png\" alt=\"Park Customer Dial\" style=\"vertical-align:middle\" />Solicitar Transferência</a>";
			document.getElementById("ParkCustomerDial").style.display="block";
			
            document.getElementById("HangupXferLine").innerHTML ="<img src=\"/images/icons/telephone_delete_32.png\"  alt=\"Hangup Xfer Line\" style=\"vertical-align:middle\" />Desligar o solicitado";
			document.getElementById("HangupXferLine").style.display="none";
			
            document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><img src=\"./images/vdc_XB_hangupbothlines.gif\" border=\"0\" alt=\"Hangup Both Lines\" style=\"vertical-align:middle\" /></a>";
			}
		}

// ################################################################################
// Send Hangup command for any Local call that is not in the quiet(7) entry - used to stop manual dials even if no connect
	function DialTimeHangup(tasktypecall,DestPhoneTransfer) 
        {
            if(tasktypecall==='HangupTransfer') {
                MD_channel_look=0;
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{ console.log("1.3");
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ console.log("1.4");
			var queryCID = "HTvdcW" + epoch_sec + user_abb;
			custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=HangupConfDial&format=text&user=" + user + "&pass=" + pass + "&exten=" + DestPhoneTransfer + "&ext_context=" + ext_context + "&queryCID=" + queryCID + "&log_campaign=" + campaign;
			console.log("1.5" + custhangup_query);
                        xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(custhangup_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					}
				}
			delete xmlhttp;
			}
                        
			XD_live_customer_call = 0;
			XD_live_call_secondS = 0;
			MD_ring_secondS = 0;
			MD_channel_look=0;
			XDnextCID = '';
			XDcheck = '';
			xferchannellive=0;

		//  DEACTIVATE CHANNEL-DEPENDANT BUTTONS AND VARIABLES
			document.vicidial_form.xferchannel.value = "";
			lastxferchannel='';

        		document.getElementById("Leave3WayCall").style.display="none";

            document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\"><img src=\"./images/vdc_XB_dialwithcustomer.gif\" border=\"0\" alt=\"Dial With Customer\" style=\"vertical-align:middle\" /></a>";

            document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\"><img src=\"/images/icons/telephone_add_32.png\" alt=\"Park Customer Dial\" style=\"vertical-align:middle\" />Solicitar Transferência</a>";
			document.getElementById("ParkCustomerDial").style.display="block";
			
            document.getElementById("HangupXferLine").innerHTML ="<img src=\"/images/icons/telephone_delete_32.png\"  alt=\"Hangup Xfer Line\" style=\"vertical-align:middle\" />Desligar o solicitado";
			document.getElementById("HangupXferLine").style.display="none";
			
            document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><img src=\"./images/vdc_XB_hangupbothlines.gif\" border=\"0\" alt=\"Hangup Both Lines\" style=\"vertical-align:middle\" /></a>";
		
            } else {
            
                if ( (RedirecTxFEr < 1) && (leaving_threeway < 1) )
                	{ console.log("1.2");
		MD_channel_look=0;
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{ console.log("1.3");
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ console.log("1.4");
			var queryCID = "HTvdcW" + epoch_sec + user_abb;
			custhangup_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=HangupConfDial&format=text&user=" + user + "&pass=" + pass + "&exten=" + session_id + "&ext_context=" + ext_context + "&queryCID=" + queryCID + "&log_campaign=" + campaign;
			console.log("1.5" + custhangup_query);
                        xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(custhangup_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					Nactiveext = null;
					Nactiveext = xmlhttp.responseText;
					}
				}
			delete xmlhttp;
			}
			}
		}
        }


// ################################################################################
// Update vicidial_list lead record with all altered values from form
	function CustomerData_update()
		{

		var REGcommentsAMP = new RegExp('&',"g");
		var REGcommentsQUES = new RegExp("\\?","g");
		var REGcommentsPOUND = new RegExp("\\#","g");
		var REGcommentsRESULT = document.vicidial_form.comments.value.replace(REGcommentsAMP, "--AMP--");
		REGcommentsRESULT = REGcommentsRESULT.replace(REGcommentsQUES, "--QUES--");
		REGcommentsRESULT = REGcommentsRESULT.replace(REGcommentsPOUND, "--POUND--");

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			if (hide_gender < 1)
				{
				var genderIndex = document.getElementById("gender_list").selectedIndex;
				var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
				document.vicidial_form.gender.value = genderValue;
				}

			VLupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&campaign=" + campaign +  "&ACTION=updateLEAD&format=text&user=" + user + "&pass=" + pass + 
			"&lead_id=" + encodeURIComponent(document.vicidial_form.lead_id.value) + 
			"&vendor_lead_code=" + encodeURIComponent(document.vicidial_form.vendor_lead_code.value) + 
			"&phone_number=" + encodeURIComponent(document.vicidial_form.phone_number.value) + 
			"&title=" + encodeURIComponent(document.vicidial_form.title.value) + 
			"&first_name=" + encodeURIComponent(document.vicidial_form.first_name.value) + 
			"&middle_initial=" + encodeURIComponent(document.vicidial_form.middle_initial.value) + 
			"&last_name=" + encodeURIComponent(document.vicidial_form.last_name.value) + 
			"&address1=" + encodeURIComponent(document.vicidial_form.address1.value) + 
			"&address2=" + encodeURIComponent(document.vicidial_form.address2.value) + 
			"&address3=" + encodeURIComponent(document.vicidial_form.address3.value) + 
			"&city=" + encodeURIComponent(document.vicidial_form.city.value) + 
			"&state=" + encodeURIComponent(document.vicidial_form.state.value) + 
			"&province=" + encodeURIComponent(document.vicidial_form.province.value) + 
			"&postal_code=" + encodeURIComponent(document.vicidial_form.postal_code.value) + 
			"&country_code=" + encodeURIComponent(document.vicidial_form.country_code.value) + 
			"&gender=" + encodeURIComponent(document.vicidial_form.gender.value) + 
			"&date_of_birth=" + encodeURIComponent(document.vicidial_form.date_of_birth.value) + 
			"&alt_phone=" + encodeURIComponent(document.vicidial_form.alt_phone.value) + 
			"&email=" + encodeURIComponent(document.vicidial_form.email.value) + 
			"&security_phrase=" + encodeURIComponent(document.vicidial_form.security_phrase.value) + 
			"&comments=" + encodeURIComponent(REGcommentsRESULT) + 
			"&extra1=" + encodeURIComponent(document.vicidial_form.extra1.value) +
			"&extra2=" + encodeURIComponent(document.vicidial_form.extra2.value) +
			"&extra3=" + encodeURIComponent(document.vicidial_form.extra3.value) +
			"&extra4=" + encodeURIComponent(document.vicidial_form.extra4.value) +
			"&extra5=" + encodeURIComponent(document.vicidial_form.extra5.value) +
			"&extra6=" + encodeURIComponent(document.vicidial_form.extra6.value) +
			"&extra7=" + encodeURIComponent(document.vicidial_form.extra7.value) +
			"&extra8=" + encodeURIComponent(document.vicidial_form.extra8.value) +
			"&extra9=" + encodeURIComponent(document.vicidial_form.extra9.value) +
			"&extra10=" + encodeURIComponent(document.vicidial_form.extra10.value) +
			"&extra11=" + encodeURIComponent(document.vicidial_form.extra11.value) +
			"&extra12=" + encodeURIComponent(document.vicidial_form.extra12.value) +
			"&extra13=" + encodeURIComponent(document.vicidial_form.extra13.value) +
			"&extra14=" + encodeURIComponent(document.vicidial_form.extra14.value) +
			"&extra15=" + encodeURIComponent(document.vicidial_form.extra15.value);
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VLupdate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					if (xmlhttp.responseText != '' ) { alert_box(xmlhttp.responseText); }	
					
					}
				}
			delete xmlhttp;
			}

		}

// ################################################################################
// Generate the Call Disposition Chooser panel
	function DispoSelectContent_create(taskDSgrp,taskDSstage)
		{
                        if (customer_3way_hangup_dispo_message.length > 1)
                        {
                            document.getElementById("Dispo3wayMessage").innerHTML = "<br /><b><font color=\"red\" size=\"3\">" + customer_3way_hangup_dispo_message + "</font></b><br />";
                        }
                        if (APIManualDialQueue > 0)
                        {
                            document.getElementById("DispoManualQueueMessage").innerHTML = "<br /><b><font color=\"red\" size=\"3\">Manual Dial Queue Calls Waiting: " + APIManualDialQueue + "</font></b><br />";
                        }
                        if (per_call_notes == 'ENABLED')
                        {
                            var test_notes = document.vicidial_form.call_notes_dispo.value;
                            if (test_notes.length > 0)
                            {
                                document.vicidial_form.call_notes.value = document.vicidial_form.call_notes_dispo.value;
                            }
                            document.getElementById("PerCallNotesContent").innerHTML = "<br /><b><font size=\"3\">Call Notes: </font></b><br /><textarea name=\"call_notes_dispo\" id=\"call_notes_dispo\" rows=\"2\" cols=\"100\" class=\"cust_form_text\" value=\"\">" + document.vicidial_form.call_notes.value + "</textarea>";
                        }
                        else
                        {
                            document.getElementById("PerCallNotesContent").innerHTML = "<input type=\"hidden\" name=\"call_notes_dispo\" id=\"call_notes_dispo\" value=\"\" />";
                        }
                        FecharCallbacks();
                        HidEGenDerPulldown();
                        AgentDispoing = 1;
                        var CBflag = '';
                        var dispo_HTML = "";
                        $.each(campaign_status, function() {
                            CBflag = (this.callback) ? '<i class="icon-book"></i>' : "";
                            if (taskDSgrp == this.status) {
                                dispo_HTML = dispo_HTML + "<a href=\"#\" onclick=\"DispoSelect_submit();return false;\" class=\"btn btn-success\">" + CBflag + this.name + "</a>";
                            } else {
                                dispo_HTML = dispo_HTML + "<a href=\"#\" onclick=\"DispoSelectContent_create('" + this.status + "','ADD');return false;\" class=\"btn\">" + CBflag + this.name + "</a>";
                            }
                        });


                        if (taskDSstage == 'ReSET') {
                            document.vicidial_form.DispoSelection.value = '';
                        }
                        else {
                            document.vicidial_form.DispoSelection.value = taskDSgrp;
                        }

                        document.getElementById("DispoSelectContent").innerHTML = dispo_HTML;
                        if (focus_blur_enabled == 1)
                        {
                            document.inert_form.inert_button.focus();
                            document.inert_form.inert_button.blur();
                        }
                        if (my_callback_option == 'CHECKED')
                        {
                            document.vicidial_form.tipo_callback.checked = true;
                        }
                    }
                //Dispo Search
                $(document).on("input","#dispo_search",	function(){
                var that =this;
		$("#DispoSelectContent a").each(
			function(){
				if($(this).text().toLowerCase().match($(that).val().toLowerCase()))
                                {
                                    $(this).show();
                                }else
                                {
                                    $(this).hide();
                                }
                            });
                        });
// ################################################################################
// Generate the Pause Code Chooser panel
	function PauseCodeSelectContent_create()
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1','');
				}
			else
				{
				move_on=0;
				alert_box("Tem que estar pausado para inserir o motivo de pausa");
				}
			}
		if (move_on == 1)
			{
			if (APIManualDialQueue > 0)
				{
				PauseCodeSelect_submit('NXDIAL');
				}
			else
				{
				HidEGenDerPulldown();
				showDiv('PauseCodeSelectBox');
				WaitingForNextStep=1;
				PauseCode_HTML = '';
				document.vicidial_form.PauseCodeSelection.value = '';		
				//var VD_pause_codes_ct_half = parseInt(VD_pause_codes_ct / 2);
				var loop_ct = 0;
				while (loop_ct < VD_pause_codes_ct)
					{
                    PauseCode_HTML = PauseCode_HTML + "<a href=\"#\" onclick=\"PauseCodeSelect_submit('" + VARpause_codes[loop_ct] + "');return false;\" class=btn >" + VARpause_code_names[loop_ct] + "</a>";
					loop_ct++;
					/*if (loop_ct == VD_pause_codes_ct_half) 
                        {PauseCode_HTML = PauseCode_HTML + "</span></td><td height=\"300px\" width=\"240px\" valign=\"top\"><span id=PauseCodeSelectB>";}*/
					}

				if (agent_pause_codes_active=='FORCE')
					{var Go_BacK_LinK = '';}
				else
                    {var Go_BacK_LinK = "<b><a href=\"#\" onclick=\"PauseCodeSelect_submit('');return false;\">Go Back</a>";}

                PauseCode_HTML = PauseCode_HTML + Go_BacK_LinK;
				document.getElementById("PauseCodeSelectContent").innerHTML = PauseCode_HTML;
				}
			}
		if (focus_blur_enabled==1)
			{
			document.inert_form.inert_button.focus();
			document.inert_form.inert_button.blur();
			}
		}

// ################################################################################
// Open lead search form panel
	function OpeNSearcHForMDisplaYBox()
		{
                    var go_on = divchecker("search");
                    if (!go_on) { return; }
                        
                    
                    if ( AgentDispoing > 0) {
                        alert_box('Termine Wrap-up da chamada.')
                        return;
                    }
                if  ( AutoDialReady == 0 && auto_dial_level > 0 && pause_code_counter == 0) {
                    alert_box('Seleccione o motivo de pausa por favor.');
                    return;
                }
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("Tem que estar em pause para procurar um contacto");
				}
			}
		if (move_on == 1)
			{
				
			HidEGenDerPulldown();
			showDiv('SearcHForMDisplaYBox');
			WaitingForNextStep=1;
			}
		}
// ################################################################################
// Submit chosen Preset
	function PresetSelect_submit(taskpresetname,taskpresetnumber,taskpresetdtmf,taskhidenumber)
		{
		hideDiv('PresetsSelectBox');
		document.vicidial_form.conf_dtmf.value = taskpresetdtmf;
		document.vicidial_form.xfername.value = taskpresetname;
		if ( (taskhidenumber=='Y') && (hide_xfer_number_to_dial=='DISABLED') )
			{
			document.vicidial_form.xfernumhidden.value = taskpresetnumber;
			document.vicidial_form.xfernumber.value='';
			}
		else
			{
			document.vicidial_form.xfernumhidden.value = '';
			document.vicidial_form.xfernumber.value = taskpresetnumber;
			}
		scroll(0,0);
		}


// ################################################################################
// Generate the Group Alias Chooser panel
	function GroupAliasSelectContent_create(task3way)
		{
		HidEGenDerPulldown();
		showDiv('GroupAliasSelectBox');
		WaitingForNextStep=1;
		GroupAlias_HTML = '';
		document.vicidial_form.GroupAliasSelection.value = '';		
		var VD_group_aliases_ct_half = parseInt(VD_group_aliases_ct / 2);
        GroupAlias_HTML = "<table cellpadding=\"5\" cellspacing=\"5\" width=\"500px\"><tr><td colspan=\"2\"><b> GROUP ALIAS</b></td></tr><tr><td  height=\"300px\" width=\"240px\" valign=\"top\"><span id=\"GroupAliasSelectA\">";
		if (task3way > 0)
			{
			VD_group_aliases_ct_half = (VD_group_aliases_ct_half - 1);
            GroupAlias_HTML = GroupAlias_HTML + "<font size=\"2\" style=\"BACKGROUND-COLOR: #FFFFCC\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('CAMPAIGN','" + campaign_cid + "','0');return false;\">CAMPAIGN - " + campaign_cid + "</a></b></font><br /><br />";
            GroupAlias_HTML = GroupAlias_HTML + "<font size=\"2\" style=\"BACKGROUND-COLOR: #FFFFCC\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('CUSTOMER','" + document.vicidial_form.phone_number.value + "','0');return false;\">CUSTOMER - " + document.vicidial_form.phone_number.value + "</a></b></font><br /><br />";
            GroupAlias_HTML = GroupAlias_HTML + "<font size=\"2\" style=\"BACKGROUND-COLOR: #FFFFCC\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('AGENT_PHONE','" + outbound_cid + "','0');return false;\">AGENT_PHONE - " + outbound_cid + "</a></b></font><br /><br />";
			}
		var loop_ct = 0;
		while (loop_ct < VD_group_aliases_ct)
			{
            GroupAlias_HTML = GroupAlias_HTML + "<font size=\"2\" style=\"BACKGROUND-COLOR: #FFFFCC\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('" + VARgroup_alias_ids[loop_ct] + "','" + VARcaller_id_numbers[loop_ct] + "','1');return false;\">" + VARgroup_alias_ids[loop_ct] + " - " + VARgroup_alias_names[loop_ct] + " - " + VARcaller_id_numbers[loop_ct] + "</a></b></font><br /><br />";
			loop_ct++;
			if (loop_ct == VD_group_aliases_ct_half) 
                {GroupAlias_HTML = GroupAlias_HTML + "</span></td><td  height=\"300px\" width=\"240px\" valign=\"top\"><span id=GroupAliasSelectB>";}
			}

        var Go_BacK_LinK = "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFCC\"><b><a href=\"#\" onclick=\"GroupAliasSelect_submit('');return false;\">Go Back</a>";

        GroupAlias_HTML = GroupAlias_HTML + "</span></td></tr></table><br /><br />" + Go_BacK_LinK;
		document.getElementById("GroupAliasSelectContent").innerHTML = GroupAlias_HTML;
		if (focus_blur_enabled==1)
			{
			document.inert_form.inert_button.focus();
			document.inert_form.inert_button.blur();
			}
		}

// ################################################################################
// open web form, then submit disposition
	function WeBForMDispoSelect_submit()
		{
		leaving_threeway=0;
		blind_transfer=0;
		document.getElementById("callchannel").innerHTML = '';
		document.vicidial_form.callserverip.value = '';
		document.vicidial_form.xferchannel.value = '';
        document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\"><img src=\"./images/vdc_XB_dialwithcustomer.gif\" border=\"0\" alt=\"Dial With Customer\" style=\"vertical-align:middle\" /></a>";
        document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\"><img src=\"/images/icons/telephone_add_32.png\" alt=\"Park Customer Dial\" style=\"vertical-align:middle\" />Solicitar Transferência</a>";
        document.getElementById("ParkCustomerDial").style.display="block";
        
        document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><img src=\"./images/vdc_XB_hangupbothlines.gif\" border=\"0\" alt=\"Hangup Both Lines\" style=\"vertical-align:middle\" /></a>";

		var DispoChoice = document.vicidial_form.DispoSelection.value;

		if (DispoChoice.length < 1) {alert_box("Tem que seleccionar um resultado!!");}
		else
			{
			document.getElementById("CusTInfOSpaN").innerHTML = "";
			document.getElementById("CusTInfOSpaN").style.background = panel_bgcolor;

			LeaDDispO = DispoChoice;
	
			WebFormRefresH('NO','YES');

            document.getElementById("WebFormSpan").innerHTML = "<img src=\"./images/vdc_LB_webform_OFF.gif\" border=\"0\" alt=\"Web Form\" />";
			if (enable_second_webform > 0)
				{
                document.getElementById("WebFormSpanTwo").innerHTML = "<img src=\"./images/vdc_LB_webform_two_OFF.gif\" border=\"0\" alt=\"Web Form 2\" />";
				}
			window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');

			DispoSelect_submit();
			}
		}

		

// ################################################################################
// Update vicidial_list lead record with disposition selection
	function DispoSelect_submit()
		{
		
		var SelectedFeedback = document.vicidial_form.DispoSelection.value;
		
                if (SelectedFeedback === "MARC") {


                    if ($("#vcFormIFrame").contents().find(".radiocheck-required:checked").length) {

                        if ($("#vcFormIFrame").contents().find(".radiocheck-required:checked").val() === "CATOS") {
                            if ($("#vcFormIFrame").contents().find(".multiple-requiredconsultorio option:selected").val() === "nenhum" || typeof $("#vcFormIFrame").contents().find(".multiple-requiredconsultorio option:selected").html() === "undefined") {
                                alert_box("Campo do CATO com erros de preenchimento ou vazio.");
                                return false;
                            }
                        }
                        if ($("#vcFormIFrame").contents().find(".radiocheck-required:checked").val() === "Branch") {
                            if ($("#vcFormIFrame").contents().find(".multiple-requiredconsultoriodois option:selected").val() === "nenhum" || typeof $("#vcFormIFrame").contents().find(".multiple-requiredconsultoriodois option:selected").html() === "undefined") {
                                alert_box("Campo do Consultório com erros de preenchimento ou vazio.");
                                return false;
                            }
                        }

                    } else {
                        alert_box("Campo do tipo de consulta com erros de preenchimento.");
                        return false;
                    }

                    if ($("#vcFormIFrame").contents().find(".hour-required").val().length === "---") {
                        alert_box("Campo da Hora da Marcação com erros de preenchimento ou vazio.");
                        return false;
                    }
                    if ($("#vcFormIFrame").contents().find(".minute-required").val().length === "---") {
                        alert_box("Campo dos Minutos da Marcação com erros de preenchimento ou vazio.");
                        return false;
                    }
                    if ($("#vcFormIFrame").contents().find(".date-required").val().length <= 0) {
                        alert_box("Campo da Data da Marcação com erros de preenchimento ou vazio.");
                        return false;
                    }


                }
		
		
		
		CustomerData_update();
		vcFormIFrame.document.form_custom_fields.submit();
		
		if (VDCL_group_id.length > 1)
			{var group = VDCL_group_id;}
		else
			{var group = campaign;}
		leaving_threeway=0;
		blind_transfer=0;
		CheckDEADcallON=0;
		customer_3way_hangup_counter=0;
		customer_3way_hangup_counter_trigger=0;
		document.getElementById("callchannel").innerHTML = '';
		document.vicidial_form.callserverip.value = '';
		document.vicidial_form.xferchannel.value = '';
        document.getElementById("DialWithCustomer").innerHTML ="<a href=\"#\" onclick=\"SendManualDial('YES');return false;\"><img src=\"./images/vdc_XB_dialwithcustomer.gif\" border=\"0\" alt=\"Dial With Customer\" style=\"vertical-align:middle\" /></a>";
        document.getElementById("ParkCustomerDial").innerHTML ="<a href=\"#\" onclick=\"xfer_park_dial();return false;\"><img src=\"/images/icons/telephone_add_32.png\" alt=\"Park Customer Dial\" style=\"vertical-align:middle\" />Solicitar Transferência</a>";
        document.getElementById("ParkCustomerDial").style.display="block";
        
        document.getElementById("HangupBothLines").innerHTML ="<a href=\"#\" onclick=\"bothcall_send_hangup();return false;\"><img src=\"./images/vdc_XB_hangupbothlines.gif\" border=\"0\" alt=\"Hangup Both Lines\" style=\"vertical-align:middle\" /></a>";
 
		var DispoChoice = document.vicidial_form.DispoSelection.value;

		if (DispoChoice.length < 1) {alert_box("Tem que escolher um resultado!!");}
		else
			{
			document.getElementById("CusTInfOSpaN").innerHTML = "";
			document.getElementById("CusTInfOSpaN").style.background = panel_bgcolor;
                        var isCB=(campaign_status[DispoChoice] === undefined)?false:campaign_status[DispoChoice].callback;
			if ( isCB && (scheduled_callbacks > 0)) {
                            showDiv('CallBackSelectBox');
                        }
			else
				{
                                    
                                inOUT_hack=inOUT;
                                lead_id_hack=$("#lead_id").val();
		DSupdate_query = {server_ip:server_ip,session_name:session_name,ACTION:"updateDISPO",format:"text",user:user,pass:pass,dispo_choice:DispoChoice,lead_id:document.vicidial_form.lead_id.value,campaign:campaign,auto_dial_level:auto_dial_level,agent_log_id:agent_log_id,CallBackDatETimE:CallBackDatETimE,list_id:document.vicidial_form.list_id.value,recipient:CallBackrecipient,use_internal_dnc:use_internal_dnc,use_campaign_dnc:use_campaign_dnc,MDnextCID:LasTCID,stage:group,vtiger_callback_id:vtiger_callback_id,phone_number:document.vicidial_form.phone_number.value,phone_code:document.vicidial_form.phone_code.value,dial_method:dial_method,uniqueid:document.vicidial_form.uniqueid.value,CallBackLeadStatus:CallBackLeadStatus,comments:CallBackCommenTs,custom_field_names:custom_field_names,call_notes:document.vicidial_form.call_notes_dispo.value,cb_to_other_user:cb_to_other_user,cb_to_other_username:cb_to_other_username};
					$.post('vdc_db_query.php',DSupdate_query,function(data){
                                          if(nc_live && nc_live_id!==undefined){
                                              nc_log();
                                              nc_live=false;
                                              nc_live_id=undefined;}
                                            
                                                        
							var check_dispo = null;
							check_dispo = data;
							var check_DS_array=check_dispo.split("\n");
							if (check_DS_array[1] == 'Next agent_log_id:')
								{
								agent_log_id = check_DS_array[2];
								}
                                        });		
                                              
						
				// CLEAR ALL FORM VARIABLES
				document.getElementById("DiaLControl").innerHTML = "<td><img src='/images/icons/control_end_blue.png' /></td><td><a href='#' onclick=\"ManualDialNext('','','','','','0');\">Marcar Seguinte</a></td>";
				
				document.vicidial_form.lead_id.value		='';
				document.vicidial_form.vendor_lead_code.value='';
				document.vicidial_form.list_id.value		='';
				document.vicidial_form.entry_list_id.value	='';
				document.vicidial_form.gmt_offset_now.value	='';
				document.vicidial_form.phone_code.value		='';
				if ( (disable_alter_custphone=='Y') || (disable_alter_custphone=='HIDE') )
					{
					var tmp_pn = document.getElementById("phone_numberDISP");
					tmp_pn.innerHTML			= '';
					}
				document.vicidial_form.phone_number.value	='';
				document.vicidial_form.title.value			='';
				document.vicidial_form.first_name.value		='';
				document.vicidial_form.middle_initial.value	='';
				document.vicidial_form.last_name.value		='';
				document.vicidial_form.address1.value		='';
				document.vicidial_form.address2.value		='';
				document.vicidial_form.address3.value		='';
				document.vicidial_form.city.value			='';
				document.vicidial_form.state.value			='';
				document.vicidial_form.province.value		='';
				document.vicidial_form.postal_code.value	='';
				document.vicidial_form.country_code.value	='';
				document.vicidial_form.gender.value			='';
				document.vicidial_form.date_of_birth.value	='';
				document.vicidial_form.alt_phone.value		='';
				document.vicidial_form.email.value			='';
				document.vicidial_form.security_phrase.value='';
				document.vicidial_form.comments.value		='';
				document.vicidial_form.called_count.value	='';
				document.vicidial_form.call_notes.value		='';
				document.vicidial_form.call_notes_dispo.value ='';
				document.vicidial_form.owner.value ='';
				document.vicidial_form.extra1.value ='';
				document.vicidial_form.extra2.value ='';
				document.vicidial_form.extra3.value ='';
				document.vicidial_form.extra4.value ='';
				document.vicidial_form.extra5.value ='';
				document.vicidial_form.extra6.value ='';
				document.vicidial_form.extra7.value ='';
				document.vicidial_form.extra8.value ='';
				document.vicidial_form.extra9.value ='';
				document.vicidial_form.extra10.value ='';
				document.vicidial_form.extra11.value ='';
				document.vicidial_form.extra12.value ='';
				document.vicidial_form.extra13.value ='';
				document.vicidial_form.extra14.value ='';
				document.vicidial_form.extra15.value ='';
				VDCL_group_id = '';
				fronter = '';
				inOUT = 'OUT';
				vtiger_callback_id='0';
				recording_filename='';
				recording_id='';
				document.vicidial_form.uniqueid.value='';
				MDuniqueid='';
				XDuniqueid='';
				tmp_vicidial_id='';
				EAphone_code='';
				EAphone_number='';
				EAalt_phone_notes='';
				EAalt_phone_active='';
				EAalt_phone_count='';
				XDnextCID='';
				XDcheck = '';
				MDnextCID='';
				XD_live_customer_call = 0;
				XD_live_call_secondS = 0;
				xfer_in_call=0;
				MD_channel_look=0;
				MD_ring_secondS=0;
				uniqueid_status_display='';
				uniqueid_status_prefix='';
				custom_call_id='';
				API_selected_xfergroup='';
				API_selected_callmenu='';
				timer_action='';
				timer_action_seconds='';
				timer_action_mesage='';
				timer_action_destination='';
				did_pattern='';
				did_id='';
				did_extension='';
				did_description='';
				closecallid='';
				xfercallid='';
				custom_field_names='';
				custom_field_values='';
				custom_field_types='';
				customerparked=0;
				customerparkedcounter=0;
				document.getElementById("ParkCounterSpan").innerHTML = '';
				document.vicidial_form.xfername.value='';
				document.vicidial_form.xfernumhidden.value='';
				document.getElementById("debugbottomspan").innerHTML = '';
				customer_3way_hangup_dispo_message='';
				document.getElementById("Dispo3wayMessage").innerHTML = '';
				document.getElementById("DispoManualQueueMessage").innerHTML = '';
				document.getElementById("ManualQueueNotice").innerHTML = '';
				APIManualDialQueue_last=0;
				document.vicidial_form.FORM_LOADED.value = '0';
				CallBackLeadStatus = '';
				document.vicidial_form.MDPhonENumbeR.value = '';
				document.vicidial_form.MDDiaLOverridE.value = '';
				document.vicidial_form.MDLeadID.value = '';
				document.vicidial_form.MDType.value = '';

				if (post_phone_time_diff_alert_message.length > 10)
					{
					document.getElementById("post_phone_time_diff_span_contents").innerHTML = "";
					hideDiv('post_phone_time_diff_span');
					post_phone_time_diff_alert_message='';
					}

				if (manual_dial_in_progress==1)
					{
					manual_dial_finished();
					}
				if (hide_gender < 1)
					{
					document.getElementById("GENDERhideFORieALT").innerHTML = '';
					document.getElementById("GENDERhideFORie").innerHTML = '<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - Undefined</option><option value="M">M - Male</option><option value="F">F - Female</option></select>';
					}
				hideDiv('DispoSelectBox');
				hideDiv('DispoButtonHideA');
				hideDiv('DispoButtonHideB');
				hideDiv('DispoButtonHideC');
				//document.getElementById("DispoSelectBox").style.top = '80px';  // Firefox error on this line for some reason
				//document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMinimize()\"> minimize </a>";
				//document.getElementById("DispoSelectHAspan").innerHTML = "<a href=\"#\" onclick=\"DispoHanguPAgaiN()\">Hangup Again</a>";

				CBcommentsBoxhide();
				EAcommentsBoxhide();

				AgentDispoing = 0;

				if (shift_logout_flag < 1)
					{
					if (wrapup_waiting == 0)
						{	
						if (document.vicidial_form.DispoSelectStop.checked==true)
							{
							if (auto_dial_level != '0')
								{
								AutoDialWaiting = 0;
								AutoDial_ReSume_PauSe("VDADpause");
								}
								
							VICIDiaL_pause_calling = 1;
							if (dispo_check_all_pause != '1')
								{
								document.vicidial_form.DispoSelectStop.checked=false;
								}
							}
						else
							{ 
							if (auto_dial_level != '0')
								{
								AutoDialWaiting = 1;
								agent_log_id = AutoDial_ReSume_PauSe("VDADready","NEW_ID");
								}
							else
								{ 
								// trigger HotKeys manual dial automatically go to next lead
								
								if (manual_auto_hotkey == '1')
									{
									manual_auto_hotkey = 0;
									ManualDialNext('','','','','','0');
									}
								}
							}
						}
					}
				else
					{
					LogouT('SHIFT');
					}
				if (focus_blur_enabled==1)
					{
					document.inert_form.inert_button.focus();
					document.inert_form.inert_button.blur();
					}
				}
			// scroll back to the top of the page
			scroll(0,0);
			}
		}
function FecharCallbacks()
{
hideDiv('CallBackSelectBox');
}

// ################################################################################
// Submit the Pause Code 
	function PauseCodeSelect_submit(newpausecode)
		{
		hideDiv('PauseCodeSelectBox');
		ShoWGenDerPulldown();

		WaitingForNextStep=0;

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			VMCpausecode_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=PauseCodeSubmit&format=text&status=" + newpausecode + "&agent_log_id=" + agent_log_id + "&campaign=" + campaign + "&extension=" + extension + "&protocol=" + protocol + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&stage=" + pause_code_counter + "&campaign_cid=" + LastCallCID + "&auto_dial_level=" + starting_dial_level;
			pause_code_counter++;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCpausecode_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					var check_pause_code = null;
					var check_pause_code = xmlhttp.responseText;
					var check_PC_array=check_pause_code.split("\n");
					if (check_PC_array[1] == 'Next agent_log_id:')
						{
                                                    agent_log_id = check_PC_array[2];
                                                    				
                                    document.getElementById("ResumeControl").innerHTML = ResumeControl_auto_ON_HTML;
                                                }
					}
				}
			delete xmlhttp;
			}
		LastCallCID='';
		scroll(0,0);
		}


// ################################################################################
// Submit the Group Alias 
	function GroupAliasSelect_submit(newgroupalias,newgroupcid,newusegroup)
		{
		hideDiv('GroupAliasSelectBox');
		ShoWGenDerPulldown();
		WaitingForNextStep=0;
		
		if (newusegroup > 0)
			{
			active_group_alias = newgroupalias;
            document.getElementById("ManuaLDiaLGrouPSelecteD").innerHTML = "<font size=\"2\" face=\"Arial,Helvetica\">Group Alias: " + active_group_alias + "</font>";
            document.getElementById("XfeRDiaLGrouPSelecteD").innerHTML = "<font size=\"1\" face=\"Arial,Helvetica\">Group Alias: " + active_group_alias + "</font>";
			}
		cid_choice = newgroupcid;
		scroll(0,0);
		}


// ################################################################################
// Populate the dtmf and xfer number for each preset link in xfer-conf frame
	function DtMf_PreSet_a()
		{
		document.vicidial_form.conf_dtmf.value = CalL_XC_a_Dtmf;
		document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;
		document.vicidial_form.xfername.value = 'D1';
		}
	function DtMf_PreSet_b()
		{
		document.vicidial_form.conf_dtmf.value = CalL_XC_b_Dtmf;
		document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;
		document.vicidial_form.xfername.value = 'D2';
		}
	function DtMf_PreSet_c()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;
		document.vicidial_form.xfername.value = 'D3';
		}
	function DtMf_PreSet_d()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;
		document.vicidial_form.xfername.value = 'D4';
		}
	function DtMf_PreSet_e()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;
		document.vicidial_form.xfername.value = 'D5';
		}

	function DtMf_PreSet_a_DiaL()
		{
		document.vicidial_form.conf_dtmf.value = CalL_XC_a_Dtmf;
		document.vicidial_form.xfernumber.value = CalL_XC_a_NuMber;
		basic_originate_call(CalL_XC_a_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function DtMf_PreSet_b_DiaL()
		{
		document.vicidial_form.conf_dtmf.value = CalL_XC_b_Dtmf;
		document.vicidial_form.xfernumber.value = CalL_XC_b_NuMber;
		basic_originate_call(CalL_XC_b_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function DtMf_PreSet_c_DiaL()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_c_NuMber;
		basic_originate_call(CalL_XC_c_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function DtMf_PreSet_d_DiaL()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_d_NuMber;
		basic_originate_call(CalL_XC_d_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function DtMf_PreSet_e_DiaL()
		{
		document.vicidial_form.xfernumber.value = CalL_XC_e_NuMber;
		basic_originate_call(CalL_XC_e_NuMber,'NO','YES',session_id,'YES','','1','0');
		}
	function hangup_timer_xfer()
		{
		hideDiv('CustomerGoneBox');
		WaitingForNextStep=0;
		custchannellive=0;

		dialedcall_send_hangup();
		}
	function extension_timer_xfer()
		{
		document.vicidial_form.xfernumber.value = timer_action_destination;
		mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
		}
	function callmenu_timer_xfer()
		{
		API_selected_callmenu = timer_action_destination;
		document.vicidial_form.xfernumber.value = timer_action_destination;
		mainxfer_send_redirect('XfeRBLIND',lastcustchannel,lastcustserverip);
		}
	function ingroup_timer_xfer()
		{
		API_selected_xfergroup = timer_action_destination;
		document.vicidial_form.xfernumber.value = timer_action_destination;
		mainxfer_send_redirect('XfeRLOCAL',lastcustchannel,lastcustserverip);
		}

// ################################################################################
// Show message that customer has hungup the call before agent has
	function CustomerChanneLGone()
		{
		showDiv('CustomerGoneBox');
        
        $("#test_custchannellive").html(custchannellive);
        $("#test_lastcustchannel").html(lastcustchannel);
        $("#test_no_empty_session_warnings").html(no_empty_session_warnings);
        

		document.getElementById("callchannel").innerHTML = '';
		document.vicidial_form.callserverip.value = '';
		document.getElementById("CustomerGoneChanneL").innerHTML = lastcustchannel;
		if( document.images ) { document.images['livecall'].src = image_livecall_OFF.src;}
		WaitingForNextStep=1;
		}
	function CustomerGoneOK()
		{
		hideDiv('CustomerGoneBox');
		WaitingForNextStep=0;
		custchannellive=0;
		}
	function CustomerGoneHangup()
		{
		hideDiv('CustomerGoneBox');
		WaitingForNextStep=0;

		custchannellive=0;

		dialedcall_send_hangup();
		}
// ################################################################################
// Show message that there are no voice channels in the VICIDIAL session
	function NoneInSession()
		{
		showDiv('NoneInSessionBox');
		document.getElementById("NoneInSessionID").innerHTML = session_id;
		WaitingForNextStep=1;
		}
	function NoneInSessionOK()
		{
		hideDiv('NoneInSessionBox');
		WaitingForNextStep=0;
		nochannelinsession=0;
		}
	function NoneInSessionCalL()
		{
		hideDiv('NoneInSessionBox');
		WaitingForNextStep=0;
		nochannelinsession=0;

		if ( (protocol == 'EXTERNAL') || (protocol == 'Local') )
			{
			var protodial = 'Local';
			var extendial = extension;
	//		var extendial = extension + "@" + ext_context;
			}
		else
			{
			var protodial = protocol;
			var extendial = extension;
			}
		var originatevalue = protodial + "/" + extendial;
		var queryCID = "ACagcW" + epoch_sec + user_abb;

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			VMCoriginate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass  + "&ACTION=OriginateVDRelogin&format=text&channel=" + originatevalue + "&queryCID=" + queryCID + "&exten=" + session_id + "&ext_context=" + ext_context + "&ext_priority=1" + "&extension=" + extension + "&protocol=" + protocol + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&allow_sipsak_messages=" + allow_sipsak_messages + "&campaign=" + campaign + "&outbound_cid=" + campaign_cid;
			xmlhttp.open('POST', 'manager_send.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(VMCoriginate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
			//		alert(xmlhttp.responseText);
					}
				}
			delete xmlhttp;
			}
		if (auto_dial_level > 0)
			{
			AutoDial_ReSume_PauSe("VDADpause");
			}
		}


// ################################################################################
// Generate the Closer In Group Chooser panel
	function CloserSelectContent_create()
		{
		HidEGenDerPulldown();
		if ( (VU_agent_choose_ingroups == '1') && (manager_ingroups_set < 1) )
			{
            var live_CSC_HTML = "<table class=\"table table-mod\">\n\
                                    <thead>\n\
                                    <tr>\n\
                                    <th>\n\
                                    <label class=\"label label-important\">Não Seleccionados</label>\n\
                                    </th>\n\
                                    <th>\n\
                                    <label class=\"label label-success\">Seleccionados</label>\n\
                                    </th>\n\
                                    </tr>\n\
                                    </thead>\n\
                                    <tbody>\n\
                                    <tr>\n\
                                    <td valign=\"top\">\n\
                                    <span id=CloserSelectAdd><a href=\"#\" class=\"btn\" onclick=\"CloserSelect_change('-----ADD-ALL-----','ADD');return false;\">Todos <i class=\"icon-circle-arrow-right\"></i></a><br />";
			var loop_ct = 0;
			while (loop_ct < INgroupCOUNT)
				{
                live_CSC_HTML = live_CSC_HTML + "<a href=\"#\" onclick=\"CloserSelect_change('" + VARingroups[loop_ct] + "','ADD');return false;\">" + VARingroups[loop_ct] + " <i class=\"icon-circle-arrow-right\"></i></a><br />";
				loop_ct++;
				}
            live_CSC_HTML = live_CSC_HTML + "</span>\n\
                                            </td>\n\
                                            <td valign=\"top\">\n\
                                            <span id=CloserSelectDelete></span>\n\
                                            </td>\n\
                                            </tr>\n\
                                            </tbody>\n\
                                            </table>";

			document.vicidial_form.CloserSelectList.value = '';
			document.getElementById("CloserSelectContent").innerHTML = live_CSC_HTML;
			}
		else
			{
			VU_agent_choose_ingroups_DV = "MGRLOCK";
            var live_CSC_HTML = "Manager has selected groups for you<br />";
			document.vicidial_form.CloserSelectList.value = '';
			document.getElementById("CloserSelectContent").innerHTML = live_CSC_HTML;
			}
		if (focus_blur_enabled==1)
			{
			document.inert_form.inert_button.focus();
			document.inert_form.inert_button.blur();
			}
		}

// ################################################################################
// Move a Closer In Group record to the selected column or reverse
	function CloserSelect_change(taskCSgrp,taskCSchange)
		{
		var CloserSelectListValue = document.vicidial_form.CloserSelectList.value;
		var CSCchange = 0;
		var regCS = new RegExp(" " + taskCSgrp + " ","ig");
		var regCSall = new RegExp("-ALL-----","ig");
		var regCSallADD = new RegExp("-----ADD-ALL-----","ig");
		var regCSallDELETE = new RegExp("-----DELETE-ALL-----","ig");
		if ( (CloserSelectListValue.match(regCS)) && (CloserSelectListValue.length > 3) )
			{
			if (taskCSchange == 'DELETE') {CSCchange = 1;}
			}
		else
			{
			if (taskCSchange == 'ADD') {CSCchange = 1;}
			}
		if (taskCSgrp.match(regCSall))
			{CSCchange = 1;}

	
		if (CSCchange==1) 
			{
			var loop_ct = 0;
			var CSCcolumn = '';
			var live_CSC_HTML_ADD = '';
			var live_CSC_HTML_DELETE = '';
			var live_CSC_LIST_value = " ";
			while (loop_ct < INgroupCOUNT)
				{
				var regCSL = new RegExp(" " + VARingroups[loop_ct] + " ","ig");
				if (CloserSelectListValue.match(regCSL)) {CSCcolumn = 'DELETE';}
				else {CSCcolumn = 'ADD';}
				if ( ( (VARingroups[loop_ct] == taskCSgrp) && (taskCSchange == 'DELETE') ) || (taskCSgrp.match(regCSallDELETE)) ) {CSCcolumn = 'ADD';}
				if ( ( (VARingroups[loop_ct] == taskCSgrp) && (taskCSchange == 'ADD') ) || (taskCSgrp.match(regCSallADD)) ) {CSCcolumn = 'DELETE';}
					

				if (CSCcolumn == 'DELETE')
					{
                    live_CSC_HTML_DELETE = live_CSC_HTML_DELETE + "<a href=\"#\" onclick=\"CloserSelect_change('" + VARingroups[loop_ct] + "','DELETE');return false;\"><i class=\"icon-circle-arrow-left\"></i> " + VARingroups[loop_ct] + "</a><br />";
					live_CSC_LIST_value = live_CSC_LIST_value + VARingroups[loop_ct] + " ";
					}
				else
					{
                    live_CSC_HTML_ADD = live_CSC_HTML_ADD + "<a href=\"#\" onclick=\"CloserSelect_change('" + VARingroups[loop_ct] + "','ADD');return false;\">" + VARingroups[loop_ct] + " <i class=\"icon-circle-arrow-right\"></i></a><br />";
					}
				loop_ct++;
				}

			document.vicidial_form.CloserSelectList.value = live_CSC_LIST_value;
            document.getElementById("CloserSelectAdd").innerHTML = "  <a href=\"#\" class=\"btn\" onclick=\"CloserSelect_change('-----ADD-ALL-----','ADD');return false;\">Todos <i class=\"icon-circle-arrow-right\"></i></a><br />" + live_CSC_HTML_ADD;
            document.getElementById("CloserSelectDelete").innerHTML = "  <a href=\"#\" class=\"btn\" onclick=\"CloserSelect_change('-----DELETE-ALL-----','DELETE');return false;\"><i class=\"icon-circle-arrow-left\"></i>Todos</a><br />" + live_CSC_HTML_DELETE;
			}
		}

// ################################################################################
// Update vicidial_live_agents record with closer in group choices
	function CloserSelect_submit()
		{
		if (dial_method == "INBOUND_MAN")
			{document.vicidial_form.CloserSelectBlended.checked=false;}
		if (document.vicidial_form.CloserSelectBlended.checked==true)
			{VICIDiaL_closer_blended = 1;}
		else
			{VICIDiaL_closer_blended = 0;}

		var CloserSelectChoices = document.vicidial_form.CloserSelectList.value;

		if (call_requeue_button > 0)
			{
            document.getElementById("ReQueueCall").innerHTML =  "<img src=\"./images/vdc_LB_requeue_call_OFF.gif\" border=\"0\" alt=\"Re-Queue Call\" />";
			}
		else
			{
			document.getElementById("ReQueueCall").innerHTML =  "";
			}

		if (VU_agent_choose_ingroups_DV == "MGRLOCK")
			{CloserSelectChoices = "MGRLOCK";}
CSCupdate_query = {server_ip:server_ip,session_name:session_name,ACTION:"regCLOSER",format:"text",user:user,pass:pass,comments:VU_agent_choose_ingroups_DV,closer_blended:VICIDiaL_closer_blended,campaign:campaign,qm_phone:qm_phone,dial_method:dial_method,closer_choice:CloserSelectChoices+"-"};
			
$.post('vdc_db_query.php',CSCupdate_query);
		

		hideDiv('CloserSelectBox');
		MainPanelToFront();
		CloserSelecting = 0;
		scroll(0,0);
		}


// ################################################################################
// Generate the Territory Chooser panel
	function TerritorySelectContent_create()
		{
		if (agent_select_territories == '1')
			{
			HidEGenDerPulldown();
			if (agent_choose_territories > 0)
				{
                var live_TERR_HTML = "<table cellpadding=\"5\" cellspacing=\"5\" width=\"500px\"><tr><td><b>TERRITORIES NOT SELECTED</b></td><td><b>SELECTED TERRITORIES</b></td></tr><tr><td  height=\"300px\" width=\"240px\" valign=\"top\"><span id=TerritorySelectAdd>  <a href=\"#\" onclick=\"TerritorySelect_change('-----ADD-ALL-----','ADD');return false;\"><b>--- ADD ALL ---</b><br />";
				var loop_ct = 0;
				while (loop_ct < territoryCOUNT)
					{
                    live_TERR_HTML = live_TERR_HTML + "<a href=\"#\" onclick=\"TerritorySelect_change('" + VARterritories[loop_ct] + "','ADD');return false;\">" + VARterritories[loop_ct] + "<br />";
					loop_ct++;
					}
                live_TERR_HTML = live_TERR_HTML + "</span></td><td  height=\"300px\" width=\"240px\" valign=\"top\"><span id=TerritorySelectDelete></span></td></tr></table>";

				document.vicidial_form.TerritorySelectList.value = '';
				document.getElementById("TerritorySelectContent").innerHTML = live_TERR_HTML;
				}
			else
				{
				agent_select_territories = "MGRLOCK";
                var live_TERR_HTML = "Manager has selected territories for you<br />";
				document.vicidial_form.TerritorySelectList.value = '';
				document.getElementById("TerritorySelectContent").innerHTML = live_TERR_HTML;
				}
			}
		if (focus_blur_enabled==1)
			{
			document.inert_form.inert_button.focus();
			document.inert_form.inert_button.blur();
			}
		}

// ################################################################################
// Move a Territory record to the selected column or reverse
	function TerritorySelect_change(taskTERRgrp,taskTERRchange)
		{
		var TerritorySelectListValue = document.vicidial_form.TerritorySelectList.value;
		var TERRchange = 0;
		var regTERR = new RegExp(" " + taskTERRgrp + " ","ig");
		var regTERRall = new RegExp("-ALL-----","ig");
		var regTERRallADD = new RegExp("-----ADD-ALL-----","ig");
		var regTERRallDELETE = new RegExp("-----DELETE-ALL-----","ig");
		if ( (TerritorySelectListValue.match(regTERR)) && (TerritorySelectListValue.length > 3) )
			{
			if (taskTERRchange == 'DELETE') {TERRchange = 1;}
			}
		else
			{
			if (taskTERRchange == 'ADD') {TERRchange = 1;}
			}
		if (taskTERRgrp.match(regTERRall))
			{TERRchange = 1;}
		if (TERRchange==1) 
			{
			var loop_ct = 0;
			var TERRcolumn = '';
			var live_TERR_HTML_ADD = '';
			var live_TERR_HTML_DELETE = '';
			var live_TERR_LIST_value = " ";
			while (loop_ct < territoryCOUNT)
				{
				var regTERRL = new RegExp(" " + VARterritories[loop_ct] + " ","ig");
				if (TerritorySelectListValue.match(regTERRL)) {TERRcolumn = 'DELETE';}
				else {TERRcolumn = 'ADD';}
				if ( ( (VARterritories[loop_ct] == taskTERRgrp) && (taskTERRchange == 'DELETE') ) || (taskTERRgrp.match(regTERRallDELETE)) ) 
					{TERRcolumn = 'ADD';}
				if ( ( (VARterritories[loop_ct] == taskTERRgrp) && (taskTERRchange == 'ADD') ) || (taskTERRgrp.match(regTERRallADD)) ) 
					{TERRcolumn = 'DELETE';}

				if (TERRcolumn == 'DELETE')
					{
                    live_TERR_HTML_DELETE = live_TERR_HTML_DELETE + "<a href=\"#\" onclick=\"TerritorySelect_change('" + VARterritories[loop_ct] + "','DELETE');return false;\">" + VARterritories[loop_ct] + "<br />";
					live_TERR_LIST_value = live_TERR_LIST_value + VARterritories[loop_ct] + " ";
					}
				else
					{
                    live_TERR_HTML_ADD = live_TERR_HTML_ADD + "<a href=\"#\" onclick=\"TerritorySelect_change('" + VARterritories[loop_ct] + "','ADD');return false;\">" + VARterritories[loop_ct] + "<br />";
					}
				loop_ct++;
				}

			document.vicidial_form.TerritorySelectList.value = live_TERR_LIST_value;
            document.getElementById("TerritorySelectAdd").innerHTML = "  <a href=\"#\" onclick=\"TerritorySelect_change('-----ADD-ALL-----','ADD');return false;\"><b>--- ADD ALL ---</b><br />" + live_TERR_HTML_ADD;
            document.getElementById("TerritorySelectDelete").innerHTML = "  <a href=\"#\" onclick=\"TerritorySelect_change('-----DELETE-ALL-----','DELETE');return false;\"><b>--- DELETE ALL ---</b><br />" + live_TERR_HTML_DELETE;
			}
		}

// ################################################################################
// Enable or Disable manual dial queue calls
	function ManualQueueChoiceChange(task_amqc)
		{
		AllowManualQueueCalls = task_amqc;
		var TerritorySelectChoices = document.vicidial_form.TerritorySelectList.value;

		if (AllowManualQueueCalls == '0')
            {document.getElementById("ManualQueueChoice").innerHTML = "<a href=\"#\" onclick=\"ManualQueueChoiceChange('1');return false;\">Manual Queue is Off</a><br />";}
		else
            {document.getElementById("ManualQueueChoice").innerHTML = "<a href=\"#\" onclick=\"ManualQueueChoiceChange('0');return false;\">Manual Queue in On</a><br />";}
		}

// ################################################################################
// Update vicidial_live_agents record with territory choices
	function TerritorySelect_submit()
		{
		var TerritorySelectChoices = document.vicidial_form.TerritorySelectList.value;

		if (agent_select_territories == "MGRLOCK")
			{TerritorySelectChoices = "MGRLOCK";}

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			TERRupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=regTERRITORY&format=text&user=" + user + "&pass=" + pass + "&comments=" + agent_select_territories + "&campaign=" + campaign + "&agent_territories=" + TerritorySelectChoices + "-";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(TERRupdate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					}
				}
			delete xmlhttp;
			}

		hideDiv('TerritorySelectBox');
		MainPanelToFront();
		TerritorySelecting = 0;
		scroll(0,0);
		}


// ################################################################################
// clear api field
	function Clear_API_Field(temp_field)
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			TERRupdate_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=Clear_API_Field&format=text&user=" + user + "&pass=" + pass + "&comments=" + temp_field;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(TERRupdate_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Log the user out of the system when they close their browser while logged in
	function BrowserCloseLogout()
		{
		if (logout_stop_timeouts < 1)
			{
			if (VDRP_stage != 'PAUSED')
				{
				AutoDial_ReSume_PauSe("VDADpause",'','','',"LOGOUT");
				}
			LogouT('CLOSE');
			alert("Por favor carrege no link de Logout da próxima vez..\n");
			}
		}


// ################################################################################
// Normal logout with check for pause stage first
	function NormalLogout()
		{
		if (logout_stop_timeouts < 1)
			{
			if (VDRP_stage != 'PAUSED')
				{
				AutoDial_ReSume_PauSe("VDADpause",'','','',"LOGOUT");
				}
			LogouT('NORMAL');
			}
		}


// ################################################################################
// Log the user out of the system, if active call or active dial is occuring, don't let them.
	function logout_change_page()
	{
		window.location = agcPAGE + "?relogin=YES&session_epoch=" + epoch_sec + "&session_id=" + session_id + "&session_name=" + session_name + "&VD_login=" + user + "&VD_campaign=" + campaign + "&phone_login=" + phone_login + "&phone_pass=" + phone_pass + "&VD_pass=" + pass;
	}


	function LogouT(tempreason)
		{
		if (MD_channel_look==1)
			{alert("Não pode fazer logout. \nWait Desligue a chamada primeiro.");}
		else
			{
			if (VD_live_customer_call==1)
				{
				alert("Desligue a chamada antes de fazer logout.\n");
				}
			else
				{
				if (alt_dial_status_display==1)
					{
					alert("Estamos a tentar outros contactos. Aguarde por favor.\n" + reselect_alt_dial);
					}
				else
					{
                                            if(document.vicidial_form.lead_id.value.length!=0){
				alert("Termine a acção actual antes de fazer logout.\n");
                                                
                                            }else{
                                                window.onbeforeunload ="";
					var xmlhttp=false;
					/*@cc_on @*/
					/*@if (@_jscript_version >= 5)
					// JScript gives us Conditional compilation, we can cope with old IE versions.
					// and security blocked creation of the objects.
					 try {
					  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
					 } catch (e) {
					  try {
					   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
					  } catch (E) {
					   xmlhttp = false;
					  }
					 }
					@end @*/
					if (!xmlhttp && typeof XMLHttpRequest!='undefined')
						{
						xmlhttp = new XMLHttpRequest();
						}
					if (xmlhttp) 
						{ 
						VDlogout_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=userLOGout&format=text&user=" + user + "&pass=" + pass + "&campaign=" + campaign + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&agent_log_id=" + agent_log_id + "&no_delete_sessions=" + no_delete_sessions + "&phone_ip=" + phone_ip + "&enable_sipsak_messages=" + enable_sipsak_messages + "&LogouTKicKAlL=" + LogouTKicKAlL + "&ext_context=" + ext_context;
						xmlhttp.open('POST', 'vdc_db_query.php'); 
						xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
						xmlhttp.send(VDlogout_query); 
						xmlhttp.onreadystatechange = function() 
							{ 
							if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
								{
					//			alert(xmlhttp.responseText);var logout_content='';
						if (tempreason=='SHIFT')
                        {logout_content='Your Shift is over or has changed, you have been logged out of your session<br /><br />';}
						logout_stop_timeouts = 1; 
						logout_change_page()
				
								}
							}
						delete xmlhttp;
						}

			
							
					}
				}
                                }
			}
		}
// ################################################################################
// disable enter/return keys to not clear out vars on customer info
	function enter_disable(evt)
		{
		var e = evt? evt : window.event;
		if(!e) return;
		var key = 0;
		if (e.keyCode) { key = e.keyCode; } // for moz/fb, if keyCode==0 use 'which'
		else if (typeof(e.which)!= 'undefined') { key = e.which; }
		return key != 13;
		}


// ################################################################################
// decode the scripttext and scriptname so that it can be displayed
	function URLDecode(encodedvar,scriptformat,urlschema,webformnumber)
	{
   // Replace %ZZ with equivalent character
   // Put [ERR] in output if %ZZ is invalid.
	var HEXCHAR = "0123456789ABCDEFabcdef"; 
	var encoded = encodedvar;
	var decoded = '';
	var web_form_varsX = '';
	var i = 0;
	var RGnl = new RegExp("[\\r]\\n","g");
	var RGtab = new RegExp("\t","g");
	var RGplus = new RegExp(" |\\t|\\n","g");
	var RGiframe = new RegExp("iframe","gi");
	var regWF = new RegExp("\\`|\\~|\\:|\\;|\\#|\\'|\\\"|\\{|\\}|\\(|\\)|\\*|\\^|\\%|\\$|\\!|\\%|\\r|\\t|\\n","ig");

	var xtest;
	xtest=unescape(encoded);
	encoded=utf8_decode(xtest);

	if (urlschema == 'DEFAULT')
		{
		web_form_varsX = 
		"&lead_id=" + document.vicidial_form.lead_id.value + 
		"&vendor_id=" + document.vicidial_form.vendor_lead_code.value + 
		"&list_id=" + document.vicidial_form.list_id.value + 
		"&gmt_offset_now=" + document.vicidial_form.gmt_offset_now.value + 
		"&phone_code=" + document.vicidial_form.phone_code.value + 
		"&phone_number=" + document.vicidial_form.phone_number.value + 
		"&title=" + document.vicidial_form.title.value + 
		"&first_name=" + document.vicidial_form.first_name.value + 
		"&middle_initial=" + document.vicidial_form.middle_initial.value + 
		"&last_name=" + document.vicidial_form.last_name.value + 
		"&address1=" + document.vicidial_form.address1.value + 
		"&address2=" + document.vicidial_form.address2.value + 
		"&address3=" + document.vicidial_form.address3.value + 
		"&city=" + document.vicidial_form.city.value + 
		"&state=" + document.vicidial_form.state.value + 
		"&province=" + document.vicidial_form.province.value + 
		"&postal_code=" + document.vicidial_form.postal_code.value + 
		"&country_code=" + document.vicidial_form.country_code.value + 
		"&gender=" + document.vicidial_form.gender.value + 
		"&date_of_birth=" + document.vicidial_form.date_of_birth.value + 
		"&alt_phone=" + document.vicidial_form.alt_phone.value + 
		"&email=" + document.vicidial_form.email.value + 
		"&security_phrase=" + document.vicidial_form.security_phrase.value + 
		"&comments=" + document.vicidial_form.comments.value + 
		"&user=" + user + 
		"&pass=" + pass + 
		"&campaign=" + campaign + 
		"&phone_login=" + phone_login + 
		"&original_phone_login=" + original_phone_login +
		"&phone_pass=" + phone_pass + 
		"&fronter=" + fronter + 
		"&closer=" + user + 
		"&group=" + group + 
		"&channel_group=" + group + 
		"&SQLdate=" + SQLdate + 
		"&epoch=" + UnixTime + 
		"&uniqueid=" + document.vicidial_form.uniqueid.value + 
		"&customer_zap_channel=" + lastcustchannel + 
		"&customer_server_ip=" + lastcustserverip +
		"&server_ip=" + server_ip + 
		"&SIPexten=" + extension + 
		"&session_id=" + session_id + 
		"&phone=" + document.vicidial_form.phone_number.value + 
		"&parked_by=" + document.vicidial_form.lead_id.value +
		"&dispo=" + LeaDDispO + '' +
		"&dialed_number=" + dialed_number + '' +
		"&dialed_label=" + dialed_label + '' +
		"&source_id=" + source_id + '' +
		"&rank=" + document.vicidial_form.rank.value + '' +
		"&owner=" + document.vicidial_form.owner.value + '' +
		"&camp_script=" + campaign_script + '' +
		"&in_script=" + CalL_ScripT_id + '' +
		"&script_width=" + script_width + '' +
		"&script_height=" + script_height + '' +
		"&fullname=" + LOGfullname + '' +
		"&recording_filename=" + recording_filename + '' +
		"&recording_id=" + recording_id + '' +
		"&user_custom_one=" + VU_custom_one + '' +
		"&user_custom_two=" + VU_custom_two + '' +
		"&user_custom_three=" + VU_custom_three + '' +
		"&user_custom_four=" + VU_custom_four + '' +
		"&user_custom_five=" + VU_custom_five + '' +
		"&preset_number_a=" + CalL_XC_a_NuMber + '' +
		"&preset_number_b=" + CalL_XC_b_NuMber + '' +
		"&preset_number_c=" + CalL_XC_c_NuMber + '' +
		"&preset_number_d=" + CalL_XC_d_NuMber + '' +
		"&preset_number_e=" + CalL_XC_e_NuMber + '' +
		"&preset_dtmf_a=" + CalL_XC_a_Dtmf + '' +
		"&preset_dtmf_b=" + CalL_XC_b_Dtmf + '' +
		"&did_id=" + did_id + '' +
		"&did_extension=" + did_extension + '' +
		"&did_pattern=" + did_pattern + '' +
		"&did_description=" + did_description + '' +
		"&closecallid=" + closecallid + '' +
		"&xfercallid=" + xfercallid + '' +
		"&agent_log_id=" + agent_log_id + '' +
		"&entry_list_id=" + document.vicidial_form.entry_list_id.value + '' +
		"&web_vars=" + LIVE_web_vars + '' +
		webform_session;
		
		if (custom_field_names.length > 2)
			{
			var url_custom_field='';
			var CFN_array=custom_field_names.split('|');
			var CFN_count=CFN_array.length;
			var CFN_tick=0;
			while (CFN_tick < CFN_count)
				{
				var CFN_field = CFN_array[CFN_tick];
				if (CFN_field.length > 0)
					{
					var url_custom_field = url_custom_field + "&" + CFN_field + "=--A--" + CFN_field + "--B--";
					}
				CFN_tick++;
				}
			if (url_custom_field.length > 10)
				{
				url_custom_field = '&CF_uses_custom_fields=Y' + url_custom_field;
				}
			web_form_varsX = web_form_varsX + '' + url_custom_field;
			scriptformat='YES';
			}

		web_form_varsX = web_form_varsX.replace(RGplus, '+');
		web_form_varsX = web_form_varsX.replace(RGnl, '+');
		web_form_varsX = web_form_varsX.replace(regWF, '');

		var regWFAvars = new RegExp("\\?","ig");
		if (encoded.match(regWFAvars))
			{web_form_varsX = '&' + web_form_varsX}
		else
			{web_form_varsX = '?' + web_form_varsX}

		var TEMPX_VDIC_web_form_address = encoded + "" + web_form_varsX;

		var regWFAqavars = new RegExp("\\?&","ig");
		var regWFAaavars = new RegExp("&&","ig");
		TEMPX_VDIC_web_form_address = TEMPX_VDIC_web_form_address.replace(regWFAqavars, '?');
		TEMPX_VDIC_web_form_address = TEMPX_VDIC_web_form_address.replace(regWFAaavars, '&');
		encoded = TEMPX_VDIC_web_form_address;
		}
	if (scriptformat == 'YES')
		{
		// custom fields populate if lead information is sent with custom field names
		if (custom_field_names.length > 2)
			{
			var CFN_array=custom_field_names.split('|');
			var CFV_array=custom_field_values.split('----------');
			var CFT_array=custom_field_types.split('|');
			var CFN_count=CFN_array.length;
			var CFN_tick=0;
			var CFN_debug='';
			var CF_loaded = document.vicidial_form.FORM_LOADED.value;
			while (CFN_tick < CFN_count)
				{
				var CFN_field = CFN_array[CFN_tick];
				var RG_CFN_field = new RegExp("--A--" + CFN_field + "--B--","g");
				if ( (CFN_field.length > 0) && (encoded.match(RG_CFN_field)) )
					{
					if (CF_loaded=='1')
						{
						var CFN_value='';
						var field_parsed=0;
						if ( (CFT_array[CFN_tick]=='TIME') && (field_parsed < 1) )
							{
							var CFN_field_hour = 'HOUR_' + CFN_field;
							var cIndex_hour = vcFormIFrame.document.form_custom_fields[CFN_field_hour].selectedIndex;
							var CFN_value_hour =  vcFormIFrame.document.form_custom_fields[CFN_field_hour].options[cIndex_hour].value;
							var CFN_field_minute = 'MINUTE_' + CFN_field;
							var cIndex_minute = vcFormIFrame.document.form_custom_fields[CFN_field_minute].selectedIndex;
							var CFN_value_minute =  vcFormIFrame.document.form_custom_fields[CFN_field_minute].options[cIndex_minute].value;
							var CFN_value = CFN_value_hour + ':' + CFN_value_minute + ':00'
							field_parsed=1;
							}
						if ( (CFT_array[CFN_tick]=='SELECT') && (field_parsed < 1) )
							{
							var cIndex = vcFormIFrame.document.form_custom_fields[CFN_field].selectedIndex;
							var CFN_value =  vcFormIFrame.document.form_custom_fields[CFN_field].options[cIndex].value;
							field_parsed=1;
							}
						if ( (CFT_array[CFN_tick]=='MULTI') && (field_parsed < 1) )
							{
							var chosen = '';
							var CFN_field = CFN_field + '[]';
							for (i=0; i<vcFormIFrame.document.form_custom_fields[CFN_field].options.length; i++) 
								{
								if (vcFormIFrame.document.form_custom_fields[CFN_field].options[i].selected) 
									{
									chosen = chosen + '' + vcFormIFrame.document.form_custom_fields[CFN_field].options[i].value + ',';
									}
								}
							var CFN_value = chosen;
							if (CFN_value.length > 0) {CFN_value = CFN_value.slice(0,-1);}
							field_parsed=1;
							}
						if ( ( (CFT_array[CFN_tick]=='RADIO') || (CFT_array[CFN_tick]=='CHECKBOX') ) && (field_parsed < 1) )
							{
							var chosen = '';
							var CFN_field = CFN_field + '[]';
							var len = vcFormIFrame.document.form_custom_fields[CFN_field].length;
							for (i = 0; i <len; i++) 
								{
								if (vcFormIFrame.document.form_custom_fields[CFN_field][i].checked) 
									{
									chosen = chosen + '' + vcFormIFrame.document.form_custom_fields[CFN_field][i].value + ',';
									}
								}
							var CFN_value = chosen;
							if (CFN_value.length > 0) {CFN_value = CFN_value.slice(0,-1);}
							field_parsed=1;
							}
						if (field_parsed < 1)
							{
							var CFN_value = vcFormIFrame.document.form_custom_fields[CFN_field].value;
							field_parsed=1;
							}
						}
					else
						{
						var CFN_value = CFV_array[CFN_tick];
						}
					CFN_value = CFN_value.replace(RGnl,'+');
					CFN_value = CFN_value.replace(RGtab,'+');
					CFN_value = CFN_value.replace(RGplus,'+');
					encoded = encoded.replace(RG_CFN_field, CFN_value);
					web_form_varsX = web_form_varsX.replace(RG_CFN_field, CFN_value);
					CFN_debug = CFN_debug + '|' + CFN_field + '-' + CFN_value;
					}
				CFN_tick++;
				}
			document.getElementById("debugbottomspan").innerHTML = CFN_debug;
			}

		if (webformnumber == '1')
			{web_form_vars = web_form_varsX;}
		if (webformnumber == '2')
			{web_form_vars_two = web_form_varsX;}

		var SCvendor_lead_code = document.vicidial_form.vendor_lead_code.value;
		var SCsource_id = source_id;
		var SClist_id = document.vicidial_form.list_id.value;
		var SCgmt_offset_now = document.vicidial_form.gmt_offset_now.value;
		var SCcalled_since_last_reset = "";
		var SCphone_code = document.vicidial_form.phone_code.value;
		var SCphone_number = document.vicidial_form.phone_number.value;
		var SCtitle = document.vicidial_form.title.value;
		var SCfirst_name = document.vicidial_form.first_name.value;
		var SCmiddle_initial = document.vicidial_form.middle_initial.value;
		var SClast_name = document.vicidial_form.last_name.value;
		var SCaddress1 = document.vicidial_form.address1.value;
		var SCaddress2 = document.vicidial_form.address2.value;
		var SCaddress3 = document.vicidial_form.address3.value;
		var SCcity = document.vicidial_form.city.value;
		var SCstate = document.vicidial_form.state.value;
		var SCprovince = document.vicidial_form.province.value;
		var SCpostal_code = document.vicidial_form.postal_code.value;
		var SCcountry_code = document.vicidial_form.country_code.value;
		var SCgender = document.vicidial_form.gender.value;
		var SCdate_of_birth = document.vicidial_form.date_of_birth.value;
		var SCalt_phone = document.vicidial_form.alt_phone.value;
		var SCemail = document.vicidial_form.email.value;
		var SCsecurity_phrase = document.vicidial_form.security_phrase.value;
		var SCcomments = document.vicidial_form.comments.value;
		var SCfullname = LOGfullname;
		var SCfronter = fronter;
		var SCuser = user;
		var SCpass = pass;
		var SClead_id = document.vicidial_form.lead_id.value;
		var SCcampaign = campaign;
		var SCphone_login = phone_login;
		var SCoriginal_phone_login = original_phone_login;
		var SCgroup = group;
		var SCchannel_group = group;
		var SCSQLdate = SQLdate;
		var SCepoch = UnixTime;
		var SCuniqueid = document.vicidial_form.uniqueid.value;
		var SCcustomer_zap_channel = lastcustchannel;
		var SCserver_ip = server_ip;
		var SCSIPexten = extension;
		var SCsession_id = session_id;
		var SCdispo = LeaDDispO;
		var SCdialed_number = dialed_number;
		var SCdialed_label = dialed_label;
		var SCrank = document.vicidial_form.rank.value;
		var SCowner = document.vicidial_form.owner.value;
		var SCcamp_script = campaign_script;
		var SCin_script = CalL_ScripT_id;
		var SCscript_width = script_width;
		var SCscript_height = script_height;
		var SCrecording_filename = recording_filename;
		var SCrecording_id = recording_id;
		var SCuser_custom_one = VU_custom_one;
		var SCuser_custom_two = VU_custom_two;
		var SCuser_custom_three = VU_custom_three;
		var SCuser_custom_four = VU_custom_four;
		var SCuser_custom_five = VU_custom_five;
		var SCpreset_number_a = CalL_XC_a_NuMber;
		var SCpreset_number_b = CalL_XC_b_NuMber;
		var SCpreset_number_c = CalL_XC_c_NuMber;
		var SCpreset_number_d = CalL_XC_d_NuMber;
		var SCpreset_number_e = CalL_XC_e_NuMber;
		var SCpreset_dtmf_a = CalL_XC_a_Dtmf;
		var SCpreset_dtmf_b = CalL_XC_b_Dtmf;
		var SCdid_id = did_id;
		var SCdid_extension = did_extension;
		var SCdid_pattern = did_pattern;
		var SCdid_description = did_description;
		var SCclosecallid = closecallid;
		var SCxfercallid = xfercallid;
		var SCagent_log_id = agent_log_id;
		var SCweb_vars = LIVE_web_vars;

		if (encoded.match(RGiframe))
			{
			SCvendor_lead_code = SCvendor_lead_code.replace(RGplus,'+');
			SCsource_id = SCsource_id.replace(RGplus,'+');
			SClist_id = SClist_id.replace(RGplus,'+');
			SCgmt_offset_now = SCgmt_offset_now.replace(RGplus,'+');
			SCcalled_since_last_reset = SCcalled_since_last_reset.replace(RGplus,'+');
			SCphone_code = SCphone_code.replace(RGplus,'+');
			SCphone_number = SCphone_number.replace(RGplus,'+');
			SCtitle = SCtitle.replace(RGplus,'+');
			SCfirst_name = SCfirst_name.replace(RGplus,'+');
			SCmiddle_initial = SCmiddle_initial.replace(RGplus,'+');
			SClast_name = SClast_name.replace(RGplus,'+');
			SCaddress1 = SCaddress1.replace(RGplus,'+');
			SCaddress2 = SCaddress2.replace(RGplus,'+');
			SCaddress3 = SCaddress3.replace(RGplus,'+');
			SCcity = SCcity.replace(RGplus,'+');
			SCstate = SCstate.replace(RGplus,'+');
			SCprovince = SCprovince.replace(RGplus,'+');
			SCpostal_code = SCpostal_code.replace(RGplus,'+');
			SCcountry_code = SCcountry_code.replace(RGplus,'+');
			SCgender = SCgender.replace(RGplus,'+');
			SCdate_of_birth = SCdate_of_birth.replace(RGplus,'+');
			SCalt_phone = SCalt_phone.replace(RGplus,'+');
			SCemail = SCemail.replace(RGplus,'+');
			SCsecurity_phrase = SCsecurity_phrase.replace(RGplus,'+');
			SCcomments = SCcomments.replace(RGplus,'+');
			SCfullname = SCfullname.replace(RGplus,'+');
			SCfronter = SCfronter.replace(RGplus,'+');
			SCuser = SCuser.replace(RGplus,'+');
			SCpass = SCpass.replace(RGplus,'+');
			SClead_id = SClead_id.replace(RGplus,'+');
			SCcampaign = SCcampaign.replace(RGplus,'+');
			SCphone_login = SCphone_login.replace(RGplus,'+');
			SCoriginal_phone_login = SCoriginal_phone_login.replace(RGplus,'+');
			SCgroup = SCgroup.replace(RGplus,'+');
			SCchannel_group = SCchannel_group.replace(RGplus,'+');
			SCSQLdate = SCSQLdate.replace(RGplus,'+');
			SCuniqueid = SCuniqueid.replace(RGplus,'+');
			SCcustomer_zap_channel = SCcustomer_zap_channel.replace(RGplus,'+');
			SCserver_ip = SCserver_ip.replace(RGplus,'+');
			SCSIPexten = SCSIPexten.replace(RGplus,'+');
			SCdispo = SCdispo.replace(RGplus,'+');
			SCdialed_number = SCdialed_number.replace(RGplus,'+');
			SCdialed_label = SCdialed_label.replace(RGplus,'+');
			SCrank = SCrank.replace(RGplus,'+');
			SCowner = SCowner.replace(RGplus,'+');
			SCcamp_script = SCcamp_script.replace(RGplus,'+');
			SCin_script = SCin_script.replace(RGplus,'+');
			SCscript_width = SCscript_width.replace(RGplus,'+');
			SCscript_height = SCscript_height.replace(RGplus,'+');
			SCrecording_filename = SCrecording_filename.replace(RGplus,'+');
			SCrecording_id = SCrecording_id.replace(RGplus,'+');
			SCuser_custom_one = SCuser_custom_one.replace(RGplus,'+');
			SCuser_custom_two = SCuser_custom_two.replace(RGplus,'+');
			SCuser_custom_three = SCuser_custom_three.replace(RGplus,'+');
			SCuser_custom_four = SCuser_custom_four.replace(RGplus,'+');
			SCuser_custom_five = SCuser_custom_five.replace(RGplus,'+');
			SCpreset_number_a = SCpreset_number_a.replace(RGplus,'+');
			SCpreset_number_b = SCpreset_number_b.replace(RGplus,'+');
			SCpreset_number_c = SCpreset_number_c.replace(RGplus,'+');
			SCpreset_number_d = SCpreset_number_d.replace(RGplus,'+');
			SCpreset_number_e = SCpreset_number_e.replace(RGplus,'+');
			SCpreset_dtmf_a = SCpreset_dtmf_a.replace(RGplus,'+');
			SCpreset_dtmf_b = SCpreset_dtmf_b.replace(RGplus,'+');
			SCdid_id = SCdid_id.replace(RGplus,'+');
			SCdid_extension = SCdid_extension.replace(RGplus,'+');
			SCdid_pattern = SCdid_pattern.replace(RGplus,'+');
			SCdid_description = SCdid_description.replace(RGplus,'+');
			SCweb_vars = SCweb_vars.replace(RGplus,'+');
			}

		var RGvendor_lead_code = new RegExp("--A--vendor_lead_code--B--","g");
		var RGsource_id = new RegExp("--A--source_id--B--","g");
		var RGlist_id = new RegExp("--A--list_id--B--","g");
		var RGgmt_offset_now = new RegExp("--A--gmt_offset_now--B--","g");
		var RGcalled_since_last_reset = new RegExp("--A--called_since_last_reset--B--","g");
		var RGphone_code = new RegExp("--A--phone_code--B--","g");
		var RGphone_number = new RegExp("--A--phone_number--B--","g");
		var RGtitle = new RegExp("--A--title--B--","g");
		var RGfirst_name = new RegExp("--A--first_name--B--","g");
		var RGmiddle_initial = new RegExp("--A--middle_initial--B--","g");
		var RGlast_name = new RegExp("--A--last_name--B--","g");
		var RGaddress1 = new RegExp("--A--address1--B--","g");
		var RGaddress2 = new RegExp("--A--address2--B--","g");
		var RGaddress3 = new RegExp("--A--address3--B--","g");
		var RGcity = new RegExp("--A--city--B--","g");
		var RGstate = new RegExp("--A--state--B--","g");
		var RGprovince = new RegExp("--A--province--B--","g");
		var RGpostal_code = new RegExp("--A--postal_code--B--","g");
		var RGcountry_code = new RegExp("--A--country_code--B--","g");
		var RGgender = new RegExp("--A--gender--B--","g");
		var RGdate_of_birth = new RegExp("--A--date_of_birth--B--","g");
		var RGalt_phone = new RegExp("--A--alt_phone--B--","g");
		var RGemail = new RegExp("--A--email--B--","g");
		var RGsecurity_phrase = new RegExp("--A--security_phrase--B--","g");
		var RGcomments = new RegExp("--A--comments--B--","g");
		var RGfullname = new RegExp("--A--fullname--B--","g");
		var RGfronter = new RegExp("--A--fronter--B--","g");
		var RGuser = new RegExp("--A--user--B--","g");
		var RGpass = new RegExp("--A--pass--B--","g");
		var RGlead_id = new RegExp("--A--lead_id--B--","g");
		var RGcampaign = new RegExp("--A--campaign--B--","g");
		var RGphone_login = new RegExp("--A--phone_login--B--","g");
		var RGoriginal_phone_login = new RegExp("--A--original_phone_login--B--","g");
		var RGgroup = new RegExp("--A--group--B--","g");
		var RGchannel_group = new RegExp("--A--channel_group--B--","g");
		var RGSQLdate = new RegExp("--A--SQLdate--B--","g");
		var RGepoch = new RegExp("--A--epoch--B--","g");
		var RGuniqueid = new RegExp("--A--uniqueid--B--","g");
		var RGcustomer_zap_channel = new RegExp("--A--customer_zap_channel--B--","g");
		var RGserver_ip = new RegExp("--A--server_ip--B--","g");
		var RGSIPexten = new RegExp("--A--SIPexten--B--","g");
		var RGsession_id = new RegExp("--A--session_id--B--","g");
		var RGdispo = new RegExp("--A--dispo--B--","g");
		var RGdialed_number = new RegExp("--A--dialed_number--B--","g");
		var RGdialed_label = new RegExp("--A--dialed_label--B--","g");
		var RGrank = new RegExp("--A--rank--B--","g");
		var RGowner = new RegExp("--A--owner--B--","g");
		var RGcamp_script = new RegExp("--A--camp_script--B--","g");
		var RGin_script = new RegExp("--A--in_script--B--","g");
		var RGscript_width = new RegExp("--A--script_width--B--","g");
		var RGscript_height = new RegExp("--A--script_height--B--","g");
		var RGrecording_filename = new RegExp("--A--recording_filename--B--","g");
		var RGrecording_id = new RegExp("--A--recording_id--B--","g");
		var RGuser_custom_one = new RegExp("--A--user_custom_one--B--","g");
		var RGuser_custom_two = new RegExp("--A--user_custom_two--B--","g");
		var RGuser_custom_three = new RegExp("--A--user_custom_three--B--","g");
		var RGuser_custom_four = new RegExp("--A--user_custom_four--B--","g");
		var RGuser_custom_five = new RegExp("--A--user_custom_five--B--","g");
		var RGpreset_number_a = new RegExp("--A--preset_number_a--B--","g");
		var RGpreset_number_b = new RegExp("--A--preset_number_b--B--","g");
		var RGpreset_number_c = new RegExp("--A--preset_number_c--B--","g");
		var RGpreset_number_d = new RegExp("--A--preset_number_d--B--","g");
		var RGpreset_number_e = new RegExp("--A--preset_number_e--B--","g");
		var RGpreset_dtmf_a = new RegExp("--A--preset_dtmf_a--B--","g");
		var RGpreset_dtmf_b = new RegExp("--A--preset_dtmf_b--B--","g");
		var RGdid_id = new RegExp("--A--did_id--B--","g");
		var RGdid_extension = new RegExp("--A--did_extension--B--","g");
		var RGdid_pattern = new RegExp("--A--did_pattern--B--","g");
		var RGdid_description = new RegExp("--A--did_description--B--","g");
		var RGclosecallid = new RegExp("--A--closecallid--B--","g");
		var RGxfercallid = new RegExp("--A--xfercallid--B--","g");
		var RGagent_log_id = new RegExp("--A--agent_log_id--B--","g");
		var RGweb_vars = new RegExp("--A--web_vars--B--","g");

		encoded = encoded.replace(RGvendor_lead_code, SCvendor_lead_code);
		encoded = encoded.replace(RGsource_id, SCsource_id);
		encoded = encoded.replace(RGlist_id, SClist_id);
		encoded = encoded.replace(RGgmt_offset_now, SCgmt_offset_now);
		encoded = encoded.replace(RGcalled_since_last_reset, SCcalled_since_last_reset);
		encoded = encoded.replace(RGphone_code, SCphone_code);
		encoded = encoded.replace(RGphone_number, SCphone_number);
		encoded = encoded.replace(RGtitle, SCtitle);
		encoded = encoded.replace(RGfirst_name, SCfirst_name);
		encoded = encoded.replace(RGmiddle_initial, SCmiddle_initial);
		encoded = encoded.replace(RGlast_name, SClast_name);
		encoded = encoded.replace(RGaddress1, SCaddress1);
		encoded = encoded.replace(RGaddress2, SCaddress2);
		encoded = encoded.replace(RGaddress3, SCaddress3);
		encoded = encoded.replace(RGcity, SCcity);
		encoded = encoded.replace(RGstate, SCstate);
		encoded = encoded.replace(RGprovince, SCprovince);
		encoded = encoded.replace(RGpostal_code, SCpostal_code);
		encoded = encoded.replace(RGcountry_code, SCcountry_code);
		encoded = encoded.replace(RGgender, SCgender);
		encoded = encoded.replace(RGdate_of_birth, SCdate_of_birth);
		encoded = encoded.replace(RGalt_phone, SCalt_phone);
		encoded = encoded.replace(RGemail, SCemail);
		encoded = encoded.replace(RGsecurity_phrase, SCsecurity_phrase);
		encoded = encoded.replace(RGcomments, SCcomments);
		encoded = encoded.replace(RGfullname, SCfullname);
		encoded = encoded.replace(RGfronter, SCfronter);
		encoded = encoded.replace(RGuser, SCuser);
		encoded = encoded.replace(RGpass, SCpass);
		encoded = encoded.replace(RGlead_id, SClead_id);
		encoded = encoded.replace(RGcampaign, SCcampaign);
		encoded = encoded.replace(RGphone_login, SCphone_login);
		encoded = encoded.replace(RGoriginal_phone_login, SCoriginal_phone_login);
		encoded = encoded.replace(RGgroup, SCgroup);
		encoded = encoded.replace(RGchannel_group, SCchannel_group);
		encoded = encoded.replace(RGSQLdate, SCSQLdate);
		encoded = encoded.replace(RGepoch, SCepoch);
		encoded = encoded.replace(RGuniqueid, SCuniqueid);
		encoded = encoded.replace(RGcustomer_zap_channel, SCcustomer_zap_channel);
		encoded = encoded.replace(RGserver_ip, SCserver_ip);
		encoded = encoded.replace(RGSIPexten, SCSIPexten);
		encoded = encoded.replace(RGsession_id, SCsession_id);
		encoded = encoded.replace(RGdispo, SCdispo);
		encoded = encoded.replace(RGdialed_number, SCdialed_number);
		encoded = encoded.replace(RGdialed_label, SCdialed_label);
		encoded = encoded.replace(RGrank, SCrank);
		encoded = encoded.replace(RGowner, SCowner);
		encoded = encoded.replace(RGcamp_script, SCcamp_script);
		encoded = encoded.replace(RGin_script, SCin_script);
		encoded = encoded.replace(RGscript_width, SCscript_width);
		encoded = encoded.replace(RGscript_height, SCscript_height);
		encoded = encoded.replace(RGrecording_filename, SCrecording_filename);
		encoded = encoded.replace(RGrecording_id, SCrecording_id);
		encoded = encoded.replace(RGuser_custom_one, SCuser_custom_one);
		encoded = encoded.replace(RGuser_custom_two, SCuser_custom_two);
		encoded = encoded.replace(RGuser_custom_three, SCuser_custom_three);
		encoded = encoded.replace(RGuser_custom_four, SCuser_custom_four);
		encoded = encoded.replace(RGuser_custom_five, SCuser_custom_five);
		encoded = encoded.replace(RGpreset_number_a, SCpreset_number_a);
		encoded = encoded.replace(RGpreset_number_b, SCpreset_number_b);
		encoded = encoded.replace(RGpreset_number_c, SCpreset_number_c);
		encoded = encoded.replace(RGpreset_number_d, SCpreset_number_d);
		encoded = encoded.replace(RGpreset_number_e, SCpreset_number_e);
		encoded = encoded.replace(RGpreset_dtmf_a, SCpreset_dtmf_a);
		encoded = encoded.replace(RGpreset_dtmf_b, SCpreset_dtmf_b);
		encoded = encoded.replace(RGdid_id, SCdid_id);
		encoded = encoded.replace(RGdid_extension, SCdid_extension);
		encoded = encoded.replace(RGdid_pattern, SCdid_pattern);
		encoded = encoded.replace(RGdid_description, SCdid_description);
		encoded = encoded.replace(RGclosecallid, SCclosecallid);
		encoded = encoded.replace(RGxfercallid, SCxfercallid);
		encoded = encoded.replace(RGagent_log_id, SCagent_log_id);
		encoded = encoded.replace(RGweb_vars, SCweb_vars);
		}
	decoded=encoded; // simple no ?
	decoded = decoded.replace(RGnl, '+');
	decoded = decoded.replace(RGplus,'+');
	decoded = decoded.replace(RGtab,'+');

	
	return decoded;
	};


// ################################################################################
// Taken form php.net Angelos
function utf8_decode(utftext) {
        var string = "";
        var i = 0;
        var c = c1 = c2 = 0;

        while ( i < utftext.length ) {

            c = utftext.charCodeAt(i);

            if (c < 128) {
                string += String.fromCharCode(c);
                i++;
            }
            else if((c > 191) && (c < 224)) {
                c2 = utftext.charCodeAt(i+1);
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                i += 2;
            }
            else {
                c2 = utftext.charCodeAt(i+1);
                c3 = utftext.charCodeAt(i+2);
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                i += 3;
            }

        }

        return string;
    };


// ################################################################################
// phone number format
function phone_number_format(formatphone) {
	/*/ customer_local_time, status date display 9999999999
	//	vdc_header_phone_format
    //  US_DASH 000-000-0000 - USA dash separated phone number<br />
    //  US_PARN (000)000-0000 - USA dash separated number with area code in parenthesis<br />
    //  UK_DASH 00 0000-0000 - UK dash separated phone number with space after city code<br />
    //  AU_SPAC 000 000 000 - Australia space separated phone number<br />
    //  IT_DASH 0000-000-000 - Italy dash separated phone number<br />
    //  FR_SPAC 00 00 00 00 00 - France space separated phone number<br />
	var regUS_DASHphone = new RegExp("US_DASH","g");
	var regUS_PARNphone = new RegExp("US_PARN","g");
	var regUK_DASHphone = new RegExp("UK_DASH","g");
	var regAU_SPACphone = new RegExp("AU_SPAC","g");
	var regIT_DASHphone = new RegExp("IT_DASH","g");
	var regFR_SPACphone = new RegExp("FR_SPAC","g");
	var status_display_number = formatphone;
	var dispnum = formatphone;
	if (disable_alter_custphone == 'HIDE')
		{
		var status_display_number = 'XXXXXXXXXX';
		var dispnum = 'XXXXXXXXXX';
		}
	if (vdc_header_phone_format.match(regUS_DASHphone))
		{
		var status_display_number = dispnum.substring(0,3) + '-' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);
		}
	if (vdc_header_phone_format.match(regUS_PARNphone))
		{
		var status_display_number = '(' + dispnum.substring(0,3) + ')' + dispnum.substring(3,6) + '-' + dispnum.substring(6,10);
		}
	if (vdc_header_phone_format.match(regUK_DASHphone))
		{
		var status_display_number = dispnum.substring(0,2) + ' ' + dispnum.substring(2,6) + '-' + dispnum.substring(6,10);
		}
	if (vdc_header_phone_format.match(regAU_SPACphone))
		{
		var status_display_number = dispnum.substring(0,3) + ' ' + dispnum.substring(3,6) + ' ' + dispnum.substring(6,9);
		}
	if (vdc_header_phone_format.match(regIT_DASHphone))
		{
		var status_display_number = dispnum.substring(0,4) + '-' + dispnum.substring(4,7) + '-' + dispnum.substring(8,10);
		}
	if (vdc_header_phone_format.match(regFR_SPACphone))
		{
		var status_display_number = dispnum.substring(0,2) + ' ' + dispnum.substring(2,4) + ' ' + dispnum.substring(4,6) + ' ' + dispnum.substring(6,8) + ' ' + dispnum.substring(8,10);
		}

	return status_display_number;*/
    return formatphone;
	};


// ################################################################################
// RefresH the agents view sidebar or xfer frame
	function refresh_agents_view(RAlocation,RAcount)
		{
                if (RAcount > 0)
                {
                    if (even > 0)
                    {
                        RAview_query = {server_ip:server_ip,session_name:session_name,ACTION:"AGENTSview",format:"text",user: user,pass:pass,user_group: VU_user_group,conf_exten: session_id,extension:extension,protocol:protocol,stage: agent_status_view_time ,campaign:campaign,comments: RAlocation};
                        $.post("vdc_db_query.php", RAview_query, function(data) {
                            var newRAlocationHTML = data;

                            if (RAlocation == 'AgentXferViewSelect')
                            {
                                document.getElementById(RAlocation).innerHTML = newRAlocationHTML + "\n<br /><br /><a href=\"#\" onclick=\"AgentsXferSelect('0','AgentXferViewSelect');return false;\">Close Window</a>";
                            }
                            else
                            {
                                var target=$("#"+RAlocation+" tbody").empty();
                                $.each(data.colegas,function(){
                                    target.append(
                                            $("<tr>",{class:this.color})
                                            .append($("<td>").text(this.name))
                                            .append($("<td>").text(moment.duration(this.time, "seconds").humanize()))
                                );
                                });
                            }
                        },"json");

                    }
                }
            }


// ################################################################################
// Grab the call in queue and bring it into the session
	function callinqueuegrab(CQauto_call_id)
		{
		if (CQauto_call_id > 0)
			{
			var move_on=1;
			if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
				{
				if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
					{
					agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1','GRABCL');
					}
				else
					{
					move_on=0;
					alert_box("Tem que estar em pausa para puxar chamadas para si");
					}
				}
			if (move_on == 1)
				{
				var xmlhttp=false;
				/*@cc_on @*/
				/*@if (@_jscript_version >= 5)
				// JScript gives us Conditional compilation, we can cope with old IE versions.
				// and security blocked creation of the objects.
				 try {
				  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
				 } catch (e) {
				  try {
				   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				  } catch (E) {
				   xmlhttp = false;
				  }
				 }
				@end @*/
				if (!xmlhttp && typeof XMLHttpRequest!='undefined')
					{
					xmlhttp = new XMLHttpRequest();
					}
				if (xmlhttp) 
					{ 
					RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=CALLSINQUEUEgrab&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&campaign=" + campaign + "&stage=" + CQauto_call_id;
					xmlhttp.open('POST', 'vdc_db_query.php'); 
					xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xmlhttp.send(RAview_query); 
					xmlhttp.onreadystatechange = function() 
						{ 
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
							{
							var CQgrabresponse = xmlhttp.responseText;
							var regCQerror = new RegExp("ERROR","ig");
							if (CQgrabresponse.match(regCQerror))
								{
								alert_box(CQgrabresponse);
								}
							else
								{
								AutoDial_ReSume_PauSe("VDADready",'','','NO_STATUS_CHANGE');
								AutoDialWaiting=1;
								}
							}
						}
					delete xmlhttp;
					}

				}
			}
		}


// ################################################################################
// RefresH the calls in queue bottombar
	function refresh_calls_in_queue(CQcount)
		{
		if (CQcount > 0)
			{
			if (even > 0)
				{
				var xmlhttp=false;
				/*@cc_on @*/
				/*@if (@_jscript_version >= 5)
				// JScript gives us Conditional compilation, we can cope with old IE versions.
				// and security blocked creation of the objects.
				 try {
				  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
				 } catch (e) {
				  try {
				   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
				  } catch (E) {
				   xmlhttp = false;
				  }
				 }
				@end @*/
				if (!xmlhttp && typeof XMLHttpRequest!='undefined')
					{
					xmlhttp = new XMLHttpRequest();
					}
				if (xmlhttp) 
					{ 
					RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=CALLSINQUEUEview&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&campaign=" + campaign + "&stage=<?php echo $CQwidth ?>";
					xmlhttp.open('POST', 'vdc_db_query.php'); 
					xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
					xmlhttp.send(RAview_query); 
					xmlhttp.onreadystatechange = function() 
						{ 
						if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
							{
							document.getElementById('callsinqueuelist').innerHTML = xmlhttp.responseText + "\n";
							}
						}
					delete xmlhttp;
					}

				}
			}
		}


// ################################################################################
// Open or close the callsinqueue view bottombar
	function show_calls_in_queue(CQoperation)
		{
		if (CQoperation=='SHOW')
			{
			document.getElementById("callsinqueuelink").innerHTML = "<a href=\"#\"  onclick=\"show_calls_in_queue('HIDE');\">Esconder chamadas em espera</a>";
			view_calls_in_queue_active=1;
			}
		else
			{
			document.getElementById("callsinqueuelink").innerHTML = "<a href=\"#\"  onclick=\"show_calls_in_queue('SHOW');\">Mostrar chamadas em espera</a>";
			view_calls_in_queue_active=0;
			hideDiv('callsinqueuedisplay');
			}
		}


// ################################################################################
// Open or close the agents view sidebar or xfer frame
	function AgentsViewOpen(AVlocation,AVoperation)
		{
		if (AVoperation=='open')
			{
			if (AVlocation=='AgentViewSpan')
				{
				document.getElementById("AgentViewLink").innerHTML = "<a href=\"#\" onclick=\"AgentsViewOpen('AgentViewSpan','close');return false;\">Fechar Ver Colegas</a>";
				agent_status_view_active=1;
				}
			showDiv(AVlocation);
			}
		else
			{
			if (AVlocation=='AgentViewSpan')
				{
				document.getElementById("AgentViewLink").innerHTML = "<a href=\"#\" onclick=\"AgentsViewOpen('AgentViewSpan','open');return false;\">Ver Colegas</a>";
				agent_status_view_active=0;
				}
			hideDiv(AVlocation);
			}
		}


// ################################################################################
// Open or close the webphone view sidebar
	function webphoneOpen(WVlocation,WVoperation)
		{
		if (WVoperation=='open')
			{
			document.getElementById("webphoneLink").innerHTML = "  <a href=\"#\" onclick=\"webphoneOpen('webphoneSpan','close');return false;\">WebPhone View -</a>";
			showDiv(WVlocation);
			}
		else
			{
			document.getElementById("webphoneLink").innerHTML = "  <a href=\"#\" onclick=\"webphoneOpen('webphoneSpan','open');return false;\">WebPhone View +</a>";
			hideDiv(WVlocation);
			}
		}


// ################################################################################
// Populate the number to dial field with the selected user ID
	function AgentsXferSelect(AXuser,AXlocation)
		{
		xfer_select_agents_active=0;
		document.getElementById('AgentXferViewSelect').innerHTML = '';
		hideDiv('AgentXferViewSpan');
		hideDiv(AXlocation);
		document.vicidial_form.xfernumber.value = AXuser;
		}


// ################################################################################
// OnChange function for transfer group select list
	function XferAgentSelectLink()
		{
		var XfeRSelecT = document.getElementById("XfeRGrouP");
		var XScheck = XfeRSelecT.value
		if (XScheck.match(/AGENTDIRECT/))
			{
			showDiv('agentdirectlink');
			}
		else
			{
			hideDiv('agentdirectlink');
			}
		}


// ################################################################################
// function for number to dial for AGENTDIRECT in-group transfers
	function XferAgentSelectLaunch()
		{
		var XfeRSelecT = document.getElementById("XfeRGrouP");
		var XScheck = XfeRSelecT.value
		if (XScheck.match(/AGENTDIRECT/))
			{
			showDiv('AgentXferViewSpan');
			AgentsViewOpen('AgentXferViewSelect','open');
			refresh_agents_view('AgentXferViewSelect',agent_status_view)
			xfer_select_agents_active=1;
			document.vicidial_form.xfername.value='';
			}
		}



// ################################################################################
// Call ReQueue call back to AGENTDIRECT queue launch
	function call_requeue_launch()
		{
		document.vicidial_form.xfernumber.value = user;

		// Build transfer pull-down list
		var loop_ct = 0;
		var live_XfeR_HTML = '';
		var XfeR_SelecT = '';
		while (loop_ct < XFgroupCOUNT)
			{
			if (VARxfergroups[loop_ct] == 'AGENTDIRECT')
				{XfeR_SelecT = 'selected ';}
			else {XfeR_SelecT = '';}
			live_XfeR_HTML = live_XfeR_HTML + "<option " + XfeR_SelecT + "value=\"" + VARxfergroups[loop_ct] + "\">" + VARxfergroups[loop_ct] + " - " + VARxfergroupsnames[loop_ct] + "</option>\n";
			loop_ct++;
			}
        document.getElementById("XfeRGrouPLisT").innerHTML = "<select size=\"1\" name=\"XfeRGrouP\" class=\"cust_form\" id=\"XfeRGrouP\" onchange=\"XferAgentSelectLink();return false;\">" + live_XfeR_HTML + "</select>";

		mainxfer_send_redirect('XfeRLOCAL',lastcustchannel,lastcustserverip,'','NO');

		document.vicidial_form.DispoSelection.value = 'RQXFER';
		DispoSelect_submit();

		AutoDial_ReSume_PauSe("VDADpause",'','','',"REQUEUE",'1','RQUEUE');

//		PauseCodeSelect_submit("RQUEUE");
		}
// ################################################################################
// Refresh the call log display
	function VieWCalLLoG(logdate,formdate)
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("Tem que estar em pausa para ver o seu log");
				}
			}
		if (move_on == 1)
			{
			showDiv('CalLLoGDisplaYBox');

			if (formdate=='form')
				{logdate = document.vicidial_form.calllogdate.value;}

			var xmlhttp=false;
			/*@cc_on @*/
			/*@if (@_jscript_version >= 5)
			// JScript gives us Conditional compilation, we can cope with old IE versions.
			// and security blocked creation of the objects.
			 try {
			  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
			 } catch (e) {
			  try {
			   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");

			  } catch (E) {
			   xmlhttp = false;
			  }
			 }
			@end @*/
			if (!xmlhttp && typeof XMLHttpRequest!='undefined')
				{
				xmlhttp = new XMLHttpRequest();
				}
			if (xmlhttp) 
				{ 
				RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=CALLLOGview&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&date=" + logdate + "&campaign=" + campaign + "&stage=<?php echo $HCwidth ?>";
				xmlhttp.open('POST', 'vdc_db_query.php'); 
				xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
				xmlhttp.send(RAview_query); 
				xmlhttp.onreadystatechange = function() 
					{ 
					if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
						{
					//	alert(xmlhttp.responseText);
						document.getElementById('CallLogSpan').innerHTML = xmlhttp.responseText + "\n";
						}
					}
				delete xmlhttp;
				}
			}
		}

// ################################################################################
// Pesquisa por codigo postal
	function pesquisa_morada() {
	    var cp_4 = $("#cp_4").val();
	    var cp_3 = $("#cp_3").val();

	    if (cp_4.length == 0 && cp_3.length == 0) {
	        alert_box('Tem de inserir algum parâmetro de pesquisa.');
	    } else {

	        $.post('vdc_db_query.php', {
	            server_ip: server_ip,
	            session_name: session_name,
	            user: user,
	            pass: pass,
	            ACTION: "pesquisa_morada",
	            cp_4: cp_4,
	            cp_3: cp_3
	        }, function (data) {
                    var content=$('#result_moradas tbody');
                    content.empty();
                    $.each(data,function(){
                        content.append("<tr>\n\
                                            <td>"+this.rua+"</td>\n\
                                            <td>"+this.cod_postal+"</td>\n\
                                            <td>"+this.localidade+"</td>\n\
                                            <td>"+this.distrito+"</td>\n\
                                            <td>"+this.conselho+"</td>\n\
                                            <td><button onclick=\"aplica_morada(this)\" class=\"btn btn-mini icon-alone\" data-rua=\""+this.rua+"\" data-cp7=\""+this.cod_postal+"\" data-localidade=\""+this.localidade+"\" data-distrito=\""+this.distrito+"\" data-conselho=\""+this.conselho+"\"><i class=\"icon-map-marker\"></i></button></td>\n\
                                        </tr>");
                    });
	        },"json");

	    }
	}

	function aplica_morada(that) {
	    $("#address1").val($(that).data().rua);
	    $("#postal_code").val($(that).data().cp7);
	    $("#city").val($(that).data().localidade);
	    $("#state").val($(that).data().distrito);
            $("#province").val($(that).data().concelho);
	    hideDiv('pesquisa_morada');
	}


// ################################################################################
// Gather and display lead search data
	function LeadSearchSubmit()
		{
    if ((AutoDialWaiting == 1) || (VD_live_customer_call == 1) || (alt_dial_active == 1) || (MD_channel_look == 1) || (in_lead_preview_state == 1))
    {
        alert_box("Tem que estar em pausa para pequisar um contacto");
    }
    else
    {
        showDiv('SearcHResultSDisplaYBox');

        document.getElementById('SearcHResultSSpan').innerHTML = "<tr><td colspan=9 ><img src=/images/icons/ajax-loader.gif id=loader style='display: inline;vertical-align:middle;'> A Procurar...</td></tr> ";

        LSview_query = {server_ip: server_ip, session_name: session_name, ACTION: "SEARCHRESULTSview", format: "text", user: user, pass: pass, conf_exten: session_id, extension: extension, protocol: protocol, search_field: document.vicidial_form.search_field.value, search_query: document.vicidial_form.search_query.value, campaign: campaign};
        $.post("vdc_db_query.php",
                LSview_query,
                function(data) {
                    var container = $('#SearcHResultSSpan').empty();
                    if (data.error.length) {
                        $.each(data.error, function() {
                            container.append("<tr>\n\
                                                <td colspan=9 >" + this + "</td>\n\
                                                </tr>");
                        }
                        );

                        return false;
                    }
                    $.each(data.leads, function() {
                        container.append("<tr>\n\
                                                <td>" + this.name + "</td>\n\
                                                <td>" + this.phone_number + "</td>\n\
                                                <td>" + this.status + "</td>\n\
                                                <td>" + moment(this.call_date).fromNow() + "</td>\n\
                                                <td>" + this.city + "</td>\n\
                                                <td>" + this.state + "</td>\n\
                                                <td>" + this.postal_code + "</td>\n\
                                                <td><button class=\"btn icon-alone btn-mini\" onclick=\"VieWLeaDInfO(" + this.lead_id + ");\" ><i class=\"icon-info-sign\"></i></button></td>\n\
                                                <td><button class=\"btn icon-alone btn-mini leadSearchCall\" data-phone='" + this.phone_number + "' \"><i class=\"icon-phone\"></i></button></td>\n\
                                        </tr>");
                    });
                },"json");


    }
}

$(document).on("click",".leadSearchCall",function(){

    $("#SearcHForMDisplaYBox, #SearcHResultSDisplaYBox").hide();
    document.vicidial_form.MDPhonENumbeR.value = $(this).data().phone;
    NeWManuaLDiaLCalLSubmiT('PREVIEW');
});


// ################################################################################
// Hide manual dial form
	function ManualDialHide()
		{
		if (auto_resume_precall == 'Y')
			{
			AutoDial_ReSume_PauSe("VDADready");
			}
		hideDiv('NeWManuaLDiaLBox');
		document.vicidial_form.MDPhonENumbeR.value = '';
		document.vicidial_form.MDDiaLOverridE.value = '';
		document.vicidial_form.MDLeadID.value = '';
		document.vicidial_form.MDType.value = '';
		}


// ################################################################################
// Refresh the lead notes display
	function VieWNotesLoG(logframe)
		{
		showDiv('CalLNotesDisplaYBox');

		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			RAview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=LEADINFOview&search=logfirst&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&extension=" + extension + "&protocol=" + protocol + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign + "&stage=<?php echo $HCwidth ?>";
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(RAview_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(xmlhttp.responseText);
					document.getElementById('CallNotesSpan').innerHTML = xmlhttp.responseText + "\n";
					}
				}
			delete xmlhttp;
			}
		}



// ################################################################################
// Run the logging process for customer 3way hangup
	function customer_3way_hangup_process(temp_hungup_time,temp_xfer_call_seconds)
		{
		var xmlhttp=false;
		/*@cc_on @*/
		/*@if (@_jscript_version >= 5)
		// JScript gives us Conditional compilation, we can cope with old IE versions.
		// and security blocked creation of the objects.
		 try {
		  xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		 } catch (e) {
		  try {
		   xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		  } catch (E) {
		   xmlhttp = false;
		  }
		 }
		@end @*/
		if (!xmlhttp && typeof XMLHttpRequest!='undefined')
			{
			xmlhttp = new XMLHttpRequest();
			}
		if (xmlhttp) 
			{ 
			CTHPview_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&ACTION=customer_3way_hangup_process&format=text&user=" + user + "&pass=" + pass + "&conf_exten=" + session_id + "&lead_id=" + document.vicidial_form.lead_id.value + "&campaign=" + campaign + "&status=" + temp_hungup_time + "&stage=" + temp_xfer_call_seconds;
			xmlhttp.open('POST', 'vdc_db_query.php'); 
			xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
			xmlhttp.send(CTHPview_query); 
			xmlhttp.onreadystatechange = function() 
				{ 
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
					{
				//	alert(xmlhttp.responseText);
					document.getElementById("debugbottomspan").innerHTML = "CUSTOMER 3WAY HANGUP " + xmlhttp.responseText;
					}
				}
			delete xmlhttp;
			}
		}


// ################################################################################
// Refresh the FORM content
	function FormContentsLoad()
		{
		
		
		var form_list_id = document.vicidial_form.campaign_id.value;
		var form_entry_list_id = document.vicidial_form.entry_list_id.value;
		
		
		if (limesurvey_enabled === 1){
		document.getElementById('vcFormIFrame').src= TEMP_VDIC_web_form_address ; } else {
		
		document.getElementById('vcFormIFrame').src='./vdc_form_display.php?in_group_id=' + VDCL_group_id + '&lead_id=' + document.vicidial_form.lead_id.value + '&list_id=' + form_list_id + '&user=' + user + '&pass=' + pass + '&campaign=' + campaign + '&server_ip=' + server_ip + '&session_id=' + '&uniqueid=' + document.vicidial_form.uniqueid.value + '&stage=DISPLAY' + "&campaign=" + campaign + "&phone_login=" + phone_login + "&original_phone_login=" + original_phone_login +"&phone_pass=" + phone_pass + "&fronter=" + fronter + "&closer=" + user + "&group=" + group + "&channel_group=" + group + "&SQLdate=" + SQLdate + "&epoch=" + UnixTime + "&uniqueid=" + document.vicidial_form.uniqueid.value + "&customer_zap_channel=" + lastcustchannel + "&customer_server_ip=" + lastcustserverip +"&server_ip=" + server_ip + "&SIPexten=" + extension + "&session_id=" + session_id + "&phone=" + document.vicidial_form.phone_number.value + "&parked_by=" + document.vicidial_form.lead_id.value +"&dispo=" + LeaDDispO + '' +"&dialed_number=" + dialed_number + '' +"&dialed_label=" + dialed_label + '' +"&camp_script=" + campaign_script + '' +"&in_script=" + CalL_ScripT_id + '' +"&script_width=" + script_width + '' +"&script_height=" + script_height + '' +"&fullname=" + LOGfullname + '' +"&recording_filename=" + recording_filename + '' +"&recording_id=" + recording_id + '' +"&user_custom_one=" + VU_custom_one + '' +"&user_custom_two=" + VU_custom_two + '' +"&user_custom_three=" + VU_custom_three + '' +"&user_custom_four=" + VU_custom_four + '' +"&user_custom_five=" + VU_custom_five + '' +"&preset_number_a=" + CalL_XC_a_NuMber + '' +"&preset_number_b=" + CalL_XC_b_NuMber + '' +"&preset_number_c=" + CalL_XC_c_NuMber + '' +"&preset_number_d=" + CalL_XC_d_NuMber + '' +"&preset_number_e=" + CalL_XC_e_NuMber + '' +"&preset_dtmf_a=" + CalL_XC_a_Dtmf + '' +"&preset_dtmf_b=" + CalL_XC_b_Dtmf + '' +"&did_id=" + did_id + '' +"&did_extension=" + did_extension + '' +"&did_pattern=" + did_pattern + '' +"&did_description=" + did_description + '' +"&closecallid=" + closecallid + '' +"&xfercallid=" + xfercallid + '' +"&agent_log_id=" + agent_log_id + '' +"&web_vars=" + LIVE_web_vars + '';
		}
		
		}
		
// ################################################################################
// Move the Dispo frame out of the way and change the link to maximize
	function DispoMinimize()
		{
		showDiv('DispoButtonHideA');
		showDiv('DispoButtonHideB');
		showDiv('DispoButtonHideC');
		document.getElementById("DispoSelectBox").style.top = '340px';
		document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMaximize()\"> maximize </a>";
		}


// ################################################################################
// Move the Dispo frame to the top and change the link to minimize
	function DispoMaximize()
		{
		document.getElementById("DispoSelectBox").style.top = '80px';
		document.getElementById("DispoSelectMaxMin").innerHTML = "<a href=\"#\" onclick=\"DispoMinimize()\"> minimize </a>";
		hideDiv('DispoButtonHideA');
		hideDiv('DispoButtonHideB');
		hideDiv('DispoButtonHideC');
		}


// ################################################################################
// Show the groups selection span
	function OpeNGrouPSelectioN()
        
		{   
                    var go_on = divchecker("closer");
                    if (!go_on) { return; }
                    if ( AgentDispoing > 0) {
                        alert_box('Termine Wrap-up da chamada.')
                        return;
                    }
                    if  ( AutoDialReady == 0 && auto_dial_level > 0 && pause_code_counter == 0) {
                    alert_box('Seleccione o motivo de pausa por favor.');
                    return;
                }
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("Tem que estar em pausa para mudar de grupo");
				}
			}
		if (move_on == 1)
			{
			if (manager_ingroups_set > 0)
				{
				alert_box("Manager " + external_igb_set_name + " has selected your in-group choices");
				}
			else
				{
				HidEGenDerPulldown();
				showDiv('CloserSelectBox')
				}
			}
		}


// ################################################################################
// Show the territories selection span
	function OpeNTerritorYSelectioN()
		{
		var move_on=1;
		if ( (AutoDialWaiting == 1) || (VD_live_customer_call==1) || (alt_dial_active==1) || (MD_channel_look==1) || (in_lead_preview_state==1) )
			{
			if ((auto_pause_precall == 'Y') && ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') ) && (AutoDialWaiting == 1) && (VD_live_customer_call!=1) && (alt_dial_active!=1) && (MD_channel_look!=1) && (in_lead_preview_state!=1) )
				{
				agent_log_id = AutoDial_ReSume_PauSe("VDADpause",'','','','','1',auto_pause_precall_code);
				}
			else
				{
				move_on=0;
				alert_box("Tem que estar em pausa para mudar de território");
				}
			}
		if (move_on == 1)
			{
			showDiv('TerritorySelectBox')
			}
		}


// ################################################################################
// Hide the CBcommentsBox span upon click
	function CBcommentsBoxhide()
		{
		CBentry_time = '';
		CBcallback_time = '';
		CBuser = '';
		CBcomments = '';
		document.getElementById("CBcommentsBoxA").innerHTML = "";
		document.getElementById("CBcommentsBoxB").innerHTML = "";
		document.getElementById("CBcommentsBoxC").innerHTML = "";
		document.getElementById("CBcommentsBoxD").innerHTML = "";
		hideDiv('CBcommentsBox');
		}


// ################################################################################
// Hide the EAcommentsBox span upon click
	function EAcommentsBoxhide(minimizetask)
		{
		hideDiv('EAcommentsBox');
		if (minimizetask=='YES')
			{showDiv('EAcommentsMinBox');}
		else
			{hideDiv('EAcommentsMinBox');}
		}


// ################################################################################
// Show the EAcommentsBox span upon click
	function EAcommentsBoxshow()
		{
		showDiv('EAcommentsBox');
		hideDiv('EAcommentsMinBox');
		}


// ################################################################################
// Populating the date field in the callback frame prior to submission
	function CB_date_pick(taskdate)
		{
		document.vicidial_form.CallBackDatESelectioN.value = taskdate;
		document.getElementById("CallBackDatEPrinT").innerHTML = taskdate;
		}

var cb_to_other_user=false, cb_to_other_username="";
// ################################################################################
// Submitting the callback date and time to the system
	function CallBackDatE_submit()
		{
		
			if(document.getElementById('data_callback').value.length < 3){ 
                            $('#NoDateSelected').html("Por favor preencha uma data para o Callback.").show().fadeOut(4000);
                            return false;
                    }
                    if($("#cb_other_user").prop("checked") && $("#cb_other_username").val()===""){ 
                            $('#NoDateSelected').html("Por favor preencha o utilizador alternativo.").show().fadeOut(4000);
                            return false;
                    }
						
			var callback_date_time_temp = document.getElementById('data_callback').value.split("     ");
			var callback_date_temp = callback_date_time_temp[0].split("/");
			var data = callback_date_temp[2] + "-" + callback_date_temp[1] + "-" + callback_date_temp[0];
			var hora = callback_date_time_temp[1] + ":00";
			var callback_date_time = data + " " + hora;
			
			if( $("input[name='tipo_callback']:checked").attr("id") == "cb_pessoal" ) { CallBackrecipient = "USERONLY"; } else { CallBackrecipient = "ANYONE"; }
			
			CallBackDatETimE = callback_date_time;
			CallBackCommenTs = document.getElementById('comentarios_callback').value;
			CallBackLeadStatus = document.vicidial_form.DispoSelection.value;
			document.vicidial_form.DispoSelection.value = 'CBHOLD';
			
                        cb_to_other_user=$("#cb_other_user").prop("checked");
                        cb_to_other_username=$("#cb_other_username").val();
                        if(cb_to_other_user){$("#cb_other_user").click();}
                        
			$('#data_callback').val(" ");
			$('#comentarios_callback').val(" ");
			
			
			hideDiv('CallBackSelectBox');
			DispoSelect_submit();
			
			
			
		}



	function reactive_last_callback()
		{	
                    $.post('vdc_db_query.php', { 
                        server_ip: server_ip,
                        session_name: session_name,
                        user: user,
                        pass: pass,
                        ACTION: "reactive_callback",
                        ultimo_callback: ultimo_callback });	
		}




// ################################################################################
// Finish the wrapup timer early
	function TimerActionRun(taskaction,taskdialalert)
		{
		var next_action=0;
		if (taskaction == 'DiaLAlerT')
			{
            document.getElementById("TimerContentSpan").innerHTML = "<b>Atenção!<br /><br />" + taskdialalert.replace("\n","<br />") + "</b>";

			showDiv('TimerSpan');
			}
		else
			{
			if ( (timer_action_message.length > 0) || (timer_action == 'MESSAGE_ONLY') )
				{
                document.getElementById("TimerContentSpan").innerHTML = "<b>TIMER NOTIFICATION: " + timer_action_seconds + " seconds<br /><br />" + timer_action_message + "</b>";

				showDiv('TimerSpan');
				}

			if (timer_action == 'WEBFORM')
				{
				WebFormRefresH('NO','YES');
				window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
				}
			if (timer_action == 'WEBFORM2')
				{
				WebFormTwoRefresH('NO','YES');
				window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
				}
			if (timer_action == 'D1_DIAL')
				{
				DtMf_PreSet_a_DiaL();
				}
			if (timer_action == 'D2_DIAL')
				{
				DtMf_PreSet_b_DiaL();
				}
			if (timer_action == 'D3_DIAL')
				{
				DtMf_PreSet_c_DiaL();
				}
			if (timer_action == 'D4_DIAL')
				{
				DtMf_PreSet_d_DiaL();
				}
			if (timer_action == 'D5_DIAL')
				{
				DtMf_PreSet_e_DiaL();
				}
			if ( (timer_action == 'HANGUP') && (VD_live_customer_call==1) )
				{
				hangup_timer_xfer();
				}
			if ( (timer_action == 'EXTENSION') && (VD_live_customer_call==1) && (timer_action_destination.length > 0) )
				{
				extension_timer_xfer();
				}
			if ( (timer_action == 'CALLMENU') && (VD_live_customer_call==1) && (timer_action_destination.length > 0) )
				{
				callmenu_timer_xfer();
				}
			if ( (timer_action == 'IN_GROUP') && (VD_live_customer_call==1) && (timer_action_destination.length > 0) )
				{
				ingroup_timer_xfer();
				}
			if (timer_action_destination.length > 0)
				{
				var regNS = new RegExp("nextstep---","ig");
				if (timer_action_destination.match(regNS))
					{
					next_action=1;
					timer_action = 'NONE';
					var next_action_array=timer_action_destination.split("nextstep---");
					var next_action_details_array=next_action_array[1].split("--");
					timer_action = next_action_details_array[0];
					timer_action_seconds = parseInt(next_action_details_array[1]);
					timer_action_seconds = (timer_action_seconds + VD_live_call_secondS);
					timer_action_destination = next_action_details_array[2];
					timer_action_message = next_action_details_array[3];
					}
				}
			}

		if (next_action < 1)
			{timer_action = 'NONE';}	
		}


// ################################################################################
// Finish the wrapup timer early
	function WrapupFinish()
		{
		wrapup_counter=999;
		}
// ################################################################################
	function CheckForceReady()
	{
$.post("vdc_db_query.php", { 
    server_ip: server_ip,
    session_name: session_name,
    user: user,
    pass: pass,
    ACTION: "forcereadycheck" },
function(data){if(data == 'force')
    {AutoDial_ReSume_PauSe("VDADready");}
    else 
    {return false;}});	
        }
 
function start_all_refresh()
		{
		CheckForceReady();
		                  // NEW CODE2   
        if(AgentMsgFlag){ MsgReader(); }
        // END NEW CODE2 
		if (VICIDiaL_closer_login_checked==0)
			{
			hideDiv('NothingBox');
			hideDiv('AlertBox');
			hideDiv('CBcommentsBox');
			hideDiv('EAcommentsBox');
			hideDiv('EAcommentsMinBox');
			hideDiv('HotKeyActionBox');
			hideDiv('HotKeyEntriesBox');
			hideDiv('MainPanel');
			hideDiv('ScriptPanel');
			hideDiv('ScriptRefresH');
			hideDiv('FormRefresH');
			hideDiv('DispoSelectBox');
			hideDiv('LogouTBox');
			hideDiv('AgenTDisablEBoX');
			hideDiv('SysteMDisablEBoX');
			hideDiv('CustomerGoneBox');
			hideDiv('NoneInSessionBox');
			hideDiv('WrapupBox');
			hideDiv('TransferMain');
			hideDiv('WelcomeBoxA');
			hideDiv('CallBackSelectBox');
			hideDiv('DispoButtonHideA');
			hideDiv('DispoButtonHideB');
			hideDiv('DispoButtonHideC');
			hideDiv('CallBacKsLisTBox');
			hideDiv('NeWManuaLDiaLBox');
			hideDiv('PauseCodeSelectBox');
			hideDiv('PresetsSelectBox');
			hideDiv('GroupAliasSelectBox');
			hideDiv('AgentViewSpan');
			hideDiv('AgentXferViewSpan');
			hideDiv('TimerSpan');
			hideDiv('CalLLoGDisplaYBox');
			hideDiv('CalLNotesDisplaYBox');
			hideDiv('SearcHForMDisplaYBox');
			hideDiv('SearcHResultSDisplaYBox');
			hideDiv('LeaDInfOBox');
			hideDiv('agentdirectlink');
			hideDiv('blind_monitor_notice_span');
			hideDiv('post_phone_time_diff_span');
			hideDiv('ivrParkControl');
			if (is_webphone!='Y')
				{hideDiv('webphoneSpan');}
			if (view_calls_in_queue_launch != '1')
				{hideDiv('callsinqueuedisplay');}
			if (agentonly_callbacks != '1')
				{hideDiv('CallbacksButtons');}
			if (allow_alerts < 1)
				{hideDiv('AgentAlertSpan');}
			if (agentcall_manual != '1')
				{hideDiv('ManuaLDiaLButtons');}
			if (agent_call_log_view != '1')
				{
				hideDiv('CallNotesButtons');
				hideDiv('CallLogButtons');
				}
			if (callholdstatus != '1')
				{hideDiv('AgentStatusCalls');}
			if (agentcallsstatus != '1')
				{hideDiv('AgentStatusSpan');}
			if ( ( (auto_dial_level > 0) && (dial_method != "INBOUND_MAN") ) || (manual_dial_preview < 1) )
				{clearDiv('DiaLLeaDPrevieW');}
			if (alt_phone_dialing != 1)
				{clearDiv('DiaLDiaLAltPhonE');}
			if (volumecontrol_active != '1')
				{hideDiv('VolumeControlSpan');}
			if (DefaulTAlTDiaL == '1')
				{document.vicidial_form.DiaLAltPhonE.checked=true;}
			if (agent_status_view != '1')
				{document.getElementById("AgentViewLink").innerHTML = "";}
			if (dispo_check_all_pause == '1')
				{document.vicidial_form.DispoSelectStop.checked=true;}
			if (agent_xfer_consultative < 1)
				{hideDiv('consultative_checkbox');}
			if (agent_xfer_dial_override < 1)
				{hideDiv('dialoverride_checkbox');}
			if (agent_xfer_vm_transfer < 1)
				{hideDiv('DialBlindVMail');}
			if (agent_xfer_blind_transfer < 1)
				{hideDiv('DialBlindTransfer');}
			if (agent_xfer_dial_with_customer < 1)
				{hideDiv('DialWithCustomer');}
			if (agent_xfer_park_customer_dial < 1)
				{hideDiv('ParkCustomerDial');}
			if (AllowManualQueueCallsChoice == '1')
                {document.getElementById("ManualQueueChoice").innerHTML = "<a href=\"#\" onclick=\"ManualQueueChoiceChange('1');return false;\">Manual Queue is Off</a><br />";}

			document.vicidial_form.LeadLookuP.checked=true;

			if ( (agent_pause_codes_active=='Y') || (agent_pause_codes_active=='FORCE') )
				{
				document.getElementById("PauseCodeLinkSpan").innerHTML = "<a href=\"#\" onclick=\"PauseCodeSelectContent_create();return false;\"><i class=\"icon-glass\"></i>Tipo de pausa</a>";
				}
			if (VICIDiaL_allow_closers < 1)
				{
				document.getElementById("LocalCloser").style.display = 'none';
				}
			document.getElementById("sessionIDspan").innerHTML = session_id;
			if ( (LIVE_campaign_recording == 'NEVER') || (LIVE_campaign_recording == 'ALLFORCE') )
				{
                document.getElementById("RecorDControl").innerHTML = "<img src=\"./images/vdc_LB_startrecording_OFF.gif\" border=\"0\" alt=\"Start Recording\" />";
				}
			if (INgroupCOUNT > 0)
				{
				if (VU_closer_default_blended == 1)
					{document.vicidial_form.CloserSelectBlended.checked=true}
				CloserSelectContent_create();
				showDiv('CloserSelectBox');
				var CloserSelecting = 1;
				CloserSelectContent_create();
				}
			else
				{
				hideDiv('CloserSelectBox');
				MainPanelToFront();
				var CloserSelecting = 0;
				if (dial_method == "INBOUND_MAN")
					{
					dial_method = "MANUAL";
					auto_dial_level=0;
					starting_dial_level=0;
					document.getElementById("DiaLControl").innerHTML = DiaLControl_manual_HTML;
					}
				}
			if (territoryCOUNT > 0)
				{
				showDiv('TerritorySelectBox');
				var TerritorySelecting = 1;
				TerritorySelectContent_create();
				}
			else
				{
				hideDiv('TerritorySelectBox');
				MainPanelToFront();
				var TerritorySelecting = 0;
				}
			if ( (VtigeRLogiNScripT == 'Y') && (VtigeREnableD > 0) )
				{
				document.getElementById("ScriptContents").innerHTML = "<iframe src=\"" + VtigeRurl + "/index.php?module=Users&action=Authenticate&return_module=Users&return_action=Login&user_name=" + user + "&user_password=" + pass + "&login_theme=softed&login_language=en_us\" style=\"background-color:transparent;z-index:17;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"popupFrame\" name=\"popupFrame\" width=\"" + script_width + "px\" height=\"" + script_height + "px\"> </iframe> ";
				}
			if ( (VtigeRLogiNScripT == 'NEW_WINDOW') && (VtigeREnableD > 0) )
				{
				var VtigeRall = VtigeRurl + "/index.php?module=Users&action=Authenticate&return_module=Users&return_action=Login&user_name=" + user + "&user_password=" + pass + "&login_theme=softed&login_language=en_us";
				
				VtigeRwin =window.open(VtigeRall, web_form_target,'toolbar=1,location=1,directories=1,status=1,menubar=1,scrollbars=1,resizable=1,width=700,height=480');

				VtigeRwin.blur();
				}
			if ( (crm_popup_login == 'Y') && (crm_login_address.length > 4) )
				{
				var regWFAcustom = new RegExp("^VAR","ig");
				var TEMP_crm_login_address = URLDecode(crm_login_address,'YES');
				TEMP_crm_login_address = TEMP_crm_login_address.replace(regWFAcustom, '');

				var CRMwin = 'CRMwin';
				CRMwin = window.open(TEMP_crm_login_address, CRMwin,'toolbar=1,location=1,directories=1,status=1,menubar=1,scrollbars=1,resizable=1,width=700,height=480');

				CRMwin.blur();
				}
			if (INgroupCOUNT > 0)
				{
				HidEGenDerPulldown();
				}
			if (is_webphone=='Y')
				{
				NoneInSession();
				document.getElementById("NoneInSessionLink").innerHTML = "<a href=\"#\" onclick=\"NoneInSessionCalL();return false;\">Call Agent Webphone -></a>";
				
				var WebPhonEtarget = 'webphonewindow';

				}

			if ( (ivr_park_call=='ENABLED') || (ivr_park_call=='ENABLED_PARK_ONLY') )
				{
				showDiv('ivrParkControl');
				}

			VICIDiaL_closer_login_checked = 1;
			}
		else
			{

			var WaitingForNextStep=0;
			if ( (CloserSelecting==1) || (TerritorySelecting==1) )	{WaitingForNextStep=1;}
			if (open_dispo_screen==1)
				{
				wrapup_counter=0;
				if (wrapup_seconds > 0)	
					{
					showDiv('WrapupBox');
					document.getElementById("WrapupTimer").innerHTML = wrapup_seconds;
					wrapup_waiting=1;
					}
				// CustomerData_update();
				if (hide_gender < 1)
					{
					document.getElementById("GENDERhideFORie").innerHTML = '';
					document.getElementById("GENDERhideFORieALT").innerHTML = '<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - Undefined</option><option value="M">M - Male</option><option value="F">F - Female</option></select>';
					}
				showDiv('DispoSelectBox');
				DispoSelectContent_create('','ReSET');
				WaitingForNextStep=1;
				open_dispo_screen=0;
				LIVE_default_xfer_group = default_xfer_group;
				LIVE_campaign_recording = campaign_recording;
				LIVE_campaign_rec_filename = campaign_rec_filename;
				if (disable_alter_custphone!='HIDE')
					{document.getElementById("DispoSelectPhonE").innerHTML = dialed_number;}
				else
					{document.getElementById("DispoSelectPhonE").innerHTML = '';}
				if (auto_dial_level == 0)
					{
					if (document.vicidial_form.DiaLAltPhonE.checked==true)
						{
						reselect_alt_dial = 1;
                        document.getElementById("DiaLControl").innerHTML = "<td><img src='/images/icons/control_end_blue.png' /></td><td><a href='#' onclick=\"ManualDialNext('','','','','','0');\">Marcar Seguinte</a></td>";
						
						document.getElementById("MainStatuSSpan").innerHTML = "Dial Next Call";
						}
					else
						{
						reselect_alt_dial = 0;
						}
					}

				// Submit custom form if it is custom_fields_enabled
				
				
				if (custom_fields_enabled > 0)
					{
					//vcFormIFrame.document.form_custom_fields.submit();
					}
				}
			if (AgentDispoing > 0)	
				{
				WaitingForNextStep=1;
				check_for_conf_calls(session_id, '0');
				AgentDispoing++;
				}
			if (logout_stop_timeouts==1)	{WaitingForNextStep=1;}
			if ( (custchannellive < -30) && (lastcustchannel.length > 3) && (no_empty_session_warnings < 1) ) {/*CustomerChanneLGone();*/}
			if ( (custchannellive < -10) && (lastcustchannel.length > 3) ) {ReChecKCustoMerChaN();}
			if ( (nochannelinsession > 16) && (check_n > 15) && (no_empty_session_warnings < 1) ) {NoneInSession();}
			if (WaitingForNextStep==0)
				{
				if (trigger_ready > 0)
					{
					trigger_ready=0;
					if (auto_resume_precall == 'Y')
						{AutoDial_ReSume_PauSe("VDADready");}
					}
				// check for live channels in conference room and get current datetime
				check_for_conf_calls(session_id, '0');
				// refresh agent status view
				if (agent_status_view_active > 0)
					{
					refresh_agents_view('AgentViewStatus',agent_status_view);
					}
				if (view_calls_in_queue_active > 0)
					{
					refresh_calls_in_queue(view_calls_in_queue);
					}
				if (xfer_select_agents_active > 0)
					{
					refresh_agents_view('AgentXferViewSelect',agent_status_view);
					}
				if (agentonly_callbacks == '1')
					{CB_count_check++;}

				if (AutoDialWaiting == 1)
					{
					check_for_auto_incoming();
					}
				// look for a channel name for the manually dialed call
				if (MD_channel_look==1)
					{
					ManualDialCheckChanneL(XDcheck);
					}
				if ( (CB_count_check > 19) && (agentonly_callbacks == '1') )
					{
					CalLBacKsCounTCheck();
					CB_count_check=0;
					}
				if ( (even > 0) && (agent_display_dialable_leads > 0) )
					{
					DiaLableLeaDsCounT();
					}
				if (VD_live_customer_call==1)
					{
					VD_live_call_secondS++;
					document.vicidial_form.SecondS.value		= VD_live_call_secondS;
					document.getElementById("SecondSDISP").innerHTML = VD_live_call_secondS;
					}
				if (XD_live_customer_call==1)
					{
					XD_live_call_secondS++;
					document.vicidial_form.xferlength.value		= XD_live_call_secondS;
					}
				if (customerparked==1)
					{
					customerparkedcounter++;
					var parked_mm = Math.floor(customerparkedcounter/60);  // The minutes
					var parked_ss = customerparkedcounter % 60;              // The balance of seconds
					if (parked_ss < 10)
						{parked_ss = "0" + parked_ss;}
					var parked_mmss = parked_mm + ":" + parked_ss;
					document.getElementById("ParkCounterSpan").innerHTML = "Time On Park: " + parked_mmss;
					}
				if (customer_3way_hangup_counter_trigger > 0)
					{
					if (customer_3way_hangup_counter > customer_3way_hangup_seconds)
						{
						var customer_3way_timer_seconds = (XD_live_call_secondS - customer_3way_hangup_counter);
						customer_3way_hangup_process('DURING_CALL',customer_3way_timer_seconds);

						customer_3way_hangup_counter=0;
						customer_3way_hangup_counter_trigger=0;

						if (customer_3way_hangup_action=='DISPO')
							{
							customer_3way_hangup_dispo_message='Customer Hung-up, 3-way Call Ended Automatically';
							bothcall_send_hangup();
							}
						}
					else
						{
						customer_3way_hangup_counter++;
						document.getElementById("debugbottomspan").innerHTML = "CUSTOMER 3WAY HANGUP " + customer_3way_hangup_counter;
						}
					}
				if ( (update_fields > 0) && (update_fields_data.length > 2) )
					{
					UpdateFieldsData();
					}
				if ( (timer_action != 'NONE') && (timer_action.length > 3) && (timer_action_seconds <= VD_live_call_secondS) && (timer_action_seconds >= 0) )
					{
					TimerActionRun('','');
					}
				if (HKdispo_display > 0)
					{
					if ( (HKdispo_display == 3) && (HKfinish==1) )
						{
						HKfinish=0;
						DispoSelect_submit();
						}
					if (HKdispo_display == 1)
						{
						if (hot_keys_active==1)
							{showDiv('HotKeyEntriesBox');}
						hideDiv('HotKeyActionBox');
						}
					HKdispo_display--;
					}
				if (all_record == 'YES')
					{
					if (all_record_count < allcalls_delay)
						{all_record_count++;}
					else
						{
						conf_send_recording('MonitorConf',session_id ,'');
						all_record = 'NO';
						all_record_count=0;
						}
					}


				if (active_display==1)
					{
					check_s = check_n.toString();
						if ( (check_s.match(/00$/)) || (check_n<2) ) 
							{
						//	check_for_conf_calls();
							}
					}
				if (check_n<2) 
					{
					}
				else
					{
					check_s = check_n.toString();
					}
				if ( (blind_monitoring_now > 0) && ( (blind_monitor_warning=='ALERT') || (blind_monitor_warning=='NOTICE') ||  (blind_monitor_warning=='AUDIO') || (blind_monitor_warning=='ALERT_NOTICE') || (blind_monitor_warning=='ALERT_AUDIO') || (blind_monitor_warning=='NOTICE_AUDIO') || (blind_monitor_warning=='ALL') ) )
					{
					if ( (blind_monitor_warning=='NOTICE') || (blind_monitor_warning=='ALERT_NOTICE') || (blind_monitor_warning=='NOTICE_AUDIO') || (blind_monitor_warning=='ALL') )
						{
                        document.getElementById("blind_monitor_notice_span_contents").innerHTML = blind_monitor_message + "<br />";
						showDiv('blind_monitor_notice_span');
						}
					if (blind_monitoring_now_trigger > 0)
						{
						if ( (blind_monitor_warning=='ALERT') || (blind_monitor_warning=='ALERT_NOTICE')|| (blind_monitor_warning=='ALERT_AUDIO') || (blind_monitor_warning=='ALL') )
							{
							document.getElementById("blind_monitor_alert_span_contents").innerHTML = blind_monitor_message;
							showDiv('blind_monitor_alert_span');
							}
						if ( (blind_monitor_filename.length > 0) && ( (blind_monitor_warning=='AUDIO') || (blind_monitor_warning=='ALERT_AUDIO')|| (blind_monitor_warning=='NOTICE_AUDIO') || (blind_monitor_warning=='ALL') ) )
							{
							basic_originate_call(blind_monitor_filename,'NO','YES',session_id,'YES','','1','0','1');
							}
						blind_monitoring_now_trigger=0;
						}
					}
				else
					{
					hideDiv('blind_monitor_notice_span');
					document.getElementById("blind_monitor_notice_span_contents").innerHTML = '';
					hideDiv('blind_monitor_alert_span');
					}
					
			//AQUI
				if (wrapup_seconds > 0)	
					{
					document.getElementById("WrapupTimer").innerHTML = (wrapup_seconds - wrapup_counter);
					wrapup_counter++;
					if ( (wrapup_counter > wrapup_seconds) && (document.getElementById("WrapupBox").style.display != 'none') )
						{
						wrapup_waiting=0;
						hideDiv('WrapupBox');
						if (document.vicidial_form.DispoSelectStop.checked==true)
							{
							if (auto_dial_level != '0')
								{
								AutoDialWaiting = 0;
								AutoDial_ReSume_PauSe("VDADpause");
								}
							VICIDiaL_pause_calling = 1;
							if (dispo_check_all_pause != '1')
								{
								document.vicidial_form.DispoSelectStop.checked=false;
								}
							}
						else
							{
							if (auto_dial_level != '0')
								{
								AutoDialWaiting = 1;
								AutoDial_ReSume_PauSe("VDADready","NEW_ID","WRAPUP");
								}
							}
						}
					}
				}
			}
		setTimeout("all_refresh()", refresh_interval);
		}
	function all_refresh()
		{
		epoch_sec++;
		check_n++;
		even++;
		if (even > 1)
			{even=0;}
		var year= t.getYear()
		var month= t.getMonth()
			month++;
		var daym= t.getDate()
		var hours = t.getHours();
		var min = t.getMinutes();
		var sec = t.getSeconds();
		var regMSdate = new RegExp("MS_","g");
		var regUSdate = new RegExp("US_","g");
		var regEUdate = new RegExp("EU_","g");
		var regALdate = new RegExp("AL_","g");
		var regAMPMdate = new RegExp("AMPM","g");
		if (year < 1000) {year+=1900}
		if (month< 10) {month= "0" + month}
		if (daym< 10) {daym= "0" + daym}
		if (hours < 10) {hours = "0" + hours;}
		if (min < 10) {min = "0" + min;}
		if (sec < 10) {sec = "0" + sec;}
		var Tyear = (year-2000);
		filedate = year + "" + month + "" + daym + "-" + hours + "" + min + "" + sec;
		tinydate = Tyear + "" + month + "" + daym + "" + hours + "" + min + "" + sec;
		SQLdate = year + "-" + month + "-" + daym + " " + hours + ":" + min + ":" + sec;

		var status_date = '';
		var status_time = hours + ":" + min + ":" + sec;
		if (vdc_header_date_format.match(regMSdate))
			{
			status_date = year + "-" + month + "-" + daym;
			}
		if (vdc_header_date_format.match(regUSdate))
			{
			status_date = month + "/" + daym + "/" + year;
			}
		if (vdc_header_date_format.match(regEUdate))
			{
			status_date = daym + "/" + month + "/" + year;
			}
		if (vdc_header_date_format.match(regALdate))
			{
			var statusmon='';
			if (month == 1) {statusmon = "JAN";}
			if (month == 2) {statusmon = "FEB";}
			if (month == 3) {statusmon = "MAR";}
			if (month == 4) {statusmon = "APR";}
			if (month == 5) {statusmon = "MAY";}
			if (month == 6) {statusmon = "JUN";}
			if (month == 7) {statusmon = "JLY";}
			if (month == 8) {statusmon = "AUG";}
			if (month == 9) {statusmon = "SEP";}
			if (month == 10) {statusmon = "OCT";}
			if (month == 11) {statusmon = "NOV";}
			if (month == 12) {statusmon = "DEC";}

			status_date = statusmon + " " + daym;
			}
		if (vdc_header_date_format.match(regAMPMdate))
			{
			var AMPM = 'AM';
			if (hours == 12) {AMPM = 'PM';}
			if (hours == 0) {AMPM = 'AM'; hours = '12';}
			if (hours > 12) {hours = (hours - 12);   AMPM = 'PM';}
			status_time = hours + ":" + min + ":" + sec + " " + AMPM;
			}

		document.getElementById("dataHeader").innerHTML = status_date + " " + status_time  + display_message;
		if (VD_live_customer_call==1)
			{
			var customer_gmt = parseFloat(document.vicidial_form.gmt_offset_now.value);
			var AMPM = 'AM';
			var customer_gmt_diff = (customer_gmt - local_gmt);
			var UnixTimec = (UnixTime + (3600 * customer_gmt_diff));
			var UnixTimeMSc = (UnixTimec * 1000);
			c.setTime(UnixTimeMSc);
			var Cyear= c.getYear()
			var Cmon= c.getMonth()
				Cmon++;
			var Cdaym= c.getDate()
			var Chours = c.getHours();
			var Cmin = c.getMinutes();
			var Csec = c.getSeconds();
			if (Cyear < 1000) {Cyear+=1900}
			if (Cmon < 10) {Cmon= "0" + Cmon}
			if (Cdaym < 10) {Cdaym= "0" + Cdaym}
			if (Chours < 10) {Chours = "0" + Chours;}
			if ( (Cmin < 10) && (Cmin.length < 2) ) {Cmin = "0" + Cmin;}
			if ( (Csec < 10) && (Csec.length < 2) ) {Csec = "0" + Csec;}
			if (Cmin < 10) {Cmin = "0" + Cmin;}
			if (Csec < 10) {Csec = "0" + Csec;}

		var customer_date = '';
		var customer_time = Chours + ":" + Cmin + ":" + Csec;
		if (vdc_customer_date_format.match(regMSdate))
			{
			customer_date = Cyear + "-" + Cmon + "-" + Cdaym;
			}
		if (vdc_customer_date_format.match(regUSdate))
			{
			customer_date = Cmon + "/" + Cdaym + "/" + Cyear;
			}
		if (vdc_customer_date_format.match(regEUdate))
			{
			customer_date = Cdaym + "/" + Cmon + "/" + Cyear;
			}
		if (vdc_customer_date_format.match(regALdate))
			{
			var customermon='';
			if (Cmon == 1) {customermon = "JAN";}
			if (Cmon == 2) {customermon = "FEB";}
			if (Cmon == 3) {customermon = "MAR";}
			if (Cmon == 4) {customermon = "APR";}
			if (Cmon == 5) {customermon = "MAY";}
			if (Cmon == 6) {customermon = "JUN";}
			if (Cmon == 7) {customermon = "JLY";}
			if (Cmon == 8) {customermon = "AUG";}
			if (Cmon == 9) {customermon = "SEP";}
			if (Cmon == 10) {customermon = "OCT";}
			if (Cmon == 11) {customermon = "NOV";}
			if (Cmon == 12) {customermon = "DEC";}

			customer_date = customermon + " " + Cdaym + " ";
			}
		if (vdc_customer_date_format.match(regAMPMdate))
			{
			var AMPM = 'AM';
			if (Chours == 12) {AMPM = 'PM';}
			if (Chours == 0) {AMPM = 'AM'; Chours = '12';}
			if (Chours > 12) {Chours = (Chours - 12);   AMPM = 'PM';}
			customer_time = Chours + ":" + Cmin + ":" + Csec + " " + AMPM;
			}

			var customer_local_time = customer_date + " " + customer_time;
			document.getElementById("custdatetime").innerHTML = customer_local_time;
			}
		start_all_refresh();

		if (check_n==2)
			{
		$("#LoadingBox").fadeOut("slow");
			}
		}
	function pause()	// Pauses the refreshing of the lists
		{active_display=2;  display_message="  - ACTIVE DISPLAY PAUSED - ";}
	function start()	// resumes the refreshing of the lists
		{active_display=1;  display_message='';}
	function faster()	// lowers by 1000 milliseconds the time until the next refresh
		{
		 if (refresh_interval>1001)
			{refresh_interval=(refresh_interval - 1000);}
		}
	function slower()	// raises by 1000 milliseconds the time until the next refresh
		{
		refresh_interval=(refresh_interval + 1000);
		}

	// activeext-specific functions
	function activeext_force_refresh()	// forces immediate refresh of list content
		{getactiveext();}
	function activeext_order_asc()	// changes order of activeext list to ascending
		{
		activeext_order="asc";   getactiveext();
		desc_order_HTML ='<a href="#" onclick="activeext_order_desc();return false;">ORDER</a>';
		document.getElementById("activeext_order").innerHTML = desc_order_HTML;
		}
	function activeext_order_desc()	// changes order of activeext list to descending
		{
		activeext_order="desc";   getactiveext();
		asc_order_HTML ='<a href="#" onclick="activeext_order_asc();return false;">ORDER</a>';
		document.getElementById("activeext_order").innerHTML = asc_order_HTML;
		}

	// busytrunk-specific functions
	function busytrunk_force_refresh()	// forces immediate refresh of list content
		{getbusytrunk();}
	function busytrunk_order_asc()	// changes order of busytrunk list to ascending
		{
		busytrunk_order="asc";   getbusytrunk();
		desc_order_HTML ='<a href="#" onclick="busytrunk_order_desc();return false;">ORDER</a>';
		document.getElementById("busytrunk_order").innerHTML = desc_order_HTML;
		}
	function busytrunk_order_desc()	// changes order of busytrunk list to descending
		{
		busytrunk_order="desc";   getbusytrunk();
		asc_order_HTML ='<a href="#" onclick="busytrunk_order_asc();return false;">ORDER</a>';
		document.getElementById("busytrunk_order").innerHTML = asc_order_HTML;
		}
	function busytrunkhangup_force_refresh()	// forces immediate refresh of list content
		{busytrunkhangup();}

	// busyext-specific functions
	function busyext_force_refresh()	// forces immediate refresh of list content
		{getbusyext();}
	function busyext_order_asc()	// changes order of busyext list to ascending
		{
		busyext_order="asc";   getbusyext();
		desc_order_HTML ='<a href="#" onclick="busyext_order_desc();return false;">ORDER</a>';
		document.getElementById("busyext_order").innerHTML = desc_order_HTML;
		}
	function busyext_order_desc()	// changes order of busyext list to descending
		{
		busyext_order="desc";   getbusyext();
		asc_order_HTML ='<a href="#" onclick="busyext_order_asc();return false;">ORDER</a>';
		document.getElementById("busyext_order").innerHTML = asc_order_HTML;
		}
	function busylocalhangup_force_refresh()	// forces immediate refresh of list content
		{busylocalhangup();}


		
	// functions to hide and show different DIVs
	function showDiv(divvar) 
		{
		if ($('#'+divvar).length)
			{
                            $('#'+divvar).show();
			}
		}
	function hideDiv(divvar)
		{
		if ($('#'+divvar).length)
			{
                            $('#'+divvar).hide();
			}
		}
	function clearDiv(divvar)
		{
		if (document.getElementById(divvar))
			{
			document.getElementById(divvar).innerHTML = '';
			if (divvar == 'DiaLLeaDPrevieW')
				{
                var buildDivHTML = "<input type=\"checkbox\" name=\"LeadPreview\" size=\"1\" value=\"0\" /> Preview de chamada<br />";
				document.getElementById("DiaLLeaDPrevieWHide").innerHTML = buildDivHTML;
				}
			if (divvar == 'DiaLDiaLAltPhonE')
				{
                var buildDivHTML = " <input type=\"checkbox\" name=\"DiaLAltPhonE\" size=\"1\" value=\"0\" /> ALT PHONE DIAL<br />";
				document.getElementById("DiaLDiaLAltPhonEHide").innerHTML = buildDivHTML;
				}
			if (DefaulTAlTDiaL == '1')
				{document.vicidial_form.DiaLAltPhonE.checked=true;}
			}
		}
	function buildDiv(divvar)
		{
		if (document.getElementById(divvar))
			{
			var buildDivHTML = "";
			if (divvar == 'DiaLLeaDPrevieW')
				{
				document.getElementById("DiaLLeaDPrevieWHide").innerHTML = '';
                var buildDivHTML = " <input type=\"checkbox\" name=\"LeadPreview\" size=\"1\" value=\"0\" /> Preview de chamada<br />";
				document.getElementById(divvar).innerHTML = buildDivHTML;
				if (reselect_preview_dial==1)
					{document.vicidial_form.LeadPreview.checked=true}
				}
			if (divvar == 'DiaLDiaLAltPhonE')
				{
				document.getElementById("DiaLDiaLAltPhonEHide").innerHTML = '';
                var buildDivHTML = " <input type=\"checkbox\" name=\"DiaLAltPhonE\" size=\"1\" value=\"0\" /> ALT PHONE DIAL<br />";
				document.getElementById(divvar).innerHTML = buildDivHTML;
				if (reselect_alt_dial==1)
					{document.vicidial_form.DiaLAltPhonE.checked=true}
				if (DefaulTAlTDiaL == '1')
					{document.vicidial_form.DiaLAltPhonE.checked=true;}
				}
			}
		}
          // ################################################################################
// Check to see if there are any channels live in the agent's conference meetme room
            function check_for_conf_calls(taskconfnum, taskforce)
            {
                if (typeof(xmlhttprequestcheckconf) === "undefined") {
                    //alert (xmlhttprequestcheckconf == xmlhttpSendConf);
                    xmlhttprequestcheckconf_wait = 0;
                    custchannellive--;
                    if ((agentcallsstatus === '1') || (callholdstatus === '1'))
                    {
                        campagentstatct++;
                        if (campagentstatct > campagentstatctmax)
                        {
                            campagentstatct = 0;
                            var campagentstdisp = 'YES';
                        }
                        else
                        {
                            var campagentstdisp = 'NO';
                        }
                    }
                    else
                    {
                        var campagentstdisp = 'NO';
                    }

                    xmlhttprequestcheckconf = false;
                    /*@cc_on @*/
                    /*@if (@_jscript_version >= 5)
                     // JScript gives us Conditional compilation, we can cope with old IE versions.
                     // and security blocked creation of the objects.
                     try {
                     xmlhttprequestcheckconf = new ActiveXObject("Msxml2.XMLHTTP");
                     } catch (e) {
                     try {
                     xmlhttprequestcheckconf = new ActiveXObject("Microsoft.XMLHTTP");
                     } catch (E) {
                     xmlhttprequestcheckconf = false;
                     }
                     }
                     @end @*/
                    //alert ("1");
                    if (!xmlhttprequestcheckconf && typeof XMLHttpRequest !== 'undefined')
                    {
                        xmlhttprequestcheckconf = new XMLHttpRequest();
                    }
                    if (xmlhttprequestcheckconf)
                    {
                        checkconf_query = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&client=vdc&conf_exten=" + taskconfnum + "&auto_dial_level=" + auto_dial_level + "&campagentstdisp=" + campagentstdisp;
                        xmlhttprequestcheckconf.open('POST', 'conf_exten_check.php');
                        xmlhttprequestcheckconf.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                        xmlhttprequestcheckconf.send(checkconf_query);
                        xmlhttprequestcheckconf.onreadystatechange = function()
                        {
                            if (xmlhttprequestcheckconf && xmlhttprequestcheckconf.readyState === 4 && xmlhttprequestcheckconf.status === 200)
                            {
                                var check_conf = null;
                                var LMAforce = taskforce;


                                check_conf = xmlhttprequestcheckconf.responseText;

                                //	alert(checkconf_query);
                                //	alert(xmlhttprequestcheckconf.responseText);
                                var check_ALL_array = $.parseJSON(check_conf);
                                if (check_ALL_array.Invalid_session_name !== undefined) {
                                    return;
                                }
                                var check_time_array = check_ALL_array[0];


                                UnixTime = check_time_array.UnixTime;
                                UnixTime = parseInt(UnixTime);
                                UnixTimeMS = (UnixTime * 1000);
                                t.setTime(UnixTimeMS);
                                if ((callholdstatus === '1') || (agentcallsstatus === '1') || (vicidial_agent_disable !== 'NOT_ACTIVE'))
                                {
                                    var AGLogiN = check_time_array.Logged;
                                    var CamPCalLs = check_time_array.CampCalls;
                                    var DiaLCalLs = check_time_array.DiaLCalls;
                                    if (AGLogiN !== 'N')
                                    {
                                        document.getElementById("AgentStatusStatus").innerHTML = AGLogiN;
                                    }
                                    if (CamPCalLs !== 'N')
                                    {
                                        document.getElementById("AgentStatusCalls").innerHTML = CamPCalLs;
                                    }
                                    if (DiaLCalLs !== 'N')
                                    {
                                        document.getElementById("AgentStatusDiaLs").innerHTML = DiaLCalLs;
                                    }
                                    if ((AGLogiN === 'DEAD_VLA') && ((vicidial_agent_disable === 'LIVE_AGENT') || (vicidial_agent_disable === 'ALL')))
                                    {
                                        showDiv('AgenTDisablEBoX');
                                    }
                                    if ((AGLogiN === 'DEAD_EXTERNAL') && ((vicidial_agent_disable === 'EXTERNAL') || (vicidial_agent_disable === 'ALL')))
                                    {
                                        showDiv('AgenTDisablEBoX');
                                    }
                                    if ((AGLogiN === 'TIME_SYNC') && (vicidial_agent_disable === 'ALL'))
                                    {
                                        showDiv('SysteMDisablEBoX');
                                    }
                                    if (AGLogiN === 'SHIFT_LOGOUT')
                                    {
                                        shift_logout_flag = 1;
                                    }
                                }
                                var VLAStatuS = check_time_array.Status;
                                if ((VLAStatuS === 'PAUSED') && (AutoDialWaiting === 1))
                                {
                                    if (PausENotifYCounTer > 10)
                                    {
                                        alert_box('A sua sessão está em pausa');
                                        AutoDial_ReSume_PauSe('VDADpause');
                                        PausENotifYCounTer = 0;
                                    }
                                    else {
                                        PausENotifYCounTer++;
                                    }
                                }
                                else {
                                    PausENotifYCounTer = 0;
                                }

                                var APIHanguP = check_time_array.APIHanguP;
                                var APIStatuS = check_time_array.APIStatuS;
                                var APIPausE = check_time_array.APIPausE;
                                var APIDiaL = check_time_array.APIDiaL;
                                APIManualDialQueue = check_time_array.APIManualDialQueue;
                                var CheckDEADcall = check_time_array.DEADcall;
                                var InGroupChange = check_time_array.InGroupChange;
                                var InGroupChangeBlend = check_time_array[12];
                                var InGroupChangeName = check_time_array[14];
                                update_fields = check_time_array.APIFields;
                                update_fields_data = check_time_array.APIFieldsData;
                                api_timer_action = check_time_array.APITimerAction;
                                api_timer_action_message = check_time_array.APITimerMessage;
                                api_timer_action_seconds = check_time_array.APITimerSeconds;
                                api_timer_action_destination = check_time_array.APITimerDestination;
                                api_dtmf = check_time_array.APIdtmf;
                                var api_transferconf_values_array = check_time_array.APItransferconf;
                                api_transferconf_function = api_transferconf_values_array[0];
                                api_transferconf_group = api_transferconf_values_array[1];
                                api_transferconf_number = api_transferconf_values_array[2];
                                api_transferconf_consultative = api_transferconf_values_array[3];
                                api_transferconf_override = api_transferconf_values_array[4];
                                api_parkcustomer = check_time_array.APIpark;

                                if (api_transferconf_function !== undefined)
                                {
                                    if (api_transferconf_function === 'HANGUP_XFER')
                                    {
                                        xfercall_send_hangup();
                                    }
                                    if (api_transferconf_function === 'HANGUP_BOTH')
                                    {
                                        bothcall_send_hangup();
                                    }
                                    if (api_transferconf_function === 'LEAVE_VM')
                                    {
                                        mainxfer_send_redirect('XfeRVMAIL', lastcustchannel, lastcustserverip);
                                    }
                                    if (api_transferconf_function === 'LEAVE_3WAY_CALL')
                                    {
                                        leave_3way_call('FIRST');
                                    }
                                    if (api_transferconf_function === 'BLIND_TRANSFER')
                                    {
                                        document.vicidial_form.xfernumber.value = api_transferconf_number;
                                        mainxfer_send_redirect('XfeRBLIND', lastcustchannel, lastcustserverip);
                                    }
                                    if (api_transferconf_function === 'LOCAL_CLOSER')
                                    {
                                        API_selected_xfergroup = api_transferconf_group;
                                        document.vicidial_form.xfernumber.value = api_transferconf_number;
                                        mainxfer_send_redirect('XfeRLOCAL', lastcustchannel, lastcustserverip);
                                    }
                                    if (api_transferconf_function === 'DIAL_WITH_CUSTOMER')
                                    {
                                        if (api_transferconf_consultative === 'YES')
                                        {
                                            document.vicidial_form.consultativexfer.checked = true;
                                        }
                                        if (api_transferconf_consultative === 'NO')
                                        {
                                            document.vicidial_form.consultativexfer.checked = false;
                                        }
                                        if (api_transferconf_override === 'YES')
                                        {
                                            document.vicidial_form.xferoverride.checked = true;
                                        }
                                        API_selected_xfergroup = api_transferconf_group;
                                        document.vicidial_form.xfernumber.value = api_transferconf_number;
                                        SendManualDial('YES');
                                    }
                                    if (api_transferconf_function === 'PARK_CUSTOMER_DIAL')
                                    {
                                        if (api_transferconf_consultative === 'YES')
                                        {
                                            document.vicidial_form.consultativexfer.checked = true;
                                        }
                                        if (api_transferconf_consultative === 'NO')
                                        {
                                            document.vicidial_form.consultativexfer.checked = false;
                                        }
                                        if (api_transferconf_override === 'YES')
                                        {
                                            document.vicidial_form.xferoverride.checked = true;
                                        }
                                        API_selected_xfergroup = api_transferconf_group;
                                        document.vicidial_form.xfernumber.value = api_transferconf_number;
                                        xfer_park_dial();
                                    }
                                    Clear_API_Field('external_transferconf');
                                }
                                if (api_parkcustomer === 'PARK_CUSTOMER')
                                {
                                    mainxfer_send_redirect('ParK', lastcustchannel, lastcustserverip);
                                }
                                if (api_parkcustomer === 'GRAB_CUSTOMER')
                                {
                                    mainxfer_send_redirect('FROMParK', lastcustchannel, lastcustserverip);
                                }
                                if (api_parkcustomer === 'PARK_IVR_CUSTOMER')
                                {
                                    mainxfer_send_redirect('ParKivr', lastcustchannel, lastcustserverip);
                                }
                                if (api_parkcustomer === 'GRAB_IVR_CUSTOMER')
                                {
                                    mainxfer_send_redirect('FROMParKivr', lastcustchannel, lastcustserverip);
                                }
                                if (api_dtmf.length > 0)
                                {
                                    var REGdtmfPOUND = new RegExp("P", "g");
                                    var REGdtmfSTAR = new RegExp("S", "g");
                                    var REGdtmfQUIET = new RegExp("Q", "g");
                                    api_dtmf = api_dtmf.replace(REGdtmfPOUND, '#');
                                    api_dtmf = api_dtmf.replace(REGdtmfSTAR, '*');
                                    api_dtmf = api_dtmf.replace(REGdtmfQUIET, ',');
                                    document.vicidial_form.conf_dtmf.value = api_dtmf;
                                    SendConfDTMF(session_id);
                                }

                                if (api_timer_action.length > 2)
                                {
                                    timer_action = api_timer_action;
                                    timer_action_message = api_timer_action_message;
                                    timer_action_seconds = api_timer_action_seconds;
                                    timer_action_destination = api_timer_action_destination;
                                }
                                if ((APIHanguP === 1) && (VD_live_customer_call === 1))
                                {
                                    hideDiv('CustomerGoneBox');
                                    WaitingForNextStep = 0;
                                    custchannellive = 0;

                                    dialedcall_send_hangup();
                                }
                                if ((APIStatuS.length < 10) && (APIStatuS.length > 0) && (AgentDispoing > 1))
                                {
                                    document.vicidial_form.DispoSelection.value = APIStatuS;
                                    DispoSelect_submit();
                                }
                                if (APIPausE.length > 4)
                                {
                                    var APIPausE_array = APIPausE.split("!");
                                    if (APIPausE_ID === APIPausE_array[1])
                                    {
                                    }
                                    else
                                    {
                                        APIPausE_ID = APIPausE_array[1];
                                        if (APIPausE_array[0] === 'PAUSE')
                                        {
                                            if (VD_live_customer_call === 1)
                                            {
                                                // set to pause on next dispo
                                                document.vicidial_form.DispoSelectStop.checked = true;
                                            }
                                            else
                                            {
                                                if (AutoDialReady === 1)
                                                {
                                                    if (auto_dial_level !== '0')
                                                    {
                                                        AutoDialWaiting = 0;
                                                        AutoDial_ReSume_PauSe("VDADpause");
                                                    }
                                                    VICIDiaL_pause_calling = 1;
                                                }
                                            }
                                        }
                                        if ((APIPausE_array[0] === 'RESUME') && (AutoDialReady < 1) && (auto_dial_level > 0))
                                        {
                                            AutoDialWaiting = 1;
                                            AutoDial_ReSume_PauSe("VDADready");
                                        }
                                    }
                                }
                                if ((APIDiaL.length > 9) && (AllowManualQueueCalls === '0'))
                                {
                                    APIManualDialQueue++;
                                }
                                if (APIManualDialQueue !== APIManualDialQueue_last)
                                {
                                    APIManualDialQueue_last = APIManualDialQueue;
                                    document.getElementById("ManualQueueNotice").innerHTML = "<b><font color=\"red\" size=\"3\">Manual Queue: " + APIManualDialQueue + "</font></b><br />";
                                }
                                if ((APIDiaL.length > 9) && (WaitingForNextStep === '0') && (AllowManualQueueCalls === '1') && (check_n > 2))
                                {
                                    var APIDiaL_array_detail = APIDiaL;
                                    if (APIDiaL_ID === APIDiaL_array_detail[6])
                                    {
                                    }
                                    else
                                    {
                                        APIDiaL_ID = APIDiaL_array_detail[6];
                                        document.vicidial_form.MDDiaLCodE.value = APIDiaL_array_detail[1];
                                        document.vicidial_form.phone_code.value = APIDiaL_array_detail[1];
                                        document.vicidial_form.MDPhonENumbeR.value = APIDiaL_array_detail[0];
                                        document.vicidial_form.vendor_lead_code.value = APIDiaL_array_detail[5];
                                        prefix_choice = APIDiaL_array_detail[7];
                                        active_group_alias = APIDiaL_array_detail[8];
                                        cid_choice = APIDiaL_array_detail[9];
                                        vtiger_callback_id = APIDiaL_array_detail[10];
                                        document.vicidial_form.MDLeadID.value = APIDiaL_array_detail[11];
                                        document.vicidial_form.MDType.value = APIDiaL_array_detail[12];


                                        if (APIDiaL_array_detail[2] === 'YES')  // lookup lead in system
                                        {
                                            document.vicidial_form.LeadLookuP.checked = true;
                                        }
                                        else
                                        {
                                            document.vicidial_form.LeadLookuP.checked = false;
                                        }
                                        if (APIDiaL_array_detail[4] === 'YES')  // focus on vicidial agent screen
                                        {
                                            window.focus();
                                            alert_box("Placing call to:" + APIDiaL_array_detail[1] + " " + APIDiaL_array_detail[0]);
                                        }
                                        if (APIDiaL_array_detail[3] === 'YES')  // call preview
                                        {
                                            NeWManuaLDiaLCalLSubmiT('PREVIEW');
                                        }
                                        else
                                        {
                                            NeWManuaLDiaLCalLSubmiT('NOW');
                                        }
                                    }
                                }
                                stats_update();
                                if ((CheckDEADcall > 0) && (VD_live_customer_call === 1))
                                {
                                    dead_time++;


                                    if (CheckDEADcallON < 1)
                                    {
                                        if (document.images)
                                        {
                                            document.images['livecall'].src = image_livecall_DEAD.src;
                                        }
                                        CheckDEADcallON = 1;

                                        if ((xfer_in_call > 0) && (customer_3way_hangup_logging === 'ENABLED'))
                                        {
                                            customer_3way_hangup_counter_trigger = 1;
                                            customer_3way_hangup_counter = 1;
                                        }
                                    }
                                }
                                if (InGroupChange > 0)
                                {
                                    var external_blended = InGroupChangeBlend;
                                    external_igb_set_name = InGroupChangeName;
                                    manager_ingroups_set = 1;

                                    if ((external_blended === '1') && (dial_method !== 'INBOUND_MAN'))
                                    {
                                        VICIDiaL_closer_blended = '1';
                                    }

                                    if (external_blended === '0')
                                    {
                                        VICIDiaL_closer_blended = '0';
                                    }
                                }

                                var check_conf_array = check_ALL_array[1];
                                var live_conf_calls = check_conf_array[0];
                                var conf_chan_array = check_conf_array[1];
                                if ((conf_channels_xtra_display === 1) || (conf_channels_xtra_display === 0))
                                {
                                    if (live_conf_calls > 0)
                                    {
                                        var temp_blind_monitors = 0;
                                        var loop_ct = 0;
                                        var ARY_ct = 0;
                                        var LMAalter = 0;
                                        var LMAcontent_change = 0;
                                        var LMAcontent_match = 0;
                                        agentphonelive = 0;
                                        var conv_start = -1;
                                        var live_conf_HTML = "<font face=\"Arial,Helvetica\"><b>LIVE CALLS IN YOUR SESSION:</b></font><br /><table ><tr><td><font class=\"log_title\">#</font></td><td><font class=\"log_title\">REMOTE CHANNEL</font></td><td><font class=\"log_title\">HANGUP</font></td><td><font class=\"log_title\">VOLUME</font></td></tr>";
                                        if ((LMAcount > live_conf_calls) || (LMAcount < live_conf_calls) || (LMAforce > 0))
                                        {
                                            LMAe[0] = '';
                                            LMAe[1] = '';
                                            LMAe[2] = '';
                                            LMAe[3] = '';
                                            LMAe[4] = '';
                                            LMAe[5] = '';
                                            LMAcount = 0;
                                            LMAcontent_change++;
                                        }
                                        while (loop_ct < live_conf_calls)
                                        {
                                            loop_ct++;
                                            loop_s = loop_ct.toString();
                                            if (loop_s.match(/1$|3$|5$|7$|9$/))
                                            {
                                                var row_color = '#DDDDFF';
                                            }
                                            else
                                            {
                                                var row_color = '#CCCCFF';
                                            }
                                            var conv_ct = (loop_ct + conv_start);
                                            var channelfieldA = conf_chan_array[conv_ct];
                                            var regXFcred = new RegExp(flag_string, "g");
                                            var regRNnolink = new RegExp('Local/5' + taskconfnum, "g")
                                            if ((channelfieldA.match(regXFcred)) && (flag_channels > 0))
                                            {
                                                var chan_name_color = 'log_text_red';
                                            }
                                            else
                                            {
                                                var chan_name_color = 'log_text';
                                            }
                                            if ((HidEMonitoRSessionS === 1) && (channelfieldA.match(/ASTblind/)))
                                            {
                                                var hide_channel = 1;
                                                blind_monitoring_now++;
                                                temp_blind_monitors++;
                                                if (blind_monitoring_now === 1)
                                                {
                                                    blind_monitoring_now_trigger = 1;
                                                }
                                            }
                                            else
                                            {
                                                if (channelfieldA.match(regRNnolink))
                                                {
                                                    // do not show hangup or volume control links for recording channels
                                                    live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td>" + loop_ct + "</font></td><td><font class=\"" + chan_name_color + "\">" + channelfieldA + "</td><td>recording</td><td></td></tr>";
                                                }
                                                else
                                                {
                                                    if (volumecontrol_active !== 1)
                                                    {
                                                        live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td>" + loop_ct + "</font></td><td><font class=\"" + chan_name_color + "\">" + channelfieldA + "</td><td><a href=\"#\" onclick=\"livehangup_send_hangup('" + channelfieldA + "');return false;\">HANGUP</a></td><td></td></tr>";
                                                    }
                                                    else
                                                    {
                                                        live_conf_HTML = live_conf_HTML + "<tr bgcolor=\"" + row_color + "\"><td>" + loop_ct + "</font></td><td><font class=\"" + chan_name_color + "\">" + channelfieldA + "</td><td><a href=\"#\" onclick=\"livehangup_send_hangup('" + channelfieldA + "');return false;\">HANGUP</a></td><td><a href=\"#\" onclick=\"volume_control('UP','" + channelfieldA + "','');return false;\"><img src=\"./images/vdc_volume_up.gif\" border=\"0\" /></a>  <a href=\"#\" onclick=\"volume_control('DOWN','" + channelfieldA + "','');return false;\"><img src=\"./images/vdc_volume_down.gif\" border=\"0\" /></a>    <a href=\"#\" onclick=\"volume_control('MUTING','" + channelfieldA + "','');return false;\"><img src=\"./images/vdc_volume_MUTE.gif\" border=\"0\" /></a>  <a href=\"#\" onclick=\"volume_control('UNMUTE','" + channelfieldA + "','');return false;\"><img src=\"./images/vdc_volume_UNMUTE.gif\" border=\"0\" /></a></td></tr>";
                                                    }
                                                }
                                            }

                                            if (channelfieldA === lastcustchannel) {
                                                custchannellive++;
                                            }
                                            else
                                            {
                                                if (customerparked === 1)
                                                {
                                                    custchannellive++;
                                                }
                                                // allow for no customer hungup errors if call from another server
                                                if (server_ip === lastcustserverip)
                                                {
                                                    var nothing = '';
                                                }
                                                else
                                                {
                                                    custchannellive++;
                                                }
                                            }

                                            if (volumecontrol_active > 0)
                                            {
                                                if ((protocol !== 'EXTERNAL') && (protocol !== 'Local'))
                                                {
                                                    var regAGNTchan = new RegExp(protocol + '/' + extension, "g");
                                                    if ((channelfieldA.match(regAGNTchan)) && (agentchannel !== channelfieldA))
                                                    {
                                                        agentchannel = channelfieldA;

                                                        document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><img src=\"./images/vdc_volume_MUTE.gif\" border=\"0\" /></a>";
                                                    }
                                                }
                                                else
                                                {
                                                    if (agentchannel.length < 3)
                                                    {
                                                        agentchannel = channelfieldA;

                                                        document.getElementById("AgentMuteSpan").innerHTML = "<a href=\"#CHAN-" + agentchannel + "\" onclick=\"volume_control('MUTING','" + agentchannel + "','AgenT');return false;\"><img src=\"./images/vdc_volume_MUTE.gif\" border=\"0\" /></a>";
                                                    }
                                                }
                                            }


                                            if (!LMAe[ARY_ct])
                                            {
                                                LMAe[ARY_ct] = channelfieldA;
                                                LMAcontent_change++;
                                                LMAalter++;
                                            }
                                            else
                                            {
                                                if (LMAe[ARY_ct].length < 1)
                                                {
                                                    LMAe[ARY_ct] = channelfieldA;
                                                    LMAcontent_change++;
                                                    LMAalter++;
                                                }
                                                else
                                                {
                                                    if (LMAe[ARY_ct] === channelfieldA) {
                                                        LMAcontent_match++;
                                                    }
                                                    else {
                                                        LMAcontent_change++;
                                                        LMAe[ARY_ct] = channelfieldA;
                                                    }
                                                }
                                            }
                                            if (LMAalter > 0) {
                                                LMAcount++;
                                            }

                                            if (agentchannel === channelfieldA) {
                                                agentphonelive++;
                                            }

                                            ARY_ct++;
                                        }

                                        if (agentphonelive < 1) {
                                            agentchannel = '';
                                        }

                                        live_conf_HTML = live_conf_HTML + "</table>";

                                        if (LMAcontent_change > 0)
                                        {
                                            if (conf_channels_xtra_display === 1)
                                            {
                                                document.getElementById("outboundcallsspan").innerHTML = live_conf_HTML;
                                            }
                                        }
                                        nochannelinsession = 0;
                                        if (temp_blind_monitors < 1)
                                        {
                                            no_blind_monitors++;
                                            if (no_blind_monitors > 2)
                                            {
                                                blind_monitoring_now = 0;
                                            }
                                        }
                                    }
                                    else
                                    {
                                        LMAe[0] = '';
                                        LMAe[1] = '';
                                        LMAe[2] = '';
                                        LMAe[3] = '';
                                        LMAe[4] = '';
                                        LMAe[5] = '';
                                        LMAcount = 0;
                                        if (conf_channels_xtra_display === 1)
                                        {
                                            if (document.getElementById("outboundcallsspan").innerHTML.length > 2)
                                            {
                                                document.getElementById("outboundcallsspan").innerHTML = '';
                                            }
                                        }
                                        custchannellive = -99;
                                        nochannelinsession++;

                                        no_blind_monitors++;
                                        if (no_blind_monitors > 2)
                                        {
                                            blind_monitoring_now = 0;
                                        }
                                    }
                                }
                                delete xmlhttprequestcheckconf;
                                xmlhttprequestcheckconf = undefined;
                            }
                            else if (xmlhttprequestcheckconf && xmlhttprequestcheckconf.readyState === 4 && xmlhttprequestcheckconf.status !== 200)
                            {
                                // Cleanup  after AJAX Request returns error.
                                delete xmlhttprequestcheckconf;
                                xmlhttprequestcheckconf = undefined;
                            }
                        }
                    }
                }
                else
                {
                    if (xmlhttprequestcheckconf)
                    {
                        xmlhttprequestcheckconf_wait++;
                        if (xmlhttprequestcheckconf_wait >= conf_check_attempts)
                        {
                            // Abort AJAX Request, due to timeout.
                            // The handler must take care of cleanup.
                            xmlhttprequestcheckconf.abort();
                        }
                    }
                    if (xmlhttprequestcheckconf_wait >= conf_check_attempts_cleanup)
                    {
                        // In case the handler function fails to do cleanup, cleanup manually.
                        xmlhttprequestcheckconf_wait = 0;
                        delete xmlhttprequestcheckconf;
                        xmlhttprequestcheckconf = undefined;
                    }
                    else
                    {
                        xmlhttprequestcheckconf = undefined;
                    }
                }
            }
              function CustomCheckRequired() 
            {
                var xmlhttp = false;
                if (!xmlhttp && typeof XMLHttpRequest != 'undefined')
                {
                    xmlhttp = new XMLHttpRequest();
                }
                if (xmlhttp)
                {
                    var current_camp = campaign;

                    var customcheckrequired = "server_ip=" + server_ip + "&session_name=" + session_name + "&user=" + user + "&pass=" + pass + "&ACTION=custom_required&current_campaign=" + current_camp;
                    xmlhttp.open('POST', 'vdc_db_query.php');
                    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
                    xmlhttp.send(customcheckrequired);
                    xmlhttp.onreadystatechange = function()

                    {
                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
                        {
                            alert(xmlhttp.responseText);

                            var a_rslt = xmlhttp.responseText.split("\n");
                            var i = 0;
                            for (i = 0; i < a_rslt.length; i++)
                            {
                                var a_custom = a_rslt[i].split("---")

                                switch (a_custom[1])
                                {

                                    case 'TEXT':
                                        if (vcFormIFrame.document.getElementById(a_custom[0]).value == "") {
                                            alert('preencher - text');
                                        }
                                        ;
                                        break;
                                    case 'AREA':
                                        if (vcFormIFrame.document.getElementById(a_custom[0]).value == "") {
                                            alert('preencher - area');
                                        }
                                        ;
                                        break;
                                    case 'SELECT':
                                        alert(vcFormIFrame.document.getElementById(a_custom[0]).selectedIndex);
                                        if (vcFormIFrame.document.getElementById(a_custom[0]).selectedIndex == 0) {
                                            alert('preencher - select');
                                        }
                                        ;
                                        break;
                                    case 'MULTI':
                                        var valid = false;
                                        for (var p = 0; p < vcFormIFrame.document.getElementById(a_custom[0]).options.length; p++) {
                                            if (vcFormIFrame.document.getElementById(a_custom[0]).options[p].selected) {
                                                valid = true;
                                                break;
                                            }
                                        }
                                        break;
                                    case 'RADIO':
                                        if (vcFormIFrame.document.getElementById(a_custom[0]).value == "")
                                            alert("No radiobutton selected...");
                                        break;
                                    case 'CHECKBOX':
                                        var valid = false;
                                        for (var p = 0; p < vcFormIFrame.document.getElementById(a_custom[0]).options.length; p++) {
                                            if (vcFormIFrame.document.getElementById(a_custom[0]).options[p].checked) {
                                                valid = true;
                                                break;
                                            }
                                        }
                                        break;
                                    default:
                                        alert('ta tudo mamado' + a_custom[1]);
                                }
                            }

                        }
                    }
                    delete xmlhttp;
                }
            }
            
	function conf_channels_detail(divvar) 
		{
		if (divvar == 'SHOW')
			{
			conf_channels_xtra_display = 1;
			document.getElementById("busycallsdisplay").innerHTML = "<a href=\"#\"  onclick=\"conf_channels_detail('HIDE');\">Hide conference call channel information</a>";
			LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
			LMAcount=0;
			}
		else
			{
			conf_channels_xtra_display = 0;
            document.getElementById("busycallsdisplay").innerHTML = "<a href=\"#\" onclick=\"conf_channels_detail('SHOW');\">Show conference call channel information</a><br /><br />";
			document.getElementById("outboundcallsspan").innerHTML = '';
			LMAe[0]=''; LMAe[1]=''; LMAe[2]=''; LMAe[3]=''; LMAe[4]=''; LMAe[5]=''; 
			LMAcount=0;
			}
		}
function HotKeys(HKstate) 
		{
		if ( (HKstate == 'ON') && (HKbutton_allowed == 1) )
			{
			showDiv('HotKeyEntriesBox');
			hot_keys_active = 1;
            document.getElementById("hotkeysdisplay").innerHTML = "<a href=\"#\" onMouseOut=\"HotKeys('OFF')\"><img src=\"./images/vdc_XB_hotkeysactive.gif\" border=\"0\" alt=\"HOT KEYS ACTIVE\" /></a>";
			}
		else
			{
			hideDiv('HotKeyEntriesBox');
			hot_keys_active = 0;
            document.getElementById("hotkeysdisplay").innerHTML = "<a href=\"#\" onMouseOver=\"HotKeys('ON')\"><img src=\"./images/vdc_XB_hotkeysactive_OFF.gif\" border=\"0\" alt=\"HOT KEYS INACTIVE\" /></a>";
			}
		}
                
	function HidEGenDerPulldown()
		{
		if (hide_gender < 1)
			{
			var gIndex = 0;
			var genderIndex = document.getElementById("gender_list").selectedIndex;
			var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
			if (genderValue == 'M') {var gIndex = 1;}
			if (genderValue == 'F') {var gIndex = 2;}
			document.getElementById("GENDERhideFORieALT").innerHTML = '<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - Undefined</option><option value="M">M - Male</option><option value="F">F - Female</option></select>';
			document.getElementById("GENDERhideFORie").innerHTML = '';
			document.getElementById("gender_list").selectedIndex = gIndex;
			}
		}

	function ShoWGenDerPulldown()
		{
		if (hide_gender < 1)
			{
			var gIndex = 0;
			var genderIndex = document.getElementById("gender_list").selectedIndex;
			var genderValue =  document.getElementById('gender_list').options[genderIndex].value;
			if (genderValue == 'M') {var gIndex = 1;}
			if (genderValue == 'F') {var gIndex = 2;}
			document.getElementById("GENDERhideFORie").innerHTML = '<select size="1" name="gender_list" class="cust_form" id="gender_list"><option value="U">U - Undefined</option><option value="M">M - Male</option><option value="F">F - Female</option></select>';
			document.getElementById("GENDERhideFORieALT").innerHTML = '';
			document.getElementById("gender_list").selectedIndex = gIndex;
			}
		}

		
// ################################################################################
// Send the Manual Dial Next Number request
            function ManualDialNext(mdnCBid, mdnBDleadid, mdnDiaLCodE, mdnPhonENumbeR, mdnStagE, mdVendorid, mdgroupalias, mdtype)
            {
                redial_number = mdnPhonENumbeR;
                inOUT = 'OUT';
                all_record = 'NO';
                all_record_count = 0;
                if (dial_method == "INBOUND_MAN")
                {
                    auto_dial_level = 0;

                    if (VDRP_stage != 'PAUSED')
                    {
                        agent_log_id = AutoDial_ReSume_PauSe("VDADpause", '', '', '', "DIALNEXT", '1', 'NXDIAL');

                        //	PauseCodeSelect_submit("NXDIAL");
                    }
                    else
                    {
                        auto_dial_level = starting_dial_level;
                    }

                    $("#DiaLControl").html("<img src=\"./images/vdc_LB_pause_OFF.gif\" border=\"0\" alt=\" Pause \" /><img src=\"./images/vdc_LB_resume_OFF.gif\" border=\"0\" alt=\"Resume\" /><br /><img src=\"./images/vdc_LB_dialnextnumber_OFF.gif\" border=\"0\" alt=\"Dial Next Number\" />");

                }
                else
                {
                    $("#DiaLControl").html("<td><img src='/images/icons/control_end.png' /></td><td>Marcar Seguinte</td>");

                }
                var manual_dial_only_type_flag = '';
                if ((mdtype == 'ALT') || (mdtype == 'ADDR3'))
                {
                    agent_dialed_type = mdtype;
                    agent_dialed_number = mdnPhonENumbeR;
                    if (mdtype == 'ALT')
                    {
                        manual_dial_only_type_flag = 'ALTPhonE';
                    }
                    if (mdtype == 'ADDR3')
                    {
                        manual_dial_only_type_flag = 'AddresS3';
                    }

                    //TEST VER SCRIPT ANTES DE FAZER A CHAMADA

                    if (manual_dial_only_type_flag == 'ALTPhonE')
                    {
                        var manDiaLonly_num = document.vicidial_form.alt_phone.value;
                        lead_dial_number = document.vicidial_form.alt_phone.value;
                        dialed_number = lead_dial_number;
                        dialed_label = 'ALT';
                        WebFormRefresH('');
                    }
                    else
                    {
                        if (manual_dial_only_type_flag == 'AddresS3')
                        {
                            var manDiaLonly_num = document.vicidial_form.address3.value;
                            lead_dial_number = document.vicidial_form.address3.value;
                            dialed_number = lead_dial_number;
                            dialed_label = 'ADDR3';
                            WebFormRefresH('');
                        }
                        else
                        {
                            var manDiaLonly_num = document.vicidial_form.phone_number.value;
                            lead_dial_number = document.vicidial_form.phone_number.value;
                            dialed_number = lead_dial_number;
                            dialed_label = 'MAIN';
                            WebFormRefresH('');
                        }
                    }

                    //END TEST VER SCRIPT ANTES DE FAZER A CHAMADA

                }
                if (manual_preview_dial != "DISABLED") {
                    document.vicidial_form.LeadPreview.checked = true;
                } 

                if (document.vicidial_form.LeadPreview.checked)
                {
                    reselect_preview_dial = 1;
                    in_lead_preview_state = 1;
                    var man_preview = 'YES';
                    var man_status = "<a href=\"#\" onclick=\"ManualDialOnly('" + manual_dial_only_type_flag + "')\" class=\"btn\">Marcar Principal</a> ou <a href=\"#\" onclick=\"ManualDialOnly('ALTPhonE')\" class=\"btn\">Marcar Alternativo</a> ou <a href=\"#\" onclick=\"ManualDialOnly('AddresS3')\" class=\"btn\">Marcar Alternativo 2</a> ou <a href=\"#\" onclick=\"ManualDialSkip()\" class=\"btn\">Cancelar Ligação</a>";
                    if (manual_preview_dial == 'PREVIEW_ONLY')
                    {
                        var man_status = "<a href=\"#\" onclick=\"ManualDialOnly('" + manual_dial_only_type_flag + "')\" style='font-size:14px; font-weight: bold; text-decoration:underline'>Fazer Ligação</a>";
                    }
                }
                else
                {
                    reselect_preview_dial = 0;
                    var man_preview = 'NO';
                    var man_status = "Á espera de ligação";
                }


                if (cid_choice.length > 3)
                {
                    var call_cid = cid_choice;
                }
                else
                {
                    var call_cid = campaign_cid;
                    if (manual_dial_cid == 'AGENT_PHONE')
                    {
                        call_cid = outbound_cid;
                    }
                }
                if (prefix_choice.length > 0)
                {
                    var call_prefix = prefix_choice;
                }
                else
                {
                    var call_prefix = manual_dial_prefix;
                }



                $.post("vdc_db_query.php",
                        {server_ip: server_ip,
                            session_name: session_name,
                            ACTION: "manDiaLnextCaLL",
                            conf_exten: session_id,
                            user: user,
                            pass: pass,
                            campaign: campaign,
                            ext_context: ext_context,
                            dial_timeout: dial_timeout,
                            dial_prefix: call_prefix,
                            campaign_cid: call_cid,
                            preview: man_preview,
                            agent_log_id: agent_log_id,
                            callback_id: mdnCBid,
                            lead_id: mdnBDleadid,
                            phone_code: mdnDiaLCodE,
                            phone_number: mdnPhonENumbeR,
                            list_id: mdnLisT_id,
                            stage: mdnStagE,
                            use_internal_dnc: use_internal_dnc,
                            use_campaign_dnc: use_campaign_dnc,
                            omit_phone_code: omit_phone_code,
                            manual_dial_filter: manual_dial_filter,
                            vendor_lead_code: mdVendorid,
                            usegroupalias: mdgroupalias,
                            account: active_group_alias,
                            agent_dialed_number: agent_dialed_number,
                            agent_dialed_type: agent_dialed_type,
                            vtiger_callback_id: vtiger_callback_id,
                            dial_method: dial_method,
                            manual_dial_call_time_check: manual_dial_call_time_check,
                            portabilidade: portabilidade},
                function(data) {
                    {
                        var MDnextResponse_array = data;
                        MDnextCID = MDnextResponse_array[0];
                        LastCallCID = MDnextResponse_array[0];

                        var regMNCvar = new RegExp("HOPPER EMPTY", "ig");
                        var regMDFvarDNC = new RegExp("DNC", "ig");
                        var regMDFvarCAMP = new RegExp("CAMPLISTS", "ig");
                        var regMDFvarTIME = new RegExp("OUTSIDE", "ig");
                        if ((MDnextCID.match(regMNCvar)) || (MDnextCID.match(regMDFvarDNC)) || (MDnextCID.match(regMDFvarCAMP)) || (MDnextCID.match(regMDFvarTIME)))
                        {
                            var alert_displayed = 0;
                            trigger_ready = 1;
                            alt_phone_dialing = starting_alt_phone_dialing;
                            auto_dial_level = starting_dial_level;
                            MainPanelToFront();
                            CalLBacKsCounTCheck();

                            if (MDnextCID.match(regMNCvar))
                            {
                                alert_box("Já não existem mais contactos na campanha:\n" + campaign_name);
                                alert_displayed = 1;
                            }
                            if (MDnextCID.match(regMDFvarDNC))
                            {
                                alert_box("Este nº está na lista negra:\n" + mdnPhonENumbeR);
                                alert_displayed = 1;
                            }
                            if (MDnextCID.match(regMDFvarCAMP))
                            {
                                alert_box("Este nº não existe nesta campanha:\n" + mdnPhonENumbeR);
                                alert_displayed = 1;
                            }
                            if (MDnextCID.match(regMDFvarTIME))
                            {
                                alert_box("Está fora do horário de marcação:\n" + mdnPhonENumbeR);
                                alert_displayed = 1;
                            }
                            if (alert_displayed == 0)
                            {
                                alert_box("Erro não especificado:\n" + mdnPhonENumbeR + "|" + MDnextCID);
                                alert_displayed = 1;
                            }
                            if (alert_displayed)
                            {
                                return false;
                            }

                            if (starting_dial_level == 0)
                            {
                                $("#DiaLControl").html("<td><img src='/images/icons/control_end_blue.png' /></td><td><a href='#' onclick=\"ManualDialNext('','','','','','0');\">Marcar Seguinte</a></td>");

                            }
                            else
                            {
                                if (dial_method == "INBOUND_MAN")
                                {
                                    auto_dial_level = starting_dial_level;

                                    $("#DiaLControl").html("<img src=\"./images/vdc_LB_pause_OFF.gif\" border=\"0\" alt=\" Pause \" /><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\"./images/vdc_LB_resume.gif\" border=\"0\" alt=\"Resume\" /></a><br /><a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><img src=\"./images/vdc_LB_dialnextnumber.gif\" border=\"0\" alt=\"Dial Next Number\" /></a>");
                                }
                                else
                                {
                                    $("#ResumeControl").html(ResumeControl_auto_ON_HTML);
                                    $("#PauseControl").html(PauseControl_auto_OFF_HTML);

                                }
                                reselect_alt_dial = 0;
                            }
                        }
                        else
                        {
                            fronter = user;
                            LasTCID = MDnextResponse_array[0];
                            document.vicidial_form.lead_id.value = MDnextResponse_array[1];
                            LeaDPreVDispO = MDnextResponse_array[2];
                            document.vicidial_form.vendor_lead_code.value = MDnextResponse_array[4];
                            document.vicidial_form.list_id.value = MDnextResponse_array[5];
                            document.vicidial_form.gmt_offset_now.value = MDnextResponse_array[6];
                            document.vicidial_form.phone_code.value = MDnextResponse_array[7];
                            if ((disable_alter_custphone == 'Y') || (disable_alter_custphone == 'HIDE'))
                            {
                                var tmp_pn = document.getElementById("phone_numberDISP");
                                if (disable_alter_custphone == 'Y')
                                {
                                    tmp_pn.innerHTML = "<p>" + MDnextResponse_array[8] + "</p>";
                                }
                            }
                            document.vicidial_form.phone_number.value = MDnextResponse_array[8];
                            document.vicidial_form.title.value = MDnextResponse_array[9];
                            document.vicidial_form.first_name.value = MDnextResponse_array[10];
                            document.vicidial_form.middle_initial.value = MDnextResponse_array[11];
                            document.vicidial_form.last_name.value = MDnextResponse_array[12];
                            document.vicidial_form.address1.value = MDnextResponse_array[13];
                            document.vicidial_form.address2.value = MDnextResponse_array[14];
                            document.vicidial_form.address3.value = MDnextResponse_array[15];
                            document.vicidial_form.city.value = MDnextResponse_array[16];
                            document.vicidial_form.state.value = MDnextResponse_array[17];
                            document.vicidial_form.province.value = MDnextResponse_array[18];
                            document.vicidial_form.postal_code.value = MDnextResponse_array[19];
                            document.vicidial_form.country_code.value = MDnextResponse_array[20];
                            document.vicidial_form.gender.value = MDnextResponse_array[21];
                            document.vicidial_form.date_of_birth.value = MDnextResponse_array[22];
                            document.vicidial_form.alt_phone.value = MDnextResponse_array[23];
                            document.vicidial_form.email.value = MDnextResponse_array[24];
                            document.vicidial_form.security_phrase.value = MDnextResponse_array[25];
                            var REGcommentsNL = new RegExp("!N", "g");
                            MDnextResponse_array[26] = MDnextResponse_array[26].replace(REGcommentsNL, "\n");
                            document.vicidial_form.comments.value = MDnextResponse_array[26];
                            document.vicidial_form.called_count.value = MDnextResponse_array[27];
                            previous_called_count = MDnextResponse_array[27];
                            previous_dispo = MDnextResponse_array[2];
                            CBentry_time = MDnextResponse_array[28];
                            CBcallback_time = MDnextResponse_array[29];
                            CBuser = MDnextResponse_array[30];
                            CBcomments = MDnextResponse_array[31];
                            dialed_number = MDnextResponse_array[32];
                            dialed_label = MDnextResponse_array[33];
                            source_id = MDnextResponse_array[34];
                            document.vicidial_form.rank.value = MDnextResponse_array[35];
                            document.vicidial_form.owner.value = MDnextResponse_array[36];
                            //	CalL_ScripT_id					= MDnextResponse_array[37];
                            script_recording_delay = MDnextResponse_array[38];
                            CalL_XC_a_NuMber = MDnextResponse_array[39];
                            CalL_XC_b_NuMber = MDnextResponse_array[40];
                            CalL_XC_c_NuMber = MDnextResponse_array[41];
                            CalL_XC_d_NuMber = MDnextResponse_array[42];
                            CalL_XC_e_NuMber = MDnextResponse_array[43];
                            document.vicidial_form.entry_list_id.value = MDnextResponse_array[44];
                            custom_field_names = MDnextResponse_array[45];
                            custom_field_values = MDnextResponse_array[46];
                            custom_field_types = MDnextResponse_array[47];
                            var list_webform = MDnextResponse_array[48];
                            var list_webform_two = MDnextResponse_array[49];
                            post_phone_time_diff_alert_message = MDnextResponse_array[50];

                            document.vicidial_form.extra1.value = MDnextResponse_array[51];
                            document.vicidial_form.extra2.value = MDnextResponse_array[52];
                            document.vicidial_form.extra3.value = MDnextResponse_array[53];
                            document.vicidial_form.extra4.value = MDnextResponse_array[54];
                            document.vicidial_form.extra5.value = MDnextResponse_array[55];
                            document.vicidial_form.extra6.value = MDnextResponse_array[56];
                            document.vicidial_form.extra7.value = MDnextResponse_array[57];
                            document.vicidial_form.extra8.value = MDnextResponse_array[58];
                            document.vicidial_form.extra9.value = MDnextResponse_array[59];
                            document.vicidial_form.extra10.value = MDnextResponse_array[60];
                            document.vicidial_form.extra11.value = MDnextResponse_array[61];
                            document.vicidial_form.extra12.value = MDnextResponse_array[62];
                            document.vicidial_form.extra13.value = MDnextResponse_array[63];
                            document.vicidial_form.extra14.value = MDnextResponse_array[64];
                            document.vicidial_form.extra15.value = MDnextResponse_array[65];

                            timer_action = campaign_timer_action;
                            timer_action_message = campaign_timer_action_message;
                            timer_action_seconds = campaign_timer_action_seconds;
                            timer_action_destination = campaign_timer_action_destination;

                            lead_dial_number = dialed_number;
                            var dispnum = dialed_number;
                            var status_display_number = phone_number_format(dispnum);
                            $("#ResumeControl").html(ResumeControl_auto_OFF_HTML);
                            $("#MainStatuSSpan").html(" A Marcar: " + status_display_number + " ID: " + MDnextCID + "  " + man_status);
                            if ((dialed_label.length < 2) || (dialed_label == 'NONE')) {
                                dialed_label = 'MAIN';
                            }

                            if (hide_gender > 0)
                            {
                                document.vicidial_form.gender_list.value = MDnextResponse_array[21];
                            }
                            else
                            {
                                var gIndex = 0;
                                if (document.vicidial_form.gender.value == 'M') {
                                    var gIndex = 1;
                                }
                                if (document.vicidial_form.gender.value == 'F') {
                                    var gIndex = 2;
                                }
                                document.getElementById("gender_list").selectedIndex = gIndex;
                                var genderIndex = document.getElementById("gender_list").selectedIndex;
                                var genderValue = document.getElementById('gender_list').options[genderIndex].value;
                                document.vicidial_form.gender.value = genderValue;
                            }

                            LeaDDispO = '';

                            VDIC_web_form_address = VICIDiaL_web_form_address
                            VDIC_web_form_address_two = VICIDiaL_web_form_address_two
                            if (list_webform.length > 5) {
                                VDIC_web_form_address = list_webform;
                            }
                            if (list_webform_two.length > 5) {
                                VDIC_web_form_address_two = list_webform_two;
                            }

                            var regWFAcustom = new RegExp("^VAR", "ig");
                            if (VDIC_web_form_address.match(regWFAcustom))
                            {
                                TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address, 'YES', 'CUSTOM');
                                TEMP_VDIC_web_form_address = TEMP_VDIC_web_form_address.replace(regWFAcustom, '');
                            }
                            else
                            {
                                TEMP_VDIC_web_form_address = URLDecode(VDIC_web_form_address, 'YES', 'DEFAULT', '1');
                            }

                            if (VDIC_web_form_address_two.match(regWFAcustom))
                            {
                                TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two, 'YES', 'CUSTOM');
                                TEMP_VDIC_web_form_address_two = TEMP_VDIC_web_form_address_two.replace(regWFAcustom, '');
                            }
                            else
                            {
                                TEMP_VDIC_web_form_address_two = URLDecode(VDIC_web_form_address_two, 'YES', 'DEFAULT', '2');
                            }

                            document.getElementById("WebFormSpan").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormRefresH();\"><img src=\"./images/vdc_LB_webform.gif\" border=\"0\" alt=\"Web Form\" /></a>\n";
                            if (enable_second_webform > 0)
                            {
                                document.getElementById("WebFormSpanTwo").innerHTML = "<a href=\"" + TEMP_VDIC_web_form_address_two + "\" target=\"" + web_form_target + "\" onMouseOver=\"WebFormTwoRefresH();\"><img src=\"./images/vdc_LB_webform_two.gif\" border=\"0\" alt=\"Web Form 2\" /></a>\n";
                            }

                            if (CBentry_time.length > 2)
                            {
                                document.getElementById("CusTInfOSpaN").innerHTML = " <b> Call-back já existia </b>";
                                document.getElementById("CusTInfOSpaN").style.background = CusTCB_bgcolor;
                                document.getElementById("CBcommentsBoxA").innerHTML = CBentry_time;
                                document.getElementById("CBcommentsBoxB").innerHTML = CBcallback_time;
                                document.getElementById("CBcommentsBoxC").innerHTML = CBuser;
                                document.getElementById("CBcommentsBoxD").innerHTML = CBcomments;
                                showDiv('CBcommentsBox');
                            }

                            if (post_phone_time_diff_alert_message.length > 10)
                            {
                                document.getElementById("post_phone_time_diff_span_contents").innerHTML = "  " + post_phone_time_diff_alert_message + "<br />";
                                showDiv('post_phone_time_diff_span');
                            }

                            if (document.vicidial_form.LeadPreview.checked == false)
                            {
                                reselect_preview_dial = 0;
                                MD_channel_look = 1;
                                custchannellive = 1;

                                document.getElementById("HangupControl").innerHTML = "<td onclick='dialedcall_send_hangup();' style='cursor:pointer' width=32px><img src='/images/icons/control_eject_blue.png' /></td><td onclick='dialedcall_send_hangup();' style='cursor:pointer'><a href='#'  >Desligar Chamada</a></td>";

                                if ((LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE'))
                                {
                                    all_record = 'YES';
                                }

                                if ((view_scripts == 1) && (campaign_script.length > 0))
                                {
                                    var SCRIPT_web_form = 'http://127.0.0.1/testing.php';
                                    var TEMP_SCRIPT_web_form = URLDecode(SCRIPT_web_form, 'YES', 'DEFAULT', '1');

                                    if ((script_recording_delay > 0) && ((LIVE_campaign_recording == 'ALLCALLS') || (LIVE_campaign_recording == 'ALLFORCE')))
                                    {
                                        delayed_script_load = 'YES';
                                        RefresHScript('CLEAR');
                                    }
                                    else
                                    {
                                        load_script_contents();
                                    }
                                }
                                if (limesurvey_enabled === 1) {
                                    FormContentsLoad();
                                }
                                if (custom_fields_enabled > 0)
                                {
                                    FormContentsLoad();
                                }
                                if (get_call_launch == 'SCRIPT')
                                {
                                    if (delayed_script_load == 'YES')
                                    {
                                        load_script_contents();
                                    }
                                    ScriptPanelToFront();
                                }

                                if (get_call_launch == 'FORM')
                                {
                                    FormPanelToFront();
                                }


                                if (get_call_launch == 'WEBFORM')
                                {
                                    window.open(TEMP_VDIC_web_form_address, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
                                }
                                if (get_call_launch == 'WEBFORMTWO')
                                {
                                    window.open(TEMP_VDIC_web_form_address_two, web_form_target, 'toolbar=1,scrollbars=1,location=1,statusbar=1,menubar=1,resizable=1,width=640,height=450');
                                }

                            }
                            else
                            {
                                reselect_preview_dial = 1;
                            }
                        }

                        FormContentsLoad();
                    }
                }, "json").complete(function() {
                    if (manual_preview_dial != "DISABLED") { 
                        document.vicidial_form.LeadPreview.checked = false;
                            } 
                            });

                if (document.vicidial_form.LeadPreview.checked == false)
                {
                    active_group_alias = '';
                    cid_choice = '';
                    prefix_choice = '';
                    agent_dialed_number = '';
                    agent_dialed_type = '';
                    CalL_ScripT_id = '';
                }



            }
            
            

    // ################################################################################
    // W3C-compliant hotkeypress function to bind hotkeys defined in the campaign to dispositions
                function hotkeypress(evt)
                {
                    enter_disable();
                    if ((hot_keys_active == 1) && ((VD_live_customer_call == 1) || (MD_ring_secondS > 4)))
                    {
                        var e = evt ? evt : window.event;
                        if (!e)
                            return;
                        var key = 0;
                        if (e.keyCode) {
                            key = e.keyCode;
                        } // for moz/fb, if keyCode==0 use 'which'
                        else if (typeof(e.which) != 'undefined') {
                            key = e.which;
                        }
                        //
                        var HKdispo = hotkeys[String.fromCharCode(key)];
                        if (HKdispo)
                        {
                            if (focus_blur_enabled == 1)
                            {
                                document.inert_form.inert_button.focus();
                                document.inert_form.inert_button.blur();
                            }
                            CustomerData_update();
                            var HKdispo_ary = HKdispo.split(" ----- ");
                            if ((HKdispo_ary[0] == 'ALTPH2') || (HKdispo_ary[0] == 'ADDR3'))
                            {
                                if (document.vicidial_form.DiaLAltPhonE.checked == true)
                                {
                                    dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
                                }
                            }
                            else
                            {
                                HKdispo_display = 4;
                                HKfinish = 1;
                                document.getElementById("HotKeyDispo").innerHTML = HKdispo_ary[0] + " - " + HKdispo_ary[1];
                                showDiv('HotKeyActionBox');
                                hideDiv('HotKeyEntriesBox');
                                document.vicidial_form.DispoSelection.value = HKdispo_ary[0];
                                dialedcall_send_hangup('NO', 'YES', HKdispo_ary[0]);
                                if (custom_fields_enabled > 0)
                                {
                                    vcFormIFrame.document.form_custom_fields.submit();
                                }
                            }
                        }
                    }
                }

//### end of onkeypress functions


// ################################################################################
// Generate the Presets Chooser span content
            function generate_presets_pulldown()
            {
                showDiv('PresetsSelectBox');
                Presets_HTML = '';
                document.vicidial_form.PresetSelection.value = '';
                Presets_HTML = "<table cellpadding=\"5\" cellspacing=\"5\" width=\"400px\"><tr><td bgcolor=\"#CCCCFF\" width=\"400px\" valign=\"bottom\">";
                var loop_ct = 0;
                while (loop_ct < VD_preset_names_ct)
                {
                    Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('" + VARpreset_names[loop_ct] + "','" + VARpreset_numbers[loop_ct] + "','" + VARpreset_dtmfs[loop_ct] + "','" + VARpreset_hide_numbers[loop_ct] + "');return false;\">" + VARpreset_names[loop_ct];
                    if (VARpreset_hide_numbers[loop_ct] == 'N')
                    {
                        Presets_HTML = Presets_HTML + " - " + VARpreset_numbers[loop_ct];
                    }
                    Presets_HTML = Presets_HTML + "</a></b></font><br />";
                    loop_ct++;
                }

                if ((CalL_XC_a_NuMber.length > 0) || (CalL_XC_a_Dtmf.length > 0))
                {
                    Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D1','" + CalL_XC_a_NuMber + "','" + CalL_XC_a_Dtmf + "');return false;\">D1";
                    if (hide_xfer_number_to_dial == 'DISABLED')
                    {
                        Presets_HTML = Presets_HTML + " - " + CalL_XC_a_NuMber;
                    }
                    Presets_HTML = Presets_HTML + "</a></b></font><br />";
                }
                if ((CalL_XC_b_NuMber.length > 0) || (CalL_XC_b_Dtmf.length > 0))
                {
                    Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D2','" + CalL_XC_b_NuMber + "','" + CalL_XC_b_Dtmf + "');return false;\">D2";
                    if (hide_xfer_number_to_dial == 'DISABLED')
                    {
                        Presets_HTML = Presets_HTML + " - " + CalL_XC_b_NuMber;
                    }
                    Presets_HTML = Presets_HTML + "</a></b></font><br />";
                }
                if (CalL_XC_c_NuMber.length > 0)
                {
                    Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D3','" + CalL_XC_c_NuMber + "','');return false;\">D3";
                    if (hide_xfer_number_to_dial == 'DISABLED')
                    {
                        Presets_HTML = Presets_HTML + " - " + CalL_XC_c_NuMber;
                    }
                    Presets_HTML = Presets_HTML + "</a></b></font><br />";
                }
                if (CalL_XC_d_NuMber.length > 0)
                {
                    Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D4','" + CalL_XC_d_NuMber + "','');return false;\">D4";
                    if (hide_xfer_number_to_dial == 'DISABLED')
                    {
                        Presets_HTML = Presets_HTML + " - " + CalL_XC_d_NuMber;
                    }
                    Presets_HTML = Presets_HTML + "</a></b></font><br />";
                }
                if (CalL_XC_e_NuMber.length > 0)
                {
                    Presets_HTML = Presets_HTML + "<font size=\"3\" style=\"BACKGROUND-COLOR: #FFFFFF\"><b><a href=\"#\" onclick=\"PresetSelect_submit('D5','" + CalL_XC_e_NuMber + "','');return false;\">D5";
                    if (hide_xfer_number_to_dial == 'DISABLED')
                    {
                        Presets_HTML = Presets_HTML + " - " + CalL_XC_e_NuMber;
                    }
                    Presets_HTML = Presets_HTML + "</a></b></font><br />";
                }

                Presets_HTML = Presets_HTML + "</td></tr></table><br /><br /><table cellpadding=\"0\" cellspacing=\"0\"><tr><td width=\"330px\" align=\"left\"><font size=\"3\" style=\"BACKGROUND-COLOR: #CCCCFF\"><b><a href=\"#\" onclick=\"hideDiv('PresetsSelectBox');return false;\">Close [X]</a></b></font></td></tr></table>";
                document.getElementById("PresetsSelectBoxContent").innerHTML = Presets_HTML;
            }






// ################################################################################
// View Customer lead information
            function VieWLeaDInfO(VLI_lead_id, VLI_cb_id)
            {

                $.post("vdc_db_query.php",
                        {server_ip: server_ip,
                            session_name: session_name,
                            ACTION: "LEADINFOview",
                            format: "text",
                            user: user,
                            pass: pass,
                            conf_exten: session_id,
                            extension: extension,
                            protocol: protocol,
                            lead_id: VLI_lead_id,
                            campaign: campaign,
                            callback_id: VLI_cb_id},
                function(data) {
                    $("#LeaDInfOSpan").html(data);
                    showDiv('LeaDInfOBox');
                });

            }

            function ShoWTransferMain(showxfervar, showoffvar)
            {
                if (VU_vicidial_transfers == '1')
                {
                    XferAgentSelectLink();

                    if (showxfervar == 'ON')
                    {
                        if (alt_phone_dialing > 0) {
                        }
                        if ((auto_dial_level == 0) && (manual_dial_preview == 1)) {
                        }
                        HKbutton_allowed = 0;
                        showDiv('TransferMain');
                        document.getElementById("XferControl").innerHTML = "<td onclick=\"ShoWTransferMain('OFF','YES');\" style='cursor:pointer'><img src='/images/icons/control_repeat_blue.png' /></td><td onclick=\"ShoWTransferMain('OFF','YES');\" style='cursor:pointer'><a href=\"#\" >Transferir Chamada</a></td>";
                        if ((quick_transfer_button_enabled > 0) && (quick_transfer_button_locked < 1))
                        {
                            document.getElementById("QuickXfer").innerHTML = "<img src=\"./images/vdc_LB_quickxfer_OFF.gif\" border=\"0\" alt=\"QUICK TRANSFER\" />";
                        }
                    }
                    else
                    {
                        HKbutton_allowed = 1;
                        hideDiv('TransferMain');
                        hideDiv('agentdirectlink');
                        if (showoffvar == 'YES')
                        {
                            document.getElementById("XferControl").innerHTML = "<td onclick=\"ShoWTransferMain('ON');\" style='cursor:pointer'><img src='/images/icons/control_repeat_blue.png' /></td><td onclick=\"ShoWTransferMain('ON');\" style='cursor:pointer'><a href=\"#\" >Transferir Chamada</a></td>";

                            if ((quick_transfer_button == 'IN_GROUP') || (quick_transfer_button == 'LOCKED_IN_GROUP'))
                            {
                                document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRLOCAL','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;\"><img src=\"./images/vdc_LB_quickxfer.gif\" border=\"0\" alt=\"QUICK TRANSFER\" /></a>";
                            }
                            if ((quick_transfer_button == 'PRESET_1') || (quick_transfer_button == 'PRESET_2') || (quick_transfer_button == 'PRESET_3') || (quick_transfer_button == 'PRESET_4') || (quick_transfer_button == 'PRESET_5') || (quick_transfer_button == 'LOCKED_PRESET_1') || (quick_transfer_button == 'LOCKED_PRESET_2') || (quick_transfer_button == 'LOCKED_PRESET_3') || (quick_transfer_button == 'LOCKED_PRESET_4') || (quick_transfer_button == 'LOCKED_PRESET_5'))
                            {
                                document.getElementById("QuickXfer").innerHTML = "<a href=\"#\" onclick=\"mainxfer_send_redirect('XfeRBLIND','" + lastcustchannel + "','" + lastcustserverip + "','','','" + quick_transfer_button_locked + "');return false;\"><img src=\"./images/vdc_LB_quickxfer.gif\" border=\"0\" alt=\"QUICK TRANSFER\" /></a>";
                            }
                        }
                    }
                    if (three_way_call_cid == 'AGENT_CHOOSE')
                    {
                        if ((active_group_alias.length < 1) && (LIVE_default_group_alias.length > 1) && (LIVE_caller_id_number.length > 3))
                        {
                            active_group_alias = LIVE_default_group_alias;
                            cid_choice = LIVE_caller_id_number;
                        }
                        document.getElementById("XfeRDiaLGrouPSelecteD").innerHTML = "<font size=\"1\" face=\"Arial,Helvetica\">Group Alias: " + active_group_alias + "</font>";
                        document.getElementById("XfeRCID").innerHTML = "<a href=\"#\" onclick=\"GroupAliasSelectContent_create('1');\"><font size=\"1\" face=\"Arial,Helvetica\">Click Here to Choose a Group Alias</font></a>";
                    }
                    else
                    {
                        document.getElementById("XfeRCID").innerHTML = "";
                        document.getElementById("XfeRDiaLGrouPSelecteD").innerHTML = "";
                    }
                }
                else
                {
                    if (showxfervar != 'OFF')
                    {
                        alert_box('Não tem permissão para transferir chamadas');
                    }
                }
            }

            function MainPanelToFront(resumevar)
            {
                hideDiv('ScriptPanel');
                hideDiv('ScriptRefresH');
                hideDiv('FormRefresH');
                //showDiv('MainPanel');
                $("#tab-MainTable").tab("show");
                ShoWGenDerPulldown();

                if (resumevar != 'NO')
                {
                    if (alt_phone_dialing == 1)
                    {
                        buildDiv('DiaLDiaLAltPhonE');
                    }
                    else
                    {
                        clearDiv('DiaLDiaLAltPhonE');
                    }
                    if (auto_dial_level == 0)
                    {
                        if (auto_dial_alt_dial == 1)
                        {
                            auto_dial_alt_dial = 0;
                            document.getElementById("ResumeControl").innerHTML = ResumeControl_auto_OFF_HTML;
                            document.getElementById("PauseControl").innerHTML = PauseControl_auto_OFF_HTML;
                        }
                        else
                        {
                            document.getElementById("DiaLControl").innerHTML = DiaLControl_manual_HTML;
                            if (manual_dial_preview == 1)
                            {
                                buildDiv('DiaLLeaDPrevieW');
                            }
                        }
                    }
                    else
                    {
                        if (dial_method == "INBOUND_MAN")
                        {
                            document.getElementById("DiaLControl").innerHTML = "<img src=\"./images/vdc_LB_pause_OFF.gif\" border=\"0\" alt=\" Pause \" /><a href=\"#\" onclick=\"AutoDial_ReSume_PauSe('VDADready');\"><img src=\"./images/vdc_LB_resume.gif\" border=\"0\" alt=\"Resume\" /></a><br /><a href=\"#\" onclick=\"ManualDialNext('','','','','','0');\"><img src=\"./images/vdc_LB_dialnextnumber.gif\" border=\"0\" alt=\"Dial Next Number\" /></a>";



                            if (manual_dial_preview == 1)
                            {
                                buildDiv('DiaLLeaDPrevieW');
                            }
                        }
                        else
                        {
                            document.getElementById("ResumeControl").innerHTML = ResumeControl_auto_ON_HTML;
                            document.getElementById("PauseControl").innerHTML = PauseControl_auto_OFF_HTML;
                            clearDiv('DiaLLeaDPrevieW');
                        }
                    }
                }
            }

            function ScriptPanelToFront()
            {
                document.getElementById("CallbacksButtons").style.top = '560px';
                document.getElementById("CallbacksButtons").style.left = '40px';
                showDiv('ScriptPanel');
                showDiv('ScriptRefresH');
                hideDiv('FormRefresH');

                HidEGenDerPulldown();
            }

            function FormPanelToFront()
            {
                $("#tab-FormPanel").tab("show");
                //showDiv('FormPanel');
                showDiv('FormRefresH');
                hideDiv('ScriptPanel');
                hideDiv('ScriptRefresH');

                HidEGenDerPulldown();
            }

/* Função que controla as tabs do menu lateral */		
function ChangeTabs(obj_id_num) 
{
document.cookie = 0;
if (document.getElementById('tab'+obj_id_num).style.display == 'block') 
	{ 
		document.getElementById('tab'+obj_id_num).style.display = 'none'; 
		
		return;
	}
var i=1;	
while (document.getElementById("tab"+i))  
	{
		document.getElementById('tab'+i).style.display = 'none';
		if (i == obj_id_num) 
		{ 
			document.getElementById('tab'+obj_id_num).style.display = 'block';
			document.cookie = obj_id_num;
		}		
	i++;
	}

}		
/* Função que controla as tabs do menu lateral */	




function sugestoes(valor)
{
	var url = valor;
	window.open(url, "Janela", "status=no, width=900, height=350, menubar =no, titlebar=no, scrollbars=yes")
}

function vendas(user)
{
	var url = "../../sips/listaclientes.php?user=" + user;
	window.open (url, "status=yes, menubar =yes, titlebar=yes, scrollbars=yes");
}


function mail(user)
{
	var url = "../../sips/enviamail.php?user=" + user;
	window.open (url,"Janela","status=no, menubar =no, titlebar=yes, scrollbars=no, width=850, height=350");
	
}

// To disable f5
function disableF5(e) { if ((e.which || e.keyCode) == 116) e.preventDefault(); };
$(document).bind("keydown", disableF5);
   
     (function($) {
                $.widget("custom.combobox", {
                    _create: function() {
                        this.wrapper = $("<span>")
                                .addClass("custom-combobox")
                                .hide()
                                .insertAfter(this.element);

                        this.element.hide();
                        this._createAutocomplete();
                    },
                    _createAutocomplete: function() {
                        var selected = this.element.children(":selected"),
                                value = selected.val() ? selected.text() : "";

                        this.input = $("<input type='text'>")
                                .appendTo(this.wrapper)
                                .val(value)
                                .addClass("custom-combobox-input ui-widget ui-widget-content")
                                .autocomplete({
                            delay: 300,
                            minLength: 3,
                            source: $.proxy(this, "_source")
                        })
                                .tooltip({
                            tooltipClass: "ui-state-highlight"
                        });

                        this._on(this.input, {
                            autocompleteselect: function(event, ui) {
                                ui.item.option.selected = true;
                                this._trigger("select", event, {
                                    item: ui.item.option
                                });
                            },
                            autocompletechange: "_removeIfInvalid"
                        });
                   },
                    _source: function(request, response) {
                        var matcher = new RegExp($.ui.autocomplete.escapeRegex(request.term), "i");
                        response(this.element.children("option").map(function() {
                            var text = $(this).text();
                            if (this.value && (!request.term || matcher.test(text)))
                                return {
                                    label: text,
                                    value: text,
                                    option: this
                                };
                        }));
                    },
                    _removeIfInvalid: function(event, ui) {

                        // Selected an item, nothing to do
                        if (ui.item) {
                            return;
                        }

                        // Search for a match (case-insensitive)
                        var value = this.input.val(),
                                valueLowerCase = value.toLowerCase(),
                                valid = false;
                        this.element.children("option").each(function() {
                            if ($(this).text().toLowerCase() === valueLowerCase) {
                                this.selected = valid = true;
                                return false;
                            }
                        });

                        // Found a match, nothing to do
                        if (valid) {
                            return;
                        }

                        // Remove invalid value
                        this.input
                                .val("")
                                .attr("title", value + "não é nenhum utilizador valido.")
                                .tooltip("open");
                        this.element.val("");
                        this._delay(function() {
                            this.input.tooltip("close").attr("title", "");
                        }, 2500);
                        this.input.data("ui-autocomplete").term = "";
                    },
                    _destroy: function() {
                        this.wrapper.remove();
                        this.element.show();
                    }
                });
            })(jQuery);


            $(function() {

                $("#cb_other_username").combobox().hide();

                $("#cb_other_user").on("change",
                        function() {
                            if ($(this).prop("checked")) {
                                $("#cb_pessoal").prop("checked", true);
                                $("#cb_geral").prop("disabled", true);
                                $(".custom-combobox").show();
                            } else {
                                $("#cb_geral").prop("disabled", false);
                            if(my_callback_option != "CHECKED"){$('#cb_geral').prop('checked',true);}
                                $(".custom-combobox").hide();
                            }
                        });
            
                    $("#data_callback").datetimepicker({
                                timeFormat: 'hh:mm',
                                separator: "     ",
                                hour: 08,
                                minute: 00,
                                minDate: 0
                            });
                    
                    
                        $("#MDPhonENumbeR").keypress(function(e) {
                            if (e.which == 13) {
                                NeWManuaLDiaLCalLSubmiT('PREVIEW');
                                $("#MDPhonENumbeR")[0].blur();
                            }
                    });
                    });
                       
                        
            function mpass(user) {
                var url = "alterarpassword.php?user=" + user;
                testwindow = window.open(url, "mywindow", "status=no, menubar=no, titlebar=no, location=1,status=1,scrollbars=1,width=400,height=300");
                testwindow.moveTo(300, 250);
            }

            function ultimoscontactos(user)
            {
                var url = "ultimoscontactos.php?user=" + user;
                testwindow = window.open(url, "Janela", "status=no, width=760, height=500, menubar=no, titlebar=no, scrollbars=yes");
                testwindow.moveTo(300, 250);
            }
            
            $(function() {
                            $("#cb_date_1").datepicker({
                                changeMonth: true,
                                changeYear: true,
                                dateFormat: "yy-mm-dd",
                                onClose: function(selectedDate) {
                                    $("#cb_date_2").datepicker("option", "minDate", selectedDate);
                                }
                            });
                            $("#cb_date_2").datepicker({
                                changeMonth: true,
                                changeYear: true,
                                dateFormat: "yy-mm-dd",
                                onClose: function(selectedDate) {
                                    $("#cb_date_1").datepicker("option", "maxDate", selectedDate);
                                }
                            });
                        });