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
    <body>

        <?php
        require("../dbconnect.php");

function log_admin($topic, $event, $id, $query, $comments = "") {
    global $link, $PHP_AUTH_USER;
    $stmt = "INSERT INTO vicidial_admin_log set event_date=NOW(), user='$PHP_AUTH_USER', ip_address='$IP', event_section='$topic', event_type='MODIFY', record_id='$id', event_code='$event', event_sql='" . mysql_real_escape_string($query) . "', event_notes='$comments';";
    $rslt = mysql_query($stmt, $link);
    if (!$rslt) {
        echo "Log Error: " . mysql_error() . " Whole query:" . $stmt;
    }
}
        $PHP_AUTH_USER = $_SERVER['PHP_AUTH_USER'];
        $PHP_AUTH_PW = $_SERVER['PHP_AUTH_PW'];

        if ($force_logout) {
            if ((strlen($PHP_AUTH_USER) > 0) or (strlen($PHP_AUTH_PW) > 0)) {
                Header("WWW-Authenticate: Basic realm=\"GOAUTODIAL-PROJECTS\"");
                Header("HTTP/1.0 401 Unauthorized");
            }
            echo "You have now logged out. Thank you\n";
            echo "<html><head><title>GoAutoDial - Logout</title><script>function update(){top.location='../../index.php';}var refresh=setInterval('update()',1000);</script></head><body onload=refresh></body></html>";
            exit;
        }

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
            Header("WWW-Authenticate: Basic realm=\"GOAUTODIAL-PROJECTS\"");
            Header("HTTP/1.0 401 Unauthorized");
            echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
            echo "<html><head><title>GoAutoDial - Logout</title><script>function update(){top.location='../../index.php';}var refresh=setInterval('update()',1000);</script></head><body onload=refresh></body></html>";
            exit;
        }
        if (isset($_POST['submit'])) {
            if ($_POST['submit'] == "Download") {
                $fname = $_POST['file'];
                $bname = basename($fname);
                header("Content-type: application/txt");
                header("Content-Disposition: attachment; filename=\"$bname\"");
                $file = fopen($fname, 'r');
                if ($file == FALSE) {
                    ?>
                    <script>
                        $(function() {
                            makeAlert("#wr", "Error opening <?= $fname ?>!", "Please make sure the web server is authorized to read this file!", 1, true, false);
                        });
                    </script>
                    <?php
                }
                $contents = fread($file, filesize($fname));
                echo $contents;
                fclose($file);
                log_admin("Files","Download",$fname,"");
                exit();
            }
            if ($_POST['submit'] == "Save") {
                $data = $_POST['data'];
                $fname = $_POST['file'];
                $file = fopen($fname, 'w');
                if ($file != FALSE) {
                    fwrite($file, $data);
                    fclose($file);
                    ?>
                    <script>
                        $(function() {
                            makeAlert("#wr", "Successo", "Configuration file <?= $fname ?> was successfully saved! :-).", 4, false, false);
                        });
                    </script>
                    <?php
                log_admin("Files","Save",$fname,"");
                } else {
                    ?>
                    <script>
                        $(function() {
                            makeAlert("#wr", "Error saving <?= $fname ?>!", "Please make sure the web server is authorized to save this file!", 1, true, false);
                        });
                    </script>
                    <?php
                }
            }
        }
        $fname = (isset($_GET['file'])) ? $_GET['file'] : $fname;
        $file = fopen($fname, 'r');
        if ($file == FALSE) {
            ?>
            <script>
                $(function() {
                    makeAlert("#wr", "Error opening <?= $fname ?>!", "Please make sure the web server has authority to read this file!", 1, true, false);
                });
            </script>
            <?php
        }
        $contents = fread($file, filesize($fname));
        fclose($file);
        ?>         
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <div class=content>


            <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                <input type="hidden" name="file" value="<?= $fname ?>"/>

                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Configuration file: <?= $fname ?></div>
                        <div class="pull-right"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-content">
                        <div id="wr"></div>
                        <div class="formRow">
                            <textarea name="data" rows=20 class="resize-text span">
<?= htmlentities($contents) ?>
                            </textarea>
                        </div> 
                        <div class="clear"></div><div class="seperator_dashed"></div>
                        <div class="grid-content">
                            <p class="text-right">
                                <input type="submit" name="submit" value="Save" id="save" class="btn btn-primary"/> 
                                <a href="folder_list.php" class="btn">Discard</a>
                                <input type="submit" name="submit" value="Download" class="btn"/> 
                            </p>
                        </div>

                    </div>
                </div>

            </form>
        </div>
            <script>
                $(function() {
                    $("#loader").fadeOut("slow");
                });
                $(window).bind('keydown', function(event) {
                    if (event.ctrlKey || event.metaKey) {
                        switch (String.fromCharCode(event.which).toLowerCase()) {
                        case 's':
                            event.preventDefault();
                            $("#save").click();
                            break;
                        }
                    }
                });
            </script>
    </body>
</html>