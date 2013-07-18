<?php
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');

foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}


$line_explode = explode(",", $hidden_error_line);
$text_explode = explode(",", $hidden_error_text);
$phone_explode = explode(",", $hidden_error_phone);

header("Content-Disposition: attachment; filename=load_errors.csv");
$output = fopen('php://output', 'w');

fputcsv($output, array('Linha', 'Erro'), ";");

foreach ($line_explode as $key=>$value){
 
 
    switch($text_explode[$key])
            {
                case 1: $msg = "O Número de Telefone (".$phone_explode[$key].") é inválido. Os campos 'Telefone', 'Telefone Alternativo', e 'Telemóvel' apenas podem conter nove números."; break;
                case 2: $msg = "O Número (".$phone_explode[$key].") já existente na Campanha."; break;
            } 
 
 
    fputcsv($output, array($line_explode[$key], $msg ), ";");
 
    
}


?>