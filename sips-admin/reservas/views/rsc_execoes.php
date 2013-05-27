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
    #schedulers td:nth-child(3) { text-align: center }
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
                    <th>Data Começo</th>
                    <th>Data Fecho</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT id_execao,id_resource, start_date, end_date FROM sips_sd_execoes WHERE id_resource=$id_resource;";
                $result = mysql_query($query, $link);
                while ($row = mysql_fetch_assoc($result)) {
                    echo"<tr>
				<td>".substr($row[start_date],0,-3)."</td>
				<td>".substr($row[end_date],0,-3)."</td>
                                <td><img src='".ROOT."images/icons/cross_16.png' title='Eliminar' style='cursor:pointer;' onclick=del($row[id_execao],this) ></td>
			</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th>Data Começo</th>
                    <th>Data Fecho</th>
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
                    <input type="text" maxlength="15" value="" readonly="" id="beg" style="width: 120px"/>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Fim
                        </p>
                    </div>
                    <input type="text" maxlength="15" value="" readonly="" id="end" style="width: 120px"/>
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


var rsc = <?php echo $id_resource; ?> ;

$(function () {
    $("input:button").button();
    $.datepicker.setDefaults($.datepicker.regional["pt"]);
    $('#beg').datetimepicker({dateFormat:"yy/mm/dd"});
    $('#end').datetimepicker({dateFormat:"yy/mm/dd"});
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
        $.post("../ajax/rsc_execoes_do.php", {
            id: rsc,
            beg: $('#beg').val(),
            end: $('#end').val()
        },

        function (data) {
            $("#sch")[0].reset();
            otable.dataTable().fnAddData([data.beg, data.end, '<img src="<?php echo ROOT ?>images/icons/cross_16.png" title="Eliminar" style="cursor:pointer;" onclick=del(' + data.id + ',this) >']);
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
    var result = Date.parse( $('#beg').val()) < Date.parse($('#end').val());
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
          $.post("../ajax/rsc_execoes_do.php", {
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

