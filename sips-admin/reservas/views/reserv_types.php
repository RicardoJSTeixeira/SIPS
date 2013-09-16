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
        <link type="text/css" rel="stylesheet" href="/jquery/colourPicker/colourPicker.css" />

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
        <script type="text/javascript" src="../../../jquery/colourPicker/colourPicker.js"></script>

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
            <div class="pull-left">Tipos de Reserva</div>
            <div class="pull-right">
                <span class="btn" onclick="location = 'sch_admin.php'"><i class="icon-arrow-left"></i>Voltar</span>
            </div>
        </div>

        <div class="grid-content">

            <table id="schedulers" class='table table-mod-2'>
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cor</th>
                        <th>Activo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $user = new user;

                    $query = "SELECT id_reservations_types, display_text, color,active FROM sips_sd_reservations_types WHERE user_group='$user->user_group';";
                    $result = mysql_query($query, $link);
                    while ($row = mysql_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td><?= $row["display_text"] ?></td>
                            <td><div style='background:<?= $row["color"] ?>'>&nbsp;</div></td>
                            <td><?= (($row["active"] == 1) ? "<img style='cursor:pointer' src='/images/icons/tick_16.png' onclick=change('$row[id_reservations_types]',this,0) />" : "<img style='cursor:pointer' src='/images/icons/cross_16.png' onclick=change('$row[id_reservations_types]',this,1) />") ?>
                                <div class="view-button"><span class='btn btn-mini' title='Eliminar' onclick='del(<?= $row["id_reservations_types"] ?>, this)' ><i class='icon-trash'></i>Eliminar</span></div>
                            </td>
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
                        <input type="text" maxlength="255" value="" id="display_text" class='span'/>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label">Cor:</label>
                    <div class="controls">
                        <select id="colour_picker">
                            <option value="ffffff">#ffffff</option>
                            <option value="ffccc9">#ffccc9</option>
                            <option value="ffce93">#ffce93</option>
                            <option value="fffc9e">#fffc9e</option>
                            <option value="ffffc7">#ffffc7</option>
                            <option value="9aff99">#9aff99</option>
                            <option value="96fffb">#96fffb</option>
                            <option value="cdffff">#cdffff</option>
                            <option value="cbcefb">#cbcefb</option>
                            <option value="cfcfcf">#cfcfcf</option>
                            <option value="fd6864">#fd6864</option>
                            <option value="fe996b">#fe996b</option>
                            <option value="fffe65">#fffe65</option>
                            <option value="fcff2f">#fcff2f</option>
                            <option value="67fd9a">#67fd9a</option>
                            <option value="38fff8">#38fff8</option>
                            <option value="68fdff">#68fdff</option>
                            <option value="9698ed">#9698ed</option>
                            <option value="c0c0c0">#c0c0c0</option>
                            <option value="fe0000">#fe0000</option>
                            <option value="f8a102">#f8a102</option>
                            <option value="ffcc67">#ffcc67</option>
                            <option value="f8ff00">#f8ff00</option>
                            <option value="34ff34">#34ff34</option>
                            <option value="68cbd0">#68cbd0</option>
                            <option value="34cdf9">#34cdf9</option>
                            <option value="6665cd">#6665cd</option>
                            <option value="9b9b9b">#9b9b9b</option>
                            <option value="cb0000">#cb0000</option>
                            <option value="f56b00">#f56b00</option>
                            <option value="ffcb2f">#ffcb2f</option>
                            <option value="ffc702">#ffc702</option>
                            <option value="32cb00">#32cb00</option>
                            <option value="00d2cb">#00d2cb</option>
                            <option value="3166ff">#3166ff</option>
                            <option value="6434fc">#6434fc</option>
                            <option value="656565">#656565</option>
                            <option value="9a0000">#9a0000</option>
                            <option value="ce6301">#ce6301</option>
                            <option value="cd9934">#cd9934</option>
                            <option value="999903">#999903</option>
                            <option value="009901">#009901</option>
                            <option value="329a9d">#329a9d</option>
                            <option value="3531ff">#3531ff</option>
                            <option value="6200c9">#6200c9</option>
                            <option value="343434">#343434</option>
                            <option value="680100">#680100</option>
                            <option value="963400">#963400</option>
                            <option value="986536" selected="selected">#986536</option>
                            <option value="646809">#646809</option>
                            <option value="036400">#036400</option>
                            <option value="34696d">#34696d</option>
                            <option value="00009b">#00009b</option>
                            <option value="303498">#303498</option>
                            <option value="000000">#000000</option>
                            <option value="330001">#330001</option>
                            <option value="643403">#643403</option>
                            <option value="663234">#663234</option>
                            <option value="343300">#343300</option>
                            <option value="013300">#013300</option>
                            <option value="003532">#003532</option>
                            <option value="010066">#010066</option>
                            <option value="340096">#340096</option>
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
                        "sPaginationType"
                                : "full_numbers",
                        "aoColumns"
                                : [
                            {"bSortable": true},
                            {"bSortable": true, "sType": "string"},
                            {"bSortable": true, "sType": "string"}
                        ],
                        "oLanguage"
                                : {
                            "sUrl"
                                    : "/jquery/jsdatatable/language/pt-pt.txt"
                        }
                    });
                    $('#saveForm').click(function() {
                        if (verify()) {
                            $.post("../ajax/reserv_types_do.php", {
                                display_text: $('#display_text').val(),
                                color: "#" + $('#colour_picker').val()
                            },
                            function(data) {
                                $("#sch :input:text").val('');
                                otable.dataTable().fnAddData([data.text, "<div style='background:" + data.color + "'>&nbsp;</div>", "<img style='cursor:pointer' src='/images/icons/tick_16.png' onclick=change('" + data.id + "',this,0) />\n\
                                        <div class=\"view-button\"><span class='btn btn-mini' title='Eliminar' onclick'=del(" + data.id + ", this)' ><i class='icon-trash'></i>Eliminar</span></div>"]);
                            }, "json").fail(function() {
                                showDialog("Sucedeu-se um erro.");
                            });
                        }
                    });
                    function del(nr, r) {
                        (confirma("Deseja eliminar a serie?").done(function() {
                            var nTr = otable.fnGetPosition($(r).closest("tr").get(0));
                            $.post("../ajax/reserv_types_do.php", {
                                nr: nr
                            },
                            function(data) {
                                if (data.sucess == 1) {
                                    otable.fnDeleteRow(nTr);
                                } else if (data.sucess == 2) {
                                    showDialog("Não pode eliminar este 'Tipo' pois existem reservas feitas com esta associação.");
                                } else {
                                    showDialog("Sucedeu-se um erro.");
                                }
                            }, "json").fail(function() {
                                showDialog("Sucedeu-se um erro.");
                            });
                        }));
                    }

                    function change(nr, i, f) {
                        var nTr = i;
                        $.post("../ajax/reserv_types_do.php", {
                            id: nr,
                            act: f
                        },
                        function(data) {
                            if (data.sucess == 1) {
                                $(nTr).attr("src", (f == 1) ? "/images/icons/tick_16.png" : "/images/icons/cross_16.png").attr("onclick", (f == 1) ? "change('" + nr + "',this,0)" : "change('" + nr + "',this,1)");
                            } else {
                                showDialog("Sucedeu-se um erro.");
                            }
                        }, "json").fail(function() {
                            showDialog("Sucedeu-se um erro.");
                        });
                    }

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
                    function verify() {
                        return $("#sch :input:text,textarea,select").removeClass('alert').filter(function() {
                            return !/\S+/.test($(this).val());
                        }).addClass('alert').size() == 0;
                    }

                    $(document).ready(function() {
                        $("#table_conteiner").animate({opacity: 1});
                    });
                    $('#colour_picker').colourPicker({
                        ico: '/jquery/colourPicker/colourPicker.gif',
                        title: false
                    });
    </script>

</body>
</html>