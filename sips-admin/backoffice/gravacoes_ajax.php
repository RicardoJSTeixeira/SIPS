<?php

error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
ini_set('display_errors', '1');

$self = count(explode('/', $_SERVER['PHP_SELF']));
for ($i = 0; $i < $self - 2; $i++) {
    $header.="../";
}
define("ROOT", $header);
require(ROOT . 'ini/dbconnect.php');

function curPageURL() {
    $pageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"]; //.$_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function secondsToTime($seconds) {
    // extract hours
    $hours = floor($seconds / (60 * 60));

    // extract minutes
    $divisor_for_minutes = $seconds % (60 * 60);
    $minutes = floor($divisor_for_minutes / 60);

    // extract the remaining seconds
    $divisor_for_seconds = $divisor_for_minutes % 60;
    $seconds = ceil($divisor_for_seconds);

    // return the final array
    $obj = array(
        "h" => (int) sprintf("%02d",$hours),
        "m" => (int) sprintf("%02d",$minutes),
        "s" => (int) sprintf("%02d",$seconds),
    );
    return $obj;
}
$current_admin=$_GET[user];
$user_selected=$_GET[user_selected];
//Users INICIO
$query = "select a.user_group,allowed_campaigns,user_level from vicidial_users a inner join `vicidial_user_groups` b on a.user_group=b.user_group where user='$current_admin'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_assoc($result);


$user_level = $row['user_level'];
$allowed_camps_regex = str_replace(" ", "|", trim(rtrim($row['allowed_campaigns'], " -")));

if ($row['user_group'] != "ADMIN" and $user_selected=="--ALL--") {
        $ret = "WHERE allowed_campaigns REGEXP '$allowed_camps_regex'";


$user_groups = "";
$result = mysql_query("SELECT `user_group`, `allowed_campaigns` FROM `vicidial_user_groups` $ret ") or die(mysql_error());
while ($row1 = mysql_fetch_assoc($result)) {
    $user_groups .= "'$row1[user_group]',";
}
$user_groups = rtrim($user_groups, ","); 

$users_regex = "";
$result = mysql_query("SELECT `user` FROM `vicidial_users` WHERE user_group in ($user_groups) AND user_level < $user_level") or die(mysql_error());
while ($rugroups = mysql_fetch_assoc($result)) {
    $users_regex .= "$rugroups[user]|";
}
$users_regex = rtrim($users_regex, "|"); 
$users_regex = "AND a.user REGEXP '^$users_regex$'";
}elseif($user_selected!="--ALL--"){
    $users_regex="AND a.user='$user_selected'";
}
//Users FIM

//Intervalo INICIO
if ($_GET['datainicio']!="" and $_GET['datafim']!="") {
    $datainicio = $_GET['datainicio'];
    $datafim=($_GET['datainicio'] == $_GET['datafim'])?date("Y-m-d", strtotime("+1 day" . $_GET['datafim'])):$_GET['datafim'];
} 
elseif($_GET['datainicio']!="" and $_GET['datafim']=="") {
    $datainicio = $_GET['datainicio'];
    $datafim=($_GET['datainicio'] == date('Y-m-d'))?date("Y-m-d", strtotime("+1 day" . $_GET['datafim'])):date('Y-m-d');
} 
elseif($_GET['datainicio']=="" and $_GET['datafim']!=""){
    $datainicio = ($_GET['datafim'] == date('Y-m-d'))?date("Y-m-d", strtotime("-1 day" . $_GET['datafim'])):date('Y-m-d');
    $datafim=$_GET['datafim'];
}
if($datainicio!="" AND $datafim!=""){
$datainicio = "AND start_time >= '$datainicio 00:00:00'";
$datafim = "AND end_time <= '$datafim 23:59:59'";}
//INTERVALO FIM

$leads="";
if ($_GET['ntlf'] != '') {
    $query = "SELECT lead_id FROM vicidial_list WHERE phone_number = '$_GET[ntlf]' or alt_phone= '$_GET[ntlf]' or address3= '$_GET[ntlf]' ";
    $leadQry = mysql_query($query) or die(mysql_error());

    #for mysql rows concatenar lead id 
    while ($row1 = mysql_fetch_assoc($leadQry)) {
        $leadSET .= "'$row1[lead_id]',";
    }
    $leadSET = rtrim($leadSET, ",");
$leads=" AND a.lead_id IN ($leadSET) ";
} 
    
$qryStr = "SELECT start_time, end_time, length_in_sec, filename, a.lead_id, a.user, b.phone_number, location FROM recording_log a INNER JOIN vicidial_list b ON a.lead_id = b.lead_id WHERE length_in_sec >= '20' $leads  $datainicio  $datafim $users_regex LIMIT 1000";
$recordList = mysql_query($qryStr) or die(mysql_error());

$gravacoes['aaData'] = array();

$curpage = curPageURL();
While ($curRecord = mysql_fetch_assoc($recordList)) {
    $mp3File = "#";

    if (strlen($curRecord[location]) > 0) {
        $tmp = explode("/", $curRecord[location]);
        $ip = $tmp[2];
        $tmp = explode(".", $ip);
        $ip = $tmp[3];

        switch ($ip) {
            case "248":
                $port = ":20248";
                break;
            case "247":
                $port = ":20247";
                break;
            default:
                $port = "";
                break;
        }

        $mp3File = $curpage . $port . "/RECORDINGS/MP3/$curRecord[filename]-all.mp3";
        $audioPlayer = "Há gravação";
    } else {
        $audioPlayer = "Não há gravação!";
    }
    //$lenghtInMin = secondsToTime($curRecord['length_in_sec']);
    $lenghtInMin = date("i:s",$curRecord[length_in_sec]);

    $gravacoes['aaData'][] = array( $curRecord['user'], $curRecord['phone_number'], $curRecord['start_time'], $lenghtInMin."<div class='view-button'><a href=# title='Editar e ver todo o estado desta lead' class='btn  btn-mini activator crm' data-lead_id='".$curRecord[lead_id]."' style='font-size: 12px;' ><i class='icon-pencil'></i><span>Editar</span></a></div>"."<div class='view-button'><a href='$mp3File' class='btn  btn-mini activator' style='font-size: 12px;' ><i class='icon-download'></i><span>$audioPlayer</span></a></div>");
}
echo json_encode($gravacoes);
?>
