<?php

require("../../ini/dbconnect.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


if ($action == "lostcalls") {

    $today = date("o-m-d");

    $aColumns = array('first_name', 'phone_number', 'call_date_pt', 'term_reason', 'marcar', 'contactado');
    $sQuery = "
			SELECT	B.first_name, A.phone_number, A.call_date AS call_date_pt, A.term_reason, A.campaign_id, B.last_name, A.lead_id, A.call_date
			FROM vicidial_closer_log A INNER JOIN vicidial_list B ON A.lead_id = B.lead_id
			WHERE A.agent_only='$sent_user_id'
			AND A.call_date > '$today' 
			ORDER BY A.call_date DESC
			";
    //echo $sQuery;
    $rResult = mysql_query($sQuery, $link) or die(mysql_error());
    $output = array("aaData" => array());

    while ($aRow = mysql_fetch_array($rResult)) {

        if ($aRow['term_reason'] == 'AFTERHOURS' || $aRow['term_reason'] == 'QUEUETIMEOUT' || $aRow['term_reason'] == 'ABANDON' || $aRow['term_reason'] == 'NOAGENT') {


            $query = "SELECT count(*) FROM user_call_log WHERE lead_id ='$aRow[lead_id]' AND call_date > '$aRow[call_date]'";
            $query = mysql_query($query, $link) or die(mysql_error());

            $row1 = mysql_fetch_row($query);
            $result = $row1[0];

            if ($result) {
                $return_call_flag = 1;
            } else {
                $return_flag_flag = 0;
            }

            // EIDTING

            $row = array();
            for ($i = 0; $i < count($aColumns); $i++) {
                switch ($aColumns[$i]) {
                    case "term_reason": {
                            if ($aRow['campaign_id'] == 'CCLojas') {
                                $xfer_flag = '(SEDE) ';
                            } else {
                                $xfer_flag = '';
                            }
                            switch ($aRow['term_reason']) {
                                case "AFTERHOURS": $row[] = $xfer_flag . "Chamada Fora de Horas";
                                    break;
                                case "QUEUETIMEOUT": $row[] = $xfer_flag . "Tempo Máximo em Espera Atingido";
                                    break;
                                case "ABANDON": $row[] = $xfer_flag . "Cliente Disistiu da Chamada";
                                    break;
                                case "NOAGENT": $row[] = $xfer_flag . "Sem Operador Disponivel";
                                    break;
                            }
                            break;
                        }
                    case "marcar": {
                        $row[] = "<button class=\"btn btn-mini icon-alone LostCallDial\" data-phone=\"$aRow[phone_number]\" ><i class=\"icon-phone\"></i></button>";
                            break;
                        }
                    case "first_name": {
                            if ($aRow['first_name'] == "") {
                                $row[] = "<i>Sem Nome</i>";
                            } else {
                                $row[] = $aRow['first_name'] . " " . $aRow['last_name'];
                            } break;
                        }
                    case "contactado" : {
                            if ($return_call_flag) {
                                $row[] = "<b style='color:green'>Sim (" . $result . ")</b>";
                            } else {
                                $row[] = "<b style='color:red'>Não<b>";
                            }
                        }
                    case "call_date_pt" : {
                            $row[] = "<time datetime=" . date("c", strtotime($aRow[$aColumns[$i]])) . " ></time>";
                            break;
                        }
                    default: $row[] = $aRow[$aColumns[$i]];
                        break;
                }
            }

            $output['aaData'][] = $row;
        }
    }
    //print_r($output);
    echo json_encode($output);
}


if ($action == "manual") {

    $today = date("o-m-d");

    $aColumns = array('first_name', 'phone_number', 'call_date_pt', 'term_reason', 'marcar', 'contactado');
    $sQuery = "
			SELECT	B.first_name, A.phone_number, A.call_date AS call_date_pt, A.lead_id, B.last_name
			FROM user_call_log A INNER JOIN vicidial_list B ON A.lead_id = B.lead_id
			WHERE A.user='$sent_user_id'
			AND A.call_date > '$today' group by call_date
			ORDER BY A.call_date DESC
			";
    //echo $sQuery;
    $rResult = mysql_query($sQuery, $link) or die(mysql_error());
    $output = array("aaData" => array());

    while ($aRow = mysql_fetch_array($rResult)) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {

            switch ($aColumns[$i]) {
                case "first_name": {
                        if ($aRow['first_name'] == "") {
                            $row[] = "<i>Sem Nome</i>";
                        } else {
                            $row[] = $aRow['first_name'] . " " . $aRow['last_name'];
                        } break;
                    }
                case "marcar": {
                        $row[] = "<button class=\"btn btn-mini icon-alone LostCallDial\" data-phone=\"$aRow[phone_number]\" ><i class=\"icon-phone\"></i></button>";
                        break;
                    }
                case "term_reason" : {
                        $row[] = "<i>n/a</i>";
                        break;
                    }
                case "contactado" : {
                        $row[] = "<i>n/a</i>";
                        break;
                    }
                case "call_date_pt" : {
                        $row[] = "<time datetime=" . date("c", strtotime($aRow[$aColumns[$i]])) . " ></time>";
                        break;
                    }
                default: $row[] = $aRow[$aColumns[$i]];
            }
        }
        $output['aaData'][] = $row;
    }
    //print_r($output);
    echo json_encode($output);
}



if ($action == "outbound") {

    $today = date("o-m-d");

    $aColumns = array('first_name', 'phone_number', 'call_date_pt', 'term_reason', 'marcar', 'contactado');
    $sQuery = "
			SELECT	B.first_name, A.phone_number,A.call_date AS call_date_pt, A.lead_id, B.last_name
			FROM vicidial_log A INNER JOIN vicidial_list B ON A.lead_id = B.lead_id
			WHERE A.user='$sent_user_id'
			AND A.call_date > '$today' 
			AND A.comments != 'MANUAL'
			ORDER BY A.call_date DESC
			";
    //echo $sQuery;
    $rResult = mysql_query($sQuery, $link) or die(mysql_error());
    $output = array("aaData" => array());

    while ($aRow = mysql_fetch_array($rResult)) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            switch ($aColumns[$i]) {
                case "first_name": {
                        if ($aRow['first_name'] == "") {
                            $row[] = "<i>Sem Nome</i>";
                        } else {
                            $row[] = $aRow['first_name'] . " " . $aRow['last_name'];
                        } break;
                    }
                case "marcar": {
                        $row[] = "<button class=\"btn btn-mini icon-alone LostCallDial\" data-phone=\"$aRow[phone_number]\" ><i class=\"icon-phone\"></i></button>";
                        break;
                    }
                case "term_reason" : {
                        $row[] = "<i>n/a</i>";
                        break;
                    }
                case "contactado" : {
                        $row[] = "<i>n/a</i>";
                        break;
                    }
                case "call_date_pt" : {
                        $row[] = "<time datetime=" . date("c", strtotime($aRow[$aColumns[$i]])) . " ></time>";
                        break;
                    }
                default: $row[] = $aRow[$aColumns[$i]];
            }
        }
        $output['aaData'][] = $row;
    }
    //print_r($output);
    echo json_encode($output);
}

if ($action == "inbound") {

    $today = date("o-m-d");

    $aColumns = array('first_name', 'phone_number', 'call_date_pt', 'term_reason', 'marcar', 'contactado');
    $sQuery = "
			SELECT	B.first_name, A.phone_number, A.call_date AS call_date_pt, A.lead_id, B.last_name
			FROM vicidial_closer_log A INNER JOIN vicidial_list B ON A.lead_id = B.lead_id
			WHERE A.agent_only='$sent_user_id'
			AND A.call_date > '$today' 
			ORDER BY A.call_date DESC
			";
    //echo $sQuery;
    $rResult = mysql_query($sQuery, $link) or die(mysql_error());
    $output = array("aaData" => array());

    while ($aRow = mysql_fetch_array($rResult)) {
        $row = array();
        for ($i = 0; $i < count($aColumns); $i++) {
            switch ($aColumns[$i]) {
                case "first_name": {
                        if ($aRow['first_name'] == "") {
                            $row[] = "<i>Sem Nome</i>";
                        } else {
                            $row[] = $aRow['first_name'] . " " . $aRow['last_name'];
                        } break;
                    }
                case "marcar": {
                        $row[] = "<button class=\"btn btn-mini icon-alone LostCallDial\" data-phone=\"$aRow[phone_number]\" ><i class=\"icon-phone\"></i></button>";
                        break;
                    }
                case "term_reason" : {
                        $row[] = "<i>n/a</i>";
                        break;
                    }
                case "contactado" : {
                        $row[] = "<i>n/a</i>";
                        break;
                    }
                case "call_date_pt" : {
                        $row[] = "<time datetime='" . date("c", strtotime($aRow[$aColumns[$i]])) . "' ></time>";
                        break;
                    }
                default: $row[] = $aRow[$aColumns[$i]];
            }
        }
        $output['aaData'][] = $row;
    }
    //print_r($output);
    echo json_encode($output);
}
?>
