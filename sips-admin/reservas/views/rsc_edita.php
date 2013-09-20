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
        require '../func/reserve_utils.php';
        if (isset($_GET['rsc'])) {
            $id_resource = preg_replace($only_nr, '', $_GET['rsc']);
        } else {
            exit;
        }

        $query = "Select a.id_scheduler,a.id_resource,a.display_text,a.alias_code,a.active,a.restrict_days,(SELECT count(id_serie)From sips_sd_series WHERE id_resource=$id_resource) series,(SELECT count(id_execao)From sips_sd_execoes WHERE id_resource=$id_resource) execoes From sips_sd_resources a Where a.id_resource=$id_resource";
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
    <div class='grid' >
        <div class="grid-title">
            <div class="pull-left">Editar recurso</div>
            <div class="pull-right">
                <span class="btn" onclick="location = 'rsc_admin.php?sch=<?= $row["id_scheduler"] ?>'"><i class="icon-arrow-left"></i>Voltar</span>
            </div>
        </div>
        <div class="grid-content">
            <legend>Recurso: <strong><?= $row["display_text"] ?></strong></legend>

            <form class="form-horizontal" id="rsc">

                <div class="control-group">
                    <label class="control-label">Nome:</label>
                    <div class="controls">
                        <input type="text" maxlength="255" value="<?= $row["display_text"] ?>" name="display_text" class="span"/>
                    </div>
                </div>

                <div class="control-group">
                    <label class="control-label">Código de Referência:</label>
                    <div class="controls">
                        <input type="text" maxlength="255" value="<?= $row["alias_code"] ?>" name="alias_code" class="span"/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Activo:</label>
                    <div class="controls">
                        <input type="radio" value="1" id="activo" name="active" <?= ($row["active"] == 1) ? "checked" : "" ?> /><label for="activo" class="inline radio"><span></span>Activo</label>
                        <input type="radio" value="0" id="inactivo" name="active" <?= ($row["active"] == 1) ? "" : "checked" ?> /><label for="inactivo" class="inline radio"><span></span>Inactivo</label>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Bloqueios Invertidos:</label>
                    <div class="controls">
                        <input type="radio" value="1" id="invertido" name="inverted" <?= ($row["restrict_days"] == 1) ? "checked='checked'" : "" ?> /><label for="invertido" class="inline radio"><span></span>Sim</label>
                        <input type="radio" value="0" id="ninvertido" name="inverted" <?= ($row["restrict_days"] == 1) ? "" : "checked='checked'" ?> /><label for="ninvertido" class="inline radio"><span></span>Não</label> 
                    </div>
                </div>


                <div class="control-group">
                    <label class="control-label">Series:</label>
                    <div class="controls">
                        <span class="btn" onclick="location = 'rsc_series.php?rsc=<?= $id_resource; ?>'" ><?= $row[series]; ?> <i class="icon-pencil"></i>Editar</span>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Execoes:</label>
                    <div class="controls">
                        <span class="btn" onclick="location = 'rsc_execoes.php?rsc=<?= $id_resource; ?>'" ><?= $row[execoes]; ?> <i class="icon-pencil"></i>Editar</span>
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <input type="button" class="btn btn-primary" id="saveForm" value="Editar">
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="dialog-confirm" title="Resultado"  style="display: none;">
        <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
    </div>
    <script>

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

                    $('#saveForm').click(function() {
                        if (verify()) {
                            d = $('#rsc').serialize();
                            d += "&id=" + encodeURIComponent(<?= $id_resource; ?>);
                            $.post("../ajax/rsc_edita_do.php", d,
                                    function(data) {
                                        if (data.sucess == 1) {
                                            showDialog("Editado com sucesso.");
                                        }
                                        else {
                                            showDialog("Sucedeu-se um erro.");
                                        }
                                    }, "json").fail(function() {
                                showDialog("Sucedeu-se um erro.");
                            })
                        }
                    });

                    function verify() {
                        return ($("#sch :input:text,textarea,select").removeClass('alert').filter(function() {
                            return !/\S+/.test($(this).val());
                        }).addClass('alert').size() == 0);
                    }
    </script>
</body>
</html>

