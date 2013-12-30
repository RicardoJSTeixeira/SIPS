<?php
// database & mysqli
require("../../ini/dbconnect.php");

// post & get
foreach ($_POST as $key => $value) { ${$key} = $value; }
foreach ($_GET as $key => $value) { ${$key} = $value; }


if($action == "get_msgs"){
    $js=array();
    $query = "SELECT `from`, `msg`, `type`, event_date FROM sips_msg WHERE `to`='$sent_agent' AND delivered <> 1 AND type='alert' ORDER BY event_date ASC LIMIT 1";
    $query = mysql_query($query) or die(mysql_error());
    while($row = mysql_fetch_row($query)){
    $js['msg_alert']=array('from'=> $row[0],'body'=> $row[1],'type'=> $row[2],'date'=> date("c",strtotime($row[3])));
    }
    $today = date("Y-m-d");
    
    $js['msg_marquee']=array();
    $query = "SELECT `from`, `msg`, `type`, event_date FROM sips_msg WHERE `to`='$sent_agent' AND type='fixed' AND event_date > '$today' ORDER BY event_date";
    $query = mysql_query($query) or die(mysql_error());
    while($row = mysql_fetch_row($query)){
    $js['msg_marquee'][]=array('from'=> $row[0],'body'=> $row[1],'type'=> $row[2],'date'=> date("c",strtotime($row[3])));
        
    }
    echo json_encode($js);
   
}

if($action == "read_msg"){
    $query = "UPDATE sips_msg SET delivered=1 WHERE `to`='$sent_agent' AND delivered <> 1 AND type='alert' ORDER BY event_date ASC LIMIT 1";
    $query = mysql_query($query) or die(mysql_error());
}

?>
