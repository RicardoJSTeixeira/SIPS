<?php

require ("dbconnect.php");
require ('../swiftemail/lib/swift_required.php');

function removeAcentos($string) {

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

$PHP_AUTH_USER = $user;
$IP = "";

function log_admin($topic, $event, $id, $query, $comments = "", $type = "") {
    global $link, $IP, $PHP_AUTH_USER;
    $stmt = "INSERT INTO vicidial_admin_log set event_date=NOW(), user='$PHP_AUTH_USER', ip_address='$IP', event_section='$topic', event_type='$type', record_id='$id', event_code='$event', event_sql='" . mysql_real_escape_string($query) . "', event_notes='$comments';";
    $rslt = mysql_query($stmt, $link);
    if (!$rslt) {
        echo "Log Error: " . mysql_error() . " Whole query:" . $stmt;
    }
}

function send_email($email_address, $email_name, $msg, $nr, $port) {
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

function send_gateway($msg, $phone_number, $gateways_ports, $count = 0) {
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

    if ($response) {
        return true;
    }

    return false;
}

function send_sms($msg, $gateways_ports, $listas, $camp_id) {
    global $link;

    $msg = removeAcentos($msg);

    $count = count($listas);

    //log_admin("SMS", "SEND " . $count . "sms Start", $camp_id, "");

    for ($i = 0; $i < $count; $i++) {

        if ($gateways_ports[type] == "GATEWAY") {

            if (!send_gateway($msg, $listas[$i][phone_number], $gateways_ports, $count)) {
                return false;
            }
        } else {
            if (!send_email("ccamemail@gmail.com", "Acustica MyPBX", $msg, $listas[$i][phone_number], $gateways_ports[port])) {
                return false;
            }
        }
        mysql_query("INSERT INTO sms_list VALUES (NULL , '$camp_id', NOW(), '" . $listas[$i][lead_id] . "', '" . $listas[$i][phone_number] . "','" . mysql_real_escape_string($msg) . "','$gateways_ports[descricao]')", $link);
    }

    //log_admin("SMS", "SEND " . $count . "sms End", $camp_id, "");
    // close cURL resource, and free up system resources

    return true;
}

function validate_phone($phone) {
    if (preg_match('/^9[0-9]{8}$/', $phone)) {
        return array(TRUE, "9");
    /*if (preg_match('/^96[0-9]{7}$/', $phone)) {
        return array(TRUE, "96");
    } elseif (preg_match('/^91[0-9]{7}$/', $phone)) {
        return array(TRUE, "91");
    } elseif (preg_match('/^93[0-9]{7}$/', $phone)) {
        return array(TRUE, "93");
    } elseif (preg_match('/^929[0-9]{6}$/', $phone)) {
        return array(TRUE, "9");
    } elseif (preg_match('/^92[024567][0-9]{6}$/', $phone)) {
        return array(TRUE, "96");*/
    } else {
        return array(FALSE);
    }
}

function month2mes($mes) {

    switch ($mes) {
        case 1: return "Janeiro";
        case 2: return "Fevereiro";
        case 3: return "Março";
        case 4: return "Abril";
        case 5: return "Maio";
        case 6: return "Junho";
        case 7: return "Julho";
        case 8: return "Agosto";
        case 9: return "Setembro";
        case 10: return "Outubro";
        case 11: return "Novembro";
        case 12: return "Dezembro";

        default:return 'Erro';
    }
}

$query = "Select phone_number, alt_phone, address3 from vicidial_list where lead_id=$lead_id;";
$rslt = mysql_query($query, $link);
if (mysql_num_rows($rslt)) {
    $row = mysql_fetch_assoc($rslt);

    $phone_number = validate_phone($row[phone_number]);
    $alt_phone = validate_phone($row[alt_phone]);
    $address3 = validate_phone($row[address3]);

    if ($phone_number[0]) {
        $match = $phone_number[1];
        $nr = $row[phone_number];
    } elseif ($alt_phone[0]) {
        $match = $alt_phone[1];
        $nr = $row[alt_phone];
    } elseif ($address3[0]) {
        $match = $address3[1];
        $nr = $row[address3];
    } else {
        exit(); //Não há telemoveis
    }

    if (mysql_num_rows(mysql_query("SHOW TABLES LIKE 'custom_" . strtoupper(trim($campaign)) . "'"))) {
        $stmt = "SELECT `marchora`,`marcdata`,`tipoconsulta`,`consultorio`,`consultoriodois`,`postal_code` FROM   `custom_" . strtoupper(trim($campaign)) . "` a INNER JOIN vicidial_list b ON a.lead_id = b.lead_id WHERE a.lead_id='$lead_id'";
        $rslt = mysql_query($stmt, $link);
        if (mysql_num_rows($rslt)) {
            $cons = mysql_fetch_assoc($rslt);
            if ($cons[tipoconsulta] == 'Home' AND preg_match("/9000|9999/", $cons[postal_code])) {
                $msg[0] = "Caro(a) Cliente, em breve será contactado pelo tecnico Audioprotesista ACUSTICA MEDICA para agendar consigo o dia e a hora da consulta gratuita em sua casa.";
            } elseif ($cons[tipoconsulta] == 'Home') {
                $msg[0] = "Caro(a) Cliente, confirmamos a sua consulta auditiva marcada com a ACUSTICA MEDICA para dia " . date("j/", strtotime($cons[marcdata])) . month2mes(date("n", strtotime($cons[marcdata]))) . date("-H\hi", strtotime($cons[marchora])) . " em sua casa. Nosso contacto 808 231 231";
            } elseif ($cons[tipoconsulta] == 'Branch') {
                $q = "SELECT `morada`,`localidade` FROM `sips_sd_asm` WHERE `code` like '$cons[consultoriodois]';";
                $r = mysql_query($q, $link);
                if (mysql_num_rows($r)) {
                    $r = mysql_fetch_assoc($r);
                    $msg[0] = "Caro(a) Cliente, confirmamos sua consulta auditiva marcada para dia " . date("j/", strtotime($cons[marcdata])) . month2mes(date("n", strtotime($cons[marcdata]))) . date("-H\hi", strtotime($cons[marchora])) . " no Consultório ACUSTICA MEDICA $r[localidade].";

                    $msg[1] = "Esperamos por si na morada: $r[morada]";
                }
            } elseif ($cons[tipoconsulta] == 'CATOS') {

                $q = "SELECT `morada`,`localidade` FROM `sips_sd_asm` WHERE `code` like '$cons[consultorio]';";
                $r = mysql_query($q, $link);
                if (mysql_num_rows($r)) {
                    $r = mysql_fetch_assoc($r);

                    $msg[0] = "Caro(a) Cliente, confirmamos sua consulta auditiva marcada para dia " . date("j/", strtotime($cons[marcdata])) . month2mes(date("n", strtotime($cons[marcdata]))) . date("-H\hi", strtotime($cons[marchora])) . " no Centro de Atendimento ACUSTICA MEDICA $r[localidade].";

                    $msg[1] = "Esperamos por si na morada: $r[morada]";
                }
            }
        } else {
            exit(); //Não há lead na custom
        }
    } else {
        exit(); //Não há custom
    }

    $list = array(array(phone_number => $nr, lead_id => $lead_id, first_name => ""));

    $gateways_brute = mysql_query("Select IP,user,pass,port,descricao,type from gsm_gateways WHERE ext like '$match' and active=1  order by rand();", $link);
    if (mysql_num_rows($gateways_brute)) {
        $gateways_ports = mysql_fetch_assoc($gateways_brute);
        for ($i = 0; $i < count($msg); $i++) {
            if (!send_sms($msg[$i], $gateways_ports, $list, $campaign)) {
                exit();
            }
        }
    }
}
?>

