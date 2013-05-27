<?php 
#HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
$header.="ini/header.php";
require($header);
#END HEADER



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
		//echo $whereLOGallowed_campaignsSQL;
		}




if ($_POST[user]==''){
$navigation = '1_CONFIG_CAMPAIGN';
$user = $_SERVER['PHP_AUTH_USER'];
$query = "SELECT user_group FROM vicidial_users WHERE user='$user'";
$query = mysql_query($query, $link);
$user_group_row = mysql_fetch_row($query);
$user_group = $user_group_row[0];  


$query = "SELECT cloud FROM servers";
$query = mysql_query($query);
$row = mysql_fetch_row($query);
$is_cloud = $row[0];
}


		#############################################################
		### 1_CONFIG_CAMPAIGN
		#############################################################
		if (isset($_POST['1_CONFIG_CAMPAIGN'])) {
           
            $user = $_POST['user'];
            $user_group = $_POST['user_group'];
            $is_cloud = $_POST['is_cloud'];
			
			$query = "SELECT campaign_id FROM vicidial_campaigns";
			$query = mysql_query($query, $link) or die(mysql_error());
			$num_campaigns = (mysql_num_rows($query)+100);
			
			while(strlen($num_campaigns) < 5) { $num_campaigns = "0".$num_campaigns; }
			
			$id_campanha = "C".$num_campaigns; 
			
			$nome_campanha = $_POST['nome_campanha'];
			$tipo_campanha = $_POST['tipo_campanha'];
			$navigation = '2_CONFIG_PAUSE_CODES';
		}
		#############################################################
		### 2_CONFIG_PAUSE_CODES
		#############################################################
		if (isset($_POST['2_CONFIG_PAUSE_CODES'])) {

			$temp_pausas="";
			for ($i = 0; $i < count($_POST['opcoes_pausas']); $i++) {

				$temp_pausas .= $_POST['opcoes_pausas'][$i] . (($i!=(count($_POST['opcoes_pausas'])-1)) ? "|_|" : "");
				
			}
            
            $user = $_POST['user'];
            $user_group = $_POST['user_group'];
            $is_cloud = $_POST['is_cloud'];			
			$id_campanha = $_POST['id_campanha'];
			$nome_campanha = $_POST['nome_campanha'];
			$tipo_campanha = $_POST['tipo_campanha'];
			
			$pausas_campanha = $temp_pausas; 
			
			//echo $pausas_campanha;
			
			$navigation = '3_CONFIG_FEEDBACK_CODES';
		}
		#############################################################
		### 2_1_CREATE_PAUSE_CODES
		#############################################################
		if (isset($_POST['2_1_CREATE_PAUSE_CODES'])) {
           
            $user = $_POST['user'];
            $user_group = $_POST['user_group'];
            $is_cloud = $_POST['is_cloud'];
            $id_campanha = $_POST['id_campanha'];
            $nome_campanha = $_POST['nome_campanha'];
            $tipo_campanha=$_POST['tipo_campanha']; 
            
            $nova_pausa = $_POST['nova_pausa'];

			$query = "SELECT distinct pause_code, pause_code_name FROM vicidial_pause_codes $whereLOGallowed_campaignsSQL GROUP BY pause_code_name";
			$query = mysql_query($query, $link) or die(mysql_error());
			$num_pauses = (mysql_num_rows($query)+1);
			
			while(strlen($num_pauses) < 5) { $num_pauses = "0".$num_pauses; }
			
			$id_pausa = "P".$num_pauses; 
			
            echo $id_pausa;
            			
			//$query = "INSERT INTO sips_pause_codes_default (pause_code_id,pause_code_name) values ('$id_pausa','$nova_pausa')";
			//$query = mysql_query($query, $link) or die(mysql_error());

			$navigation = "2_CONFIG_PAUSE_CODES";
		}
		#############################################################
		### 3_CONFIG_FEEDBACK_CODES
		#############################################################
		if (isset($_POST['3_CONFIG_FEEDBACK_CODES'])) {
			
			$temp_feedbacks="";
			for ($i = 0; $i < count($_POST['opcoes_feedbacks']); $i++) {

				$temp_feedbacks .= $_POST['opcoes_feedbacks'][$i] . (($i!=(count($_POST['opcoes_feedbacks'])-1)) ? "|_|" : "");
				
			}		
		
            $user = $_POST['user'];
            $user_group = $_POST['user_group'];
            $is_cloud = $_POST['is_cloud'];
			$id_campanha = $_POST['id_campanha'];
			$nome_campanha = $_POST['nome_campanha'];
			$tipo_campanha = $_POST['tipo_campanha'];
			$pausas_campanha = $_POST['pausas_campanha'];
			
			$feedbacks_campanha = $temp_feedbacks; 
		
		
			$navigation = '4_CONFIG_SCRIPTS';		
		}
		#############################################################
		### 3_1_CREATE_FEEDBACK_CODES
		#############################################################
		if (isset($_POST['3_1_CREATE_FEEDBACK_CODES'])) {
			
            $user = $_POST['user'];
            $user_group = $_POST['user_group'];
            $is_cloud = $_POST['is_cloud'];
            $id_campanha = $_POST['id_campanha'];
            $nome_campanha = $_POST['nome_campanha'];
            $tipo_campanha=$_POST['tipo_campanha']; 
            
            $pausas_campanha=$_POST['pausas_campanha'];
            
			$is_callback=(isset($_POST['is_callback']))?1:0; 
			$is_human=(isset($_POST['is_human']))?1:0; 

			$query = "SELECT status_code_id FROM sips_status_codes_default";
			$query = mysql_query($query, $link) or die(mysql_error());
			$num_feedbacks = (mysql_num_rows($query)+1);
					
			while(strlen($num_feedbacks) < 5) { $num_feedbacks = "0".$num_feedbacks; }
			
			$id_feedback = "S".$num_feedbacks;
			$nome_feedback = $_POST['novo_feedback'];

			$query = "INSERT INTO sips_status_codes_default (status_code_id,status_code_name,human_answer,callback) VALUES ('$id_feedback','$nome_feedback','$is_human', '$is_callback')";
			$query = mysql_query($query, $link);
	

			$navigation = '3_CONFIG_FEEDBACK_CODES';
		}
		#############################################################
		### 4_CONFIG_SCRIPTS
		#############################################################
		if (isset($_POST['4_CONFIG_SCRIPTS'])) {
	
            
            $user = $_POST['user'];
            $user_group = $_POST['user_group'];
            $is_cloud = $_POST['is_cloud'];
            $id_campanha = $_POST['id_campanha'];
            $nome_campanha = $_POST['nome_campanha'];
            $tipo_campanha=$_POST['tipo_campanha']; 
            $pausas_campanha=$_POST['pausas_campanha'];
            $feedbacks_campanha=$_POST['feedbacks_campanha'];
            $script_campanha=$_POST['opcoes_script'];
     
			$navigation = '5_SHOW_DATA_AND_CONFIRM';
		}
		#############################################################
		### 5_1_SUBMIT_DATA
		#############################################################
		if (isset($_POST['5_1_SUBMIT_DATA'])){
			
        $user = $_POST['user'];
        $user_group = $_POST['user_group'];
        $is_cloud = $_POST['is_cloud'];
		$id_campanha = $_POST['id_campanha'];
		$nome_campanha = $_POST['nome_campanha'];
		$tipo_campanha = $_POST['tipo_campanha'];
        $pausas_campanha = $_POST['pausas_campanha'];
        $feedbacks_campanha = $_POST['feedbacks_campanha'];
		$opcoes_script = $_POST['script_campanha'];
			

		//Campanha	
		if($tipo_campanha=="ratio"){$ratio=1.5;} else {$ratio=0;}
		$insert = "INSERT INTO vicidial_campaigns
		(
		campaign_id,
		campaign_name,
		campaign_description,
		active,
		lead_order,
		allow_closers,
		hopper_level,
		auto_dial_level,
		next_agent_call,
		local_call_time,
		dial_timeout,
		dial_prefix,
		allcalls_delay,
		campaign_recording,
		campaign_rec_filename,
		scheduled_callbacks,
		drop_call_seconds,
		drop_action,
		dial_method,
		adaptive_dropped_percentage,
		no_hopper_leads_logins,
		scheduled_callbacks_count,
		scheduled_callbacks_alert,
		dial_statuses,
		agent_pause_codes_active,
		omit_phone_code,
		auto_alt_dial
		)
		VALUES		(
		'". strtoupper($id_campanha) ."',
		'".mysql_real_escape_string($nome_campanha)."',
		'',
		'Y',
		'RANDOM',
		'Y',
		'50',
		'$ratio',
		'longest_wait_time',
		'24hours',
		'35',
		'X',
		'0',
		'ALLFORCE',
		'FULLDATE_CUSTPHONE',
		'Y',
		'15',
		'HANGUP',
		'". strtoupper($tipo_campanha) ."',
		'3',
		'Y',
		'LIVE',
		'BLINK_RED',
		' DC PU PDROP ERI NA DROP B NEW -',
		'FORCE',
		'Y',
		'ALT_AND_ADDR3'); "; 
		
        //Dados da campanha no RealtimeReport
	    $insert .= "INSERT INTO	vicidial_campaign_stats
		(campaign_id)
		VALUES		
		('$id_campanha'); ";  
		
		//cria pauses 
        $exp1_pauses=explode("|_|", $pausas_campanha);
        for ($i = 0; $i < count($exp1_pauses); $i++) {
            
            $exp2_pauses = explode("#_#", $exp1_pauses[$i]);
            $pause_code_id = $exp2_pauses[0];
            $pause_code_name = $exp2_pauses[1];

            $insert .= "INSERT INTO vicidial_pause_codes (pause_code,pause_code_name,campaign_id,active) VALUES ('$pause_code_id','$pause_code_name','$id_campanha','1'); ";
            
        }
        
        //cria os feedback
        $count_feedbacks = explode("|_|",$feedbacks_campanha);
        for ($i = 0; $i < count($count_feedbacks); $i++) {

            $explode = explode("#_#", $count_feedbacks[$i]);
            $status_code_id = $explode[0];
            $status_code_name = $explode[1];
            $is_human = ($explode[2]=="Y")?"Y":"N";
            $is_callback = ($explode[3]=="Y")?"Y":"N";

            $insert .= "INSERT INTO vicidial_campaign_statuses (status,status_name,campaign_id,human_answered,scheduled_callback,selectable) VALUES ('$status_code_id','$status_code_name','$id_campanha','Y','$is_callback','Y'); ";
            
        }
		
        //Reciclagem
		$insert .= "INSERT INTO vicidial_lead_recycle 
		(campaign_id, status, attempt_delay, attempt_maximum, active) 
		VALUES
		('$id_campanha','B','1800','10','Y'),
		('$id_campanha','DC','1800','10','Y'),
		('$id_campanha','DROP','120','10','Y'),
		('$id_campanha','ERI','1800','10','Y'),
		('$id_campanha','NA','3600','10','Y'),
		('$id_campanha','PDROP','120','10','Y'),
		('$id_campanha','PU','120','10','Y'); "; 

			
		//User Groups
        
        $query = "SELECT user_group FROM vicidial_users WHERE user='$user'";
        $query = mysql_query($query, $link);
        $user_type = mysql_fetch_row($query);
        $user_type = strtoupper($user_type[0]);
        	
        if($user_type=='ADMIN') {
           $query = "SELECT allowed_campaigns, user_group FROM vicidial_user_groups ";
           $query = mysql_query($query);
               
        for($i=0;$i<mysql_num_rows($query);$i++)
        {
            $row=mysql_fetch_assoc($query);
            $allowed_campaigns = $row['allowed_campaigns'];
            $new_allowed_campaigns = " $id_campanha$allowed_campaigns";
            $user_group = $row['user_group'];
            $insert .= "UPDATE vicidial_user_groups SET allowed_campaigns='$new_allowed_campaigns' WHERE user_group='$user_group'; ";
        }
        } else {
            $query="SELECT allowed_campaigns, vug.user_group FROM vicidial_user_groups vug INNER JOIN vicidial_users vu ON vug.user_group=vu.user_group WHERE user='$user'; ";
            $query = mysql_query($query);
            $row = mysql_fetch_assoc($query);
            $allowed_campaigns = $row['allowed_campaigns'];
            $new_allowed_campaigns = " $id_campanha$allowed_campaigns";
            $user_group = $row['user_group'];
            $insert .= "UPDATE vicidial_user_groups SET allowed_campaigns='$new_allowed_campaigns' WHERE user_group='$user_group'; ";
        } 	
			
			
        //Cópia do Script
        if($opcoes_script!="naocopiar"){
            $insert .= "CREATE TABLE custom_$id_campanha LIKE custom_$opcoes_script; ";
            //$query = mysql_query($query) or die(mysql_error());
            $insert .= "INSERT INTO vicidial_lists_fields 
                        (SELECT '', `list_id`, `field_label`, `field_name`, `field_description`, `field_rank`, `field_help`, `field_type`, `field_options`, `field_size`, `field_max`, `field_default`, `field_cost`, `field_required`, `name_position`, `multi_position`, `field_order`, '$id_campanha', `action` 
                        FROM vicidial_lists_fields where campaign_id='$opcoes_script'); "; 
            //$query = mysql_query($query) or die(mysql_error());
        }

        
		//Correr as QUERIES
		$in = explode("; ", $insert);
		for ($i=0; $i < (count($in)-1); $i++) { 
	    //echo $in[$i]."<br><br>";	
		mysql_query($in[$i],$link) or die(mysql_error()) ;
				
			} 
									
			
			echo "<center>Campanha Criada com sucesso.</center>";
		
		}

	#############################################################
	### 1_CONFIG_CAMPAIGN
	#############################################################
	if ($navigation == '1_CONFIG_CAMPAIGN') {
    ?>
	<script>
		function Next() 
		{
			CampaignName = $('#nome_campanha');
		 	if (CampaignName.val()==='') 
		 	{
		 		alert('Tem de preencher algo no "Nome da Campanha".')
		 	}
		 	else
		 	{
		 		$('#form_campanha').submit();
		 	}
		 
		}
	</script>
	<div class="cc-mstyle">
		<table>
			<tr>
				<td id="icon32"><img src='../../images/icons/wizard.png' /></td>
				<td id='submenu-title' style='width:350px'> Configuração | Definições da Campanha</td>
				<td style='text-align:left'></td>
			</tr>
		</table>
	</div>

	<div id='work-area' style='min-height:200px'>
		<br>
		<br>
		<div class='cc-mstyle' style='border:none'>
			<form id='form_campanha' action='campaign_create.php' method='POST'>
				<input type='hidden' name='1_CONFIG_CAMPAIGN' value='default'>
				<input type='hidden' name='user' value='<?php echo $user; ?>'>
				<input type='hidden' name='user_group' value='<?php echo $user_group; ?>'>
				<input type='hidden' name='is_cloud' value='<?php echo $is_cloud; ?>'>
				
				<table>
					<tr>
						<td style='width:200px'>
						<div class='cc-mstyle' style='height:28px;'>
							<p>
								Nome da Campanha
							</p>
						</div></td>
						<td>
						<input type=text style='text-align:center;' name="nome_campanha" id="nome_campanha" value="">
						</td>
					</tr>
					<tr>
						<td style='width:200px'>
						<div class='cc-mstyle' style='height:28px;'>
							<p>
								Tipo de Campanha
							</p>
						</div></td>
						<td>
						<input type="radio" name="tipo_campanha" id="ratio" value="ratio" checked >
						<label for="ratio" style="display: inline;"> Racio</label>
						<input type="radio" name="tipo_campanha" id="manual" value='manual' >
						<label for="manual" style="display: inline;"> Manual</label>
						</td>
					</tr>
				</table>
				<br>
				<table>
					<tr>
						<td ><span style='float:right;cursor:pointer;' onclick="Next();">Seguinte
						<img style="vertical-align: middle;" src='../../images/icons/shape_square_go_32.png' /></span>
						</td>
					</tr>
				</table>
			</form>
		</div>

<?php } 
    #############################################################
	### 2_CONFIG_PAUSE_CODES
	#############################################################
	if ($navigation == '2_CONFIG_PAUSE_CODES' ) { 


            $query = "SELECT distinct pause_code, pause_code_name FROM vicidial_pause_codes $whereLOGallowed_campaignsSQL GROUP BY pause_code_name";
			//echo $query;
            $query = mysql_query($query,$link) or die(mysql_error());
            for ($i=0;$i<mysql_num_rows($query);$i++)
            {
            $row = mysql_fetch_assoc($query);
            $row['pause_code'] = strtoupper($row['pause_code']);
            $checkbox_options.= "<input class='check_pausas' type=checkbox name=opcoes_pausas[] value='$row[pause_code]#_#$row[pause_code_name]' id='$row[pause_code]' ><label style='display:inline;' for='$row[pause_code]'> $row[pause_code_name]</label><br>";
            }
            if(!mysql_num_rows($query)){
                $checkbox_options .= "<br>Não existem Códigos de Pausa definidos.";
            }
        
		 
 			?>
				<script>
					function Next() {
					  if ($('.check_pausas:checked').length===0) {
					  	alert("Tem de escolher pelo menos um código de pausa.");
					  }
					  else
					  {
					  	$('#form_pausas').submit();
					  };
					  
					}
					var pause_counter = 1;
					function CreatePauseCode() {
					  if ($('#nova_pausa').val()==='')
					  {
					  	alert('O campo "Nome da Pausa", não pode estar vazio.');
					  }else
					  {
					  	//$('#form_cria_pausa').submit();
						
					$.ajax({
						type: "POST",
						url: "_requests.php",
						data: {action: "get_pause_counter", counter: pause_counter},
						error: function()
						{
							alert("Ocorreu um Erro.");
						},
						success: function(data)	
						{
							//alert(data);
							var pause_id = data;
							var pause_name = $("#nova_pausa").val(); 
							$("#td-pause-list").append("<input class='check_pausas' type=checkbox name=opcoes_pausas[] value='" + pause_id + "#_#" + pause_name + "' id='"+ pause_id +"' ><label style='display:inline;' for='" + pause_id + "'> "+ pause_name +" </label><br>");
							$("#nova_pausa").val("");
							pause_counter++;
							//alert(pause_counter);
						}
					});

						
						
						
						
						
						
					  }
					}
				</script>
				<div class=cc-mstyle>
					<table>
						<tr>
							<td id='icon32'><img src='../../images/icons/wizard.png' /></td>
							<td id='submenu-title' style='width:350px'> Configuração | Definição de Pausas</td>
							<td style='text-align:left'></td>
						</tr>
					</table>
				</div>

				<div id=work-area style='min-height:200px'>
					<br>
					<br>
					<div class=cc-mstyle style='border:none'>
						<form id='form_pausas' action='campaign_create.php' method='POST'>
							<input type='hidden' name='2_CONFIG_PAUSE_CODES' value='default'>
							<input type='hidden' name='user' value='<?php echo $user; ?>'>
                            <input type='hidden' name='user_group' value='<?php echo $user_group; ?>'>
                            <input type='hidden' name='is_cloud' value='<?php echo $is_cloud; ?>'>
							<input type='hidden' name='id_campanha' value='<?php echo $id_campanha; ?>'>
							<input type='hidden' name='nome_campanha' value='<?php echo $nome_campanha; ?>'>
							<input type='hidden' name='tipo_campanha' value='<?php echo $tipo_campanha; ?>'>
							<table>
								<tr>
									<td id="td-pause-list" style='text-align:left'><?php echo $checkbox_options; ?></td>
								</tr>
							</table>

							<br>
							<table>
								<tr>
									<td style='text-align:right'>
										<span style='cursor:pointer;float:right;' onclick="Next();">
											Seguinte
										<img style="vertical-align: middle;" src='../../images/icons/shape_square_go_32.png' />
										</span>
									</td>
								</tr>
							</table>
						</form>
						<hr>
						<br>
						<!---------------------------------------------------------------
						2_1_CREATE_PAUSE_CODES	
						---------------------------------------------------------------->
						<form id='form_cria_pausa' action='campaign_create.php' method='POST'>
							<input type='hidden' name='2_1_CREATE_PAUSE_CODES' value='default'>
							<input type='hidden' name='user' value='<?php echo $user; ?>'>
                            <input type='hidden' name='user_group' value='<?php echo $user_group; ?>'>
                            <input type='hidden' name='is_cloud' value='<?php echo $is_cloud; ?>'>	
							<input type='hidden' name='id_campanha' value='<?php echo $id_campanha; ?>'>
							<input type='hidden' name='nome_campanha' value='<?php echo $nome_campanha; ?>'>
							<input type='hidden' name='tipo_campanha' value='<?php echo $tipo_campanha; ?>'>
							<table>
								<tr>
									<td style='text-align:left;' colspan=3>Adicionar Nova Pausa</td>
								</tr>
								<tr>
									<td colspan='3'>&nbsp;</td>
								</tr>
								<tr>
									<td style='width:375px'>
									<div class=cc-mstyle style='height:28px;'>
										<p>
											Nome da Pausa
										</p>
									</div></td><td>
									<input type=text style='text-align:center' id='nova_pausa' name='nova_pausa' size=40 maxlength=40 value='' >
									</td><td style=width:30px></td>
								</tr>
							</table>

							<br>
							<table>
								<tr>
									<td>
										<span style='cursor:pointer;float:right;' onclick="CreatePauseCode();">
											Adicionar
										<img style="vertical-align: middle;" src='../../images/icons/shape_square_add_32.png' />
									</span></td>
								</tr>
							</table>
							</table>
						</form>
					</div>

					<?php } 
    #############################################################
	### 3_CONFIG_FEEDBACK_CODES
	#############################################################
	if ($navigation == '3_CONFIG_FEEDBACK_CODES') {
		
        

            $query="SELECT distinct status, status_name, human_answered, scheduled_callback FROM vicidial_campaign_statuses $whereLOGallowed_campaignsSQL GROUP BY status_name";
			
            $query = mysql_query($query,$link) or die(mysql_error());
            for ($i=0;$i<mysql_num_rows($query);$i++)
            {
            $row = mysql_fetch_assoc($query);
            $row['status'] = strtoupper($row['status']);
            $status_options.= "<input class='check_feedbacks' type=checkbox name=opcoes_feedbacks[] value='$row[status]#_#$row[status_name]#_#$row[human_answered]#_#$row[scheduled_callback]' id='$row[status]'><label style='display:inline;' for='$row[statusd]'> $row[status_name]</label> <br>";
            }
            if(!mysql_num_rows($query)){
                $status_options .= "<br>Não existem Feedbacks definidos.";
            }



?>
				<script>
					function Next() {
					  if ($('.check_feedbacks:checked').length===0) {
					  	alert("Tem de escolher pelo menos um Feedback.");
					  }
					  else
					  {
					  	$('#form_feedbacks').submit();
					  };
					  
					}
				
				
				feedback_counter = 0;
				function CreateFeedback() {
				  if ($("#novo_feedback").val()=="") {
				  	alert("Tem de preencher pelo menos o Nome do Feedback.")
				  }else
				  {
				  	$.ajax({
						type: "POST",
						url: "_requests.php",
						data: {action: "get_feedback_counter", counter: feedback_counter},
						error: function()
						{
							alert("Ocorreu um Erro.");
						},
						success: function(data)	
						{
							//alert(data);
							var feed_id = data;
							var feed_name = $("#novo_feedback").val();
							if($("#is_callback").prop("checked")) { var is_callback="Y"; } else { var is_callback ="N"; }
							if($("#is_human").prop("checked")) { var is_human="Y"; } else { var is_human ="N"; }
							
							$("#td-feed-list").append("<input class='check_feedbacks' type=checkbox name=opcoes_feedbacks[] value='"+ feed_id +"#_#"+ feed_name +"#_#" + is_human + "#_#"+ is_callback +"' id='"+ feed_id +"'><label style='display:inline;' for='" + feed_id + "'> "+feed_name+"</label> <br>");
							$("#novo_feedback").val("");
							$("#is_callback").prop("checked", false);
							$("#is_human").prop("checked", false)
							feedback_counter++;
							
						}
					});
				  };
				}
				</script>
				<div class='cc-mstyle'>
					<table>
						<tr>
							<td id='icon32'><img src='../../images/icons/wizard.png' /></td>
							<td id='submenu-title' style='width:350px'> Configuração | Definição de Feedbacks</td>
							<td style='text-align:left'></td>
						</tr>
					</table>
				</div>

				<div id='work-area' style='min-height:200px'>
					<br>
					<br>
					<div class='cc-mstyle' style='border:none'>
						<form action='campaign_create.php' method='POST' id='form_feedbacks' >
 							<input type='hidden' name='3_CONFIG_FEEDBACK_CODES' value='default'>
                            <input type='hidden' name='user' value='<?php echo $user; ?>'>            
                            <input type='hidden' name='user_group' value='<?php echo $user_group; ?>'>
                            <input type='hidden' name='is_cloud' value='<?php echo $is_cloud; ?>'> 
							<input type='hidden' name='id_campanha' value='<?php echo $id_campanha; ?>'>
							<input type='hidden' name='nome_campanha' value='<?php echo $nome_campanha; ?>'>
							<input type='hidden' name='tipo_campanha' value='<?php echo $tipo_campanha; ?>'>
							<input type='hidden' name='pausas_campanha' value='<?php echo $pausas_campanha; ?>'>
							<table>
								<tr>
									<td id="td-feed-list" style='text-align:left'><?php echo $status_options;
									?></td>
								</tr>
								<tr>
									<td colspan='3' >
									<span style="float:right;cursor: pointer;" onclick="Next();">
										Seguinte
										<img style="vertical-align: middle;" src='../../images/icons/shape_square_go_32.png' />
									</span>
									</td>
								</tr>
							</table>
						</form>
						<hr>
						<br>
						<!---------------------------------------------------------
						3_1_CREATE_FEEDBACK_CODES
						---------------------------------------------------------->
						<form action='campaign_create.php' method='POST' id='form_novo_feedback' >
							<input type='hidden' name='3_1_CREATE_FEEDBACK_CODES' value='default'>
							<input type='hidden' name='user' value='<?php echo $user; ?>'>            
                            <input type='hidden' name='user_group' value='<?php echo $user_group; ?>'>
                            <input type='hidden' name='is_cloud' value='<?php echo $is_cloud; ?>'> 
							<input type='hidden' name='id_campanha' value='<?php echo $id_campanha; ?>'>
							<input type='hidden' name='nome_campanha' value='<?php echo $nome_campanha; ?>'>
							<input type='hidden' name='tipo_campanha' value='<?php echo $tipo_campanha; ?>'>
							<input type='hidden' name='pausas_campanha' value='<?php echo $pausas_campanha; ?>'>
							
							<table>
								<tr>
									<td colspan='3' style='text-align:left;'>Adicionar Novo Feedback</td>
								</tr>
								<tr>
									<td colspan='3'>&nbsp;</td>
								</tr>
								<tr>
									<td style='width:375px'>
									<div class='cc-mstyle' style='height:28px;'>
										<p>
											Nome do Feedback
										</p>
									</div>
									</td>
									<td>
										<input type='text' style='text-align:center' name='novo_feedback' value='' id="novo_feedback">
									</td>
									<td style='width:30px'></td>
									</tr>
									<tr>
										<td style='width:375px'>
											<div class='cc-mstyle' style='height:28px;'>
												<p>
													Atributos
												</p>
											</div>
										</td>
										<td>
											<input type=checkbox name="is_callback" id="is_callback"><label for="is_callback" style="display: inline;"> Callback</label> 
										<!--	<input type=checkbox name="is_human" id="is_human"><label for="is_human" style="display: inline;"> Resposta Humana</label> -->
										</td>
									</tr>
								<tr>
									<td colspan='3'>&nbsp;</td>
								</tr>
								<tr>
									<td colspan='3' >
										<span onclick="CreateFeedback();" style="float: right;cursor: pointer;">
											Adicionar
											<img  style='vertical-align: middle;' src='../../images/icons/shape_square_add_32.png' />
										</span>
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
<?php }
	#############################################################
	### 4_CONFIG_SCRIPTS
	#############################################################
	if ($navigation=='4_CONFIG_SCRIPTS') {
			
       
                $query = "SELECT distinct campaign_id FROM vicidial_lists_fields $whereLOGallowed_campaignsSQL";
                $query = mysql_query($query);
                $camps_count = mysql_num_rows($query);
                for ($i=0;$i<$camps_count;$i++)
                {
                    $camps = mysql_fetch_row($query);
                    if ($camps_count == 1) 
                        {
                        $camps_IN = "'".$camps[0]."'"; 
                    }
                elseif ($camps_count-1 == $i)
                    {
                    $camps_IN .= "'".$camps[0]."'"; 
                    }   
                else
                    {
                    $camps_IN .= "'".$camps[0]."',";
                    }
                }   
                $query = "SELECT campaign_name,campaign_id FROM vicidial_campaigns WHERE campaign_id IN($camps_IN)";
                $query = mysql_query($query);
                for ($i=0;$i<mysql_num_rows($query);$i++)
                {
                    $row=mysql_fetch_assoc($query);
					$row['campaign_id'] = strtoupper($row['campaign_id']);
                    $select_options .= "<option value=$row[campaign_id]>$row[campaign_name]</option>"; 
                } 
                
           
            /*    $query = "SELECT distinct campaign_id FROM vicidial_lists_fields WHERE campaign_id LIKE '%$user_group%'";
                $query = mysql_query($query);
                $camps_count = mysql_num_rows($query);
                for ($i=0;$i<$camps_count;$i++)
                {
                    $camps = mysql_fetch_row($query);
                    if ($camps_count == 1) 
                        {
                        $camps_IN = "'".$camps[0]."'"; 
                    }
                elseif ($camps_count-1 == $i)
                    {
                    $camps_IN .= "'".$camps[0]."'"; 
                    }   
                else
                    {
                    $camps_IN .= "'".$camps[0]."',";
                    }
                }   
                $query = "SELECT campaign_name,campaign_id FROM vicidial_campaigns WHERE campaign_id IN($camps_IN)";
                $query = mysql_query($query);
                for ($i=0;$i<mysql_num_rows($query);$i++)
                {
                    $row=mysql_fetch_assoc($query);
                    $select_options .= "<option value=$row[campaign_id]>$row[campaign_name]</option>"; 
                } */
            
					
			
		
	 	?>
	 	<script>
					function Next() {
					  	$('#form_scripts').submit();
					}
				</script>
				<div class='cc-mstyle'>
					<table>
						<tr>
							<td id='icon32'><img src='../../images/icons/wizard.png' /></td>
							<td id='submenu-title' style='width:350px'> Configuração | Scripts</td>
							<td style='text-align:left'></td>
						</tr>
					</table>
				</div>
				

				<div id='work-area' style='min-height:200px'>
					<br>
					<br>
					<div class='cc-mstyle' style='border:none'>
						
					<form action='campaign_create.php' method='POST' id='form_scripts' >
							<input type='hidden' name='4_CONFIG_SCRIPTS' value='default'>
							<input type='hidden' name='user' value='<?php echo $user; ?>'>            
                            <input type='hidden' name='user_group' value='<?php echo $user_group; ?>'>
                            <input type='hidden' name='is_cloud' value='<?php echo $is_cloud; ?>'> 
							<input type='hidden' name='id_campanha' value='<?php echo $id_campanha; ?>'>
							<input type='hidden' name='nome_campanha' value='<?php echo $nome_campanha; ?>'>
							<input type='hidden' name='tipo_campanha' value='<?php echo $tipo_campanha; ?>'>
							<input type='hidden' name='pausas_campanha' value='<?php echo $pausas_campanha; ?>'>
							<input type='hidden' name='feedbacks_campanha' value='<?php echo $feedbacks_campanha; ?>'>
							<table>
								<tr>
								<td>
								<label for=script_camp>Copiar Script da Campanha</label>
								<select name='opcoes_script' id='opcoes_script'>
									<option value='naocopiar'>Não Copiar</option>
									<?php echo $select_options; ?>
								</select>
								</td>
								</tr>
								<tr>
								<td>
									<span style="float:right; cursor:pointer;" onclick="Next();">
										Seguinte
										<img style="vertical-align: middle;" src='../../images/icons/shape_square_go_32.png' />
									</span>
								</td>
								</tr>
							</table>
					</form>
						</div>
						
					</div>
				</div>
		
	<?php } 
	#############################################################
	### 5_SHOW_DATA_AND_CONFIRM
	#############################################################
	if ($navigation=='5_SHOW_DATA_AND_CONFIRM') {
		
	switch($tipo_campanha){
        case "ratio": $tipo_campanha_edit = "Rácio"; break;
        case "manual": $tipo_campanha_edit = "Manual"; break;
	}
	
    $temp1_e_pausas_campanha = explode("|_|",$pausas_campanha);
    for($i=0;$i<count($temp1_e_pausas_campanha);$i++){
        $temp2_e_pausas_campanha[$i] = explode("#_#",$temp1_e_pausas_campanha[$i]);
        for($o=0;$o<count($temp2_e_pausas_campanha);$o++){
            $nome_pausa[$o] = $temp2_e_pausas_campanha[$o][1];
        }
    }

    $temp1_e_feedbacks_campanha = explode("|_|",$feedbacks_campanha);
    for($i=0;$i<count($temp1_e_feedbacks_campanha);$i++){
        $temp2_e_feedbacks_campanha[$i] = explode("#_#",$temp1_e_feedbacks_campanha[$i]);
        for($o=0;$o<count($temp2_e_feedbacks_campanha);$o++){
            $nome_feedback[$o] = $temp2_e_feedbacks_campanha[$o][1];
        }
    }
    
    if($script_campanha=="naocopiar")
    {
        $script_campanha_edit= "<i>Sem cópia de Script</i>";
    }
    else
    {
        $query = "SELECT campaign_name FROM vicidial_campaigns WHERE campaign_id='$script_campanha'";
        $query = mysql_query($query); 
        $row = mysql_fetch_row($query);
        $script_campanha_edit=$row[0];       
    }
   // echo "<br><br><br>";
    
   // print_r($nome_pausa);
    
	?>	
        <script>
        function Next() {
            $('#form_confirm').submit();
        }
        </script>
        <form action='campaign_create.php' method='POST' id='form_confirm' >
        <input type='hidden' name='5_1_SUBMIT_DATA' value='default'>
        <input type='hidden' name='user' value='<?php echo $user; ?>'>            
        <input type='hidden' name='user_group' value='<?php echo $user_group; ?>'>
        <input type='hidden' name='is_cloud' value='<?php echo $is_cloud; ?>'> 
        <input type='hidden' name='id_campanha' value='<?php echo $id_campanha; ?>'>
        <input type='hidden' name='nome_campanha' value='<?php echo $nome_campanha; ?>'>
        <input type='hidden' name='tipo_campanha' value='<?php echo $tipo_campanha; ?>'>
        <input type='hidden' name='pausas_campanha' value='<?php echo $pausas_campanha; ?>'>
        <input type='hidden' name='feedbacks_campanha' value='<?php echo $feedbacks_campanha; ?>'>
        <input type='hidden' name='script_campanha' value='<?php echo $script_campanha; ?>'>
        </form>
        
        <div class='cc-mstyle'>
        <table>
            <tr>
                <td id='icon32'><img src='../../images/icons/wizard.png' /></td>
                <td id='submenu-title' style='width:350px'> Configuração | Confirmação de Dados</td>
                <td style='text-align:left'></td>
            </tr>
        </table>
        </div>
        
        
        <div id='work-area' style='min-height:200px'>
        <br>
        <br>
        <div class='cc-mstyle' style='border:none'>
        
        <div>
            <font size='2' color=grey><i>Por favor confirme os dados antes de criar a nova Campanha.</i></font>
            <span style="float:right; cursor:pointer;" onclick="Next();">
            <font color='black'>Criar Campanha</font>
            <img style="vertical-align: middle;" src='../../images/icons/shape_square_go_32.png' />
            </span>    
            
        </div>
        <br><br>
        <table border=1>
            <tr>
                <td>
                    <b>Nome da Campanha:</b>
                </td>
                <td>
                    <?php echo $nome_campanha; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Método de Marcação:</b>
                </td>
                <td>
                    <?php echo $tipo_campanha_edit; ?>
                </td>
            </tr>
            <tr>
                <td>
                    <b>Pausas da Campanha:</b>
                </td>
                <td>
                    <?php echo count($nome_pausa); ?>
                </td>
            </tr>
        <?php
        
        for($p=0;$p<count($nome_pausa);$p++)
        {
            echo "
            <tr>
            <td>Pausa #$p</td><td>$nome_pausa[$p]</td>
            </tr>
            
            ";
        }
        
        
        ?>
        
        <tr>
            <td><b>Feedbacks da Campanha:</b></td>
            <td><?php echo count($nome_feedback); ?></td>
        </tr>
        
                <?php
        
        for($p=0;$p<count($nome_feedback);$p++)
        {
            echo "
            <tr>
            <td>Feedback #$p</td><td>$nome_feedback[$p]</td>
            </tr>
            
            ";
        }
        
        
        ?>
        <tr>
            <td>
                <b>Copiar Script da Campanha:</b>
            </td>
            <td>
                <?php echo $script_campanha_edit; ?>
            </td>
        </tr>


		</table>
	</div>
	</div>
	<?php }

#FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
#END FOOTER
?>

