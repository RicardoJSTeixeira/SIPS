<?php

error_reporting();
ini_set('display_errors', '1');

require("../dbconnect.php");
require('../../swiftemail/lib/swift_required.php');

if (isset($_POST["NR"])) {
    $nr = $_POST["NR"];
}
if (isset($_POST["MSG"])) {
    $msg = $_POST["MSG"];
}
if (isset($_POST["gateway"])) {
    $gateid = $_POST["gateway"];
}
if (isset($_POST["lista"])) {
    $lista = $_POST["lista"];
}
if (isset($_POST["operador"])) {
    $operador = $_POST["operador"];
}
if (isset($_POST["modo"])) {
    $modo = $_POST["modo"];
}
if (isset($_POST['id_camp'])) {
    $id_camp = $_POST['id_camp'];
}
if (isset($_POST['LIM'])) {
    $LIM = $_POST['LIM'];
}
if (isset($_POST['msg_comments'])) {
    $msg_comments = $_POST['msg_comments'];
}
if ($VARDB_server!=="192.168.1.252")
    die("deprecated ".$VARDB_server);

$PHP_AUTH_USER = $_SERVER[PHP_AUTH_USER];
$IP = $_SERVER[REMOTE_ADDR];

function removeAcentos($string)
{
    $string = strtr($string, array(
        'Á' => 'A', 'À' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A', 'Ä' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
        'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ð' => 'Eth',
        'Ñ' => 'N', 'Ó' => 'O', 'Ò' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
        'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y',
        'á' => 'a', 'à' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a', 'ä' => 'a', 'æ' => 'ae', 'ç' => 'c',
        'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e', 'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'eth',
        'ñ' => 'n', 'ó' => 'o', 'ò' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
        'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y',
        'ß' => 'sz', 'þ' => 'thorn', 'ÿ' => 'y'));
    return $string;
}

function log_admin($topic, $event, $id, $query, $comments = "")
{
    global $link, $PHP_AUTH_USER;
    $stmt = "INSERT INTO vicidial_admin_log set event_date=NOW(), user='$PHP_AUTH_USER', ip_address='$IP', event_section='$topic', event_type='ADD', record_id='$id', event_code='$event', event_sql='" . mysql_real_escape_string($query) . "', event_notes='$comments';";
    $rslt = mysql_query($stmt, $link);
    if (!$rslt) {
        echo "Log Error: " . mysql_error() . " Whole query:" . $stmt;
    }
}

function send_sms_api($nr, $msg)
{

//set POST variables
    $url = 'https://message-router.appspot.com/api/v0/sms';
    $fields = array(
        "account_id" => "5634472569470976",
        "private_key" => "26ddf75c-9bc6-44e4-a099-1c5f3b7d2995",
        "sender" => "ACUSTICA ME",
        "msisdn" => $nr,
        "msg" => $msg
    );
    $fields_string = json_encode($fields);

//open connection
    $ch = curl_init($url);
//set the url, number of POST vars, POST data
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($fields_string))
    );
//execute post
    $result = curl_exec($ch);

//close connection
    curl_close($ch);

    return $result;
}

function send_email($email_address, $email_name, $msg, $nr, $port)
{
    $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
        ->setUsername('ccamemail@gmail.com')
        ->setPassword('ccamemail1234');

    $mailer = Swift_Mailer::newInstance($transport);

    $message = Swift_Message::newInstance("port:$port-$nr")
        ->setFrom(array('ccamemail@gmail.com' => 'Acústica Médica'))
        ->setTo(array($email_address => $email_name));

    $message->setBody($msg);

    $result = $mailer->send($message);

    if ($result >= 1) {
        return true;
    } else {
        return false;
    }
}

function send_gateway($msg, $phone_number, $gateways_ports, $count = 0)
{
    global $link;

    $url = "http://$gateways_ports[IP]/cgi/WebCGI?11401=";
    $fields = array(
        'destination' => urlencode($phone_number),
        'port' => urlencode($gateways_ports[port]),
        'content' => urlencode($msg),
        'callingcode' => urlencode("351"),
        'account' => urlencode($gateways_ports[user]),
        'password' => urlencode($gateways_ports[pass])
    );
    foreach ($fields as $key => $value) {
        $fields_string .= $key . '=' . $value . '&';
    }
    $fields_string = rtrim($fields_string, "&");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 6);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    curl_close($ch);
    if ($count > 1) {
        sleep(10);
    }

    if ($response)
        return true;

    return false;
}

function send_sms($msg, $gateways_ports, $listas, $camp_id, $comments = false)
{
    global $link;
    /* if (!$comments) {
         $msg = removeAcentos($msg);
     }*/
    $count = count($listas);

    log_admin("SMS", "SEND " . $count . "sms Start", $camp_id, "");

    for ($i = 0; $i < $count; $i++) {

        if (!$comments) {
            $msg_per = str_replace("--A--first_name--B--", $listas[$i]["first_name"], $msg);

            $msg_per = str_replace("--A--f--B--", $listas[$i]["middle_initial"], $msg_per);
            $msg_per = str_replace("--A--m--B--", $listas[$i]["vendor_lead_code"], $msg_per);

            $msg_G = str_replace("--A--first_name--B--", $listas[$i]["first_name"], $msg);

            $msg_G = str_replace("--A--f--B--", $listas[$i]["middle_initial"], $msg_G);
            $msg_G = str_replace("--A--m--B--", $listas[$i]["vendor_lead_code"], $msg_G);


            $msg_per = removeAcentos($msg_per);
        } else {
            $msg_per = removeAcentos($listas[$i]["comments"]);
            $msg_G = $msg_per;
        }

        $result = send_sms_api($listas[$i]["phone_number"], $msg_per);
        /*
                if ($gateways_ports[type] == "GATEWAY") {
                    if (!send_gateway($msg_per, $listas[$i][phone_number], $gateways_ports, $count)) {
                        return false;
                    }
                } else {
                    if (!send_email("ccamemail@gmail.com", "Acustica MyPBX", $msg_per, $listas[$i][phone_number], $gateways_ports[port])) {
                        return false;
                    }
                }*/


        mysql_query("INSERT INTO sms_list VALUES (NULL , $camp_id, NOW(), '" . $listas[$i][lead_id] . "', '" . $listas[$i][phone_number] . "','" . mysql_real_escape_string($msg_G) . "','$gateways_ports[descricao]')", $link) or die(mysql_error());
    }

    log_admin("SMS", "SEND " . $count . "sms End", $camp_id, "");
    // close cURL resource, and free up system resources

    return $result;
}

$gateways_brute = mysql_query("Select IP,user,pass,port,descricao,ext,type from gsm_gateways WHERE ID_gsm=$gateid;", $link) or die(mysql_error());
$gateways_ports = mysql_fetch_assoc($gateways_brute);


$nr = preg_replace("/[^0-9]/", "", $nr);
$LIM = preg_replace("/[^0-9]/", "", $LIM);

//Manda sms por campanha form edit
if ($modo == "camp" and isset($_POST['id_camp']) AND $msg_comments != 1) {
    if (!is_numeric($LIM) or $LIM == 0) {
        echo "ERRO: O limite não pode vir vazio ou a 0.";
        exit;
    }

    $execao_tmn = ($operador == 96) ? "OR ( a.`phone_number`  REGEXP '^92[024567]')" : "";

    $listas_brute = mysql_query("SELECT a.lead_id,a.phone_number,a.first_name,middle_initial,vendor_lead_code FROM `vicidial_list` a LEFT JOIN (SELECT * FROM `sms_list` WHERE id_sms_campaign=$id_camp) s ON a.lead_id=s.lead_id WHERE s.lead_id is NULL  and list_id='$lista' AND ( a.`phone_number` LIKE '$operador%' $execao_tmn ) Limit 0,$LIM", $link) or die(mysql_error());;
    if (mysql_num_rows($listas_brute) == 0) {
        echo "Não há Nºs $operador .";
    }

    $i = 0;
    while ($row = mysql_fetch_assoc($listas_brute)) {
        $listas[$i] = $row;
        $i++;
    }

    if (send_sms($msg, $gateways_ports, $listas, $id_camp)) {
        echo 'Enviadas com sucesso.';
    } else {
        header('HTTP/1.1 500 The gateways could be unaccessible');
        die('ERROR');
    }

    //Manda sms por campanha form normal
} elseif ($modo == "camp" AND $msg_comments != 1) {
    if (!is_numeric($LIM) or $LIM == 0) {
        echo "ERRO: O limite não pode vir vazio ou a 0.";
        exit;
    }

    $execao_tmn = ($operador == 96) ? "OR ( `phone_number`  REGEXP '^92[024567]' )" : "";

    mysql_query("INSERT INTO sms_campaigns VALUES (NULL , '" . mysql_real_escape_string($msg) . "', '$operador')", $link) or die(mysql_error());
    $id = mysql_insert_id($link);

    $listas_brute = mysql_query("SELECT lead_id,phone_number,first_name,middle_initial,vendor_lead_code FROM `vicidial_list` WHERE list_id='$lista' AND (`phone_number` LIKE '$operador%' $execao_tmn ) Limit 0,$LIM", $link) or die(mysql_error());;
    if (mysql_num_rows($listas_brute) == 0) {
        echo "Não há Nºs $operador .";
        exit;
    }
    $i = 0;
    while ($row = mysql_fetch_assoc($listas_brute)) {
        $listas[$i] = $row;
        $i++;
    }

    if (send_sms($msg, $gateways_ports, $listas, $id)) {
        echo json_encode(array('Enviadas com sucesso.'));
    } else {
        header('HTTP/1.1 500 The gateways could be unaccessible');
        die('ERROR');
    }


    //Manda sms por unidade form normal
} elseif ($modo == "uni") {
    $list = array(array(phone_number => $nr, lead_id => "N", first_name => ""));

    if (send_sms($msg, $gateways_ports, $list, "998")) {
        echo json_encode(array('Enviada com sucesso.'));
    } else {
        header('HTTP/1.1 500 The gateways could be unaccessible');
        die('ERROR');
    }
} elseif ($modo == "camp" and isset($_POST['id_camp']) AND $msg_comments == 1) {
    if (!is_numeric($LIM) or $LIM == 0) {
        echo "ERRO: O limite não pode vir vazio ou a 0.";
        exit;
    }

    $execao_tmn = ($operador == 96) ? "OR ( a.`phone_number`  REGEXP '^92[024567]')" : "";

    $listas_brute = mysql_query("SELECT a.lead_id,a.phone_number,a.first_name,middle_initial,vendor_lead_code,comments FROM `vicidial_list` a LEFT JOIN (SELECT * FROM `sms_list` WHERE id_sms_campaign=$id_camp) s ON a.lead_id=s.lead_id WHERE s.lead_id is NULL  and list_id='$lista' AND ( a.`phone_number` LIKE '$operador%' $execao_tmn ) Limit 0,$LIM", $link) or die(mysql_error());;
    if (mysql_num_rows($listas_brute) == 0) {
        echo "Não há Nºs $operador .";
    }

    $i = 0;
    while ($row = mysql_fetch_assoc($listas_brute)) {
        $listas[$i] = $row;
        $i++;
    }

    if ($result = send_sms($msg, $gateways_ports, $listas, $id_camp, true)) {
        echo json_encode(array('Enviadas com sucesso.', $result));
    } else {
        header('HTTP/1.1 500 The gateways could be unaccessible');
        die('ERROR');
    }
} elseif ($modo == "camp" AND $msg_comments == 1) {
    if (!is_numeric($LIM) or $LIM == 0) {
        echo "ERRO: O limite não pode vir vazio ou a 0.";
        exit;
    }

    $execao_tmn = ($operador == 96) ? "OR ( `phone_number`  REGEXP '^92[024567]' )" : "";

    mysql_query("INSERT INTO sms_campaigns VALUES (NULL , '" . mysql_real_escape_string($msg) . "', '$operador',1)", $link) or die(mysql_error());
    $id = mysql_insert_id($link);

    $listas_brute = mysql_query("SELECT lead_id,phone_number,first_name,middle_initial,vendor_lead_code,comments FROM `vicidial_list` WHERE list_id='$lista' AND (`phone_number` LIKE '$operador%' $execao_tmn ) Limit 0,$LIM", $link) or die(mysql_error());
    if (mysql_num_rows($listas_brute) == 0) {
        echo "Não há Nºs $operador .";
        exit;
    }
    $i = 0;
    while ($row = mysql_fetch_assoc($listas_brute)) {
        $listas[$i] = $row;
        $i++;
    }

    if (send_sms($msg, $gateways_ports, $listas, $id, true)) {
        echo json_encode(array('Enviadas com sucesso.'));
    } else {
        header('HTTP/1.1 500 The gateways could be unaccessible');
        die('ERROR');
    }
}
?>
		
