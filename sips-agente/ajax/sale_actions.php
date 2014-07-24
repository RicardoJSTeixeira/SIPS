<?php
require("../../ini/dbconnect.php");
if (isset($_GET['client'])) { $client = $_GET['client']; } else { $client = $_POST['client']; }
if (isset($_GET['campaign_id']))  { $campaign_id = $_GET['campaign_id']; } else { $campaign_id = $_POST['campaign_id']; }
if (isset($_GET['lead_id']))  { $lead_id = $_GET['lead_id']; } else { $lead_id = $_POST['lead_id']; }
if (isset($_GET['dispoAtt']))  { $dispoAtt = $_GET['dispoAtt']; } else { $dispoAtt = $_POST['dispoAtt']; }


switch ($client) { 
    case 'connecta' : {
            switch ($campaign_id) {
                case 'W00003' :
                case 'W00015' :
                case 'W00016' :
                case 'W00017' : connectaPostCalendar(); break;
                case 'W00004' : connectaMensageiros(); break;
                case 'W00009' : connectaMensageiros(); break;
            }
            confirmacao($lead_id, $dispoAtt, $link);
            break;
        }
    case 'acusticamedica': SendSms(); break;
}

function confirmacao($lead_id, $dispoAtt, $link) {
    function removeConfirm($lead_id, $link) {
        $qdelete = "update crm_confirm_feedback_last set sale = '1' where lead_id='" . mysql_real_escape_string($lead_id) . "';";
        mysql_query($qdelete, $link) or die(mysql_error());
        $qdelete = "Update vicidial_list set validation = NULL where lead_id = '" . mysql_real_escape_string($lead_id) . "';";
        mysql_query($qdelete, $link) or die(mysql_error());
    }
    if ($dispoAtt["completed"] == 'true') {
        removeConfirm($lead_id, $link);
    } else {
        return false;
    }
}


function connectaMensageiros() {

function query($sQuery, $hDb_conn, $sError, $bDebug)
{
    if(!$rQuery = @mssql_query($sQuery, $hDb_conn))
    {
        $sMssql_get_last_message = mssql_get_last_message();
        $sQuery_added  = "BEGIN TRY\n";
        $sQuery_added .= "\t".$sQuery."\n";
        $sQuery_added .= "END TRY\n";
        $sQuery_added .= "BEGIN CATCH\n";
        $sQuery_added .= "\tSELECT 'Error: '  + ERROR_MESSAGE()\n";
        $sQuery_added .= "END CATCH";
        $rRun2= @mssql_query($sQuery_added, $hDb_conn);
        $aReturn = @mssql_fetch_assoc($rRun2);
        if(empty($aReturn))
        {
            echo $sError.'. MSSQL returned: '.$sMssql_get_last_message.'.<br>Executed query: '.nl2br($sQuery);
        }
        elseif(isset($aReturn['computed']))
        {
            echo $sError.'. MSSQL returned: '.$aReturn['computed'].'.<br>Executed query: '.nl2br($sQuery);
        }
        return FALSE;
    }
    else
    {
        return $rQuery;
    }
}



    if (isset($_GET['lead_id'])) { $lead_id = $_GET['lead_id']; } else { $lead_id = $_POST['lead_id']; }
    if (isset($_GET['uniqueid'])) { $unique_id = $_GET['uniqueid']; } else { $unique_id = $_POST['uniqueid']; }
    if (isset($_GET['user'])) { $user = $_GET['user']; } else { $user = $_POST['user']; }
    
    $link = mysql_connect("172.16.7.25", "sipsadmin", "sipsps2012");
    mysql_select_db("asterisk");
    $query = "SELECT tag_elemento,valor FROM `script_result` WHERE tag_elemento IN ('159','153', '155', '160', '156', '157', '154', '161', '165') and unique_id = '$unique_id' order by tag_elemento ASC";
    
    $query = mysql_query($query, $link) or die(mysql_error());
    
    for ($i = 0; $i < mysql_num_rows($query); $i++) {
        $row = mysql_fetch_row($query);
        $results[$row[0]] = $row[1];
    }
    
   // $lead_id;
   // $user;
    $data_visita = str_replace("'", "", $results[159]);
    $hora_visita = '09h-18h';
    $nome = str_replace("'", "", $results[153]);
    $morada = str_replace("'", "", $results[155]);
    $cp = str_replace("'", "", $results[160]);
    $localidade = str_replace("'", "", $results[156]);
    $concelho = str_replace("'", "", $results[157]);
    $telefone = str_replace("'", "", $results[154]);
    $entrega_docs = str_replace("'", "", $results[161]);
    $observacoes = str_replace("'", "", $results[165]);
                
   // $query_final = "exec clientes.InserirVisitaMensageiros $lead_id, '$user', '$data_visita', '$hora_visita', '$nome', '$morada', '$cp', '$localidade', '$concelho', '$telefone', '$entrega_docs', '$observacoes'";
   // echo $query_final;

    $link = mssql_connect('172.16.5.2', 'gocontact', '') or die(mssql_get_last_message());
    mssql_select_db('Clientes', $link) or die(mssql_get_last_message());
    
    $query_final = utf8_decode("INSERT INTO Clientes.[532_Agenda] (idagenda, comercial, estado, operador, datamarcacao, horamarcacao, datavisita, horavisita, idcliente, nome, contacto, morada, codpostal, localidade, concelho, [observações], mensageirova, entregadocs) SELECT (SELECT MAX(idagenda) + 1 as ultimo from Clientes.[532_Agenda]), 'mensageiros', -1, '$user', convert(datetime,getdate(),105), convert(varchar(5),getdate(),108), convert(datetime,'$data_visita',105), '$hora_visita', '$lead_id', '$nome', '$telefone', '$morada', '$cp', '$localidade', '$concelho', '$observacoes', '2', '$entrega_docs'");
    
    query($query_final, $link);
    echo $query_final;
   // $sql = mssql_query($query_final, $link) or die(mssql_get_last_message());
   // mssql_get_last_message();
    
}
    
function connectaPostCalendar() {
    if (isset($_GET['lead_id'])) { $lead_id = $_GET['lead_id']; } else { $lead_id = $_POST['lead_id']; }
    if (isset($_GET['uniqueid'])) { $unique_id = $_GET['uniqueid']; } else { $unique_id = $_POST['uniqueid']; }
    if (isset($_GET['user'])) { $user = $_GET['user']; } else { $user = $_POST['user']; }
    
    $link = mysql_connect("172.16.7.25:3306", "sipsadmin", "sipsps2012");
    mysql_select_db("asterisk");
    $query = "SELECT middle_initial FROM `vicidial_list` WHERE lead_id = '$lead_id'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_assoc($query);
    $origem = utf8_decode($row['middle_initial']); // middle_initial
    $query = "SELECT tag_elemento,valor FROM `script_result` WHERE tag_elemento IN ('20','73', '76', '78', '79', '80', '75', '77', '74', '81', '82', '87', '88') and unique_id = '$unique_id' order by tag_elemento ASC";
    
    $query = mysql_query($query, $link) or die(mysql_error());
    
    for ($i = 0; $i < mysql_num_rows($query); $i++) {
        $row = mysql_fetch_row($query);
        $results[$row[0]] = $row[1];
    }
    
  //  print_r($results);

    $tipo_vencimento = $results[20]; // cp co ->script 20
    $nome_cliente = utf8_decode(str_replace("'", "", $results[73])); // first_name 73
    $idade = $results[74]; // ->script 74
    $morada = utf8_decode(str_replace("'", "", $results[75])); // script 75
    $localidade = utf8_decode(str_replace("'", "", $results[76])); // city 76
    $cod_postal = $results[77]; // 1234-567 script 77
    $telefone = $results[78]; // phone_number 78
    $telefone_alternativo = $results[79]; // alt_phone or address3 79
    $observações = utf8_decode(str_replace("'", "", $results[80])); // comments 80
    $tipo_cartao = $results[81]; // ->script 81
    $num_cartoes = $results[82]; // ->script 82
    $nif = $results[87]; // ->script  87
    $tem_credito = $results[88]; // script 88
    $query = "SELECT start_date, id_resource FROM sips_sd_reservations where lead_id = '$lead_id' and start_date > DATE(NOW()) LIMIT 1";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_row($query);
    $distrito = ''; // vazio
    $data_visita = date('d/m/Y',strtotime($row[0])); // dd/mm/yyyy -> calendario
    $hora_visita =  date('H:i',strtotime($row[0])); // hh:mm -> calendario
    $query = "SELECT a.display_text FROM sips_sd_schedulers a inner join sips_sd_resources b on a.id_scheduler = b.id_scheduler where id_resource = '$row[1]'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_row($query);
    $concelho = str_replace("'", "", $row[0]); // provincia -> ref
    
    $query = "SELECT DISTRITO FROM Distritos_BarclayCard where CONCELHO like '$concelho'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_row($query);
    
    $concelho = utf8_decode($concelho);
    
    $distrito = utf8_decode($row[0]);
    //$query_final = "exec clientes.InserirVisita 'TESTE' , 1, 'TESTE'  , 'TEST'  , '25/01/2014', '10:00', 'TESTE'  , 'TESTE'  , '1234-123'  , 'TESTE'  , 'Lisboa'  , '918099390'  , '918099390'  , 34, 'CO'  , 'Cartão GOLD' , 1, '123456789', 'S' , 'Lisboa', 'teste de marcação por sp'";
    //exec clientes.InserirVisita 'Alcobaça' , 168029, 'barc1'  , 'barc1'  , '11/11/2013', '10:00', 'asdasdas'  , 'asdasdasd'  , '1234-123'  , 'asdasd'  , 'Alcobaça'  , '1231231'  , 'Gold'  , 12, 'CO'  , '229722210' , S, '', '' , 'Leiria', '1'
     $query_final = "exec clientes.InserirVisita '$origem' , $lead_id, '$user'  , '$user'  , '$data_visita', '$hora_visita', '$nome_cliente'  , '$morada'  , '$cod_postal'  , '$localidade'  , '$concelho'  , '$telefone'  , '$telefone_alternativo'  , $idade, '$tipo_vencimento'  , '$tipo_cartao' , $num_cartoes, '$nif', '$tem_credito' , '$distrito', '$observações'";
     echo $query_final;
     $link = mssql_connect('172.16.5.2', 'gocontact', '') or die(mssql_get_last_message());
     $sql = @mssql_query($query_final, $link) or die(mssql_get_last_message());
     mssql_get_last_message();
}

function SendSms(){
    usleep(2000000);
    if (isset($_GET['lead_id'])) { $lead_id = $_GET['lead_id']; } else { $lead_id = $_POST['lead_id']; }
    if (isset($_GET['uniqueid'])) { $unique_id = $_GET['uniqueid']; } else { $unique_id = $_POST['uniqueid']; }
    if (isset($_GET['user'])) { $user = $_GET['user']; } else { $user = $_POST['user']; }
    if (isset($_GET['campaign_id']))  { $campaign = $_GET['campaign_id']; } else { $campaign = $_POST['campaign_id']; }

    $link = mysql_connect("192.168.1.252", "sipsadmin", "sipsps2012");
    mysql_select_db("asterisk");
    
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

    function log_admin($topic, $event, $id, $query, $comments = "", $type = "") {
        global $link, $user;
        $stmt = "INSERT INTO vicidial_admin_log set event_date=NOW(), user='$user', ip_address='', event_section='$topic', event_type='$type', record_id='$id', event_code='$event', event_sql='" . mysql_real_escape_string($query) . "', event_notes='$comments';";
        $rslt = mysql_query($stmt, $link);
        if (!$rslt) {
            echo "Log Error: " . mysql_error() . " Whole query:" . $stmt;
        }
    }

    function send_gateway($msg, $phone_number, $gateways_ports) {
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

        return (bool) $response;
    }

    function send_sms($msg, $gateways_ports, $listas, $camp_id) {
        global $link;

        $msg = removeAcentos($msg);

        $count = count($listas);

        for ($i = 0; $i < $count; $i++) {

            if (!send_gateway($msg, $listas[$i][phone_number], $gateways_ports, $count)) {
                mysql_query("INSERT INTO sms_list VALUES (NULL , '$camp_id', NOW(), '" . $listas[$i][lead_id] . "', '" . $listas[$i][phone_number] . "','" . mysql_real_escape_string($msg) . "','$gateways_ports[descricao]')", $link);
            }
        }

        return true;
    }

    function validate_phone($phone) {
        if (preg_match('/^9[0-9]{8}$/', $phone)) {
            return array(TRUE, "9");
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
}