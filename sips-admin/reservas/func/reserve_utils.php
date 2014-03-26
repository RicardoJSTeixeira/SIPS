<?php

$self = count(explode('/', $_SERVER['PHP_SELF']));
$header = "";
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);

//includes
require(ROOT . "ini/dbconnect.php");
require(ROOT . "ini/user.php");
require(ROOT . "requests/_json_convert.php");

//regex
$only_nr = '#[^0-9]#';
$only_nr_p = '#[^0-9:]#';
$only_text_sp = "#[^A-Za-z0-9_-\s]#";
$only_text = "#[^A-Za-z0-9_-]#";
$only_ascii = "/[^(\x20-\x7F)]*/";

//verifications
function checkDateTime($data, $format = 'Y-m-d H:i:s') {
    return (date($format, strtotime($data)) == $data);
}

function checkSeriesDate($date, $time1, $day1, $time2, $day2) {
    $time1 = explode(":", $time1);
    $time2 = explode(":", $time2);

    $result1 = (strtotime($date) >= strtotime(date('Y-m-d', strtotime($date)) . " +$time1[0] hours $time1[1] minutes "));
    $result2 = (strtotime($date) <= strtotime(date('Y-m-d', strtotime($date)) . " +$time2[0] hours $time2[1] minutes "));
    $result3 = date("N", strtotime($date)) >= $day1;
    $result4 = date("N", strtotime($date)) <= $day2;
    return ($result1 && $result2 && $result3 && $result4);
}

function checkExecoesDate($date, $beg, $end) {

    $result1 = (strtotime($date) >= strtotime($beg));
    $result2 = (strtotime($date) <= strtotime($end));
    return ($result1 && $result2);
}

function check2DateTimeEqual($data1, $data2) {
    return (date('Y-m-d H:i', strtotime($data1)) == date('Y-m-d H:i', strtotime($data2)));
}

function checkReserva($beg, $end, $rbeg, $rend) {
    $result1 = (strtotime($beg) >= strtotime($rbeg));
    $result2 = (strtotime($beg) <= strtotime($rend));
    $result5 = (strtotime($end) >= strtotime($rbeg));
    $result6 = (strtotime($end) <= strtotime($rend));
    return ($result1 && $result2) OR ($result1 && $result6) OR ($result2 && $result5) OR ($result5 && $result6);
}

//design
function m2h($min, $full = false) {
    $min = abs($min);
    if (!$full) {
        return (($min < 60) ? '' : floor($min / 60) . 'h : ') . sprintf("%02dm", $min % 60);
    } else {
        return floor($min / 60) . 'h : ' . sprintf("%02dm", $min % 60);
    }
}

//end design
//nav
function last_week($date,$users) {
    if($users->user_level>5){
        return (date('D') == 'Mon') ? date("Y-m-d", strtotime($date . " 2 mondays ago")) : date("Y-m-d", strtotime($date . " last monday"));
    }else{
    return date("Y-m-d", strtotime($_GET["dt"] . ' last '.date('l',strtotime('next weekday'))));
    }
}

function next_week($date) {
   return (date('D') == 'Mon') ? date("Y-m-d", strtotime($date . " this monday")) : date("Y-m-d", strtotime($date . " next monday"));
}

//end nav
//is reserva
function set_estado($beg, $end, $rsc, &$reservas, $series, $inverted, $execoes, $date, $min, $slot2change, $i_reservas,$users) {

    $estado = "";


    if (is_past($date, $min)) {
        $estado.= " past";
    }

    for ($i = 0; $i < count($i_reservas); $i++) {
        if (check2DateTimeEqual($beg, $i_reservas[$i]["start_date"]) && ($rsc == $i_reservas[$i]["id_resource"])) {
            $estado.=" imported";

            unset($i_reservas[$i]);
            $i_reservas = array_values($i_reservas);
        }
    }
    for ($i = 0; $i < count($reservas); $i++) {
        //if (check2DateTimeEqual($beg, $reservas[$i][start_date]) && check2DateTimeEqual($end, $reservas[$i][end_date]) && ($rsc == $reservas[$i][id_resource])) {
        if (checkReserva($beg, $end, $reservas[$i]["start_date"], $reservas[$i]["end_date"]) && ($rsc == $reservas[$i]["id_resource"])) {
            if ($reservas[$i]["id_user"] != $users->id AND $users->user_level<5) {
                $estado.=" deles";
            }
            $lead_id = $reservas[$i]["lead_id"];
            $postal = $reservas[$i]["postal_code"];
            $type = $reservas[$i]["display_text"];
            $id_type = "t" . $reservas[$i]["id_reservation_type"];
            $result = ($slot2change == $reservas[$i]["id_reservation"]) ? ' muda' : ' reservado';
            //BEGIN elimina a reserva do array
            unset($reservas[$i]);
            $reservas = array_values($reservas);
            //END elimina a reserva do array

            return array("id" => $lead_id, "type" => $type, "stat" => "$estado $result $id_type","postal"=>$postal);
        }
    }


    if (strlen($estado) > 0) {
        return array("id" => 0, "type" => 0, "stat" => "$estado","postal"=>null);
    }

    for ($k = 0; $k < count($execoes); $k++) {
        if ($execoes[$k]["id_resource"] == $rsc) {
            if (($inverted == 1)) {
                if (checkExecoesDate($beg, $execoes[$k]["start_date"], $execoes[$k]["end_date"])) {
                    return array("id" => 0, "type" => "", "stat" => ($slot2change == 0) ? ' reservavel' : ' disponivel',"postal"=>null);
                }
            } else {
                if (checkExecoesDate($beg, $execoes[$k]["start_date"], $execoes[$k]["end_date"])) {
                    return array("id" => 0, "type" => "", "stat" => ' bloqueado',"postal"=>null);
                }
            }
        }
    }

    for ($j = 0; $j < count($series); $j++) {
        if ($series[$j]["id_resource"] == $rsc) {
            if (($inverted == 1)) {
                if (checkSeriesDate($beg, $series[$j]["start_time"], $series[$j]["day_of_week_start"], $series[$j]["end_time"], $series[$j]["day_of_week_end"])) {
                    return array("id" => 0, "type" => "", "stat" => ($slot2change == 0) ? ' reservavel' : ' disponivel',"postal"=>null);
                }
            } else {
                if (checkSeriesDate($beg, $series[$j]["start_time"], $series[$j]["day_of_week_start"], $series[$j]["end_time"], $series[$j]["day_of_week_end"])) {
                    return array("id" => 0, "type" => "", "stat" => ' bloqueado',"postal"=>null);
                }
            }
        }
    }


    if (is_past($date, $min)) {
        return array("id" => 0, "type" => "", "stat" => " past","postal"=>null);
    }
    if (($inverted == 1)) {
        return array("id" => 0, "type" => "", "stat" => " bloqueado","postal"=>null);
    } else {
        return array("id" => 0, "type" => "", "stat" => ($slot2change == 0) ? " reservavel" : " disponivel","postal"=>null);
    }
}

function is_past($date, $i) {
    //return date("Y-m-d H:i:s", strtotime($date . "+$i minutes")) < date("Y-m-d H:i:s",strtotime("now"));
    return date("Y-m-d", strtotime($date)) <= date("Y-m-d", strtotime("now"));
}

function days2dias($data) {
    $day_of_week = date('D', $data);

    switch ($day_of_week) {
        case "Mon": return "Segunda";
        case "Tue": return "Terça";
        case "Wed": return "Quarta";
        case "Thu": return "Quinta";
        case "Fri": return "Sexta";
        case "Sat": return "Sábado";
        case "Sun": return "Domingo";

        default:return 'Erro';
    }
}

function nr2days($nr) {
    switch ($nr) {
        case 1:return 'Monday';
        case 2:return 'Tuesday';
        case 3:return 'Wednesday';
        case 4:return 'Thursday';
        case 5:return 'Friday';
        case 6:return 'Saturday';
        case 7:return 'Sunday';

        default:return 'Erro';
    }
}

function nr2dias($nr) {
    switch ($nr) {
        case 1: return "Segunda";
        case 2: return "Terça";
        case 3: return "Quarta";
        case 4: return "Quinta";
        case 5: return "Sexta";
        case 6: return "Sábado";
        case 7: return "Domingo";

        default: return "Erro";
    }
}

function gwsc() {
    $cs = array('00', '33', '66', '99', 'CC', 'FF');

    for ($i = 0; $i < 6; $i++) {
        for ($j = 0; $j < 6; $j++) {
            for ($k = 0; $k < 6; $k++) {
                $c = $cs[$i] . $cs[$j] . $cs[$k];
                echo "<option value=\"$c\">#$c</option>\n";
            }
        }
    }
}

function log_admin($topic, $event, $id, $query, $PHP_AUTH_USER, $IP, $comments = "") {
    global $link;
    $stmt = "INSERT INTO vicidial_admin_log set event_date=NOW(), user='$PHP_AUTH_USER', ip_address='$IP', event_section='$topic', event_type='ADD', record_id='$id', event_code='$event', event_sql='" . mysql_real_escape_string($query) . "', event_notes='$comments';";
    $rslt = mysql_query($stmt, $link);
    if (!$rslt) {
        echo "Log Error: " . mysql_error() . " Whole query:" . $stmt;
    }
}

?>
