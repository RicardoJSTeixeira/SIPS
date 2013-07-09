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
            .c_bg-2{
                overflow-y: auto;
                height:486px;
                box-shadow:0px 0px 8px -3px rgba(0,0,0,0.5 ) inset;
            }
            pre{
                border: 0;
                padding: 1em 0;
                background: 0;
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
        ?> 
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <div class=content>

            <input type="hidden" name="file" value="<?= $fname ?>"/>

            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Relatório em Tempo Real: <?= $fname ?></div>
                    <div class="pull-right">
                        <div>
                            <button id="start" class="btn btn-large btn-success"><i class="icon-play"></i>Iniciar</button>
                            <button id="stop" class="btn btn-large btn-danger" disabled><i class="icon-pause"></i>Parar</button>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                    <div class="search-item c_bg-2">
                        <pre id="bord"></pre>
                    </div>

            </div>
        </div>

        <script>
            var a=0,b=0;
            var stop = true;
            var bord = $("#bord");
            var intervalo = "2000";
            function update() {
                if (stop) {
                return;
                } else{
                    $.post("realtime_request.php",
                            {
                                user: "<?= $PHP_AUTH_USER ?>",
                                pass:"<?= base64_encode($PHP_AUTH_PW) ?>"
                            }, function(data) {
                        bord.text(data.logs);
                        
                    setTimeout(update, intervalo);
                    },"json");
                }
            }
            function start_realtime(){
                    stop = false;
                    $("#stop").removeAttr("disabled");
                    $("#start").attr("disabled", 'disabled');
                update();
                }
                function stop_realtime(){
                    stop = true;
                    $("#start").removeAttr("disabled");
                    $("#stop").attr("disabled", 'disabled');
                }
            $(function() {
                $("#loader").fadeOut("slow");
                $("#start").on("click", start_realtime);
                $("#stop").on("click", stop_realtime);
            });
        </script>
    </body>
</html>
