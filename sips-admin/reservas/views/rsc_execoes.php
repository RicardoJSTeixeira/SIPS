<!DOCTYPE html>
<html> 
    <head> 
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
        require_once('../func/reserve_utils.php');
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
            input.alert {
                border: solid 1px red !important;
                box-shadow:0 0 3px 1px rgba(255, 0, 0, 0.5) !important;
            }
        </style>
    </head>
    <body>
        <div class='grid' >
            <div class="grid-title">
                <div class="pull-left">Editar exeções</div>
                <div class="pull-right">
                    <span class="btn" onclick="location = 'rsc_edita.php?rsc=<?= $id_resource ?>'"><i class="icon-arrow-left"></i>Voltar</span>
                </div>
            </div>

            <div class="grid-content">
                <legend>Recurso: <strong><?= $row[display_text] ?></strong></legend>

                <table id="schedulers" class='table table-mod-2'>
                    <thead>
                        <tr>
                            <th>Data Começo</th>
                            <th>Data Fecho</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query = "SELECT id_execao,id_resource, start_date, end_date FROM sips_sd_execoes WHERE id_resource=$id_resource;";
                        $result = mysql_query($query, $link);
                        while ($row = mysql_fetch_assoc($result)) {
                            ?>
                            <tr>
                                <td><?= substr($row[start_date], 0, -3) ?></td>
                                <td><?= substr($row[end_date], 0, -3) ?>
                                    <div class="view-button"><span class='btn btn-mini' title='Eliminar' onclick='del(<?= $row[id_execao] ?>, this);' ><i class='icon-trash'></i>Eliminar</span></div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

                <div class="clear"></div>
                <div class="seperator_dashed"></div>
                <form class="form-horizontal" id="sch">

                    <div class="control-group">
                        <label class="control-label">Inicio:</label>
                        <div class="controls">
                            <input type="text" maxlength="15" value="" readonly id="beg" />
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Fim:</label>
                        <div class="controls">
                            <input type="text" maxlength="15" value="" readonly id="end" />
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

                        var rsc = <?= $id_resource; ?>;
                        $(function() {
                            $.datepicker.setDefaults($.datepicker.regional["pt"]);
                            $('#beg').datetimepicker({dateFormat: "yy/mm/dd"});
                            $('#end').datetimepicker({dateFormat: "yy/mm/dd"});
                        });
                        var otable = $('#schedulers').dataTable({
                            "sPaginationType": "full_numbers",
                            "oLanguage": {
                                "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
                            }
                        });
                        $('#saveForm').click(function() {
                            if (verify()) {
                                $.post("../ajax/rsc_execoes_do.php", {
                                    id: rsc,
                                    beg: $('#beg').val(),
                                    end: $('#end').val()
                                },
                                function(data) {
                                    $("#sch")[0].reset();
                                    otable.dataTable().fnAddData([data.beg,
                                        data.end +
                                                "<div class='view-button'><span class='btn btn-mini' title='Eliminar' onclick=del(" + data.id + ", this) ><i class='icon-trash'></i>Eliminar</span></div>"]);
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
                            })
                        }

                        function verify() {
                            $('#beg').removeClass('alert');
                            $('#end').removeClass('alert');
                            var result = Date.parse($('#beg').val()) < Date.parse($('#end').val());
                            if (!result) {
                                $('#beg').addClass('alert');
                                $('#end').addClass('alert');
                            }
                            return result;
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
                                $.post("../ajax/rsc_execoes_do.php", {
                                    nr: nr
                                },
                                function(data) {
                                    if (data.sucess == 1) {
                                        otable.fnDeleteRow(nTr);
                                    } else
                                        showDialog("Sucedeu-se um erro.");
                                }, "json").fail(function() {
                                    showDialog("Sucedeu-se um erro.");
                                })
                            })
                        }
        </script>

    </body>
</html>
