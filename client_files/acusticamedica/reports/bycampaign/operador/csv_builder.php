<?
// REQUIRES
require('../../../../../ini/dbconnect_noutf8.php');
require('../../../../../functions/functions.php');

// DATES
$data_inicial = DatePT2DateSQL($_POST['data_inicial']);
$data_final = DatePT2DateSQL($_POST['data_final']);
if($data_final==$data_inicial) { $data_final = date("o-m-d", strtotime("+1 day".$data_final)); }

//HEADER CONFIG
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=feedbacks_por_operador_'.$data_inicial.'_'.$data_final.'.csv'); 

// USERS
if(isset($_POST['active_agents'])){ $user_SQL = "AND active = 'Y'"; } else { $user_SQL = ""; }
$query = "  SELECT user, full_name FROM vicidial_users WHERE user_level = '1' $user_SQL ";
$query = mysql_query($query, $link) or die(mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
    $row=mysql_fetch_assoc($query);
    $user[$i] = $row['user'];
    $full_name[$i] = $row['full_name']; 
}

// CSV HEADER 
$output = fopen('php://output', 'w');
fputcsv($output, array(" "),";");
fputcsv($output, array(" ", "Report:","Feedbacks por Campanhas"),";");
fputcsv($output, array(" ","De:", $data_inicial), ";");
fputcsv($output, array(" ","A:", $data_final), ";");
fputcsv($output, array(" "),";");

//--> WITH GROUPED CAMPAINGS
if(isset($_POST['group_camps'])){

// CAMPS IN FROM POST   
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
// LISTS IN FROM DB   
$query = "  SELECT list_id FROM vicidial_lists WHERE campaign_id IN ($campaigns_IN) AND list_id<>'998'  ";
$query = mysql_query($query, $link) or die(mysql_error());            
$listas_count = mysql_num_rows($query);
for ($p=0;$p<$listas_count;$p++)
    {
    $listas = mysql_fetch_row($query);
    if ($listas_count == 1) 
        {
        $listas_IN = "'".$listas[0]."'"; 
        }
    elseif ($listas_count-1 == $p)
        {
        $listas_IN .= "'".$listas[0]."'";    
        }    
    else
        {
        $listas_IN .= "'".$listas[0]."',"; 
        }
    }    
// FOREACH USER    
foreach ($user as $key=>$value)
{




    fputcsv($output, array(" "),";");    
    fputcsv($output, array(" ","$value", "Feedback", "Contagem"),";"); 

    $query ="   SELECT status_name, count(A.status) FROM vicidial_list A INNER JOIN (SELECT status,status_name FROM vicidial_campaign_statuses GROUP BY status UNION ALL SELECT status,status_name FROM vicidial_statuses GROUP BY status) B ON A.status=B.status WHERE user='$value' AND list_id IN($listas_IN ,'999','998') AND last_local_call_time BETWEEN '$data_inicial' AND '$data_final' GROUP BY A.status ORDER BY B.status_name   ";
    
    
    
    $query = mysql_query($query, $link) or die(mysql_error());
    
    while ($row = mysql_fetch_assoc($query))
    {
         fputcsv($output, array(" ", " ", $row['status_name'], $row['count(A.status)'] ),";");    
    }





}   
    




    
//    
} else {
//

// CAMPANHAS IN
$camp_options = $_POST['camp_options'];
$campanhas_count = count($camp_options);
for ($i=0;$i<$campanhas_count;$i++)
    {
    if ($campanhas_count == 1) 
        {
        $camps_IN = "'".$camp_options[$i]."'"; 
        }
    elseif ($campanhas_count-1 == $i)
        {
        $camps_IN .= "'".$camp_options[$i]."'";    
        }    
    else
        {
        $camps_IN .= "'".$camp_options[$i]."',";
        }
    }

foreach ($camp_options as $key1=>$value1)
{
$query = "  SELECT list_id FROM vicidial_lists WHERE campaign_id ='$value1' AND list_id<>'998'  ";
        
$query = mysql_query($query, $link) or die(mysql_error());            
$listas_count = mysql_num_rows($query);
$listas_IN='';
for ($p=0;$p<$listas_count;$p++)
{
    $listas = mysql_fetch_row($query);
    if ($listas_count == 1) 
        {
        $listas_IN = "'".$listas[0]."'"; 
        }
    elseif ($listas_count-1 == $p)
        {
        $listas_IN .= "'".$listas[0]."'";    
        }    
    else
        {
        $listas_IN .= "'".$listas[0]."',"; 
        }
     
}
    
    
    
$query = "  SELECT campaign_name FROM vicidial_campaigns WHERE campaign_id = '$value1'";
$query = mysql_query($query, $link) or die(mysql_error());    
$camp_name= mysql_fetch_assoc($query);    


fputcsv($output, array(" "),";");    
fputcsv($output, array(" ", "Campanha:", $camp_name['campaign_name']),";");


$counter=0;
$counter_marc=0;


foreach ($user as $key=>$value)
{

fputcsv($output, array(" "),";");    
fputcsv($output, array(" ","$value", "Feedback", "Contagem"),";");        




$query = "SELECT ifnull(a.conta,0) as 'count(a.status)', c.status,  '$value' from (SELECT count(status) conta,status FROM    vicidial_list  
            WHERE         
user='$value' AND
list_id IN($listas_IN) 
            AND        last_local_call_time >= '$data_inicial' 
            AND        last_local_call_time <= '$data_final' 
GROUP BY user,status) as a            
right JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses` WHERE status NOT IN('QUEUE', 'NEW', 'INCALL', 'DROP', 'NA', 'CALLBK')) c on a. status =c.status order by status_name
"; 

//echo $query; 

/*$query = "    SELECT    count(status),
                    status,
                    user
            FROM    vicidial_list  
            WHERE    user='$value'
            AND     list_id IN(SELECT list_id FROM vicidial_lists WHERE campaign_id='$camp_options' AND list_id<>998)
            AND        last_local_call_time >= '$data_inicial' 
            AND        last_local_call_time <= '$data_final'
            AND     status IN(SELECT status FROM vicidial_statuses UNION ALL SELECT status FROM vicidial_campaign_statuses)
            GROUP BY user,status
            ;";*/
            
  /*                      $query = "    SELECT    count(a.status),
                    a.status,
                    a.user
            FROM    vicidial_list as a  INNER JOIN (SELECT distinct  status,status_name FROM `vicidial_campaign_statuses` 
UNION ALL SELECT distinct  status,status_name FROM `vicidial_statuses`) as b ON a.status = b.status 
            WHERE    a.user='$value'
            AND     list_id IN($listas_IN)
            AND        a.last_local_call_time >= '$data_inicial' 
            AND        a.last_local_call_time <= '$data_final'
            GROUP BY a.status
            ORDER BY b.status_name";
}  */


#############################################

//fputcsv($output, array($query),";");
$master_count=0;
$counter_marc=0;
$counter_total=0;
$query = mysql_query($query, $link) or die (mysql_error());
for ($i=0;$i<mysql_num_rows($query);$i++)
{
    $row=mysql_fetch_assoc($query);

    $master_count += $row['count(a.status)'];
    
    if($row['status'] == "MARC" || $row['status']== "NOVOCL") {$counter_marc +=$row['count(a.status)'];}

    if( ($row['status']!="VM") && ($row['status']!="I") && ($row['status']!="B") && ($row['status']!="P") && ($row['status']!="PDROP") && ($row['status']!="DNC") && ($row['status']!="I") && ($row['status']!="NAT") && ($row['status']!="FAX") && ($row['status']!="ERRO") && ($row['status']!="ERI") && ($row['status']!="PU") && ($row['status']!="DC"))
    {$counter_total+=$row['count(a.status)'];}
    $queryf = mysql_query("SELECT status_name FROM vicidial_campaign_statuses WHERE status='".$row['status']."'", $link);
    if (mysql_num_rows($queryf) > 0) {
    $queryf = mysql_fetch_assoc($queryf);
    $feedback_full = $queryf['status_name']; }
    
    $queryf = mysql_query("SELECT status_name FROM vicidial_statuses WHERE status='".$row['status']."'", $link);
    if (mysql_num_rows($queryf) > 0) {
    $queryf = mysql_fetch_assoc($queryf);
    $feedback_full = $queryf['status_name'];}
    fputcsv($output, array(" ", " ", $feedback_full, $row['count(a.status)'] ),";");
        
}

fputcsv($output, array(" ","", "Total de Feedbacks:", $master_count),";");    

$r_user_name[$counter] = $value;
$r_marc[$counter] = $counter_marc;
$r_total_calls[$counter]= $counter_total;
$counter++;


}

fputcsv($output, array(" "),";");    
fputcsv($output, array(" ", $camp_name['campaign_name'], "Total Marcacoes", "Total Contactos Uteis"),";");
fputcsv($output, array(" "),";");

for($h=0;$h<count($r_user_name);$h++)
{
    fputcsv($output, array(" ", $r_user_name[$h], $r_marc[$h], $r_total_calls[$h]),";");
}

fputcsv($output, array(" ", " "),";");
fputcsv($output, array(" ", " "),";");
fputcsv($output, array(" ", " "),";");
        
}    

}
?>