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
        <div class=content>
            <div class="grid">
                <div class="grid-title">
                    <div class="pull-left">Lista dos Grupos de Utilizadores</div>
                    <div class="pull-right"><a href="novousergroup.php" class="btn btn-large btn-primary">Novo</a></div>
                    <div class="clear"></div>
                </div>
                <div class="grid-content">
                    <div id="wr"></div>
                    <table id='lists' class="table table-mod-2 table-striped" >
                        <thead>
                            <tr>
                                <th> Grupo </th>
                                <th> Nome </th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $user = $_SERVER['PHP_AUTH_USER'];
                            $stmt = "SELECT user_group FROM vicidial_users WHERE user='$user'";
                            $rslt = mysql_query($stmt, $link);

                            $rslt = mysql_fetch_assoc($rslt);
                            $grupo = $rslt['user_group'];


                            if ($grupo == 'ADMIN') {
                                $stmt = "SELECT user_group,group_name,forced_timeclock_login from vicidial_user_groups order by user_group";
                            } else {
                                $stmt = "SELECT user_group,group_name from vicidial_user_groups WHERE user_group = '$grupo'";
                            }
                            $rslt = mysql_query($stmt, $link);
                            $usergroups_to_print = mysql_num_rows($rslt);


                            while ($row = mysql_fetch_row($rslt)) {
                                ?>
                                <tr>
                                    <td><?= $row[0] ?></td>
                                    <td><?= $row[1] ?><div class="view-button"><a href="editausergroup.php?user_group=<?= $row[0] ?>" target='_self' class="btn  btn-mini"><i class="icon-pencil"></i>Editar</a></div></td>
                                </tr>
<? } ?>
                        </tbody>
                    </table>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <script>
                

                var otable;
                $(function() {
                    otable = $('#lists').dataTable({
                        "sPaginationType": "full_numbers",
                        "oLanguage": {
                            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
                        }
                    });
                    $(".activator").on("click",function(){
                        var that=$(this);
                        var active=(that.data("active")=="Y")?"N":"Y";
                        var user_id=that.data("userid");
                        $.post("_requests.php", {action: "user_change_status", user: user_id, active: active}, function(data) {
                            that.data("active",active);
                            that.find("i").attr("class","icon-check"+((active=="N")?"-empty":""))
                            .parent().find("span").text((active=="Y")?"Activo":"Inactivo");
                        },"json");
                    });
                    $("[name=estado]").on("click",function(){$(this).parent().parent().submit();});
                    $("#loader").fadeOut("slow");
                });

     <?php if (isset($_GET["success"])) { ?> 
                           $(function() {
                                makeAlert("#wr", "SUCCESS", "Grupo de Utilizadores Adicionado com Sucesso. :-).", 4, false, false);
                            });
          <?php } ?>
            </script>
    </body>
</html>
