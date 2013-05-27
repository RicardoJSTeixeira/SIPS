<?
require "../functions.php";
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$PHP_AUTH_USER=$_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW=$_SERVER["PHP_AUTH_PW"];

        function help($where, $text) {
            return '<a onclick="openNewWindow(\'../admin.php?ADD=99999#' . $where . '\')">' . $text . '</a>';
        }
           
            function only_array_values($array){
                $values=array();
                foreach ($array as $value) {
                    $values[$value]=$value;
                }
                return $values;
            }

#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

require(ROOT . "ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-tagmanager.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-tagmanager.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/colorpicker.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
    <style>
        [name="did_route"],[name="filter_action"]{
            border-color:rgba(82, 168, 236, 0.8);
        }
        .chzn-select{
            width: 350px;
        }
        select.min-input {
            width: 50px;
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
        #rank > tbody td{
            vertical-align: middle;
        }
        #rank > tbody td:nth-child(2),#rank > tbody td:nth-child(3),#rank > tbody td:nth-child(4){
            text-align: center;
        }
        #no_agent_action{
            border-color: rgba(82, 168, 236, 0.8);
        }
    </style>
    </head>
    <body>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <? 
        $IGhandle_method_list = array('CID','CIDLOOKUP','CIDLOOKUPRL','CIDLOOKUPRC','CIDLOOKUPALT','CIDLOOKUPRLALT','CIDLOOKUPRCALT','CIDLOOKUPADDR3','CIDLOOKUPRLADDR3','CIDLOOKUPRCADDR3','CIDLOOKUPALTADDR3','CIDLOOKUPRLALTADDR3','CIDLOOKUPRCALTADDR3','ANI','ANILOOKUP','ANILOOKUPRL','VIDPROMPT','VIDPROMPTLOOKUP','VIDPROMPTLOOKUPRL','VIDPROMPTLOOKUPRC','CLOSER','3DIGITID','4DIGITID','5DIGITID','10DIGITID');

	$IGsearch_method_list = array('LB'=>'Equilibrado','LO'=>'Equilibrado com Transbordo','SO','Só no próprio Servidor');

        $stmt="SELECT allowed_campaigns,allowed_reports,admin_viewable_groups,admin_viewable_call_times from vicidial_user_groups where user_group='$LOGuser_group';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$LOGallowed_campaigns =			$row[0];
	$LOGallowed_reports =			$row[1];
	$LOGadmin_viewable_groups =		$row[2];
	$LOGadmin_viewable_call_times =	$row[3];

	$LOGallowed_campaignsSQL='';
	$whereLOGallowed_campaignsSQL='';
	if ( (!eregi("-ALL",$LOGallowed_campaigns)) )
		{
		$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
		$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
		$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
		$whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
		}
        
                $regexLOGallowed_campaigns = " $LOGallowed_campaigns ";

	$admin_viewable_groupsALL=0;
	$LOGadmin_viewable_groupsSQL='';
	$whereLOGadmin_viewable_groupsSQL='';
	$valLOGadmin_viewable_groupsSQL='';
	$vmLOGadmin_viewable_groupsSQL='';
	if ( (!eregi("--ALL--",$LOGadmin_viewable_groups)) and (strlen($LOGadmin_viewable_groups) > 3) )
		{
		$rawLOGadmin_viewable_groupsSQL = preg_replace("/ -/",'',$LOGadmin_viewable_groups);
		$rawLOGadmin_viewable_groupsSQL = preg_replace("/ /","','",$rawLOGadmin_viewable_groupsSQL);
		$LOGadmin_viewable_groupsSQL = "and user_group IN('---ALL---','$rawLOGadmin_viewable_groupsSQL')";
		$whereLOGadmin_viewable_groupsSQL = "where user_group IN('---ALL---','$rawLOGadmin_viewable_groupsSQL')";
		$valLOGadmin_viewable_groupsSQL = "and val.user_group IN('---ALL---','$rawLOGadmin_viewable_groupsSQL')";
		$vmLOGadmin_viewable_groupsSQL = "and vm.user_group IN('---ALL---','$rawLOGadmin_viewable_groupsSQL')";
		}
	else 
		{$admin_viewable_groupsALL=1;}
	$regexLOGadmin_viewable_groups = " $LOGadmin_viewable_groups ";

	$LOGadmin_viewable_call_timesSQL='';
	$whereLOGadmin_viewable_call_timesSQL='';
	if ( (!eregi("--ALL--",$LOGadmin_viewable_call_times)) and (strlen($LOGadmin_viewable_call_times) > 3) )
		{
		$rawLOGadmin_viewable_call_timesSQL = preg_replace("/ -/",'',$LOGadmin_viewable_call_times);
		$rawLOGadmin_viewable_call_timesSQL = preg_replace("/ /","','",$rawLOGadmin_viewable_call_timesSQL);
		$LOGadmin_viewable_call_timesSQL = "and call_time_id IN('---ALL---','$rawLOGadmin_viewable_call_timesSQL')";
		$whereLOGadmin_viewable_call_timesSQL = "where call_time_id IN('---ALL---','$rawLOGadmin_viewable_call_timesSQL')";
		}
	$regexLOGadmin_viewable_call_times = " $LOGadmin_viewable_call_times ";
        
        $voice_mail_list = array();
        $query = "SELECT voicemail_id,fullname,email,extension from phones where active='Y'  $LOGadmin_viewable_groupsSQL order by voicemail_id ASC";
        $rslt = mysql_query($query, $link);
        while ($row1 = mysql_fetch_row($rslt)) {
            $voice_mail_list[$row1[0]] = "$row1[3] - $row1[1]";
        }
        
        $stmt="SELECT campaign_id,campaign_name from vicidial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id;";
	$rslt=mysql_query($stmt, $link);
	$IGcampaigns_to_print = mysql_num_rows($rslt);
	$IGcampaign_id_list=array();
	$i=0;
	while ($i < $IGcampaigns_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$IGcampaign_id_list [$row[0]]= $row[1];
		$i++;
		}
                
        $stmt = "SELECT menu_id,menu_name from vicidial_call_menu $LOGadmin_viewable_groupsSQL order by menu_id limit 10000;";
        $rslt = mysql_query($stmt, $link);
        $menus_to_print = mysql_num_rows($rslt);
        $call_menu_list = array();
        $i = 0;
        while ($i < $menus_to_print) {
            $row = mysql_fetch_row($rslt);
            $call_menu_list[$row[0]] =$row[1];
            $i++;
        }
        
        $stmt="SELECT group_id,group_name from vicidial_inbound_groups where active='Y' and group_id NOT LIKE \"AGENTDIRECT%\" $LOGadmin_viewable_groupsSQL order by group_id;";
	$rslt=mysql_query($stmt, $link);
	$ingroups_to_print = mysql_num_rows($rslt);
	$ingroup_list=array();
	$i=0;
	while ($i < $ingroups_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$ingroup_list[$row[0]]= $row[1];
		$i++;
		}
                
                $stmt="SELECT did_pattern,did_description,did_route from vicidial_inbound_dids where did_active='Y' $LOGadmin_viewable_groupsSQL order by did_pattern;";
	$rslt=mysql_query($stmt, $link);
	$dids_to_print = mysql_num_rows($rslt);
	$did_list=array();
	$i=0;
	while ($i < $dids_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$did_list[$row[0]]= "$row[0] - $row[1] - $row[2]";
		$i++;
		}
        
        if ($ADD == 4111) {
            if ((strlen($group_name) < 2) or (strlen($group_color) < 2)) {
?>
        <script>
            $(function(){makeAlert("#wr","Grupo Inbound não modificado","Nome do grupo e cor têm que ser superior a 2 caracteres",1,true,false);});
        </script>
            <?php
            } else {
                $p = 0;
                $qc_statuses_ct = count($qc_statuses);
                while ($p < $qc_statuses_ct) {
                    $QC_statuses .= " $qc_statuses[$p]";
                    $p++;
                }
                $p = 0;
                $qc_lists_ct = count($qc_lists);
                while ($p < $qc_lists_ct) {
                    $QC_lists .= " $qc_lists[$p]";
                    $p++;
                }

                if (strlen($QC_statuses) > 0) {
                    $QC_statuses .= " -";
                }
                if (strlen($QC_lists) > 0) {
                    $QC_lists .= " -";
                }


                if ($no_agent_action == "INGROUP") {
                    if (isset($_GET["IGgroup_id_no_agent_action"])) {
                        $IGgroup_id = $_GET["IGgroup_id_no_agent_action"];
                    } elseif (isset($_POST["IGgroup_id_no_agent_action"])) {
                        $IGgroup_id = $_POST["IGgroup_id_no_agent_action"];
                    }
                    if (isset($_GET["IGhandle_method_no_agent_action"])) {
                        $IGhandle_method = $_GET["IGhandle_method_no_agent_action"];
                    } elseif (isset($_POST["IGhandle_method_no_agent_action"])) {
                        $IGhandle_method = $_POST["IGhandle_method_no_agent_action"];
                    }
                    if (isset($_GET["IGsearch_method_no_agent_action"])) {
                        $IGsearch_method = $_GET["IGsearch_method_no_agent_action"];
                    } elseif (isset($_POST["IGsearch_method_no_agent_action"])) {
                        $IGsearch_method = $_POST["IGsearch_method_no_agent_action"];
                    }
                    if (isset($_GET["IGlist_id_no_agent_action"])) {
                        $IGlist_id = $_GET["IGlist_id_no_agent_action"];
                    } elseif (isset($_POST["IGlist_id_no_agent_action"])) {
                        $IGlist_id = $_POST["IGlist_id_no_agent_action"];
                    }
                    if (isset($_GET["IGcampaign_id_no_agent_action"])) {
                        $IGcampaign_id = $_GET["IGcampaign_id_no_agent_action"];
                    } elseif (isset($_POST["IGcampaign_id_no_agent_action"])) {
                        $IGcampaign_id = $_POST["IGcampaign_id_no_agent_action"];
                    }
                    if (isset($_GET["IGphone_code_no_agent_action"])) {
                        $IGphone_code = $_GET["IGphone_code_no_agent_action"];
                    } elseif (isset($_POST["IGphone_code_no_agent_action"])) {
                        $IGphone_code = $_POST["IGphone_code_no_agent_action"];
                    }
                    if (strlen($IGhandle_method) < 1) {
                        if (isset($_GET["IGhandle_method_"])) {
                            $IGhandle_method = $_GET["IGhandle_method_"];
                        } elseif (isset($_POST["IGhandle_method_"])) {
                            $IGhandle_method = $_POST["IGhandle_method_"];
                        }
                    }
                    if (strlen($IGsearch_method) < 1) {
                        if (isset($_GET["IGsearch_method_"])) {
                            $IGsearch_method = $_GET["IGsearch_method_"];
                        } elseif (isset($_POST["IGsearch_method_"])) {
                            $IGsearch_method = $_POST["IGsearch_method_"];
                        }
                    }
                    if (strlen($IGlist_id) < 1) {
                        if (isset($_GET["IGlist_id_"])) {
                            $IGlist_id = $_GET["IGlist_id_"];
                        } elseif (isset($_POST["IGlist_id_"])) {
                            $IGlist_id = $_POST["IGlist_id_"];
                        }
                    }
                    if (strlen($IGcampaign_id) < 1) {
                        if (isset($_GET["IGcampaign_id_"])) {
                            $IGcampaign_id = $_GET["IGcampaign_id_"];
                        } elseif (isset($_POST["IGcampaign_id_"])) {
                            $IGcampaign_id = $_POST["IGcampaign_id_"];
                        }
                    }
                    if (strlen($IGphone_code) < 1) {
                        if (isset($_GET["IGphone_code_"])) {
                            $IGphone_code = $_GET["IGphone_code_"];
                        } elseif (isset($_POST["IGphone_code_"])) {
                            $IGphone_code = $_POST["IGphone_code_"];
                        }
                    }

                    $no_agent_action_value = "$IGgroup_id,$IGhandle_method,$IGsearch_method,$IGlist_id,$IGcampaign_id,$IGphone_code";
                    if ($DB) {
                        echo "\nNANQUE:     |$no_agent_action_value|$no_agent_action|\n";
                    }
                }

                if ($no_agent_action == "EXTENSION") {
                    if (isset($_GET["EXextension_no_agent_action"])) {
                        $EXextension = $_GET["EXextension_no_agent_action"];
                    } elseif (isset($_POST["EXextension_no_agent_action"])) {
                        $EXextension = $_POST["EXextension_no_agent_action"];
                    }
                    if (isset($_GET["EXcontext_no_agent_action"])) {
                        $EXcontext = $_GET["EXcontext_no_agent_action"];
                    } elseif (isset($_POST["EXcontext_no_agent_action"])) {
                        $EXcontext = $_POST["EXcontext_no_agent_action"];
                    }

                    $no_agent_action_value = "$EXextension,$EXcontext";
                }

                $no_agent_action_value = ereg_replace("[^-\/\|\_\#\*\,\.\_0-9a-zA-Z]", "", $no_agent_action_value);

                ?>
        <script>
            $(function(){makeAlert("#wr","Grupo Inbound Modificado com Sucesso","",4,true,false);});
        </script>
            <?php

                $stmt = "UPDATE vicidial_inbound_groups set group_name='$group_name', group_color='$group_color', active='$active', web_form_address='" . mysql_real_escape_string($web_form_address) . "', voicemail_ext='$voicemail_ext', next_agent_call='$next_agent_call', fronter_display='$fronter_display', ingroup_script='$script_id', get_call_launch='$get_call_launch', xferconf_a_dtmf='$xferconf_a_dtmf',xferconf_a_number='$xferconf_a_number', xferconf_b_dtmf='$xferconf_b_dtmf',xferconf_b_number='$xferconf_b_number',drop_action='$drop_action',drop_call_seconds='$drop_call_seconds',drop_exten='$drop_exten',call_time_id='$call_time_id',after_hours_action='$after_hours_action',after_hours_message_filename='$after_hours_message_filename',after_hours_exten='$after_hours_exten',after_hours_voicemail='$after_hours_voicemail',welcome_message_filename='$welcome_message_filename',moh_context='$moh_context',onhold_prompt_filename='$onhold_prompt_filename',prompt_interval='$prompt_interval',agent_alert_exten='$agent_alert_exten',agent_alert_delay='$agent_alert_delay',default_xfer_group='$default_xfer_group',queue_priority='$queue_priority',drop_inbound_group='$drop_inbound_group',ingroup_recording_override='$ingroup_recording_override',ingroup_rec_filename='$ingroup_rec_filename',afterhours_xfer_group='$afterhours_xfer_group',qc_enabled='$qc_enabled',qc_statuses='$QC_statuses',qc_shift_id='$qc_shift_id',qc_get_record_launch='$qc_get_record_launch',qc_show_recording='$qc_show_recording',qc_web_form_address='$qc_web_form_address',qc_script='$qc_script',play_place_in_line='$play_place_in_line',play_estimate_hold_time='$play_estimate_hold_time',hold_time_option='$hold_time_option',hold_time_option_seconds='$hold_time_option_seconds',hold_time_option_exten='$hold_time_option_exten',hold_time_option_voicemail='$hold_time_option_voicemail',hold_time_option_xfer_group='$hold_time_option_xfer_group',hold_time_option_callback_filename='$hold_time_option_callback_filename',hold_time_option_callback_list_id='$hold_time_option_callback_list_id',hold_recall_xfer_group='$hold_recall_xfer_group',no_delay_call_route='$no_delay_call_route',play_welcome_message='$play_welcome_message',answer_sec_pct_rt_stat_one='$answer_sec_pct_rt_stat_one',answer_sec_pct_rt_stat_two='$answer_sec_pct_rt_stat_two',default_group_alias='$default_group_alias',no_agent_no_queue='$no_agent_no_queue',no_agent_action='$no_agent_action',no_agent_action_value='$no_agent_action_value',web_form_address_two='" . mysql_real_escape_string($web_form_address_two) . "',timer_action='$timer_action',timer_action_message='$timer_action_message',timer_action_seconds='$timer_action_seconds',start_call_url='" . mysql_real_escape_string($start_call_url) . "',dispo_call_url='" . mysql_real_escape_string($dispo_call_url) . "',xferconf_c_number='$xferconf_c_number',xferconf_d_number='$xferconf_d_number',xferconf_e_number='$xferconf_e_number',ignore_list_script_override='$ignore_list_script_override',extension_appended_cidname='$extension_appended_cidname',uniqueid_status_display='$uniqueid_status_display',uniqueid_status_prefix='$uniqueid_status_prefix',hold_time_option_minimum='$hold_time_option_minimum',hold_time_option_press_filename='$hold_time_option_press_filename',hold_time_option_callmenu='$hold_time_option_callmenu',onhold_prompt_no_block='$onhold_prompt_no_block',onhold_prompt_seconds='$onhold_prompt_seconds',hold_time_option_no_block='$hold_time_option_no_block',hold_time_option_prompt_seconds='$hold_time_option_prompt_seconds',hold_time_second_option='$hold_time_second_option',hold_time_third_option='$hold_time_third_option',wait_hold_option_priority='$wait_hold_option_priority',wait_time_option='$wait_time_option',wait_time_second_option='$wait_time_second_option',wait_time_third_option='$wait_time_third_option',wait_time_option_seconds='$wait_time_option_seconds',wait_time_option_exten='$wait_time_option_exten',wait_time_option_voicemail='$wait_time_option_voicemail',wait_time_option_xfer_group='$wait_time_option_xfer_group',wait_time_option_callmenu='$wait_time_option_callmenu',wait_time_option_callback_filename='$wait_time_option_callback_filename',wait_time_option_callback_list_id='$wait_time_option_callback_list_id',wait_time_option_press_filename='$wait_time_option_press_filename',wait_time_option_no_block='$wait_time_option_no_block',wait_time_option_prompt_seconds='$wait_time_option_prompt_seconds',timer_action_destination='$timer_action_destination',calculate_estimated_hold_seconds='$calculate_estimated_hold_seconds',add_lead_url='" . mysql_real_escape_string($add_lead_url) . "',eht_minimum_prompt_filename='$eht_minimum_prompt_filename',eht_minimum_prompt_no_block='$eht_minimum_prompt_no_block',eht_minimum_prompt_seconds='$eht_minimum_prompt_seconds',on_hook_ring_time='$on_hook_ring_time' where group_id='$group_id';";
                $rslt = mysql_query($stmt, $link);

                ### LOG INSERTION Admin Log Table ###
                $SQL_log = "$stmt|";
                $SQL_log = ereg_replace(';', '', $SQL_log);
                $SQL_log = addslashes($SQL_log);
                $stmt = "INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='INGROUPS', event_type='MODIFY', record_id='$group_id', event_code='ADMIN MODIFY INGROUP', event_sql=\"$SQL_log\", event_notes='';";
                if ($DB) {
                    echo "|$stmt|\n";
                }
                $rslt = mysql_query($stmt, $link);
            }
        }




        if (($SSadmin_modify_refresh > 1) and ($modify_refresh_set < 1)) {
            $modify_url = "$PHP_SELF?group_id=$group_id";
            $modify_footer_refresh = 1;
        }

        $stmt = "SELECT group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,drop_call_seconds,drop_action,drop_exten,call_time_id,after_hours_action,after_hours_message_filename,after_hours_exten,after_hours_voicemail,welcome_message_filename,moh_context,onhold_prompt_filename,prompt_interval,agent_alert_exten,agent_alert_delay,default_xfer_group,queue_priority,drop_inbound_group,ingroup_recording_override,ingroup_rec_filename,afterhours_xfer_group,qc_enabled,qc_statuses,qc_shift_id,qc_get_record_launch,qc_show_recording,qc_web_form_address,qc_script,play_place_in_line,play_estimate_hold_time,hold_time_option,hold_time_option_seconds,hold_time_option_exten,hold_time_option_voicemail,hold_time_option_xfer_group,hold_time_option_callback_filename,hold_time_option_callback_list_id,hold_recall_xfer_group,no_delay_call_route,play_welcome_message,answer_sec_pct_rt_stat_one,answer_sec_pct_rt_stat_two,default_group_alias,no_agent_no_queue,no_agent_action,no_agent_action_value,web_form_address_two,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,ignore_list_script_override,extension_appended_cidname,uniqueid_status_display,uniqueid_status_prefix,hold_time_option_minimum,hold_time_option_press_filename,hold_time_option_callmenu,onhold_prompt_no_block,onhold_prompt_seconds,hold_time_option_no_block,hold_time_option_prompt_seconds,hold_time_second_option,hold_time_third_option,wait_hold_option_priority,wait_time_option,wait_time_second_option,wait_time_third_option,wait_time_option_seconds,wait_time_option_exten,wait_time_option_voicemail,wait_time_option_xfer_group,wait_time_option_callmenu,wait_time_option_callback_filename,wait_time_option_callback_list_id,wait_time_option_press_filename,wait_time_option_no_block,wait_time_option_prompt_seconds,timer_action_destination,calculate_estimated_hold_seconds,add_lead_url,eht_minimum_prompt_filename, eht_minimum_prompt_no_block, eht_minimum_prompt_seconds,on_hook_ring_time from vicidial_inbound_groups where group_id='$group_id';";

        $rslt = mysql_query($stmt, $link);
        $row = mysql_fetch_row($rslt);
        $group_name = $row[1];
        $group_color = $row[2];
        $active = $row[3];
        $web_form_address = stripslashes($row[4]);
        $voicemail_ext = $row[5];
        $next_agent_call = $row[6];
        $fronter_display = $row[7];
        $script_id = $row[8];
        $get_call_launch = $row[9];
        $xferconf_a_dtmf = $row[10];
        $xferconf_a_number = $row[11];
        $xferconf_b_dtmf = $row[12];
        $xferconf_b_number = $row[13];
        $drop_call_seconds = $row[14];
        $drop_action = $row[15];
        $drop_exten = $row[16];
        $call_time_id = $row[17];
        $after_hours_action = $row[18];
        $after_hours_message_filename = $row[19];
        $after_hours_exten = $row[20];
        $after_hours_voicemail = $row[21];
        $welcome_message_filename = $row[22];
        $moh_context = $row[23];
        $onhold_prompt_filename = $row[24];
        $prompt_interval = $row[25];
        $agent_alert_exten = $row[26];
        $agent_alert_delay = $row[27];
        $default_xfer_group = $row[28];
        $queue_priority = $row[29];
        $drop_inbound_group = $row[30];
        $ingroup_recording_override = $row[31];
        $ingroup_rec_filename = $row[32];
        $afterhours_xfer_group = $row[33];
        $qc_enabled = $row[34];
        $qc_statuses = $row[35];
        $qc_shift_id = $row[36];
        $qc_get_record_launch = $row[37];
        $qc_show_recording = $row[38];
        $qc_web_form_address = stripslashes($row[39]);
        $qc_script = $row[40];
        $play_place_in_line = $row[41];
        $play_estimate_hold_time = $row[42];
        $hold_time_option = $row[43];
        $hold_time_option_seconds = $row[44];
        $hold_time_option_exten = $row[45];
        $hold_time_option_voicemail = $row[46];
        $hold_time_option_xfer_group = $row[47];
        $hold_time_option_callback_filename = $row[48];
        $hold_time_option_callback_list_id = $row[49];
        $hold_recall_xfer_group = $row[50];
        $no_delay_call_route = $row[51];
        $play_welcome_message = $row[52];
        $answer_sec_pct_rt_stat_one = $row[53];
        $answer_sec_pct_rt_stat_two = $row[54];
        $default_group_alias = $row[55];
        $no_agent_no_queue = $row[56];
        $no_agent_action = $row[57];
        $no_agent_action_value = $row[58];
        $web_form_address_two = stripslashes($row[59]);
        $timer_action = $row[60];
        $timer_action_message = $row[61];
        $timer_action_seconds = $row[62];
        $start_call_url = $row[63];
        $dispo_call_url = $row[64];
        $xferconf_c_number = $row[65];
        $xferconf_d_number = $row[66];
        $xferconf_e_number = $row[67];
        $ignore_list_script_override = $row[68];
        $extension_appended_cidname = $row[69];
        $uniqueid_status_display = $row[70];
        $uniqueid_status_prefix = $row[71];
        $hold_time_option_minimum = $row[72];
        $hold_time_option_press_filename = $row[73];
        $hold_time_option_callmenu = $row[74];
        $onhold_prompt_no_block = $row[75];
        $onhold_prompt_seconds = $row[76];
        $hold_time_option_no_block = $row[77];
        $hold_time_option_prompt_seconds = $row[78];
        $hold_time_second_option = $row[79];
        $hold_time_third_option = $row[80];
        $wait_hold_option_priority = $row[81];
        $wait_time_option = $row[82];
        $wait_time_second_option = $row[83];
        $wait_time_third_option = $row[84];
        $wait_time_option_seconds = $row[85];
        $wait_time_option_exten = $row[86];
        $wait_time_option_voicemail = $row[87];
        $wait_time_option_xfer_group = $row[88];
        $wait_time_option_callmenu = $row[89];
        $wait_time_option_callback_filename = $row[90];
        $wait_time_option_callback_list_id = $row[91];
        $wait_time_option_press_filename = $row[92];
        $wait_time_option_no_block = $row[93];
        $wait_time_option_prompt_seconds = $row[94];
        $timer_action_destination = $row[95];
        $calculate_estimated_hold_seconds = $row[96];
        $add_lead_url = $row[97];
        $eht_minimum_prompt_filename = $row[98];
        $eht_minimum_prompt_no_block = $row[99];
        $eht_minimum_prompt_seconds = $row[100];
        $on_hook_ring_time = $row[101];


        ##### get callmenu listings for dynamic pulldown
		$stmt="SELECT menu_id,menu_name from vicidial_call_menu $whereLOGadmin_viewable_groupsSQL order by menu_id;";
		$rslt=mysql_query($stmt, $link);
		$Xmenus_to_print = mysql_num_rows($rslt);
		$o=0;
		$Xmenuslist='';
		$Wmenuslist='';
		while ($Xmenus_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$Xmenuslist .= "<option ";
			$Wmenuslist .= "<option ";
			if ($hold_time_option_callmenu == "$rowx[0]") 
				{
				$Xmenuslist .= "SELECTED ";
				$Xmenus_selected++;
				}
			if ($wait_time_option_callmenu == "$rowx[0]") 
				{
				$Wmenuslist .= "SELECTED ";
				$Wmenus_selected++;
				}
			$Xmenuslist .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$Wmenuslist .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$o++;
			}
		if ($Xmenus_selected < 1) 
			{$Xmenuslist .= "<option SELECTED value=\"---NONE---\">Nenhum</option>\n";}
		if ($Wmenus_selected < 1) 
			{$Wmenuslist .= "<option SELECTED value=\"---NONE---\">Nenhum</option>\n";}


        ##### get in-groups listings for dynamic pulldown
		$stmt="SELECT group_id,group_name from vicidial_inbound_groups where group_id NOT IN('AGENTDIRECT') $LOGadmin_viewable_groupsSQL order by group_id;";
		$rslt=mysql_query($stmt, $link);
		$Xgroups_to_print = mysql_num_rows($rslt);
		$Xgroups_menu='';
		$Xgroups_selected=0;
		$Dgroups_menu='';
		$Dgroups_selected=0;
		$Agroups_menu='';
		$Agroups_selected=0;
		$Hgroups_menu='';
		$Hgroups_selected=0;
		$Wgroups_menu='';
		$Wgroups_selected=0;
		$Tgroups_menu='';
		$Tgroups_selected=0;
		$o=0;
		while ($Xgroups_to_print > $o) 
			{
			$rowx=mysql_fetch_row($rslt);
			$Xgroups_menu .= "<option ";
			$Dgroups_menu .= "<option ";
			$Agroups_menu .= "<option ";
			$Tgroups_menu .= "<option ";
			$Wgroups_menu .= "<option ";
			$Hgroups_menu .= "<option ";
			if ($default_xfer_group == "$rowx[0]") 
				{
				$Xgroups_menu .= "SELECTED ";
				$Xgroups_selected++;
				}
			if ($drop_inbound_group == "$rowx[0]") 
				{
				$Dgroups_menu .= "SELECTED ";
				$Dgroups_selected++;
				}
			if ($afterhours_xfer_group == "$rowx[0]") 
				{
				$Agroups_menu .= "SELECTED ";
				$Agroups_selected++;
				}
			if ($hold_time_option_xfer_group == "$rowx[0]") 
				{
				$Tgroups_menu .= "SELECTED ";
				$Tgroups_selected++;
				}
			if ($wait_time_option_xfer_group == "$rowx[0]") 
				{
				$Wgroups_menu .= "SELECTED ";
				$Wgroups_selected++;
				}
			if ($hold_recall_xfer_group == "$rowx[0]") 
				{
				$Hgroups_menu .= "SELECTED ";
				$Hgroups_selected++;
				}
			$Xgroups_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			$Dgroups_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
			if ($group_id!=$rowx[0])
				{
				$Agroups_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
				$Tgroups_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
				$Wgroups_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
				$Hgroups_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
				}
			$o++;
			}
		if ($Xgroups_selected < 1) 
			{$Xgroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>\n";}
		else 
			{$Xgroups_menu .= "<option value=\"---NONE---\">Nenhum</option>\n";}
		if ($Dgroups_selected < 1) 
			{$Dgroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>\n";}
		else 
			{$Dgroups_menu .= "<option value=\"---NONE---\">Nenhum</option>\n";}
		if ($Agroups_selected < 1) 
			{$Agroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>\n";}
		else 
			{$Agroups_menu .= "<option value=\"---NONE---\">Nenhum</option>\n";}
		if ($Tgroups_selected < 1) 
			{$Tgroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>\n";}
		else 
			{$Tgroups_menu .= "<option value=\"---NONE---\">Nenhum</option>\n";}
		if ($Wgroups_selected < 1) 
			{$Wgroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>\n";}
		else 
			{$Wgroups_menu .= "<option value=\"---NONE---\">Nenhum</option>\n";}
		if ($Hgroups_selected < 1) 
			{$Hgroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>\n";}
		else 
			{$Hgroups_menu .= "<option value=\"---NONE---\">Nenhum</option>\n";}
        
        ##### get call_times listing for dynamic pulldown
	$stmt="SELECT call_time_id,call_time_name from vicidial_call_times order by call_time_id";
	$rslt=mysql_query($stmt, $link);
	$times_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($times_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		//$call_times_list= "<option value=\"$rowx[0]\">$rowx[1]</option>\n";
		$call_timename_list[$rowx[0]] = $rowx[1];
		$o++;
		}
                  
        ?>
        <script>
            function openNewWindow(url) 
		{
		window.open (url,"",'width=620,height=300,scrollbars=yes,menubar=yes,address=yes');
		}
        </script>
        <div class=content>
            <form action=edit_inbound.php method=POST name=admin_form id=admin_form >

                <input type=hidden name=ADD value=4111>
                <input type=hidden name=group_id value="<?= $row[0] ?>">
                <input type=hidden name=DB value="<?= $DB ?>">
                <input type=hidden name=stage value="SUBMIT">

                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Edita Grupo Inbound</div>
                        <div class="pull-right"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-content">
                        <div id="wr"></div>
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">ID do Grupo:</label>
                            <div class="formRight">
                                <b><?= $row[0] ?></b>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">Nome do Grupo:</label>
                            <div class="formRight">
                                <input type=text name=group_name class="span" maxlength=30 value="<?= $row[1] ?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-group_color", "Cor do Grupo") ?>:</label>
                            <div class="formRight" id="group_color_td">
                                  <div class="input-append color" data-color="<?= $row[2] ?>" data-color-format="hex" id="colorp">
                                    <input type="text" class="span2" name=group_color value="<?= $row[2] ?>" >
                                    <span class="add-on"><i style="background-color: <?= $row[2] ?>"></i></span>
                                  </div>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">Activo:</label>
                            <div class="formRight">
                                <select name=active>
                                    <option value='Y' <?= ($active == "Y") ? "SELECTED" : "" ?>>Sim</option>
                                    <option value='N' <?= ($active == "N") ? "SELECTED" : "" ?>>Não</option>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix hide">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-web_form_address", "Web Form") ?>:</label>
                            <div class="formRight">
                                <input type=text name=web_form_address class="span" maxlength=1055 value="<?= $web_form_address ?>">
                            </div>
                        </div> 


                        <?php if ($SSenable_second_webform > 0) { ?>
                            <div class="formRow op fix hide">
                                <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-web_form_address", "Extra Web Form") ?>:</label>
                                <div class="formRight" >
                                    <input type=text name=web_form_address_two class="span" maxlength=1055 value="<?= $web_form_address_two ?>">
                                </div>
                            </div> 
                        <?php } ?>


                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-next_agent_call", "Próxima Chamada do Agente") ?>:</label>
                            <div class="formRight">
                                <select class="span" name=next_agent_call>
                                    <option <?= ($next_agent_call == 'random') ? "SELECTED" : "" ?> value='random' >Aleatório</option>
                                    <option <?= ($next_agent_call == 'oldest_call_start') ? "SELECTED" : "" ?> value='oldest_call_start'>Inicio de chamada mais antiga</option>
                                    <option <?= ($next_agent_call == 'oldest_call_finish') ? "SELECTED" : "" ?> value='oldest_call_finish'>Fim de chamada mais antigo</option>
                                    <option <?= ($next_agent_call == 'overall_user_level') ? "SELECTED" : "" ?> value='overall_user_level'>Nível de utilizador geral</option>
                                    <option <?= ($next_agent_call == 'inbound_group_rank') ? "SELECTED" : "" ?> value='inbound_group_rank'>Rank de Grupo de Inbound</option>
                                    <option <?= ($next_agent_call == 'campaign_rank') ? "SELECTED" : "" ?> value='campaign_rank'>Rank de Campanha</option>
                                    <option <?= ($next_agent_call == 'fewest_calls') ? "SELECTED" : "" ?> value='fewest_calls'>Menos chamadas atendidas (Inbound)</option>
                                    <option <?= ($next_agent_call == 'fewest_calls_campaign') ? "SELECTED" : "" ?> value='fewest_calls_campaign'>Menos chamadas atendidas (Campanha)</option>
                                    <option <?= ($next_agent_call == 'longest_wait_time') ? "SELECTED" : "" ?> value='longest_wait_time'>Maior tempo de espera</option>
                                    <option <?= ($next_agent_call == 'ring_all') ? "SELECTED" : "" ?> value='ring_all'>Tocar em todos</option>
                                </select>
                            </div>
                        </div> 


                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-queue_priority", "Prioridade de atendimento do grupo") ?>:</label>
                            <div class="formRight">
                                <select name=queue_priority>
                                    <?php
                                    $n = 99;
                                    while ($n >= -99) {
                                        $dtl = 'Igual';
                                        if ($n < 0) {
                                            $dtl = 'Baixo';
                                        }
                                        if ($n > 0) {
                                            $dtl = 'Elevado';
                                        }
                                        if ($n == $queue_priority) {
                                            echo "<option SELECTED value=\"$n\">$n - $dtl</option>\n";
                                        } else {
                                            echo "<option value=\"$n\">$n - $dtl</option>\n";
                                        }
                                        $n--;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div> 	

                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-on_hook_ring_time", "Tempo de toque <i>on hook</i>") ?>:</label>
                            <div class="formRight">
                                <input type=text name=on_hook_ring_time class="span" maxlength=4 value="<?= $on_hook_ring_time ?>">
                            </div>
                        </div> 

                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-fronter_display", "Mostrar Número Linha Inbound") ?>:</label>
                            <div class="formRight">
                                <select name=fronter_display>
                                    <option <?= ($fronter_display) ? "SELECTED" : "" ?> value='Y'>Sim</option>
                                    <option <?= (!$fronter_display) ? "SELECTED" : "" ?> value='N'>Não</option>
                                </select>
                            </div>
                        </div> 
                        <!--<span id='audio_chooser_span'></span>-->
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-get_call_launch", "Inicio Chamada abrir Script") ?>:</label>
                            <div class="formRight">
                                <select name=get_call_launch>
                                    <option <?= ($get_call_launch == 'NONE') ? "SELECTED" : "" ?> value='NONE'>Não</option>
                                    <!--<option <?= ($get_call_launch == 'SCRIPT') ? "SELECTED" : "" ?> value='SCRIPT'>Script</option>-->
                                    <!--<option <?= ($get_call_launch == 'WEBFORM') ? "SELECTED" : "" ?> value='WEBFORM'>Página Externa 1</option>-->
                                    <!--<option <?= ($get_call_launch == 'WEBFORM2') ? "SELECTED" : "" ?> value='WEBFORM2'>Página Externa 2</option>-->
                                    <option <?= ($get_call_launch == 'CRM') ? "SELECTED" : "" ?> value='FORM'>Sim</option>
                                </select>
                            </div>
                        </div> 
                        
                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-drop_call_seconds", "Segundos para uma Chamada Perdida") ?>:</label>
                            <div class="formRight">
                                <input type=text name=drop_call_seconds class="span" maxlength=4 value="<?= $drop_call_seconds ?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-drop_action", "Acção de Chamadas Perdidas") ?>:</label>
                            <div class="formRight">
                                <select name=drop_action>
                                    <option <?= ($drop_action == 'HANGUP') ? "SELECTED" : "" ?> value='HANGUP'>Desligar</option>
                                    <option <?= ($drop_action == 'MESSAGE') ? "SELECTED" : "" ?> value='MESSAGE'>Extensão</option>
                                    <option <?= ($drop_action == 'VOICEMAIL') ? "SELECTED" : "" ?> value='VOICEMAIL'>Voicemail</option>
                                    <option <?= ($drop_action == 'IN_GROUP') ? "SELECTED" : "" ?> value='IN_GROUP'>Grupo de Inbound</option>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-drop_exten", "Extensão de Chamadas Perdidas") ?>:</label>
                            <div class="formRight">
                                <input type=text name=drop_exten class="span" maxlength=20 value="<?= $drop_exten ?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-voicemail_ext", "Voicemail") ?>:</label>
                            <div class="formRight">
                                <select name=voicemail_ext id=voicemail_ext class="chzn-select">
                                    <?=populate_options($voice_mail_list, $voicemail_ext)?>
                                </select>   
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-drop_inbound_group", "Grupo transferencia para Chamadas Perdidas") ?>:</label>
                            <div class="formRight">
                                <select class="span" name=drop_inbound_group>
                                <?=$Dgroups_menu?>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-call_time_id", "Horário de Atendimento") ?>:</label>
                            <div class="formRight">
                                <select class="span" name=call_time_id>
                                    <?=populate_options($call_timename_list,$call_time_id)?>
                                </select>
                            </div>
                        </div> 
                        
                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                        <div class="formRow op fix afth route">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-after_hours_action", "Acção Fora de Atendimento") ?>:</label>
                            <div class="formRight">
                                <select name=after_hours_action>
                                    <option <?= ($after_hours_action == 'HANGUP') ? "SELECTED" : "" ?> value="HANGUP">Desligar</option>
                                    <option <?= ($after_hours_action == 'MESSAGE') ? "SELECTED" : "" ?> value="MESSAGE">Mensagem</option>
                                    <option <?= ($after_hours_action == 'EXTENSION') ? "SELECTED" : "" ?> value="EXTENSION">Extensão</option>
                                    <option <?= ($after_hours_action == 'VOICEMAIL') ? "SELECTED" : "" ?> value="VOICEMAIL">VoiceMail</option>
                                    <option <?= ($after_hours_action == 'IN_GROUP') ? "SELECTED" : "" ?> value="IN_GROUP">Grupo Inbound</option>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix afth MSG">
                            <label data-t="tooltip" title=""><?= help("vicidial_inbound_groups-after_hours_message_filename", "Nome do ficheiro (Fora de Atendimento)") ?>:</label>
                            <div class="formRight">
                                <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..." name=after_hours_message_filename value="<?=$after_hours_message_filename?>" id=after_hours_message_filename class="span6" readonly/>
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                            </div>
                        </div>
                        <div class="formRow op fix afth EXT">
                            <label data-t="tooltip" title="">
                                <?= help("vicidial_inbound_groups-after_hours_exten", "Extensão (Fora de Atendimento)") ?>:
                            </label>
                            <div class="formRight">
                                <input type=text name=after_hours_exten class="span" maxlength=20 value="<?= $after_hours_exten ?>">
                            </div>
                        </div> 
                        <div class="formRow op fix afth VCM">
                            <label data-t="tooltip" title="">
                                <?= help("vicidial_inbound_groups-after_hours_voicemail", "Voicemail (Fora de Atendimento)") ?>:
                            </label>
                            <div class="formRight">
                                    <select name=after_hours_voicemail id=after_hours_voicemail class="chzn-select">
                                    <?=populate_options($voice_mail_list, $after_hours_voicemail)?>
                                    </select>   
                            </div>
                        </div> 
                        <div class="formRow op fix afth ING">
                            <label data-t="tooltip" title="">
                                <?= help("vicidial_inbound_groups-afterhours_xfer_group", "Grupo de Transferência (Fora de Atendimento)") ?>:
                            </label>
                            <div class="formRight">
                                <select class="span" name=afterhours_xfer_group>
                                    <?= $Agroups_menu ?>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?= help("vicidial_inbound_groups-no_agent_no_queue", "Não aceitar chamadas sem agentes") ?>:
                            </label>
                            <div class="formRight">
                                <select name=no_agent_no_queue>
                                    <option <?= ($no_agent_no_queue == 'Y') ? "SELECTED" : "" ?> value="Y">Sim</option>
                                    <option <?= ($no_agent_no_queue == 'N') ? "SELECTED" : "" ?> value="N">Não</option>
                                    <option <?= ($no_agent_no_queue == 'NO_PAUSED') ? "SELECTED" : "" ?> value="NO_PAUSED">Sim, mesmo que em pausa</option>
                                </select>
                            </div>
                        </div> 
                        
                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?= help("vicidial_inbound_groups-no_agent_no_queue", "Acção sem agentes logados") ?>:
                            </label>
                            <div class="formRight">
                                <select class="span" name=no_agent_action id=no_agent_action onChange="dynamic_call_action('no_agent_action', '<?=$no_agent_action?>', '<?=$no_agent_action_value?>', '600');">
                                    <option <?= ($no_agent_action == 'CALLMENU') ? "SELECTED" : "" ?> value="CALLMENU">IVR</option>
                                    <option <?= ($no_agent_action == 'INGROUP') ? "SELECTED" : "" ?> value="INGROUP">Grupo Inbound</option>
                                    <option <?= ($no_agent_action == 'DID') ? "SELECTED" : "" ?> value="DID">DID</option>
                                    <option <?= ($no_agent_action == 'MESSAGE') ? "SELECTED" : "" ?> value="MESSAGE">Mensagem</option>
                                    <option <?= ($no_agent_action == 'EXTENSION') ? "SELECTED" : "" ?> value="EXTENSION">Extensão</option>
                                    <option <?= ($no_agent_action == 'VOICEMAIL') ? "SELECTED" : "" ?> value="VOICEMAIL">VoiceMail</option>
                                </select>
                            </div>
                        </div> 




                            <div id="no_agent_action_value_span">
                                <?php
                                if ($no_agent_action == 'CALLMENU') {
                                    ?>
                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            <span id=no_agent_action_value_link>
                                                <a href="edit_ivr.php?ADD=3511&menu_id=<?=$no_agent_action_value?>">IVR:</a>
                                            </span>
                                        </label>
                                        <div class="formRight">
                                            <select class="span" name=no_agent_action_value id=no_agent_action_value onChange="dynamic_call_action_link('no_agent_action', 'CALLMENU');">
                                                <?= populate_options($call_menu_list,$no_agent_action_value) ?>
                                            </select>
                                        </div>
                                    </div> 


                                    <?php
                                    }
                                    if ($no_agent_action == 'INGROUP') {
                                        if (strlen($no_agent_action_value) < 10) {
                                            $no_agent_action_value = 'SALESLINE,CID,LB,998,TESTCAMP,1';
                                        }
                                        $IGno_agent_action_value = explode(",", $no_agent_action_value);
                                        $IGgroup_id = $IGno_agent_action_value[0];
                                        $IGhandle_method = $IGno_agent_action_value[1];
                                        $IGsearch_method = $IGno_agent_action_value[2];
                                        $IGlist_id = $IGno_agent_action_value[3];
                                        $IGcampaign_id = $IGno_agent_action_value[4];
                                        $IGphone_code = $IGno_agent_action_value[5];
                                        ?>


                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            <span id=no_agent_action_value_link>
                                                <a href="edit_inbound.php?group_id=<?=$IGgroup_id?>">Grupo Inbound:</a>
                                            </span>
                                        </label>
                                        <div class="formRight">
                                            <select class="span" name=IGgroup_id_no_agent_action id=IGgroup_id_no_agent_action onChange="dynamic_call_action_link('IGgroup_id_no_agent_action', 'INGROUP');">
                                                <?= populate_options($ingroup_list,$IGgroup_id) ?>
                                            </select>
                                        </div>
                                    </div> 

                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            Handle Method:
                                        </label>
                                        <div class="formRight">
                                            <select class="span" name=IGhandle_method_<?=$j?> id=IGhandle_method_<?= $j ?>>
                                                <?= populate_options($IGhandle_method_list,$IGhandle_method,false) ?>
                                            </select>
                                        </div>
                                    </div> 

                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            Search Method:
                                        </label>
                                        <div class="formRight">
                                            <select class="span" name=IGsearch_method_<?=$j?> id=IGsearch_method_<?= $j ?>>
                                                <?=populate_options($IGsearch_method_list,$IGsearch_method)?>
                                            </select>
                                        </div>
                                    </div> 

                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            List ID:
                                        </label>
                                        <div class="formRight">
                                            <input type=text class="span2" maxlength=14 name=IGlist_id_<?=$j?> id=IGlist_id_<?= $j ?> value="<?= $IGlist_id ?>">
                                        </div>
                                    </div> 

                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            Campaign ID: 
                                        </label>
                                        <div class="formRight">
                                            <select class="chzn-select" name=IGcampaign_id_<?=$j?> id=IGcampaign_id_<?= $j ?>>
                                                <?= populate_options($IGcampaign_id_list,$IGcampaign_id)?>
                                            </select>
                                        </div>
                                    </div> 

                                    <div class="formRow op fix hide">
                                        <label data-t="tooltip" title="">
                                            Phone Code: 
                                        </label>
                                        <div class="formRight">
                                            <input type=text class="span" maxlength=14 name=IGphone_code_<?=$j?> id=IGphone_code_<?= $j ?> value="<?= $IGphone_code ?>">
                                        </div>
                                    </div> 
                                        <?php
                                        }
                                        if ($no_agent_action == 'DID') {
                                            $stmt = "SELECT did_id from vicidial_inbound_dids where did_pattern='$no_agent_action_value';";
                                            $rslt = mysql_query($stmt, $link);
                                            $row = mysql_fetch_row($rslt);
                                            $did_id = $row[0];
                                            ?>
                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            <span id=no_agent_action_value_link>
                                                <a href="edit_did.php?did_id=<?=$did_id?>">DID:</a>
                                            </span>
                                        </label>
                                        <div class="formRight">
                                            <select class="chzn-select" name=no_agent_action_value id=no_agent_action_value onChange="dynamic_call_action_link('no_agent_action', 'DID');">
                                                <?= populate_options($did_list,$no_agent_action_value) ?>
                                            </select>
                                        </div>
                                    </div> 
                                        <?php
                                        }
                                        if ($no_agent_action == 'MESSAGE') {
                                            if (strlen($no_agent_action_value) < 3) {
                                                $no_agent_action_value = 'nbdy-avail-to-take-call|vm-goodbye';
                                            }
                                            ?>
                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            Audio File:
                                        </label>
                                        <div class="formRight">
                                            <div class="input-append">
                                                <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..." value="<?=$no_agent_action_value?>" name=no_agent_action_value id=no_agent_action_value class="span6" readonly/>
                                                <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                                <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                            </div>
                                        </div>
                                    </div> 
                                    <?php
                                    }
                                    if ($no_agent_action == 'EXTENSION') {
                                        if (strlen($no_agent_action_value) < 3) {
                                            $no_agent_action_value = '8304,default';
                                        }
                                        $EXno_agent_action_value = explode(",", $no_agent_action_value);
                                        $EXextension = $EXno_agent_action_value[0];
                                        $EXcontext = $EXno_agent_action_value[1];
                                        ?>
                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            Extension:
                                        </label>
                                        <div class="formRight">
                                            <input type=text name=EXextension_no_agent_action id=EXextension_no_agent_action class="span" maxlength=255 value="<?= $EXextension ?>"> 
                                        </div>
                                    </div> 

                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            Context:
                                        </label>
                                        <div class="formRight">
                                            <input type=text name=EXcontext_no_agent_action id=EXcontext_no_agent_action class="span" maxlength=255 value="<?= $EXcontext ?>">
                                        </div>
                                    </div> 
                                    <?php
                                    }
                                    if ($no_agent_action == 'VOICEMAIL') {
                                        ?>
                                    <div class="formRow op fix">
                                        <label data-t="tooltip" title="">
                                            Voicemail Box:
                                        </label>
                                        <div class="formRight">
                                            <div class="input-append">
                                                <select name=no_agent_action_value id=no_agent_action_value class="chzn-select">
                                                    <?=populate_options($voice_mail_list, $voicemail_ext)?>
                                                </select>  
                                            </div>
                                        </div>
                                    </div> 
                                <?php } ?>
                            </div>
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-play_welcome_message", "Tocar Mensagem de Entrada") ?>:
                                </label>
                                <div class="formRight">
                                    <select name=play_welcome_message>
                                        <option <?= ($play_welcome_message == 'ALWAYS') ? "SELECTED" : "" ?> value="ALWAYS">Sempre</option>
                                        <option <?= ($play_welcome_message == 'NEVER') ? "SELECTED" : "" ?> value="NEVER">Nunca</option>
                                        <option <?= ($play_welcome_message == 'IF_WAIT_ONLY') ? "SELECTED" : "" ?> value="IF_WAIT_ONLY">Se em espera só</option>
                                        <option <?= ($play_welcome_message == 'YES_UNLESS_NODELAY') ? "SELECTED" : "" ?> value="YES_UNLESS_NODELAY"> Sim ou se sem espera</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-welcome_message_filename", "Mensagem de Entrada") ?>:
                                </label>
                                <div class="formRight">
                                    <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..." value="<?=$welcome_message_filename?>"  name=welcome_message_filename id=welcome_message_filename class="span6" readonly/>
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                    </div>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-moh_context", "Música em Espera") ?>:
                                </label>
                                <div class="formRight">
                                <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..."  value="<?=$moh_context?>" name=moh_context id=moh_context class="span6" readonly/>            
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-onhold_prompt_filename", "Ficheiro som Em Espera") ?>:
                                </label>
                                <div class="formRight">
                                <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..." value="<?=$onhold_prompt_filename?>"  name=onhold_prompt_filename id=onhold_prompt_filenameno_agent_action_value class="span6" readonly/>
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-prompt_interval", "Intervalo de toque em Espera (s)") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=prompt_interval class="span" maxlength=5 value="<?= $prompt_interval ?>">
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-onhold_prompt_seconds", "Duração toque em Espera (s)") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=onhold_prompt_seconds class="span" maxlength=5 value="<?= $onhold_prompt_seconds ?>">
                                </div>
                            </div> 
                        
                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-play_place_in_line", "Anúnciar posição na fila") ?>:
                                </label>
                                <div class="formRight">
                                    <select name=play_place_in_line>
                                        <option <?= ($play_place_in_line == 'Y') ? "SELECTED" : "" ?> value="Y">Sim</option>
                                        <option <?= ($play_place_in_line == 'N') ? "SELECTED" : "" ?> value="N">Não</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-play_estimate_hold_time", "Anúnciar tempo estimado de espera") ?>:
                                </label>
                                <div class="formRight">
                                    <select name=play_estimate_hold_time>
                                        <option <?= ($play_estimate_hold_time == 'Y') ? "SELECTED" : "" ?> value="Y">Sim</option>
                                        <option <?= ($play_estimate_hold_time == 'N') ? "SELECTED" : "" ?> value="N">Não</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-calculate_estimated_hold_seconds", "Tempo a que anuncia o tempo estimado de espera") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=calculate_estimated_hold_seconds class="span" maxlength=5 value="<?= $calculate_estimated_hold_seconds ?>">
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-eht_minimum_prompt_filename", "Ficheiro Anuncio Tempo estimado de Espera") ?>:
                                </label>
                                <div class="formRight">
                                <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..." value="<?=$eht_minimum_prompt_filename?>"  name=eht_minimum_prompt_filename id=eht_minimum_prompt_filename class="span6" readonly/>
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                                </div>
                            </div>
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-eht_minimum_prompt_no_block", "Tempo estimado de Espera No Block") ?>:
                                </label>
                                <div class="formRight">
                                    <select name=eht_minimum_prompt_no_block>
                                        <option <?= ($eht_minimum_prompt_no_block == 'N') ? "SELECTED" : "" ?> value="N">Não</option>
                                        <option <?= ($eht_minimum_prompt_no_block == 'Y') ? "SELECTED" : "" ?> value="Y">Sim</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-eht_minimum_prompt_seconds", "Tempo estimado de Espera Minimum Prompt") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=eht_minimum_prompt_seconds class="span" maxlength=5 value="<?= $eht_minimum_prompt_seconds ?>">
                                </div>
                            </div> 
                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option","Opção Tempo Espera") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=wait_time_option>
                                        <option <?= ($wait_time_option == 'NONE') ? "SELECTED" : "" ?> value="NONE">Nenhum</option>
                                        <option <?= ($wait_time_option == 'PRESS_STAY') ? "SELECTED" : "" ?> value="PRESS_STAY">Carrega Para Ficar</option>
                                        <option <?= ($wait_time_option == 'PRESS_VMAIL') ? "SELECTED" : "" ?> value="PRESS_VMAIL">Carrega Para VoiceMail</option>
                                        <option <?= ($wait_time_option == 'PRESS_EXTEN') ? "SELECTED" : "" ?> value="PRESS_EXTEN">Carrega Para Extensão</option>
                                        <option <?= ($wait_time_option == 'PRESS_CALLMENU') ? "SELECTED" : "" ?> value="PRESS_CALLMENU">Carrega Para IVR</option>
                                        <option <?= ($wait_time_option == 'PRESS_CID_CALLBACK') ? "SELECTED" : "" ?> value="PRESS_CID_CALLBACK">Carrega CallerID para CallBack</option>
                                        <option <?= ($wait_time_option == 'PRESS_INGROUP') ? "SELECTED" : "" ?> value="PRESS_INGROUP">Carrega para grupo Inbound</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_second_option", "Segunda Opção Tempo Espera") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=wait_time_second_option>
                                        <option <?= ($wait_time_second_option == 'NONE') ? "SELECTED" : "" ?> value="NONE">Nenhum</option>
                                        <option <?= ($wait_time_second_option == 'PRESS_STAY') ? "SELECTED" : "" ?> value="PRESS_STAY">Carrega Para Ficar</option>
                                        <option <?= ($wait_time_second_option == 'PRESS_VMAIL') ? "SELECTED" : "" ?> value="PRESS_VMAIL">Carrega Para VoiceMail</option>
                                        <option <?= ($wait_time_second_option == 'PRESS_EXTEN') ? "SELECTED" : "" ?> value="PRESS_EXTEN">Carrega Para Extensão</option>
                                        <option <?= ($wait_time_second_option == 'PRESS_CALLMENU') ? "SELECTED" : "" ?> value="PRESS_CALLMENU">Carrega Para IVR</option>
                                        <option <?= ($wait_time_second_option == 'PRESS_CID_CALLBACK') ? "SELECTED" : "" ?> value="PRESS_CID_CALLBACK">Carrega CallerID para CallBack</option>
                                        <option <?= ($wait_time_second_option == 'PRESS_INGROUP') ? "SELECTED" : "" ?> value="PRESS_INGROUP">Carrega para grupo Inbound</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_third_option", "Terceira Opção Tempo Espera") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=wait_time_third_option>
                                        <option <?= ($wait_time_third_option == 'NONE') ? "SELECTED" : "" ?> value="NONE">Nenhum</option>
                                        <option <?= ($wait_time_third_option == 'PRESS_STAY') ? "SELECTED" : "" ?> value="PRESS_STAY">Carrega Para Ficar</option>
                                        <option <?= ($wait_time_third_option == 'PRESS_VMAIL') ? "SELECTED" : "" ?> value="PRESS_VMAIL">Carrega Para VoiceMail</option>
                                        <option <?= ($wait_time_third_option == 'PRESS_EXTEN') ? "SELECTED" : "" ?> value="PRESS_EXTEN">Carrega Para Extensão</option>
                                        <option <?= ($wait_time_third_option == 'PRESS_CALLMENU') ? "SELECTED" : "" ?> value="PRESS_CALLMENU">Carrega Para IVR</option>
                                        <option <?= ($wait_time_third_option == 'PRESS_CID_CALLBACK') ? "SELECTED" : "" ?> value="PRESS_CID_CALLBACK">Carrega CallerID para CallBack</option>
                                        <option <?= ($wait_time_third_option == 'PRESS_INGROUP') ? "SELECTED" : "" ?> value="PRESS_INGROUP">Carrega para grupo Inbound</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_seconds", "Segundos Opção Tempo Espera") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=wait_time_option_seconds class="span" maxlength=5 value="<?= $wait_time_option_seconds ?>">
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_exten", "Extensão Opção Tempo Espera") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=wait_time_option_exten class="span" maxlength=20 value="<?= $wait_time_option_exten ?>">
                                </div>
                            </div>  
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_callmenu", "IVR Opção Tempo Espera") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=wait_time_option_callmenu>
                                    <?= $Wmenuslist ?>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_voicemail", "VoiceMail Opção Tempo Espera") ?>:
                                </label>
                                <div class="formRight">
                                    <div class="input-append">
                                    <select name=wait_time_option_voicemail id=wait_time_option_voicemail class="chzn-select">
                                    <?=populate_options($voice_mail_list, $wait_time_option_voicemail)?>
                                    </select>   
                                    </div>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_xfer_group", "Grupo Inbound Tranferencia Opção Tempo Espera") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=wait_time_option_xfer_group>
                                        <?= $Wgroups_menu ?>
                                    </select>
                                </div>
                            </div> 
  

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_press_filename", "Opção Tempo Espera Press Filename") ?>:
                                </label>
                                <div class="formRight">
                                <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..."  value="<?=$wait_time_option_press_filename?>" name=wait_time_option_press_filename id=wait_time_option_press_filename class="span6" readonly/>
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_no_block", "Opção Tempo Espera Press No Block") ?>:
                                </label>
                                <div class="formRight">
                                    <select name=wait_time_option_no_block>
                                        <option <?= ($wait_time_option_no_block == 'N') ? "SELECTED" : "" ?> value="N">Não</option>
                                        <option <?= ($wait_time_option_no_block == 'Y') ? "SELECTED" : "" ?> value="Y">Sim</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_prompt_seconds", "Opção Tempo Espera Prompt Seconds") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=wait_time_option_prompt_seconds class="span" maxlength=5 value="<?= $wait_time_option_prompt_seconds ?>">
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_callback_filename", "Opção Tempo Espera Callback Filename") ?>:
                                </label>
                                <div class="formRight">
                                <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..." value="<?=$wait_time_option_callback_filename?>"  name=wait_time_option_callback_filename id=wait_time_option_callback_filename class="span6" readonly/>
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                                </div>
                            </div>

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_time_option_callback_list_id", "Opção Tempo Espera Callback List ID") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=wait_time_option_callback_list_id class="span" maxlength=14 value="<?= $wait_time_option_callback_list_id ?>">
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-wait_hold_option_priority", "Opção Tempo Espera Priority") ?>:
                                </label>
                                <div class="formRight">
                                    <select name=wait_hold_option_priority>
                                        <option <?= ($wait_hold_option_priority == 'WAIT') ? "SELECTED" : "" ?> value="WAIT">Espera</option>
                                        <option <?= ($wait_hold_option_priority == 'BOTH') ? "SELECTED" : "" ?> value="BOTH">Ambos</option>
                                    </select>
                                </div>
                            </div> 
  

                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option", "Estimated  Hold Time Option") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=hold_time_option>
                                        <option <?= ($hold_time_option == 'NONE') ? "SELECTED" : "" ?> value="NONE">Nenhum</option>
                                        <option <?= ($hold_time_option == 'EXTENSION') ? "SELECTED" : "" ?> value="EXTENSION">Extensão</option>
                                        <option <?= ($hold_time_option == 'CALL_MENU') ? "SELECTED" : "" ?> value="CALL_MENU">IVR</option>
                                        <option <?= ($hold_time_option == 'VOICEMAIL') ? "SELECTED" : "" ?> value="VOICEMAIL">VoiceMail</option>
                                        <option <?= ($hold_time_option == 'IN_GROUP') ? "SELECTED" : "" ?> value="IN_GROUP">Grupo Inbound</option>
                                        <option <?= ($hold_time_option == 'CALLERID_CALLBACK') ? "SELECTED" : "" ?> value="CALLERID_CALLBACK">CALLERID_CALLBACK</option>
                                        <option <?= ($hold_time_option == 'DROP_ACTION') ? "SELECTED" : "" ?> value="DROP_ACTION">DROP_ACTION</option>
                                        <option <?= ($hold_time_option == 'PRESS_STAY') ? "SELECTED" : "" ?> value="PRESS_STAY">Carrega Para Ficar</option>
                                        <option <?= ($hold_time_option == 'PRESS_VMAIL') ? "SELECTED" : "" ?> value="PRESS_VMAIL">Carrega Para VoiceMail</option>
                                        <option <?= ($hold_time_option == 'PRESS_EXTEN') ? "SELECTED" : "" ?> value="PRESS_EXTEN">Carrega Para Extensão</option>
                                        <option <?= ($hold_time_option == 'PRESS_CALLMENU') ? "SELECTED" : "" ?> value="PRESS_CALLMENU">Carrega Para IVR</option>
                                        <option <?= ($hold_time_option == 'PRESS_CID_CALLBACK') ? "SELECTED" : "" ?> value="PRESS_CID_CALLBACK">Carrega CallerID para CallBack</option>
                                        <option <?= ($hold_time_option == 'PRESS_INGROUP') ? "SELECTED" : "" ?> value="PRESS_INGROUP">Carrega para grupo Inbound</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_second_option", "Hold Time Second Option") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=hold_time_second_option>
                                        <option <?= ($hold_time_second_option == 'NONE') ? "SELECTED" : "" ?> value="NONE">Nenhum</option>
                                        <option <?= ($hold_time_second_option == 'PRESS_STAY') ? "SELECTED" : "" ?> value="PRESS_STAY">Carrega Para Ficar</option>
                                        <option <?= ($hold_time_second_option == 'PRESS_VMAIL') ? "SELECTED" : "" ?> value="PRESS_VMAIL">Carrega Para VoiceMail</option>
                                        <option <?= ($hold_time_second_option == 'PRESS_EXTEN') ? "SELECTED" : "" ?> value="PRESS_EXTEN">Carrega Para Extensão</option>
                                        <option <?= ($hold_time_second_option == 'PRESS_CALLMENU') ? "SELECTED" : "" ?> value="PRESS_CALLMENU">Carrega Para IVR</option>
                                        <option <?= ($hold_time_second_option == 'PRESS_CID_CALLBACK') ? "SELECTED" : "" ?> value="PRESS_CID_CALLBACK">Carrega CallerID para CallBack</option>
                                        <option <?= ($hold_time_second_option == 'PRESS_INGROUP') ? "SELECTED" : "" ?> value="PRESS_INGROUP">Carrega para grupo Inbound</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_third_option", "Hold Time Third Option") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=hold_time_third_option>
                                        <option <?= ($hold_time_third_option == 'NONE') ? "SELECTED" : "" ?> value="NONE">Nenhum</option>
                                        <option <?= ($hold_time_third_option == 'PRESS_STAY') ? "SELECTED" : "" ?> value="PRESS_STAY">Carrega Para Ficar</option>
                                        <option <?= ($hold_time_third_option == 'PRESS_VMAIL') ? "SELECTED" : "" ?> value="PRESS_VMAIL">Carrega Para VoiceMail</option>
                                        <option <?= ($hold_time_third_option == 'PRESS_EXTEN') ? "SELECTED" : "" ?> value="PRESS_EXTEN">Carrega Para Extensão</option>
                                        <option <?= ($hold_time_third_option == 'PRESS_CALLMENU') ? "SELECTED" : "" ?> value="PRESS_CALLMENU">Carrega Para IVR</option>
                                        <option <?= ($hold_time_third_option == 'PRESS_CID_CALLBACK') ? "SELECTED" : "" ?> value="PRESS_CID_CALLBACK">Carrega CallerID para CallBack</option>
                                        <option <?= ($hold_time_third_option == 'PRESS_INGROUP') ? "SELECTED" : "" ?> value="PRESS_INGROUP">Carrega para grupo Inbound</option>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_seconds", "Hold Time Option Seconds") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=hold_time_option_seconds class="span" maxlength=5 value="<?= $hold_time_option_seconds ?>">
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_minimum", "Hold Time Option Minimum") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=hold_time_option_minimum class="span" maxlength=5 value="<?= $hold_time_option_minimum ?>">
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_exten","Hold Time Option Extension") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=hold_time_option_exten class="span" maxlength=20 value="<?= $hold_time_option_exten ?>">
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_callmenu", "Hold Time Option IVR") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=hold_time_option_callmenu>
                                        <?= $Xmenuslist ?>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_voicemail", "Hold Time Option Voicemail") ?>:
                                </label>
                                <div class="formRight">
                                    <div class="input-append">
                                    <select name=hold_time_option_voicemail id=hold_time_option_voicemail class="chzn-select">
                                    <?=populate_options($voice_mail_list, $hold_time_option_voicemail)?>
                                    </select> 
                                    </div>
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_xfer_group", "Hold Time Option Transfer Group") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=hold_time_option_xfer_group>
                                    <?= $Tgroups_menu ?>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_press_filename", "Hold Time Option Press Filename") ?>:
                                </label>
                                <div class="formRight">
                                <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..." value="<?=$hold_time_option_press_filename?>"  name=hold_time_option_press_filename id=hold_time_option_press_filename class="span6" readonly/>
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_no_block", "Hold Time Option Press No Block") ?>:
                                </label>
                                <div class="formRight">
                                    <select name=hold_time_option_no_block>
                                        <option <?= ($hold_time_option_no_block == 'N') ? "SELECTED" : "" ?> value="N">Não</option>
                                        <option <?= ($hold_time_option_no_block == 'Y') ? "SELECTED" : "" ?> value="Y">Sim</option>
                                    </select>
                                </div>
                            </div>
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_prompt_seconds", "Hold Time Option Press Filename Options") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=hold_time_option_prompt_seconds class="span" maxlength=5 value="<?= $hold_time_option_prompt_seconds ?>">
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_callback_filename", "Hold Time Option After Press Filename") ?>:
                                </label>
                                <div class="formRight">
                                <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..." value="<?=$hold_time_option_callback_filename?>"  name=hold_time_option_callback_filename id=hold_time_option_callback_filename class="span6" readonly/>
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_time_option_callback_list_id", "Hold Time Option Callback List ID") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=hold_time_option_callback_list_id class="span" maxlength=14 value="<?= $hold_time_option_callback_list_id ?>">
                                </div>
                            </div>   

                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-agent_alert_exten", "agent Alert Filename") ?>:
                                </label>
                                <div class="formRight">
                                <div class="input-append">
                                    <input type="text" placeholder="Escolha um ficheiro com o gestor de audio..." value="<?=$agent_alert_exten?>"  name=agent_alert_exten id=agent_alert_exten class="span6" readonly/>
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-agent_alert_delay", "Agent Alert Delay") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=agent_alert_delay class="span" maxlength=6 value="<?= $agent_alert_delay ?>">
                                </div>
                            </div>   

                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-default_xfer_group", "Default Transfer Group") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=default_xfer_group>
                                        <?= $Xgroups_menu ?>
                                    </select>
                                </div>
                            </div> 

                                    <?php
                                    ##### get groups_alias listings for dynamic default group alias pulldown list menu
                                    $stmt = "SELECT group_alias_id,group_alias_name from groups_alias where active='Y' order by group_alias_id";
                                    $rslt = mysql_query($stmt, $link);
                                    $group_alias_to_print = mysql_num_rows($rslt);
                                    $group_alias_menu = '';
                                    $group_alias_selected = 0;
                                    $o = 0;
                                    while ($group_alias_to_print > $o) {
                                        $rowx = mysql_fetch_row($rslt);
                                        $group_alias_menu .= "<option ";
                                        if ($default_group_alias == "$rowx[0]") {
                                            $group_alias_menu .= "SELECTED ";
                                            $group_alias_selected++;
                                        }
                                        $group_alias_menu .= "value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                                        $o++;
                                    }
                                    ?>

                            <div class="formRow op fix hide">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-default_group_alias ", "Default Group Alias") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=default_group_alias>
                                        <option value="">Nenhum</option>
                                        <?= $group_alias_menu ?>
                                    </select>
                                </div>
                            </div>      
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-hold_recall_xfer_group", "Hold Recall Tranfer Group") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=hold_recall_xfer_group>
                                    <?= $Hgroups_menu ?>
                                    </select>
                                </div>
                            </div>       
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-no_delay_call_route", "No Delay Call Route") ?>:
                                </label>
                                <div class="formRight">
                                    <select name=no_delay_call_route>
                                        <option <?= ($no_delay_call_route == 'Y') ? "SELECTED" : "" ?> value="Y">Sim</option>
                                        <option <?= ($no_delay_call_route == 'N') ? "SELECTED" : "" ?> value="N">Não</option>
                                    </select>
                                </div>
                            </div>        

                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                         
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-ingroup_recording_override", "In Group Recording Override") ?>:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=ingroup_recording_override>
                                        <option <?= ($ingroup_recording_override == 'DISABLED') ? "SELECTED" : "" ?> value="DISABLED">Inactivo</option>
                                        <option <?= ($ingroup_recording_override == 'NEVER') ? "SELECTED" : "" ?> value="NEVER">Nunca</option>
                                        <option <?= ($ingroup_recording_override == 'ONDEMAND') ? "SELECTED" : "" ?> value="ONDEMAND">ONDEMAND</option>
                                        <option <?= ($ingroup_recording_override == 'ALLCALLS') ? "SELECTED" : "" ?> value="ALLCALLS">Todas as Chamadas</option>
                                        <option <?= ($ingroup_recording_override == 'ALLFORCE') ? "SELECTED" : "" ?> value="ALLFORCE">Forçar Todas</option>
                                    </select>
                                </div>
                            </div>       
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-ingroup_rec_filename", "In Group Recording Filename") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=ingroup_rec_filename class="span" maxlength=50 value="<?= $ingroup_rec_filename ?>">
                                </div>
                            </div>    

                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-answer_sec_pct_rt_stat_one", "% of Calls Answered Within X seconds 1") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=answer_sec_pct_rt_stat_one class="span" maxlength=5 value="<?= $answer_sec_pct_rt_stat_one ?>">
                                </div>
                            </div>  

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-answer_sec_pct_rt_stat_one", "% of Calls Answered Within X seconds 2") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=answer_sec_pct_rt_stat_two class="span" maxlength=5 value="<?= $answer_sec_pct_rt_stat_two ?>">
                                </div>
                            </div> 

                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                            <div class="formRow op fix hide">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-start_call_url", "Start Call Url") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=start_call_url class="span" maxlength=2000 value="<?= $start_call_url ?>">
                                </div>
                            </div> 

                            <div class="formRow op fix hide">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-dispo_call_url", "Dispo Call URL") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=dispo_call_url class="span" maxlength=2000 value="<?= $dispo_call_url ?>">
                                </div>
                            </div> 

                            <div class="formRow op fix hide">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-add_lead_url", "Add Lead URL") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=add_lead_url class="span" maxlength=2000 value="<?= $add_lead_url ?>:">
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-extension_appended_cidname", "Extension Appended CID") ?>
                                </label>
                                <div class="formRight">
                                    <select name=extension_appended_cidname>
                                        <option <?= ($extension_appended_cidname == 'Y') ? "SELECTED" : "" ?> value="Y">Sim</option>
                                        <option <?= ($extension_appended_cidname == 'N') ? "SELECTED" : "" ?> value="N">Não</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-uniqueid_status_display", "Unique ID Status Display") ?>:
                                </label>
                                <div class="formRight">
                                    <select name=uniqueid_status_display>
                                        <option <?= ($uniqueid_status_display == 'DISABLED') ? "SELECTED" : "" ?> value="DISABLED">Inactivo</option>
                                        <option <?= ($uniqueid_status_display == 'ENABLED') ? "SELECTED" : "" ?> value="ENABLED">Activo</option>
                                        <option <?= ($uniqueid_status_display == 'ENABLED_PREFIX') ? "SELECTED" : "" ?> value="ENABLED_PREFIX">ENABLED_PREFIX</option>
                                        <option <?= ($uniqueid_status_display == 'ENABLED_PRESERVE') ? "SELECTED" : "" ?> value="ENABLED_PRESERVE">ENABLED_PRESERVE</option>
                                    </select>
                                </div>
                            </div> 

                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <?= help("vicidial_inbound_groups-uniqueid_status_prefix", "Unique Id Status Prefix") ?>:
                                </label>
                                <div class="formRight">
                                    <input type=text name=uniqueid_status_prefix class="span" maxlength=50 value="<?= $uniqueid_status_prefix ?>">
                                </div>
                            </div> 

                            <div class="clear"></div>
                            <div class="seperator_dashed"></div>
                            <p class="text-right">
                                <button class="btn btn-success">Gravar</button>
                            </p>
                    </div>    
                </div>



                <?php
/*
                if ($SSqc_features_active > 0) {
                    echo "<tr bgcolor=#dddddd><td align=center colspan=2> &nbsp; </td></tr>\n";
                    echo "<tr bgcolor=#dddddd><td align=center colspan=2> Inbound Group QC Settings: </td></tr>\n";

                    ##### get status listings for dynamic pulldown
                    $qc_statuses = preg_replace("/^ | -$/", "", $qc_statuses);
                    $QCstatuses = explode(" ", $qc_statuses);
                    $QCs_to_print = (count($QCstatuses) - 0);
                    $stmt = "SELECT status,status_name,selectable,human_answered,category,sale,dnc,customer_contact,not_interested,unworkable,scheduled_callback from vicidial_statuses where status NOT IN('QUEUE','INCALL') order by status";
                    $rslt = mysql_query($stmt, $link);
                    $statuses_to_print = mysql_num_rows($rslt);
                    $qc_statuses_list = '';

                    $o = 0;
                    while ($statuses_to_print > $o) {
                        $rowx = mysql_fetch_row($rslt);
                        $qc_statuses_list .= "<input type=\"checkbox\" name=\"qc_statuses[]\" value=\"$rowx[0]\"";
                        $p = 0;
                        while ($p < $QCs_to_print) {
                            if ($rowx[0] == $QCstatuses[$p]) {
                                $qc_statuses_list .= " CHECKED";
                            }
                            $p++;
                        }
                        $qc_statuses_list .= "> $rowx[0] - $rowx[1]<BR>\n";

                        $o++;
                    }

                    $stmt = "SELECT distinct(status),status_name from vicidial_campaign_statuses order by status";
                    $rslt = mysql_query($stmt, $link);
                    $Cstatuses_to_print = mysql_num_rows($rslt);

                    $o = 0;
                    while ($Cstatuses_to_print > $o) {
                        $rowx = mysql_fetch_row($rslt);
                        if (!ereg("\"$rowx[0]\"", $qc_statuses_list)) {
                            $qc_statuses_list .= "<input type=\"checkbox\" name=\"qc_statuses[]\" value=\"$rowx[0]\"";
                            $p = 0;
                            while ($p < $QCs_to_print) {
                                if ($rowx[0] == $QCstatuses[$p]) {
                                    $qc_statuses_list .= " CHECKED";
                                }
                                $p++;
                            }
                            $qc_statuses_list .= "> $rowx[0] - $rowx[1]<BR>\n";
                        }
                        $o++;
                    }

                    ##### get scripts listings for pulldown
                    $stmt = "SELECT script_id,script_name from vicidial_scripts order by script_id";
                    $rslt = mysql_query($stmt, $link);
                    $scripts_to_print = mysql_num_rows($rslt);
                    $QCscripts_list = "";
                    $o = 0;
                    while ($scripts_to_print > $o) {
                        $rowx = mysql_fetch_row($rslt);
                        $QCscripts_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                        $scriptname_list["$rowx[0]"] = "$rowx[1]";
                        $o++;
                    }
                    ##### get shifts listings for pulldown
                    $stmt = "SELECT shift_id,shift_name from vicidial_shifts order by shift_id";
                    $rslt = mysql_query($stmt, $link);
                    $shifts_to_print = mysql_num_rows($rslt);
                    $QCshifts_list = "";
                    $o = 0;
                    while ($shifts_to_print > $o) {
                        $rowx = mysql_fetch_row($rslt);
                        $QCshifts_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
                        $shiftname_list["$rowx[0]"] = "$rowx[1]";
                        $o++;
                    }

                    echo "<tr bgcolor=#bcbcbc><td>QC Enabled: </td><td><select class='span' name=qc_enabled><option>Y</option><option>N</option><option SELECTED>$qc_enabled</option></select> $NWB#sips_inbound_groups-qc_enabled$NWE</td></tr>\n";
                    echo "<tr bgcolor=#bcbcbc><td>QC Statuses: <BR> $NWB#sips_inbound_groups-qc_statuses$NWE</td><td>$qc_statuses_list</td></tr>\n";
                    echo "<tr bgcolor=#cccccc><td>QC WebForm: </td><td align=left><input type=text name=qc_web_form_address class='span' maxlength=255 value=\"$qc_web_form_address\">$NWB#sips_inbound_groups-qc_web_form_address$NWE</td></tr>\n";

                    echo "<tr bgcolor=#cccccc><td><a href=\"$PHP_SELF?ADD=3111111&script_id=$script_id\">QC Script</a>: </td><td align=left><select class='span' name=qc_script>\n";
                    echo "$QCscripts_list";
                    echo "<option selected value=\"$qc_script\">$qc_script - $scriptname_list[$qc_script]</option>\n";
                    echo "</select>$NWB#sips_inbound_groups-qc_script$NWE</td></tr>\n";

                    echo "<tr bgcolor=#cccccc><td><a href=\"$PHP_SELF?ADD=331111111&shift_id=$qc_shift_id\">QC Shift</a>: </td><td align=left><select class='span' name=qc_shift_id>\n";
                    echo "$QCshifts_list";
                    echo "<option selected value=\"$qc_shift_id\">$qc_shift_id - $shiftname_list[$qc_shift_id]</option>\n";
                    echo "</select>$NWB#sips_inbound_groups-qc_shift_id$NWE</td></tr>\n";

                    echo "<tr bgcolor=#cccccc><td>QC Get Record Launch: </td><td><select class='span' name=qc_get_record_launch><option>NONE</option><option>SCRIPT</option><option>WEBFORM</option><option>QCSCRIPT</option><option>QCWEBFORM</option><option SELECTED>$qc_get_record_launch</option></select> $NWB#sips_inbound_groups-qc_get_record_launch$NWE</td></tr>\n";
                    echo "<tr bgcolor=#cccccc><td>QC Show Recording: </td><td><select class='span' name=qc_show_recording><option>Y</option><option>N</option><option SELECTED>$qc_show_recording</option></select> $NWB#sips_inbound_groups-qc_show_recording$NWE</td></tr>\n";
                    echo "<tr bgcolor=#cccccc><td align=center colspan=2><input type=submit name=submit value=SUBMIT></td></tr>\n";
                }


*/
                ### list of agent rank or skill-level for this inbound group
                # ZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZZ
                ?>

                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Colaboradores neste Grupo</div>
                        <div class="pull-right"></div>
                        <div class="clear"></div>
                    </div>
                    <table class="table table-condensed table-striped table-mod" id="rank">
                        <thead>
                            <tr>
                                <th>Colaborador</th>
                                <th>Selecionado<a href="#" id="rank_check_all" class="right"><i class="icon-check"></i> Seleccionar Todos</a></th>
                                <th>Rank*</th>
                                <th>Chamadas Recebidas Hoje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = "SELECT user,full_name,closer_campaigns from vicidial_users where active='Y' order by user;";
                            $rsltx = mysql_query($stmt, $link);
                            $users_to_print = mysql_num_rows($rsltx);

                            $o = 0;
                            while ($users_to_print > $o) {
                                $rowx = mysql_fetch_row($rsltx);
                                $o++;

                                $ARIG_user[$o] = $rowx[0];
                                $ARIG_name[$o] = $rowx[1];
                                $ARIG_close[$o] = $rowx[2];
                                $ARIG_check[$o] = '';
                                if (preg_match("/ $group_id /", $ARIG_close[$o])) {
                                    $ARIG_check[$o] = ' CHECKED';
                                }
                            }

                            $o = 0;
                            $ARIG_changenotes = '';
                            $stmtDlog = '';
                            while ($users_to_print > $o) {
                                $o++;
                                $stmt = "SELECT group_rank,calls_today from vicidial_inbound_group_agents where group_id='$group_id' and user='$ARIG_user[$o]';";
                                $rsltx = mysql_query($stmt, $link);
                                $viga_to_print = mysql_num_rows($rsltx);
                                if ($viga_to_print > 0) {
                                    $rowx = mysql_fetch_row($rsltx);
                                    $ARIG_rank[$o] = $rowx[0];
                                    $ARIG_calls[$o] = $rowx[1];
                                } else {
                                    $stmtD = "INSERT INTO vicidial_inbound_group_agents set calls_today='0',group_rank='0',group_weight='0',user='$ARIG_user[$o]',group_id='$group_id';";
                                    $rslt = mysql_query($stmtD, $link);
                                    if ($DB > 0) {
                                        echo "|$stmtD|";
                                    }
                                    $stmtDlog .= "$stmtD|";
                                    $ARIG_changenotes .= "added missing user to viga table $ARIG_user[$o]|";
                                    $ARIG_rank[$o] = '0';
                                    $ARIG_calls[$o] = '0';
                                }
                            }
                            if (strlen($ARIG_changenotes) > 10) {
                                ### LOG INSERTION Admin Log Table ###
                                $SQL_log = "$stmtDlog|";
                                $SQL_log = ereg_replace(';', '', $SQL_log);
                                $SQL_log = addslashes($SQL_log);
                                $stmt = "INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='INGROUPS', event_type='MODIFY', record_id='$group_id', event_code='USER INGROUP VIGA ADD', event_sql=\"$SQL_log\", event_notes='$ARIG_changenotes';";
                                if ($DB) {
                                    echo "|$stmt|\n";
                                }
                                $rslt = mysql_query($stmt, $link);
                            }


                            if ($stage == 'SUBMIT') {
                                $o = 0;
                                while ($users_to_print > $o) {
                                    $o++;

                                    $ARIG_checked = '';
                                    $ARIG_ranked = '';

                                    $checkbox_field = "CHECK_$ARIG_user[$o]$US$group_id";
                                    $rank_field = "RANK_$ARIG_user[$o]$US$group_id";

                                    if (isset($_GET["$checkbox_field"])) {
                                        $ARIG_checked = $_GET["$checkbox_field"];
                                    } elseif (isset($_POST["$checkbox_field"])) {
                                        $ARIG_checked = $_POST["$checkbox_field"];
                                    }
                                    if (isset($_GET["$rank_field"])) {
                                        $ARIG_ranked = $_GET["$rank_field"];
                                    } elseif (isset($_POST["$rank_field"])) {
                                        $ARIG_ranked = $_POST["$rank_field"];
                                    }
                                    $ARIG_checked = ereg_replace("[^A-Z]", "", $ARIG_checked);
                                    $ARIG_ranked = ereg_replace("[^-0-9]", "", $ARIG_ranked);

                                    $stmtA = '';
                                    $stmtB = '';
                                    $stmtC = '';
                                    $ARIG_updated = 0;
                                    $ARIG_changenotes = '';
                                    if (($ARIG_check[$o] == '') and ($ARIG_checked == 'YES')) {
                                        if (strlen($ARIG_close[$o]) < 4) {
                                            $ARIG_close[$o] = ' - ';
                                        }
                                        $stmtA = "UPDATE vicidial_users set closer_campaigns=' $group_id$ARIG_close[$o]' where user='$ARIG_user[$o]';";
                                        $rslt = mysql_query($stmtA, $link);
                                        if ($DB > 0) {
                                            echo "|$stmtA|";
                                        }
                                        $ARIG_updated++;
                                        $ARIG_changenotes .= "added $group_id to selected in-groups|";
                                    }
                                    if (($ARIG_check[$o] == ' CHECKED') and ($ARIG_checked == '')) {
                                        $ARIG_close[$o] = preg_replace("/ $group_id /", ' ', $ARIG_close[$o]);
                                        $stmtB = "UPDATE vicidial_users set closer_campaigns='$ARIG_close[$o]' where user='$ARIG_user[$o]';";
                                        $rslt = mysql_query($stmtB, $link);
                                        if ($DB > 0) {
                                            echo "|$stmtB|";
                                        }
                                        $ARIG_updated++;
                                        $ARIG_changenotes .= "removed $group_id from selected in-groups|";
                                    }
                                    if (($ARIG_ranked < $ARIG_rank[$o]) or ($ARIG_ranked > $ARIG_rank[$o])) {
                                        $stmtC = "UPDATE vicidial_inbound_group_agents set group_rank='$ARIG_ranked',group_weight='$ARIG_ranked' where user='$ARIG_user[$o]' and group_id='$group_id';";
                                        $rslt = mysql_query($stmtC, $link);
                                        if ($DB > 0) {
                                            echo "|$stmtC|";
                                        }
                                        $ARIG_updated++;
                                        $ARIG_changenotes .= "changed rank from $ARIG_rank[$o] to $ARIG_ranked|";
                                    }
                                    if ($ARIG_updated > 0) {
                                        ### LOG INSERTION Admin Log Table ###
                                        $SQL_log = "$stmtA|$stmtB|$stmtC|";
                                        $SQL_log = ereg_replace(';', '', $SQL_log);
                                        $SQL_log = addslashes($SQL_log);
                                        $stmt = "INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='USERS', event_type='MODIFY', record_id='$ARIG_user[$o]', event_code='USER INGROUP SETTINGS', event_sql=\"$SQL_log\", event_notes='$ARIG_changenotes';";
                                        if ($DB) {
                                            echo "|$stmt|\n";
                                        }
                                        $rslt = mysql_query($stmt, $link);
                                    }
                                }

                                $stmt = "SELECT vu.user,viga.group_rank,calls_today,full_name,closer_campaigns from vicidial_inbound_group_agents viga, vicidial_users vu where group_id='$group_id' and active='Y' and vu.user=viga.user;";
                                $rsltx = mysql_query($stmt, $link);
                                $users_to_print = mysql_num_rows($rsltx);

                                $o = 0;
                                while ($users_to_print > $o) {
                                    $rowx = mysql_fetch_row($rsltx);
                                    $o++;

                                    $ARIG_user[$o] = $rowx[0];
                                    $ARIG_rank[$o] = $rowx[1];
                                    $ARIG_calls[$o] = $rowx[2];
                                    $ARIG_name[$o] = $rowx[3];
                                    $ARIG_close[$o] = $rowx[4];
                                    $ARIG_check[$o] = '';
                                    if (preg_match("/ $group_id /", $ARIG_close[$o])) {
                                        $ARIG_check[$o] = ' CHECKED';
                                    }
                                }
                            }

                            $checkbox_count = 0;
                            $o = 0;
                            while ($users_to_print > $o) {
                                $o++;



                                $checkbox_field = "CHECK_$ARIG_user[$o]$US$group_id";
                                $rank_field = "RANK_$ARIG_user[$o]$US$group_id";
                                $grade_field="GRADE_$ARIG_user[$o]$US$group_id";
                                $checkbox_count++;

                                $users_output .= "<tr><td><a href=\"$PHP_SELF?ADD=3&user=$ARIG_user[$o]\">$ARIG_name[$o]</a></td>\n";
                                $users_output .= "<td><input type=checkbox name=\"$checkbox_field\" id=\"$checkbox_field\" value=\"YES\"$ARIG_check[$o]><label for='$checkbox_field'><span></span></label></td>\n";
                                $users_output .= "<td><select class='min-input' name=$rank_field>\n";
                                $h = "9";
                                while ($h >= -9) {
                                    $users_output .= "<option value=\"$h\"";
                                    if ($h == $ARIG_rank[$o]) {
                                        $users_output .= " SELECTED";
                                    }
                                    $users_output .= ">$h</option>\n";
                                    $h--;
                                }
                                $users_output .= "</select></td>\n";
                                $users_output .= "<td><select class='hide' name=\"$grade_field\">\n";
                                $h="10";
                                while ($h>=1)
				{
				$users_output .= "<option value=\"$h\"";
				if ($h==$ARIG_grade[$o])
					{$users_output .= " SELECTED";}
				$users_output .= ">$h</option>";
				$h--;
				}
                                $users_output .= "</select>\n";
                                $users_output .= "$ARIG_calls[$o]</td></tr>\n";
                            }


                            echo "$users_output";
                            ?>
                        </tbody>
                    </table> 


                    <div class="clear"></div>
                    <div class="seperator_dashed"></div>
                    <div class="grid-content">
                    <p class="text-right">
                        <button class="btn btn-success">Gravar</button>
                    </p>
                    </div>


                    </FORM>
                </div>
        </div>
    </div>
    
     
<?php include 'audio-modal.php'; ?>
   
    
      <script type="text/javascript">
          
          var se=new RegExp("VOICEMAIL|MESSAGE|INGROUP|DID");
          function populate_options(array, selected, keys) {
                var populus = "";
                if (keys===undefined) {
                    for (var key in array) {
                        populus+="<option value='"+key+"'" + ((key === selected) ? "SELECTED" : "") + " >"+array[key]+"</option>";
                    }
                } else {
                    for (var key in array) {
                        populus+="<option value='"+array[key]+"'" + ((array[key] === selected) ? "SELECTED" : "") + " >"+array[key]+"</option>";
                    }
                }
                return populus;
            }
               
            function dynamic_call_action(option, route, value, chooser_height)
            {
                var call_menu_list = <?php echo json_encode($call_menu_list) ?>;
                var ingroup_list = <?php echo json_encode($ingroup_list) ?>;
                var IGcampaign_id_list = <?php echo json_encode($IGcampaign_id_list) ?>;
                var IGhandle_method_list = <?php echo json_encode($IGhandle_method_list) ?>;
                var IGsearch_method_list = <?php echo json_encode($IGsearch_method_list) ?>;
                var did_list = <?php echo json_encode($did_list) ?>;
                var voice_mail_list = <?php echo json_encode($voice_mail_list) ?>;
                var selected_value = '';
                var selected_context = '';
                var new_content = '';

                var select_list = document.getElementById(option + "");
                var selected_route = select_list.value;
                var span_to_update = document.getElementById(option + "_value_span");

                if (selected_route === 'CALLMENU')
                {
                    if (route === selected_route)
                    {
                        selected_value = '<option SELECTED value="' + value + '">' + value + "</option>\n";
                    }
                    else
                    {
                        value = '';
                    }
                    new_content = '<div class="formRow op fix"><label data-t="tooltip" title=""><span name=' + option + '_value_link id=' + option + '_value_link><a href="admin.php?ADD=3511&amp;menu_id=' + value + '">IVR: </a></span></label><div class="formRight"><select class="span" name=' + option + '_value id=' + option + "_value onChange=\"dynamic_call_action_link('" + option + "','CALLMENU');\">" + populate_options(call_menu_list,selected_value) + '</select></div></div>';
                }
                if (selected_route === 'INGROUP')
                {
                    if ((route !== selected_route) || (value.length < 10))
                    {
                        value = 'SALESLINE,CID,LB,998,TESTCAMP,1,,,,';
                    }
                    var value_split = value.split(",");
                    var IGgroup_id = value_split[0];
                    var IGhandle_method = value_split[1];
                    var IGsearch_method = value_split[2];
                    var IGlist_id = value_split[3];
                    var IGcampaign_id = value_split[4];
                    var IGphone_code = value_split[5];
                    var IGvid_enter_filename = value_split[6];
                    var IGvid_id_number_filename = value_split[7];
                    var IGvid_confirm_filename = value_split[8];
                    var IGvid_validate_digits = value_split[9];

                    if (route === selected_route)
                    {
                        selected_value = IGgroup_id;
                    }

                    new_content = new_content + '<div class="formRow op fix"><label data-t="tooltip" title=""><span name=' + option + '_value_link id=' + option + '_value_link><a href="admin.php?ADD=3111&amp;group_id=' + IGgroup_id + '">Grupo Inbound:</a> </span> </label><div class="formRight"><select class="span" name=IGgroup_id_' + option + ' id=IGgroup_id_' + option + ' onChange="dynamic_call_action_link("IGgroup_id_' + option + '","INGROUP");">' + populate_options(ingroup_list,selected_value) + '</select></div></div>';
                    new_content = new_content + '<div class="formRow op fix"><label data-t="tooltip" title="">Handle Method:</label><div class="formRight"><select class="span" name=IGhandle_method_' + option + ' id=IGhandle_method_' + option + '>' + populate_options(IGhandle_method_list,IGhandle_method,1) + '</select></div></div>';
                    new_content = new_content + '<div class="formRow op fix"><label data-t="tooltip" title="">Search Method:</label><div class="formRight"><select class="span" name=IGsearch_method_' + option + ' id=IGsearch_method_' + option + '>' + populate_options(IGsearch_method_list,IGsearch_method) + '</select></div></div>';
                    new_content = new_content + '<div class="formRow op fix"><label data-t="tooltip" title="">List ID:</label><div class="formRight"><input type=text class="span2" maxlength=14 name=IGlist_id_' + option + ' id=IGlist_id_' + option + ' value="' + IGlist_id + '"></div></div>';
                    new_content = new_content + '<div class="formRow op fix"><label data-t="tooltip" title="">Campaign ID:</label><div class="formRight"><select class="chzn-select" name=IGcampaign_id_' + option + ' id=IGcampaign_id_' + option + '>' + populate_options(IGcampaign_id_list,IGcampaign_id) + '</select></div></div>';
                    new_content = new_content + '<div class="formRow op fix hide"><label data-t="tooltip" title="">Phone Code:</label><div class="formRight"><input type=text class="span" maxlength=14 name=IGphone_code_' + option + ' id=IGphone_code_' + option + ' value="' + IGphone_code + '"></div></div>';

                }
                if (selected_route === 'DID')
                {
                    if (route === selected_route)
                    {
                        selected_value = value;
                    }
                    else
                    {
                        value = '';
                    }
                    new_content = '<div class="formRow op fix"><label data-t="tooltip" title=""><span name=' + option + '_value_link id=' + option + '_value_link><a href="admin.php?ADD=3311&amp;did_pattern=' + value + '">DID:</a> </span></label><div class="formRight"><select class="chzn-select" name=' + option + '_value id=' + option + "_value onChange=\"dynamic_call_action_link('" + option + "','DID');\">" + populate_options(did_list,selected_value) + '</select></div></div>';
                }
                if (selected_route === 'MESSAGE')
                {
                    if (route === selected_route)
                    {
                        selected_value = value;
                    }
                    else
                    {
                        value = 'nbdy-avail-to-take-call|vm-goodbye';
                    }
                    new_content = "<div class='formRow op fix'><label data-t='tooltip' title=''>Audio File:</label><div class='formRight'>\n\
                                <div class=\"input-append\">\n\
                                    <input type=\"text\" placeholder=\"Escolha um ficheiro com o gestor de audio...\" class=\"span6\" value="+value+"  name=" + option + "_value id=" + option + "_value readonly/>\n\
                                            <a href=\"#\" class=\"btn audio-clean\"><i class=\"icon-remove-circle\"></i>Limpar</a>\n\
                                            <a href=\"#\" class=\"btn audio-open\"><i class=\"icon-bullhorn\"></i>Gestor de Audio</a>\n\
                                            </div>\n\
                                            </div></div>";
                }
                if (selected_route === 'EXTENSION')
                {
                    if ((route !== selected_route) || (value.length < 3))
                    {
                        value = '8304,default';
                    }
                    var value_split = value.split(",");
                    var EXextension = value_split[0];
                    var EXcontext = value_split[1];

                    new_content = "<div class='formRow op fix'><label data-t='tooltip' title=''>Extension:</label><div class='formRight'><input type=text name=EXextension_" + option + " id=EXextension_" + option + " class='span' maxlength=255 value=\"" + EXextension + "\"></div></div><div class='formRow op fix'><label data-t='tooltip' title=''>Context:</label><div class='formRight'><input type=text name=EXcontext_" + option + " id=EXcontext_" + option + " class='span' maxlength=255 value=\"" + EXcontext + "\"></div></div>";
                }
                if (selected_route === 'VOICEMAIL')
                {
                    if (route === selected_route)
                    {
                        selected_value = value;
                    }
                    else
                    {
                        value = '101';
                    }
                    new_content = "<div class='formRow op fix'><label data-t='tooltip' title=''>Voicemail Box:</label><div class='formRight'>\n\
                                        <select name=" + option + "_value id=" + option + "_value class='chzn-select'>\n\
                                            "+populate_options(voice_mail_list, value)+"\n\
                                        </select>\n\
                                  </div></div>";
                }

                if (new_content.length < 1)
                {
                    new_content = selected_route;
                }

                span_to_update.innerHTML = new_content;
                if (selected_route.match(se))
                {$(".chzn-select").chosen({no_results_text: "Não foi encontrado."});}
            }

            function dynamic_call_action_link(field, route)
            {
                var selected_value = '';
                var new_content = '';

                if ((route === 'CALLMENU') || (route === 'DID'))
                {
                    var select_list = document.getElementById(field + "_value");
                }
                if (route === 'INGROUP')
                {
                    var select_list = document.getElementById(field + "");
                    field = field.replace(/IGgroup_id_/, "");
                }
                var selected_value = select_list.value;
                var span_to_update = document.getElementById(field + "_value_link");

                if (route === 'CALLMENU')
                {
                    new_content = '<a href="admin.php?ADD=3511&amp;menu_id=' + selected_value + '">IVR:</a>';
                }
                if (route === 'INGROUP')
                {
                    new_content = '<a href="admin.php?ADD=3111&amp;group_id=' + selected_value + '">Grupo Inbound:</a>';
                }
                if (route === 'DID')
                {
                    new_content = '<a href="admin.php?ADD=3311&amp;did_pattern=' + selected_value + '">DID:</a>';
                }

                if (new_content.length < 1)
                {
                    new_content = selected_route;
                }

                span_to_update.innerHTML = new_content;
                
            }
            //var que liga o gestor de audio ao input  !!IMPORTANT!!
            var audio_on_fly;
 $(function(){
                $("[data-t=tooltip]").tooltip({placement:"right",html:true});
                $('#colorp').colorpicker();
                $(".chzn-select").chosen({no_results_text: "Não foi encontrado."});
                
                $("#loader").fadeOut("slow");
                
                $(".audio-open").live("click",function(){
                    audio_on_fly=$(this).parent().find("input");
                    $("#modal-audio-upload")
                            .modal()
                            .find(".modal-header h4")
                            .text(audio_on_fly.parent().parent().parent().find("label").text());
                    return false;});
                
                $("#rank_check_all").toggle(
                   function(){
                        $("#rank > tbody td:nth-child(2) input[type=checkbox]").prop('checked', true);
                   return false;
                   },
                   function(){
                       $("#rank > tbody td:nth-child(2) input[type=checkbox]").prop('checked', false);
                   return false;
                   });
                $(".audio-clean").live("click",function(){
                    $(this).parent().find("input").val("");
                    return false;
                });
               
 });
               </script>  
<?php
#FOOTER
$footer = ROOT . "ini/footer.php";
require($footer);
?>		