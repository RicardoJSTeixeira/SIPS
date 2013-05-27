<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {$far.="../";}
define("ROOT", $far);
require(ROOT . "ini/header.php");
require_once('../func/reserve_utils.php');
if (isset($_GET['sch'])) {
    $id_scheduler = preg_replace($only_nr, '', $_GET['sch']);
} else {
    exit;
}?>

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
</style>
<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='<?php echo ROOT; ?>images/icons/calendar_32.png' /></td>
            <td id='submenu-title'> Editar calendário </td>
            <td><span style='float: right;cursor: pointer;' onclick="location='rsc_admin.php?sch=<?php echo $id_scheduler; ?>'"><img src='<?php echo ROOT; ?>images/icons/resultset_previous_32.png' >Voltar</span></td>
        </tr>
    </table>
</div>

<?php
$query = "Select `display_text`,`alias_code`,`days_visible`,`blocks`,`begin_time`,`end_time`,`active` From sips_sd_schedulers Where id_scheduler=$id_scheduler;";
$result = mysql_query($query, $link);
$row = mysql_fetch_assoc($result);
?>
<div id=work-area>
    
     <div style="width:400px;margin:20px auto 0;" class="cc-mstyle"><h1 style="text-align: center;">Calendário: <strong><?php echo $row[display_text]; ?></strong></h1></div>

<form id="sch">
        <div id="main" class="cc-mstyle" style='border: none;margin-top: 20px;clear: both;'>
            <ul>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Nome
                        </p>
                    </div>
                    <input type="text" maxlength="255" value="<?php echo $row[display_text]; ?>" name="display_text"/>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Codigo de Referência
                        </p>
                    </div>
                    <input type="text" maxlength="255" value="<?php echo $row[alias_code]; ?>" name="alias_code"/>
                </li>
                <li style="display:none">
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Dias visiveis
                        </p>
                    </div>
                    <input type="text" maxlength="3" value="<?php echo $row[days_visible]; ?>" name="display_days" style="text-align: right; width: 30px;"/>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Blocos
                        </p>
                    </div>
                    <select name="blocks">
                        <?php
                        for ($i = 5; $i <= 120; $i+=5) {
                            echo "<option value='$i' " . (($i == $row[blocks]) ? "Selected" : "") . ">" . m2h($i) . "</option>";
                        }
                        ?>
                    </select>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Horário de funcionamento
                        </p>
                    </div>
                    <select name="begin_time" id="begin_time">
                        <?php
                        for ($i = 0; $i < 1440; $i+=15) {
                            echo "<option value='$i' " . (($i == $row[begin_time]) ? "Selected" : "") . ">" . m2h($i, true) . "</option>";
                        }
                        ?>
                    </select>
                    <select name="end_time" id="end_time">
                        <?php
                        for ($i = 0; $i < 1440; $i+=15) {
                            echo "<option value='$i' " . (($i == $row[end_time]) ? "Selected" : "") . ">" . m2h($i, true) . "</option>";
                        }
                        ?>
                    </select>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Activo
                        </p>
                    </div>
                    <div id="radio" style="display: inline;">
                        <input type="radio" value="1" id="activo" name="active" <?php echo ($row[active]==1)? "checked='checked'":"" ?> /><label for="activo">Activo</label>
                        <input type="radio" value="0" id="inactivo" name="active" <?php echo ($row[active]==1)? "":"checked='checked'" ?> /><label for="inactivo">Inactivo</label>
                    </div>
                </li> 
                <li>
                    <input type="button" class="styled-button" id="saveForm" value="Guardar">
                </li>
            </ul>
        </div>
    </form>
</div>

<div id="dialog-confirm" title="Resultado"  style="display: none;">
    <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
</div>

<script>
    $(function() {
        $("input:button").button();
    });
    
    $(function() {
	$( "#radio" ).buttonset();
	});
    
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
        
    $('#saveForm').click(function() {
        if (verify()){
            d=$('#sch').serialize();
            d += "&id=" + encodeURIComponent(<?php echo $id_scheduler; ?>);
            $.post("../ajax/sch_edita_do.php", d,
            function(data) {if(data.sucess==1)
                showDialog("Editado com sucesso.");
            else
                showDialog("Sucedeu-se um erro.");
               },"json").fail(function(){
                showDialog("Sucedeu-se um erro.");})
        }
    });
        
    function verify(){
        var result1= ($("#sch :input:text,textarea,select").removeClass('alert').filter(function() {return !/\S+/.test($(this).val());}).addClass('alert').size() == 0);
        $('#begin_time').removeClass('alert');
        $('#end_time').removeClass('alert');
        var result2= (parseInt($('#begin_time').val())<parseInt($('#end_time').val()));
        if (!result2){
            $('#begin_time').addClass('alert');
            $('#end_time').addClass('alert');
        }
        return (result1 && result2);
    }
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
require(ROOT . "ini/footer.php");
?>

