<?php

$client = $_POST['client'];


switch ($client) { 
    case 'connecta' : {
            connectaPostCalendar();
        }
}
    
function connectaPostCalendar() {
    $lead_id = $_POST['lead_id'];
    $unique_id = $_POST['uniqueid'];
    $user = $_POST['user']; 
    
    $link = mysql_connect("172.16.7.25:3306", "sipsadmin", "sipsps2012");
    mysql_select_db("asterisk");
    $query = "SELECT middle_initial FROM `vicidial_list` WHERE lead_id = '$lead_id'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_assoc($query);
    $origem = $row['middle_initial']; // middle_initial
    $query = "SELECT valor FROM `script_result` WHERE tag_elemento IN ('20','73', '76', '78', '79', '80', '75', '77', '74', '81', '82', '87', '88') and unique_id = '$unique_id' order by tag_elemento ASC";
    
    $query = mysql_query($query, $link) or die(mysql_error());
    
    for ($i = 0; $i < mysql_num_rows($query); $i++) {
        $row = mysql_fetch_row($query);
        $results[$i] = $row[0];
    }



    $tipo_vencimento = $results[0]; // cp co ->script 
    $nome_cliente = $results[1]; // first_name 73
    $idade = $results[2]; // ->script 74
    $morada = $results[3]; // script 75
    $localidade = $results[4]; // city 76
    $cod_postal = $results[5]; // 1234-567 script 77
    $telefone = $results[6]; // phone_number 78
    $telefone_alternativo = $results[7]; // alt_phone or address3 79
    $observações = $results[8]; // comments 80
    $tipo_cartao = $results[9]; // ->script 81
    $num_cartoes = $results[10]; // ->script 82
    $nif = $results[11]; // ->script  87
    $tem_credito = $results[12];
    $query = "SELECT start_date, id_resource FROM sips_sd_reservations where lead_id = '$lead_id' and start_date > DATE(NOW()) LIMIT 1";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_row($query);
    $distrito = ''; // vazio
    $data_visita = date('d/m/Y',strtotime($row[0])); // dd/mm/yyyy -> calendario
    $hora_visita =  date('h:i',strtotime($row[0])); // hh:mm -> calendario
    $query = "SELECT a.display_text FROM sips_sd_schedulers a inner join sips_sd_resources b on a.id_scheduler = b.id_scheduler where id_resource = '$row[1]'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_row($query);
    $concelho = $row[0]; // provincia -> ref
    
    $query = "SELECT DISTRITO FROM Distritos_BarclayCard where CONCELHO = '$concelho'";
    $query = mysql_query($query, $link) or die(mysql_error());
    $row = mysql_fetch_row($query);
    
    $distrito = $row[0];
    //$query_final = "exec clientes.InserirVisita 'TESTE' , 1, 'TESTE'  , 'TEST'  , '25/01/2014', '10:00', 'TESTE'  , 'TESTE'  , '1234-123'  , 'TESTE'  , 'Lisboa'  , '918099390'  , '918099390'  , 34, 'CO'  , 'Cartão GOLD' , 1, '123456789', 'S' , 'Lisboa', 'teste de marcação por sp'";
      $query_final = "exec clientes.InserirVisita '$origem' , $lead_id, '$user'  , '$user'  , '$data_visita', '$hora_visita', '$nome_cliente'  , '$morada'  , '$cod_postal'  , '$localidade'  , '$concelho'  , '$telefone'  , '$telefone_alternativo'  , $idade, '$tipo_vencimento'  , '$tipo_cartao' , $num_cartoes, '$nif', '$tem_credito' , '$distrito', '$observações'";
      $link = mssql_connect('172.16.5.2', 'gocontact', '') or die(mssql_get_last_message());
    $sql = @mssql_query($query_final, $link) or die(mssql_get_last_message());
        echo $query_final;
}
