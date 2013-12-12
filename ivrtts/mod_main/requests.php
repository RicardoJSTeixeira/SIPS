<?php

// database & mysqli
require("../database/db_connect.php");

// post & get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

// function filter
switch ($zero) {
    case 'SpeedbarLinks' : SpeedbarLinks($link_id);
        break;
    case 'GetActiveCampaignsDropDownList' :GetActiveCampaignsDropDownList();
        break;
    default : header("HTTP/1.1 500 Internal Server Error");
        exit;
}

function SpeedbarLinks($link_id) {
    global $db;
    $params = array($link_id);
    $results = $db->rawQuery("SELECT path, label FROM zero.menu_sub_links WHERE id_menu_link = ? AND visible = 1", $params);
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

function GetActiveCampaignsDropDownList() {
    global $db;
    //$params = array("Y");
    $results = $db->rawQuery("SELECT A.campaign_name, A.campaign_id, A.active FROM vicidial_campaigns A INNER JOIN zero.allowed_campaigns B ON A.campaign_id=B.campaigns ORDER BY A.campaign_name");
    foreach ($results as $key => $value) {
        $js['camp_list'][] = $value;
    }
    echo json_encode($js);
}
