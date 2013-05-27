
<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>Criar CALL TIME</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/jquery/jsdatatable/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" /><?

        function help($where, $text) {
            return '<a onclick="openNewWindow(\'../admin.php?ADD=99999#' . $where . '\')">' . $text . '</a>';
        }

        $PHP_AUTH_USER = $_SERVER["PHP_AUTH_USER"];
        $PHP_AUTH_PW = $_SERVER["PHP_AUTH_PW"];

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
        $stmt = "SELECT delete_call_times,modify_call_times,user_group from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW';";
        $rslt = mysql_query($stmt, $link);
        $row = mysql_fetch_row($rslt);
        $LOGmodify_call_times = $row[1];
        $LOGuser_group = $row[2];

        $stmt = "SELECT allowed_campaigns,allowed_reports,admin_viewable_groups,admin_viewable_call_times from vicidial_user_groups where user_group='$LOGuser_group';";
        $rslt = mysql_query($stmt, $link);
        $row = mysql_fetch_row($rslt);
        $LOGadmin_viewable_groups = $row[2];

        $whereLOGadmin_viewable_groupsSQL = '';
        if ((!eregi("--ALL--", $LOGadmin_viewable_groups)) and (strlen($LOGadmin_viewable_groups) > 3)) {
            $whereLOGadmin_viewable_groupsSQL = "where user_group IN('---ALL---','$rawLOGadmin_viewable_groupsSQL')";
        } else {
            $admin_viewable_groupsALL = 1;
        }

        if ($LOGmodify_call_times != 1) {
            echo "You do not have permission to view this page\n";
            exit;
        }

        if ($ADD == 211111111) {
            $stmt = "SELECT count(*) from vicidial_call_times where call_time_id='$call_time_id';";
            $rslt = mysql_query($stmt, $link);
            $row = mysql_fetch_row($rslt);
            if ($row[0] > 0) {
                ?>
                <script>
                    $(function() {
                        makeAlert("#wr", "CALL TIME DEFINITION NOT ADDED", "there is already a call time entry with this ID.", 1, true, true);
                    });
                </script>
            <?php
            } else {
                if ((strlen($call_time_id) < 2) or (strlen($call_time_name) < 2)) {
                    ?>
                    <script>
                        $(function() {
                            makeAlert("#wr", "CALL TIME DEFINITION NOT ADDED", "Call Time ID and name must be at least 2 characters in length.", 1, true, true);
                        });
                    </script>
                    <?php
                } else {
                    $stmt = "INSERT INTO vicidial_call_times SET call_time_id='$call_time_id',call_time_name='$call_time_name',call_time_comments='$call_time_comments';";
                    $rslt = mysql_query($stmt, $link);
                    if ($DB > 0) {
                        echo "|$stmt|";
                    }


                    ### LOG INSERTION Admin Log Table ###
                    $SQL_log = "$stmt|";
                    $SQL_log = ereg_replace(';', '', $SQL_log);
                    $SQL_log = addslashes($SQL_log);
                    $stmt = "INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='CALLTIMES', event_type='ADD', record_id='$call_time_id', event_code='ADMIN ADD CALL TIME', event_sql=\"$SQL_log\", event_notes='';";
                    if ($DB) {
                        echo "|$stmt|\n";
                    }
                    $rslt = mysql_query($stmt, $link);

                    echo "<script type='text/javascript'>  
				window.location = 'list_call_times.php?success=1';
				</script>";
                }
            }
        }
        ?>
    </head>
    <body>
        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">NEW CALL TIME:</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <form action="" method=POST>
                        <input type=hidden name=ADD value=211111111>

                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                Call Time ID:
                            </label>
                            <div class="formRight">
                                <input type=text name=call_time_id class="span" maxlength=10 required>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                Call Time Name:
                            </label>
                            <div class="formRight">
                                <input type=text name=call_time_name class="span" maxlength=30 required>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="">
                                Call Time Comments:
                            </label> 
                            <div class="formRight">
                                <input type=text name=call_time_comments class="span" maxlength=30>
                            </div>
                        </div> 
                        <div class="clear"></div>
                        <input class="btn btn-primary right" type=submit name=SUBMIT value=Gravar>
                        <div class="clear"></div>

                    </form>
                </div>
            </div>
        </div>
    </body>
</html>
