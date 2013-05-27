<?php
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {
    $far.="../";
}
define("ROOT", $far);
require(ROOT . "ini/header.php");
require_once '../func/reserve_utils.php';
if (isset($_GET['rsc'])) {
    $id_resource = preg_replace($only_nr, '', $_GET['rsc']);
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

    <?php 
    $query= "Select a.id_scheduler,a.id_resource,a.display_text,a.alias_code,a.active,a.restrict_days,(SELECT count(id_serie)From sips_sd_series WHERE id_resource=$id_resource) series,(SELECT count(id_execao)From sips_sd_execoes WHERE id_resource=$id_resource) execoes From sips_sd_resources a Where a.id_resource=$id_resource";
    $result= mysql_query($query, $link);
    $row=  mysql_fetch_assoc($result);
    ?>

<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='<?php echo ROOT; ?>images/icons/calendar_32.png' ></td>
            <td id='submenu-title'> Criar recurso </td>
            <td><span style='float: right;cursor: pointer;' onclick="location='rsc_admin.php?sch=<?php echo  $row[id_scheduler] ?>'"><img src='<?php echo ROOT; ?>images/icons/resultset_previous_32.png' >Voltar</span></td>
        </tr>
    </table>
</div>

<div id=work-area>
       
    <div style="width:400px;margin:20px auto 0;" class="cc-mstyle"><h1 style="text-align: center;">Recurso: <strong><?php echo $row[display_text]; ?></strong></h1></div>
    
   <form id="rsc">
        <div id="main" class="cc-mstyle" style='border: none;margin-top: 20px;clear: both;'>
            <ul>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Nome
                        </p>
                    </div>
                    <input type="text" maxlength="255" value="<?php echo $row[display_text] ?>" name="display_text"/>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Codigo de Referência
                        </p>
                    </div> 
                    <input type="text" maxlength="255" value="<?php echo $row[alias_code] ?>" name="alias_code"/>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Activo
                        </p>
                    </div>
                    <div id="activos" style="display: inline;">
                        <input type="radio" value="1" id="activo" name="active" <?php echo ($row[active]==1)? "checked='checked'":"" ?> /><label for="activo">Activo</label>
                        <input type="radio" value="0" id="inactivo" name="active" <?php echo ($row[active]==1)? "":"checked='checked'" ?> /><label for="inactivo">Inactivo</label>
                    </div>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Bloqueios Invertidos
                        </p>
                    </div>
                    <div id="invertidos" style="display: inline;">
                        <input type="radio" value="1" id="invertido" name="inverted" <?php echo ($row[restrict_days]==1)? "checked='checked'":"" ?> /><label for="invertido">Sim</label>
                        <input type="radio" value="0" id="ninvertido" name="inverted" <?php echo ($row[restrict_days]==1)? "":"checked='checked'" ?> /><label for="ninvertido">Não</label> 
                    </div>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Series
                        </p>
                    </div>
                    <div onclick="location='rsc_series.php?rsc=<?php echo $id_resource; ?>'" style="border:solid 1px #c0c0c0;height: 28px;display:inline-block;padding-top: 2px;cursor:pointer;"><?php echo $row[series]; ?><img src="<?php echo ROOT; ?>images/icons/livejournal_16.png"></div>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Execoes
                        </p>
                    </div>
                    <div onclick="location='rsc_execoes.php?rsc=<?php echo $id_resource; ?>'" style="border:solid 1px #c0c0c0;height: 28px;display:inline-block;padding-top: 2px;cursor:pointer;"><?php echo $row[execoes]; ?><img src="<?php echo ROOT; ?>images/icons/livejournal_16.png"></div>
                </li>
                <li>
                    <input type="button" class="styled-button" id="saveForm" value="Editar">
                </li>
            </ul>
        </div>
    </form>
</div>

<div id="dialog-confirm" title="Resultado"  style="display: none;">
    <p><span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;"></span><span id="alertbox"></span></p>
</div>
<script>
    $(function () {
     $("input:button").button();
 });

 $(function () {
     $("#activos").buttonset();
     $("#invertidos").buttonset();
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

 $('#saveForm').click(function () {
     if (verify()) {
         d = $('#rsc').serialize();
         d += "&id=" + encodeURIComponent( <?php echo $id_resource; ?> );
         $.post("../ajax/rsc_edita_do.php", d,

         function (data) {
             if (data.sucess == 1){
                showDialog("Editado com sucesso.");}
             else{
                showDialog("Sucedeu-se um erro.");}
         }, "json").fail(function () {
             showDialog("Sucedeu-se um erro.");
         })
     }
 });

 function verify() {
     return ($("#sch :input:text,textarea,select").removeClass('alert').filter(function () {
         return !/\S+/.test($(this).val());
     }).addClass('alert').size() == 0);
 }
</script>
<?php
require(ROOT . "ini/footer.php");
?>

