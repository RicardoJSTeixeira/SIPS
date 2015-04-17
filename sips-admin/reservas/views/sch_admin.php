<!DOCTYPE html>
<html> 
    <head> 
        <meta charset="utf-8"> 
        <title>Criar calendário</title>
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

        <?php
        require('../func/reserve_utils.php');
        ?>
        <style>
            .view-button{
                margin-left: 0.5em;
            }
            input.alert {
                border: solid 1px red !important;
                box-shadow:0 0 3px 1px rgba(255, 0, 0, 0.5) !important;
            }
        </style>
    </head>
    <div class='grid' >
        <div class="grid-title">
            <div class="pull-left">Criar calendário</div>
            <div class="pull-right">
                <span class="btn" onclick="location='agente_ref.php';"><i class="icon-user"></i>Referências</span>
                <span class="btn" onclick="location='reserv_types.php';"><i class="icon-bolt"></i>Tipos de reserva</span>
            </div>
        </div>
        <div class="grid-content">

            <table id="schedulers" class="table table-mod-2">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cód. Ref.</th>
                        <th>Dias vis.</th>
                        <th>Blocos</th>
                        <th>Começa</th>
                        <th>Fecha</th>
                        <th>Activo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $user = new UserLogin($db);
                    $user->confirm_login();
                    $u=$user->getUser();

                    $usrQry = mysql_query("SELECT user_group, custom_one from vicidial_users WHERE user = '$u->username'") or die(mysql_error());
                    $usrQry = mysql_fetch_assoc($usrQry);

                    $usrCode = $usrQry['custom_one'];

                    $u_g = $usrQry['user_group'];
                    if ($u_g == 'AreaSalesManager' or $u_g == 'AAL' or $u_g == 'AMB' or $u_g == 'ANU' or $u_g == 'FPO' or $u_g == 'RGE' or $u_g == "BIO" or $u_g == "SPICE") {
                        $query = "SELECT id_scheduler,display_text,alias_code,days_visible,blocks,begin_time,end_time,active FROM sips_sd_schedulers a WHERE a.alias_code in ($usrCode) ;";
                    } elseif($u->user_group=="ADMIN") {
                        $query = "SELECT id_scheduler,display_text,alias_code,days_visible,blocks,begin_time,end_time,active FROM sips_sd_schedulers;";
                    } else {
                        $query = "SELECT id_scheduler,display_text,alias_code,days_visible,blocks,begin_time,end_time,active FROM sips_sd_schedulers WHERE user_group='$u->user_group';";
                    }


                    $result = mysql_query($query, $link);
                    while ($row = mysql_fetch_assoc($result)) { ?>
                        <tr>
				<td><?=$row["display_text"]?></td>
				<td><?=$row["alias_code"]?></td>
				<td><?=$row["days_visible"]?></td>
				<td><?=m2h($row["blocks"])?></td>
				<td><?=m2h($row["begin_time"])?></td>
				<td><?=m2h($row["end_time"])?></td>
				<td>
                            <div class="view-button"><a href='sch_edita.php?sch=<?=$row["id_scheduler"]?>' target='_self' class="btn  btn-mini"><i class="icon-pencil"></i>Editar</a></div>
                            <div class="view-button"><a href='rsc_admin.php?sch=<?=$row["id_scheduler"]?>' target='_self' class="btn  btn-mini"><i class="icon-folder-open"></i>Ver</a></div>
                            <?=(($row["active"] == 1) ? " <img src='/images/icons/tick_16.png'  >" : "<img src='/images/icons/cross_16.png' >")?></td>
			</tr>
                    <?php } ?>
                </tbody>
            </table>
                                        
            <div class="clear"></div>
            <div class="seperator_dashed"></div>
            <form class="form-horizontal" id="sch">

                <div class="control-group">
                    <label class="control-label">Nome:</label>
                    <div class="controls">
                        <input type="text" maxlength="255" value="" name="display_text" class="span"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Codigo de Referência:</label>
                    <div class="controls">
                        <input type="text" maxlength="255" value="" name="alias_code" class="span"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Dias visiveis:</label>
                    <div class="controls">
                        <input type="text" maxlength="3" value="7" name="display_days" class="span1"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Blocos:</label>
                    <div class="controls">
                        <select name="blocks">
                            <?php
                            for ($i = 5; $i <= 120; $i+=5) {
                                echo "<option value='$i' " . (($i == 30) ? "Selected" : "") . ">" . m2h($i) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">Horário de funcionamento:</label>
                    <div class="controls">
                        <select name="begin_time" id="begin_time">
                            <?php
                            for ($i = 0; $i < 1440; $i+=15) {
                                echo "<option value='$i' " . (($i == 480) ? "Selected" : "") . ">" . m2h($i, true) . "</option>";
                            }
                            ?>
                        </select>
                        <select name="end_time" id="end_time">
                            <?php
                            for ($i = 0; $i < 1440; $i+=15) {
                                echo "<option value='$i' " . (($i == 1080) ? "Selected" : "") . ">" . m2h($i, true) . "</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="control-group">
                    <div class="controls">
                        <input type="button" class="btn btn-primary" id="saveForm" value="Criar">
                    </div>
                </div>

            </form>      
        </div>
    </div>

    <div id="dialog-confirm" title="Resultado"  style="display: none;">
        <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
    </div>
    <script>

                        var otable = $('#schedulers').dataTable({
                            "sDom": '<"top"f>rt<"bottom"p>',
                            "sPaginationType": "full_numbers",
                            "aoColumns": [{
                                    "bSortable": true
                                }, {
                                    "bSortable": true
                                }, {
                                    "bSortable": true
                                }, {
                                    "bSortable": true
                                }, {
                                    "bSortable": true
                                }, {
                                    "bSortable": true
                                }, {
                                    "bSortable": true,
                                    "sType": "string"
                                }],
                            "oLanguage": {
                                "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
                            }
                        });


                        $('#saveForm').click(function() {
                            if (verify()) {
                                $.post("../ajax/sch_admin_do.php", $('#sch').serialize(),
                                        function(data) {
                                            if (data.sucess == 1) {
                                                $("#sch")[0].reset();
                                                otable.dataTable().fnAddData(
                                                        [data.display_text ,
                                                    data.alias_code,
                                                    data.display_days,
                                                    data.blocks,
                                                    data.begin_time,
                                                    data.end_time,
                                                    '<img src="/images/icons/tick_16.png" >\n\
                            <div class="view-button"><a href=\'sch_edita.php?sch='+data.id+'\' target=\'_self\' class="btn  btn-mini"><i class="icon-pencil"></i>Editar</a></div>\n\
                            <div class="view-button"><a href=\'rsc_admin.php?sch='+data.id+'\' target=\'_self\' class="btn  btn-mini"><i class="icon-folder-open"></i>Ver</a></div>']);
                                            } else {
                                                showDialog("Sucedeu-se um erro.");
                                            }
                                        }, "json").fail(function() {
                                    showDialog("Sucedeu-se um erro.");
                                });
                            }
                        });

                        function showDialog(msg) {
                            $('#alertbox').html(msg);
                            $('#dialog-confirm').dialog("open");
                        }
                        $('#dialog-confirm').dialog({
                            autoOpen:false,
                            modal: true,
                            buttons: {
                                Ok: function() {
                                    $(this).dialog("close");
                                }
                            }
                        });

                        function verify() {
                            var result1 = ($("#sch :input:text,textarea,select").removeClass('alert').filter(function() {
                                return !/\S+/.test($(this).val());
                            }).addClass('alert').size() == 0);
                            $('#begin_time').removeClass('alert');
                            $('#end_time').removeClass('alert');
                            var result2 = (parseInt($('#begin_time').val()) < parseInt($('#end_time').val()));
                            if (!result2) {
                                $('#begin_time').addClass('alert');
                                $('#end_time').addClass('alert');
                            }
                            return (result1 && result2);
                        }

                        $(document).ready(function() {

                            $("#table_conteiner").animate({opacity: 1});
                            $(".num").keydown(function(event) {
                                if ((!event.shiftKey && !event.ctrlKey && !event.altKey) && ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105))) {
                                } else if (event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39) {
                                    event.preventDefault();
                                }
                            });
                        });
    </script>

</body>
</html>
