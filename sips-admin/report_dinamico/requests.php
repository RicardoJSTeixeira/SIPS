<?php

require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


switch ($action) {
    //------------------------------------------------//    
    //---------------------GET------------------------//  
    //------------------------------------------------//

    case 'get_user':
        $query = "SELECT vicidial_users.user as user, vicidial_users.full_name as full_name FROM vicidial_users  where vicidial_users.user is not null and  vicidial_users.active='y'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["user"], name => $row["full_name"]);
        }
        echo json_encode($js);
        break;


    case 'get_user_group':
        $query = "SELECT  vicidial_user_groups.user_group as user_group, vicidial_user_groups.group_name as group_name FROM  vicidial_user_groups";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["user_group"], name => $row["group_name"]);
        }
        echo json_encode($js);
        break;

    case 'get_campaign':
        $query = "SELECT  campaign_id,campaign_name  FROM  vicidial_campaigns where active='y' order by campaign_name ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["campaign_id"], name => $row["campaign_name"]);
        }
        echo json_encode($js);
        break;


    case 'get_linha_inbound':
        $query = "SELECT group_id,group_name FROM vicidial_inbound_groups";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["group_id"], name => $row["group_name"]);
        }
        echo json_encode($js);
        break;
    case "get_scripts":
        $query = "SELECT * FROM script_dinamico_master";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["id"], name => $row["name"]);
        }
        echo json_encode($js);
        break;

    case 'get_feedbacks':
        $query = "(SELECT status ,status_name  FROM vicidial_campaign_statuses where visible='1' group by status)
union all
(SELECT status ,status_name  FROM vicidial_statuses group by status)";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(id => $row["status"], name => $row["status_name"]);
        }
        echo json_encode($js);
        break;



    //------------------------------------------------//
    //-----------------EDIT---------------------------//
    //------------------------------------------------//
    //------------------------------------------------//
    //-----------------ADD----------------------------//
    //------------------------------------------------//
    //-----------------DELETE-------------------------//
    //------------------------------------------------//
}
?>
