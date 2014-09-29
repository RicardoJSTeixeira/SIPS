<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require "../lib_php/db.php";
require "../lib_php/calendar.php";
require '../lib_php/logger.php';
require("../lib_php/user.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);
$calendar = new Calendars($db);
$user->confirm_login();
$u = $user->getUser();
$variables = array();
$unique_id = time() . "." . rand(1, 1000);
$log = new Logger($db, $user->getUser());
switch ($action) {
    case "insert_consulta":
        $query = "DELETE FROM spice_consulta WHERE reserva_id=:id";

        $stmt = $db->prepare($query);
        $stmt->execute(array($reserva_id));

        $query = "INSERT INTO spice_consulta (data,user,reserva_id,lead_id,campanha,consulta,consulta_razao,exame,exame_razao,venda,venda_razao,left_ear,right_ear,produtos,feedback,terceira_pessoa,closed)
            VALUES (:data,:user,:reserva_id,:lead_id,:campanha,:consulta,:consulta_razao,:exame,:exame_razao,:venda,:venda_razao,:left_ear,:right_ear,:produtos,:feedback,:terceira_pessoa,:closed)";

        $stmt = $db->prepare($query);
        $stmt->execute(array(
            ":data" => date("Y-m-d H:i:s"),
            ":user" => $u->username,
            ":reserva_id" => $reserva_id,
            ":lead_id" => $lead_id,
            ":campanha" => "campanha_spice",
            ":consulta" => $consulta,
            ":consulta_razao" => $consulta_razao,
            ":exame" => $exame,
            ":exame_razao" => $exame_razao,
            ":venda" => $venda,
            ":venda_razao" => $venda_razao,
            ":left_ear" => $left_ear,
            ":right_ear" => $right_ear,
            ":produtos" => json_encode($produtos),
            ":feedback" => $feedback,
            ":terceira_pessoa" => ($terceira_pessoa) ? json_encode($terceira_pessoa) : json_encode(array()),
            ":closed" => $closed));
        $log->set($reserva_id, Logger::T_INS, Logger::S_CNSLT, json_encode(array("Lead_id" => $lead_id,
            ":left_ear" => $left_ear,
            ":right_ear" => $right_ear,
            ":produtos" => json_encode($produtos),
            ":feedback" => $feedback,
            ":closed" => $closed)), logger::A_SENT);
        if ($consulta_razao == "DEST" || $consulta_razao == 'NOSHOW') {
            $calendar->deleteReserva($reserva_id);
        }

        echo json_encode("saved");
        break;

    case "get_consulta":
        $stmt = $db->prepare("SELECT id,data,reserva_id,lead_id,campanha,consulta,consulta_razao,exame,exame_razao,venda,venda_razao,left_ear,right_ear,produtos,feedback,terceira_pessoa,closed FROM spice_consulta WHERE reserva_id=:reserva_id");
        $stmt->execute(array(":reserva_id" => $reserva_id));
        if ($result = $stmt->fetch(PDO::FETCH_OBJ)) {
            $result->consulta = (int)$result->consulta;
            $result->exame = (int)$result->exame;
            $result->venda = (int)$result->venda;
            $result->closed = (int)$result->closed;
            $result->produtos = json_decode($result->produtos);
            $result->terceira_pessoa = json_decode($result->terceira_pessoa);

            $stmt = $db->prepare("SELECT reserva_id FROM spice_proposta WHERE reserva_id=:reserva_id");
            $stmt->execute(array(":reserva_id" => $reserva_id));
            $temp = $stmt->fetch(PDO::FETCH_OBJ);
            $result->proposta_comercial = $temp->reserva_id ? 1 : 0;
        }
        echo json_encode($result);
        break;
}