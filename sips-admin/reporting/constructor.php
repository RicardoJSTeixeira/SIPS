<?php

//vai dissecar a váriaveis  que vêm do Post e Get
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

require("../../ini/dbconnect.php");
require("../../ini/db.php");
require("../../ini/user.php");
//excel wrapper and phpexcel classes
require '../../ini/phpexcel/PHPExcel.php';
require '../../ivrtts/report/excelwraper.php';

$user = new mysiblings($db);

switch ($action) {
    case 'getCampaign':
        echo json_encode($user->get_campaigns());
        break;
    case 'getAgent':
        echo json_encode($user->get_agentes());
        break;
    case 'getList':
        echo json_encode(get_list($db));
        break;
    case 'getInbound':
        echo json_encode($user->get_linha_inbound());
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
        echo json_encode($user->get_feedbacks($campId));
        break;
    case 'getUser':
        echo json_encode($user->get_agentes());
        break;
    case 'saveTemplate':
        echo json_encode(saveTemplate($db, $users, $name, (object) $dateRange, $type, json_decode($typeId), json_decode($template)));
        break;
    case 'editTemplate':
        echo json_encode(editTemplate($db, $users, (object) $dateRange, $type, json_decode($typeId), json_decode($template), $templateId));
        break;
    case 'constructPreview':
        echo json_encode(constructPreview($db, $templateId, $cabiType));
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
    case'getTemplateListUser':
        echo json_encode(getTemplateListUser($db, $user));
        break;
    default:
        break;
}

function saveTemplate($db, $users, $name, $dateRange, $type, $typeId, $template) {

    $querySaveTemplate = "INSERT INTO `report_template`(`name`, `start`, `end`, `tipo`, `tipo_id`,`template`) VALUES (:name, :start, :end, :tipo,:tipo_id,:template)";
    $stmt = $db->prepare($querySaveTemplate);
    $stmt->execute(array(":name" => $name, ":start" => $dateRange->start, ":end" => $dateRange->end, ":tipo" => $type, ":tipo_id" => json_encode($typeId), ":template" => json_encode($template)));

    $templateId = $db->lastInsertId();

    $querySaveUserTemplate = "INSERT INTO `report_template_users`(`user`, `id_template`) VALUES (:user, :id_template)";
    $stmt = $db->prepare($querySaveUserTemplate);

    foreach ($users as $value) {

        $stmt->execute(array(":user" => $value, ":id_template" => $templateId));
    }

    return $templateId;
}

function editTemplate($db, $users, $dateRange, $type, $typeId, $template, $templateId) {

    $querySaveTemplate = "UPDATE `report_template` SET `start`=:start,`end`=:end,`tipo`=:tipo,`tipo_id`=:tipo_id,`template`=:template WHERE `id_template`=:id";

    $stmt = $db->prepare($querySaveTemplate);
    $stmt->execute(array(":start" => $dateRange->start, ":end" => $dateRange->end, ":tipo" => $type, ":tipo_id" => json_encode($typeId), ":template" => json_encode($template), ":id" => $templateId));


    $queryDeletUsers = "DELETE FROM `report_template_users` WHERE `id_template`=:id";

    $stmt = $db->prepare($queryDeletUsers);

    $stmt->execute(array(":id" => $templateId));


    $querySaveUserTemplate = "INSERT INTO `report_template_users`(`user`, `id_template`) VALUES (:user, :id_template)";
    $stmt = $db->prepare($querySaveUserTemplate);

    foreach ($users as $value) {

        $stmt->execute(array(":user" => $value, ":id_template" => $templateId));
    }

    return true;
}

function getTemplateList($db) {

    $query = "SELECT id_template,name FROM report_template";

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

    $query = "SELECT `name`,`start`,`end`,`tipo`,`tipo_id`,`template` FROM report_template WHERE id_template=:id";

    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $templateId));

    $template = $stmt->fetch(PDO::FETCH_ASSOC);

    $template['template'] = json_decode($template['template']);
    $template['tipo_id'] = json_decode($template['tipo_id']);

    $query = "SELECT `user` FROM `report_template_users` WHERE `id_template`=:id";

    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $templateId));

    $template['users'] = $stmt->fetchAll(PDO::FETCH_NUM);
    return $template;
}

function deleteTemplate($db, $templateId) {

    $query = "DELETE FROM `report_template` WHERE `id_template`=:id";

    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $templateId));


    $query = "DELETE FROM `report_template_users` WHERE `id_template`=:id";

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
            if (isset($campaignValues[$children->propertyOf][$children->status]["calls"])) {
                $value = $campaignValues[$children->propertyOf][$children->status]["calls"];
            } else {
                $value = "n/a";
            }

            $dataExcel[] = array($children->text, $value);
        }


        $toExcel->maketable($dataExcel, TRUE, $data->text, NULL, NULL, 'chart2', 'r', 'bars', 'bars', TRUE, TRUE);
        $dataExcel = array();
    }

    $toExcel->backGroundStyle('FFFFFF');

    $toExcel->save('Report', TRUE);
    ob_end_clean();

    $toExcel->send();
}

function constructPreview($db, $templateId) {

    $template = getTemplate($db, $templateId);

    $campaignId = $template['tipo_id'];

    $myPreview = array();
    $temp = [];

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


    foreach ($template['template']as $data) {

        $temp = array(
            'name' => $data->text,
            'total' => '',
            'perc' => '',
            'values' => []
        );

        foreach ($data->children as $children) {

            if (isset($campaignValues[$children->propertyOf][$children->status]["calls"])) {
                $value = $campaignValues[$children->propertyOf][$children->status]["calls"];
            } else {
                $value = "n/a";
            }


            $temp['values'][] = array(
                'name' => $children->text,
                'value' => $value,
                'perc' => '',
            );
        }
        $myPreview[] = $temp;
    }

    return $myPreview;
}

//*cabi:campanha,Agentes,bds e inbounds
function mongoDBData($ownerId, $ownerType, $dateStart, $dateEnd) {
////////////!!!!!!!!!!!!!!!!!!!!!!!!!!!! ALTERAR !!!!!!!!!!!!!!!!!!!!
    $result = file_get_contents("http://goviragem.dyndns.org:10000/ccstats/v0/total/calls/" . $dateStart . "T00:00/" . $dateEnd . "T00:00?$ownerType=$ownerId&by=status");
    return json_decode($result);
}

/*
  function searchStatus($templateStatus, $dataType, $propertyOf, $templateTipo, $dateStart, $dateEnd) {

  if ($templateTipo == 'list') {

  $templateTipo = 'database';
  }

  $mongoData = json_decode(mongoDBData($propertyOf, $templateTipo, $dateStart, $dateEnd));

  foreach ($mongoData as $value) {

  if ($templateStatus == $value->status) {

  return $value->$dataType;
  }
  }

  return 'n/a';
  } */

///////////////////////////////////////////////---------------------------------------------

function get_list($db) {

    $query = "SELECT `list_id`,`list_name` FROM `vicidial_lists`";

    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getCampaignId($db, $cabiId, $id) {

    if ($cabiId == '#selectlist') {

        $query = "SELECT `campaign_id` FROM `vicidial_lists` WHERE `list_id`=:id";

        $stmt = $db->prepare($query);
        $stmt->execute(array(":id" => $id));

        $campaignId = $stmt->fetch(PDO::FETCH_ASSOC);

        return $campaignId;
    }
}

function getInboundFeeds($db, $id) {

    $query = "SELECT a.status id, b.status_name name,human_answered,sale,dnc,customer_contact workable,not_interested, unworkable ,scheduled_callback,completed FROM vicidial_closer_log a inner join (select status, status_name,human_answered,sale,dnc,customer_contact,not_interested, unworkable ,scheduled_callback,completed from vicidial_campaign_statuses UNION ALL select status, status_name,human_answered,sale,dnc,customer_contact,not_interested, unworkable ,scheduled_callback,completed from vicidial_statuses) b on a.status = b.status where a.campaign_id =:id group by a.status";

    $stmt = $db->prepare($query);
    $stmt->execute(array(":id" => $id));


    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//Select do Agrupador
function getTemplatecampaignName($db, $idSeries) {

    for ($index = 0; $index < count($idSeries); $index++) {
        $prepare_hack.="?,";
    }

    $stmt = $db->prepare("SELECT campaign_id id,campaign_name name FROM `vicidial_campaigns` WHERE `campaign_id` IN (" . rtrim($prepare_hack, ",") . ");");

    $stmt->execute($idSeries);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getTemplatelistName($db, $idSeries) {

    for ($index = 0; $index < count($idSeries); $index++) {
        $prepare_hack.="?,";
    }

    $stmt = $db->prepare("SELECT `list_id` id,`list_name` name FROM `vicidial_lists` WHERE `list_id` IN (" . rtrim($prepare_hack, ",") . ");");

    $stmt->execute($idSeries);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
