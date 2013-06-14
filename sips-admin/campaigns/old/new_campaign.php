<!DOCTYPE html>
<html>
<head>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<title>Gestão de Campanhas</title>

<link type="text/css" rel="stylesheet" href="/jquery/themes/flick/flick.css" />
<link type="text/css" rel="stylesheet" href="/jquery/jsdatatable/css/jquery.dataTables_themeroller.css" />
<link type="text/css" rel="stylesheet" href="/jquery/uniform/css/uniform.default.css" media="screen" charset="utf-8" />
<link type="text/css" rel="stylesheet" title="sipsdefault" href="/css/style.css" />
<link type="text/css" rel="stylesheet" title="sipsdefault" href="style.css" />
</head>
<body>

<?
// PHP ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



// OPTIONS
/*	$query = "SELECT user_group, group_name FROM vicidial_user_groups";
	$query = mysql_query($query, $link);
	$half = round((mysql_num_rows($query)/2),0);
	for ($i=0;$i<mysql_num_rows($query);$i++)
	{
		$row = mysql_fetch_assoc($query);
		if($i>=$half-1){
            $groups1 .= "	<tr>
							<td><input class='groups-checkbox' type='checkbox' value='".$row['user_group']."' name='".$row['user_group']."' id='".$row['user_group']."'><label style='display:inline;' for='".$row['user_group']."'>".$row['group_name']."</label></td>
							</tr>";   
        } else {
            $groups2 .= "	<tr>
							<td><input class='groups-checkbox' type='checkbox' value='".$row['user_group']."' name='".$row['user_group']."' id='".$row['user_group']."'><label style='display:inline;' for='".$row['user_group']."'>".$row['group_name']."</label></td>
							</tr>"; 
		}	
	}

/*
// RECICLAGEM
    $query = mysql_query("SELECT * FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id'", $link) or die(mysql_query);
   
    $default_recycle = array("NA","B","DROP");
    $default_recycle_names = array("Ninguém Atendeu", "Ocupado", "Sem Operador Disponível");
    
    if(mysql_num_rows($query)>0){} else {
        
        for($i=0;$i<count($default_recycle);$i++)
        {
            $recycle .= "<tr id='$default_recycle[$i]'>
                            <td>
                                <input type='checkbox' checked='checked' name='$default_recycle[$i]' id='$default_recycle[$i]' >
                                <label style='display:inline;' for='$default_recycle[$i]'>$default_recycle_names[$i]</label>
                            </td>
                            <td class='td-icon'>
                                <img id='edit-recycle-time' class='mono-icon cursor-pointer edit-recycle-time' src='/images/icons/mono_stopwatch_16.png'>
                            </td>
                            <td class='td-recycle-time' style='width:48px; padding-left:2px'>15m</td>
                            <td class='td-icon'>
                                <img id='edit-recycle-tries' class='mono-icon cursor-pointer edit-recycle-tries' src='/images/icons/mono_reload_16.png'>
                            </td>
                            <td class='td-recycle-tries' style='width:32px; padding-left:2px'>8</td>
                            <td class='td-icon'>
                                <img class='mono-icon' src='/images/icons/mono_attention_16.png'>
                            </td>
                            <td style='width:32px; padding-left:2px'>
                                0
                            </td>"; 
        }
        
        }
    //echo "SELECT status, status_name FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id' AND scheduled_callback <> 'Y' GROUP BY status";
    $query = mysql_query("SELECT status, status_name FROM vicidial_campaign_statuses WHERE campaign_id='C00273' AND scheduled_callback <> 'Y' GROUP BY status", $link) or die(mysql_error());
    
    while ($row = mysql_fetch_assoc($query))
    {
        $recycle_avail_feedbacks .= "<option value='$row[status]'>$row[status_name]</option>";
    }       
    
        	
// SCRIPTS
    $query = "SELECT DISTINCT(campaign_id) FROM vicidial_lists_fields WHERE campaign_id IN ($AllowedCampaigns)";
    $query = mysql_query($query) or die(mysql_error());
	
	while($row = mysql_fetch_assoc($query))
	{
		$array_scripts[] = $row['campaign_id'];
	}
    
    $camps_IN = implode("','", $array_scripts);
    
    $query = "SELECT campaign_name,campaign_id FROM vicidial_campaigns WHERE campaign_id IN('$camps_IN') ORDER BY campaign_name";
    $query = mysql_query($query);
    $half = round((mysql_num_rows($query)/2),0);
    for ($i=0;$i<mysql_num_rows($query);$i++)
    {
        $row=mysql_fetch_assoc($query);
		$row['campaign_id'] = strtoupper($row['campaign_id']);
        if($i>=$half){
            $scripts2 .= "<input type='radio' name='radio_scripts' id='$row[campaign_id]'><label style='display:inline;' for='$row[campaign_id]'> $row[campaign_name]</label><br>"; 
        } else {
            $scripts1 .= "<input type='radio' name='radio_scripts' id='$row[campaign_id]'><label style='display:inline;' for='$row[campaign_id]'> $row[campaign_name]</label><br>"; 
        }
    }
// CAMPOS DINAMICOS
    $query = mysql_query("SELECT Name, Display_name, readonly, active, field_order FROM vicidial_list_ref WHERE campaign_id='$campaign_id'");
    
        
        
    // TEMPLATE DEFAULT
    
    $default_fields = array("PHONE_NUMBER", "ALT_PHONE", "ADDRESS3", "FIRST_NAME", "ADDRESS1", "POSTAL_CODE", "EMAIL", "COMMENTS");
    $default_labels = array("Telefone", "Telefone Alternativo #1", "Telefone Alternativo #2", "Nome ou Empresa", "Morada", "Código Postal", "E-mail", "Observações");
    
    for($i=0; $i<count($default_fields); $i++)
    {   
        if($default_fields[$i]=="PHONE_NUMBER" || $default_fields[$i]=="ALT_PHONE" || $default_fields[$i]=="ADDRESS3" || $default_fields[$i]=="FIRST_NAME" || $default_fields[$i]=="COMMENTS") 
        { $td_cancel = "<td width='19px'> </td>"; }
        else 
        { $td_cancel = "<td width='16px'> <img class='mono-icon cursor-pointer remove-field' src='/images/icons/mono_cancel_16.png'> </td> "; }
        $dinamic_fields .= "<li id='$default_fields[$i]' class='cursor-move'>
                            <table>
                                <tr class='height24'>
                                    
                                    $td_cancel 
                                    <td width='16px'> <img id='ro-$default_fields[$i]' class='mono-icon cursor-pointer icon-readonly' src='/images/icons/mono_document_empty_16.png'> </td>
                                    <td><label class='cursor-move' style='display:inline; margin-left:3px' for='i-$default_fields[$i]'>$default_labels[$i]</label></td>
                                    <td width='16px'><img class='mono-icon cursor-pointer edit-field' src='/images/icons/mono_document_edit_16.png'></td>
                                </tr>
                            </table>
                        </li>";
                        /* <input id='i-$default_fields[$i]' name='i-$default_fields[$i]' type='checkbox' checked='checked' $option_disabled > */  
 /*   }



	$query = "SELECT A.campaign_id, B.campaign_name FROM vicidial_list_ref A INNER JOIN vicidial_campaigns B ON A.campaign_id=B.campaign_id WHERE A.campaign_id IN($AllowedCampaigns) GROUP BY A.campaign_id";
	$query = mysql_query($query, $link) or die(mysql_error());
	
	$campaign_copy = "<option>---</option>";
	while ($row = mysql_fetch_assoc($query))
	{
		$campaign_copy .= "<option value='$row[campaign_id]'>$row[campaign_name]</option>";
	}

// BASES DE DADOS 
    $query = mysql_query("SELECT list_id, list_name FROM vicidial_lists WHERE campaign_id='$campaign_id'") or die(mysql_error());
    if(mysql_num_rows($query) > 1){
        
        while($row = mysql_fetch_assoc($query)){
                
                
        $bd_list .= "<li id='$row[list_id]' class=''>
                        <table>
                        <tr>
                            <td style='width:16px'><input id='$row[list_id]' type='checkbox'></td><td><label class='' style='display:inline;' for='$row[list_id]'>$row[list_name]</label></td>
                            <td width='24px'><img class='mono-icon cursor-pointer load-leads' src='/images/icons/mono_icon_database_16.png'></td>
                            <td width='24px'><img class='mono-icon cursor-pointer' src='/images/icons/mono_wrench_16.png'></td>
                        </tr>
                        </table>
                    </li>"; 
            
            
            
            
            
            }

        
        
        } else { $bd_list = "<span id='no-dbs'>Sem Bases de Dados associadas.</span>";
        
    
        
        
          } */

// END PHP /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
?>
<!-- TABS -->

<br>
<div id='main-div' style='width:100%; height:510px; margin:0 auto; border:0; display:none;'>
    <div id="wizard-tabs"> 
        <ul> 
            <li id="li-opcoes"><a href="#tab1">Opções Gerais</a></li> 
            <li id="li-pausas"><a href="#tab2">Pausas</a></li>
            <li id="li-feeds"><a href="#tab3">Feedbacks</a></li>
            <li id="li-recycle"><a href="#tab4">Reciclagem</a></li> 
            <li id="li-scripts"><a href="#tab5">Scripts</a></li> 
            <li id="li-dfields"><a href="#tab6">Campos Dinâmicos</a></li>
            <li id="li-dbs"><a href="#tab7">Bases de Dados</a></li>  
        </ul>
        
		<!-- OPÇÕES GERAIS -->
        <div id="tab1">
        
        	<table class="table-100"> 
        	<tr>
            	<td class="td-half-left">
            	
                    <table>
                    <tr>
                        <td style="width:100px">
                        <label class="input-text-label" for="campaign-name">Nome da Campanha</label><input maxlength="31" style="width:295px !important;" class="input-text" type="text" id="campaign-name">
                        </td>
                        <td valign="bottom">
                        <span style="font-size:10px; margin:0px 0px 0px 3px">(max. 30 caracteres, obrigatório)</span>
                        </td>
                    </tr>
                    </table>
                
                    <table>
                    <tr>
                        <td valign="bottom" style="width:100px">
                        <label class="input-text-label" for="campaign-description">Descrição da Campanha</label><textarea style="margin-bottom:-3px; height:73px" maxlength="201" class="input-textarea" id="campaign-description"></textarea>
                        </td>
                        <td valign="bottom">
                        <span style="font-size:10px; margin:0px 0px 5px 3px">(max. 200 caracteres, opcional)</span>
                        </td>
                    </tr>
                    </table>     
    
                    <div class="div-title">Grupos com Acesso à Campanha</div>
                    <div style="overflow-y:auto; height:249px">
                    
                    <table id="table-groups">
                    <tr>
                        <td width="50%" valign="top"><table id="table-groups-1"></table></td>
                        <td width="50%" valign="top"><table id="table-groups-2"></table></td>             
                    </tr>
                    </table>
                    
                    </div>
                    
                    
				</td>

            	<td class="td-half-right">
                
                    <div class="div-title">Opções Gerais</div>
                    
                    <table>
                    <tr class="tr-options">
                    	<td>Activa</td>
                        <td class="tright">

                        <input type="radio" class='campaign-active-switch' id="campaign_active_yes" name="campaign_active" /><label for="campaign_active_yes">Sim</label>
                        <input type="radio" class='campaign-active-switch' id="campaign_active_no" name="campaign_active" /><label for="campaign_active_no">Não</label>

                        </td>
					</tr>
                    <tr class="tr-options">
                        <td>Tipo de Campanha</td>
                        <td class="tright">
                        <input type="radio" class='campaign-type-switch' id="campaign_type_auto" name="campaign_type" /><label for="campaign_type_auto">Automática</label>
                        <input type="radio" class='campaign-type-switch' id="campaign_type_manual" name="campaign_type" /><label for="campaign_type_manual">Manual</label>
                        </td>
					</tr>
                    <tr class="tr-options">
                        <td>Rácio</td>
                        <td class="tright">
                        <input class="text-spinner" style="width:16px" id="ratio-spinner" name="ratio-spinner" value="2">
                        </td>
                    </tr>
                    </table>
                    
                    <div class="div-title">Opções das Chamadas</div>
                    
                    <table>
                    <tr class="tr-options">
                        <td>Gravação de Chamadas</td>
                        <td class="tright">
                        <input type="radio" class="campaign-recording-switch" id="campaign_recording_yes" name="campaign_recording" /><label for="campaign_recording_yes">Sim</label>
                        <input type="radio" class="campaign-recording-switch" id="campaign_recording_no" name="campaign_recording" /><label for="campaign_recording_no">Não</label>
                        </td>
                    </tr>
                    <tr class="tr-options">
                        <td>Ordem de Marcação de Chamadas</td>
                        <td class="tright">
                        <input type="radio" class="campaign-lead-order-switch" id="campaign_lead_order_random" name="campaign_lead_order"  /><label for="campaign_lead_order_random">Aleatória</label>
                        <input type="radio" class="campaign-lead-order-switch" id="campaign_lead_order_ordered" name="campaign_lead_order" /><label for="campaign_lead_order_ordered">Ordenada</label>
                        </td>
                    </tr>
                    <tr class="tr-options">
                        <td>Atribuição de Chamadas aos Operadores</td>
                        <td class="tright">
                        <input class="text-spinner" style="width:160px;" id="campaign_atrib_calls" name="campaign_atrib_calls" value="Aleatória">
                        </td>
                    </tr>
                    </table>
                    
                    <div class="div-title">Configurar Feedbacks chamados pela Campanha</div>
                    
                    <table>
                    <tr>
                        <td>Aqui podem ser configurados todos os Feedbacks que a Campanha está autorizada a chamar.</td>
                        <td><button id="btn-config-dial-status">Configurar</button></td>
                    </tr>
                    </table>
				</td>
            </tr>
            <tr style='height:30px'>
                <td><div style='border-top: 1px solid #DDDDDD; margin:10px 16px 6px 16px'></div>
                    <div style='margin:0px 13px 0px 13px'><button id='all-groups'>Todos</button><button id='no-groups' style='float:right'>Nenhum</button></div>  </td>
                <td valign="bottom"><button class="next-button">Seguinte</button><button class="back-button">Voltar</button></td>
            </tr>
            </table>
        
		</div>       
                 
        <!-- PAUSAS -->
    	<div id="tab2">
        	<table class="table-100">
       		<tr>
                <td class="td-half-left">
                    
                    <div class="div-title">Pausas Disponíveis <span class='span-pauses-active-inactive'></span><div style='float:right'><span>Campanha:</span>&nbsp;<span class="span-active-campaign-name" style='color:#e17009'></span></div></div>
                    
                    <div style='overflow-y:auto; height:396px; padding-right:6px'>
                    	<span id='span-no-pauses'></span>
                    	<table id="tbl-pauses">
                    	</table> 
                    </div>

                </td>
    	        <td class="td-half-right">
        	        
                    <div class="div-title">Adicionar Nova Pausa</div>    
                	
                    <table>
                    <tr>
                        <td width="284px"><input class="input-text" style='width:272px' maxlength="30" type="text" id="input-new-pause-name"></td>
                        <td width="150px" valign="bottom"><span style="font-size:10px; margin:0px 0px 0px 0px">(max. 30 caracteres)</span></td>
                        <td width="25px" style="text-align:right; padding-right:3px;"><input id='spinner-new-pause-time' style="width:22px;" value="0" /></td>
                        <td>minutos</td>
                    </tr>
                    </table>

                    <table style='margin-top:3px' >
                    <tr>
                    	<td colspan="3" id='td-new-pause-error'></td>
                    	<td style='text-align:right'><button id="btn-new-pause">Adicionar</button></td>
                    </tr>       
                </table>
                
                <div class="div-title">Legenda</div>
                <table>
                <tr>
                    <td width="20px"><img style='margin-top:-3px' class='mono-icon' src='/images/icons/mono_cup_16.png'></td><td width=25%>Tempo de Pausa</td> 
                    <td width="20px"><img class='mono-icon' src='/images/icons/mono_wrench_16.png'></td><td width=25%>Configurar Pausa</td>  
                    <td width="20px"><img class='mono-icon' src='/images/icons/mono_plus_16.png'></td><td width=25%>Activar Pausa</td>
                    <td width="20px"><img class='mono-icon' src='/images/icons/mono_trash_16.png'></td><td width=25%>Desactivar Pausa</td>                
                </tr>
                </table>

              	<br />
               
                <div class="div-title">Aplicar a todas as Campanhas</div>
                <table>
                <tr>
                	<td>Esta opção permite aplicar as definições de Pausas que tem actualmente nesta Campanha, a todas as suas Campanhas.</td>
                	<td><button id='btn-pause-apply-to-all-campaigns'>Aplicar</button></td>
                </tr>
                </table>
                
                <div class="div-title">Informação</div>
                <table>
                <tr>
                    <td style="text-justify: distribute">Se editar pausas já existentes vai actualizar todas as Pausas com o mesmo nome em todas as campanhas. Se precisa de Pausas com nomes ou tempos diferentes sugerimos que crie Pausas novas. </i></td>                
                </tr>
	            </table>
			</td>
        </tr>
        <tr style='height:30px'>
        	<td><div style='border-top: 1px solid #DDDDDD; margin:10px 16px 6px 16px'></div>
                    <div style='margin:0px 13px 0px 13px'>                         
                    <table>
                    <tr>
                    <td width="33%" style='text-align:left'><button id='btn-select-all-pauses'>Todas</button></td>
                    <td width="33%" style='text-align:center'><button id='btn-pauses-active-or-inactive'>Inactivas</button></td>
                    <td width="33%" style='text-align:right'><button id='btn-select-no-pauses'>Nenhuma</button></td></tr>
                    </table>                    
                    </div> 
            </td>
            <td valign="bottom"><button class="next-button">Seguinte</button><button class="back-button">Voltar</button></td>
        </tr>
        </tr>
        </table>    
        </div>  
        
        <!-- FEEDBACKS -->
   		<div id="tab3">
        <table class="table-100">
        <tr>
			<td class="td-half-left">
                <div class="div-title">Feedbacks Disponíveis <span class='span-feeds-active-inactive'></span><div style='float:right'><span>Campanha:</span>&nbsp;<span class="span-active-campaign-name" style='color:#e17009'></span></div></div>
                <div style='overflow-y:auto; height:396px; padding-right:6px'>
                <span id='span-no-feeds'></span>
               	 
                <table id="tbl-feeds">
                </table> 
                </div>
                
                
	        </td>
            
            <td class="td-half-right">
        	        <div class="div-title">Adicionar Novo Feedback</div>    
                	<table>
                    <tr>
                        <td width=284px><input class="input-text" style='width:272px' maxlength="20" type="text" id="input-new-feed-name"></td>
                        <td width="150px" valign="bottom"><span style="font-size:10px; margin:0px 0px 0px 0px">(max. 20 caracteres)</span></td>
                      
                        <td width="20px"><input type='checkbox' checked="checked" id='input-new-feed-human' /></td>
                        <td width="22px"><img class='mono-icon' src='/images/icons/mono_speech_dual_16.png' /></td>
                        <td width="24px"></td>
						<td width='20px'><input type='checkbox' id='input-new-feed-callback' /></td>
                        <td width="22px"><img class='mono-icon' src='/images/icons/mono_phone_inverse_16.png' /></td>
						<td>&nbsp;</td>
 
                    </tr>
                    </table>
                    <table style='margin-top:3px' >
                    <tr>
                    <td colspan="3" id='td-new-feed-error'>
                    </td>
                    <td style='text-align:right'><button id="btn-new-feed">Adicionar</button></td>
                    </tr>
                    

                </table>
                <div class="div-title">Legenda</div>
                <table>
                    <tr>
                        <td width="20px"><img class='mono-icon' src='/images/icons/mono_speech_dual_16.png'></td><td width=20%>Resposta Humana</td>   
                        <td width="20px"><img class='mono-icon' src='/images/icons/mono_phone_inverse_16.png'></td><td width=10%>Callback</td>
                        <td width="20px"><img class='mono-icon' src='/images/icons/mono_wrench_16.png'></td><td width=20%>Configurar Feedback</td>
                        <td width="20px"><img class='mono-icon' src='/images/icons/mono_plus_16.png'></td><td width=20%>Activar Feedback</td>  
                        <td width="20px"><img class='mono-icon' src='/images/icons/mono_trash_16.png'></td><td width=20%>Desactivar Feedback</td>                                       
                    </tr>


                </table>
              	<br />
               
                <div class="div-title">Aplicar a todas as Campanhas</div>
                <table>
                <tr>
                <td>
                Esta opção permite aplicar as definições de Feedbacks que tem actualmente nesta Campanha, a todas as suas Campanhas. 
                </td>
                <td>
                <button id='btn-feed-apply-to-all-campaigns'>Aplicar</button>
                </td>
                </tr>
                </table>
                
                <div class="div-title">Informação</div>
                <table>
                    <tr>
                        <td style="text-justify: distribute">
                        	Se editar Feedbacks já existentes vão ser actualizados todos os Feedbacks com o mesmo nome em todas as campanhas. Se precisa de Feedbakcs com nomes ou definições diferentes sugerimos que crie Feedbacks novos. </i>
  						</td>                
                    </tr>
	            </table>
     
    
            </td>
        </tr>       
        <tr style='height:30px'>
        	<td><div style='border-top: 1px solid #DDDDDD; margin:10px 16px 6px 16px'></div>
                    <div style='margin:0px 13px 0px 13px'>                         
                    <table>
                    <tr>
                    <td width="33%" style='text-align:left'><button id='btn-select-all-feeds'>Todos</button></td>
                    <td width="33%" style='text-align:center'><button id='btn-feeds-active-or-inactive'>Ver Inactivos</button></td>
                    <td width="33%" style='text-align:right'><button id='btn-select-no-feeds'>Nenhum</button></td></tr>
                    </table>                    
                    </div> 
            </td>
            <td valign="bottom"><button class="next-button">Seguinte</button><button class="back-button">Voltar</button></td>
        </tr>
        </table>        
        </div>  
        
       	<!-- RECICLAGEM -->
 		<div id="tab4">

        <table class="table-100">
        <tr>
            <td class="td-half-left">
                
                <div class="div-title">Feedbacks em Reciclagem <div style='float:right'><span>Campanha:</span>&nbsp;<span class="span-active-campaign-name" style='color:#e17009'></span></div></div></div>
                <div style='overflow-y:auto; height:396px; padding-right:6px'>
                <span id='span-no-recycle'></span>
               	 
                <table id="tbl-recycle">
                </table> 
                </div> 
                
                
            </td>
            <td class="td-half-right">
             <div class="div-title">Adicionar Reciclagem</div>    
                <table>
                <tr>
                    <td width="284px"> <select id="select-new-recycle" style="width:284px;"></select> </td>
                    
                    <td width="150px" valign="bottom"><span style="font-size:10px; margin:0px 0px 0px 0px"></span></td>
             
                    <td width="20px"><img class='mono-icon' src='/images/icons/mono_stopwatch_16.png'></td>
                    <td width="22px"><input id='spinner-recycle-time' style="width:22px;" value="15" /></td>
                    <td width="24px"></td>
                    <td width='20px'><img  class='mono-icon' src='/images/icons/mono_reload_16.png'></td>
                    <td width="22px"><input id='spinner-recycle-tries' style="width:16px;" value="5" /></td>
                    <td>&nbsp;</td>
                </tr>
                </table>
                <table style='margin-top:3px' >
                <tr>
                <td colspan="3" id='td-new-recycle-error'>
                </td>
                <td style='text-align:right'><button id="btn-new-recycle">Adicionar</button></td>
                </tr>
	            </table>

				<div class="div-title">Legenda</div>
                <table>
                    <tr>
                    	<td width="20px"><img class='mono-icon' src='/images/icons/mono_refresh_16.png'></td><td width=25%>Contactos para Reciclar</td>
                        <td width="20px"><img class='mono-icon' src='/images/icons/mono_stopwatch_16.png'></td><td width=25%>Intervalo de Reciclagem</td>   
                        <td width="20px"><img class='mono-icon' src='/images/icons/mono_reload_16.png'></td><td width=25%>Nº máximo de Tentativas</td>
                        <td width="20px"><img class='mono-icon' src='/images/icons/mono_wrench_16.png'></td><td width=25%>Configurar Reciclagem</td>           
                    </tr>


                </table>
              	<br />	
				<div class="div-title">Aplicar a todas as Campanhas</div>
                <table>
                <tr>
                <td>
                Esta opção permite aplicar as definições de Reciclagem que tem actualmente nesta Campanha, a todas as suas Campanhas. 
                </td>
                <td>
                <button id='btn-recycle-apply-to-all-campaigns'>Aplicar</button>
                </td>
                </tr>
                </table>	


				<div class="div-title">Informação</div>
                <table>
                    <tr>
                        <td style="text-justify: distribute">
                        	A edição dos Feedbacks em Reciclagem apenas diz respeito à Campanha que está a ser editada, sendo diferente do funcionamento das Pausas e Feedbacks onde a edição aplica-se a todas as Campanhas. Para tornar as definições de Reciclagem igual para todas as Campanhas use a opção "Aplicar a todas as Campanhas".  
  						</td>                
                    </tr>
	            </table>
				
               
            </td>
        </tr>
        <tr style='height:30px'>
        	<td><div style='border-top: 1px solid #DDDDDD; margin:10px 16px 6px 16px'></div>
                    <div style='margin:0px 13px 0px 13px'>                         
                    <table>
                    <tr>

                    <td width="33%" style='text-align:center'><button style='visibility:hidden'>Ver Inactivos</button></td>

                    </table>                    
                    </div> 
            </td>
            <td valign="bottom"><button class="next-button">Seguinte</button><button class="back-button">Voltar</button></td>
        </tr>
        </table>    
 
        </div> 
        
        <!-- SCRIPTS -->
        <div id="tab5">
        <table class="table-100">
        <tr>
            <td class="td-half-left">
                
                <div class="div-title">Scripts Disponiveis</div>
                <div style="height:361px; overflow-y:auto;">
                <table id="table-scripts">
                    <tr>
                        <td width="50%" valign="top"><? echo $scripts1; ?></td>
                        <td width="50%" valign="top"><? echo $scripts2; ?></td>             
                    </tr>
                    

                </table>
                </div>
                <br>
                
            </td>
            <td class="td-half-right">
            <div class="div-title">Associar Script</div>

            <table>
                <tr>
                    <td>
                    <span id="span-script">Nenhum Script associado.</span>
                    </td>
                    <td class="td-icon">
                            <img id="new-script" class="img-icon" src="/images/icons/script_add.png">
                        </td>
                </tr>
                
            </table>
            <div class="div-title">Alerta!</div>   
                        <b style="color:#000">Quando o Script de uma Campanha é alterado ou removido, todos os dados referentes ao script antigo são apagados.</b>
            </td>
        </tr>       
        <tr class="tr-next-button"><td colspan="2"><button class="next-button">Gravar</button></td></tr>
        </table>    
        </div> 
        <!-- CAMPOS DINAMICOS -->
   		<div id="tab6">
                    
        
            
            
        <table class="table-100">
        <tr>
            <td class="td-half-left">
                
                <div class="div-title">Campos Dinâmicos</div>
                
                <div style='overflow-y:auto; height:363px'>
                <ul id="dfields-sortable">
                <? echo $dinamic_fields; ?> 
                </ul>
                </div>
                
                
                
                
                
            </td>
            <td class="td-half-right">
                <div class="div-title">Adicionar Novo Campo</div>
            
                <table>
                    <tr>
                        <td>
                            <input id="add-field-name" class="input-text" style='width:95%' type="text" >
                        </td>
                        <td width=10px></td>
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_document_empty_16.png'></td>
                        <td class="rdonly-chooser"><input id="new-field-rdonly-no" checked="checked" type="radio" name="rdonly"></td>
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_document_16.png'></td>
                        <td class="rdonly-chooser"><input id="new-field-rdonly-yes" type="radio" name="rdonly"></td>
                        <td class="td-icon">
                            <img id="new-field" class="img-icon" src="/images/icons/tag_blue_add_32.png">
                        </td>
                    </tr>

                </table>
                <div class="div-title">Copiar Definições de outra Campanha</div>
                <table>
                <tr>
                <td>
                <select id='campaign-copy'>
                <?=$campaign_copy?>
                </select>
                </td>
                <td>
                <button id='campaign-copy-button' style='float:right'>Copiar</button>
                </td>
                </tr>
                </table>
                <div class="div-title">Legenda</div>
                <table>
                     <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_cursor_move_16.png'></td><td>Ordenação</td>              
                    </tr>
                    <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_document_empty_16.png'></td><td>Editável</td>                
                    </tr>
                    <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_document_16.png'></td><td>Só de Leitura</td>             
                    </tr>
                    <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_document_edit_16.png'></td><td>Editar Campo</td>             
                    </tr>

                    <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_cancel_16.png'></td><td>Eliminar Campo</td>              
                    </tr>


                    <tr><td id="dumper"></td></tr>
                    

                </table>
            </td>
        </tr>       
        <tr class="tr-next-button"><td colspan="2"><button class="next-button">Gravar</button></td></tr>
        </table>
        </div> 
        <!-- BASES DE DADOS -->
        <div id="tab7">
        <table class="table-100">
        <tr>
            <td class="td-half-left">
                
                <div class="div-title">Bases de Dados da Campanha</div>
                
                
                <ul id="database-list">
                <? echo $bd_list; ?>    
                </ul>
            </td>
            <td class="td-half-right">
                <div class="div-title">Criar nova Base de Dados</div>
            
                <table>
                    <tr>
                        <td>
                            <input id="add-database-name" class="input-text" style='width:95%' type="text" >
                        </td>
                        <td width=10px></td>
                        
                        <td class="td-icon">
                            <img id="add-new-database" class="img-icon" src="/images/icons/database_add_32.png">
                        </td>
                    </tr>

                </table>
                
                <div class="div-title">Legenda</div>
                <table>
                     <tr class="height24">
                        <td class="td-icon"><img class='mono-icon load-leads' src='/images/icons/mono_icon_database_16.png'></td><td>Carregar Contactos</td>               
                    </tr>
                    
                     <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_wrench_16.png'></td><td>Ver Detalhes / Configurações</td>                
                    </tr>
                </table>
            </td>
        </tr>       
        <tr class="tr-next-button"><td colspan="2"><button class="next-button">Gravar</button></td></tr>
        </table>
        
</div>
        


    	<!-- DIALOGS -->
        
       
        <div id="dialog-edit-feed-name" style="display:none">
        <label class="input-text-label" for="edit-feed-name">Nome do Feedback</label><input style='width:194px' class="input-text" type="text" id="edit-feed-name">
        <div class="div-title">Informação</div>
        As alterações aos Feedbacks feitas aqui apenas ficaram gravadas quando o utilizador clicar no botão "Gravar" no menu geral de edição dos Feedbacks. 
        </div> 
        
        <!--
        <div id="dialog-rollback" style="display:none">
            <div class="div-title">Informação</div>
            
            texto.
            
            <div class="div-title">Feedbacks Disponíveis</div>
            <div style='height:210px; overflow:auto;'>
            <table id="table-config-dial-status">
                <tr>
                    <td width="50%" valign="top"><table id="table-config-dial-status-1"></table></td>
                    <td width="50%" valign="top"><table id="table-config-dial-status-2"></table></td>             
                </tr>
            </table>
                
            </div>
            <div class="div-title"></div>
            <table>
            <tr>
            <td style='text-align:left; width:50%;'><button id="btn-config-dial-status-all">Todos</button></td>
            <td style='text-align:right; width:50%;'><button id="btn-config-dial-status-none">Nenhum</button></td>
            </tr>
            </table>
        </div> -->
        
        
        
        <!-- opcões gerais -->
        <div id="dialog-config-dial-status" style="display:none">
            <div class="div-title">Informação</div>
            
            A lista que se segue representa todos os Feedbacks que a Campanha está autorizada a chamar.
            Dentro das bases de dados da Campanha todos os contactos que estiverem com um dos Feedbacks abaixo configurados são contactos que o SIPS usará para realizar chamadas. 
            <br /><br />
            Uma das principais utilizações desta funcionalidade é a de configurar quais os Contactos que vão ser chamados depois de um Reset Manual às Bases de Dados da Campanha.
            <br /><br /> 
            A Reciclagem trabalha em conjunto com estas definições, sendo que, um Feedback que esteja configurado para ser reciclado tem de obrigatóriamente estar autorizado nesta lista. 
            <br /><br /> 
            As Chamadas Manuais não são limitadas por estas regras.  
            
            <div class="div-title">Feedbacks Disponíveis</div>
            <div style='height:213px; overflow:auto;'>
            <table id="table-config-dial-status">
                <tr>
                    <td width="50%" valign="top"><table id="table-config-dial-status-1"></table></td>
                    <td width="50%" valign="top"><table id="table-config-dial-status-2"></table></td>             
                </tr>
            </table>
                 
            </div>
            <div class="div-title"></div>
            <table>
            <tr>
            <td style='text-align:left; width:33%;'><button id="btn-config-dial-status-all">Todos</button></td>
            <td style='text-align:center; width:33%;'><button id="btn-config-dial-status-default">Repor</button></td>
            <td style='text-align:right; width:33%;'><button id="btn-config-dial-status-none">Nenhum</button></td>
            </tr>
            </table>
        </div> 
        
        <!-- pausas -->
        <!-- DIALOG CONFIRM APPLY TO ALL CAMPS --> 
        <div id="dialog-confirm-pause-apply-to-all-campaigns" style="display:none">
            <div class="div-title">Confirmação</div>
            Esta opção vai aplicar as definições da Campanha actual a <b>todas as Campanhas</b> existentes em sistema. <br /><br /> Clique <b><i>OK</i></b> para confirmar ou <b><i>Cancelar</i></b> para cancelar as alterações. 
        </div> 
        
        <!-- DIALOG EDIT PAUSE -->        
        <div id="dialog-edit-pause" style="display:none">
            <div class="div-title">Nome e Tempo de Pausa</div>
            <table >
            <tr>
            <td width="284px">
            <input style='width:272px' class="input-text" maxlength="30" type="text" id="input-edit-pause-name">
            </td>
            <td valign="bottom">
                            <span style="font-size:10px; margin:0px 0px 0px 0px">(max. 30 caracteres)</span>
                            </td>
            <td style='text-align:right'>
            <input id="spinner-pause-time-edit" style="width:24px;" type="text"  value =""/>
            </td>
            <td style='text-align:left; padding-left:3px; width:30px'>
            minutos
            </td>
            
            </tr>
            </table>
            
            <div class="div-title">Activa / Inactiva nas Campanhas</div>
            <div style='height:210px; overflow:auto;'>
            <table id="table-edit-pause-campaigns">
                <tr>
                    <td width="50%" valign="top"><table id="table-edit-pause-campaigns-1"></table></td>
                    <td width="50%" valign="top"><table id="table-edit-pause-campaigns-2"></table></td>             
                </tr>
            </table>
                
            </div>
            <div class="div-title"></div>
            <table>
            <tr>
            <td style='text-align:left; width:33%;'><button id="btn-edit-pause-all-campaigns">Todas</button></td>
            <td style='text-align:center; width:33%;'><button id="btn-edit-pause-current-campaign">Actual</button></td>
            <td style='text-align:right; width:33%;'><button id="btn-edit-pause-no-campaigns">Nenhuma</button></td>
            </tr>
            </table>
        </div> 
        
        <!-- feedbacks -->
        <!-- DIALOG EDIT FEED -->        
        <div id="dialog-edit-feed" style="display:none">
            <div class="div-title">Nome do Feedback</div>
            <table>
            <tr>
            <td width="284px">
            <input style='width:272px' class="input-text" maxlength="20" type="text" id="input-edit-feed-name">
            </td>
            <td valign="bottom">
                            <span style="font-size:10px; margin:0px 0px 0px 0px">(max. 20 caracteres)</span>
                            </td>
           
            </tr>
            </table>
            
            <div class="div-title">Activo / Inactivo nas Campanhas</div>
            <div style='height:210px; overflow:auto;'>
            <table id="table-edit-feed-campaigns">
                <tr>
                    <td width="50%" valign="top"><table id="table-edit-feed-campaigns-1"></table></td>
                    <td width="50%" valign="top"><table id="table-edit-feed-campaigns-2"></table></td>             
                </tr>
            </table>
                
            </div>
            <div class="div-title"></div>
            <table>
            <tr>
            <td style='text-align:left; width:33%;'><button id="btn-edit-feed-all-campaigns">Todas</button></td>
            <td style='text-align:center; width:33%;'><button id="btn-edit-feed-current-campaign">Actual</button></td>
            <td style='text-align:right; width:33%;'><button id="btn-edit-feed-no-campaigns">Nenhuma</button></td>
            </tr>
            </table>
        </div> 
        
        <!-- DIALOG CONFIRM APPLY TO ALL CAMPS --> 
        <div id="dialog-confirm-feed-apply-to-all-campaigns" style="display:none">
            <div class="div-title">Confirmação</div>
            Esta opção vai aplicar as definições da Campanha actual a <b>todas as Campanhas</b> existentes em sistema. <br /><br /> Clique <b><i>OK</i></b> para confirmar ou <b><i>Cancelar</i></b> para cancelar as alterações. 
        </div> 
		<!-- reciclagem -->
        <!-- DIALOG CONFIRM APPLY TO ALL CAMPS --> 
        <div id="dialog-confirm-recycle-apply-to-all-campaigns" style="display:none">
            <div class="div-title">Confirmação</div>
            Esta opção vai aplicar as definições da Campanha actual a <b>todas as Campanhas</b> existentes em sistema. <br /><br /> Clique <b><i>OK</i></b> para confirmar ou <b><i>Cancelar</i></b> para cancelar as alterações. 
        </div> 
        
        
        <!-- DIALOG EDIT RECYCLE -->        
        <div id="dialog-edit-recycle" style="display:none">
            <div class="div-title">Editar Valores</div>
            <table>
            <tr>
            	<td>Intervalo entre Tentativas:</td>
                <td width="20px"><img class='mono-icon' src='/images/icons/mono_stopwatch_16.png'></td>
                <td width="22px"><input id='spinner-edit-recycle-time' style="width:22px;" value="" /></td>
                <td width="24px"></td>
                <td>Nº máximo de Tentativas:</td>
                <td width='20px'><img  class='mono-icon' src='/images/icons/mono_reload_16.png'></td>
                <td width="22px"><input id='spinner-edit-recycle-tries' style="width:16px;" value="" /></td>
            </tr>
            </table>
            <br>
            <div class="div-title">Estado da Reciclagem</div>
    <div id='div-recycle-details-container' style='display:none;' class='dt-div-wrapper-10lines'>
        <table id='recycle-details'>
        <thead></thead>
        <tbody></tbody>
        <tfoot></tfoot>
        </table>
    </div>
    <center><button id='btn-recycle-reset-feedback' style='margin-top:12px'>Reset ao Feedback</button></center>


        </div> 
        
         <!-- DIALOG EDIT RECYCLE CONTACT DETAILS -->        
        <div id="dialog-edit-recycle-contact-details" style="display:none">
        <div id='div-recycle-contact-details-container' style='display:none;' class='dt-div-wrapper-10lines-with-icon'>
        <table id='recycle-contact-details'>
        <thead></thead>
        <tbody></tbody>
        <tfoot></tfoot>
        </table>
    	</div>
		<br>
    	<div class="div-title">Lista de Contactos <span class='css-orange-caret'>></span> Acções</div>
		<table border=1>
        <tr>
        <td width='20px'><button class="css-btn-small" id="btn-recycle-details-reset-all"><img style='opacity:0.6' src='icons/mono_undo_16.png'></button></td><td>Realiza um <i>reset</i> a todos os contactos da lista.</td><td width="32px"><button class="css-btn-small" id="btn-recycle-details-reset-all">Retirar da Reciclagem</button></td><td>Retira da Reciclagem todos os Contactos da lista.</td>
        </tr>
        </table>

        </div>
        
        
        
        <div id="dialog-edit-field-name" style="display:none">
        <label class="input-text-label" for="new-field-name">Nome do Campo Dinâmico</label><input style='width:194px' class="input-text" type="text" id="new-field-name">
        <div class="div-title">Informação</div>
        As alterações aos nomes dos campos feitas aqui apenas ficaram gravadas quando o utilizador clicar no botão "Gravar" no menu geral de edição de Campos Dinâmicos. 
        </div>
        
    
        <div id="dialog-edit-recycle-time" style="display:none">
        <div class="div-title">Novo Tempo de Reciclagem</div>
        <table>
            <tr>
                <td>
                    Em Minutos
                </td>
                <td style='text-align:right'>
                   <input id="spinner-edit-recycle-time" style="width:24px;" type="text"  value =""/>
                </td>
            </tr>
        </table>
        
        </div> 
        
        <div id="dialog-edit-recycle-tries" style="display:none">
        <div class="div-title">Nº de Tentativas de Reciclagem</div>
        <table>
            <tr>
                <td>
                    Em Minutos
                </td>
                <td style='text-align:right'>
                       <input id="spinner-edit-recycle-tries" style="width:24px;" type="text"  value =""/>
                </td>
            </tr>
        </table>
    
        </div> 
     
     
     
        
      
        
        <div id="dialog-load-leads" style="display:none">
    
            <div id='div-file-upload'>
                <div class="div-title">Escolha do Ficheiro</div>
                <form enctype="multipart/form-data" method="POST" action="upload.php">
                <table border=0>
                <tr>
                    <td><input type="file" size="80"  name="fileToUpload" id="fileToUpload" /></td>
                    
                </tr>
                <tr style='height:16px'>
                </tr>
                <tr>
                    <td style='text-align:center'><input id='btn-load-leads' type="button" onclick="UploadFile()" value="Carregar Contactos" /></td>
                </tr>
                </table>
                </form>
                
                <div id='div-upload-progress' style='display:none'>
                    <div class="div-title">Progresso</div>
                     
                    <table id="table-file-loader-progress">
                    <tr style="height:28px">
                    <td id="file-loader-progress-msg">A carregar o ficheiro com os contactos:</td> <td style="width:50px" id="file-loader-progress-loader"><img class="reset-loader" style='margin-top:3px; float:right;' src='/images/icons/loader-arrows.gif'></td>
                    </tr>
                    </table>
                    <br />
                    <center><input id="btn-continue" type="button" value="Continuar" /></center>
                </div>
            </div> 
            
        
        
            <div id="div-field-match" style='display:none'>
                <div class="div-title">Associação de Campos</div>
                <div style='height:503px; overflow-y:auto;'>
                <table id="table-field-match">
                </table>
                <br />
                
                </div>
                <br />
                <center><input type="button" id="btn-conclude" value="Continuar"  /></center>
            </div>
            
            <div id="div-field-match-result" style='display:none'>
                <div class="div-title">Progresso</div>
                <table id="table-field-match-progress">
                <tr style="height:28px">
                <td id="field-match-progress-msg">A inserir os contactos na base de dados:</td> <td style="width:50px" id="field-match-progress-loader"><img class="reset-loader" style='margin-top:3px; float:right;' src='/images/icons/loader-arrows.gif'></td>
                </tr>
                </table>
                <div class="div-title">Relatório</div>
                <table>
                <tr>
                <td>
                Total de Contactos:
                </td>
                <td style='text-align:right' id='td-error-log-total'></td>
                </tr>
                <tr>
                <td>
                Contactos Inseridos com Sucesso:
                </td>
                <td style='text-align:right' id='td-error-log-success'></td>
                </tr>
                <tr>
                <td>
                Contactos com Erro:
                </td>
                <td style='text-align:right' id='td-error-log-error'></td>
                </tr>
                </table>
                
                
                
                <center><input id="btn-final" type="button" value="Concluir e Fechar" /></center>
                
            </div>        
            
            
            
            
        </div>
        
       <div id="dialog-edit-dbs" style="display:none">
         <label class="input-text-label" for="edit-db-name">Nome da Base de Dados</label><input style='width:194px' class="input-text" type="text" id="edit-db-name">
    	<div class="div-title">Associar a outra Campanha</div>
        <select id='select-db-assoc-copy'></select>
        <div class="div-title">Reset à Base de Dados</div>
        <div><button id='btn-db-reset'>Reset à Base de Dados</button><button id='btn-view-dial-statuses'>Ver Estados em Marcação</button></div>
        <div class="div-title">Estado da Base de Dados</div>
        
        <table style='margin: 6px'><tr>
        			<td class='db-status-total1' style='text-align:center'></td>
                    <td class='db-status-total2' style='text-align:center'></td>
                    <td class='db-status-total3' style='text-align:center'></td>
		</tr></table>
        
        
        <div class='dt-div-wrapper-10lines'>
        <table id='table-db-status'>
        <thead></thead>
        <tbody></tbody>
        <tfoot></tfoot>
        </table>
    	</div>
        <br />
		<div style='width:100%; margin:4px;'><b>Informação: </b> A coluna "Chamados" e "Não Chamados" representam, respectivamente, os contactos que foram ou não chamados desde o último <i>reset</i> à Base de Dados.  </div>
        </div>   
    
    
		<!-- END -->

</div>
</div>


<script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
<script type="text/javascript" src="/jquery/jsdatatable/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/jquery/jsdatatable/plugins/plugin.fnAjaxReload.js"></script>
<script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.9.2.custom.min.js"></script>
<script type="text/javascript" src="/jquery/jqueryUI/plugins/datetimepicker.js"></script>
<script type="text/javascript" src="/jquery/jqueryUI/language/pt-pt.js"></script>
<script type="text/javascript" src="/jquery/colourPicker/colourPicker.js"></script>
<script type="text/javascript" src="/jquery/uniform/jquery.uniform.js"></script>



<script>
/* INITIALIZATION
*/
var User = "<?=$_SERVER['PHP_AUTH_USER']?>";
var CampaignID = "<?=$_GET['cid']?>";
var CampaignEdit;
var AllowedCampaigns;
var CampaignLists;

$(".back-button").button().click(function(){ window.location = "index.php"; })

$(function(){
	
if(CampaignID) { CampaignEdit = true; $(".back-button").show(); } else { CampaignEdit = false; $(".next-button").show(); }	


	//$('#wizard-tabs').tabs("option", "disabled", [1, 2, 3, 4, 5, 6]); 

	$("input[type=checkbox], input[type=radio] ").uniform();
	$( "#wizard-tabs" ).tabs({
			activate: function( event, ui ) 
			{
				if(ui.newPanel.selector == "#tab2"){ if(CampaignEdit) { PauseListBuilder('EDIT'); } else { PauseListBuilder('ALL'); } }
				if(ui.newPanel.selector == "#tab3"){ if(CampaignEdit) { FeedListBuilder('EDIT'); } else { FeedListBuilder('ALL'); } }
				if(ui.newPanel.selector == "#tab4"){ RecycleListBuilder('ALL'); }
			},
			heightStyle: "fill"
	}).show();
	


})


/* CONFIRM WIZARD EXIT / ROLLBACK
*/

$(window).bind('beforeunload', function(){

//$("#dialog-rollback").dialog("open");


	$.ajax({
			type: "POST",
			url: "requests.php",
			async: false,
			data: 
			{ 
				action: "RollbackIncompleteCampaign",
				sent_campaign_id: CampaignID
			},
			success: function(data) {

				}
	});

  
});

/*
$("#dialog-rollback").dialog({ 
    title: ' <span style="font-size:13px; color:black">Configurar Feedbacks chamados pela Campanha</span> ',
    autoOpen: false,
    height: 470,
    width: 550,
    resizable: false,
	modal: true,
	appendTo: window.parent.$("#ib"),
    buttons: { "Gravar" : function() { 

                    },
				"Cancelar" : function() { 
                    $(this).dialog("close"); 
                    }
            },
    open: function(){ 

        }
}); 
*/


/* ODDEVEN REFRESH 
*/
function OddEvenRefresh(Flag, Table)
{
	var oddeven;

	if($("#"+Table+" tr").length == 0 && Flag == "REMOVE"){ 
	
	if(Table.match("pauses")) $('#span-no-pauses').html("Não existem Pausas inactivas em sistema."); 
	if(Table.match("feeds")) $('#span-no-feeds').html("Não existem Feedbacks inactivos em sistema.");
	
	}

	$.each($("#"+Table+" tr"), function(index, value){
	
		if( ((index/2) % 1) != 0 )
		{
			oddeven = 0;
		}
		else
		{
			oddeven = 1;
		}
		
		$.each($(this).children(), function(){
			
			$(this).removeClass("odd-even-table-rows")	
			
			if($(this).is("td") && oddeven)
			{
				if(!$(this).hasClass("odd-even-ignore")){ $(this).addClass("odd-even-table-rows") }
			}

		})
	
	}) 

	
}


/* TAB NAVIGATION
*/
var LastEnabledTab = 0;
$(".next-button").button().click(function() 
{
	var CurrentTab = $( "#wizard-tabs" ).tabs( "option", "active" );
	var DisabledTabs = $( "#wizard-tabs" ).tabs( "option", "disabled" );
	var NextDisabledTab = DisabledTabs[0];
	switch(CurrentTab)
	{
		case 0:
		{  
			if(submit_OpcoesGerais())
			{ 
				if(NextDisabledTab-1 == CurrentTab)
				{
					$("#wizard-tabs").tabs("enable" , NextDisabledTab);
					$("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
				}
	
			}
		break;
		}
		case 1:
		{ 
			if(submit_Pausas())
			{ 
				if(NextDisabledTab-1 == CurrentTab)
				{
					$("#wizard-tabs").tabs("enable" , NextDisabledTab);
					$("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
				}
	
			}
		break;
		}
		case 2:
		{ 
			if(submit_Feedbacks())
			{ 
				if(NextDisabledTab-1 == CurrentTab)
				{
					$("#wizard-tabs").tabs("enable" , NextDisabledTab);
					$("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
				}
	
			}
		break;
		}
		case 3:
		{ 
			if(submit_Reciclagem())
			{ 
				if(NextDisabledTab-1 == CurrentTab)
				{
					$("#wizard-tabs").tabs("enable" , NextDisabledTab);
					$("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
				}
	
			}
		break;
		}
		case 4:
		{ 
			if(submit_Script())
			{ 
				if(NextDisabledTab-1 == CurrentTab)
				{
					$("#wizard-tabs").tabs("enable" , NextDisabledTab);
					$("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
				}
	
			}
		break;
		}
		case 5:
		{ 
			if(submit_Dfields())
			{ 
				if(NextDisabledTab-1 == CurrentTab)
				{
					$("#wizard-tabs").tabs("enable" , NextDisabledTab);
					$("#wizard-tabs").tabs("option" , "active", NextDisabledTab);
				}
	
			}
		break;
		}		
		/*case "li-pausas":{ var submitted = submit_Pausas(); console.log("passou pausas"); break;  }
		case "li-feeds" :{ var submitted = submit_Feedbacks(); break; }
		case "li-scripts" :{ var submitted = submit_Scripts(); break; }
		case "li-dfields" :{ var submitted = submit_DFields(); break; }
		case "li-recycle" :{ var submitted = submit_Reciclagem(); break; } */
	}

})


// OPCOES GERAIS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* INI
*/

$(function()
{
	$.ajax({
			type: "POST",
			url: "requests.php",
			dataType: "JSON",
			data: 
			{ 
				action: "CampaignInicialization",
				sent_campaign_id: CampaignID,
				sent_user: User
			},
			success: function(data) 
			{
				console.log(data);
				if(CampaignEdit)
				{
					var Checked;
					var OddEven;
					$("#campaign-name").val(data.c_name);
					$("#campaign-description").val(data.c_description)
					if(data.c_active == "Y"){ $("#campaign_active_yes").parent().addClass("checked"); } else { $("#campaign_active_no").parent().addClass("checked"); }
					if(data.c_dial_method == "RATIO"){ $("#campaign_type_auto").parent().addClass("checked"); $("#ratio-spinner").val(data.c_auto_dial_level); } else { $("#campaign_type_manual").parent().addClass("checked"); $( "#ratio-spinner" ).spinner("disable"); $("#ratio-spinner").val(0); }
					if(data.c_recording == "ALLFORCE"){ $("#campaign_recording_yes").parent().addClass("checked"); } else { $("#campaign_recording_no").parent().addClass("checked"); }
					if(data.c_lead_order == "RANDOM"){ $("#campaign_lead_order_random").parent().addClass("checked"); } else { $("#campaign_lead_order_ordered").parent().addClass("checked"); }
					if(data.c_next_agent_call == "longest_wait_time"){ $("#campaign_atrib_calls").val("Maior Tempo em Espera") } else if(data.c_next_agent_call == "random") { $("#campaign_atrib_calls").val("Aleatória") } else { $("#campaign_atrib_calls").val("Menos Chamadas Recebidas") }
					
					$.each(data.user_groups_id, function(index, value)
					{
						Checked = "";
						$.each(data.selected_user_groups, function(index1, value1)
						{
							if(Checked != ""){return false;}
							if(value == value1)
							{
								if(value == data.user_group)
								{ 
									Checked = "checked='checked' disabled='disabled'";
								} 
								else 
								{ 
									Checked = "checked='checked'";
								}
							}
							else
							{
								Checked = "";
							}
			
						})
					
					if(((index/2) % 1) != 0)
					{
						//if( (($("#table-groups-2 tr").length/2) % 1) == 0){ OddEven = " odd-even-table-rows"; } else { OddEven = "";}
						$("#table-groups-2").append("<tr class='"+OddEven+"'><td width=10px><input class='groups-checkbox' "+Checked+" type='checkbox' value='" + data.user_groups_id[index] + "' name='" + data.user_groups_id[index] + "' id='" + data.user_groups_id[index] + "'></td><td style='padding-top:1px'><label style='display:inline;' for='" + data.user_groups_id[index] + "'>" + data.user_groups_name[index] + "</label></td></tr>");
					}
					else
					{
						//if( (($("#table-groups-1 tr").length/2) % 1) == 0){ OddEven = " odd-even-table-rows"; } else { OddEven = "";}
						$("#table-groups-1").append("<tr class='"+OddEven+"'><td width=10px><input class='groups-checkbox' "+Checked+" type='checkbox' value='" + data.user_groups_id[index] + "' name='" + data.user_groups_id[index] + "' id='" + data.user_groups_id[index] + "'></td><td style='padding-top:1px'><label style='display:inline;' for='" + data.user_groups_id[index] + "'>" + data.user_groups_name[index] + "</label></td></tr>")
					}
					})
					$(".groups-checkbox").uniform();
					AllowedCampaigns = data.allowed;
										CampaignLists = data.campaign_lists;
					console.log(CampaignLists);
				}
				else
				{
					$("#campaign-name").val(data.new_campaign_name);
					
					
					
					$("#campaign_active_yes").parent().addClass("checked"); 
					$("#campaign_type_auto").parent().addClass("checked");
					$("#campaign_recording_yes").parent().addClass("checked"); 
					$("#campaign_lead_order_random").parent().addClass("checked"); 
					$("#campaign_atrib_calls").val("Maior Tempo em Espera");

					
					
					
					
					
					$.each(data.user_groups_id, function(index, value)
					{
						var Checked;				
						if(data.user_groups_id[index] == data.user_group)
						{ 
							Checked = "checked='checked' disabled='disabled'";
						} 
						else 
						{ 
							Checked = "";
						}	
						if(((index/2) % 1) != 0)
						{
							$("#table-groups-2").append("<tr><td width=10px><input class='groups-checkbox' "+Checked+" type='checkbox' value='" + data.user_groups_id[index] + "' name='" + data.user_groups_id[index] + "' id='" + data.user_groups_id[index] + "'></td><td style='padding-top:1px'><label style='display:inline;' for='" + data.user_groups_id[index] + "'>" + data.user_groups_name[index] + "</label></td></tr>");
						}
						else
						{
							$("#table-groups-1").append("<tr><td width=10px><input class='groups-checkbox' "+Checked+" type='checkbox' value='" + data.user_groups_id[index] + "' name='" + data.user_groups_id[index] + "' id='" + data.user_groups_id[index] + "'></td><td style='padding-top:1px'><label style='display:inline;' for='" + data.user_groups_id[index] + "'>" + data.user_groups_name[index] + "</label></td></tr>")
						}
					})
					$				
					$(".groups-checkbox").uniform();
					AllowedCampaigns = data.allowed;
					CampaignID = data.new_campaign_id;

				}
			$("#main-div").show();
			}
	});
})

/* CAMPAIGN DETAILS
*/

function UpdateCampaignName()
{
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "EditCampaignName",
				sent_campaign_id: CampaignID,
				sent_campaign_name: $("#campaign-name").val()
			},
			success: function(data) 
			{}
	}); 
	
}


$("#campaign-name")
.focus(function(){ $(this).css("border-color", "#E17009"); })
.blur(function(){ $(this).css("border-color", "#C0C0C0"); UpdateCampaignName(); })
.bind("keydown", function(e){ if(e.which == 13){ $(this).blur(); } })

function UpdateCampaignDescription()
{
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "EditCampaignDescription",
				sent_campaign_id: CampaignID,
				sent_campaign_description: $("#campaign-description").val()
			},
			success: function(data) 
			{}
	}); 
	
}

$("#campaign-description")
.focus(function(){ $(this).css("border-color", "#E17009"); })
.blur(function(){ $(this).css("border-color", "#C0C0C0"); UpdateCampaignDescription(); })



/* USER GROUPS
*/

$(".groups-checkbox").live("click", function()
{
	var AddorRemove;
	if($(this).parent().hasClass("checked"))
	{ 
		AddorRemove = 1;
	} 
	else 
	{ 
		AddorRemove = 0;
	}
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "EditCampaignAllowedGroups",
				sent_campaign_id: CampaignID,
				sent_user_group: $(this).attr("id"),
				sent_add_or_remove: AddorRemove
			},
			success: function(data) 
			{}
	});   
})

$("#all-groups").click(function()
{
	var AllGroups = new Array();
	$.each($(".groups-checkbox"), function(index, value)
	{
		if(!$(this).parent().parent().hasClass("disabled"))
		{
			$(this).parent().addClass("checked");
			AllGroups.push($(this).attr("id"))
		}
	})
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "EditCampaignAllowedGroupsALL",
				sent_campaign_id: CampaignID,
				sent_user_groups: AllGroups
			},
			success: function(data) 
			{}
		});   
})

$("#no-groups").click(function()
{
	var NoGroups = new Array();
	$.each($(".groups-checkbox"), function(index, value)
	{
		if(!$(this).parent().parent().hasClass("disabled")) 
		{ 
			$(this).parent().removeClass("checked");
			NoGroups.push($(this).attr("id"))
		}
		$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "EditCampaignAllowedGroupsNONE",
				sent_campaign_id: CampaignID,
				sent_user_groups: NoGroups
			},
			success: function(data) 
			{}
		});   

	})
})

/* GENERAL OPTIONS
*/


$(".campaign-active-switch").click(function()
{
	var CampaignActive;
	if($(this).attr("id") == 'campaign_active_yes')
	{
		CampaignActive = "Y";
	}
	else
	{
		CampaignActive = "N";
	}
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "EditCampaignActive",
				sent_campaign_id: CampaignID,
				sent_campaign_active: CampaignActive,
			},
			success: function(data) 
			{}
		});   
})

/**/

$(".campaign-type-switch").click(function()
{
	var CampaignType;
	var TempCampaignRatio;
	if($(this).attr("id") == 'campaign_type_auto')
	{
		CampaignType = "RATIO";
		TempCampaignRatio = 2;
		$("#ratio-spinner").spinner("enable");
		$("#ratio-spinner").val(2);
	}
	else
	{
		CampaignType = "MANUAL";
		TempCampaignRatio = 0;
		$( "#ratio-spinner" ).spinner( "disable" );
		$( "#ratio-spinner" ).val(0);
	}
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "EditCampaignType",
				sent_campaign_id: CampaignID,
				sent_campaign_type: CampaignType,
				sent_temp_ratio: TempCampaignRatio
			},
			success: function(data) 
			{}
		});   
})

/**/

$("#ratio-spinner").spinner({
    min:1, 
    max:5,
	spin: 	function(event, ui)
			{
				$.ajax({
						type: "POST",
						url: "requests.php",
						data: 
						{ 
							action: "EditCampaignRatio",
							sent_campaign_id: CampaignID,
							sent_value: ui.value 
						},
						success: function(data) 
						{}
					});   
			}
});

/* CALL OPTIONS
*/
$(".campaign-recording-switch").click(function()
{
	var Recording;
	if($(this).attr("id") == 'campaign_recording_yes')
	{
		Recording = "ALLFORCE";
	}
	else
	{
		Recording = "NEVER";
	}
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "EditCampaignRecording",
				sent_campaign_id: CampaignID,
				sent_campaign_recording: Recording 
			},
			success: function(data) 
			{}
		});   
})

/**/

$(".campaign-lead-order-switch").click(function()
{
	var LeadOrder;
	if($(this).attr("id") == 'campaign_lead_order_ordered')
	{
		LeadOrder = "DOWN";
	}
	else
	{
		LeadOrder = "RANDOM";
	}
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "EditLeadOrder",
				sent_campaign_id: CampaignID,
				sent_lead_order: LeadOrder 
			},
			success: function(data) 
			{}
		});   
})

/**/

$.widget("ui.modspinner1", $.ui.spinner, 
{
    options: 
	{
        min: 1,
        max: 3
    },
    _parse: function(value) 
	{
        if (typeof value === "string") 
		{
        	switch(value)
			{
                case "Maior Tempo em Espera" : return 1 ; 
                case "Aleatória" : return 2 ; 
                case "Menos Chamadas Recebidas": return 3;
            }
        }
        return value;
    },
    _format: function(value) 
	{
        switch(value)
		{
            case 1 : return "Maior Tempo em Espera"; 
            case 2 : return "Aleatória"; 
            case 3 : return "Menos Chamadas Recebidas"; 
        }
    }
});

$(function() 
{
    $("#campaign_atrib_calls").modspinner1({
        spin: 	function( event, ui ) 
				{
					//console.log(ui.value, $(this).attr("aria-valuenow"))
					if(ui.value != $(this).attr("aria-valuenow") || ui.value == 2)
					{
						$.ajax({
							type: "POST",
							url: "requests.php",
							data: 
							{ 
								action: "EditCallAtrib",
								sent_campaign_id: CampaignID,
								sent_value: ui.value 
							},
							success: function(data) 
							{}
						});            
					}
				}
	})
})


/* CONFIG DIAL STATUS
*/
$("#btn-config-dial-status").button().click(function(){
	$("#dialog-config-dial-status").dialog( "open" )
});

$("#dialog-config-dial-status").dialog({ 
    title: ' <span style="font-size:13px; color:black">Configurar Feedbacks chamados pela Campanha</span> ',
    autoOpen: false,
    height: 600,
    width: 550,
    resizable: false,
    buttons: { 	"Gravar":	function() 
							{ 
								var EditedDialStatus = new Array();
								$.each($(".checkbox-edit-dial-status"), function()
								{							
									if($(this).parent().hasClass("checked"))
									{
										EditedDialStatus.push($(this).attr("name"));
									}
								});
								$.ajax({
									type: "POST",
									url: "requests.php",
									data: 
									{ 
										action: "SaveCampaignDialStatus",
									 	sent_campaign_id: CampaignID,
									 	sent_edited_status: EditedDialStatus
									},
									success: function(data) {}
								});
							$(this).dialog("close");
							},
				"Fechar": function() 
							{ 
                    			$(this).dialog("close"); 
                			}
            },
    open: 	function()
			{ 
				$.ajax({
					type: "POST",
					url: "requests.php",
					dataType: "JSON",
					data:
					{ 
						action: "GetCampaignDialStatuses",
						sent_campaign_id: CampaignID,
						sent_allowed_campaigns: AllowedCampaigns
					},
					success: function(data) 
					{
						$("#table-config-dial-status-1").empty();
						$("#table-config-dial-status-2").empty();					
						$.each(data.status, function(index, value)
						{	
							var Checked;	
							var Disabled;
							var Recycle;
							if(data.selected[index] == 1)
							{ 
								Checked = "checked='checked'";
							} 
							else 
							{ 
								Checked = "";
							}
							if(data.status[index] == "ERI" || data.status[index] == "PDROP" || data.status[index] == "DC" || data.status[index] == "PU")
							{
								Disabled = "disabled='disabled'";
							}
							else
							{
								Disabled = "";
							} 	 
							if(index == 8)
							{
								$("#table-config-dial-status-2").append("<tr height='6px'><td></td></tr>");
								$("#table-config-dial-status-1").append("<tr height='6px'><td></td></tr>");
							}
							if(data.recycle[index] == 1)
							{
								Recycle = "<img style='opacity:0.6; height:14px; width:14px; margin-top:2px;' title='Em Reciclagem' src='/images/icons/mono_refresh_16.png'>";
								Disabled = "disabled='disabled'";
							}
							else
							{
								Recycle = "";
							}
							if( ((index/2) % 1) != 0 )
							{
								$("#table-config-dial-status-2").append("<tr><td>"+Recycle+"</td><td width=10px><input "+Disabled+" class='checkbox-edit-dial-status' "+Checked+" type='checkbox' value='"+data.status[index]+"' name='"+data.status[index]+"' id='"+data.status[index]+"'></td><td style='padding-top:1px'><label style='display:inline;' for='"+data.status[index]+"'>"+data.status_name[index]+"</label></td></tr>")
							}
							else
							{
								$("#table-config-dial-status-1").append("<tr><td>"+Recycle+"</td><td width=10px><input "+Disabled+" class='checkbox-edit-dial-status' "+Checked+" type='checkbox' value='"+data.status[index]+"' name='"+data.status[index]+"' id='"+data.status[index]+"'></td><td style='padding-top:1px'><label style='display:inline;' for='"+data.status[index]+"'>"+data.status_name[index]+"</label></td></tr>")
							}

						}) 
						$(".checkbox-edit-dial-status").uniform();
					}
			});
		}
}); 

$("#btn-config-dial-status-all").click(function()
{
	$.each($(".checkbox-edit-dial-status").parent(), function(index, value)
	{
		$(this).addClass("checked");
	})
})

$("#btn-config-dial-status-none").click(function()
{
	$.each($(".checkbox-edit-dial-status").parent(), function(index, value)
	{
		$(this).removeClass("checked");
	})
})

$("#btn-config-dial-status-default").click(function()
{
	var default_feeds = ['DC', 'PU', 'PDROP', 'ERI', 'NA', 'DROP', 'B', 'NEW']
	$.each($(".checkbox-edit-dial-status").parent(), function(index, value)
	{
		$(this).removeClass("checked");
	})
	$.each(default_feeds, function(index, value)
	{
		$(".checkbox-edit-dial-status[value='"+value+"']").parent().addClass("checked");	
	})
})


// PAUSAS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* INI
*/
var editedPause = "";

$('#spinner-new-pause-time').spinner({
    min: 0,
    max: 120,
    step: 5
});

$('#spinner-pause-time-edit').spinner({
    min: 0,
    max: 120,
    step: 5
});

	
/* PAUSE LIST BUILDER 
*/
function PauseListBuilder(Flag)
{
if(	($("#tbl-pauses tr").length == 0 && Flag == "ALL") || (Flag == "REFRESH") || (Flag == "DISABLED") || (Flag == "NEW") || ($("#tbl-pauses tr").length == 0 && Flag == "EDIT"))
{
	if(Flag != "NEW") { $("#tbl-pauses").hide(); }
	

	$.ajax({
		type: "POST",
		url: "requests.php",
		dataType: "JSON",
		data: { action: "PauseListBuilder",
				sent_campaign_id: CampaignID,
				sent_allowed_campaigns: AllowedCampaigns,
				sent_pause_code_name: $.trim($("#input-new-pause-name").val()),
				sent_max_time: $('#spinner-new-pause-time').val(),
				sent_flag: Flag
				},
		success: function(data) 
		{
			if(Flag == 'ALL' || Flag == "REFRESH" || Flag == "DISABLED" || Flag == "EDIT"){ $('#tbl-pauses').empty(); }
			if(data){
				
				var OddEven;
				var Checked;
				
				if($( "#span-no-pauses" ).length > 0){ $( "#span-no-pauses" ).html(""); }
		
				
				$.each(data.pause_code, function(index, value){
				
				if(Flag == "EDIT" || Flag == "REFRESH")
				{
					if(data.active[index] == 1)
					{
						Checked = "checked='checked'";
					}
					else
					{
						Checked = "";
					}
				}
					
				if(Flag == "DISABLED")
				{ 
					var PauseCheckBox = ""; 
					var PauseConfig = ""; 
					var PauseEnable = "<td width='16px' class='td-icon'><img class='mono-icon edit-pause-activate' to-enable='"+ data.pause_code[index] + "' title='Activar' style='cursor:pointer;' src='/images/icons/mono_plus_16.png'></td>"; 
				} 
				else
				{
					var PauseCheckBox = "<td class='odd-even-ignore' style='width:10px'><input "+Checked+" class='pause-active-inactive pause-checkbox' type='checkbox' value='"+ data.pause_code[index] + "' name='"+ data.pause_code[index] + "' id='"+ data.pause_code[index] + "'></td>";
					var PauseConfig = "<td width='32px' style='text-align:center' class=''><img style='float:none; cursor:pointer' to-edit='"+ data.pause_code[index] + "' class='mono-icon edit-pause' title='Configurar' style='cursor:pointer;' src='/images/icons/mono_wrench_16.png'></td>";
					var PauseEnable = "<td width='16px' class='td-icon'><img class='mono-icon edit-pause-deactivate' to-remove='"+ data.pause_code[index] + "' title='Desactivar' style='cursor:pointer;' src='/images/icons/mono_trash_16.png'></td>";
				}
			
							
					$("#tbl-pauses").prepend("\n\
					<tr class='tr-pause-rows' pause-id='"+ data.pause_code[index] + "' style='height:22px;'>\n\
						"+PauseCheckBox+"\n\
						<td style='padding:1px 0px 0px 3px; text-align:left;'><label class='label-pause-name' for='"+ data.pause_code[index] + "'>"+ data.pause_code_name[index] + "</label></td>\n\
						<td class='td-icon'><img class='mono-icon' style='margin-top:-3px' title='Tempo de Pausa' src='/images/icons/mono_cup_16.png'></td>\n\
						<td width='24px' style='text-align:left; padding:1px 0px 0px 3px;'><span><span id='span-pause-time-"+ data.pause_code[index] + "'>"+ data.max_time[index] + "</span></span>m</td>\n\
						<td width='24px'></td>\n\
						"+PauseEnable+"\n\
						"+PauseConfig+"\n\
					</tr>\n\
					")
				
				})

				$(".pause-checkbox").uniform();	
				OddEvenRefresh("", "tbl-pauses");
				$("#input-new-pause-name").val("");
				$("#spinner-new-pause-time").val("0");
				
			}
			else
			{
				if(Flag == "NEW"){
					$("#td-new-pause-error").html("<b>Já existe uma pausa com esse nome. Por favor altere o nome, e tente novamente.<b>");
					$("#input-new-pause-name").css("border-color", "red");
				}
				else
				{
					if(Flag == "ALL" || Flag == "EDIT"){ $('#span-no-pauses').html("Não existem Pausas em sistema."); }
					if(Flag == "DISABLED") { $('#span-no-pauses').html("Não existem Pausas inactivas em sistema."); }
					
				}
			}

		if(Flag != "NEW") { $("#tbl-pauses").fadeIn(400); }			
		}
	});
}
}

/* ACTIVATE/DEACTIVATE PAUSE 
*/
$(".pause-active-inactive").live("click", function()
{
	var Checked;
	var NumPauses;
	if($(this).parent().hasClass("checked"))
	{
		Checked = 1;
		NumPauses = 1;
	}
	else 
	{
		Checked = 0;
		NumPauses = -1;
	}

	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "PauseActiveInactive",
				sent_campaign_id: CampaignID,
				sent_pause_code_id:$(this).attr("id"),
				sent_pause_status: Checked,
				sent_num_pauses: NumPauses
			},
			success: function(data) {}
	});
});

/* HIDE PAUSE 
*/
$(".edit-pause-deactivate").live("click", function(){
	var toRemove = $(this).attr("to-remove");	
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "HidePause",
					sent_pause_code: toRemove
			},
			success: function(data) {
				$("tr[pause-id='"+toRemove+"']").remove()
				OddEvenRefresh("REMOVE", "tbl-pauses");
			}
	});
});

/* SHOW PAUSE 
*/
$(".edit-pause-activate").live("click", function(){
	var toEnable = $(this).attr("to-enable");	
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "ShowPause",
					sent_pause_code: toEnable
			},
			success: function(data) {
				$("tr[pause-id='"+toEnable+"']").remove()
				OddEvenRefresh("REMOVE", "tbl-pauses");
			}
	});

});

/* NEW PAUSE 
*/
$("#btn-new-pause").button().click(function(){
	PauseListBuilder("NEW");
});

$("#input-new-pause-name").bind("input", function(){
	if($("#td-new-pause-error").html().length > 0)
	{
		$("#td-new-pause-error").empty();
		$("#input-new-pause-name").css("border-color", "#C0C0C0");
	}	
});


/* VIEW ACTIVE/INACTIVE PAUSES
*/
$("#btn-pauses-active-or-inactive").click(function(){

if($("#btn-pauses-active-or-inactive").html().match("Inactivas")){
	PauseListBuilder("DISABLED");
	$(".span-pauses-active-inactive").html("&nbsp;(Inactivas)");
	$("#btn-pauses-active-or-inactive").html("Activas");
	$("#btn-select-all-pauses").attr("disabled", "disabled");
	$("#btn-select-no-pauses").attr("disabled", "disabled");
} 
else 
{
	PauseListBuilder("REFRESH");
	$(".span-pauses-active-inactive").html("");
	$("#btn-pauses-active-or-inactive").html("Ver Inactivas");
	$("#btn-select-all-pauses").removeAttr("disabled");
	$("#btn-select-no-pauses").removeAttr("disabled");
}

})

/* ACTIVATE ALL PAUSES
*/
$("#btn-select-all-pauses").click(function(){
	var Counter = 0;
	$.each($(".pause-active-inactive"), function(){
		$(this).parent().addClass("checked");
		Counter++;
	})
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "ActivateAllPauses",
					sent_campaign_id: CampaignID,
					sent_total_pauses: Counter
			},
			success: function(data) {
				
			}
	});
	

});

/* DEACTIVATE ALL PAUSES
*/
$("#btn-select-no-pauses").click(function(){
	
	$.each($(".pause-active-inactive"), function(){
		$(this).parent().removeClass("checked");
	})
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "DeactivateAllPauses",
					sent_campaign_id: CampaignID
			},
			success: function(data) {
				
			}
	});
});


/* APPLY CURRENT TO ALL CAMPAIGNS 
*/
$("#btn-pause-apply-to-all-campaigns").button().click(function(){
	$("#dialog-confirm-pause-apply-to-all-campaigns").dialog("open");
});


$("#dialog-confirm-pause-apply-to-all-campaigns").dialog({ 
    title: ' <span style="font-size:13px; color:black">Alerta!</span> ',
    autoOpen: false,
    height: 250,
    width: 300,
    resizable: false,
    buttons: { "OK" : function() 
	{ 
		
		
    var pause_ids = new Array();
    var pause_names = new Array();
    var pause_active = new Array();
    var pause_time = new Array();  
    
    
    
    $("#tbl-pauses tr td div span").each(function(){
        pause_ids.push($(this).children().attr("id"));
        pause_names.push($("label[for="+$(this).children().attr("id")+"]").html());
        if($(this).hasClass("checked")){pause_active.push(1)} else {pause_active.push(0)}
    });
    
    $("#tbl-pauses tr td span span").each(function(){     pause_time.push($(this).html()) })
    
    
    $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "ApplyPausesToAllCampaings",
                    sent_pause_ids: pause_ids, 
                    sent_pause_names: pause_names,
                    sent_pause_active: pause_active,
                    sent_pause_time: pause_time
                    },
            success: function(data) {}
        }); 		
		
		$(this).dialog("close");
	 
	}, 
	"Cancelar": function() { $(this).dialog("close");  }  },
    open: function(){}
}); 

/* EDIT PAUSE 
*/
$('.edit-pause').live("click", function(){
    editedPause = $(this).attr("to-edit");
    $("#dialog-edit-pause").dialog( "open" )
    
})
$("#dialog-edit-pause").dialog({ 
    title: ' <span style="font-size:13px; color:black">Editar Pausa</span> ',
    autoOpen: false,
    height: 470,
    width: 550,
    resizable: false,
    buttons: { "Gravar" : function() { 

					var editedPause_save_flag = true;
					$.each($(".label-pause-name"), function(){
						if(editedPause != $(this).attr("for"))
						{
							if($(this).html() == $("#input-edit-pause-name").val()){ editedPause_save_flag = false;}
						}
								
						
	
						})
				
					if(editedPause_save_flag)
					{
						
						$(this).dialog("close"); 
					 
					 
					var edited_campaigns_id = new Array();
					var edited_campaigns_active = new Array();
					
					
					$.each($(".checkbox-edit-pause-campaigns"), function(){
					
					edited_campaigns_id.push($(this).attr("name"));
					
					if($(this).parent().hasClass("checked"))
					{
						if($(this).attr("name") == CampaignID) { $("input[name='"+editedPause+"']").parent().addClass("checked")}
						edited_campaigns_active.push(1);
					}
					else
					{
						if($(this).attr("name") == CampaignID) { $("input[name='"+editedPause+"']").parent().removeClass("checked")}
						edited_campaigns_active.push(0);
					}
					
					});
					
					
					
					$.each(edited_campaigns_id, function(index, value){

						if(alteredPauses[value] != edited_campaigns_active[index] && edited_campaigns_active[index] == 1)
						{
							alteredPauses[value] = 1;
						}
						else if(alteredPauses[value] != edited_campaigns_active[index] && edited_campaigns_active[index] == 0)
						{
							alteredPauses[value] = -1;
						}				
						else
						{
							alteredPauses[value] = 0;
						}
		
					
						})
						
					//console.log(alteredPauses);
					
					
					
					 $.ajax({
						type: "POST",
						url: "requests.php",
						data: { action: "EditPause",
								sent_pause_id: editedPause,
								sent_pause_name: $("#input-edit-pause-name").val(),
								sent_pause_time: $("#spinner-pause-time-edit").val(),
								sent_edited_campaigns_ids: edited_campaigns_id,
								sent_edited_campaigns_active: edited_campaigns_active,
								sent_altered_pauses: alteredPauses
								},
						success: function(data) {
							
							
							                    
                    $("#span-pause-time-"+editedPause).html($("#spinner-pause-time-edit").val());
					$("label[for='"+editedPause+"']").html($("#input-edit-pause-name").val());
					
							
							}
					});
						
					}	
                    },
				"Fechar" : function() { 
                    $(this).dialog("close"); 
                    }
            },
    open: function(){ 
            $("#spinner-pause-time-edit").val($("#span-pause-time-"+editedPause).html())
			$("#input-edit-pause-name").val($("label[for='"+editedPause+"']").html());
			
			$("#table-edit-pause-campaigns-1").hide();
			$("#table-edit-pause-campaigns-2").hide();
			
			 
			$.ajax({
					type: "POST",
					url: "requests.php",
					dataType: "JSON",
					data: { action: "GetPauseEditCampaigns",
							sent_allowed_campaigns: AllowedCampaigns, 
							sent_pause_code_id: editedPause		
							},
					success: function(data) {
						
						alteredPauses = {};
						var checked_code;
						var BoldCurrent;
						
						$("#table-edit-pause-campaigns-1").empty();
						$("#table-edit-pause-campaigns-2").empty();
						
						$.each(data.c_id, function(index, value){
							
							alteredPauses[data.c_id[index]] = data.active[index];
							
							if(data.active[index] == "1"){ checked_code = "checked='checked'";} else { checked_code = "";}
							if(data.c_id[index] == CampaignID) { BoldCurrent = "font-weight:bold; font-size:13px"; } else { BoldCurrent = "";}	
							
							if( ((index/2) % 1) != 0 )
							{
								$("#table-edit-pause-campaigns-2").append("<tr><td><input class='checkbox-edit-pause-campaigns' "+checked_code+" type='checkbox' value='"+data.c_id[index]+"' name='"+data.c_id[index]+"' id='"+data.c_id[index]+"'><label style='display:inline; "+BoldCurrent+"' for='"+data.c_id[index]+"'>"+data.c_name[index]+"</label></td></tr>")
							}
							else
							{
								$("#table-edit-pause-campaigns-1").append("<tr><td><input class='checkbox-edit-pause-campaigns' "+checked_code+" type='checkbox' value='"+data.c_id[index]+"' name='"+data.c_id[index]+"' id='"+data.c_id[index]+"'><label style='display:inline; "+BoldCurrent+"' for='"+data.c_id[index]+"'>"+data.c_name[index]+"</label></td></tr>")
							}

						})
								 
						$(".checkbox-edit-pause-campaigns").uniform();
						
						$("#table-edit-pause-campaigns-1").fadeIn(300);
						$("#table-edit-pause-campaigns-2").fadeIn(300);
						
						
						}
				});
        }
}); 

/* EDIT PAUSE - ACTIVATE ALL CAMPAIGNS
*/
$("#btn-edit-pause-all-campaigns").click(function(){
	$.each($(".checkbox-edit-pause-campaigns").parent(), function(){
		$(this).addClass("checked");
	})
})

/* EDIT PAUSE - CURRENT CAMPAIGN
*/
$("#btn-edit-pause-current-campaign").click(function(){
	$.each($(".checkbox-edit-pause-campaigns").parent(), function(){
		$(this).removeClass("checked");
	})
	$("input[value="+CampaignID+"]").parent().addClass("checked");
})

/* EDIT PAUSE - DEACTIVATE ALL CAMPAIGNS
*/	
$("#btn-edit-pause-no-campaigns").click(function(){
	$.each($(".checkbox-edit-pause-campaigns").parent(), function(){
		$(this).removeClass("checked");
	})
})

// FEEDBACKS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* INIS
*/

var editedFeed;
var alteredFeeds = {};


/* FEED LIST BUILDER
*/
function FeedListBuilder(Flag)
{
if(	($("#tbl-feeds tr").length == 0 && Flag == "ALL") || (Flag == "REFRESH") || (Flag == "DISABLED") || (Flag == "NEW") || ( $("#tbl-feeds tr").length == 0 && Flag == "EDIT" ))
{
	if(Flag != "NEW") { $("#tbl-feeds").hide(); }
	

	$.ajax({
		type: "POST",
		url: "requests.php",
		dataType: "JSON",
		data: { action: "FeedListBuilder",
				sent_campaign_id: CampaignID,
				sent_allowed_campaigns: AllowedCampaigns,
				sent_status_name: $.trim($("#input-new-feed-name").val()),
				sent_status_human: function() { if($("#input-new-feed-human").parent().hasClass("checked")){ return "Y";} else { return "N"; } },
				sent_status_callback: function() { if($("#input-new-feed-callback").parent().hasClass("checked")){ return "Y";} else { return "N"; } },
				sent_flag: Flag
				},
		success: function(data) 
		{
			if(Flag == 'ALL' || Flag == "REFRESH" || Flag == "DISABLED" || Flag == "EDIT"){ $('#tbl-feeds').empty(); }
			if(data){
				
				var OddEven;
				var Checked;
				var human_checked;
				var callback_checked;
				
				if($( "#span-no-feeds" ).length > 0){ $( "#span-no-feeds" ).html(""); }
		
				
				$.each(data.status, function(index, value){
					
				if(Flag == "EDIT")
				{
					if(data.selectable[index] == "Y")
					{
						Checked = "checked='checked'";
					}
					else
					{
						Checked = "";
					}
				}
					
				if(data.human[index] == "Y"){ human_checked = "checked"; }	else { human_checked = ""; }
				if(data.callback[index] == "Y"){ callback_checked = "checked"; } else { callback_checked = ""; }
					
					
				if(Flag == "DISABLED")
				{ 
					var FeedCheckBox = ""; 
					var FeedConfig = ""; 
					var FeedEnable = "<td width='32px' style='text-align:center !important'><img class='mono-icon edit-feed-activate' to-enable='"+ data.status[index] + "' title='Activar' style='cursor:pointer; float:none' src='/images/icons/mono_plus_16.png'></td>"; 
				} 
				else
				{
					var FeedCheckBox = "<td class='odd-even-ignore' style='width:10px'><input "+Checked+" class='feed-active-inactive  feed-checkbox' type='checkbox' value='"+ data.status[index] + "' name='"+ data.status[index] + "' id='"+ data.status[index] + "'></td>";
					var FeedConfig = "<td class='css-td-list-actions'><img to-edit='"+ data.status[index] + "' class='css-img-list-actions edit-feed' title='Configurar' src='/images/icons/mono_wrench_16.png'></td>";
					var FeedEnable = "<td width='32px' style='text-align:center !important'><img class='mono-icon edit-feed-deactivate' to-remove='"+ data.status[index] + "' title='Desactivar' style='cursor:pointer; float:none;' src='/images/icons/mono_trash_16.png'></td>";
				}
			
	
					$("#tbl-feeds").prepend("\n\
					<tr class='tr-feed-rows' feed-id='"+ data.status[index] + "' style='height:22px;'>\n\
						"+FeedCheckBox+"\n\
						<td style='padding:1px 0px 0px 3px; text-align:left;'><label class='label-feed-name' for='"+ data.status[index] + "'>"+ $.trim(data.status_name[index]) + "</label></td>\n\
						<td class='odd-even-ignore' width='20px'><input class='input-feed-human feed-checkbox' type='checkbox' "+human_checked+" human='"+data.status[index]+"'></td>\n\
						<td width='22px'><img class='mono-icon' title='Resposta Humana' src='/images/icons/mono_speech_dual_16.png'></td>\n\
						<td width='12px'></td>\n\
						<td class='odd-even-ignore' width='20px'><input class='input-feed-callback feed-checkbox' type='checkbox' "+callback_checked+" callback='"+data.status[index]+"'></td>\n\
						<td width='22px'><img class='mono-icon' title='Callback' src='/images/icons/mono_phone_inverse_16.png'></td>\n\
						<td width='32px'></td>\n\
						"+FeedEnable+"\n\
						"+FeedConfig+"\n\
					</tr>\n\
					")
				
				})
				$(".feed-checkbox").uniform();
				OddEvenRefresh("", "tbl-feeds");
				$("#input-new-feed-name").val("");
				$("#input-new-feed-human").parent().addClass("checked");
				$("#input-new-feed-callback").parent().removeClass("checked");
				
			}
			else
			{
				if(Flag == "NEW"){
					$("#td-new-feed-error").html("<b>Já existe um Feedback com esse nome. Por favor altere o nome, e tente novamente.<b>");
					$("#input-new-feed-name").css("border-color", "red");
				}
				else
				{
					if(Flag == "ALL" || Flag == "EDIT"){ $('#span-no-feeds').html("Não existem Feedbacks em sistema."); }
					if(Flag == "DISABLED") { $('#span-no-feeds').html("Não existem Feedbacks inactivos em sistema."); }
					
				}
			}

		if(Flag != "NEW") { $("#tbl-feeds").fadeIn(400); }			
		}
	});
}
}

/* ACTIVATE/DEACTIVATE FEED
*/
$(".feed-active-inactive").live("click", function()
{
	var Checked;
	var NumFeeds;
	if($(this).parent().hasClass("checked"))
	{
		Checked = "Y";
		NumFeeds = 1;
	}
	else 
	{
		Checked = "N";
		NumFeeds = -1;
	}

	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "FeedActiveInactive",
				sent_campaign_id: CampaignID,
				sent_status:$(this).attr("id"),
				sent_status_status: Checked,
				sent_num_feeds: NumFeeds
			},
			success: function(data) {}
	});
});

/* ACTIVATE/DEACTIVATE HUMAN
*/
$(".input-feed-human").live("click", function()
{
	var Checked;
	if(typeof $(this).attr("checked") == 'undefined')
	{
		Checked = "N";
	}
	else 
	{
		Checked = "Y";
	}

	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "HumanActiveInactive",
				sent_campaign_id: CampaignID,
				sent_status:$(this).attr("human"),
				sent_status_status: Checked
			},
			success: function(data) {}
	});
});

/* ACTIVATE/DEACTIVATE CALLBACK
*/
$(".input-feed-callback").live("click", function()
{
	var Checked;
	if(typeof $(this).attr("checked") == 'undefined')
	{
		Checked = "N";
	}
	else 
	{
		Checked = "Y";
	}

	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "CallbackActiveInactive",
				sent_campaign_id: CampaignID,
				sent_status:$(this).attr("callback"),
				sent_status_status: Checked
			},
			success: function(data) {}
	});
});

/* HIDE FEED
*/
$(".edit-feed-deactivate").live("click", function(){
	var toRemove = $(this).attr("to-remove");	
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "HideFeed",
					sent_status: toRemove
			},
			success: function(data) {
				$("tr[feed-id='"+toRemove+"']").remove()
				OddEvenRefresh("REMOVE", "tbl-feeds");
			}
	});
});

/* SHOW FEED 
*/
$(".edit-feed-activate").live("click", function(){
	var toEnable = $(this).attr("to-enable");	
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "ShowFeed",
					sent_status: toEnable
			},
			success: function(data) {
				$("tr[feed-id='"+toEnable+"']").remove()
				OddEvenRefresh("REMOVE", "tbl-feeds");
			}
	});

});

/* NEW FEEDBACK
*/
$("#btn-new-feed").button().click(function(){
	FeedListBuilder("NEW");
	$("#tbl-recycle").empty();
	$("#select-new-recycle").empty();
});

$("#input-new-feed-name").bind("input", function(){
	if($("#td-new-feed-error").html().length > 0)
	{
		$("#td-new-feed-error").empty();
		$("#input-new-feed-name").css("border-color", "#C0C0C0");
	}	
});

/* VIEW ACTIVE/INACTIVE FEEDS
*/
$("#btn-feeds-active-or-inactive").click(function(){

if($("#btn-feeds-active-or-inactive").html().match("Inactivos")){
	FeedListBuilder("DISABLED");
	$(".span-feeds-active-inactive").html("&nbsp;(Inactivos)");
	$("#btn-feeds-active-or-inactive").html("Ver Activos");
	$("#btn-select-all-feeds").attr("disabled", "disabled");
	$("#btn-select-no-feeds").attr("disabled", "disabled");
} 
else 
{
	FeedListBuilder("REFRESH");
	$(".span-feeds-active-inactive").html("");
	$("#btn-feeds-active-or-inactive").html("Ver Inactivos");
	$("#btn-select-all-feeds").removeAttr("disabled");
	$("#btn-select-no-feeds").removeAttr("disabled");
}

})

/* ACTIVATE ALL FEEDS
*/
$("#btn-select-all-feeds").click(function(){
	$.each($(".feed-active-inactive"), function(){
		$(this).parent().addClass("checked");
	})
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "ActivateAllFeeds",
					sent_campaign_id: CampaignID
			},
			success: function(data) {
				
			}
	});
	

});

/* DEACTIVATE ALL FEEDS
*/
$("#btn-select-no-feeds").click(function(){
	
	$.each($(".feed-active-inactive"), function(){
		$(this).parent().removeClass("checked");
	})
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "DeactivateAllFeeds",
					sent_campaign_id: CampaignID
			},
			success: function(data) {
				
			}
	});
});

/* APPLY CURRENT TO ALL CAMPAIGNS 
*/
$("#btn-feed-apply-to-all-campaigns").button().click(function(){
	$("#dialog-confirm-feed-apply-to-all-campaigns").dialog("open");
});


$("#dialog-confirm-feed-apply-to-all-campaigns").dialog({ 
    title: ' <span style="font-size:13px; color:black">Alerta!</span> ',
    autoOpen: false,
    height: 250,
    width: 300,
    resizable: false,
    buttons: { "OK" : function() 
	{ 
    var feed_ids = new Array(), feed_names = new Array(), feed_callback = new Array(), feed_ishuman = new Array(), feed_active = new Array();
  
	$(".feed-active-inactive").each(function()
	{
        feed_ids.push($(this).attr("id"));
        feed_names.push($("label[for="+$(this).attr("id")+"]").html());
        if($(this).parent().hasClass("checked"))
		{
			feed_active.push("Y")
		} 
		else 
		{
			feed_active.push("N")
		}
    });
    



    $(".input-feed-human").each(function()
	{
        if($(this).parent().hasClass("checked"))
		{
          	feed_ishuman.push("Y")
	    }
		else 
		{
			feed_ishuman.push("N")
		}
	})


	$(".input-feed-callback").each(function()
	{
        if($(this).parent().hasClass("checked"))
		{
          	feed_callback.push("Y")
	    }
		else 
		{
			feed_callback.push("N")
		}
	})

   
    $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "ApplyFeedsToAllCampaings",
                    sent_campaign_id: CampaignID,
                    sent_feed_ids: feed_ids, 
                    sent_feed_names: feed_names,
                    sent_feed_active: feed_active,
                    sent_feed_callback: feed_callback,
                    sent_feed_ishuman: feed_ishuman
                    },
            success: function(data) {}
        }); 
    
    
 		
		
		$(this).dialog("close"); 
	 
	}, 
	"Cancelar": function() { $(this).dialog("close");  }  },
    open: function(){} 
}); 

/* EDIT FEEDBACK
*/
$('.edit-feed').live("click", function(){
    editedFeed = $(this).attr("to-edit");
    $("#dialog-edit-feed").dialog( "open" )
    
})
$("#dialog-edit-feed").dialog({ 
    title: ' <span style="font-size:13px; color:black">Editar Feedback</span> ',
    autoOpen: false,
    height: 470,
    width: 550,
    resizable: false,
    buttons: { "Gravar" : function() { 

					var editedFeed_save_flag = true;
					$.each($(".label-feed-name"), function(){
						if(editedFeed != $(this).attr("for"))
						{
							if($(this).html() == $("#input-edit-feed-name").val()){ editedFeed_save_flag = false;}
						}
								
						
	
						})
				
					if(editedFeed_save_flag)
					{
						
						$(this).dialog("close"); 
					 
					 
					var edited_campaigns_id = new Array();
					var edited_campaigns_active = new Array();
					
					
					$.each($(".checkbox-edit-feed-campaigns"), function(index, value){
					
					edited_campaigns_id.push($(this).attr("name"));
					
					
					
					if($(this).parent().hasClass("checked"))
					{
						if($(this).attr("name") == CampaignID) { $("input[name='"+editedFeed+"']").parent().addClass("checked")}
						edited_campaigns_active.push("Y");
					}
					else
					{
						if($(this).attr("name") == CampaignID) { $("input[name='"+editedFeed+"']").parent().removeClass("checked")}
						edited_campaigns_active.push("N");
					}
					

					
					});
					
					
					
						$.each(edited_campaigns_id, function(index, value){

						if(alteredFeeds[value] != edited_campaigns_active[index] && edited_campaigns_active[index] == "Y")
						{
							alteredFeeds[value] = 1;
						}
						else if(alteredFeeds[value] != edited_campaigns_active[index] && edited_campaigns_active[index] == "N")
						{
							alteredFeeds[value] = -1;
						}				
						else
						{
							alteredFeeds[value] = 0;
						}
		
					
						})
						
					//console.log(alteredFeeds);
					
					
					 $.ajax({
						type: "POST",
						url: "requests.php",
						data: { action: "EditFeed",
								sent_status: editedFeed,
								sent_status_name: $("#input-edit-feed-name").val(),
								sent_edited_campaigns_ids: edited_campaigns_id,
								sent_edited_campaigns_active: edited_campaigns_active,
								sent_altered_feeds: alteredFeeds
								},
						success: function(data) {
							
							
							                    
                    
					$("label[for='"+editedFeed+"']").html($("#input-edit-feed-name").val());
					
							
							}
					});
						
					}	
                    },
				"Fechar" : function() { 
                    $(this).dialog("close"); 
                    }
            },
    open: function(){ 

			$("#input-edit-feed-name").val($("label[for='"+editedFeed+"']").html());
			
			$("#table-edit-feed-campaigns-1").hide();
			$("#table-edit-feed-campaigns-2").hide();
			
			 
			$.ajax({
					type: "POST",
					url: "requests.php",
					dataType: "JSON",
					data: { action: "GetFeedEditCampaigns",
							sent_allowed_campaigns: AllowedCampaigns, 
							sent_status: editedFeed
							},
					success: function(data) {
						alteredFeeds = {};
						var checked_code;
						var BoldCurrent;
						
						$("#table-edit-feed-campaigns-1").empty();
						$("#table-edit-feed-campaigns-2").empty();
						
						$.each(data.c_id, function(index, value){

							alteredFeeds[data.c_id[index]] = data.selectable[index];
													
							if(data.selectable[index] == "Y"){ checked_code = "checked='checked'";} else { checked_code = "";}	
							if(data.c_id[index] == CampaignID){ CurrentBold = "font-weight:bold; font-size:13px";} else { CurrentBold = "";}	
							
							if( ((index/2) % 1) != 0 )
							{
								$("#table-edit-feed-campaigns-2").append("<tr><td><input class='checkbox-edit-feed-campaigns' "+checked_code+" type='checkbox' value='"+data.c_id[index]+"' name='"+data.c_id[index]+"' id='"+data.c_id[index]+"'><label style='display:inline; "+CurrentBold+"' for='"+data.c_id[index]+"'>"+data.c_name[index]+"</label></td></tr>")
							}
							else
							{
								$("#table-edit-feed-campaigns-1").append("<tr><td><input class='checkbox-edit-feed-campaigns' "+checked_code+" type='checkbox' value='"+data.c_id[index]+"' name='"+data.c_id[index]+"' id='"+data.c_id[index]+"'><label style='display:inline; "+CurrentBold+"' for='"+data.c_id[index]+"'>"+data.c_name[index]+"</label></td></tr>")
							}

						})
						
						//console.log(alteredFeeds);
								 
						$(".checkbox-edit-feed-campaigns").uniform();
						
						$("#table-edit-feed-campaigns-1").fadeIn(300);
						$("#table-edit-feed-campaigns-2").fadeIn(300);
						
						
						}
				});
        }
}); 


/* EDIT FEED - ACTIVATE ALL CAMPAIGNS
*/
$("#btn-edit-feed-all-campaigns").click(function(){
	$.each($(".checkbox-edit-feed-campaigns").parent(), function(){
		$(this).addClass("checked");
	})
})

/* EDIT FEED - CURRENT CAMPAIGN
*/
$("#btn-edit-feed-current-campaign").click(function(){
	$.each($(".checkbox-edit-feed-campaigns").parent(), function(){
		$(this).removeClass("checked");
	})
	$("input[value="+CampaignID+"]").parent().addClass("checked");
})

/* EDIT FEED - DEACTIVATE ALL CAMPAIGNS
*/	
$("#btn-edit-feed-no-campaigns").click(function(){
	$.each($(".checkbox-edit-feed-campaigns").parent(), function(){
		$(this).removeClass("checked");
	})
})

// RECICLAGEM //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/* INIS
*/
var editedRecycle;

$('#spinner-recycle-time').spinner({
    min: 5,
    max: 180,
    step: 5
});

$('#spinner-recycle-tries').spinner({
    min: 1,
    max: 10,
    step: 1
});

var EditRecycleTimeSpinner = $('#spinner-edit-recycle-time').spinner({
    min: 5,
    max: 180,
    step: 5
});

var EditRecycleTriesSpinner = $('#spinner-edit-recycle-tries').spinner({
    min: 1,
    max: 10,
    step: 1
});

/* RECYCLE LIST BUILDER
*/

function RecycleListBuilder(Flag)
{ 
if(	($("#tbl-recycle tr").length == 0 && Flag == "ALL") || ((Flag == "NEW") && ($("#select-new-recycle option:selected").val() != "---")) )
{
	if(Flag != "NEW") { $("#tbl-recycle").hide(); }
	
	$.ajax({
		type: "POST",
		url: "requests.php",
		dataType: "JSON",
		data: { action: "RecycleListBuilder",
				sent_campaign_id: CampaignID,
				sent_campaign_lists: CampaignLists,
				sent_recycle: $("#select-new-recycle option:selected").val(),
				sent_recycle_name: $("#select-new-recycle option:selected").html(),
				sent_delay: $("#spinner-recycle-time").val(),
				sent_maximum: $("#spinner-recycle-tries").val(),
				sent_allowed_campaigns: AllowedCampaigns,
				sent_flag: Flag
				},
		success: function(data) 
		{
			//Activate DataTables
			BuildTableRecycleContactDetails();
			
			
			console.log(data);
			if(Flag == 'ALL'){ $('#tbl-recycle').empty(); }
			if(data){
				
				var OddEven;
				var Checked;
				
				$.each(data.recycle, function(index, value){

				if(data.active[index] == "Y")
				{
					Checked = "checked='checked'";
				}	
				else
				{
					Checked = "";
				}
				
				if(data.recycle[index] == "ERI" || data.recycle[index] == "PDROP" || data.recycle[index] == "DC" || data.recycle[index] == "PU" || data.recycle[index] == "NA" || data.recycle[index] == "DROP" || data.recycle[index] == "B")
				{
					Disabled = "disabled='disabled'";
				}
				else
				{
					Disabled = "";
				} 	

				$("#tbl-recycle").prepend("\n\
					<tr class='tr-recycle-rows' recycle-id='"+ data.recycle[index] + "' style='height:22px;'>\n\
						<td class='css-td-list-check odd-even-ignore'><input "+Disabled+" "+Checked+" class='recycle-active-inactive recycle-checkbox' type='checkbox' id='"+ data.recycle[index] + "'></td>\n\
						<td class='css-td-list-label'><label class='label-recycle-name' for='"+ data.recycle[index] + "'>"+data.recycle_name[index]+"</label></td>\n\
						<td class='css-td-list-icon'><img class='css-img-list-icon' title='Contactos para Reciclar' src='/images/icons/mono_refresh_16.png'></td>\n\
						<td class='css-td-list-text'>"+data.recycle_count[index]+"</td>\n\
						<td class='css-td-list-icon'><img class='css-img-list-icon' title='Intervalo de Reciclagem' src='/images/icons/mono_stopwatch_16.png'></td>\n\
						<td class='css-td-list-text recycle-attempt-delay'>"+data.attempt_delay[index]+" m</td>\n\
						<td class='css-td-list-icon'><img class='css-img-list-icon' title='Nº máximo de Tentativas' src='/images/icons/mono_reload_16.png'></td>\n\
						<td class='css-td-list-text recycle-attempt-maximum'>"+data.attempt_maximum[index]+"</td>\n\
						<td class='css-td-list-actions'><img class='css-img-list-actions edit-recycle' to-edit='"+ data.recycle[index] + "' title='Configurar' src='/images/icons/mono_wrench_16.png'></td>\n\
					</tr>\n\
					")
				})
		
				
				$(".recycle-checkbox").uniform();
				OddEvenRefresh("", "tbl-recycle");
				
				if(Flag != "NEW"){
					$("#select-new-recycle").append("<option value='---'>---</option>")
					$.ajax({
						type: "POST",
						url: "requests.php",
						dataType: "JSON",
						data: { action: "RecycleAvailFeeds",
								sent_campaign_id: CampaignID
								},
						success: function(data){
						
						if(data)
						{
							$.each(data.status, function(index, value){
						
							$("#select-new-recycle").append("<option value='"+data.status[index]+"'>"+data.status_name[index]+"</option>")
						
						})
						}
						

								
						}
					})
				} else { $("#select-new-recycle option:selected").remove(); }
			}
			else
			{
				if(Flag == "NEW"){

				}
				else
				{
					
				} 
			}

		if(Flag != "NEW") { $("#tbl-recycle").fadeIn(400); }	
		}
	});

	}
	else
	{
		if(Flag == "NEW"){
			$("#td-new-recycle-error").html("<b>Por favor escolha um Feedback válido.</b>")
		}
		
	}
}

/* ACTIVATE/DEACTIVATE RECYCLE
*/
$(".recycle-active-inactive").live("click", function()
{
	var Checked;
	var NumRecycle;
	if($(this).parent().hasClass("checked"))
	{
		Checked = "Y";
		NumRecycle = 1;
	}
	else 
	{
		Checked = "N";
		NumRecycle = -1;
	}

	$.ajax({
			type: "POST",
			url: "requests.php",
			data: 
			{ 
				action: "RecycleActiveInactive",
				sent_campaign_id: CampaignID,
				sent_recycle:$(this).attr("id"),
				sent_recycle_status: Checked,
				sent_num_recycle: NumRecycle
			},
			success: function(data) {}
	});
});

/* NEW RECYCLE
*/
$("#btn-new-recycle").button().click(function(){
	RecycleListBuilder('NEW');
});

$("#select-new-recycle").click(function(){

$("#td-new-recycle-error").html("");

})

/* EDIT RECYCLE
*/
$('.edit-recycle').live("click", function(){
    editedRecycle = $(this).attr("to-edit");
    $("#dialog-edit-recycle").dialog( "open" )
    
})
$("#dialog-edit-recycle").dialog({ 
    title: "<span style='font-size:13px; color:black'>Editar Feedback em Reciclagem - <span style='color:#0073EA' id='span-edited-recycle'></span></span> ",
    autoOpen: false,
    height: 590,
    width: 550,
    resizable: false,
    buttons: { "Gravar" : function() { 


				$("tr[recycle-id='"+editedRecycle+"']").find(".recycle-attempt-delay").html(EditRecycleTimeSpinner.spinner("value") + " m")
				$("tr[recycle-id='"+editedRecycle+"']").find(".recycle-attempt-maximum").html(EditRecycleTriesSpinner.spinner("value"))


				 $.ajax({
						type: "POST",
						url: "requests.php",
						data: { action: "EditRecycle",
								sent_recycle: editedRecycle,
								sent_recycle_time: EditRecycleTimeSpinner.spinner("value"),
								sent_recycle_tries: EditRecycleTriesSpinner.spinner("value"),
								sent_campaign_id: CampaignID
								},
						success: function(data) {}
					}); 

				$(this).dialog("close");

				
                    },
				"Fechar" : function() { 
                    $(this).dialog("close"); 
                    }
            },
    open: function(){ 
				$("#div-recycle-details-container").hide();
			$("#span-edited-recycle").html($("label[for='"+editedRecycle+"']").html());
			
			EditRecycleTimeSpinner.spinner("value", $.trim($("tr[recycle-id='"+editedRecycle+"']").find(".recycle-attempt-delay").html().replace(" m", "")));
			EditRecycleTriesSpinner.spinner("value", $.trim($("tr[recycle-id='"+editedRecycle+"']").find(".recycle-attempt-maximum").html()));
		
			BuildTableRecycleDetails();
			
        }
}); 





$(".recycle-details-reset-single").live("click", function(){

var clickedRow = $(this).closest("tr")[0]._DT_RowIndex;
var clickedIndex = recycleTable.fnGetData( clickedRow );

var zeroTriesValue = $(this).closest("tbody")[0].rows[0].cells[1];
var resetedValue = $(this).closest("tr")[0].cells[1];


if(clickedIndex[4] != 0){
	
	$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "EditRecycleResetSingleTries",
					sent_recycle: editedRecycle,
					sent_index: clickedIndex[4],
					sent_campaign_lists: CampaignLists
					},
			success: function(data) { }
		}); 
		
		zeroTriesValue.innerHTML = parseInt(resetedValue.innerHTML) + parseInt(zeroTriesValue.innerHTML);
		resetedValue.innerHTML = 0;
}
})

$("#btn-recycle-reset-feedback").click(function(){

var totalTries = 0;

$.each($("#recycle-details tbody tr"), function(){
	var currentCell = $(this)[0].cells[1];
	totalTries += parseInt(currentCell.innerHTML)
	currentCell.innerHTML = 0;
})
$("#recycle-details tbody")[0].rows[0].cells[1].innerHTML = totalTries;

$.ajax({
		type: "POST",
		url: "requests.php",
		data: { action: "EditRecycleResetAllTries",
				sent_recycle: editedRecycle,
				sent_campaign_lists: CampaignLists
				},
		success: function(data) { }
		}); 




})






var recycleTable;
function BuildTableRecycleDetails()
{

	recycleTable = $('#recycle-details').dataTable( {
		"aaSorting": [[4, 'asc']],
		"iDisplayLength": 12,
		"sDom": '<"top"><rt><"bottom">',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true, 
		"bRetrieve": false,
		"bDestroy": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": 'requests.php',
		"fnServerParams": function (aoData)  
		{
			aoData.push( 
						{ "name": "action", "value": "GetRecycleDetails" },
						{ "name": "sent_recycle", "value": editedRecycle },
						{ "name": "sent_campaign_id", "value": CampaignID },
						{ "name": "sent_campaign_lists", "value": CampaignLists }
					   )
		},
		"aoColumnDefs": [
                        { "bSearchable": false, "bVisible": false, "aTargets": [ 4 ] }
                    ],
		"aoColumns": [ 
						
						{ "sTitle": "Tentativas", "sWidth":"16px", "sClass":"css-dt-column-align-center" },
						{ "sTitle": "Nº de Contactos", "sWidth": "64px", "sClass":"css-dt-column-align-center tempclass" }, 
						{ "sTitle": "Reset", "sWidth": "16px", "sClass":"css-dt-column-align-center" },
						{ "sTitle": "Ver Detalhes", "sWidth": "32px", "sClass":"css-dt-column-align-center" },
						{ "sTitle": "index" }
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnDrawCallback": 	function()
							{ 
								$("#recycle-details").css("width", "100%");						
							}
		});
	
	$("#div-recycle-details-container").fadeIn(400);
	
}

/* RECYCLE CONTACT DETAILS
*/

var editedTryType;

$(".recycle-details-view-contacts").live("click", function(){

editedTryType = $(this).attr("inner-try-type");
$("#dialog-edit-recycle-contact-details").dialog("open");


console.log($(this).attr("inner-try-type"))


})


$("#dialog-edit-recycle-contact-details").dialog({ 
    title: "<span style='font-size:13px; color:black'><span class='ui-icon ui-icon-info css-dialog-title-icon'></span> Ver Detalhes </span> ",
    autoOpen: false,
    height: 590,
    width: 950,
    resizable: false,
    buttons: { "Fechar" : function() { 
                    $(this).dialog("close"); 
					
             }
    },
    open: function(){ 	$("#span-recycle-contact-details-feed").html($("label[for='"+editedRecycle+"']").html())
						switch(editedTryType)
						{
							case "N": $("#span-recycle-contact-details-try").html("Não Chamado"); break;
						}
						$("#div-recycle-contact-details-container").hide(); 
						recycleContactDetails.fnReloadAjax(); 
						$("#div-recycle-contact-details-container").fadeIn(250);  
					},
	position: { my: "center+10%", of: "#recycle-details" }
}); 

var recycleContactDetails;


function BuildTableRecycleContactDetails()
{
	recycleContactDetails = $('#recycle-contact-details').dataTable( {
		"aaSorting": [[0, 'asc']],
		"iDisplayLength": 10,
		"sDom": '<"top"f><"dt-fixed-10lines-with-icon"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true, 
		"bRetrieve": false,
		"bDestroy": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": 'requests.php',
		"fnServerParams": function (aoData)  
		{
			aoData.push( 
						{ "name": "action", "value": "GetRecycleContactDetails" },
						{ "name": "sent_recycle", "value": editedRecycle },
						{ "name": "sent_try_type", "value": editedTryType },
						{ "name": "sent_campaign_lists", "value": CampaignLists }
					   )
		},
		"aoColumnDefs": [
                        
                    ],
		"aoColumns": [ 
						
						{ "sTitle": "Nome", "sClass":"css-dt-column-align-left" },
						{ "sTitle": "Telefone", "sWidth": "16px", "sClass":"css-dt-column-align-center" }, 
						{ "sTitle": "Total de Chamadas", "sWidth": "16px", "sClass":"css-dt-column-align-center" },
						{ "sTitle": "Última Chamada", "sWidth": "125px", "sClass":"css-dt-column-align-center" },
						{ "sTitle": "Reset", "sWidth": "16px", "sClass":"css-dt-column-align-center" },
						{ "sTitle": "Retirar da Reciclagem", "sWidth": "16px", "sClass":"css-dt-column-align-center" }
					 ], 
		"fnDrawCallback": 	function()
							{ 
							if(typeof $("#div-recycle-contact-details-title").html() == 'undefined') $("#recycle-contact-details_wrapper .top").append("<div class='css-dt-title' id='div-recycle-contact-details-title'>Lista de Contactos <span class='css-orange-caret'>></span> Feedback: <span style='color:#0073EA' id='span-recycle-contact-details-feed'></span> <span class='css-orange-caret'>></span> Reciclagem: <span style='color:#0073EA' id='span-recycle-contact-details-try'></span></div>"); 
							},
		"oLanguage": { "sUrl": "extras/datatables/language.txt" }
		});
	
	
}




/* APPLY CURRENT TO ALL CAMPAIGNS 
*/
$("#btn-recycle-apply-to-all-campaigns").button().click(function(){
	$("#dialog-confirm-recycle-apply-to-all-campaigns").dialog("open");
});


$("#dialog-confirm-recycle-apply-to-all-campaigns").dialog({ 
    title: ' <span style="font-size:13px; color:black">Alerta!</span> ',
    autoOpen: false,
    height: 250,
    width: 300,
    resizable: false,
    buttons: { "OK" : function() 
	{ 
		
		
    var recycle_ids = new Array();
    var recycle_active = new Array();
    var recycle_time = new Array();
	var recycle_tries = new Array();  
    
    
    
    $("#tbl-recycle tr").each(function(){

	recycle_ids.push($(this).attr("recycle-id"));
	
	if($(this).find(".checker").children().hasClass("checked"))
	{ recycle_active.push("Y") }
	else
	{ recycle_active.push("N") }
	
	recycle_time.push($(this).find(".recycle-attempt-delay").html().replace(" m", ""));
	recycle_tries.push($(this).find(".recycle-attempt-maximum").html())
	

	


    });

    
    $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "ApplyRecycleToAllCampaings",
                    sent_recycle_ids: recycle_ids, 
                    sent_recycle_active: recycle_active,
                    sent_recycle_time: recycle_time,
                    sent_recycle_tries: recycle_tries
                    },
            success: function(data) {}
        }); 	
		
		$(this).dialog("close");
	 
	}, 
	"Cancelar": function() { $(this).dialog("close");  }  },
    open: function(){}
}); 


// ASSOCIAR SCRIPT /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function submit_Script(){
    return true;    
}

$( "#new-script" ).click(function(){
    $("#table-scripts tr td div span").each(function(){
        if($(this).hasClass("checked"))
        {   
        
            var camp_id = $(this).children().attr("id");
            var camp_name = $("label[for="+camp_id+"]").html();
        
            $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "submit_script",
                    sent_campaign_id: CampaignID, 
                    sent_campaign_copy: camp_id 
                    },
            success: function(data) { }
            });
             $("#span-script").html("<table><tr><td style='width:16px'><img style='cursor:pointer' id='remove-script' class='mono-icon' src='/images/icons/mono_cancel_16.png'></td><td style='width:8px'></td><td>O Script <b>"+ camp_name +"</b> está associado à Campanha. </td></tr></table>") }
        
    })
})

$("#remove-script").live("click", function(){
    $("#span-script").html("Nenhum Script associado.");
    $("#table-scripts tr td div span").each(function(){
        $(this).removeClass("checked");
    })
	        $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "remove_script",
                    sent_campaign_id: CampaignID
                    },
            success: function(data) { }
            });
})

// CAMPOS DINAMICOS ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function submit_Dfields(){
    
var sortedIDs = $("#dfields-sortable").sortable("toArray");
var sortedLabels = new Array();
var sortedReadOnly = new Array();
var sortedOrder = new Array();  

$.each(sortedIDs, function(index, value){
    sortedLabels.push($("label[for=i-"+value+"]").html())
    if($("#ro-"+value).attr("src")=="/images/icons/mono_document_16.png"){sortedReadOnly.push(1)} else {sortedReadOnly.push(0)};
    sortedOrder.push(index);
})

    // VER FIELDS IN USE
    var array_in_use = new Array();
    $("#dfields-sortable li").each(function(){
        array_in_use.push($(this).attr("id"))
    })
    
    //VER OS QUE FALTAM
    var current_array_item = "";
    $.each(array_in_use, function(index, value){
        current_array_item = value;
        $.each(all_fields, function(index, value){ if(current_array_item == value){all_fields.splice(index, 1)}   })
        })
    



$.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "submit_dfields",
                    sent_campaign_id : CampaignID,
                    sent_sortedIDs: sortedIDs,
                    sent_sortedLabels: sortedLabels,
                    sent_sortedReadOnly: sortedReadOnly,
                    sent_fillers : all_fields,
                    sent_sortedOrder: sortedOrder
                    },
            success: function(data) { }
            });
}

////////



$(".remove-field").live("click", function(){
    $(this).parent().parent().parent().parent().parent().remove();
})

var edit_id = ""; 
$(".edit-field").live("click", function(){
    edit_id = $(this).parent().parent().parent().parent().parent().attr("id");  
    $("#dialog-edit-field-name").dialog("open");
})

var avail_fields;
var all_fields = new Array('vendor_lead_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','date_of_birth','alt_phone','email','security_phrase', 'owner','comments', 'extra1', 'extra2', 'extra3', 'extra4', 'extra5', 'extra6', 'extra7', 'extra8', 'extra9', 'extra10', 'extra11', 'extra12', 'extra13', 'extra14', 'extra15')


////////

$('#new-field').click(function(){
    var current_li = "";
	avail_fields = new Array('PHONE_NUMBER','TITLE','FIRST_NAME','MIDDLE_INITIAL','LAST_NAME','ADDRESS1','ADDRESS2','ADDRESS3','CITY','STATE','PROVINCE','POSTAL_CODE','COUNTRY_CODE','DATE_OF_BIRTH','ALT_PHONE','EMAIL','SECURITY_PHRASE','COMMENTS', 'extra1', 'extra2', 'extra3', 'extra4', 'extra5', 'extra6', 'extra7', 'extra8', 'extra9', 'extra10', 'extra11', 'extra12', 'extra13', 'extra14', 'extra15');
    $("#dfields-sortable li").each(function(index1, value1){
		current_li = $(this).attr("id");
		$.each(avail_fields, function(index2, current_field){ if(current_li == current_field){ avail_fields.splice(index2, 1) } })
        })  
    var new_field_id = avail_fields[0];
    var new_field_value = $('#add-field-name').val();
    var new_field_readonly = "";
    $(".rdonly-chooser div span").each(function(){

        if($(this).hasClass("checked") && $(this).children().attr("id")=="new-field-rdonly-no"){ new_field_readonly = "/images/icons/mono_document_empty_16.png"; return false; } else { new_field_readonly = "/images/icons/mono_document_16.png"; return false; }
        
        })
    $('#dfields-sortable').append("<li id='"+new_field_id+"' class='cursor-move'>\n\
									<table>\n\
									<tr class='height24'><td width='16px'><img class='mono-icon cursor-pointer remove-field' src='/images/icons/mono_cancel_16.png'></td>\n\
									<td width='16px'><img id='ro-"+new_field_id+"' class='mono-icon cursor-pointer icon-readonly' src='"+new_field_readonly+"'></td>\n\
									<td><label class='cursor-move' style='display:inline; margin-left:3px' for='i-"+new_field_id+"'>"+new_field_value+"</label></td>\n\
									<td width='16px'><img class='mono-icon cursor-pointer edit-field' src='/images/icons/mono_document_edit_16.png'></td>\n\
									</tr>\n\
									</table>\n\
									</li>\n\
									")   
    $("#add-field-name").val("");
})

$('.icon-readonly').live("click", function(){
    if($(this).attr("src")=="/images/icons/mono_document_16.png"){$(this).attr("src", "/images/icons/mono_document_empty_16.png")} else { $(this).attr("src", "/images/icons/mono_document_16.png") }
    })

$("#dialog-edit-field-name").dialog({ 
    title: ' <span style="font-size:13px; color:black">Alteração do Nome do Campo</span> ',
    autoOpen: false,
    height: 280,
    width: 250,
    resizable: false,
    buttons: { "Gravar" : function() { $(this).dialog("close"); $("label[for=i-"+edit_id+"]").html($("#new-field-name").val())  }, "Fechar" : function() { $(this).dialog("close"); } },
    open: function(){ $("button").blur(); $("#new-field-name").val($("label[for=i-"+edit_id+"]").html()) }
}); 

$("#dfields-sortable").sortable();


$("#campaign-copy-button").click(function(){
	$.ajax({
            type: "POST",
            url: "requests.php",
			dataType: "JSON",
            data: { action: "copy_dfields",
					sent_campaign_id : CampaignID,
                    sent_campaign_id_copy : $("#campaign-copy option:selected").val(),
                    },
            success: function(data) {
					$("#dfields-sortable").empty();
					
					$.each(data.name, function(index, value){
						
					if(value == "PHONE_NUMBER" || value == "ALT_PHONE" || value == "ADDRESS3" || value == "FIRST_NAME" || value == "COMMENTS")
					{
						var cancel = "<td width='19px'></td>";
					}
					else 
					{
						var cancel = "<td width='16px'><img class='mono-icon cursor-pointer remove-field' src='/images/icons/mono_cancel_16.png'></td>";
					}
					
					$("#dfields-sortable").append("<li id='"+value+"' class='cursor-move'>\n\
					<table>\n\
					<tr class='height24'>\n\
					"+cancel+"\n\
					<td width='16px'><img id='ro-"+value+"' class='mono-icon cursor-pointer icon-readonly' src='/images/icons/mono_document_empty_16.png'></td>\n\
					<td><label class='cursor-move' style='display:inline; margin-left:3px' for='i-"+value+"'>"+data.display_name[index]+"</label></td>\n\
					<td width='16px'><img class='mono-icon cursor-pointer edit-field' src='/images/icons/mono_document_edit_16.png'></td>\n\
					<tr>\n\
					</table>\n\
					</li>\n\
					")	
						
						
						
					})
					
					
				
				
				
				 }
            });


	
	
})

/*


if($default_fields[$i]=="phone_number" || $default_fields[$i]=="alt_phone" || $default_fields[$i]=="address3" || $default_fields[$i]=="first_name" || $default_fields[$i]=="comments") 
        { $td_cancel = "<td width='19px'> </td>"; }
        else 
        { $td_cancel = "<td width='16px'> <img class='mono-icon cursor-pointer remove-field' src='/images/icons/mono_cancel_16.png'> </td> "; }
        $dinamic_fields .= "<li id='$default_fields[$i]' class='cursor-move'>
                            <table>
                                <tr class='height24'>
                                    
                                    $td_cancel 
                                    <td width='16px'> <img id='ro-$default_fields[$i]' class='mono-icon cursor-pointer icon-readonly' src='/images/icons/mono_document_empty_16.png'> </td>
                                    <td><label class='cursor-move' style='display:inline; margin-left:3px' for='i-$default_fields[$i]'>$default_labels[$i]</label></td>
                                    <td width='16px'><img class='mono-icon cursor-pointer edit-field' src='/images/icons/mono_document_edit_16.png'></td>
                                </tr>
                            </table>
                        </li>";




*/



// BASES DE DADOS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

var ActiveDB;
var ActiveDB_edit;
var ConvertedFile;

// DIALOG EDIT DB

var oTable_db_status;

$(".edit-dbs").live("click", function(){
	ActiveDB_edit = $(this).attr("diff");
    $("#dialog-edit-dbs").dialog("open");
	
	
	oTable_db_status = $('#table-db-status').dataTable( {
		"aaSorting": [[0, 'asc']],
		"iDisplayLength": 12,
		"sDom": '<"top"><"dt-fixed-10lines"rt><"bottom"p>',
		"bSortClasses": false,
		"bJQueryUI": true,  
		"bProcessing": true,
		"bRetrieve": false,
		"bDestroy": true,
		"sPaginationType": "full_numbers",
		"sAjaxSource": 'requests.php',
		"fnServerParams": function (aoData)  
		{ 
			aoData.push( 
						{ 	"name": "action", "value": "datatable-db-status"  }, 
						{ 	"name": "sent_list_id", "value": "40088"  }
					   )
		},
		"aoColumns": [ 
						{ "sTitle": "Estado do Contacto", "sWidth":"96px" },
						{ "sTitle": "Chamados *", "sWidth":"16px", "sClass":"dt-column-center" }, 
						{ "sTitle": "Não Chamados *", "sWidth": "16px", "sClass": "dt-column-center" }
						
						
						
					 ], 
		"oLanguage": { "sUrl": "../../jquery/jsdatatable/language/pt-pt.txt" },
		"fnDrawCallback": function()
		{
			
			var db_status_total = 0;
			var db_status_called = 0;
			var db_status_not_called = 0;
			
			var db_status_data = oTable_db_status.fnGetData();
			
			

			$.each(db_status_data, function(index, value){
				
				
				db_status_called += parseInt(value[1]);
				db_status_not_called += parseInt(value[2]);
				db_status_total += (parseInt(value[1]) + parseInt(value[2]));				
				
			
				
			
				
			})
			$(".db-status-total1").html("Total de Contactos: <b>" + db_status_total + "</b>");
			$(".db-status-total2").html("Contactos Chamados: <b>" + db_status_called + "</b>");
			$(".db-status-total3").html("Contactos Não Marcados: <b>" + db_status_not_called + "</b>");
			
			 
		}
		});
	
	
	
	
	
	
	
	
})

$("#dialog-edit-dbs").dialog({ 
    title: ' <span style="font-size:13px; color:black">Editar Base de Dados</span> ',
    autoOpen: false,
    height: 700,
    width: 600,
    resizable: false,
    buttons: 
	{ "OK" : function() 
		{ 
			$(this).dialog("close");
			if($("#edit-db-name").val() != $("label[for=i-"+ActiveDB_edit+"]").html()){
		
				$.ajax({
					type: "POST",
					url: "requests.php",
					dataType: "JSON",
					data: { action: "change_db_name",
							sent_db_id: ActiveDB_edit,
							sent_db_name: $("#edit-db-name").val(),
							},
					success: function(result){
						$("label[for=i-"+ActiveDB_edit+"]").html($("#edit-db-name").val()) 
					}
				})
			}
			//console.log($("#select-db-assoc-copy option:selected").val());
		}, 
		"Cancelar" : function() { $(this).dialog("close"); } },
    open: function(){ 
	
	$("#edit-db-name").val($("label[for=i-"+ActiveDB_edit+"]").html());
	
	/* TEMP TEMP TEMP TEMP TEMP */
	campaign_id = "C00273";
	
	
	
	$.ajax({
            type: "POST",
            url: "requests.php",
            dataType: "JSON",
            data: { action: "get_db_campaign_copy"

					
                    },
            success: function(result){
				
				//console.log(result)
				
				$("#select-db-assoc-copy").empty();

				
				$.each(result.c_id, function(index, value){
				
				var selected = "";
								
				if(value == CampaignID){  
					$("#uniform-select-db-assoc-copy span").html(result.c_name[index]); 
					selected = "selected='selected'";
				}
				
				$("#select-db-assoc-copy").append("<option "+selected+" value='"+result.c_id[index]+"'>"+result.c_name[index]+"</option>");
				
				})
				
				
            }
    })
	
	
	
	
		
    }   
});


function ResetLeadsDialog()
{
	
	ActiveDB = "";
	ConvertedFile = "";
	
	$(".reset-loader").attr("src", "/images/icons/loader-arrows.gif")
	
	$("#div-field-match-result").hide();
	$("#div-field-match").hide();
	$("#div-upload-progress").hide();
	$("#div-file-upload").show();
	
	$("#fileToUpload").val("");
	
	$("#btn-load-leads").removeAttr("disabled");
	$("#btn-load-leads").removeClass("ui-state-disabled");
	$("#btn-load-leads").removeClass("ui-state-hover");
	
	$("#btn-continue").removeAttr("disabled");
	$("#btn-continue").removeClass("ui-state-disabled");
	$("#btn-continue").removeClass("ui-state-hover");
	
	
}


$("#btn-db-reset").button().click(function(){
	
	
	$.ajax({
            type: "POST",
            url: "requests.php",
            dataType: "JSON",
            data: { action: "reset_db",
                    sent_db_id: ActiveDB_edit,
                    },
            success: function(result){
                 oTable_db_status.fnReloadAjax();
            }
    })
	
	
	
	
	
	
	
	
 oTable_db_status.fnReloadAjax();	

});


$("#btn-view-dial-statuses").button();

$("#btn-load-leads").button();
$("#btn-continue").button().click(function(){
	
	//console.log(ActiveDB);
	//console.log(ConvertedFile);
	
	
	 $.ajax({
            type: "POST",
            url: "requests.php",
            dataType: "JSON",
            data: { action: "get_match_fields",
                    sent_campaign_id: CampaignID,
                    sent_db_id: ActiveDB,
                    sent_file_id: ConvertedFile
                    },
            success: function(result){
                var html_select;
				$("#table-field-match").empty();
                $.each(result.name, function(index, value){
                    html_select = "<select class='select-field-match' id='"+value+"'><option value='-1'>---</option>"
                    
					$.each(result.headers, function(index1, value1){
                        html_select += "<option value='"+index1+"'>"+value1+"</option>";
                    })
                    html_select += "</select>";
                    
					
					if( ((index/2) % 1) != 0 )
					{
						$("#table-field-match").append("<tr style='height:28px'><td width=50%>"+ result.display_name[index] +"</td><td width=50% style='text-align:right; padding-right:6px'>"+html_select+"</td></tr>")
					}
					else
					{
						$("#table-field-match").append("<tr style='height:28px; background-color:#E2E4FF'><td width=50%>"+ result.display_name[index] +"</td><td width=50% style='text-align:right; padding-right:6px'>"+html_select+"</td></tr>")
					}
					
    
                })    
  
            }
    })
	$("#div-file-upload").hide();
	$("#div-field-match").show();
	
	});
$("#btn-conclude").button().click(function(){

    
    var field_match_ids = new Array();
    var field_match_headers = new Array();

    $.each($('.select-field-match option:selected '), function(index, value){
        
        field_match_ids.push($(this).parent().attr("id"));
        field_match_headers.push($(this).val())
      
        
    }) 
	
	var control_flag = false;
	$.each(field_match_headers, function(index, value){
		if(value != "-1"){control_flag = true;}
		})


	if(control_flag)
	{
		$("#div-field-match").hide();
		$("#div-field-match-result").show();
		$("#btn-final").addClass("ui-state-disabled");
		$("#btn-final").attr("disabled", "disabled");
		
		$.ajax({
            type: "POST",
            url: "requests.php",
            dataType: "JSON",
            data: { action: "submit_match_fields",
                    sent_campaign_id: CampaignID,
                    sent_list_id: ActiveDB,
                    sent_file_id: ConvertedFile,
                    sent_match_ids: field_match_ids,
                    sent_match_headers: field_match_headers
                    },
            success: function(result){ 
			$("#td-error-log-total").html(result.linecount);
			$("#td-error-log-success").html(result.success);
			$("#td-error-log-error").html(result.errors);
			
			
			
			$("#field-match-progress-loader").html("<img class='reset-loader' style='margin-top:3px; float:right;' src='/images/icons/mono_checkmark_16.png'>");
			$("#btn-final").removeClass("ui-state-disabled");
			$("#btn-final").removeAttr("disabled");
			
			

            }
    	})
	}

 	
});

$("#btn-final").button().click(function(){
	ResetLeadsDialog();
	})


// NEW DB
$("#add-new-database").click(function(){
    $.ajax({
            type: "POST",
            url: "requests.php",
			dataType: "JSON",
            data: { action: "add_new_database",
                    sent_campaign_id: CampaignID,
                    sent_database_name: $("#add-database-name").val()
                    },
            success: function(data)  { 
            	$("#no-dbs").html("");
                $("#database-list").append("<li id='"+data.result+"' class=''>\n\
												<table>\n\
													<tr>\n\
													<td style='width:16px'>\n\
														<input name='i-"+data.result+"' id='i-"+data.result+"' type='checkbox' checked='checked'>\n\
													</td>\n\
													<td>\n\
														<label class='' style='display:inline;' for='i-"+data.result+"'>"+$("#add-database-name").val()+"</label>\n\
													</td>\n\
													<td width='24px'>\n\
														<img class='mono-icon cursor-pointer load-leads' diff='"+data.result+"' src='/images/icons/mono_icon_database_16.png'>\n\
													</td>\n\
													<td width='24px'>\n\
														<img class='mono-icon cursor-pointer edit-dbs' diff='"+data.result+"' src='/images/icons/mono_wrench_16.png'>\n\
													</td>\n\
													</tr>\n\
												</table>\n\
											</li>"); 
											
                $("#add-database-name").val("");
                $("input[name=i-" +data.result+ "]").uniform();
                }
            }); 
})

// DIALOG LOAD LEADS

$(".load-leads").live("click", function(){
		ActiveDB = $(this).attr("diff");
    $("#dialog-load-leads").dialog("open");

})

$("#dialog-load-leads").dialog({ 
    title: ' <span style="font-size:13px; color:black">Carregar Novos Contactos</span> ',
    autoOpen: false,
    height: 700,
    width: 600,
    resizable: false,
    buttons: { "Fechar" : function() { $(this).dialog("close"); ResetLeadsDialog();   } },
    open: function(){ 

    }   
});

// LOADER


function UploadFile() {
	
	
	if($("#fileToUpload").val() == '' || $("#fileToUpload").val() == null ) { return false; } 
		
	$("#div-upload-progress").show();
	$("#btn-load-leads").addClass("ui-state-disabled");
	$("#btn-load-leads").attr("disabled", "disabled");
	$("#btn-continue").addClass("ui-state-disabled");
	$("#btn-continue").attr("disabled", "disabled");
	
	
    var FileData = new FormData();
    FileData.append("fileToUpload", document.getElementById('fileToUpload').files[0]);
    var xhr = new XMLHttpRequest();
    xhr.upload.addEventListener("progress", uploadProgress, false);
    xhr.addEventListener("load", uploadComplete, false);
    xhr.addEventListener("error", uploadFailed, false);
    xhr.addEventListener("abort", uploadCanceled, false); 
    xhr.open("POST", "upload.php");
    xhr.send(FileData);
}

function uploadProgress(evt) {
/*	alert("loading");
	if (evt.lengthComputable) {
		var percentComplete = Math.round(evt.loaded * 100 / evt.total);
		if(percentComplete > 80){
			alert("Loaded");
			$("#file-loader-progress-loader").html("<img style='margin-top:3px' src='/images/icons/mono_checkmark_16.png'>");
		}
	} */
} 

function uploadComplete(evt) {
ConvertedFile = evt.target.responseText;
	$("#file-loader-progress-loader").html("<img class='reset-loader' style='margin-top:3px; float:right;' src='/images/icons/mono_checkmark_16.png'>");
	$("#btn-continue").removeClass("ui-state-disabled");
	$("#btn-continue").removeAttr("disabled");
}

function uploadFailed(evt) {
alert("There was an error attempting to upload the file.");
}

function uploadCanceled(evt) {
alert("The upload has been canceled by the user or the browser dropped the connection.");
}




</script>


</body>
</html>