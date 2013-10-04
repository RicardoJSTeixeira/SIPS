<?php

require("../../ini/dbconnect.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


        $js = array();
        $query = "SELECT ccf.id as id, vl.first_name as full_name,vl.phone_number as phone_number,ccf.lead_id as lead_id, ccf.comment as comment,ccf.date as date FROM crm_confirm_feedback_last ccf left join vicidial_list vl on ccf.lead_id=vl.lead_id  where ccf.agent='$user' and ccf.sale!='1' order by ccf.id asc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {

            $js[] = array("has_info"=>true, "id" => $row["id"], "full_name" => $row["full_name"], "phone_number" => $row["phone_number"], "lead_id" => $row["lead_id"],   "comment" => $row["comment"],"date"=> date("c",strtotime($row["date"])));
        }

               echo json_encode($js);

      


?>
