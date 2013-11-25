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

        if ($all_date == "false") {
            if ($data_filtro == "true")
                $date = "and a.callback_time between '$data_inicio 00:00:00' and '$data_fim 23:59:59'";
            else
                $date = "and a.entry_time between '$data_inicio 00:00:00' and '$data_fim 23:59:59'";
        } else
            $date = "";


        if ($recipient == "true")
            $recipient = "ANYONE";
        else
            $recipient = "USERONLY";

     



        $query = "SELECT a.lead_id,b.first_name,d.campaign_name,a.entry_time,a.callback_time,a.comments,a.callback_id from vicidial_callbacks a 
            left join vicidial_list b on a.lead_id=b.lead_id
            left join vicidial_campaigns d on d.campaign_id=a.campaign_id where a.status<>'INACTIVE' and a.recipient='$recipient' and a.user='$user' $date limit 100";
     
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $row["comments"] = htmlspecialchars($row["comments"]);
            $js['aaData'][] = $row;
        }

        echo json_encode($js);
        break;

    case "get_agent_name_by_user":
        $query = "SELECT full_name from vicidial_users where user ='$user' ";
        $query = mysql_query($query, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($query);
        echo ($row["full_name"]);
        break;


    case "get_agents":
        $users_regex = "";
        $tmp = "";
        $allowed_camps_regex = implode("|", $user_class->allowed_campaigns);
        if (!$user_class->is_all_campaigns) {
            $ret = "WHERE allowed_campaigns REGEXP '$allowed_camps_regex'";
            $user_groups = "";
            $result = mysql_query("SELECT `user_group`, `allowed_campaigns` FROM `vicidial_user_groups` $ret ") or die(mysql_error());
            while ($row1 = mysql_fetch_assoc($result)) {
                $user_groups .= "'$row1[user_group]',";
            }
            $user_groups = rtrim($user_groups, ",");
            $result = mysql_query("SELECT `user` FROM `vicidial_users` WHERE user_group in ($user_groups) AND user_level < $user_class->user_level") or die(mysql_error());
            while ($rugroups = mysql_fetch_assoc($result)) {
                $tmp .= "$rugroups[user]|";
            }
            $tmp = rtrim($tmp, "|");
            $users_regex = "Where user REGEXP '^$tmp'";
        }
        $js = array();
        $query = "SELECT user,full_name  FROM vicidial_users $users_regex group by user ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("user" => $row["user"], "full_name" => $row["full_name"]);
        }
        echo json_encode($js);
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

    case 'get_campaign_all':

        $campaigns = implode("','", $user_class->allowed_campaigns);

        if ($user_class->is_all_campaigns)
            $campaigns = "";
        else
            $campaigns = "and campaign_id in('$campaigns')";
        $query = "SELECT  campaign_id,campaign_name  FROM  vicidial_campaigns where active='y' $campaigns  order by campaign_name ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["campaign_id"], "name" => $row["campaign_name"]);
        }
        echo json_encode($js);
        break;


    case "get_callback_by_id":
        $query = "SELECT b.first_name,c.list_name,d.campaign_name,d.campaign_id,a.entry_time,a.callback_time,e.full_name,a.comments FROM `vicidial_callbacks` a     
            left join vicidial_list b on a.lead_id=b.lead_id
            left join vicidial_lists c on a.list_id=c.list_id
             left join vicidial_campaigns d on d.campaign_id=a.campaign_id
                    left join vicidial_users e on e.user=a.user
            where callback_id=$callback_id";
        $query = mysql_query($query, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($query);
        echo (json_encode($row));
        break;





    case "reset_callbacks_by_user":
        $query = "UPDATE vicidial_callbacks b left join vicidial_list a  on a.lead_id=b.lead_id set b.status='INACTIVE', a.status='NEW', a.called_since_last_reset='N' where  b.user='$user' ";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;


    case "reset_callbacks_by_dc":
        if ($all_date == "false")
            $date = "and entry_time between '$data_inicio' and '$data_fim'";
        else
            $date = "";

        $query = "UPDATE vicidial_callbacks b left join  vicidial_list a   on a.lead_id=b.lead_id set b.status='INACTIVE', a.status='NEW', a.called_since_last_reset='N' where b.user='$user' and b.campaign_id='$campaign_id' $date  ";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;


    case "reset_callbacks_by_id":
        $query = "UPDATE vicidial_callbacks b left join vicidial_list a on a.lead_id=b.lead_id set  b.status='INACTIVE', a.status='NEW', a.called_since_last_reset='N'  where b.callback_id='$callback_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;





    case "edit_callbacks_by_id":
        $query = "UPDATE `vicidial_callbacks` SET user='$user',campaign_id='$campaign_id',callback_time='$callback_date', comments='$comments' WHERE callback_id=$callback_id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;


    case "transfer_callbacks_to_agent":
        $query = "UPDATE `vicidial_callbacks` SET user='$new_user' WHERE user='$old_user'";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;


    case "transfer_callbacks_to_agent_by_c":
        $query = "UPDATE `vicidial_callbacks` SET user='$new_user', campaign_id='$campaign_id' WHERE user='$old_user'";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo("1");
        break;
}