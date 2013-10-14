<?php

require("../../../ini/dbconnect.php");
require("../../../ini/user.php");


foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
error_reporting();
ini_set('display_errors', '1');

$user_class = new users;

switch ($action) {
    //------------------------------------------------//    
    //---------------------GET------------------------//  
    //------------------------------------------------//
    case "get_callback_by_user":
       $js['aaData'] = array();
        
        if ($all_date == "false")
            $date = "and a.entry_time between '$data_inicio' and '$data_fim'";
        else
            $date = "";

        $query = "SELECT a.lead_id,b.first_name,d.campaign_name,a.entry_time,a.callback_time,a.comments,a.callback_id from vicidial_callbacks a 
            left join vicidial_list b on a.lead_id=b.lead_id
  
            left join vicidial_campaigns d on d.campaign_id=a.campaign_id where a.status<>'INACTIVE' and a.user='$user' $date limit 100";

        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $row["comments"] = htmlentities($row["comments"]);
            $js['aaData'][] = $row;
        }
        
        echo json_encode($js);
        break;

    case "get_agent_name_by_user":
        $query = "SELECT full_name from vicidial_users where user ='$user'";
        $query = mysql_query($query, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($query);
        echo ($row["full_name"]);
        break;

    case "get_agents":
        $query = "SELECT user,full_name from vicidial_users";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("user" => $row["user"], "full_name" => $row["full_name"]);
        }
        echo json_encode($js);
        break;



    case "reset_callbacks_by_user":
        $query = "DELETE FROM `vicidial_callbacks` where user ='$user'";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;


    case "reset_callbacks_by_dc":
        if ($all_date == "false")
            $date = "and entry_time between '$data_inicio' and '$data_fim'";
        else
            $date = "";

        $query = "DELETE FROM `vicidial_callbacks` where user ='$user' and campaign_id='$campaign_id' $date";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;


    case 'get_campaign':
        $query = "SELECT  a.campaign_id,a.campaign_name  FROM  vicidial_campaigns a
            left join vicidial_callbacks b on a.campaign_id=b.campaign_id  where a.active='y' and b.user='$user' group by a.campaign_id";
  
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["campaign_id"], "name" => $row["campaign_name"]);
        }
        echo json_encode($js);
        break;


    case "get_callback_by_id":
        $query = "SELECT b.first_name,c.list_name,d.campaign_name,a.entry_time,a.callback_time,e.full_name,a.comments FROM `vicidial_callbacks` a     
            left join vicidial_list b on a.lead_id=b.lead_id
            left join vicidial_lists c on a.list_id=c.list_id
             left join vicidial_campaigns d on d.campaign_id=a.campaign_id
                    left join vicidial_users e on e.user=a.user
            where callback_id=$callback_id";
        $query = mysql_query($query, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($query);
        echo (json_encode($row));
        break;

    case "delete_callbacks_by_id":
        $query = "DELETE FROM `vicidial_callbacks` where callback_id=$callback_id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;

    case "edit_callbacks_by_id":
        $query = "UPDATE `vicidial_callbacks` SET user='$user',comments='$comments' WHERE callback_id=$callback_id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;
}
?>
