<?php

#error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE);
#ini_set('display_errors', '1');
$root = realpath($_SERVER["DOCUMENT_ROOT"]);
require "$root/AM/lib_php/db.php";

require "$root/AM/lib_php/user.php";
foreach ($_POST as $key => $value) {
    ${$key} = $value;
}
foreach ($_GET as $key => $value) {
    ${$key} = $value;
}
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
echo "\xEF\xBB\xBF";

$user = new UserLogin($db);
$user->confirm_login();

$u = $user->getUser();

$includes = array(
    "novasMarc" => 'includes/novasMarc.php',
    "lead_id_follow_up_cc" => 'includes/novasLeadsFollowUp.php',
    "lead_id_follow_up_dispenser" => 'includes/novasLeadsFollowUp.php',
    "consulta_ftpv" => 'includes/consulta_ftpv.php',
    "consulta_result_fecho" => 'includes/consulta_result_fecho.php',
    "consultas" => 'includes/consultas.php',
    "audiograma" => 'includes/audiograma.php');

if (array_key_exists($action, $includes))
    include $includes[$action];
else
    echo 'Fail';


function divide($a, $b)
{
    return ($a != 0 AND $b != 0) ? round($a / $b, 2) * 100 : 0;
}