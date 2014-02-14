<?php

require("../../ini/db.php");
require("../lib/translater.php");

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
ini_set("display_errors", "1");

if ($action == "RollbackEverything") {
    $params0 = array($sent_campaign_id);
    $params1 = array($sent_list_id);

    $stmt = $db->prepare("DELETE FROM vicidial_campaigns WHERE campaign_id IN (?)");
    $stmt->execute($params0);
    $stmt = $db->prepare("DELETE FROM vicidial_campaign_stats WHERE campaign_id IN (?)");
    $stmt->execute($params0);
    $stmt = $db->prepare("DELETE FROM vicidial_campaign_statuses WHERE campaign_id IN (?)");
    $stmt->execute($params0);
    $stmt = $db->prepare("DELETE FROM vicidial_lead_recycle WHERE campaign_id IN (?)");
    $stmt->execute($params0);

    $stmt = $db->prepare("DELETE FROM vicidial_lists WHERE list_id IN (?)");
    $stmt->execute($params1);
    $stmt = $db->prepare("DELETE FROM vicidial_list WHERE list_id IN (?)");
    $stmt->execute($params1);
}

if ($action == "GetPreview") {


    $file = fopen("/tmp/$sent_converted_file", "r");

    $buffer = rtrim(fgets($file, 4096)); // headers!

    $counter = 0;
    while ($counter < 3) {
        $buffer = rtrim(fgets($file, 4096));

        $js['buffer'][] = $buffer;

        $rows = explode("\t", $buffer);

        $js[$counter][] = $rows[0];
        $js[$counter][] = $rows[1];
        $js[$counter][] = $rows[2];
        $js[$counter][] = $rows[3];

        $counter++;
    }

    fclose($file);
    echo json_encode($js);
}


if ($action == "CreateCampaign") {
    $stmt = $db->prepare("SELECT count(*) FROM vicidial_campaigns");
    $stmt->execute();
    $result_camps = $stmt->fetchAll(PDO::FETCH_BOTH);
    $TempCampaignID = $result_camps[0]['count(*)'];
    while (strlen($TempCampaignID) < 4) {
        $TempCampaignID = "0" . $TempCampaignID;
    }
    $CampaignID = "CT" . $TempCampaignID;
    switch ($lang) {
        case 'pt-male' : $voice = 'Vicente';
            break;
        case 'pt-female' : $voice = 'Violeta';
            break;
    }
    // ALLOWED CAMPAIGNS
    $params = array($CampaignID);
    $stmt = $db->prepare("INSERT INTO zero.allowed_campaigns (campaigns) VALUES (?)");
    $stmt->execute($params);


    // CREATE CAMPAIGNS
    $params1 = array($CampaignID, $sent_campaign_name, 'N', 'DOWN', 'Y', '50', '1', 'longest_wait_time', 'weekwork', '50', '0134', '0', 'ALLFORCE', 'FULLDATE_CUSTPHONE', '0', 'HANGUP', 'RATIO', '3', 'Y', 'DC PU PDROP ERI NA DROP B NEW -', 'Y', 'ALT_AND_ADDR3', $voice);
    $stmt = $db->prepare("INSERT INTO vicidial_campaigns
	(
	campaign_id,
	campaign_name,
	active,
	lead_order,
	allow_closers,
	hopper_level,
	auto_dial_level,
	next_agent_call,
	local_call_time,
	dial_timeout,
	dial_prefix,
	allcalls_delay,
	campaign_recording,
	campaign_rec_filename,
	drop_call_seconds,
	drop_action,
	dial_method,
	adaptive_dropped_percentage,
	no_hopper_leads_logins,
	dial_statuses,
	omit_phone_code,
	auto_alt_dial,
        campaign_description
	)
	VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute($params1);

    // CAMPAING STATS
    $params2 = array($CampaignID);
    $stmt = $db->prepare("INSERT INTO vicidial_campaign_stats (campaign_id) VALUES (?)");
    $stmt->execute($params2);
    $params3 = array($CampaignID, 'demoij');
    $stmt = $db->prepare("UPDATE vicidial_user_groups SET allowed_campaigns = CONCAT(?, allowed_campaigns) WHERE user_group LIKE ?");
    $stmt->execute($params3);


    // CREATE DBS

    $stmt = $db->prepare("SELECT count(*) FROM vicidial_lists");
    $stmt->execute();
    $result1 = $stmt->fetchAll(PDO::FETCH_BOTH);
    $ListID = ($result1[0]['count(*)'] + 50000);

    $params3 = array($ListID, $sent_campaign_name, $CampaignID, 'Y');
    $stmt = $db->prepare("INSERT INTO vicidial_lists (list_id, list_name, campaign_id, active) VALUES (?, ?, ?, ?)");
    $stmt->execute($params3);

    // RECYCLE

    $params4 = array(
        array($CampaignID, 'B', 1800, 10, 'Y'),
        array($CampaignID, 'DC', 1800, 10, 'Y'),
        array($CampaignID, 'DROP', 120, 10, 'Y'),
        array($CampaignID, 'ERI', 1800, 10, 'Y'),
        array($CampaignID, 'NA', 3600, 10, 'Y'),
        array($CampaignID, 'PDROP', 120, 10, 'Y'),
        array($CampaignID, 'PU', 120, 10, 'Y')
    );

    for ($i = 0; $i < count($params4); $i++) {
        $stmt = $db->prepare("INSERT INTO vicidial_lead_recycle (campaign_id, status, attempt_delay, attempt_maximum, active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute($params4[$i]);
    }

    // STATS

    $params5 = array(array('MSG001', 'Ouviu Mensagem', 'N', $CampaignID, 'Y', 'N'),
        array('MSG002', 'Declinou Mensagem', 'N', $CampaignID, 'Y', 'N'),
        array('MSG003', 'Atendeu e Declinou', 'N', $CampaignID, 'Y', 'N'),
        array('MSG004', 'Ouviu Mensagem e SMS', 'N', $CampaignID, 'Y', 'N'),
        array('MSG005', 'Ouviu Mensagem e EMAIL', 'N', $CampaignID, 'Y', 'N'),
        array('MSG006', 'Tranferência para Call-Center', 'N', $CampaignID, 'Y', 'N'),
        array('MSG007', 'Solicitou Contacto', 'N', $CampaignID, 'Y', 'N')
    );
    for ($i = 0; $i < count($params5); $i++) {
        $stmt = $db->prepare("INSERT INTO vicidial_campaign_statuses (status, status_name, selectable, campaign_id, human_answered, scheduled_callback) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute($params5[$i]);
    }


    // REMOTE AGENTS

    $stmt = $db->prepare("SELECT server_ip FROM servers");
    $stmt->execute();
    $result8 = $stmt->fetchAll(PDO::FETCH_BOTH);
    $ServerIP = $result8[0]['server_ip'];

    for ($i = 0; $i < 10; $i++) {
        $params = array($result_camps[0]['count(*)'] . "00" . $i, 1, $ServerIP, 787778, "INACTIVE", $CampaignID);
        $stmt = $db->prepare("INSERT INTO vicidial_remote_agents (user_start, number_of_lines ,server_ip, conf_exten, status, campaign_id) values(?,?,?,?,?,?)");
        $stmt->execute($params);

        $params = array($result_camps[0]['count(*)'] . "00" . $i, 1234, $CampaignID, "Y");
        $stmt = $db->prepare("INSERT INTO vicidial_users (user, pass, full_name, active) VALUES (?, ?, ?, ?)");
        $stmt->execute($params);
    }

    $js['result'][] = $CampaignID;
    $js['result'][] = $ListID;

    echo json_encode($js);
}

if ($action == "LoadLeads") {
    $dt=new dictionary($db);
    $translation=$dt->getFormated();
    // REGEX
    $regex_filter = "/['\"`\\;]/";

    // INIS
    $entry_date = date("Y-m-d H:i:s");
    $last_local_call_time = "2008-01-01 00:00:00";
    $gmt_offset = '0';
    $called_since_last_reset = 'N';

    $file = fopen("/tmp/$sent_converted_file", "r");
    $headers = explode("\t", rtrim(fgets($file, 4096)));

    // COUNTERS
    $LineCounter = 0;
    $Errors = 0;
    $stmt = $db->prepare("INSERT INTO vicidial_list (phone_number, comments, email, extra1, entry_date, called_since_last_reset, gmt_offset_now, last_local_call_time, list_id, status ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    while (!feof($file)) {
        $buffer = rtrim(fgets($file, 4096));
        if (strlen($buffer) > 0) {
            $buffer = stripslashes($buffer);
            $buffer = explode("\t", $buffer);

            $ErrorCode = 0;

            $PhoneNumber = preg_replace("/[^0-9]/", "", $buffer[0]);
            $PhoneNumberFlag = true;

            $Msg1 = preg_replace("/['\"`\\;]/", "", $buffer[1]);
            $Msg2 = preg_replace("/['\"`\\;]/", "", $buffer[2]);
            $id = preg_replace("/['\"`\\;]/", "", $buffer[3]);
            
            $Msg1 = $Msg1;
            $Msg2 = $Msg2;
            
            $Msg1 = strtr($Msg1,$translation);
            $Msg2 = strtr($Msg2,$translation);


            if (strlen($PhoneNumber) != 9) {
                $ErrorCode = 1;
                $Errors++;
            } else {
                
            }

            if ($ErrorCode == 0) {
                $params0 = array($PhoneNumber, $Msg1, $Msg2,$id, $entry_date, 'N', 0, "2008-01-01 00:00:00", $sent_list_id, 'tts');
                $stmt->execute($params0);
            } else {
                switch ($ErrorCode) {
                    case 1: $js['errortext'][] = "The phone number ($buffer[0]) in line: " . ($LineCounter + 1) . ", contains errors.";
                }
            }


            $LineCounter++;
        }
    }

    $params = array($LineCounter, $sent_list_id);
    $stmt = $db->prepare("UPDATE vicidial_lists SET list_description = ? WHERE list_id = ?");
    $stmt->execute($params);


    //$buffer = rtrim(fgets($file, 4096));
    //$buffer = explode("\t", $buffer);
    fclose($file);
    $js['leads'] = $LineCounter;
    $js['errors'] = $Errors;

    echo json_encode($js);
}