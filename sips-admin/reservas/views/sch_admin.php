<?php
#HEADER
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/header.php");

require_once('../func/reserve_utils.php');
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
        height: 380px;
        width:99%;
        margin:20px auto 0;
    }
    #schedulers td:nth-child(7) { text-align: center }
</style>
<div class=cc-mstyle>
    <table>
        <tr>
            <td id='icon32'><img src='/images/icons/calendar_32.png' /></td>
            <td id='submenu-title'> Criar calendário </td>
            <td ><span style='float:right;cursor:pointer;' onclick="location='reserv_types.php'" >Tipos de reserva<img src='/images/icons/to_do_list_cheked_all_32.png' /></span></td>
        </tr>
    </table>
</div>

<div id=work-area>
    <div id="table_conteiner" style="opacity: 0">
        <table id="schedulers">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Cód. Ref.</th>
                    <th>Dias vis.</th>
                    <th>Blocos</th>
                    <th>Começa</th>
                    <th>Fecha</th>
                    <th>Activo</th>
                </tr>
            </thead>
            <tbody>
                <?php
				$user =$_SERVER['PHP_AUTH_USER'];
				
					$usrQry = mysql_query("SELECT user_group, custom_one from vicidial_users WHERE user = '$user'") or die(mysql_error());
					$usrQry = mysql_fetch_assoc($usrQry);
				
					$usrCode = $usrQry['custom_one'];
					
					//echo "<script type='text/javascript'> console.log(\"SELECT id_scheduler,display_text,alias_code,days_visible,blocks,begin_time,end_time,active FROM sips_sd_schedulers a WHERE a.alias_code in ($usrCode) ;\"); </script>"; 
				$u_g=$usrQry['user_group'];
					if ($u_g=='AreaSalesManager' or $u_g=='AAL' or $u_g=='AMB' or $u_g=='ANU' or $u_g=='FPO' or $u_g=='RGE' or $u_g=="BIO") {
					 $query = "SELECT id_scheduler,display_text,alias_code,days_visible,blocks,begin_time,end_time,active FROM sips_sd_schedulers a WHERE a.alias_code in ($usrCode) ;";
					
					} else {  $query = "SELECT id_scheduler,display_text,alias_code,days_visible,blocks,begin_time,end_time,active FROM sips_sd_schedulers ;"; }
				
               
                $result = mysql_query($query, $link);
                while ($row = mysql_fetch_assoc($result)) {
                    echo"<tr>
				<td><img src='".ROOT."images/icons/livejournal_16.png' title='Editar' style='cursor:pointer;' onclick=location='sch_edita.php?sch=$row[id_scheduler]' ><a href='rsc_admin.php?sch=$row[id_scheduler]'>$row[display_text]</a></td>
				<td>$row[alias_code]</td>
				<td>$row[days_visible]</td>
				<td>" . m2h($row[blocks]) . "</td>
				<td>" . m2h($row[begin_time]) . "</td>
				<td>" . m2h($row[end_time]) . "</td>
				<td>".(($row[active]==1)?" <img src='".ROOT."images/icons/tick_16.png'  >":"<img src='".ROOT."images/icons/cross_16.png' >")."</td>
			</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <form id="sch">
        <div id="main" class="cc-mstyle" style='border: none;clear: both;'>
            <ul>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Nome
                        </p>
                    </div>
                    <input type="text" maxlength="255" value="" name="display_text"/>
                </li>
                <li>
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Codigo de Referência
                        </p>
                    </div>
                    <input type="text" maxlength="255" value="" name="alias_code"/>
                </li>
                <li style="display:none">
                    <div class="cc-mstyle" style='height:28px;'>
                        <p>
                            Dias visiveis
                        </p>
                    </div>
                    <input type="text" maxlength="3" value="7" name="display_days" style="text-align: right; width: 30px;"/>
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
                            echo "<option value='$i' " . (($i == 30) ? "Selected" : "") . ">" . m2h($i) . "</option>";
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
                            echo "<option value='$i' " . (($i == 480) ? "Selected" : "") . ">" . m2h($i, true) . "</option>";
                        }
                        ?>
                    </select>
                    <select name="end_time" id="end_time">
                        <?php
                        for ($i = 0; $i < 1440; $i+=15) {
                            echo "<option value='$i' " . (($i == 1080) ? "Selected" : "") . ">" . m2h($i, true) . "</option>";
                        }
                        ?>
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
   $(function () {
     $("input:button").button();
 });

 var otable = $('#schedulers').dataTable({
     "bJQueryUI": true,
     "sDom": '<"top"f>rt<"bottom"p>',
     "sPaginationType": "full_numbers",
     "aoColumns": [{
         "bSortable": true
     }, {
         "bSortable": true
     }, {
         "bSortable": true
     }, {
         "bSortable": true
     }, {
         "bSortable": true
     }, {
         "bSortable": true
     }, {
         "bSortable": true,
         "sType": "string"
     }],
     "oLanguage": {
         "sUrl": "/jquery/jsdatatable/language/pt-pt.txt"
     }  
 });


 $('#saveForm').click(function () {
     if (verify()) {
         $.post("../ajax/sch_admin_do.php", $('#sch').serialize(),

         function (data) {
             if (data.sucess == 1) {
                 $("#sch")[0].reset();
                 otable.dataTable().fnAddData(['<img src="/images/icons/livejournal_16.png" title="Editar" style="cursor:pointer;" onclick=location="rsc_edita.php?rsc=' + data.id + '" ><a href=rsc_admin.php?sch=' + data.id + '>' + data.display_text + '</a>', data.alias_code, data.display_days, data.blocks, data.begin_time, data.end_time, '<img src="<?php echo ROOT ?>images/icons/tick_16.png"  >']);
             } else {
                 showDialog("Sucedeu-se um erro.");
             }
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
     var result1 = ($("#sch :input:text,textarea,select").removeClass('alert').filter(function () {
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

 $(document).ready(function () {
     
     $("#table_conteiner").animate({opacity:1});
     $(".num").keydown(function (event) {
         if ((!event.shiftKey && !event.ctrlKey && !event.altKey) && ((event.keyCode >= 48 && event.keyCode <= 57) || (event.keyCode >= 96 && event.keyCode <= 105))) {} else if (event.keyCode != 8 && event.keyCode != 46 && event.keyCode != 37 && event.keyCode != 39) {
             event.preventDefault();
         }
     });
 });
</script>


<?php
#FOOTER
require(ROOT . "ini/footer.php");
?>
