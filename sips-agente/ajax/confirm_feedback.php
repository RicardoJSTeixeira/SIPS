<?php

require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
$js = array();
switch ($action) {
    case "get_info":
        $query = "SELECT ccf.id as id, vl.first_name as full_name,vl.phone_number as phone_number,ccf.lead_id as lead_id, ccf.comment as comment,ccf.date as date FROM crm_confirm_feedback_last ccf left join vicidial_list vl on ccf.lead_id=vl.lead_id  where ccf.agent='$user' and ccf.sale='2' order by ccf.id asc";
    
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {

            $js[] = array("has_info" => true, "id" => $row["id"], "full_name" => $row["full_name"], "phone_number" => $row["phone_number"], "lead_id" => $row["lead_id"], "comment" => $row["comment"], "date" => date("c", strtotime($row["date"])));
        }
        echo json_encode($js);
        break;

    case "validate_call":
        $query = "INSERT INTO `crm_confirm_feedback`(`id`, `lead_id`, `feedback`, `sale`, `campaign`, `agent`, `comment`,date,admin) VALUES (NULL,$lead_id,'validado por agente',0,'$campaign','$user','$comment','" . date('Y-m-d H:i:s') . "','Inexistente')";
        $query = mysql_query($query, $link) or die(mysql_error());
        $query = "UPDATE crm_confirm_feedback_last SET sale=1,agent='$user',comment='$comment',date='" . date('Y-m-d H:i:s') . "',admin='NULL' WHERE lead_id='$lead_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
          $query = "UPDATE vicidial_list SET validation=NULL WHERE lead_id='$lead_id'";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;
}

