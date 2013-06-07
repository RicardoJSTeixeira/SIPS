<?

//error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
//ini_set('display_errors', '1');

require('../../../ini/dbconnect.php');
require('../../../ini/functions.php');
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
echo "\xEF\xBB\xBF";


foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

function columnmakerwithtotal($array_head, $array, $total = false, $text = false, $i = 0) {
    $sum = 0;
    if (count($array) > 0) {
        foreach ($array as $value) {
            $array_head[] = $value;
            if ($total) {
                $sum+=$value;
            }
        }
    }

    if ($i > 0) {
        if (count($array) < $i) {
            $max = $i - count($array);
            for ($index = 0; $index < $max; $index++) {
                $array_head[] = 0;
            }
        }
    }
    if ($total) {
        $array_head[] = $sum;
    }
    if ($text) {
        $array_head[] = "Total";
    }
    return $array_head;
}

function joinarray($arr1,$arr2){
    if(is_array($arr1) and !is_array($arr2)){return $arr1;}
    if(is_array($arr2) and !is_array($arr1)){return $arr2;}
    if(!is_array($arr2) and !is_array($arr1)){return array();}
    
    $add = function($a, $b) { return $a + $b; };
return array_map($add, $arr1, $arr2);
}

if (isset($report_marc_outbound)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "marc_outbound_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');

    fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'), ";");

    foreach ($camp_options as $currentCamp) {
        $query_log = "SELECT a.lead_id,a.campaign_id,a.call_date AS data,a.status AS resultado, a.user as utilizador, b.*, c.*, d.campaign_name AS campanha FROM vicidial_log a JOIN custom_" . strtoupper($currentCamp) . " b ON a.lead_id = b.lead_id JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_campaigns d ON a.campaign_id = d.campaign_id where a.status IN ('MARC', 'NOVOCL') AND a.campaign_id LIKE '$currentCamp' AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'";

        $query_log = mysql_query($query_log, $link) or die(mysql_error());

        for ($i = 0; $i < mysql_num_rows($query_log); $i++) {
            $row = mysql_fetch_assoc($query_log);


            $cod = "";
            if ($row['tipoconsulta'] == 'CATOS') {
                $cod = $row['consultorio'];
            } else {
                if ($row['tipoconsulta'] == 'Branch') {
                    $cod = $row['consultoriodois'];
                }
            }

            $campid = $row['extra1'];
            $no = $row['extra2'];
            $c_message = "Sem Campanha/Inbound/Chamada Manual";
            if ($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") {
                $c_message = "Lead Duplicada - Ignorar/Dados Incompletos";
            }
            if ((preg_match("/", $row['consultorio']) === 1)) {
                $c_message = "bom";
            }
            fputcsv($output, array(
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                $row['extra3'],
                $row['extra10'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $row['marchora'],
                $row['marcdata'],
                $row['tipoconsulta'],
                "",
                $row['obs'],
                "",
                "",
                $row['utilizador'],
                $row['resultado'],
                $row['campanha'],
                $row['data']
                    ), ";");
        }
    }
}

if (isset($report_feedback_outbound)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "feedback_outbound_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');

    fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'), ";");
    $fbs = implode("','", $feedbacks);

    foreach ($camp_options as $currentCamp) {

        $query_log = "SELECT a.lead_id,a.campaign_id,a.call_date AS data,a.status AS resultado, a.user as utilizador, b.*, c.*, d.campaign_name AS campanha FROM vicidial_log a JOIN custom_".  strtoupper($currentCamp)." b ON a.lead_id = b.lead_id JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_campaigns d ON a.campaign_id = d.campaign_id where a.status IN ('$fbs') AND a.campaign_id LIKE '$currentCamp' AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00' group by a.lead_id";
        $query_log = mysql_query($query_log, $link) or die(mysql_error());

        for ($i = 0; $i < mysql_num_rows($query_log); $i++) {
            $row = mysql_fetch_assoc($query_log);


            $cod = "";
            if ($row['tipoconsulta'] == 'CATOS') {
                $cod = $row['consultorio'];
            } else {
                if ($row['tipoconsulta'] == 'Branch') {
                    $cod = $row['consultoriodois'];
                }
            }

            $campid = $row['extra1'];
            $no = $row['extra2'];
            $c_message = "Sem Campanha/Inbound/Chamada Manual";
            if ($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") {
                $c_message = "Lead Duplicada - Ignorar/Dados Incompletos";
            }
            if ((preg_match("/", $row['consultorio']) === 1)) {
                $c_message = "bom";
            }
            fputcsv($output, array(
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                $row['extra3'],
                $row['extra10'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $row['marchora'],
                $row['marcdata'],
                $row['tipoconsulta'],
                "",
                $row['obs'],
                "",
                "",
                $row['utilizador'],
                $row['resultado'],
                $row['campanha'],
                $row['data']
                    ), ";");
        }
    }
}
if (isset($report_desm_remarc_outb)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "desmarc_outbound_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'), ";");


    foreach ($camp_options as $currentCamp) {
        $query_log = "SELECT a.lead_id,a.campaign_id,a.call_date AS data,a.status AS resultado,a.user as utilizador, b.*, c.*, d.campaign_name AS campanha FROM vicidial_log a JOIN custom_$currentCamp b ON a.lead_id = b.lead_id JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_campaigns d ON a.campaign_id = d.campaign_id where a.status IN ('DM', 'RM', 'RMC', 'NSMARC') AND a.campaign_id LIKE '$currentCamp' AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'";
        $query_log = mysql_query($query_log, $link) or die(mysql_error());

        for ($i = 0; $i < mysql_num_rows($query_log); $i++) {
            $row = mysql_fetch_assoc($query_log);


            $cod = "";
            if ($row['tipoconsulta'] == 'CATOS') {
                $cod = $row['consultorio'];
            } else {
                if ($row['tipoconsulta'] == 'Branch') {
                    $cod = $row['consultoriodois'];
                }
            }

            $campid = $row['extra1'];
            $no = $row['extra2'];
            $c_message = "Sem Campanha/Inbound/Chamada Manual";
            if ($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") {
                $c_message = "Lead Duplicada - Ignorar/Dados Incompletos";
            }
            if ((preg_match("/", $row['consultorio']) === 1)) {
                $c_message = "bom";
            }
            fputcsv($output, array(
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                $row['extra3'],
                $row['extra10'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $row['marchora'],
                $row['marcdata'],
                $row['tipoconsulta'],
                "",
                $row['obs'],
                "",
                "",
                $row['utilizador'],
                $row['resultado'],
                $row['campanha'],
                $row['data']
                    ), ";");
        }
    }
}
if (isset($report_desm_remarc_inb)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "desmarc_inbound_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'), ";");


    foreach ($camp_options as $currentCamp) {
        $query_log = "SELECT a.lead_id,a.campaign_id,a.call_date AS data,a.status AS resultado,a.user as utilizador, b.*, c.*, d.group_name AS campanha FROM vicidial_closer_log a JOIN custom_$currentCamp b ON a.lead_id = b.lead_id JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_inbound_groups d ON a.campaign_id = d.group_id where a.status IN ('DM', 'RM', 'RMC', 'NSMARC') AND a.campaign_id LIKE '$currentCamp' AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'";
        $query_log = mysql_query($query_log, $link) or die(mysql_error());

        for ($i = 0; $i < mysql_num_rows($query_log); $i++) {
            $row = mysql_fetch_assoc($query_log);


            $cod = "";
            if ($row['tipoconsulta'] == 'CATOS') {
                $cod = $row['consultorio'];
            } else {
                if ($row['tipoconsulta'] == 'Branch') {
                    $cod = $row['consultoriodois'];
                }
            }

            $campid = $row['extra1'];
            $no = $row['extra2'];
            $c_message = "Sem Campanha/Inbound/Chamada Manual";
            if ($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") {
                $c_message = "Lead Duplicada - Ignorar/Dados Incompletos";
            }
            if ((preg_match("/", $row['consultorio']) === 1)) {
                $c_message = "bom";
            }
            fputcsv($output, array(
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                $row['extra3'],
                $row['extra10'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $row['marchora'],
                $row['marcdata'],
                $row['tipoconsulta'],
                "",
                $row['obs'],
                "",
                "",
                $row['utilizador'],
                $row['resultado'],
                $row['campanha'],
                $row['data']
                    ), ";");
        }
    }
}
if (isset($report_marc_inbound)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "marc_inbound_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");

    $output = fopen('php://output', 'w');
    fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'), ";");
    foreach ($camp_options as $currentCamp) {


        $query_log = "SELECT a.lead_id,a.campaign_id AS linhainbound,a.call_date AS data,a.status AS resultado,a.user as utilizador, b.*, c.*, d.campaign_name AS campanha FROM vicidial_closer_log a JOIN vicidial_agent_log b ON a.uniqueid = b.uniqueid JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_campaigns d ON b.campaign_id = d.campaign_id where a.status IN ('MARC', 'NOVOCL') AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND a.campaign_id LIKE '$currentCamp'";
        $query_log = mysql_query($query_log, $link) or die(mysql_error());

        for ($i = 0; $i < mysql_num_rows($query_log); $i++) {
            $row = mysql_fetch_assoc($query_log);

            $custom_row = mysql_query("SELECT * FROM custom_" . $row['linhainbound'] . " where lead_id LIKE '$row[lead_id]' ");
            if (mysql_num_rows($custom_row) == 0) {
                continue;
            }
            $custom_row = mysql_fetch_assoc($custom_row) or die(mysql_error());

            $cod = "";
            if ($custom_row['tipoconsulta'] == 'CATOS') {
                $cod = $custom_row['consultorio'];
            } else {
                if ($custom_row['tipoconsulta'] == 'Branch') {
                    $cod = $custom_row['consultoriodois'];
                }
            }


            $campid = $row['extra1'];
            $no = $row['extra2'];
            $c_message = "Sem Campanha/Inbound/Chamada Manual";
            if ($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") {
                $c_message = "Lead Duplicada - Ignorar/Dados Incompletos";
            }
            if ((preg_match("/", $row['consultorio']) === 1)) {
                $c_message = "bom";
            }
            fputcsv($output, array(
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                $row['extra3'],
                $row['extra10'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $custom_row['marchora'],
                $custom_row['marcdata'],
                $custom_row['tipoconsulta'],
                "",
                $custom_row['obs'],
                "",
                "",
                $row['utilizador'],
                $row['resultado'],
                $row['linhainbound'],
                $row['data']
                    ), ";");
        }
    }
}/*
if (isset($report_marc_inbound)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "marc_inbound_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');

    fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'), ";");

    foreach ($camp_options as $currentCamp) {
        $query_log = "SELECT a.lead_id,a.campaign_id,a.call_date AS data,a.status AS resultado, a.user as utilizador, b.*, c.*, d.group_name AS campanha FROM vicidial_closer_log a JOIN custom_" . strtoupper($currentCamp) . " b ON a.lead_id = b.lead_id JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_inbound_groups d ON a.campaign_id = d.group_id where a.status IN ('MARC', 'NOVOCL') AND a.campaign_id LIKE '$currentCamp' AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'";

        $query_log = mysql_query($query_log, $link) or die(mysql_error());

        for ($i = 0; $i < mysql_num_rows($query_log); $i++) {
            $row = mysql_fetch_assoc($query_log);


            $cod = "";
            if ($row['tipoconsulta'] == 'CATOS') {
                $cod = $row['consultorio'];
            } else {
                if ($row['tipoconsulta'] == 'Branch') {
                    $cod = $row['consultoriodois'];
                }
            }

            $campid = $row['extra1'];
            $no = $row['extra2'];
            $c_message = "Sem Campanha/Inbound/Chamada Manual";
            if ($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") {
                $c_message = "Lead Duplicada - Ignorar/Dados Incompletos";
            }
            if ((preg_match("/", $row['consultorio']) === 1)) {
                $c_message = "bom";
            }
            fputcsv($output, array(
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                $row['extra3'],
                $row['extra10'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $row['marchora'],
                $row['marcdata'],
                $row['tipoconsulta'],
                "",
                $row['obs'],
                "",
                "",
                $row['utilizador'],
                $row['resultado'],
                $row['campanha'],
                $row['data']
                    ), ";");
        }
    }
}*/

if (isset($report_feedback_inbound)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "feedbacks_inbound_" . $curTime;

    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'), ";");
    $fbs = implode("','", $feedbacks);
    foreach ($camp_options as $currentCamp) {
        $query_log = "SELECT a.lead_id,a.campaign_id AS linhainbound,a.call_date AS data,a.status AS resultado,a.user as utilizador, b.*, c.*, d.campaign_name AS campanha FROM vicidial_closer_log a JOIN vicidial_agent_log b ON a.uniqueid = b.uniqueid JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_campaigns d ON b.campaign_id = d.campaign_id where a.status IN ('$fbs') AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND a.campaign_id LIKE '$currentCamp' group by a.lead_id";

        $query_log = mysql_query($query_log, $link) or die(mysql_error());
        for ($i = 0; $i < mysql_num_rows($query_log); $i++) {
            $row = mysql_fetch_assoc($query_log);

            $custom_row = mysql_query("SELECT * FROM custom_" . $row['linhainbound'] . " where lead_id LIKE '$row[lead_id]' ");
            $custom_row = mysql_fetch_assoc($custom_row) or die(mysql_error());

            $cod = "";
            if ($row['tipoconsulta'] == 'CATOS') {
                $cod = $row['consultorio'];
            } else {
                if ($row['tipoconsulta'] == 'Branch') {
                    $cod = $row['consultoriodois'];
                }
            }

            $campid = $row['extra1'];
            $no = $row['extra2'];
            $c_message = "Sem Campanha/Inbound/Chamada Manual";
            if ($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") {
                $c_message = "Lead Duplicada - Ignorar/Dados Incompletos";
            }
            if ((preg_match("/", $row['consultorio']) === 1)) {
                $c_message = "bom";
            }
            fputcsv($output, array(
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                $row['extra3'],
                $row['extra10'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $custom_row['marchora'],
                $custom_row['marcdata'],
                $custom_row['tipoconsulta'],
                "",
                $custom_row['obs'],
                "",
                "",
                $row['utilizador'],
                $row['resultado'],
                $row['linhainbound'],
                $row['data']
                    ), ";");
        }
    }
}

if (isset($resumo_geral_camp)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "resumo_campanha_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];

    fputcsv($output, array(" "), ";");
    fputcsv($output, array(" ", "Report:", "Resumo Geral"), ";");
    fputcsv($output, array(" ", "De:", $data_inicial), ";");
    fputcsv($output, array(" ", "A:", $data_final), ";");
$i = 0;
    foreach ($_POST['camp_options'] as $key => $campanha) {
      $i++;
       

        $query_marc = "select count(status) from
    (select 
        *
    from
        (select 
        user, status, campaign_id, lead_id
    from
        vicidial_log
    where
        campaign_id LIKE '$campanha' AND status IN ('MARC')
        AND call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'    
    order by call_date DESC) a
    group by lead_id) b";
        $query_marc = mysql_query($query_marc, $link) or die(mysql_error());
        $query_marc = mysql_fetch_row($query_marc);
        $tot_marc[$i] = $query_marc[0];


        $query_novocl = "select count(status) from
    (select 
        *
    from
        (select 
        user, status, campaign_id, lead_id
    from
        vicidial_log
    where
        campaign_id LIKE '$campanha' AND status IN ('NOVOCL')
        AND call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'    
    order by call_date DESC) a
    group by lead_id) b";

        $query_novocl = mysql_query($query_novocl, $link) or die(mysql_error());
        $query_novocl = mysql_fetch_row($query_novocl);
        $tot_novocl[$i] = $query_novocl[0];

        $total_bds = "select GROUP_CONCAT(a.list_id) from (select b.list_id from vicidial_log b inner join vicidial_lists c on b.list_id = c.list_id  where c.campaign_id = '$campanha' and call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00' group by b.list_id) a";
        $total_bds = mysql_query($total_bds, $link) or die(mysql_error());
        $total_bds = mysql_fetch_row($total_bds);
        $total_bds = $total_bds[0];
        $total_bds = explode(",", $total_bds);
        $total_bds = implode("','", $total_bds);



        //$query_total_registos = "select count(lead_id) from vicidial_list where list_id in('" . $total_bds . "') and status <> 'NOVOCL' ";
        $query_total_registos = "select count(a.lead_id) from vicidial_list a  inner join vicidial_lists b ON a.list_id=b.list_id where a.list_id in('$total_bds') and status <> 'NOVOCL'  and active='Y'";
        $query_total_registos = mysql_query($query_total_registos, $link);
        $query_total_registos = mysql_fetch_row($query_total_registos);

        $total_registos[$i] = $query_total_registos[0];

        ##############################################################
        $query_n = "SELECT campaign_name from vicidial_campaigns where campaign_id='$campanha'";
        $query_n = mysql_query($query_n);
        $row_n = mysql_fetch_row($query_n);
        $bds[$i] = $row_n[0];
        ##############################################################
        ### Contagem do Total de Registos
        $total_leads[$i] = 0;
        $dificuldades[$i] = 0;
        $array_negativos = array('AS', 'PRANK', 'CC', 'C', 'DESLI', 'EPIL', 'EPLM', 'A', 'EC', 'ER', 'F', 'FA', 'IDD', 'IF', 'INFO', 'JFC', 'S00046', 'NI', 'NRM', 'DNC', 'O', 'PPA', 'S00045', 'R', 'RD', 'TR', 'VOLC', 'OUTROS');
        $array_dificuldades = array('FAX', 'NA', 'NAT', 'I', 'NAOEX', 'NNP', 'P', 'VM');
 
        
        
        
        $query_global = "select d.status, c.soma, d.status_name from (select status, count(status) as soma from (select lead_id, status from (select lead_id, status from vicidial_log where campaign_id LIKE '$campanha' 
            and call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00' order by call_date DESC) a group by lead_id) b group by status) c inner join (select 
            (status), status_name from vicidial_campaign_statuses group by status UNION ALL select status, status_name from vicidial_statuses group by status) d ON c.status = d.status group by status";
        //fputcsv($output, array(" ", "Query1:", $query_global), ";");
        $query_global = mysql_query($query_global) or die(mysql_error());
        
        while ($row = mysql_fetch_row($query_global)) {
            $tot_leads_2[$i] += $row[1];
            ##############################################################
            ### Contagem dos Agendamentos/Callbacks 'CBACK'

            $status_count[$row[0]][$i] = $row[1];

            if (in_array($row[0], $array_negativos)) {
                $negativos[$i] += $row[1];
            }
            if (in_array($row[0], $array_dificuldades)) {
                $dificuldades[$i] += $row[1];
            }

            if ($row[0] == 'CALLBK' || $row[0] == 'CBHOLD') {
                $agendamentos2[$i] += $row[1];
            }
            if ($row[0] == 'NNP') {
                $nnp[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'PRANK') {
                $brincadeira[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'ER') {
                $exame_recente2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'F') {
                $falecido2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'IDD') {
                $idade2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'JFC') {
                $jconsultorio2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'S00046') {
                $jtmarc[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'VOLC') {
                $volc[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem das Marcacoes 'MARC'

            if ($row[0] == 'MARC') {
                $marcacoes2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem dos Novos Clientes 'NOVOCL'

            if ($row[0] == 'NOVOCL') {
                $novos_clientes2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Amostra 'A'

            if ($row[0] == 'A') {
                $amostra2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback NI 'NI'

            if ($row[0] == 'NI') {
                $ni2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Concorrencia 'C'
            if ($row[0] == 'C') {
                $conc2 = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Realizou Exame 'ER'
            ##############################################################
            ### Contagem do Feedback Otorrino 'O'

            if ($row[0] == 'O') {
                $otorrino2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Nao contactar 'DNC'

            if ($row[0] == 'DNC') {
                $nao_contactar2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Falecido 'F'
            ##############################################################
            ### Contagem do Feedback Futuras Acçoes 'FA'

            if ($row[0] == 'FA') {
                $futuras2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback idade < 45 anos 'IDD'
            ##############################################################
            ### Contagem do ja foi ao consultorio 'JFC'
            ##############################################################
            ### Contagem do Feedback outros 'OUTROS'

            if ($row[0] == 'OUTROS') {
                $outros2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback reclamacao 'R'

            if ($row[0] == 'R') {
                $reclamacao2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Ja tem aparelho AM 'JA'

            if ($row[0] == 'JA') {
                $jatem2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback estabelecimento comercial 'EC'

            if ($row[0] == 'EC') {
                $comercial2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback disponivel apos 20:00 'D'

            if ($row[0] == 'D') {
                $disponivel2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback contactado recente 'CR'

            if ($row[0] == 'CR') {
                $contactado2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback nao reside 'RM'

            if ($row[0] == 'NRM' || $row[0] == 'RM') {
                $naoreside[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem do Feedback nao reside 'TINV'

            if ($row[0] == 'TINV') {
                $tlfinv[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            ##############################################################
            ### Contagem de numeros invalidos 'NAOEX'

            if ($row[0] == 'NAOEX' || $row[0] == 'I') {
                $inv[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }

            ##############################################################
            ### Contagem dos dificuldades REFAZER


            if ($row[0] == 'B') {
                $b2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'ERI') {
                $eri2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'FAX') {
                $fax2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'I') {
                $i2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'NA' || $row[0] == 'NAT') {
                $na2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            /*if () {
                $nat2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }*/
            if ($row[0] == 'RD') {
                $rd2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'VM') {
                $vm2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
        }




//$contactos_negativos = $contactado2+$disponivel2+$comercial2+$jatem2+$reclamacao2+$outros2+$jconsultorio2+$idade2+$futuras2+$falecido2+$nao_contactar2+$otorrino2+$exame_recente2+$conc2+$ni2+$amostra2+$naoreside;

        $contactos_efectuados[$i] = $tot_marc[$i] + $negativos[$i] + $agendamentos2[$i] + $nnp[$i];

        $contactos_uteis[$i] = $tot_marc[$i] + $agendamentos2[$i] + $negativos[$i] - ($brincadeira[$i] + $exame_recente2[$i] + $falecido2[$i] + $idade2[$i] + $jtmarc[$i] + $jconsultorio2[$i] + $volc[$i]);

        $marc_total[$i] = $marcacoes2[$i] + $novos_clientes2[$i];
        $tot_marc_novocl[$i] = $tot_marc[$i] + $tot_novocl[$i];
        $m_TR[$i] = number_format($tot_marc_novocl[$i] / $total_registos[$i] * 100, 2) . " %";
        $m_CE[$i] = number_format($tot_marc_novocl[$i] / $contactos_efectuados[$i] * 100, 2) . " %";
        $m_CU[$i] = number_format($tot_marc_novocl[$i] / $contactos_uteis[$i] * 100, 2) . " %";

        $na_total[$i] = $na2[$i] + $nat2[$i];
        $inv_total[$i] = $inv[$i] + $i2[$i];

        fputcsv($output, array(" "), ";");
        fputcsv($output, array(" "), ";");
        fputcsv($output, array(" ", "Resumo total do Query"), ";");
        fputcsv($output, array(" "), ";");
        mysql_data_seek($query_global, 0);

        while($row = mysql_fetch_row($query_global)){
            if ($row[0] != 'MARC') {
                fputcsv($output, array(" ", $row[2], $row[1], $row[0]), ";");
            } else {
                fputcsv($output, array(" ", $row[2], $tot_marc, $row[0]), ";");
            }
        }
        
        fputcsv($output, array(" "), ";");
        fputcsv($output, array(" "), ";");
        fputcsv($output, array(" "), ";");
    }
    
        fputcsv($output, array(" "), ";");
        fputcsv($output, array(" "), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Campanha: "), $bds), ";");
        fputcsv($output, array(" "), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Total Registos: "), $total_registos), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Total Registos Trabalhados: "), $tot_leads_2), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Contactos Efectuados: "), $contactos_efectuados), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Contactos Uteis: "), $contactos_uteis), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Agendamentos: "), $agendamentos2), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Marcacoes: "), $tot_marc), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Novo Cliente: "), $tot_novocl), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Total Marcacoes: "), $tot_marc_novocl), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Contactos Negativos: "), $negativos), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Dificuldades: "), $dificuldades), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Marcacoes/Total Registos: "), $m_TR), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Marcacoes/Contactos Efectuados: "), $m_CE), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Marcacoes/Contactos Uteis: "), $m_CU), ";");
        fputcsv($output, array(" "), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Total Contactos Negativos: "), $negativos), ";");


//('AS','PRANK','CC','C','DESLI','EPIL','EPLM', 'A', 'EC','ER', 'F','FA','IDD', 'IF', 'INFO', 'JFC','S00046','NI','NRM','DNC','O','PPA','S00045','R','RD','TR','VOLC');

        fputcsv($output, columnmakerwithtotal(array(" ", "  Assistencia : "), $status_count['AS'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Brincadeira : "), $status_count['PRANK'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Chamada Caiu : "), $status_count['CC'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Concorrencia : "), $status_count['C'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Desligou : "), $status_count['DESLI'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Enviar Pilhas : "), $status_count['EPIL'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Enviar Pilhas Marcacao : "), $status_count['EPLM'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Enviar Amostra : "), $status_count['A'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Estabelecimento Comercial : "), $status_count['EC'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Exame Recente < 5 Meses : "), $status_count['ER'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Falecido : "), $status_count['F'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Futuras Accoes : "), $status_count['FA'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Idade < 45 anos : "), $status_count['IDD'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Informacao : "), $status_count['INFO'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Ja foi ao Consultorio : "), $status_count['JFC'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Ja tem marcacao : "), $status_count['S00046'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Não Interessado : "), $status_count['NI'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Não Reside/Mudou : "), $status_count['NRM'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Nunca mais ligar: "), $status_count['DNC'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Otorrino : "), $status_count['O'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Pessoa que pediu Amostra : "), $status_count['PPA'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Reclamacao : "), $status_count['R'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Referencia Duplicada : "), $status_count['RD'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Comprou recentemente : "), $status_count['TR'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Voltar a Contactar : "), $status_count['VOLC'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Outros : "), $status_count['OUTROS'], false, false, $i), ";");

//'FAX','NA','I', 'NAOEX', 'NNP', 'P', 'VM');

        fputcsv($output, array(" "), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "Total Dificuldades: "), $dificuldades), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  FAX : "), $status_count['FAX'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Não Atendeu : "), $status_count['NA'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Número Inválido : "), joinarray($status_count['NAOEX'] , $status_count['I']), false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Número não pertence : "), $status_count['NNP'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Portabilidade : "), $status_count['P'], false, false, $i), ";");
        fputcsv($output, columnmakerwithtotal(array(" ", "  Voicemail : "), $status_count['VM'], false, false, $i), ";");
       }

if (isset($resumo_geral_operador_camp)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "report_marc_operador_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];

    fputcsv($output, array(" "), ";");
    fputcsv($output, array(" ", "Report:", "Marcacoes de Operadores em Outbound"), ";");
    fputcsv($output, array(" ", "De:", $data_inicial), ";");
    fputcsv($output, array(" ", "A:", $data_final), ";");

    foreach ($_POST['camp_options'] as $key => $campanha) {
        fputcsv($output, array(" ", "", ""), ";");
        fputcsv($output, array(" ", "Campanha", $camp_name), ";");
        fputcsv($output, array(" ", "Operador", "N Total Marcacoes", "N Total Registos Trabalhados", "Taxa de Conversao"), ";");

        $qry_user = "select 
  a.user
from
    vicidial_log a inner join vicidial_users b on a.user = b.user 
where
    call_date between '$data_inicial 01:00:00' and '$data_final 23:00:00' and campaign_id = '$campanha' and b.user_group = 'Agentes' group by a.user
";

        $goUsers = mysql_query($qry_user, $link) or die(mysql_error());

        for ($i = 0; $i < mysql_num_rows($goUsers); $i++) {



            $curUser = mysql_fetch_row($goUsers);
            $curUser = $curUser[0];

            $qry_status = "select
     status, count(status), user from
(select * from    
(select * from
(select 
    status, lead_id, user
from
    vicidial_log
where
    call_date between '$data_inicial 01:00:00' and '$data_final 23:00:00' and campaign_id = '$campanha' order by call_date DESC
) b group by lead_id) c where user LIKE '$curUser') d group by status";
            $qry_status = mysql_query($qry_status, $link) or die(mysql_error());


            $query_marc = "select count(status) from
    (select 
        *
    from
        (select 
        user, status, campaign_id, lead_id
    from
        vicidial_log
    where
        campaign_id LIKE '$campanha' AND status IN ('MARC') and user like '$curUser'
        AND call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'    
    order by call_date DESC) a
    group by lead_id) b";
            $query_marc = mysql_query($query_marc, $link) or die(mysql_error());
            $query_marc = mysql_fetch_row($query_marc);
            $tot_marc = $query_marc[0];

            $array_negativos = array('OUTROS', 'AS', 'CC', 'C', 'DESLI', 'EPIL', 'EPLM', 'A', 'EC', 'FA', 'IF', 'INFO', 'NI', 'DNC', 'O', 'S00045', 'R', 'RD', 'TR');
            $negativos = 0;
            $agendamentos2 = 0;
            $brincadeira = 0;
            $exame_recente2 = 0;
            $falecido2 = 0;
            $idade2 = 0;
            $jconsultorio2 = 0;


            for ($a = 0; $a < mysql_num_rows($qry_status); $a++) {
                $row = mysql_fetch_row($qry_status);
                if (in_array($row[0], $array_negativos)) {
                    $negativos += $row[1];
                }
                if ($row[0] == 'CALLBK' || $row[0] == 'CBHOLD') {
                    $agendamentos2 += $row[1];
                }
                if ($row[0] == 'PRANK') {
                    $brincadeira = $row[1];
                }
                if ($row[0] == 'ER') {
                    $exame_recente2 = $row[1];
                }
                if ($row[0] == 'F') {
                    $falecido2 = $row[1];
                }
                if ($row[0] == 'IDD') {
                    $idade2 = $row[1];
                }
                if ($row[0] == 'JFC') {
                    $jconsultorio2 = $row[1];
                }
                if ($row[0] == 'S00046') {
                    $jtmarc = $row[1];
                }
                if ($row[0] == 'VOLC') {
                    $volc = $row[1];
                }
            }

            $contactos_uteis = $tot_marc + $agendamentos2 + $negativos;


            $conv_rate = round(($tot_marc / $contactos_uteis), 4) * 100;
            if ($tot_marc > 0) {
                fputcsv($output, array(" ", $curUser, $tot_marc, $contactos_uteis, $conv_rate . "%"), ";");
            }


//for($i=0;$i<mysql_num_rows($qry_status);$i++) {
//    $row = mysql_fetch_row($qry_status);
//    $firstval = $row[2] + 0;
//    $secval = $row[3] + 0;
//    
//    $conv_rate = round(($firstval/$secval),4)*100;
//    
//    fputcsv($output, array(" ",$row[0], $row[2], $row[3], $conv_rate."%"), ";");
//    
//    
//}
        }
    }
}
if (isset($report_drops_inb)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "drops_inb_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    // $query_log = "SELECT a.lead_id,a.campaign_id AS linhainbound,a.call_date AS data,a.status AS resultado,a.user as utilizador, b.*, c.*, d.campaign_name AS campanha FROM vicidial_closer_log a JOIN vicidial_agent_log b ON a.uniqueid = b.uniqueid JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_campaigns d ON b.campaign_id = d.campaign_id where a.status LIKE 'DROP' AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'";

    $output = fopen('php://output', 'w');
    fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'), ";");

    foreach ($camp_options as $currentCamp) {
        $query_log = "SELECT a.lead_id,a.campaign_id AS linhainbound,a.call_date AS data,a.status AS resultado,a.user as utilizador,c.* FROM vicidial_closer_log a JOIN vicidial_list c ON a.lead_id = c.lead_id where a.status LIKE 'DROP' AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND a.campaign_id LIKE '$currentCamp'";

        $query_log = mysql_query($query_log, $link) or die(mysql_error());

        for ($i = 0; $i < mysql_num_rows($query_log); $i++) {
            $row = mysql_fetch_assoc($query_log);


            fputcsv($output, array(
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                $row['extra3'],
                $row['extra10'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $custom_row['marchora'],
                $custom_row['marcdata'],
                $custom_row['tipoconsulta'],
                "",
                $custom_row['obs'],
                "",
                "",
                $row['utilizador'],
                $row['resultado'],
                $row['linhainbound'],
                $row['data']
                    ), ";");
        }
    }
}
if (isset($report_novas_leads)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "novas_leads_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");

    $output = fopen('php://output', 'w');
    fputcsv($output, array('Title',
        'Campaign No.',
        'First Name',
        'Middle Name',
        'Surname',
        'Address 1',
        'Address 2',
        'Address 3',
        'County',
        'Post Code',
        'Area Code',
        'No. Porta',
        'City',
        'Concelho',
        'Country Code',
        'Phone No.',
        'Mobile Phone No.',
        'Work Phone No.',
        'Date of Birth',
        'No.',
        'Update contact',
        'Service Request',
        'Territory Code',
        'Salesperson Code',
        'On Hold',
        'Exclude Reason Code',
        'Pensionner',
        'Want Info from other companies',
        'Appointment time',
        'Appointment date',
        'Visit Location',
        'Branch',
        'Comments',
        'Salesperson Team',
        'Tipo Cliente',
        'Operador',
        'Feedback',
        'Campanha',
        'Data da Chamada',
        'Avisos'), ";");
    foreach ($camp_options as $currentCamp) {
        $query_log = "SELECT a.lead_id,a.campaign_id AS linhainbound,a.call_date AS data,a.status AS resultado,a.user as utilizador, b.*, c.*, d.campaign_name AS campanha FROM vicidial_closer_log a JOIN vicidial_agent_log b ON a.uniqueid = b.uniqueid JOIN vicidial_list c ON a.lead_id = c.lead_id JOIN vicidial_campaigns d ON b.campaign_id = d.campaign_id where a.status IN ('NL') AND a.call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND a.campaign_id LIKE '$currentCamp'";
        $query_log = mysql_query($query_log, $link) or die(mysql_error());

        for ($i = 0; $i < mysql_num_rows($query_log); $i++) {
            $row = mysql_fetch_assoc($query_log);

            $custom_row = mysql_query("SELECT * FROM custom_" . $row['linhainbound'] . " where lead_id LIKE '$row[lead_id]' ");
            if (mysql_num_rows($custom_row) == 0) {
                continue;
            }
            $custom_row = mysql_fetch_assoc($custom_row) or die(mysql_error());

            $cod = "";
            if ($row['tipoconsulta'] == 'CATOS') {
                $cod = $row['consultorio'];
            } else {
                if ($row['tipoconsulta'] == 'Branch') {
                    $cod = $row['consultoriodois'];
                }
            }

            $campid = $row['extra1'];
            $no = $row['extra2'];
            $c_message = "Sem Campanha/Inbound/Chamada Manual";
            if ($row['tipoconsulta'] == null || $row['tipoconsulta'] == "" || $row['tipoconsulta'] == "semconsulta") {
                $c_message = "Lead Duplicada - Ignorar/Dados Incompletos";
            }
            if ((preg_match("/", $row['consultorio']) === 1)) {
                $c_message = "bom";
            }
            fputcsv($output, array(
                $row['title'],
                $campid,
                $row['first_name'],
                $row['middle_initial'],
                $row['last_name'],
                $row['address1'],
                $row['address2'],
                $row['address3'],
                $row['state'],
                $row['postal_code'],
                $row['extra3'],
                $row['extra10'],
                $row['city'],
                $row['province'],
                $row['country_code'],
                $row['phone_number'],
                $row['alt_phone'],
                "",
                $row['date_of_birth'],
                $no,
                "",
                "",
                "",
                $cod,
                "",
                "",
                "",
                "",
                $custom_row['marchora'],
                $custom_row['marcdata'],
                $custom_row['tipoconsulta'],
                "",
                $custom_row['obs'],
                "",
                "",
                $row['utilizador'],
                $row['resultado'],
                $row['linhainbound'],
                $row['data']
                    ), ";");
        }
    }
}
if (isset($resumo_geral_db)) {
    $curTime = date("Y-m-d H:i:s");
    $filename = "resumo_geral_bd_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];


    $i = 0;
    foreach ($_POST['db_options'] as $key => $db) {
        $i++;

        $query_marc = "select count(status) from
    (select 
        *
    from
        (select 
        user, status, campaign_id, lead_id
    from
        vicidial_log
    where
        list_id LIKE '$db' AND status IN ('MARC')
        AND call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'    
    order by call_date DESC) a
    group by lead_id) b";
        $query_marc = mysql_query($query_marc, $link) or die(mysql_error());
        $query_marc = mysql_fetch_row($query_marc);
        $tot_marc[$i] = $query_marc[0];


        $query_novocl = "select count(status) from
    (select 
        *
    from
        (select 
        user, status, campaign_id, lead_id
    from
        vicidial_log
    where
        list_id LIKE '$db' AND status IN ('NOVOCL')
        AND call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00'    
    order by call_date DESC) a
    group by lead_id) b";

        $query_novocl = mysql_query($query_novocl, $link) or die(mysql_error());
        $query_novocl = mysql_fetch_row($query_novocl);
        $tot_novocl[$i] = $query_novocl[0];



        $query_total_registos = "select count(lead_id) from vicidial_list where list_id LIKE '$db' and status <> 'NOVOCL'";
        $query_total_registos = mysql_query($query_total_registos, $link);
        $query_total_registos = mysql_fetch_row($query_total_registos);

        $total_registos[$i] = $query_total_registos[0];

        ##############################################################
        $query_n = "SELECT list_name from vicidial_lists where list_id='$db'";
        $query_n = mysql_query($query_n);
        $row_n = mysql_fetch_row($query_n);
        $bds[$i] = $row_n[0];
        fputcsv($output, array(" ", "nomes:", $bds[$i]), ";");
        ##############################################################
        ### Contagem do Total de Registos
        $total_leads[$i] = 0;
        $dificuldades[$i] = 0;
        $array_negativos = array('AS', 'PRANK', 'CC', 'C', 'DESLI', 'EPIL', 'EPLM', 'A', 'EC', 'ER', 'F', 'FA', 'IDD', 'IF', 'INFO', 'JFC', 'S00046', 'NI', 'NRM', 'DNC', 'O', 'PPA', 'S00045', 'R', 'RD', 'TR', 'VOLC', 'OUTROS');
        $array_dificuldades = array('FAX', 'NA','NAT', 'I', 'NAOEX', 'NNP', 'P', 'VM');



        $query_global = "select d.status, c.soma, d.status_name from (select status, count(status) as soma from (select lead_id, status from (select lead_id, status from vicidial_log where list_id LIKE '$db' and (user_group LIKE 'Agentes' or user like 'VDAD')
            and call_date BETWEEN '$data_inicial 01:00:00' AND '$data_final 23:00:00' order by call_date DESC) a group by lead_id) b group by status) c inner join (select 
            (status), status_name from vicidial_campaign_statuses group by status UNION ALL select status, status_name from vicidial_statuses group by status) d ON c.status = d.status group by status";
        //fputcsv($output, array(" ", "Query1:", $query_global), ";");
        $query_global = mysql_query($query_global) or die(mysql_error());

        while ($row = mysql_fetch_row($query_global)) {
            $tot_leads_2[$i] += $row[1];
            ##############################################################
            ### Contagem dos Agendamentos/Callbacks 'CBACK'

            $status_count[$row[0]][$i] = $row[1];

            if (in_array($row[0], $array_negativos)) {
                $negativos[$i]+= $row[1];
            }
            if (in_array($row[0], $array_dificuldades)) {
                $dificuldades[$i] += $row[1];
            }

            if ($row[0] == 'CALLBK' || $row[0] == 'CBHOLD') {
                $agendamentos2[$i] += $row[1];
            }
            if ($row[0] == 'NNP') {
                $nnp[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'PRANK') {
                $brincadeira[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'ER') {
                $exame_recente2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'F') {
                $falecido2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'IDD') {
                $idade2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'JFC') {
                $jconsultorio2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'S00046') {
                $jtmarc[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'VOLC') {
                $volc[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem das Marcacoes 'MARC'

            if ($row[0] == 'MARC') {
                $marcacoes2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem dos Novos Clientes 'NOVOCL'

            if ($row[0] == 'NOVOCL') {
                $novos_clientes2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Amostra 'A'

            if ($row[0] == 'A') {
                $amostra2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback NI 'NI'

            if ($row[0] == 'NI') {
                $ni2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Concorrencia 'C'
            if ($row[0] == 'C') {
                $conc2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Realizou Exame 'ER'
            ##############################################################
            ### Contagem do Feedback Otorrino 'O'

            if ($row[0] == 'O') {
                $otorrino2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Nao contactar 'DNC'

            if ($row[0] == 'DNC') {
                $nao_contactar2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Falecido 'F'
            ##############################################################
            ### Contagem do Feedback Futuras Acçoes 'FA'

            if ($row[0] == 'FA') {
                $futuras2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback idade < 45 anos 'IDD'
            ##############################################################
            ### Contagem do ja foi ao consultorio 'JFC'
            ##############################################################
            ### Contagem do Feedback outros 'OUTROS'

            if ($row[0] == 'OUTROS') {
                $outros2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback reclamacao 'R'

            if ($row[0] == 'R') {
                $reclamacao2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback Ja tem aparelho AM 'JA'

            if ($row[0] == 'JA') {
                $jatem2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback estabelecimento comercial 'EC'

            if ($row[0] == 'EC') {
                $comercial2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback disponivel apos 20:00 'D'

            if ($row[0] == 'D') {
                $disponivel2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback contactado recente 'CR'

            if ($row[0] == 'CR') {
                $contactado2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback nao reside 'RM'

            if ($row[0] == 'NRM' || $row[0] == 'RM') {
                $naoreside[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem do Feedback nao reside 'TINV'

            if ($row[0] == 'TINV') {
                $tlfinv[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem de numeros invalidos 'NAOEX'

            if ($row[0] == 'NAOEX' || $row[0] == 'I') {
                $inv[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            ##############################################################
            ### Contagem dos dificuldades REFAZER


            if ($row[0] == 'B') {
                $b2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'ERI') {
                $eri2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'FAX') {
                $fax2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'I') {
                $i2[$i] = $row[1];
                $total_leads[$i] += $row[1];
            }
            if ($row[0] == 'NA' || $row[0] == 'NAT') {
                $na2[$i] += $row[1];
                $total_leads[$i]+= $row[1];
            }
            /*if ($row[0] == 'NAT') {
                $nat2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }*/
            if ($row[0] == 'RD') {
                $rd2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
            if ($row[0] == 'VM') {
                $vm2[$i] = $row[1];
                $total_leads[$i]+= $row[1];
            }
        }




//$contactos_negativos = $contactado2+$disponivel2+$comercial2+$jatem2+$reclamacao2+$outros2+$jconsultorio2+$idade2+$futuras2+$falecido2+$nao_contactar2+$otorrino2+$exame_recente2+$conc2+$ni2+$amostra2+$naoreside;

        $contactos_efectuados[$i] = $tot_marc[$i] + $negativos[$i] + $agendamentos2[$i] + $nnp[$i];

        $contactos_uteis[$i] = $tot_marc[$i] + $agendamentos2[$i] + $negativos[$i] - ($brincadeira[$i] + $exame_recente2[$i] + $falecido2[$i] + $idade2[$i] + $jtmarc[$i] + $jconsultorio2[$i] + $volc[$i]);

        $marc_total[$i] = $marcacoes2[$i] + $novos_clientes2[$i];
        $tot_marc_novocl[$i] = $tot_marc[$i] + $tot_novocl[$i];
        $m_TR[$i] = number_format($tot_marc_novocl[$i] / $total_registos[$i] * 100, 2) . " %";
        $m_CE[$i] = number_format($tot_marc_novocl[$i] / $contactos_efectuados[$i] * 100, 2) . " %";
        $m_CU[$i] = number_format($tot_marc_novocl[$i] / $contactos_uteis[$i] * 100, 2) . " %";

        $na_total[$i] = $na2[$i] + $nat2[$i];
        $inv_total[$i] = $inv[$i] + $i2[$i];
        
        fputcsv($output, array(" "), ";");
          fputcsv($output, array(" ", "Resumo total do Query"), ";");
          fputcsv($output, array(" "), ";");
          mysql_data_seek($query_global, 0);
          
    while($row = mysql_fetch_row($query_global)){
          if ($row[0] != 'MARC') {
          fputcsv($output, array(" ", $row[2], $row[1], $row[0]), ";");
          } else {
          fputcsv($output, array(" ", $row[2], $tot_marc[$i], $row[0]), ";");
          }
          }
        fputcsv($output, array(" "), ";");
    }

    fputcsv($output, array(" "), ";");
    fputcsv($output, array(" "), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Base de Dados: "), $bds, false, true), ";");
    fputcsv($output, array(" "), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Total Registos: "), $total_registos, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Total Registos Trabalhados: "), $tot_leads_2, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Contactos Efectuados: "), $contactos_efectuados, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Contactos Uteis: "), $contactos_uteis, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Agendamentos: "), $agendamentos2, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Marcacoes: "), $tot_marc, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Novo Cliente: "), $tot_novocl, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Total Marcacoes: "), $tot_marc_novocl, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Contactos Negativos: "), $negativos, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Dificuldades: "), $dificuldades, true), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Marcacoes/Total Registos: "), $m_TR, false), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Marcacoes/Contactos Efectuados: "), $m_CE, false), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Marcacoes/Contactos Uteis: "), $m_CU, false), ";");
    fputcsv($output, array(" "), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Total Contactos Negativos: "), $negativos, true), ";");


//('AS','PRANK','CC','C','DESLI','EPIL','EPLM', 'A', 'EC','ER', 'F','FA','IDD', 'IF', 'INFO', 'JFC','S00046','NI','NRM','DNC','O','PPA','S00045','R','RD','TR','VOLC');

    fputcsv($output, columnmakerwithtotal(array(" ", " Assistencia : "), $status_count['AS'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Brincadeira : "), $status_count['PRANK'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Chamada Caiu : "), $status_count['CC'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Concorrencia : "), $status_count['C'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Desligou : "), $status_count['DESLI'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Enviar Pilhas : "), $status_count['EPIL'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Enviar Pilhas Marcacao : "), $status_count['EPLM'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Enviar Amostra : "), $status_count['A'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Estabelecimento Comercial : "), $status_count['EC'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Exame Recente < 5 Meses : "), $status_count['ER'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Falecido : "), $status_count['F'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Futuras Accoes : "), $status_count['FA'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Idade < 45 anos : "), $status_count['IDD'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Informacao : "), $status_count['INFO'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Ja foi ao Consultorio : "), $status_count['JFC'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Ja tem marcacao : "), $status_count['S00046'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Não Interessado : "), $status_count['NI'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Não Reside/Mudou : "), $status_count['NRM'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Nunca mais ligar: "), $status_count['DNC'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Otorrino : "), $status_count['O'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Pessoa que pediu Amostra : "), $status_count['PPA'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Reclamacao : "), $status_count['R'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Referencia Duplicada : "), $status_count['RD'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Comprou recentemente : "), $status_count['TR'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Voltar a Contactar : "), $status_count['VOLC'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Outros : "), $status_count['OUTROS'], true, false, $i), ";");

//'FAX','NA','I', 'NAOEX', 'NNP', 'P', 'VM');

    fputcsv($output, array(" "), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "Total Dificuldades: "), $dificuldades, true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  FAX : "), $status_count['FAX'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Não Atendeu : "), $na2, true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Número Inválido : "), joinarray($status_count['NAOEX'],$status_count['I']), true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Número não pertence : "), $status_count['NNP'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Portabilidade : "), $status_count['P'], true, false, $i), ";");
    fputcsv($output, columnmakerwithtotal(array(" ", "  Voicemail : "), $status_count['VM'], true, false, $i), ";");
    fputcsv($output, array(" "), ";");
    fputcsv($output, array(" "), ";");
}

if (isset($report_inbound_tvi)) {

    foreach ($camp_options as $currentCamp) {
        $did_in.="'$currentCamp',";
    }
    $did_in = rtrim($did_in, ",");

    $curTime = date("Y-m-d H:i:s");
    $filename = "resumo_geral_bd_" . $curTime;
    header("Content-Disposition: attachment; filename=" . $filename . ".csv");
    $output = fopen('php://output', 'w');

    fputcsv($output, array(" "), ";");

    // Total de Chamadas atendidas
    $query = "SELECT count(1) conta FROM vicidial_did_log a inner join vicidial_inbound_dids b ON a.did_id=b.did_id inner join vicidial_closer_log c on a.uniqueid=c.uniqueid where a.did_id in ($did_in) AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND status!='DROP';";
    $result = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_row($result);
    $chamadas_atendidas = $row[0];
    fputcsv($output, array("", "Chamadas atendidas:", "", $chamadas_atendidas), ";");
////////////////////////////////////////////////////////////////////////////////////////
    fputcsv($output, array(" "), ";");
    // Novas Leads por DID 
    fputcsv($output, array(" ", "Novas Leads por Linha"), ";");
    $query = "Select ifnull(conta,0) conta, did_description did from (SELECT count(status) conta, did_id FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid AND status = 'NL' AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' group by b.did_id , status order by a.call_date DESC) a right join vicidial_inbound_dids c ON a.did_id = c.did_id where c.did_id in ($did_in)";
    //$query="Select ifnull(conta,0) conta, if(LENGTH(extra1)>0,extra1,'Sem codigo') did from (SELECT count(a.status) conta, extra1,a.call_date FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid  inner join vicidial_list c on a.lead_id=c.lead_id WHERE a.status = 'NL' AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND did_id in ($did_in) group by c.extra1 , a.status) a order by a.call_date DESC ";
    $result = mysql_query($query) or die(mysql_error() . "2");
    $total_NL = 0;
    while ($row = mysql_fetch_assoc($result)) {
        fputcsv($output, array("", "", "$row[did]: ", $row["conta"]), ";");
        $total_NL+=$row["conta"];
    }
    fputcsv($output, array("", "Total: ", "", $total_NL), ";");
    fputcsv($output, array(" "), ";");


    // Marcações por DID 
    fputcsv($output, array(" ", "Marcações por Linha"), ";");
    $query = "Select ifnull(conta,0) conta, did_description did from (SELECT count(status) conta, did_id FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid AND status = 'MARC' AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' group by b.did_id , status order by a.call_date DESC) a right join vicidial_inbound_dids c ON a.did_id = c.did_id where c.did_id in ($did_in)";
    //$query="Select ifnull(conta,0) conta, if(LENGTH(extra1)>0,extra1,'Sem codigo') did from (SELECT count(a.status) conta, extra1,a.call_date FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid  inner join vicidial_list c on a.lead_id=c.lead_id WHERE a.status = 'MARC' AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND did_id in ($did_in) group by c.extra1 , a.status) a order by a.call_date DESC ";
    $result = mysql_query($query) or die(mysql_error() . "3");
    $total_MARC = 0;
    while ($row = mysql_fetch_assoc($result)) {
        fputcsv($output, array("", "", "$row[did]: ", $row["conta"]), ";");
        $total_MARC+=$row["conta"];
    }
    fputcsv($output, array("", "Total: ", "", $total_MARC), ";");
    fputcsv($output, array(" "), ";");
    fputcsv($output, array(" "), ";");
    
/////////////////////////////////////////////////////////////////////
    fputcsv($output, array(" "), ";");
    // Novas Leads por CAMP 
    fputcsv($output, array(" ", "Novas Leads por Campanha"), ";");
    //$query = "Select ifnull(conta,0) conta, did_description did from (SELECT count(status) conta, did_id FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid AND status = 'NL' AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' group by b.did_id , status order by a.call_date DESC) a right join vicidial_inbound_dids c ON a.did_id = c.did_id where c.did_id in ($did_in)";
    $query="Select ifnull(conta,0) conta, if(LENGTH(extra1)>0,extra1,'Sem codigo') did from (SELECT count(a.status) conta, extra1,a.call_date FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid  inner join vicidial_list c on a.lead_id=c.lead_id WHERE a.status = 'NL' AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND did_id in ($did_in) group by c.extra1 , a.status) a order by a.call_date DESC ";
    $result = mysql_query($query) or die(mysql_error() . "2");
    $total_NL = 0;
    while ($row = mysql_fetch_assoc($result)) {
        fputcsv($output, array("", "", "$row[did]: ", $row["conta"]), ";");
        $total_NL+=$row["conta"];
    }
    fputcsv($output, array("", "Total: ", "", $total_NL), ";");
    fputcsv($output, array(" "), ";");


    // Marcações por CAMP 
    fputcsv($output, array(" ", "Marcações por Campanha"), ";");
    //$query = "Select ifnull(conta,0) conta, did_description did from (SELECT count(status) conta, did_id FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid AND status = 'MARC' AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' group by b.did_id , status order by a.call_date DESC) a right join vicidial_inbound_dids c ON a.did_id = c.did_id where c.did_id in ($did_in)";
    $query="Select ifnull(conta,0) conta, if(LENGTH(extra1)>0,extra1,'Sem codigo') did from (SELECT count(a.status) conta, extra1,a.call_date FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid  inner join vicidial_list c on a.lead_id=c.lead_id WHERE a.status = 'MARC' AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND did_id in ($did_in) group by c.extra1 , a.status) a order by a.call_date DESC ";
    $result = mysql_query($query) or die(mysql_error() . "3");
    $total_MARC = 0;
    while ($row = mysql_fetch_assoc($result)) {
        fputcsv($output, array("", "", "$row[did]: ", $row["conta"]), ";");
        $total_MARC+=$row["conta"];
    }
    fputcsv($output, array("", "Total: ", "", $total_MARC), ";");
    fputcsv($output, array(" "), ";");
///////////////////////////////////////////////////////////////
    // FEED BACKS 
    fputcsv($output, array(" ", "Contactos negativos"), ";");
    $query = "Select ifnull(conta,0) conta, status_name estado From (SELECT count(a.status) conta, a.status FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid inner join vicidial_inbound_dids c ON b.did_id = c.did_id where b.did_id in ($did_in) AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND a.status not in ('MARC' , 'NL' , 'DROP') group by a.status) a right join ((select status, status_name From vicidial_campaign_statuses Where status not in ('MARC' , 'NL' , 'DROP') and `status` NOT REGEXP  '^FL000[0-9]|FL001[0-9]|FL0020$' group by status) Union ALL (select status, status_name from vicidial_statuses WHERE scheduled_callback='N')) c ON a.status = c.status order by estado ASC";

    $result = mysql_query($query) or die(mysql_error() . "4");
    $total_NG = 0;
    while ($row = mysql_fetch_assoc($result)) {
        fputcsv($output, array("", "", $row["estado"], $row["conta"]), ";");
        $total_NG+=$row["conta"];
    }
    fputcsv($output, array("", "Total: ", "", $total_NG), ";");
    fputcsv($output, array(" "), ";");

    // CallBacks 
    $query = "SELECT count(1) conta FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid=b.uniqueid  inner join vicidial_inbound_dids c ON b.did_id=c.did_id inner join vicidial_statuses d ON a.status=d.status where b.did_id in ($did_in) AND scheduled_callback='Y' AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' order by a.call_date DESC";
    $result = mysql_query($query) or die(mysql_error() . "5");
    $row = mysql_fetch_row($result);
    fputcsv($output, array("", "Agendamentos: ", "", $row[0]), ";");
    fputcsv($output, array(" "), ";");

    // Total de Chamadas perdidas
    $query = "SELECT count(1) conta FROM vicidial_did_log a inner join vicidial_inbound_dids b ON a.did_id=b.did_id inner join vicidial_closer_log c on a.uniqueid=c.uniqueid where a.did_id in ($did_in) AND a.call_date between '$data_inicial 01:00:00' AND '$data_final 23:00:00' AND status='DROP';";
    $result = mysql_query($query) or die(mysql_error() . "6");
    $row = mysql_fetch_row($result);
    $chamadas_perdidas = $row[0];
    fputcsv($output, array("", "Chamadas Perdidas: ", "", $chamadas_perdidas), ";");
    fputcsv($output, array(" "), ";");

    fputcsv($output, array("", "Total chamadas: ", "", $chamadas_perdidas + $chamadas_atendidas), ";");
    fputcsv($output, array(" "), ";");

    fputcsv($output, array("", "Tx registos positivos: ", "", number_format((float) ((($total_NL + $total_MARC) / $chamadas_atendidas) * 100), 2, '.', '') . "%"), ";");
    fputcsv($output, array(" "), ";");

    fputcsv($output, array("", "Tx Registos não válidos: ", "", number_format((float) (($total_NG / $chamadas_atendidas) * 100), 2, '.', '') . "%"), ";");
    fputcsv($output, array(" "), ";");

    fputcsv($output, array("", "% Chamadas Perdidas: ", "", number_format((float) (($chamadas_perdidas / ($chamadas_perdidas + $chamadas_atendidas)) * 100), 2, '.', '') . "%"), ";");
    fputcsv($output, array(" "), ";");

    //Tempo médio em chamada (min)
    $query = "SELECT avg(a.length_in_sec/60) media FROM vicidial_closer_log a inner join vicidial_did_log b ON a.uniqueid = b.uniqueid AND status = 'NL' AND a.call_date between '2013-04-15 01:00:00' AND '2013-04-17 23:00:00' AND b.did_id in ($did_in) order by a.call_date DESC ";
    $result = mysql_query($query) or die(mysql_error() . "6");
    $row = mysql_fetch_row($result);
    $tempo_medio = $row[0];
    fputcsv($output, array("", "Tempo médio chamada (minuto): ", "", number_format((float) $tempo_medio, 2, '.', '')), ";");
    fputcsv($output, array(" "), ";");
}
?>