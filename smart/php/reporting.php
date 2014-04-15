<?php

ini_set(display_errors, 1);

require '../../ini/db.php';

$host = "mysql:host=".$VARDB_server .";dbname=" . $VARDB_database . ";charset=utf8";
$varDbUser="sipsadmin";
$varDbPass="sipsps2012";
try {
    $db = new PDO($host, $varDbUser, $varDbPass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

$server = "http://$VARDB_server:10000/ccstats/v0/";

switch ($_POST['action']) {
    case 'recycling': getRecycling($db, $_POST['id']);
        break;
    case 'getStatus': getStatus($db, $_POST['id']);
        break;
    case 'new': novo($db, $_POST['id']);
        break;
    case 'total': total($db, $_POST['id']);
        break;
    case 'close':close($db, $_POST['id']);
        break;
    case 'called':called($db, $_POST['id']);
        break;
    case 'agents':agents($db);
        break;
    case 'allCampaigns':allCampaigns($db);
        break;
    case 'db': db($db);
        break;
    case 'pausas':pausas($db, $_POST['id']);
        break;
    case 'getInbound': getInbound($db);
        break;
    case 'inboundLines':inboundLines($db);
        break;
    case 'inboundAgents': inboundAgents($db);
        break;
    case 'liveAgents':liveAgents($db);
        break;
    case 'pausas_agents': pausas_agents($db);
        break;
    case 'human': human($db, $_POST['id']);
        break;
    case 'total_human': total_human($db);
        break;
    case 'db_new': db_new($db, $_POST['id']);
        break;
    case 'db_called': db_called($db, $_POST['id'], $_POST['cID']);
        break;
    case 'db_close': db_close($db, $_POST['id'], $_POST['cID']);
        break;
    case 'db_recy': db_recy($db, $_POST['id'], $_POST['cID']);
        break;
    case 'db_total':db_total($db, $_POST['id']);
        break;
    case 'db_graph':db_graph($db);
        break;
    case 'active_db':active_db($db);
        break;
    case 'pausas_total':pausas_total($db);
        break;
    case 'getDID':getDID($db);
        break;
    case 'getCampaignStatus':getCampaignStatus($db);
        break;
    case 'getAgentsCampaign':getAgentsCampaign($db, $_POST['id'], $_POST['start'], $_POST['end'],$server);
        break;
    case 'timeline':timeline($db, $_POST['dados'], $_POST['id'], $_POST['start'], $_POST['end']);
        break;
    case 'getCampaignValue': getCampaignValue($db, $_POST['start'], $_POST['end']);
        break;
    case 'hour':hour($db, $_POST['dados'], $_POST['id'], $_POST['start'], $_POST['end']);
        break;
    case 'databaseCallback': databaseCallback($db, $_POST['id']);
        break;
}

function databaseCallback($db, $id){
    $callback = array();
    $date = array();
    //$status = array();
    //max=array();
    
    $stmt= $db->prepare("SELECT GROUP_CONCAT(`status`) as x,`attempt_maximum` as max,campaign_id FROM `vicidial_lead_recycle` WHERE campaign_id=:id and active='Y'");
    $stmt->execute(array('id'=>$id));
    While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
     $status=$data->x; 
     $max=$data->max;
    }
    
    $stmt= $db->prepare("select entry_date, list_id from vicidial_list group by list_id order by entry_date asc");
    $stmt->execute();
    While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
     $date[$data->list_id]=$data->entry_date;   
    }
    
    $stmt= $db->prepare("select count(lead_id)as count, list_id as id from vicidial_callbacks where status in ('ACTIVE', 'LIVE') group by list_id");
    $stmt->execute();
    While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
     $callback[$data->id]=$data->count;   
    }
    
    
    echo json_encode(array('Callback' => $callback, 'Date'=>$date, 'Status'=>$status, 'Max'=>$max));
}

function getCampaignValue($db, $start, $end) {
    $campaign = array();
    $get_total = file_get_contents($server."total/calls/$start/$end?by=campaign");
    $total_content = json_decode($get_total);

    foreach ($total_content as $value) {
        $campaign[$value->campaign]['calls'] = $value->calls;
        $campaign[$value->campaign]['length'] = $value->length;

        $sql = "select * FROM `vicidial_campaign_statuses` Where campaign_id ='" . $value->campaign . "' and selectable ='y'";
        $stmt = $db->prepare($sql);
        $stmt->execute();

        $human = array();
        $util = array();
        $sucesso = array();

        While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
            if ($data->human_answered === 'Y') {
                $human[] = $data->status;
            }
            if ($data->sale === 'Y') { //sucesso
                $sucesso[] = $data->status;
            }
            if ($data->customer_contact === 'Y') { //util
                $util[] = $data->status;
            }
        }

       
        $get_hour = file_get_contents($server."total/agent_log/$start/$end?by=$value->campaign");
        $horas = json_decode($get_hour);
        
        foreach($horas as $valu){
            $campaign[$value->campaign]['horas']= $valu->sum_talk + $valu->sum_pause + $valu->sum_dead + $valu->sum_dispo + $valu->sum_wait + $valu->sum_billable_pause;
        }

        if (count($human) > 0) {
            $get_total1 = file_get_contents($server."total/calls/$start/$end?campaign=$value->campaign&status=" . implode(',', $human));
            $total_content1 = json_decode($get_total1);
            foreach ($total_content1 as $value1) {
                $campaign[$value->campaign]['human'] = $value1->calls;
            }
        }

        if (count($util) > 0) {
            $get_total2 = file_get_contents($server."total/calls/$start/$end?campaign=$value->campaign&status=" . implode(',', $util));
            $total_content2 = json_decode($get_total2);
            foreach ($total_content2 as $value2) {
                $campaign[$value->campaign]['util'] = $value2->calls;
            }
        }

        if (count($sucesso) > 0) {
            $get_total3 = file_get_contents($server."total/calls/$start/$end?campaign=$value->campaign&status=" . implode(',', $sucesso));
            $total_content3 = json_decode($get_total3);
            foreach ($total_content3 as $value3) {
                $campaign[$value->campaign]['sucesso'] = $value3->calls;
            }
        }
    }

    echo json_encode(array('Campaign' => $campaign));
}

function hour($db, $ar, $id, $start, $end) {
    $total = array();
    $talk = array();
    $drop = array();
    $util = array();
    $sucesso = array();
    $callback = array();
    $complete = array();
    $nutil = array();
    $unwork = array();


    $sql = "select * FROM `vicidial_campaign_statuses` Where campaign_id ='" . $id . "' and selectable ='y'";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $Hum = array();
    $Suc = array();
    $DNC = array();
    $utils = array();
    $NUtils = array();
    $Unworkable = array();
    $Callbacks = array();
    $Completes = array();

    While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
        if ($data->human_answered === 'Y') {
            $Hum[] = $data->status;
        }
        if ($data->sale === 'Y') { //sucesso
            $Suc[] = $data->status;
        }
        if ($data->dnc === 'Y') { // DNC
            $DNC[] = $data->status;
        }
        if ($data->customer_contact === 'Y') { //util
            $utils[] = $data->status;
        }
        if ($data->not_interested === 'Y') { // Nao util
            $NUtils[] = $data->status;
        }
        if ($data->unworkable === 'Y') { // Unworkable
            $Unworkable[] = $data->status;
        }
        if ($data->scheduled_callback === 'Y') { //Callback
            $Callbacks[] = $data->status;
        }
        if ($data->completed === 'Y') { //Complete
            $Completes[] = $data->status;
        }
    }



    if (in_array("total", $ar)) {
        $get_total = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id");
        $total_content = json_decode($get_total);

        foreach ($total_content as $value) {
            $total[] = array($value->hour, round($value->length/60));
        }
    }
    if (in_array("talk", $ar)) {
        $get_total1 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Hum));
        $total_content1 = json_decode($get_total1);

        foreach ($total_content1 as $value) {
            $talk[] = array($value->hour, round($value->length/60));
        }
    }
    if (in_array("drop", $ar)) {
        $get_total2 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=DROP");
        $total_content2 = json_decode($get_total2);

        foreach ($total_content2 as $value) {
            $drop[] = array($value->hour, round($value->length/60));
        }
    }
    if (in_array("util", $ar)) {
        $get_total3 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $utils));
        $total_content3 = json_decode($get_total3);

        foreach ($total_content3 as $value) {
            $util[] = array($value->hour, round($value->length/60));
        }
    }
    if (in_array("sucesso", $ar)) {
        $get_total4 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Suc));
        $total_content4 = json_decode($get_total4);

        foreach ($total_content4 as $value) {
            $sucesso[] = array($value->hour, round($value->length/60));
        }
    }
    if (in_array("callback", $ar)) {
        $get_total5 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Callbacks));
        $total_content5 = json_decode($get_total5);

        foreach ($total_content5 as $value) {
            $callback[] = array($value->hour, round($value->length/60));
        }
    }
    if (in_array("complete", $ar)) {
        $get_total6 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Completes));
        $total_content6 = json_decode($get_total6);

        foreach ($total_content6 as $value) {
            $complete[] = array($value->hour, round($value->length/60));
        }
    }
    if (in_array("nutil", $ar)) {
        $get_total7 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $NUtils));
        $total_content7 = json_decode($get_total7);

        foreach ($total_content7 as $value) {
            $nutil[] = array($value->hour, round($value->length/60));
        }
    }
    if (in_array("unwork", $ar)) {
        $get_total8 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Unworkable));
        $total_content8 = json_decode($get_total8);

        foreach ($total_content8 as $value) {
            $unwork[] = array($value->hour, round($value->length/60));
        }
    }

    echo json_encode(array('Total' => $total, 'Talk' => $talk, 'Util' => $util, 'Sucesso' => $sucesso, 'Callback' => $callback, 'Complete' => $complete, 'NUtil' => $nutil, 'Unwork' => $unwork, 'Drop' => $drop));
}

function timeline($db, $ar, $id, $start, $end) {
    $total = array();
    $talk = array();
    $drop = array();
    $util = array();
    $sucesso = array();
    $callback = array();
    $complete = array();
    $nutil = array();
    $unwork = array();


    $sql = "select * FROM `vicidial_campaign_statuses` Where campaign_id ='" . $id . "' and selectable ='y'";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $Hum = array();
    $Suc = array();
    $DNC = array();
    $utils = array();
    $NUtils = array();
    $Unworkable = array();
    $Callbacks = array();
    $Completes = array();

    While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
        if ($data->human_answered === 'Y') {
            $Hum[] = $data->status;
        }
        if ($data->sale === 'Y') { //sucesso
            $Suc[] = $data->status;
        }
        if ($data->dnc === 'Y') { // DNC
            $DNC[] = $data->status;
        }
        if ($data->customer_contact === 'Y') { //util
            $utils[] = $data->status;
        }
        if ($data->not_interested === 'Y') { // Nao util
            $NUtils[] = $data->status;
        }
        if ($data->unworkable === 'Y') { // Unworkable
            $Unworkable[] = $data->status;
        }
        if ($data->scheduled_callback === 'Y') { //Callback
            $Callbacks[] = $data->status;
        }
        if ($data->completed === 'Y') { //Complete
            $Completes[] = $data->status;
        }
    }



    if (in_array("total", $ar)) {
        $get_total = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id");
        $total_content = json_decode($get_total);

        foreach ($total_content as $value) {
            $total[] = array($value->hour, $value->calls);
        }
    }
    if (in_array("talk", $ar)) {
        $get_total1 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Hum));
        $total_content1 = json_decode($get_total1);

        foreach ($total_content1 as $value) {
            $talk[] = array($value->hour, $value->calls);
        }
    }
    if (in_array("drop", $ar)) {
        $get_total2 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=DROP");
        $total_content2 = json_decode($get_total2);

        foreach ($total_content2 as $value) {
            $drop[] = array($value->hour, $value->calls);
        }
    }
    if (in_array("util", $ar)) {
        $get_total3 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $utils));
        $total_content3 = json_decode($get_total3);

        foreach ($total_content3 as $value) {
            $util[] = array($value->hour, $value->calls);
        }
    }
    if (in_array("sucesso", $ar)) {
        $get_total4 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Suc));
        $total_content4 = json_decode($get_total4);

        foreach ($total_content4 as $value) {
            $sucesso[] = array($value->hour, $value->calls);
        }
    }
    if (in_array("callback", $ar)) {
        $get_total5 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Callbacks));
        $total_content5 = json_decode($get_total5);

        foreach ($total_content5 as $value) {
            $callback[] = array($value->hour, $value->calls);
        }
    }
    if (in_array("complete", $ar)) {
        $get_total6 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Completes));
        $total_content6 = json_decode($get_total6);

        foreach ($total_content6 as $value) {
            $complete[] = array($value->hour, $value->calls);
        }
    }
    if (in_array("nutil", $ar)) {
        $get_total7 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $NUtils));
        $total_content7 = json_decode($get_total7);

        foreach ($total_content7 as $value) {
            $nutil[] = array($value->hour, $value->calls);
        }
    }
    if (in_array("unwork", $ar)) {
        $get_total8 = file_get_contents($server."total/calls/$start/$end?by=hour&campaign=$id&status=" . implode(',', $Unworkable));
        $total_content8 = json_decode($get_total8);

        foreach ($total_content8 as $value) {
            $unwork[] = array($value->hour, $value->calls);
        }
    }

    echo json_encode(array('Total' => $total, 'Talk' => $talk, 'Util' => $util, 'Sucesso' => $sucesso, 'Callback' => $callback, 'Complete' => $complete, 'NUtil' => $nutil, 'Unwork' => $unwork, 'Drop' => $drop));
}

function getAgentsCampaign($db, $id, $start, $end,$server) {
    $sql = "select * FROM `vicidial_campaign_statuses` Where campaign_id ='" . $id . "' and selectable ='y'";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $sucesso = array();
    $util = array();
    $hu = array();

    $hours = array();
    $calls = array();
    $time = array();
    $sucess = array();
    $positive = array();
    $human = array();

    while ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
        if ($data->sale === 'Y') {
            $sucesso[] = $data->status;
        }
        if ($data->customer_contact === 'Y') {
            $util[] = $data->status;
        }
        if ($data->human_answered === 'Y') {
            $hu[] = $data->status;
        }
    }
   
    $get_hours = file_get_contents($server."total/agent_log/$start/$end?by=agent&campaign=$id");
    $hours_content = json_decode($get_hours);
   
    $get_calls = file_get_contents($server."total/calls/$start/$end?by=agent&campaign=$id");
    $calls_content = json_decode($get_calls);

    if (count($sucesso)) {
        $get_sucesso = file_get_contents($server."total/calls/$start/$end?by=agent&campaign=$id&status=" . implode(',', $sucesso));
        $sucesso_content = json_decode($get_sucesso);
        foreach ($sucesso_content as $value) {
            $sucess[$value->agent] = $value->calls;
        }
    }

    if (count($util)) {
        $get_util = file_get_contents($server."total/calls/$start/$end?by=agent&campaign=$id&status=" . implode(',', $util));
        $util_content = json_decode($get_util);
        foreach ($util_content as $value) {
            $positive[$value->agent] = $value->calls;
        }
    }

    if (count($hu)) {
        $get_human = file_get_contents($server."total/calls/$start/$end?by=agent&campaign=$id&status=" . implode(',', $hu));
        $hu_content = json_decode($get_human);
        foreach ($hu_content as $value) {
            $human[$value->agent] = $value->calls;
        }
    }

    foreach ($hours_content as $value) {
        $hours[$value->agent] = $value->sum_talk + $value->sum_pause + $value->sum_dead + $value->sum_dispo + $value->sum_wait + $value->sum_billable_pause;
    }

    foreach ($calls_content as $value) {
        $calls[$value->agent] = $value->calls;
        $time[$value->agent] = $value->length;
    }

    echo json_encode(array('Calls' => $calls, 'Hour' => $hours, 'Time' => $time, 'Sucesso' => $sucess, 'Util' => $positive, 'Human' => $human));
}

function getDID($db) {
    $sql = 'SELECT count(*) as count FROM `vicidial_inbound_dids`';
    $query = $db->prepare($sql);
    $query->execute();
    $total = $query->fetch(PDO::FETCH_OBJ);

    $sql_active = "SELECT count(*) as count FROM `vicidial_inbound_dids` where did_active = 'Y'";
    $query_active = $db->prepare($sql_active);
    $query_active->execute();
    $active = $query_active->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('active' => $active, 'total' => $total));
}

function active_db($db) {
    $sql = "SELECT count(*) as count FROM `vicidial_lists` where active='y'";
    $query = $db->prepare($sql);
    $query->execute();
    $active = $query->fetch(PDO::FETCH_OBJ);

    $sql_total = "SELECT count(*) as count FROM `vicidial_lists`";
    $query_total = $db->prepare($sql_total);
    $query_total->execute();
    $total = $query_total->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('active' => $active, 'total' => $total));
}

function db_graph($db) {
    $sql_new = 'select count(lead_id) as count from vicidial_list where status ="NEW"';
    $query_new = $db->prepare($sql_new);
    $query_new->execute();
    $new = $query_new->fetch(PDO::FETCH_OBJ);

    $sql_contact = 'select count(lead_id) as count from vicidial_list';
    $query_contact = $db->prepare($sql_contact);
    $query_contact->execute();
    $contact = $query_contact->fetch(PDO::FETCH_OBJ);

    $sql_more = 'SELECT count(lead_id) as count FROM `vicidial_list` where status<>"NEW" AND called_since_last_reset <>"NEW" AND called_since_last_reset <> "Y"';
    $query_more = $db->prepare($sql_more);
    $query_more->execute();
    $more = $query_more->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('new' => $new, 'all' => $contact, 'more' => $more));
}

function db_total($db, $database) {
    $sql = 'select count(lead_id) as count from vicidial_list where list_id="' . $database . '"';
    $query = $db->prepare($sql);
    $query->execute();
    $total = $query->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('total' => $total));
}

function db_recy($db, $database, $campaign) {
    $status_query = $db->prepare("SELECT `status` FROM `vicidial_lead_recycle` WHERE `campaign_id`=:campaign AND active='Y'");
    $status_query->execute(array(":campaign" => $campaign));
    $status = $status_query->fetchAll(PDO::FETCH_COLUMN, 0);

    $allStatus = implode("','", $status);

    $limite_query = $db->prepare("SELECT `status`,`attempt_maximum` FROM `vicidial_lead_recycle` WHERE `campaign_id`=:campaign AND active='Y' ");
    $limite_query->execute(array(":campaign" => $campaign));
    $limite = $limite_query->fetchAll(PDO::FETCH_OBJ);


    $if1 = " IF( status = ";
    $if2 = " , IF( (SUBSTR(called_since_last_reset, 2) <";
    $andsub = " AND SUBSTR(called_since_last_reset, 2) > 0), 1, 0), 0) = 1 ";
    $phrase = array();
    for ($i = 0; $i < count($limite); ++$i) {
        $ph = $if1 . " '" . $limite[$i]->status . "'" . $if2 . $limite[$i]->attempt_maximum . $andsub;
        array_push($phrase, $ph);
    }

    $final = implode("  OR ", $phrase);
    $sql = "select status, count(lead_id) as count from vicidial_list where status in('" . $allStatus . " ') and ( " . $final . " ) AND list_id = '" . $database . "' group by status";
    $data_query = $db->prepare($sql);
    $data_query->execute();
    $data = $data_query->fetchAll(PDO::FETCH_OBJ);

    echo json_encode(Array('result' => $data));
}

function db_close($db, $database, $campaign) {
    $sql = 'select count(lead_id) as count from vicidial_list where list_id ="' . $database . '" and status in (select distinct(status) from vicidial_lead_recycle where campaign_id like "' . $campaign . '" and active = "Y") and called_since_last_reset NOT IN ("N", "Y")';
    $query = $db->prepare($sql);
    $query->execute();
    $close = $query->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('close' => $close));
}

function db_called($db, $database, $campaign) {
    $sql = 'select count(lead_id) as count from vicidial_list where list_id ="' . $database . '" and status in (select status from vicidial_lead_recycle where campaign_id like "' . $campaign . '" and active = "Y" group by status) and called_since_last_reset IN ("N", "Y")';
    $query = $db->prepare($sql);
    $query->execute();
    $called = $query->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('called' => $called));
}

function db_new($db, $database) {
    $sql_new = 'select count(lead_id) as count from vicidial_list where list_id="' . $database . '" and status = "NEW"';
    $query_new = $db->prepare($sql_new);
    $query_new->execute();
    $new = $query_new->fetch(PDO::FETCH_OBJ);
    echo json_encode(Array('new' => $new));
}

function total_human($db) {
    $sql_sys = 'SELECT status FROM `vicidial_statuses` where human_answered = "Y" ';
    $query_sys = $db->prepare($sql_sys);
    $query_sys->execute();
    $sys = array();
    while ($row = $query_sys->fetch(PDO::FETCH_NUM)) {
        $sys[] = $row[0];
    }

    $sql_campaign = 'SELECT status FROM `vicidial_campaign_statuses` where human_answered = "y" ';
    $query_campaign = $db->prepare($sql_campaign);
    $query_campaign->execute();
    $campaign = array();
    while ($row = $query_campaign->fetch(PDO::FETCH_NUM)) {
        $campaign[] = $row[0];
    }
    echo json_encode(Array('human' => $sys + $campaign));
}

function human($db, $CampaignID) {
    $sql_sys = 'SELECT status FROM `vicidial_statuses` where human_answered = "Y" ';
    $query_sys = $db->prepare($sql_sys);
    $query_sys->execute();
    $sys = array();
    while ($row = $query_sys->fetch(PDO::FETCH_BOTH)) {
        $sys[] = $row[0];
    }

    $sql_campaign = 'SELECT status FROM `vicidial_campaign_statuses` where campaign_id=:CampaignID and human_answered = "y" ';
    $query_campaign = $db->prepare($sql_campaign);
    $query_campaign->execute(Array(':CampaignID' => $CampaignID));
    $campaign = array();
    while ($row = $query_campaign->fetch(PDO::FETCH_BOTH)) {
        $campaign[] = $row[0];
    }
    echo json_encode(Array('human' => array_merge($sys, $campaign)));
}

function pausas_agents($db) {
    $sql = 'select pause_code, pause_code_name from vicidial_pause_codes';
    $query = $db->prepare($sql);
    $query->execute();
    $pausa = $query->fetchAll(PDO::FETCH_OBJ);

    echo json_encode(Array('pausa' => $pausa));
}

function liveAgents($db) {
    $sql_in_live = 'select count(*) as count from vicidial_live_inbound_agents';
    $query_in_live = $db->prepare($sql_in_live);
    $query_in_live->execute();
    $in_live = $query_in_live->fetch(PDO::FETCH_OBJ);

    $sql_out_live = 'SELECT count(*) as count FROM vicidial_live_agents';
    $query_out_live = $db->prepare($sql_out_live);
    $query_out_live->execute();
    $out_live = $query_out_live->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('out' => $out_live, 'in' => $in_live));
}

function inboundAgents($db) {
    $sql_live = 'select distinct user from `vicidial_live_inbound_agents` group by user';
    $query_live = $db->prepare($sql_live);
    $query_live->execute();
    $live = $query_live->fetchAll(PDO::FETCH_ASSOC);

    $sql_total = "SELECT DISTINCT user FROM `vicidial_inbound_group_agents` group by user";
    $query_total = $db->prepare($sql_total);
    $query_total->execute();
    $total = $query_total->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(Array('live' => $live, 'total' => $total));
}

function inboundLines($db) {
    $sql_active = "SELECT count(*) as count FROM `vicidial_inbound_groups` where active = 'Y'";
    $query_active = $db->prepare($sql_active);
    $query_active->execute();
    $active = $query_active->fetch(PDO::FETCH_OBJ);

    $sql_total = "SELECT count(*) as count FROM `vicidial_inbound_groups`";
    $query_total = $db->prepare($sql_total);
    $query_total->execute();
    $total = $query_total->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('active' => $active, 'total' => $total));
}

function getInbound($db) {
    $sql_inbound = 'select group_id, group_name from vicidial_inbound_groups';
    $query_inbound = $db->prepare($sql_inbound);
    $query_inbound->execute();
    $inbound = $query_inbound->fetchAll(PDO::FETCH_OBJ);
    echo json_encode(Array('inbound' => $inbound));
}

function pausas_total($db) {
    $sql = 'select pause_code, pause_code_name from vicidial_pause_codes';
    $query = $db->prepare($sql);
    $query->execute();
    $pausas = $query->fetchAll(PDO::FETCH_OBJ);

    echo json_encode(Array('pausas' => $pausas));
}

function pausas($db, $CampaignID) {
    $sql_pausas = 'select pause_code, pause_code_name from vicidial_pause_codes where campaign_id like "' . $CampaignID . '" ';
    $query_pausas = $db->prepare($sql_pausas);
    $query_pausas->execute();
    $pausas = $query_pausas->fetchAll(PDO::FETCH_OBJ);

    echo json_encode(Array('pausas' => $pausas));
}

function db($db) {
    $sql_db_active = 'select count(*) as count from vicidial_lists where active = "y"';
    $query_active = $db->prepare($sql_db_active);
    $query_active->execute();
    $active = $query_active->fetch(PDO::FETCH_OBJ);

    $sql_db_total = 'select count(*) as count from vicidial_lists';
    $query_total = $db->prepare($sql_db_total);
    $query_total->execute();
    $total = $query_total->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('active' => $active, 'total' => $total));
}

function allCampaigns($db) {
    $sql_campaigns = 'SELECT count(*) as count FROM `vicidial_campaigns`';
    $query_campaigns = $db->prepare($sql_campaigns);
    $query_campaigns->execute();
    $campaigns = $query_campaigns->fetch(PDO::FETCH_OBJ);

    $sql_active = 'select count(*) as count from vicidial_campaigns where active = "Y"';
    $query_active = $db->prepare($sql_active);
    $query_active->execute();
    $active = $query_active->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('total' => $campaigns, 'active' => $active));
}

function agents($db) {
    $sql_agents = "SELECT count(*) as count FROM `vicidial_live_agents` WHERE comments <> 'REMOTE'";
    $query_agents = $db->prepare($sql_agents);
    $query_agents->execute();
    $agents = $query_agents->fetch(PDO::FETCH_OBJ);

    $sql_total = 'SELECT count(*) as count FROM `vicidial_users` where vicidial_users.user_group <> "NULL"';
    $query_total = $db->prepare($sql_total);
    $query_total->execute();
    $total = $query_total->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('agents' => $agents, 'total' => $total));
}

function called($db, $CampaignID) {
    $sql_called = 'select count(lead_id) as count from vicidial_list where list_id IN (select list_id from vicidial_lists where campaign_id like "' . $CampaignID . '" and active = "Y") and status in (select status from vicidial_lead_recycle where campaign_id like "' . $CampaignID . '" and active = "Y" group by status) and called_since_last_reset IN ("N", "Y")';
    $query_called = $db->prepare($sql_called);
    $query_called->execute();
    $called = $query_called->fetch(PDO::FETCH_OBJ);
    echo json_encode(Array('called' => $called));
}

function novo($db, $CampaignID) {
    $sql_new = 'select status, count(lead_id) as count from vicidial_list where status in("NEW") AND list_id in (select list_id from vicidial_lists where campaign_id like "' . $CampaignID . '" AND active="Y" ) group by status';
    $query_new = $db->prepare($sql_new);
    $query_new->execute();
    $new = $query_new->fetch(PDO::FETCH_OBJ);
    echo json_encode(Array('new' => $new));
}

function total($db, $CampaignID) {
    $sql_total = 'select  count(lead_id) as count from vicidial_list where  list_id in (select list_id from vicidial_lists where campaign_id like "' . $CampaignID . '" AND active="Y" ) ';
    $query_total = $db->prepare($sql_total);
    $query_total->execute();
    $total = $query_total->fetch(PDO::FETCH_OBJ);
    echo json_encode(Array('total' => $total));
}

function close($db, $CampaignID) {
    $sql_close_recy = "select count(lead_id) as count from vicidial_list where list_id IN (select list_id from vicidial_lists where campaign_id like '" . $CampaignID . "' and active = 'Y') and status in (select distinct(status) from vicidial_lead_recycle where campaign_id like '" . $CampaignID . "' and active = 'Y') and called_since_last_reset NOT IN ('N', 'Y')";
    $close_recy_query = $db->prepare($sql_close_recy);
    $close_recy_query->execute();
    $close_recy = $close_recy_query->fetch(PDO::FETCH_OBJ);


    $sql_close_normal = "select count(lead_id) as count from vicidial_list where list_id IN (select list_id from vicidial_lists where campaign_id like '" . $CampaignID . "' and active = 'Y') and status not in (select distinct(status) from vicidial_lead_recycle where campaign_id like '" . $CampaignID . "' and active = 'Y') and called_since_last_reset NOT IN ('N')";
    $close_normal_query = $db->prepare($sql_close_normal);
    $close_normal_query->execute();
    $close_normal = $close_normal_query->fetch(PDO::FETCH_OBJ);

    echo json_encode(Array('closeNormal' => $close_normal, 'closeRecy' => $close_recy));
}

function getRecycling($db, $CampaignID) {

    $status_query = $db->prepare("SELECT `status` FROM `vicidial_lead_recycle` WHERE `campaign_id`=:campaign AND active='Y'");
    $status_query->execute(array(":campaign" => $CampaignID));
    $status = $status_query->fetchAll(PDO::FETCH_COLUMN, 0);

    $allStatus = implode("','", $status);

    $limite_query = $db->prepare("SELECT `status`,`attempt_maximum` FROM `vicidial_lead_recycle` WHERE `campaign_id`=:campaign AND active='Y' ");
    $limite_query->execute(array(":campaign" => $CampaignID));
    $limite = $limite_query->fetchAll(PDO::FETCH_OBJ);


    $if1 = " IF( status = ";
    $if2 = " , IF( (SUBSTR(called_since_last_reset, 2) <";
    $andsub = " AND SUBSTR(called_since_last_reset, 2) > 0), 1, 0), 0) = 1 ";
    $phrase = array();
    for ($i = 0; $i < count($limite); ++$i) {
        $ph = $if1 . " '" . $limite[$i]->status . "'" . $if2 . $limite[$i]->attempt_maximum . $andsub;
        array_push($phrase, $ph);
    }

    $final = implode("  OR ", $phrase);
    $sql = "select status, count(lead_id) as count from vicidial_list where status in('" . $allStatus . " ') and ( " . $final . " ) AND list_id in (select list_id from vicidial_lists where campaign_id like  '" . $CampaignID . "' AND active='Y' ) group by status";
    $data_query = $db->prepare($sql);
    $data_query->execute();
    $data = $data_query->fetchAll(PDO::FETCH_OBJ);

    echo json_encode(Array('result' => $data));
}

function getCampaignStatus($db) {
    $sucesso = array();
    $util = array();
    $human = array();

    $stmt = $db->prepare('SELECT status,campaign_id FROM `vicidial_campaign_statuses` where sale="Y"');
    $stmt->execute();

    while ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
        $sucesso[$data->campaign_id][] = $data->status;
    }

    $stmt1 = $db->prepare('SELECT status,campaign_id FROM `vicidial_campaign_statuses` where customer_contact="Y"');
    $stmt1->execute();

    while ($result = $stmt1->fetch(PDO::FETCH_OBJ)) {
        $util[$result->campaign_id][] = $result->status;
    }

    $stmt2 = $db->prepare('SELECT status,campaign_id FROM `vicidial_campaign_statuses` where human_answered="Y"');
    $stmt2->execute();

    while ($re = $stmt2->fetch(PDO::FETCH_OBJ)) {
        $human[$re->campaign_id][] = $re->status;
    }

    echo json_encode(array('sucesso' => $sucesso, 'util' => $util, 'human' => $human));
}

function getStatus($db, $CampaignID) {
    $sql = "select * FROM `vicidial_campaign_statuses` Where campaign_id ='" . $CampaignID . "' and selectable ='y'";
    $stmt = $db->prepare($sql);
    $stmt->execute();

    $Human = array();
    $Sucess = array();
    $DNC = array();
    $util = array();
    $NUtil = array();
    $Unworkable = array();
    $Callback = array();
    $Complete = array();
    $Drop[] = 'Drop';

    While ($data = $stmt->fetch(PDO::FETCH_OBJ)) {
        if ($data->human_answered === 'Y') {
            $Human[] = $data->status;
        }
        if ($data->sale === 'Y') { //sucesso
            $Sucess[] = $data->status;
        }
        if ($data->dnc === 'Y') { // DNC
            $DNC[] = $data->status;
        }
        if ($data->customer_contact === 'Y') { //util
            $util[] = $data->status;
        }
        if ($data->not_interested === 'Y') { // Nao util
            $NUtil[] = $data->status;
        }
        if ($data->unworkable === 'Y') { // Unworkable
            $Unworkable[] = $data->status;
        }
        if ($data->scheduled_callback === 'Y') { //Callback
            $Callback[] = $data->status;
        }
        if ($data->completed === 'Y') { //Complete
            $Complete[] = $data->status;
        }
    }

    echo json_encode(Array('Answer' => $Human, 'Sucesso' => $Sucess, 'DNC' => $DNC, 'Util' => $util, 'NUtil' => $NUtil, 'Unworkable' => $Unworkable, 'Callback' => $Callback, 'Complete' => $Complete, 'DROP' => $Drop));
}

/*Fechados por Max Recilcagem
 * 
 * select count(lead_id) from vicidial_list where list_id IN (select list_id from vicidial_campaigns where campaign_id like 'W00003' and active = 'Y') and status in (select distinct(status) from vicidial_lead_recycle where campaign_id like 'W00003' and active = 'Y') and called_since_last_reset NOT IN ('N', 'Y')
 * 
 * fechado directo
 * select count(lead_id) from vicidial_list where list_id IN (select list_id from vicidial_campaigns where campaign_id like 'W00003' and active = 'Y') and status not in (select distinct(status) from vicidial_lead_recycle where campaign_id like 'W00003' and active = 'Y') and called_since_last_reset NOT IN ('N')
 */

/* NEW -> select status, count(lead_id) as count from vicidial_list where status in('NEW') AND list_id in (select list_id from vicidial_lists where campaign_id like 'w00003' AND active='Y' ) group by status