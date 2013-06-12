<?php

require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


switch ($action) {
    case 'fromTo':
        $query = "SELECT  TRIM(LEADING 'SIP/' from(SUBSTRING_index(channel,'-',1))) as  canal,a.length_in_sec as segundos, a.uniqueid as uniqueId FROM call_log a inner join vicidial_log b on a.uniqueid=b.uniqueid WHERE start_time between '$dataInicio 00:00:00' and '$dataFim 23:59:59'  and channel like '$channelSearch' and b.campaign_id like '$campaing' and a.length_in_sec>0 and number_dialed $dialled_Number and b.user_group like '$user_Group' order by channel";
      
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[$row["canal"]]+=$row["segundos"];
        }
        echo json_encode($js);
        break;
    case 'Dp2':

        $query = "SELECT  TRIM(LEADING 'SIP/' from(SUBSTRING_index(channel,'-',1))) as  canal FROM call_log where start_time between '$dataInicio 00:00:00' and '$dataFim 23:59:59' and channel like '%SIP/%' and length_in_sec>0 group by canal ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {

            $js[] = $row["canal"];
        };
        echo json_encode($js);
        break;
    case 'Dp3':
        $query = "select c.campaign_name as cp_name, c.campaign_id as uniqueId from call_log a inner join vicidial_log b on b.uniqueid=a.uniqueid  inner join vicidial_campaigns c on c.campaign_id=b.campaign_id where start_time between '$dataInicio  00:00:00' and '$dataFim 23:59:59' group by cp_name ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(cp_name => $row["cp_name"], uniqueId => $row["uniqueId"]);
        };
        echo json_encode($js);
        break;
        
        
        case 'Dp4':
        $query = "select b.user_group as uG from call_log a inner join vicidial_log b on b.uniqueid=a.uniqueid where start_time between '$dataInicio  00:00:00' and '$dataFim 23:59:59' and b.user_group IS NOT NULL group by uG ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
             $js[] = $row["uG"];
        };
        echo json_encode($js);
        break;
}
?>
