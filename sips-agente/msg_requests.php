<?php
// database & mysqli
require("../ini/dbconnect.php");

// post & get
foreach ($_POST as $key => $value) { ${$key} = $value; }
foreach ($_GET as $key => $value) { ${$key} = $value; }




if($action == "get_msgs"){
    $query = "SELECT `from`, `msg`, `type`, DATE_FORMAT(event_date,'%d-%m-%Y às %H:%i:%s') FROM sips_msg WHERE `to`='$sent_agent' AND delivered <> 1 AND type='alert' ORDER BY event_date ASC LIMIT 1";
    $query = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_row($query);
    $js['msg_alert']['from'][] = $row[0];
    $js['msg_alert']['body'][] = $row[1];
    $js['msg_alert']['type'][] = $row[2];
    $js['msg_alert']['date'][] = $row[3];
    
    $today = date("Y-m-d");
    
    $query = "SELECT `from`, `msg`, `type`, DATE_FORMAT(event_date,'%H:%i:%s') FROM sips_msg WHERE `to`='$sent_agent' AND type='fixed' AND event_date > '$today' ORDER BY event_date";
    $query = mysql_query($query) or die(mysql_error());
    $count = mysql_num_rows($query);
    while($row = mysql_fetch_row($query)){
        $js['msg_marquee']['from'][] = $row[0];
        $js['msg_marquee']['body'][] = $row[1];
        $js['msg_marquee']['type'][] = $row[2];
        $js['msg_marquee']['date'][] = $row[3];
        
    }
    $js['msg_marquee']['count'][] = $count;
    echo json_encode($js);
   
}

if($action == "read_msg"){
    $query = "UPDATE sips_msg SET delivered=1 WHERE `to`='$sent_agent' AND delivered <> 1 AND type='alert' ORDER BY event_date ASC LIMIT 1";
    $query = mysql_query($query) or die(mysql_error());
}





?>