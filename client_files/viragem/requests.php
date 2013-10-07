<?php

require("../../ini/dbconnect.php");
require("../../ini/user.php");
require ('../../swiftemail/lib/swift_required.php');

error_reporting();
ini_set('display_errors', '1');

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

$js["por_abrir"] = array();
$js["fechados"] = array();
$js["abertos"] = array();
$js["expirados"] = array();
switch ($action) {
    //------------------------------------------------//    
    //---------------------GET------------------------//  
    //------------------------------------------------//

    case "get_table_data":



        //ABERTOS E FECHADOS
        $query = "SELECT id,lead_id,nome,campanha,comentario,email,data,tipo,tipo_reclamacao,tipificacao_reclamacao from reclamacao  where data between '$data_inicio' and '$data_fim'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            if ((int) $row["tipo"])
                $js["fechados"][] = array("nome" => $row["nome"], "campanha" => $row["campanha"], "tipo_reclamacao" => $row["tipo_reclamacao"], "tipificacao_reclamacao" => $row["tipificacao_reclamacao"], "data" => $row["data"], "id" => $row["id"], "lead_id" => $row["lead_id"], "comentario" => $row["comentario"], "email" => json_decode($row["email"]), "tipo" => ((int) $row["tipo"]) ? "fechados" : "abertos");
            else
                $js["abertos"][] = array("nome" => $row["nome"], "campanha" => $row["campanha"], "tipo_reclamacao" => $row["tipo_reclamacao"], "tipificacao_reclamacao" => $row["tipificacao_reclamacao"], "data" => $row["data"], "id" => $row["id"], "lead_id" => $row["lead_id"], "comentario" => $row["comentario"], "email" => json_decode($row["email"]), "tipo" => ((int) $row["tipo"]) ? "fechados" : "abertos");
        }

//EXPIRADOS
        $date = date("Y-m-d H:i:s", strtotime('-1 month'));
        $query = "SELECT id,lead_id,nome,campanha,comentario,email,data,tipo,tipo_reclamacao,tipificacao_reclamacao from reclamacao where data<'$date' and tipo='0' order  by data desc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $leads = array_push($leads, $row["lead_id"]);
            $js["expirados"][] = array("nome" => $row["nome"], "campanha" => $row["campanha"], "tipo_reclamacao" => $row["tipo_reclamacao"], "tipificacao_reclamacao" => $row["tipificacao_reclamacao"], "data" => $row["data"], "id" => $row["id"], "lead_id" => $row["lead_id"], "comentario" => $row["comentario"], "email" => json_decode($row["email"]), "tipo" => ((int) $row["tipo"]) ? "fechado" : "aberto");
        }



        //POR ABRIR
        $query = "SELECT a.lead_id,c.first_name,a.campaign_id,b.campaign_name, a.call_date from vicidial_log a inner join vicidial_campaigns b on a.campaign_id=b.campaign_id left join vicidial_list c on a.lead_id=c.lead_id left join reclamacao d on d.lead_id=a.lead_id where a.status='S00014' and d.lead_id is NULL and call_date between '$data_inicio' and '$data_fim'";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js["por_abrir"][] = array("nome" => $row["first_name"], "campanha" => $row["campaign_name"], "data" => $row["call_date"], "campaign_id" => $row["campaign_id"], "lead_id" => $row["lead_id"], "tipo" => "por_abrir");
        }



        echo json_encode($js);
        break;

    case "get_script_fields":
        $ja = array();
        $query = "SELECT a.id,a.tag,a.type,c.valor from script_dinamico a inner join script_assoc b on a.id_script=b.id_script inner join script_result c on a.tag=c.tag_elemento and lead_id='$lead_id'  where b.id_camp_linha='$campaign_id' and a.type in ('textarea','texto')";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            switch ($row["type"]) {
                case "textarea";
                    $row["type"] = "Input de texto";
                    break;
                case "texto";
                    $row["type"] = "Caixa de Texto";
            }
            $ja[] = array("id" => $row["id"], "tag" => $row["tag"], "type" => $row["type"], "valor" => $row["valor"]);
        }
        echo json_encode($ja);
        break;



    case "edit_estado":
        $query = "UPDATE `reclamacao` SET tipo=1 WHERE id=$id";
        $query = mysql_query($query, $link) or die(mysql_error());
        echo json_encode(1);
        break;

    case "send_mail":
        $result = 1;
        //save to DB
        $query = "INSERT INTO `reclamacao`(`id`, `lead_id`, `nome`, `campanha`, `comentario`, `email`, `data`, `tipo`,tipo_reclamacao,tipificacao_reclamacao) VALUES (NULL,$lead_id,'$nome','$campanha','$comentario','" . mysql_real_escape_string(json_encode($email)) . "','".date("Y-m-d H:i:s")."',$tipo,'$tipo_reclamacao','$tipificacao_reclamacao')";
        $query = mysql_query($query, $link) or die(mysql_error());

        if ($tipo) {
            $transport = Swift_SmtpTransport::newInstance('mail.viragem.com', 465, 'ssl')
                    ->setUsername('viragem@viragem.com')
                    ->setPassword('password12345##');

            $mailer = Swift_Mailer::newInstance($transport);

            // Create the message
            $message = Swift_Message::newInstance();





            $query = "SELECT date_of_birth,address3,address2,last_name,middle_initial from vicidial_list where lead_id='$lead_id'";
            $query = mysql_query($query, $link) or die(mysql_error());
            $row = mysql_fetch_assoc($query);


            $struture =
                    "
Ex.mo(ma) Senhor(a), 
 
 
Assunto da Reclamação:$tipo_reclamacao 
 
Descrição da Reclamação:  $tipificacao_reclamacao 
 
Data da Visita:". $row["date_of_birth"] ."
 
Data da Reclamação: $date
 
 
 
Dados do contacto


Nome: " . $nome . "
Telemóvel: ". $row["address3"] ."
 
Marca: ". $row["address2"] ."
Modelo: ". $row["last_name"] ."
Matrícula: ". $row["middle_initial"] ."
 
 
$comentario
  
 
Atentamente 
             ";




            $message
                    ->setFrom(array('viragem@viragem.com' => 'Viragem'))
                    ->setSubject($tipo_reclamacao)
                    ->setBody($struture);
            foreach ($email as $value) {
                $message->addTo($value);
            }
            $result = $mailer->send($message);
        }
        echo json_encode($result >= 1);

        break;
}
?>







