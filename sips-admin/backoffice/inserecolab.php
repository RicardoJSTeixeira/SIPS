<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>SIPS - Insere Colaborador</title>

        <?php
        $nome = $_POST['nome'];
        $morada = $_POST['morada'];
        $porta = $_POST['porta'];
        $andar = $_POST['andar'];
        $codpostal = $_POST['codpostal'];
        $codrua = $_POST['codrua'];
        $local = $_POST['local'];
        $tlf = $_POST['tlf'];
        $tlml = $_POST['tlml'];
        $email = $_POST['email'];
        $bi = $_POST['bi'];
        $nif = $_POST['nif'];
        $segsocial = $_POST['segsocial'];
        $datanasc = $_POST['datanasc'];
        $hablit = $_POST['hablit'];
        $banco = $_POST['banco'];
        $nib = $_POST['nib'];
        $estcivil = $_POST['estcivil'];
        $dependentes = $_POST['dependentes'];
        $horario = $_POST['horario'];
        $expcc = $_POST['expcc'];
        $expvendas = $_POST['expvendas'];
        $expzon = $_POST['expzon'];
        $data = $_POST['data'];
        $grupo = $_POST['usergroup'];
        $user = $_POST['user'];
        $pass = $_POST['password'];

        $usertype = $_POST['usertype'];



        if ($tlf == "") {
            $tlf = 0;
        }
        if ($tlml == "") {
            $tlml = 0;
        }
        if ($segsocial == "") {
            $segsocial = 0;
        }


        if (file_exists("/etc/astguiclient.conf")) {
            $DBCagc = file("/etc/astguiclient.conf");
            foreach ($DBCagc as $DBCline) {
                $DBCline = preg_replace("/ |>|\n|\r|\t|\#.*|;.*/", "", $DBCline);
                if (ereg("^VARDB_server", $DBCline)) {
                    $VARDB_server = $DBCline;
                    $VARDB_server = preg_replace("/.*=/", "", $VARDB_server);
                }
            }
        } else {
            #defaults for DB connection
            $VARDB_server = 'localhost';
        }


        $con = mysql_connect($VARDB_server, "sipsadmin", "sipsps2012");
        if (!$con) {
            die('Não me consegui ligar' . mysql_error());
        }
        mysql_select_db("asterisk", $con);


        if (mysql_num_rows(mysql_query("SELECT user from vicidial_users WHERE user LIKE '$user'"))) {
           ?>
			<script type='text/javascript'>
				alert('Nome de utilizador já existe. Escolha outro por favor');
				history.back();
			</script>
            <?php
        }

        if ($usertype == 'admin') {

            mysql_query("INSERT INTO vicidial_users (
	user ,pass ,full_name ,user_level ,user_group ,phone_login ,phone_pass ,delete_users ,delete_user_groups ,delete_lists ,delete_campaigns ,delete_ingroups ,delete_remote_agents ,load_leads ,campaign_detail ,ast_admin_access ,ast_delete_phones ,delete_scripts ,modify_leads ,hotkeys_active ,change_agent_campaign ,agent_choose_ingroups ,closer_campaigns ,scheduled_callbacks ,agentonly_callbacks ,agentcall_manual ,vicidial_recording ,vicidial_transfers ,delete_filters ,alter_agent_interface_options ,closer_default_blended ,delete_call_times ,modify_call_times ,modify_users ,modify_campaigns ,modify_lists ,modify_scripts ,modify_filters ,modify_ingroups ,modify_usergroups ,modify_remoteagents ,modify_servers ,view_reports ,vicidial_recording_override ,alter_custdata_override ,qc_enabled ,qc_user_level ,qc_pass ,qc_finish ,qc_commit ,add_timeclock_log ,modify_timeclock_log ,delete_timeclock_log ,alter_custphone_override ,vdc_agent_api_access ,modify_inbound_dids ,delete_inbound_dids ,active ,alert_enabled ,download_lists ,agent_shift_enforcement_override ,manager_shift_enforcement_override ,shift_override_flag ,export_reports ,delete_from_dnc ,email ,user_code ,territory ,allow_alerts ,agent_choose_territories ,custom_one ,custom_two ,custom_three ,custom_four ,custom_five ,voicemail_id ,agent_call_log_view_override ,callcard_admin ,agent_choose_blended ,realtime_block_user_info ,custom_fields_modify ,force_change_password ,agent_lead_search_override
	)
	VALUE
	('$user','$pass','$nome','8','$grupo','','','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','DISABLED','NOT_ACTIVE','','0','','','','1','1','1','NOT_ACTIVE','1','1','1','Y','0','1','DISABLED','1','0','1','1','','','','0','','','','','','','','DISABLED','1','1','0','1','N','NOT_ACTIVE'
	)") or die(mysql_error());
        } else {

            mysql_query("INSERT INTO vicidial_users (
	user,
	pass,
	full_name,
	user_level,
	user_group,
	agentonly_callbacks,
	agentcall_manual,
	custom_one)
	VALUE
	('$user', '$pass', '$nome', '1', '$grupo', '1', '1', '$user')") or die(mysql_error());
        }


        // user SIPS

        mysql_select_db("sips", $con);

        $uservici = $user;

        $insercolab = mysql_query("INSERT INTO t_colaborador (
				nome,
				morada,
				codpostal,
				telefone,
				telemovel,
				email,
				bi,
				nif,
				segsocial,
				datanasc,
				hablit,
				banco,
				nib,
				estcivil,
				ndepend,
				htrab,
				ecc,
				evendas,
				ezon,
				idintegra,
				uservici,
				datainsc) VALUES
				
				('$nome',
				'$morada',
				'$codpostal',
				'$tlf',
				'$tlml',
				'$email',
				'$bi',
				'$nif',
				'$segsocial',
				'$datanasc',
				'$hablit',
				'$banco',
				'$nib',
				'$estcivil',
				'$dependentes',
				'$horario',
				'$expcc',
				'$expvendas',
				'$expzon',
				'$idintegra',
				'$uservici',
				'$data');") or die(mysql_error());

        mysql_close($con);

        ?>
                        <script type='text/javascript'>
			alert('Utilizador criado com sucesso!');
			window.location='listausers.php';
		  </script>
        
       
    </head>

    <body>

    </body>
</html>