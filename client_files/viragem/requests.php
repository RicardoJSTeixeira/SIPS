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
        $query = "SELECT id,lead_id,nome,campanha,comentario,email,data,tipo,tipo_reclamacao,tipificacao_reclamacao from reclamacao  where data between '$data_inicio' and '$data_fim' order by data desc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            if ((int) $row["tipo"])
                $js["fechados"][] = array("id" => $row["id"],"nome" => $row["nome"], "campanha" => $row["campanha"], "tipo_reclamacao" => $row["tipo_reclamacao"], "tipificacao_reclamacao" => $row["tipificacao_reclamacao"], "data" => $row["data"], "lead_id" => $row["lead_id"], "comentario" => $row["comentario"], "email" => json_decode($row["email"]), "tipo" => ((int) $row["tipo"]) ? "Fechados" : "Abertos");
            else
                $js["abertos"][] = array("id" => $row["id"],"nome" => $row["nome"], "campanha" => $row["campanha"], "tipo_reclamacao" => $row["tipo_reclamacao"], "tipificacao_reclamacao" => $row["tipificacao_reclamacao"], "data" => $row["data"],  "lead_id" => $row["lead_id"], "comentario" => $row["comentario"], "email" => json_decode($row["email"]), "tipo" => ((int) $row["tipo"]) ? "Fechados" : "Abertos");
        }
        //EXPIRADOS
        $date = date("Y-m-d H:i:s", strtotime('-1 month'));
        $query = "SELECT id,lead_id,nome,campanha,comentario,email,data,tipo,tipo_reclamacao,tipificacao_reclamacao from reclamacao where data<'$date' and tipo='0' order  by data desc";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $js["expirados"][] = array("id" => $row["id"],"nome" => $row["nome"], "campanha" => $row["campanha"], "tipo_reclamacao" => $row["tipo_reclamacao"], "tipificacao_reclamacao" => $row["tipificacao_reclamacao"], "data" => $row["data"],  "lead_id" => $row["lead_id"], "comentario" => $row["comentario"], "email" => json_decode($row["email"]), "tipo" => ((int) $row["tipo"]) ? "Fechado" : "Aberto");
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
        $email = is_array($email) ? $email : array();
        //save to DB
        $query = "INSERT INTO `reclamacao`(`id`, `lead_id`, `nome`, `campanha`, `comentario`, `email`, `data`, `tipo`,tipo_reclamacao,tipificacao_reclamacao,concessionario) VALUES (NULL,$lead_id,'$nome','$campanha','$comentario','" . mysql_real_escape_string(json_encode($email)) . "','" . date("Y-m-d H:i:s") . "',$tipo,'$tipo_reclamacao','$tipificacao_reclamacao','$concessionario')";
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

            $message
                    ->setFrom(array('viragem@viragem.com' => 'Viragem'))
                    ->setSubject($tipo_reclamacao)
                    ->setBody(
                            '
                        


<div align="center">
  <table border="0" cellspacing="0" cellpadding="0" width="500" style="width:375.0pt">
    <tbody>
      <tr style="height:75.0pt">
        <td valign="top" style="background:white;padding:0cm 0cm 0cm 0cm;height:75.0pt">
        </td>
      </tr>
      <tr style="height:2.25pt">
        <td valign="top" style="background:#ff9900;padding:0cm 0cm 0cm 0cm;height:2.25pt">
        </td>
      </tr>
      <tr style="height:225.0pt">
        <td valign="top" style="background:white;padding:0cm 0cm 0cm 0cm;height:225.0pt">
          <table border="0" cellspacing="5" cellpadding="0" width="550" style="width:412.5pt">
            <tbody>
              <tr style="height:300.0pt">
                <td valign="top" style="background:white;padding:3.75pt 3.75pt 3.75pt 3.75pt;height:300.0pt">
                  <div>
                    <p class="MsoNormal">
                      <span>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Ex.mo(ma) Senhor(a),&nbsp;
                        </span>
                      </span>
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        <u>
                        </u>
                        <u>
                        </u>
                      </span>
                    </p>
                  </div>
             
                    <p class="MsoNormal">
                      <b>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Assunto da Reclamação:
                        </span>
                      </b>
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        &nbsp;'.$tipo_reclamacao.' 
                        <u>
                        </u>
                        <u>
                        </u>
                      </span>
                    </p>
                    <p class="MsoNormal">
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        <u>
                        </u>
                        &nbsp;
                        <u>
                        </u>
                      </span>
                    </p>
                    <p class="MsoNormal">
                      <b>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Descrição da Reclamação:
                        </span>
                      </b>
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        &nbsp;'.$tipificacao_reclamacao .'
                        <u>
                        </u>
                        <u>
                        </u>
                      </span>
                    </p>
                    <p class="MsoNormal">
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        <u>
                        </u>
                        &nbsp;
                        <u>
                        </u>
                      </span>
                    </p>
                    <p class="MsoNormal">
                      <b>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Data da Visita:
                        </span>
                      </b>
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        &nbsp;'. $row["country_code"] .'
                        <u>
                        </u>
                        <u>
                        </u>
                      </span>
                    </p>
                    <p class="MsoNormal">
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        <u>
                        </u>
                        &nbsp;
                        <u>
                        </u>
                      </span>
                    </p>
                    <p class="MsoNormal">
                      <b>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Data da Reclamação:
                        </span>
                      </b>
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        &nbsp;'.$date.'
                        <u>
                        </u>
                        <u>
                        </u>
                      </span>
                    </p>
            
                  
                    <div>
                      <p class="MsoNormal">
                        <span>
                          <b>
                            <u>
                              <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                                Dados do contacto
                              </span>
                            </u>
                          </b>
                        </span>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          <u>
                          </u>
                          <u>
                          </u>
                        </span>
                      </p>
                    </div>
                    
                    <div>
                      <p class="MsoNormal">
                        <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Nome:&nbsp;'. $nome .'
                        </span>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          <u>
                          </u>
                          <u>
                          </u>
                        </span>
                      </p>
                    </div>
                    <div>
                      <p class="MsoNormal">
                        <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Telemóvel:&nbsp;'.$row["address3"].'
                        </span>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          <u>
                          </u>
                          <u>
                          </u>
                        </span>
                      </p>
                    </div>
           
                    <div>
                      <p class="MsoNormal">
                        <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Marca:&nbsp;'.$row["extra5"].'
                        </span>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          <u>
                          </u>
                          <u>
                          </u>
                        </span>
                      </p>
                    </div>
                    <div>
                      <p class="MsoNormal">
                        <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Modelo:&nbsp;'.$row["city"].'
                        </span>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          <u>
                          </u>
                          <u>
                          </u>
                        </span>
                      </p>
                    </div>
                    <div>
                      <p class="MsoNormal">
                        <span style="font-size:10.0pt;font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          Matrícula:&nbsp;'.$row["title"].'
                        </span>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          <u>
                          </u>
                          <u>
                          </u>
                        </span>
                      </p>
                    </div>
             
                    <div>
                      <p class="MsoNormal">
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          <u>
                          </u>
                          &nbsp;
                          <u>
                          </u>
                        </span>
                      </p>
                    </div>
                    <p class="MsoNormal">
                      <span>
                        <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                          '.$comentario.'
                        </span>
                        <u>
                        </u>
                        <u>
                        </u>
                      </span>
                    </p>
                    <p class="MsoNormal" style="text-align:justify">
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        &nbsp;&nbsp;
                      </span>
                      <u>
                      </u>
                      <u>
                      </u>
                    </p>
                    <p class="MsoNormal" style="text-align:justify">
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        &nbsp;
                        <u>
                        </u>
                        <u>
                        </u>
                      </span>
                    </p>
                    <p class="MsoNormal" style="text-align:justify">
                      <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                        Atentamente&nbsp;
                        <span>
                          <font color="#888888">
                            <u>
                            </u>
                            <u>
                            </u>
                          </font>
                        </span>
                      </span>
                    </p>
                    <span>
                      <font color="#888888">
                        <p class="MsoNormal" style="text-align:justify">
                          <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                            &nbsp;
                            <u>
                            </u>
                            <u>
                            </u>
                          </span>
                        </p>
                        <p class="MsoNormal" style="text-align:justify">
                          <span style="font-family:&quot;Arial&quot;,&quot;sans-serif&quot;">
                            Mário Garcez
                            <u>
                            </u>
                            <u>
                            </u>
                          </span>
                        </p>
                      </font>
                    </span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </td>
      </tr>
      <tr style="height:3.75pt">
        <td valign="top" style="background:#aaaaff;padding:0cm 0cm 0cm 0cm;height:3.75pt">
        </td>
      </tr>
      <tr style="height:75.0pt">
        <td valign="top" style="background:white;padding:0cm 0cm 0cm 0cm;height:75.0pt">
        </td>
      </tr>
      <tr style="height:3.75pt">
        <td valign="top" style="background:#aaaaff;padding:0cm 0cm 0cm 0cm;height:3.75pt">
        </td>
      </tr>
    </tbody>
  </table>
</div>
', 'text/html');

            foreach ($email as $value) {
                $message->addTo($value);
            }
            $result = $mailer->send($message);
        }
        echo json_encode($result >= 1);
        break;





    case "write_to_file":
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        echo "\xEF\xBB\xBF";
        header('Content-Disposition: attachment; filename=Report-' . date("Y-m-d_H:i:s") . '.csv');

        $output = fopen('php://output', 'w');


        $titles = array('lead', 'nome', 'campanha', 'comentario', 'email', 'data', 'tipo', 'tipo_reclamacao', 'tipificacao_reclamacao', 'concessionario');



        fputcsv($output, $titles, ";", '"');

        $query = "SELECT lead_id,nome,campanha,comentario,email,data,tipo,tipo_reclamacao,tipificacao_reclamacao,concessionario from reclamacao where data between '$data_inicio' and '$data_fim' and concessionario=$concessionario_id";
        $result = mysql_query($query, $link) or die(mysql_error());


        $client = array();
        while ($row1 = mysql_fetch_assoc($result)) {
            $client["lead"] = $row1["lead_id"];
            $client["nome"] = $row1["nome"];
            $client["campanha"] = $row1["campanha"];
            $client["comentario"] = $row1["comentario"];
            $client["email"] = implode(",", json_decode($row1["email"]));
            $client["data"] = $row1["data"];
            $client["tipo"] = ($row1["tipo"] == "1") ? "Fechado" : "Aberto";
            $client["tipo_reclamacao"] = $row1["tipo_reclamacao"];
            $client["tipificacao_reclamacao"] = $row1["tipificacao_reclamacao"];
            $client["concessionario"] = $concessionario_name;
            fputcsv($output, $client, ";", '"');
        }



        fclose($output);
        break;
}
?>







