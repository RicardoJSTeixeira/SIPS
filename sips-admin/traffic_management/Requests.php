<?php

require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


switch ($action) {




    case 'search':

        $data_inicio .= " 00:00:00";
        $data_fim .= " 23:59:59";


        if ($opcao === "1")
            $filtro = "and campaign_id='$filtro_val'";
        else if ($opcao === "2")
            $filtro = "and user_group='$filtro_val'";
        else if ($opcao === "3")
            $filtro = "";

     
        $query = "SELECT length_in_sec,phone_number FROM `vicidial_log`  WHERE call_date between '$data_inicio' and '$data_fim'  $filtro";

        $query = mysql_query($query, $link) or die(mysql_error());

        while ($row = mysql_fetch_assoc($query)) {
            $info[] = array(channel => $row["channel"], length_in_sec => $row["length_in_sec"], phone_number => $row["phone_number"]);
        }

        $trafego = array();

        $trafego["outros"]+=0;
        $trafego["TMN"]+=0;
        $trafego["VODAFONE"]+=0;
        $trafego["OPTIMUS"]+=0;
        $trafego["FIXO"]+=0;



        foreach ($info as $item) {

            //list($inicio, $trunk, $fim) = split('[/@-]', $item['channel']);


            if (preg_match('/^96|92[0-9]{7}$/', $item['phone_number'])) {
                $trafego["TMN"]+=intval($item["length_in_sec"]);
                continue;
            }
            if (preg_match('/^91[0-9]{7}$/', $item['phone_number'])) {
                $trafego["VODAFONE"]+=intval($item["length_in_sec"]);
                continue;
            }
            if (preg_match('/^93[0-9]{7}$/', $item['phone_number'])) {
                $trafego["OPTIMUS"]+=intval($item["length_in_sec"]);
                continue;
            }
            if (preg_match('/^[2-3][0-9]{8}$/', $item['phone_number'])) {
                $trafego["FIXO"]+=intval($item["length_in_sec"]);
                continue;
            }
            $trafego["outros"]+=intval($item["length_in_sec"]);
        }

        echo json_encode($trafego);
        break;






    case 'campaign':
        $query = "SELECT  a.campaign_id,b.campaign_name  FROM  vicidial_campaign_statuses a inner join vicidial_campaigns b on a.campaign_id=b.campaign_id where active='y'  group by  campaign_id";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(campaign_id => $row["campaign_id"], campaign_name => $row["campaign_name"]);
        }
        echo json_encode($js);
        break;


    case 'user_group':
        $query = "SELECT  vicidial_user_groups.user_group as user_group, vicidial_user_groups.group_name as group_name FROM  vicidial_log inner join vicidial_user_groups on vicidial_user_groups.user_group= vicidial_log.user_group   where call_date between date_sub(NOW(), INTERVAL 24 hour) and now() and vicidial_log.user_group is not NUll group by vicidial_log.user_group ";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array(user_group => $row["user_group"], group_name => $row["group_name"]);
        }
        echo json_encode($js);
        break;
}

function Sec2Time($time) {
    $hours = 0;
    $minutes = 0;
    $seconds = 0;
    if ($time >= 3600) {
        $hours = floor($time / 3600);
        $time = ($time % 3600);
    }
    if ($time >= 60) {
        $minutes = floor($time / 60);
        $time = ($time % 60);
    }
    $seconds = floor($time);

    if ($hours < 10)
        $hours = "0" . $hours;
    if ($minutes < 10)
        $minutes = "0" . $minutes;
    if ($seconds < 10)
        $seconds = "0" . $seconds;

    return $hours . ":" . $minutes . ":" . $seconds;
}
?>
