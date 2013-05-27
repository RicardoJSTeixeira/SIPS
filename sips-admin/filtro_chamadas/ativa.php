<?php
error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

if (!isset($_POST["branch"])) {
    header('HTTP/1.1 500');
}

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . "ini/dbconnect.php");

//Pesquiso uma linha com o branch
$query = "SELECT id_resource FROM `sips_sd_filter` WHERE id_resource=" . mysql_real_escape_string($_POST['branch']) ." AND campaign_id='".mysql_real_escape_string($_POST['campaign'])."'";
$result = mysql_query($query, $link) or die("1".mysql_error());
//Verifico se o branch já está activo (tem uma linha na tabela)
if (mysql_num_rows($result)) {//está activo
    //Pesquiso o codigo e cp4 do branch
    $query = "SELECT tecnico, cp FROM `sips_sd_filter` WHERE id_resource=" . mysql_real_escape_string($_POST['branch'])." AND campaign_id='".mysql_real_escape_string($_POST['campaign'])."' group by tecnico";
    $result = mysql_query($query, $link) or die("2".mysql_error());
        $data = mysql_fetch_assoc($result);
    //Verifico se existe
    if (!is_null($data[cp])) {
        $query = "Delete LOW_PRIORITY FROM sips_sd_filter WHERE id_resource=" . mysql_real_escape_string($_POST['branch'])."  AND campaign_id='".mysql_real_escape_string($_POST['campaign'])."'";
        mysql_query($query, $link) or die("3".mysql_error());
          
       
    } else {
        $query = "Delete LOW_PRIORITY FROM sips_sd_filter WHERE id_resource=" . mysql_real_escape_string($_POST['branch'])." AND campaign_id='".mysql_real_escape_string($_POST['campaign'])."'";
        mysql_query($query, $link) or die("5".mysql_error());

        
    }
    echo json_encode(array("active"=>0));exit;
} else {//não está activo
    $query = "SELECT tecnico, group_concat(cp SEPARATOR '|') cp FROM `sips_sd_resources` a inner join `sips_sd_cp` b on a.alias_code like concat('%',b.tecnico) WHERE id_resource=" . mysql_real_escape_string($_POST['branch'])." group by tecnico";
    $result = mysql_query($query, $link) or die("7".mysql_error());

    if (mysql_num_rows($result)) {
        $data = mysql_fetch_assoc($result);
        
        $data[cp]= $data[cp];
        
        $query = "Insert LOW_PRIORITY into sips_sd_filter(`id_resource`, `cp`, `tecnico`, `campaign_id`) Values(" . mysql_real_escape_string($_POST['branch']) . ",'$data[cp]','$data[tecnico]','".mysql_real_escape_string($_POST['campaign'])."')";
        mysql_query($query, $link) or die("8".mysql_error());
        
    } else {
        $query = "SELECT alias_code FROM `sips_sd_resources` WHERE id_resource=" . mysql_real_escape_string($_POST['branch']);
        $result = mysql_query($query, $link) or die("10".mysql_error());
    
        $data = mysql_fetch_assoc($result);
        $query = "Insert LOW_PRIORITY into sips_sd_filter(`id_resource`, `cp`, `tecnico`, `campaign_id`) Values(" . mysql_real_escape_string($_POST['branch']) . ",null,'$data[alias_code]','".mysql_real_escape_string($_POST['campaign'])."')";
        mysql_query($query, $link) or die("11".mysql_error());

    }
    echo json_encode(array("active"=>1));exit;
}
?>
