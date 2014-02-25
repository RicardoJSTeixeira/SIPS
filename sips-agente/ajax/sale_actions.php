<?php
require("../../ini/dbconnect.php");
if (isset($_GET['client'])) { $client = $_GET['client']; } else { $client = $_POST['client']; }
if (isset($_GET['campaign_id']))  { $campaign_id = $_GET['campaign_id']; } else { $campaign_id = $_POST['campaign_id']; }




switch ($client) { 
    case 'connecta' : {
            switch ($campaign_id) {
                case 'W00003' : connectaPostCalendar(); break;
                case 'W00004' : connectaMensageiros(); break;
                case 'W00009' : connectaMensageiros(); break;
            }
            confirmacao($lead_id, $uniqueid, $user, $length_in_sec, $dispoAtt, $link);
            break;
        }
}

function confirmacao($lead_id, $unique_id, $user, $length_in_sec, $dispoAtt, $link) {

    function confirmPos($lead_id, $link) {
        for ($index = 1; $index < 4; $index++) {
            $query = "Select time$index from vicidial_list where lead_id='" . mysql_real_escape_string($lead_id) . "'";
            $result = mysql_query($query, $link);
            $row = mysql_fetch_array($result);
            if (strlen($row[0]) == 0) {
                break;
            }
        }
        return (object) array("index" => $index, "user" => $row["login$index"]);
    }

    function saveInfo($index, $lead_id, $unique_id, $length_in_sec, $link) {
        $qupdate = "Update vicidial_list Set time$index='$length_in_sec',SertranRec='$unique_id' where lead_id='" . mysql_real_escape_string($lead_id) . "';";
        mysql_query($qupdate, $link);
    }

    function removeConfirm($lead_id, $link) {
        $qdelete = "Delete From crm_confirm_feedback where lead_id='" . mysql_real_escape_string($lead_id) . "';";
        mysql_query($qdelete, $link);
    }

    if (!$dispoAtt["completed"]) {
        return false;
    }

    $UPos = confirmPos($lead_id, $unique_id, $user, $length_in_sec, $link);

    if ($Upos->user == $user) {

        if ($dispoAtt["sale"]) {
            saveInfo($Upos->index, $lead_id, $unique_id, $user, $length_in_sec, $link);
        }
        removeConfirm($lead_id, $link);
    }
}


function connectaMensageiros() {

function query($sQuery, $hDb_conn, $sError, $bDebug)
{
    if(!$rQuery = @mssql_query($sQuery, $hDb_conn))
    {
        $sMssql_get_last_message = mssql_get_last_message();
        $sQuery_added  = "BEGIN TRY\n";
        $sQuery_added .= "\t".$sQuery."\n";
        $sQuery_added .= "END TRY\n";
        $sQuery_added .= "BEGIN CATCH\n";
        $sQuery_added .= "\tSELECT 'Error: '  + ERROR_MESSAGE()\n";
        $sQuery_added .= "END CATCH";
        $rRun2= @mssql_query($sQuery_added, $hDb_conn);
        $aReturn = @mssql_fetch_assoc($rRun2);
        if(empty($aReturn))
        {
            echo $sError.'. MSSQL returned: '.$sMssql_get_last_message.'.<br>Executed query: '.nl2br($sQuery);
        }
        elseif(isset($aReturn['computed']))
        {
            echo $sError.'. MSSQL returned: '.$aReturn['computed'].'.<br>Executed query: '.nl2br($sQuery);
        }
        return FALSE;
    }
    else
    {
        return $rQuery;
    }
}



    if (isset($_GET['lead_id'])) { $lead_id = $_GET['lead_id']; } else { $lead_id = $_POST['lead_id']; }
    if (isset($_GET['uniqueid'])) { $unique_id = $_GET['uniqueid']; } else { $unique_id = $_POST['uniqueid']; }
    if (isset($_GET['user'])) { $user = $_GET['user']; } else { $user = $_POST['user']; }
    
    $link = mysql_connect("172.16.7.25", "sipsadmin", "sipsps2012");
    mysql_select_db("asterisk");
    $query = "SELECT tag_elemento,valor FROM `script_result` WHERE tag_elemento IN ('159','153', '155', '160', '156', '157', '154', '161', '165') and unique_id = '$unique_id' order by tag_elemento ASC";
    
    $query = mysql_query($query, $link) or die(mysql_error());
    
    for ($i = 0; $i < mysql_num_rows($query); $i++) {
        $row = mysql_fetch_row($query);
        $results[$row[0]] = $row[1];
    }
    
   // $lead_id;
   // $user;
    $data_visita = $results[159];
    $hora_visita = '09h-18h';
    $nome = $results[153];
    $morada = $results[155];
    $cp = $results[160];
    $localidade = $results[156];
    $concelho = $results[157];
    $telefone = $results[154];
    $entrega_docs = $results[161];
    $observacoes = $results[165];
                
   // $query_final = "exec clientes.InserirVisitaMensageiros $lead_id, '$user', '$data_visita', '$hora_visita', '$nome', '$morada', '$cp', '$localidade', '$concelho', '$telefone', '$entrega_docs', '$observacoes'";
   // echo $query_final;

    $link = mssql_connect('172.16.5.2', 'gocontact', '') or die(mssql_get_last_message());
    mssql_select_db('Clientes', $link) or die(mssql_get_last_message());
    
    $query_final = utf8_decode("INSERT INTO Clientes.[532_Agenda] (idagenda, comercial, estado, operador, datamarcacao, horamarcacao, datavisita, horavisita, idcliente, nome, contacto, morada, codpostal, localidade, concelho, [observações], mensageirova, entregadocs) SELECT (SELECT MAX(idagenda) + 1 as ultimo from Clientes.[532_Agenda]), 'mensageiros', -1, '$user', convert(datetime,getdate(),105), convert(varchar(5),getdate(),108), convert(datetime,'$data_visita',105), '$hora_visita', '$lead_id', '$nome', '$telefone', '$morada', '$cp', '$localidade', '$concelho', '$observacoes', '2', '$entrega_docs'");
    
    query($query_final, $link);
    echo $query_final;
   // $sql = mssql_query($query_final, $link) or die(mssql_get_last_message());
   // mssql_get_last_message();
    
}
    
function connectaPostCalendar() {
    if (isset($_GET['lead_id'])) { $lead_id = $_GET['lead_id']; } else { $lead_id = $_POST['lead_id']; }
    if (isset($_GET['uniqueid'])) { $unique_id = $_GET['uniqueid']; } else { $unique_id = $_POST['uniqueid']; }
    if (isset($_GET['user'])) { $user = $_GET['user']; } else { $user = $_POST['user']; }
    
    $link = mysql_connect("172.16.7.25:3306", "sipsadmin", "sipsps2012");
    mysql_select_db("asterisk");
    $query = "SELECT middle_initial FROM `vicidial_list` WHERE lead_id = '$lead_id'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_assoc($query);
    $origem = utf8_decode($row['middle_initial']); // middle_initial
    $query = "SELECT tag_elemento,valor FROM `script_result` WHERE tag_elemento IN ('20','73', '76', '78', '79', '80', '75', '77', '74', '81', '82', '87', '88') and unique_id = '$unique_id' order by tag_elemento ASC";
    
    $query = mysql_query($query, $link) or die(mysql_error());
    
    for ($i = 0; $i < mysql_num_rows($query); $i++) {
        $row = mysql_fetch_row($query);
        $results[$row[0]] = $row[1];
    }
    
  //  print_r($results);

    $tipo_vencimento = $results[20]; // cp co ->script 20
    $nome_cliente = utf8_decode($results[73]); // first_name 73
    $idade = $results[74]; // ->script 74
    $morada = utf8_decode($results[75]); // script 75
    $localidade = utf8_decode($results[76]); // city 76
    $cod_postal = $results[77]; // 1234-567 script 77
    $telefone = $results[78]; // phone_number 78
    $telefone_alternativo = $results[79]; // alt_phone or address3 79
    $observações = utf8_decode($results[80]); // comments 80
    $tipo_cartao = $results[81]; // ->script 81
    $num_cartoes = $results[82]; // ->script 82
    $nif = $results[87]; // ->script  87
    $tem_credito = $results[88]; // script 88
    $query = "SELECT start_date, id_resource FROM sips_sd_reservations where lead_id = '$lead_id' and start_date > DATE(NOW()) LIMIT 1";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_row($query);
    $distrito = ''; // vazio
    $data_visita = date('d/m/Y',strtotime($row[0])); // dd/mm/yyyy -> calendario
    $hora_visita =  date('H:i',strtotime($row[0])); // hh:mm -> calendario
    $query = "SELECT a.display_text FROM sips_sd_schedulers a inner join sips_sd_resources b on a.id_scheduler = b.id_scheduler where id_resource = '$row[1]'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_row($query);
    $concelho = $row[0]; // provincia -> ref
    
    $query = "SELECT DISTRITO FROM Distritos_BarclayCard where CONCELHO like '$concelho'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_row($query);
    
    $concelho = utf8_decode($concelho);
    
    $distrito = utf8_decode($row[0]);
    //$query_final = "exec clientes.InserirVisita 'TESTE' , 1, 'TESTE'  , 'TEST'  , '25/01/2014', '10:00', 'TESTE'  , 'TESTE'  , '1234-123'  , 'TESTE'  , 'Lisboa'  , '918099390'  , '918099390'  , 34, 'CO'  , 'Cartão GOLD' , 1, '123456789', 'S' , 'Lisboa', 'teste de marcação por sp'";
    //exec clientes.InserirVisita 'Alcobaça' , 168029, 'barc1'  , 'barc1'  , '11/11/2013', '10:00', 'asdasdas'  , 'asdasdasd'  , '1234-123'  , 'asdasd'  , 'Alcobaça'  , '1231231'  , 'Gold'  , 12, 'CO'  , '229722210' , S, '', '' , 'Leiria', '1'
     $query_final = "exec clientes.InserirVisita '$origem' , $lead_id, '$user'  , '$user'  , '$data_visita', '$hora_visita', '$nome_cliente'  , '$morada'  , '$cod_postal'  , '$localidade'  , '$concelho'  , '$telefone'  , '$telefone_alternativo'  , $idade, '$tipo_vencimento'  , '$tipo_cartao' , $num_cartoes, '$nif', '$tem_credito' , '$distrito', '$observações'";
     echo $query_final;
     $link = mssql_connect('172.16.5.2', 'gocontact', '') or die(mssql_get_last_message());
     $sql = @mssql_query($query_final, $link) or die(mssql_get_last_message());
     mssql_get_last_message();
}
