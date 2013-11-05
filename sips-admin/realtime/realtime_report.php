<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {
    $far.="../";
}
define("ROOT", $far);
require(ROOT . "ini/header.php");
$is_upload = $_FILES["ref_file"]["error"] == 0 && isset($_FILES["ref_file"]);
$PHP_SELF = $_SERVER['PHP_SELF'];
?>
<?php
# realtime_report.php
# 
# Copyright (C) 2011  Matt Florell <vicidial@gmail.com>    LICENSE: AGPLv2
#
# live real-time stats for the VICIDIAL Auto-Dialer all servers
#
# Rewritten from AST_timeonVDADall.php report to be AJAX and javascript instead 
# of link-driven
#
# * Requires AST_timeonVDADall.php for AJAX-derived stats information
# 
# CHANGELOG:
# 101216-1355 - First Build
# 101218-1520 - Small time reload bug fix and formatting fixes
# 110111-1557 - Added options.php options, minor bug fixes
# 110113-1736 - Small fix
# 110303-2124 - Added agent on-hook phone indication and RING status and color
# 110316-2216 - Added Agent, Carrier and Preset options.php settings
#

$version = '2.4-6';
$build = '110316-2216';


require("../functions.php");

$PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];

$PHP_SELF = $_SERVER['PHP_SELF'];
if (isset($_GET["server_ip"])) {
    $server_ip = $_GET["server_ip"];
} elseif (isset($_POST["server_ip"])) {
    $server_ip = $_POST["server_ip"];
}
if (isset($_GET["RR"])) {
    $RR = $_GET["RR"];
} elseif (isset($_POST["RR"])) {
    $RR = $_POST["RR"];
}
if (isset($_GET["inbound"])) {
    $inbound = $_GET["inbound"];
} elseif (isset($_POST["inbound"])) {
    $inbound = $_POST["inbound"];
}
if (isset($_GET["group"])) {
    $group = $_GET["group"];
} elseif (isset($_POST["group"])) {
    $group = $_POST["group"];
}
if (isset($_GET["groups"])) {
    $groups = $_GET["groups"];
} elseif (isset($_POST["groups"])) {
    $groups = $_POST["groups"];
}
if (isset($_GET["usergroup"])) {
    $usergroup = $_GET["usergroup"];
} elseif (isset($_POST["usergroup"])) {
    $usergroup = $_POST["usergroup"];
}
if (isset($_GET["DB"])) {
    $DB = $_GET["DB"];
} elseif (isset($_POST["DB"])) {
    $DB = $_POST["DB"];
}
if (isset($_GET["adastats"])) {
    $adastats = $_GET["adastats"];
} elseif (isset($_POST["adastats"])) {
    $adastats = $_POST["adastats"];
}
if (isset($_GET["submit"])) {
    $submit = $_GET["submit"];
} elseif (isset($_POST["submit"])) {
    $submit = $_POST["submit"];
}
if (isset($_GET["SUBMIT"])) {
    $SUBMIT = $_GET["SUBMIT"];
} elseif (isset($_POST["SUBMIT"])) {
    $SUBMIT = $_POST["SUBMIT"];
}
if (isset($_GET["SIPmonitorLINK"])) {
    $SIPmonitorLINK = $_GET["SIPmonitorLINK"];
} elseif (isset($_POST["SIPmonitorLINK"])) {
    $SIPmonitorLINK = $_POST["SIPmonitorLINK"];
}
if (isset($_GET["IAXmonitorLINK"])) {
    $IAXmonitorLINK = $_GET["IAXmonitorLINK"];
} elseif (isset($_POST["IAXmonitorLINK"])) {
    $IAXmonitorLINK = $_POST["IAXmonitorLINK"];
}
if (isset($_GET["UGdisplay"])) {
    $UGdisplay = $_GET["UGdisplay"];
} elseif (isset($_POST["UGdisplay"])) {
    $UGdisplay = $_POST["UGdisplay"];
}
if (isset($_GET["UidORname"])) {
    $UidORname = $_GET["UidORname"];
} elseif (isset($_POST["UidORname"])) {
    $UidORname = $_POST["UidORname"];
}
if (isset($_GET["orderby"])) {
    $orderby = $_GET["orderby"];
} elseif (isset($_POST["orderby"])) {
    $orderby = $_POST["orderby"];
}
if (isset($_GET["SERVdisplay"])) {
    $SERVdisplay = $_GET["SERVdisplay"];
} elseif (isset($_POST["SERVdisplay"])) {
    $SERVdisplay = $_POST["SERVdisplay"];
}
if (isset($_GET["CALLSdisplay"])) {
    $CALLSdisplay = $_GET["CALLSdisplay"];
} elseif (isset($_POST["CALLSdisplay"])) {
    $CALLSdisplay = $_POST["CALLSdisplay"];
}
if (isset($_GET["PHONEdisplay"])) {
    $PHONEdisplay = $_GET["PHONEdisplay"];
} elseif (isset($_POST["PHONEdisplay"])) {
    $PHONEdisplay = $_POST["PHONEdisplay"];
}
if (isset($_GET["CUSTPHONEdisplay"])) {
    $CUSTPHONEdisplay = $_GET["CUSTPHONEdisplay"];
} elseif (isset($_POST["CUSTPHONEdisplay"])) {
    $CUSTPHONEdisplay = $_POST["CUSTPHONEdisplay"];
}
if (isset($_GET["NOLEADSalert"])) {
    $NOLEADSalert = $_GET["NOLEADSalert"];
} elseif (isset($_POST["NOLEADSalert"])) {
    $NOLEADSalert = $_POST["NOLEADSalert"];
}
if (isset($_GET["DROPINGROUPstats"])) {
    $DROPINGROUPstats = $_GET["DROPINGROUPstats"];
} elseif (isset($_POST["DROPINGROUPstats"])) {
    $DROPINGROUPstats = $_POST["DROPINGROUPstats"];
}
if (isset($_GET["ALLINGROUPstats"])) {
    $ALLINGROUPstats = $_GET["ALLINGROUPstats"];
} elseif (isset($_POST["ALLINGROUPstats"])) {
    $ALLINGROUPstats = $_POST["ALLINGROUPstats"];
}
if (isset($_GET["with_inbound"])) {
    $with_inbound = $_GET["with_inbound"];
} elseif (isset($_POST["with_inbound"])) {
    $with_inbound = $_POST["with_inbound"];
}
if (isset($_GET["monitor_active"])) {
    $monitor_active = $_GET["monitor_active"];
} elseif (isset($_POST["monitor_active"])) {
    $monitor_active = $_POST["monitor_active"];
}
if (isset($_GET["monitor_phone"])) {
    $monitor_phone = $_GET["monitor_phone"];
} elseif (isset($_POST["monitor_phone"])) {
    $monitor_phone = $_POST["monitor_phone"];
}
if (isset($_GET["CARRIERstats"])) {
    $CARRIERstats = $_GET["CARRIERstats"];
} elseif (isset($_POST["CARRIERstats"])) {
    $CARRIERstats = $_POST["CARRIERstats"];
}
if (isset($_GET["PRESETstats"])) {
    $PRESETstats = $_GET["PRESETstats"];
} elseif (isset($_POST["PRESETstats"])) {
    $PRESETstats = $_POST["PRESETstats"];
}
if (isset($_GET["AGENTtimeSTATS"])) {
    $AGENTtimeSTATS = $_GET["AGENTtimeSTATS"];
} elseif (isset($_POST["AGENTtimeSTATS"])) {
    $AGENTtimeSTATS = $_POST["AGENTtimeSTATS"];
}

$report_name = 'Real-Time Main Report';
$db_source = 'M';

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,outbound_autodial_active,slave_db_server,reports_use_slave_db FROM system_settings;";
$rslt = mysql_query($stmt, $link);
if ($DB) {
    echo "$stmt\n";
}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0) {
    $row = mysql_fetch_row($rslt);
    $non_latin = $row[0];
    $outbound_autodial_active = $row[1];
    $slave_db_server = $row[2];
    $reports_use_slave_db = $row[3];
}
##### END SETTINGS LOOKUP #####
###########################################

if ((strlen($slave_db_server) > 5) and (preg_match("/$report_name/", $reports_use_slave_db))) {
    mysql_close($link);
    $use_slave_server = 1;
    $db_source = 'S';
    require("../dbconnect.php");
    echo "<!-- Using slave server $slave_db_server $db_source -->\n";
}

$webphone_width = '460';
$webphone_height = '500';
$webphone_left = '600';
$webphone_top = '27';
$webphone_bufw = '250';
$webphone_bufh = '1';
$webphone_pad = '10';
$webphone_clpos = " <a href=\"#\" onclick=\"hideDiv('webphone_content');\">webphone -</a>";

if (file_exists('../options.php')) {
    require('../options.php');
}

if (!isset($DB)) {
    if (!isset($RS_DB)) {
        $DB = 0;
    } else {
        $DB = $RS_DB;
    }
}
if (!isset($RR)) {
    if (!isset($RS_RR)) {
        $RR = 4;
    } else {
        $RR = $RS_RR;
    }
}
if (!isset($group)) {
    if (!isset($RS_group)) {
        $group = 'ALL-ACTIVE';
    } else {
        $group = $RS_group;
    }
}
if (!isset($usergroup)) {
    if (!isset($RS_usergroup)) {
        $usergroup = '';
    } else {
        $usergroup = $RS_usergroup;
    }
}
if (!isset($UGdisplay)) {
    if (!isset($RS_UGdisplay)) {
        $UGdisplay = 0;
    } else {
        $UGdisplay = $RS_UGdisplay;
    }
}
if (!isset($UidORname)) {
    if (!isset($RS_UidORname)) {
        $UidORname = 1;
    } else {
        $UidORname = $RS_UidORname;
    }
}
if (!isset($orderby)) {
    if (!isset($RS_orderby)) {
        $orderby = 'timeup';
    } else {
        $orderby = $RS_orderby;
    }
}
if (!isset($SERVdisplay)) {
    if (!isset($RS_SERVdisplay)) {
        $SERVdisplay = 0;
    } else {
        $SERVdisplay = $RS_SERVdisplay;
    }
}
if (!isset($CALLSdisplay)) {
    if (!isset($RS_CALLSdisplay)) {
        $CALLSdisplay = 1;
    } else {
        $CALLSdisplay = $RS_CALLSdisplay;
    }
}
if (!isset($PHONEdisplay)) {
    if (!isset($RS_PHONEdisplay)) {
        $PHONEdisplay = 0;
    } else {
        $PHONEdisplay = $RS_PHONEdisplay;
    }
}
if (!isset($CUSTPHONEdisplay)) {
    if (!isset($RS_CUSTPHONEdisplay)) {
        $CUSTPHONEdisplay = 0;
    } else {
        $CUSTPHONEdisplay = $RS_CUSTPHONEdisplay;
    }
}
if (!isset($PAUSEcodes)) {
    if (!isset($RS_PAUSEcodes)) {
        $PAUSEcodes = 'N';
    } else {
        $PAUSEcodes = $RS_PAUSEcodes;
    }
}
if (!isset($with_inbound)) {
    if (!isset($RS_with_inbound)) {
        if ($outbound_autodial_active > 0) {
            $with_inbound = 'Y';
        }  # N=no, Y=yes, O=only
        else {
            $with_inbound = 'O';
        }  # N=no, Y=yes, O=only
    } else {
        $with_inbound = $RS_with_inbound;
    }
}
if (!isset($CARRIERstats)) {
    if (!isset($RS_CARRIERstats)) {
        $CARRIERstats = '0';
    } else {
        $CARRIERstats = $RS_CARRIERstats;
    }
}
if (!isset($PRESETstats)) {
    if (!isset($RS_PRESETstats)) {
        $PRESETstats = '0';
    } else {
        $PRESETstats = $RS_PRESETstats;
    }
}
if (!isset($AGENTtimeSTATS)) {
    if (!isset($RS_AGENTtimeSTATS)) {
        $AGENTtimeSTATS = '0';
    } else {
        $AGENTtimeSTATS = $RS_AGENTtimeSTATS;
    }
}

$ingroup_detail = '';

if ((strlen($group) > 1) and (strlen($groups[0]) < 1)) {
    $groups[0] = $group;
} else {
    $group = $groups[0];
}

$NOW_TIME = date("Y-m-d H:i:s");
$NOW_DAY = date("Y-m-d");
$NOW_HOUR = date("H:i:s");
$STARTtime = date("U");
$epochONEminuteAGO = ($STARTtime - 60);
$timeONEminuteAGO = date("Y-m-d H:i:s", $epochONEminuteAGO);
$epochFIVEminutesAGO = ($STARTtime - 300);
$timeFIVEminutesAGO = date("Y-m-d H:i:s", $epochFIVEminutesAGO);
$epochFIFTEENminutesAGO = ($STARTtime - 900);
$timeFIFTEENminutesAGO = date("Y-m-d H:i:s", $epochFIFTEENminutesAGO);
$epochONEhourAGO = ($STARTtime - 3600);
$timeONEhourAGO = date("Y-m-d H:i:s", $epochONEhourAGO);
$epochSIXhoursAGO = ($STARTtime - 21600);
$timeSIXhoursAGO = date("Y-m-d H:i:s", $epochSIXhoursAGO);
$epochTWENTYFOURhoursAGO = ($STARTtime - 86400);
$timeTWENTYFOURhoursAGO = date("Y-m-d H:i:s", $epochTWENTYFOURhoursAGO);
$webphone_content = '';

$PHP_AUTH_USER = ereg_replace("[^0-9a-zA-Z]", "", $PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^0-9a-zA-Z]", "", $PHP_AUTH_PW);

$stmt = "SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {
    echo "|$stmt|\n";
}
if ($non_latin > 0) {
    $rslt = mysql_query("SET NAMES 'UTF8'");
}
$rslt = mysql_query($stmt, $link);
$row = mysql_fetch_row($rslt);
$auth = $row[0];

$stmt = "SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level='7' and view_reports='1' and active='Y';";
if ($DB) {
    echo "|$stmt|\n";
}
$rslt = mysql_query($stmt, $link);
$row = mysql_fetch_row($rslt);
$reports_only_user = $row[0];

if ((strlen($PHP_AUTH_USER) < 2) or (strlen($PHP_AUTH_PW) < 2) or (!$auth)) {
    Header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
}
#  and (preg_match("/MONITOR|BARGE|HIJACK/",$monitor_active) ) )
if ((!isset($monitor_phone)) or (strlen($monitor_phone) < 1)) {
    $stmt = "select phone_login from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and active='Y';";
    $rslt = mysql_query($stmt, $link);
    if ($DB) {
        echo "$stmt\n";
    }
    $row = mysql_fetch_row($rslt);
    $monitor_phone = $row[0];
}

$stmt = "SELECT realtime_block_user_info,user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 6 and view_reports='1' and active='Y';";
if ($DB) {
    echo "|$stmt|\n";
}
$rslt = mysql_query($stmt, $link);
$row = mysql_fetch_row($rslt);
$realtime_block_user_info = $row[0];
$LOGuser_group = $row[1];

$stmt = "SELECT allowed_campaigns,allowed_reports,webphone_url_override,webphone_dialpad_override,webphone_systemkey_override from vicidial_user_groups where user_group='$LOGuser_group';";
if ($DB) {
    echo "|$stmt|\n";
}
$rslt = mysql_query($stmt, $link);
$row = mysql_fetch_row($rslt);
$LOGallowed_campaigns = $row[0];
$LOGallowed_reports = $row[1];
$webphone_url = $row[2];
$webphone_dialpad_override = $row[3];
$system_key = $row[4];

if ((!preg_match("/$report_name/", $LOGallowed_reports)) and (!preg_match("/ALL REPORTS/", $LOGallowed_reports))) {
    Header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "You are not allowed to view this report: |$PHP_AUTH_USER|$report_name|\n";
    exit;
}

$LOGallowed_campaignsSQL = '';
$whereLOGallowed_campaignsSQL = '';
if ((!preg_match("/ALL-/", $LOGallowed_campaigns))) {
    $rawLOGallowed_campaignsSQL = preg_replace("/ -/", '', $LOGallowed_campaigns);
    $rawLOGallowed_campaignsSQL = preg_replace("/ /", "','", $rawLOGallowed_campaignsSQL);
    $LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
    $whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
}
$regexLOGallowed_campaigns = " $LOGallowed_campaigns ";

$allactivecampaigns = '';
$stmt = "select campaign_id,campaign_name from vicidial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id;";
$rslt = mysql_query($stmt, $link);
if ($DB) {
    echo "$stmt\n";
}
$groups_to_print = mysql_num_rows($rslt);
$i = 0;
$LISTgroups[$i] = 'ALL-ACTIVE';
$i++;
$groups_to_print++;
while ($i < $groups_to_print) {
    $row = mysql_fetch_row($rslt);
    $LISTgroups[$i] = $row[0];
    $LISTnames[$i] = $row[1];
    $allactivecampaigns .= "'$LISTgroups[$i]',";
    $i++;
}
$allactivecampaigns .= "''";

$i = 0;
$group_string = '|';
$group_ct = count($groups);
while ($i < $group_ct) {
    if ((preg_match("/ $groups[$i] /", $regexLOGallowed_campaigns)) or (preg_match("/ALL-/", $LOGallowed_campaigns))) {
        $group_string .= "$groups[$i]|";
        $group_SQL .= "'$groups[$i]',";
        $groupQS .= "&groups[]=$groups[$i]";
    }

    $i++;
}
$group_SQL = eregi_replace(",$", '', $group_SQL);

### if no campaigns selected, display all
if (($group_ct < 1) or (strlen($group_string) < 2)) {
    $groups[0] = 'ALL-ACTIVE';
    $group_string = '|ALL-ACTIVE|';
    $group = 'ALL-ACTIVE';
    $groupQS .= "&groups[]=ALL-ACTIVE";
}

if ((ereg("--NONE--", $group_string) ) or ($group_ct < 1)) {
    $all_active = 0;
    $group_SQL = "''";
    $group_SQLand = "and FALSE";
    $group_SQLwhere = "where FALSE";
} elseif (eregi('ALL-ACTIVE', $group_string)) {
    $all_active = 1;
    $group_SQL = $allactivecampaigns;
    $group_SQLand = "and campaign_id IN($allactivecampaigns)";
    $group_SQLwhere = "where campaign_id IN($allactivecampaigns)";
} else {
    $all_active = 0;
    $group_SQLand = "and campaign_id IN($group_SQL)";
    $group_SQLwhere = "where campaign_id IN($group_SQL)";
}


$stmt = "select user_group from vicidial_user_groups;";
$rslt = mysql_query($stmt, $link);
if (!isset($DB)) {
    $DB = 0;
}
if ($DB) {
    echo "$stmt\n";
}
$usergroups_to_print = mysql_num_rows($rslt);
$i = 0;
while ($i < $usergroups_to_print) {
    $row = mysql_fetch_row($rslt);
    $usergroups[$i] = $row[0];
    $i++;
}

if (!isset($RR)) {
    $RR = 4;
}


$select_list = "<div class=cc-mstyle style=\"width:600px;box-shadow:0px 0px 1px 1000000px rgba(0, 0, 0, 0.5);\">";
$select_list .= "<table>";
$select_list .= "<tr style=\'border-bottom:1px solid #90C0E8;\'><td style=width:32px><img style=\'float:left;\' src=/images/icons/legend.png /></td><td style=float:left id=submenu-title>Opções do Realtime Report</td><td id=icon32><img style=\'float:left;cursor:pointer;\' src=/images/icons/cross.png onclick=\"hideDiv(\'campaign_select_list\');\" /></td></tr> ";
$select_list .= "<tr><td colspan=3><p align=center>Escolha uma Campanha</p>";
/* $select_list .= "<select style=\'height:105px; width:450px;\' NAME=groups[] ID=groups[] multiple>";
  $o=0;
  while ($groups_to_print > $o)
  {
  if (ereg("\|$LISTgroups[$o]\|",$group_string))
  {$group_string_alterado = ($LISTgroups[$o]=="ALL-ACTIVE") ? "Todos Activos" : "$LISTnames[$o]" ;
  $select_list .= "<option selected value=\"$LISTgroups[$o]\">$group_string_alterado</option>";}
  else
  {$group_string_alterado = ($LISTgroups[$o]=="ALL-ACTIVE") ? "Todos Activos" : "$LISTnames[$o]" ;
  $select_list .= "<option value=\"$LISTgroups[$o]\">$group_string_alterado</option>";}
  $o++;
  }
  $select_list .= "</select>"; */
$select_list .="</td></table><br>";
$select_list .= "<TABLE>";


$select_list .= "<TR><TD align=right>";
$select_list .= "Tempo de Actualização:  </TD><TD align=left><SELECT SIZE=1 NAME=RR ID=RR>";
$select_list .= "<option value=\"5\"";
if ($RR < 6) {
    $select_list .= " selected";
} $select_list .= ">5 Segundos</option>";
$select_list .= "<option value=\"10\"";
if (($RR > 5) and ($RR <= 10)) {
    $select_list .= " selected";
} $select_list .= ">10 Segundos</option>";
$select_list .= "<option value=\"20\"";
if (($RR >= 11) and ($RR <= 20)) {
    $select_list .= " selected";
} $select_list .= ">20 Segundos</option>";
$select_list .= "<option value=\"30\"";
if (($RR >= 21) and ($RR <= 30)) {
    $select_list .= " selected";
} $select_list .= ">30 Segundos</option>";
$select_list .= "<option value=\"40\"";
if (($RR >= 31) and ($RR <= 40)) {
    $select_list .= " selected";
} $select_list .= ">40 Segundos</option>";
$select_list .= "<option value=\"60\"";
if (($RR >= 41) and ($RR <= 60)) {
    $select_list .= " selected";
} $select_list .= ">60 Segundos</option>";
$select_list .= "<option value=\"120\"";
if (($RR >= 61) and ($RR <= 120)) {
    $select_list .= " selected";
} $select_list .= ">2 Minutos</option>";
$select_list .= "<option value=\"300\"";
if (($RR >= 121) and ($RR <= 300)) {
    $select_list .= " selected";
} $select_list .= ">5 Minutos</option>";
$select_list .= "<option value=\"600\"";
if (($RR >= 301) and ($RR <= 600)) {
    $select_list .= " selected";
} $select_list .= ">10 Minutos</option>";
$select_list .= "<option value=\"1200\"";
if (($RR >= 601) and ($RR <= 1200)) {
    $select_list .= " selected";
} $select_list .= ">20 Minutos</option>";
$select_list .= "<option value=\"1800\"";
if (($RR >= 1201) and ($RR <= 1800)) {
    $select_list .= " selected";
} $select_list .= ">30 Minutos</option>";
$select_list .= "<option value=\"2400\"";
if (($RR >= 1801) and ($RR <= 2400)) {
    $select_list .= " selected";
} $select_list .= ">40 Minutos</option>";
$select_list .= "<option value=\"3600\"";
if (($RR >= 2401) and ($RR <= 3600)) {
    $select_list .= " selected";
} $select_list .= ">60 Minutos</option>";
$select_list .= "<option value=\"7200\"";
if (($RR >= 3601) and ($RR <= 7200)) {
    $select_list .= " selected";
} $select_list .= ">2 Horas</option>";
$select_list .= "<option value=\"63072000\"";
if ($RR >= 7201) {
    $select_list .= " selected";
} $select_list .= ">2 Anos</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Inbound:  </TD><TD align=left><SELECT SIZE=1 NAME=with_inbound ID=with_inbound>";
$select_list .= "<option value=\"N\"";
if ($with_inbound == 'N') {
    $select_list .= " selected";
}
$select_list .= ">No</option>";
$select_list .= "<option value=\"Y\"";
if ($with_inbound == 'Y') {
    $select_list .= " selected";
}
$select_list .= ">Yes</option>";
$select_list .= "<option value=\"O\"";
if ($with_inbound == 'O') {
    $select_list .= " selected";
}
$select_list .= ">Only</option>";
$select_list .= "</SELECT>"; #"</TD></TR>";
#Hack
$select_list .="<SELECT style=\"display:none\" SIZE=1 NAME=monitor_active ID=monitor_active><option>NONE</option></select>";

/* $select_list .= "<TR><TD align=right>";
  $select_list .= "Monitor:  </TD><TD align=left><SELECT SIZE=1 NAME=monitor_active ID=monitor_active>";
  $select_list .= "<option value=\"\"";
  if (strlen($monitor_active) < 2) {$select_list .= " selected";}
  $select_list .= ">NONE</option>";
  $select_list .= "<option value=\"MONITOR\"";
  if ($monitor_active=='MONITOR') {$select_list .= " selected";}
  $select_list .= ">MONITOR</option>";
  $select_list .= "<option value=\"BARGE\"";
  if ($monitor_active=='BARGE') {$select_list .= " selected";}
  $select_list .= ">BARGE</option>";

  $select_list .= "</SELECT></TD></TR>"; */

#$select_list .= "<TR><TD align=right>";
#$select_list .= "Phone:  </TD><TD align=left>";
$select_list .= "<INPUT type=hidden style=width: NAME=monitor_phone ID=monitor_phone VALUE=\"$monitor_phone\">";
#$select_list .= "</TD></TR>";
#$select_list .= "<TR><TD align=center COLSPAN=2> &nbsp; </TD></TR>";

if ($UGdisplay > 0) {
    $select_list .= "<TR><TD align=right>";
    $select_list .= "Select User Group:  </TD><TD align=left>";
    $select_list .= "<SELECT SIZE=1 NAME=usergroup ID=usergroup>";
    $select_list .= "<option value=\"\">ALL USER GROUPS</option>";
    $o = 0;
    while ($usergroups_to_print > $o) {
        if ($usergroups[$o] == $usergroup) {
            $select_list .= "<option selected value=\"$usergroups[$o]\">$usergroups[$o]</option>";
        } else {
            $select_list .= "<option value=\"$usergroups[$o]\">$usergroups[$o]</option>";
        }
        $o++;
    }
    $select_list .= "</SELECT></TD></TR>";
}

$select_list .= "<TR><TD align=right>";
$select_list .= "Alerta de Poucos Contactos Disponiveis </TD><TD align=left><SELECT SIZE=1 NAME=NOLEADSalert ID=NOLEADSalert>";

$select_list .= "<option value=\"\"";
if (strlen($NOLEADSalert) < 2) {
    $select_list .= " selected";
}

$select_list .= ">NO</option>";

$select_list .= "<option value=\"YES\"";
if ($NOLEADSalert == 'YES') {
    $select_list .= " selected";
}
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Show Drop In-Group Row:  </TD><TD align=left><SELECT SIZE=1 NAME=DROPINGROUPstats ID=DROPINGROUPstats>";
$select_list .= "<option value=\"0\"";
if ($DROPINGROUPstats < 1) {
    $select_list .= " selected";
}
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
if ($DROPINGROUPstats == '1') {
    $select_list .= " selected";
}
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

$select_list .= "<TR><TD align=right>";
$select_list .= "Show Carrier Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=CARRIERstats ID=CARRIERstats>";
$select_list .= "<option value=\"0\"";
if ($CARRIERstats < 1) {
    $select_list .= " selected";
}
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
if ($CARRIERstats == '1') {
    $select_list .= " selected";
}
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";

## find if any selected campaigns have presets enabled
$presets_enabled = 0;
$stmt = "select count(*) from vicidial_campaigns where enable_xfer_presets='ENABLED' $group_SQLand;";
$rslt = mysql_query($stmt, $link);
if ($DB) {
    $OUToutput .= "$stmt\n";
}
$presets_enabled_count = mysql_num_rows($rslt);
if ($presets_enabled_count > 0) {
    $row = mysql_fetch_row($rslt);
    $presets_enabled = $row[0];
}
if ($presets_enabled > 0) {
    $select_list .= "<TR><TD align=right>";
    $select_list .= "Show Presets Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=PRESETstats ID=PRESETstats>";
    $select_list .= "<option value=\"0\"";
    if ($PRESETstats < 1) {
        $select_list .= " selected";
    }
    $select_list .= ">NO</option>";
    $select_list .= "<option value=\"1\"";
    if ($PRESETstats == '1') {
        $select_list .= " selected";
    }
    $select_list .= ">YES</option>";
    $select_list .= "</SELECT></TD></TR>";
} else {
    $select_list .= "<INPUT TYPE=HIDDEN NAME=PRESETstats ID=PRESETstats value=0>";
}

$select_list .= "<TR><TD align=right>";
$select_list .= "Agent Time Stats:  </TD><TD align=left><SELECT SIZE=1 NAME=AGENTtimeSTATS ID=AGENTtimeSTATS>";
$select_list .= "<option value=\"0\"";
if ($AGENTtimeSTATS < 1) {
    $select_list .= " selected";
}
$select_list .= ">NO</option>";
$select_list .= "<option value=\"1\"";
if ($AGENTtimeSTATS == '1') {
    $select_list .= " selected";
}
$select_list .= ">YES</option>";
$select_list .= "</SELECT></TD></TR>";
$select_list .= "<tr><td id=icon32 colspan=3><img style=\'float:right;cursor:pointer;\' src=/images/icons/tick.png onclick=\"update_variables(\'form_submit\',\'\');\" /></td></tr>";
$select_list .= "</TABLE>";
$select_list .= "</TD></TR></table></form>";

$open_list = "<TABLE WIDTH=250 CELLPADDING=0 CELLSPACING=0 BGCOLOR=\"#EFEFEF\"><TR><TD ALIGN=CENTER><a href=\"#\" onclick=\"showDiv(\'campaign_select_list\');\">Choose Report Display Options</a></TD></TR></TABLE>";

if (strlen($monitor_phone) > 1) {
    $stmt = "SELECT extension,dialplan_number,server_ip,login,pass,protocol,conf_secret,is_webphone,use_external_server_ip,codecs_list,webphone_dialpad,outbound_cid from phones where login='$monitor_phone' and active = 'Y';";
    if ($DB) {
        echo "|$stmt|\n";
    }
    $rslt = mysql_query($stmt, $link);
    $Mph_ct = mysql_num_rows($rslt);
    if ($Mph_ct > 0) {
        $row = mysql_fetch_row($rslt);
        $extension = $row[0];
        $dialplan_number = $row[1];
        $webphone_server_ip = $row[2];
        $login = $row[3];
        $pass = $row[4];
        $protocol = $row[5];
        $conf_secret = $row[6];
        $is_webphone = $row[7];
        $use_external_server_ip = $row[8];
        $codecs_list = $row[9];
        $webphone_dialpad = $row[10];
        $outbound_cid = $row[11];

        if ($is_webphone == 'Y') {
            ### build Iframe variable content for webphone here
            $codecs_list = preg_replace("/ /", '', $codecs_list);
            $codecs_list = preg_replace("/-/", '', $codecs_list);
            $codecs_list = preg_replace("/&/", '', $codecs_list);

            if ($use_external_server_ip == 'Y') {
                ##### find external_server_ip if enabled for this phone account
                $stmt = "SELECT external_server_ip FROM servers where server_ip='$webphone_server_ip' LIMIT 1;";
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01065', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                if ($DB) {
                    echo "$stmt\n";
                }
                $exip_ct = mysql_num_rows($rslt);
                if ($exip_ct > 0) {
                    $row = mysql_fetch_row($rslt);
                    $webphone_server_ip = $row[0];
                }
            }
            if (strlen($webphone_url) < 6) {
                ##### find webphone_url in system_settings and generate IFRAME code for it #####
                $stmt = "SELECT webphone_url FROM system_settings LIMIT 1;";
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01066', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                if ($DB) {
                    echo "$stmt\n";
                }
                $wu_ct = mysql_num_rows($rslt);
                if ($wu_ct > 0) {
                    $row = mysql_fetch_row($rslt);
                    $webphone_url = $row[0];
                }
            }
            if (strlen($system_key) < 1) {
                ##### find system_key in system_settings if populated #####
                $stmt = "SELECT webphone_systemkey FROM system_settings LIMIT 1;";
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01068', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                if ($DB) {
                    echo "$stmt\n";
                }
                $wsk_ct = mysql_num_rows($rslt);
                if ($wsk_ct > 0) {
                    $row = mysql_fetch_row($rslt);
                    $system_key = $row[0];
                }
            }
            if (($webphone_dialpad_override != 'DISABLED') and (strlen($webphone_dialpad_override) > 0)) {
                $webphone_dialpad = $webphone_dialpad_override;
            }
            $webphone_options = 'INITIAL_LOAD';
            if ($webphone_dialpad == 'Y') {
                $webphone_options .= "--DIALPAD_Y";
            }
            if ($webphone_dialpad == 'N') {
                $webphone_options .= "--DIALPAD_N";
            }
            if ($webphone_dialpad == 'TOGGLE') {
                $webphone_options .= "--DIALPAD_TOGGLE";
            }
            if ($webphone_dialpad == 'TOGGLE_OFF') {
                $webphone_options .= "--DIALPAD_OFF_TOGGLE";
            }
            $session_name = 'RTS01234561234567890';

            ### base64 encode variables
            $b64_phone_login = base64_encode($extension);
            $b64_phone_pass = base64_encode($conf_secret);
            $b64_session_name = base64_encode($session_name);
            $b64_server_ip = base64_encode($webphone_server_ip);
            $b64_callerid = base64_encode($outbound_cid);
            $b64_protocol = base64_encode($protocol);
            $b64_codecs = base64_encode($codecs_list);
            $b64_options = base64_encode($webphone_options);
            $b64_system_key = base64_encode($system_key);

            $WebPhonEurl = "$webphone_url?phone_login=$b64_phone_login&phone_login=$b64_phone_login&phone_pass=$b64_phone_pass&server_ip=$b64_server_ip&callerid=$b64_callerid&protocol=$b64_protocol&codecs=$b64_codecs&options=$b64_options&system_key=$b64_system_key";
            $webphone_content = "<iframe src=\"$WebPhonEurl\" style=\"width:" . $webphone_width . ";height:" . $webphone_height . ";background-color:transparent;z-index:17;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"webphone\" name=\"webphone\" width=\"" . $webphone_width . "\" height=\"" . $webphone_height . "\"> </iframe>";
        }
    }
}
?>


<script language="Javascript">

var window_focus=true;

$(window).focus(function() {
    window_focus = true;
    $("#info").text("Ligado");
})
    .blur(function() {
    window_focus = false;
    $("#info").text("Desligado");
    });

    window.onload = startup;

    // functions to detect the XY position on the page of the mouse
    function startup() 
    {
        hideDiv('webphone_content');
        document.getElementById('campaign_select_list').innerHTML = select_list;
        hideDiv('campaign_select_list');

        hide_ingroup_info();
        
        realtime_refresh_display();
    }

    function getCursorXY(e) 
    {
        document.getElementById('cursorX').value = (window.Event) ? e.pageX : event.clientX + (document.documentElement.scrollLeft ? document.documentElement.scrollLeft : document.body.scrollLeft);
        document.getElementById('cursorY').value = (window.Event) ? e.pageY : event.clientY + (document.documentElement.scrollTop ? document.documentElement.scrollTop : document.body.scrollTop);
    }

    var PHP_SELF = '<?php echo $PHP_SELF ?>';
    var select_list = '<?php echo $select_list ?>';
    var open_list = '<?php echo $open_list ?>';
    var monitor_phone = '<?php echo $monitor_phone ?>';
    var user = '<?php echo $PHP_AUTH_USER ?>';
    var pass = '<?php echo $PHP_AUTH_PW ?>';
    var RR = '<?php echo $RR ?>';
    var groupQS = '<?php echo $groupQS ?>';
    var DB = '<?php echo $DB ?>';
    var adastats = '<?php echo $adastats ?>';
    var SIPmonitorLINK = '<?php echo $SIPmonitorLINK ?>';
    var IAXmonitorLINK = '<?php echo $IAXmonitorLINK ?>';
    var usergroup = '<?php echo $usergroup ?>';
    var UGdisplay = '<?php echo $UGdisplay ?>';
    var UidORname = '<?php echo $UidORname ?>';
    var orderby = '<?php echo $orderby ?>';
    var SERVdisplay = '<?php echo $SERVdisplay ?>';
    var CALLSdisplay = '<?php echo $CALLSdisplay ?>';
    var PHONEdisplay = '<?php echo $PHONEdisplay ?>';
    var CUSTPHONEdisplay = '<?php echo $CUSTPHONEdisplay ?>';
    var with_inbound = '<?php echo $with_inbound ?>';
    var monitor_active = '<?php echo $monitor_active ?>';
    var monitor_phone = '<?php echo $monitor_phone ?>';
    var ALLINGROUPstats = '<?php echo $ALLINGROUPstats ?>';
    var DROPINGROUPstats = '<?php echo $DROPINGROUPstats ?>';
    var NOLEADSalert = '<?php echo $NOLEADSalert ?>';
    var CARRIERstats = '<?php echo $CARRIERstats ?>';
    var PRESETstats = '<?php echo $PRESETstats ?>';
    var AGENTtimeSTATS = '<?php echo $AGENTtimeSTATS ?>';

    function ForceReady(user,name)
    {
        if (confirm("Tem a certeza que quer tirar o utilzador "+name+" de pausa?")) {
            $.post("sips_xmlrequests.php", { user: user, ACTION: "updateforceready" });
        }
		
    }	
    function ForceLogout(user,name)
    {
        if (confirm("Tem a certeza que quer fazer Logout ao utilizador "+name+"?")) {
            $.post("../user_status.php", { user: user, stage: "log_agent_out" });
        }		
    }	
	

    function showDiv(divvar) 
    {
        if ($('#'+divvar))
        {
            $('#'+divvar).show('fast')
        }
    }
    function hideDiv(divvar)
    {
        if ($('#'+divvar))
        {
            $('#'+divvar).hide()
        }
    }

    function ShowWebphone(divvis)
    {
        if (divvis == 'show')
        {
            divref = document.getElementById('webphone_content').style;
            divref.visibility = 'visible';
            document.getElementById("webphone_visibility").innerHTML = "<a href=\"#\" onclick=\"ShowWebphone('hide');\">webphone -</a>";
        }
        else
        {
            divref = document.getElementById('webphone_content').style;
            divref.visibility = 'hidden';
            document.getElementById("webphone_visibility").innerHTML = "<a href=\"#\" onclick=\"ShowWebphone('show');\">webphone +</a>";
        }
    }

    // function to launch monitoring calls
    function send_monitor(session_id,server_ip,stage)
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
	
        var monitor_phone = prompt("Indique o seu telefone: ", "");
        if (!monitor_phone) {return;}  
	
        if (!xmlhttp && typeof XMLHttpRequest!='undefined')
        {
            xmlhttp = new XMLHttpRequest();
        }
        if (xmlhttp) 
        {
            var monitorQuery = "source=realtime&function=blind_monitor&user=" + user + "&pass=" + pass + "&phone_login=" + monitor_phone + "&session_id=" + session_id + '&server_ip=' + server_ip + '&stage=' + stage;
            xmlhttp.open('POST', '../non_agent_api.php'); 
            xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
            xmlhttp.send(monitorQuery); 
            xmlhttp.onreadystatechange = function() 
            {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
                {
                    //alert(xmlhttp.responseText);
                    var Xoutput = null;
                    Xoutput = xmlhttp.responseText;
                    var regXFerr = new RegExp("ERROR","g");
                    var regXFscs = new RegExp("SUCCESS","g");
                    if (Xoutput.match(regXFerr))
                    {alert(xmlhttp.responseText);}
                    if (Xoutput.match(regXFscs))
                    { 
                    }
                }
            }
            delete xmlhttp;
        }
    }
	
	
    function change_auto_dial_level(active_campaign)
    {
	
        var xmlhttp=false;
	
        var new_auto_dial_level = prompt("Indique o novo Rácio de Marcação para esta Campanha:", "");
        if (!new_auto_dial_level) {return;}  
	
        if (!xmlhttp && typeof XMLHttpRequest!='undefined')
        {
            xmlhttp = new XMLHttpRequest();
        }
        if (xmlhttp) 
        {
            var c_autodial = "source=realtime&function=change_dial_level&user=" + user + "&pass=" + pass + "&newdiallevel=" + new_auto_dial_level + "&campaign_id=" + active_campaign;
            xmlhttp.open('POST', '../non_agent_api.php'); 
            xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
            xmlhttp.send(c_autodial); 
            xmlhttp.onreadystatechange = function() 
            { 
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
                { 
                    if (xmlhttp.responseText != "") {alert(xmlhttp.responseText);} else {update_variables('form_submit','','YES');};
                    var Xoutput = null;
                    Xoutput = xmlhttp.responseText;
                    var regXFerr = new RegExp("ERROR","g");
                    var regXFscs = new RegExp("SUCCESS","g");
                    if (Xoutput.match(regXFerr))
                    {alert(xmlhttp.responseText);}
                    if (Xoutput.match(regXFscs))
                    { 
                    }
                }
            }
            delete xmlhttp;
		
        }
        //
    }
	


    // function to change in-groups selected for a specific agent
    function submit_ingroup_changes(temp_agent_user)
    {
        var temp_ingroup_add_remove_changeIndex = document.getElementById("ingroup_add_remove_change").selectedIndex;
        var temp_ingroup_add_remove_change =  document.getElementById('ingroup_add_remove_change').options[temp_ingroup_add_remove_changeIndex].value;

        var temp_set_as_defaultIndex = document.getElementById("set_as_default").selectedIndex;
        var temp_set_as_default =  document.getElementById('set_as_default').options[temp_set_as_defaultIndex].value;

        var temp_blendedIndex = document.getElementById("blended").selectedIndex;
        var temp_blended =  document.getElementById('blended').options[temp_blendedIndex].value;

        var temp_ingroup_choices = '';
        var txtSelectedValuesObj = document.getElementById('txtSelectedValues');
        var selectedArray = new Array();
        var selObj = document.getElementById('ingroup_new_selections');
        var i;
        var count = 0;
        for (i=0; i<selObj.options.length; i++) 
        {
            if (selObj.options[i].selected) 
            {
                temp_ingroup_choices = temp_ingroup_choices + '+' + selObj.options[i].value;
                count++;
            }
        }

        temp_ingroup_choices = temp_ingroup_choices + '+-';

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
            var changeQuery = "source=realtime&function=change_ingroups&user=" + user + "&pass=" + pass + "&agent_user=" + temp_agent_user + "&value=" + temp_ingroup_add_remove_change + '&set_as_default=' + temp_set_as_default + '&blended=' + temp_blended + '&ingroup_choices=' + temp_ingroup_choices;
            xmlhttp.open('POST', '../agc/api.php'); 
            xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
            xmlhttp.send(changeQuery); 
            xmlhttp.onreadystatechange = function() 
            { 
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
                {
                    var Xoutput = null;
                    Xoutput = xmlhttp.responseText;
                    var regXFerr = new RegExp("ERROR","g");
                    if (Xoutput.match(regXFerr))
                    {alert(xmlhttp.responseText);}
                    else
                    {
                        alert(xmlhttp.responseText);
                        hide_ingroup_info();
                    }
                }
            }
            delete xmlhttp;
        }
    }


    // function to display in-groups selected for a specific agent
    function ingroup_info(agent_user,count)
    {
	
        var cursorheight = (document.REALTIMEform.cursorY.value - 0);
        var newheight = (cursorheight + 10);
        document.getElementById("agent_ingroup_display").style.top = newheight;
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
            var monitorQuery = "source=realtime&function=agent_ingroup_info&stage=change&user=" + user + "&pass=" + pass + "&agent_user=" + agent_user;
            xmlhttp.open('POST', '../non_agent_api.php'); 
            xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
            xmlhttp.send(monitorQuery); 
            xmlhttp.onreadystatechange = function() 
            { 
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
                {
                    var Xoutput = null;
                    Xoutput = xmlhttp.responseText;
                    var regXFerr = new RegExp("ERROR","g");
                    if (Xoutput.match(regXFerr))
                    {alert(xmlhttp.responseText);}
                    else
                    {
                        document.getElementById("agent_ingroup_display").visibility = "visible";
                        document.getElementById("agent_ingroup_display").innerHTML = Xoutput;
                    }
                }
            }
            delete xmlhttp;
	
        }
    }


    // function to display in-groups selected for a specific agent
    function hide_ingroup_info()
    {
        document.getElementById("agent_ingroup_display").visibility = "hidden";
        document.getElementById("agent_ingroup_display").innerHTML = '';
    }

    var ar_refresh=<?php echo "$RR;"; ?>
    var ar_seconds=<?php echo "$RR;"; ?>
    var $start_count=0;

    function realtime_refresh_display()
    {
        if ($start_count < 1)
        {
            gather_realtime_content();
        }
        $start_count++;
        if (ar_seconds > 0)
        {
            document.getElementById("refresh_countdown").innerHTML = "" + ar_seconds + "";
            ar_seconds = (ar_seconds - 1);
            setTimeout("realtime_refresh_display()",1000);
        }
        else
        {
            document.getElementById("refresh_countdown").innerHTML = "0";
            ar_seconds = ar_refresh;
            //	window.location.reload();
            if(window_focus){
            gather_realtime_content();}
			<?php if ($LOGuser_group == "demoij") {?>RefreshGraph();<?php } ?>
            setTimeout("realtime_refresh_display()",1000);
        }
    }


    // function to gather calls and agents statistical content
    function gather_realtime_content()
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
            RTupdate_query = "RTajax=1&DB=" + DB + "" + groupQS + "&adastats=" + adastats + "&SIPmonitorLINK=" + SIPmonitorLINK + "&IAXmonitorLINK=" + IAXmonitorLINK + "&usergroup=" + usergroup + "&UGdisplay=" + UGdisplay + "&UidORname=" + UidORname + "&orderby=" + orderby + "&SERVdisplay=" + SERVdisplay + "&CALLSdisplay=" + CALLSdisplay + "&PHONEdisplay=" + PHONEdisplay + "&CUSTPHONEdisplay=" + CUSTPHONEdisplay + "&with_inbound=" + with_inbound + "&monitor_active=" + monitor_active + "&monitor_phone=" + monitor_phone + "&ALLINGROUPstats=" + ALLINGROUPstats + "&DROPINGROUPstats=" + DROPINGROUPstats + "&NOLEADSalert=" + NOLEADSalert + "&CARRIERstats=" + CARRIERstats + "&PRESETstats=" + PRESETstats + "&AGENTtimeSTATS=" + AGENTtimeSTATS + "";

            xmlhttp.open('POST', 'AST_timeonVDADall.php'); 
            xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
            xmlhttp.send(RTupdate_query); 
            xmlhttp.onreadystatechange = function() 
            { 
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) 
                {
                    document.getElementById("realtime_content").innerHTML = xmlhttp.responseText;
                }
            }
            delete xmlhttp;
        }
    }


    // function to update variables based upon on-page links and forms without reload page(in most cases)
    function update_variables(task_option,task_choice,force_reload)
    {
		
        if (task_option == 'SIPmonitorLINK')
        {
            if (SIPmonitorLINK == '1') {SIPmonitorLINK='0';}
            else {SIPmonitorLINK='1';}
        }
        if (task_option == 'IAXmonitorLINK')
        {
            if (IAXmonitorLINK == '1') {IAXmonitorLINK='0';}
            else {IAXmonitorLINK='1';}
        }
        if (task_option == 'UidORname')
        {
            if (UidORname == '1') {UidORname='0';}
            else {UidORname='1';}
        }
        if (task_option == 'orderby')
        {
            if (task_choice == 'phone')
            {
                if (orderby=='phoneup') {orderby='phonedown';}
                else {orderby='phoneup';}
            }
            if (task_choice == 'user')
            {
                if (orderby=='userup') {orderby='userdown';}
                else {orderby='userup';}
            }
            if (task_choice == 'group')
            {
                if (orderby=='groupup') {orderby='groupdown';}
                else {orderby='groupup';}
            }
            if (task_choice == 'time')
            {
                if (orderby=='timeup') {orderby='timedown';}
                else {orderby='timeup';}
            }
            if (task_choice == 'campaign')
            {
                if (orderby=='campaignup') {orderby='campaigndown';}
                else {orderby='campaignup';}
            }
        }
        if (task_option == 'adastats')
        {
            if (adastats == '1') {adastats='2';   document.getElementById("adastatsTXT").innerHTML = 'Detalhes';}
            else {adastats='1';   document.getElementById("adastatsTXT").innerHTML = 'Detalhes';}
        }
        if (task_option == 'UGdisplay')
        {
            if (UGdisplay == '1') {UGdisplay='0';   document.getElementById("UGdisplayTXT").innerHTML = 'Grupos';}
            else {UGdisplay='1';   document.getElementById("UGdisplayTXT").innerHTML = 'Grupos';}
        }
        if (task_option == 'SERVdisplay')
        {
            if (SERVdisplay == '1') {SERVdisplay='0';   document.getElementById("SERVdisplayTXT").innerHTML = 'SHOW SERVER INFO';}
            else {SERVdisplay='1';   document.getElementById("SERVdisplayTXT").innerHTML = 'HIDE SERVER INFO';}
        }
        if (task_option == 'CALLSdisplay')
        {
            if (CALLSdisplay == '1') {CALLSdisplay='0';   document.getElementById("CALLSdisplayTXT").innerHTML = 'Em Espera';}
            else {CALLSdisplay='1';   document.getElementById("CALLSdisplayTXT").innerHTML = 'Em Espera';}
        }
        if (task_option == 'PHONEdisplay')
        {
            if (PHONEdisplay == '1') {PHONEdisplay='0';   document.getElementById("PHONEdisplayTXT").innerHTML = 'SHOW PHONES';}
            else {PHONEdisplay='1';   document.getElementById("PHONEdisplayTXT").innerHTML = 'HIDE PHONES';}
        }
        if (task_option == 'CUSTPHONEdisplay')
        {
            if (CUSTPHONEdisplay == '1') {CUSTPHONEdisplay='0';   document.getElementById("CUSTPHONEdisplayTXT").innerHTML = 'SHOW CUSTPHONES';}
            else {CUSTPHONEdisplay='1';   document.getElementById("CUSTPHONEdisplayTXT").innerHTML = 'HIDE CUSTPHONES';}
        }
        if (task_option == 'ALLINGROUPstats')
        {
            if (ALLINGROUPstats == '1') {ALLINGROUPstats='0';   document.getElementById("ALLINGROUPstatsTXT").innerHTML = 'SHOW IN-GROUP STATS';}
            else {ALLINGROUPstats='1';   document.getElementById("ALLINGROUPstatsTXT").innerHTML = 'HIDE IN-GROUP STATS';}
        }
        if (task_option == 'form_submit')
        {
            var RRFORM = document.getElementById('RR');
            RR = RRFORM[RRFORM.selectedIndex].value;
            ar_refresh=RR;
            ar_seconds=RR;
            var with_inboundFORM = document.getElementById('with_inbound');
            with_inbound = with_inboundFORM[with_inboundFORM.selectedIndex].value;
            var monitor_activeFORM = document.getElementById('monitor_active');
            monitor_active = monitor_activeFORM[monitor_activeFORM.selectedIndex].value;
            var DROPINGROUPstatsFORM = document.getElementById('DROPINGROUPstats');
            DROPINGROUPstats = DROPINGROUPstatsFORM[DROPINGROUPstatsFORM.selectedIndex].value;
            var NOLEADSalertFORM = document.getElementById('NOLEADSalert');
            NOLEADSalert = NOLEADSalertFORM[NOLEADSalertFORM.selectedIndex].value;
            var CARRIERstatsFORM = document.getElementById('CARRIERstats');
            CARRIERstats = CARRIERstatsFORM[CARRIERstatsFORM.selectedIndex].value;
<?php
if ($presets_enabled > 0) {
    ?>
                                        var PRESETstatsFORM = document.getElementById('PRESETstats');
                                        PRESETstats = PRESETstatsFORM[PRESETstatsFORM.selectedIndex].value;
    <?php
} else {
    echo "PRESETstats=0;\n";
}
?>
                            var AGENTtimeSTATSFORM = document.getElementById('AGENTtimeSTATS');
                            AGENTtimeSTATS = AGENTtimeSTATSFORM[AGENTtimeSTATSFORM.selectedIndex].value;
                            var temp_monitor_phone = document.REALTIMEform.monitor_phone.value;

                            var temp_camp_choices = '';
                            var selCampObj = document.getElementById('groups[]');
                            var i;
                            var count = 0;
                            var selected_all=0;
                            for (i=0; i<selCampObj.options.length; i++) 
                            {
                                if ( (selCampObj.options[i].selected) && (selected_all < 1) )
                                {
                                    temp_camp_choices = temp_camp_choices + '&groups[]=' + selCampObj.options[i].value;
                                    count++;
                                    if (selCampObj.options[i].value == 'ALL-ACTIVE')
                                    {selected_all++;}
                                }
                            }
                            groupQS = temp_camp_choices;
                            hideDiv('campaign_select_list');

                            // force a reload if the phone is changed
                            if ( (temp_monitor_phone != monitor_phone) || (force_reload=='YES') )
                            {
                                reload_url = PHP_SELF + "?RR=" + RR + "&DB=" + DB + "" + groupQS + "&adastats=" + adastats + "&SIPmonitorLINK=" + SIPmonitorLINK + "&IAXmonitorLINK=" + IAXmonitorLINK + "&usergroup=" + usergroup + "&UGdisplay=" + UGdisplay + "&UidORname=" + UidORname + "&orderby=" + orderby + "&SERVdisplay=" + SERVdisplay + "&CALLSdisplay=" + CALLSdisplay + "&PHONEdisplay=" + PHONEdisplay + "&CUSTPHONEdisplay=" + CUSTPHONEdisplay + "&with_inbound=" + with_inbound + "&monitor_active=" + monitor_active + "&monitor_phone=" + temp_monitor_phone + "&ALLINGROUPstats=" + ALLINGROUPstats + "&DROPINGROUPstats=" + DROPINGROUPstats + "&NOLEADSalert=" + NOLEADSalert + "&CARRIERstats=" + CARRIERstats + "&PRESETstats=" + PRESETstats + "&AGENTtimeSTATS=" + AGENTtimeSTATS + "";

                                window.location.href = reload_url;
                            }

                            monitor_phone = document.REALTIMEform.monitor_phone.value;
                        }
                        gather_realtime_content();
						<?php if ($LOGuser_group == "demoij") {?>RefreshGraph();<?php } ?>
                    }

                    function closeAlert(a){
                        $("#"+a).hide();
                    }

</script>
<STYLE type="text/css">

    .green {color: #000000; 
            background-color: #EFFFBC;
            /* For WebKit (Safari, Chrome, etc) */
            background: #EFFFBC -webkit-gradient(linear, left top, left bottom, from(#7FA016), to(#EFFFBC)) no-repeat;
            /* Mozilla,Firefox/Gecko */
            background: #EFFFBC -moz-linear-gradient(top, #7FA016, #EFFFBC) no-repeat;
            /* IE 5.5 - 7 */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#7FA016, endColorstr=#EFFFBC) no-repeat;
            /* IE 8 */
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#7FA016, endColorstr=#7FA016)" no-repeat;

    }
    .red {color: #000000; 
          background-color: #FF7392;
          /* For WebKit (Safari, Chrome, etc) */
          background: #FF7392 -webkit-gradient(linear, left top, left bottom, from(#D90030), to(#FF7392)) no-repeat;
          /* Mozilla,Firefox/Gecko */
          background: #FF7392 -moz-linear-gradient(top, #D90030, #FF7392) no-repeat;
          /* IE 5.5 - 7 */
          filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#D90030, endColorstr=#FF7392) no-repeat;
          /* IE 8 */
          -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#D90030, endColorstr=#D90030)" no-repeat;
    }

    .lightblue {color: #000000;
                background-color: #FFFFFF;
                /* For WebKit (Safari, Chrome, etc) */
                background: #FFFFFF -webkit-gradient(linear, left top, left bottom, from(#73ADC0), to(#FFFFFF)) no-repeat;
                /* Mozilla,Firefox/Gecko */
                background: #FFFFFF -moz-linear-gradient(top, #73ADC0, #FFFFFF) no-repeat;
                /* IE 5.5 - 7 */
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#73ADC0, endColorstr=#FFFFFF) no-repeat;
                /* IE 8 */
                -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#73ADC0, endColorstr=#73ADC0)" no-repeat;
    }

    .blue {color: #000000; 
           background-color: #78BFFF;
           /* For WebKit (Safari, Chrome, etc) */
           background: #78BFFF -webkit-gradient(linear, left top, left bottom, from(#003B70), to(#78BFFF)) no-repeat;
           /* Mozilla,Firefox/Gecko */
           background: #78BFFF -moz-linear-gradient(top, #003B70, #78BFFF) no-repeat;
           /* IE 5.5 - 7 */
           filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#003B70, endColorstr=#78BFFF) no-repeat;
           /* IE 8 */
           -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#003B70, endColorstr=#003B70)" no-repeat;
    }

    .midnightblue {color: #FFFFFF;
                   background-color: #9999E3;
                   /* For WebKit (Safari, Chrome, etc) */
                   background: #9999E3 -webkit-gradient(linear, left top, left bottom, from(#05054A), to(#9999E3)) no-repeat;
                   /* Mozilla,Firefox/Gecko */
                   background: #9999E3 -moz-linear-gradient(top, #05054A, #9999E3) no-repeat;
                   /* IE 5.5 - 7 */
                   filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#05054A, endColorstr=#9999E3) no-repeat;
                   /* IE 8 */
                   -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#05054A, endColorstr=#05054A)" no-repeat;
    }

    .purple {color: #99FFFF; 
             background-color: #EB99C3;
             /* For WebKit (Safari, Chrome, etc) */
             background: #EB99C3 -webkit-gradient(linear, left top, left bottom, from(#52042D), to(#EB99C3)) no-repeat;
             /* Mozilla,Firefox/Gecko */
             background: #EB99C3 -moz-linear-gradient(top, #52042D, #EB99C3) no-repeat;
             /* IE 5.5 - 7 */
             filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#52042D, endColorstr=#EB99C3) no-repeat;
             /* IE 8 */
             -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#52042D, endColorstr=#52042D)" no-repeat;
    }

    .violet {color: #000000; 
             background-color: #B973FF;
             /* For WebKit (Safari, Chrome, etc) */
             background: #B973FF -webkit-gradient(linear, left top, left bottom, from(#6C00D9), to(#B973FF)) no-repeat;
             /* Mozilla,Firefox/Gecko */
             background: #B973FF -moz-linear-gradient(top, #6C00D9, #B973FF) no-repeat;
             /* IE 5.5 - 7 */
             filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#6C00D9, endColorstr=#B973FF) no-repeat;
             /* IE 8 */
             -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#6C00D9, endColorstr=#6C00D9)" no-repeat;
    }

    .thistle {color: #7700CC; 
              background-color: #FFFFFF;
              /* For WebKit (Safari, Chrome, etc) */
              background: #FFFFFF -webkit-gradient(linear, left top, left bottom, from(#B283B2), to(#FFFFFF)) no-repeat;
              /* Mozilla,Firefox/Gecko */
              background: #FFFFFF -moz-linear-gradient(top, #B283B2, #FFFFFF) no-repeat;
              /* IE 5.5 - 7 */
              filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#B283B2, endColorstr=#FFFFFF) no-repeat;
              /* IE 8 */
              -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#B283B2, endColorstr=#B283B2)" no-repeat;
    }

    .olive {color: #332255; 
            background-color: #F3F36D;
            /* For WebKit (Safari, Chrome, etc) */
            background: #F3F36D -webkit-gradient(linear, left top, left bottom, from(#5A5A00), to(#F3F36D)) no-repeat;
            /* Mozilla,Firefox/Gecko */
            background: #F3F36D -moz-linear-gradient(top, #5A5A00, #F3F36D) no-repeat;
            /* IE 5.5 - 7 */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#5A5A00, endColorstr=#F3F36D) no-repeat;
            /* IE 8 */
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#5A5A00, endColorstr=#5A5A00)" no-repeat;
    }

    .lime {color: #000000;
           background-color: #DCFF73;
           /* For WebKit (Safari, Chrome, etc) */
           background: #DCFF73 -webkit-gradient(linear, left top, left bottom, from(#A2D900), to(#DCFF73)) no-repeat;
           /* Mozilla,Firefox/Gecko */
           background: #DCFF73 -moz-linear-gradient(top, #A2D900, #DCFF73) no-repeat;
           /* IE 5.5 - 7 */
           filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#A2D900, endColorstr=#DCFF73) no-repeat;
           /* IE 8 */
           -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#A2D900, endColorstr=#A2D900)" no-repeat;
    }

    .yellow {color: #333355;
             background-color: #FFFFFF;
             /* For WebKit (Safari, Chrome, etc) */
             background: #FFFFFF -webkit-gradient(linear, left top, left bottom, from(#D7D75F), to(#FFFFFF)) no-repeat;
             /* Mozilla,Firefox/Gecko */
             background: #FFFFFF -moz-linear-gradient(top, #D7D75F, #FFFFFF) no-repeat;
             /* IE 5.5 - 7 */
             filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#D7D75F, endColorstr=#FFFFFF) no-repeat;
             /* IE 8 */
             -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#D7D75F, endColorstr=#D7D75F)" no-repeat;
    }

    .khaki {color: #333355; 
            background-color: #FFFFFF;
            /* For WebKit (Safari, Chrome, etc) */
            background: #FFFFFF -webkit-gradient(linear, left top, left bottom, from(#9D855D), to(#FFFFFF)) no-repeat;
            /* Mozilla,Firefox/Gecko */
            background: #FFFFFF -moz-linear-gradient(top, #9D855D, #FFFFFF) no-repeat;
            /* IE 5.5 - 7 */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#9D855D, endColorstr=#FFFFFF) no-repeat;
            /* IE 8 */
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#9D855D, endColorstr=#9D855D)" no-repeat;
    }

    .orange {color: #003355; 
             background-color: #FFCA73;
             /* For WebKit (Safari, Chrome, etc) */
             background: #FFCA73 -webkit-gradient(linear, left top, left bottom, from(#D98700), to(#FFCA73)) no-repeat;
             /* Mozilla,Firefox/Gecko */
             background: #FFCA73 -moz-linear-gradient(top, #D98700, #FFCA73) no-repeat;
             /* IE 5.5 - 7 */
             filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#D98700, endColorstr=#FFCA73) no-repeat;
             /* IE 8 */
             -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#D98700, endColorstr=#D98700)" no-repeat;
    }

    .black {color: #BB9955; 
            background-color: #837F7C;
            /* For WebKit (Safari, Chrome, etc) */
            background: #837F7C -webkit-gradient(linear, left top, left bottom, from(#000000), to(#837F7C)) no-repeat;
            /* Mozilla,Firefox/Gecko */
            background: #837F7C -moz-linear-gradient(top, #000000, #837F7C) no-repeat;
            /* IE 5.5 - 7 */
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#000000, endColorstr=#837F7C) no-repeat;
            /* IE 8 */
            -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#000000, endColorstr=#000000)" no-repeat;
    }

    .salmon {color: #113355; 
             background-color: #FFE4DC;
             /* For WebKit (Safari, Chrome, etc) */
             background: #FFE4DC -webkit-gradient(linear, left top, left bottom, from(#D95E39), to(#FFE4DC)) no-repeat;
             /* Mozilla,Firefox/Gecko */
             background: #FFE4DC -moz-linear-gradient(top, #D95E39, #FFE4DC) no-repeat;
             /* IE 5.5 - 7 */
             filter: progid:DXImageTransform.Microsoft.gradient(startColorstr=#D95E39, endColorstr=#FFE4DC) no-repeat;
             /* IE 8 */
             -ms-filter: "progid:DXImageTransform.Microsoft.gradient(startColorstr=#D95E39, endColorstr=#D95E39)" no-repeat;
    }


    .r1 {color: black; background-color: #FFCCCC}
    .r2 {color: black; background-color: #FF9999}
    .r3 {color: black; background-color: #FF6666}
    .r4 {color: white; background-color: #FF0000}
    .b1 {color: black; background-color: #CCCCFF}
    .b2 {color: black; background-color: #9999FF}
    .b3 {color: black; background-color: #6666FF}
    .b4 {color: white; background-color: #0000FF}
<?php
$stmt = "select group_id,group_color from vicidial_inbound_groups;";
$rslt = mysql_query($stmt, $link);
if ($DB) {
    echo "$stmt\n";
}
$INgroups_to_print = mysql_num_rows($rslt);
if ($INgroups_to_print > 0) {
    $g = 0;
    while ($g < $INgroups_to_print) {
        $row = mysql_fetch_row($rslt);
        $group_id[$g] = $row[0];
        $group_color[$g] = $row[1];
        echo "   .csc$group_id[$g] {color: black; background-color: $group_color[$g]}\n";
        $g++;
    }
}

?></STYLE>
<?php


$stmt = "select count(*) from vicidial_campaigns where active='Y' and campaign_allow_inbound='Y' $group_SQLand;";
$rslt = mysql_query($stmt, $link);
if ($DB) {
    echo "$stmt\n";
}
$row = mysql_fetch_row($rslt);
$campaign_allow_inbound = $row[0];



########################################################################################################################
# NEW REALTIME																																													#
########################################################################################################################

echo "<FORM ACTION=\"$PHP_SELF\" METHOD=GET NAME=REALTIMEform ID=REALTIMEform>\n";
echo "<INPUT TYPE=HIDDEN NAME=cursorX ID=cursorX>\n";
echo "<INPUT TYPE=HIDDEN NAME=cursorY ID=cursorY>\n";

echo "<span style=\"position:absolute;left:10px;top:120px;z-index:14;\" id=agent_ingroup_display>\n";
echo "</span>\n";


# POP UP das Campanhas
echo "<span style=\"position:absolute;left:100px;top:22px;z-index:21;\" id=campaign_select_list>";
echo "<table width=180><tr><td align=center>";
echo "</td></tr></table>";
echo "</span>";

echo "<div class='cc-mstyle'>";
echo "<table>";
echo "<tr>";
echo "<td id=icon32><img src='/images/icons/color_swatch_32.png' /></td>";
echo "<td id=submenu-title style='width:90px;'> Painel Geral </td>";
#echo "<td style='width:80px;'><a href='#' onclick=\"showDiv('campaign_select_list');\">Opções</a> </td>";
echo "<td><SELECT style='width:200px' class='select-campanhas' NAME=groups[] id=groups[]  onchange=update_variables('form_submit',''); >";
$o = 0;
while ($groups_to_print > $o) {
    if (ereg("\|$LISTgroups[$o]\|", $group_string)) {
        $group_string_alterado = ($LISTgroups[$o] == "ALL-ACTIVE") ? "Todas" : "$LISTnames[$o]";
        echo "<option selected value=\"$LISTgroups[$o]\">$group_string_alterado</option>";
    } else {
        $group_string_alterado = ($LISTgroups[$o] == "ALL-ACTIVE") ? "Todas" : "$LISTnames[$o]";
        echo "<option value=\"$LISTgroups[$o]\">$group_string_alterado</option>";
    }
    $o++;
}
echo "</SELECT></td>";

if ($LOGuser_group == "demoij") { ?>
	
    
    
    <td class="play-camp-icon" style="width:32px; cursor:pointer"></td><td class="play-camp-text" style="text-align:left"></td>

<script>




$(".play-camp-icon").click(function(){

	var campaign_id = $(".select-campanhas option:selected").val()	
	
	var status = "";
	
	if($(".play-camp-icon").hasClass("play")) { status="ACTIVE";  } else { status="INACTIVE"; }
	
	
	$.ajax({
		type: "POST",
		url: "_requests.php",
		data: {	action: "start_stop_campaign", 
				sent_campaign_id: campaign_id, 
				sent_status: status
			},
		error: function()
			{
				alert("Ocorreu um Erro.");
			},
		success: function(data)	
			{
				if(status=="ACTIVE"){
					$(".play-camp-icon").html("<img src='/images/icons/control_pause_32.png' />");
					$(".play-camp-text").html("Pausar Campanha");
					$(".play-camp-icon").addClass("pause");
					$(".play-camp-icon").removeClass("play")
					} else {
					$(".play-camp-icon").html("<img src='/images/icons/control_play_32.png' />");
					$(".play-camp-text").html("Iniciar Campanha");
					$(".play-camp-icon").addClass("play");
					$(".play-camp-icon").removeClass("pause")
					}
				update_variables('form_submit','','YES')
			}
		});


	
})


$(".select-campanhas").change(function(){
	
	
	
	
	var campaign_id = $(".select-campanhas option:selected").val()
	
	if(campaign_id=="ALL-ACTIVE") {$(".play-camp-icon").html("");
					$(".play-camp-text").html("");
					return false;}
	
	
	$.ajax({
		type: "POST",
		url: "_requests.php",
		data: {	action: "check_remote_active", 
				sent_campaign_id: campaign_id
			},
		error: function()
			{
				alert("Ocorreu um Erro.");
			},
		success: function(data)	
			{
				if(data==1){
					$(".play-camp-icon").html("<img src='/images/icons/control_pause_32.png' />");
					$(".play-camp-text").html("Pausar Campanha");
					$(".play-camp-icon").addClass("pause");
					$(".play-camp-icon").removeClass("play")
					} else {
					$(".play-camp-icon").html("<img src='/images/icons/control_play_32.png' />");
					$(".play-camp-text").html("Iniciar Campanha");
					$(".play-camp-icon").addClass("play");
					$(".play-camp-icon").removeClass("pause")
					
					
					}
			}
		});
	
	
	})
	
$(document).ready(function(){
	<?php if ($LOGuser_group == "demoij") {?>RefreshGraph();<?php } ?>
	
	var campaign_id = $(".select-campanhas option:selected").val()
	
	if(campaign_id=="ALL-ACTIVE") {$(".play-camp-icon").html("");
					$(".play-camp-text").html("");
					return false;}
	
	
	$.ajax({
		type: "POST",
		url: "_requests.php",
		data: {	action: "check_remote_active", 
				sent_campaign_id: campaign_id
			},
		error: function()
			{
				alert("Ocorreu um Erro.");
			},
		success: function(data)	
			{
				if(data==1){
					$(".play-camp-icon").html("<img src='/images/icons/control_pause_32.png' />");
					$(".play-camp-text").html("Pausar Campanha");
					$(".play-camp-icon").addClass("pause");
					$(".play-camp-icon").removeClass("play")
					} else {
					$(".play-camp-icon").html("<img src='/images/icons/control_play_32.png' />");
					$(".play-camp-text").html("Iniciar Campanha");
					$(".play-camp-icon").addClass("play");
					$(".play-camp-icon").removeClass("pause")
					
					
					}
			}
		})
	
	
	})




</script>



<? } 




echo "<td style=text-align:right> <span id=info >Ligado</span> Actualização em:<span id=refresh_countdown name=refresh_countdown></span> segundos</td>\n\n";
echo "<td id=icon32 style='padding-right:12px; padding-left:0;'><img style='cursor:pointer;' src='/images/icons/arrow_refresh.png' onclick=\"update_variables('form_submit','','YES')\" /></td>";
echo "</tr>";
echo "</table>";
echo "</div>";
echo "<br>";

echo "<div id=realtime_content name=realtime_content></div>\n";









?>

<? if ($LOGuser_group == "demoij") {
	
	$query1 = mysql_fetch_assoc(mysql_query("SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]';", $link)) or die(mysql_error());
$query2 = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$query1[user_group]';", $link)) or die(mysql_error());
$AllowedCampaigns = "'" . preg_replace("/ /","','" , preg_replace("/ -/",'',$query2['allowed_campaigns'])) . "'";
	
	
	
	
	 ?>

<br /><br />
        <center><div id="placeholder" style="width:850px;height:300px; margin-left:32px;"></div></center>

        




        <script>
        /* ----------------------------------------------------------------------------------------------------------------- */




var AllowedCampaigns = "<? echo $AllowedCampaigns; ?>";

var options = {
				lines: 	{ 	show: true	},
				points: { 	show: true	},
				xaxis: 	{ 	tickDecimals: 0, tickSize: 1	},
				yaxis: 	{	tickDecimals: 0, tickSize: 10	}, 
				legend: {	position: "ne", margin: [-50, 320]	}
			};

var data = [];
var seriesToGet = new Array("calls_hora", "calls_atendidas", "msg_entregues")
var placeholder = $("#placeholder");
$.plot(placeholder, data, options);

var alreadyFetched = {};
        
function onDataReceived(series) {
	if (!alreadyFetched[series.label]) {
		alreadyFetched[series.label] = true;
		data.push(series);
	}
	$.plot(placeholder, data, options);
}
		
		
$("#getgraph").click(function(){<?php if ($LOGuser_group == "demoij") {?>RefreshGraph();<?php } ?>})		
		
		
		
	
	function RefreshGraph(){	
		alreadyFetched = {};
		data = [];
		//$.plot(placeholder, data, options);
		
		$.each(seriesToGet, function(index, value){
					
			$.ajax({
				type: "POST",
				dataType: "JSON",
				url: "sips_xmlrequests.php",
				data: {	action: "get_graph_data", sent_series : value, sent_allowed_campaigns: AllowedCampaigns },
				success: onDataReceived
			})	
			
		})
	}
			
        
    
		




        </script>
<? } ?>
    </BODY>
    </HTML>
    
    
    
