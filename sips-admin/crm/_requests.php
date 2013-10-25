<?php

require("../../ini/_json_convert.php");
require("../../ini/dbconnect.php");
require("../../ini/functions.php");
require("../../ini/user.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


$user = new users;

switch ($action) {
    case "last_outbound":
        if ($apenas_campanha) {
            $filtro_campanha = " AND vclog.campaign_id='$campaign_id' ";
        } else {
            $filtro_campanha = "  ";
        }
        if ($chm_auto) {
            $filtro_chm = " AND vclog.comments='AUTO' ";
        } else {
            $filtro_chm = " AND vclog.comments='MANUAL' ";
        }


        $aColumns = array('first_name', 'phone_number', 'call_date');
        $sQuery = "
		SELECT vclist.first_name, vclog.phone_number, DATE_FORMAT(vclog.call_date,'%H:%i:%s  %d/%m/%Y') AS call_date, vclog.lead_id
		FROM   vicidial_log AS vclog INNER JOIN vicidial_list AS vclist ON vclog.lead_id=vclist.lead_id
		WHERE vclog.user='$agent'
		$filtro_chm
		$filtro_campanha
		LIMIT 250
		
		";
//echo $sQuery;
        $rResult = mysql_query($sQuery, $link) or die(mysql_error());
        $output = array("aaData" => array());

        while ($aRow = mysql_fetch_array($rResult)) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {

                switch ($aColumns[$i]) {
                    case "first_name": $row[] = "<a onclick='LoadCRMEdit($aRow[lead_id]);' style='cursor:pointer'> " . $aRow['first_name'] . "<img title='Ver Detalhes' style='float:right' src='../../images/icons/livejournal_16.png'></a>";
                        break;
                    case "phone_number" : $row[] = "<a onclick='???($aRow[lead_id]);' style='cursor:pointer'> " . $aRow['phone_number'] . "<img title='Chamar' style='float:right' src='../../images/icons/telephone_go_16.png'></a>";
                        break;
                    default : $row[] = $aRow[$aColumns[$i]];
                        break;
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        break;

    /* Actualização das Dropdowns qnd se muda de campanha */
    case "campaign_change_db":
        $query = "SELECT list_id, list_name FROM vicidial_lists WHERE campaign_id='$sent_campaign'";
        $query = mysql_query($query, $link);

        $rows = array();
        while ($r = mysql_fetch_assoc($query)) {
            $rows['db_list'][] = $r;
        }
        print json_encode($rows);
        break;


    case "campaign_change_feedback":
        $query = "SELECT status, status_name FROM vicidial_campaign_statuses WHERE campaign_id='$sent_campaign' union all select status, status_name from vicidial_statuses order by status_name ";
        $query = mysql_query($query, $link);

        $rows = array();
        while ($r = mysql_fetch_assoc($query)) {
            $rows['feed_list'][] = $r;
        }
        print json_encode($rows);
        break;




    /* Contrução da tabela de resultados conforme os filtros escolhidos pelo utilizador */
    case "get_table_data":
# Contrução do Filtro das Datas
        if ($dataflag == 1) {

            $data_QUERY = " AND last_local_call_time >= '$datai 00:00:00' AND last_local_call_time <= '$dataf 23:59:59' ";
        } else {
            $data_QUERY = " AND entry_date >= '$datai 00:00:00' AND entry_date <= '$dataf 23:59:59' ";
        }
# Construção do Filtro das Campanhas/BDs
        if ($filtro_dbs == "all") {
            $query = "SELECT list_id FROM vicidial_lists WHERE campaign_id='$filtro_campanha'";
            $query = mysql_query($query, $link);
            $list_IN = Query2IN($query, 0);
        } else {
            $list_IN = "'" . $filtro_dbs . "'";
        }
# Construção dos Filtros dos Operadores
        if ($filtro_operador != 'all') {
            $operador_QUERY = " AND user='$filtro_operador'";
        }
# Construção dos Filtros dos Feedbacks
        if ($filtro_feedback != 'all') {
            $feedback_QUERY = " AND status='$filtro_feedback'";
        }
        $aColumns = array('lead_id', 'first_name', 'phone_number', 'address1', 'last_local_call_time');
        if ($contact_id != "" && $contact_id != null) {

            $sQuery = "
            SELECT first_name, phone_number, address1 ,last_local_call_time, lead_id
            FROM   vicidial_list
            WHERE lead_id= '$contact_id'
            
            ";
        } elseif ($phone_number != "" && $phone_number != null) {
            $sQuery = "
            SELECT first_name, phone_number, address1 ,last_local_call_time, lead_id
            FROM   vicidial_list
            WHERE phone_number= '$phone_number'
         
            ";
        } else {
            $sQuery = "
            SELECT first_name, phone_number, address1 ,last_local_call_time, lead_id
            FROM   vicidial_list
            WHERE list_id IN($list_IN)
            $data_QUERY
            $operador_QUERY
            $feedback_QUERY
            LIMIT 3000
            ";
        }
//echo $sQuery;
        $rResult = mysql_query($sQuery, $link) or die(mysql_error());
        $output = array("aaData" => array());

        while ($aRow = mysql_fetch_array($rResult)) {
            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {

                if ($aColumns[$i] == 'last_local_call_time') {
                    $row[] = $aRow[$aColumns[$i]] . "<div class='view-button' ><span class='btn btn-mini' onclick='LoadHTML($aRow[lead_id]);' ><i class='icon-edit'></i>Ver</span></div>";
                } else {
                    $row[] = $aRow[$aColumns[$i]];
                }
            }
            $output['aaData'][] = $row;
        }
        echo json_encode($output);
        break;




    case "update_contact_field":
        $query = "UPDATE vicidial_list SET $send_field='$send_field_value' where lead_id='$send_lead_id'";
        $query = mysql_query($query, $link);
        echo $query;
        break;


    case "update_feedback":
# Update vicidial_list
        $query = "UPDATE vicidial_list SET status='$send_feedback' WHERE lead_id='$send_lead_id'";
        $querya = mysql_query($query);

# Update vicidial_log 
        $query = "UPDATE vicidial_log SET status='$send_feedback' WHERE lead_id='$send_lead_id' ORDER BY call_date DESC LIMIT 1";
        $queryb = mysql_query($query);

# Update vicidial_agent_log
        $query = "UPDATE vicidial_agent_log SET status='$send_feedback' WHERE lead_id='$send_lead_id' ORDER BY agent_log_id DESC LIMIT 1";
        $queryc = mysql_query($query);

# Update vicidial_closer_log | inbound
        $query = "UPDATE vicidial_closer_log SET status='$send_feedback' WHERE lead_id='$send_lead_id'  ORDER BY call_date DESC LIMIT 1";
        $queryd = mysql_query($query);

        if ($querya and $queryb and $queryc and $queryd) {
            echo 1;
        } else {
            echo 0;
        }
        break;














    case "get_agentes":

        $allowed_camps_regex = implode("|", $user->allowed_campaigns);
        if (!$user->is_all_campaigns) {
            $ret = "WHERE allowed_campaigns REGEXP '$allowed_camps_regex'";


            $user_groups = "";
            $result = mysql_query("SELECT `user_group`, `allowed_campaigns` FROM `vicidial_user_groups` $ret ") or die(mysql_error());
            while ($row1 = mysql_fetch_assoc($result)) {
                $user_groups .= "'$row1[user_group]',";
            }
            $user_groups = rtrim($user_groups, ",");

            $result = mysql_query("SELECT `user` FROM `vicidial_users` WHERE user_group in ($user_groups) AND user_level < $user->user_level") or die(mysql_error());
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



    case "add_info_crm":

        if ($sale == "false") {
            $query = "INSERT INTO `crm_confirm_feedback`(`id`, `lead_id`, `feedback`, `sale`, `campaign`, `agent`, `comment`,date,admin) VALUES (NULL,$lead_id,'SP',$sale,'$campaign','$agent','$comment','" . date('Y-m-d H:i:s') . "','$user->id')";
            $query = mysql_query($query, $link) or die(mysql_error());
            $query = "Update vicidial_list set status='SP' where lead_id=$lead_id";
            $query = mysql_query($query, $link) or die(mysql_error());
            $query = "Update vicidial_log set status='SP' where lead_id=$lead_id and campaign_id='$campaign_id' order by uniqueid desc limit 1";
            $query = mysql_query($query, $link) or die(mysql_error());
        } else {
            $query = "INSERT INTO `crm_confirm_feedback`(`id`, `lead_id`, `feedback`, `sale`, `campaign`, `agent`, `comment`,date,admin) VALUES (NULL,$lead_id,'$feedback',$sale,'$campaign','$agent','$comment','" . date('Y-m-d H:i:s') . "','$user->id')";
            $query = mysql_query($query, $link) or die(mysql_error());
        }


        $query = "SELECT EXISTS(SELECT * FROM crm_confirm_feedback_last WHERE lead_id='$lead_id') as count";
        $query = mysql_query($query, $link) or die(mysql_error());
        $row = mysql_fetch_assoc($query);

        if ($row['count'] == "0") {
            $query = "INSERT INTO `crm_confirm_feedback_last`(`id`, `lead_id`, `feedback`, `sale`, `campaign`, `agent`, `comment`,date,admin) VALUES (NULL,$lead_id,'$feedback',$sale,'$campaign','$agent','$comment','" . date('Y-m-d H:i:s') . "','$user->id')";
            $query = mysql_query($query, $link) or die(mysql_error());
        } else {
            $query = "UPDATE crm_confirm_feedback_last SET feedback='$feedback',sale=$sale,agent='$agent',comment='$comment',date='" . date('Y-m-d H:i:s') . "',admin='$user->id' WHERE lead_id='$lead_id'";
            $query = mysql_query($query, $link) or die(mysql_error());
        }

        echo json_encode(array(1));
        break;



    case "get_info_crm":
        $js = array();
        $query = "SELECT `id`, `lead_id`, `feedback`, `sale`, `campaign`,admin, `agent`, `comment`,date FROM crm_confirm_feedback where lead_id=$lead_id order by id asc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {

            $js[] = array("has_info" => true, "id" => $row["id"], "lead_id" => $row["lead_id"], "feedback" => $row["feedback"], "sale" => $row["sale"] == 1, "campaign" => $row["campaign"], "admin" => $row["admin"], "agent" => $row["agent"], "comment" => $row["comment"], "date" => $row["date"]);
        }

        if (sizeof($js) < 1) {
            $query = "SELECT * FROM vicidial_campaign_statuses vcs inner join vicidial_campaigns vc on vcs.campaign_id=vc.campaign_id WHERE status='$status' and vc.campaign_id='$campaign_id'";
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $js[] = array("has_info" => false, "status" => $row["status"], "sale" => $row["sale"]);
            }
        }
        echo json_encode($js);

        break;
}
?> 