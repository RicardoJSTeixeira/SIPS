<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require("../lib/db.php");

require("../lib/user.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);

$user->confirm_login();


$variables = array();
switch ($action) {

    case "get_campaign":
        $query = "select b.campaign_id from vicidial_list a inner join vicidial_lists b on a.list_id=b.list_id where lead_id=?";
        $variables[] = $lead_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($row);
        break;


    case "get_basic_info":
        $query = "select a.lead_id,a.phone_number,b.list_name bd,c.campaign_name,d.full_name ,e.status_name,e.status,a.called_count,a.last_local_call_time,a.entry_date  from vicidial_list a 
left join vicidial_lists b on a.list_id=b.list_id
left join vicidial_campaigns c on c.campaign_id=b.campaign_id
left join vicidial_users d on d.user=a.user
left join ((SELECT status ,status_name FROM vicidial_campaign_statuses where campaign_id=?) union all (SELECT status, status_name FROM vicidial_statuses)) e on e.status=a.status
where a.lead_id=?";
        $variables[] = $campaign_id;
        $variables[] = $lead_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($row);
        break;


    case "get_dynamic_field":
        $result = array();
        $query = "SELECT Name,Display_name FROM vicidial_list_ref WHERE campaign_id=? AND active=1 Order by field_order ASC";
        $variables[] = $campaign_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $temp = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $temp[] = $row["Name"];
            $result[] = array("name" => $row["Name"], "display_name" => $row["Display_name"], "value" => 0);
        }
        $variables = array();
        $query = "SELECT " . implode(",", $temp) . " FROM vicidial_list WHERE lead_id=?";
        $variables[] = $lead_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        foreach ($result as &$value) {
            $value["value"] = $row[$value["name"]];
        }
        echo json_encode($result);
        break;


    case "get_feedbacks":
        $query = ("select * from((SELECT status,status_name FROM vicidial_campaign_statuses where campaign_id=? ) union all (SELECT status, status_name FROM vicidial_statuses)) a  group by status order by status_name asc");
        $variables[] = $campaign_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $row = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($row);
        break;


    case "get_chamadas":
        $query = "select a.call_date,a.length_in_sec,a.phone_number,b.full_name,c.status_name,d.campaign_name,e.list_name from vicidial_log a
left join vicidial_users b on b.user=a.user         
left join ((SELECT status ,status_name FROM vicidial_campaign_statuses where campaign_id=?) union all (SELECT status, status_name FROM vicidial_statuses)) c on c.status=a.status
left join vicidial_campaigns d on d.campaign_id=?
left join vicidial_lists e on a.list_id=e.list_id
where lead_id=?";

        $variables[] = $campaign_id;
        $variables[] = $campaign_id;
        $variables[] = $lead_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $result['aaData'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {

            $row[1] = gmdate("H:i:s", $row[1]);
            $result['aaData'][] = $row;
        }

        echo json_encode($result);
        break;





    case "get_gravacoes":
        $query = "SELECT 
				start_time AS data,
				start_time AS hora_inicio,
				end_time AS hora_fim,
				length_in_sec,
                rl.user,				
                filename,
				location,
				lead_id,
				
				full_name
			FROM 
				recording_log rl
			INNER JOIN vicidial_users vu ON rl.user=vu.user
			WHERE 
				lead_id=? 
			ORDER BY 
				recording_id 
			DESC LIMIT 500;";

        $variables[] = $lead_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        $result['aaData'] = array();
        while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {

            $row[0] = date('Y-m-d', strtotime($row[0]));
            $row[1] = date('H:i:s', strtotime($row[1]));
            $row[2] = date('H:i:s', strtotime($row[2]));
            $row[3] = gmdate("H:i:s", $row[3]);

            $row[4] = $row[4] . " <div class='view-button'><a class='btn btn-mini' target='_new' href='../../AM/view/script.html?lead_id=" . $lead_id . "&campaign_id=" . $campaign_id . "&user=" . $user->id . "&pass=" . $user->password . "'><i class='icon-bookmark'></i>Script</a></div>";

            $curpage = curPageURL();
            $mp3File = "";
            if (strlen($row["location"]) > 0) {
                $tmp = explode("/", $row["location"]);
                $ip = $tmp[2];
                $tmp = explode(".", $ip);
                $ip = $tmp[3];

                switch ($ip) {
                    case "248":
                        $port = ":20248";
                        break;
                    case "247":
                        $port = ":20247";
                        break;
                    default:
                        $port = "";
                        break;
                }

                $mp3File = $curpage . $port . "/RECORDINGS/MP3/" . $row["filename"] . "-all.mp3";
            }


            $row[4] = $row[4] . "<div class='view-button'><a href=' $mp3File ' target='_self' class='btn btn-mini'><i class='icon-play'></i>Ouvir</a></div>";
            $result['aaData'][] = $row;
        }

        echo json_encode($result);
        break;



    case "change_feedback":
        $query = "update vicidial_list set status=? where lead_id=?";
        $variables[] = $status;
        $variables[] = $lead_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        echo (1);
        break;



    case "update_form_field":
        $query = "UPDATE vicidial_list SET $field=? where lead_id=?";
        $variables[] = $value;
        $variables[] = $lead_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
        echo (1);
        break;
}

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"]; //.$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

?>
