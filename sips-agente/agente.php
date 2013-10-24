<?php
$mel = 1;     # Mysql Error Log enabled = 1 
$one_mysql_log = 0;

require("dbconnect.php");
require("functions.php");

if (isset($_GET["sips_login"])) {
    $sips_login = $_GET["sips_login"];
} elseif (isset($_POST["sips_login"])) {
    $sips_login = $_POST["sips_login"];
}
if (isset($_GET["sips_pass"])) {
    $sips_pass = $_GET["sips_pass"];
} elseif (isset($_POST["sips_pass"])) {
    $sips_pass = $_POST["sips_pass"];
}

$curLogo = $_POST['curlogo'];
if (isset($_GET["DB"])) {
    $DB = $_GET["DB"];
} elseif (isset($_POST["DB"])) {
    $DB = $_POST["DB"];
}
if (isset($_GET["JS_browser_width"])) {
    $JS_browser_width = $_GET["JS_browser_width"];
} elseif (isset($_POST["JS_browser_width"])) {
    $JS_browser_width = $_POST["JS_browser_width"];
}
if (isset($_GET["JS_browser_height"])) {
    $JS_browser_height = $_GET["JS_browser_height"];
} elseif (isset($_POST["JS_browser_height"])) {
    $JS_browser_height = $_POST["JS_browser_height"];
}
if (isset($_GET["phone_login"])) {
    $phone_login = $_GET["phone_login"];
} elseif (isset($_POST["phone_login"])) {
    $phone_login = $_POST["phone_login"];
}
if (isset($_GET["phone_pass"])) {
    $phone_pass = $_GET["phone_pass"];
} elseif (isset($_POST["phone_pass"])) {
    $phone_pass = $_POST["phone_pass"];
}
if (isset($_GET["VD_login"])) {
    $VD_login = $_GET["VD_login"];
} elseif (isset($_POST["VD_login"])) {
    $VD_login = $_POST["VD_login"];
}
if (isset($_GET["VD_pass"])) {
    $VD_pass = $_GET["VD_pass"];
} elseif (isset($_POST["VD_pass"])) {
    $VD_pass = $_POST["VD_pass"];
}
if (isset($_GET["VD_campaign"])) {
    $VD_campaign = $_GET["VD_campaign"];
} elseif (isset($_POST["VD_campaign"])) {
    $VD_campaign = $_POST["VD_campaign"];
}
if (isset($_GET["relogin"])) {
    $relogin = $_GET["relogin"];
} elseif (isset($_POST["relogin"])) {
    $relogin = $_POST["relogin"];
}
if (isset($_GET["MGR_override"])) {
    $MGR_override = $_GET["MGR_override"];
} elseif (isset($_POST["MGR_override"])) {
    $MGR_override = $_POST["MGR_override"];
}
if (!isset($phone_login)) {
    if (isset($_GET["pl"])) {
        $phone_login = $_GET["pl"];
    } elseif (isset($_POST["pl"])) {
        $phone_login = $_POST["pl"];
    }
}
if (!isset($phone_pass)) {
    if (isset($_GET["pp"])) {
        $phone_pass = $_GET["pp"];
    } elseif (isset($_POST["pp"])) {
        $phone_pass = $_POST["pp"];
    }
}
if (isset($VD_campaign)) {
    $VD_campaign = strtoupper($VD_campaign);
    $VD_campaign = eregi_replace(" ", '', $VD_campaign);
}
if (!isset($flag_channels)) {
    $flag_channels = 0;
    $flag_string = '';
}


### security strip all non-alphanumeric characters out of the variables ###
$DB = ereg_replace("[^0-9a-z]", "", $DB);
$phone_login = ereg_replace("[^\,0-9a-zA-Z]", "", $phone_login);
$phone_pass = ereg_replace("[^0-9a-zA-Z]", "", $phone_pass);
$VD_login = ereg_replace("[^-_0-9a-zA-Z]", "", $VD_login);
$VD_pass = ereg_replace("[^-_0-9a-zA-Z]", "", $VD_pass);
$VD_campaign = ereg_replace("[^-_0-9a-zA-Z]", "", $VD_campaign);

//$no_campaign = 0;
if ($VD_campaign == '') {
    $no_campaign = 1;
} else {
    $no_campaign = 0;
}

if ($phone_login != NULL) {

    $query = "SELECT extension FROM phones where extension='$phone_login';";
    $query_result = mysql_query($query, $link);
    $result_phone_login = mysql_num_rows($query_result);

    $phone_exists = 0;
    $pl_message = '';

    if ($result_phone_login == 0) {
        $phone_exists = 0;
        $pl_message = "Essa licença não se encontra registada.";
    } else {
        $phone_exists = 1;
    }
} else {
    $pl_message = '';
}


$forever_stop = 0;

if ($force_logout) {
    echo "Logout com sucesso. Obrigado\n";
    exit;
}

$isdst = date("I");
$StarTtimE = date("U");
$NOW_TIME = date("Y-m-d H:i:s");
$tsNOW_TIME = date("YmdHis");
$FILE_TIME = date("Ymd-His");
$loginDATE = date("Ymd");
$CIDdate = date("ymdHis");
$month_old = mktime(11, 0, 0, date("m"), date("d") - 2, date("Y"));
$past_month_date = date("Y-m-d H:i:s", $month_old);
$minutes_old = mktime(date("H"), date("i") - 2, date("s"), date("m"), date("d"), date("Y"));
$past_minutes_date = date("Y-m-d H:i:s", $minutes_old);
$webphone_width = 460;
$webphone_height = 500;


$random = (rand(1000000, 9999999) + 10000000);

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,vdc_header_date_format,vdc_customer_date_format,vdc_header_phone_format,webroot_writable,timeclock_end_of_day,vtiger_url,enable_vtiger_integration,outbound_autodial_active,enable_second_webform,user_territories_active,static_agent_url,custom_fields_enabled FROM system_settings;";
$rslt = mysql_query($stmt, $link);
if ($mel > 0) {
    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01001', $VD_login, $server_ip, $session_name, $one_mysql_log);
}
if ($DB) {
    echo "$stmt\n";
}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0) {
    $row = mysql_fetch_row($rslt);
    $non_latin = $row[0];
    $vdc_header_date_format = $row[1];
    $vdc_customer_date_format = $row[2];
    $vdc_header_phone_format = $row[3];
    $WeBRooTWritablE = $row[4];
    $timeclock_end_of_day = $row[5];
    $vtiger_url = $row[6];
    $enable_vtiger_integration = $row[7];
    $outbound_autodial_active = $row[8];
    $enable_second_webform = $row[9];
    $user_territories_active = $row[10];
    $static_agent_url = $row[11];
    $custom_fields_enabled = $row[12];
}
##### END SETTINGS LOOKUP #####
###########################################
##### DEFINABLE SETTINGS AND OPTIONS
###########################################
# set defaults for hard-coded variables
$conf_silent_prefix = '5'; # vicidial_conferences prefix to enter silently and muted for recording
$dtmf_silent_prefix = '7'; # vicidial_conferences prefix to enter silently
$HKuser_level = '5'; # minimum vicidial user_level for HotKeys
$campaign_login_list = '1'; # show drop-down list of campaigns at login	
$manual_dial_preview = '1'; # allow preview lead option when manual dial
$view_scripts = '1'; # set to 1 to show the SCRIPTS tab
$dispo_check_all_pause = '0'; # set to 1 to allow for persistent pause after dispo
$callholdstatus = '1'; # set to 1 to show calls on hold count
$agentcallsstatus = '0'; # set to 1 to show agent status and call dialed count
$campagentstatctmax = '3'; # Number of seconds for campaign call and agent stats
$show_campname_pulldown = '1'; # set to 1 to show campaign name on login pulldown
$webform_sessionname = '1'; # set to 1 to include the session_name in webform URL
$local_consult_xfers = '1'; # set to 1 to send consultative transfers from original server
$clientDST = '1'; # set to 1 to check for DST on server for agent time
$no_delete_sessions = '1'; # set to 1 to not delete sessions at logout
$volumecontrol_active = '1'; # set to 1 to allow agents to alter volume of channels
$PreseT_DiaL_LinKs = '0'; # set to 1 to show a DIAL link for Dial Presets
$LogiNAJAX = '1'; # set to 1 to do lookups on campaigns for login
$HidEMonitoRSessionS = '1'; # set to 1 to hide remote monitoring channels from "session calls"
$hangup_all_non_reserved = '1'; # set to 1 to force hangup all non-reserved channels upon Desligar Chamada
$LogouTKicKAlL = '1'; # set to 1 to hangup all calls in session upon agent logout
$PhonESComPIP = '1'; # set to 1 to log computer IP to phone if blank, set to 2 to force log each login
$DefaulTAlTDiaL = '0'; # set to 1 to enable ALT DIAL by default if enabled for the campaign
$AgentAlert_allowed = '1'; # set to 1 to allow Agent alert option
$disable_blended_checkbox = '0'; # set to 1 to disable the BLENDED checkbox from the in-group chooser screen
$hide_timeclock_link = '1'; # set to 1 to hide the timeclock link on the agent login screen
$conf_check_attempts = '3'; # number of attempts to try before loosing webserver connection, for bad network setups
$focus_blur_enabled = '0'; # set to 1 to enable the focus/blur enter key blocking(some IE instances have issues)
$TEST_all_statuses = '0'; # TEST variable allows all statuses in dispo screen

$stretch_dimensions = '1'; # sets the vicidial screen to the size of the browser window
$BROWSER_HEIGHT = 850; # set to the minimum browser height, default=500
$BROWSER_WIDTH = 1100; # set to the minimum browser width, default=770
$webphone_location = 'right'; # set the location on the agent screen 'right' or 'bar'
$MAIN_COLOR = '#FFFFFF'; # old default is 
$SIDEBAR_COLOR = '#F6F6F6';

# if options file exists, use the override values for the above variables
#   see the options-example.php file for more information
if (file_exists('options.php')) {
    require('options.php');
}


$stmt = "SELECT NAME,DISPLAY_NAME,READONLY,ACTIVE FROM `vicidial_list_ref` WHERE campaign_id='$VD_campaign' ORDER BY field_order asc";
$rslt = mysql_query($stmt, $link);

$search_opt = "";

$fields_order = array();
for ($i = 0; $i < mysql_num_rows($rslt); $i++) {
    $row = mysql_fetch_assoc($rslt);
    $fields_order[$i][0] = strtolower($row['NAME']);
    $fields_order[$i][1] = $row['ACTIVE'] == 1;
    $fields_order[$i][2] = ($row['READONLY'] == 1 ? "readonly='readonly'" : "");
    $fields_order[$i][3] = $row['DISPLAY_NAME'];
    $search_opt.= ($row['ACTIVE'] == 1) ? "<option value='$row[NAME]'>$row[DISPLAY_NAME]</option>" : "";
}
##END OF CUSTOM FIELDS

$hide_gender = 0;
if ($label_gender == '---HIDE---' or 1) {
    $hide_gender = 1;
}

$US = '_';
$CL = ':';
$AT = '@';
$DS = '-';
$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");
$script_name = getenv("SCRIPT_NAME");
$server_name = getenv("SERVER_NAME");
$server_port = getenv("SERVER_PORT");
if (eregi("443", $server_port)) {
    $HTTPprotocol = 'https://';
} else {
    $HTTPprotocol = 'http://';
}
if (($server_port == '80') or ($server_port == '443')) {
    $server_port = '';
} else {
    $server_port = "$CL$server_port";
}
$agcPAGE = "$HTTPprotocol$server_name$server_port$script_name";
$agcDIR = eregi_replace('vicidial.php', '', $agcPAGE);
if (strlen($static_agent_url) > 5) {
    $agcPAGE = $static_agent_url;
}


header("Content-type: text/html; charset=utf-8");
header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");                          // HTTP/1.0
?>
<!DOCTYPE html>
<html> 
    <head> 

        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/animate.min.css" />

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/moment.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/moment.langs.min.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.9.0.custom.min.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/plugins/datetimepicker.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/language/pt-pt.js"></script>
        <script type="text/javascript" src="/jquery/jsdatatable/plugins/plugin.fnAjaxReload.js"></script>
        <script type="text/javascript" src="/ini/SeamlessLoop.js"></script>
        <script type="text/javascript" src="/jquery/scrollto/jquery.scrollTo-1.4.3.1-min.js"></script>

        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <style>
            html, body, iframe,.height100{
                height:100%}
            div#tcal{
                z-index: 2147483647;
                position: fixed;
            }
            div#tcalShade{
                z-index: 2147483646;
                position: fixed;
            }
            .grid-agent{
                left:50%;
                bottom:0%;
                position:fixed;
                background-color: #ffffff;
                border: 1px solid #999;
                border: 1px solid rgba(0, 0, 0, 0.3);
                border-top-left-radius: 6px;
                border-top-right-radius: 6px;
                border-bottom-right-radius: 0px;
                border-bottom-left-radius: 0px;
                -webkit-border-top-left-radius: 6px;
                -webkit-border-top-right-radius: 6px;
                -webkit-border-bottom-right-radius: 0px;
                -webkit-border-bottom-left-radius: 0px;
                -moz-border-top-left-radius: 6px;
                -moz-border-top-right-radius: 6px;
                -moz-border-bottom-right-radius: 0px;
                -moz-border-bottom-left-radius: 0px;
                -webkit-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
                -moz-box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
                box-shadow: 0 3px 7px rgba(0, 0, 0, 0.3);
                -webkit-background-clip: padding-box;
                -moz-background-clip: padding-box;
                background-clip: padding-box;
            }
            .grid-title-icon{margin-top: 7px;}
            #DispoSelectContent .btn,#PauseCodeSelectContent .btn {
                margin:2px 2px;}
            .div-title {
                width:100%;
                border-bottom: 1px solid #DDDDDD;
                font-weight:bold;
                margin:8px 0 10px 0; }
            #cc-header {
                position:relative;
                border-bottom: 3px solid rgb(168, 168, 168);
            }


            .cc-menu {
                color: rgb(105, 105, 105);
                border-bottom: 2px solid rgb(192, 192, 192);
            } 
            #LoadingBox{
                background: #f9f9f9;
                top: 0px;
                left: 0px;
                position: absolute;
                height: 100%;
                width: 100%;
                z-index: 90000;
            }
            #LoadingBox > img{
                position:absolute;
                left:50%;
                top:50%;
                margin-left: -33px;
                margin-top: -33px;
            }
            .msg-marquee{
                margin-left: 10px;
            }
            .view-button{
                margin-left: 0.5em;
            }
            .box_title {
                color: rgb(255, 255, 255);
            }
        </style>

        <?php
        if ($campaign_login_list > 0) {
            $camp_form_code = "<select name=\"VD_campaign\" id=\"VD_campaign\" onfocus=\"login_allowable_campaigns()\">\n";
            $camp_form_code .= "<option value=\"\"></option>\n";

            $LOGallowed_campaignsSQL = '';
            if ($relogin == 'YES') {
                $stmt = "SELECT user_group from vicidial_users where user='$VD_login' and pass='$VD_pass';";
                if ($non_latin > 0) {
                    $rslt = mysql_query("SET NAMES 'UTF8'");
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01002', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $row = mysql_fetch_row($rslt);
                $VU_user_group = $row[0];

                $stmt = "SELECT allowed_campaigns from vicidial_user_groups where user_group='$VU_user_group';";
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01003', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $row = mysql_fetch_row($rslt);
                if ((!eregi("ALL-CAMPAIGNS", $row[0]))) {
                    $LOGallowed_campaignsSQL = eregi_replace(' -', '', $row[0]);
                    $LOGallowed_campaignsSQL = eregi_replace(' ', "','", $LOGallowed_campaignsSQL);
                    $LOGallowed_campaignsSQL = "and campaign_id IN('$LOGallowed_campaignsSQL')";
                }
            }

            ### code for manager override of shift restrictions
            if ($MGR_override > 0) {
                if (isset($_GET["MGR_login$loginDATE"])) {
                    $MGR_login = $_GET["MGR_login$loginDATE"];
                } elseif (isset($_POST["MGR_login$loginDATE"])) {
                    $MGR_login = $_POST["MGR_login$loginDATE"];
                }
                if (isset($_GET["MGR_pass$loginDATE"])) {
                    $MGR_pass = $_GET["MGR_pass$loginDATE"];
                } elseif (isset($_POST["MGR_pass$loginDATE"])) {
                    $MGR_pass = $_POST["MGR_pass$loginDATE"];
                }

                $stmt = "SELECT count(*) from vicidial_users where user='$MGR_login' and pass='$MGR_pass' and manager_shift_enforcement_override='1' and active='Y';";
                if ($DB) {
                    echo "|$stmt|\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01058', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $row = mysql_fetch_row($rslt);
                $MGR_auth = $row[0];

                if ($MGR_auth > 0) {
                    $stmt = "UPDATE vicidial_users SET shift_override_flag='1' where user='$VD_login' and pass='$VD_pass';";
                    if ($DB) {
                        echo "|$stmt|\n";
                    }
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01059', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    echo "<!-- Shift Override entered for $VD_login by $MGR_login -->\n";

                    ### Add a record to the vicidial_admin_log
                    $SQL_log = "$stmt|";
                    $SQL_log = ereg_replace(';', '', $SQL_log);
                    $SQL_log = addslashes($SQL_log);
                    $stmt = "INSERT INTO vicidial_admin_log set event_date='$NOW_TIME', user='$MGR_login', ip_address='$ip', event_section='AGENT', event_type='OVERRIDE', record_id='$VD_login', event_code='MANAGER OVERRIDE OF AGENT SHIFT ENFORCEMENT', event_sql=\"$SQL_log\", event_notes='user: $VD_login';";
                    if ($DB) {
                        echo "|$stmt|\n";
                    }
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01060', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                }
            }


            $stmt = "SELECT campaign_id,campaign_name from vicidial_campaigns where active='Y' $LOGallowed_campaignsSQL order by campaign_id;";
            //echo $stmt;
            if ($non_latin > 0) {
                $rslt = mysql_query("SET NAMES 'UTF8'");
            }
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01004', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $camps_to_print = mysql_num_rows($rslt);

            $o = 0;
            while ($camps_to_print > $o) {
                $rowx = mysql_fetch_row($rslt);
                if ($show_campname_pulldown) {
                    $campname = "$rowx[1]";
                } else {
                    $campname = '';
                }
                if ($VD_campaign) {
                    if ((eregi("$VD_campaign", $rowx[0])) and (strlen($VD_campaign) == strlen($rowx[0]))) {
                        $camp_form_code .= "<option value=\"$rowx[0]\" selected=\"selected\">$campname</option>\n";
                    } else {
                        if (!ereg('login_allowable_campaigns', $camp_form_code)) {
                            $camp_form_code .= "<option value=\"$rowx[0]\">$campname</option>\n";
                        }
                    }
                } else {
                    if (!ereg('login_allowable_campaigns', $camp_form_code)) {
                        $camp_form_code .= "<option value=\"$rowx[0]\">$campname</option>\n";
                    }
                }
                $o++;
            }
            $camp_form_code .= "</select>\n";
        } else {
            $camp_form_code = "<input type=\"text\" name=\"vd_campaign\" style=width:200px maxlength=\"20\" value=\"$VD_campaign\" />\n";
        }


        if ($LogiNAJAX > 0) {
            ?>

            <script type="text/javascript">
                function login_allowable_campaigns()
                {
                    logincampaign_query = {user: document.vicidial_form.VD_login.value, pass: document.vicidial_form.VD_pass.value, ACTION: "LogiNCamPaigns", format: "html"};

                    $.post('vdc_db_query.php', logincampaign_query,
                            function(data) {
                                Nactiveext = null;
                                Nactiveext = data;

                                $("#LogiNCamPaigns").html(data);
                            });

                }
            </script>

            <?php
        }

        $query = "SELECT default_phone_login_password FROM system_settings;";
        $query_result = mysql_query($query, $link);
        $result_phone_pass = mysql_fetch_row($query_result);

        if ($relogin == 'YES') {

            $phone_pass = $result_phone_pass[0];
            ?>
            <title>Go Contact: Relogin</title>
        </head>
        <body>
            <div style="width: 525px;margin: auto;">
                <img style='margin-top:10%;' src=../images/pictures/go_logo_35.png ALT=Logo />
                <div class="grid">
                    <div class="grid-content">

                        <form name="vicidial_form" id="vicidial_form" class="form-horizontal" action="<?= $agcPAGE ?>" method="post">
                            <input type="hidden" name="curlogo" value="<?= $curLogo ?>" />
                            <input type="hidden" name="DB" id="DB" value="<?= $DB ?>" />
                            <input type="hidden" name="JS_browser_height" id="JS_browser_height" value="" />
                            <input type="hidden" name="JS_browser_width" id="JS_browser_width" value="" />
                            <input type="hidden" name="phone_pass" value="<?= $phone_pass ?>" />

                            <div class="control-group">
                                <label class="control-label">Licença: </label>
                                <div class="controls">
                                    <input type="text" name="phone_login" maxlength="20" value="<?= $phone_login ?>"  />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Operador: </label>
                                <div class="controls">
                                    <input type="text" name="VD_login" maxlength="20" value="<?= $VD_login ?>"  />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Password: </label>
                                <div class="controls">
                                    <input type="password" name="VD_pass" maxlength="20" value="<?= $VD_pass ?>"  />
                                </div>
                            </div>
                            <div class="control-group">
                                <label class="control-label">Campanha: </label>
                                <div class="controls" id="LogiNCamPaigns">
                                    <?= $camp_form_code ?>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <button class="btn btn-primary"><i class="icon-signin"></i> Log-In </button>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </form>
                    </div>
                </div>

                <?php
                $queryClient = "SELECT server_description from servers limit 1";
                $queryClient = mysql_query($queryClient, $link) or die(mysql_error());
                $curClient = mysql_fetch_row($queryClient);
                
                unset($curLogo);
                if (file_exists("../client_files/$curClient[0]/logo.gif")) {
                    $curLogo = "client_files/$curClient[0]/logo.gif";
                    echo "<input type=hidden name=curlogo value=$curLogo />";
                } else {
                    unset($curLogo);
                }

                if (isset($curLogo) && $curLogo != "") {
                    ?>
                    <br><br><img src=../<?= $curLogo ?> />
                <?php } ?>
            </div>
        </body>
    </html>
    <?php
    exit;
}
#############################################################################
#1			Primeiro Menu de Login                              #
#############################################################################

if ($phone_exists == 0) {
    $phone_pass = $result_phone_pass[0];
    ?>
    <title>Go Contact: Login de Operador</title>
    </head>
    <body>
        <div style="width: 525px;margin: auto;">
            <img style='margin-top:10%;' src=../images/pictures/go_logo_35.png />

            <div class="grid">
                <div class="grid-content">
                    <form name="vicidial_form" id="vicidial_form" class="form-horizontal" action="<?= $agcPAGE ?>" method="post">
                        <input type="hidden" name="curlogo" value="<?= $curLogo ?>" />
                        <input type="hidden" name="DB" value="<?= $DB ?>" />
                        <input type="hidden" name="JS_browser_height" id="JS_browser_height" value="" />
                        <input type="hidden" name="JS_browser_width" id="JS_browser_width" value="" />
                        <input type="hidden" name="phone_pass" value="<?= $phone_pass ?>" />
                        <input type="hidden" name="sips_login" value="<?= $sips_login ?>" />
                        <input type="hidden" name="sips_pass" value="<?= $sips_pass ?>" />
                        <?php if (strlen($pl_message)) { ?>
                            <div class="alert"><?= $pl_message ?></div>
                        <?php } ?>
                        <div class="control-group">
                            <label class="control-label">Licença: </label>
                            <div class="controls">
                                <input type="text" name="phone_login" value="<?= $phone_login ?>" />
                            </div>
                        </div>
                        <div class="control-group">
                            <div class="controls">
                                <button class="btn btn-primary"><i class="icon-signin"></i> Log-In</button>
                            </div>
                        </div>
                        <div class="clear"></div>
                    </form>
                    <span id="LogiNReseT"></span>
                </div>
            </div>
            <?php
            $curLogo = $_POST['curlogo'];
            if (isset($curLogo) && $curLogo != "") {
                ?>
                <br><br><img src=../<?= $curLogo ?> />
            <?php } ?>

        </div>
    </body>
    </html>
    <?php
    exit;
} else {
    if ($WeBRooTWritablE > 0) {
        $fp = fopen("./vicidial_auth_entries.txt", "a");
    }
    $VDloginDISPLAY = 0;

    if ((strlen($VD_login) < 2) or (strlen($VD_pass) < 2) or (strlen($VD_campaign) < 2)) {
        $VDloginDISPLAY = 1;
    } else {
        $stmt = "SELECT count(*) from vicidial_users where user='$VD_login' and pass='$VD_pass' and user_level > 0 and active='Y';";
        if ($DB) {
            echo "|$stmt|\n";
        }
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01006', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }
        $row = mysql_fetch_row($rslt);
        $auth = $row[0];

        if ($auth > 0) {
            $login = strtoupper($VD_login);
            $password = strtoupper($VD_pass);
            ##### grab the full name of the agent
            $stmt = "SELECT full_name,user_level,hotkeys_active,agent_choose_ingroups,scheduled_callbacks,agentonly_callbacks,agentcall_manual,vicidial_recording,vicidial_transfers,closer_default_blended,user_group,vicidial_recording_override,alter_custphone_override,alert_enabled,agent_shift_enforcement_override,shift_override_flag,allow_alerts,closer_campaigns,agent_choose_territories,custom_one,custom_two,custom_three,custom_four,custom_five,agent_call_log_view_override,agent_choose_blended,agent_lead_search_override from vicidial_users where user='$VD_login' and pass='$VD_pass'";
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01007', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $row = mysql_fetch_row($rslt);
            $LOGfullname = $row[0];
            $user_level = $row[1];
            $VU_hotkeys_active = $row[2];
            $VU_agent_choose_ingroups = $row[3];
            $VU_scheduled_callbacks = $row[4];
            $agentonly_callbacks = $row[5];
            $agentcall_manual = $row[6];
            $VU_vicidial_recording = $row[7];
            $VU_vicidial_transfers = $row[8];
            $VU_closer_default_blended = $row[9];
            $VU_user_group = $row[10];
            $VU_vicidial_recording_override = $row[11];
            $VU_alter_custphone_override = $row[12];
            $VU_alert_enabled = $row[13];
            $VU_agent_shift_enforcement_override = $row[14];
            $VU_shift_override_flag = $row[15];
            $VU_allow_alerts = $row[16];
            $VU_closer_campaigns = $row[17];
            $VU_agent_choose_territories = $row[18];
            $VU_custom_one = $row[19];
            $VU_custom_two = $row[20];
            $VU_custom_three = $row[21];
            $VU_custom_four = $row[22];
            $VU_custom_five = $row[23];
            $VU_agent_call_log_view_override = $row[24];
            $VU_agent_choose_blended = $row[25];
            $VU_agent_lead_search_override = $row[26];


            if (($VU_alert_enabled > 0) and ($VU_allow_alerts > 0)) {
                $VU_alert_enabled = 'ON';
            } else {
                $VU_alert_enabled = 'OFF';
            }
            $AgentAlert_allowed = $VU_allow_alerts;

            ### Gather timeclock and shift enforcement restriction settings
            $stmt = "SELECT forced_timeclock_login,shift_enforcement,group_shifts,agent_status_viewable_groups,agent_status_view_time,agent_call_log_view,agent_xfer_consultative,agent_xfer_dial_override,agent_xfer_vm_transfer,agent_xfer_blind_transfer,agent_xfer_dial_with_customer,agent_xfer_park_customer_dial,agent_fullscreen,webphone_url_override,webphone_dialpad_override,webphone_systemkey_override from vicidial_user_groups where user_group='$VU_user_group';";
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01052', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $row = mysql_fetch_row($rslt);
            $forced_timeclock_login = $row[0];
            $shift_enforcement = $row[1];
            $LOGgroup_shiftsSQL = eregi_replace('  ', '', $row[2]);
            $LOGgroup_shiftsSQL = eregi_replace(' ', "','", $LOGgroup_shiftsSQL);
            $LOGgroup_shiftsSQL = "shift_id IN('$LOGgroup_shiftsSQL')";
            $agent_status_viewable_groups = $row[3];
            $agent_status_viewable_groupsSQL = eregi_replace('  ', '', $agent_status_viewable_groups);
            $agent_status_viewable_groupsSQL = eregi_replace(' ', "','", $agent_status_viewable_groupsSQL);
            $agent_status_viewable_groupsSQL = "user_group IN('$agent_status_viewable_groupsSQL')";
            $agent_status_view = 0;
            if (strlen($agent_status_viewable_groups) > 2) {
                $agent_status_view = 1;
            }
            $agent_status_view_time = 0;
            if ($row[4] == 'Y') {
                $agent_status_view_time = 1;
            }
            if ($row[5] == 'Y') {
                $agent_call_log_view = 1;
            }
            if ($row[6] == 'Y') {
                $agent_xfer_consultative = 1;
            }
            if ($row[7] == 'Y') {
                $agent_xfer_dial_override = 1;
            }
            if ($row[8] == 'Y') {
                $agent_xfer_vm_transfer = 1;
            }
            if ($row[9] == 'Y') {
                $agent_xfer_blind_transfer = 1;
            }
            if ($row[10] == 'Y') {
                $agent_xfer_dial_with_customer = 1;
            }
            if ($row[11] == 'Y') {
                $agent_xfer_park_customer_dial = 1;
            }
            if ($VU_agent_call_log_view_override == 'Y') {
                $agent_call_log_view = 1;
            }
            if ($VU_agent_call_log_view_override == 'N') {
                $agent_call_log_view = 0;
            }
            $agent_fullscreen = $row[12];
            $webphone_url = $row[13];
            $webphone_dialpad_override = $row[14];
            $system_key = $row[15];
            if (($webphone_dialpad_override != 'DISABLED') and (strlen($webphone_dialpad_override) > 0)) {
                $webphone_dialpad = $webphone_dialpad_override;
            }

            ### BEGIN - CHECK TO SEE IF AGENT IS LOGGED IN TO TIMECLOCK, IF NOT, OUTPUT ERROR
            if ((ereg('Y', $forced_timeclock_login)) or ( (ereg('ADMIN_EXEMPT', $forced_timeclock_login)) and ($VU_user_level < 8) )) {
                $last_agent_event = '';
                $HHMM = date("Hi");
                $HHteod = substr($timeclock_end_of_day, 0, 2);
                $MMteod = substr($timeclock_end_of_day, 2, 2);

                if ($HHMM < $timeclock_end_of_day) {
                    $EoD = mktime($HHteod, $MMteod, 10, date("m"), date("d") - 1, date("Y"));
                } else {
                    $EoD = mktime($HHteod, $MMteod, 10, date("m"), date("d"), date("Y"));
                }

                $EoDdate = date("Y-m-d H:i:s", $EoD);

                ##### grab timeclock logged-in time for each user #####
                $stmt = "SELECT event from vicidial_timeclock_log where user='$VD_login' and event_epoch >= '$EoD' order by timeclock_id desc limit 1;";
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01053', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $events_to_parse = mysql_num_rows($rslt);
                if ($events_to_parse > 0) {
                    $rowx = mysql_fetch_row($rslt);
                    $last_agent_event = $rowx[0];
                }
                if ($DB > 0) {
                    echo "|$stmt|$events_to_parse|$last_agent_event|";
                }
                if ((strlen($last_agent_event) < 2) or (ereg('LOGOUT', $last_agent_event))) {
                    $VDloginDISPLAY = 1;
                    $VDdisplayMESSAGE = "YOU MUST LOG IN TO THE TIMECLOCK FIRST<br />";
                }
            }
            ### END - CHECK TO SEE IF AGENT IS LOGGED IN TO TIMECLOCK, IF NOT, OUTPUT ERROR
            ### BEGIN - CHECK TO SEE IF SHIFT ENFORCEMENT IS ENABLED AND AGENT IS OUTSIDE OF THEIR SHIFTS, IF SO, OUTPUT ERROR
            if (( (ereg("START|ALL", $shift_enforcement)) and (!ereg("OFF", $VU_agent_shift_enforcement_override)) ) or (ereg("START|ALL", $VU_agent_shift_enforcement_override))) {
                $shift_ok = 0;
                if ((strlen($LOGgroup_shiftsSQL) < 3) and ($VU_shift_override_flag < 1)) {
                    $VDloginDISPLAY = 1;
                    $VDdisplayMESSAGE = "ERROR: There are no Shifts enabled for your user group<br />";
                } else {
                    $HHMM = date("Hi");
                    $wday = date("w");

                    $stmt = "SELECT shift_id,shift_start_time,shift_length,shift_weekdays from vicidial_shifts where $LOGgroup_shiftsSQL order by shift_id";
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01056', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    $shifts_to_print = mysql_num_rows($rslt);

                    $o = 0;
                    while (($shifts_to_print > $o) and ($shift_ok < 1)) {
                        $rowx = mysql_fetch_row($rslt);
                        $shift_id = $rowx[0];
                        $shift_start_time = $rowx[1];
                        $shift_length = $rowx[2];
                        $shift_weekdays = $rowx[3];

                        if (eregi("$wday", $shift_weekdays)) {
                            $HHshift_length = substr($shift_length, 0, 2);
                            $MMshift_length = substr($shift_length, 3, 2);
                            $HHshift_start_time = substr($shift_start_time, 0, 2);
                            $MMshift_start_time = substr($shift_start_time, 2, 2);
                            $HHshift_end_time = ($HHshift_length + $HHshift_start_time);
                            $MMshift_end_time = ($MMshift_length + $MMshift_start_time);
                            if ($MMshift_end_time > 59) {
                                $MMshift_end_time = ($MMshift_end_time - 60);
                                $HHshift_end_time++;
                            }
                            if ($HHshift_end_time > 23) {
                                $HHshift_end_time = ($HHshift_end_time - 24);
                            }
                            $HHshift_end_time = sprintf("%02s", $HHshift_end_time);
                            $MMshift_end_time = sprintf("%02s", $MMshift_end_time);
                            $shift_end_time = "$HHshift_end_time$MMshift_end_time";

                            if (
                                    ( ($HHMM >= $shift_start_time) and ($HHMM < $shift_end_time) ) or
                                    ( ($HHMM < $shift_start_time) and ($HHMM < $shift_end_time) and ($shift_end_time <= $shift_start_time) ) or
                                    ( ($HHMM >= $shift_start_time) and ($HHMM >= $shift_end_time) and ($shift_end_time <= $shift_start_time) )
                            ) {
                                $shift_ok++;
                            }
                        }
                        $o++;
                    }

                    if (($shift_ok < 1) and ($VU_shift_override_flag < 1)) {
                        $VDloginDISPLAY = 1;
                        $VDdisplayMESSAGE = "ERROR: You are not allowed to log in outside of your shift<br />";
                    }
                }
                if (($shift_ok < 1) and ($VU_shift_override_flag < 1) and ($VDloginDISPLAY > 0)) {
                    $VDdisplayMESSAGE.= "<br /><br />MANAGER OVERRIDE:<br />\n";
                    $VDdisplayMESSAGE.= "<form action=\"$PHP_SELF\" method=\"post\">\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"MGR_override\" value=\"1\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"relogin\" value=\"YES\" />\n";

                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"VD_login\" value=\"$VD_login\" />\n";
                    $VDdisplayMESSAGE.= "<input type=\"hidden\" name=\"VD_pass\" value=\"$VD_pass\" />\n";
                    $VDdisplayMESSAGE.= "Manager Login: <input type=\"text\" name=\"MGR_login$loginDATE\" size=\"10\" maxlength=\"20\" /><br />\n";
                    $VDdisplayMESSAGE.= "Manager Password: <input type=\"password\" name=\"MGR_pass$loginDATE\" size=\"10\" maxlength=\"20\" /><br />\n";
                    $VDdisplayMESSAGE.= "<input type=\"submit\" name=\"submit\" value=\"Submit\" /></form>\n";
                }
            }
            ### END - CHECK TO SEE IF SHIFT ENFORCEMENT IS ENABLED AND AGENT IS OUTSIDE OF THEIR SHIFTS, IF SO, OUTPUT ERROR


            if ($WeBRooTWritablE > 0) {
                fwrite($fp, "vdweb|GOOD|$date|$VD_login|$VD_pass|$ip|$browser|$LOGfullname|\n");
                fclose($fp);
            }
            $user_abb = "$VD_login$VD_login$VD_login$VD_login";
            while ((strlen($user_abb) > 4) and ($forever_stop < 200)) {
                $user_abb = eregi_replace("^.", "", $user_abb);
                $forever_stop++;
            }

            $stmt = "SELECT allowed_campaigns from vicidial_user_groups where user_group='$VU_user_group';";
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01008', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $row = mysql_fetch_row($rslt);
            $LOGallowed_campaigns = $row[0];

            if ((!eregi("$VD_campaign", $LOGallowed_campaigns)) and (!eregi("ALL-CAMPAIGNS", $LOGallowed_campaigns))) {
                echo "<title>Go Contact: Login</title>\n";
                echo "</head>\n";
                echo "<body>\n";
                if ($hide_timeclock_link < 1) {
                    echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";
                }
                echo "<table border=1 width=\"100%\"><tr><td></td>\n";
                echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
                echo "</tr></table>\n";

                echo "<div align=center><img src=images/vicidial_logo.gif /></div>";
                echo "<table align=center border=0 cellpadding=0 cellspacing=0>\n";
                echo "      <tbody><tr>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=1 width=15></td>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=1 width=77></td>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=1 width=19></td>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=1 width=68></td>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=1 width=19></td>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=1 width=1></td>\n";
                echo "      </tr>\n";
                echo "      <tr>\n";
                echo "        <td><img name=dispo_r1_c1 src=images/dispo_r1_c1.jpg id=dispo_r1_c1 border=0 height=21 width=15></td>\n";
                echo "        <td background=images/dispo_r1_c2.jpg></td>\n";
                echo "        <td background=images/dispo_r1_c3.jpg></td>\n";
                echo "        <td background=images/dispo_r1_c4.jpg></td>\n";
                echo "        <td><img name=dispo_r1_c5 src=images/dispo_r1_c5.jpg id=dispo_r1_c5 border=0 height=21 width=19></td>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=21 width=1></td>\n";
                echo "      </tr>\n";
                echo "      <tr>\n";
                echo "        <td rowspan=3 background=images/dispo_r3_c1.jpg></td>\n";
                echo "        <td colspan=3 rowspan=3 background=images/dispo_r2_c2.jpg valign=middle align=center>\n";

                echo "<b>Pedimos desculpa mas não tem autorização para aceder a esta campanha: $VD_campaign</b>\n";
                echo "<form action=\"$PHP_SELF\" method=\"post\">\n";
                echo "<input type=\"hidden\" name=\"db\" value=\"$DB\" />\n";
                echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
                echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
                echo "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
                echo "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";
                echo "Login: <input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" />\n<br />";
                echo "Password: <input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /><br />\n";
                echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br />\n";
                echo "<input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" />  \n";
                echo "<span id=\"LogiNReseT\"></span><br>\n";
                echo "</form>\n\n";

                echo "		</td>\n";
                echo "        <td rowspan=3 background=images/dispo_r3_c5.jpg></td>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=75 width=1></td>\n";
                echo "      </tr>\n";
                echo "      <tr>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=36 width=1></td>\n";
                echo "      </tr>\n";
                echo "      <tr>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=75 width=1></td>\n";
                echo "      </tr>\n";
                echo "      <tr>\n";
                echo "        <td><img name=dispo_r5_c1 src=images/dispo_r5_c1.jpg id=dispo_r5_c1 border=0 height=25 width=15></td>\n";
                echo "        <td background=images/dispo_r5_c2.jpg></td>\n";
                echo "        <td background=images/dispo_r5_c3.jpg></td>\n";
                echo "        <td background=images/dispo_r5_c3.jpg></td>\n";
                echo "        <td><img name=dispo_r5_c5 src=images/dispo_r5_c5.jpg id=dispo_r5_c5 border=0 height=25 width=19></td>\n";
                echo "        <td><img src=images/spacer.gif border=0 height=25 width=1></td>\n";
                echo "      </tr>\n";
                echo "    </tbody></table>\n";

                echo "</body>\n\n";
                echo "</html>\n\n";
                exit;
            }

            ##### check to see that the campaign is active
            $stmt = "SELECT count(*) FROM vicidial_campaigns where campaign_id='$VD_campaign' and active='Y';";
            if ($DB) {
                echo "|$stmt|\n";
            }
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01009', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $row = mysql_fetch_row($rslt);
            $CAMPactive = $row[0];
            if ($CAMPactive > 0) {
                $campaign_status = array();
                $VARstatuses = '';
                $VARstatusnames = '';
                $VARSELstatuses = '';
                $VARSELstatuses_ct = 0;
                $VARCBstatuses = '';
                $VARCBstatusesLIST = '';
                ##### grab the statuses that can be used for dispositioning by an agent
                $stmt = "SELECT `status`, `status_name`, `selectable`, `human_answered`, `category`, `sale`, `dnc`, `customer_contact`, `not_interested`, `unworkable`, `scheduled_callback`, `completed` FROM vicidial_statuses WHERE status != 'NEW' AND selectable='Y' order by status_name limit 500;";
                $rslt = mysql_query($stmt, $link) or die(mysql_error());
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01010', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                if ($DB) {
                    echo "$stmt\n";
                }
                $VD_statuses_ct = mysql_num_rows($rslt);
                $i = 0;
                while ($i < $VD_statuses_ct) {
                    $row = mysql_fetch_assoc($rslt);
                    $campaign_status[$row["status"]] = array(
                        "status" => $row["status"],
                        "name" => $row["status_name"],
                        "callback" => ($row["scheduled_callback"] == "Y") ? true : false,
                        "sale" => ($row["sale"] == "Y") ? true : false,
                        "dnc" => ($row["dnc"] == "Y") ? true : false,
                        "not_interested" => ($row["not_interested"] == "Y") ? true : false,
                        "unworkable" => ($row["unworkable"] == "Y") ? true : false,
                        "completed" => ($row["completed"] == "Y") ? true : false);

                    $statuses[$i] = $row[0];
                    $status_names[$i] = $row[1];
                    $CBstatuses[$i] = $row[2];
                    $SELstatuses[$i] = $row[3];
                    if ($TEST_all_statuses > 0) {
                        $SELstatuses[$i] = 'Y';
                    }
                    $VARstatuses = "$VARstatuses'$statuses[$i]',";
                    $VARstatusnames = "$VARstatusnames'$status_names[$i]',";
                    $VARSELstatuses = "$VARSELstatuses'$SELstatuses[$i]',";
                    $VARCBstatuses = "$VARCBstatuses'$CBstatuses[$i]',";
                    if ($CBstatuses[$i] == 'Y') {
                        $VARCBstatusesLIST .= " $statuses[$i]";
                    }
                    if ($SELstatuses[$i] == 'Y') {
                        $VARSELstatuses_ct++;
                    }
                    $i++;
                }

                ##### grab the campaign-specific statuses that can be used for dispositioning by an agent
                $stmt = "SELECT `status`, `status_name`, `selectable`, `human_answered`, `category`, `sale`, `dnc`, `customer_contact`, `not_interested`, `unworkable`, `scheduled_callback`, `completed` FROM vicidial_campaign_statuses WHERE status != 'NEW' and campaign_id='$VD_campaign' AND selectable='Y' order by status_name limit 500;";
                $rslt = mysql_query($stmt, $link) or die(mysql_error());
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01011', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                if ($DB) {
                    echo "$stmt\n";
                }
                $VD_statuses_camp = mysql_num_rows($rslt);
                $j = 0;
                while ($j < $VD_statuses_camp) {
                    $row = mysql_fetch_assoc($rslt);
                    $campaign_status[$row["status"]] = array(
                        "status" => $row["status"],
                        "name" => $row["status_name"],
                        "callback" => ($row["scheduled_callback"] == "Y") ? true : false,
                        "sale" => ($row["sale"] == "Y") ? true : false,
                        "dnc" => ($row["dnc"] == "Y") ? true : false,
                        "not_interested" => ($row["not_interested"] == "Y") ? true : false,
                        "unworkable" => ($row["unworkable"] == "Y") ? true : false,
                        "completed" => ($row["completed"] == "Y") ? true : false);

                    $statuses[$i] = $row[0];
                    $status_names[$i] = $row[1];
                    $CBstatuses[$i] = $row[2];
                    $SELstatuses[$i] = $row[3];
                    if ($TEST_all_statuses > 0) {
                        $SELstatuses[$i] = 'Y';
                    }
                    $VARstatuses = "$VARstatuses'$statuses[$i]',";
                    $VARstatusnames = "$VARstatusnames'$status_names[$i]',";
                    $VARSELstatuses = "$VARSELstatuses'$SELstatuses[$i]',";
                    $VARCBstatuses = "$VARCBstatuses'$CBstatuses[$i]',";
                    if ($CBstatuses[$i] == 'Y') {
                        $VARCBstatusesLIST .= " $statuses[$i]";
                    }
                    if ($SELstatuses[$i] == 'Y') {
                        $VARSELstatuses_ct++;
                    }
                    $i++;
                    $j++;
                }
                $VD_statuses_ct = ($VD_statuses_ct + $VD_statuses_camp);
                $VARstatuses = substr("$VARstatuses", 0, -1);
                $VARstatusnames = substr("$VARstatusnames", 0, -1);
                $VARSELstatuses = substr("$VARSELstatuses", 0, -1);
                $VARCBstatuses = substr("$VARCBstatuses", 0, -1);
                $VARCBstatusesLIST .= " ";

                ##### grab the campaign-specific HotKey statuses that can be used for dispositioning by an agent
                $stmt = "SELECT hotkey,status,status_name FROM vicidial_campaign_hotkeys WHERE selectable='Y' and status != 'NEW' and campaign_id='$VD_campaign' order by hotkey limit 9;";
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01012', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                if ($DB) {
                    echo "$stmt\n";
                }
                $HK_statuses_camp = mysql_num_rows($rslt);
                $w = 0;
                $HKboxA = '';
                $HKboxB = '';
                $HKboxC = '';
                while ($w < $HK_statuses_camp) {
                    $row = mysql_fetch_row($rslt);
                    $HKhotkey[$w] = $row[0];
                    $HKstatus[$w] = $row[1];
                    $HKstatus_name[$w] = $row[2];
                    $HKhotkeys = "$HKhotkeys'$HKhotkey[$w]',";
                    $HKstatuses = "$HKstatuses'$HKstatus[$w]',";
                    $HKstatusnames = "$HKstatusnames'$HKstatus_name[$w]',";
                    if ($w < 3) {
                        $HKboxA = "$HKboxA <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<br />";
                    }
                    if (($w >= 3) and ($w < 6)) {
                        $HKboxB = "$HKboxB <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<br />";
                    }
                    if ($w >= 6) {
                        $HKboxC = "$HKboxC <font class=\"skb_text\">$HKhotkey[$w]</font> - $HKstatus[$w] - $HKstatus_name[$w]<br />";
                    }
                    $w++;
                }
                $HKhotkeys = substr("$HKhotkeys", 0, -1);
                $HKstatuses = substr("$HKstatuses", 0, -1);
                $HKstatusnames = substr("$HKstatusnames", 0, -1);

                ##### grab the campaign settings
                $stmt = "SELECT park_ext,park_file_name,web_form_address,allow_closers,auto_dial_level,dial_timeout,dial_prefix,campaign_cid,campaign_vdad_exten,campaign_rec_exten,campaign_recording,campaign_rec_filename,campaign_script,get_call_launch,am_message_exten,xferconf_a_dtmf,xferconf_a_number,xferconf_b_dtmf,xferconf_b_number,alt_number_dialing,scheduled_callbacks,wrapup_seconds,wrapup_message,closer_campaigns,use_internal_dnc,allcalls_delay,omit_phone_code,agent_pause_codes_active,no_hopper_leads_logins,campaign_allow_inbound,manual_dial_list_id,default_xfer_group,xfer_groups,disable_alter_custphone,display_queue_count,manual_dial_filter,agent_clipboard_copy,use_campaign_dnc,three_way_call_cid,dial_method,three_way_dial_prefix,web_form_target,vtiger_screen_login,agent_allow_group_alias,default_group_alias,quick_transfer_button,prepopulate_transfer_preset,view_calls_in_queue,view_calls_in_queue_launch,call_requeue_button,pause_after_each_call,no_hopper_dialing,agent_dial_owner_only,agent_display_dialable_leads,web_form_address_two,agent_select_territories,crm_popup_login,crm_login_address,timer_action,timer_action_message,timer_action_seconds,start_call_url,dispo_call_url,xferconf_c_number,xferconf_d_number,xferconf_e_number,use_custom_cid,scheduled_callbacks_alert,scheduled_callbacks_count,manual_dial_override,blind_monitor_warning,blind_monitor_message,blind_monitor_filename,timer_action_destination,enable_xfer_presets,hide_xfer_number_to_dial,manual_dial_prefix,customer_3way_hangup_logging,customer_3way_hangup_seconds,customer_3way_hangup_action,ivr_park_call,manual_preview_dial,api_manual_dial,manual_dial_call_time_check,my_callback_option,per_call_notes,agent_lead_search,agent_lead_search_method,queuemetrics_phone_environment,auto_pause_precall,auto_pause_precall_code,auto_resume_precall,manual_dial_cid,campaign_name,callback_other_user,agent_allow_transfers,agent_allow_dtmf,callback_hours_block FROM vicidial_campaigns where campaign_id = '$VD_campaign';";
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01013', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                if ($DB) {
                    echo "$stmt\n";
                }
                $row = mysql_fetch_row($rslt);
                $park_ext = $row[0];
                $park_file_name = $row[1];
                $web_form_address = stripslashes($row[2]);
                $allow_closers = $row[3];
                $auto_dial_level = $row[4];
                $dial_timeout = $row[5];
                $dial_prefix = $row[6];
                $campaign_cid = $row[7];
                $campaign_vdad_exten = $row[8];
                $campaign_rec_exten = $row[9];
                $campaign_recording = $row[10];
                $campaign_rec_filename = $row[11];
                $campaign_script = $row[12];
                $get_call_launch = $row[13];
                $campaign_am_message_exten = '8320';
                $xferconf_a_dtmf = $row[15];
                $xferconf_a_number = $row[16];
                $xferconf_b_dtmf = $row[17];
                $xferconf_b_number = $row[18];
                $alt_number_dialing = $row[19];
                $VC_scheduled_callbacks = $row[20];
                $wrapup_seconds = $row[21];
                $wrapup_message = $row[22];
                $closer_campaigns = $row[23];
                $use_internal_dnc = $row[24];
                $allcalls_delay = $row[25];
                $omit_phone_code = $row[26];
                $agent_pause_codes_active = $row[27];
                $no_hopper_leads_logins = $row[28];
                $campaign_allow_inbound = $row[29];
                $manual_dial_list_id = $row[30];
                $default_xfer_group = $row[31];
                $xfer_groups = $row[32];
                $disable_alter_custphone = $row[33];
                $display_queue_count = $row[34];
                $manual_dial_filter = $row[35];
                $CopY_tO_ClipboarD = $row[36];
                $use_campaign_dnc = $row[37];
                $three_way_call_cid = $row[38];
                $dial_method = $row[39];
                $three_way_dial_prefix = $row[40];
                $web_form_target = $row[41];
                $vtiger_screen_login = $row[42];
                $agent_allow_group_alias = $row[43];
                $default_group_alias = $row[44];
                $quick_transfer_button = $row[45];
                $prepopulate_transfer_preset = $row[46];
                $view_calls_in_queue = $row[47];
                $view_calls_in_queue_launch = $row[48];
                $call_requeue_button = $row[49];
                $pause_after_each_call = $row[50];
                $no_hopper_dialing = $row[51];
                $agent_dial_owner_only = $row[52];
                $agent_display_dialable_leads = $row[53];
                $web_form_address_two = $row[54];
                $agent_select_territories = $row[55];
                $crm_popup_login = $row[56];
                $crm_login_address = $row[57];
                $timer_action = $row[58];
                $timer_action_message = $row[59];
                $timer_action_seconds = $row[60];
                $start_call_url = $row[61];
                $dispo_call_url = $row[62];
                $xferconf_c_number = $row[63];
                $xferconf_d_number = $row[64];
                $xferconf_e_number = $row[65];
                $use_custom_cid = $row[66];
                $scheduled_callbacks_alert = $row[67];
                $scheduled_callbacks_count = $row[68];
                $manual_dial_override = $row[69];
                $blind_monitor_warning = $row[70];
                $blind_monitor_message = $row[71];
                $blind_monitor_filename = $row[72];
                $timer_action_destination = $row[73];
                $enable_xfer_presets = $row[74];
                $hide_xfer_number_to_dial = $row[75];
                $manual_dial_prefix = $row[76];
                $customer_3way_hangup_logging = $row[77];
                $customer_3way_hangup_seconds = $row[78];
                $customer_3way_hangup_action = $row[79];
                $ivr_park_call = $row[80];
                $manual_preview_dial = $row[81];
                $api_manual_dial = $row[82];
                $manual_dial_call_time_check = $row[83];
                $my_callback_option = $row[84];
                $per_call_notes = $row[85];
                $agent_lead_search = $row[86];
                $agent_lead_search_method = $row[87];
                $qm_phone_environment = $row[88];
                $auto_pause_precall = $row[89];
                $auto_pause_precall_code = $row[90];
                $auto_resume_precall = $row[91];
                $manual_dial_cid = $row[92];
                $campaign_name = $row[93];
                $callback_other_user = $row[94];
                $agent_allow_transfers = $row[95];
                $agent_allow_dtmf = $row[96];
                $callback_hours_block = $row[97];
                if (($VU_agent_lead_search_override == 'ENABLED') or ($VU_agent_lead_search_override == 'DISABLED')) {
                    $agent_lead_search = $VU_agent_lead_search_override;
                }
                $AllowManualQueueCalls = 1;
                $AllowManualQueueCallsChoice = 0;
                if ($api_manual_dial == 'QUEUE') {
                    $AllowManualQueueCalls = 0;
                    $AllowManualQueueCallsChoice = 1;
                }
                if ($manual_preview_dial == 'DISABLED') {
                    $manual_dial_preview = 0;
                }
                if ($manual_dial_override == 'ALLOW_ALL') {
                    $agentcall_manual = 1;
                }
                if ($manual_dial_override == 'DISABLE_ALL') {
                    $agentcall_manual = 0;
                }
                if ($user_territories_active < 1) {
                    $agent_select_territories = 0;
                }
                if (preg_match("/Y/", $agent_select_territories)) {
                    $agent_select_territories = 1;
                } else {
                    $agent_select_territories = 0;
                }

                if (preg_match("/Y/", $agent_display_dialable_leads)) {
                    $agent_display_dialable_leads = 1;
                } else {
                    $agent_display_dialable_leads = 0;
                }

                if (preg_match("/Y/", $no_hopper_dialing)) {
                    $no_hopper_dialing = 1;
                } else {
                    $no_hopper_dialing = 0;
                }

                if ((preg_match("/Y/", $call_requeue_button)) and ($auto_dial_level > 0)) {
                    $call_requeue_button = 1;
                } else {
                    $call_requeue_button = 0;
                }

                if ((preg_match("/AUTO/", $view_calls_in_queue_launch)) and ($auto_dial_level > 0)) {
                    $view_calls_in_queue_launch = 1;
                } else {
                    $view_calls_in_queue_launch = 0;
                }

                if ((!preg_match("/NONE/", $view_calls_in_queue)) and ($auto_dial_level > 0)) {
                    $view_calls_in_queue = 1;
                } else {
                    $view_calls_in_queue = 0;
                }

                if (preg_match("/Y/", $pause_after_each_call)) {
                    $dispo_check_all_pause = 1;
                }

                $quick_transfer_button_enabled = 0;
                $quick_transfer_button_locked = 0;
                if (preg_match("/IN_GROUP|PRESET_1|PRESET_2|PRESET_3|PRESET_4|PRESET_5/", $quick_transfer_button)) {
                    $quick_transfer_button_enabled = 1;
                }
                if (preg_match("/LOCKED/", $quick_transfer_button)) {
                    $quick_transfer_button_locked = 1;
                }

                $preset_populate = '';
                $prepopulate_transfer_preset_enabled = 0;
                if (preg_match("/PRESET_1|PRESET_2|PRESET_3|PRESET_4|PRESET_5/", $prepopulate_transfer_preset)) {
                    $prepopulate_transfer_preset_enabled = 1;
                    if (preg_match("/PRESET_1/", $prepopulate_transfer_preset)) {
                        $preset_populate = $xferconf_a_number;
                    }
                    if (preg_match("/PRESET_2/", $prepopulate_transfer_preset)) {
                        $preset_populate = $xferconf_b_number;
                    }
                    if (preg_match("/PRESET_3/", $prepopulate_transfer_preset)) {
                        $preset_populate = $xferconf_c_number;
                    }
                    if (preg_match("/PRESET_4/", $prepopulate_transfer_preset)) {
                        $preset_populate = $xferconf_d_number;
                    }
                    if (preg_match("/PRESET_5/", $prepopulate_transfer_preset)) {
                        $preset_populate = $xferconf_e_number;
                    }
                }

                $VARpreset_names = '';
                $VARpreset_numbers = '';
                $VARpreset_dtmfs = '';
                $VARpreset_hide_numbers = '';
                if ($enable_xfer_presets == 'ENABLED') {
                    ##### grab the presets for this campaign
                    $stmt = "SELECT preset_name,preset_number,preset_dtmf,preset_hide_number FROM vicidial_xfer_presets WHERE campaign_id='$VD_campaign' order by preset_name limit 500;";
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01067', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    if ($DB) {
                        echo "$stmt\n";
                    }
                    $VD_presets = mysql_num_rows($rslt);
                    $j = 0;
                    while ($j < $VD_presets) {
                        $row = mysql_fetch_row($rslt);
                        $preset_names[$j] = $row[0];
                        $preset_numbers[$j] = $row[1];
                        $preset_dtmfs[$j] = $row[2];
                        $preset_hide_numbers[$j] = $row[3];
                        $VARpreset_names = "$VARpreset_names'$preset_names[$j]',";
                        $VARpreset_numbers = "$VARpreset_numbers'$preset_numbers[$j]',";
                        $VARpreset_dtmfs = "$VARpreset_dtmfs'$preset_dtmfs[$j]',";
                        $VARpreset_hide_numbers = "$VARpreset_hide_numbers'$preset_hide_numbers[$j]',";
                        $j++;
                    }
                    $VARpreset_names = substr("$VARpreset_names", 0, -1);
                    $VARpreset_numbers = substr("$VARpreset_numbers", 0, -1);
                    $VARpreset_dtmfs = substr("$VARpreset_dtmfs", 0, -1);
                    $VARpreset_hide_numbers = substr("$VARpreset_hide_numbers", 0, -1);
                    $VD_preset_names_ct = $j;
                    if ($j < 1) {
                        $enable_xfer_presets = 'DISABLED';
                    }
                }

                $default_group_alias_cid = '';
                if (strlen($default_group_alias) > 1) {
                    $stmt = "select caller_id_number from groups_alias where group_alias_id='$default_group_alias';";
                    if ($DB) {
                        echo "$stmt\n";
                    }
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01055', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    $VDIG_cidnum_ct = mysql_num_rows($rslt);
                    if ($VDIG_cidnum_ct > 0) {
                        $row = mysql_fetch_row($rslt);
                        $default_group_alias_cid = $row[0];
                    }
                }

                $stmt = "select group_web_vars from vicidial_campaign_agents where campaign_id='$VD_campaign' and user='$VD_login';";
                if ($DB) {
                    echo "$stmt\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01056', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $VDIG_cidogwv = mysql_num_rows($rslt);
                if ($VDIG_cidogwv > 0) {
                    $row = mysql_fetch_row($rslt);
                    $default_web_vars = $row[0];
                }

                if ((!ereg('DISABLED', $VU_vicidial_recording_override)) and ($VU_vicidial_recording > 0)) {
                    $campaign_recording = $VU_vicidial_recording_override;
                    echo "<!-- USER RECORDING OVERRIDE: |$VU_vicidial_recording_override|$campaign_recording| -->\n";
                }
                if (($VC_scheduled_callbacks == 'Y') and ($VU_scheduled_callbacks == '1')) {
                    $scheduled_callbacks = '1';
                }
                if ($VU_vicidial_recording == '0') {
                    $campaign_recording = 'NEVER';
                }
                if ($VU_alter_custphone_override == 'ALLOW_ALTER') {
                    $disable_alter_custphone = 'N';
                }
                if (strlen($manual_dial_prefix) < 1) {
                    $manual_dial_prefix = $dial_prefix;
                }
                if (strlen($three_way_dial_prefix) < 1) {
                    $three_way_dial_prefix = $dial_prefix;
                }
                if ($alt_number_dialing == 'Y') {
                    $alt_phone_dialing = '1';
                } else {
                    $alt_phone_dialing = '0';
                    $DefaulTAlTDiaL = '0';
                }
                if ($display_queue_count == 'N') {
                    $callholdstatus = '0';
                }
                if (($dial_method == 'INBOUND_MAN') or ($outbound_autodial_active < 1)) {
                    $VU_closer_default_blended = 0;
                }

                $closer_campaigns = preg_replace("/^ | -$/", "", $closer_campaigns);
                $closer_campaigns = preg_replace("/ /", "','", $closer_campaigns);
                $closer_campaigns = "'$closer_campaigns'";

                if ((ereg('Y', $agent_pause_codes_active)) or (ereg('FORCE', $agent_pause_codes_active))) {
                    ##### grab the pause codes for this campaign
                    $stmt = "SELECT pause_code,pause_code_name FROM vicidial_pause_codes WHERE campaign_id='$VD_campaign' and active='1' order by pause_code limit 100;";
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01014', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    if ($DB) {
                        echo "$stmt\n";
                    }
                    $VD_pause_codes = mysql_num_rows($rslt);
                    $j = 0;
                    while ($j < $VD_pause_codes) {
                        $row = mysql_fetch_row($rslt);
                        $pause_codes[$i] = $row[0];
                        $pause_code_names[$i] = $row[1];
                        $VARpause_codes = "$VARpause_codes'$pause_codes[$i]',";
                        $VARpause_code_names = "$VARpause_code_names'$pause_code_names[$i]',";
                        $i++;
                        $j++;
                    }
                    $VD_pause_codes_ct = ($VD_pause_codes_ct + $VD_pause_codes);
                    $VARpause_codes = substr("$VARpause_codes", 0, -1);
                    $VARpause_code_names = substr("$VARpause_code_names", 0, -1);
                }

                ##### grab the inbound groups to choose from if campaign contains CLOSER
                $VARingroups = "''";
                if (($campaign_allow_inbound == 'Y') and ($dial_method != 'MANUAL')) {
                    $VARingroups = '';
                    $stmt = "select group_id from vicidial_inbound_groups where active = 'Y' and group_id IN($closer_campaigns) order by group_id limit 800;";
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01015', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    if ($DB) {
                        echo "$stmt\n";
                    }
                    $closer_ct = mysql_num_rows($rslt);
                    $INgrpCT = 0;
                    while ($INgrpCT < $closer_ct) {
                        $row = mysql_fetch_row($rslt);
                        $closer_groups[$INgrpCT] = $row[0];
                        $VARingroups = "$VARingroups'$closer_groups[$INgrpCT]',";
                        $INgrpCT++;
                    }
                    $VARingroups = substr("$VARingroups", 0, -1);
                } else {
                    $closer_campaigns = "''";
                }

                ##### gather territory listings for this agent if select territories is enabled
                $VARterritories = '';
                if ($agent_select_territories > 0) {
                    $stmt = "SELECT territory from vicidial_user_territories where user='$VD_login';";
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01062', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    if ($DB) {
                        echo "$stmt\n";
                    }
                    $territory_ct = mysql_num_rows($rslt);
                    $territoryCT = 0;
                    while ($territoryCT < $territory_ct) {
                        $row = mysql_fetch_row($rslt);
                        $territories[$territoryCT] = $row[0];
                        $VARterritories = "$VARterritories'$territories[$territoryCT]',";
                        $territoryCT++;
                    }
                    $VARterritories = substr("$VARterritories", 0, -1);
                    echo "<!-- $territory_ct  $territoryCT |$stmt| -->\n";
                }

                ##### grab the allowable inbound groups to choose from for transfer options
                $xfer_groups = preg_replace("/^ | -$/", "", $xfer_groups);
                $xfer_groups = preg_replace("/ /", "','", $xfer_groups);
                $xfer_groups = "'$xfer_groups'";
                $VARxfergroups = "''";
                if ($allow_closers == 'Y') {
                    $VARxfergroups = '';
                    $stmt = "select group_id,group_name from vicidial_inbound_groups where active = 'Y' and group_id IN($xfer_groups) order by group_id limit 800;";
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01016', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    if ($DB) {
                        echo "$stmt\n";
                    }
                    $xfer_ct = mysql_num_rows($rslt);
                    $XFgrpCT = 0;
                    while ($XFgrpCT < $xfer_ct) {
                        $row = mysql_fetch_row($rslt);
                        $VARxfergroups = "$VARxfergroups'$row[0]',";
                        $VARxfergroupsnames = "$VARxfergroupsnames'$row[1]',";
                        if ($row[0] == "$default_xfer_group") {
                            $default_xfer_group_name = $row[1];
                        }
                        $XFgrpCT++;
                    }
                    $VARxfergroups = substr("$VARxfergroups", 0, -1);
                    $VARxfergroupsnames = substr("$VARxfergroupsnames", 0, -1);
                }

                if (ereg('Y', $agent_allow_group_alias)) {
                    ##### grab the active group aliases
                    $stmt = "SELECT group_alias_id,group_alias_name,caller_id_number FROM groups_alias WHERE active='Y' order by group_alias_id limit 1000;";
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01054', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    if ($DB) {
                        echo "$stmt\n";
                    }
                    $VD_group_aliases = mysql_num_rows($rslt);
                    $j = 0;
                    while ($j < $VD_group_aliases) {
                        $row = mysql_fetch_row($rslt);
                        $group_alias_id[$i] = $row[0];
                        $group_alias_name[$i] = $row[1];
                        $caller_id_number[$i] = $row[2];
                        $VARgroup_alias_ids = "$VARgroup_alias_ids'$group_alias_id[$i]',";
                        $VARgroup_alias_names = "$VARgroup_alias_names'$group_alias_name[$i]',";
                        $VARcaller_id_numbers = "$VARcaller_id_numbers'$caller_id_number[$i]',";
                        $i++;
                        $j++;
                    }
                    $VD_group_aliases_ct = ($VD_group_aliases_ct + $VD_group_aliases);
                    $VARgroup_alias_ids = substr("$VARgroup_alias_ids", 0, -1);
                    $VARgroup_alias_names = substr("$VARgroup_alias_names", 0, -1);
                    $VARcaller_id_numbers = substr("$VARcaller_id_numbers", 0, -1);
                }

                ##### grab the number of leads in the hopper for this campaign
                $stmt = "SELECT count(*) FROM vicidial_hopper where campaign_id = '$VD_campaign' and status='READY';";
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01017', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                if ($DB) {
                    echo "$stmt\n";
                }
                $row = mysql_fetch_row($rslt);
                $campaign_leads_to_call = $row[0];
                echo "<!-- $campaign_leads_to_call - leads left to call in hopper -->\n";
            } else {
                $VDloginDISPLAY = 1;
                $VDdisplayMESSAGE = "Esta campanha não está activa. Tente novamente<br />";
            }
        } else {
            if ($WeBRooTWritablE > 0) {
                fwrite($fp, "vdweb|FAIL|$date|$VD_login|$VD_pass|$ip|$browser|\n");
                fclose($fp);
            }
            $VDloginDISPLAY = 1;
            $VDdisplayMESSAGE = "Login incorrecto. Tente novamente<br />";
        }
    }
#####################################################################
#2	Segundo Menu de Log in (Campanhas)                          #
#####################################################################		
    if ($VDloginDISPLAY && ($no_campaign == 1)) {





        if ($val == 1 or 1) {

            $phone_pass = $result_phone_pass[0];
            ?>
            <title>Go Contact: Login de Operador</title>
            </head>
            <body onload=login_allowable_campaigns();>
                <div style="width: 525px;margin: auto;">
                    <img style='margin-top:10%;' src=../images/pictures/go_logo_35.png />
                    <div class="grid" >

                        <div class="grid-content">
                            <form name="vicidial_form" id="vicidial_form" action="<?= $agcPAGE ?>" class="form-horizontal" method="post">
                                <input type="hidden" name="curlogo" value="<?= $curLogo ?>" />
                                <input type="hidden" name="DB" value="<?= $DB ?>" />
                                <input type="hidden" name="JS_browser_height" id="JS_browser_height" value="" />
                                <input type="hidden" name="JS_browser_width" id="JS_browser_width" value="" />

                                <input type="hidden" name="phone_login" value="<?= $phone_login ?>" />
                                <input type="hidden" name="phone_pass" value="<?= $phone_pass ?>" />
                                <input type="hidden" name="VD_login" value="<?= $sips_login ?>" />
                                <input type="hidden" name="VD_pass" value="<?= $sips_pass ?>" />
                                <div class="control-group">
                                    <label class="control-label">Campanha: </label>
                                    <div class="controls" id="LogiNCamPaigns">
                                        <?= $camp_form_code ?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <div class="controls">
                                        <button class="btn btn-primary"><i class="icon-signin"></i> Log-In</button>
                                    </div>
                                </div>
                                <div class="clear"></div>
                            </form>
                        </div>
                    </div>
                    <?php
                    $curLogo = $_POST['curlogo'];
                    if (isset($curLogo) && $curLogo != "") {
                        ?>
                        <br><br><img src=../<?= $curLogo ?> />
                    <?php } ?>

                </div>
            </body>
            </html>
            <?php
            exit;
        } else {

            echo "<center><img style='margin-top:10%;' src=../images/pictures/go_logo_35.png />";
            echo "<div id=work-area style='width:40%; min-height:0px; min-width:500px'>";

            echo "<div class=cc-mstyle style='border:none; margin-top:32px;'>";
            echo "<form name=sips_login id=sips_login action=index.php method=POST>";
            echo "<input type=hidden value=go name=reset_login>";
            echo "<table width=100% border=0>";
            echo "<tr><td style='min-width:150px'> Log In Errado </td>";
            echo "<tr><td></td><tr><td></td>";
            echo "<tr><td>Tentar Novamente</td><td><a href='../index.php'><img src='/images/icons/key_go_32.png'></a></td>";
            echo "</table><br><br></div></div>";
            exit;
        }

        echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
        echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
        echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";

        echo "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
        echo "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";

        if (strlen($VDdisplayMESSAGE) > 0) {
            echo "<center><br />$VDdisplayMESSAGE<br /></center>";
        }
        echo "<table border=0 width=\"400px\" >";

        echo "<tr><td align=\"right\">Utilizador:  </td>";

        echo "<tr><td align=\"center\" colspan=\"2\"><input type=\"submit\" name=\"SUBMIT\" value=\"Login\" />  \n";

        echo "<tr><td align=\"center\" colspan=\"2\"><font size=\"1\"><br /></font></td></tr>\n";
        echo "</table><br>\n";
        echo "</form>\n\n";

        echo "		</td>\n";
        echo "        <td rowspan=3 ></td>\n";
        echo "        <td></td>\n";
        echo "      </tr>\n";
        echo "      <tr>\n";
        echo "        <td></td>\n";
        echo "      </tr>\n";
        echo "      <tr>\n";
        echo "        <td></td>\n";
        echo "      </tr>\n";
        echo "      <tr>\n";
        echo "        <td></td>\n";
        echo "        <td ></td>\n";
        echo "        <td ></td>\n";
        echo "        <td ></td>\n";
        echo "        <td></td>\n";
        echo "        <td><img src=images/spacer.gif border=0 height=25 width=1></td>\n";
        echo "      </tr>\n";
        echo "    </tbody></table>\n";

        echo "</body>\n\n";
        echo "</html>\n\n";
        exit;
    }

#####################################################################
#                                                                   #
#####################################################################

    $original_phone_login = $phone_login;

    # code for parsing load-balanced agent phone allocation where agent interface
    # will send multiple phones-table logins so that the script can determine the
    # server that has the fewest agents logged into it.
    #   login: ca101,cb101,cc101
    $alias_found = 0;
    $stmt = "select count(*) from phones_alias where alias_id = '$phone_login';";
    $rslt = mysql_query($stmt, $link);
    if ($mel > 0) {
        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01018', $VD_login, $server_ip, $session_name, $one_mysql_log);
    }
    $alias_ct = mysql_num_rows($rslt);
    if ($alias_ct > 0) {
        $row = mysql_fetch_row($rslt);
        $alias_found = "$row[0]";
    }
    if ($alias_found > 0) {
        $stmt = "select alias_name,logins_list from phones_alias where alias_id = '$phone_login' limit 1;";
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01019', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }
        $alias_ct = mysql_num_rows($rslt);
        if ($alias_ct > 0) {
            $row = mysql_fetch_row($rslt);
            $alias_name = "$row[0]";
            $phone_login = "$row[1]";
        }
    }

    $pa = 0;
    if ((eregi(',', $phone_login)) and (strlen($phone_login) > 2)) {
        $phoneSQL = "(";
        $phones_auto = explode(',', $phone_login);
        $phones_auto_ct = count($phones_auto);
        while ($pa < $phones_auto_ct) {
            if ($pa > 0) {
                $phoneSQL .= " or ";
            }
            $desc = ($phones_auto_ct - $pa); # traverse in reverse order
            $phoneSQL .= "(login='$phones_auto[$desc]' and pass='$phone_pass')";
            $pa++;
        }
        $phoneSQL .= ")";
    } else {
        $phoneSQL = "login='$phone_login' and pass='$phone_pass'";
    }

    $authphone = 0;
    #$stmt="SELECT count(*) from phones where $phoneSQL and active = 'Y';";
    $stmt = "SELECT count(*) from phones,servers where $phoneSQL and phones.active = 'Y' and active_agent_login_server='Y' and phones.server_ip=servers.server_ip;";
    if ($DB) {
        echo "|$stmt|\n";
    }
    $rslt = mysql_query($stmt, $link);
    if ($mel > 0) {
        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01020', $VD_login, $server_ip, $session_name, $one_mysql_log);
    }
    $row = mysql_fetch_row($rslt);
    $authphone = $row[0];
    if (!$authphone) {
        ?>
        <title>Go Contact: Erro no Login de Licença</title>
        </head>
        <body id='ib'>
            <div style="width: 525px;margin: auto;">
                <img style='margin-top:10%;' src=../images/pictures/go_logo_35.png />
                <div class="grid" >

                    <div class="grid-content">
                        <form name="vicidial_form" id="vicidial_form" class="form-horizontal" action="<?= $agcPAGE ?>" method="post">
                            <input type="hidden" name="curlogo" value="<?= $curLogo ?>" />
                            <input type="hidden" name="DB" value="<?= $DB ?>">
                            <input type="hidden" name="VD_login" value="<?= $VD_login ?>" />
                            <input type="hidden" name="VD_pass" value="<?= $VD_pass ?>" />
                            <input type="hidden" name="VD_campaign" value="<?= $VD_campaign ?>" />

                            <div class="alert">Os dados de Login que inseriu não estão correctos. Tente novamente.</div>

                            <div class="control-group">
                                <label class="control-label">Licença: </label>
                                <div class="controls" id="LogiNCamPaigns">
                                    <input type="text" name="phone_login" size="10" maxlength="20" value="<?= $phone_login ?>">
                                    <?php $phone_pass = $result_phone_pass[0]; ?>
                                    <input type="hidden"  name="phone_pass" value="<?= $phone_pass ?>">
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <button class="btn btn-primary"><i class="icon-signin"></i> Log-In</button>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </form>
                    </div>
                </div>
                <?php
                $curLogo = $_POST['curlogo'];
                if (isset($curLogo) && $curLogo != "") {
                    ?>
                    <br><br><img src=../<?= $curLogo ?> />
                <?php } ?>

            </div>

        </body>
        </html>
        <?php
        exit;
    } else {
        ### go through the entered phones to figure out which server has fewest agents
        ### logged in and use that phone login account
        if ($pa > 0) {
            $pb = 0;
            $pb_login = '';
            $pb_server_ip = '';
            $pb_count = 0;
            $pb_log = '';
            while ($pb < $phones_auto_ct) {
                ### find the server_ip of each phone_login
                $stmtx = "SELECT server_ip from phones where login = '$phones_auto[$pb]';";
                if ($DB) {
                    echo "|$stmtx|\n";
                }
                if ($non_latin > 0) {
                    $rslt = mysql_query("SET NAMES 'UTF8'");
                }
                $rslt = mysql_query($stmtx, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01021', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $rowx = mysql_fetch_row($rslt);

                ### get number of agents logged in to each server
                $stmt = "SELECT count(*) from vicidial_live_agents where server_ip = '$rowx[0]';";
                if ($DB) {
                    echo "|$stmt|\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01022', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $row = mysql_fetch_row($rslt);

                ### find out whether the server is set to active
                $stmt = "SELECT count(*) from servers where server_ip = '$rowx[0]' and active='Y' and active_agent_login_server='Y';";
                if ($DB) {
                    echo "|$stmt|\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01023', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $rowy = mysql_fetch_row($rslt);

                ### find out if this server has a twin
                $twin_not_live = 0;
                $stmt = "SELECT active_twin_server_ip from servers where server_ip = '$rowx[0]';";
                if ($DB) {
                    echo "|$stmt|\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01070', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $rowyy = mysql_fetch_row($rslt);
                if (strlen($rowyy[0]) > 4) {
                    ### find out whether the twin server_updater is running
                    $stmt = "SELECT count(*) from server_updater where server_ip = '$rowyy[0]' and last_update > '$past_minutes_date';";
                    if ($DB) {
                        echo "|$stmt|\n";
                    }
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01071', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    $rowyz = mysql_fetch_row($rslt);
                    if ($rowyz[0] < 1) {
                        $twin_not_live = 1;
                    }
                }

                ### find out whether the server_updater is running
                $stmt = "SELECT count(*) from server_updater where server_ip = '$rowx[0]' and last_update > '$past_minutes_date';";
                if ($DB) {
                    echo "|$stmt|\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01024', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $rowz = mysql_fetch_row($rslt);

                $pb_log .= "$phones_auto[$pb]|$rowx[0]|$row[0]|$rowy[0]|$rowz[0]|$twin_not_live|   ";

                if (($rowy[0] > 0) and ($rowz[0] > 0) and ($twin_not_live < 1)) {
                    if (($pb_count >= $row[0]) or (strlen($pb_server_ip) < 4)) {
                        $pb_count = $row[0];
                        $pb_server_ip = $rowx[0];
                        $phone_login = $phones_auto[$pb];
                    }
                }
                $pb++;
            }
            echo "<!-- Phones balance selection: $phone_login|$pb_server_ip|$past_minutes_date|     |$pb_log -->\n";
        }
        echo "<title>Go Contact</title>\n";
        $stmt = "SELECT extension,dialplan_number,voicemail_id,phone_ip,computer_ip,server_ip,login,pass,status,active,phone_type,fullname,company,picture,messages,old_messages,protocol,local_gmt,ASTmgrUSERNAME,ASTmgrSECRET,login_user,login_pass,login_campaign,park_on_extension,conf_on_extension,VICIDIAL_park_on_extension,VICIDIAL_park_on_filename,monitor_prefix,recording_exten,voicemail_exten,voicemail_dump_exten,ext_context,dtmf_send_extension,call_out_number_group,client_browser,install_directory,local_web_callerID_URL,VICIDIAL_web_URL,AGI_call_logging_enabled,user_switching_enabled,conferencing_enabled,admin_hangup_enabled,admin_hijack_enabled,admin_monitor_enabled,call_parking_enabled,updater_check_enabled,AFLogging_enabled,QUEUE_ACTION_enabled,CallerID_popup_enabled,voicemail_button_enabled,enable_fast_refresh,fast_refresh_rate,enable_persistant_mysql,auto_dial_next_number,VDstop_rec_after_each_call,DBX_server,DBX_database,DBX_user,DBX_pass,DBX_port,DBY_server,DBY_database,DBY_user,DBY_pass,DBY_port,outbound_cid,enable_sipsak_messages,email,template_id,conf_override,phone_context,phone_ring_timeout,conf_secret,is_webphone,use_external_server_ip,codecs_list,webphone_dialpad,phone_ring_timeout,on_hook_agent from phones where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
        if ($DB) {
            echo "|$stmt|\n";
        }
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01025', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }
        $row = mysql_fetch_row($rslt);
        $extension = $row[0];
        $dialplan_number = $row[1];
        $voicemail_id = $row[2];
        $phone_ip = $row[3];
        $computer_ip = $row[4];
        $server_ip = $row[5];
        $login = $row[6];
        $pass = $row[7];
        $status = $row[8];
        $active = $row[9];
        $phone_type = $row[10];
        $fullname = $row[11];
        $company = $row[12];
        $picture = $row[13];
        $messages = $row[14];
        $old_messages = $row[15];
        $protocol = $row[16];
        $local_gmt = $row[17];
        $ASTmgrUSERNAME = $row[18];
        $ASTmgrSECRET = $row[19];
        $login_user = $row[20];
        $login_pass = $row[21];
        $login_campaign = $row[22];
        $park_on_extension = $row[23];
        $conf_on_extension = $row[24];
        $VICIDiaL_park_on_extension = $row[25];
        $VICIDiaL_park_on_filename = $row[26];
        $monitor_prefix = $row[27];
        $recording_exten = $row[28];
        $voicemail_exten = $row[29];
        $voicemail_dump_exten = $row[30];
        $ext_context = $row[31];
        $dtmf_send_extension = $row[32];
        $call_out_number_group = $row[33];
        $client_browser = $row[34];
        $install_directory = $row[35];
        $local_web_callerID_URL = $row[36];
        $VICIDiaL_web_URL = $row[37];
        $AGI_call_logging_enabled = $row[38];
        $user_switching_enabled = $row[39];
        $conferencing_enabled = $row[40];
        $admin_hangup_enabled = $row[41];
        $admin_hijack_enabled = $row[42];
        $admin_monitor_enabled = $row[43];
        $call_parking_enabled = $row[44];
        $updater_check_enabled = $row[45];
        $AFLogging_enabled = $row[46];
        $QUEUE_ACTION_enabled = $row[47];
        $CallerID_popup_enabled = $row[48];
        $voicemail_button_enabled = $row[49];
        $enable_fast_refresh = $row[50];
        $fast_refresh_rate = $row[51];
        $enable_persistant_mysql = $row[52];
        $auto_dial_next_number = $row[53];
        $VDstop_rec_after_each_call = $row[54];
        $DBX_server = $row[55];
        $DBX_database = $row[56];
        $DBX_user = $row[57];
        $DBX_pass = $row[58];
        $DBX_port = $row[59];
        $outbound_cid = $row[65];
        $enable_sipsak_messages = $row[66];
        $conf_secret = $row[72];
        $is_webphone = $row[73];
        $use_external_server_ip = $row[74];
        $codecs_list = $row[75];
        $webphone_dialpad = $row[76];
        $phone_ring_timeout = $row[77];
        $on_hook_agent = $row[78];

        $no_empty_session_warnings = 0;
        if (($phone_login == 'nophone') or ($on_hook_agent == 'Y')) {
            $no_empty_session_warnings = 1;
        }
        if ($PhonESComPIP == '1') {
            if (strlen($computer_ip) < 4) {
                $stmt = "UPDATE phones SET computer_ip='$ip' where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
                if ($DB) {
                    echo "|$stmt|\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01026', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
            }
        }
        if ($PhonESComPIP == '2') {
            $stmt = "UPDATE phones SET computer_ip='$ip' where login='$phone_login' and pass='$phone_pass' and active = 'Y';";
            if ($DB) {
                echo "|$stmt|\n";
            }
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01027', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
        }
        if ($clientDST) {
            $local_gmt = ($local_gmt + $isdst);
        }
        if ($protocol == 'EXTERNAL') {
            $protocol = 'Local';
            $extension = "$dialplan_number$AT$ext_context";
        }
        $SIP_user = "$protocol/$extension";
        $SIP_user_DiaL = "$protocol/$extension";
        if ((ereg('8300', $dialplan_number)) and (strlen($dialplan_number) < 5) and ($protocol == 'Local')) {
            $SIP_user = "$protocol/$extension$VD_login";
        }

        $stmt = "SELECT asterisk_version from servers where server_ip='$server_ip';";
        if ($DB) {
            echo "|$stmt|\n";
        }
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01028', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }
        $row = mysql_fetch_row($rslt);
        $asterisk_version = $row[0];

        # If a park extension is not set, use the default one
        if ((strlen($park_ext) > 0) && (strlen($park_file_name) > 0)) {
            $VICIDiaL_park_on_extension = "$park_ext";
            $VICIDiaL_park_on_filename = "$park_file_name";
            echo "<!-- CAMPAIGN CUSTOM PARKING:  |$VICIDiaL_park_on_extension|$VICIDiaL_park_on_filename| -->\n";
        }
        echo "<!-- CAMPAIGN DEFAULT PARKING: |$VICIDiaL_park_on_extension|$VICIDiaL_park_on_filename| -->\n";

        # If a web form address is not set, use the default one
        if (strlen($web_form_address) > 0) {
            $VICIDiaL_web_form_address = "$web_form_address";
            echo "<!-- CAMPAIGN CUSTOM WEB FORM:   |$VICIDiaL_web_form_address| -->\n";
        } else {
            #$VICIDiaL_web_form_address = "$VICIDiaL_web_URL";
            print "<!-- CAMPAIGN DEFAULT WEB FORM:  |$VICIDiaL_web_form_address| -->\n";
            $VICIDiaL_web_form_address_enc = rawurlencode($VICIDiaL_web_form_address);
        }
        $VICIDiaL_web_form_address_enc = rawurlencode($VICIDiaL_web_form_address);

        # If a web form address two is not set, use the first one
        if (strlen($web_form_address_two) > 0) {
            $VICIDiaL_web_form_address_two = "$web_form_address_two";
            echo "<!-- CAMPAIGN CUSTOM WEB FORM 2:   |$VICIDiaL_web_form_address_two| -->\n";
        } else {
            $VICIDiaL_web_form_address_two = "$VICIDiaL_web_form_address";
            echo "<!-- CAMPAIGN DEFAULT WEB FORM 2:  |$VICIDiaL_web_form_address_two| -->\n";
            $VICIDiaL_web_form_address_two_enc = rawurlencode($VICIDiaL_web_form_address_two);
        }
        $VICIDiaL_web_form_address_two_enc = rawurlencode($VICIDiaL_web_form_address_two);

        # If closers are allowed on this campaign
        if ($allow_closers == "Y") {
            $VICIDiaL_allow_closers = 1;
            echo "<!-- CAMPAIGN ALLOWS CLOSERS:    |$VICIDiaL_allow_closers| -->\n";
        } else {
            $VICIDiaL_allow_closers = 0;
            echo "<!-- CAMPAIGN ALLOWS NO CLOSERS: |$VICIDiaL_allow_closers| -->\n";
        }


        $session_ext = eregi_replace("[^a-z0-9]", "", $extension);
        if (strlen($session_ext) > 10) {
            $session_ext = substr($session_ext, 0, 10);
        }
        $session_rand = (rand(1, 9999999) + 10000000);
        $session_name = "$StarTtimE$US$session_ext$session_rand";

        if ($webform_sessionname) {
            $webform_sessionname = "&session_name=$session_name";
        } else {
            $webform_sessionname = '';
        }

        $stmt = "DELETE from web_client_sessions where start_time < '$past_month_date' and extension='$extension' and server_ip = '$server_ip' and program = 'vicidial';";
        if ($DB) {
            echo "|$stmt|\n";
        }
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01029', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }

        $stmt = "INSERT INTO web_client_sessions values('$extension','$server_ip','vicidial','$NOW_TIME','$session_name');";
        if ($DB) {
            echo "|$stmt|\n";
        }
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01030', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }

        if (( ($campaign_allow_inbound == 'Y') and ($dial_method != 'MANUAL') ) || ($campaign_leads_to_call > 0) || (ereg('Y', $no_hopper_leads_logins))) {
            ##### check to see if the user has a conf extension already, this happens if they previously exited uncleanly
            $stmt = "SELECT conf_exten FROM vicidial_conferences where extension='$SIP_user' and server_ip = '$server_ip' LIMIT 1;";
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01032', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            if ($DB) {
                echo "$stmt\n";
            }
            $prev_login_ct = mysql_num_rows($rslt);
            $i = 0;
            while ($i < $prev_login_ct) {
                $row = mysql_fetch_row($rslt);
                $session_id = $row[0];
                $i++;
            }
            if ($prev_login_ct > 0) {
                echo "<!-- USING PREVIOUS MEETME ROOM - $session_id - $NOW_TIME - $SIP_user -->\n";
            } else {
                ##### grab the next available vicidial_conference room and reserve it
                $stmt = "SELECT count(*) FROM vicidial_conferences where server_ip='$server_ip' and ((extension='') or (extension is null));";
                if ($DB) {
                    echo "$stmt\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01033', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $row = mysql_fetch_row($rslt);
                if ($row[0] > 0) {
                    $stmt = "UPDATE vicidial_conferences set extension='$SIP_user', leave_3way='0' where server_ip='$server_ip' and ((extension='') or (extension is null)) limit 1;";
                    if ($format == 'debug') {
                        echo "\n<!-- $stmt -->";
                    }
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01034', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }

                    $stmt = "SELECT conf_exten from vicidial_conferences where server_ip='$server_ip' and ( (extension='$SIP_user') or (extension='$VD_login') );";
                    if ($format == 'debug') {
                        echo "\n<!-- $stmt -->";
                    }
                    $rslt = mysql_query($stmt, $link);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01035', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    $row = mysql_fetch_row($rslt);
                    $session_id = $row[0];
                }
                echo "<!-- USING NEW MEETME ROOM - $session_id - $NOW_TIME - $SIP_user -->\n";
            }

            ### mark leads that were not dispositioned during previous calls as ERI
            $stmt = "UPDATE vicidial_list set status='ERI', user='' where status IN('QUEUE','INCALL') and user ='$VD_login';";
            if ($DB) {
                echo "$stmt\n";
            }
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01036', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $vlERIaffected_rows = mysql_affected_rows($link);
            echo "<!-- old QUEUE and INCALL reverted list:   |$vlERIaffected_rows| -->\n";

            $stmt = "DELETE from vicidial_hopper where status IN('QUEUE','INCALL','DONE') and user ='$VD_login';";
            if ($DB) {
                echo "$stmt\n";
            }
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01037', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $vhICaffected_rows = mysql_affected_rows($link);
            echo "<!-- old QUEUE and INCALL reverted hopper: |$vhICaffected_rows| -->\n";

            $stmt = "DELETE from vicidial_live_agents where user ='$VD_login';";
            if ($DB) {
                echo "$stmt\n";
            }
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01038', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $vlaLIaffected_rows = mysql_affected_rows($link);
            echo "<!-- old vicidial_live_agents records cleared: |$vlaLIaffected_rows| -->\n";

            $stmt = "DELETE from vicidial_live_inbound_agents where user ='$VD_login';";
            if ($DB) {
                echo "$stmt\n";
            }
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01039', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $vliaLIaffected_rows = mysql_affected_rows($link);
            echo "<!-- old vicidial_live_inbound_agents records cleared: |$vliaLIaffected_rows| -->\n";

            ### insert an entry into the user log for the login event
            $vul_data = "$vlERIaffected_rows|$vhICaffected_rows|$vlaLIaffected_rows|$vliaLIaffected_rows";
            $stmt = "INSERT INTO vicidial_user_log (user,event,campaign_id,event_date,event_epoch,user_group,session_id,server_ip,extension,computer_ip,browser,data) values('$VD_login','LOGIN','$VD_campaign','$NOW_TIME','$StarTtimE','$VU_user_group','$session_id','$server_ip','$protocol/$extension','$ip','$browser','$vul_data')";
            if ($DB) {
                echo "|$stmt|\n";
            }
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01031', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }

            #   echo "<b>You have logged in as user: $VD_login on phone: $SIP_user to campaign: $VD_campaign</b><br />\n";
            $VICIDiaL_is_logged_in = 1;

            ### set the callerID for manager middleware-app to connect the phone to the user
            $SIqueryCID = "S$CIDdate$session_id";

            #############################################
            ##### START SYSTEM_SETTINGS LOOKUP #####
            $stmt = "SELECT enable_queuemetrics_logging,queuemetrics_server_ip,queuemetrics_dbname,queuemetrics_login,queuemetrics_pass,queuemetrics_log_id,vicidial_agent_disable,allow_sipsak_messages,queuemetrics_loginout,queuemetrics_addmember_enabled FROM system_settings;";
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01040', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            if ($DB) {
                echo "$stmt\n";
            }
            $qm_conf_ct = mysql_num_rows($rslt);
            if ($qm_conf_ct > 0) {
                $row = mysql_fetch_row($rslt);
                $enable_queuemetrics_logging = $row[0];
                $queuemetrics_server_ip = $row[1];
                $queuemetrics_dbname = $row[2];
                $queuemetrics_login = $row[3];
                $queuemetrics_pass = $row[4];
                $queuemetrics_log_id = $row[5];
                $vicidial_agent_disable = $row[6];
                $allow_sipsak_messages = $row[7];
                $queuemetrics_loginout = $row[8];
                $queuemetrics_addmember_enabled = $row[9];
            }
            ##### END QUEUEMETRICS LOGGING LOOKUP #####
            ###########################################

            if (($enable_sipsak_messages > 0) and ($allow_sipsak_messages > 0) and (eregi("SIP", $protocol))) {
                $SIPSAK_prefix = 'LIN-';
                echo "<!-- sending login sipsak message: $SIPSAK_prefix$VD_campaign -->\n";
                passthru("/usr/local/bin/sipsak -M -O desktop -B \"$SIPSAK_prefix$VD_campaign\" -r 5060 -s sip:$extension@$phone_ip > /dev/null");
                $SIqueryCID = "$SIPSAK_prefix$VD_campaign$DS$CIDdate";
            }

            $webphone_content = '';
            if ($is_webphone != 'Y') {
                $TEMP_SIP_user_DiaL = $SIP_user_DiaL;
                if ($on_hook_agent == 'Y') {
                    $TEMP_SIP_user_DiaL = 'Local/8300@default';
                }
                ### insert a NEW record to the vicidial_manager table to be processed
                $stmt = "INSERT INTO vicidial_manager values('','','$NOW_TIME','NEW','N','$server_ip','','Originate','$SIqueryCID','Channel: $TEMP_SIP_user_DiaL','Context: $ext_context','Exten: $session_id','Priority: 1','Callerid: \"$SIqueryCID\" <$campaign_cid>','','','','','');";
                if ($DB) {
                    echo "$stmt\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01041', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $affected_rows = mysql_affected_rows($link);
                echo "<!-- call placed to session_id: $session_id from phone: $SIP_user $SIP_user_DiaL -->\n";
            } else {
                ### build Iframe variable content for webphone here
                $codecs_list = preg_replace("/ /", '', $codecs_list);
                $codecs_list = preg_replace("/-/", '', $codecs_list);
                $codecs_list = preg_replace("/&/", '', $codecs_list);
                $webphone_server_ip = $server_ip;
                if ($use_external_server_ip == 'Y') {
                    ##### find external_server_ip if enabled for this phone account
                    $stmt = "SELECT external_server_ip FROM servers where server_ip='$server_ip' LIMIT 1;";
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
                if ($webphone_location == 'bar') {
                    $webphone_content = "<iframe src=\"$WebPhonEurl\" style=\"width:" . $webphone_width . "px;height:" . $webphone_height . "px;background-color:transparent;z-index:17;\" scrolling=\"no\" frameborder=\"0\" allowtransparency=\"true\" id=\"webphone\" name=\"webphone\" width=\"" . $webphone_width . "px\" height=\"" . $webphone_height . "px\"> </iframe>";
                } else {
                    $webphone_content = "<iframe src=\"$WebPhonEurl\" style=\"width:" . $webphone_width . "px;height:" . $webphone_height . "px;background-color:transparent;z-index:17;\" scrolling=\"auto\" frameborder=\"0\" allowtransparency=\"true\" id=\"webphone\" name=\"webphone\" width=\"" . $webphone_width . "px\" height=\"" . $webphone_height . "px\"> </iframe>";
                }
            }

            ##### grab the campaign_weight and number of calls today on that campaign for the agent
            $stmt = "SELECT campaign_weight,calls_today FROM vicidial_campaign_agents where user='$VD_login' and campaign_id = '$VD_campaign';";
            $rslt = mysql_query($stmt, $link);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01042', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            if ($DB) {
                echo "$stmt\n";
            }
            $vca_ct = mysql_num_rows($rslt);
            if ($vca_ct > 0) {
                $row = mysql_fetch_row($rslt);
                $campaign_weight = $row[0];
                $calls_today = $row[1];
                $i++;
            } else {
                $campaign_weight = '0';
                $calls_today = '0';
                $stmt = "INSERT INTO vicidial_campaign_agents (user,campaign_id,campaign_rank,campaign_weight,calls_today) values('$VD_login','$VD_campaign','0','0','$calls_today');";
                if ($DB) {
                    echo "$stmt\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01043', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $affected_rows = mysql_affected_rows($link);
                echo "<!-- new vicidial_campaign_agents record inserted: |$affected_rows| -->\n";
            }

            if ($auto_dial_level > 0) {
                echo "<!-- campaign is set to auto_dial_level: $auto_dial_level -->\n";

                $closer_chooser_string = '';
                $stmt = "INSERT INTO vicidial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,closer_campaigns,user_level,campaign_weight,calls_today,last_state_change,outbound_autodial,manager_ingroup_set,on_hook_ring_time,on_hook_agent) values('$VD_login','$server_ip','$session_id','$SIP_user','PAUSED','','$VD_campaign','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','$closer_chooser_string','$user_level','$campaign_weight','$calls_today','$NOW_TIME','Y','N','$phone_ring_timeout','$on_hook_agent');";
                if ($DB) {
                    echo "$stmt\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01044', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $affected_rows = mysql_affected_rows($link);
                echo "<!-- new vicidial_live_agents record inserted: |$affected_rows| -->\n";

                if ($enable_queuemetrics_logging > 0) {
                    $QM_LOGIN = 'AGENTLOGIN';
                    $QM_PHONE = "$VD_login@agents";
                    if (($queuemetrics_loginout == 'CALLBACK') or ($queuemetrics_loginout == 'NONE')) {
                        $QM_LOGIN = 'AGENTCALLBACKLOGIN';
                        $QM_PHONE = "$SIP_user_DiaL";
                    }
                    $linkB = mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                    mysql_select_db("$queuemetrics_dbname", $linkB);

                    if ($queuemetrics_loginout != 'NONE') {
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='$QM_LOGIN',data1='$QM_PHONE',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
                        if ($DB) {
                            echo "$stmt\n";
                        }
                        $rslt = mysql_query($stmt, $linkB);
                        if ($mel > 0) {
                            mysql_error_logging($NOW_TIME, $linkB, $mel, $stmt, '01045', $VD_login, $server_ip, $session_name, $one_mysql_log);
                        }
                        $affected_rows = mysql_affected_rows($linkB);
                        echo "<!-- queue_log $QM_LOGIN entry added: $VD_login|$affected_rows|$QM_PHONE -->\n";
                    }

                    $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEALL',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
                    if ($DB) {
                        echo "$stmt\n";
                    }
                    $rslt = mysql_query($stmt, $linkB);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $linkB, $mel, $stmt, '01046', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    $affected_rows = mysql_affected_rows($linkB);
                    echo "<!-- queue_log PAUSE entry added: $VD_login|$affected_rows -->\n";

                    if ($queuemetrics_addmember_enabled > 0) {
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='ADDMEMBER2',data1='$QM_PHONE',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
                        if ($DB) {
                            echo "$stmt\n";
                        }
                        $rslt = mysql_query($stmt, $linkB);
                        if ($mel > 0) {
                            mysql_error_logging($NOW_TIME, $linkB, $mel, $stmt, '01069', $VD_login, $server_ip, $session_name, $one_mysql_log);
                        }
                        $affected_rows = mysql_affected_rows($linkB);
                        echo "<!-- queue_log ADDMEMBER2 entry added: $VD_login|$affected_rows -->\n";
                    }

                    mysql_close($linkB);
                    mysql_select_db("$VARDB_database", $link);
                }


                if (($campaign_allow_inbound == 'Y') and ($dial_method != 'MANUAL')) {
                    print "<!-- CLOSER-type campaign -->\n";
                }
            } else {
                print "<!-- campaign is set to manual dial: $auto_dial_level -->\n";

                $stmt = "INSERT INTO vicidial_live_agents (user,server_ip,conf_exten,extension,status,lead_id,campaign_id,uniqueid,callerid,channel,random_id,last_call_time,last_update_time,last_call_finish,user_level,campaign_weight,calls_today,last_state_change,outbound_autodial,manager_ingroup_set,on_hook_ring_time,on_hook_agent) values('$VD_login','$server_ip','$session_id','$SIP_user','PAUSED','','$VD_campaign','','','','$random','$NOW_TIME','$tsNOW_TIME','$NOW_TIME','$user_level', '$campaign_weight', '$calls_today','$NOW_TIME','N','N','$phone_ring_timeout','$on_hook_agent');";
                if ($DB) {
                    echo "$stmt\n";
                }
                $rslt = mysql_query($stmt, $link);
                if ($mel > 0) {
                    mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01047', $VD_login, $server_ip, $session_name, $one_mysql_log);
                }
                $affected_rows = mysql_affected_rows($link);
                echo "<!-- new vicidial_live_agents record inserted: |$affected_rows| -->\n";

                if ($enable_queuemetrics_logging > 0) {
                    $QM_LOGIN = 'AGENTLOGIN';
                    $QM_PHONE = "$VD_login@agents";
                    if (($queuemetrics_loginout == 'CALLBACK') or ($queuemetrics_loginout == 'NONE')) {
                        $QM_LOGIN = 'AGENTCALLBACKLOGIN';
                        $QM_PHONE = "$SIP_user_DiaL";
                    }
                    $linkB = mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
                    mysql_select_db("$queuemetrics_dbname", $linkB);

                    if ($queuemetrics_loginout != 'NONE') {
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='$QM_LOGIN',data1='$QM_PHONE',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
                        if ($DB) {
                            echo "$stmt\n";
                        }
                        $rslt = mysql_query($stmt, $linkB);
                        if ($mel > 0) {
                            mysql_error_logging($NOW_TIME, $linkB, $mel, $stmt, '01048', $VD_login, $server_ip, $session_name, $one_mysql_log);
                        }
                        $affected_rows = mysql_affected_rows($linkB);
                        echo "<!-- queue_log $QM_LOGIN entry added: $VD_login|$affected_rows|$QM_PHONE -->\n";
                    }

                    $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEALL',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
                    if ($DB) {
                        echo "$stmt\n";
                    }
                    $rslt = mysql_query($stmt, $linkB);
                    if ($mel > 0) {
                        mysql_error_logging($NOW_TIME, $linkB, $mel, $stmt, '01049', $VD_login, $server_ip, $session_name, $one_mysql_log);
                    }
                    $affected_rows = mysql_affected_rows($linkB);
                    echo "<!-- queue_log PAUSE entry added: $VD_login|$affected_rows -->\n";

                    if ($queuemetrics_addmember_enabled > 0) {
                        $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimE',call_id='NONE',queue='$VD_campaign',agent='Agent/$VD_login',verb='ADDMEMBER2',data1='$QM_PHONE',serverid='$queuemetrics_log_id',data4='$qm_phone_environment';";
                        if ($DB) {
                            echo "$stmt\n";
                        }
                        $rslt = mysql_query($stmt, $linkB);
                        if ($mel > 0) {
                            mysql_error_logging($NOW_TIME, $linkB, $mel, $stmt, '01072', $VD_login, $server_ip, $session_name, $one_mysql_log);
                        }
                        $affected_rows = mysql_affected_rows($linkB);
                        echo "<!-- queue_log ADDMEMBER2 entry added: $VD_login|$affected_rows -->\n";
                    }

                    mysql_close($linkB);
                    mysql_select_db("$VARDB_database", $link);
                }
            }
        } else {

            echo "<title>Go Contact: Erro no Login </title>";
            echo "</head>";
            echo "<body id='ib'>";
            echo "<center><img style='margin-top:10%; ' src=../images/pictures/go_logo_35.png />";
            echo "<div id=work-area style='width:40%; min-height:0px;'>";
            echo "<br>";
            echo "<div class=cc-mstyle style='border:none'>";

            echo "
<table>
<tr><td><p>Não existem contactos disponiveis nesta campanha.</p></td><td><a href='agente.php'><img src='/images/icons/arrow_rotate_clockwise_32.png'/></a></td><td>Voltar</td></td></table><br>";




            echo "</body>\n\n";
            echo "</html>\n\n";
            exit;
        }
        if (strlen($session_id) < 1) {
            echo "<title>Go Contact: Campaign Login</title>\n";
            echo "</head>\n";
            echo "<body  id='ib' >\n";
            if ($hide_timeclock_link < 1) {
                echo "<a href=\"./timeclock.php?referrer=agent&amp;pl=$phone_login&amp;pp=$phone_pass&amp;VD_login=$VD_login&amp;VD_pass=$VD_pass\"> Timeclock</a><br />\n";
            }
            echo "<table width=\"100%\"><tr><td></td>\n";
            echo "<!-- INTERNATIONALIZATION-LINKS-PLACEHOLDER-VICIDIAL -->\n";
            echo "</tr></table>\n";

            echo "<div align=center><img src=images/vicidial_logo.gif /></div>";
            echo "<table align=center border=0 cellpadding=0 cellspacing=0>\n";
            echo "      <tbody><tr>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=1 width=15></td>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=1 width=77></td>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=1 width=19></td>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=1 width=68></td>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=1 width=19></td>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=1 width=1></td>\n";
            echo "      </tr>\n";
            echo "      <tr>\n";
            echo "        <td><img name=dispo_r1_c1 src=images/dispo_r1_c1.jpg id=dispo_r1_c1 border=0 height=21 width=15></td>\n";
            echo "        <td background=images/dispo_r1_c2.jpg></td>\n";
            echo "        <td background=images/dispo_r1_c3.jpg></td>\n";
            echo "        <td background=images/dispo_r1_c4.jpg></td>\n";
            echo "        <td><img name=dispo_r1_c5 src=images/dispo_r1_c5.jpg id=dispo_r1_c5 border=0 height=21 width=19></td>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=21 width=1></td>\n";
            echo "      </tr>\n";
            echo "      <tr>\n";
            echo "        <td rowspan=3 background=images/dispo_r3_c1.jpg></td>\n";
            echo "        <td colspan=3 rowspan=3 background=images/dispo_r2_c2.jpg valign=middle align=center>\n";

            echo "<b>Sorry, there are no available sessions</b>\n";
            echo "<form action=\"$PHP_SELF\" method=\"post\" />\n";
            echo "<input type=\"hidden\" name=\"DB\" value=\"$DB\" />\n";
            echo "<input type=\"hidden\" name=\"JS_browser_height\" id=\"JS_browser_height\" value=\"\" />\n";
            echo "<input type=\"hidden\" name=\"JS_browser_width\" id=\"JS_browser_width\" value=\"\" />\n";
            echo "<input type=\"hidden\" name=\"phone_login\" value=\"$phone_login\" />\n";
            echo "<input type=\"hidden\" name=\"phone_pass\" value=\"$phone_pass\" />\n";
            echo "Login: <input type=\"text\" name=\"VD_login\" size=\"10\" maxlength=\"20\" value=\"$VD_login\" />\n<br />";
            echo "Password: <input type=\"password\" name=\"VD_pass\" size=\"10\" maxlength=\"20\" value=\"$VD_pass\" /><br />\n";
            echo "Campaign: <span id=\"LogiNCamPaigns\">$camp_form_code</span><br />\n";
            echo "<input type=\"submit\" name=\"SUBMIT\" value=\"Submit\" />  \n";
            echo "<span id=\"LogiNReseT\"></span><br>\n";
            echo "</FORM>\n\n";

            echo "		</td>\n";
            echo "        <td rowspan=3 background=images/dispo_r3_c5.jpg></td>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=75 width=1></td>\n";
            echo "      </tr>\n";
            echo "      <tr>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=36 width=1></td>\n";
            echo "      </tr>\n";
            echo "      <tr>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=75 width=1></td>\n";
            echo "      </tr>\n";
            echo "      <tr>\n";
            echo "        <td><img name=dispo_r5_c1 src=images/dispo_r5_c1.jpg id=dispo_r5_c1 border=0 height=25 width=15></td>\n";
            echo "        <td background=images/dispo_r5_c2.jpg></td>\n";
            echo "        <td background=images/dispo_r5_c3.jpg></td>\n";
            echo "        <td background=images/dispo_r5_c3.jpg></td>\n";
            echo "        <td><img name=dispo_r5_c5 src=images/dispo_r5_c5.jpg id=dispo_r5_c5 border=0 height=25 width=19></td>\n";
            echo "        <td><img src=images/spacer.gif border=0 height=25 width=1></td>\n";
            echo "      </tr>\n";
            echo "    </tbody></table>\n";

            echo "</body>\n\n";
            echo "</html>\n\n";
            exit;
        }

        if (ereg('MSIE', $browser)) {
            $useIE = 1;
            echo "<!-- client web browser used: MSIE |$browser|$useIE| -->\n";
        } else {
            $useIE = 0;
            echo "<!-- client web browser used: W3C-Compliant |$browser|$useIE| -->\n";
        }

        $StarTtimE = date("U");
        $NOW_TIME = date("Y-m-d H:i:s");
        ##### Agent is going to log in so insert the vicidial_agent_log entry now
        $stmt = "INSERT INTO vicidial_agent_log (user,server_ip,event_time,campaign_id,pause_epoch,pause_sec,wait_epoch,user_group,sub_status) values('$VD_login','$server_ip','$NOW_TIME','$VD_campaign','$StarTtimE','0','$StarTtimE','$VU_user_group','LOGIN');";
        if ($DB) {
            echo "$stmt\n";
        }
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01050', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }
        $affected_rows = mysql_affected_rows($link);
        $agent_log_id = mysql_insert_id($link);
        echo "<!-- vicidial_agent_log record inserted: |$affected_rows|$agent_log_id| -->\n";

        ##### update vicidial_campaigns to show agent has logged in
        $stmt = "UPDATE vicidial_campaigns set campaign_logindate='$NOW_TIME' where campaign_id='$VD_campaign';";
        if ($DB) {
            echo "$stmt\n";
        }
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01064', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }
        $VCaffected_rows = mysql_affected_rows($link);
        echo "<!-- vicidial_campaigns campaign_logindate updated: |$VCaffected_rows|$NOW_TIME| -->\n";

        if ($enable_queuemetrics_logging > 0) {
            $StarTtimEpause = ($StarTtimE + 1);
            $linkB = mysql_connect("$queuemetrics_server_ip", "$queuemetrics_login", "$queuemetrics_pass");
            mysql_select_db("$queuemetrics_dbname", $linkB);

            $stmt = "INSERT INTO queue_log SET partition='P01',time_id='$StarTtimEpause',call_id='NONE',queue='NONE',agent='Agent/$VD_login',verb='PAUSEREASON',data1='LOGIN',data3='$QM_PHONE',serverid='$queuemetrics_log_id';";
            if ($DB) {
                echo "$stmt\n";
            }
            $rslt = mysql_query($stmt, $linkB);
            if ($mel > 0) {
                mysql_error_logging($NOW_TIME, $linkB, $mel, $stmt, '01063', $VD_login, $server_ip, $session_name, $one_mysql_log);
            }
            $affected_rows = mysql_affected_rows($linkB);
            echo "<!-- queue_log PAUSEREASON LOGIN entry added: $VD_login|$affected_rows|$QM_PHONE -->\n";

            mysql_close($linkB);
            mysql_select_db("$VARDB_database", $link);
        }

        $stmt = "UPDATE vicidial_live_agents SET agent_log_id='$agent_log_id' where user='$VD_login';";
        if ($DB) {
            echo "$stmt\n";
        }
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01061', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }
        $VLAaffected_rows_update = mysql_affected_rows($link);

        $stmt = "UPDATE vicidial_users SET shift_override_flag='0' where user='$VD_login' and shift_override_flag='1';";
        if ($DB) {
            echo "$stmt\n";
        }
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01057', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }
        $VUaffected_rows = mysql_affected_rows($link);

        $S = '*';
        $D_s_ip = explode('.', $server_ip);
        if (strlen($D_s_ip[0]) < 2) {
            $D_s_ip[0] = "0$D_s_ip[0]";
        }
        if (strlen($D_s_ip[0]) < 3) {
            $D_s_ip[0] = "0$D_s_ip[0]";
        }
        if (strlen($D_s_ip[1]) < 2) {
            $D_s_ip[1] = "0$D_s_ip[1]";
        }
        if (strlen($D_s_ip[1]) < 3) {
            $D_s_ip[1] = "0$D_s_ip[1]";
        }
        if (strlen($D_s_ip[2]) < 2) {
            $D_s_ip[2] = "0$D_s_ip[2]";
        }
        if (strlen($D_s_ip[2]) < 3) {
            $D_s_ip[2] = "0$D_s_ip[2]";
        }
        if (strlen($D_s_ip[3]) < 2) {
            $D_s_ip[3] = "0$D_s_ip[3]";
        }
        if (strlen($D_s_ip[3]) < 3) {
            $D_s_ip[3] = "0$D_s_ip[3]";
        }
        $server_ip_dialstring = "$D_s_ip[0]$S$D_s_ip[1]$S$D_s_ip[2]$S$D_s_ip[3]$S";

        ##### grab the datails of all active scripts in the system
        $stmt = "SELECT script_id,script_name FROM vicidial_scripts WHERE active='Y' order by script_id limit 1000;";
        $rslt = mysql_query($stmt, $link);
        if ($mel > 0) {
            mysql_error_logging($NOW_TIME, $link, $mel, $stmt, '01051', $VD_login, $server_ip, $session_name, $one_mysql_log);
        }
        if ($DB) {
            echo "$stmt\n";
        }
        $MM_scripts = mysql_num_rows($rslt);
        $e = 0;
        while ($e < $MM_scripts) {
            $row = mysql_fetch_row($rslt);
            $MMscriptid[$e] = $row[0];
            $MMscriptname[$e] = urlencode($row[1]);
            $MMscriptids = "$MMscriptids'$MMscriptid[$e]',";
            $MMscriptnames = "$MMscriptnames'$MMscriptname[$e]',";
            $e++;
        }
        $MMscriptids = substr("$MMscriptids", 0, -1);
        $MMscriptnames = substr("$MMscriptnames", 0, -1);
    }
}


                $queryClient = "SELECT server_description from servers limit 1";
                $queryClient = mysql_query($queryClient, $link) or die(mysql_error());
                $curClient = mysql_fetch_row($queryClient);
           
                unset($curLogo);
                if (file_exists("../client_files/$curClient[0]/logo.gif")) {
                    $curLogo = "client_files/$curClient[0]/logo.gif";
                    echo "<input type=hidden name=curlogo value=$curLogo />";
                } else {
                    unset($curLogo);
                }


?>

<script type="text/javascript" src="js/mensagens.js"></script>
<script type="text/javascript" src="js/novo_cliente.js"></script>
<script type="text/javascript" src="js/historico.js"></script>
<script type="text/javascript" src="js/agente.js"></script>
<script type="text/javascript" src="/ini/SeamlessLoop.js"></script>
<script language="Javascript">
            function soundsLoaded() {
                loop.start("ring");
                loop.stop("ring");
            }
            ;
            var loop = new SeamlessLoop();
            var loop_warning = new SeamlessLoop();
            var script_dinamico =<?= ($agent_fullscreen == "Y") ? "true" : "false" ?>;
            loop.addUri("/ini/telephone-ring-4.ogg", 4000, "ring");
            loop_warning.addUri("/ini/disconnected.ogg", 10000, "disconnected");
            loop.callback(soundsLoaded);
            moment.lang('pt');
            var clientName = '<? echo $curClient[0]; ?>'; 
            window.name = 'GCC_window';
            var campaign_status = <?= json_encode($campaign_status) ?>;
            var my_callback_option = '<?= $my_callback_option ?>';
            var MTvar;
            var NOW_TIME = '<?= $NOW_TIME ?>';
            var SQLdate = '<?= $NOW_TIME ?>';
            var StarTtimE = '<?= $StarTtimE ?>';
            var UnixTime = '<?= $StarTtimE ?>';
            var UnixTimeMS = 0;
            var t = new Date();
            var c = new Date();
            LCAe = new Array('', '', '', '', '', '');
            LCAc = new Array('', '', '', '', '', '');
            LCAt = new Array('', '', '', '', '', '');
            LMAe = new Array('', '', '', '', '', '');
            var max_callback = '<?= $callback_hours_block ?>';
            var CalL_XC_a_Dtmf = '<?= $xferconf_a_dtmf ?>';
            var CalL_XC_a_NuMber = '<?= $xferconf_a_number ?>';
            var CalL_XC_b_Dtmf = '<?= $xferconf_b_dtmf ?>';
            var CalL_XC_b_NuMber = '<?= $xferconf_b_number ?>';
            var CalL_XC_c_NuMber = '<?= $xferconf_c_number ?>';
            var CalL_XC_d_NuMber = '<?= $xferconf_d_number ?>';
            var CalL_XC_e_NuMber = '<?= $xferconf_e_number ?>';
            var VU_hotkeys_active = '<?= $VU_hotkeys_active ?>';
            var VU_agent_choose_ingroups = '<?= $VU_agent_choose_ingroups ?>';
            var VU_agent_choose_ingroups_DV = '';
            var agent_choose_territories = '<?= $VU_agent_choose_territories ?>';
            var agent_select_territories = '<?= $agent_select_territories ?>';
            var agent_choose_blended = '<?= $VU_agent_choose_blended ?>';
            var VU_closer_campaigns = '<?= $VU_closer_campaigns ?>';
            var CallBackDatETimE = '';
            var CallBackrecipient = '';
            var CallBackCommenTs = '';
            var CallBackLeadStatus = '';
            var scheduled_callbacks = '<?= $scheduled_callbacks ?>';
            var dispo_check_all_pause = '<?= $dispo_check_all_pause ?>';
            var api_check_all_pause = '<?= $api_check_all_pause ?>';
            VARgroup_alias_ids = new Array(<?= $VARgroup_alias_ids ?>);
            VARgroup_alias_names = new Array(<?= $VARgroup_alias_names ?>);
            VARcaller_id_numbers = new Array(<?= $VARcaller_id_numbers ?>);
            var VD_group_aliases_ct = '<?= $VD_group_aliases_ct ?>';
            var agent_allow_group_alias = '<?= $agent_allow_group_alias ?>';
            var default_group_alias = '<?= $default_group_alias ?>';
            var default_group_alias_cid = '<?= $default_group_alias_cid ?>';
            var active_group_alias = '';
            var agent_pause_codes_active = '<?= $agent_pause_codes_active ?>';
            VARpause_codes = new Array(<?= $VARpause_codes ?>);
            VARpause_code_names = new Array(<?= $VARpause_code_names ?>);
            var VD_pause_codes_ct = '<?= $VD_pause_codes_ct ?>';
            VARpreset_names = new Array(<?= $VARpreset_names ?>);
            VARpreset_numbers = new Array(<?= $VARpreset_numbers ?>);
            VARpreset_dtmfs = new Array(<?= $VARpreset_dtmfs ?>);
            VARpreset_hide_numbers = new Array(<?= $VARpreset_hide_numbers ?>);
            var VD_preset_names_ct = '<?= $VD_preset_names_ct ?>';
            VARstatuses = new Array(<?= $VARstatuses ?>);
            VARstatusnames = new Array(<?= $VARstatusnames ?>);
            VARSELstatuses = new Array(<?= $VARSELstatuses ?>);
            VARCBstatuses = new Array(<?= $VARCBstatuses ?>);
            var VARCBstatusesLIST = '<?= $VARCBstatusesLIST ?>';
            var VD_statuses_ct = '<?= $VD_statuses_ct ?>';
            var VARSELstatuses_ct = '<?= $VARSELstatuses_ct ?>';
            VARingroups = new Array(<?= $VARingroups ?>);
            var INgroupCOUNT = '<?= $INgrpCT ?>';
            VARterritories = new Array(<?= $VARterritories ?>);
            var territoryCOUNT = '<?= $territoryCT ?>';
            VARxfergroups = new Array(<?= $VARxfergroups ?>);
            VARxfergroupsnames = new Array(<?= $VARxfergroupsnames ?>);
            var XFgroupCOUNT = '<?= $XFgrpCT ?>';
            var default_xfer_group = '<?= $default_xfer_group ?>';
            var default_xfer_group_name = '<?= $default_xfer_group_name ?>';
            var LIVE_default_xfer_group = '<?= $default_xfer_group ?>';
            var HK_statuses_camp = '<?= $HK_statuses_camp ?>';
            HKhotkeys = new Array(<?= $HKhotkeys ?>);
            HKstatuses = new Array(<?= $HKstatuses ?>);
            HKstatusnames = new Array(<?= $HKstatusnames ?>);
            var hotkeys = new Array();
            var callback_limit_reached=false;
<?php
$h = 0;
while ($HK_statuses_camp > $h) {
    echo "hotkeys['$HKhotkey[$h]'] = \"$HKstatus[$h] ----- $HKstatus_name[$h]\";\n";
    $h++;
}
?>
            var HKdispo_display = 0;
            var HKbutton_allowed = 1;
            var HKfinish = 0;
            var scriptnames = new Array();
<?php
$h = 0;
while ($MM_scripts > $h) {
    echo "scriptnames['$MMscriptid[$h]'] = \"$MMscriptname[$h]\";\n";
    $h++;
}
?>
            var view_scripts = '<?= $view_scripts ?>';
            var LOGfullname = '<?= $LOGfullname ?>';
            var recLIST = '';
            var filename = '';
            var last_filename = '';
            var LCAcount = 0;
            var LMAcount = 0;
            var filedate = '<?= $FILE_TIME ?>';
            var agcDIR = '<?= $agcDIR ?>';
            var agcPAGE = '<?= $agcPAGE ?>';
            var extension = '<?= $extension ?>';
            var extension_xfer = '<?= $extension ?>';
            var dialplan_number = '<?= $dialplan_number ?>';
            var ext_context = '<?= $ext_context ?>';
            var protocol = '<?= $protocol ?>';
            var agentchannel = '';
            var local_gmt = '<?= $local_gmt ?>';
            var server_ip = '<?= $server_ip ?>';
            var server_ip_dialstring = '<?= $server_ip_dialstring ?>';
            var asterisk_version = '<?= $asterisk_version ?>';
            var refresh_interval =<?= ($enable_fast_refresh < 1) ? 1000 : $fast_refresh_rate ?>;
            var session_id = '<?= $session_id ?>';
            var VICIDiaL_closer_login_checked = 0;
            var VICIDiaL_closer_login_selected = 0;
            var VICIDiaL_pause_calling = 1;
            var CalLCID = '';
            var MDnextCID = '';
            var XDnextCID = '';
            var LasTCID = '';
            var lead_dial_number = '';
            var MD_channel_look = 0;
            var XD_channel_look = 0;
            var MDuniqueid = '';
            var MDchannel = '';
            var MD_ring_secondS = 0;
            var MDlogEPOCH = 0;
            var VD_live_customer_call = 0;
            var VD_live_call_secondS = 0;
            var XD_live_customer_call = 0;
            var XD_live_call_secondS = 0;
            var xfer_in_call = 0;
            var open_dispo_screen = 0;
            var AgentDispoing = 0;
            var logout_stop_timeouts = 0;
            var VICIDiaL_allow_closers = '<?= $VICIDiaL_allow_closers ?>';
            var VICIDiaL_closer_blended = '0';
            var VU_closer_default_blended = '<?= $VU_closer_default_blended ?>';
            var VDstop_rec_after_each_call = '<?= $VDstop_rec_after_each_call ?>';
            var phone_login = '<?= $phone_login ?>';
            var original_phone_login = '<?= $original_phone_login ?>';
            var phone_pass = '<?= $phone_pass ?>';
            var user = '<?= $VD_login ?>';
            var user_abb = '<?= $user_abb ?>';
            var pass = '<?= $VD_pass ?>';
            var campaign = '<?= $VD_campaign ?>';
            var campaign_name = '<?= $campaign_name ?>';
            var group = '<?= $VD_campaign ?>';
            var VICIDiaL_web_form_address_enc = '<?= $VICIDiaL_web_form_address_enc ?>';
            var VICIDiaL_web_form_address = '<?= $VICIDiaL_web_form_address ?>';
            var VDIC_web_form_address = '<?= $VICIDiaL_web_form_address ?>';
            var VICIDiaL_web_form_address_two_enc = '<?= $VICIDiaL_web_form_address_two_enc ?>';
            var VICIDiaL_web_form_address_two = '<?= $VICIDiaL_web_form_address_two ?>';
            var VDIC_web_form_address_two = '<?= $VICIDiaL_web_form_address_two ?>';
            var CalL_ScripT_id = '';
            var CalL_AutO_LauncH = '';
            var panel_bgcolor = '<?= $MAIN_COLOR ?>';
            var CusTCB_bgcolor = '#FFFF66';
            var dead_time = 0;
            var auto_dial_level = '<?= $auto_dial_level ?>';
            var starting_dial_level = '<?= $auto_dial_level ?>';
            var dial_timeout = '<?= $dial_timeout ?>';
            var dial_prefix = '<?= $dial_prefix ?>';
            var manual_dial_prefix = '<?= $manual_dial_prefix ?>';
            var three_way_dial_prefix = '<?= $three_way_dial_prefix ?>';
            var campaign_cid = '<?= $campaign_cid ?>';
            var use_custom_cid = '<?= $use_custom_cid ?>';
            var campaign_vdad_exten = '<?= $campaign_vdad_exten ?>';
            var campaign_leads_to_call = '<?= $campaign_leads_to_call ?>';
            var epoch_sec = <?= $StarTtimE ?>;
            var dtmf_send_extension = '<?= $dtmf_send_extension ?>';
            var recording_exten = '<?= $campaign_rec_exten ?>';
            var campaign_recording = '<?= $campaign_recording ?>';
            var campaign_rec_filename = '<?= $campaign_rec_filename ?>';
            var LIVE_campaign_recording = '<?= $campaign_recording ?>';
            var LIVE_campaign_rec_filename = '<?= $campaign_rec_filename ?>';
            var LIVE_default_group_alias = '<?= $default_group_alias ?>';
            var LIVE_caller_id_number = '<?= $default_group_alias_cid ?>';
            var LIVE_web_vars = '<?= $default_web_vars ?>';
            var default_web_vars = '<?= $default_web_vars ?>';
            var campaign_script = '<?= $campaign_script ?>';
            var get_call_launch = '<?= $get_call_launch ?>';
            var campaign_am_message_exten = '<?= $campaign_am_message_exten ?>';
            var park_on_extension = '<?= $VICIDiaL_park_on_extension ?>';
            var park_count = 0;
            var customerparked = 0;
            var customerparkedcounter = 0;
            var check_n = 0;
            var conf_check_recheck = 0;
            var lastconf = '';
            var lastcustchannel = '';
            var lastcustserverip = '';
            var lastxferchannel = '';
            var custchannellive = 0;
            var xferchannellive = 0;
            var nochannelinsession = 0;
            var agc_dial_prefix = '91';
            var dtmf_silent_prefix = '<?= $dtmf_silent_prefix ?>';
            var conf_silent_prefix = '<?= $conf_silent_prefix ?>';
            var menuheight = 30;
            var menuwidth = 30;
            var menufontsize = 8;
            var textareafontsize = 10;
            var check_s;
            var active_display = 1;
            var conf_channels_xtra_display = 0;
            var display_message = '';
            var web_form_vars = '';
            var Nactiveext;
            var Nbusytrunk;
            var Nbusyext;
            var extvalue = extension;
            var activeext_query;
            var busytrunk_query;
            var busyext_query;
            var busytrunkhangup_query;
            var busylocalhangup_query;
            var activeext_order = 'asc';
            var busytrunk_order = 'asc';
            var busyext_order = 'asc';
            var busytrunkhangup_order = 'asc';
            var busylocalhangup_order = 'asc';
            var xmlhttp = false;
            var XfeR_channel = '';
            var XDcheck = '';
            var agent_log_id = '<?= $agent_log_id ?>';
            var session_name = '<?= $session_name ?>';
            var AutoDialReady = 0;
            var AutoDialWaiting = 0;
            var fronter = '';
            var VDCL_group_id = '';
            var previous_dispo = '';
            var previous_called_count = '';
            var hot_keys_active = 0;
            var all_record = 'NO';
            var all_record_count = 0;
            var LeaDDispO = '';
            var LeaDPreVDispO = '';
            var AgaiNHanguPChanneL = '';
            var AgaiNHanguPServeR = '';
            var AgainCalLSecondS = '';
            var AgaiNCalLCID = '';
            var CB_count_check = 60;
            var callholdstatus = '<?= $callholdstatus ?>';
            var agentcallsstatus = '<?= $agentcallsstatus ?>';
            var campagentstatctmax = '<?= $campagentstatctmax ?>';
            var campagentstatct = '0';
            var manual_dial_in_progress = 0;
            var auto_dial_alt_dial = 0;
            var reselect_preview_dial = 0;
            var in_lead_preview_state = 0;
            var reselect_alt_dial = 0;
            var alt_dial_active = 0;
            var alt_dial_status_display = 0;
            var mdnLisT_id = '<?= $manual_dial_list_id ?>';
            var VU_vicidial_transfers = '<?= $VU_vicidial_transfers ?>';
            var agentonly_callbacks = '<?= $agentonly_callbacks ?>';
            var agentcall_manual = '<?= $agentcall_manual ?>';
            var manual_dial_preview = '<?= $manual_dial_preview ?>';
            var manual_preview_dial = '<?= $manual_preview_dial ?>';
            var starting_alt_phone_dialing = '<?= $alt_phone_dialing ?>';
            var alt_phone_dialing = '<?= $alt_phone_dialing ?>';
            var DefaulTAlTDiaL = '<?= $DefaulTAlTDiaL ?>';
            var wrapup_seconds = '<?= $wrapup_seconds ?>';
            var wrapup_message = '<?= $wrapup_message ?>';
            var wrapup_counter = 0;
            var wrapup_waiting = 0;
            var use_internal_dnc = '<?= $use_internal_dnc ?>';
            var use_campaign_dnc = '<?= $use_campaign_dnc ?>';
            var three_way_call_cid = '<?= $three_way_call_cid ?>';
            var outbound_cid = '<?= $outbound_cid ?>';
            var threeway_cid = '';
            var cid_choice = '';
            var prefix_choice = '';
            var agent_dialed_number = '';
            var agent_dialed_type = '';
            var allcalls_delay = '<?= $allcalls_delay ?>';
            var omit_phone_code = '<?= $omit_phone_code ?>';
            var no_delete_sessions = '<?= $no_delete_sessions ?>';
            var webform_session = '<?= $webform_sessionname ?>';
            var local_consult_xfers = '<?= $local_consult_xfers ?>';
            var vicidial_agent_disable = '<?= $vicidial_agent_disable ?>';
            var CBentry_time = '';
            var CBcallback_time = '';
            var CBuser = '';
            var CBcomments = '';
            var volumecontrol_active = '<?= $volumecontrol_active ?>';
            var PauseCode_HTML = '';
            var manual_auto_hotkey = 0;
            var dialed_number = '';
            var dialed_label = '';
            var source_id = '';
            var DispO3waychannel = '';
            var DispO3wayXtrAchannel = '';
            var DispO3wayCalLserverip = '';
            var DispO3wayCalLxfernumber = '';
            var DispO3wayCalLcamptail = '';
            var PausENotifYCounTer = 0;
            var RedirecTxFEr = 0;
            var phone_ip = '<?= $phone_ip ?>';
            var enable_sipsak_messages = '<?= $enable_sipsak_messages ?>';
            var allow_sipsak_messages = '<?= $allow_sipsak_messages ?>';
            var HidEMonitoRSessionS = '<?= $HidEMonitoRSessionS ?>';
            var LogouTKicKAlL = '<?= $LogouTKicKAlL ?>';
            var flag_channels = '<?= $flag_channels ?>';
            var flag_string = '<?= $flag_string ?>';
            var vdc_header_date_format = '<?= $vdc_header_date_format ?>';
            var vdc_customer_date_format = '<?= $vdc_customer_date_format ?>';
            var vdc_header_phone_format = '<?= $vdc_header_phone_format ?>';
            var disable_alter_custphone = '<?= $disable_alter_custphone ?>';
            var manual_dial_filter = '<?= $manual_dial_filter ?>';
            var CopY_tO_ClipboarD = '<?= $CopY_tO_ClipboarD ?>';
            var inOUT = 'OUT';
            var useIE = '<?= $useIE ?>';
            var random = '<?= $random ?>';
            var threeway_end = 0;
            var agentphonelive = 0;
            var conf_dialed = 0;
            var leaving_threeway = 0;
            var blind_transfer = 0;
            var hangup_all_non_reserved = '<?= $hangup_all_non_reserved ?>';
            var dial_method = '<?= $dial_method ?>';
            var web_form_target = '<?= $web_form_target ?>';
            var TEMP_VDIC_web_form_address = '';
            var TEMP_VDIC_web_form_address_two = '';
            var APIPausE_ID = '99999';
            var APIDiaL_ID = '99999';
            var CheckDEADcall = 0;
            var CheckDEADcallON = 0;
            var VtigeRLogiNScripT = '<?= $vtiger_screen_login ?>';
            var VtigeRurl = '<?= $vtiger_url ?>';
            var VtigeREnableD = '<?= $enable_vtiger_integration ?>';
            var alert_enabled = '<?= $VU_alert_enabled ?>';
            var allow_alerts = '<?= $VU_allow_alerts ?>';
            var shift_logout_flag = 0;
            var vtiger_callback_id = 0;
            var agent_status_view = '<?= $agent_status_view ?>';
            var agent_status_view_time = '<?= $agent_status_view_time ?>';
            var agent_status_view_active = 0;
            var xfer_select_agents_active = 0;
            var even = 0;
            var VU_user_group = '<?= $VU_user_group ?>';
            var quick_transfer_button = '<?= $quick_transfer_button ?>';
            var quick_transfer_button_enabled = '<?= $quick_transfer_button_enabled ?>';
            var quick_transfer_button_orig = '';
            var quick_transfer_button_locked = '<?= $quick_transfer_button_locked ?>';
            var prepopulate_transfer_preset = '<?= $prepopulate_transfer_preset ?>';
            var prepopulate_transfer_preset_enabled = '<?= $prepopulate_transfer_preset_enabled ?>';
            var view_calls_in_queue = '<?= $view_calls_in_queue ?>';
            var view_calls_in_queue_launch = '<?= $view_calls_in_queue_launch ?>';
            var view_calls_in_queue_active = '<?= $view_calls_in_queue_launch ?>';
            var call_requeue_button = '<?= $call_requeue_button ?>';
            var no_hopper_dialing = '<?= $no_hopper_dialing ?>';
            var agent_dial_owner_only = '<?= $agent_dial_owner_only ?>';
            var agent_display_dialable_leads = '<?= $agent_display_dialable_leads ?>';
            var no_empty_session_warnings = '<?= $no_empty_session_warnings ?>';
            var script_width = '<?= $SDwidth ?>';
            var script_height = '<?= $SSheight ?>';
            var enable_second_webform = '<?= $enable_second_webform ?>';
            var no_delete_VDAC = 0;
            var manager_ingroups_set = 0;
            var external_igb_set_name = '';
            var recording_filename = '';
            var recording_id = '';
            var delayed_script_load = '';
            var script_recording_delay = '';
            var VDRP_stage = 'PAUSED';
            var VU_custom_one = '<?= $VU_custom_one ?>';
            var VU_custom_two = '<?= $VU_custom_two ?>';
            var VU_custom_three = '<?= $VU_custom_three ?>';
            var VU_custom_four = '<?= $VU_custom_four ?>';
            var VU_custom_five = '<?= $VU_custom_five ?>';
            var crm_popup_login = '<?= $crm_popup_login ?>';
            var crm_login_address = '<?= $crm_login_address ?>';
            var update_fields = 0;
            var update_fields_data = '';
            var campaign_timer_action = '<?= $timer_action ?>';
            var campaign_timer_action_message = '<?= $timer_action_message ?>';
            var campaign_timer_action_seconds = '<?= $timer_action_seconds ?>';
            var campaign_timer_action_destination = '<?= $timer_action_destination ?>';
            var timer_action = '';
            var timer_action_message = '';
            var timer_action_seconds = '';
            var timer_action_destination = '';
            var is_webphone = '<?= $is_webphone ?>';
            var WebPhonEurl = '<?= $WebPhonEurl ?>';
            var pause_code_counter = 1;
            var agent_call_log_view = '<?= $agent_call_log_view ?>';
            var scheduled_callbacks_alert = '<?= $scheduled_callbacks_alert ?>';
            var scheduled_callbacks_count = '<?= $scheduled_callbacks_count ?>';
            var tmp_vicidial_id = '';
            var agent_xfer_consultative = '<?= $agent_xfer_consultative ?>';
            var agent_xfer_dial_override = '<?= $agent_xfer_dial_override ?>';
            var agent_xfer_vm_transfer = '<?= $agent_xfer_vm_transfer ?>';
            var agent_xfer_blind_transfer = '<?= $agent_xfer_blind_transfer ?>';
            var agent_xfer_dial_with_customer = '<?= $agent_xfer_dial_with_customer ?>';
            var agent_xfer_park_customer_dial = '<?= $agent_xfer_park_customer_dial ?>';
            var EAphone_code = '';
            var EAphone_number = '';
            var EAalt_phone_notes = '';
            var EAalt_phone_active = '';
            var EAalt_phone_count = '';
            var conf_check_attempts = '<?= $conf_check_attempts ?>';
            var conf_check_attempts_cleanup = '<?= ($conf_check_attempts + 2) ?>';
            var blind_monitor_warning = '<?= $blind_monitor_warning ?>';
            var blind_monitor_message = '<?= $blind_monitor_message ?>';
            var blind_monitor_filename = '<?= $blind_monitor_filename ?>';
            var blind_monitoring_now = 0;
            var blind_monitoring_now_trigger = 0;
            var no_blind_monitors = 0;
            var uniqueid_status_display = '';
            var uniqueid_status_prefix = '';
            var custom_call_id = '';
            var api_dtmf = '';
            var api_transferconf_function = '';
            var api_transferconf_group = '';
            var api_transferconf_number = '';
            var api_transferconf_consultative = '';
            var api_transferconf_override = '';
            var api_parkcustomer = '';
            var API_selected_xfergroup = '';
            var API_selected_callmenu = '';
            if (VICIDiaL_web_form_address != '') {
                var custom_fields_enabled = 0;
            }
            else {
                var custom_fields_enabled = '<?= $custom_fields_enabled ?>';
            }
            var form_contents_loaded = 0;
            var enable_xfer_presets = '<?= $enable_xfer_presets ?>';
            var hide_xfer_number_to_dial = '<?= $hide_xfer_number_to_dial ?>';
            var Presets_HTML = '';
            var did_pattern = '';
            var did_id = '';
            var did_extension = '';
            var did_description = '';
            var closecallid = '';
            var xfercallid = '';
            var custom_field_names = '';
            var custom_field_values = '';
            var custom_field_types = '';
            var customer_3way_hangup_logging = '<?= $customer_3way_hangup_logging ?>';
            var customer_3way_hangup_seconds = '<?= $customer_3way_hangup_seconds ?>';
            var customer_3way_hangup_action = '<?= $customer_3way_hangup_action ?>';
            var customer_3way_hangup_counter = 0;
            var customer_3way_hangup_counter_trigger = 0;
            var customer_3way_hangup_dispo_message = '';
            var ivr_park_call = '<?= $ivr_park_call ?>';
            var qm_phone = '<?= $QM_PHONE ?>';
            var APIManualDialQueue = 0;
            var APIManualDialQueue_last = 0;
            var api_manual_dial = '<?= $api_manual_dial ?>';
            var manual_dial_call_time_check = '<?= $manual_dial_call_time_check ?>';
            var CloserSelecting = 0;
            var TerritorySelecting = 0;
            var WaitingForNextStep = 0;
            var AllowManualQueueCalls = '<?= $AllowManualQueueCalls ?>';
            var AllowManualQueueCallsChoice = '<?= $AllowManualQueueCallsChoice ?>';
            var call_variables = '';
            var focus_blur_enabled = '<?= $focus_blur_enabled ?>';
            var CBlinkCONTENT = '';
            var my_callback_option = '<?= $my_callback_option ?>';
            var per_call_notes = '<?= $per_call_notes ?>';
            var agent_lead_search = '<?= $agent_lead_search ?>';
            var agent_lead_search_method = '<?= $agent_lead_search_method ?>';
            var qm_phone_environment = '<?= $qm_phone_environment ?>';
            var LastCallCID = '';
            var LastCallbackCount = 0;
            var LastCallbackViewed = 0;
            var auto_pause_precall = '<?= $auto_pause_precall ?>';
            var auto_pause_precall_code = '<?= $auto_pause_precall_code ?>';
            var auto_resume_precall = '<?= $auto_resume_precall ?>';
            var trigger_ready = 0;
            var hide_gender = '<?= $hide_gender ?>';
            var manual_dial_cid = '<?= $manual_dial_cid ?>';
            var post_phone_time_diff_alert_message = '';

            var ResumeControl_auto_ON_HTML = "<td style='cursor:pointer' onClick=\"AutoDial_ReSume_PauSe('VDADready');\"><i class=\"fam-control-play\"></i></td><td onClick=\"AutoDial_ReSume_PauSe('VDADready');\" style='cursor:pointer'><a href='#' >Retomar</a></td></tr>";
            var ResumeControl_auto_OFF_HTML = "<td><i class=\"fam-control-play-blue\"></i></td><td>Retomar</td>";

            var PauseControl_auto_ON_HTML = "<td onclick=\"PauseCodeSelectContent_create();\" style='cursor:pointer'><i class=\"fam-control-stop\"></i></td><td onclick=\"PauseCodeSelectContent_create();\" style='cursor:pointer'><a href=\"#\" >Pausa</a></td></tr>";
            var PauseControl_auto_OFF_HTML = "<td><i class=\"fam-control-stop-blue\"></i></td><td>Pausa</td>";

            var HangupControl_auto_ON_HTML = "<td onclick='dialedcall_send_hangup();' style='cursor:pointer' ><i class=\"fam-control-eject\"/></td><td onclick='dialedcall_send_hangup();' style='cursor:pointer'><a href='#'>Desligar</a></td>";
            var HangupControl_auto_OFF_HTML = "<td><i class=\"fam-control-eject-blue\"/></td><td>Desligar</td>";

            var XferControl_auto_ON_HTML = "<td onclick=\"ShoWTransferMain('ON');\" style='cursor:pointer'><i class=\"fam-control-repeat\"/></td><td onclick=\"ShoWTransferMain('ON');\" style='cursor:pointer'><a href=\"#\">Transferir</a></td>";
            var XferControl_auto_OFF_HTML = "<td><i class=\"fam-control-repeat-blue\"/></td><td>Transferir</td>";

            var WaitControl_auto_OFF_HTML = "<td><i class=\"fam-control-pause-blue\"/></td><td>Espera</td>";

            var DiaLControl_manual_HTML = "<td><i class=\"fam-control-end\"></i></td><td><a href='#' onclick=\"ManualDialNext('','','','','','0');\">Seguinte</a></td>";
            var DiaLControl_manual_HTML_OFF = "<td><i class=\"fam-control-end-blue\"></i></td><td>Seguinte</td>";

            var redial_number = 0;
            var ultimo_callback = 0;

</script>


</head>
<?php $zi = 2; ?>
<body onLoad="all_refresh();"  onunload="BrowserCloseLogout();" id="ib">

    <div id="LoadingBox"><img src="/images/icons/big-loader.gif"/></div>


    <div class="container-fluid height100">

        <form name=vicidial_form id=vicidial_form style="height:80%" onSubmit="return false;">

            <div class="row-fluid height100">

                <div class="span3" id="toolbox">


                    <div class="grid">
                        <div class="grid-title" style="overflow:visible;">
                            <div class="pull-left">Info Box</div>
                            <div class="pull-right">
                                <div class="dropdown">
                                    <span class="btn icon-alone dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-cog"></i>
                                    </span>
                                    <div class="dropdown-menu">
                                        <ul>
                                            <li style="display:none"><a tabindex="-1" href="#" onclick="AgentsViewOpen('AgentViewSpan', 'open');
                return false;"><i class="icon-group"></i>Ver Colegas</a></li>
                                            <li id="callsinqueuelink">
                                                <?php
                                                if ($view_calls_in_queue > 0) {
                                                    if ($view_calls_in_queue_launch > 0) {
                                                        echo "<a tabindex=\"-1\" href=\"#\" onclick=\"show_calls_in_queue('HIDE');\"><i class=\"icon-road\"></i>Chamadas em espera</a>\n";
                                                    } else {
                                                        echo "<a tabindex=\"-1\" href=\"#\" onclick=\"show_calls_in_queue('SHOW');\"><i class=\"icon-road\"></i>Chamadas em espera</a>\n";
                                                    }
                                                }
                                                ?>
                                            </li>
                                            <li><a tabindex="-1" href="#" onclick="mpass(user);
                return false;"><i class="icon-key"></i>Alterar Password</a></li>
                                            <li><a tabindex="-1" href="#" onclick="NormalLogout();
                return false;"><i class="icon-signout"></i>Logout</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-condensed" id="statsTimer">
                            <tr>
                                <th>Campanha</th>
                                <td><?= $campaign_name ?></td>
                            </tr>
                            <tr>
                                <th>Hangup</th>
                                <td> <i class="icon-time"></i> <span id="stats_dead">00:00:00</span></td>
                            </tr>
                            <tr>
                                <th>Chamada</th>
                                <td> <i class="icon-time"></i> <span id="stats_incall">00:00:00</span></td>
                            </tr>
                            <tr>
                                <th>Feedback</th>
                                <td> <i class="icon-time"></i> <span id="stats_feedback">00:00:00</span></td>
                            </tr>
                            <tr <?= ($agent_display_dialable_leads == "N") ? "style='display:none'" : "" ?>>
                                <th>Contactos Disponiveis</th>
                                <td> <i class="icon-tasks"></i> <span id="dialableleadsspan"></span></td>
                            </tr>
                        </table>
                    </div>

                    <div class='grid' id="callsinqueuedisplay" style='overflow-x:auto'>
                        <div class="grid-title">
                            <div class="pull-left">Espera</div>
                            <div class="pull-right">
                                <span class="btn icon-alone" onclick="show_calls_in_queue('HIDE');" >
                                    <i class="icon-remove"></i>
                                </span>
                            </div>
                        </div>
                        <table class="table table-condensed table-mod table-striped">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Nome</th>
                                    <th><i class='icon-time'></i></th>
                                    <th><i class='icon-phone'></i></th>
                                </tr>
                            </thead>
                            <tbody id="callsinqueuelist"></tbody>
                        </table>
                    </div>

                    <div class="grid">
                        <div class="grid-title" style="overflow:visible;">
                            <div class="pull-left">Pausas</div>
                            <div class="pull-right">
                                <div class="dropdown">
                                    <span class="btn icon-alone dropdown-toggle" data-toggle="dropdown">
                                        <i class="icon-glass"></i>
                                    </span>
                                    <div class="dropdown-menu">
                                        <ul>
                                            <li id="PauseCodeLinkSpan"></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <table class="table table-condensed">
                            <tbody id='tpausa'>
                            </tbody>
                        </table>
                    </div>
                    


                    <div class="grid" id="AgentViewSpan">
                        <div class="grid-title">
                            <div class="pull-left">Colegas</div>
                            <div class="pull-right">
                                <span class="btn icon-alone" onclick="AgentsViewOpen('AgentViewSpan', 'close');" >
                                    <i class="icon-remove"></i>
                                </span>
                            </div>
                        </div>

                        <span id="AgentViewStatus" style="display:block;max-height:200px;overflow-y:auto;">
                            <table class="table table-condensed">
                                <tbody>

                                </tbody>
                            </table>
                        </span>

                    </div>

                </div>
                <div class="span7 height100">
                    <div id="NoneInSessionBox" class="alert alert-block alert-error">
                        <h4 class="alert-titleing">Não foi possível estabelecer a ligação.</h4>
                        <p>
                            <span id="NoneInSessionID"></span>
                        </p>
                        <p>
                            <a class="btn btn-danger" href="#" onClick="NoneInSessionCalL();
                return false;" >Voltar a fazer ligação</a>
                            <a class="btn" href="#" onClick="NoneInSessionOK();
                return false;">Ignorar</a>
                        </p>
                    </div>


                    <div class="alert alert-block" id="SysteMDisablEBoX">
                        <h4 class="alert-titleing">Existe um problema de sincronização com o servidor, avise o administrador de sistemas.</h4>
                        <p><a href="#" class="btn" onClick="hideDiv('SysteMDisablEBoX');
                return false;">Voltar</a></p>    
                    </div>

                    <div class="alert alert-block alert-error" id="AgenTDisablEBoX">
                        <h4 class="alert-titleing">A sua sessão foi terminada.</h4>
                        <p>
                            <a href="#" class="btn btn-danger" onClick="LogouT('DISABLED');
                return false;">Logout</a>
                            <a href="#" class="btn" onClick="hideDiv('AgenTDisablEBoX');
                return false;">Voltar</a>
                        </p>
                    </div>

                    <span class="alert alert-info alert-block "  id="CustomerGoneBox">
                        <h4 class="alert-titleing">O Cliente desligou:</h4>
                        <p id="CustomerGoneChanneL"></p>
                        <p id="test_custchannellive"></p>
                        <p id="test_lastcustchannel"></p>
                        <p id="test_no_empty_session_warnings"></p>
                        <p>
                            <a href="#" class="btn btn-danger" onClick="CustomerGoneHangup();
                return false;">Desligar e Preencher FeedBack</a>
                            <a href="#" class="btn" onClick="CustomerGoneOK();
                return false;">Voltar</a>
                        </p>
                    </span>
                   
                        <ul id="Main-tabs" class="nav nav-tabs tabs-main">
                            <li class="active"><a href="#MainTable" data-toggle="tab" id="tab-MainTable" >Dados do Cliente</a></li>
                            <li class=""><a href="#FormPanel" data-toggle="tab" id="tab-FormPanel" >Script</a></li>
                            <li class=""><a href="#LeadLog" data-toggle="tab" onclick="leadlog();" id="tab-FormPanel" >Histórico</a></li>
                     <?php
                    if ($curClient === 'necomplus') { ?>        
                            <li class=""><a href="#infoPi" data-toggle="tab" onclick="getPi();" id="tab-FormPanel" >Info Pi</a></li>
                    <? } ?>        
                        </ul>
                    
                        
                    

                    <div class="tab-content tabs-main-content height100">

                        <div id="FormPanel" class="tab-pane tab-overflow-main height100">
                            <iframe src="./vdc_form_display.php?lead_id=&list_id=&stage=WELCOME" style="width:100%;min-height: 70%" scrolling="auto" frameborder="0" allowtransparency="true" id="vcFormIFrame" name="vcFormIFrame" > </iframe>
                            <div class="clear"></div>
                        </div>

                        <div id="LeadLog" class="tab-pane tab-overflow-main">
                            <table class="table table-mod table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Operador</th>
                                        <th>Tempo (seg.)</th>
                                        <th>Estado</th>
                                        <th>Nº</th>
                                        <th>Campanha</th>
                                        <th title='Entrada Ou Saida'>IO</th>
                                        <th>Desligou</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        
                        <div id="infoPi" class="tab-pane tab-overflow-main">
                            <table class="table table-mod table-striped table-bordered" id="clientePi">
                                <tbody>
                                    <tr>
                                        <td>Nome:</td><td id="nomePi"></td>
                                    </tr>
                                    <tr>
                                        <td>Codigo:</td><td id="codigoPi"></td>
                                    </tr>
                                    <tr>
                                        <td>Morada:</td><td id="moradaPi"></td>
                                    </tr>
                                    <tr>
                                        <td>Contacto:</td><td id="contactoPi"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <br />
                            <br />
                            <table class="table table-mod table-striped table-bordered" id="tpaPi">
                                <thead>
                                    <th>ID TPA</th>
                                    <th>Número de Série</th>
                                </thead>    
                                <tbody>
                                   
                                </tbody>
                            </table>
                            <br />
                            <br />
                            <table class="table table-mod table-striped table-bordered" id="ordensPi">
                                <thead>
                                    <th>Estado</th>
                                    <th>Nº Ordem</th>
                                    <th>Tipo de Ordem</th>
                                </thead>    
                                <tbody>
                                   
                                </tbody>
                            </table>
                        </div>

                        <div id="MainTable" class="tab-pane active tab-overflow-main">

                            <input type="hidden" name="lead_id" id="lead_id" value="" />
                            <input type="hidden" name="entry_list_id" id="entry_list_id" value="" />
                            <input type="hidden" name="called_count" id="called_count" value="" />

                            <input type="hidden" name="gmt_offset_now" id="gmt_offset_now" value="" />

                            <input type="hidden" name="uniqueid" id="uniqueid" value="" />
                            <input type="hidden" name="callserverip" id="callserverip" value="" />
                            <input type="hidden" name="SecondS" id="SecondS" value="" />

                            <input type="hidden" name="gender_list" id="gender_list" value="" />

                            <div id="MainPanelCustInfo">


                                <input type=hidden name=call_notes id=call_notes value= /><span id=CallNotesButtons></span>

                                <div class="grid-content">

                                    <div id="MainStatuSSpan" class="alert alert-info" ></div>


                                    <div style="z-index:1001;" id="CBcommentsBox">
                                        <table class="table table-bordered" >
                                            <tr>
                                                <th colspan="4">Informação sobre call-backs antigos: <button class="close" type="button" onClick="CBcommentsBoxhide();" >×</button></th>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p>
                                                        <span class="label label-success">Ultima chamada:</span>
                                                        <span id="CBcommentsBoxA"></span>
                                                    </p>
                                                </td>
                                                <td>
                                                    <p>
                                                        <span class="label label-success">CallBack:</span>
                                                        <span id="CBcommentsBoxB"></span>
                                                    </p>
                                                </td>
                                                <td>
                                                    <p>
                                                        <span class="label label-success">Agente:</span>
                                                        <span id="CBcommentsBoxC"></span>
                                                    </p>
                                                </td>
                                                <td>
                                                    <p>
                                                        <span class="label label-success">Comentários:</span>
                                                        <span id="CBcommentsBoxD"></span>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>

                                    <span id='phone_numberDISP' style='display:none'></span> 
                                    <div class="form-horizontal">

                                        <?php
                                        for ($index = 0; $index < count($fields_order); $index++) {
                                            if ($fields_order[$index][0] == "comments") {
                                                if ($fields_order[$index][1] == 0) {
                                                    ?>
                                                    <input type='hidden' name='comments' id='comments' value='' />
                                                <?php } else { ?>
                                                    <div class="control-group">
                                                        <label class="control-label"><?= $fields_order[$index][3] ?>:</label>
                                                        <div class="controls">
                                                            <textarea <?= $fields_order[$index][2] ?> class="span" name='<?= $fields_order[$index][0] ?>' id='<?= $fields_order[$index][0] ?>'></textarea>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                if ($fields_order[$index][1] == 0) {
                                                    ?>
                                                    <input type='hidden' name='<?= $fields_order[$index][0] ?>' id='<?= $fields_order[$index][0] ?>' value='' />
                                                <?php } else { ?>
                                                    <div class="control-group">
                                                        <label class="control-label"><?= $fields_order[$index][3] ?>:</label>
                                                        <div class="controls">
                                                            <?= (($fields_order[$index][0] == "address1") ? "<div style=\"display:block;\" class=\"input-append\">" : "") ?>
                                                            <input <?= $fields_order[$index][2] ?> type=text name='<?= $fields_order[$index][0] ?>' id='<?= $fields_order[$index][0] ?>' class="<?= ($fields_order[$index][0] == "address1") ? "span9" : "span" ?>">
                                                            <?= (($fields_order[$index][0] == "address1") ? "<span onclick=\"showDiv('pesquisa_morada');\" class=\"btn\" ><i class=\"icon-map-marker\"></i> Pesquisar</span></div>" : "") ?>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>	
                        </div>
                    </div>
                </div>
                <div class="span2">

                    <div class="grid">
                        <div class="grid-title">
                            <div class="pull-left" title="Controlo de Chamadas">Controlo</div>
                            <span style='display:none;' id="dataHeader"></span>
                            <div class="pull-right">
                                <div class="grid-title-icon">
                                    <i class="icon-phone-sign"></i>
                                </div>
                            </div>
                        </div>
                        <table class="table table-condensed" >
                            <tr id="DiaLControl" <?= ($auto_dial_level > 0 and $dial_method != "INBOUND_MAN") ? "style='display:none'" : ""; ?>>
                                <td><i class="fam-control-end"></i></td>
                                <td>
                                    <a href="#" onClick="ManualDialNext('', '', '', '', '', '0');">Marcar Seguinte</a>
                                </td>
                            </tr>
                            <tr id="ResumeControl" <?= ($auto_dial_level < 1) ? "style='display:none'" : ""; ?>>
                                <td><i class="fam-control-play"></i></td>
                                <td><a href="#" onClick="AutoDial_ReSume_PauSe('VDADready');">Retomar Chamadas</a></td>
                            </tr>
                            <tr id="PauseControl" <?= ($auto_dial_level < 1) ? "style='display:none'" : ""; ?>>
                                <td><i class="fam-control-stop"></i></td>
                                <td><a href="#" onClick="AutoDial_ReSume_PauSe('VDADpause');">Pausar Chamadas</a></td>
                            </tr>
                            <tr id="HangupControl">
                                <td><i class="fam-control-eject-blue"></i></td>
                                <td>Desligar</td>
                            </tr>


                            <tr id='ParkControl'>
                                <td><i class="fam-control-pause-blue"></i></td>
                                <td>Espera</td>
                            </tr>
                            <tr id='XferControl' <?= (!$agent_allow_transfers) ? "style='display:none;'" : "" ?>>
                                <td><i class="fam-control-repeat-blue"></i></td>
                                <td>Transferir</td>
                            </tr>
                            <tr <?= (!$agent_allow_dtmf) ? "style='display:none;'" : "" ?>>
                                <td><i class="fam-sitemap-color"></i></td>
                                <td>
                                    <a href="#" id="SendDTMF" data-toggle="popover" data-placement="left" data-original-title="Digite o nº" data-content="<div class='input-append'><input type='text'  name='conf_dtmf' id='conf_dtmf' class='span2'  maxlength='10' /><span class='btn btn-primary' onclick='SendConfDTMF(session_id);return false;'>Enviar</span></div>" >DTMF</a> 
                                </td>
                            </tr>   

                            <tr onClick="redial();" style='cursor:pointer'>
                                <td><i class="fam-arrow-undo"></i></td>
                                <td class='btn-link'>Re-Marcar</td>
                            </tr>  

                            <tr id='search' onClick="OpeNSearcHForMDisplaYBox();" style='cursor:pointer;<?= ($agent_lead_search == "DISABLED") ? "display:none;" : "" ?>'>
                                <td><i class="fam-zoom-in"></i></td>
                                <td class='btn-link'>Procurar</td>
                            </tr>
                            <tr id='mdial' onClick="NeWManuaLDiaLCalL('NO');" style='cursor:pointer'>
                                <td><i class="fam-telephone-delete"></i></td>
                                <td>
                                    <span id="MDstatusSpan">
                                        <a href="#" >Manual</a>
                                    </span>
                                </td>
                            </tr>
                            <tr id='CallbacksButtons' style='cursor:pointer' data-toggle="tooltip" title="Verdes: prontos. Vermelhos: expirados.">
                                <td><i class="fam-calendar"></i></td>
                                <td>
                                    <span id="CBstatusSpan" >Callbacks</span> 
                                </td>
                            </tr>
                            <tr id='confirm_feedback_log_button' style='cursor:pointer' data-toggle="tooltip" title="Log do feedback">
                                <td><i class="fam-book-go"></i></td>
                                <td>
                                    <span class="btn-link" onclick="confirm_feedback_load();">Vendas não confirmadas</span> 
                                </td>
                            </tr>
                            <?php if ($campaign_allow_inbound == 'Y') { ?>
                                <tr style='cursor:pointer' onclick="OpeNGrouPSelectioN();">
                                    <td><i class="fam-group-add"></i></td>
                                    <td class='btn-link'>Inbound</td>
                                </tr>
                            <?php } ?>

                            <?php if ($on_hook_agent == "Y") { ?>
                                <tr style='cursor:pointer' onclick="NoneInSessionCalL();">
                                    <td><i class="fam-telephone-add"></i></td>
                                    <td class='btn-link'>Ligação</td>
                                </tr>
                            <?php } ?>

                            <tr id="CallHistoryStart" style="cursor:pointer">
                                <td> <i class="fam-book-delete"></i></td>
                                <td class='btn-link'>Histórico</td>
                            </tr>
                        </table>

                        <?php
                        $query = "SELECT url,imgpath,label FROM sips_agent_links where grupo='$VU_user_group';";
                        $result = mysql_query($query, $link);

                        if (mysql_num_rows($result) > 0) {


                            echo "<div style='border-top:2px solid #e8edff;'>
		<table>";
                            while ($row = mysql_fetch_assoc($result)) {

                                echo "<tr>
				<td>
					<a target='novapagina' href='" . $row[url] . "'>
						<img style='vertical-align:middle;' src='" . $row[imgpath] . "'>
						      " . $row[label] . "
					</a>
				
        <div id='dialog-email'>
            <div class='div-title'>Dados do Email</div>
            <table>
            <tr>
            <td>Morada de Email: </td><td class='td-text'><input style='width:200px;' id='email-address' value=''> </td>
            </tr>
             <tr class='spacer8'></tr>
            <tr>
            <td>Destinatário: </td><td class='td-text'><input style='width:200px;' id='email-name' value=''> </td>
            </tr>
            
            <tr class='spacer16'></tr>
            <tr><td colspan='2' style='text-align:left'><button id='send-email'>Enviar Email</button></td></tr>
            <tr class='spacer16'></tr>
            <tr><td id='email-log'></td></tr>
            </table>    
        </div>
        <style>
        .div-title { width:100%; border-bottom: 1px solid #DDDDDD; font-weight:bold; margin:8px 0 10px 0; }
        .td-text { text-align: right; padding:0 6px 0 0; }
        .td-indent { padding-left:12px; }
        .td-text-justify { text-align: justify; padding:0 12px 0 6px; vertical-align:top; }
        .td-text-justify-snippet { font-weight:700; }
        .td-text-justify-link { color:#0073EA; cursor:pointer; } 
        .td-subitem { padding: 0 0 0 12px; }
        .td-icon { width: 15px; }
        .td-image-help { margin-top:0px; cursor:pointer; }
        .td-image-edit { margin-top:0px; cursor:pointer; }
        .spacer16 { height: 16px; }
        .spacer8 { height: 8px; }
        </style>
        
        <script>
            $('#dialog-email').dialog({ 
                title: ' <span style=font-size:13px; color:black>Envio de Email</span> ',
                autoOpen: false,
                height: 300,
                width: 450,
                resizable: false,
                buttons: {
                    'Fechar' : function() { $(this).dialog('close'); }
                    },
                open : function() { $('button').blur();  }
                });
            $('#email-link').click(function() {
                var email_lead_id = $('#lead_id').val();
                $('#email-log').html('');
                $('#email-address').val('');
                $.ajax({
                        type: 'POST',
                        url: '../client_files/energy/email/_requests.php',
                        data: {action: 'get_email', lead_id: email_lead_id },
                        success: function(data, textStatus, jqXHR) { $('#email-address').val(data); $('#email-name').val($('#first_name').val())  }
                        });
            $('#dialog-email').dialog( 'open' );
            });
            
            $('#send-email').button().click(function(){
            var email_address = $('#email-address').val();
            var email_name = $('#email-name').val();
            var sent_by_user = user;
            var sent_by_campaign = campaign;
            $('#email-log').html('<img src=/images/icons/ajax-loader.gif>')
            $.ajax({
                    type: 'POST',
                    url: '../client_files/energy/email/_requests.php',
                    data: {action: 'send_email', email_address: email_address, email_name: email_name, sent_by_user: sent_by_user, sent_by_campaign: sent_by_campaign},
                    success: function(data, textStatus, jqXHR) { $('#email-log').html(data);  }
                    });
            
            
            });
    
        </script>   
                     
                </td>
			</tr>";
                            }


                            echo "</table>
		</div>";
                        }
                        ?>

                    </div> 


                    <div class="grid-transparent">
                        <? if (isset($curlogo) && $curLogo != "") { ?>
                            <img class="left" src='<?= "../$curlogo"; ?>' id="menu-hide" > 
                        <? } else { ?>
                            <img class="left" src='/images/pictures/go_logo_15.png' id='menu-hide' >
                        <?php } ?>
                        <div class='clear'></div>
                    </div>

                </div>

            </div>


            <div id="CallHistoryDialog" style="display:none">
                <div class="text-center">
                    <button id="change-lostcalls" class="btn btn-small">Chamadas Perdidas</button>
                    <button id="change-inbound" class="btn btn-small">Chamadas de Inbound</button>
                    <button id="change-manual" class="btn btn-small">Chamadas Manuais</button>
                    <button id="change-outbound" class="btn btn-small">Chamadas de Outbound</button>
                </div>
                <div> 
                    <table id='table-CallHistory' class="table table-mod-2 table-condensed">
                        <thead></thead>
                        <tbody></tbody>
                        <tfoot></tfoot>
                    </table>
                </div>		
            </div>
            <div id="dialog-agent-msg" style="display:none;"></div>

            <div class="grid-agent" style="width: 1000px; margin-left: -500px; display: none" id="marquee-msg"></div>

            <input type="hidden" name="curlogo" value="<?= $curLogo; ?>" />
            <input type="hidden" name="extension" id="extension" />
            <input type="hidden" name="custom_field_values" id="custom_field_values" value="" />
            <input type="hidden" name="FORM_LOADED" id="FORM_LOADED" value="0" />
            <input type="hidden" name="custdatetime" id=custdatetime value="">
            <input type="hidden" name="callchannel" id=callchannel value="">
            <input type="hidden" name="campaign_id" id=campaign_id value="<?= $VD_campaign; ?>" />



            <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="Header">
                <TABLE style='display:none'>
                    <TR>
                        <TD>
                            <span id=status ></span>
                        </TD>
                        <TD>
                            <?php
                            if ($territoryCT > 0) {
                                echo "<a href=\"#\" onclick=\"OpeNTerritorYSelectioN();return false;\">TERRITORIES</a>   \n";
                            }
                            ?>
                            <?php
                            if ($INgrpCT > 0) {
                                echo "<a href=\"#\" onclick=\"OpeNGrouPSelectioN();return false;\">GROUPS</a>   \n";
                            }
                            ?>
                        </TD>
                    </TR>
                </TABLE>
            </span> 



            <span style="position:absolute;left:0px;top:13px;z-index:<?= ++$zi ?>; display:none" id="Tabs">
                <table>
                    <tr>
                        <td><a href="#" onClick="MainPanelToFront('NO');"><img src="./images/vdc_tab_vicidial.gif" alt="MAIN"/></a></td>
                        <?php
                        if ($custom_fields_enabled > 0) {
                            echo "<td><a href=\"#\" onclick=\"FormPanelToFront();\"><img src=\"./images/vdc_tab_form.gif\" alt=\"FORM\" width=\"67px\" height=\"30px\" border=\"0\" /></a></td>\n";
                        }
                        ?>
                    </tr>
                </table>
            </span>


            <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="WelcomeBoxA">
                <table>
                    <tr>
                        <td align="center">
                            <span id="WelcomeBoxAt">Agent Screen</span>
                        </td>
                    </tr>
                </table>
            </span>


            <span style="display:none;position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="debugbottomspan">

            </span>

            <span style="position:absolute;left:3px;top:380px;z-index:1000; width:1200px; " id="ManuaLDiaLButtons"  valign="center">
                <span style="position:absolute; left:0px; top:50px; z-index:<?= ++$zi ?>; width:1200px; display:none;" valign="center" id="MaiNfooterspan">
                    <span id="blind_monitor_notice_span" >
                        <b>   
                            <span id="blind_monitor_notice_span_contents"></span>
                        </b>

                    </span>
                    <table id="MaiNfooter" >
                        <TR>
                            <td>
                                <IMG SRC="./images/agc_live_call_OFF.gif" NAME=livecall ALT="Live Call" WIDTH=60 HEIGHT=60 BORDER=0>

                                <a href='#' class="btn" onclick="NormalLogout();" />Logout</a>

                            </td>
                            <td>
                                <span id="MDstatusSpan">
                                    <a href="#" class="btn" onClick="NeWManuaLDiaLCalL('NO');
                return false;">Fazer Chamada Manual</a>
                                </span>
                                <a href="#" class="btn" onClick="NeWManuaLDiaLCalL('FAST');
                return false;"></a>
                                <br />

                                <span style="z-index:1000;" id="CallLogButtons">
                                    <span id="CallLogLinkSpan">
                                        <a href="#" class="btn" onClick="VieWCalLLoG();
                return false;">Ver o log de chamadas</a>
                                    </span>
                                </span>

                                <span style="z-index:<?= ++$zi ?>;" id="AgentViewLinkSpan">

                                    <span id="AgentViewLink">
                                        <a href="#" class="btn" onClick="AgentsViewOpen('AgentViewSpan', 'open');
                return false;">Ver colegas +</a>
                                    </span>
                                </span>




                                <span id=AgentStatusCalls style="display:none"></span>

                                <span id="RecorDControl" style="display:none">
                                    <a href="#" class="btn" onClick="conf_send_recording('MonitorConf', session_id, '');
                return false;">
                                        <img src="./images/vdc_LB_startrecording_OFF.gif" border="0" alt="Start Recording" />
                                    </a>
                                </span>
                            </td>

                            <td >
                                <span id="busycallsdebug"></span>
                                <span id="RecorDingFilename" style="display:none"></span>
                                <span id="RecorDID" style="display:none"></span>

                                <span style="z-index:<?= ++$zi ?>;" id="AgentMuteSpan" ></span><br><br>
                                <span style="z-index:<?= ++$zi ?>;" id="VolumeControlSpan" >
                                    <span id="VolumeUpSpan" >
                                        <img src="./images/vdc_volume_up_off.gif" border="0" />
                                    </span>
                                    <br />
                                    <span id="VolumeDownSpan" >
                                        <img src="./images/vdc_volume_down_off.gif" border="0" />
                                    </span>
                                </span>

                                <span id="DiaLLeaDPrevieW" style="display:none"> <input type="checkbox" name="LeadPreview" size="1" value="0" checked="checked" /> Preview Chamada<br /></span>
                                <span id="DiaLDiaLAltPhonE" style="display:none"> <input type="checkbox" name="DiaLAltPhonE" size="1" value="0" /> Marcar Alternativo<br /></span>

                                <br>

                                <span style="z-index:<?= ++$zi ?>;" id="SecondSspan">
                                    Duração da Chamada: 
                                    <span id="SecondSDISP"></span>
                                </span>
                                <br>
                                Duração da Espera: <span id="ParkCounterSpan">  </span>
                                <br />

                            </td>
                        </tr>
                    </table>

                    <br>
                    <table style="display:none">
                        <tr>
                            <td >
                                <span id="ManualQueueNotice" style="display:none"></span>
                                <span id="ManualQueueChoice" style="display:none"></span>

                                <span style="display:none;" id="WebFormSpan">
                                    <img src="./images/vdc_LB_webform_OFF.gif" border="0" alt="Web Form" /></span>
                                <?php
                                if ($enable_second_webform > 0) {
                                    echo "<span style=\"display:none;\" id=\"WebFormSpanTwo\"><img src=\"./images/vdc_LB_webform_two_OFF.gif\" border=\"0\" alt=\"Web Form 2\" /></span>\n";
                                }
                                ?>
                                <br />



                                <?php
                                if (($ivr_park_call == 'ENABLED') or ($ivr_park_call == 'ENABLED_PARK_ONLY')) {
                                    echo "<span id=\"ivrParkControl\"><img src=\"./images/vdc_LB_ivrparkcall_OFF.gif\" alt=\"IVR Park Call\" /></span><br />\n";
                                } else {
                                    echo "<span id=\"ivrParkControl\"></span>\n";
                                }
                                ?>

                                <?php
                                if ($quick_transfer_button_enabled > 0) {
                                    echo "<span  id=\"QuickXfer\"><img src=\"./images/vdc_LB_quickxfer_OFF.gif\" alt=\"Quick Transfer\" /></span><br />\n";
                                }
                                ?>

                                <span id="ReQueueCall"></span>

                            <td colspan="2"></td>
                        </tr>

                        <tr>
                            <td rowspan="5" valign="top" width="288">



                                <span id="post_phone_time_diff_span">
                                    <b>
                                        <font color="red">
                                        <span id="post_phone_time_diff_span_contents"></span>
                                        </font>
                                    </b>

                                </span>
                                <span id=sessionIDspan style="display:none"></span> 

                                <INPUT TYPE=HIDDEN NAME=extension2> <span id="busycallsdisplay"></span> <span id="CusTInfOSpaN"></span>
                            </td>
                        </tr>

                        <tr>
                            <td colspan=3>
                                <span id="outboundcallsspan"></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan=3>
                                <span id="AgentAlertSpan">
                                    <?php
                                    if ((ereg('ON', $VU_alert_enabled)) and ($AgentAlert_allowed > 0)) {
                                        echo "<a href=\"#\" onclick=\"alert_control('OFF');return false;\">Alert is ON</a>";
                                    } else {
                                        echo "<a href=\"#\" onclick=\"alert_control('ON');return false;\">Alert is OFF</a>";
                                    }
                                    ?>
                                </span>
                            </td>
                        </tr>
                    </table>
                </span>

                <?php if (($HK_statuses_camp > 0) && ( ($user_level >= $HKuser_level) or ($VU_hotkeys_active > 0) )) { ?>
                    <span style="position:absolute;left:50%;top:0px;z-index:<?= ++$zi ?>;" id="hotkeysdisplay">
                        <a href="#" onMouseOver="HotKeys('ON')"><img src="./images/vdc_XB_hotkeysactive_OFF.gif" alt="HOT KEYS INACTIVE" /></a>
                    </span>
                <?php } ?>

            </span>






            <span style="position:absolute;left:35px;top:0px;z-index:<?= ++$zi ?>;" id="AgentStatusSpan">
                Your Status: <span id="AgentStatusStatus"></span> <br />Calls Dialing: <span id="AgentStatusDiaLs"></span>
            </span>

            <span style="position:absolute;left:50%;top:0px;z-index:<?= ++$zi ?>;" id="AgentMuteANDPreseTDiaL">
                <?php
                if ($PreseT_DiaL_LinKs) {
                    echo "<a href=\"#\" onclick=\"DtMf_PreSet_a_DiaL();return false;\"><font class=\"body_tiny\">D1 - DIAL</font></a>\n";
                    echo " <br /> \n";
                    echo "<a href=\"#\" onclick=\"DtMf_PreSet_b_DiaL();return false;\"><font class=\"body_tiny\">D2 - DIAL</font></a>\n";
                } else {
                    echo "<br />\n";
                }
                ?>
                <br /><br />  <br />
            </span>




            <?php
            $zi++;
            if ($webphone_location == 'bar') {
                echo "<span style=\"position:absolute;left:0px;top:46px;height:" . $webphone_height . "px;width=" . $webphone_width . "px;overflow:hidden;z-index:$zi;background-color:$SIDEBAR_COLOR;\" id=\"webphoneSpan\"><span id=\"webphonecontent\" style=\"overflow:hidden;\">$webphone_content</span></span>\n";
            } else {
                echo "<span style=\"position:absolute;left:" . $SBwidth . "px;top:15px;height:500px;overflow:scroll;display:none;z-index:$zi;background-color:$SIDEBAR_COLOR;\" id=\"webphoneSpan\">
    <span id=\"webphonecontent\">$webphone_content</span></span>\n";
            }
            ?>




            <?php
            if ($is_webphone == 'Y') {
                ?>
                <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="webphoneLinkSpan">
                    <table cellpadding="0" cellspacing="0" border="0" width="120px">
                        <tr>
                            <td align="right">
                                <span id="webphoneLink">
                                    <a href="#" onClick="webphoneOpen('webphoneSpan', 'close');
                    return false;">Ver Webphone -</a>
                                </span>
                            </td>
                        </tr>
                    </table>
                </span>
                <?php
            }
            ?>


            <span style="display:none;position:absolute;left:200px;top:0px;z-index:<?= ++$zi ?>;" id="ScriptPanel">
                <table>
                    <tr>
                        <td align="left" valign="top">
                            <div class="scroll_script" id="ScriptContents" style="padding: 3px 3px 3px 3px; border:#000000 solid 1px; display:none;">AGENT SCRIPT</div>
                        </td>
                    </tr>
                </table>
            </span>

            <span style="position:absolute;left:50%;top:0px;z-index:<?= ++$zi ?>;" id="ScriptRefresH">
                <a href="#" onClick="RefresHScript();"><font class="body_small">refresh</font></a>
            </span>



            <span style="position:absolute;left:50%;top:0px;z-index:<?= ++$zi ?>;" id="FormRefresH">
                <a href="#" onClick="FormContentsLoad();"><font class="body_small">refresh</font></a>
            </span>


            <div class="grid-agent" style="width:500px;margin-left:-250px;margin-top:-100px;z-index:<?= ++$zi ?>;" id="TransferMain">
                <div class="modal-header">
                    <button class="close" type="button" onClick="ShoWTransferMain('OFF', 'YES');" >×</button>
                    Transferencia de chamada - Conferencia
                </div>
                <span id="XfeRDiaLGrouPSelecteD"></span>  <span id="XfeRCID"></span>
                <div class="modal-body">
                    <table>
                        <tbody>
                            <tr>
                                <td align="left" colspan="3">
                                    <span  style="display:none">
                                        <span id="XfeRGrouPLisT">
                                            <select size="1" name="XfeRGrouP" id="XfeRGrouP" class="cust_form" onChange="XferAgentSelectLink();">
                                                <option>-- SELECT A GROUP TO SEND YOUR CALL TO --</option>
                                            </select>
                                        </span>
                                        <span id="LocalCloser"><img src="./images/vdc_XB_localcloser_OFF.gif" border="0" alt="LOCAL CLOSER" style="vertical-align:middle" /></span>
                                    </span>	
                                </td>
                                <td>
                                    <span id="HangupXferLine"><img src="/images/icons/telephone_delete_32.png" border="0" alt="Hangup Xfer Line" style="vertical-align:middle" />Desligar o solicitado</span>
                                </td>
                            </tr>

                            <tr>
                                <td align="left" colspan="2">
                                    <span style="display:none">
                                        <label for="xferlength">Segundos</label><input type="text" size="2" name="xferlength" id="xferlength" maxlength="4" style="width:30px" readonly />
                                        <label for="xferchannel">Canal</label><input type="text" size="12" name="xferchannel" id="xferchannel" maxlength="200" style="width:100px" readonly />
                                    </span>	
                                </td>
                                <td align="left">
                                    <span id="consultative_checkbox"  style="display:none"><input type="checkbox" name="consultativexfer" id="consultativexfer" size="1" value="0"><label for="consultativexfer" > CONSULTATIVE </label></span>
                                </td>
                                <td align="left">
                                    <span style="display:none"><span id="HangupBothLines"><a href="#" onClick="bothcall_send_hangup();
                return false;"><img src="./images/vdc_XB_hangupbothlines.gif" border="0" alt="Hangup Both Lines" style="vertical-align:middle" /></a></span></span>
                                </td>
                            </tr>

                            <tr>
                                <td align="left" colspan="2">
                                    <div  style="display:inline-block;width:100px;height: 28px;margin-right:10px;"><p>Nº a Chamar</p></div>
                                    <?php if ($hide_xfer_number_to_dial == 'ENABLED') { ?>
                                        <input type="hidden" name="xfernumber" id="xfernumber" value="<?= $preset_populate ?>" />
                                    <?php } else { ?>
                                        <input type="text" size="20" name="xfernumber" id="xfernumber" maxlength="25" style="width:100px" value="<?= $preset_populate ?>" />
                                    <?php } ?>
                                    <span id="agentdirectlink"><font class="body_small_bold"><a href="#" onClick="XferAgentSelectLaunch();
                return false;">AGENTS</a></font></span>
                                    <input type="hidden" name="xferuniqueid" id="xferuniqueid" />
                                    <input type="hidden" name="xfername" id="xfername" />
                                    <input type="hidden" name="xfernumhidden" id="xfernumhidden" />
                                </td>
                                <td align="left">
                                    <span id="dialoverride_checkbox"  style="display:none"><input type="checkbox" name="xferoverride" id="xferoverride" size="1" value="0"><label for="xferoverride" style="display: inline;"> DIAL OVERRIDE</label></span>
                                </td>
                                <td align="left" style="height: 36px; width: 180px;">
                                    <span style="display:none;float:left;" id="Leave3WayCall">
                                        <a href="#" onClick="leave_3way_call('FIRST');
                return false;"><img src="/images/icons/telephone_go_32.png" border="0" alt="LEAVE 3-WAY CALL" style="vertical-align:middle" />Tranferir a Chamada</a>
                                    </span>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="3"></td>
                                <td style="height: 36px; width: 180px;">
                                    <span style="display:none">
                                        <span id="DialBlindTransfer"><img src="./images/vdc_XB_blindtransfer_OFF.gif" border="0" alt="Dial Blind Transfer" style="vertical-align:middle" /></span>
                                        <span id="DialWithCustomer">
                                            <a href="#" onClick="SendManualDial('YES');
                return false;"><img src="./images/vdc_XB_dialwithcustomer.gif" border="0" alt="Dial With Customer" style="vertical-align:middle" /></a>
                                        </span>
                                    </span>
                                    <span style="float:left;" id="ParkCustomerDial">
                                        <a href="#" onClick="xfer_park_dial();
                return false;"><img src="/images/icons/telephone_add_32.png" alt="Park Customer Dial" style="vertical-align:middle" />Solicitar Transferência</a>
                                    </span>

                                    <?php
                                    if ($enable_xfer_presets == 'ENABLED') {
                                        ?>
                                        <span style="background-color: <?= $MAIN_COLOR ?>" id="PresetPullDown">
                                            <a href="#" onClick="generate_presets_pulldown();
                    return false;"><img src="./images/vdc_XB_presetsbutton.gif" border="0" alt="Presets Button" style="vertical-align:middle" /></a>
                                        </span>
                                        <?php
                                    } else {
                                        ?>
                                        <span style="display: none;">
                                            <a href="#" onClick="DtMf_PreSet_a();
                    return false;">D1</a> 
                                            <a href="#" onClick="DtMf_PreSet_b();
                    return false;">D2</a>
                                            <a href="#" onClick="DtMf_PreSet_c();
                    return false;">D3</a>
                                            <a href="#" onClick="DtMf_PreSet_d();
                    return false;">D4</a>
                                            <a href="#" onClick="DtMf_PreSet_e();
                    return false;">D5</a>
                                        </span>
                                        <?php
                                    }
                                    ?>
                                    <span style="display: none;">
                                        <span style="background-color: <?= $MAIN_COLOR ?>" id="DialBlindVMail"><img src="./images/vdc_XB_ammessage_OFF.gif" border="0" alt="Blind Transfer VMail Message" style="vertical-align:middle" /></span>
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

            <span style="position:absolute;left:0px;top:0px;width:100px;height:200px;overflow:scroll;z-index:<?= ++$zi ?>;" id="AgentXferViewSpan">
                <center>
                    Available Agents Transfer: <span id="AgentXferViewSelect"></span>
                </center>

            </span>


            <span style="position:absolute;left:5px;top:0>px;z-index:<?= ++$zi ?>;" id="HotKeyActionBox">
                <table>
                    <tr bgcolor="#FFEEBB">
                        <td height="70px"> Lead Dispositioned As: <br /><br /><center><span id="HotKeyDispo"> - </span></center>
                    </td>
                    </tr>
                </table>
            </span>

            <span style="position:absolute;left:0px;top:80px;z-index:<?= ++$zi ?>;" id="HotKeyEntriesBox">
                <table>
                    <tr>
                        <td height="20px"> Disposition Hot Keys: </td>
                    </TR>
                    <TR>
                        <td height="10">
                            When active, simply press the keyboard key for the desired disposition for this call. The call will then be hungup and dispositioned automatically:</td>
                    </tr>
                    <tr>
                        <td>
                            <span id="HotKeyBoxA"><?= $HKboxA ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span id="HotKeyBoxB"><?= $HKboxB ?></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span id="HotKeyBoxC"><?= $HKboxC ?></span>
                        </td>
                    </tr>
                </table>
            </span>



            <span style="position:absolute;left:5px;top:0px;z-index:<?= ++$zi ?>;" id="EAcommentsBox">
                <table>
                    <tr bgcolor="#FFFF66">
                        <td align="left"> Extended Alt Phone Information: </td>
                        <td align="right"> <a href="#" onClick="EAcommentsBoxhide('YES');
                return false;"> minimize </a></td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <span id="EAcommentsBoxC"></span><br />
                            <span id="EAcommentsBoxB"></span><br />
                        </td>
                        <td width="320px" valign="top">
                            <span id="EAcommentsBoxA"></span><br />
                            <span id="EAcommentsBoxD"></span>
                        </td>
                    </tr>
                </table>
            </span>

            <span style="position:absolute;left:695px;top:0px;z-index:<?= ++$zi ?>;" id="EAcommentsMinBox">
                <table border="0" bgcolor="#FFFFCC" width="40px" height="20px">
                    <tr bgcolor="#FFFF66">
                        <td align="left">
                            <a href="#" onClick="EAcommentsBoxshow();
                return false;"> maximize </a> <br />Alt Phone Info
                        </td>
                    </tr>
                </table>
            </span>

            <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="WrapupBox">
                <table>
                    <tr>
                        <td align="center"> Call Wrapup: <span id="WrapupTimer"></span> seconds remaining in wrapup<br /><br />
                            <span id="WrapupMessage"><?= $wrapup_message ?></span>
                            <br /><br />
                            <a href="#" onClick="WrapupFinish();
                return false;">Finish Wrapup and Move On</a>
                        </td>
                    </tr>
                </table>
            </span>

            <span  id="TimerSpan" class='modal'> 
                <div class="modal-header">
                    <button class="close" type="button" onClick="hideDiv('TimerSpan');
                return false;" >×</button>
                    Aviso
                </div>
                <div class="modal-body">
                    <b><p id="TimerContentSpan"></p></b>
                </div>
            </span>

            <span style="position:fixed;left:0;top:0;z-index:1001; width:100%; height:100%;" id="LogouTBox" class="form_settings">
                <table>
                    <tr>
                        <td align="center"><span id="LogouTBoxLink">Logout</span></td>
                    </tr>
                </table>
            </span>

            <span style="position:absolute;left:0px;top:70px;z-index:<?= ++$zi ?>;" id="DispoButtonHideA">
                <table>
                    <tr>
                        <td align="center" valign="top"></td>
                    </tr>
                </table>
            </span>

            <span style="position:absolute;left:0px;top:138px;z-index:<?= ++$zi ?>;" id="DispoButtonHideB">
                <table>
                    <tr>
                        <td align="center" valign="top"></td>
                    </tr>
                </table>
            </span>

            <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="DispoButtonHideC">
                <table>
                    <tr>
                        <td align="center" valign="top">Any changes made to the customer information below at this time will not be comitted, You must change customer information before you Hangup the call. </td>
                    </tr>
                </table>
            </span>

            <div style="overflow-y:auto; z-index:<?= ++$zi ?>; width:60%;margin-left: -30%;" class='grid-agent' id="DispoSelectBox">
                <div class="modal-header box_title label-info">
                    Resultado da Chamada:<strong id="DispoSelectPhonE"></strong>
                    <span class="close" style="margin-left:10px">​-</span>​
                    <div class="input-prepend right" style="margin-top:-5px">
                        <span style='color:black' class="add-on">Filtro</span><input type=text id=dispo_search >
                    </div>
                </div>
                <div class="notification-mes c_bg-2" id="DispoSelectContent"  style="margin:0;max-height: 330px;overflow-y: auto;">

                </div>
                <div class="notification-mes  last-item"  style='margin:0;'>
                    <span id="DispoSelectHAspan"></span> 
                    <span id="DispoSelectMaxMin"></span>
                    <span id="Dispo3wayMessage" style="display:none"></span>
                    <span id="DispoManualQueueMessage" style="display:none"></span>
                    <span id="PerCallNotesContent" style="display:none"><input type="hidden" name="call_notes_dispo" id="call_notes_dispo" value="" /></span>
                    <input type="hidden" name="DispoSelection" id="DispoSelection" />
                    <input type="checkbox" name="DispoSelectStop" id="DispoSelectStop" size="1" value="0" />
                    <label for="DispoSelectStop"><span></span> Pausa após terminar esta chamada</label>
                </div>

            </div>


            <div style="width:650px;z-index:1002;  margin-left: -300px;margin-top: -300px;" class='grid-agent'  id="CallBackSelectBox">
                <div class="modal-header">
                    <button class="close" type="button" onClick="FecharCallbacks();" >×</button>
                    Marcação de Callbacks
                </div>
                <div class="modal-body">
                    <div id="NoDateSelected" class="alert" style="display:none"></div>
                    <table>
                        <tr>
                            <td style='text-align:right;'>Data do Callback:</td>
                            <td style='text-align:left; width:225px;'>
                                <input readonly type="text" class="span2" id="data_callback" value="">
                            </td>
                            <td>
                                <table>
                                    <tr>
                                        <td>
                                            <input type="radio" id="cb_pessoal" <?= ($my_callback_option == "CHECKED") ? "checked='checked'" : "" ?> name="tipo_callback" />
                                            <label for="cb_pessoal"><span></span>Callback Pessoal</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="radio" id="cb_geral" <?= ($my_callback_option != "CHECKED") ? "checked='checked'" : "" ?> name="tipo_callback" />
                                            <label for="cb_geral"><span></span>Callback Geral</label>
                                        </td>
                                    </tr>
                                </table>
                        </tr>
                        <?php
                        $userlist = "<option></option>";
                        $results = mysql_query("Select user From vicidial_users Where active='Y'");
                        while ($result = mysql_fetch_array($results)) {
                            $userlist.= "<option>$result[0]</option>\n";
                        }
                        ?>

                        <tr <?= ($callback_other_user) ? "" : "style='display:none'" ?>>
                            <td style='text-align:right'>User:</td>
                            <td style='text-align:left; width:225px;'><select style="width:170px" id="cb_other_username" disabled><?= $userlist ?></select></td>
                            <td>
                                <table style='width:100%;'>
                                    <tr>
                                        <td>
                                            <input type="checkbox" id="cb_other_user" name="cb_other_user" />
                                            <label for="cb_other_user"><span></span> Outro User</label>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td style='text-align:right; vertical-align:top;'>Comentários do Callback:</td>
                            <td colspan="2">
                                <textarea id="comentarios_callback" style="width:375px; height:150px"></textarea>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="3">
                                <table class="right">
                                    <tr>
                                        <td></td>
                                        <td>
                                            <button onClick="CallBackDatE_submit();" class="btn btn-primary">Gravar</button>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="grid-agent" style="z-index:1001; width:1000px; margin-left: -500px; margin-top: -305px;" id="CallBacKsLisTBox" >
                <div class="modal-header">
                    <button class="close" type="button" onclick="CalLBacKsLisTClose();">×</button>
                    Lista de Callbacks
                </div>
                <div class="modal-body">
                    <div class="form-inline">
                        <input type="text" class="input-small" maxlength="4" id="cb_date_1" placeholder="Data Inicio">
                        <i class="icon-minus"></i>
                        <input type="text" class="input-small" maxlength="3" id="cb_date_2" placeholder="Data Fim">
                        <button class="btn" onClick="CalLBacKsLisTCheck();" style="cursor: pointer;"><i class="icon-refresh"></i>Atualizar</button>
                    </div>
                    <span class="help-block">Se não preencher as datas com o calendario, apenas aparecerá os callbacks de hoje.</span>
                    <div class="formRow" style="max-height: 330px;overflow-y: auto;">
                        <table class="table table-mod table-striped table-bordered table-condensed">
                            <thead>
                                <tr>
                                    <th>Data Call-back</th>
                                    <th>Nº Telefone</th>
                                    <th style='width:150px'>Comentário</th>
                                    <th>Nome</th>
                                    <th>Estado</th>
                                    <th>Campanha</th>
                                    <th>Última Chamada</th>
                                </tr>
                            </thead>
                            <tbody id="CallBacKsLisT" ></tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div class="grid-agent"  id="NeWManuaLDiaLBox" style="width: 520px;margin-left: -260px;" >
                <div class="modal-header">
                    <button class="close" type="button" onClick="ManualDialHide();">×</button>
                    Chamada Manual
                </div>
                <div class="modal-body">
                    <table style="margin:10px auto;width:95%">
                        <tr>
                            <td colspan="3">
                                <div class="help-inline">Atenção: os nºs novos vão ser associados á lista: <?= $manual_dial_list_id ?></div>
                            </td>
                        </tr>
                        <tr style="display:none">
                            <td>
                                <input type="hidden" size="7" maxlength="10" name="MDDiaLCodE" id="MDDiaLCodE" value="1" />
                                <span style="float: right;"> Portabilidade: </span>
                            </td>
                            <td colspan="2">
                                <span style="float: left;">
                                    <select id=portabilidade name=portabilidade >
                                        <option value=0 >Nenhuma</option>
                                        <option value=1 >TMN</option>
                                        <option value=2 >Vodafone</option>
                                        <option value=3 >Optimus</option>
                                        <option value=4 >Todas</option>
                                    </select>
                                </span>
                            </td>
                        </tr>	
                        <tr>
                            <td>
                                <span class="right"> Nº de telefone: </span>
                            </td>
                            <td colspan="2">
                                <span class="left"> 
                                    <span class="input-append">
                                        <input type="text"  maxlength="18" name="MDPhonENumbeR" id="MDPhonENumbeR" value="" class="num" placeholder="Insere o nº" />
                                        <a  href="#" onClick="NeWManuaLDiaLCalLSubmiT('PREVIEW');
                return false;" class="btn btn-primary">Marcar</a>
                                    </span>
                                    <input type="hidden" name="MDLeadID" id="MDLeadID" value="" />
                                    <input type="hidden" name="MDType" id="MDLeadID" value="" />
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span class="right">Ver BD:</span>
                            </td>
                            <td colspan="2">
                                <span class="left">
                                    <input type="checkbox" name="LeadLookuP" id="LeadLookuP" size="1" value="0" />
                                    <label for="LeadLookuP" ><span></span>Procurar na base de dados antes de chamar</label>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td align="left" colspan="3">
                                <span id="ManuaLDiaLGrouPSelecteD"></span><span id="ManuaLDiaLGrouP"></span>
                                <input type="hidden" size="24" maxlength="20" name="MDDiaLOverridE" id="MDDiaLOverridE" class="cust_form" value="" />
                            </td>
                        </tr>
                        <tr>
                            <td align="right" style="display:none" colspan="3">
                                <a href="#" onClick="NeWManuaLDiaLCalLSubmiT('NOW');
                return false;">Chamar já</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="grid-agent" id="confirm_feedback_log" style="z-index:1001; width:1000px; margin-left: -500px; display:none;" >
                <div class="modal-header">
                    <button class="close" type="button" onclick="hideDiv('confirm_feedback_log');">×</button>
                    Vendas não confirmadas
                </div>
                <div class="modal-body">
                    <table class="table table-mod table-bordered">
                        <thead>
                        <th>Comentário</th>

                        <th> Cliente </th>
                        <th> Data </th>
                        </thead>
                        <tbody id="comment_log_tbody">
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="grid-agent" style="overflow-y: auto;margin-left:-175px;margin-top:-250px; width: 350px; z-index:<?= ++$zi ?>;" id="CloserSelectBox">
                <div class="modal-header">
                    Seleccção de Grupos Inbound
                </div>
                <div id="CloserSelectContent">

                </div>
                <div  class="notification-mes c_bg-2" style='margin:0;' >
                    <input type="hidden" name="CloserSelectList" id="CloserSelectList" />
                    <?php
                    if (($outbound_autodial_active > 0) and ($disable_blended_checkbox < 1) and ($dial_method != 'INBOUND_MAN') and ($VU_agent_choose_blended > 0)) {
                        ?>
                        <input type="checkbox" name="CloserSelectBlended" id="CloserSelectBlended"  value="0" /><label for="CloserSelectBlended"><span></span> Chamadas <i>Inb/Out Blended</i></label>
                    <?php } ?>
                </div>
                <div  class="notification-mes" >
                    <div class="right">
                        <a href="#" onClick="CloserSelectContent_create();
                return false;" class="btn">Limpar</a>
                        <a href="#" onClick="CloserSelect_submit();
                return false;" class="btn btn-primary">Ok</a>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

            <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="TerritorySelectBox">
                <table>
                    <tr>
                        <td align="center" valign="top"> TERRITORY SELECTION <br />
                            <span id="TerritorySelectContent"> Territory Selection </span>
                            <input type="hidden" name="TerritorySelectList" id="TerritorySelectList" /><br />
                            <a href="#" onClick="TerritorySelectContent_create();
                return false;"> RESET </a> | 
                            <a href="#" onClick="TerritorySelect_submit();
                return false;">SUBMIT</a>
                        </td>
                    </tr>
                </table>
            </span>


            <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="NothingBox">
                <span id="DiaLLeaDPrevieWHide"> Channel</span>
                <span id="DiaLDiaLAltPhonEHide"> Channel</span>
                <?php
                if (!$agentonly_callbacks) {
                    echo "<input type=\"checkbox\" name=\"CallBackOnlyMe\" id=\"CallBackOnlyMe\" size=\"1\" value=\"0\" /> MY CALLBACK ONLY <br />";
                }
                if (($outbound_autodial_active < 1) or ($disable_blended_checkbox > 0) or ($dial_method == 'INBOUND_MAN') or ($VU_agent_choose_blended < 1)) {
                    echo "<input type=\"checkbox\" name=\"CloserSelectBlended\" id=\"CloserSelectBlended\" size=\"1\" value=\"0\" /> BLENDED CALLING<br />";
                }
                ?>
            </span>

            <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="CalLLoGDisplaYBox">
                <table>
                    <tr>
                        <td align="center" valign="top">AGENT CALL LOG:<a href="#" onClick="CalLLoGVieWClose();
                return false;">close [X]</a>
                            <br />
                            <div class="scroll_calllog" id="CallLogSpan"> Call log List </div>
                            <br />
                            <br /> 
                        </td>
                    </tr>
                </table>
            </span>
            <div class='grid-agent' style="margin-left:-250px;width:500px;z-index:<?= $zi++; ?>;" id="SearcHForMDisplaYBox">
                <div class="modal-header">
                    <button class="close" type="button" onClick="LeaDSearcHVieWClose();">×</button>
                    Procura de Contactos
                </div>	
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="control-group">
                            <label class="control-label" for="search_field">Seleccione o campo</label>
                            <div class="controls">
                                <select id=search_field><?= $search_opt ?></select>
                            </div>
                        </div>                            
                        <div class="control-group">
                            <label class="control-label" for="search_query">Escreva a pesquisa</label>
                            <div class="controls">
                                <input type=text maxlength="20" id=search_query />
                            </div>
                        </div>                          
                        <div class="control-group">
                            <div class="controls">
                                <button class="btn" onClick="LeadSearchSubmit();" style="cursor: pointer;"><i class="icon-search"></i>Procurar</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class='grid-agent' style="margin-left:-300px;width:600px;z-index:<?= ++$zi ?>;display:none" id="pesquisa_morada">
                <div class="modal-header">
                    <button class="close" type="button" onClick="hideDiv('pesquisa_morada');">×</button>
                    Procura de Moradas
                </div>
                <div class="modal-body">
                    <div class="form-inline">
                        <input type="text" class="input-small" id="cp_4" maxlength="4" placeholder="CP4">
                        <i class="icon-minus"></i>
                        <input type="text" class="input-small" id="cp_3" maxlength="3" placeholder="CP3" >
                        <button onClick="pesquisa_morada();" class="btn" >Pesquisar</button>
                    </div>
                    <div class="formRow" style="height: 140px;overflow-y: auto;">
                        <table class="table table-striped table-bordered table-condensed table-mod" id="result_moradas">
                            <thead> 
                                <tr>
                                    <th>Rua</th>
                                    <th>Cód. Postal</th>
                                    <th>Localidade</th>
                                    <th>Distrito</th>
                                    <th>Concelho</th>
                                    <th>Escolher</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class='grid-agent' style="margin-left:-400px;margin-top:-250px;width:800px;height:500px;z-index:<?= ++$zi ?>;" id="SearcHResultSDisplaYBox">
                <div class="modal-header">
                    <button class="close" type="button" onClick="hideDiv('SearcHResultSDisplaYBox');">×</button>
                    Resultado da Procura
                </div>
                <div class="modal-body">
                    <table class="table table-mod table-striped table-bordered table-condensed">
                        <thead>
                            <TR>
                                <TH> Nome </TH>
                                <TH> Telefone </TH>
                                <TH> Estado </TH>
                                <TH> Ultima Chamada </TH>
                                <TH> Localidade </TH>
                                <TH> Freguesia </TH>
                                <TH> Cod. Postal </TH>
                                <TH> Info </TH>
                                <TH> Chamar </TH>
                            </TR>
                        </thead>
                        <tbody id="SearcHResultSSpan">

                        </tbody>
                    </table>
                </div>
            </div>

            <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="CalLNotesDisplaYBox">
                <table>
                    <tr>
                        <td align="center" valign="top">CALL NOTES LOG:<a href="#" onClick="hideDiv('CalLNotesDisplaYBox');
                return false;">close [X]</a><br />
                            <div class="scroll_calllog" id="CallNotesSpan"> Call Notes List </div>
                            <br /><br /> 
                            <a href="#" onClick="hideDiv('CalLNotesDisplaYBox');
                return false;">Close Info Box</a>
                        </td>
                    </tr>
                </table>
            </span>

            <div style="margin-left:-500px;margin-top:-305px;z-index:1002;width: 1000px;" id="LeaDInfOBox" class="grid-agent">
                <div class="modal-header">
                    <button class="close" type="button" onclick="hideDiv('LeaDInfOBox');" >×</button>
                    Informação do Cliente
                </div>
                <div class="modal-body" id="LeaDInfOSpan">
                    <div class="span5">
                        <h4>Informação do Callback</h4>
                        <table class="table table-mod table-bordered">
                            <tr>
                                <th>Estado de Callback:</th>
                                <td id="cb_status"></td>
                            </tr>
                            <tr>
                                <th>Estado de Lead:</th>
                                <td id="lead_status"></td>
                            </tr>
                            <tr>
                                <th>Entrada de Callback:</th>
                                <td id="cb_entry_time"></td>
                            </tr>
                            <tr>
                                <th>Hora de Callback:</th>
                                <td id="cb_date"></td>
                            </tr>
                            <tr>
                                <th>Comentário de callback:</th>
                                <td id="cb_comment"></td>
                            </tr>
                        </table>
                    </div>
                    <div class="span6">
                        <h4>Informação da Lead</h4>
                        <table class="table table-mod table-bordered" id="lead_info">

                        </table>
                    </div>
                    <div class="span12">
                        <h4>Histórico da Lead</h4>
                        <table class="table table-mod table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Operador</th>
                                    <th>Tempo (seg.)</th>
                                    <th>Estado</th>
                                    <th>Nº</th>
                                    <th>Campanha</th>
                                    <th title='Entrada Ou Saida'>IO</th>
                                    <th>Desligou</th>
                                </tr>
                            </thead>
                            <tbody id="lead_log">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div style="overflow-y:auto;z-index:1001; width:60%;margin-left: -30%;" class="grid-agent"  id="PauseCodeSelectBox" >
                <div class="modal-header box_title label-info">
                    Seleccione o motivo da pausa
                    <span class="close" onclick="hideDiv('PauseCodeSelectBox');">&times;</span>
                </div>
                <div class="modal-body" id="PauseCodeSelectContent"></div>
                <input type="hidden" name="PauseCodeSelection" id="PauseCodeSelection" />
            </div>

            <span style="position:absolute;left:50%;top:40px;z-index:<?= ++$zi ?>;" id="PresetsSelectBox">
                <table>
                    <tr>
                        <td align="center" valign="top"> SELECT A PRESET :<br />
                            <span id="PresetsSelectBoxContent"> Presets Selection </span>
                            <input type="hidden" name="PresetSelection" id="PresetSelection" />
                        </td>
                    </tr>
                </table>
            </span>

            <span style="position:absolute;left:0px;top:0px;z-index:<?= ++$zi ?>;" id="GroupAliasSelectBox">
                <table>
                    <tr>
                        <td align="center" valign="top"> SELECT A GROUP ALIAS :<br />
                            <span id="GroupAliasSelectContent"> Group Alias Selection </span>
                            <input type="hidden" name="GroupAliasSelection" id="GroupAliasSelection" />
                        </td>
                    </tr>
                </table>
            </span>

            <span style="z-index:<?= ++$zi ?>; width:500px; height:200px;" id="blind_monitor_alert_span" class="grid-agent">
                <table>
                    <tr>
                        <td> Alerta :<br />
                            <b>
                                <span id="blind_monitor_alert_span_contents"></span>
                            </b>
                            <br/>
                            <br/>
                            <a href="#" onClick="hideDiv('blind_monitor_alert_span');
                return false;">Voltar</a>
                        </td>
                    </tr>
                </table>
            </span>


            <span style="z-index:<?= ++$zi ?>;" id="GENDERhideFORieALT"></span>

        </form>


        <form name="inert_form" id="inert_form" onSubmit="return false;">
            <input type="hidden" name="inert_button" id="inert_button" onClick="return false;" />
        </form>

        <div style="margin-left:-200px;z-index:<?= ++$zi ?>; overflow-y:auto" id="AlertBox" class="grid-agent" >
            <div class="modal-header box_title label-warning">
                <button class="close" type="button" onClick="hideDiv('AlertBox');">×</button>
                <h3>Alerta!</h3>
            </div>
            <div class="modal-body">
                <p id="AlertBoxContent"></p>
            </div>
            <div class="modal-footer">
                <button type="button" id="alert_button" class="btn btn-primary" onClick="hideDiv('AlertBox');">OK</button>
            </div>
        </div>


        <div style="margin-left:-200px;z-index:1000;display:none;overflow-y:auto" id="AlertBox_max_callback" class="grid-agent"  >
            <div class="modal-header box_title label-warning">
                <button class="close" type="button" onClick="hideDiv('AlertBox_max_callback');">×</button>
                <h3>Alerta!</h3>
            </div>
            <div class="modal-body">
                Atingiu o máximo de callbacks por campanha.
                <p id="max_callback_info"></p>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" onClick="hideDiv('AlertBox_max_callback');">Sair</button>
                <button type="button" class="btn btn-primary" id="alertbox_eliminar_callbacks" >Eliminar Callbacks</button>
            </div>
        </div>


        <div id="bitch-answer" class="modal" style="display:none">
            <div class="modal-header">
                <button class="close" type="button" >×</button>
                <h3>Cliente em espera</h3>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" id="bitch-answer-answer"><i class="icon-phone"></i>Atender</button>
                <button class="btn btn-link">Cancelar</button>
            </div>
        </div>
    </div>
    <script>
            setTimeout(function() {
                window.onbeforeunload = function() {
                    return "Não feche a janela, faça logout primeiro se faz favor :-(.";
                };
            }, 500);
    </script>
</body>
</html>
