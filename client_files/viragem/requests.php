<?php

require("../../ini/dbconnect.php");
require("../../ini/user.php");
require('../../swiftemail/lib/swift_required.php');

error_reporting();
ini_set('display_errors', '1');

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}

switch ($action) {
    //------------------------------------------------//    
    //---------------------GET------------------------//  
    //------------------------------------------------//

    case "get_table_data":
        $js = array(
            "aFechados" => array(),
            "aAbertos" => array(),
            "aExpirados" => array(),
            "aPorAbrir" => array()
        );
        //ABERTOS E FECHADOS
        $query = <<<sql
SELECT id,lead_id,nome,campanha,comentario,email,data,tipo,tipo_reclamacao,tipificacao_reclamacao
FROM reclamacao
WHERE data BETWEEN '$data_inicio 00:00:00' and '$data_fim 23:59:59'
ORDER BY data DESC
sql;
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            $row["email"] = json_decode($row["email"]);
            $row["tipo"] = ((int)$row["tipo"]) ? "Fechados" : "Abertos";

            $workedRow = array(
                $row["id"],
                $row["nome"],
                $row["campanha"],
                $row["tipo_reclamacao"],
                $row["tipificacao_reclamacao"],
                $row["data"] . "<div class='view-button'><button id='" . $row["id"] . "I' class='btn btn-mini icon-reorder ver_reclamacao' data-info='" . json_encode($row) . "'> Ver </button></div>"
            );

            if ((int)$row["tipo"])
                $js["aFechados"][] = $workedRow;
            else
                $js["aAbertos"][] = $workedRow;
        }
            //EXPIRADOS
            $date = date("Y-m-d H:i:s", strtotime('-1 month'));
            $query = <<<sql
SELECT id,lead_id,nome,campanha,comentario,email,data,tipo,tipo_reclamacao,tipificacao_reclamacao
FROM reclamacao
WHERE data<'$date 00:00:00' and tipo='0'
ORDER BY data DESC
sql;
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $row["email"] = json_decode($row["email"]);
                $js["aExpirados"][] = array(
                    $row["id"],
                    $row["nome"],
                    $row["campanha"],
                    $row["tipo_reclamacao"],
                    $row["tipificacao_reclamacao"],
                    $row["data"],
                    (((int)$row["tipo"]) ? "Fechado" : "Aberto") . "<div class='view-button'><button id='" . $row["id"] . "I' class='btn btn-mini icon-reorder ver_reclamacao' data-info='" . json_encode($row) . "'> Ver </button></div>"
                );
            }


            //POR ABRIR
            $query = <<<sql
SELECT a.lead_id, c.first_name nome, a.campaign_id, b.campaign_name, a.call_date data, 'por_abrir' tipo
FROM vicidial_log a
INNER JOIN vicidial_campaigns b ON a.campaign_id=b.campaign_id
LEFT JOIN vicidial_list c ON a.lead_id=c.lead_id
LEFT JOIN reclamacao d ON d.lead_id=a.lead_id
WHERE a.status='S00014' AND d.lead_id IS NULL and call_date BETWEEN '$data_inicio 00:00:00' AND '$data_fim 23:59:59'
sql;
            $query = mysql_query($query, $link) or die(mysql_error());
            while ($row = mysql_fetch_assoc($query)) {
                $js["aPorAbrir"][] = array(
                    $row["nome"],
                    $row["campaign_name"],
                    $row["data"] . "<div class='view-button'><button id='" . $row["lead_id"] . "L' class='btn btn-mini icon-reorder ver_reclamacao' data-info='" . json_encode($row) . "'> Ver </button></div>"
                );

            }
            echo json_encode($js);
            break;


        case
            "get_script_fields":
        $ja = array();
        $query = "SELECT a.id,a.tag,a.type,c.valor,a.texto from script_dinamico a inner join script_assoc b on a.id_script=b.id_script inner join script_result c on a.tag=c.tag_elemento and lead_id='$lead_id'  where b.id_camp_linha='$campaign_id' and a.type in ('textarea','texto')";
        $query = mysql_query($query, $link) or die(mysql_error());
        while ($row = mysql_fetch_assoc($query)) {
            switch ($row["type"]) {
                case "textarea";
                    $row["type"] = "Input de texto";
                    break;
                case "texto";
                    $row["type"] = "Caixa de Texto";
            }
            $ja[] = array("id" => $row["id"], "tag" => $row["tag"], "type" => $row["type"], "valor" => $row["valor"], "texto" => $row["texto"]);
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
        $date = date("Y-m-d H:i:s");
        //save to DB
        $query = "INSERT INTO `reclamacao`(`lead_id`, `nome`, `campanha`, `comentario`, `email`, `data`, `tipo`,tipo_reclamacao,tipificacao_reclamacao,concessionario) VALUES ($lead_id,'" . mysql_real_escape_string($nome)."','$campanha','" . mysql_real_escape_string($comentario)."','" . mysql_real_escape_string(json_encode($email)) . "','" . $date . "',$tipo,'$tipo_reclamacao','$tipificacao_reclamacao','$concessionario')";
        $query = mysql_query($query, $link) or die(mysql_error());
        $id_reclamacao = mysql_insert_id();


        $consecionarios_raw = file_get_contents("emails.json");
        $consecionarios = json_decode($consecionarios_raw);
        if ($tipo) {
            $transport = Swift_SmtpTransport::newInstance('mail.viragem.com', 465, 'ssl')
                ->setUsername('viragem@viragem.com')
                ->setPassword('password12345##');
            $mailer = Swift_Mailer::newInstance($transport);
            // Create the message
            $message = Swift_Message::newInstance();
            $daFields = getDafields($lead_id);

            $query = "SELECT " . $daFields['Data da Visita'] . " 'data', " . $daFields['Telefone'] . " 'telemovel', " . $daFields['Marca'] . " 'marca'," . $daFields['Modelo'] . " 'modelo'," . $daFields['Matricula'] . " 'matricula' from vicidial_list where lead_id='$lead_id'";

            $query = mysql_query($query, $link) or die(mysql_error());
            $row = mysql_fetch_assoc($query);

            $message
                ->setFrom(array('viragem@viragem.com' => 'Viragem'))
                ->setSubject("#$id_reclamacao $row[matricula] " . $consecionarios->concessionarios[$concessionario]->nome . " $tipo_reclamacao")
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
                        &nbsp;' . $tipo_reclamacao . ' 
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
                        &nbsp;' . $tipificacao_reclamacao . '
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
                        &nbsp;' . $row["data"] . '
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
                        &nbsp;' . $date . '
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
                          Nome:&nbsp;' . $nome . '
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
                          Telemóvel:&nbsp;' . $row["telemovel"] . '
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
                          Marca:&nbsp;' . $row["marca"] . '
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
                          Modelo:&nbsp;' . $row["modelo"] . '
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
                          Matrícula:&nbsp;' . $row["matricula"] . '
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
                          ' . $comentario . '
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
                            Viragem
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

        $titles = array('Lead', 'Nome', 'Campanha', 'Comentário', 'Email', 'Data', 'Tipo', 'Tipo Reclamação', 'Tipificação Reclamação', 'Concessionário');

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

        function getDafields($lead_id)
        {
            global $link;
            $result = mysql_query("SELECT campaign_id id from vicidial_log where lead_id='$lead_id' and status='S00014' limit 1;", $link) or die(mysql_error());
            $log = mysql_fetch_object($result);

            $fields_raw = mysql_query("SELECT name, display_name from vicidial_list_ref where campaign_id='$log->id';", $link) or die(mysql_error());

            $fields = array();
            while ($row = mysql_fetch_object($fields_raw)) {
                $fields[$row->display_name] = $row->name;
            }

            return $fields;
        }
