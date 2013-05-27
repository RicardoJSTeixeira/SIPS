<?
require "../functions.php";
#HEADER
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');


$PHP_AUTH_USER = $_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW = $_SERVER["PHP_AUTH_PW"];

function help($where, $text) {
    return '<a onclick="openNewWindow(\'../admin.php?ADD=99999#' . $where . '\')">' . $text . '</a>';
}

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

//includes
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
        </style>
    </head>
    <body>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <?php
        $stmt = "SELECT delete_call_times,modify_call_times,user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW';";
        $rslt = mysql_query($stmt, $link);
        $row = mysql_fetch_row($rslt);
        $LOGmodify_call_times = $row[1];
        $LOGuser_group = $row[2];

        $stmt="SELECT allowed_campaigns,allowed_reports,admin_viewable_groups,admin_viewable_call_times from vicidial_user_groups where user_group='$LOGuser_group';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$LOGadmin_viewable_groups =		$row[2];
        
        $whereLOGadmin_viewable_groupsSQL='';
	if ( (!eregi("--ALL--",$LOGadmin_viewable_groups)) and (strlen($LOGadmin_viewable_groups) > 3) )
		{
		$whereLOGadmin_viewable_groupsSQL = "where user_group IN('---ALL---','$rawLOGadmin_viewable_groupsSQL')";
		}
	else 
		{$admin_viewable_groupsALL=1;}
        
$UUgroups_list=array();
	if ($admin_viewable_groupsALL > 0)
		{$UUgroups_list["---ALL---"]= "Todos";}
	$stmt="SELECT user_group,group_name from vicidial_user_groups $whereLOGadmin_viewable_groupsSQL order by user_group;";
	$rslt=mysql_query($stmt, $link);
	while ($rowx=mysql_fetch_row($rslt)) 
		{
		$UUgroups_list[$rowx[0]]=$rowx[1];
		}
                
                
                if ($ADD==411111111)
	{
	if ($LOGmodify_call_times==1)
		{

		if ( (strlen($call_time_id) < 2) or (strlen($call_time_name) < 2) )
			{?>
                    <script>
                        $(function(){makeAlert("#wr","CALL TIME NOT MODIFIED","Call Time ID and name must be at least 2 characters in length",1,true,false);});
                    </script>
                        <?php
			}
		else
			{
			$ct_default_start = preg_replace('/\D/', '', $ct_default_start);
			$ct_default_stop = preg_replace('/\D/', '', $ct_default_stop);
			$ct_sunday_start = preg_replace('/\D/', '', $ct_sunday_start);
			$ct_sunday_stop = preg_replace('/\D/', '', $ct_sunday_stop);
			$ct_monday_start = preg_replace('/\D/', '', $ct_monday_start);
			$ct_monday_stop = preg_replace('/\D/', '', $ct_monday_stop);
			$ct_tuesday_start = preg_replace('/\D/', '', $ct_tuesday_start);
			$ct_tuesday_stop = preg_replace('/\D/', '', $ct_tuesday_stop);
			$ct_wednesday_start = preg_replace('/\D/', '', $ct_wednesday_start);
			$ct_wednesday_stop = preg_replace('/\D/', '', $ct_wednesday_stop);
			$ct_thursday_start = preg_replace('/\D/', '', $ct_thursday_start);
			$ct_thursday_stop = preg_replace('/\D/', '', $ct_thursday_stop);
			$ct_friday_start = preg_replace('/\D/', '', $ct_friday_start);
			$ct_friday_stop = preg_replace('/\D/', '', $ct_friday_stop);
			$ct_saturday_start = preg_replace('/\D/', '', $ct_saturday_start);
			$ct_saturday_stop = preg_replace('/\D/', '', $ct_saturday_stop);
			$stmt="UPDATE vicidial_call_times set call_time_name='$call_time_name', call_time_comments='$call_time_comments', ct_default_start='$ct_default_start', ct_default_stop='$ct_default_stop', ct_sunday_start='$ct_sunday_start', ct_sunday_stop='$ct_sunday_stop', ct_monday_start='$ct_monday_start', ct_monday_stop='$ct_monday_stop', ct_tuesday_start='$ct_tuesday_start', ct_tuesday_stop='$ct_tuesday_stop', ct_wednesday_start='$ct_wednesday_start', ct_wednesday_stop='$ct_wednesday_stop', ct_thursday_start='$ct_thursday_start', ct_thursday_stop='$ct_thursday_stop', ct_friday_start='$ct_friday_start', ct_friday_stop='$ct_friday_stop', ct_saturday_start='$ct_saturday_start', ct_saturday_stop='$ct_saturday_stop', default_afterhours_filename_override='$default_afterhours_filename_override', sunday_afterhours_filename_override='$sunday_afterhours_filename_override', monday_afterhours_filename_override='$monday_afterhours_filename_override', tuesday_afterhours_filename_override='$tuesday_afterhours_filename_override', wednesday_afterhours_filename_override='$wednesday_afterhours_filename_override', thursday_afterhours_filename_override='$thursday_afterhours_filename_override', friday_afterhours_filename_override='$friday_afterhours_filename_override', saturday_afterhours_filename_override='$saturday_afterhours_filename_override',user_group='$user_group' where call_time_id='$call_time_id';";
			$rslt=mysql_query($stmt, $link);
?>
        <script>
            $(function(){makeAlert("#wr","CALL TIME Modificado com Sucesso","",4,true,false);});
        </script>
            <?php

			### LOG INSERTION Admin Log Table ###
			$SQL_log = "$stmt|";
			$SQL_log = ereg_replace(';','',$SQL_log);
			$SQL_log = addslashes($SQL_log);
			$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='CALLTIMES', event_type='MODIFY', record_id='$call_time_id', event_code='ADMIN MODIFY CALL TIME', event_sql=\"$SQL_log\", event_notes='';";
			if ($DB) {echo "|$stmt|\n";}
			$rslt=mysql_query($stmt, $link);
			}
		}
	else
		{
		echo "You do not have permission to view this page\n";
		exit;
		}
	$ADD=311111111;	# go to call time modification form below
	}
                
                
                
                
        if ($LOGmodify_call_times == 1) {


            $stmt = "SELECT call_time_id,call_time_name,call_time_comments,ct_default_start,ct_default_stop,ct_sunday_start,ct_sunday_stop,ct_monday_start,ct_monday_stop,ct_tuesday_start,ct_tuesday_stop,ct_wednesday_start,ct_wednesday_stop,ct_thursday_start,ct_thursday_stop,ct_friday_start,ct_friday_stop,ct_saturday_start,ct_saturday_stop,ct_state_call_times,default_afterhours_filename_override,sunday_afterhours_filename_override,monday_afterhours_filename_override,tuesday_afterhours_filename_override,wednesday_afterhours_filename_override,thursday_afterhours_filename_override,friday_afterhours_filename_override,saturday_afterhours_filename_override from vicidial_call_times where call_time_id='$call_time_id';";
            $rslt = mysql_query($stmt, $link);
            $row = mysql_fetch_row($rslt);
            $call_time_name = $row[1];
            $call_time_comments = $row[2];
            $ct_default_start = $row[3];
            $ct_default_stop = $row[4];
            $ct_sunday_start = $row[5];
            $ct_sunday_stop = $row[6];
            $ct_monday_start = $row[7];
            $ct_monday_stop = $row[8];
            $ct_tuesday_start = $row[9];
            $ct_tuesday_stop = $row[10];
            $ct_wednesday_start = $row[11];
            $ct_wednesday_stop = $row[12];
            $ct_thursday_start = $row[13];
            $ct_thursday_stop = $row[14];
            $ct_friday_start = $row[15];
            $ct_friday_stop = $row[16];
            $ct_saturday_start = $row[17];
            $ct_saturday_stop = $row[18];
            $ct_state_call_times = $row[19];
            $default_afterhours_filename_override = $row[20];
            $sunday_afterhours_filename_override = $row[21];
            $monday_afterhours_filename_override = $row[22];
            $tuesday_afterhours_filename_override = $row[23];
            $wednesday_afterhours_filename_override = $row[24];
            $thursday_afterhours_filename_override = $row[25];
            $friday_afterhours_filename_override = $row[26];
            $saturday_afterhours_filename_override = $row[27];
            ?>
                <div class=content>
            <form action="" method=POST>
                    <div class="grid">
                        <div class="grid-title">
                            <div class="pull-left">MODIFY A CALL TIME</div>
                            <div class="pull-right"></div>
                            <div class="clear"></div>
                        </div>
                        <div class="grid-content">
                            <div id="wr"></div>
                            <input type=hidden name=ADD value=411111111>
                            <input type=hidden name=DB value="<?= $DB ?>">
                            <input type=hidden name=call_time_id value="<?= $call_time_id ?>">
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Call Time ID: 
                                </label>
                                <div class="formRight">
                                    <?= $call_time_id ?>
                                </div>
                            </div>  
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Call Time Name: 
                                </label>
                                <div class="formRight">
                                    <input type=text name=call_time_name class="span" maxlength=50 value="<?= $call_time_name ?>">
                                </div>
                            </div>   
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Call Time Comments:
                                </label>
                                <div class="formRight">
                                    <input type=text name=call_time_comments class="span" size=50 maxlength=255 value="<?= $call_time_comments ?>">
                                </div>
                            </div>   
                            <div class="formRow op fix">
                                <label data-t="tooltip" title="">
                                    Admin User Group:
                                </label>
                                <div class="formRight">
                                    <select class="chzn-select" name=user_group>
                                        <?=populate_options($UUgroups_list,$user_group)?>
                                    </select>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <td colspan="6"><input class="btn btn-primary right" type=submit name=SUBMIT value=Gravar>
                            <div class="clear"></div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="grid-title">
                            <div class="pull-left">CALL TIME</div>
                            <div class="pull-right"></div>
                            <div class="clear"></div>
                        </div>
                        <table class="table table-mod">
                            <thead>
                                <tr>
                                    <th colspan="2">DEFAULT START</th>
                                    <th colspan="2">DEFAULT STOP</th>
                                    <th colspan="2">OVERWRITE MESSAGE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Default Start:</td>
                                    <td><input type=text name=ct_default_start maxlength=4 value="<?= $ct_default_start ?>"></td>
                                    <td>Default Stop:</td>
                                    <td><input type=text name=ct_default_stop maxlength=4 value="<?= $ct_default_stop ?>"></td>
                                    <td>AH Override:</td>
                                    <td>
                                        <div class="input-append">
                                            <input type=text name=default_afterhours_filename_override id=default_afterhours_filename_override maxlength=255 value="<?= $default_afterhours_filename_override ?>"> 
                                            <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                            <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Sunday Start:</td>
                                    <td><input type=text name=ct_sunday_start maxlength=4 value="<?= $ct_sunday_start ?>"></td>
                                    <td>Sunday Stop:</td>
                                    <td><input type=text name=ct_sunday_stop maxlength=4 value="<?= $ct_sunday_stop ?>">
                                    <td>AH Override:</td>
                                    <td>
                                        <div class="input-append">
                                            <input type=text name=sunday_afterhours_filename_override id=sunday_afterhours_filename_override maxlength=255 value="<?= $sunday_afterhours_filename_override ?>">
                                            <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                            <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Monday Start:</td>
                                    <td><input type=text name=ct_monday_start maxlength=4 value="<?= $ct_monday_start ?>"></td>
                                    <td>Monday Stop:</td>
                                    <td><input type=text name=ct_monday_stop maxlength=4 value="<?= $ct_monday_stop ?>"></td>
                                    <td>AH Override:</td>
                                    <td>
                                        <div class="input-append">
                                            <input type=text name=monday_afterhours_filename_override id=monday_afterhours_filename_override maxlength=255 value="<?= $monday_afterhours_filename_override ?>">
                                            <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                            <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Tuesday Start:</td>
                                    <td><input type=text name=ct_tuesday_start maxlength=4 value="<?= $ct_tuesday_start ?>"> </td>
                                    <td>Tuesday Stop:</td>
                                    <td><input type=text name=ct_tuesday_stop maxlength=4 value="<?= $ct_tuesday_stop ?>"></td>
                                    <td>AH Override:</td>
                                    <td>
                                        <div class="input-append">
                                            <input type=text name=tuesday_afterhours_filename_override id=tuesday_afterhours_filename_override maxlength=255 value="<?= $tuesday_afterhours_filename_override ?>">
                                            <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                            <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Wednesday Start:</td>
                                    <td><input type=text name=ct_wednesday_start maxlength=4 value="<?= $ct_wednesday_start ?>"> </td>
                                    <td>Wednesday Stop:</td>
                                    <td><input type=text name=ct_wednesday_stop maxlength=4 value="<?= $ct_wednesday_stop ?>"> </td>
                                    <td>AH Override:</td>
                                    <td>
                                        <div class="input-append">
                                            <input type=text name=wednesday_afterhours_filename_override id=wednesday_afterhours_filename_override maxlength=255 value="<?= $wednesday_afterhours_filename_override ?>">
                                            <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                            <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thursday Start:</td>
                                    <td><input type=text name=ct_thursday_start maxlength=4 value="<?= $ct_thursday_start ?>"> </td>
                                    <td>Thursday Stop:</td>
                                    <td><input type=text name=ct_thursday_stop maxlength=4 value="<?= $ct_thursday_stop ?>"> 
                                    <td>AH Override:</td>
                                    <td>
                                        <div class="input-append">
                                            <input type=text name=thursday_afterhours_filename_override id=thursday_afterhours_filename_override maxlength=255 value="<?= $thursday_afterhours_filename_override ?>">
                                            <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                            <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Friday Start:</td>
                                    <td><input type=text name=ct_friday_start maxlength=4 value="<?= $ct_friday_start ?>"> </td>
                                    <td>Friday Stop:</td>
                                    <td><input type=text name=ct_friday_stop maxlength=4 value="<?= $ct_friday_stop ?>"></td>
                                    <td>AH Override:</td>
                                    <td>
                                        <div class="input-append">
                                            <input type=text name=friday_afterhours_filename_override id=friday_afterhours_filename_override maxlength=255 value="<?= $friday_afterhours_filename_override ?>">
                                            <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                            <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Saturday Start:</td>
                                    <td><input type=text name=ct_saturday_start maxlength=4 value="<?= $ct_saturday_start ?>"> </td>
                                    <td>Saturday Stop:</td>
                                    <td><input type=text name=ct_saturday_stop maxlength=4 value="<?= $ct_saturday_stop ?>"></td>
                                    <td>AH Override:</td>
                                    <td>
                                        <div class="input-append">
                                            <input type=text name=saturday_afterhours_filename_override id=saturday_afterhours_filename_override maxlength=255 value="<?= $saturday_afterhours_filename_override ?>">
                                            <a href="#" class="btn audio-clean"><i class="icon-remove-circle"></i>Limpar</a>
                                            <a href="#" class="btn audio-open"><i class="icon-bullhorn"></i>Gestor de Audio</a>
                                        </div>
                                    </td>
                                </tr>
                                <tr><td colspan="6"><input class="btn btn-primary right" type=submit name=SUBMIT value=Gravar></td></tr>
                            </tbody>
                        </table> 
                    </div>
            </FORM> 
            <?php
            /* 	$ct_srs=1;
              $b=0;
              $srs_SQL ='';
              if (strlen($ct_state_call_times)>2)
              {
              $state_rules = explode('|',$ct_state_call_times);
              $ct_srs = ((count($state_rules)) - 1);
              }
              echo "<tr bgcolor=#dddddd><td align=center rowspan=$ct_srs>Active State Call Time Definitions for this Record: </td>\n";
              echo "<td align=center colspan=3>&nbsp;</td></tr>\n";
              while($ct_srs >= $b)
              {
              if (strlen($state_rules[$b])>0)
              {
              $stmt="SELECT state_call_time_state,state_call_time_name from vicidial_state_call_times where state_call_time_id='$state_rules[$b]';";
              $rslt=mysql_query($stmt, $link);
              $row=mysql_fetch_row($rslt);
              echo "<tr bgcolor=#dddddd><td align=right colspan=2><a href=\"$PHP_SELF?ADD=3111111111&call_time_id=$state_rules[$b]\">$state_rules[$b] </a> - <a href=\"$PHP_SELF?ADD=321111111&call_time_id=$call_time_id&state_rule=$state_rules[$b]&stage=REMOVE\">REMOVE </a></td><td align=left>$row[0] - $row[1]</td></tr>\n";
              $srs_SQL .= "'$state_rules[$b]',";
              $srs_state_SQL .= "'$row[0]',";
              }
              $b++;
              }
              if (strlen($srs_SQL)>2)
              {
              $srs_SQL = "$srs_SQL''";
              $srs_state_SQL = "$srs_state_SQL''";
              $srs_SQL = "where state_call_time_id NOT IN($srs_SQL) and state_call_time_state NOT IN($srs_state_SQL)";
              }
              else
              {$srs_SQL='';}
              $stmt="SELECT state_call_time_id,state_call_time_name from vicidial_state_call_times $srs_SQL order by state_call_time_id;";
              $rslt=mysql_query($stmt, $link);
              $sct_to_print = mysql_num_rows($rslt);
              $sct_list='';

              $o=0;
              while ($sct_to_print > $o)
              {
              $rowx=mysql_fetch_row($rslt);
              $sct_list .= "<option value=\"$rowx[0]\">$rowx[0] - $rowx[1]</option>\n";
              $o++;
              }
              echo "<tr bgcolor=#dddddd><td><form action=$PHP_SELF method=POST>\n";
              echo "<input type=hidden name=ADD value=321111111>\n";
              echo "<input type=hidden name=stage value=\"ADD\">\n";
              echo "<input type=hidden name=call_time_id value=\"$call_time_id\">\n";
              echo "Add state call time rule: </td><td align=left colspan=2><select size=1 name=state_rule>\n";
              echo "$sct_list";
              echo "</select></td>\n";
              echo "<td align=center colspan=4><input type=submit name=SUBMIT value=SUBMIT></FORM></td></tr>\n";
              echo "</TABLE><BR><BR>\n"; */
            ?>
            <div class="grid-transparent">
                <div class="accordion" id="accordion2">
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseOne">
                                CAMPAIGNS USING THIS CALL TIME
                            </a>
                        </div>
                        <div id="collapseOne" class="accordion-body collapse">
                            <div class="accordion-inner">
                                <ul class="none-list"> 

                                    <?php
                                    $stmt = "SELECT campaign_id,campaign_name,active from vicidial_campaigns where local_call_time='$call_time_id' $LOGallowed_campaignsSQL order by campaign_name ASC;";
                                    $rslt = mysql_query($stmt, $link);
                                    $camps_to_print = mysql_num_rows($rslt);
                                    while ($row = mysql_fetch_row($rslt)) {
                                        ?>
                                        <li><i class="fam-bullet-<?= ($row[2] == "Y") ? "green" : "black" ?>"></i> <?= $row[1] ?></li> 
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-group">
                        <div class="accordion-heading">
                            <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">
                                INBOUND GROUPS USING THIS CALL TIME
                            </a>
                        </div>
                        <div id="collapseTwo" class="accordion-body collapse">
                            <div class="accordion-inner">

                                <ul class="none-list"> 

                                    <?php
                                    $stmt = "SELECT group_id,group_name,active from vicidial_inbound_groups where call_time_id='$call_time_id' order by group_name;";
                                    $rslt = mysql_query($stmt, $link);
                                    $camps_to_print = mysql_num_rows($rslt);
                                    while ($row = mysql_fetch_row($rslt)) {
                                        ?>
                                        <li><i class="fam-bullet-<?= ($row[2] == "Y") ? "green" : "black" ?>"></i> <?= $row[1] ?></li> 
                                        <?php } ?>

                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                $(function(){
                $("[data-t=tooltip]").tooltip({placement:"right",html:true});
                $(".chzn-select").chosen({no_results_text: "Não foi encontrado."});
                
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
            });
            </script>
            <?php include 'audio-modal.php'; ?>
            <?php
            /*
              if ($LOGdelete_call_times > 0) {
              echo "<br><br><a href=\"$PHP_SELF?ADD=511111111&call_time_id=$call_time_id\">DELETE THIS CALL TIME DEFINITION</a>\n";
              }
              if ($LOGuser_level >= 9) {
              echo "<br><br><a href=\"$PHP_SELF?ADD=720000000000000&category=CALLTIMES&stage=$call_time_id\">Click here to see Admin changes to this call time</FONT>\n";
              }
             */
        } else {
            echo "You are not authorized to view this page. Please go back.";
        }
        ?>
                </div>
    </body>
</html>