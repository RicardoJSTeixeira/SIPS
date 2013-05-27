<?php #HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
require(ROOT."ini/header.php");
require("../../ini/functions.php");
ini_set("display_errors", "1");
?>

<?
// PHP ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// ALLOWED CAMPAIGNS
$query1 = mysql_fetch_assoc(mysql_query("SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]';", $link)) or die(mysql_error());
$query2 = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$query1[user_group]';", $link)) or die(mysql_error());
$AllowedCampaigns = "'" . preg_replace("/ /","','" , preg_replace("/ -/",'',$query2['allowed_campaigns'])) . "'";
// NEW CAMPAIGN ID
    $query = mysql_fetch_row(mysql_query("SELECT count(*) FROM vicidial_campaigns",$link));
    $num_campaigns = ($query[0]+1);
    while(strlen($num_campaigns) < 5) { $num_campaigns = "0".$num_campaigns; }
    $campaign_id = "C".$num_campaigns;

// OPTIONS
	$query = "SELECT user_group, group_name FROM vicidial_user_groups";
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

// PAUSES
	
				$query = "SELECT distinct pause_code, pause_code_name, max_time, active FROM vicidial_pause_codes WHERE campaign_id IN ($AllowedCampaigns) GROUP BY pause_code_name";
				$query = mysql_query($query,$link) or die(mysql_error());
				
				if(!mysql_num_rows($query))
				{
					$pauses .= "<span id='temp-nopauses'>Não existem Códigos de Pausa definidos.</span>";
				} 
				else 
				{ 
					for ($i=0;$i<mysql_num_rows($query);$i++)
					{
						$row = mysql_fetch_assoc($query);
						
						$row['max_time'] = $row['max_time'] / 60;       
						
						$pauses .= "<tr>";
						
						$pauses .= "<td>";
						$pauses .= "<input type='checkbox' value='".$row['pause_code']."' name='".$row['pause_code']."' id='".$row['pause_code']."' >";
						$pauses .= "<label style='display:inline;' for='".$row['pause_code']."'>".$row['pause_code_name']."</label>";
						$pauses .= "</td>";
						
						$pauses .= "<td style='text-align:right; padding-right:3px;'>";
						$pauses .= "<span><span id='span-pause-time-".$row['pause_code']."'>".$row['max_time']."</span></span> minutos";
						$pauses .= "</td>";
						
						$pauses .= "<td class='td-icon'>";
						$pauses .= "<img id='img-pause-time-".$row['pause_code']."' class='mono-icon edit-pause-time' style='cursor:pointer' src='/images/icons/mono_cup_16.png'>";
						$pauses .= "</td>";
						
						$pauses .= "</tr>"; 
						 
						
					}
				}
// FEEDBACKS
    $query="SELECT distinct status, status_name, human_answered, scheduled_callback FROM vicidial_campaign_statuses WHERE campaign_id IN ($AllowedCampaigns) GROUP BY status_name";
    $query = mysql_query($query,$link) or die(mysql_error());
    $half = round((mysql_num_rows($query)/2),0);
    for ($i=0;$i<mysql_num_rows($query);$i++)
    {
        $row = mysql_fetch_assoc($query);
        
        if($row['human_answered']=="Y"){ $human_answered = "<img class='mono-icon' src='/images/icons/mono_speech_16.png'>"; } else { $human_answered = "<img class='mono-icon' style='opacity:0.2' src='/images/icons/mono_speech_16.png'>"; }
        if($row['scheduled_callback']=="Y"){ $scheduled_callback = "<img class='mono-icon'  src='/images/icons/mono_phone_16.png'>"; } else { $scheduled_callback = "<img class='mono-icon' style='opacity:0.2'  src='/images/icons/mono_phone_16.png'>"; }
        
        if($i>=$half){
            $feeds2 .= "<tr class='tr-icon'>
							<td class='hover-callback' width='16px'>$scheduled_callback</td>
							<td class='hover-ishuman' width='16px'>$human_answered</td>
							<td><input type='checkbox' value='$row[status]' id='$row[status]'><label style='display:inline;' for='$row[status]'> $row[status_name]</label></td>
						</tr>";   
        } else {
            $feeds1 .= "<tr class='tr-icon'>
							<td class='hover-callback' width='16px'>$scheduled_callback</td>
							<td class='hover-ishuman' width='16px'>$human_answered</td>
							<td><input type='checkbox' value='$row[status]' id='$row[status]'><label style='display:inline;' for='$row[status]'> $row[status_name]</label></td>
						</tr>";
        }
    }
    if(!mysql_num_rows($query)){
        $status_options .= "<br>Não existem Feedbacks definidos.";
    }
// SCRIPTS
    $query = "SELECT DISTINCT(campaign_id) FROM vicidial_lists_fields WHERE campaign_id IN ($AllowedCampaigns)";
    $query = mysql_query($query) or die(mysql_error());
    
    $camps_IN = Query2IN($query, 0);
    
    $query = "SELECT campaign_name,campaign_id FROM vicidial_campaigns WHERE campaign_id IN($camps_IN)";
    $query = mysql_query($query);
    $half = round((mysql_num_rows($query)/2),0);
    for ($i=0;$i<mysql_num_rows($query);$i++)
    {
        $row=mysql_fetch_assoc($query);
        if($i>=$half){
            $scripts2 .= "<input type='radio' name='radio_scripts' id='$row[campaign_id]'><label style='display:inline;' for='$row[campaign_id]'> $row[campaign_name]</label>"; 
        } else {
            $scripts1 .= "<input type='radio' name='radio_scripts' id='$row[campaign_id]'><label style='display:inline;' for='$row[campaign_id]'> $row[campaign_name]</label>"; 
        }
    }
// CAMPOS DINAMICOS
    $query = mysql_query("SELECT Name, Display_name, readonly, active, field_order FROM vicidial_list_ref WHERE campaign_id='$campaign_id'");
    
        
        
    // TEMPLATE DEFAULT
    
    $default_fields = array("phone_number", "alt_phone", "address3", "first_name", "address1", "postal_code", "email", "comments");
    $default_labels = array("Telefone", "Telefone Alternativo #1", "Telefone Alternativo #2", "Nome ou Empresa", "Morada", "Código Postal", "E-mail", "Observações");
    
    for($i=0; $i<count($default_fields); $i++)
    {   
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
                        /* <input id='i-$default_fields[$i]' name='i-$default_fields[$i]' type='checkbox' checked='checked' $option_disabled > */  
    }

// BASES DE DADOS 
    $query = mysql_query("SELECT list_id, list_name FROM vicidial_lists WHERE campaign_id='$campaign_id'") or die(mysql_query);
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
        
    
        
        
          }
// RECICLAGEM
    $query = mysql_query("SELECT * FROM vicidial_lead_recycle WHERE campaign_id='$campaign_id'", $link) or die(mysql_query);
    $default_recycle = array("NA","B","DROP");
    $default_recycle_names = array("Ninguém Atendeu","Ocupado", "Sem Operador Disponível");
    
    if(mysql_num_rows($query)>0){} else {
        
        for($i=0;$i<count($default_recycle);$i++)
        {
            $recycle .= "<tr id='$default_recycle[$i]'>
                            <td>
                                <input type='checkbox' checked='checked' name='$default_recycle[$i]' id='$default_recycle[$i]' >
                                <label style='display:inline;' for='$default_recycle[$i]'>$default_recycle_names[$i]</label>
                            </td>
                            <td class='td-icon'>
                                <img id='edit-recycle-time' class='mono-icon cursor-pointer' src='/images/icons/mono_stopwatch_16.png'>
                            </td>
                            <td class='td-recycle-time' style='width:48px; padding-left:2px'>15m</td>
                            <td class='td-icon'>
                                <img id='edit-recycle-tries' class='mono-icon  cursor-pointer' src='/images/icons/mono_reload_16.png'>
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
    $query = mysql_query("SELECT status, status_name FROM vicidial_campaign_statuses WHERE campaign_id='$campaign_id' AND scheduled_callback <> 'Y' GROUP BY status", $link) or die(mysql_error());
    
    while ($row = mysql_fetch_assoc($query))
    {
        $recycle_avail_feedbacks .= "<option value='$row[status]'>$row[status_name]</option>";
    }       
    
        
// END PHP /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////    
?>

<!-- TABS -->

<br>
<div style='width:90%; height:475px; margin:0 auto; border:0'>
    <div id="wizard-tabs"> 
        <ul> 
            <li id="li-opcoes"><a href="#tab1">Opções Gerais</a></li> 
            <li id="li-pausas"><a href="#tab2">Pausas</a></li>
            <li id="li-feeds"><a href="#tab3">Feedbacks</a></li>
            <li id="li-recycle"><a href="#tab8">Reciclagem</a></li> 
            <li id="li-scripts"><a href="#tab4">Scripts</a></li> 
            <li id="li-dfields"><a href="#tab5">Campos Dinâmicos</a></li>
            <li id="li-dbs"><a href="#tab6">Bases de Dados</a></li>  
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
                <span style="font-size:10px; margin:0px 0px 0px 0px">(max. 30 caracteres)</span>
                </td>
                </tr>
                </table>
                
                <table>
                <tr>
                <td valign="bottom" style="width:100px">
                <label class="input-text-label" for="campaign-description">Descrição da Campanha</label><textarea style="margin-bottom:-3px" maxlength="201" class="input-textarea" id="campaign-description"></textarea>
                </td>
                <td valign="bottom">
                <span style="font-size:10px; margin:0px 0px 5px 0px">(max. 200 caracteres)</span>
                </td>
                </tr>
                </table>    
                    

                    <div class="div-title">Grupos com Acesso à Campanha</div>
                    <div style="overflow-y:auto; height:170px">
                    <table id="table-groups">
                        <tr>
                            <td width="50%" valign="top"><table id="table-groups-1"><?=$groups1?></table></td>
                            <td width="50%" valign="top"><table id="table-groups-2"><?=$groups2?></table></td>             
                        </tr>
                    </table>
                </div>
                <div style='margin-top:8px'>
                <button id='all-groups'>Todos</button><button id='no-groups' style='float:right'>Nenhum</button>
                </div>  
                </td>
    
    
    
                <td class="td-half-right">
                    <div class="div-title">Opções Gerais</div>
                    <table>
                        <tr class="tr-options">
                            <td>Activa</td>
                            <td class="tright">
                            <input type="radio" id="campaign_active_yes" name="campaign_active" checked="checked" /><label for="campaign_active_yes">Sim</label>
                            <input type="radio" id="campaign_active_no" name="campaign_active" /><label for="campaign_active_no">Não</label>
                            </td>
                        </tr>
                        <tr class="tr-options">
                            <td>Tipo de Campanha</td>
                            <td class="tright">
                            <input type="radio" id="campaign_type_auto" name="campaign_type" checked="checked" /><label for="campaign_type_auto">Automática</label>
                            <input type="radio" id="campaign_type_manual" name="campaign_type" /><label for="campaign_type_manual">Manual</label>
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
                            <input type="radio" id="campaign_recording_yes" name="campaign_recording" checked="checked" /><label for="campaign_recording_yes">Sim</label>
                            <input type="radio" id="campaign_recording_no" name="campaign_recording" /><label for="campaign_recording_no">Não</label>
                            </td>
                        </tr>
                        <tr class="tr-options">
                            <td>Ordem das Chamadas</td>
                            <td class="tright">
                            <input type="radio" id="campaign_lead_order_random" name="campaign_lead_order" checked="checked" /><label for="campaign_lead_order_random">Aleatória</label>
                            <input type="radio" id="campaign_lead_order_orderer" name="campaign_lead_order" /><label for="campaign_lead_order_orderer">Ordenada</label>
                            </td>
                        </tr>
                        <tr class="tr-options">
                            <td>Atribuição de Chamadas</td>
                            <td class="tright">
                            <input class="text-spinner" style="width:160px;" id="campaign_atrib_calls" name="campaign_atrib_calls" value="Aleatória">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr style="height:0px"></tr>
            <tr class="tr-next-button">
            <td></td>
                <td>
                    <span class="error-msg" >
                    	<img style="float:left" src="/images/icons/mono_burst_16.png" />
                    	<span style="margin:0px 0px 0px 6px" id="error-text"></span>
                    </span>
                    <button class="next-button">Gravar</button>
                    </td>
            </tr>
            </table>
            </div>
            
             
        <!-- PAUSAS -->
    	<div id="tab2">
        	<table class="table-100">
       		<tr>
            	<td class="td-half-left">
                	<div class="div-title">Pausas Disponíveis</div>
	                <table id="table-pauses">
                	<?=$pauses?>
	                </table> 
	            </td>
    	        <td class="td-half-right">
        	        <div class="div-title">Adicionar Nova Pausa</div>    
                	<table>
                    <tr>
                        <td>
                            <input class="input-text" style='width:90%' type="text" id="pause-name">
                        </td>
                        <td style="text-align:right; padding-right:3px">
                        	<input id='spinner-pause-time' style="width:22px;" value="0" />
                        </td>
                        <td>
                        	minutos
                        </td>
                        <td class="td-icon">
                            <img id="new_pause" class="img-icon" src="/images/icons/clock_add_32.png">
                        </td>
                    </tr>

                </table>
                <div class="div-title">Legenda</div>
                <table>
                    <tr>
                        <td class="td-icon"><img class='mono-icon'  src='/images/icons/mono_cup_16.png'></td><td>Editar Pausa</td>                
                    </tr>


                </table>
              	<br />
                <div class="div-title">Informação</div>
                <table>
                    <tr>
                        <td style="text-justify: distribute">
                        	Se editar pausas já existentes, quando Gravar, vai actualizar todas as pausas com o mesmo nome em todas as campanhas. Se precisa de Pausas com nomes ou tempos especificos sugerimos que crie pausas novas. </i>
  						</td>                
                    </tr>
	            </table>
     
    
            </td>
        </tr>
        <tr class="tr-next-button"><td colspan="2"><button class="next-button">Gravar</button></td></td></tr>
        </table>    
        </div>  
        <!-- FEEDBACKS -->
   		<div id="tab3">
        <table class="table-100">
        <tr>
            <td class="td-half-left">
                
                <div class="div-title">Feedbacks Disponíveis</div>
                <div style='overflow-y:auto; height:329px'>
                <table >
                    <tr>
                        <td width="50%" valign="top"><table id="table-feeds-1"><? echo $feeds1; ?></table></td>
                        <td width="50%" valign="top"><table id="table-feeds-2"><? echo $feeds2; ?></table></td>             
                    </tr>
                    

                </table>
                </div>
                <br>
                
            </td>
            <td class="td-half-right">
                <div class="div-title">Adicionar Novo Feedback</div>
                
                <table >
                    <tr>
                        <td>
                            <input id="feed-name" class="input-text" style='width:95%' type="text" >
                        </td>
                        <td width=10px></td>
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_speech_16.png'></td>
                        <td><input id="is_human" checked="checked" type="checkbox"></td>
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_phone_16.png'></td>
                        <td><input id="is_callback" type="checkbox"></td>
                        <td class="td-icon">
                            <img id="new-feed" class="img-icon" src="/images/icons/note_add_32.png">
                        </td>
                    </tr>

                </table>
                <div class="div-title">Legenda</div>
                <table>
                    <tr>
                        <td class="td-icon"><img class='mono-icon'  src='/images/icons/mono_speech_16.png'></td><td>Resposta Humana</td>                
                    </tr>
                    <tr>
                        <td class="td-icon"><img class='mono-icon'  src='/images/icons/mono_phone_16.png'></td><td>Callback</td>
                    </tr>
                    

                </table>
            </td>
        </tr>       
        <tr class="tr-next-button"><td colspan="2"><button class="next-button">Gravar</button></td></tr>
        </table>        
        </div>  
        <!-- SCRIPTS -->
        <div id="tab4">
        <table class="table-100">
        <tr>
            <td class="td-half-left">
                
                <div class="div-title">Scripts Disponiveis</div>
                <table id="table-scripts">
                    <tr>
                        <td width="50%" valign="top"><? echo $scripts1; ?></td>
                        <td width="50%" valign="top"><? echo $scripts2; ?></td>             
                    </tr>
                    

                </table>
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
            <div class="div-title">Informação do Script</div>   
                        <table>
                <tr>
                    <td>
                    Associado às Campanhas
                    </td>
                </tr>
                <tr>
                    <td>
                    Nº de Campos
                    </td>
                </tr>
                <tr style="height:16px"></tr>
            </table>
            </td>
        </tr>       
        <tr class="tr-next-button"><td colspan="2"><button class="next-button">Gravar</button></td></tr>
        </table>    
        </div> 
        <!-- CAMPOS DINAMICOS -->
   		<div id="tab5">
                    
        
            
            
        <table class="table-100">
        <tr>
            <td class="td-half-left">
                
                <div class="div-title">Campos Dinâmicos</div>
                
                
                <ul id="dfields-sortable">
                <? echo $dinamic_fields; ?> 
                </ul>
                
                
                
                
                
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
        <div id="tab6">
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
                            <img id="new-database" class="img-icon" src="/images/icons/database_add_32.png">
                        </td>
                    </tr>

                </table>
                
                <div class="div-title">Legenda</div>
                <table>
                     <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_icon_database_16.png'></td><td>Carregar Contactos</td>               
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
        

        <!-- RECICLAGEM -->
 		<div id="tab8">

        <table class="table-100">
        <tr>
            <td class="td-half-left">
                
                <div class="div-title">Feedbacks em Reciclagem</div>
                
                 
                <table id="table-recycle">
                <? echo $recycle; ?>
                </table> 
                
                
            </td>
            <td class="td-half-right">
                <div class="div-title">Adicionar Feedback em Reciclagem</div>
                
                <table id='table-new-recycle'>
                    <tr>
                        <td>
                            <select id="select-new-recycle" style='width:90%;'><option value="---">---</option><? echo $recycle_avail_feedbacks; ?></select>
                        </td>
                        
                        <td class='td-icon'><img class='mono-icon' src='/images/icons/mono_stopwatch_16.png'></td>
                        <td style="width:0px">
                        <input id='spinner-recycle-time' style="width:22px;" value="0" />
                        </td>
                        <td style='padding-right:12px; padding-left:1px;'>
                        minutos
                        </td>
                        <td class='td-icon'><img  class='mono-icon' src='/images/icons/mono_reload_16.png'></td>
                        <td style="width:0px;">
                        <input id='spinner-recycle-tries' style="width:16px;" value="0" />
                        </td>
                        <td style='padding-right:12px; padding-left:1px;'>
                        tentativas
                        </td>
                        <td class="td-icon">
                            <img id="new-recycle" class="img-icon" src="/images/icons/calendar_add_32.png">
                        </td>
                        
                        
                    </tr>

                </table>
                <div class="div-title">Legenda</div>
                <table>
                     <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_stopwatch_16.png'></td><td>Intervalo de Reciclagem</td>              
                    </tr>
                    <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_reload_16.png'></td><td>Número de Tentativas</td>                
                    </tr>
                    <tr class="height24">
                        <td class="td-icon"><img class='mono-icon' src='/images/icons/mono_attention_16.png'></td><td>Contactos no Limite de Tentativas</td>                
                    </tr>                   


                    <tr><td id="dumper"></td></tr>
                    

                </table>
            </td>
        </tr>
        <tr class="tr-next-button"><td colspan="2"><button class="next-button">Gravar</button></td></td></tr>
        </table>    
 
        </div> 
    	<!-- DIALOGS -->
			
    <!-- Editar pausa -->	    
    <div id="dialog-edit-pause-time" style="display:none">
    <label class="input-text-label" for="edit-pause-name">Nome da Pausa</label><input style='width:194px' class="input-text" type="text" id="edit-pause-name">
    <div class="div-title">Tempo de Pausa</div>
    <table>
        <tr>
            <td>
                Em Minutos
            </td>
            <td style='text-align:right'>
                <input id="spinner-new-pause-time" style="width:24px;" type="text"  value =""/>
            </td>
        </tr>
    </table>
     </div> 
    
    <div id="dialog-edit-field-name" style="display:none">
    <div class="div-title">Novo Nome para o Campo</div>
    <input  id="new-field-name" type="text" value=""/>
    </div>
    
    <div id="dialog-load-leads" style="display:none">

    <div id='div-file-upload'>
    <div class="div-title">Escolha do Ficheiro</div>
    <form enctype="multipart/form-data" method="POST" action="_upload.php">
    <table>
    <tr>
        <td><input type="file" name="fileToUpload" id="fileToUpload" /><br /></td>
        <td><input type="button" onclick="UploadFile()" value="Carregar" /></td>
    </tr>
    </table>
    <div id="progressNumber"></div>
    <br /><br /><br />
    
    <table  id="table-file-loader-progress">
    <tr style="height:28px">
    <td id="file-loader-progress-msg">A carregar o ficheiro com os contactos...</td> <td style="width:50px" id="file-loader-progress-loader"><img style='margin-top:3px' src='/images/icons/loader-arrows.gif'></td>
    </tr>
    </table>
    <br />
    <table id="table-file-converter-progress">
    <tr style="height:28px">
    <td id="file-converter-progress-msg">A converter o ficheiro de contactos...</td> <td style="width:50px" id="file-converter-progress-loader"><img style='margin-top:3px' src='/images/icons/loader-arrows.gif'></td>
    </tr>
    </table>

    
    <center><div><input id="continue-button" type="button" value="Continuar" /></div></center>
    </form>
    </div>


    <div id="div-field-match">
    <div class="div-title">Associação de Campos</div>
    <table id="table-field-match">
    </table>
    <table>
    <tr>
    <td>
    <input type="button" id="submit-field-match" value="Concluir"  />
    </td>
    <td id="file-insert-progress-msg" style="text-align:right;"></td>
    <td id="file-insert-progress-loader" style="width:16px">
    
    </td>
    </tr>
    </table>
    
    </div>

    <div id="dialog-edit-recycle-time" style="display:none">
    <div class="div-title">Novo Tempo de Reciclagem</div>
    <input id="spinner-edit-recycle-time" style="width:24px;" type="text"  value =""/>
    </div> 
    
    <div id="dialog-edit-recycle-tries" style="display:none">
    <div class="div-title">Nº de Tentativas de Reciclagem</div>
    <input id="spinner-edit-recycle-tries" style="width:24px;" type="text"  value =""/>
    </div> 
    
    <div id="dialog-new-campaign-name" style="display:none">
    <div class="div-title">Nome da Campanha</div>
    <input id="new-campaign-name-start" style="width:150px;" type="text"  value=""/>
    </div> 
    
    <div id="all-done-confirmation" style="display:none">
    <br />
    <br />
    Campanha Criada com sucesso!

    </div> 
    


</div> 

</div>
</div>

<script>
var campaign_id = '<? echo $campaign_id; ?>'

// document ready

$(function(){
    $("input[type=checkbox], input[type=radio], select ").uniform();
    $("input[type=file]").uniform({"size": "400"});
    $( "#wizard-tabs" ).tabs({ heightStyle: "fill"}).show(); 
    //$('#wizard-tabs').tabs("option", "disabled", [1, 2, 3, 4, 5, 6]); 
    $( "#wizard-tabs" ).tabs("refresh");
    })



// TAB NAVIGATION
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
		/*case "li-pausas":{ var submitted = submit_Pausas(); console.log("passou pausas"); break;  }
		case "li-feeds" :{ var submitted = submit_Feedbacks(); break; }
		case "li-scripts" :{ var submitted = submit_Scripts(); break; }
		case "li-dfields" :{ var submitted = submit_DFields(); break; }
		case "li-recycle" :{ var submitted = submit_Reciclagem(); break; } */
	}

})
// OPCOES GERAIS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var OpcoesGeraisCheck = true;

function submit_OpcoesGerais(){
	
	if( !OpcoesGeraisCheck ){ return false;  } else {

		var groups = new Array();
		
		$("#table-groups tr td div span").each(function()
		{
			//console.log($(this).children().attr("id"));
			if($(this).hasClass("checked"))
			{
				groups.push($(this).children().attr("id"));
			} 
    	});
		
		var campaign_name = $("#campaign-name").val();
		var campaign_description = $("#campaign-description").val();
	
		if($("#uniform-campaign_active_yes span").hasClass("checked")){ var campaign_active = "Y"; } else { var campaign_active = "N"; }
		if($("#uniform-campaign_type_auto span").hasClass("checked")){ var campaign_type = "RATIO"; } else { var campaign_type = "MANUAL"; }
		var campaign_ratio = $("#ratio-spinner").val();
		if($("#uniform-campaign_recording_yes span").hasClass("checked")){ var campaign_recording = "ALLFORCE"; } else { var campaign_recording = "NEVER"; }
		if($("#uniform-campaign_lead_order_random span").hasClass("checked")){ var campaign_lead_order = "RANDOM"; } else { var campaign_lead_order = "DOWN"; }
		
		switch($("#campaign_atrib_calls").attr("aria-valuenow")){
			case "1": { var campaign_next_call = "longest_wait_time"; break; }
			case "2": { var campaign_next_call = "random"; break; }
			case "3": { var campaign_next_call = "fewest_calls"; break; }
		}
		   
		$.ajax({
			type: "POST",
			url: "requests.php",
			data: { action: "submit_opcoesgerais",
					sent_campaign_id: campaign_id,
					sent_campaign_name: campaign_name, 
					sent_campaign_description: campaign_description, 
					sent_campaign_active: campaign_active, 
					sent_campaign_type: campaign_type, 
					sent_campaign_ratio: campaign_ratio,
					sent_campaign_recording: campaign_recording, 
					sent_campaign_lead_order: campaign_lead_order,
					sent_campaign_next_call: campaign_next_call,
					sent_campaign_allowed_groups: groups},
			success: function(data) {}
	
		});                
		return true; 
	}    
}




$("#campaign-name").bind("input", function()
{
	if($(this).val().length > 30)
	{
		$(".error-msg").show();
		$("#error-text").html("Atingido o número máximo de caracteres para o Nome da Campanha.");
		OpcoesGeraisCheck = false;
	}
	else
	{
		$(".error-msg").hide();
		OpcoesGeraisCheck = true;
	}
})

$("#campaign-description").bind("input", function()
{
	if($(this).val().length > 200)
	{
		$(".error-msg").show();
		$("#error-text").html("Atingido o número máximo de caracteres para a Descrição da Campanha.");
		OpcoesGeraisCheck = false;
	}
	else
	{
		$(".error-msg").hide();
		OpcoesGeraisCheck = true;
	}
})

$(" #all-groups ").click(function()
{
	$.each($( ".groups-checkbox" ).parent(), function(index, value)
	{
		$(this).addClass("checked");
	})
})

$( "#no-groups" ).click(function()
{
	$.each($( ".groups-checkbox" ).parent(), function(index, value)
	{
		$(this).removeClass("checked");
	})
})


/* $('*').click(function(e){
    console.log(e.target);
}); */


$("#uniform-campaign_type_auto").live("click", function(){
	$( "#ratio-spinner" ).spinner( "enable" );
	$( "#ratio-spinner" ).val( 1 );
	
})
$("#uniform-campaign_type_manual").live("click", function(){
	$( "#ratio-spinner" ).spinner( "disable" );
	$( "#ratio-spinner" ).val( 0 );
})

$("#ratio-spinner").spinner({
    min:1, 
    max:5
});

$.widget("ui.modspinner1", $.ui.spinner, {
    options: {
        min: 0,
        max: 4
    },
    _parse: function(value) {
        if (typeof value === "string") {
            switch(value){
                case "Maior Tempo em Espera" : return 1 ; 
                case "Aleatória" : return 2 ; 
                case "Menos Chamadas Recebidas": return 3;
            }
        }
        return value;
    },
    _format: function(value) {
        switch(value){
            case 1 : return "Maior Tempo em Espera"; 
            case 2 : return "Aleatória"; 
            case 3 : return "Menos Chamadas Recebidas"; 
        }
    }
});

$(function() {
    $("#campaign_atrib_calls").modspinner1({
        spin: function( event, ui ) {
            if ( ui.value > 3) {
                $( this ).modspinner1( "value", 1 );
                return false;
            } else if( ui.value < 1){
                $( this ).modspinner1( "value", 3 );
                return false;
            }
        }
    });
});

// PAUSAS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function submit_Pausas()
{
    pause_counter = 1;
    var pause_ids = new Array();
    var pause_names = new Array();
    var pause_active = new Array();
    var pause_time = new Array();  
    
    
    
    $("#table-pauses tr td div span").each(function(){
        pause_ids.push($(this).children().attr("id"));
        pause_names.push($("label[for="+$(this).children().attr("id")+"]").html());
        if($(this).hasClass("checked")){pause_active.push(1)} else {pause_active.push(0)}
    });
    
    $("#table-pauses tr td span span").each(function(){     pause_time.push($(this).html()) })
    
    
    $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "submit_pausas",
                    sent_campaign_id: campaign_id,
                    sent_pause_ids: pause_ids, 
                    sent_pause_names: pause_names,
                    sent_pause_active: pause_active,
                    sent_pause_time: pause_time
                    },
            success: function(data) {}
        });
		return true;
}

$('#spinner-pause-time').spinner({
    min: 0,
    max: 120,
    step: 5
    });
$('#spinner-new-pause-time').spinner({
    min: 0,
    max: 120,
    step: 5
    });

var pause_counter = 1;

$(" #new_pause ").click(function(){
    $.ajax({
            type: "POST",
            url: "requests.php",
            data: {action: "add_new_pause", counter: pause_counter},
            success: function(data) 
            {
                
                if($( "#temp-nopauses" ).length > 0){ $( "#temp-nopauses" ).html(""); }
                var pause_id = data;
                var pause_name = $("#pause-name").val();
                var pause_time = $('#spinner-pause-time').val();
                
                $( "#table-pauses" ).append("<tr><td><input type='checkbox' checked='checked' value='"+pause_id+"' name='"+pause_id+"' id='"+pause_id+"' ><label style='display:inline;' for='"+pause_id+"'>"+pause_name+"</label></td><td style='text-align:right; padding-right:3px;'><span><span id='span-pause-time-"+pause_id+"'>"+ pause_time +"</span></span> minutos</td><td class='td-icon'><img id='img-pause-time-"+pause_id+"' class='mono-icon edit-pause-time' style='cursor:pointer' src='/images/icons/mono_cup_16.png'></td></tr>")

    
                $("#pause-name").val("");
                $("input[name=" +pause_id+ "]").uniform();
                $( "#wizard-tabs" ).tabs( "refresh" ); 
                pause_counter++;
            }
    });

})

var pause_time_edit = "";
$('.edit-pause-time').live("click", function(){
    pause_time_edit = $(this).attr("id").split("-");
    pause_time_edit = pause_time_edit[3]
    $("#dialog-edit-pause-time").dialog( "open" )
    
})


$("#dialog-edit-pause-time").dialog({ 
    title: ' <span style="font-size:13px; color:black">Editar Pausa</span> ',
    autoOpen: false,
    height: 250,
    width: 230,
    resizable: false,
    buttons: { "Gravar" : function() { 
                    $(this).dialog("close"); 
                    $("#span-pause-time-"+pause_time_edit).html($("#spinner-new-pause-time").val());
					$("label[for='"+pause_time_edit+"']").html($("#edit-pause-name").val()); 
              /*      $.ajax({
                        type: "POST",
                        url: "requests.php",
                        data: {action: "edit_pause_time", sent_new_time: $("#spinner-new-pause-time").val(), sent_pause_id: pause_time_edit },
                        success: function(data) 
                        {
                        }
                }); */
                    
                    
                    }
            },
    open: function(){ 
            $("button").blur(); 
            $("#spinner-new-pause-time").val($("#span-pause-time-"+pause_time_edit).html())
			$("#edit-pause-name").val($("label[for='"+pause_time_edit+"']").html()); 
        }
}); 



// FEEDBACKS ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function submit_Feedbacks(){
    var feed_ids = new Array(), feed_names = new Array(), feed_callback = new Array(), feed_ishuman = new Array(), feed_active = new Array();
    
	feed_counter = 1; // reset feed counter
    
	$("#table-feeds-1 tr td div span").each(function()
	{
        feed_ids.push($(this).children().attr("id"));
        feed_names.push($("label[for="+$(this).children().attr("id")+"]").html());
        if($(this).hasClass("checked"))
		{
			feed_active.push("Y")
		} 
		else 
		{
			feed_active.push("N")
		}
    });
    
    $("#table-feeds-2 tr td div span").each(function()
	{
        feed_ids.push($(this).children().attr("id"));
        feed_names.push($("label[for="+$(this).children().attr("id")+"]").html());
        if($(this).hasClass("checked"))
		{
			feed_active.push("Y")
		} 
		else
		{
			feed_active.push("N")
		}
    });

    $('#table-feeds-1 tr td').each(function()
	{
        if($(this).hasClass("hover-callback"))
		{
            if($(this).children().css("opacity")=="0.2")
			{
            	feed_callback.push("N")
            } 
			else 
			{
            	feed_callback.push("Y")
            }
        }
        if($(this).hasClass("hover-ishuman"))
		{
            if($(this).children().css("opacity")=="0.2")
			{
            	feed_ishuman.push("N")
            } 
			else 
			{
            	feed_ishuman.push("Y")
            }   
        }   
	})
	
	$('#table-feeds-2 tr td').each(function()
	{
        if($(this).hasClass("hover-callback"))
		{
            if($(this).children().css("opacity")=="0.2")
			{
            	feed_callback.push("N")
            } 
			else 
			{
            	feed_callback.push("Y")
            }
        }
        if($(this).hasClass("hover-ishuman"))
		{
            if($(this).children().css("opacity")=="0.2")
			{
            	feed_ishuman.push("N")
            } 
			else 
			{
            	feed_ishuman.push("Y")
            }   
        }   
	})
    
/*	$('#table-feeds-2 tr td').each(function(){
        if($(this).hasClass("hover-callback")){
            if($(this).html()==""){
                    feed_callback.push("N")
                } else {
                    feed_callback.push("Y")
                    }
        }
        if($(this).hasClass("hover-ishuman")){
            if($(this).html()==""){
                    feed_ishuman.push("N")
                } else {
                    feed_ishuman.push("Y")
                    }   
        }   
        
    }) */
    
    
/*  alert(feed_ids);
    alert(feed_names);
    alert(feed_active);
    
    alert(feed_callback)
    alert(feed_ishuman); */
    
	//console.log(feed_callback);
	//console.log(feed_ishuman);
    
    $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "submit_feeds",
                    sent_campaign_id: campaign_id,
                    sent_feed_ids: feed_ids, 
                    sent_feed_names: feed_names,
                    sent_feed_active: feed_active,
                    sent_feed_callback: feed_callback,
                    sent_feed_ishuman: feed_ishuman
                    },
            success: function(data) {}
        });
    
    
}




var feed_counter = 1;
$(" #new-feed ").click(function(){
    $.ajax({
            type: "POST",
            url: "requests.php",
            data: {action: "add_new_feed", counter: feed_counter},
            success: function(data) 
            {
				var feed_id = data;
                var feed_name = $("#feed-name").val();
                
				/*if($( "#temp-nofeeds" ).length > 0)
				{ 
					$( "#td-feeds-1" ).html(""); 
				} */
                
                if($( "#uniform-is_human span" ).hasClass( "checked" ) ) 
				{ 
					var human_icon = "<img class='mono-icon'  src='/images/icons/mono_speech_16.png'>"; 
				} 
				else 
				{ 
					var human_icon = "<img class='mono-icon' style='opacity:0.2'  src='/images/icons/mono_speech_16.png'>"; 
				}  
                
				if($( "#uniform-is_callback span" ).hasClass( "checked" ) ) 
				{ 
					var callback_icon = "<img class='mono-icon'  src='/images/icons/mono_phone_16.png'>"; 
				} 
				else 
				{ 
					var callback_icon = "<img class='mono-icon' style='opacity:0.2'  src='/images/icons/mono_phone_16.png'>"; 
				}  
                
                if($( "#table-feeds-1 td" ).length > $( "#table-feeds-2 td" ).length)
                {
                    $( "#table-feeds-2" ).append("<tr class='tr-icon'><td class='hover-callback' width='16px'>"+callback_icon+"</td><td class='hover-ishuman' width='16px'>"+human_icon+"</td><td><input type='checkbox' name='"+feed_id+"' value='"+feed_id+"' id='"+feed_id+"'><label style='display:inline;' for='"+feed_id+"'>"+feed_name+"</label></td></tr>")
                } 
				else 
				{
                    $( "#table-feeds-1" ).append("<tr class='tr-icon'><td class='hover-callback' width='16px'>"+callback_icon+"</td><td class='hover-ishuman' width='16px'>"+human_icon+"</td><td><input type='checkbox' name='"+feed_id+"' value='"+feed_id+"' id='"+feed_id+"'><label style='display:inline;' for='"+feed_id+"'>"+feed_name+"</label></td></tr>")
                }
                 
                $("#feed-name").val("");
                //$( "#uniform-is_callback span" ).removeClass("checked");
                //$( "#uniform-is_human span" ).removeClass("checked");
                $("input[name=" +feed_id+ "]").uniform();
                $( "#wizard-tabs" ).tabs( "refresh" ); 
                feed_counter++;
            }
    });
    
})

var hover_flag = 0;
var icon_flag = 0;

$( ".hover-callback" ).live( "mouseenter mouseleave", function(event) 
{
	if (event.type == "mouseenter") 
	{
  		if($(this).children().css("opacity")=="0.2")
		{
			$(this).children().css("opacity", "0.75"); 
			hover_flag = 1; 
			icon_flag=0;
		} 
		else 
		{
			hover_flag = 0; 
			icon_flag = 1; 
		}                    
  	} 
	else 
	{
    	if(hover_flag==1)
		{
			$(this).children().css("opacity", "0.2"); 
		}
  	}
});

$( ".hover-ishuman" ).live( "mouseenter mouseleave", function(event) 
{
	if (event.type == "mouseenter") {
		//console.log($(this).children().css("opacity"));
  		if($(this).children().css("opacity")=="0.2")
		{
			$(this).children().css("opacity", "0.75"); 
			hover_flag = 1; 
			icon_flag=0;
		} 
		else 
		{
			hover_flag = 0; 
			icon_flag = 1; 
		}                    
  	} 
	else 
	{
    	if(hover_flag==1)
		{
			$(this).children().css("opacity", "0.2"); 
		}
  	}
});


$('.hover-callback').live("click", function(){ if(icon_flag==1){$(this).children().css("opacity", "0.2")} else {hover_flag=0;}  });
$('.hover-ishuman').live("click", function(){ if(icon_flag==1){$(this).children().css("opacity", "0.2")} else {hover_flag=0;} });

// ASSOCIAR SCRIPT /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function submit_Scripts(){
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
                    sent_campaign_id: campaign_id, 
                    sent_campaign_copy: camp_id 
                    },
            success: function(data) { }
            });
            //alert(camp_id); 
            //alert($("label[for="+camp_id+"]").html()); 
            $("#span-script").html("<table><tr><td style='width:16px'><img style='cursor:pointer' id='remove-script' class='mono-icon' src='/images/icons/mono_cancel_16.png'></td><td style='width:8px'></td><td>O Script <b>"+ camp_name +"</b> está associado à Campanha. </td></tr></table>") }
        
        //alert( $(this).attr("class") )
    })
})

$("#remove-script").live("click", function(){
    $("#span-script").html("Nenhum Script associado.");
    $("#table-scripts tr td div span").each(function(){
        $(this).removeClass("checked");
        
        $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "remove_script",
                    sent_campaign_id: campaign_id
                    },
            success: function(data) { }
            });
        
        
        
    })
})

// CAMPOS DINAMICOS ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function submit_DFields(){
    
var sortedIDs = $("#dfields-sortable").sortable("toArray");
var sortedLabels = new Array();
var sortedReadOnly = new Array();
//var sortedActive = new Array();
var sortedOrder = new Array();  

$.each(sortedIDs, function(index, value){
    sortedLabels.push($("label[for=i-"+value+"]").html())
    if($("#ro-"+value).attr("src")=="/images/icons/mono_document_16.png"){sortedReadOnly.push(0)} else {sortedReadOnly.push(1)};
    //if($("#uniform-i-"+value+" span").hasClass("checked")){sortedActive.push(1)} else {sortedActive.push(0)};
    sortedOrder.push(index);
})

    // VER FIELDS IN USE
    var array_in_use = new Array();
    $("#dfields-sortable li").each(function(){
        array_in_use.push($(this).attr("id"))
    })
    alert(array_in_use);    
    //VER OS QUE FALTAM
    var current_array_item = "";
    $.each(array_in_use, function(index, value){
        current_array_item = value;
        $.each(all_fields, function(index, value){ if(current_array_item == value){all_fields.splice(index, 1)}   })
        })
    
alert(all_fields);

/*alert(sortedIDs);
alert(sortedLabels);
alert(sortedReadOnly);
//alert(sortedActive);
alert(sortedOrder); */

$.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "submit_dfields",
                    sent_campaign_id : campaign_id,
                    sent_sortedIDs: sortedIDs,
                    sent_sortedLabels: sortedLabels,
                    sent_sortedReadOnly: sortedReadOnly,
                    //sent_sortedActive: sortedActive,
                    sent_fillers : all_fields,
                    sent_sortedOrder: sortedOrder
                    },
            success: function(data) { }
            });
}

var avail_fields = new Array("phone_number","title", "first_name", "middle_initial", "last_name", "address1", "address2", "address3", "city", "state", "province", "postal_code", "country_code", "date_of_birth", "email", "security_phrase", "comments", "extra1", "extra2", "extra3", "extra4", "extra5", "extra6", "extra7", "extra8", "extra9", "extra10", "extra11", "extra12", "extra13", "extra14", "extra15");
var all_fields = new Array('vendor_lead_code','phone_number','title','first_name','middle_initial','last_name','address1','address2','address3','city','state','province','postal_code','country_code','date_of_birth','alt_phone','email','security_phrase', 'owner','comments', 'extra1', 'extra2', 'extra3', 'extra4', 'extra5', 'extra6', 'extra7', 'extra8', 'extra9', 'extra10', 'extra11', 'extra12', 'extra13', 'extra14', 'extra15')

$(".remove-field").live("click", function(){
    $(this).parent().parent().parent().parent().parent().remove();
})

var edit_id = ""; 
$(".edit-field").live("click", function(){
    edit_id = $(this).parent().parent().parent().parent().parent().attr("id");  
    $("#dialog-edit-field-name").dialog("open");
})

$('#new-field').click(function(){
    var current_li = "";
    $("#dfields-sortable li").each(function(){
        current_li = $(this).attr("id");
        $.each(avail_fields, function(index, value){ if(current_li == value){ avail_fields.splice(index, 1) } })
        })  

    var new_field_id = avail_fields[0];
    var new_field_value = $('#add-field-name').val();
    var new_field_readonly = "";
    $(".rdonly-chooser div span").each(function(){

        if($(this).hasClass("checked") && $(this).children().attr("id")=="new-field-rdonly-no"){ new_field_readonly = "/images/icons/mono_document_empty_16.png"; return false; } else { new_field_readonly = "/images/icons/mono_document_16.png"; return false; }
        
        })
    $('#dfields-sortable').append("<li id='"+new_field_id+"' class='cursor-move'><table><tr><td width='16px'> <img class='mono-icon cursor-pointer remove-field' src='/images/icons/mono_cancel_16.png'> </td><td width='16px'> <img id='ro-"+new_field_id+"' class='mono-icon cursor-pointer icon-readonly' src='"+new_field_readonly+"'> </td><td><label class='cursor-move' style='display:inline; margin-left:3px' for='i-"+new_field_id+"'>"+new_field_value+"</label></td><td width='16px'><img class='mono-icon cursor-pointer edit-field' src='/images/icons/mono_document_edit_16.png'></td></tr></table></li>")   
    $("#add-field-name").val("");
    //$("input[name=i-" +new_field_id+ "]").uniform(); <input id='i-"+new_field_id+"' name='i-"+new_field_id+"' type='checkbox' checked='checked' >
    $("#wizard-tabs").tabs( "refresh" ); 
})

$('.icon-readonly').live("click", function(){
    if($(this).attr("src")=="/images/icons/mono_document_16.png"){$(this).attr("src", "/images/icons/mono_document_empty_16.png")} else { $(this).attr("src", "/images/icons/mono_document_16.png") }
    })

$("#dialog-edit-field-name").dialog({ 
    title: ' <span style="font-size:13px; color:black">Alteração do Nome do Campo</span> ',
    autoOpen: false,
    height: 300,
    width: 300,
    resizable: false,
    buttons: { "Gravar" : function() { $(this).dialog("close"); $("label[for=i-"+edit_id+"]").html($("#new-field-name").val())  } },
    open: function(){ $("button").blur(); $("#new-field-name").val($("label[for=i-"+edit_id+"]").html()) }
}); 

$("#dfields-sortable").sortable();



// BASES DE DADOS //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$("#add-database-name").focus(function(){$(this).css("border", "1px solid #c0c0c0")});
$("#new-database").click(function(){
    
    if($("#add-database-name").val()==""){ $("#add-database-name").css("border", "1px solid red"); return false; }
    
    var database_name = $("#add-database-name").val(); 
    var new_campaign = 0;
    
    
    if($("ul[id='database-list'] li").length==0) {new_campaign = 1} 
    
    $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "add_new_database_campaign",
                    sent_new_campaign: new_campaign,
                    sent_campaign_id: campaign_id,
                    sent_database_name: database_name,
                    sent_campaign_name: new_campaign_name
                    },
            success: function(list_id)  { 
            
            $("#dialog-testing").html(list_id);
                $("#database-list").append("<li id='"+list_id+"' class=''><table><tr><td style='width:16px'><input name='i-"+list_id+"' id='i-"+list_id+"' type='checkbox' checked='checked'></td><td><label class='' style='display:inline;' for='i-"+list_id+"'>"+database_name+"</label></td><td width='24px'><img class='mono-icon cursor-pointer load-leads' src='/images/icons/mono_icon_database_16.png'></td><td width='24px'><img class='mono-icon cursor-pointer' src='/images/icons/mono_wrench_16.png'></td></tr></table></li>"); 
                $("#add-database-name").val("");
                $("input[name=i-" +list_id+ "]").uniform();
                $( "#wizard-tabs" ).tabs( "refresh" ); 
                }
            }); 
    $("#no-dbs").html("");
    })


var clicked_db = "";
$(".load-leads").live("click", function(){
    clicked_db = $(this).parent().parent().parent().parent().parent().attr("id");

    $("#dialog-load-leads").dialog("open");
    })

$("#dialog-load-leads").dialog({ 
    title: ' <span style="font-size:13px; color:black">Carregar Contactos</span> ',
    autoOpen: false,
    height: 500,
    width: 600,
    resizable: false,
    buttons: { "Fechar" : function() { $(this).dialog("close"); CancelAddLeadsToDatabase();   } },
    open: function(){ 
        $("button").blur(); 
        $("#div-field-match").hide(); 
        $("#table-file-loader-progress").hide(); 
        $("#table-file-converter-progress").hide(); 
        $("#continue-button").hide();
        $("#file-loader-progress-msg").html("A carregar o ficheiro com os contactos...");
        $("#file-loader-progress-loader").html("<img style='margin-top:3px' src='/images/icons/loader-arrows.gif'>")
        
        $("#file-converter-progress-msg").html("A converter o ficheiro de contactos...");
        $("#file-converter-progress-loader").html("<img style='margin-top:3px' src='/images/icons/loader-arrows.gif'>")
        
    }
    
    
    
    
    
    
});

function CancelAddLeadsToDatabase(){
    $("#div-field-match").hide(); 
    $('#div-file-upload').show(); 
    $('.filename').html(""); 
    $(".select-field-match").empty(); 
    $("#table-field-match").html("");
    $("#file-insert-progress-loader").html("");
    $("#file-insert-progress-msg").html("");
}

// GET DINAMIC FIELDS

$("#continue-button").click(function(){

    
    $.ajax({
            type: "POST",
            url: "upload.php",
            dataType: "JSON",
            data: { action: "get_campaign_fields",
                    sent_campaign_id: campaign_id,
                    sent_db_id: clicked_db,
                    sent_file_name: converted_file
                    },
            success: function(result){
                var html_select = "";
                $.each(result.name, function(index, value){
                    html_select = "<select class='select-field-match' id='"+value+"'><option value='-1'>---</option>"
                    $.each(result.headers, function(index1, value1){
                        html_select += "<option value='"+index1+"'>"+value1+"</option>";
                    })
                    html_select += "</select>"
                    $("#table-field-match").append("<tr style='height:28px'><td>"+ result.display_name[index] +"</td><td>"+html_select+"</td></tr>")
    
                })              
            }
    })
    $("#div-file-upload").hide();
    $("#div-field-match").show();
})

// SUBMIT MATCHING FIELDS AND UPLOAD DATA



$('#submit-field-match').click(function(){
    
    $("#file-insert-progress-loader").html("<img style='margin-top:3px' src='/images/icons/loader-arrows.gif'>");
    $("#file-insert-progress-msg").html("A inserir contactos na base de dados...");
    
    var field_match_ids = new Array();
    var field_match_headers = new Array();

    $.each($('.select-field-match option:selected '), function(index, value){
        
        
        field_match_ids.push($(this).parent().attr("id"));
        field_match_headers.push($(this).val())
        
        
        
        } ) 
    
    //alert(field_match_ids);
    //alert(field_match_headers);
    
    $.ajax({
            type: "POST",
            url: "upload.php",
            dataType: "JSON",
            data: { action: "submit_campaign_fields",
                    sent_campaign_id: campaign_id,
                    sent_list_id: clicked_db,
                    sent_file_name: converted_file,
                    sent_match_ids: field_match_ids,
                    sent_match_headers: field_match_headers
                    },
            success: function(result){ 
                $("#file-insert-progress-loader").html("<img style='margin-top:3px' src='/images/icons/mono_checkmark_16.png'>");
                $("#file-insert-progress-msg").html("Contactos inseridos com sucesso na base de dados.");
                
                
            }
    })
    
})

$("#all-done-confirmation").dialog({ 
    title: ' <span style="font-size:13px; color:black"></span> ',
    autoOpen: false,
    height: 250,
    width: 300,
    resizable: false,
    buttons: { "OK" : function() { $(this).dialog("close"); parent.mbody.location = "../index.php"; } }

});




// LOADER ENGINE

var converted_file = "";

function UploadFile() {
    
    $("#table-file-loader-progress").show();
    
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
if (evt.lengthComputable) {
  var percentComplete = Math.round(evt.loaded * 100 / evt.total);
  if(percentComplete > 80){
      
    $("#file-loader-progress-msg").html("Ficheiro carregado com sucesso.");
    $("#file-loader-progress-loader").html("<img style='margin-top:3px' src='/images/icons/mono_checkmark_16.png'>");  
    $("#table-file-converter-progress").show();
    
      
      
      }
  //document.getElementById('progressNumber').innerHTML = percentComplete.toString() + '%';
}
else {
  //document.getElementById('progressNumber').innerHTML = 'unable to compute';
} 
} 

function uploadComplete(evt) {
/* This event is raised when the server send back a response */
//alert(evt.target.responseText);
    $("#file-converter-progress-msg").html("Ficheiro convertido com sucesso.");
    $("#file-converter-progress-loader").html("<img style='margin-top:3px' src='/images/icons/mono_checkmark_16.png'>");
    
    $("#continue-button").show();

$('#continue-button').attr("disabled", false);

      
      
        
        

converted_file = evt.target.responseText;

}

function uploadFailed(evt) {
alert("There was an error attempting to upload the file.");
}

function uploadCanceled(evt) {
alert("The upload has been canceled by the user or the browser dropped the connection.");
}








// RECICLAGEM //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


function submit_Reciclagem()
{
    var recycle_id = new Array();
    var recycle_time = new Array();
    var recycle_tries = new Array();
    var recycle_active = new Array();
    var recycle_campaign = campaign_id;
    
    $("#table-recycle tr td div span").each(function(){
        recycle_id.push($(this).children().attr("id"));
        if($(this).hasClass("checked")){recycle_active.push("Y")} else {recycle_active.push("N")}
    });
    
    $("#table-recycle tr td[class=td-recycle-time]").each(function(){
        
        var split_time = $(this).html().split("m");
        split_time = split_time[0];
        recycle_time.push(split_time);
        
        })
    
    $("#table-recycle tr td[class=td-recycle-tries]").each(function(){
        
        recycle_tries.push($(this).html());
        
    })
    
    
    
    alert(recycle_tries);
    alert(recycle_time);
    
    alert(recycle_id);
    alert(recycle_active);
    
    $.ajax({
            type: "POST",
            url: "requests.php",
            data: { action: "submit_reciclagem",
                    sent_campaign_id : recycle_campaign,
                    sent_recycle_id : recycle_id,
                    sent_recycle_active : recycle_active,
                    sent_recycle_time : recycle_time
                    },
            success: function(data) {}
        });
}
    
    
    


$('#spinner-recycle-time').spinner({
    min: 0,
    max: 180,
    step: 5
    });
$('#spinner-recycle-tries').spinner({
    min: 0,
    max: 10,
    step: 1
    });


$('#new-recycle').click(function(){
    
    var recycle_feed = $("#table-new-recycle tr td div select option:selected").val();
    var recycle_feed_name = $("#table-new-recycle tr td div span").html();
    var recycle_time = "";
    var recycle_tries = "";
    
    $("#table-new-recycle tr td span input").each(function(index, value){ if(index==0){ recycle_time = $(this).attr("aria-valuenow")} else {recycle_tries = $(this).attr("aria-valuenow")}   })
    
    
    $("#table-recycle").append("<tr id='"+recycle_feed+"'><td><input type='checkbox' name='"+recycle_feed+"' id='"+recycle_feed+"' ><label style='display:inline;' for='"+recycle_feed+"'>"+recycle_feed_name+"</label></td><td class='td-icon'><img class='mono-icon cursor-pointer' src='/images/icons/mono_stopwatch_16.png'></td><td class='td-recycle-time' style='width:48px; padding-left:2px'>"+recycle_time+"m</td><td class='td-icon'><img class='mono-icon cursor-pointer' src='/images/icons/mono_reload_16.png'></td><td class='td-recycle-tries cursor-pointer' style='width:32px; padding-left:2px'>"+recycle_tries+"</td><td class='td-icon'><img class='mono-icon' src='/images/icons/mono_attention_16.png'></td><td  style='width:32px; padding-left:2px'>0</td>")
    $("input[name="+recycle_feed+"]").uniform();
    
    $('#spinner-recycle-time').spinner( "value", 0);
    $('#spinner-recycle-tries').spinner( "value", 0);
    
    $("#select-new-recycle option[value='"+recycle_feed+"']").remove();
    $("#select-new-recycle option[value='---']").attr("selected", true);
    $("#table-new-recycle tr td div span").html("---");
    
})

// EDIT RECYCLE TIME
$('#spinner-edit-recycle-time').spinner({
    min: 0,
    max: 180,
    step: 5
});

var edit_recycle_time_id = "";
var edit_recycle_time_value = ""    
$("#edit-recycle-time").live("click", function(){
    
    edit_recycle_time_id = $(this).parent().parent().attr("id");
    
    edit_recycle_time_value = $(this).parent().next().html().split("m");
    edit_recycle_time_value = edit_recycle_time_value[0];
    
    $("#dialog-edit-recycle-time").dialog("open");

    })
    
$("#dialog-edit-recycle-time").dialog({ 
    title: ' <span style="font-size:13px; color:black">Alterar o Tempo de Reciclagem</span> ',
    autoOpen: false,
    height: 300,
    width: 300,
    resizable: false,
    buttons: { "Gravar" : function() { 
                    $(this).dialog("close"); 
                    $("tr[id='"+edit_recycle_time_id+"'] td[class~='td-recycle-time']").html($("#spinner-edit-recycle-time").spinner("value")+"m");
                    }
            },
    open: function(){ 
            $("button").blur(); 
            $("#spinner-edit-recycle-time").spinner("value", edit_recycle_time_value); 
        }
}); 

// EDIT RECYCLE TRIES
$('#spinner-edit-recycle-tries').spinner({
    min: 0,
    max: 10,
    step: 1
});

var edit_recycle_tries_id = "";
var edit_recycle_tries_value = ""   
$("#edit-recycle-tries").live("click", function(){
    
    edit_recycle_tries_id = $(this).parent().parent().attr("id");
    
    edit_recycle_tries_value = $(this).parent().next().html();

    
    $("#dialog-edit-recycle-tries").dialog("open");

    })
    
$("#dialog-edit-recycle-tries").dialog({ 
    title: ' <span style="font-size:13px; color:black">Alterar as Tentativas de Reciclagem</span> ',
    autoOpen: false,
    height: 300,
    width: 300,
    resizable: false,
    buttons: { "Gravar" : function() { 
                    $(this).dialog("close"); 
                    $("tr[id='"+edit_recycle_tries_id+"'] td[class~='td-recycle-tries']").html($("#spinner-edit-recycle-tries").spinner("value"));
                    }
            },
    open: function(){ 
            $("button").blur(); 
            $("#spinner-edit-recycle-tries").spinner("value", edit_recycle_tries_value); 
        }
}); 




</script>

<style>
label {
-webkit-touch-callout: none;
-webkit-user-select: none;
-khtml-user-select: none;
-moz-user-select: none;
-ms-user-select: none;
user-select: none;
}

.input-text-label { color: rgb(68,68,68); font-weight: bold; margin: 8px 0px 2px 0px; display:block; }
.input-text { background-color: #FFFFFF; border: 1px solid #C0C0C0; margin: 0; height: 24px; padding: 0 5px; width: 200px;}
.input-textarea { background-color: #FFFFFF; border: 1px solid #C0C0C0; margin: 0; height: 50px; width: 300px; resize:none; padding: 5px 0px 0px 5px; } 


.td-half-left { text-align: left !important; padding:0 16px 0 16px !important; width:50% !important; vertical-align:top !important; border-right: 1px solid #DDDDDD !important; }
.td-half-right { text-align: left !important; padding:0 16px 0 16px !important; width:50% !important; vertical-align:top !important; }
.div-title { color: rgb(68,68,68); width:100%; border-bottom: 1px solid #DDDDDD; font-weight:bold; margin:8px 0 10px 0; padding-bottom:2px; }
.radio-wrapper { float:right; }

.spacer64 { height:64px; }
.tr-next-button { height:30px; }
.table-100 { height:100%; }
.next-button { float:right; margin-right:3px; }
.error-info-box {  }

.mono-icon { height: 16px; width: 16px; opacity: 0.75; margin-top:1px; margin-right:3px; float:right; }

.tr-icon { height: 22px; }
.td-icon { width:18px; }
.img-icon { cursor:pointer; margin: 6px 0px 0px 0px; }

.cursor-move { cursor:move; }
.cursor-pointer {cursor:pointer; }

.tright {text-align: right; }
.tr-options {height:24px;}

.height24 {height:24px;}

.text-spinner { text-align: center; color: #444444; }

.hover-callback { cursor:pointer;  }
.hover-ishuman { cursor: pointer; }

.error-msg
{
	float: left; margin:8px 0px 0px 6px; font-weight:bold; display:none;
}


/*div.uploader { width: 290px; }
div.uploader input { left:10px; }
div.uploader span.filename { width: 182px; }
div.uploader span.action { cursor:pointer; } */


.tab-visible {display: inline;}
.tab-hidden {display: none;}

</style>

<?php #FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>