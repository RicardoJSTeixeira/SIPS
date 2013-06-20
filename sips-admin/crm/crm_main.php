<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
?>

<?php 
$daystart = date("d/m/Y")."     00:00:00"; 
$dayend = date("d/m/Y")."     23:59:59";

$query = "SELECT cloud FROM servers";
$query = mysql_query($query, $link);
$is_cloud = mysql_fetch_row($query);
$is_cloud = $is_cloud[0];
$user = $_SERVER['PHP_AUTH_USER'];

if($is_cloud) //Incomplete
{
/* CONTRUIR CODIGO PARA CLOUD */	
} 
else 
{
	# Campanhas
	
	$current_admin = $_SERVER['PHP_AUTH_USER'];
	$query = mysql_query("select user_group from vicidial_users where user='$current_admin'") or die(mysql_error());
	$query = mysql_fetch_assoc($query);
	$usrgrp = $query['user_group'];
	$stmt="SELECT allowed_campaigns,allowed_reports from vicidial_user_groups where user_group='$usrgrp';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	$LOGallowed_campaigns = $row[0];
	$LOGallowed_reports =	$row[1];

	$LOGallowed_campaignsSQL='';
	$whereLOGallowed_campaignsSQL='';
	if ( (!eregi("-ALL",$LOGallowed_campaigns)) )
		{
		$rawLOGallowed_campaignsSQL = preg_replace("/ -/",'',$LOGallowed_campaigns);
		$rawLOGallowed_campaignsSQL = preg_replace("/ /","','",$rawLOGallowed_campaignsSQL);
		$LOGallowed_campaignsSQL = "and campaign_id IN('$rawLOGallowed_campaignsSQL')";
		$whereLOGallowed_campaignsSQL = "where campaign_id IN('$rawLOGallowed_campaignsSQL')";
		}

	$query = "SELECT campaign_name, campaign_id FROM vicidial_campaigns $whereLOGallowed_campaignsSQL";
	$query = mysql_query($query);
	while($row = mysql_fetch_assoc($query))
	{
		$select_campaigns .= "<option value='$row[campaign_id]'>$row[campaign_name]</option>";
	}
	#Bases de Dados
	$query = "SELECT list_name, list_id FROM vicidial_lists";
	$query = mysql_query($query);
	$select_bds = "<option value='all'>Todas</option>";
	while($row = mysql_fetch_assoc($query))
	{
		$select_bds .= "<option value='$row[list_id]'>$row[list_name]</option>";
	}
	#Operadores
	if ($usrgrp == 'ADMIN') {
	$query = "SELECT full_name, user FROM vicidial_users WHERE user NOT IN('VDAD', '1000')"; } else {
	$query = "SELECT full_name, user FROM vicidial_users WHERE user NOT IN('VDAD', '1000') AND user_group = '$usrgrp' "; }	
	$query = mysql_query($query);
	
	$select_operadores = "<option value='all'>Todos</option>";
	while($row = mysql_fetch_assoc($query))
	{
		$tExplode = explode(" ", $row['full_name']);
		$t = count($tExplode);
		if($t==1){$full_name = $tExplode[0];} else {$full_name = $tExplode[0]." ".$tExplode[$t-1];}
		$select_operadores .= "<option value='$row[user]'>$full_name</option>";
	}
	#Feedbacks
	$query = "SELECT status, status_name FROM vicidial_campaign_statuses";
	$query = mysql_query($query);
	
	$select_feedback = "<option value='all'>Todos</option>";
	while($row = mysql_fetch_assoc($query))
	{
		$select_feedback .= "<option value='$row[status]'>$row[status_name]</option>";
	}
}


?>
<style>
.ui-datepicker-trigger {
	cursor:pointer;
	width:32px;
	height:32px;
	}
.datepicker-input {
	width:170px !important;
	text-align:center;
	}
div.ui-datepicker{
 	font-size:12px;
	}
.dataTables_filter input[type="text"] {
	width:150px;
	height:20px;
	}
.my_class {
	text-align:center;
	}
.pesquisa {
	cursor:pointer;
}
</style>

<!-- Procura de Contactos -->

<div class="cc-mstyle"> 
<table>
<tr>
<td id="icon32"><img src="/images/icons/report_magnify_32.png" /></td> 
<td id="submenu-title"> Procura de Contactos </td>
<td style="text-align:left"></td>
<td style='text-align:right;'><span class="pesquisa"> Pesquisar </span></td>
<td style='width:32px'><img class="pesquisa" src='../../images/icons/page_white_magnify_32.png' /></td>
</tr>
</table>
</div>

<div id="work-area">
<br><br>
<div class="cc-mstyle" style="border:none"> 
<table>
<tr>
	<td style='text-align:right'>Data Inicial:</td>
    <td style='text-align:left'><input readonly="readonly" class="datepicker-input" type="text" id="datai" value="<?php echo $daystart; ?>"></td>
    <td style='text-align:right'>Campanha:</td>
    <td style='text-align:left'><select style="width:275px" id='filtro_campanha'><?php echo $select_campaigns; ?></select></td>

</tr>
<tr>
	<td style='text-align:right'>Data Final:</td> 
    <td style='text-align:left'><input readonly="readonly" class="datepicker-input" type="text" id="dataf" value="<?php echo $dayend; ?>"></td>
    <td style='text-align:right'>Base de Dados:</td> 
    <td style='text-align:left'><select style="width:275px;" id="filtro_dbs"><?php echo $select_bds; ?></select></td>
</tr>
<tr>
<td>&nbsp;</td>
</tr>
<tr>
    <td colspan="2"><input  type="radio" id="dcarregamento" name="data-search-type" />
	<label for='dcarregamento'>Por data de carregamento</label></td>

    <td style='text-align:right'>Operador:</td>
    <td style='text-align:left'><select style="width:275px" id='filtro_operador'><?php echo $select_operadores;  ?></select></td>
</tr>
<tr>
    <td colspan="2"><input type="radio" id="dultima" checked="checked" name="data-search-type" />
	<label for="dultima">Por data da ultima chamada</label></td>

    <td style='text-align:right'>Feedback:</td>
    <td style='text-align:left'><select style="width:275px" id='filtro_feedback'><?php echo $select_feedback; ?></select></td>
</tr>
<tr>
    <td></td><td></td><td style='text-align:right'>ID do Contacto:</td></td><td style='text-align:left'><input type="text" id='crm-contact-id' style="width:263px"><span style="color:grey"><i>(O uso desta opção invalída os outros filtros)</i></span></td>
</tr>

</table>
<br />
<br />
</div>
</div>
<br />
<!-- Lista de Contactos -->

<center>
<div style="width:90%">

<table id='contact_list'>
<thead></thead>
<tbody></tbody>
<tfoot></tfoot>
</table>
</div>
</center>
<br />

<div id="html_loader"  style='display:none; overflow-y:scroll; height:100%; width:100%; position:absolute; background-color:white; top:0; left:0; border-radius:2px' >
</div>

<script>
/* Inicialização dos DateTimePickers JQueryUI */
$(function() {
	$( "#datai" ).datetimepicker({
		timeFormat: 'hh:mm:ss',
		separator: "     ",
		hour: 00,
		minute: 00,
		secound: 00,
		showSecond: true,  
		showOn: "both",
		buttonImage: "../../images/icons/date_32.png",
		buttonImageOnly: true });
	$( "#dataf" ).datetimepicker({ 
		timeFormat: 'hh:mm:ss',
		separator: "     ",
		hour: 00,
		minute: 00,
		secound: 00,
		showSecond: true, 
		showOn: "both",
		buttonImage: "../../images/icons/date_32.png",
		buttonImageOnly: true });
});
/* Função que actualiza as dropdowns qnd se muda de campanha */ 
$('#filtro_campanha').change(function()
{
var campaign = $('#filtro_campanha').val();	
var db_list = "";
var feed_list = "";
$.ajax({
		type: "POST",
		dataType: "json",
		url: "_requests.php",
		data: {action: "campaign_change_db", sent_campaign: campaign},
		success: function(data)
		{
			if(data==""){
				$("#filtro_dbs").html("<option value=''>Nenhuma Base de Dados Associada</option>").prop("disabled", true);
				} else {
				db_list = "<option value='all'>Todas</option>";  
				$.each(data.db_list, function(key, obj){
					db_list += "<option value='" + obj.list_id + "'>" + obj.list_name + "</option>";
				}); 
			$("#filtro_dbs").html(db_list).prop("disabled", false);
			}
			$.ajax({
					type: "POST",
					dataType: "json",
					url: "_requests.php",
					data: {action: "campaign_change_feedback", sent_campaign: campaign},
					success: function(data)	
					{
						if(data==""){
							$("#filtro_feedback").html("<option value=''>Nenhum Feedback Associado</option>").prop("disabled", true);
							} else {
							feed_list = "<option value='all'>Todos</option>";  
							$.each(data.feed_list, function(key, obj){
								feed_list += "<option value='" + obj.status + "'>" + obj.status_name + "</option>";
							});  
						$("#filtro_feedback").html(feed_list).prop("disabled", false);
						}
					}
				});
		
		}
	});	
});
/* Função que realiza a pesquisa e que mostra a tabela com os resultados */
$(".pesquisa").click( function() {  
	$('#contact_list').hide();
	var datai = $("#datai").val();
	var dataf = $("#dataf").val();
	var filtro_campanha = $("#filtro_campanha").val();
	var filtro_dbs = $("#filtro_dbs").val();
	var filtro_operador = $("#filtro_operador").val();
	var filtro_feedback = $("#filtro_feedback").val();
	var dataflag = "";
	if( $("input[name='data-search-type']:checked").attr("id") == 'dcarregamento'){
		dataflag = 0;
	} else {
		dataflag = 1;
	} 

	var oTable = $('#contact_list').dataTable( {
		"bSortClasses": false,
		"bJQueryUI": true, 
		"bProcessing": true,
		"bDestroy": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": '_requests.php',
		"fnServerParams": function (aoData) {
			aoData.push( 
							{ "name": "action", "value": "get_table_data" }, 
							{ "name": "datai", "value": datai },
							{ "name": "dataf", "value": dataf },
							{ "name": "filtro_campanha", "value": filtro_campanha },
							{ "name": "filtro_dbs", "value": filtro_dbs },
							{ "name": "filtro_operador", "value": filtro_operador },
							{ "name": "filtro_feedback", "value": filtro_feedback },
							{ "name": "dataflag", "value": dataflag },
                                                                                                                                                           {"name": "contact_id", "value": $("#crm-contact-id").val()}
						)},
		"aoColumns": [ { "sTitle": "ID", "sWidth": "100px"}, { "sTitle": "Nome", "sWidth": "250px"}, { "sTitle": "Telefone", "sWidth": "50px"}, { "sTitle": "Morada", "sWidth": "350px"}, { "sTitle": "Ultima Chamada", "sWidth": "100px"} ], 
		"fnDrawCallback": function(oSettings, json){ $('#contact_list').show(); $('#contact_list').css({"width":"100%"}) },

			
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" }
	});
	
});
/* Função que força uma pesquisa "Todas" quando se filtra por data de carregamento */ 
$("input:radio[name=data-search-type]").change(function(){
	if($("input:radio[name=data-search-type]:checked").attr("id")=="dcarregamento")
	{
	$("#filtro_dbs").val("all").prop("disabled", true); 
	} else {
	$("#filtro_dbs").val("all").prop("disabled", false);	
	}

});






//$("#go").click( function(){ $('#contact_list').dataTable({"bDestroy": true}) } );




/* function GetContactDetails(lead_id){

$('#edit-container').css({ "display":"inline"});

/* $('html, body').animate({
         scrollTop: $("#edit-container").offset().top
     }, 2000); 


$.ajax({
		type: "POST",
		dataType: "json",
		url: "_requests.php",
		data: {action: "get_contact_details", sent_lead_id: lead_id },
		success: function(leadinfo)
		{  
		var status; 
		var campaign_id = leadinfo.campaign_id;
		if(leadinfo.status_name_one==null){ status = leadinfo.status_name_two; } else {status = leadinfo.status_name_one;}
		
		$('#lead_info-lead_id').html(lead_id);
		$('#lead_info-phone_number').html(leadinfo.phone_number);
		$('#lead_info-list_name').html(leadinfo.list_name);
		$('#lead_info-campaign_name').html(leadinfo.campaign_name);
		$('#lead_info-full_name').html(leadinfo.full_name);
		$('#lead_info-status_name').html(status);
		$('#lead_info-called_count').html(leadinfo.called_count);
		
		$('#lead_info-date_load').html(leadinfo.date_load);
		$('#lead_info-hour_load').html(leadinfo.hour_load);
		$('#lead_info-date_lcall').html(leadinfo.date_lcall);
		$('#lead_info-hour_lcall').html(leadinfo.hour_lcall);
		
		$.ajax({
				type: "POST",
				dataType: "json",
				url: "_requests.php",
				data: {action: "get_contact_info", sent_campaign_id: campaign_id, sent_lead_id: lead_id },
				success: function(display_names)
				{  
				
				$.each(display_names, function()
				{

				} 
				);
				
				
				
					
				}
				});
		
		
		
		
		}
		});



} */
function CloseHTML() 
{
	$('#html_loader').css({"display":"none"})

}

function LoadHTML(lead_id)
{

$.ajax({
			type: "POST",
			dataType: "html",
			url: "crm_edit.php?lead_id="+lead_id,
			success: function(msg)
			{  
			
			$('#html_loader').css({"display":"inline"}).html("<br>" + msg);;

			
			
			
				
			}
			});

}




$(document).ready
(
	/* Inicialização da página conforme a primeira campanha da dropdown */
	function()
	{
	var campaign = $('#filtro_campanha').val();	
	var db_list = "";
	var feed_list = "";
	$.ajax({
			type: "POST",
			dataType: "json",
			url: "_requests.php",
			data: {action: "campaign_change_db", sent_campaign: campaign},
			success: function(data)
			{
				if(data==""){
					$("#filtro_dbs").html("<option value=''>Nenhuma Base de Dados Associada</option>").prop("disabled", true);
					} else {
					db_list = "<option value='all'>Todas</option>";  
					$.each(data.db_list, function(key, obj){
						db_list += "<option value='" + obj.list_id + "'>" + obj.list_name + "</option>";
					}); 
				$("#filtro_dbs").html(db_list).prop("disabled", false);
				}
				$.ajax({
						type: "POST",
						dataType: "json",
						url: "_requests.php",
						data: {action: "campaign_change_feedback", sent_campaign: campaign},
						success: function(data)	
						{
							if(data==""){
								$("#filtro_feedback").html("<option value=''>Nenhum Feedback Associado</option>").prop("disabled", true);
								} else {
								feed_list = "<option value='all'>Todos</option>";  
								$.each(data.feed_list, function(key, obj){
									feed_list += "<option value='" + obj.status + "'>" + obj.status_name + "</option>";
								});  
							$("#filtro_feedback").html(feed_list).prop("disabled", false);
							}
						}
					});
			
			}
		});	
	}
);

</script>



<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>