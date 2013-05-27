<?php
date_default_timezone_set('Europe/Lisbon');
$event_date = date("Y-m-d H:i:s");
$user = $_SERVER["PHP_AUTH_USER"];
require("../../ini/_json_convert.php");
require("../../ini/dbconnect.php");
require("../../ini/functions.php");
foreach ($_POST as $key => $value) {
	${$key} = $value;
}
foreach ($_GET as $key => $value) {
	${$key} = $value;
}
/*
    REQUESTS DO WIZARD
*/
if($action== "get_pause_counter")
{
    $query = mysql_query("SELECT distinct(pause_code) FROM vicidial_pause_codes", $link);
    $num_pauses = (mysql_num_rows($query) + $counter);
    while(strlen($num_pauses) < 5) { $num_pauses = "0".$num_pauses; }
    $id_pausa = "P".$num_pauses; 
    echo $id_pausa;
}

if($action== "get_feedback_counter")
{
    $query = mysql_query("SELECT distinct(status) FROM vicidial_campaign_statuses", $link);
    $num_feeds = (mysql_num_rows($query) + $counter);
    while(strlen($num_feeds) < 5) { $num_feeds = "0".$num_feeds; }
    $id_feed = "S".$num_feeds; 
    echo $id_feed;
}



/*
	PRELOAD REQUESTS
*/
if($action == "pre_all_leads")
{
	$sQuery = "SELECT count(lead_id) FROM vicidial_list WHERE list_id IN($sent_all_lists)";
	$rQuery = mysql_query($sQuery, $link);
	$rQuery = mysql_fetch_row($rQuery);
	$nAllLeads = NumberPT($rQuery[0]);
	echo $nAllLeads;
}
if($action == "pre_dialer_leads")
{
	$sQuery = "SELECT count(*) FROM vicidial_hopper WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	$rQuery = mysql_fetch_row($rQuery);
	$nDialerLeads = NumberPT($rQuery[0]);
	echo $nDialerLeads;
}
if($action == "pre_last_call")
{
	$sQuery = "SELECT DATE_FORMAT(call_date,'%d-%m-%Y às %H:%i:%s')
			FROM vicidial_log 
			WHERE campaign_id='$sent_campaign_id'
			AND user<>'VDAD'
			ORDER BY call_date DESC
			LIMIT 1";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	$rQuery = mysql_fetch_row($rQuery);
	$nLastCall = $rQuery[0];
	echo $nLastCall;
}
if($action == "pre_recycling_leads")
{
	$sQuery = "SELECT status FROM vicidial_lead_recycle WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	$inRecycling = Query2IN($rQuery, 0);
	$sQuery = "SELECT count(*) FROM vicidial_list WHERE status IN ($inRecycling) AND list_id IN($sent_all_lists)";
	$rQuery = mysql_query($sQuery, $link);
	$rQuery = mysql_fetch_row($rQuery) or die(mysql_error());
	$nRecycling = NumberPT($rQuery[0]);
	echo "~ ".$nRecycling;
}
if($action == "pre_callback_leads")
{
	$sQuery = "SELECT count(*) FROM vicidial_callbacks WHERE campaign_id='$sent_campaign_id' AND status IN ('ACTIVE','LIVE')";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	$rQuery = mysql_fetch_row($rQuery);
	$nCallbackLeads = NumberPT($rQuery[0]);
	echo $nCallbackLeads;
}

// Detalhes

/* 
	REQUESTS: 	"Nome da Campanha"
 */
if($action == "change_campaign_name")
{
	$sQuery = "UPDATE vicidial_campaigns SET campaign_name='$sent_campaign_name', campaign_changedate='$event_date' WHERE campaign_id='$sent_campaign_id'";
	mysql_query($sQuery, $link) or die(mysql_error());
	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração do Nome da Campanha");
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração do Nome da Campanha");
	echo __json_encode( $json );
}

/* 
	REQUESTS: 	"Descrição da Campanha"
 */
if($action == "change_campaign_description")
{
	$sQuery = "UPDATE vicidial_campaigns SET campaign_description='$sent_campaign_description', campaign_changedate='$event_date' WHERE campaign_id='$sent_campaign_id'";
	mysql_query($sQuery, $link);
	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração da Descrição da Campanha");
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração da Descrição da Campanha");
	echo __json_encode( $json );
}

/* 
	REQUESTS: 	"Última Alteração"
 */
if($action == "table-campaign-changedate")
{
	$aColumns = array( 'event_date', 'event_date_pt', 'full_name', 'event_notes' ); 
	$sQuery = "
			SELECT	event_date, DATE_FORMAT(event_date,'%d-%m-%Y às %H:%i:%s') AS event_date_pt, B.full_name, event_notes
			FROM   	vicidial_admin_log A
			INNER JOIN vicidial_users B ON A.user=B.user 
			WHERE record_id='$sent_campaign_id'
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
		switch($aColumns[$i]) 
		{ 
			default: $row[] = $aRow[ $aColumns[$i] ];
		}
	}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );

}

/* 
	REQUESTS: 	"Últimos Logins"
 */
if($action == "table-campaign-logindate")
{
	$aColumns = array( 'event_time', 'event_date_pt', 'full_name' ); 
	$sQuery = "
			SELECT	event_time, DATE_FORMAT(event_time,'%d-%m-%Y às %H:%i:%s') AS event_date_pt, B.full_name
			FROM vicidial_agent_log A
			INNER JOIN vicidial_users B ON A.user=B.user
			WHERE campaign_id='$sent_campaign_id'
			AND sub_status='LOGIN'
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
		switch($aColumns[$i]) 
		{ 
			default: $row[] = $aRow[ $aColumns[$i] ];
		}
	}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );

}

/* 
	REQUESTS: 	"Últimas Chamadas"
 */
if($action == "table-campaign-calldate")
{
	$aColumns = array( 'call_date', 'call_date_pt', 'full_name', 'phone_number' ); 
	$sQuery = "
			SELECT	call_date, DATE_FORMAT(call_date,'%d-%m-%Y às %H:%i:%s') AS call_date_pt, B.full_name, phone_number
			FROM vicidial_log A
			INNER JOIN vicidial_users B ON A.user=B.user
			WHERE campaign_id='$sent_campaign_id'
			AND A.user<>'VDAD'
			ORDER BY call_date DESC
			LIMIT 1500
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
		switch($aColumns[$i]) 
		{ 
			default: $row[] = $aRow[ $aColumns[$i] ];
		}
	}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );

}

/* 
	REQUESTS: 	"Nº de Dbs"
 */
if($action == "table-campaign-dbs")
{
	$aColumns = array( 'list_name', 'active' ); 
	$sQuery = "
			SELECT	list_id, list_name, active
			FROM vicidial_lists
			WHERE campaign_id='$sent_campaign_id'
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
		switch($aColumns[$i]) 
		{ 
			case "active": if($aRow[ $aColumns[$i] ]=="Y"){ $row[] = "<img width='12' height='12' title='Clique para alterar.' class='elem-pointer' id='db-enabler-".$aRow['list_id']."' onclick=\"DBEnabler('$aRow[list_id]');\" src='/images/icons/tick_16.png'>"; } else { $row[] = "<img width='12' height='12' title='Clique para alterar.' class='elem-pointer' onclick=\"DBEnabler('$aRow[list_id]');\" id='db-enabler-".$aRow['list_id']."' src='/images/icons/cross_16.png'>"; }; break; 
			
			default: $row[] = $aRow[ $aColumns[$i] ];
		}
	}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );

}
if($action == "db-enabler")
{
	$sQuery = "UPDATE vicidial_lists SET active='$sent_state' WHERE list_id='$sent_list'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
}

/* 
	REQUESTS ITEM: 	"Total de Contactos"
 */
if($action == "table-campaign-leads")
{
	$aColumns = array( 'estado', 'ns' ); 
	$sQuery = "
			SELECT COUNT(vl.status) AS ns, vs.status_name AS estado  
			FROM vicidial_list vl 
			INNER JOIN (SELECT DISTINCT(status), status_name FROM vicidial_campaign_statuses WHERE campaign_id='$sent_campaign_id' 
						UNION ALL 
						SELECT status, status_name FROM vicidial_statuses) vs ON vs.status=vl.status 
			WHERE vl.list_id IN ($sent_list_filter)
			GROUP by vl.status
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
			switch($aColumns[$i]) 
			{  
				default: $row[] = $aRow[ $aColumns[$i] ];
			}
		}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );
}

/* 
	REQUESTS ITEM: 	"Contactos no Dialer"
 */
if($action == "table-campaign-dialer")
{
	$aColumns = array( 'phone_number', 'first_name' ); 
	$sQuery = "
			SELECT 	B.phone_number, B.first_name  
			FROM	vicidial_list B
			INNER JOIN vicidial_hopper A ON B.lead_id=A.lead_id
			WHERE A.campaign_id='$sent_campaign_id' 
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
			switch($aColumns[$i]) 
			{  
				default: $row[] = $aRow[ $aColumns[$i] ];
			}
		}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );
}

/* 
	REQUESTS ITEM: 	"Contactos em Reciclagem"
 */
if($action == "get_table-campaign-recycle")
{
	$aColumns = array( 'estado', 'onrecycle' ); 
	$sQuery = "
			SELECT vs.status_name AS estado, count(vl.status) as onrecycle
			FROM vicidial_list vl 
			INNER JOIN (SELECT DISTINCT(status), status_name FROM vicidial_campaign_statuses WHERE campaign_id='$sent_campaign_id' 
						UNION ALL 
						SELECT status, status_name FROM vicidial_statuses ) vs ON vs.status=vl.status
			INNER JOIN (SELECT status from vicidial_lead_recycle WHERE campaign_id='$sent_campaign_id' AND active='Y') vlr ON vlr.status=vl.status 
			WHERE vl.list_id IN($sent_list_filter) 
			GROUP BY vl.status
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$row[] = $aRow[ $aColumns[$i] ];
		}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );
}

/* 
	REQUESTS ITEM: 	"Callbacks"
 */
if($action == "table-campaign-callbacks")
{
	$aColumns = array( 'full_name', 'count(status)' ); 
	$sQuery = "
			SELECT	B.full_name, count(status)
			FROM vicidial_callbacks A
			INNER JOIN vicidial_users B ON A.user=B.user
			WHERE campaign_id='$sent_campaign_id'
			AND status IN('ACTIVE','LIVE')
			GROUP BY B.full_name
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$row[] = $aRow[ $aColumns[$i] ];
		}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );
}







// Opções Gerais

/* 
	REQUESTS ITEM: 	"Campanha Activa/Inactiva"
 */
if($action == "change_campaign_active") 
{
	$sQuery = "UPDATE vicidial_campaigns SET active='$sent_state' WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	if($sent_state=="Y"){$estado="Activa";} else {$estado="Inactiva";}
	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração do Estado da Campanha para $estado");
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração do Estado da Campanha para $estado");
	echo __json_encode( $json );
}

/* 
	REQUESTS ITEM: 	"Campanha Ratio/Manual - Inbound"
 */
if($action == "change-campaign-dialmethod") 
{
	$sQuery = "UPDATE vicidial_campaigns SET dial_method='$sent_dial', campaign_allow_inbound='$sent_inbound' WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	if($sent_dial=="RATIO"){$dial="Chamadas Automáticas";} else {$dial="Chamadas Manuais";}
	if($sent_inbound=="Y"){$inbound=" com Inbound";} else {$inbound=" sem Inbound";}
	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração do Tipo de Campanha para $dial$inbound");
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração do Estado da Campanha para $dial$inbound");
	echo __json_encode( $json );
}

/* 
	REQUESTS ITEM: 	"Atribuição de Chamadas"
 */
if($action == "change-campaign-nextcall") 
{
	$sQuery = "UPDATE vicidial_campaigns SET next_agent_call='$sent_nextcall' WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());

	switch($sent_nextcall){
		case "longest_wait_time": $nextcall = "Maior Tempo à Espera"; break;
		case "fewest_calls": $nextcall = "Menos Chamadas Realizadas"; break;
		case "campaign_rank": $nextcall = "Nível do Operador"; break;
		case "random": $nextcall = "Aleatória"; break;
		}

	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração da Atribuição de Chamadas para $nextcall");
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração da Atribuição de Chamadas para $nextcall");
	echo __json_encode( $json );
}

/* 
	REQUESTS ITEM: 	"Rácio da Campanha"
 */
if($action == "change-campaign-ratio") 
{
	$sQuery = "UPDATE vicidial_campaigns SET auto_dial_level='$sent_ratio' WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());

	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração do Rácio da Campanha para \"$sent_ratio\"");
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração do Rácio da Campanha para \"$sent_ratio\"");
	echo __json_encode( $json );
}

















// Opções dos Operadores

/* 
	REQUESTS ITEM: 	"Pausas dos Operadores Activas/Inactivas"
 */
if($action == "change_campaign_agentpausecodes") 
{
	$sQuery = "UPDATE vicidial_campaigns SET agent_pause_codes_active='$sent_state' WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	
	if($sent_state=="Y"){ $state = "Activas"; } else { $state = "Inactivas"; }

	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração das Pausas dos Operadores para $state");
	
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração das Pausas dos Operadores para $state");
	echo __json_encode( $json );
}

/* 
	REQUESTS ITEM: 	"Pausa Depois de Cada Chamada"
 */
if($action == "change_campaign_pauseaftercall") 
{
	$sQuery = "UPDATE vicidial_campaigns SET pause_after_each_call='$sent_state' WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	
	if($sent_state=="Y"){ $state = "Activo"; } else { $state = "Inactivo"; }

	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração das Pausas Automáticas para $state");
	
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração das Pausas Automáticas para $state");
	echo __json_encode( $json );
}

/* 
	REQUESTS ITEM: 	"Callbacks Activos"
 */
if($action == "change_campaign_callbacks_active") 
{
	$sQuery = "UPDATE vicidial_campaigns SET scheduled_callbacks='$sent_state' WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	
	if($sent_state=="Y"){ $state = "Activos"; } else { $state = "Inactivos"; }

	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração dos Callbacks para $state");
	
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração dos Callbacks para $state");
	echo __json_encode( $json );
	
}

/* 
	REQUESTS ITEM: 	"Contagem de Callbacks"
 */
if($action == "change_campaign_callbacks_count") 
{
	$sQuery = "UPDATE vicidial_campaigns SET scheduled_callbacks_count='$sent_state' WHERE campaign_id='$sent_campaign_id'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	
	if($sent_state=="Y"){ $state = "Todos"; } else { $state = "Apenas Activos"; }

	AdminLogger("CAMPAIGNS", "MODIFY", $sent_campaign_id, "ADMIN MODIFY CAMPAIGN", $sQuery, "Alteração da Contagem de Callbacks para $state");
	
	$json['reply'] = array($event_date, DateSQL2DatePT($event_date), $user, "Alteração da Contagem de Callbacks para $state");
	echo __json_encode( $json );
}











































if($action == "get_campaign_list") 
{
			
		
	// ALLOWED CAMPAIGNS
$query1 = mysql_fetch_assoc(mysql_query("SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]';", $link)) or die(mysql_error());
$query2 = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$query1[user_group]';", $link)) or die(mysql_error());
$AllowedCampaigns = "'" . preg_replace("/ /","','" , preg_replace("/ -/",'',$query2['allowed_campaigns'])) . "'";
		
	
	 


	$aColumns = array( 'campaign_id', 'campaign_name', 'active', 'dial_method', 'auto_dial_level'); 
	$sQuery = "
			SELECT campaign_id, campaign_name, active, dial_method, auto_dial_level
			FROM   vicidial_campaigns WHERE campaign_id IN($AllowedCampaigns)
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
		switch($aColumns[$i]) 
		{ /* /sips-admin/admin.php?ADD=34&campaign_id=$aRow[campaign_id] */ 
			case "campaign_id": $row[] = "<a href='campaign_edit.php?campaign_id=$aRow[campaign_id]&campaign_name=$aRow[campaign_name]'><img class='elem-pointer'  src='/images/icons/shape_square_edit_16.png'></a>"; break;
			case "dial_method": if($aRow[ $aColumns[$i] ] == "RATIO"){ $row[] = "Auto"; } else { $row[] = "Manual"; }; break;
			case "active": if($aRow[ $aColumns[$i] ]=="Y"){ $row[] = "<img title='Clique para alterar.' class='elem-pointer' id='cmp-enabler-".$aRow['campaign_id']."' onclick=\"CampaignEnabler('$aRow[campaign_id]');\" src='/images/icons/tick_16.png'>"; } else { $row[] = "<img title='Clique para alterar.' class='elem-pointer' onclick=\"CampaignEnabler('$aRow[campaign_id]');\" id='cmp-enabler-".$aRow['campaign_id']."' src='/images/icons/cross_16.png'>"; }; break; 
			default: $row[] = $aRow[ $aColumns[$i] ];
		}
		
		
			
		}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );
	
}



### REQUESTS EDITAR CAMPANHA #######################

### REQUESTS GERIR PAUSAS ##########################
if($action == "get_all_pause_codes")
{
	$aColumns = array( 'pause_code_name', 'max_time', 'active' ); 
	$sQuery = "
			SELECT 	pause_code_id, pause_code_name, max_time, active
			FROM   	sips_pause_codes_default
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
		switch($aColumns[$i]) 
		{  
			//case "fd": $row[] = "<a href='campaign_edit.php?campaign_id=$aRow[campaign_id]&campaign_name=$aRow[campaign_name]'><img class='elem-pointer' onclick=\"CampaignEdit('$aRow[campaign_id]');\" src='/images/icons/shape_square_edit_16.png'></a>"; break;
			//case "df": if($aRow[ $aColumns[$i] ] == "RATIO"){ $row[] = "Auto"; } else { $row[] = "Manual"; }; break;
			//case "active": if($aRow[ 'active' ]==1){ $row[] = "<img title='Clique para alterar.' class='elem-pointer' id='pause-enabler-".$aRow['pause_code']."' onclick=\"PauseEnabler('$aRow[pause_code]');\" src='/images/icons/tick_16.png'>"; } else { $row[] = "<img title='Clique para alterar.' class='elem-pointer' onclick=\"PauseEnabler('$aRow[pause_code]');\" id='pause-enabler-".$aRow['pause_code']."' src='/images/icons/cross_16.png'>"; }; break; 
			default: $row[] = $aRow[ $aColumns[$i] ];
		}
		
		
			
		}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );
}

### REQUESTS DAS PAUSAS ############################
if($action == "get_campaign_pauses")
{
	$aColumns = array( 'pause_code_name', 'max_time', 'active' ); 
	$sQuery = "
			SELECT 	pause_code, pause_code_name, max_time, active
			FROM   	vicidial_pause_codes
			WHERE	campaign_id='$sent_campaign_id'
			";
	//echo $sQuery;
	$rResult = mysql_query( $sQuery, $link ) or die(mysql_error());
	$output = array("aaData" => array()	);
		
	while ( $aRow = mysql_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
		
		switch($aColumns[$i]) 
		{  
			case "campaign_id": $row[] = "<a href='campaign_edit.php?campaign_id=$aRow[campaign_id]&campaign_name=$aRow[campaign_name]'><img class='elem-pointer' src='/images/icons/shape_square_edit_16.png'></a>"; break;
			case "dial_method": if($aRow[ $aColumns[$i] ] == "RATIO"){ $row[] = "Auto"; } else { $row[] = "Manual"; }; break;
			case "active": if($aRow[ 'active' ]==1){ $row[] = "<img title='Clique para alterar.' class='elem-pointer' id='pause-enabler-".$aRow['pause_code']."' onclick=\"PauseEnabler('$aRow[pause_code]');\" src='/images/icons/tick_16.png'>"; } else { $row[] = "<img title='Clique para alterar.' class='elem-pointer' onclick=\"PauseEnabler('$aRow[pause_code]');\" id='pause-enabler-".$aRow['pause_code']."' src='/images/icons/cross_16.png'>"; }; break; 
			default: $row[] = $aRow[ $aColumns[$i] ];
		}
		
		
			
		}
	$output['aaData'][] = $row;
	}
	echo __json_encode( $output );
}
 
if($action == "change_pause_state") 
{
	$sQuery = "UPDATE vicidial_pause_codes SET active='$sent_state' WHERE pause_code='$sent_pause_code'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
}
##################################################


if($action == "change_campaign_state") 
{
	$sQuery = "UPDATE vicidial_campaigns SET active='$sent_state' WHERE campaign_id='$sent_campaign'";
	$rQuery = mysql_query($sQuery, $link) or die(mysql_error());
	
	if($sent_state == "Y") {$sent_state="ACTIVE";} else {$sent_state="INACTIVE";}
	
	mysql_query("UPDATE vicidial_remote_agents SET status = '$sent_state'");
	
}



?>