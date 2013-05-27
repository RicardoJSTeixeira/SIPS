<?php
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $path.="../";
}
define("ROOT", $path);
require(ROOT . "ini/dbconnect.php");
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-tagmanager.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>

        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-tagmanager.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/colorpicker.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />

        <script type="text/javascript" src="/jquery/jsdatatable/plugins/plugin.fnAjaxReload.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.9.2.custom.min.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/plugins/datetimepicker.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/language/pt-pt.js"></script>
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />

        <style>
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
            .view-button{
                margin-left: 0.5em;
            }
        </style>

    </head>

    <body>


        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>

        <?php
        $user = $_SERVER['PHP_AUTH_USER'];
        $estado = (!isset($_POST["estado"])) ? 'activo' : $_POST["estado"];

        //Users INICIO
        $query = "select a.user_group,allowed_campaigns,user_level from vicidial_users a inner join `vicidial_user_groups` b on a.user_group=b.user_group where user='$user'";
        $result = mysql_query($query) or die(mysql_error());
        $row = mysql_fetch_assoc($result);


        $user_level = $row['user_level'];
        $allowed_camps_regex = str_replace(" ", "|", trim(rtrim($row['allowed_campaigns'], " -")));

        if ($row['user_group'] != "ADMIN") {
            $ret = "WHERE allowed_campaigns REGEXP '$allowed_camps_regex'";


            $user_groups = "";
            $result = mysql_query("SELECT `user_group`, `allowed_campaigns` FROM `vicidial_user_groups` $ret ") or die(mysql_error());
            while ($row1 = mysql_fetch_assoc($result)) {
                $user_groups .= "'$row1[user_group]',";
            }
            $user_groups = rtrim($user_groups, ",");

            $users_regex = "";
            $result = mysql_query("SELECT `user` FROM `vicidial_users` WHERE user_group in ($user_groups) AND user_level < $user_level") or die(mysql_error());
            while ($rugroups = mysql_fetch_assoc($result)) {
                $users_regex .= "$rugroups[user]|";
            }
            $users_regex = rtrim($users_regex, "|");
            $users_regex = "AND user REGEXP '^$users_regex$'";
        }
//Users FIM





        if ($estado == 'activo') {
            $colab = mysql_query("SELECT * FROM vicidial_users WHERE active = 'Y'  $users_regex") or die(mysql_error());
        } elseif ($estado == 'inactivo') {
            $colab = mysql_query("SELECT * FROM vicidial_users WHERE active = 'N'  $users_regex") or die(mysql_error());
        } else {
            $colab = mysql_query("SELECT * FROM vicidial_users WHERE  " . ltrim($users_regex, "AND")) or die(mysql_error());
        }
        ?>


        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Colaboradores</div>
                    <div class="pull-right">
                        <div class="distance" style="width: 320px;">
                            <form name="festado" method="post">
                                <p>
                                    <input type="radio" name="estado" id="act" value="activo" <?= ($estado == 'activo') ? "checked" : "" ?>>
                                    <label for="act"><span></span>Activos</label>
                                </p>
                                <p>
                                    <input type="radio" name="estado" id="inac" value="inactivo" <?= ($estado == 'inactivo') ? "checked" : "" ?>>
                                    <label for="inac"><span></span>Inactivos</label>
                                </p>
                                <p>
                                    <input type="radio" name="estado" id="all"  value="todos" <?= ($estado == 'todos') ? "checked" : "" ?>>
                                    <label for="all"><span></span>Todos</label>
                                </p>
                            </form>
                            <a href="novouser.php" class="btn btn-large btn-primary">Novo</a>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <table id='lists' class="table table-mod-2 table-striped" >
                        <thead>
                            <tr>
                                <th>Grupo</th>
                                <th>Nome</th>
                                <th>Username</th>
                            </tr>
                        </thead>
                        <tbody>	
                            <?php
                            while ($rcolab = mysql_fetch_assoc($colab)) {
                                ?>		
                                <tr>
                                    <td><?= $rcolab['user_group'] ?></td>
                                    <td><?= $rcolab['full_name'] ?></td>
                                    <td><?= $rcolab['user'] ?>
                                        <div class="view-button"><a href='editauser.php?user=<?= $rcolab['user'] ?>' target='_self' class="btn  btn-mini"><i class="icon-pencil"></i>Editar</a></div>
                                        <div class="view-button"><a href='presencas.php?user=<?= $rcolab['user'] ?>' target='_self' class="btn  btn-mini" ><i class="icon-calendar"></i> Faltas</a></div>
                                        <div class="view-button"><a href='../user_stats.php?user=<?= $rcolab['user'] ?>' target='_self'  class="btn  btn-mini"><i class="icon-bar-chart"></i> Estatística</a></div>
                                        <div class="view-button"><a href='gravacoes.php?user=<?= $rcolab['user'] ?>' target='_self'  class="btn  btn-mini"><i class="icon-headphones"></i> Gravações</a></div>
                                        <div class="view-button"><a href="#" data-userid='<?= $rcolab['user_id'] ?>' data-active="<?= $rcolab['active'] ?>" class="btn  btn-mini activator"> <i class="icon-check<?= ($rcolab['active'] == "Y") ? "" : "-empty" ?>" ></i><span><?= ($rcolab['active'] == "Y") ? "Activo" : "Inactivo" ?></span></a></div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <div class="clear"></div>
                </div>   
            </div>   
        </div>   

        <script>


            var otable;
            var eu;
            $(function() {
                otable = $('#lists').dataTable({
                    "sPaginationType": "full_numbers",
                    "oLanguage": {
                        "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
                    }
                });
                $(".activator").on("click", function() {
                    var that = $(this);
                    eu = that;
                    var active = (that.data("active") == "Y") ? "N" : "Y";
                    var user_id = that.data("userid");
                    $.post("_requests.php", {action: "user_change_status", user: user_id, active: active}, function(data) {
                        that.data("active", active);
                        that.find("i").attr("class", "icon-check" + ((active == "N") ? "-empty" : ""))
                                .parent().find("span").text((active == "Y") ? "Activo" : "Inactivo");
                    }, "json");
                    return false;
                });
                $("[name=estado]").on("click", function() {
                    $(this).parent().parent().submit();
                });
                $("#loader").fadeOut("slow");
            });


        </script>
    </body>
</html>
