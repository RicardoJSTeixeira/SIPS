<?
require "../functions.php";

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$PHP_AUTH_USER=$_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW=$_SERVER["PHP_AUTH_PW"];

function help($where, $text) {
    return '<a onclick="openNewWindow(\'../admin.php?ADD=99999#' . $where . '\')" >' . $text . '</a>';
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

            function only_array_values($array){
                $values=array();
                foreach ($array as $value) {
                    $values[$value]=$value;
                }
                return $values;
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
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
    <style>
        [name="did_route"],[name="filter_action"]{
            border-color:rgba(82, 168, 236, 0.8);
        }
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
        #table-options td{
            vertical-align: middle;
        }
    </style>
    </head>
    <body>
    <?php
$dtmf[0]='0';			$dtmf_key[0]='0';
$dtmf[1]='1';			$dtmf_key[1]='1';
$dtmf[2]='2';			$dtmf_key[2]='2';
$dtmf[3]='3';			$dtmf_key[3]='3';
$dtmf[4]='4';			$dtmf_key[4]='4';
$dtmf[5]='5';			$dtmf_key[5]='5';
$dtmf[6]='6';			$dtmf_key[6]='6';
$dtmf[7]='7';			$dtmf_key[7]='7';
$dtmf[8]='8';			$dtmf_key[8]='8';
$dtmf[9]='9';			$dtmf_key[9]='9';
$dtmf[10]='HASH';		$dtmf_key[10]='#';
$dtmf[11]='STAR';		$dtmf_key[11]='*';
$dtmf[12]='A';			$dtmf_key[12]='A';
$dtmf[13]='B';			$dtmf_key[13]='B';
$dtmf[14]='C';			$dtmf_key[14]='C';
$dtmf[15]='D';			$dtmf_key[15]='D';
$dtmf[16]='TIMECHECK';	$dtmf_key[16]='TIMECHECK';
$dtmf[17]='TIMEOUT';	$dtmf_key[17]='TIMEOUT';
$dtmf[18]='INVALID';	$dtmf_key[18]='INVALID';


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
        
                  ##### get call_times listing for dynamic pulldown
	$stmt="SELECT call_time_id,call_time_name from vicidial_call_times order by call_time_id";
	$rslt=mysql_query($stmt, $link);
	$times_to_print = mysql_num_rows($rslt);

	$o=0;
	while ($times_to_print > $o)
		{
		$rowx=mysql_fetch_row($rslt);
		//$call_times_list= "<option value=\"$rowx[0]\">$rowx[1]</option>\n";
		$call_times_list[$rowx[0]] = $rowx[1];
		$o++;
		}

	$IGhandle_method_list = array('CID','CIDLOOKUP','CIDLOOKUPRL','CIDLOOKUPRC','CIDLOOKUPALT','CIDLOOKUPRLALT','CIDLOOKUPRCALT','CIDLOOKUPADDR3','CIDLOOKUPRLADDR3','CIDLOOKUPRCADDR3','CIDLOOKUPALTADDR3','CIDLOOKUPRLALTADDR3','CIDLOOKUPRCALTADDR3','ANI','ANILOOKUP','ANILOOKUPRL','VIDPROMPT','VIDPROMPTLOOKUP','VIDPROMPTLOOKUPRL','VIDPROMPTLOOKUPRC','CLOSER','3DIGITID','4DIGITID','5DIGITID','10DIGITID');

	$IGsearch_method_list = array('LB'=>'Equilibrado','LO'=>'Equilibrado com Transbordo','SO','Só no próprio Servidor');

	$stmt="select login,server_ip,extension,dialplan_number from phones where active='Y' order by login,server_ip;";
	$rslt=mysql_query($stmt, $link);
	$phones_to_print = mysql_num_rows($rslt);
	$phone_list=array();
	$i=0;
	while ($i < $phones_to_print)
		{
		$row=mysql_fetch_row($rslt);
		$phone_list[$row[0]]= "$row[0] - $row[1]";
		$i++;
		}
                
        $voice_mail_list = array();
        $query = "SELECT voicemail_id,fullname,email,extension from phones where active='Y'  $LOGadmin_viewable_groupsSQL order by voicemail_id ASC";
        $rslt = mysql_query($query, $link);
        while ($row1 = mysql_fetch_row($rslt)) {
            $voice_mail_list[$row1[0]] = "$row1[3] - $row1[1]";
        }

if ($ADD==4511)
	{
if (strlen($menu_id) < 1)
			{
			?>
        <script>
            $(function(){makeAlert("#wr","IVR não modificado","O IVR está sem identificador.",1,true,false);});
        </script>
            <?php
			}
		else
			{
			

			$stmt="UPDATE vicidial_call_menu set menu_name='$menu_name',menu_prompt='$menu_prompt',menu_timeout='$menu_timeout',menu_timeout_prompt='$menu_timeout_prompt',menu_invalid_prompt='$menu_invalid_prompt',menu_repeat='$menu_repeat',menu_time_check='$menu_time_check',call_time_id='$call_time_id',track_in_vdac='$track_in_vdac',custom_dialplan_entry='$custom_dialplan_entry',tracking_group='$tracking_group' where menu_id='$menu_id';";
			$rslt=mysql_query($stmt, $link);

			$h=0;
			$option_value_list='|';
			while ($h <= 18)
				{
				$option_value=''; $option_description=''; $option_route=''; $option_route_value=''; $option_route_value_context='';

				if (isset($_GET["option_value_$h"]))				{$option_value=$_GET["option_value_$h"];}
					elseif (isset($_POST["option_value_$h"]))		{$option_value=$_POST["option_value_$h"];}
				if (isset($_GET["option_description_$h"]))			{$option_description=$_GET["option_description_$h"];}
					elseif (isset($_POST["option_description_$h"]))	{$option_description=$_POST["option_description_$h"];}
				if (isset($_GET["option_route_$h"]))				{$option_route=$_GET["option_route_$h"];}
					elseif (isset($_POST["option_route_$h"]))		{$option_route=$_POST["option_route_$h"];}
				if (isset($_GET["option_route_value_$h"]))			{$option_route_value=$_GET["option_route_value_$h"];}
					elseif (isset($_POST["option_route_value_$h"]))	{$option_route_value=$_POST["option_route_value_$h"];}
				if (isset($_GET["option_route_value_context_$h"]))	{$option_route_value_context=$_GET["option_route_value_context_$h"];}
					elseif (isset($_POST["option_route_value_context_$h"]))	{$option_route_value_context=$_POST["option_route_value_context_$h"];}

				if ($option_route == "INGROUP")
					{
					if (isset($_GET["IGhandle_method_$h"]))						{$IGhandle_method=$_GET["IGhandle_method_$h"];}
						elseif (isset($_POST["IGhandle_method_$h"]))			{$IGhandle_method=$_POST["IGhandle_method_$h"];}
					if (isset($_GET["IGsearch_method_$h"]))						{$IGsearch_method=$_GET["IGsearch_method_$h"];}
						elseif (isset($_POST["IGsearch_method_$h"]))			{$IGsearch_method=$_POST["IGsearch_method_$h"];}
					if (isset($_GET["IGlist_id_$h"]))							{$IGlist_id=$_GET["IGlist_id_$h"];}
						elseif (isset($_POST["IGlist_id_$h"]))					{$IGlist_id=$_POST["IGlist_id_$h"];}
					if (isset($_GET["IGcampaign_id_$h"]))						{$IGcampaign_id=$_GET["IGcampaign_id_$h"];}
						elseif (isset($_POST["IGcampaign_id_$h"]))				{$IGcampaign_id=$_POST["IGcampaign_id_$h"];}
					if (isset($_GET["IGphone_code_$h"]))						{$IGphone_code=$_GET["IGphone_code_$h"];}
						elseif (isset($_POST["IGphone_code_$h"]))				{$IGphone_code=$_POST["IGphone_code_$h"];}
					if (isset($_GET["IGvid_enter_filename_$h"]))				{$IGvid_enter_filename=$_GET["IGvid_enter_filename_$h"];}
						elseif (isset($_POST["IGvid_enter_filename_$h"]))		{$IGvid_enter_filename=$_POST["IGvid_enter_filename_$h"];}
					if (isset($_GET["IGvid_id_number_filename_$h"]))			{$IGvid_id_number_filename=$_GET["IGvid_id_number_filename_$h"];}
						elseif (isset($_POST["IGvid_id_number_filename_$h"]))	{$IGvid_id_number_filename=$_POST["IGvid_id_number_filename_$h"];}
					if (isset($_GET["IGvid_confirm_filename_$h"]))				{$IGvid_confirm_filename=$_GET["IGvid_confirm_filename_$h"];}
						elseif (isset($_POST["IGvid_confirm_filename_$h"]))		{$IGvid_confirm_filename=$_POST["IGvid_confirm_filename_$h"];}
					if (isset($_GET["IGvid_validate_digits_$h"]))				{$IGvid_validate_digits=$_GET["IGvid_validate_digits_$h"];}
						elseif (isset($_POST["IGvid_validate_digits_$h"]))		{$IGvid_validate_digits=$_POST["IGvid_validate_digits_$h"];}

					$option_route_value_context = "$IGhandle_method,$IGsearch_method,$IGlist_id,$IGcampaign_id,$IGphone_code,$IGvid_enter_filename,$IGvid_id_number_filename,$IGvid_confirm_filename,$IGvid_validate_digits";
					}

				if ($non_latin < 1)
					{
					$option_value = ereg_replace("[^-\_0-9A-Z]","",$option_value);
					$option_description = ereg_replace("[^- \:\/\_0-9a-zA-Z]","",$option_description);
					$option_route = ereg_replace("[^-_0-9a-zA-Z]","",$option_route);
					$option_route_value = ereg_replace("[^-\/\|\_\#\*\,\.\_0-9a-zA-Z]","",$option_route_value);
					$option_route_value_context = ereg_replace("[^,-_0-9a-zA-Z]","",$option_route_value_context);
					}

				if (strlen($option_route) > 0)
					{
					$stmtA="SELECT count(*) from vicidial_call_menu_options where menu_id='$menu_id' and option_value='$option_value';";
					$rslt=mysql_query($stmtA, $link);
					$row=mysql_fetch_row($rslt);
					$option_exists = $row[0];

					if ($option_exists > 0)
						{
						$stmtA="UPDATE vicidial_call_menu_options SET option_description='$option_description',option_route='$option_route',option_route_value='$option_route_value',option_route_value_context='$option_route_value_context' where menu_id='$menu_id' and option_value='$option_value';";
						$rslt=mysql_query($stmtA, $link);
						$stmtAX .= "$stmtA|";
						}
					else
						{
						$stmtA="INSERT INTO vicidial_call_menu_options SET menu_id='$menu_id',option_value='$option_value',option_description='$option_description',option_route='$option_route',option_route_value='$option_route_value',option_route_value_context='$option_route_value_context';";
						$rslt=mysql_query($stmtA, $link);
						$stmtAX .= "$stmtA|";
						}
					}
				else
					{
					$stmtA="SELECT count(*) from vicidial_call_menu_options where menu_id='$menu_id' and option_value='$option_value';";
					$rslt=mysql_query($stmtA, $link);
					$row=mysql_fetch_row($rslt);
					$option_exists_db = $row[0];

					if ($option_exists_db > 0)
						{
						$stmtA="DELETE FROM vicidial_call_menu_options where menu_id='$menu_id' and option_value='$option_value';";
						$rslt=mysql_query($stmtA, $link);
						$stmtAX .= "$stmtA|";
						}
					}
				$option_value_list .= "$option_value|";
				$h++;
				}
			## delete existing database records that were not in the submit
			while ($h <= 18)
				{
				if (!preg_match("/\|$dtmf[$h]\|/i",$option_value_list))
					{
					$stmtA="SELECT count(*) from vicidial_call_menu_options where menu_id='$menu_id' and option_value='$dtmf[$h]';";
					$rslt=mysql_query($stmtA, $link);
					$row=mysql_fetch_row($rslt);
					$option_exists_db = $row[0];

					if ($option_exists_db > 0)
						{
						$stmtA="DELETE FROM vicidial_call_menu_options where menu_id='$menu_id' and option_value='$dtmf[$h]';";
						$rslt=mysql_query($stmtA, $link);
						$stmtAX .= "$stmtA|";
						}
					}
				$h++;
				}

                                 ?>
        <script>
            $(function(){makeAlert("#wr","IVR Modificado com Sucesso","",4,true,false);});
        </script>
            <?php
                
                                
			$stmtA="UPDATE servers set rebuild_conf_files='Y' where generate_vicidial_conf='Y' and active_asterisk_server='Y';";
			$rslt=mysql_query($stmtA, $link);
			$stmtAX .= "$stmtA|";

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|$stmtAX";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='CALLMENUS', event_type='MODIFY', record_id='$menu_id', event_code='ADMIN MODIFY CALL MENU', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
			}
$stmt="SELECT menu_name,menu_prompt,menu_timeout,menu_timeout_prompt,menu_invalid_prompt,menu_repeat,menu_time_check,call_time_id,track_in_vdac,custom_dialplan_entry,tracking_group from vicidial_call_menu where menu_id='$menu_id';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$menu_name =			$row[0];
		$menu_prompt =			$row[1];
		$menu_timeout =			$row[2];
		$menu_timeout_prompt =	$row[3];
		$menu_invalid_prompt =	$row[4];
		$menu_repeat =			$row[5];
		$menu_time_check =		$row[6];
		$call_time_id =			$row[7];
		$track_in_vdac =		$row[8];
		$custom_dialplan_entry= $row[9];
		$tracking_group =		$row[10];

            
                     
		?>


        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <script>
            function openNewWindow(url) 
		{
		window.open (url,"",'width=620,height=300,scrollbars=yes,menubar=yes,address=yes');
		}
        </script>
        <div class=content>
		
		<form action=edit_ivr.php method=POST name=admin_form id=admin_form>
		<input type=hidden name=ADD value=4511>
		<input type=hidden name=menu_id value="<?=$menu_id?>">
                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Edita IVR</div>
                        <div class="pull-right"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-content">
        <div id="wr"></div>
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-menu_id","Menu ID")?>: 
                            </label>
                            <div class="formRight">
                                <b><?=$menu_id?></b>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-menu_name","Menu Name")?>: 
                            </label>
                            <div class="formRight">
                                <input type=text name=menu_name class="span" maxlength=50 value="<?=$menu_name?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-menu_prompt","Menu Prompt")?>: 
                            </label>
                            <div class="formRight">
                                <div class="input-append">
                                    <input type=text name=menu_prompt id=menu_prompt  class="span6" readonly maxlength=255 value="<?=$menu_prompt?>"> 
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-menu_timeout","Menu Timeout")?>: 
                            </label>
                            <div class="formRight">
                                <input type=text name=menu_timeout class="span" maxlength=5 value="<?=$menu_timeout?>">
                            </div>
                        </div>  
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-menu_timeout_prompt","Menu Timeout Prompt")?>: 
                            </label>
                            <div class="formRight">
                                <div class="input-append">
                                    <input type=text name=menu_timeout_prompt id=menu_timeout_prompt  class="span6" readonly maxlength=255 value="<?=$menu_timeout_prompt?>">
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                            </div>
                        </div>   
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-menu_invalid_prompt","Menu Invalid Prompt")?>: 
                            </label>
                            <div class="formRight">
                                <div class="input-append">
                                    <input type=text name=menu_invalid_prompt id=menu_invalid_prompt  class="span6" readonly maxlength=255 value="<?=$menu_invalid_prompt?>">
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                </div>
                            </div>
                        </div>   
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-menu_repeat","Menu Repeat")?>: 
                            </label>
                            <div class="formRight">
                                <input type=text name=menu_repeat class="span" maxlength=3 value="<?=$menu_repeat?>">
                            </div>
                        </div>   
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-menu_time_check","Menu Timecheck")?>: 
                            </label>
                            <div class="formRight">
                                <select name=menu_time_check>
                                <option <?=($menu_time_check > 0)?" ":"selected "?>value="0">No Time Check</option>
                                <option <?=($menu_time_check > 0)?"selected ":""?>value="1">Time Check</option>
                                </select>
                            </div>
                        </div>    
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-call_time_id","Call Time")?>: 
                            </label>
                            <div class="formRight">
                                <select class="span" name=call_time_id>
                                <?=populate_options($call_times_list,$call_time_id)?>
                                </select>
                            </div>
                        </div>     
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-track_in_vdac","Track Calls in Realtime Report")?>: 
                            </label>
                            <div class="formRight">
                                <select name=track_in_vdac>
                                    <option <?=($track_in_vdac > 0)?"":"selected "?>value="0">No Realtime Tracking</option>
                                    <option <?=($track_in_vdac > 0)?"selected ":""?> value="1">Realtime Tracking</option>
                                </select>
                            </div>
                        </div>
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                <?=help("vicidial_call_menu-tracking_group","Tracking Group")?>
                            </label>
                            <div class="formRight">
                                <select class="span" name=tracking_group>
                                <option value="CALLMENU">CALLMENU - Default</option>
                                <?=populate_options($ingroup_list,$tracking_group)?>
                                </select>
                            </div>
                        </div>

                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        <p class="text-right">
                            <button class="btn btn-success">Gravar</button>
                        </p> 
			
                    </div>    
                </div>
		
                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Opções do IVR</div>
                        <div class="pull-right"></div>
                        <div class="clear"></div>
                    </div>
		
                    <div class="grid-content">
                        <table class="table table-condensed table-striped" id="table-options">
                                <thead>
                                    <tr>
                                        <th>Opção</th>
                                        <th>Descrição</th>
                                        <th><?=help("vicidial_call_menu-option_value","Destino")?></th>
                                    </tr>
                                </thead>
                                <tbody>
<?php
		$j=0;
		$stmtA="SELECT option_value,option_description,option_route,option_route_value,option_route_value_context from vicidial_call_menu_options where menu_id='$menu_id' order by option_value;";
		$rslt=mysql_query($stmtA, $link);
		$menus_to_print = mysql_num_rows($rslt);

		while ($menus_to_print > $j)
			{
			$row=mysql_fetch_row($rslt);
			$Aoption_value[$j] =				$row[0];
			$Aoption_description[$j] =			$row[1];
			$Aoption_route[$j] =				$row[2];
			$Aoption_route_value[$j] =			$row[3];
			$Aoption_route_value_context[$j] =	$row[4];
			$j++;
			}

		$j=0;
		while ($menus_to_print > $j)
			{
			$choose_height = (($j * 40) + 400);
			$option_value =					$Aoption_value[$j];
			$option_description =			$Aoption_description[$j];
			$option_route =					$Aoption_route[$j];
			$option_route_value =			$Aoption_route_value[$j];
			$option_route_value_context =	$Aoption_route_value_context[$j];

			$dtmf_list = "<select class=\"span\" name=option_value_$j>\n";
			$h=0;
			while ($h <= 18)
				{
				$dtmf_list .= "\t<option";
				if ( (preg_match("/$dtmf[$h]/",$option_value) and (strlen($option_value) == strlen($dtmf[$h])) ) )
					{$dtmf_list .= " selected";}
				$dtmf_list .= " value=\"$dtmf[$h]\">$dtmf_key[$h]</option>\n";
				$h++;
				}
			$dtmf_list .= "</select>";
?>
	
                            <tr>
                            <td>
                                <?=$dtmf_list?>
                            </td>
                            <td>
                                <input type=text name=option_description_<?=$j?> class="span" maxlength=255 value="<?=$option_description?>">
                            </td>
                            <td>
                                <select class="span" name=option_route_<?=$j?> id=option_route_<?=$j?> onChange="call_menu_option('<?=$j?>','','','','<?=$choose_height?>');">
                                            <option<?=($option_route=="CALLMENU")?" selected ":""?> value="CALLMENU">IVR</option>
                                            <option<?=($option_route=="INGROUP")?" selected ":""?> value="INGROUP">Grupo Inbound</option>
                                            <option<?=($option_route=="DID")?" selected ":""?> value="DID">DDI</option>
                                            <option<?=($option_route=="HANGUP")?" selected ":""?> value="HANGUP">Desligar</option>
                                            <option<?=($option_route=="EXTENSION")?" selected ":""?> value="EXTENSION">Extensão</option>
                                            <option<?=($option_route=="PHONE")?" selected ":""?> value="PHONE">Licença</option>
                                            <option<?=($option_route=="VOICEMAIL")?" selected ":""?> value="VOICEMAIL">VoiceMail</option>
                                            <!--<option<?=($option_route=="AGI")?" selected ":""?> value="AGI">AGI</option>-->
                                            <option value="">!!Apagar!!</option>
                                     </select>
                                
                            </td>
                        </tr>
                                                
                    
			<tr><td colspan="3"><div id="option_value_value_context_<?=$j?>" name="option_value_value_context_<?=$j?>">
			<?php
			if ($option_route=='CALLMENU')
				{?>
                              
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <span name=option_route_link_<?=$j?> id=option_route_link_<?=$j?>>
                                        <a href="<?=$PHP_SELF?>?ADD=3511&menu_id=<?=$option_route_value?>">IVR:</a>
                                    </span>
                                </label>
                                <div class="formRight">
                                    <select class="span" name=option_route_value_<?=$j?> id=option_route_value_<?=$j?> onChange="call_menu_link('<?=$j?>','CALLMENU');">
                                        <?=populate_options($call_menu_list,$option_route_value)?>
                                    </select>
                                </div>
                            </div> 
                            
				
				
				<?php }
			if ($option_route=='INGROUP')
				{
				if (strlen($option_route_value_context) < 10)
					{$option_route_value_context = 'CID,LB,998,TESTCAMP,1,,,,';}
				$IGoption_route_value_context = explode(",",$option_route_value_context);
				$IGhandle_method =			$IGoption_route_value_context[0];
				$IGsearch_method =			$IGoption_route_value_context[1];
				$IGlist_id =				$IGoption_route_value_context[2];
				$IGcampaign_id =			$IGoption_route_value_context[3];
				$IGphone_code =				$IGoption_route_value_context[4];
				$IGvid_enter_filename =		$IGoption_route_value_context[5];
				$IGvid_id_number_filename =	$IGoption_route_value_context[6];
				$IGvid_confirm_filename =	$IGoption_route_value_context[7];
				$IGvid_validate_digits =	$IGoption_route_value_context[8];
                                ?>
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <span name=option_route_link_<?=$j?> id=option_route_link_<?=$j?>>
                                        <a href="<?=$PHP_SELF?>?ADD=3111&group_id=<?=$option_route_value?>">Grupo Inbound:</a>
                                    </span>
                                </label>
                                <div class="formRight">
                                    <input type=hidden name=option_route_value_context_<?=$j?> id=option_route_value_context_<?=$j?> value="<?=$option_route_value_context?>">
                                    <select class="span" name=option_route_value_<?=$j?> id=option_route_value_<?=$j?> onChange="call_menu_link('<?=$j?>','INGROUP');">
                                        <?=populate_options($ingroup_list,$option_route_value)?>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Handle Method:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=IGhandle_method_<?=$j?> id=IGhandle_method_<?=$j?>>
                                        <?= populate_options($IGhandle_method_list,$IGhandle_method,false) ?>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Search Method:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=IGsearch_method_<?=$j?> id=IGsearch_method_<?=$j?>>
                                        <?=populate_options($IGsearch_method_list,$IGsearch_method)?>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    List ID:
                                </label>
                                <div class="formRight">
                                    <input type=text class="span2" maxlength=14 name=IGlist_id_<?=$j?> id=IGlist_id_<?=$j?> value="<?=$IGlist_id?>">
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Campaign ID:
                                </label>
                                <div class="formRight">
                                    <select class="chzn-select" name=IGcampaign_id_<?=$j?> id=IGcampaign_id_<?=$j?>>
                                        <?=populate_options($IGcampaign_id_list,$IGcampaign_id)?>
                                    </select>
                                </div>
                            </div> 
                            <div class="formRow op fix hide">
                                <label data-t="tooltip" title="">
                                    Phone Code:
                                </label>
                                <div class="formRight">
                                    <input type=text class="span" maxlength=14 name=IGphone_code_<?=$j?> id=IGphone_code_<?=$j?> value="<?=$IGphone_code?>">
                                </div>
                            </div>  
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    VID Enter Filename:
                                </label>
                                <div class="formRight">
                                    <div class="input-append">
                                        <input type=text name=IGvid_enter_filename_<?=$j?> id=IGvid_enter_filename_<?=$j?>  class="span6" readonly maxlength=255 value="<?=$IGvid_enter_filename?>"> 
                                        <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                        <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                    </div>
                                </div>
                            </div>  
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    VID ID Number Filename:
                                </label>
                                <div class="formRight">
                                    <div class="input-append">
                                    <input type=text name=IGvid_id_number_filename_<?=$j?> id=IGvid_id_number_filename_<?=$j?>  class="span6" readonly maxlength=255 value="<?=$IGvid_id_number_filename?>">
                                    <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                    <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                    </div>
                                </div>
                            </div>  
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    VID Confirm Filename:
                                </label>
                                <div class="formRight">
                                    <div class="input-append">
                                        <input type=text name=IGvid_confirm_filename_<?=$j?> id=IGvid_confirm_filename_<?=$j?>  class="span6" readonly maxlength=255 value="<?=$IGvid_confirm_filename?>"> 
                                        <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                        <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                    </div>
                                </div>
                            </div> 
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    VID Digits:
                                </label>
                                <div class="formRight">
                                    <input type=text class="span1" maxlength=3 name=IGvid_validate_digits_<?=$j?> id=IGvid_validate_digits_<?=$j?> value="<?=$IGvid_validate_digits?>">
                                </div>
                            </div> 
				
                                <?php }
			if ($option_route=='DID')
				{ 
				$stmt="SELECT did_id from vicidial_inbound_dids where did_pattern='$option_route_value';";
				$rslt=mysql_query($stmt, $link);
				$row=mysql_fetch_row($rslt);
				$did_id =			$row[0];
				?>
                            
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    <span name=option_route_link_<?=$j?> id=option_route_link_<?=$j?>>
                                        <a href="edit_did?did_id=<?=$did_id?>">DID:</a>
                                    </span>
                                </label>
                                <div class="formRight">
                                    <select class="chzn-select" name=option_route_value_<?=$j?> id=option_route_value_<?=$j?> onChange="call_menu_link('<?=$j?>','DID');">
                                        <?=populate_options($did_list,$option_route_value)?>
                                    </select>
                                </div>
                            </div> 
				
				<?php }
			if ($option_route=='HANGUP')
				{ ?>
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Audio File:
                                </label>
                                <div class="formRight">
                                    <div class="input-append">
                                        <input type=text name=option_route_value_<?=$j?> id=option_route_value_<?=$j?> maxlength=255 value="<?=$option_route_value?>" class="span6" readonly> 
                                        <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                        <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                    </div>
                                </div>
                            </div> 
				<?php }
			if ($option_route=='EXTENSION')
				{ ?>
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Extension:
                                </label>
                                <div class="formRight">
                                    <input type=text name=option_route_value_<?=$j?> id=option_route_value_<?=$j?> class="span" maxlength=255 value="<?=$option_route_value?>"> Context: <input type=text name=option_route_value_context_<?=$j?> id=option_route_value_context_<?=$j?> class="span" maxlength=255 value="<?=$option_route_value_context?>">
                                </div>
                            </div> 
				
				<?php }
			if ($option_route=='PHONE')
				{ ?>
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Phone:
                                </label>
                                <div class="formRight">
                                    <select class="span" name=option_route_value_<?=$j?> id=option_route_value_<?=$j?>>
                                        <?=populate_options($phone_list,$option_route_value)?>
                                    </select>
                                </div>
                            </div> 
				<?php }
			if ($option_route=='VOICEMAIL')
				{?>
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Voicemail Box:
                                </label>
                                <div class="formRight">
                                    <input type=text name=option_route_value_<?=$j?> id=option_route_value_<?=$j?> class="span" maxlength=10 value="<?=$option_route_value?>"> <a href="javascript:launch_vm_chooser('option_route_value_<?=$j?>','vm',700);">voicemail chooser</a>
                                </div>
                            </div> 
				<?php }
			if ($option_route=='AGI')
				{ ?>
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    eAGI:
                                </label>
                                <div class="formRight">
                                    <input type=text name=option_route_value_<?=$j?> id=option_route_value_<?=$j?> class="span" maxlength=255 value="<?=$option_route_value?>">
                                </div>
                            </div>
				<?php }
?>
			</div></td></tr>
                        
                       
                                    
                        
		<?php
                        $j++;
			}

		while ($j <= 18)
			{
			$choose_height = (($j * 40) + 400);
			$dtmf_list = "<select class=\"span\" name=option_value_$j><option value=\"\"></option>";
			$h=0;
			while ($h <= 18)
				{
				$dtmf_list .= "<option value=\"$dtmf[$h]\"> $dtmf_key[$h]</option>";
				$h++;
				}
			$dtmf_list .= "</select>";

			if (eregi("1$|3$|5$|7$|9$", $j))
				{$bgcolor='bgcolor="#cccccc"';} 
			else
				{$bgcolor='bgcolor="#bcbcbc"';}
?>                   
                        
                        <tr>
                            <td>
                                <?=$dtmf_list?>
                            </td>
                            <td>
                                <input type=text name=option_description_<?=$j?> class="span" maxlength=255 value="">
                            </td>
                            <td>
                                <select class="span" name=option_route_<?=$j?> id=option_route_<?=$j?> onChange="call_menu_option('<?=$j?>','','','','<?=$choose_height?>');">
                                            <option value="CALLMENU">IVR</option>
                                            <option value="INGROUP">Grupo Inbound</option>
                                            <option value="DID">DDI</option>
                                            <option value="HANGUP">Desligar</option>
                                            <option value="EXTENSION">Extensão</option>
                                            <option value="PHONE">Licença</option>
                                            <option value="VOICEMAIL">VoiceMail</option>
                                            <!--<option>AGI</option>-->
                                            <option SELECTED value=""> </option>
                                    </select>
                                
                            </td>
                        </tr>
                        <tr><td colspan="3"><div id="option_value_value_context_<?=$j?>" name="option_value_value_context_<?=$j?>"></div></td></tr>
                       <?php $j++; } ?>
                        
                                </tbody>
                        </table>
                    <div class="formRow op fix hide">
                        <label data-t="tooltip" title="">
                            <?= help("vicidial_call_menu-custom_dialplan_entry", "Custom Dialplan Entry") ?>:
                        </label>
                        <div class="formRight">
                            <?php if ($SSallow_custom_dialplan > 0) { ?>
                                <TEXTAREA NAME=custom_dialplan_entry ROWS=5 COLS=70><?= $custom_dialplan_entry ?></TEXTAREA>
                            <?php } else { ?>
                                Disabled <input type=hidden name=custom_dialplan_entry value="">
                            <?php } ?>
                        </div>
			<div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        <p class="text-right">
                            <button class="btn btn-success">Gravar</button>
                        </p>
                    </div>  
                    </div>
                </div>
                </form>  
        </div>

        <?php include 'audio-modal.php'; ?>
        
    <script type='text/javascript'>
          var se=new RegExp("VOICEMAIL|MESSAGE|INGROUP|PHONE|DID");
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
            var a,b;
function call_menu_option(option,route,value,value_context,chooser_height)
		{
		var call_menu_list = <?php echo json_encode($call_menu_list); ?>;
		var ingroup_list = <?php echo json_encode($ingroup_list); ?>;
		var IGcampaign_id_list = <?php echo json_encode($IGcampaign_id_list); ?>;
		var IGhandle_method_list = <?php echo json_encode($IGhandle_method_list); ?>;
		var IGsearch_method_list = <?php echo json_encode($IGsearch_method_list); ?>;
		var did_list = <?php echo json_encode($did_list); ?>;
		var phone_list = <?php echo json_encode($phone_list); ?>;
                var voice_mail_list = <?php echo json_encode($voice_mail_list) ?>;
		var selected_value = '';
		var selected_context = '';
		var new_content = '';

		var select_list = document.getElementById("option_route_" + option);
		var selected_route = select_list.value;
		var span_to_update = document.getElementById("option_value_value_context_" + option);

		if (selected_route==='CALLMENU')
			{
			if (route === selected_route)
				{
				selected_value = value;
				}
			else
				{value='';}
			new_content = '<div class="formRow op fix">\n\
                                        <label data-t="tooltip" title="">\n\
                                            <span name=option_route_link_' + option + ' id=option_route_link_' + option + '>\n\
                                            <a href="edit_ivr.php?menu_id=' + value + '">IVR: </a>\n\
                                        </span>\n\
                                        </label>\n\
                                        <div class="formRight">\n\
                                            <select class="span" name=option_route_value_' + option + ' id=option_route_value_' + option + ' onChange="call_menu_link("' + option + '","CALLMENU");">\n\
                                            ' + populate_options(call_menu_list,selected_value) + '\n\
                                            </select>\n\
                                        </div>\n\
                                        </div>';
			}
		if (selected_route==='INGROUP')
			{
			if (value_context.length < 10)
				{value_context = 'CID,LB,998,TESTCAMP,1,,,,';}
			var value_context_split =		value_context.split(",");
			var IGhandle_method =			value_context_split[0];
			var IGsearch_method =			value_context_split[1];
			var IGlist_id =					value_context_split[2];
			var IGcampaign_id =				value_context_split[3];
			var IGphone_code =				value_context_split[4];
			var IGvid_enter_filename =		value_context_split[5];
			var IGvid_id_number_filename =	value_context_split[6];
			var IGvid_confirm_filename =	value_context_split[7];
			var IGvid_validate_digits =		value_context_split[8];

			if (route === selected_route)
				{
				selected_value = value;
				}

			new_content = '<input type=hidden name=option_route_value_context_' + option + ' id=option_route_value_context_' + option + ' value="' + selected_value + '">\n\
                                        <div class="formRow op fix">\n\
                                            <label data-t="tooltip" title="">\n\
                                                <span name=option_route_link_' + option + 'id=option_route_link_' + option + '>\n\
                                                 <a href="admin.php?ADD=3111&amp;group_id=' + value + '">Grupo Inbound:</a> </span>\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                                <select class="span" name=option_route_value_' + option + ' id=option_route_value_' + option + ' onChange="call_menu_link(\'' + option + '\',\'INGROUP\');">'
                                                + populate_options(ingroup_list,selected_value) + '</select>\n\
                                            </div>\n\
                                        </div>\n\
                                        \n\
                                        <div class="formRow op fix">\n\
                                            <label data-t="tooltip" title="">\n\
                                                <a href="javascript:openNewWindow(\'admin.php?ADD=99999#vicidial_call_menu-ingroup_settings\')">Handle Method</a>:\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                                <select class="span" name=IGhandle_method_' + option + ' id=IGhandle_method_' + option + '>'+
                                                populate_options(IGhandle_method_list,IGhandle_method) + '</select>\n\
                                            </div>\n\
                                        </div>\n\
                                        \n\
                                        <div class="formRow op fix">\n\
                                            <label data-t="tooltip" title="">\n\
                                                Search Method:\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                                <select class="span" name=IGsearch_method_' + option + ' id=IGsearch_method_' + option + '>'+
                                                populate_options(IGsearch_method_list,IGsearch_method) + '</select>\n\
                                            </div>\n\
                                            </div>\n\
                                        \n\
                                        <div class="formRow op fix">\n\
                                            <label data-t="tooltip" title="">\n\
                                                List ID:\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                                <input type=text class="span2" maxlength=14 name=IGlist_id_' + option + ' id=IGlist_id_' + option + ' value="' + IGlist_id + '">\n\
                                            </div>\n\
                                            </div>\n\
                                        \n\
                                        <div class="formRow op fix">\n\
                                            <label data-t="tooltip" title="">\n\
                                                Campaign ID:\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                                <select class="chzn-select" name=IGcampaign_id_' + option + ' id=IGcampaign_id_' + option + '>'+
                                                populate_options(IGcampaign_id_list,IGcampaign_id) + '</select>\n\
                                            </div>\n\
                                            </div>\n\
                                            \n\
                                        <div class="formRow op fix hide">\n\
                                            <label data-t="tooltip" title="">\n\
                                                Phone Code:\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                                <input type=text class="span" maxlength=14 name=IGphone_code_' + option + ' id=IGphone_code_' + option + ' value="' + IGphone_code + '">\n\
                                            </div>\n\
                                            </div>\n\
                                            \n\
                                        <div class="formRow op fix">\n\
                                            <label data-t="tooltip" title="">\n\
                                                VID Enter Filename:\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                            <div class="input-append">\n\
                                                <input type=text name=IGvid_enter_filename_' + option + ' id=IGvid_enter_filename_' + option + ' placeholder="Escolha um ficheiro com o gestor de audio..." class="span6" maxlength=255 value="' + IGvid_enter_filename + '" readonly >\n\
                                                <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>\n\
                                                <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>\n\
                                            </div>\n\
                                            </div>\n\
                                            </div>\n\
                                            \n\
                                        <div class="formRow op fix">\n\
                                            <label data-t="tooltip" title="">\n\
                                                VID ID Number Filename:\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                                <div class="input-append">\n\
                                                  <input type=text name=IGvid_id_number_filename' + option + ' id=IGvid_id_number_filename' + option + ' placeholder="Escolha um ficheiro com o gestor de audio..." class="span6" maxlength=255 value="' + IGvid_id_number_filename + '" readonly > \n\
                                                  <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>\n\
                                                  <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>\n\
                                                </div>\n\
                                            </div>\n\
                                            </div>\n\
                                            \n\
                                        <div class="formRow op fix">\n\
                                            <label data-t="tooltip" title="">\n\
                                                VID Confirm Filename:\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                                <div class="input-append">\n\
                                                  <input type=text name=IGvid_confirm_filename_' + option + ' id=IGvid_confirm_filename_' + option + ' placeholder="Escolha um ficheiro com o gestor de audio..." class="span6" maxlength=255 value="' + IGvid_confirm_filename + '" readonly > \n\
                                                  <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>\n\
                                                  <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>\n\
                                                </div>\n\
                                            </div>\n\
                                            </div>\n\
                                            \n\
                                        <div class="formRow op fix">\n\
                                            <label data-t="tooltip" title="">\n\
                                                VID Digits:\n\
                                            </label>\n\
                                            <div class="formRight">\n\
                                                  <input type="text" class="span1" maxlength=3 name=IGvid_validate_digits_' + option + ' id=IGvid_validate_digits_' + option + ' value="' + IGvid_validate_digits + '">\n\
                                            </div>\n\
                                            </div>';
			}
		if (selected_route==='DID')
			{
			if (route === selected_route)
				{
				selected_value = value ;
				}
			else
				{value='';}
			new_content = '<div class="formRow op fix">\n\
                                        <label data-t="tooltip" title=""><span name=option_route_link_' + option + ' id=option_route_link_' + option + '><a href="admin.php?ADD=3311&amp;did_pattern=' + value + '">DDI:</a> </span></label>\n\
                                        <div class="formRight">\n\
                                        <select class="chzn-select" name=option_route_value_' + option + ' id=option_route_value_' + option + " onChange=\"call_menu_link('" + option + "','DID');\">" 
                                                    + populate_options(did_list,selected_value) + '</select>\n\
                                        </div>\n\
                                        </div>';
			}
		if (selected_route==='HANGUP')
			{
			if (route === selected_route)
				{
				selected_value = value;
				}
			else
				{value='vm-goodbye';}
			new_content = '<div class="formRow op fix">\n\
                                        <label data-t="tooltip" title="">Audio File:</label>\n\
                                        <div class="formRight">\n\
                                        <div class="input-append">\n\
                                            <input type=text name=option_route_value_' + option + ' id=option_route_value_' + option + ' placeholder="Escolha um ficheiro com o gestor de audio..." class="span6" maxlength=255 value="' + selected_value + '" readonly >\n\
                                            <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>\n\
                                            <a href=\"#\" class=\"btn audio-open\"><i class=\"icon-bullhorn\"></i>Gestor de Audio</a>\n\
                                        </div>\n\
                                        </div>\n\
                                        </div>';
			}
		if (selected_route==='EXTENSION')
			{
			if (route === selected_route)
				{
				selected_value = value;
				selected_context = value_context;
				}
			else
				{value='8304';}
			new_content = '<div class="formRow op fix">\n\
                                        <label data-t="tooltip" title="">Extensão:</label>\n\
                                        <div class="formRight">\n\
                                            <input type=text name=option_route_value_' + option + ' id=option_route_value_' + option + ' class="span" maxlength=255 value="' + selected_value + '">\n\
                                        </div>\n\
                                        </div>\n\
                                        <div class="formRow op fix">\n\
                                        <label data-t="tooltip" title="">Context:</label>\n\
                                        <div class="formRight">\n\
                                            <input type=text name=option_route_value_context_' + option + ' id=option_route_value_context_' + option + ' class="span" maxlength=255 value="' + selected_context + '">\n\
                                        </div>\n\
                                        </div>';
			}
		if (selected_route==='PHONE')
			{
			if (route === selected_route)
				{
				selected_value =  value;
				}
			else
				{value='';}
			new_content = '<div class="formRow op fix">\n\
                                        <label data-t="tooltip" title="">Licença:</label>\n\
                                        <div class="formRight">\n\
                                            <select class="chzn-select" name=option_route_value_' + option + ' id=option_route_value_' + option + '>' + populate_options(phone_list,selected_value) + '</select>\n\
                                        </div>\n\
                                        </div>';
			}
		if (selected_route==='VOICEMAIL')
			{
			if (route === selected_route)
				{
				selected_value = value;
				}
			else
				{value='';}
			new_content = '<div class="formRow op fix">\n\
                                        <label data-t="tooltip" title="">Voicemail Box:</label>\n\
                                        <div class="formRight">\n\
                                            <select name=option_route_value_' + option + ' id=option_route_value_' + option + ' class="chzn-select" >'+ populate_options(voice_mail_list,selected_value) + '</select>\n\
                                        </div>\n\
                                        </div>';
			}
		if (selected_route==='AGI')
			{
			if (route === selected_route)
				{
				selected_value = value;
				}
			else
				{value='';}
			new_content = '<div class="formRow op fix">\n\
                                        <label data-t="tooltip" title="">AGI:</label>\n\
                                        <div class="formRight">\n\
                                            <input type=text name=option_route_value_' + option + ' id=option_route_value_' + option + ' class="span" maxlength=255 value="' + selected_value + '> \n\
                                        </div>\n\
                                        </div>';
			}

		if (new_content.length < 1)
			{new_content = selected_route;}

		span_to_update.innerHTML = new_content;
                console.log(new_content);
                if (selected_route.match(se))
                {$(".chzn-select").chosen({no_results_text: "Não foi encontrado."});}
		}

	function call_menu_link(option,route)
		{
		var selected_value = '';
		var new_content = '';

		var select_list = document.getElementById("option_route_value_" + option);
		var selected_value = select_list.value;
		var span_to_update = document.getElementById("option_route_link_" + option);

		if (route==='CALLMENU')
			{
			new_content = "<a href=\"edit_ivr.php?menu_id=" + selected_value + "\">IVR:</a>";
			}
		if (route==='INGROUP')
			{
			new_content = "<a href=\"edit_inbound.php?group_id=" + selected_value + "\">Grupo Inbound:</a>";
			}
		if (route==='DID')
			{
			new_content = "<a href=\"edit_did.php?did_pattern=" + selected_value + "\">DDI:</a>";
			}

		if (new_content.length < 1)
			{new_content = selected_route;}

		span_to_update.innerHTML = new_content;
		}
                
              //Regex Selector for jQuery by JAMES PADOLSEY  
                jQuery.expr[':'].regex = function(elem, index, match) {
                var matchParams = match[3].split(','),
                    validLabels = /^(data|css):/,
                    attr = {
                        method: matchParams[0].match(validLabels) ? 
                                    matchParams[0].split(':')[0] : 'attr',
                        property: matchParams.shift().replace(validLabels,'')
                    },
                    regexFlags = 'ig',
                    regex = new RegExp(matchParams.join('').replace(/^\s+|\s+$/g,''), regexFlags);
                return regex.test(jQuery(elem)[attr.method](attr.property));
            };

                 //var que liga o gestor de audio ao input  !!IMPORTANT!!
            var audio_on_fly,ivr_options,ivr_options_selecteds=[];
                $(function(){
                    $(".chzn-select").chosen({no_results_text: "Não foi encontrado."});
                    $("[data-t=tooltip]").tooltip({placement:"right",html:true});
                    $("#loader").fadeOut("slow");
                    
                    $(".audio-open").live("click",function(){
                    audio_on_fly=$(this).parent().find("input");
                    $("#modal-audio-upload")
                            .modal()
                            .find(".modal-header h4")
                            .text(audio_on_fly.parent().parent().parent().find("label").text());
                    return false;});
                $(".audio-clean").live("click",function(){
                    $(this).parent().find("input").val("");
                    return false;
                });
                
                ivr_options=$("#table-options select:regex(name,^option_value_*)");
                ivr_options_hide();
                
                ivr_options.change(function(){
                    ivr_options_hide();
                });
                });
            function ivr_options_hide(){
                ivr_options.each(function( index ){ivr_options_selecteds[index]=$(this).val();});
                ivr_options.each(
                                function( index ){
                                ivr_options_selecteds[index]=$(this).val();
                            $(this).find("option").each(
                                    function(){
                                if($.inArray($(this).val(),ivr_options_selecteds)!==-1 && $(this).val()!==$(this).parent().val() && $(this).val()!==""){
                                    $(this).hide();}
                                else{
                                    $(this).show();
                                };
                            }
                        );
                    }
                );
            }
</script>
    <?php
#FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>	