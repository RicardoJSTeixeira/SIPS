
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
        <?php
        require("../dbconnect.php");

        foreach ($_POST as $key => $value) {
            ${$key} = $value;
        }
        foreach ($_GET as $key => $value) {
            ${$key} = $value;
        }

        $user = $_SERVER['PHP_AUTH_USER'];

        $stmt = "SELECT user_group FROM vicidial_users WHERE user LIKE '$user'";

        $grupos = mysql_query($stmt, $link);

        $grupos = mysql_fetch_assoc($grupos);

        $grupos_a = $grupos['user_group'];


        $data = date('o-m-d');




        if ($grupos_a == 'ADMIN') {
            $grupos = mysql_query("SELECT user_group, group_name FROM vicidial_user_groups", $link) or die(mysql_error());
        } else {
            $grupos = mysql_query("SELECT user_group, group_name FROM vicidial_user_groups WHERE user_group='$grupos_a'", $link) or die(mysql_error());
        }


        if ($ADD == 211111) {
            ##### BEGIN ID override optional section, if enabled it increments user by 1 ignoring entered value #####
            $stmt = "SELECT value FROM vicidial_override_ids where id_table='vicidial_user_groups' and active='1';";
            $rslt = mysql_query($stmt, $link) or die(mysql_error());
            $voi_ct = mysql_num_rows($rslt);
            if ($voi_ct > 0) {
                $row = mysql_fetch_row($rslt);
                $user_group = ($row[0] + 1);

                $stmt = "UPDATE vicidial_override_ids SET value='$user_group' where id_table='vicidial_user_groups' and active='1';";
                $rslt = mysql_query($stmt, $link) or die(mysql_error());
            }
            ##### END ID override optional section #####


            $stmt = "SELECT count(*) from vicidial_user_groups where user_group='$user_group';";
            $rslt = mysql_query($stmt, $link) or die(mysql_error());
            $row = mysql_fetch_row($rslt);
            if ($row[0] > 0) {
                ?><script>
                            $(function() {
                                makeAlert("#wr", "Grupo de Utilizadores não Adicionado", "Já existe um Grupo com este nome.", 1, true, false);
                            });
                </script><?php
            } else {
                if ((strlen($user_group) < 2) or (strlen($group_name) < 2)) {
                    ?><script>
                            $(function() {
                                makeAlert("#wr", "Grupo de Utilizadores não Adicionado", " nome e descrição do grupo têm de conter pelo menos 2 caracteres.", 1, true, false);
                            });
                    </script><?php
                } else {
                    $stmt = "INSERT INTO vicidial_user_groups(user_group,group_name,allowed_campaigns) values('$user_group','$group_name',' -');";
                    $rslt = mysql_query($stmt, $link) or die(mysql_error());
                    ?><script>
                            window.location = 'listausersgroups.php?success=1';
                    </script><?php
                    ### LOG INSERTION Admin Log Table ###
                    $SQL_log = "$stmt|";
                    $SQL_log = ereg_replace(';', '', $SQL_log);
                    $SQL_log = addslashes($SQL_log);
                    $stmt = "INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='USERGROUPS', event_type='ADD', record_id='$user_group', event_code='ADMIN ADD USER GROUP', event_sql=\"$SQL_log\", event_notes='';";
                    if ($DB) {
                        echo "|$stmt|\n";
                    }
                    $rslt = mysql_query($stmt, $link) or die(mysql_error());
                }
            }
        }
        ?>

    </head>

    <body>
        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Criar Novo Grupo de Operadores</div>
                    <div class="pull-right"></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <form name="novogrupo" id="novocolab" method="post" target="_self">
                        <input type="hidden" name="ADD" value="211111"  />
                        <div class="formRow op fix">
                            <label>Nome:</label>
                            <div class="formRight">
                                <input type="text" name="user_group" required class="span"/>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label>Descrição:</label>
                            <div class="formRight">
                                <input type="text" name="group_name" required class="span"/>
                            </div>
                        </div> 
                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        <p class="text-right">
                            <button class="btn btn-success">Gravar</button>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>