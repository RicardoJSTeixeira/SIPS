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
            Header("WWW-Authenticate: Basic realm=\"GOAUTODIAL-PROJECTS\"");
            Header("HTTP/1.0 401 Unauthorized");
            echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
            echo "<html><head><title>GoAutoDial - Logout</title><script>function update(){top.location='../../index.php';}var refresh=setInterval('update()',1000);</script></head><body onload=refresh></body></html>";
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
                    <table class="table table-mod-2" id="phones">
                        <thead>
                            <tr>
                                <th>EXTEN</th>
                                <th>PROTO</th>
                                <th>SERVER</th>
                                <th>STATUS</th>
                                <th>NAME</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $stmt = "SELECT extension,protocol,server_ip,dialplan_number,voicemail_id,status,fullname,messages,old_messages from phones";
                            $rslt = mysql_query($stmt, $link);
                            $phones_to_print = mysql_num_rows($rslt);

                            while ($row = mysql_fetch_row($rslt)) {
                                ?>
                                <tr>
                                    <td><?= $row[0] ?></td>
                                    <td><?= $row[1] ?></td>
                                    <td><?= $row[2] ?></td>
                                    <td><?= $row[5] ?></td>
                                    <td><?= $row[6] ?>
                                        <div class="view-button"><a href="?extension=<?= $row[0] ?>&server_ip=<?= $row[2] ?>" class="btn  btn-mini activator"> <i class="icon-check"></i><span>Editar</span></a></div></td>
                                </tr>
                            <?php } ?>

                        </tbody>
                    </table>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <script>
            $(function() {
                $('#phones').dataTable({
                    "sPaginationType": "full_numbers",
                    "oLanguage": {
                        "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
                    }
                });
                $("#loader").fadeOut("slow");
            });
        </script>
    </body>
</html>