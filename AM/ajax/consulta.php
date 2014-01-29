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
        $query = "Insert into spice_consulta (id,data,reserva_id,lead_id,campanha,consulta,consulta_razao,exame,exame_razao,venda,venda_razao,left_ear,right_ear,tipo_aparelho,descricao_aparelho,feedback)
            values (NULL,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $variables[] = date("Y-m-d H:i:s");
        $variables[] = $reserva_id;
        $variables[] = $lead_id;
        $variables[] = "campanha spice";
        $variables[] = $consulta;
        $variables[] = $consulta_razao;
        $variables[] = $exame;
        $variables[] = json_encode($exame_razao);
        $variables[] = $venda;
        $variables[] = $venda_razao;
        $variables[] = $left_ear;
        $variables[] = $right_ear;
        $variables[] = $tipo_aparelho;
        $variables[] = $descricao_aparelho;
        $variables[] = $feedback;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);
      echo json_encode("1");
        break;

    case "get_client_info":
        $result = array();
        $query = "SELECT first_name nome,address1 morada,date_of_birth data_nascimento from vicidial_list where lead_id=?";
        $variables[] = $lead_id;
        $stmt = $db->prepare($query);
        $stmt->execute($variables);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);



        echo json_encode($row);
        break;
}