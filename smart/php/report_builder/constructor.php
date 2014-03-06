<?php

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');
//vai dissecar a váriaveis  que vêm do Post e Get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
require('../user.php');
require('../pdo.php');

$host = "mysql:host=goviragem.dyndns.org;dbname=asterisk;charset=utf8";

try {
    $db2 = new PDO($host, 'sipsadmin', 'sipsps2012');
    $db2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db2->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}

/*
  require("../../ini/dbconnect.php");
  require("../../ini/db.php");
  require("../../ini/user.php");
 */

//excel wrapper and phpexcel classes
require './excelwraper.php';
require '../phpexcel/Classes/PHPExcel.php';

//$user = new mysiblings($db);
$userClass = new user($db);
$userClass->confirm_login();
$user = $userClass->getUser();

$server = 'http://goviragem.dyndns.org:10000/ccstats/v0/';

switch ($action) {
    case 'getCampaign':
        echo (json_encode(getCampaign($db, $user->domain)));

        break;
    case 'getList':
        echo json_encode(getList($db, $user->domain));
        break;
    case 'getInbound':
        echo json_encode(getInbound($db, $user->domain));
        break;
    case 'getTemplate':
        echo json_encode(getTemplate($db, $templateId));
        break;
    case 'deleteTemplate':
        echo json_encode(deleteTemplate($db, $templateId));
        break;
    case 'getTemplateList':
        echo json_encode(getTemplateList($db));
        break;
    case 'getFeedBack':
        // echo json_encode($user->get_feedbacks($campId));
        echo json_encode(getFeedBack($db2, $campId));
        break;
    case 'getUser':
        echo json_encode(getUser($db, $user->domain));
        break;
    case 'saveTemplate':
        echo json_encode(saveTemplate($db, $users, $name, (object) $dateRange, $type, json_decode($typeId), json_decode($template)));
        break;
    case 'editTemplate':
        echo json_encode(editTemplate($db, $users, (object) $dateRange, $type, json_decode($typeId), json_decode($template), $templateId));
        break;
    case 'constructPreview':
        echo json_encode(constructPreview($db, $templateId, $localNow, $localSubtract));
        break;
    case'templateDownload':
        echo json_encode(templateDownload($db, $templateId));
        break;
    case'getCampaignId':
        echo json_encode(getCampaignId($db, $cabiId, $id));
        break;
    case'getInboundFeeds':
        echo json_encode(getInboundFeeds($db, $id));
        break;
    case'getTemplatecampaignName':
        echo json_encode(getTemplatecampaignName($db, $idSeries));
        break;
    case'getTemplatelistName':
        echo json_encode(getTemplatelistName($db, $idSeries));
        break;
    case'getTemplateinboundName':
        echo json_encode(getTemplateInboundName($db, $idSeries));
        break;
    case'getTemplateListUser':
        echo json_encode(getTemplateListUser($db, $user));
        break;
    case'getStatusInfo':
        echo json_encode(getStatusInfo($db, $server, $timeStart, $timeEnd, $campaignId, $status));
        break;
    default:
        break;
}

Function getFeedBack($db2, $campId) {

    //$stmt = $db->prepare("SELECT * FROM outcomes WHERE owner_id=:campid ORDER BY group_name");

    $stmt = $db2->prepare("select * from ((SELECT status id ,status_name name,human_answered,sale,dnc,customer_contact workable,not_interested, unworkable ,scheduled_callback ,completed FROM vicidial_campaign_statuses where campaign_id=?) union all (SELECT status id, status_name name,human_answered,sale,dnc,customer_contact,not_interested, unworkable ,scheduled_callback,completed FROM vicidial_statuses)) a group by id order by name asc");
    $stmt->execute(array($campId));

    // $stmt->execute(array("campid" => $campId));

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

Function getInbound($db, $domain) {

    $stmt = $db->prepare("SELECT id, name FROM inbound_groups WHERE domain=:domain");
    $stmt->execute(array("domain" => $domain));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getList($db, $domain) {

    $stmt = $db->prepare("SELECT id, name FROM databases WHERE domain=:domain");
    $stmt->execute(array("domain" => $domain));
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

Function getCampaign($db, $domain) {

    $stmt = $db->prepare("SELECT id, name FROM campaigns WHERE domain=:domain");
    $stmt->execute(array("domain" => $domain));

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUser($db, $domain) {

    $stmt = $db->prepare("SELECT u.id, CONCAT(u.first_name, ' ', u.last_name) AS name FROM users u WHERE u.domain=:domain");
    $stmt->execute(array("domain" => $domain));

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getStatusInfo($db, $server, $start, $end, $campaignId, $status) {

    $result = json_decode(file_get_contents($server . "total/calls/" . $start . "T00:00/" . $end . "T23:59?by=agent&campaign=$campaignId&status=$status"));

    foreach ($result as $value) {
        $output['aaData'][] = array($value->agent, $value->calls, $value->length);
    }
    return $output;
}

function saveTemplate($db, $users, $name, $dateRange, $type, $typeId, $template) {

    $querySaveTemplate = "INSERT INTO report_template(name, start_date, end_date, tipo, tipo_id,template, dynamic_date) VALUES (:name, :start_date, :end_date, :tipo,:tipo_id,:template,:dynamic_date) RETURNING id";
    $stmt = $db->prepare($querySaveTemplate);
    $stmt->execute(array(":name" => $name, ":start_date" => $dateRange->start, ":end_date" => $dateRange->end, ":tipo" => $type, ":tipo_id" => json_encode($typeId), ":template" => json_encode($template), ":dynamic_date" => $dateRange->dyn));

    $templateId = $stmt->fetch(PDO::FETCH_OBJ);

    $querySaveUserTemplate = "INSERT INTO report_template_users (user_id, id_template) VALUES (:user_id, :id_template)";

    $stmt = $db->prepare($querySaveUserTemplate);

    foreach ($users as $value) {

        $stmt->execute(array(":user_id" => $value, ":id_template" => $templateId->id));
    }

    return $templateId;
}

function editTemplate($db, $users, $dateRange, $type, $typeId, $template, $templateId) {

    $querySaveTemplate = "UPDATE report_template SET start_date=:start_date,end_date=:end_date,tipo=:tipo,tipo_id=:tipo_id,template=:template,dynamic_date=:dynamic_date WHERE id=:id";

    $stmt = $db->prepare($querySaveTemplate);
    $stmt->execute(array(":start_date" => $dateRange->start, ":end_date" => $dateRange->end, ":tipo" => $type, ":tipo_id" => json_encode($typeId), ":template" => json_encode($template), ":dynamic_date" => $dateRange->dyn, ":id" => $templateId));


    $queryDeletUsers = "DELETE FROM report_template_users WHERE id_template=:id";

    $stmt = $db->prepare($queryDeletUsers);

    $stmt->execute(array(":id" => $templateId));


    $querySaveUserTemplate = "INSERT INTO report_template_users(user_id, id_template) VALUES (:user_id, :id_template)";
    $stmt = $db->prepare($querySaveUserTemplate);

    foreach ($users as $value) {

        $stmt->execute(array(":user_id" => $value, ":id_template" => $templateId));
    }

    return true;
}

function getTemplateList($db) {

    $query = "SELECT  id,name  FROM report_template";

    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTemplateListUser($db, $user) {

    $query = "SELECT b.* FROM `report_template_users` a right join `report_template` b on a.id_template=b.id_template WHERE `user`='$user->id'";

    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTemplate($db, $templateId) {

    $query = "SELECT name,start_date,end_date,tipo,tipo_id,template,dynamic_date FROM report_template WHERE id=:id";

    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $templateId));

    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    $template['template'] = json_decode($template['template']);
    $template['tipo_id'] = json_decode($template['tipo_id']);

    $query = "SELECT user_id FROM report_template_users WHERE id_template=:id";

    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $templateId));

    $template['users'] = $stmt->fetchAll(PDO::FETCH_NUM);

    return $template;
}

function deleteTemplate($db, $templateId) {

    $query = "DELETE FROM report_template WHERE id=:id";

    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $templateId));

    $query = "DELETE FROM report_template_users WHERE id_template=:id";

    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $templateId));


    return true;
}

function templateDownload($db, $templateId) {

    $template = getTemplate($db, $templateId);

    $dataExcel = array();
    $toExcel = new excelwraper(New PHPExcel(), 'Report', 18, 10);
    ob_start();

    $translate = array(
        "list" => "database",
        "campaign" => "campaign"
    );

    $templateTypo = $translate[$template['tipo']];

    $campaignValues = array();

    foreach ($template['tipo_id'] as $campaignId) {

        $myMongoData = mongoDBData($campaignId, $templateTypo, $template['start'], $template['end']);

        foreach ($myMongoData as $status) {

            $campaignValues[$campaignId][$status->status] = array('length' => $status->length, 'calls' => $status->calls);
        }
    }

    foreach ($template['template'] as $data) {

        $dataExcel [] = array('Name', 'Total');


        foreach ($data->children as $children) {


            $dataExcel[] = array($children->text, $campaignValues[$children->propertyOf][$children->status]["calls"]);
        }

        $toExcel->maketable($dataExcel, TRUE, $data->text, NULL, NULL, 'chart2', 'r', 'bars', 'bars', TRUE, TRUE);
        $dataExcel = array();
    }

    $toExcel->backGroundStyle('FFFFFF');

    $toExcel->save('Report', TRUE);
    ob_end_clean();

    $toExcel->send();
}

function constructPreview($db, $templateId, $localNow, $localSubtract) {

    $template = getTemplate($db, $templateId);

    $campaignId = $template['tipo_id'];

    if ($template['start_date'] == '1337-10-01') {
        $startDate = $localSubtract;
        $endDate = $localNow;
    } else {
        $startDate = $template['start_date'];
        $endDate = $template['end_date'];
    }

    $myPreview = array();
    $temp = array();

    $translate = array(
        "list" => "database",
        "campaign" => "campaign"
    );

    $templateTypo = $translate[$template['tipo']];

    $campaignValues = array();

    foreach ($template['tipo_id'] as $campaignId) {
        
    }
    $myMongoData = mongoDBData('W00003', $templateTypo, $startDate, $endDate);

    foreach ($myMongoData as $status) {

        $campaignValues['W00003'][$status->status] = array('length' => $status->length, 'calls' => $status->calls);
    }

    /*
      foreach ($template['tipo_id'] as $campaignId) {

      $myMongoData = mongoDBData($campaignId, $templateTypo, $template['start'], $template['end']);

      foreach ($myMongoData as $status) {

      $campaignValues[$campaignId][$status->status] = array('length' => $status->length, 'calls' => $status->calls);
      }
      }
     */

    foreach ($template['template']as $data) {

        $temp = array(
            'name' => $data->text,
            'total' => '',
            'perc' => '',
            'start' => $startDate,
            'end' => $endDate,
            'values' => array()
        );
        foreach ($data->children as $children) {

            $temp['values'][] = array(
                'name' => $children->text,
                'status' => $children->status,
                'propertyOf' => $children->propertyOf,
                'value' => $campaignValues[$children->propertyOf][$children->status]["calls"],
                'perc' => '',
            );
        }
        $myPreview[] = $temp;
    }

    return $myPreview;
}

function mongoDBData($ownerId, $ownerType, $dateStart, $dateEnd) {

    $result = file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/total/calls/" . $dateStart . "T00:00/" . $dateEnd . "T00:00?$ownerType=$ownerId&by=status");

    return json_decode($result);
}

///////////////////////////////////////////////---------------------------------------------

function get_list($db, $domain) {

    $query = "SELECT `list_id` id,`list_name` name FROM `vicidial_lists`";

    $stmt = $db->prepare($query);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCampaignId($db, $cabiId, $id) {

    if ($cabiId == '#selectlist') {

        $query = "SELECT owner_id FROM  databases WHERE  id =:id";

        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));

        $campaignId = $stmt->fetch(PDO::FETCH_ASSOC);

        return $campaignId;
    }
}

//Select do Agrupador
function getTemplatecampaignName($db, $idSeries) {

    $prepare_hack = '';
    for ($index = 0; $index < count($idSeries); $index++) {
        $prepare_hack.="?,";
    }

    $stmt = $db->prepare("SELECT id ,name  FROM campaigns WHERE id IN (" . rtrim($prepare_hack, ",") . ");");

    $stmt->execute($idSeries);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTemplatelistName($db, $idSeries) {

    for ($index = 0; $index < count($idSeries); $index++) {
        $prepare_hack.="?,";
    }

    $stmt = $db->prepare("SELECT id, name FROM databases WHERE id IN (" . rtrim($prepare_hack, ",") . ");");

    $stmt->execute($idSeries);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTemplateInboundName($db, $idSeries) {

    for ($index = 0; $index < count($idSeries); $index++) {
        $prepare_hack.="?,";
    }

    $stmt = $db->prepare("SELECT id, name FROM inbound_groups WHERE id IN (" . rtrim($prepare_hack, ",") . ");");

    $stmt->execute($idSeries);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
