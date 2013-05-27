<?
#HEADER
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');


$PHP_AUTH_USER=$_SERVER["PHP_AUTH_USER"];
$PHP_AUTH_PW=$_SERVER["PHP_AUTH_PW"];
        
function help($where,$text) {
    return '<a onclick="openNewWindow(\'../admin.php?ADD=99999#'.$where.'\')">'.$text.'</a>';
}

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

//includes
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
        <title>SIPS</title>

        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
    <style>
        [name="did_route"],[name="filter_action"]{
            border-color:rgba(82, 168, 236, 0.8);
        }
        .chzn-select{
            width: 350px;
        }
        #loader{
            background: #f9f9f9;
            top: 0px;
            left: 0px;
            position: absolute;
            height: 100%;
            width: 100%;
            z-index: 2;
        }
        #loader > img{
            position:absolute;
            left:50%;
            top:50%;
            margin-left: -33px;
            margin-top: -33px;
        }
    </style>
    </head>
    <body>
    <?php

if ($ADD == 4311) {


    if ((strlen($did_id) < 1) or (strlen($did_pattern) < 1)) {

        ?>
        <script>
            $(function(){makeAlert("#wr","DID não Modificado","Verifique que a extensão está preenchida.",1,true,false);});
        </script>
            <?php
    } else {
        ?>
        <script>
            $(function(){makeAlert("#wr","DDI Modificado com Sucesso","",4,true,false);});
        </script>
            <?php

        $stmt = "UPDATE vicidial_inbound_dids set did_pattern='$did_pattern',did_description='$did_description',did_active='$did_active',did_route='$did_route',extension='$extension',exten_context='$exten_context',voicemail_ext='$voicemail_ext',phone='$phone',server_ip='$server_ip',user='$user',user_unavailable_action='$user_unavailable_action',user_route_settings_ingroup='$user_route_settings_ingroup',group_id='$group_id',call_handle_method='$call_handle_method',agent_search_method='$agent_search_method',list_id='$list_id',campaign_id='$campaign_id',phone_code='$phone_code',menu_id='$menu_id',record_call='$record_call',filter_inbound_number='$filter_inbound_number',filter_phone_group_id='$filter_phone_group_id',filter_url='$filter_url',filter_action='$filter_action',filter_extension='$filter_extension',filter_exten_context='$filter_exten_context',filter_voicemail_ext='$filter_voicemail_ext',filter_phone='$filter_phone',filter_server_ip='$filter_server_ip',filter_user='$filter_user',filter_user_unavailable_action='$filter_user_unavailable_action',filter_user_route_settings_ingroup='$filter_user_route_settings_ingroup',filter_group_id='$filter_group_id',filter_call_handle_method='$filter_call_handle_method',filter_agent_search_method='$filter_agent_search_method',filter_list_id='$filter_list_id',filter_campaign_id='$filter_campaign_id',filter_phone_code='$filter_phone_code',filter_menu_id='$filter_menu_id',filter_clean_cid_number='$filter_clean_cid_number' where did_id='$did_id';";
        $rslt = mysql_query($stmt, $link);

        ### LOG INSERTION Admin Log Table ###
        $SQL_log = "$stmt|";
        $SQL_log = ereg_replace(';', '', $SQL_log);
        $SQL_log = addslashes($SQL_log);
        $stmt = "INSERT INTO vicidial_admin_log set event_date='$SQLdate', user='$PHP_AUTH_USER', ip_address='$ip', event_section='DIDS', event_type='MODIFY', record_id='$did_id', event_code='ADMIN MODIFY DID', event_sql=\"$SQL_log\", event_notes='';";
        if ($DB) {
            echo "|$stmt|\n";
        }
        $rslt = mysql_query($stmt, $link);
    }
}





$didSQL = "did_id='$did_id'";
if ((strlen($did_id) < 1) and (strlen($did_pattern) > 0)) {
    $didSQL = "did_pattern='$did_pattern'";
}
$stmt = "SELECT did_id,did_pattern,did_description,did_active,did_route,extension,exten_context,voicemail_ext,phone,server_ip,user,user_unavailable_action,user_route_settings_ingroup,group_id,call_handle_method,agent_search_method,list_id,campaign_id,phone_code,menu_id,record_call,filter_inbound_number,filter_phone_group_id,filter_url,filter_action,filter_extension,filter_exten_context,filter_voicemail_ext,filter_phone,filter_server_ip,filter_user,filter_user_unavailable_action,filter_user_route_settings_ingroup,filter_group_id,filter_call_handle_method,filter_agent_search_method,filter_list_id,filter_campaign_id,filter_phone_code,filter_menu_id,filter_clean_cid_number from vicidial_inbound_dids where $didSQL;";
if ($DB) {
    echo "$stmt<br>";
}
$rslt = mysql_query($stmt, $link);
$row = mysql_fetch_row($rslt);
$did_id = $row[0];
$did_pattern = $row[1];
$did_description = $row[2];
$did_active = $row[3];
$did_route = $row[4];
$extension = $row[5];
$exten_context = $row[6];
$voicemail_ext = $row[7];
$phone = $row[8];
$server_ip = $row[9];
$user = $row[10];
$user_unavailable_action = $row[11];
$user_route_settings_ingroup = $row[12];
$group_id = $row[13];
$call_handle_method = $row[14];
$agent_search_method = $row[15];
$list_id = $row[16];
$campaign_id = $row[17];
$phone_code = $row[18];
$menu_id = $row[19];
$record_call = $row[20];
$filter_inbound_number = $row[21];
$filter_phone_group_id = $row[22];
$filter_url = $row[23];
$filter_action = $row[24];
$filter_extension = $row[25];
$filter_exten_context = $row[26];
$filter_voicemail_ext = $row[27];
$filter_phone = $row[28];
$filter_server_ip = $row[29];
$filter_user = $row[30];
$filter_user_unavailable_action = $row[31];
$filter_user_route_settings_ingroup = $row[32];
$filter_group_id = $row[33];
$filter_call_handle_method = $row[34];
$filter_agent_search_method = $row[35];
$filter_list_id = $row[36];
$filter_campaign_id = $row[37];
$filter_phone_code = $row[38];
$filter_menu_id = $row[39];
$filter_clean_cid_number = $row[40];


$stmt = "SELECT campaign_id,campaign_name from vicidial_campaigns $whereLOGallowed_campaignsSQL order by campaign_id";
$rslt = mysql_query($stmt, $link);
$campaigns_to_print = mysql_num_rows($rslt);
$campaigns_list = '';
$o = 0;
while ($campaigns_to_print > $o) {
    $rowx = mysql_fetch_row($rslt);
    $campaigns_list .= "<option value=\"$rowx[0]\" ".(($rowx[0]==$campaign_id)?"SELECTED":"").">$rowx[1]</option>";
    $campaigns_list_filter .= "<option value=\"$rowx[0]\" ".(($rowx[0]==$filter_campaign_id)?"SELECTED":"").">$rowx[1]</option>";
    $o++;
}

##### get in-groups listings for dynamic pulldown
$stmt = "SELECT group_id,group_name from vicidial_inbound_groups order by group_id";
$rslt = mysql_query($stmt, $link);
$Xgroups_to_print = mysql_num_rows($rslt);
$Xgroups_menu = '';
$Xgroups_selected = 0;
$FXgroups_menu = '';
$FXgroups_selected = 0;
$o = 0;
while ($Xgroups_to_print > $o) {
    $rowx = mysql_fetch_row($rslt);
    $Xgroups_menu .= "<option ";
    $FXgroups_menu .= "<option ";
    if ($user_route_settings_ingroup == "$rowx[0]") {
        $Xgroups_menu .= "SELECTED ";
        $Xgroups_selected++;
    }
    if ($filter_user_route_settings_ingroup == "$rowx[0]") {
        $FXgroups_menu .= "SELECTED ";
        $FXgroups_selected++;
    }
    $Xgroups_menu .= "value=\"$rowx[0]\">$rowx[1]</option>";
    $FXgroups_menu .= "value=\"$rowx[0]\">$rowx[1]</option>";
    $o++;
}
if ($Xgroups_selected < 1) {
    $Xgroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>";
} else {
    $Xgroups_menu .= "<option value=\"---NONE---\">Nenhum</option>";
}
if ($FXgroups_selected < 1) {
    $FXgroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>";
} else {
    $FXgroups_menu .= "<option value=\"---NONE---\">Nenhum</option>";
}


##### get in-groups listings for dynamic pulldown
$stmt = "SELECT group_id,group_name from vicidial_inbound_groups where group_id NOT IN('AGENTDIRECT') order by group_id";
$rslt = mysql_query($stmt, $link);
$Dgroups_to_print = mysql_num_rows($rslt);
$Dgroups_menu = '';
$Dgroups_selected = 0;
$FDgroups_menu = '';
$FDgroups_selected = 0;
$o = 0;
while ($Dgroups_to_print > $o) {
    $rowx = mysql_fetch_row($rslt);
    $Dgroups_menu .= "<option ";
    $FDgroups_menu .= "<option ";
    if ($group_id == "$rowx[0]") {
        $Dgroups_menu .= "SELECTED ";
        $Dgroups_selected++;
    }
    if ($filter_group_id == "$rowx[0]") {
        $FDgroups_menu .= "SELECTED ";
        $FDgroups_selected++;
    }
    $Dgroups_menu .= "value=\"$rowx[0]\">$rowx[1]</option>";
    $FDgroups_menu .= "value=\"$rowx[0]\">$rowx[1]</option>";
    $o++;
}
if ($Dgroups_selected < 1) {
    $Dgroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>";
} else {
    $Dgroups_menu .= "<option value=\"---NONE---\">Nenhum</option>";
}
if ($FDgroups_selected < 1) {
    $FDgroups_menu .= "<option SELECTED value=\"---NONE---\">Nenhum</option>";
} else {
    $FDgroups_menu .= "<option value=\"---NONE---\">Nenhum</option>";
}


$stmt="SELECT server_ip,server_description from servers order by server_ip";
	$rsltx=mysql_query($stmt, $link);
	$servers_list=array();

	$o=0;
	while (mysql_num_rows($rsltx) > $o)
		{
		$rowx= mysql_fetch_row($rsltx);
		$servers_list[]=$rowx;
		$o++;
		}
?>
        <div id="loader"><img src="/images/icons/big-loader.gif"/></div>
        <script>
            function openNewWindow(url) 
		{
		window.open (url,"",'width=620,height=300,scrollbars=yes,menubar=yes,address=yes');
		}
        </script>
        <div class=content>


            <form action='edit_did.php' method=POST>
                <input type=hidden name=ADD value=4311>
                <input type=hidden name=did_id value="<?=$did_id?>">

                <div class="grid">
                    <div class="grid-title">
                        <div class="pull-left">Edita DDI</div>
                        <div class="pull-right"></div>
                        <div class="clear"></div>
                    </div>
                    <div class="grid-content">
        <div id="wr"></div>
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="Este é o nº, extensão ou DID que vai executar estrada e vai roteala pelo sistema com esta funcção.<br>Há uma DID padrão reservada que pode usar que é só a palavra -default- sem os traços, que permitirá rotear qualquer chamada que não coincide com qualquer outro nº, extensão ou DID configurada.">
                                Extensão da DID: 
                            </label>
                            <div class="formRight">
                                <input type=text name=did_pattern class="span" maxlength=50 value="<?= $did_pattern ?>">
                            </div>
                        </div>   
                        <div class="formRow op fix">
                            <label>
                                Descrição da DID: 
                            </label>
                            <div class="formRight">
                                <input type=text name=did_description class="span" maxlength=50 value="<?= $did_description ?>">
                            </div>
                        </div>
                        <div class="formRow op fix">
                            <label>Activa: </label>
                            <div class="formRight">
                                <select class="span" name=did_active>
                                    <option value="Y" <?= $did_active=="Y"?"Selected":""; ?>>Sim</option>
                                    <option value="N" <?= $did_active=="N"?"Selected":""; ?>>Não</option>
                                </select>
                            </div>
                        </div>
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="Esta Opção permite que as chamadas recebidas por esta DID sejam gravadas.<br>Sim: Grava a chada completa.<br>Sim Sem Fila de Espera: Grava até a chamada ser desligada ou entrar numa fila de espera de um Grupo de Inbound.<br>Não: Não grava a chamada.">
                                Gravação de Chamadas: 
                            </label>
                            <div class="formRight">
                                <select class="span" name=record_call>
                                    <option value="N" <?= $record_call=="N"?"Selected":""; ?>>Não</option>
                                    <option value="Y_QUEUESTOP" <?= $record_call=="Y_QUEUESTOP"?"Selected":""; ?>>Sim Sem Fila de Espera</option>
                                    <option value="Y" <?= $record_call=="Y"?"Selected":""; ?>>Sim</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                        <div class="formRow op fix">
                            <label data-t="tooltip" title="Aqui define o roteamento de chamadas da DID.<br>Extenção: pode colocar uma extensão defenida no servidor ou um nº de telefone.<br>Agente: Encaminha a chamada para o Agente se o mesmo estiver Logado.<br>Licença: Encaminha a chamada para a Licença escolhida.<br>Grupo Inbound: Encaminha para o Grupo inbound seleccionado, e depois irá usar as definições do mesmo.<br>IVR: Encaminha para o IVR Seleccionado.">
                                Roteamento da DID: 
                            </label>
                            <div class="formRight">
                                <select class="span" name="did_route">
                                    <option value="AGENT" <?= $did_route=="AGENT"?"Selected":""; ?>>Agente</option>
                                    <option value="EXTEN" <?= $did_route=="EXTEN"?"Selected":""; ?>>Extenção</option>
                                    <option value="VOICEMAIL" <?= $did_route=="VOICEMAIL"?"Selected":""; ?>>VoiceMail</option>
                                    <option value="PHONE" <?= $did_route=="PHONE"?"Selected":""; ?>>Licença</option>
                                    <option value="IN_GROUP" <?= $did_route=="IN_GROUP"?"Selected":""; ?>>Grupo Inbound</option>
                                    <option value="CALLMENU" <?= $did_route=="CALLMENU"?"Selected":""; ?>>IVR</option>
                                </select>
                            </div>
                        </div>
                        <div class="formRow op AGT">
                            <label data-t="tooltip" title="Aqui define o Roteamento das Chamadas, quando o Agente Seleccionado Não se encontra Logado.">Roteamento Agente Não Disponivel: </label>
                            <div class="formRight">
                                <select class="span" name=user_unavailable_action>
                                    <option value="EXTEN" <?= $user_unavailable_action=="EXTEN"?"Selected":""; ?>>Extenção</option>
                                    <option value="VOICEMAIL" <?= $user_unavailable_action=="VOICEMAIL"?"Selected":""; ?>>VoiceMail</option>
                                    <option value="PHONE" <?= $user_unavailable_action=="PHONE"?"Selected":""; ?>>Licença</option>
                                    <option value="IN_GROUP" <?= $user_unavailable_action=="IN_GROUP"?"Selected":""; ?>>Grupo Inbound</option>
                                </select>
                            </div>
                        </div>
                        <div class="formRow op AGT">
                            <label>Agente: </label>
                            <div class="formRight">
                                <select data-placeholder="Escolha o Agente..." class="chzn-select" name=user >
                                    
                                    <?php
                                $stmt = "select user,full_name from vicidial_users WHERE active='Y' order by full_name;";
                                $rslt = mysql_query($stmt, $link);
                                $i = 0;
                                while ($i <  mysql_num_rows($rslt)) {
                                    $row = mysql_fetch_row($rslt);
                                    echo "<option value=\"$row[0]\" ".(($row[0]==$user)?"SELECTED":"").">$row[1]</option>";
                                    $i++;
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="formRow op AGT">
                            <label data-t="tooltip" title="Aqui escolhe o Grupo de Inbound que vai dar as definições ao encaminhamento de chamadas para o Agente.">Grp. Inb. Def. Agente: </label>
                            <div class="formRight">
                                <select class="span"  name=user_route_settings_ingroup><?=$Xgroups_menu ?></select>
                            </div>
                        </div>
                        <div class="formRow op EXT">
                            <label data-t="tooltip" title="Existe uma extensão padrão que é a 9998811112 que é: Sem Serviço.">Extensão: </label>
                            <div class="formRight">
                                <input type=text name=extension class="span" maxlength=50 value="<?= $extension ?>">
                            </div>
                        </div>
                        <div class="formRow op EXT">
                            <label data-t="tooltip" title="O contexto que a Extenção vai utilizar. O padrão é default.">Contexto da Extensão: </label>
                            <div class="formRight">
                                <input type=text name=exten_context class="span" maxlength=50 value="<?= $exten_context ?>">
                            </div>
                        </div>
                        <div class="formRow op VCM">
                            <label>
                                Voicemail: 
                            </label>
                            <div class="formRight">
                                <input type=text name=voicemail_ext id=voicemail_ext class="span" maxlength=10 value="<?= $voicemail_ext ?>">
                            </div>
                        </div>
                        <div class="formRow op PHN">
                            <label>
                                Licença: 
                            </label>
                            <div class="formRight">
                                <input type=text name=phone class="span" maxlength=100 value="<?= $phone ?>">
                            </div>
                        </div>
                        <div class="formRow op PHN">
                            <label data-t="tooltip" title="O IP do Servidor onde se encontra a Licença.">IP do Servidor: </label>
                            <div class="formRight">
                                <select class="span" name=server_ip>
                                    <?php 
                                    $o=0;
                                    while ($o<count($servers_list)) {
                                        echo '<option value="'.$servers_list[$o][0].'"'.(($servers_list[$o]==$server_ip)?" Selected":"").'>'.$servers_list[$o][0].' - '.$servers_list[$o][1].'</option>';
                                        $o++;
                                    } ?>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op IVR">
                            <label>IVR: </label>
                            <div class="formRight">
                                <select class="span" name=menu_id>
                                    
                                <?php
                                $stmt = "select menu_id,menu_name,menu_prompt from vicidial_call_menu order by menu_id;";
                                $rslt = mysql_query($stmt, $link);
                                $menus_to_print = mysql_num_rows($rslt);
                                $i = 0;
                                while ($i < $menus_to_print) {
                                    $row = mysql_fetch_row($rslt);
                                    echo "<option value=\"$row[0]\" ".(($row[0]==$menu_id)?"SELECTED":"").">$row[0] - $row[1] - $row[2]</option>";
                                    $i++;
                                }
                                ?>
                                    </select>
                            </div>
                        </div>

                        <div class="formRow op ING">
                            <label>Grupo Inb.: </label>
                            <div class="formRight">
                                <select class="span" name=group_id>
                                    <?= $Dgroups_menu ?>
                                </select>
                            </div>
                        </div>
                        <div class="formRow op ING">
                            <label data-t="tooltip" title="Isto é o método que a DID vai usar para tratar a chamada para o Grupo de Inbound.<br><br>CID=CallerID.<br>ANI=Automatic Number Identification.<br><br>CID: Cria uma lead nova a todas as chamadas.<br>CIDLOOKUP: Procura pela existencia do nº em todo o sistema.<br>CIDLOOKUPRL: Procura o nº numa Lista expecifica.<br>CIDLOOKUPRC: Procura o nº em todas as Listas associadas a uma campanha especifica.<br>CLOSER: É especifico para chamadas Closer.<br>ANI: Cria uma lead nova a todas chamadas usando o ANI como nº de telefone.<br>ANILOOKUP: Procura pela existencia do nº em todo o sistema.<br>ANILOOKUPRL: Procura o nº numa Lista expecifica.<br>XDIGITID: Esta Opção pede ao cliente um nº de X digitos antes de entrar em fila de espera.<br>VIDPROMPT: Esta Opção cria uma lead nova a todas as chamadas e pede ao cliente o seu ID e grava o nº de telefone e o ID no Vendo ID.<br>VIDPROMPTLOOKUP: Procura o ID em todo o Sistema<br>VIDPROMPTLOOKUPRL: Procura o ID numa Lista especifica.<br>VIDPROMPTLOOKUPRC: Procura o ID em todas as Listas associadas a uma campanha especifica.">
                                Método Chamada Grupo Inb.: </label>
                            <div class="formRight">
                                <select class="span" name=call_handle_method>
                                    <option value="CID" <?= $call_handle_method=="CID"?"Selected":""; ?>>CID</option>
                                    <option value="CIDLOOKUP" <?= $call_handle_method=="CIDLOOKUP"?"Selected":""; ?>>CIDLOOKUP</option>
                                    <option value="CIDLOOKUPRL" <?= $call_handle_method=="CIDLOOKUPRL"?"Selected":""; ?>>CIDLOOKUPRL</option>
                                    <option value="CIDLOOKUPRC" <?= $call_handle_method=="CIDLOOKUPRC"?"Selected":""; ?>>CIDLOOKUPRC</option>
                                    <option value="ANI" <?= $call_handle_method=="ANI"?"Selected":""; ?>>ANI</option>
                                    <option value="ANILOOKUP" <?= $call_handle_method=="ANILOOKUP"?"Selected":""; ?>>ANILOOKUP</option>
                                    <option value="ANILOOKUPRL" <?= $call_handle_method=="ANILOOKUPRL"?"Selected":""; ?>>ANILOOKUPRL</option>
                                    <option value="VIDPROMPT" <?= $call_handle_method=="VIDPROMPT"?"Selected":""; ?>>VIDPROMPT</option>
                                    <option value="VIDPROMPTLOOKUP" <?= $call_handle_method=="VIDPROMPTLOOKUP"?"Selected":""; ?>>VIDPROMPTLOOKUP</option>
                                    <option value="VIDPROMPTLOOKUPRL" <?= $call_handle_method=="VIDPROMPTLOOKUPRL"?"Selected":""; ?>>VIDPROMPTLOOKUPRL</option>
                                    <option value="VIDPROMPTLOOKUPRC" <?= $call_handle_method=="VIDPROMPTLOOKUPRC"?"Selected":""; ?>>VIDPROMPTLOOKUPRC</option>
                                    <option value="CLOSER" <?= $call_handle_method=="CLOSER"?"Selected":""; ?>>CLOSER</option>
                                    <option value="3DIGITID" <?= $call_handle_method=="3DIGITID"?"Selected":""; ?>>3DIGITID</option>
                                    <option value="4DIGITID" <?= $call_handle_method=="4DIGITID"?"Selected":""; ?>>4DIGITID</option>
                                    <option value="5DIGITID" <?= $call_handle_method=="5DIGITID"?"Selected":""; ?>>5DIGITID</option>
                                    <option value="10DIGITID" <?= $call_handle_method=="10DIGITID"?"Selected":""; ?>>10DIGITID</option>
                                </select>
                            </div>
                        </div>
                        <div class="formRow op ING">
                            <label data-t="tooltip" title="Esta opção é a que define o metodo de encaminhamento da chamada para o agente dentro do grupo de Inbound.<br><b>Equilibrado</b>: tenta encaminhar a chamada para qualquer agente, não interessa o servidor em que esteja.<br><b>Equilibrado com transbordo</b>: tenta encaminhar a chamada para um agente no servidor, e só depois tenta noutros servidores.<br><b>Só no próprio servidor</b>: Não procura nos outros.">
                                Atribuição Chamada Agente Grupo de Inb.: </label>
                            <div class="formRight">
                                <select class="span" name=agent_search_method>
                                    <option value="LB" <?= $agent_search_method=="LB"?"Selected":""; ?>>Equilibrado</option>
                                    <option value="LO" <?= $agent_search_method=="LO"?"Selected":""; ?>>Equilibrado com Transbordo</option>
                                    <option value="SO" <?= $agent_search_method=="SO"?"Selected":""; ?>>Só no próprio Servidor</option>
                                </select>
                            </div>
                        </div>
                        <div class="formRow op ING">
                            <label>ID da Lista Grupo Inb.: </label>
                            <div class="formRight">
                                <input type=text name=list_id class="span" maxlength=14 value="<?= $list_id ?>">
                            </div>
                        </div>

                        <div class="formRow op ING">
                            <label>Campanha Grupo Inb.: </label>
                            <div class="formRight">
                                <select class="chzn-select" name=campaign_id>
                                    <?= $campaigns_list ?>
                                </select>
                            </div>
                        </div>

                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        
                        <div class="formRow op hide" >
                            <label><?= help("vicidial_inbound_dids-phone_code","In Group Phone Code"); ?>: </label>
                            <div class="formRight">
                                <input type=text name=phone_code class="span" maxlength=14 value="<?= $phone_code ?>">
                            </div>
                        </div>


                        <div class="formRow op hide">
                            <label><?= help("vicidial_inbound_dids-filter_clean_cid_number","Clean CID Number"); ?>: </label>
                            <div class="formRight">
                                <input type=text name=filter_clean_cid_number class="span" maxlength=20 value="<?= $filter_clean_cid_number ?>">
                            </div>
                        </div>

                        <div class="formRow op fix FLT">
                            <label><?= help("vicidial_inbound_dids-filter_inbound_number","Filtro de Chamada"); ?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_inbound_number>
                                    <option value="DISABLED" <?= $filter_inbound_number=="DISABLED"?"Selected":""; ?>>Inactivo</option>
                                    <option value="GROUP" <?= $filter_inbound_number=="GROUP"?"Selected":""; ?>>Grupo</option>
                                    <!--<option value="URL" <?= $filter_inbound_number=="URL"?"Selected":""; ?>>URL</option>-->
                                </select>
                            </div>
                        </div>
                	
                        <div class="formRow op fix FLT">
                            <label><?= help("vicidial_inbound_dids-filter_phone_group_id","Grupo Filtro de Telefones"); ?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_phone_group_id>
                                    <option value="--NONE--" <?= (($filter_phone_group_id==$row[0])?"Selected":"") ?>>Nenhum</option>
                                    <?php
                                    $stmt = "select filter_phone_group_id,filter_phone_group_name from vicidial_filter_phone_groups order by filter_phone_group_id;";
                                    $rslt = mysql_query($stmt, $link);
                                    $Fgroups_to_print = mysql_num_rows($rslt);
                                    $i = 0;
                                    while ($i < $Fgroups_to_print) {
                                        $row = mysql_fetch_row($rslt);
                                        echo "<option value='$row[0]' ".(($filter_phone_group_id==$row[0])?"Selected":"").">$row[1]</option>";
                                        $i++;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op hide">
                            <label><?= help("vicidial_inbound_dids-filter_url","Filter URL"); ?>: </label>
                            <div class="formRight">
                                <input type=text name=filter_url class="span" maxlength=1000 value="<?= $filter_url ?>">
                            </div>
                        </div>

                        <div class="formRow op fix FLT">
                            <label><?= help("vicidial_inbound_dids-filter_action","Acção Filtro"); ?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_action>
                                    <option value="AGENT" <?= $filter_action=="AGENT"?"Selected":""; ?>>Agente</option>
                                    <option value="EXTEN" <?= $filter_action=="EXTEN"?"Selected":""; ?>>Extenção</option>
                                    <option value="VOICEMAIL" <?= $filter_action=="VOICEMAIL"?"Selected":""; ?>>VoiceMail</option>
                                    <option value="PHONE" <?= $filter_action=="PHONE"?"Selected":""; ?>>Licença</option>
                                    <option value="IN_GROUP" <?= $filter_action=="IN_GROUP"?"Selected":""; ?>>Grupo Inbound</option>
                                    <option value="CALLMENU" <?= $filter_action=="CALLMENU"?"Selected":""; ?>>IVR</option>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op AGT FLT">
                            <label><?= help("vicidial_inbound_dids-user_unavailable_action","Acção Agente Indesponivel Filtro"); ?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_user_unavailable_action>
                                    <option value="EXTEN" <?= $filter_user_unavailable_action=="EXTEN"?"Selected":""; ?>>Extenção</option>
                                    <option value="VOICEMAIL" <?= $filter_user_unavailable_action=="VOICEMAIL"?"Selected":""; ?>>VoiceMail</option>
                                    <option value="PHONE" <?= $filter_user_unavailable_action=="PHONE"?"Selected":""; ?>>Licença</option>
                                    <option value="IN_GROUP" <?= $filter_user_unavailable_action=="IN_GROUP"?"Selected":""; ?>>Grupo Inbound</option>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op AGT FLT">
                            <label><?= help("vicidial_inbound_dids-user","Agente Filtro"); ?>: </label>
                            <div class="formRight">
                                <select data-placeholder="Escolha o Agente..." class="chzn-select" name=filter_user >
                                    
                                    <?php
                                $stmt = "select user,full_name from vicidial_users WHERE active='Y' order by full_name;";
                                $rslt = mysql_query($stmt, $link);
                                $i = 0;
                                while ($i <  mysql_num_rows($rslt)) {
                                    $row = mysql_fetch_row($rslt);
                                    echo "<option value=\"$row[0]\" ".(($row[0]==$filter_user)?"SELECTED":"").">$row[1]</option>";
                                    $i++;
                                }
                                ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="formRow op EXT FLT">
                            <label><?= help("vicidial_inbound_dids-extension","Extenção Filtro"); ?>: </label>
                            <div class="formRight">
                                <input type=text name=filter_extension class="span" maxlength=50 value="<?= $filter_extension ?>">
                            </div>
                        </div>

                        <div class="formRow op EXT FLT">
                            <label><?= help("vicidial_inbound_dids-exten_context","Contexto Extenção Filtro"); ?>: </label>
                            <div class="formRight">
                                <input type=text name=filter_exten_context class="span" maxlength=50 value="<?= $filter_exten_context ?>">
                            </div>
                        </div>

                        <div class="formRow op VCM FLT">
                            <label><?= help("vicidial_inbound_dids-voicemail_ext","VoiceMail Filtro"); ?>: </label>
                            <div class="formRight">
                                <input type=text name=filter_voicemail_ext id=voicemail_ext class="span" maxlength=10 value="<?= $filter_voicemail_ext ?>">
                            </div>
                        </div>

                        <div class="formRow op PHN FLT">
                            <label><?= help("vicidial_inbound_dids-phone","Licença Filtro"); ?>: </label>
                            <div class="formRight">
                                <input type=text name=filter_phone class="span" maxlength=100 value="<?= $filter_phone ?>">
                            </div>
                        </div>

                        <div class="formRow op PHN FLT">
                            <label><?= help("vicidial_inbound_dids-server_ip","IP Servidor Filtro");?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_server_ip>
                                    <?php 
                                    $o=0;
                                    while ($o<count($servers_list)) {
                                        echo '<option value="'.$servers_list[$o][0].'"'.(($servers_list[$o]==$filter_server_ip)?"Selected":"").'>'.$servers_list[$o][0].' - '.$servers_list[$o][1].'</option>';
                                        $o++;
                                    } ?>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op IVR FLT">
                            <label><?= help("vicidial_inbound_dids-menu_id","IVR Filtro"); ?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_menu_id>
                                    <?php
                                $stmt = "select menu_id,menu_name,menu_prompt from vicidial_call_menu order by menu_id;";
                                $rslt = mysql_query($stmt, $link);
                                $menus_to_print = mysql_num_rows($rslt);
                                $i = 0;
                                while ($i < $menus_to_print) {
                                    $row = mysql_fetch_row($rslt);
                                    echo "<option value=\"$row[0]\" ".(($row[0]==$filter_menu_id)?"SELECTED":"").">$row[0] - $row[1] - $row[2]</option>";
                                    $i++;
                                }
                                ?>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op ING FLT">
                            <label><?= help("vicidial_inbound_dids-user_route_settings_ingroup","Grupo Inb. Definições Agente Filtro"); ?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_user_route_settings_ingroup>
                                    <?= $FXgroups_menu ?>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op ING FLT">
                            <label><?= help("vicidial_inbound_dids-group_id","Grupo Inb. Filtro"); ?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_group_id>
                                    <?= $FDgroups_menu ?>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op ING FLT">
                            <label><?= help("vicidial_inbound_dids-call_handle_method","Indentificador Chamada Grupo Inb. Filtro"); ?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_call_handle_method>
                                    <option value="CID" <?= $filter_call_handle_method=="CID"?"Selected":""; ?>>CID</option>
                                    <option value="CIDLOOKUP" <?= $filter_call_handle_method=="CIDLOOKUP"?"Selected":""; ?>>CIDLOOKUP</option>
                                    <option value="CIDLOOKUPRL" <?= $filter_call_handle_method=="CIDLOOKUPRL"?"Selected":""; ?>>CIDLOOKUPRL</option>
                                    <option value="CIDLOOKUPRC" <?= $filter_call_handle_method=="CIDLOOKUPRC"?"Selected":""; ?>>CIDLOOKUPRC</option>
                                    <option value="ANI" <?= $filter_call_handle_method=="ANI"?"Selected":""; ?>>ANI</option>
                                    <option value="ANILOOKUP" <?= $filter_call_handle_method=="ANILOOKUP"?"Selected":""; ?>>ANILOOKUP</option>
                                    <option value="ANILOOKUPRL" <?= $filter_call_handle_method=="ANILOOKUPRL"?"Selected":""; ?>>ANILOOKUPRL</option>
                                    <option value="VIDPROMPT" <?= $filter_call_handle_method=="VIDPROMPT"?"Selected":""; ?>>VIDPROMPT</option>
                                    <option value="VIDPROMPTLOOKUP" <?= $filter_call_handle_method=="VIDPROMPTLOOKUP"?"Selected":""; ?>>VIDPROMPTLOOKUP</option>
                                    <option value="VIDPROMPTLOOKUPRL" <?= $filter_call_handle_method=="VIDPROMPTLOOKUPRL"?"Selected":""; ?>>VIDPROMPTLOOKUPRL</option>
                                    <option value="VIDPROMPTLOOKUPRC" <?= $filter_call_handle_method=="VIDPROMPTLOOKUPRC"?"Selected":""; ?>>VIDPROMPTLOOKUPRC</option>
                                    <option value="CLOSER" <?= $filter_call_handle_method=="CLOSER"?"Selected":""; ?>>CLOSER</option>
                                    <option value="3DIGITID" <?= $filter_call_handle_method=="3DIGITID"?"Selected":""; ?>>3DIGITID</option>
                                    <option value="4DIGITID" <?= $filter_call_handle_method=="4DIGITID"?"Selected":""; ?>>4DIGITID</option>
                                    <option value="5DIGITID" <?= $filter_call_handle_method=="5DIGITID"?"Selected":""; ?>>5DIGITID</option>
                                    <option value="10DIGITID" <?= $filter_call_handle_method=="10DIGITID"?"Selected":""; ?>>10DIGITID</option>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op ING FLT">
                            <label><?= help("vicidial_inbound_dids-agent_search_method","Atribuição Chamada Agente Grupo de Inb Filtro"); ?>: </label>
                            <div class="formRight">
                                <select class="span" name=filter_agent_search_method>
                                    <option value="LB" <?= $filter_agent_search_method=="LB"?"Selected":""; ?>>Equilibrado</option>
                                    <option value="LO" <?= $filter_agent_search_method=="LO"?"Selected":""; ?>>Equilibrado com Transbordo</option>
                                    <option value="SO" <?= $filter_agent_search_method=="SO"?"Selected":""; ?>>Só no próprio Servidor</option>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op ING FLT">
                            <label><?= help("vicidial_inbound_dids-list_id","ID da Lista Grupo Inb. Filtro"); ?>: </label>
                            <div class="formRight">
                                <input type=text name=filter_list_id class="span" maxlength=14 value="<?= $filter_list_id ?>">
                            </div>
                        </div>

                        <div class="formRow op ING FLT">
                            <label><?= help("vicidial_inbound_dids-campaign_id","Campanha Grupo Inb. Filtro"); ?>: </label>
                            <div class="formRight">
                                <select class="chzn-select" name=filter_campaign_id>
                                    <?= $campaigns_list_filter ?>
                                </select>
                            </div>
                        </div>

                        <div class="formRow op hide">
                            <label><?= help("vicidial_inbound_dids-phone_code","Filter In Group Phone Code"); ?>: </label>
                            <div class="formRight">
                                <input type=text name=filter_phone_code class="span" maxlength=14 value="<?= $filter_phone_code ?>">
                            </div>
                        </div>

                        <div class="clear"></div>
                        <div class="seperator_dashed"></div>
                        <p class="text-right">
                            <button class="btn btn-success">Gravar</button>
                        </p>
                    </div>    
                </div>


               
                           
            </form>
            <script>
            $(function(){
                $("[data-t=tooltip]").tooltip({placement:"right",html:true});
                 $("#loader").fadeOut("slow");
                //para esconder o que não é para ver
                ddi_route_hider($("[name=did_route]").val());
                filter_hider($("[name=filter_action]").val());
                
                
                $("[name=did_route]").change(function(){
                    var op=$(this).val();
                    ddi_route_hider(op);
                 });
                 
                 $("[name=user_unavailable_action]").change(function(){
                    var op=$(this).val();
                    no_agent_hider(op);
                 });
                 
                 $("[name=filter_action]").change(function(){
                     var op=$(this).val();
                     filter_hider(op);
                 });
                 
                 $("[name=filter_user_unavailable_action]").change(function(){
                     var op=$(this).val();
                     filte_no_agent_hider(op);
                 });
                 
                 $(".chzn-select").chosen({no_results_text: "Não foi encontrado."});
            });
            
            function ddi_route_hider(op){
                switch(op)
                    {
                    case "CALLMENU":
                      $(".op:not(.IVR):not(.fix):not(.FLT)").hide();
                      $(".op.IVR").show();
                      break;
                    case "IN_GROUP":
                      $(".op:not(.ING):not(.fix):not(.FLT)").hide();
                      $(".op.ING").show();
                      break;
                    case "PHONE":
                      $(".op:not(.PHN):not(.fix):not(.FLT)").hide(); 
                      $(".op.PHN").show();   
                      break;
                    case "VOICEMAIL":
                      $(".op:not(.VCM):not(.fix):not(.FLT)").hide();
                      $(".op.VCM").show();    
                      break;
                    case "EXTEN":
                      $(".op:not(.EXT):not(.fix):not(.FLT)").hide();  
                      $(".op.EXT").show();  
                      break;
                    case "AGENT":
                      $(".op:not(.AGT):not(.fix):not(.FLT)").hide(); 
                      $(".op.AGT").show(); 
                      $("[name=user_unavailable_action]").change();
                      break;
                    default:
                      
                    }
            }
            
            function no_agent_hider(op){
                switch(op)
                    {
                    case "CALLMENU":
                      $(".op:not(.IVR):not(.AGT):not(.fix):not(.FLT)").hide();
                      $(".op.IVR").show();
                      break;
                    case "IN_GROUP":
                      $(".op:not(.ING):not(.AGT):not(.fix):not(.FLT)").hide();
                      $(".op.ING").show();
                      break;
                    case "PHONE":
                      $(".op:not(.PHN):not(.AGT):not(.fix):not(.FLT)").hide(); 
                      $(".op.PHN").show();   
                      break;
                    case "VOICEMAIL":
                      $(".op:not(.VCM):not(.AGT):not(.fix):not(.FLT)").hide();
                      $(".op.VCM").show();    
                      break;
                    case "EXTEN":
                      $(".op:not(.EXT):not(.AGT):not(.fix):not(.FLT)").hide();  
                      $(".op.EXT").show();  
                      break;
                    default:
                      
                    }
            }
            function filter_hider(op){
                switch(op)
                    {
                    case "CALLMENU":
                      $(".FLT.op:not(.IVR):not(.fix)").hide();
                      $(".FLT.op.IVR").show();
                      break;
                    case "IN_GROUP":
                      $(".FLT.op:not(.ING):not(.fix)").hide();
                      $(".FLT.op.ING").show();
                      break;
                    case "PHONE":
                      $(".FLT.op:not(.PHN):not(.fix)").hide(); 
                      $(".FLT.op.PHN").show();   
                      break;
                    case "VOICEMAIL":
                      $(".FLT.op:not(.VCM):not(.fix)").hide();
                      $(".FLT.op.VCM").show();    
                      break;
                    case "EXTEN":
                      $(".FLT.op:not(.EXT):not(.fix)").hide();  
                      $(".FLT.op.EXT").show();  
                      break;
                    case "AGENT":
                      $(".FLT.op:not(.AGT):not(.fix)").hide(); 
                      $(".FLT.op.AGT").show(); 
                      $("[name=filter_user_unavailable_action]").change();
                      break;
                    default:
                      
                    }
            }
            
            function filte_no_agent_hider(op){
                switch(op)
                    {
                    case "CALLMENU":
                      $(".FLT.op:not(.IVR):not(.AGT):not(.fix)").hide();
                      $(".FLT.op.IVR").show();
                      break;
                    case "IN_GROUP":
                      $(".FLT.op:not(.ING):not(.AGT):not(.fix)").hide();
                      $(".FLT.op.ING").show();
                      break;
                    case "PHONE":
                      $(".FLT.op:not(.PHN):not(.AGT):not(.fix)").hide(); 
                      $(".FLT.op.PHN").show();   
                      break;
                    case "VOICEMAIL":
                      $(".FLT.op:not(.VCM):not(.AGT):not(.fix)").hide();
                      $(".FLT.op.VCM").show();    
                      break;
                    case "EXTEN":
                      $(".FLT.op:not(.EXT):not(.AGT):not(.fix)").hide();  
                      $(".FLT.op.EXT").show();  
                      break;
                    default:
                      
                    }
            }
            </script>


<?php
#FOOTER
$footer = ROOT . "ini/footer.php";
require($footer);
?>	