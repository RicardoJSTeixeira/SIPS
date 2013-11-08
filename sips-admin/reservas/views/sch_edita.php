<!DOCTYPE html>
<html> 
    <head> 
        <meta charset="utf-8"> 
        <title>Editar calendário</title>
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
    </head>
    <body>
        <div class='grid' >
            <div class="grid-title">
                <div class="pull-left">Editar calendário</div>
                <div class="pull-right">
                    <span class="btn" onclick="location = 'rsc_admin.php?sch=<?= $id_scheduler; ?>'"><i class="icon-arrow-left"></i>Voltar</span>
                </div>
            </div>


            <?php
            $query = "Select `display_text`,`alias_code`,`days_visible`,`blocks`,`begin_time`,`end_time`,`view_postal`,`active` From sips_sd_schedulers Where id_scheduler=$id_scheduler;";
            $result = mysql_query($query, $link);
            $row = mysql_fetch_assoc($result);
            ?>
            <div class="grid-content">

                <legend>Calendário: <strong><?= $row["display_text"] ?></strong></legend>

                <form class="form-horizontal" id="sch">

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
                        <label class="control-label">Dias visiveis:</label>
                        <div class="controls">
                            <input type="text" maxlength="1" value="<?= $row["days_visible"] ?>" name="display_days" class="span1"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Blocos:</label>
                        <div class="controls">
                            <select name="blocks">
                                <?php
                                for ($i = 5; $i <= 120; $i+=5) {
                                    echo "<option value='$i' " . (($i == $row["blocks"]) ? "Selected" : "") . ">" . m2h($i) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Funcionamento:</label>
                        <div class="controls">

                            <select name="begin_time" id="begin_time">
                                <?php
                                for ($i = 0; $i < 1440; $i+=15) {
                                    echo "<option value='$i' " . (($i == $row["begin_time"]) ? "Selected" : "") . ">" . m2h($i, true) . "</option>";
                                }
                                ?>
                            </select>
                            <select name="end_time" id="end_time">
                                <?php
                                for ($i = 0; $i < 1440; $i+=15) {
                                    echo "<option value='$i' " . (($i == $row["end_time"]) ? "Selected" : "") . ">" . m2h($i, true) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Activo:</label>
                        <div class="controls">
                            <input type="radio" value="1" id="activo" name="active" <?= ($row["active"] == 1) ? "checked" : "" ?> /><label for="activo" class="radio inline"><span></span>Activo</label>
                            <input type="radio" value="0" id="inactivo" name="active" <?= ($row["active"] == 1) ? "" : "checked" ?> /><label for="inactivo" class="radio inline"><span></span>Inactivo</label>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label">Codigo Postal:</label>
                        <div class="controls">
                            <input type="checkbox" value="1" id="postal" name="postal" <?= ($row["view_postal"] == 1) ? "checked" : "" ?> /><label for="postal" class="radio inline"><span></span>Ver</label>
                        </div>
                    </div>

                    <div class="control-group">
                        <div class="controls">
                            <input type="button" class="btn btn-primary" id="saveForm" value="Guardar">
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
                                d = $('#sch').serialize();
                                d += "&id=" + encodeURIComponent(<?= $id_scheduler; ?>);
                                $.post("../ajax/sch_edita_do.php", d,
                                        function(data) {
                                            if (data.sucess == 1)
                                                showDialog("Editado com sucesso.");
                                            else
                                                showDialog("Sucedeu-se um erro.");
                                        }, "json").fail(function() {
                                    showDialog("Sucedeu-se um erro.");
                                });
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

