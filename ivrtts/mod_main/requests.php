<?php

require("../../ini/db.php");

// post & get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

// function filter
switch ($zero) {
    case 'SpeedbarLinks' : SpeedbarLinks($link_id, $db);
        break;
    case 'GetActiveCampaignsDropDownList' :GetActiveCampaignsDropDownList($db);
        break;
    default : header("HTTP/1.1 500 Internal Server Error");
        exit;
}

function SpeedbarLinks($link_id, $db) {
    $params = array($link_id);
    $stmt = $db->prepare("SELECT path, label FROM zero.menu_sub_links WHERE id_menu_link = ? AND visible = 1");
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_BOTH);
    $speedbar_html = "";
    for ($i = 0; $i < count($results); $i++) {
        if ($i == 0) {
            $speedbar_active = "act_link";
        } else {
            $speedbar_active = "";
        }
        $speedbar_html .= "<li><a class='" . $speedbar_active . "' href='" . $results[$i]['path'] . "'>" . $results[$i]['label'] . "</a></li>";
    }
    print $speedbar_html;
}

function GetActiveCampaignsDropDownList($db) {
    $stmt = $db->prepare("SELECT A.campaign_name, A.campaign_id, A.active FROM vicidial_campaigns A INNER JOIN zero.allowed_campaigns B ON A.campaign_id=B.campaigns ORDER BY A.campaign_name");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_BOTH);
    $js=array('camp_list'=>array());
    foreach ($results as $value) {
        $js['camp_list'][] = $value;
    }
    echo json_encode($js);
}
