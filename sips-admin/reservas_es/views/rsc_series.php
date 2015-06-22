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
        if (isset($_GET['rsc'])) {
            $id_resource = preg_replace($only_nr, '', $_GET['rsc']);
        } else {
            exit;
        }

        $query = "Select `display_text` From sips_sd_resources Where id_resource=$id_resource;";
        $result = mysql_query($query, $link);
        $row = mysql_fetch_assoc($result);
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
    <body>
        <div class='grid' >
            <div class="grid-title">
                <div class="pull-left">Editar series</div>
                <div class="pull-right">
                    <span class="btn" onclick="location = 'rsc_edita.php?rsc=<?= $id_resource ?>'"><i class="icon-arrow-left"></i>Voltar</span>
                </div>
            </div>
            <div class="grid-content">
                <legend>Recurso: <strong><?= $row["display_text"] ?></strong></legend>

                <table id="series" class='table table-mod-2'>
                    <thead>
                        <tr>
                            <th>Hora Começo</th>
                            <th>Hora Fecho</th>
                            <th title="Dia da Semana">D.S. Começa</th>
                            <th title="Dia da Semana">D.S. Fexa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT id_serie,id_resource, start_time, end_time, day_of_week_start, day_of_week_end FROM sips_sd_series WHERE id_resource=$id_resource;";
                        $result = mysql_query($query, $link);
                        while ($row = mysql_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td><?= substr($row["start_time"], 0, -3) ?></td>
                                <td><?= substr($row["end_time"], 0, -3) ?></td>
                                <td><?= nr2dias($row["day_of_week_start"]) ?></td>
                                <td><?= nr2dias($row["day_of_week_end"]) ?>
                                    <div class="view-button"><span class='btn btn-mini' title='Eliminar' onclick="del(<?= $row["id_serie"] ?>, this)" ><i class='icon-trash'></i>Eliminar</span></div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="clear"></div>
                <div class="seperator_dashed"></div>
                <form class="form-horizontal" id="rsc">

                    <div class="control-group">
                        <label class="control-label">Inicio:</label>
                        <div class="controls">
                            <input type="text" maxlength="5" value="" readonly id="beg" style="width: 40px"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Fim:</label>
                        <div class="controls">
                            <input type="text" maxlength="5" value="" readonly id="end" style="width: 40px"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Começa no dia da semana:</label>
                        <div class="controls">

                            <select id="d_start">
                                <option value="1">Segunda</option>
                                <option value="2">Terça</option>
                                <option value="3">Quarta</option>
                                <option value="4">Quinta</option>
                                <option value="5">Sexta</option>
                                <option value="6">Sábado</option>
                                <option value="7">Domingo</option>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Acaba no dia da semana:</label>
                        <div class="controls">
                            <select id="d_end">
                                <option value="1">Segunda</option>
                                <option value="2">Terça</option>
                                <option value="3">Quarta</option>
                                <option value="4">Quinta</option>
                                <option value="5">Sexta</option>
                                <option value="6">Sábado</option>
                                <option value="7">Domingo</option>
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

        <div id="dialog-confirm" title="Resultado"  >
            <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
        </div>
        <script>
                        function nr2dias(nr) {
                            switch (parseInt(nr)) {
                                case 1:
                                    return "Segunda";
                                case 2:
                                    return "Terça";
                                case 3:
                                    return "Quarta";
                                case 4:
                                    return "Quinta";
                                case 5:
                                    return "Sexta";
                                case 6:
                                    return "Sábado";
                                case 7:
                                    return "Domingo";
                                default:
                                    return "Erro";
                            }
                        }

                        var rsc = <?= $id_resource ?>;
                        
                        $(function() {
                            $.datepicker.setDefaults($.datepicker.regional["pt"]);
                            $('#beg').timepicker({});
                            $('#end').timepicker({});
                        });
                        
                        var otable = $('#series').dataTable({
                            "sPaginationType": "full_numbers",
                            "oLanguage": {
                                "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
                            }
                        });
                        
                        $('#saveForm').click(function() {
                            if (verify()) {
                                $.post("../ajax/rsc_series_do.php", {
                                    id: rsc,
                                    beg: $('#beg').val(),
                                    end: $('#end').val(),
                                    d_start: $('#d_start').val(),
                                    d_end: $('#d_end').val()
                                },
                                function(data) {
                                    $("#rsc")[0].reset();
                                    otable.dataTable().fnAddData([data.time_start,
                                        data.time_end,
                                        nr2dias(data.d_start),
                                        nr2dias(data.d_end) +
                                                '<div class="view-button"><span class=\'btn btn-mini\' title=\'Eliminar\' onclick="del(' + data.id + ', this)" ><i class=\'icon-trash\'></i>Eliminar</span></div>'
                                    ]);
                                }, "json").fail(function() {
                                    showDialog("Sucedeu-se um erro.");
                                });
                            }
                        });
                        
                        function showDialog(msg) {
                            $('#alertbox').html(msg);
                            $('#dialog-confirm').dialog({
                            modal: true,
                            buttons: {
                                Ok: function() {
                                    $(this).dialog("close");
                                }
                            }
                        });
                        }
                        
                        function verify() {
                            $('#beg').removeClass('alert');
                            $('#end').removeClass('alert');
                            var result1 = Date.parse('01/01/2011 ' + $('#beg').val()) < Date.parse('01/01/2011 ' + $('#end').val());
                            if (!result1) {
                                $('#beg').addClass('alert');
                                $('#end').addClass('alert');
                            }
                            $('#d_start').removeClass('alert');
                            $('#d_end').removeClass('alert');
                            var result2 = (parseInt($('#d_start').val()) <= parseInt($('#d_end').val()));
                            if (!result2) {
                                $('#d_start').addClass('alert');
                                $('#d_end').addClass('alert');
                            }
                            return (result1 && result2);
                        }

                        function confirma(msg) {
                            var def = $.Deferred();
                            $('#alertbox').html(msg);
                            $("#dialog-confirm").dialog({
                                resizable: false,
                                modal: true,
                                buttons: {
                                    Sim: function() {
                                        $(this).dialog("close");
                                        def.resolve();
                                    },
                                    Cancelar: function() {
                                        $(this).dialog("close");
                                        def.reject();
                                    }
                                }
                            });
                            return def.promise();
                        }
                        
                        function del(nr, r) {
                            confirma("Deseja eliminar a serie?").done(function() {
                                var nTr = otable.fnGetPosition($(r).closest("tr").get(0));
                                $.post("../ajax/rsc_series_do.php", {
                                    nr: nr
                                },
                                function(data) {
                                    if (data.sucess) {
                                        otable.fnDeleteRow(nTr);
                                    } else
                                        showDialog("Sucedeu-se um erro.");
                                }, "json").fail(function() {
                                    showDialog("Sucedeu-se um erro.");
                                });
                            });
                        }
        </script>

    </body>
</html>

