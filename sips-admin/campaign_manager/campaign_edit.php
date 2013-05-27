<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
?>
<?php
$campaign_name=$_GET['campaign_name'];
$campaign_id=$_GET['campaign_id'];

?>

<div class="cc-mstyle">
	<table>
		<tr>
			<td id="icon32"><img src='/images/icons/construction_32.png' /></td>
			<td id='submenu-title' style='width:400px'> Gestor de Campanhas | <span id="header-status" style="font-weight:normal"><? echo $campaign_name; ?></span></td>
			<td style='text-align:left'><span id="message"></span></td>
		</tr>
	</table>
</div>
<br />
<div id="cc-mstyle" style='width:90%; margin:0 auto;'>
  
<div id="tabs" class="tabs-edit"> 
	<ul> 
		<li><a href="tabs_campaign_details.php?campaign_id=<?php echo $campaign_id; ?>">Detalhes</a></li> 
		<li><a href="tabs_campaign_options.php?campaign_id=<?php echo $campaign_id; ?>">Opções Gerais</a></li>
        <li><a href="tabs_campaign_calls.php?campaign_id=<?php echo $campaign_id; ?>">Opções das Chamadas</a></li>
		<li><a href="tabs_campaign_agents.php?campaign_id=<?php echo $campaign_id; ?>">Opções dos Operadores</a></li>
	</ul>



</div>
<style>
/*.tabs-edit ui-state-focus { background:none; !important }
.tabs-edit ui-widget-content { border:none; !important }
.tabs-edit widget-header { background:none; border:none; border-bottom: 1px solid #DDDDDD; !important  }
.tabs-edit ui-tabs .ui-tabs-nav li { left: -2px; }
.tabs-edit ui-corner-all, .ui-corner-bottom, .ui-corner-left, .ui-corner-bl { border-radius:none; }
/*.ui-state-hover a, .ui-state-hover a:hover { color: #ff0084; text-decoration: none;  }
.ui-widget-content .ui-state-hover { border: 1px solid #DDDDDD;  background: none; font-weight: bold; color: #ffffff; } */


</style>
<script>

	
	$(function() {
		$( "#tabs" ).tabs();
	});


	
/* ERROR DIALOG */ 
var $error = $('<div></div>')
	.html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ')
	.dialog({
		autoOpen: false,
		title: "<span style='float:left; margin-right: 4px;' class='ui-icon ui-icon-alert'></span> Erro",
		width: "550",
		height: "250",
		show: "fade",
		hide: "fade",
		buttons: { "OK": function(){ $(this).dialog("close"); } }
});

/* UTILS */
function UpdateTableLog(data){
	if( $('#table-campaign-changedate tbody').html().length != 0) 
		{
			$('#table-campaign-changedate').dataTable().fnAddData([  data.reply[0],data.reply[1],data.reply[2],data.reply[3]  ]); 
		} 
}


</script>

<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>