


<?php
require("dbconnect.php");
$self=count(explode('/', $_SERVER['PHP_SELF']));
for($i=0;$i<$self-2;$i++){$header.="../";} 
define("ROOT", $header);

foreach ($_POST as $key => $value) { 
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


$PHP_AUTH_USER=$_SERVER['PHP_AUTH_USER'];
$PHP_AUTH_PW=$_SERVER['PHP_AUTH_PW'];
$PHP_SELF=$_SERVER['PHP_SELF'];


$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

$STARTtime = date("U");
$TODAY = date("Y-m-d");
$NOW_TIME = date("Y-m-d H:i:s");

#############################################
##### START SYSTEM_SETTINGS LOOKUP #####
$stmt = "SELECT use_non_latin,custom_fields_enabled FROM system_settings;";
$rslt=mysql_query($stmt, $link);
if ($DB) {echo "$stmt\n";}
$qm_conf_ct = mysql_num_rows($rslt);
if ($qm_conf_ct > 0)
	{
	$row=mysql_fetch_row($rslt);
	$non_latin =				$row[0];
	$custom_fields_enabled =	$row[1];
	}
##### END SETTINGS LOOKUP #####
###########################################

if ($non_latin < 1)
	{
	$PHP_AUTH_USER = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("[^-_0-9a-zA-Z]","",$PHP_AUTH_PW);

	$old_phone = ereg_replace("[^0-9]","",$old_phone);
	$phone_number = ereg_replace("[^0-9]","",$phone_number);
	$alt_phone = ereg_replace("[^0-9]","",$alt_phone);
	}	# end of non_latin
else
	{
	$PHP_AUTH_USER = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_USER);
	$PHP_AUTH_PW = ereg_replace("'|\"|\\\\|;","",$PHP_AUTH_PW);
	}

if (strlen($phone_number)<6) {$phone_number=$old_phone;}

$stmt="SELECT count(*) from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW' and user_level > 7 and modify_leads='1';";
if ($DB) {echo "|$stmt|\n";}
if ($non_latin > 0) {$rslt=mysql_query("SET NAMES 'UTF8'");}
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
$auth=$row[0];

if ($WeBRooTWritablE > 0)
	{$fp = fopen ("./project_auth_entries.txt", "a");}

$date = date("r");
$ip = getenv("REMOTE_ADDR");
$browser = getenv("HTTP_USER_AGENT");

if( (strlen($PHP_AUTH_USER)<2) or (strlen($PHP_AUTH_PW)<2) or (!$auth))
	{
    Header("WWW-Authenticate: Basic realm=\"SIPS Call-Center\"");
    Header("HTTP/1.0 401 Unauthorized");
    echo "Invalid Username/Password: |$PHP_AUTH_USER|$PHP_AUTH_PW|\n";
    exit;
	}
else
	{

	if($auth>0)
		{
		$stmt="SELECT full_name,modify_leads from vicidial_users where user='$PHP_AUTH_USER' and pass='$PHP_AUTH_PW'";
		$rslt=mysql_query($stmt, $link);
		$row=mysql_fetch_row($rslt);
		$LOGfullname				=$row[0];
		$LOGmodify_leads			=$row[1];

		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|GOOD|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|$LOGfullname|\n");
			fclose($fp);
			}
		}
	else
		{
		if ($WeBRooTWritablE > 0)
			{
			fwrite ($fp, "VICIDIAL|FAIL|$date|$PHP_AUTH_USER|$PHP_AUTH_PW|$ip|$browser|\n");
			fclose($fp);
			}
		}
	}

$label_title =				'Title';
$label_first_name =			'First';
$label_middle_initial =		'MI';
$label_last_name =			'Last';
$label_address1 =			'Address1';
$label_address2 =			'Address2';
$label_address3 =			'Address3';
$label_city =				'City';
$label_state =				'State';
$label_province =			'Province';
$label_postal_code =		'Postal Code';
$label_vendor_lead_code =	'Vendor ID';
$label_gender =				'Gender';
$label_phone_number =		'Phone';
$label_phone_code =			'DialCode';
$label_alt_phone =			'Alt. Phone';
$label_security_phrase =	'Show';
$label_email =				'Email';
$label_comments =			'Comments';

### find any custom field labels
$stmt="SELECT label_title,label_first_name,label_middle_initial,label_last_name,label_address1,label_address2,label_address3,label_city,label_state,label_province,label_postal_code,label_vendor_lead_code,label_gender,label_phone_number,label_phone_code,label_alt_phone,label_security_phrase,label_email,label_comments from system_settings;";
$rslt=mysql_query($stmt, $link);
$row=mysql_fetch_row($rslt);
if (strlen($row[0])>0)	{$label_title =				$row[0];}
if (strlen($row[1])>0)	{$label_first_name =		$row[1];}
if (strlen($row[2])>0)	{$label_middle_initial =	$row[2];}
if (strlen($row[3])>0)	{$label_last_name =			$row[3];}
if (strlen($row[4])>0)	{$label_address1 =			$row[4];}
if (strlen($row[5])>0)	{$label_address2 =			$row[5];}
if (strlen($row[6])>0)	{$label_address3 =			$row[6];}
if (strlen($row[7])>0)	{$label_city =				$row[7];}
if (strlen($row[8])>0)	{$label_state =				$row[8];}
if (strlen($row[9])>0)	{$label_province =			$row[9];}
if (strlen($row[10])>0) {$label_postal_code =		$row[10];}
if (strlen($row[11])>0) {$label_vendor_lead_code =	$row[11];}
if (strlen($row[12])>0) {$label_gender =			$row[12];}
if (strlen($row[13])>0) {$label_phone_number =		$row[13];}
if (strlen($row[14])>0) {$label_phone_code =		$row[14];}
if (strlen($row[15])>0) {$label_alt_phone =			$row[15];}
if (strlen($row[16])>0) {$label_security_phrase =	$row[16];}
if (strlen($row[17])>0) {$label_email =				$row[17];}
if (strlen($row[18])>0) {$label_comments =			$row[18];}


### find out if status(dispo) is a scheduled callback status
$scheduled_callback='';
$stmt="SELECT scheduled_callback from vicidial_statuses where status='$dispo';";
$rslt=mysql_query($stmt, $link);
$scb_count_to_print = mysql_num_rows($rslt);
if ($scb_count_to_print > 0) 
	{
	$row=mysql_fetch_row($rslt);
	if (strlen($row[0])>0)	{$scheduled_callback =	$row[0];}
	}
$stmt="SELECT scheduled_callback from vicidial_campaign_statuses where status='$dispo';";
$rslt=mysql_query($stmt, $link);
$scb_count_to_print = mysql_num_rows($rslt);
if ($scb_count_to_print > 0) 
	{
	$row=mysql_fetch_row($rslt);
	if (strlen($row[0])>0)	{$scheduled_callback =	$row[0];}
	}

?>

<?php #HEADER
/*
 */


include("functions.php");


####################################################################### 
### BEGIN  - Created by kant
#######################################################################
if(($_POST['stage'])=='modify_lead_info')
{ 
	$lead_id=$_POST['lead_id'];
	$i=0;
	$post_count=(count($_POST)-2);
	foreach ($_POST as $key => $value)
	{
		if(eregi('leadinfo#', $key))
		{
			$i++;
			$temp=explode("#", $key);
			$row=$temp[1];
			if ($post_count == 1) 
				{
				$mli_set = $row."='".$value."'";
				}
			elseif ($post_count == $i)
				{
				$mli_set .= $row."='".$value."'";
				}	
			else
				{
				$mli_set .= $row."='".$value."',";
				}
		}
	}
	mysql_query("UPDATE vicidial_list SET $mli_set WHERE lead_id='$lead_id'") or die(mysql_error());
}
if(($_POST['stage'])=='modify_status')
{
	$explode_array=explode("###",$_POST['feedback_list']);
	$new_feedback=$explode_array[0];
	$is_callback=$explode_array[1];
	
}
### Dados da Lead
$query="SELECT 
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
			vicidial_list vdl 
		left JOIN 
			vicidial_lists vdlf 
		ON 
			vdl.list_id=vdlf.list_id 						
		left JOIN 
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
		LIMIT 1;";
			
$query=mysql_query($query,$link) or die(mysql_error());
$lead_info=mysql_fetch_assoc($query);

if($lead_info['status_name_one']==NULL)
{$status_name=$lead_info['status_name_two'];} 
else 
{$status_name=$lead_info['status_name_one'];}

### Dados do Contacto
$query="SELECT 
			Name,
			Display_name
		FROM 
			vicidial_list_ref
		WHERE
			campaign_id='$lead_info[campaign_id]'
		AND
			active=1";
            
$query=mysql_query($query,$link) or die(mysql_error());
$fields_count = mysql_num_rows($query);
for ($i=0;$i<$fields_count;$i++)
{
	$row = mysql_fetch_row($query);
	if ($fields_count == 1) 
		{
		$fields_NAME[$i]= strtolower($row[0]);
		$fields_SELECT = $row[0]; 
		$fields_LABEL[$i] = $row[1]; 
		}
	elseif ($fields_count-1 == $i)
		{
		$fields_NAME[$i]= strtolower($row[0]);
		$fields_SELECT .= $row[0];	
		$fields_LABEL[$i] = $row[1]; 
		}	
	else
		{
		$fields_NAME[$i]= strtolower($row[0]);
		$fields_SELECT .= $row[0].","; 
		$fields_LABEL[$i] = $row[1]; 
		}
}	
$query="SELECT 
			$fields_SELECT 
		FROM 
			vicidial_list
		WHERE
			lead_id='$lead_id' 
		LIMIT 1";
        
$query=mysql_query($query,$link);
$fields = mysql_fetch_row($query);

### Construção da Lista de Feedbacks
$query="SELECT status,status_name,scheduled_callback FROM vicidial_campaign_statuses WHERE campaign_id='$lead_info[campaign_id]'";
$query=mysql_query($query,$link) or die(mysql_error());
$is_campaign_feedback=0;

for($i=0;$i<mysql_num_rows($query);$i++)
{
	$row=mysql_fetch_assoc($query) or die(mysql_query());
	
	if($row['status']==$lead_info['status'])
	{ # feedback actual (selected)
		$status_options .= "<option selected value='$row[status]'>$row[status_name]</option>\n";
		$is_campaign_feedback=1;
                $is_cb = $row['scheduled_callback'];
	}
	else
	{ # outros feedbacks da campanha
		$status_options .= "<option value='$row[status]'>$row[status_name]</option\n>";
	}
}

if(!$is_campaign_feedback)
{ # caso se o feedback actual seja de sistema
	$query="SELECT status,status_name,scheduled_callback FROM vicidial_statuses WHERE status='$lead_info[status]'";
	$query=mysql_query($query,$link) or die(mysql_error());
	$row=mysql_fetch_assoc($query);
	$status_options .= "<option selected value='$row[status]'>$row[status_name]</option>";
        $is_cb = $row['scheduled_callback'];
}


### Chamadas Feitas
$query = "SELECT 									
			vl.uniqueid, 
			vl.lead_id, 
			vl.list_id,
			vl.campaign_id,
			DATE_FORMAT(vl.call_date,'%d-%m-%Y') AS data,
			DATE_FORMAT(vl.call_date,'%H:%i:%s') AS hora,
			vl.start_epoch,
			vl.end_epoch,
			vl.length_in_sec,
			vl.status,
			vl.phone_code,
			vl.phone_number,
			vl.user,
			ifnull(vl.comments,'AUTO') comments,
			vl.processed,
			vl.user_group,
			vl.term_reason,
			vl.alt_dial,
			vu.full_name,
			(select status_name from vicidial_campaign_statuses where status=vl.status limit 1) as status_name1,
			(select status_name from vicidial_statuses where status=vl.status limit 1) as status_name2,
			vc.campaign_name,
			vls.list_name
		FROM 
			vicidial_log vl
		INNER JOIN vicidial_users vu ON vl.user=vu.user
		INNER JOIN vicidial_campaigns vc ON vl.campaign_id=vc.campaign_id 
		INNER JOIN vicidial_lists vls ON vl.list_id=vls.list_id
			
		WHERE 
			vl.lead_id='$lead_id' 
		ORDER BY
			uniqueid 
		DESC LIMIT 500;";
$chamadas_feitas=mysql_query($query, $link) or die(mysql_error());

#Gravações da lead
$query = "	SELECT 
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
?>


<!DOCTYPE html>
<html>
    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>Go Contact Center</title>

        
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/chosen.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/demo_table.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/animate.min.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/datetimepicker.css" />

    <div class="grid">
    <div class="grid-title">
        <div class="pull-left">CRM - Gestão de Leads</div>
        <div class="pull-right"><button class="btn btn-large btn-primary" onclick='CloseHTML();'>Fechar</button></div>
        <div class="clear"></div>
    </div>
    
    <div class='grid-content'>
    <?php
        if ($lead_info)
    ?>
    <table class="table table-mod table-bordered" id="ref-table">
        
            <thead> 
                <th>ID do Contacto</th>
                <th>Número de Telefone</th>
                <th>Base de Dados</th>
                <th>Campanha</th>
                <th>Operador</th>
                <th>Feedback</th>
                <th>Nº de Chamadas</th>
            </thead>
            <tbody>
                <tr>
                    <td><? echo "$lead_id"; ?></td>
                    <td><? echo "$lead_info[phone_number]"; ?></td>
                    <td><? echo "$lead_info[list_name]"; ?></td>
                    <td><? echo "$lead_info[campaign_name]"; ?></td>
                    <td><? echo "$lead_info[full_name]"; ?></td>
                    <td>
                        <div>
                            <select style='margin-bottom:0px' name=feedback_list id=feedback_list onchange='feedback_change(this.value);'>
                                <?= $status_options; ?>
                            </select>
                                <button class="btn icon-alone" id='btnCB' <?= ($is_cb == 'N') ? "style='display:none'" : "" ?> name='btnCB' title='Alterar Call-Back' onclick="$('#cbManager').modal('show');"><i class="icon-time "></i></button>
                        </div>
                    </td>
                    <td><? echo "$lead_info[called_count]"; ?></td>
                </tr>
            </tbody>
        </table>
</div>


<div class='grid-content'>
<table class='table table-mod table-bordered'>
    <thead> 
            <th>Data de Carregamento</th>
            <th>Hora de Carregamento</th>
            <th>Data da Última Chamada</th>
            <th>Hora da Última Chamada</th>
    </thead>
    <tbody>
        <tr>    <? echo "
                <td>$lead_info[data_load]</td>
                <td>$lead_info[hora_load]</td>
                <td>$lead_info[data_last]</td>
                <td>$lead_info[hora_last]</td> "; ?>
        </tr>
    </tbody>
</table>
</div>
<?php
    for($i=0;$i<count($fields);$i++) {
        if ($i % 2) {
            
           $divOne .= "<div class='control-group'><label class='control-label' for='$fields_NAME[$i]'> $fields_LABEL[$i] </label>
           <div class='controls'><input type=text class='span' name='$fields_NAME[$i]' id='$fields_NAME[$i]' size=80 maxlength=80 value='$fields[$i]'></div></div>";
        } else {
         $divTwo .= "<div class='control-group'><label class='control-label' for='$fields_NAME[$i]'> $fields_LABEL[$i] </label>
            <div class='controls'><input type=text class='span' name='$fields_NAME[$i]' id='$fields_NAME[$i]' size=80 maxlength=80 value='$fields[$i]'></div></div>";   
        }
     }?>        
        <div class='grid-content'>
            <legend>Dados do Contacto</legend>
            <div class='row-fluid'>
                <form class='form-horizontal '>
                    <div class="span6">
                        <?= $divOne; ?>
                    </div>
                    <div class="span6 ">
                        <?= $divTwo; ?>
                    </div>       
                </form>
            </div>
        </div>
    </div>
<div class="modal hide fade" id='cbManager'>
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>Gestão de Call-Backs</h3>
  </div>
    <div class="modal-body">
        
          <input size="16" type="text" value="" readonly class="form_datetime">
        
        <p>One fine body…</p>
    </div>
  <div class="modal-footer">
    <a href="#" class="btn btn-primary">Salvar</a>
  </div>
</div>

    <div class="table-title"><center>Chamadas realizadas para este Contacto</center></div>
        <div class="datagrid" style="width:90%">
            <table>
                <thead><th>Data</th><th>Hora</th><th>Duração</th><th>Número</th><th>Operador</th><th>Feedback</th><th>Man/Auto</th><th>Campanha</th><th>Base de Dados</th></thead>';
                <tbody>
<?
for($i=0;$i<mysql_num_rows($chamadas_feitas);$i++){
$row = mysql_fetch_assoc($chamadas_feitas);

$duracao = sec_convert($row['length_in_sec'],"H");
if($row[status_name1]){$status_name=$row['status_name1'];}else{$status_name=$row['status_name2'];}
echo "
<tr>
<td>$row[data]</td>
<td>$row[hora]</td>
<td>$duracao</td>
<td>$row[phone_number]</td>
<td>$row[full_name]</td>
<td>$status_name</td>
<td>$row[comments]</td>
<td>$row[campaign_name]</td>
<td>$row[list_name]</td>
</tr>
";
} ?>
</tbody>
</table>
</div>
<br>
<div class="table-title"><center>Gravações deste Contacto</center></div>
<div class="datagrid" style="width:90%">
<table>
<thead> <th>Data</th> <th>Inicio da Gravação</th> <th>Fim da Gravação</th> <th>Duração</th> <th>Ouvir Gravação</th> <th>Operador</th> </thead>';
<tbody>
<?
    for($i=0;$i<mysql_num_rows($gravacoes);$i++){
    $row = mysql_fetch_assoc($gravacoes);
    $duracao = sec_convert($row['length_in_sec'],"H");
    echo "
    <tr>
    <td>$row[data]</td>
    <td>$row[hora_inicio]</td>
    <td>$row[hora_fim]</td>
    <td>$duracao</td>
    <td><a href='$row[location]'><img src='../images/icons/sound_add_16.png'></a></td>
    <td>$row[full_name]</td>

    </tr>
    ";
    } ?>
</tbody>
</table>
</div>
        
<script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
<script type="text/javascript" src="/bootstrap/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="/bootstrap/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="/bootstrap/js/bootstrap-tagmanager.js"></script>
<script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
<script type="text/javascript" src="/bootstrap/js/datetimepicker/bootstrap-datetimepicker.min.js"></script>    
<script type="text/javascript" src="/bootstrap/js/moment.min.js"></script>
<script type="text/javascript" src="/bootstrap/js/moment.langs.min.js"></script>
    
<script type="text/javascript">
/* VARS/DIALOGS */
var lead_id ='<?= $lead_id ?>';
$(function(){
      $(".form_datetime").datetimepicker({
          format: 'yyyy-mm-dd hh:ii',
          startDate: moment().format("YYYY-MM-DD HH:mm"),
          autoclose: true,
          todayBtn: true,
          pickerPosition: "bottom-left",
          //startDate: moment().format('yyyy-mm-dd hh:ii')
      });
});

function feedback_change(val){
    $.ajax({
		type: "POST",
		url: "../../requests/admin_lead_modify_requests.php",
		data: {action: "update_status", send_feedback: val, send_lead_id: lead_id },
		success: function(isCB)
		{   console.log(isCB);
                    if (isCB == 'Y') {
                        $("#btnCB").show();
                        $('#cbManager').modal('show');
                    } else { $("#btnCB").hide(); }
                }
            });
}
$("#inputcontainer input").focus(function() 
{
	$(this).css({"border":"1px solid green"}); 

}); 
$("#inputcontainer input").blur(function()
{
	
	var field = this.name;
	var field_value = $(this).val();
	$(this).css({"border":"1px solid #c0c0c0"}); 	
	
	$.ajax({
		type: "POST",
		url: "../../requests/admin_lead_modify_requests.php",
		data: {action: "update_contact_field", send_field: field, send_field_value: field_value, send_lead_id: lead_id },
		error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
		success: function(data, textStatus, jqXHR)
		{  
			if((textStatus=='success') && (data=='')) 
			{
			$("#td_"+field).html("<span id='img_fade" + field + "'><table><tr><td style='width:18px'><img src='../../images/icons/clock_add_16.png'><td style='text-align:left;'>A Gravar</span></tr></table></span>");
			$("#img_fade"+field).fadeOut(2500);
			}  
			else
			{
			$("#td_"+field).html("<span id='img_fade" + field + "'><table><tr><td style='width:18px'><img src='../../images/icons/clock_red_16.png'><td style='text-align:left;'>Erro a Gravar</tr></table></span>");
			$("#img_fade"+field).fadeOut(2500);
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
	  url: "../../requests/admin_lead_modify_requests.php",
	  data: {action: "update_feedback", send_lead_id: lead_id, send_feedback: feedback_id},
	  error: function(jqXHR, textStatus, errorThrown) { $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + errorThrown)},
	  success: function(data, textStatus, jqXHR)
	  {   
	
	  if((textStatus=='success') && (data=='')) {
		  $("#modify_feedback_status").html("<span id='feedback_fade'><table><tr><td style='width:18px'><img src='../../images/icons/clock_add_16.png'><td style='text-align:left;'>A Gravar</span></tr></table></span>");
		  $("#feedback_fade").fadeOut(2000, function(){$("#modify_feedback_status").html("<i>(O feedback deste contacto pode ser alterado neste menu.)</i>");});
		  $("#lead_info_status").html(feedback_name);
		  } else {
			  $error.dialog("open").html('Ocorreu um erro.<br><br>Por favor tente novamente.<br><br> Mensagem de Erro: ' + data);
	  }
	
	  }
	  });	
	
});
</script>








