<?php

require("../../ini/dbconnect.php");
require("../../ini/user.php");


foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$js = array();
switch ($action) {
    //------------------------------------------------//    
    //---------------------GET------------------------//  
    //------------------------------------------------//

    case "get_table_data_new":
        $query = "SELECT a.lead_id,c.first_name,a.campaign_id,b.campaign_name, a.call_date from vicidial_log a inner join vicidial_campaigns b on a.campaign_id=b.campaign_id left join vicidial_list c on a.lead_id=c.lead_id where a.status='S00014' and call_date between '$data_inicio' and '$data_fim'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("lead_id" => $row["lead_id"], "nome" => $row["first_name"], "campaign_id" => $row["campaign_id"], "campanha" => $row["campaign_name"], "data" => $row["call_date"]);
        }
        echo json_encode(isset($js) ? $js : array());
        break;

    case "get_table_data_resolved":
        $query = "SELECT id,lead_id,nome,campanha,comentario,data,tipo,tipo_reclamacao,tipificacao_reclamacao from reclamacao  where data between '$data_inicio' and '$data_fim'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id"], "lead_id" => $row["lead_id"], "nome" => $row["nome"], "campanha" => $row["campanha"], "comentario" => $row["comentario"], "data" => $row["data"], "tipo" => (int) $row["tipo"], "tipo_reclamacao" => $row["tipo_reclamacao"], "tipificacao_reclamacao" => $row["tipificacao_reclamacao"]);
        }
        echo json_encode(isset($js) ? $js : array());
        break;

    case "get_table_data_expired":

        $date = date("Y-m-d_H:i:s", strtotime('-1 month'));
        $query = "SELECT id,lead_id,nome,campanha,comentario,data,tipo from reclamacao where data<'$date' order by data desc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js[] = array("id" => $row["id"], "lead_id" => $row["lead_id"], "nome" => $row["nome"], "campanha" => $row["campanha"], "comentario" => $row["comentario"], "data" => $row["data"], "tipo" => (int) $row["tipo"], "tipo_reclamacao" => $row["tipo_reclamacao"], "tipificacao_reclamacao" => $row["tipificacao_reclamacao"]);
        }
        echo json_encode($js);
        break;

    case "get_script_fields":
        $query = "SELECT a.id,a.tag,a.type,c.valor from script_dinamico a inner join script_assoc b on a.id_script=b.id_script inner join script_result c on a.tag=c.tag_elemento and lead_id=$lead_id   where b.id_camp_linha='$campaign_id' and a.type in ('textarea','texto')";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            switch ($row["type"]) {
                case "textarea";
                    $row["type"] = "Input de texto";
                    break;
                case "texto";
                    $row["type"] = "Caixa de Texto";
            }
            $js[] = array("id" => $row["id"], "tag" => $row["tag"], "type" => $row["type"], "valor" => $row["valor"]);
        }
        echo json_encode($js);
        break;


    case "save_mail":
        $query = "INSERT INTO `reclamacao`(`id`, `lead_id`, `nome`, `campanha`, `comentario`, `email`, `data`, `tipo`,tipo_reclamacao,tipificacao_reclamacao) VALUES (NULL,$lead_id,'$nome','$campanha','$comentario','$email','$data',$tipo,'$tipo_reclamacao','$tipificacao_reclamacao')";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;
}
?>
