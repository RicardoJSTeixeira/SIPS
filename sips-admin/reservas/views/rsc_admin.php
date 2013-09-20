<!DOCTYPE html>
<html> 
    <head> 
        <meta charset="utf-8"> 
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
        if (isset($_GET['sch'])) {
            $id_scheduler = preg_replace($only_nr, '', $_GET['sch']);
        } else {
            exit;
        }
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
        <?php
        $query = "Select `display_text` From sips_sd_schedulers Where id_scheduler=$id_scheduler;";
        $result = mysql_query($query, $link);
        $row = mysql_fetch_assoc($result);
        ?>
    </head>
    <div class='grid' >
        <div class="grid-title">
            <div class="pull-left">Criar recurso</div>
            <div class="pull-right">
                <span class="btn" onclick="location = 'sch_admin.php'"><i class="icon-arrow-left"></i>Voltar</span>
            </div>
        </div>
        <div class="grid-content">

            <legend>Calendário: <strong><?= $row["display_text"] ?></strong> <span class="btn btn-mini" onclick="location = 'sch_edita.php?sch=<?= $id_scheduler ?>'"><i class="icon-pencil"></i>Editar</span></legend>

            <div id="table_conteiner" style="opacity: 0">
                <table id="schedulers" class="table table-mod-2">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Cód. Ref.</th>
                            <th>Activo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT id_resource,b.display_text,b.alias_code,a.days_visible,a.blocks,a.begin_time,a.end_time,b.active FROM sips_sd_schedulers a INNER JOIN sips_sd_resources b ON a.id_scheduler=b.id_scheduler WHERE b.active=1 AND a.id_scheduler=$id_scheduler;";
                        $result = mysql_query($query, $link);
                        while ($row = mysql_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td><?= $row["display_text"] ?></td>
                                <td><?= $row["alias_code"] ?></td>
                                <td>
                                    <div class="view-button"><span onclick="newUser(this);" data-id='<?= $row["id_resource"] ?>'  class="btn  btn-mini"><i class="icon-user"></i>Utilizador</span></div>
                                    <div class="view-button"><a href='rsc_edita.php?rsc=<?= $row["id_resource"] ?>' target='_self' class="btn  btn-mini"><i class="icon-pencil"></i>Editar</a></div>
                                    <div class="view-button"><a href='calendar_container.php?rsc=<?= $row["id_resource"] ?>' target='_self' class="btn  btn-mini"><i class="icon-folder-open"></i>Ver</a></div>
                                    <?= (($row["active"] == 1) ? " <img src='/images/icons/tick_16.png'  >" : "<img src='/images/icons/cross_16.png' >") ?></td>				
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="clear"></div>
            <div class="seperator_dashed"></div>
            <form class="form-horizontal" id="sch">

                <div class="control-group">
                    <label class="control-label">Nome:</label>
                    <div class="controls">
                        <input type="text" maxlength="255" value="" name="display_text" id="display_text" class="span"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Codigo de Referência:</label>
                    <div class="controls">
                        <input type="text" maxlength="255" value="" name="alias_code" id="alias_code" class="span"/>
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

    <div id="dialog-addUser" title="Utilizador de Calendário"  style="display: none;">
        <p class="validateTips">Cria um utilizador só com acesso a este calendário.</p>

        <form id="usr">
            <fieldset>
                <label for="username">Username</label>
                <input type="text" id="username"  />
                <label for="pass">Password</label>
                <input type="text" id="password" value="" />
            </fieldset>
        </form>
    </div>
    <script>
                    var rsc =<?= $id_scheduler; ?>;

                    var otable = $('#schedulers').dataTable({
                        "sDom": '<"top"f>rt<"bottom"p>',
                        "sPaginationType": "full_numbers",
                        "aoColumns": [
                            {"bSortable": true},
                            {"bSortable": true},
                            {"bSortable": true, "sType": "string"}
                        ],
                        "oLanguage": {
                            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
                        }
                    });

                    $('#saveForm').click(function() {
                        if (verify()) {
                            $.post("../ajax/rsc_admin_do.php", {
                                id: rsc,
                                display_text: $('#display_text').val(),
                                alias_code: $('#alias_code').val()
                            },
                            function(data) {
                                $("#sch :input:text").val('');
                                otable.dataTable().fnAddData([data.display_text,
                                    data.alias_code,
                                    '<div class="view-button"><span onclick="newUser();" data-id=' + data.id + ' class="btn  btn-mini"><i class="icon-user"></i>Utilizador</span></div>\n\
                                 <div class="view-button"><a href=\'rsc_edita.php?rsc=' + data.id + '\' target=\'_self\' class="btn  btn-mini"><i class="icon-pencil"></i>Editar</a></div>\n\
                                 <div class="view-button"><a href=\'calendar_container.php?rsc=' + data.id + '\' target=\'_self\' class="btn  btn-mini"><i class="icon-folder-open"></i>Ver</a></div>\n\
                                 <img src="/images/icons/tick_16.png" >']);
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
                        autoOpen: false,
                        modal: true,
                        buttons: {
                            Ok: function() {
                                $(this).dialog("close");
                            }
                        }
                    });

                    function verifyUser() {
                        return $("#usr :input:text,textarea,select").removeClass('alert').filter(function() {
                            return !/\S+/.test($(this).val());
                        }).addClass('alert').size() == 0;
                    }

                    var live_rsc = 0;
                    function newUser(el) {
                        live_rsc = $(el).data().id;
                        $('#dialog-addUser').dialog("open");
                    }
                    function addUser() {
                        var user = $("#username").val(),
                                pass = $("#password").val();

                        $.post("../ajax/user_rsc.php", {username: user, password: pass, rsc: live_rsc}, function(data) {
                            var msg=(data)?"Criado com Sucesso!":"Ups algo não correu bem :-/";
                            if(data==="exist")
                                msg="Utilizador indisponivel, tente outro.";
                            showDialog(msg);
                        },"json");
                    }
                    $('#dialog-addUser').dialog({
                        autoOpen: false,
                        modal: true,
                        buttons: {
                            Ok: function() {
                                if (verifyUser()) {
                                    addUser();
                                    $("#usr :input:text,textarea,select").val("").removeClass('alert');
                                    $(this).dialog("close");
                                }
                            },
                            cancel: function() {
                                $("#usr :input:text,textarea,select").val("").removeClass('alert');
                                $(this).dialog("close");
                            }
                        }
                    });

                    function verify() {
                        return $("#sch :input:text,textarea,select").removeClass('alert').filter(function() {
                            return !/\S+/.test($(this).val());
                        }).addClass('alert').size() == 0;
                    }




                    $(function() {
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
