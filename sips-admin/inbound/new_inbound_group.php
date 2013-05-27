<?php
function help($where, $text) {
    return '<a onclick="openNewWindow(\'../admin.php?ADD=99999#' . $where . '\')">' . $text . '</a>';
}
$PHP_AUTH_USER=$_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW=$_SERVER["PHP_AUTH_PW"];

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require "../functions.php";
require(ROOT . "ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>Criar DDI</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/jquery/jsdatatable/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap-colorpicker.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/colorpicker.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <style>
        .chzn-select{
            width: 350px;
        }
        </style>
    </head>
    <body>
        
<?php
if ($ADD==2111)
	{
	##### BEGIN ID override optional section, if enabled it increments user by 1 ignoring entered value #####
	$stmt = "SELECT value FROM vicidial_override_ids where id_table='vicidial_inbound_groups' and active='1';";
	$rslt=mysql_query($stmt, $link);
	$voi_ct = mysql_num_rows($rslt);
	if ($voi_ct > 0)
		{
		$row=mysql_fetch_row($rslt);
		$group_id = ($row[0] + 1);

		$stmt="UPDATE vicidial_override_ids SET value='$group_id' where id_table='vicidial_inbound_groups' and active='1';";
		$rslt=mysql_query($stmt, $link);
		}
	##### END ID override optional section #####

	$stmt="SELECT count(*) from vicidial_inbound_groups where group_id='$group_id';";
	$rslt=mysql_query($stmt, $link);
	$row=mysql_fetch_row($rslt);
	if ($row[0] > 0)
		{ ?>
        <script>
            $(function(){makeAlert("#wr","Grupo não criado","Já existe um Grupo Inbound com o mesmo ID.",4,true,false);});
        </script>
            <?php }
	else
		{
		$stmt="SELECT count(*) from vicidial_campaigns where campaign_id='$group_id';";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		if ($row[0] > 0)
			{ ?>
        <script>
            $(function(){makeAlert("#wr","Grupo não criado","Já existe uma Campanha com o mesmo ID.",4,true,false);});
        </script>
            <?php }
		else
			{
			if ( (strlen($group_id) < 2) or (strlen($group_name) < 2)  or (strlen($group_color) < 2) or (strlen($group_id) > 20) or (eregi(' ',$group_id)) or (eregi("\-",$group_id)) or (eregi("\+",$group_id)) )
				{
                            { ?>
                    <script>
                        $(function(){makeAlert("#wr","Grupo não criado","Reveja os dados inseridos.\nO ID deve Conter 2 a 20 Caracteres.\nO nome deve ter no minimo 2 caracteres.\nA Cor é obrigatória.",4,true,true);});
                    </script>
                        <?php }
				}
			else
				{
				$stmt="INSERT INTO vicidial_inbound_groups (group_id,group_name,group_color,active,web_form_address,voicemail_ext,next_agent_call,fronter_display,ingroup_script,get_call_launch,web_form_address_two,start_call_url,dispo_call_url,add_lead_url) values('$group_id','$group_name','$group_color','$active','" . mysql_real_escape_string($web_form_address) . "','$voicemail_ext','$next_agent_call','$fronter_display','$script_id','$get_call_launch','','','','');";
				$rslt=mysql_query($stmt, $link);

				$stmtA="INSERT INTO vicidial_campaign_stats (campaign_id) values('$group_id');";
				$rslt=mysql_query($stmtA, $link);

				

				### LOG INSERTION Admin Log Table ###
				$SQL_log = "$stmt|";
				$SQL_log = ereg_replace(';','',$SQL_log);
				$SQL_log = addslashes($SQL_log);
				$stmt="INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='INGROUPS', event_type='ADD', record_id='$group_id', event_code='ADMIN ADD INBOUND GROUP', event_sql=\"$SQL_log\", event_notes='';";
				if ($DB) {echo "|$stmt|\n";}
				$rslt=mysql_query($stmt, $link);
				
				echo "<script type='text/javascript'> 
                                        window.location='list_inbound.php?success=1';
                                      </script>\n";
				
				}
			}
		}
	}

		##### BEGIN ID override optional section, if enabled it increments user by 1 ignoring entered value #####
		$stmt = "SELECT count(*) FROM vicidial_override_ids where id_table='vicidial_inbound_groups' and active='1';";
		$rslt=mysql_query($stmt, $link);
		$voi_ct = mysql_num_rows($rslt);
		if ($voi_ct > 0)
			{
			$row=mysql_fetch_row($rslt);
			$voi_count = "$row[0]";
			}
		##### END ID override optional section #####
                        $voice_mail_list = array();
        $query = "SELECT voicemail_id,fullname,email,extension from phones where active='Y'  $LOGadmin_viewable_groupsSQL order by voicemail_id ASC";
        $rslt = mysql_query($query, $link);
        while ($row1 = mysql_fetch_row($rslt)) {
            $voice_mail_list[$row1[0]] = "$row1[3] - $row1[1]";
        }
?>
 
		 <div class=content>
                     
		<form action=new_inbound_group.php method=POST>
		<input type=hidden name=ADD value=2111>
		
                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Criar Grupo Inbound</div>
                        <div class="pull-right"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-content">
                        <div id="wr"></div>
                        <div class="formRow op fix <?=($voi_count > 0)?"hide":""?>">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-group_id","ID do Grupo")?>:</label>
                            <div class="formRight">
                                <?=($voi_count > 0)?"":"<input type=text name=group_id class='span' maxlength=20>"?>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-group_name","Nome do Grupo")?>:</label>
                            <div class="formRight">
                                <input type=text name=group_name  class="span" maxlength=30>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-group_color","Cor do Grupo")?>:</label>
                            <div class="formRight">
                                <div class="input-append color" data-color="<?= $row[2] ?>" data-color-format="hex" id="colorp">
                                    <input type="text" class="span2" name=group_color value="<?= $row[2] ?>" >
                                    <span class="add-on"><i style="background-color: <?= $row[2] ?>"></i></span>
                                  </div>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-active","Activo")?>:</label>
                            <div class="formRight">
                                <select name=active>
                                    <option value="Y">Sim</option>
                                    <option value="N">Não</option>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix hide">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-web_form_address","Web Form")?>:</label>
                            <div class="formRight">
                                <input type=text name=web_form_address class="span" maxlength=1055 value="<?=$web_form_address?>">
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-voicemail_ext","Voicemail")?>:</label>
                            <div class="formRight">
                                <select name=voicemail_ext id=voicemail_ext class="chzn-select">
                                    <?=populate_options($voice_mail_list, $voicemail_ext)?>
                                </select>  
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-next_agent_call","Proxima Chamada")?>:</label>
                            <div class="formRight">
                                <select class="span" name=next_agent_call>
                                    <option value='random' >Aleatório</option>
                                    <option value='oldest_call_start'>Inicio de chamada mais antiga</option>
                                    <option value='oldest_call_finish'>Fim de chamada mais antigo</option>
                                    <option value='overall_user_level'>Nível de utilizador geral</option>
                                    <option value='inbound_group_rank'>Rank de Grupo de Inbound</option>
                                    <option value='campaign_rank'>Rank de Campanha</option>
                                    <option value='fewest_calls'>Menos chamadas atendidas (Inbound)</option>
                                    <option value='fewest_calls_campaign'>Menos chamadas atendidas (Campanha)</option>
                                    <option value='longest_wait_time'>Maior tempo de espera</option>
                                    <option value='ring_all'>Tocar em todos</option>
                                 </select>
                            </div>
                        </div> 
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-voicemail_ext","Mostrar Número Linha Inbound")?>:</label>
                            <div class="formRight">
                                <select name=fronter_display>
                                    <option value="Y">Sim</option>
                                    <option value="N">Não</option>
                                </select>
                            </div>
                        </div> 
                        <div class="formRow op fix hide">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-ingroup_script","Script")?>:</label>
                            <div class="formRight">
                                <select name=script_id>
                                <?=$scripts_list?>
                                </select>
                            </div>
                        </div> 
                        
                        <?php
                        $eswHTML = '';
                        $cfwHTML = '';
                        if ($SSenable_second_webform > 0) {
                            $eswHTML = '<option>WEBFORMTWO</option>';
                        }
                        if ($SScustom_fields_enabled > 0) {
                            $cfwHTML = '<option>FORM</option>';
                        }
                        ?>
                        
                        <div class="formRow op fix">
                            <label data-t="tooltip" title=""><?=help("vicidial_inbound_groups-get_call_launch","Inicio Chamada abrir Script")?>:</label>
                            <div class="formRight">
                                <select  name=get_call_launch>
                                    <option value='NONE'>Não</option>
                                    <option value='FORM'>Sim</option>
                                </select>
                            </div>
                        </div> 
                <div class="clear"></div>
                    <div class="seperator_dashed"></div>
                    <div class="grid-content">
                    <p class="text-right">
                        <button class="btn btn-success">Adicionar</button>
                    </p>
                    </div>
                
                    <script>
                    $(function(){
                        $('#colorp').colorpicker();
                        $(".chzn-select").chosen({no_results_text: "Não foi encontrado."});
                    });
                    </script>
	<?php	
			
#FOOTER
$footer=ROOT."ini/footer.php";
require($footer);
?>	