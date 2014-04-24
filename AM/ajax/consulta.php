<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');
require("../lib_php/db.php");

require("../lib_php/user.php");
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$user = new UserLogin($db);

$variables = array();
$unique_id = time() . "." . rand(1, 1000);
switch ($action) {
    case "insert_consulta":
        $query = "Insert into spice_consulta (id,data,reserva_id,lead_id,campanha,consulta,consulta_razao,exame,exame_razao,venda,venda_razao,left_ear,right_ear,feedback,closed)
            values (NULL,:data,:reserva_id,:lead_id,:campanha,:consulta,:consulta_razao,:exame,:exame_razao,:venda,:venda_razao,:left_ear,:right_ear,:feedback,:closed)";

        $stmt = $db->prepare($query);
        $stmt->execute(array(":data" => date("Y-m-d H:i:s"),
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
            ":feedback" => $feedback,
            ":closed" => $closed));

        if ($consulta_razao == "DEST") {
            $query = "UPDATE sips_sd_reservations SET del='1' where id_reservation=:id";

            $stmt = $db->prepare($query);
            $stmt->execute(array(":id" => $reserva_id));
        }

        echo json_encode("saved");
        break;

    case "get_consulta":
        $stmt = $db->prepare("SELECT id,data,reserva_id,lead_id,campanha,consulta,consulta_razao,exame,exame_razao,venda,venda_razao,left_ear,right_ear,feedback,closed from spice_consulta where reserva_id=:reserva_id");
        $stmt->execute(array(":reserva_id" => $reserva_id));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($result);
        break;
}