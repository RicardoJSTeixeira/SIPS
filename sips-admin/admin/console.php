<?php
############################################################################################
####  Name:             g_ast_cli.php                                                   ####
####  Version:          2.0                                                             ####
####  Copyright:        GOAutoDial Inc. - Januarius Manipol <januarius@goautodial.com>  ####
####  License:          AGPLv2                                                          ####
############################################################################################


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

$stmt = "SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 8 and view_reports='1';";
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
    Header("WWW-Authenticate: Basic realm=\"\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    echo "<html><head><title>Logout</title><script>function update(){top.location='../../index.php';}var refresh=setInterval('update()',1000);</script></head><body onload=refresh></body></html>";
    exit;
}

$stmt = "SELECT ASTmgrUSERNAME,ASTmgrSECRET,telnet_port FROM servers;";
$rslt = mysql_query($stmt, $link);
if ($DB) {
    echo "$stmt\n";
}
$m_ct = mysql_num_rows($rslt);
if ($m_ct > 0) {
    $row = mysql_fetch_row($rslt);
    $m_user = $row[0];
    $m_pass = $row[1];
    $m_port = $row[2];
}

function validateIpAddress($ip_addr) {
    if (preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/", $ip_addr)) {
        $parts = explode(".", $ip_addr);
        foreach ($parts as $ip_parts) {
            if (intval($ip_parts) > 255 || intval($ip_parts) < 0)
                return false;
        }
        return true;
    }
    else
        return false;
}

$hostp = $_SERVER['SERVER_ADDR'];
$hostn = $_SERVER['SERVER_NAME'];

if (validateIpAddress($hostn)) {
    $hostn = exec('hostname');
}
ob_start();
if (!empty($_GET['cmd'])) {
    $ffo = $_GET['cmd'];
    $token = md5(uniqid(rand()));
    $errno = 0;
    $errstr = 0;
    $fp = fsockopen("localhost", $m_port, &$errno, &$errstr, 20);
    if (!$fp) {
        echo "$errstr ($errno)<br>\n";
    } else {
        fputs($fp, "Action: login\r\n");
        fputs($fp, "Username: $m_user\r\n");
        fputs($fp, "Secret: $m_pass\r\n");
        fputs($fp, "Events: off\r\n\r\n");
        fputs($fp, "Action: COMMAND\r\n");
        fputs($fp, "command: $ffo\r\n");
        fputs($fp, "ActionID: $token\r\n\r\n");
        sleep(1);
        $results = fread($fp, 38000);
        fclose($fp);
        $result = strpos($results);
    }
    echo $results;
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
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

        <link href="/sips/csslib/gadi_content.css" rel="stylesheet" type="text/css">

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
            pre{
                border: 0;
                padding: 1em 0;
                background: 0;
            }
            #outt{
                overflow-y: auto;
                height:462px;
                box-shadow:0px 0px 8px -3px rgba(0,0,0,0.5 ) inset;
            }
            #input-conteiner{
                padding:0px;
            }
            #input-conteiner input{
                border:0;
                border-radius: 0;
                margin: 0;}
            </style>
        </head>
        <body>
            <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <div class=content>

            <input type="hidden" name="file" value="<?= $fname ?>"/>

            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Consola Asterisk: <?= $fname ?></div>
                    <div class="pull-right">
                    </div>
                    <div class="clear"></div>
                </div>
                <form onsubmit="return false" >

                    <div class="search-item c_bg-2" id="outt" >
                        <?= $hostn . '*CLI>'; ?>
                        <div class="clear"></div>
                    </div>
                    <div class="search-item c_bg-1" id="input-conteiner">
                        <input tabindex="1" onkeyup="keyE(event);" class="span" id="cmd" type="text" />
                    </div>
                    <div class="clear"></div>
                </form>
            </div>
        </div>
        <script>
                    $(function() {
                        $("#loader").fadeOut("slow");
                        $("#cmd").focus();
                    });

                    if (self.location === top.location)
                        self.location = "../";
                    var CommHis = new Array();
                    var HisP;
                    var hname = '<?= $hostn . '*CLI>'; ?>';



                    function pR(rS)
                    {
                        var _6 = document.getElementById("outt");
                        var _7 = rS.split("\n\n");
                        var _8 = "<b>" + document.getElementById("cmd").value + "</b>";
                        _6.appendChild(document.createTextNode(_8));
                        _6.appendChild(document.createElement("br"));
                        for (var _9 in _7)
                        {
                            var _a = document.createElement("pre");
                            line = document.createTextNode(_7[_9]);
                            _a.appendChild(line);
                            _6.appendChild(_a);
                            _6.appendChild(document.createElement("br"));
                        }
                        _6.appendChild(document.createTextNode(hname));
                        _6.scrollTop = _6.scrollHeight;
                        document.getElementById("cmd").value = "";
                    }
                    function keyE(_b)
                    {
                        switch (_b.keyCode)
                        {
                            case 13:
                                var _c = document.getElementById("cmd").value;
                                if (_c)
                                {
                                    CommHis[CommHis.length] = _c;
                                    HisP = CommHis.length;
                                    var _d = document.location.href + "?cmd=" + escape(_c);
                                    $.get(document.location.href, {cmd: _c}, function(data) {
                                        pR(data);
                                    });
                                }
                                break;
                            case 38:
                                if (HisP > 0)
                                {
                                    HisP--;
                                    document.getElementById("cmd").value = CommHis[HisP];
                                }
                                break;
                            case 40:
                                if (HisP < CommHis.length - 1)
                                {
                                    HisP++;
                                    document.getElementById("cmd").value = CommHis[HisP];
                                }
                                break;
                            default:
                                break;
                        }
                    }
        </script>
    </body>
</html>
