<?php

if (isset($_GET['client'])) { $client = $_GET['client']; } else { $client = $_POST['client']; }


switch ($client) { 
    case 'connecta' : {
            connectaPostCalendar();
        }
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
    
    $query = "SELECT DISTRITO FROM Distritos_BarclayCard where CONCELHO = '$concelho'";
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
