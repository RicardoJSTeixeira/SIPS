<?php
#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {$header.="../";}
define("ROOT", $header);
require(ROOT . "ini/header.php");

require_once('../func/reserve_utils.php');
if (isset($_GET['rsc'])) {
    $id_resource = preg_replace($only_nr, '', $_GET['rsc']);
} else {
    exit;
}
?>
<style>
    .alert {
        border: solid 1px red !important;
        box-shadow:0 0 3px 1px rgba(255, 0, 0, 0.5) !important;
    }
    .cc-mstyle > p {
        text-align: center;
    }
    ul {
        margin:0 auto;
    }
    ul, li {
        max-width:800px
    }
    li > .cc-mstyle {
        display: inline-block;
        width:40%;
        margin:2px 0;
    }	
    li > .styled-button {
        display: block;
        margin:10px auto;
    }
    #table_conteiner{
        width:60%;
        margin:20px auto 0;
    }
    #schedulers td:nth-child(5) { text-align: center }
</style>
<?php
$query = "Select `display_text` From sips_sd_resources Where id_resource=$id_resource;";
$result = mysql_query($query, $link);
$row = mysql_fetch_assoc($result);
?>
<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='<?php echo ROOT; ?>images/icons/calendar_32.png' ></td>
            <td id='submenu-title'> Criar recurso </td>
            <td><span style='float: right;cursor: pointer;' onclick="location='rsc_edita.php?rsc=<?php echo  $id_resource ?>'"><img src='<?php echo ROOT; ?>images/icons/resultset_previous_32.png' >Voltar</span></td>
        </tr>
    </table>
</div>

<div id=work-area>
    <div style="width:400px;margin:20px auto 0;" class="cc-mstyle"><h1 style="text-align: center;">Recurso: <strong><?php echo $row[display_text]; ?></strong></h1></div>

    <div id="table_conteiner" class="shad">
        <table id="schedulers">
            <thead>
                <tr>
                    <th>Hora Começo</th>
                    <th>Hora Fecho</th>
                    <th title="Dia da Semana">D.S. Começa</th>
                    <th title="Dia da Semana">D.S. Fexa</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT id_serie,id_resource, start_time, end_time, day_of_week_start, day_of_week_end FROM sips_sd_series WHERE id_resource=$id_resource;";
                $result = mysql_query($query, $link);
                while ($row = mysql_fetch_assoc($result)) {
                    echo"<tr>
				<td>".substr($row[start_time],0,-3)."</td>
				<td>".substr($row[end_time],0,-3)."</td>
				<td>".nr2dias($row[day_of_week_start])."</td>
                                <td>".nr2dias($row[day_of_week_end])."</td>
                                <td><img src='".ROOT."images/icons/cross_16.png' title='Eliminar' style='cursor:pointer;' onclick=del($row[id_serie],this) ></td>
			</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Hora Começo</th>
                    <th>Hora Fecho</th>
                    <th title="Dia da Semana">D.S. Começa</th>
                    <th title="Dia da Semana">D.S. Fexa</th>
                    <th>Eliminar</th>
                </tr>
            </tfoot>
        </table>
    </div>

    <form id="sch">
        <div id="main" class="cc-mstyle" style='border: none;margin-top: 20px;clear: both;'>
            <ul style="max-width:500px">
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Inicio
                        </p>
                    </div>
                    <input type="text" maxlength="5" value="" readonly="" id="beg" style="width: 40px"/>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Fim
                        </p>
                    </div>
                    <input type="text" maxlength="5" value="" readonly="" id="end" style="width: 40px"/>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Começa no dia da semana
                        </p>
                    </div>
                    <select id="d_start">
                        <option value="1">Segunda</option>
                        <option value="2">Terça</option>
                        <option value="3">Quarta</option>
                        <option value="4">Quinta</option>
                        <option value="5">Sexta</option>
                        <option value="6">Sábado</option>
                        <option value="7">Domingo</option>
                    </select>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Começa no dia da semana
                        </p>
                    </div>
                    <select id="d_end">
                        <option value="1">Segunda</option>
                        <option value="2">Terça</option>
                        <option value="3">Quarta</option>
                        <option value="4">Quinta</option>
                        <option value="5">Sexta</option>
                        <option value="6">Sábado</option>
                        <option value="7">Domingo</option>
                    </select>
                </li>
                <li>
                    <input type="button" class="styled-button" id="saveForm" value="Criar">
                </li>
            </ul>
        </div>
    </form>
</div>

<div id="dialog-confirm" title="Resultado"  style="display: none;">
    <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
</div>
<script>   
    function nr2dias(nr) {
    switch (parseInt(nr)) {
    case 1: return "Segunda";
    case 2: return "Terça";
    case 3: return "Quarta";
    case 4: return "Quinta";
    case 5: return "Sexta";
    case 6: return "Sábado";
    case 7: return "Domingo";

    default: return "Erro";
    }
}

var rsc = <?php echo $id_resource; ?> ;

$(function () {
    $("input:button").button();
    $.datepicker.setDefaults($.datepicker.regional["pt"]);
    $('#beg').timepicker({});
    $('#end').timepicker({});
});
var otable = $('#schedulers').dataTable({
    "bJQueryUI": true,
    "sPaginationType": "full_numbers",
    "oLanguage": {
        "sUrl": "<?php echo ROOT; ?>jquery/jsdatatable/language/pt-pt.txt"
    }
});

$('#saveForm').click(function () {
    if (verify()) {
        $.post("../ajax/rsc_series_do.php", {
            id: rsc,
            beg: $('#beg').val(),
            end: $('#end').val(),
            d_start: $('#d_start').val(),
            d_end: $('#d_end').val()
        },

        function (data) {
            $("#sch")[0].reset();
            otable.dataTable().fnAddData([data.time_start, data.time_end, nr2dias(data.d_start), nr2dias(data.d_end), '<img src="<?php echo ROOT ?>images/icons/cross_16.png" title="Eliminar" style="cursor:pointer;" onclick=del(' + data.id + ',this) >']);
        }, "json").fail(function () {
            showDialog("Sucedeu-se um erro.");
        })
    }
});

function showDialog(msg) {
    $('#alertbox').html(msg);
    $('#dialog-confirm').dialog({
        modal: true,
        buttons: {
            Ok: function () {
                $(this).dialog("close");
            }
        }
    })
};

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
        height: 160,
        modal: true,
        buttons: {
            Sim: function () {
                $(this).dialog("close");
                def.resolve();
            },
            Cancelar: function () {
                $(this).dialog("close");
                def.reject();
            }
        }
    });
    return def.promise();
}
    function del(nr, r) {
      (confirma("Deseja eliminar a serie?").done(function () {
          var nTr = otable.fnGetPosition($(r).closest("tr").get(0));
          $.post("../ajax/rsc_series_do.php", {
              nr: nr
          },

          function (data) {
              if (data.sucess == 1) {
                  otable.fnDeleteRow(nTr);
              } else showDialog("Sucedeu-se um erro.");
          }, "json").fail(function () {
              showDialog("Sucedeu-se um erro.");
          })
      }))
  }
</script>


<?php
#FOOTER
require(ROOT . "ini/footer.php");
?>

