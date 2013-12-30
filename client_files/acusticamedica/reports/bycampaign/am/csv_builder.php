<?
require('../../../../../ini/dbconnect_noutf8.php');
require('../../../../../functions/functions.php');
//require('../functions/functions.php');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=report_AM_campanha.csv');    



$data_inicial = DatePT2DateSQL($_POST['data_inicial']);
$data_final = DatePT2DateSQL($_POST['data_final']);
if($data_final==$data_inicial) { $data_final = date("Y-m-d", strtotime("+1 day".$data_final)); }




    
$SelectedCampaigns = $_POST['camp_options'];
$CountSelectedCampaigns = count($SelectedCampaigns);
for ($i=0;$i<$CountSelectedCampaigns;$i++)
    {
    if ($CountSelectedCampaigns == 1) 
        {
        $campaigns_IN = "'".$SelectedCampaigns[$i]."'"; 
        }
    elseif ($CountSelectedCampaigns - 1 == $i)
        {
        $campaigns_IN .= "'".$SelectedCampaigns[$i]."'";    
        }    
    else
        {
        $campaigns_IN .= "'".$SelectedCampaigns[$i]."',";
        }
    }

$SelectedFeedbacks = $_POST['feed_options'];
$CountSelectedFeedbacks = count($SelectedFeedbacks);
for ($i=0;$i<$CountSelectedFeedbacks;$i++)
    {
    if ($CountSelectedFeedbacks == 1) 
        {
        $feedbacks_IN = "'".$SelectedFeedbacks[$i]."'"; 
        }
    elseif ($CountSelectedFeedbacks - 1 == $i)
        {
        $feedbacks_IN .= "'".$SelectedFeedbacks[$i]."'";    
        }    
    else
        {
        $feedbacks_IN .= "'".$SelectedFeedbacks[$i]."',";
        }
    }
    
//
$output = fopen('php://output', 'w');
fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        '',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'),";");

    //MANUAIS
    
    $queryshow = "SHOW TABLES LIKE 'custom_%'";
    $queryshow = mysql_query($queryshow, $link);
    $build_query = "";
    
    for($f=0;$f<mysql_num_rows($queryshow);$f++)
    {
    $rowshow = mysql_fetch_row($queryshow);
    if(($f+1)==mysql_num_rows($queryshow)){ $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) "; } else { $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) UNION ALL ";}
    } 
    
     //$query = "SELECT * FROM `vicidial_list` A LEFT JOIN ( $build_query ) B on A.lead_id=B.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) C on A.status=C.status WHERE  A.list_id IN('998', '0', '999') AND A.status IN($feedbacks_IN) AND A.last_local_call_time BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.last_local_call_time";
    //$query = mysql_query($query) or die(mysql_error());

    
    $query = "SELECT *, B.status AS 'RealStatus' FROM vicidial_list A INNER JOIN vicidial_log B ON A.lead_id=B.lead_id LEFT JOIN ( $build_query ) C on A.lead_id=C.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) D on B.status=D.status WHERE B.status IN($feedbacks_IN) AND B.list_id IN('999','998','0') AND B.call_date BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.lead_id";
    //echo $query;
    $query = mysql_query($query) or die(mysql_error());
    while ($row = mysql_fetch_assoc($query)) {
                
            $cod = "";
            
            if ($row['tipoconsulta'] == 'CATOS') { $cod = $row['consultorio']; } else { if($row['tipoconsulta'] == 'Branch') { $cod = $row['consultoriodois']; }}
            
            $campid=$row['extra1'];

            $no=$row['extra2'];
            

            $c_message = "Manual";
     /*       if($row['status']=="MARC" || $row['status']=="NOVOCL") {
            $c_message = "";
            $e_message = "";
            $f_message = "";
            switch($row['tipoconsulta']){
            
            case "CATOS": { $cod = $row['consultorio']; 
                if(        ($row['consultorio'] == "" || $row['consultorio'] == "nenhum" || $row['consultorio'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem CATOS Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            
            case "Branch": { $cod = $row['consultoriodois']; 
                if(        ($row['consultoriodois'] == "" || $row['consultoriodois'] == "nenhum" || $row['consultoriodois'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem Branch Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "Home": { $cod = ""; if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case "semconsulta": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case null: { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            }
            }  */
        
            fputcsv($output, array(
            
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                "",
                $row['vendor_lead_code'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $row['marchora'],
                $row['marcdata'],  
                $row['tipoconsulta'],
                "",
                $row['obs'],
                "",
                "",
                "",
                $row['user'],
                $row['status_name'],
                $c_message,
                $row['call_date']
            
            ),";"); }

    //INBOUND
    
    $queryshow = "SHOW TABLES LIKE 'custom_%'";
    $queryshow = mysql_query($queryshow, $link);
    $build_query = "";
    
    
    for($f=0;$f<mysql_num_rows($queryshow);$f++)
    {
    $rowshow = mysql_fetch_row($queryshow);
    if(($f+1)==mysql_num_rows($queryshow)){ $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) "; } else { $build_query .= " (SELECT lead_id, tipoconsulta, consultorio, consultoriodois, marcdata, marchora, obs from $rowshow[0]) UNION ALL ";}
    } 
    
    $query = "SELECT *, B.status as 'Real Status' FROM vicidial_list A INNER JOIN vicidial_closer_log B ON A.lead_id=B.lead_id LEFT JOIN ( $build_query ) C on A.lead_id=C.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) D on B.status=D.status WHERE B.status IN($feedbacks_IN) AND B.call_date BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.lead_id";
    //echo $query;
    $query = mysql_query($query) or die(mysql_error());
    while ($row = mysql_fetch_assoc($query)) {
                
            $cod = "";
            
            if ($row['tipoconsulta'] == 'CATOS') { $cod = $row['consultorio']; } else { if($row['tipoconsulta'] == 'Branch') { $cod = $row['consultoriodois']; }}
            
            $campid=$row['extra1'];

            $no=$row['extra2'];
            
            //if (($row['extra1']==null) || ($row['extra1'])=="") {$campid=$row['source_id'];}
            //if (($row['extra2']==null) || ($row['extra2'])=="") {$no=$row['comments'];}
            $c_message = "Inbound";
      /*      if($row['status']=="MARC" || $row['status']=="NOVOCL") {
            $c_message = "";
            $e_message = "";
            $f_message = "";
            switch($row['tipoconsulta']){
            
            case "CATOS": { $cod = $row['consultorio']; 
                if(        ($row['consultorio'] == "" || $row['consultorio'] == "nenhum" || $row['consultorio'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem CATOS Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            
            case "Branch": { $cod = $row['consultoriodois']; 
                if(        ($row['consultoriodois'] == "" || $row['consultoriodois'] == "nenhum" || $row['consultoriodois'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem Branch Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "Home": { $cod = ""; if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case "semconsulta": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case null: { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            }
            }    */
        
            fputcsv($output, array(
            
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                "",
                $row['vendor_lead_code'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $row['marchora'],
                $row['marcdata'],  
                $row['tipoconsulta'],
                "",
                $row['obs'],
                "",
                "",
                "",
                $row['user'],
                $row['status_name'],
                $c_message,
                $row['call_date']
            
            ),";"); }

    //OUTBOUND

    for($i=0;$i<count($SelectedCampaigns);$i++) {
    
    $sQuery = "SELECT list_id FROM vicidial_lists WHERE campaign_id = '$SelectedCampaigns[$i]' ";
    $sQuery = mysql_query($sQuery, $link) or die(mysql_error());            
    $CountDBs = mysql_num_rows($sQuery);
    $lists_IN = "";
    for ($p=0;$p<$CountDBs;$p++)
    {
        $row = mysql_fetch_row($sQuery);
        if ($CountDBs == 1) 
            {
            $lists_IN = "'".$row[0]."'"; 
            }
        elseif ($CountDBs - 1 == $p)
            {
            $lists_IN .= "'".$row[0]."'";    
            }    
        else
            {
            $lists_IN .= "'".$row[0]."',";
            }
    }
        
    
    $SelectedCampaigns[$i] = strtoupper($SelectedCampaigns[$i]);
    
    $sQuery = "SHOW TABLES LIKE 'custom_$SelectedCampaigns[$i]'"; 

    $rQuery = mysql_query($sQuery, $link);
    $TablesToPrint = mysql_num_rows($rQuery);
    if ($TablesToPrint > 0) 
        {    

        //$sQuery = "SELECT * FROM vicidial_list A LEFT JOIN custom_$SelectedCampaigns[$i] B ON A.lead_id=B.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) C on A.status=C.status INNER JOIN vicidial_lists D ON A.list_id=D.list_id INNER JOIN (SELECT campaign_id, campaign_name FROM vicidial_campaigns GROUP BY campaign_name) E ON D.campaign_id=E.campaign_id WHERE A.list_id IN($lists_IN) AND A.status IN($feedbacks_IN) AND A.last_local_call_time BETWEEN '$data_inicial' AND '$data_final' ORDER BY A.last_local_call_time                       ";
        
        $sQuery = "SELECT * FROM vicidial_list A INNER JOIN vicidial_log B ON A.lead_id=B.lead_id LEFT JOIN custom_$SelectedCampaigns[$i] C on A.lead_id=C.lead_id INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) D on B.status=D.status INNER JOIN vicidial_lists E ON A.list_id=E.list_id INNER JOIN (SELECT campaign_id, campaign_name FROM vicidial_campaigns GROUP BY campaign_name) F ON E.campaign_id=F.campaign_id  WHERE B.status IN($feedbacks_IN) AND B.call_date BETWEEN '$data_inicial' AND '$data_final' AND (B.list_id IN($lists_IN)) ORDER BY A.lead_id";
        //echo $sQuery;
        $rQuery = mysql_query($sQuery, $link) or die(mysql_error()); 

            while ($row = mysql_fetch_assoc($rQuery)) {
                
            $cod = "";
            
            
            
            $campid=$row['extra1'];


            $no=$row['extra2'];
            
            if (($row['extra1']==null) || ($row['extra1'])=="") {$campid=$row['source_id'];}
            if (($row['extra2']==null) || ($row['extra2'])=="") {$no=$row['comments'];}
            

            // controlo de qualidade dos dados
            if($row['status']=="MARC" || $row['status']=="NOVOCL") {
            
            $c_message = "";
            $e_message = "";
            $f_message = "";
            switch($row['tipoconsulta']){
            
            case "CATOS": { $cod = $row['consultorio']; 
                if(        ($row['consultorio'] == "" || $row['consultorio'] == "nenhum" || $row['consultorio'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem CATOS Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            
            case "Branch": { $cod = $row['consultoriodois']; 
                if(        ($row['consultoriodois'] == "" || $row['consultoriodois'] == "nenhum" || $row['consultoriodois'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Sem Branch Associado "; } 
                if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "Home": { $cod = ""; if(        ($row['marcdata'] == "0000-00-00" || $row['marcdata'] == "" || $row['marcdata'] == null)         ){  $c_message = " Dados Incompletos "; $e_message .= "| Data com Erros "; } } break;
            case "": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case "semconsulta": { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            case null: { $c_message = " Lead Duplicada = Ignorar / Dados Incompletos "; $e_message .= "| Sem Tipo de Consulta "; } break;
            }
            
            $f_message = $c_message . $e_message; }
            
    /*        if($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") { $c_message .= " | Lead Duplicada - Ignorar/Dados Incompletos | Erro: Tipo Consulta"; }
            if(        ($row['consultorio'] != "" && $row['consultorio']!= "nenhum" && $row['consultorio'] != null) && ($row['tipoconsulta']!="CATOS")             ) {$c_message .= " | Lead Duplicada - Ignorar/Dados Incompletos | Erro: CATOS";} 
            if(        ($row['consultoriodois'] != "" && $row['consultoriodois']!= "nenhum" && $row['consultoriodois'] != null) && ($row['tipoconsulta']!="Branch")             ) {$c_message .= " | Lead Duplicada - Ignorar/Dados Incompletos | Erro: Branch";} 

    */        
            fputcsv($output, array(
            
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                "",
                $row['vendor_lead_code'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $row['marchora'],
                $row['marcdata'],  
                $row['tipoconsulta'],
                "",
                $row['obs'],
                "",
                "",
                "",
                $row['user'],
                $row['status_name'],
                $row['campaign_name'],
                $row['call_date'],
                $f_message
            
            ),";"); }

            }
        }

?>