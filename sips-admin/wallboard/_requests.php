<?php

date_default_timezone_set('Europe/Lisbon');

require("../../ini/dbconnect.php");
require("../../ini/functions.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
// REQUESTS DO INDEX   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($action == "create-new-template") {
    //$sQuery = mysql_query("INSERT INTO sips_wallboards (wallboard) VALUES ('$sent_wallboard_name')", $link) or die(mysql_error());
}
// REQUESTS DO EDITOR   ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($action == "wizard-show-campaigns") {
    // ALLOWED CAMPAIGNS
    $query1 = mysql_fetch_assoc(mysql_query("SELECT user_group FROM vicidial_users WHERE user='$_SERVER[PHP_AUTH_USER]';", $link)) or die(mysql_error());
    $query2 = mysql_fetch_assoc(mysql_query("SELECT allowed_campaigns FROM vicidial_user_groups WHERE user_group='$query1[user_group]';", $link)) or die(mysql_error());
    $AllowedCampaigns = "'" . preg_replace("/ /", "','", preg_replace("/ -/", '', $query2['allowed_campaigns'])) . "'";
    //
    $sQuery = mysql_query("SELECT campaign_id, campaign_name FROM vicidial_campaigns WHERE campaign_id IN ($AllowedCampaigns)", $link);
    while ($row = mysql_fetch_row($sQuery)) {
        $js['id'][] = $row[0];
        $js['name'][] = $row[1];
    }
    echo json_encode($js);
}

if ($action == "wizard-show-feedbacks") {

    if ($sent_type == "multiple") {
        $sQuery = mysql_query("SELECT status, status_name FROM vicidial_campaign_statuses WHERE campaign_id='$sent_campaign_id'", $link) or die(mysql_error());
        while ($row = mysql_fetch_row($sQuery)) {
            $js['id'][] = $row[0];
            $js['name'][] = $row[1];
        }
    } else {
        $campaigns_IN = Array2IN($sent_multiple_campaigns);
        $sQuery = mysql_query("SELECT status, status_name FROM vicidial_campaign_statuses WHERE campaign_id IN ($campaigns_IN) ORDER BY status", $link) or die(mysql_error());

        $counter = 0;
        $match_counter = 0;
        $max_counter = count($sent_multiple_campaigns);

        while ($row = mysql_fetch_row($sQuery)) {
            if ($counter == 0) {
                $previousid = $row[0];
            } else {
                if ($previousid == $row[0]) {
                    $match_counter++;
                    $previousid = $row[0];
                    if ($match_counter == ($max_counter - 1)) {
                        $js['id'][] = $row[0];
                        $js['name'][] = $row[1];
                        $match_counter = 0;
                    }
                } else {
                    $previousid = $row[0];
                }
            }
            $counter++;
        }
    }
    echo json_encode($js);
}
if ($action == "build_feedspercamps") {

    if (count($sent_campaigns) > 1) {
        $campaigns_IN = Array2IN($sent_campaigns);

        echo $campaign_IN;

        $sQuery = mysql_query("SELECT campaign_id, count(status) FROM vicidial_log WHERE campaign_id IN ($campaigns_IN) and status='$sent_feedbacks' GROUP BY campaign_id", $link) or die(mysql_error());



        for ($i = 0; $i < mysql_num_rows($sQuery); $i++) {
            $row = mysql_fetch_row($sQuery);
            $json['ticks'][] = $row[0];
            $json['series'][] = $row[1];
        }

        echo json_encode($json);
    }
}

if ($action == "change-wallboard-scheme") {
    //mysql_query("DELETE FROM sips_wallboards WHERE wallboard='$sent_wallboard'",$link) or die(mysql_error());
    //mysql_query("INSERT INTO sips_wallboards (wallboard, template) VALUES ('$sent_wallboard', '$sent_template')") or die(mysql_error());
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

if ($action == "getdata") {
    $sQuery = "  SELECT user, count(status) 
                FROM vicidial_log 
                WHERE campaign_id='ZON001' 
                AND status ='NI' 
                AND call_date BETWEEN '2012-01-01' AND '2012-01-31' 
                GROUP BY user 
                LIMIT 5 ";

    $rResult = mysql_query($sQuery, $link);
    $count = mysql_num_rows($rResult);
    $ticks = "";
    $series = "";

    for ($i = 0; $i < $count; $i++) {
        $row = mysql_fetch_row($rResult);
        $json['ticks'][] = $row[0];
        $json['series'][] = $row[1];
    }

    echo json_encode($json);
}
?>