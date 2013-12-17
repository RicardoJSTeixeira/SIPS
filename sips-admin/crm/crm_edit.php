<?php
$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/dbconnect.php");
include(ROOT . "sips-admin/functions.php");
require(ROOT . "ini/user.php");

$user = new user;
####################################################################### 
### BEGIN  - Created by kant <-- fag
#######################################################################
$campaign_id = (isset($_GET['campaign_id'])) ? $_GET['campaign_id'] : $_POST['campaign_id'];
$lead_id = (isset($_GET['lead_id'])) ? $_GET['lead_id'] : $_POST['lead_id'];





### Dados da Lead
$query = "SELECT 
			vdlf.campaign_id,
			vdc.campaign_name, 
			vdlf.list_id, 
			vdlf.list_name, 
			vu.full_name, 
			vdl.called_count, 
			DATE_FORMAT(vdl.last_local_call_time,'%H:%i:%s') AS hora_last,
			DATE_FORMAT(vdl.last_local_call_time,'%d-%m-%Y') AS data_last,
			vdl.status, 
			vds.status_name as status_name_one,
			vdcs.status_name as status_name_two,
			DATE_FORMAT(vdl.entry_date,'%H:%i:%s') AS hora_load,
			DATE_FORMAT(vdl.entry_date,'%d-%m-%Y') AS data_load,
			vdl.phone_number
			
		FROM 
			vicidial_lists vdlf 
		INNER JOIN 
			vicidial_list vdl 
		ON 
			vdl.list_id=vdlf.list_id 						
		INNER JOIN 
			vicidial_campaigns vdc 
		ON 
			vdc.campaign_id=vdlf.campaign_id
		LEFT JOIN 
			vicidial_users vu 
		ON 
			vu.user=vdl.user
		LEFT JOIN 
			vicidial_statuses vds 
		ON 
			vds.status=vdl.status
		LEFT JOIN 
			vicidial_campaign_statuses vdcs 
		ON 
			vdcs.status=vdl.status
		WHERE 
			lead_id='$lead_id'
		LIMIT 1";

$query = mysql_query($query, $link) or die(mysql_error());
$lead_info = mysql_fetch_assoc($query);

if ($lead_info['status_name_one'] == NULL) {
    $status_name = $lead_info['status_name_two'];
} else {
    $status_name = $lead_info['status_name_one'];
}

//9014063
### Dados do Contacto
$query = "SELECT 
			Name,
			Display_name
		FROM 
			vicidial_list_ref
		WHERE
			campaign_id='$lead_info[campaign_id]'
		AND
			active=1 Order by field_order ASC";
$query = mysql_query($query, $link) or die(mysql_error());
$fields_count = mysql_num_rows($query);
$fields_SELECT = array();

if ($fields_count == 0) {
    $fields_SELECT = array("phone_number" => "Nº Telefone", "first_name" => "Nome", "alt_phone" => "Telefone Alternativo", "address3" => "Telefone Alternativo 2", "address1" => "Morada", "postal_code" => "Codigo Postal", "email" => "E-mail", "comments" => "Comentários");
} else {
    while ($row = mysql_fetch_assoc($query)) {
        $fields_SELECT[$row["Name"]] = $row["Display_name"];
    }
}

$query = "SELECT 
			" . implode(",", array_keys($fields_SELECT)) . "  
		FROM 
			vicidial_list
		WHERE 
			lead_id='$lead_id' 
		LIMIT 1";
$query = mysql_query($query, $link) or die(mysql_error());
$fields = mysql_fetch_assoc($query);

### Construção da Lista de Feedbacks
$query = "SELECT status,status_name,sale FROM  (select status,status_name,sale from vicidial_campaign_statuses WHERE campaign_id='$lead_info[campaign_id]' AND scheduled_callback!=1) a union all (select status,status_name,sale from vicidial_statuses)";
$query = mysql_query($query, $link) or die(mysql_error());
$is_campaign_feedback = 0;

for ($i = 0; $i < mysql_num_rows($query); $i++) {
    $row = mysql_fetch_assoc($query) or die(mysql_query());
    if ($row['status'] == $lead_info['status']) { # feedback actual (selected)
        $status_options .= "<option data-sale='$row[sale]' selected value='$row[status]'>$row[status_name]</option>\n";
        $is_campaign_feedback = 1;
    } else { # outros feedbacks da campanha
        $status_options .= "<option data-sale='$row[sale]' value='$row[status]'>$row[status_name]</option\n>";
    }
}

if (!$is_campaign_feedback) { # caso se o feedback actual seja de sistema
    $query = "SELECT status,status_name FROM vicidial_statuses WHERE status='$lead_info[status]'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_assoc($query);
    $status_options .= "<option selected value='$row[status]'>$row[status_name]</option>";
}


### Chamadas Feitas
$query = "SELECT 									
			vl.uniqueid, 
			vl.lead_id, 
			vl.list_id,
			vl.campaign_id,
			vl.call_date AS data,
			
			vl.start_epoch,
			vl.end_epoch,
			vl.length_in_sec,
			vl.phone_code,
			vl.phone_number,
			vl.user,
			vl.comments,
			vl.processed,
			vl.user_group,
			vl.term_reason,
			vl.alt_dial,
			vu.full_name,
            vstatus.status_name,
            
			vc.campaign_name,
			vls.list_name
		FROM 
			vicidial_log vl
		left JOIN vicidial_users vu ON vl.user=vu.user
		left JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
		left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
			left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
		WHERE 
			vl.lead_id='$lead_id' 
		ORDER BY
			uniqueid 
		DESC LIMIT 500;";
$chamadas_feitas = mysql_query($query, $link) or die(mysql_error());



$query = "SELECT 									
			vl.uniqueid, 
			vl.lead_id, 
			vl.list_id,
			vl.campaign_id,
	vl.call_date AS data,
			vl.start_epoch,
			vl.end_epoch,
			vl.length_in_sec,
			vl.phone_code,
			vl.phone_number,
			vl.user,
			vl.comments,
			vl.processed,
			vl.user_group,
			vl.term_reason,
					vu.full_name,
            vstatus.status_name,
            			vc.campaign_name,
			vls.list_name
		FROM 
			vicidial_closer_log vl
		left JOIN vicidial_users vu ON vl.user=vu.user
		left JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
		left JOIN vicidial_lists vls ON vl.list_id=vls.list_id
			left join (select status,status_name from vicidial_statuses union all  select status,status_name from vicidial_campaign_statuses group by status) vstatus on vstatus.status= vl.status
		WHERE 
			vl.lead_id='$lead_id' 
		ORDER BY
			uniqueid 
		DESC LIMIT 500;";
$chamadas_feitas_in = mysql_query($query, $link) or die(mysql_error());





#Gravações da lead
$query = "SELECT 
				DATE_FORMAT(start_time,'%d-%m-%Y') AS data,
				DATE_FORMAT(start_time,'%H:%i:%s') AS hora_inicio,
				DATE_FORMAT(end_time,'%H:%i:%s') AS hora_fim,
				length_in_sec,
				filename,
				location,
				lead_id,
				rl.user,
				full_name
			FROM 
				recording_log rl
			INNER JOIN vicidial_users vu ON rl.user=vu.user
			WHERE 
				lead_id='$lead_id' 
			ORDER BY 
				recording_id 
			DESC LIMIT 500;";
$gravacoes = mysql_query($query, $link) or die(mysql_error());

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"]; //. $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"]; //.$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function get_client_ip() {
    $ipaddress = '';
    if ($_SERVER['HTTP_CLIENT_IP'])
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if ($_SERVER['HTTP_X_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_X_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if ($_SERVER['HTTP_FORWARDED_FOR'])
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if ($_SERVER['HTTP_FORWARDED'])
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if ($_SERVER['REMOTE_ADDR'])
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';

    return $ipaddress;
}

function reserved_ip($ip) {
    $reserved_ips = array(// not an exhaustive list
        '167772160' => 184549375, /*    10.0.0.0 -  10.255.255.255 */
        '3232235520' => 3232301055, /* 192.168.0.0 - 192.168.255.255 */
        '2130706432' => 2147483647, /*   127.0.0.0 - 127.255.255.255 */
        '2851995648' => 2852061183, /* 169.254.0.0 - 169.254.255.255 */
        '2886729728' => 2887778303, /*  172.16.0.0 -  172.31.255.255 */
        '3758096384' => 4026531839, /*   224.0.0.0 - 239.255.255.255 */
    );

    $ip_long = sprintf('%u', ip2long($ip));

    foreach ($reserved_ips as $ip_start => $ip_end) {
        if (($ip_long >= $ip_start) && ($ip_long <= $ip_end)) {
            return TRUE;
        }
    }
    return FALSE;
}
?>

<!-- Cabeçalho -->


<table class='table table-mod table-bordered'>
    <thead> 
        <tr>
            <th>ID do Contacto</th>
            <th>Número de Telefone</th>
            <th>Base de Dados</th>
            <th>Campanha</th>
            <th>Operador</th>
            <th>Feedback</th>
            <th>Nº de Chamadas</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $lead_id ?></td>
            <td><?= $lead_info["phone_number"] ?></td>
            <td><?= $lead_info["list_name"] ?></td>
            <td><?= $lead_info["campaign_name"] ?></td>
            <td><?= $lead_info["full_name"] ?></td>
            <td><span id='lead_info_status'><?= $status_name ?></span></td>
            <td><?= $lead_info["called_count"] ?></td>
        </tr>
    </tbody>
</table>
<br>
<table class='table table-mod table-bordered'>
    <thead> 
        <tr>
            <th>Data de Carregamento</th>
            <th>Hora de Carregamento</th>
            <th>Data da Última Chamada</th>
            <th>Hora da Última Chamada</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $lead_info["data_load"] ?></td>
            <td><?= $lead_info["hora_load"] ?></td>
            <td><?= $lead_info["data_last"] ?></td>
            <td><?= $lead_info["hora_last"] ?></td>
        </tr>
    </tbody>
</table>

<h2>Dados da Lead</h2>
<form class="form-horizontal" id='inputcontainer' >
    <?php foreach ($fields as $key => $value) { ?>
        <div class="control-group">
            <label class="control-label"><?= $fields_SELECT[$key] ?>:</label>
            <div class="controls" >
                <?php if ($key != "COMMENTS") { ?>
                    <input type=text name='<?= $key ?>' id='<?= $key ?>' class='span9' value='<?= $value ?>'>
                <?php } else { ?>
                    <textarea name='<?= $key ?>' id='<?= $key ?>' class='span9' ><?= $value ?></textarea>
                <?php } ?>
                <span id='td_<?= $fields_SELECT[$key] ?>'></span>
            </div>
        </div>
    <?php } ?>
</form>
<div class="<?= ($user->user_level > 5) ? "" : "hide" ?>">
    <h3>Alteração do Feedback</h3>
    <div class="control-group">
        <label class="control-label">Feedback Actual:</label>
        <div class="controls input-append">
            <select style='width:300px' name=feedback_list id=feedback_list><?= $status_options ?></select>
            <button class="btn btn-primary" id="confirm_feedback" style="display:none">Confirmação de feedback</button>
        </div>
        <span id=modify_feedback_status class='help-inline'><i>(O feedback deste contacto pode ser alterado neste menu.)</i></Span>
    </div>



    <div id="confirm_feedback_div"  style="display:none;width:95%">

        <div class="formRow">
            <label class="control-label">Confirmação de feedback</label>
            <div class="formRight">
                <input type="radio" id="radio_confirm_yes" name="radio_confirm_group" value="1"><label for="radio_confirm_yes"><span></span>Validado</label>
            </div>
            <div class="formRight">
                <input type="radio" id="radio_confirm_return" name="radio_confirm_group" value="2"><label for="radio_confirm_return"><span></span>Retornar para novo contacto</label>
            </div>
            <div class="formRight">
                <input type="radio" id="radio_confirm_no" name="radio_confirm_group" value="0"><label for="radio_confirm_no"><span></span>Não validado</label>
            </div>
        </div>
        <div class="clear"></div>

        <div class="formRow" id="div_comentarios">
            <label class="control-label">Log de Comentários</label>

            <table class="table table-mod table-bordered" id="comment_log_table">
                <thead>
                <th>
                    Comentario
                </th>
                <th>
                    Estado
                </th>
                <th>
                    Agente
                </th>
                <th>
                    Admin
                </th>
                <th>
                    Data
                </th>
                </thead>
                <tbody id="comment_log_tbody">

                </tbody>
            </table>

        </div>
        <div class="formRow">
            <label class="control-label">Comentários</label>
            <div class="formRight">
                <textarea id='textarea_comment' placeholder="Escreva aqui um comentario e escolha o tipo de validação e agente, depois clique em 'Guardar'" style="width: 300px;height:150px"></textarea>
            </div>
        </div>
        <div class="clear"></div>

        <div class="formRow">
            <label class="control-label">Agente</label>
            <div class="formRight">
                <select id='agente_selector'>
                </select>
            </div>
        </div>
        <div class="clear"></div>
        <div class="pull-right">
            <button class='btn btn-action' id='confirm_feedback_button'>Guardar</button>
        </div>
    </div>

</div>

<h3>Chamadas realizadas para este Contacto</h3>
<table class='table table-mod table-bordered' id="chamadas_realizadas">
    <thead>
        <tr>
            <th>Data</th>

            <th>Duração</th>
            <th>Número</th>
            <th>Operador</th>
            <th>Feedback</th>
            <th>Campanha</th>
            <th>Base de Dados</th>
        </tr>
    </thead>
    <tbody>
        <?php
        while ($row = mysql_fetch_assoc($chamadas_feitas)) {

            $duracao = sec_convert($row['length_in_sec'], "H");
            ?>

            <tr>
                <td><?= $row["data"] ?></td>

                <td><?= $duracao ?></td>
                <td><?= $row["phone_number"] ?></td>
                <td><?= $row["full_name"] ?></td>
                <td><?= $row["status_name"] ?></td>
                <td><?= $row["campaign_name"] ?></td>
                <td><?= $row["list_name"] ?>
                    <div class="view-button <?= ($user->user_level > 5) ? "" : "hide" ?>"><a class="btn btn-mini" target='_new' href='/sips-admin/crm/script_placeholder.html?lead_id=<?= $lead_id ?>&campaign_id=<?= $lead_info[campaign_id] ?>&user=<?= $user->id ?>&pass=<?= $user->password ?>&isadmin=1&unique_id=<?= $row["uniqueid"] ?>'><i class="icon-bookmark"></i>Script</a></div>

                </td>
            </tr>
        <?php } ?>

        <?php
        while ($row = mysql_fetch_assoc($chamadas_feitas_in)) {

            $duracao = sec_convert($row['length_in_sec'], "H");
            ?>

            <tr>
                <td><?= $row["data"] ?></td>

                <td><?= $duracao ?></td>
                <td><?= $row["phone_number"] ?></td>
                <td><?= $row["full_name"] ?></td>
                <td><?= $row["status_name"] ?></td>
                <td><?= $row["campaign_name"] ?></td>
                <td><?= $row["list_name"] ?>

                    <div class="view-button"><a class="btn btn-mini" target='_new' href='script_placeholder.html?lead_id=<?= $lead_id ?>&campaign_id=<?= $lead_info[campaign_id] ?>&user=<?= $user->id ?>&pass=<?= $user->password ?>&isadmin=1&unique_id=<?= $row["uniqueid"] ?>'><i class="icon-bookmark"></i>Script</a></div>



                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<div class="clear"></div>
<div class="<?= ($user->user_level > 5) ? "" : "hide" ?>">
    <h3>Gravações deste Contacto</h3>
    <table class='table table-mod table-bordered'>
        <thead>
            <tr>
                <th>Data</th>
                <th>Inicio da Gravação</th>
                <th>Fim da Gravação</th>
                <th>Duração</th>
                <th>Operador</th>

        </thead>
        <tbody>
            <?php
            $curpage = curPageURL();
            while ($row = mysql_fetch_assoc($gravacoes)) {
                ?>

                <tr>
                    <td><?= $row["data"] ?></td>
                    <td><?= $row["hora_inicio"] ?></td>
                    <td><?= $row["hora_fim"] ?></td>
                    <td><?= sec_convert($row['length_in_sec'], "H") ?></td>
                    <td><?= $row["full_name"] ?>

                        <?
                        $mp3File = "#";
                        if (strlen($row[location]) > 0) {
                        //if lan
                        if (reserved_ip(get_client_ip())) {
                        $mp3File = $row[location];
                        } else {
                        $tmp = explode("/", $row[location]);
                        $ip = $tmp[2];
                        $tmp = explode(".", $ip);
                        $ip = $tmp[3];

                        switch ($ip) {
                        case "248":
                        $port = ":20248";
                        break;
                        case "247":
                        $port = ":20247";
                        break;
                        default:
                        $port = "";
                        break;
                        }
                        $mp3File = $curpage . $port . "/RECORDINGS/MP3/$row[filename]-all.mp3";
                        }
                        $audioPlayer = "Há gravação";
                        } else {
                        $audioPlayer = "Não há gravação!";
                        }


                        $lenghtInMin = date("i:s", $row[length_in_sec]);
                        ?>


                        <div class="view-button"><a href='<?= $mp3File ?>' target='_self' class="btn btn-mini"><i class="icon-play"></i>Ouvir</a></div>

                    </td>




                </tr>
            <?php } ?>
        </tbody>
    </table>



</div>



<script type="text/javascript">

    $(function() {
<?= ($user->id) ? "" : '$("#inputcontainer").find("input,textarea").prop("disabled",true);' ?>

        if ($("#feedback_list option:selected").data("sale") == "Y")
            $("#confirm_feedback").show();
        //get agentes
        $.post("_requests.php", {action: "get_agentes"},
        function(data)
        {
            $("#agente_selector").empty();
            $.each(data, function(index, value)
            {
                if (this.full_name == "<?= $lead_info["full_name"] ?>")
                    $("#agente_selector").append("<option value=" + this.user + " selected>" + this.full_name + "</option>");
                else
                    $("#agente_selector").append("<option value=" + this.user + ">" + this.full_name + "</option>");
            });
        }, "json");

        get_validation();


    });

    var Table_chamadas = $('#chamadas_realizadas').dataTable({
        "sPaginationType": "full_numbers",
        "oLanguage": {"sUrl": "../../jquery/jsdatatable/language/pt-pt.txt"},
        "aaSorting": [[0, "desc"]]
    });
    //  Table_chamadas.fnSort([[1, 'asc']]);

    /* VARS/DIALOGS */
    var lead_id = '<?= $lead_id; ?>';
    var Campaign_id = '<?= $campaign_id; ?>';
    var $error = $('<div></div>')
            .html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ')
            .dialog({
                autoOpen: false,
                title: "<span style='float:left; margin-right: 4px;' class='ui-icon ui-icon-alert'></span> Erro",
                width: "550",
                height: "250",
                show: "fade",
                hide: "fade",
                buttons: {"OK": function() {
                        $(this).dialog("close");
                    }}
            });
    /* FUNCTIONS */


    $("#inputcontainer input").focus(function()
    {
        $(this).css({"border": "1px solid green"});
    });
    $("#inputcontainer input").blur(function()
    {

        var field = this.name;
        var field_value = $(this).val();
        $(this).css({"border": "1px solid #c0c0c0"});
        $.ajax({
            type: "POST",
            url: "/sips-agente/crm-agent/_requests.php",
            data: {action: "update_contact_field", send_field: field, send_field_value: field_value, send_lead_id: lead_id},
            error: function(jqXHR, textStatus, errorThrown) {
                $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown);
            },
            success: function(data, textStatus, jqXHR)
            {
                if ((textStatus == 'success') && (data == 1))
                {
                    $("#td_" + field).html("<span id='img_fade" + field + "'><table><tr><td style='width:18px'><td style='text-align:left;'>A Gravar</span></tr></table></span>");
                    $("#img_fade" + field).fadeOut(2500);
                }
                else
                {
                    $("#td_" + field).html("<span id='img_fade" + field + "'><table><tr><td style='width:18px'><td style='text-align:left;'>Erro a Gravar</tr></table></span>");
                    $("#img_fade" + field).fadeOut(2500);
                }
            }
        });
    });
    $("#feedback_list").change(function()
    {
        var feedback_id = $("#feedback_list").val();
        var feedback_name = $("#feedback_list option:selected").text();
        $.ajax({
            type: "POST",
            url: "/sips-agente/crm-agent/_requests.php",
            data: {action: "update_feedback", send_lead_id: lead_id, send_feedback: feedback_id},
            error: function(jqXHR, textStatus, errorThrown) {
                $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown);
            },
            success: function(data, textStatus, jqXHR)
            {

                if ((textStatus == 'success') && (data == 1)) {
                    $("#modify_feedback_status").html("<span id='feedback_fade'><table><tr><td style='width:18px'><td style='text-align:left;'>A Gravar</span></tr></table></span>");
                    $("#feedback_fade").fadeOut(2000, function() {
                        $("#modify_feedback_status").html("<i>(O feedback deste contacto pode ser alterado neste menu.)</i>");
                    });
                    $("#lead_info_status").html(feedback_name);
                } else {
                    $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + data);
                }

            }
        });

//show or hide o botao e div consoante feedback é sale ou n
        if ($("#feedback_list option:selected").data("sale") == "Y")
            $("#confirm_feedback").show();
        else
            $("#confirm_feedback").hide();

        $("#confirm_feedback_div").hide(600);
        $("#confirm_feedback").prop('disabled', false);

    });



    $("#confirm_feedback").on("click", function()
    {

        if (!$("#confirm_feedback_div").is(":visible"))
            get_validation(function() {
                $("#confirm_feedback_div").show(600);
            });
        else
            $("#confirm_feedback_div").hide(400);
    });


    //insere nova entrada
    $("#confirm_feedback_button").on("click", function()
    {
        if ($("#textarea_comment").val().length)
            $.post("_requests.php", {action: "add_info_crm", lead_id: lead_id, option: $('input[name="radio_confirm_group"]:checked').val(), campaign: Campaign_id, agent: $("#agente_selector option:selected").val(), comment: $("#textarea_comment").val()},
            function(data)
            {
                get_validation();
            }, "json");
    });


    function get_validation(callback)
    {
        $("#comment_log_tbody").empty();
        $.post("_requests.php", {action: "get_info_crm", lead_id: lead_id, status: $("#feedback_list option:selected").val(), campaign_id: Campaign_id},
        function(data)
        {
            $("#radio_confirm_no").prop("checked", true);
            if (Object.size(data))
            {
                $.each(data, function() {
                    $("#comment_log_tbody").append($("<tr>")
                            .append($("<td>").text(this.comment))
                            .append($("<td>").text(this.feedback))
                            .append($("<td>").text($("#agente_selector option[value=" + this.agent + "]").text()))
                            .append($("<td>").text(this.admin))
                            .append($("<td>").text(this.date))
                            );
                    if (this.sale == "1")
                        $("#radio_confirm_yes").prop("checked", true);
                    else if (this.sale == "0")
                        $("#radio_confirm_no").prop("checked", true);
                    else
                        $("#radio_confirm_return").prop("checked", true);
                    $("#agente_selector option[value=" + this.agent + "]").prop("selected", true);
                    $("#div_comentarios").show();
                    $("#textarea_comment").val("");
                });
            }
            else
            {
                $("#textarea_comment").val("");
                $("#div_comentarios").hide();
                $("#radio_confirm_no").prop("checked", true);
            }
            if (typeof callback === "function")
                callback();
        }, "json");
    }


    Object.size = function(a)
    {
        var count = 0;
        var i;
        for (i in a) {
            if (a.hasOwnProperty(i)) {
                count++;
            }
        }
        return count;
    };


</script>