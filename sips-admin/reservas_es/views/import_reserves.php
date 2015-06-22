<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {
    $far.="../";
}
define("ROOT", $far);
require(ROOT . "ini/header.php");
require '../func/reserve_utils.php';
$is_upload = $_FILES["ref_file"]["error"] == 0 && isset($_FILES["ref_file"]);
$PHP_SELF = $_SERVER['PHP_SELF'];

function mes2nr($mes){
    switch ($mes) {
        case "Janeiro": return 1;
        case "Fevereiro": return 2;
        case "Março": return 3;
        case "Abril": return 4;
        case "Maio": return 5;
        case "Junho": return 6;
        case "Julho": return 7;
        case "Agosto": return 8;
        case "Setembro": return 9;
        case "Outubro": return 10;
        case "Novembro": return 11;
        case "Dezembro": return 12;

        default:
            return 0;
    }
}
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
$ano=$_POST[ano];

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

                $id_res="";
                $cp4=$row[10];
                $ref=$row[23];
                $time=$row[28];
                $date=$row[29];
                
                if (strlen($ref)>0) {
                    $result=mysql_query("Select id_resource From sips_sd_resources Where alias_code='". mysql_real_escape_string($ref) ."'  limit 1",$link);
                }  else {
                    $result=mysql_query("SELECT id_resource From sips_sd_resources a Inner Join sips_sd_cp b ON a.alias_code=b.tecnico Where cp='".$cp4."' limit 1",$link);
                }
                $id_res= mysql_fetch_array($result);
                $id_res= $id_res[0];
                
                
                $expl_date=explode(" ", $date);
                $dia=$expl_date[0];
                $mes=  mes2nr($expl_date[2]);
                $date_parsed=$ano."-".$mes."-".$dia." ".$time.":00";
                
                
                if (strtotime($date_parsed)) {
                    $query = "Insert Into sips_sd_reservations_acu (`start_date`, `id_reservation_type`, `id_resource`) Values('" . mysql_real_escape_string($date_parsed) . "','" . mysql_real_escape_string(0) . "','" . mysql_real_escape_string($id_res) . "');";
                    (mysql_query($query, $link))?$good++:$bad++;
                } else {
                    $bad++;
                }
            } else {
                #vazio
            }
        }
        
            echo '<iframe src="dup.php" style="display:none"></iframe>';
    }
}
?>


<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='<?php echo ROOT; ?>images/icons/calendar_32.png' ></td>
            <td id='submenu-title'> Importação de reservas</td>
            <td><span style='float: right;cursor: pointer;' onclick="location='rsc_admin.php?sch=<?php echo $row[id_scheduler] ?>'"><img src='<?php echo ROOT; ?>images/icons/resultset_previous_32.png' >Voltar</span></td>
        </tr>
    </table>
</div>
<div id="work-area" style="min-height: 400px;">
    <form id="fileForm" method="POST" action=<?php echo $PHP_SELF ?> enctype="multipart/form-data">
        <div id="main" class="cc-mstyle" style='border: none;margin-top: 20px;clear: both;'>
            <ul>
                <li>
                    <div class="cc-mstyle" style="height: 28px;">
                        <p>
                            Ver duplicados
                        </p>
                    </div>
                    <a href="dup.php">Carregar aqui para fazer download</a>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Escolha um ficheiro
                        </p>
                    </div>
                    <input type="file" value="" name="ref_file" id="ref_file"/> 
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Ano
                        </p>
                    </div>
                    <input name="ano" id="ano" type="text" maxlength="4" required="" class="num" style="width: 60px;text-align: right" value="<?php echo date("Y") ?>"/>
                </li> 
                <li id="result" style="visibility:<?php if ($is_upload) { ?> visible <?php } else { ?> hidden<?php } ?>">
                    Sucessos:<span id="good"></span>
                    Erros:<span id="bad"></span>
                    Total:<span id="total"></span>
                </li>
                <li>
                    <input type="button" class="styled-button" id="saveForm" value="Submeter pedido"/>
                </li>
            </ul>
        </div>
    </form>
</div>

<div id="dialog-confirm" title="Resultado"  style="display: none;">
    <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
</div>

<script>
   
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
        confirma("Tem a certeza que deseja importar estas marcações?").done(function(){$("#fileForm").submit()}).fail(function(){e.preventDefault()})
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
        }); 
        
        $(document).ready(function() {
        $(".num").keydown(function(event) {
            if((!event.shiftKey && !event.ctrlKey && !event.altKey) && ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105))) {
            } else if(event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39) {
                event.preventDefault();
            }
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