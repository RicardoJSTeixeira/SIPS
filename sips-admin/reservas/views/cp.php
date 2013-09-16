<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {
    $far.="../";
}
define("ROOT", $far);
require(ROOT . "ini/header.php");
require '../func/reserve_utils.php';
$is_upload = $_FILES["ref_file"]["error"] == 0 && isset($_FILES["ref_file"]);
$PHP_SELF = $_SERVER['PHP_SELF'];
?>
<style>
    label { display: inline-block !important ;
            margin-top: 3px !important;}
    label > span {padding: 3px 5px !important }
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
    #ccp{
        min-height: 380px;
        max-width: 70%;
        margin:30px auto 10px;
    }
    .x{
        margin-right:5px;
        vertical-align: middle;
        cursor:pointer;
    }
</style> 
<style>
    #dialog-form label, #dialog-form input { display:block; }
    #dialog-form input.text { margin-bottom:12px; width:95%; padding: .4em; }
    #dialog-form fieldset { padding:0; border:0; margin-top:25px; }
    #dialog-form h1 { font-size: 1.2em; margin: .6em 0; }
    .ui-dialog .ui-state-error { padding: .3em; }
    .validateTips { border: 1px solid transparent; padding: 0.3em; }
</style>
<?php
if ($is_upload) {


#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
    $stmt = "SELECT admin_web_directory FROM system_settings;";
    $rslt = mysql_query($stmt, $link);
    $qm_conf_ct = mysql_num_rows($rslt);
    if ($qm_conf_ct > 0) {
        $row = mysql_fetch_row($rslt);
        $admin_web_directory = $row[0];
    }
##### END SETTINGS LOOKUP #####
###########################################

    $file_name = $_FILES["ref_file"]["name"];
    if (preg_match("/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$/i", $file_name)) {
        $file_name = ereg_replace("[^-\.\_0-9a-zA-Z]", "_", $file_name);
        copy($_FILES["ref_file"]["tmp_name"], "/tmp/$file_name");
        $new_filename = preg_replace("/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$/i", '.txt', $file_name);
        $convert_command = "$WeBServeRRooT/$admin_web_directory/sheet2tab.pl /tmp/$file_name /tmp/$new_filename";
        passthru("$convert_command");
        $file_path = "/tmp/$new_filename";

        if (preg_match("/\.csv$/i", $file_name)) {
            $delim_name = "CSV: Comma Separated Values";
        }
        if (preg_match("/\.xls$/i", $file_name)) {
            $delim_name = "XLS: MS Excel 2000-XP";
        }
        if (preg_match("/\.xlsx$/i", $file_name)) {
            $delim_name = "XLSX: MS Excel 2007+";
        }
        if (preg_match("/\.ods$/i", $file_name)) {
            $delim_name = "ODS: OpenOffice.org OpenDocument Spreadsheet";
        }
        if (preg_match("/\.sxc$/i", $file_name)) {
            $delim_name = "SXC: OpenOffice.org First Spreadsheet";
        }
        $delim_set = 1;
    }

    $file = fopen("$file_path", "r");

    $buffer = fgets($file, 4096);
    $tab_count = substr_count($buffer, "\t");
    $pipe_count = substr_count($buffer, "|");

    if ($delim_set < 1) {
        if ($tab_count > $pipe_count) {
            $delim_name = "tab-delimited";
        } else {
            $delim_name = "pipe-delimited";
        }
    }
    if ($tab_count > $pipe_count) {
        $delimiter = "\t";
    } else {
        $delimiter = "|";
    }
    $field_check = explode($delimiter, $buffer);

    if (count($field_check) >= 1) {
        mysql_query("Delete FROM sips_sd_cp where 1;", $link);
        flush();
        $file = fopen("$file_path", "r");

        $good = 0;
        $bad = 0;
        while (!feof($file)) {

            $buffer = rtrim(fgets($file, 4096));
            $buffer = stripslashes($buffer);



            if (strlen($buffer) > 0) {
                $record++;
                $row = explode($delimiter, eregi_replace("[\'\"]", "", $buffer));

                if (strlen(preg_replace($only_nr, '', $row[0])) > 0 && strlen($row[1]) > 0) {
                    $query = "Insert Into sips_sd_cp Values(NULL,'" . mysql_real_escape_string(preg_replace($only_nr, '', $row[0])) . "','" . mysql_real_escape_string($row[1]) . "');";
                    (mysql_query($query, $link))?$good++:$bad++;
                } else {
                    $bad++;
                }
            } else {
                #vazio
            }
        }
    }
}
?>


<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='/images/icons/calendar_32.png' ></td>
            <td id='submenu-title'> Editar Codigos Postais </td>
            <td><span style='float: right;cursor: pointer;' onclick="location='rsc_admin.php?sch=<?= $row[id_scheduler] ?>'"><img src='/images/icons/resultset_previous_32.png' >Voltar</span></td>
        </tr>
    </table>
</div>
<div id="work-area" style="min-height: 400px;">
    <form id="fileForm" method="POST" action=<?= $PHP_SELF ?> enctype="multipart/form-data">
        <div id="main" class="cc-mstyle" style='border: none;margin-top: 20px;clear: both;'>
            <ul>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Escolha um ficheiro
                        </p>
                    </div>
                    <input type="file" name="ref_file" id="ref_file"/>
                </li>

                <li id="result" style="visibility:<?=($is_upload)?"visible":"hidden"?>">
                    Sucessos:<span id="good"></span>
                    Erros:<span id="bad"></span>
                    Total:<span id="total"></span>
                </li>
                <li>
                    <div style="font-size:12px;width:80%;margin:0pt auto 20px auto;color:#6D6D6D;">
                        A folha de calculo tem de ter, na primeira coluna, o Código Postal, e na segunda coluna, o Técnico.
                    </div>
                </li>
                <li>
                    <input type="button" class="styled-button" id="saveForm" value="Submeter pedido"/>
                </li>
            </ul>
        </div>
    </form>
    <div id="ccp" style="opacity:0">
        <input type="button" value="Adicionar Codigo" id="add" style="float: right" class="styled-button" />
        <table id="cp">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Técnico</th>
                </tr>
            </thead>
            <tbody>
<?php
$query = "SELECT id_cp, cp, tecnico FROM sips_sd_cp";
$result = mysql_query($query);

while ($row1 = mysql_fetch_assoc($result)) {
    echo "<tr><td><img src='/images/icons/cross_16.png' title='Eliminar' onclick='del($row1[id_cp],this)' class='x' />$row1[cp]</td><td>$row1[tecnico]</td></tr>";
}
?>
            </tbody>
        </table>
    </div>
</div>

<div id="dialog-confirm" title="Resultado"  style="display: none;">
    <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
</div>

<div id="dialog-form" title="Adicionar Codigo">
    <p class="validateTips">Todos os campos são obrigatórios.</p>

    <form>
        <fieldset>
            <label for="name">Codigo Postal</label>
            <input type="text" id="cp4" class="text ui-widget-content ui-corner-all" />
            <label for="email">Tecnico</label>
            <input type="text" id="ref" value="" class="text ui-widget-content ui-corner-all" />
        </fieldset>
    </form>
</div>
<script>
    var otable=$('#cp').dataTable( {
        "bJQueryUI": true,        
        "sDom": '<"top"f>rt<"bottom"p>',
        "sPaginationType": "full_numbers",
        "aoColumns": [
            { "bSortable": true},
            { "bSortable": true}
        ],
        "oLanguage": {
            "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
        }
    } );
    
    function ext_valid(a){
	
        var ext = $('#ref_file').val().split('.').pop().toLowerCase();
        if($.inArray(ext, ['csv','xls','xlsx','ods','sxc']) == -1) {
            if(a){showDialog("Extenção invalida.");}
            $("#saveForm").button("disable");
        }
        else
        {  
            $("#saveForm").button("enable");
        }
    }   
    
    $(function() {
        $("#saveForm").button();
        $("#saveForm").button("disable");
    });
    
    $("#saveForm").click(function(e){
        confirma("Ao confirmar a actualização, todas as referências de Código Postal serão substiuidas pelas do ficheiro por si seleccionado.").done(function(){$("#fileForm").submit()}).fail(function(){e.preventDefault()})
    })
    
    $("#ref_file").change(function(){ext_valid(true)});     
   
<?php if ($is_upload) { ?>
                function actualiza(good,bad,total){
                    $('#good').html(good);
                    $('#bad').html(bad);
                    $('#total').html(total);
                }
<?php } ?>
        
        function showDialog(msg){
            $('#alertbox').html(msg);
            $('#dialog-confirm').dialog({
                modal : true,
                buttons : {
                    Ok : function() {
                        $(this).dialog("close");
                    }
                }
            })};
    
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
        
        $(document).ready(
        function(){
            ext_valid(false);
            $("#ccp").animate({opacity:1});
        }); 
        
        
        
        //edit ----------------------------------------------------------------------------------------------------------
    
        function del(nr, r) {
            (confirma("Deseja eliminar a serie?").done(function () {
                var nTr = otable.fnGetPosition($(r).closest("tr").get(0));
                $.post("../ajax/cp_do.php", {
                    nr: nr
                },
                function (data) {
                    if (data.sucess == 1) {
                        otable.fnDeleteRow(nTr);
                    } else showDialog("Sucedeu-se um erro.");
                }, "json").fail(function () {
                    showDialog("Sucedeu-se um erro.");
                });
            }));
        } 

        function save(cp,ref) {
            $.post("../ajax/cp_do.php", {
                cp: cp,
                ref: ref
            },
            function (data) {
                otable.dataTable().fnAddData(['<img src="/images/icons/cross_16.png" title="Eliminar" style="cursor:pointer;" class="x"  onclick=del(' + data.id + ',this) >'+data.cp, data.ref]);
            }, "json").fail(function () {
                showDialog("Sucedeu-se um erro.");
            });
        }
        
        
        $(function() {
				
            var name = $( "#name" ),
            cp4 = $( "#cp4" ),
            ref = $( "#ref" ),
            allFields = $( [] ).add( cp4 ).add( ref ),
            tips = $( ".validateTips" );

            function updateTips( t ) {
                tips
                .text( t )
                .addClass( "ui-state-highlight" );
                setTimeout(function() {
                    tips.removeClass( "ui-state-highlight", 1500 );
                }, 500 );
            }

            function checkLength( o, n, min, max ) {
                if ( o.val().length > max || o.val().length < min ) {
                    o.addClass( "ui-state-error" );
                    updateTips( "Comprimento " + n + " tem de ser entre " +
                        min + " e " + max + "." );
                    return false;
                } else {
                    return true;
                }
            }
		
            $( "#dialog-form" ).dialog({
                autoOpen: false,
                height: 300,
                width: 350,
                modal: true,
                buttons: {
                    "Ok": function() {
                        var bValid = true;
                        allFields.removeClass( "ui-state-error" );

                        bValid = bValid && checkLength( cp4, "Codigo Postal", 1, 200 );
                        bValid = bValid && checkLength( ref, "Tecnico", 1, 200 );

                        if ( bValid ) {
                            save(cp4.val(),ref.val()); 
                            $( this ).dialog( "close" );
                        }
                    },
                    Cancel: function() {
                        $( this ).dialog( "close" );
                    }
                },
                close: function() {
                    allFields.val( "" ).removeClass( "ui-state-error" );
                }
            });
            $( "#add" )
            .button()
            .click(function() {
                $( "#dialog-form" ).dialog( "open" );
            });
        });
    
    
</script>



<?php
if ($is_upload) {
    print "<script>actualiza($good,$bad,$record)</script>";
}
#FOOTER
require(ROOT . "ini/footer.php");
?>