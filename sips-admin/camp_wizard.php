<?php 
#HEADER
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);
$header.="ini/header.php";
require($header);
#END HEADER

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
			
			$query = "SELECT campaign_id FROM vicidial_campaigns WHERE campaign_id LIKE '%$user_group%'";
			$query = mysql_query($query, $link);
			$num = 0;
			if (!mysql_num_rows($query) < 1) {
				for ($i = 0; $i < mysql_num_rows($query); $i++) {
					$row = mysql_fetch_assoc($query);
					$exp_id = explode("_", $row['campaign_id']);
					if ($exp_id[2] > $num) {$num = $exp_id[2];
					}
				}
			}

			$id_campanha  = "c_" . $user_group . "_" . ($num + 1);
            $id_campanha = strtoupper($id_campanha);
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

			$query = "SELECT pause_code_id from sips_pause_codes_default where pause_code_id LIKE '%$user_group%'";
			$query = mysql_query($query, $link);
			if (mysql_num_rows($query) < 1) { $num = 0;
			} else {	$num = 0;
				for ($i = 0; $i < mysql_num_rows($query); $i++) {
					$row = mysql_fetch_assoc($query);
					$exp_id = explode("_", $row['pause_code_id']);
					if ($exp_id[2] > $num) {$num = $exp_id[2];
					}

				}
			}
			
			
			$id_pausa = "p_" . $user_group . "_" . ($num + 1);
            $id_pausa = strtoupper($id_pausa);
			$query = "insert into sips_pause_codes_default (pause_code_id,pause_code_name,user) values ('$id_pausa','$nova_pausa','$user')";
			$query = mysql_query($query, $link) or die(mysql_error());
			


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

			$query = "SELECT status_code_id from sips_status_codes_default where status_code_id LIKE '%$user_group%'";
			$query = mysql_query($query, $link);
			if (mysql_num_rows($query) < 1) { $num = 0;
			} else {	$num = 0;
				for ($i = 0; $i < mysql_num_rows($query); $i++) {
					$row = mysql_fetch_assoc($query);
					$exp_id = explode("_", $row['status_code_id']);
					if ($exp_id[2] > $num) {$num = $exp_id[2];
					}

				}
			}

			$id_feedback = "s_" . $user_group . "_" . ($num + 1);
			$nome_feedback = $_POST['novo_feedback'];

			$query = "insert into sips_status_codes_default (status_code_id,status_code_name,user,human_answer,callback) 
						values ('$id_feedback','$nome_feedback','$user','$is_human', '$is_callback')";
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

            $insert .= "INSERT INTO vicidial_pause_codes (pause_code,pause_code_name,campaign_id) VALUES ('$pause_code_id','$pause_code_name','$id_campanha'); ";
            
        }
        
        //cria os feedback
        $count_feedbacks = explode("|_|",$feedbacks_campanha);
        for ($i = 0; $i < count($count_feedbacks); $i++) {

            $explode = explode("#_#", $count_feedbacks[$i]);
            $status_code_id = $explode[0];
            $status_code_name = $explode[1];
            $is_human = ($explode[2]==1)?"Y":"N";
            $is_callback = ($explode[3]==1)?"Y":"N";

            $insert .= "INSERT INTO vicidial_campaign_statuses (status,status_name,campaign_id,human_answered,scheduled_callback,selectable) VALUES ('$status_code_id','$status_code_name','$id_campanha','$is_human','$is_callback','Y'); ";
            
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
        if($is_cloud=='0') {
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
				<td id="icon32"><img src='../images/icons/wizard.png' /></td>
				<td id='submenu-title' style='width:350px'> Configuração | Definições da Campanha</td>
				<td style='text-align:left'></td>
			</tr>
		</table>
	</div>

	<div id='work-area' style='min-height:200px'>
		<br>
		<br>
		<div class='cc-mstyle' style='border:none'>
			<form id='form_campanha' action='camp_wizard.php' method='POST'>
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
						<img style="vertical-align: middle;" src='../images/icons/shape_square_go_32.png' /></span>
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
	    
        if($is_cloud==0){
            $query = "SELECT * FROM sips_pause_codes_default";
            $query = mysql_query($query,$link) or die(mysql_error());
            for ($i=0;$i<mysql_num_rows($query);$i++)
            {
            $row = mysql_fetch_assoc($query);
            $row['pause_code_id'] = strtoupper($row['pause_code_id']);
            $checkbox_options.= "<input type=checkbox name=opcoes_pausas[] value='$row[pause_code_id]#_#$row[pause_code_name]' id='$row[pause_code_id]' ><label style='display:inline;' for='$row[pause_code_id]'> $row[pause_code_name]</label><br>";
            }
            if(!mysql_num_rows($query)){
                $checkbox_options .= "<br>Não existem Códigos de Pausa definidos.";
            }
        } else {
            $query = "SELECT * FROM sips_pause_codes_default WHERE pause_code_id LIKE '%$user_group%'";
            $query = mysql_query($query,$link) or die(mysql_error());
            for ($i=0;$i<mysql_num_rows($query);$i++)
            {
            $row = mysql_fetch_assoc($query);
            $row['pause_code_id'] = strtoupper($row['pause_code_id']);
            $checkbox_options.= "<input type=checkbox name=opcoes_pausas[] value='$row[pause_code_id]#_#$row[pause_code_name]' id='$row[pause_code_id]' ><label style='display:inline;' for='$row[pause_code_id]'> $row[pause_code_name]</label><br>";
            }
            if(!mysql_num_rows($query)){
                $checkbox_options .= "<br>Não existem Códigos de Pausa definidos.";
            }
        }
        
		
 			?>
				<script>
					function Next() {
					  if ($('input[name=opcoes_pausas[]]:checked').length===0) {
					  	alert("Tem de escolher pelo menos um código de pausa.");
					  }
					  else
					  {
					  	$('#form_pausas').submit();
					  };
					  
					}
					
					function CreatePauseCode() {
					  if ($('#nova_pausa').val()==='')
					  {
					  	alert('O campo "Nome da Pausa", não pode estar vazio.');
					  }else
					  {
					  	$('#form_cria_pausa').submit()
					  }
					}
				</script>
				<div class=cc-mstyle>
					<table>
						<tr>
							<td id='icon32'><img src='../images/icons/wizard.png' /></td>
							<td id='submenu-title' style='width:350px'> Configuração | Definição de Pausas</td>
							<td style='text-align:left'></td>
						</tr>
					</table>
				</div>

				<div id=work-area style='min-height:200px'>
					<br>
					<br>
					<div class=cc-mstyle style='border:none'>
						<form id='form_pausas' action='camp_wizard.php' method='POST'>
							<input type='hidden' name='2_CONFIG_PAUSE_CODES' value='default'>
							<input type='hidden' name='user' value='<?php echo $user; ?>'>
                            <input type='hidden' name='user_group' value='<?php echo $user_group; ?>'>
                            <input type='hidden' name='is_cloud' value='<?php echo $is_cloud; ?>'>
							<input type='hidden' name='id_campanha' value='<?php echo $id_campanha; ?>'>
							<input type='hidden' name='nome_campanha' value='<?php echo $nome_campanha; ?>'>
							<input type='hidden' name='tipo_campanha' value='<?php echo $tipo_campanha; ?>'>
							<table>
								<tr>
									<td style='text-align:left'><?php echo $checkbox_options; ?></td>
								</tr>
							</table>

							<br>
							<table>
								<tr>
									<td style='text-align:right'>
										<span style='cursor:pointer;float:right;' onclick="Next();">
											Seguinte
										<img style="vertical-align: middle;" src='../images/icons/shape_square_go_32.png' />
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
						<form id='form_cria_pausa' action='camp_wizard.php' method='POST'>
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
										<img style="vertical-align: middle;" src='../images/icons/shape_square_add_32.png' />
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
		
        
        if($is_cloud==0){
            $query="SELECT * FROM sips_status_codes_default";
            $query = mysql_query($query,$link) or die(mysql_error());
            for ($i=0;$i<mysql_num_rows($query);$i++)
            {
            $row = mysql_fetch_assoc($query);
            $row['status_code_id'] = strtoupper($row['status_code_id']);
            $status_options.= "<input type=checkbox name=opcoes_feedbacks[] value='$row[status_code_id]#_#$row[status_code_name]#_#$row[human_answer]#_#$row[callback]' id='$row[status_code_id]'><label style='display:inline;' for='$row[status_code_id]'> $row[status_code_name]</label> <br>";
            }
            if(!mysql_num_rows($query)){
                $status_options .= "<br>Não existem Feedbacks definidos.";
            }
        } else {
            $query="SELECT * FROM sips_status_codes_default WHERE status_code_id LIKE '%$user_group%'";
            $query = mysql_query($query,$link) or die(mysql_error());
            for ($i=0;$i<mysql_num_rows($query);$i++)
            {
            $row = mysql_fetch_assoc($query);
            $row['status_code_id'] = strtoupper($row['status_code_id']);
            $status_options.= "<input type=checkbox name=opcoes_feedbacks[] value='$row[status_code_id]#_#$row[status_code_name]#_#$row[human_answer]#_#$row[callback]' id='$row[status_code_id]'><label style='display:inline;' for='$row[status_code_id]'> $row[status_code_name]</label> <br>";
            }
            if(!mysql_num_rows($query)){
                $status_options .= "<br>Não existem Feedbacks definidos.";
            }
        
            
        }

?>
				<script>
					function Next() {
					  if ($('input[name=opcoes_feedbacks[]]:checked').length===0) {
					  	alert("Tem de escolher pelo menos um Feedback.");
					  }
					  else
					  {
					  	$('#form_feedbacks').submit();
					  };
					  
					}
					
				function CreateFeedback() {
				  if ($("#novo_feedback").val()=="") {
				  	alert("Tem de preencher pelo menos o Nome do Feedback.")
				  }else
				  {
				  	$('#form_novo_feedback').submit();
				  };
				}
				</script>
				<div class='cc-mstyle'>
					<table>
						<tr>
							<td id='icon32'><img src='../images/icons/wizard.png' /></td>
							<td id='submenu-title' style='width:350px'> Configuração | Definição de Feedbacks</td>
							<td style='text-align:left'></td>
						</tr>
					</table>
				</div>

				<div id='work-area' style='min-height:200px'>
					<br>
					<br>
					<div class='cc-mstyle' style='border:none'>
						<form action='camp_wizard.php' method='POST' id='form_feedbacks' >
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
									<td style='text-align:left'><?php echo $status_options;
									?></td>
								</tr>
								<tr>
									<td colspan='3' >
									<span style="float:right;cursor: pointer;" onclick="Next();">
										Seguinte
										<img style="vertical-align: middle;" src='../images/icons/shape_square_go_32.png' />
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
						<form action='camp_wizard.php' method='POST' id='form_novo_feedback' >
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
											<input type=checkbox name="is_callback" id=is_callback><label for=is_callback style="display: inline;"> Callback</label> 
											<input type=checkbox name="is_human" id=is_human><label for=is_human style="display: inline;"> Resposta Humana</label> 
										</td>
									</tr>
								<tr>
									<td colspan='3'>&nbsp;</td>
								</tr>
								<tr>
									<td colspan='3' >
										<span onclick="CreateFeedback();" style="float: right;cursor: pointer;">
											Adicionar
											<img  style='vertical-align: middle;' src='../images/icons/shape_square_add_32.png' />
										</span>
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
<?php }//fim do if
	#############################################################
	### 4_CONFIG_SCRIPTS
	#############################################################
	if ($navigation=='4_CONFIG_SCRIPTS') {
			
            if($is_cloud==0){
                $query = "SELECT distinct campaign_id FROM vicidial_lists_fields";
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
                } 
                
            } else {
                $query = "SELECT distinct campaign_id FROM vicidial_lists_fields WHERE campaign_id LIKE '%$user_group%'";
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
                }
            }
					
			
		
	 	?>
	 	<script>
					function Next() {
					  	$('#form_scripts').submit();
					}
				</script>
				<div class='cc-mstyle'>
					<table>
						<tr>
							<td id='icon32'><img src='../images/icons/wizard.png' /></td>
							<td id='submenu-title' style='width:350px'> Configuração | Scripts</td>
							<td style='text-align:left'></td>
						</tr>
					</table>
				</div>
				

				<div id='work-area' style='min-height:200px'>
					<br>
					<br>
					<div class='cc-mstyle' style='border:none'>
						
					<form action='camp_wizard.php' method='POST' id='form_scripts' >
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
										<img style="vertical-align: middle;" src='../images/icons/shape_square_go_32.png' />
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
        <form action='camp_wizard.php' method='POST' id='form_confirm' >
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
                <td id='icon32'><img src='../images/icons/wizard.png' /></td>
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
            <img style="vertical-align: middle;" src='../images/icons/shape_square_go_32.png' />
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

