<?php 
for ($i = 0; $i < count(explode('/', $_SERVER['PHP_SELF'])) - 2; $i++) {$far.="../";}
define("ROOT", $far);
require(ROOT . "ini/header.php");

$today = date("o-m-d"); ?>
<form name="report" action="exportcsv_fc.php" target="_self" method="post">
<input type="hidden" value='go' name="totais_camp3">
<div class="cc-mstyle">	
<table>
<tr>
<td id='icon32'><img src='../images/icons/document_inspector_32.png' /></td>
<td id='submenu-title'> Total de Feedbacks de Todas as Campanhas </td>
<td style="text-align:right">Obter Report</td>
<td id='icon32'><input type="image" src='../images/icons/document_export_32.png'/></td>
</tr>
</table>
</div>


<div id="work-area" style="min-height:0px"><br><br>
<div class=cc-mstyle style=border:none>
<table>
<tr>
<td>Dia Inicial:</td>
<td><input style="width:200px; text-align:center;" type="text" name='data_inicial' id='data_inicial' value='<?php echo $today; ?>' /><td>

</td>
</tr>

<tr>
<td>Dia Final:</td>
<td><input style="width:200px; text-align:center;" type="text" name='data_final' id='data_final' value='<?php echo $today; ?>' /><td>
<script language="JavaScript">
$(function() {
        $( "#data_inicial" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"yy-mm-dd",
            onClose: function( selectedDate ) {
                $( "#data_final" ).datepicker( "option", "minDate", selectedDate );
            }
        });
        $( "#data_final" ).datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat:"yy-mm-dd",
            onClose: function( selectedDate ) {
                $( "#data_inicial" ).datepicker( "option", "maxDate", selectedDate );
            }
        });
        });
</script>
</td>
</tr>
<tr>
<td>Opções do Report:</td><td><select name="flag" id="flag" style="width:315px;"><option value="todos">Todos os Feedbacks em Sistema</option><option value="encontrados">Apenas os Feedbacks Encontrados na Pesquisa</option></select></td>	
</tr>
</table>
<br><br>
</div>

</div>

</div>
<br><br>
</form>

	
</body>
</html>