<?php
// database & mysqli
require("../../ini/dbconnect.php");

// post & get
foreach ($_POST as $key => $value) { ${$key} = $value; }
foreach ($_GET as $key => $value) { ${$key} = $value; }


if($action == "get_user_groups"){
    $query = "SELECT user_group, group_name FROM vicidial_user_groups ORDER BY group_name";
    $query = mysql_query($query, $link);
    while($row = mysql_fetch_row($query)){
        $js['user_groups']['value'][] = $row[0];
        $js['user_groups']['description'][] = $row[1];
    }
    echo json_encode($js);
}



if($action == "get_users"){
    $query = "SELECT user, full_name FROM vicidial_users WHERE user_level < 4 ORDER BY full_name";
    $query = mysql_query($query, $link);
    while($row = mysql_fetch_row($query)){
        $js['users']['value'][] = $row[0];
        $js['users']['description'][] = $row[1];
    }
    echo json_encode($js);
}

if($action == "submit_msg"){
    
    $today = date("Y-m-d H:i:s");
    
    $query = mysql_query("SELECT full_name FROM vicidial_users WHERE user='$sent_from'") or die(mysql_error());
    $row = mysql_fetch_row($query);
    $sent_from = $row[0];
    
    
    
    if(count($sent_users) > 0){
        foreach ($sent_users as $key => $value) {
            $query = "INSERT INTO sips_msg (`from`, `to`, `msg`, `type`, `event_date`) VALUES ('$sent_from', '$value', '$sent_msg', '$sent_msg_type', '$today')";
            $query = mysql_query($query) or die(mysql_error());       
        }                   
    } else {
        $sent_groups = implode("','", $sent_groups);
        $query = "SELECT user FROM vicidial_users WHERE user_group IN ('".$sent_groups."')";
        $query = mysql_query($query) or die(mysql_error());
        while ($row = mysql_fetch_row($query)){ $users[] = $row[0]; }
        
        
        foreach ($users as $key => $value) {
            $query = "INSERT INTO sips_msg (`from`, `to`, `msg`, `type`, `event_date`) VALUES ('$sent_from', '$value', '$sent_msg', '$sent_msg_type', '$today')";
            $query = mysql_query($query) or die(mysql_error());       
        }  
    }
}




?>